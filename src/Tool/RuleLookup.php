<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Knowledge\Hints;
use TYPO3\DevCompanion\Knowledge\Scope;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Result\Miss;
use TYPO3\DevCompanion\Result\Prose;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Search\TermSearch;

/**
 * The local TYPO3 core contribution rules and script notes, by topic.
 */
final class RuleLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_rule_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Knowledge];
    }

    public static function description(): string
    {
        return 'Search the TYPO3 rules and procedures this server carries, by topic. The core contribution process is most of it. That is the commit message conventions, which branches take a patch today, and the changelog entry each change type owes. It is the Gerrit push and amend workflow with both refspecs, and the notes beside runTests.sh. It answers outside a core checkout too: the setup of an extension manual, PHPUnit in an extension, Playwright in a project. There the answer withholds the core-only documents and names them rather than drops them in silence. What comes back is the sections that matched, each with the document it comes from. Where more than one of them is in one document, that document comes back whole. The rest of the page regularly answers the next thing. What the code itself has to look like is typo3_hint_lookup instead: the convention at a path you change, the idiom a subsystem uses. Pass a documentId back instead of a query to read any page whole; it needs no resource list.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'minLength' => 1, 'description' => 'Topic to look up, in English, for example tests, review, deprecation, or code style. A call carries query or documentId, never both.'],
                'documentId' => ['type' => 'string', 'minLength' => 1, 'description' => 'One document to read whole instead of a search, named by the documentId a match carries, for example "core/contribution/commit-messages". Use it when a matched section came out of a document whose other sections may answer what the query did not. The whole page comes back, no search, no version filter. A call carries query or documentId, never both.'],
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version the answer has to hold on, for example "13.4" or "14". The answer leaves out a section bound to another major. Defaults to every major this repository declares typo3/cms-core for, or to the installation this server started in. Where there is neither, every section comes back with the range it holds for. Ignored for documentId, which returns the document as written.'],
            ],
            'oneOf' => [
                ['required' => ['query']],
                ['required' => ['documentId']],
            ],
        ];
    }

    /**
     * The knowledge lookup shape plus the boundary, because most of this corpus
     * is the core repository's own.
     */
    public static function outputSchema(): array
    {
        $schema = Schema::knowledgeLookup();
        $schema['properties']['scope'] = Schema::scope();
        $schema['properties']['matchedHeadings'] = Schema::listOf(Schema::string(), 'The headings the query '
            . 'matched, where more than one match was in one document and the answer is that document whole rather '
            . 'than the excerpts. Empty on every other answer, whose matches carry their own heading each. The text '
            . 'above a page\'s first heading is no heading and is not one of them. Where it is one of the matches, '
            . 'the answer names it in words.');
        $schema['properties']['withheldDocuments'] = Schema::listOf(Schema::object([
            'id' => Schema::string(),
            'title' => Schema::string(),
        ], ['id', 'title']), 'Documents that matched and stay out because they answer for the core repository '
            . 'alone. Empty inside the core. Each is still readable in full as its typo3://guides resource, which is '
            . 'the way to get one deliberately rather than by accident.');
        $schema['required'][] = 'scope';
        $schema['required'][] = 'matchedHeadings';

        return $schema;
    }

    /**
     * How many matched sections make a page the answer rather than the cut.
     *
     * One is a word that landed somewhere. `query="signed-off-by"` matched one
     * body with "signed in" in it and got six kilobytes on the proof of a
     * TypoScript condition, `D-ANS-101`.
     */
    private const CONCENTRATED = 2;

    /**
     * The one document every match came out of, or null where they are spread
     * or where a single section is the whole of the evidence.
     *
     * @param array<int, array<string, mixed>> $results
     */
    private static function oneDocument(array $results): ?string
    {
        if (count($results) < self::CONCENTRATED) {
            return null;
        }

        $ids = array_unique(array_column($results, 'id'));

        return count($ids) === 1 ? (string) reset($ids) : null;
    }

    public static function answer(array $args): ToolResult
    {
        $documentId = trim((string) ($args['documentId'] ?? ''));
        if ($documentId !== '') {
            return self::wholeDocument($documentId);
        }

        $query = (string) ($args['query'] ?? '');
        $targets = Versions::targets(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);

        // This tool answers about a topic rather than about paths, so the call
        // has one scope, the same read typo3_script_lookup makes.
        $scope = Scope::of('', $query);
        $outsideCore = $scope->isOutsideTheCore();

        $found = Documents::search($query, [], 6, $targets);
        // Held back per document rather than per call. This corpus is the
        // contribution process and the commit conventions at once, and only the
        // first half stops at the core repository. A drop of the whole tool,
        // which is what the project profile does, takes the second half with
        // it. A caller who writes a commit message in their own project needs
        // exactly that.
        $results = $outsideCore
            ? array_values(array_filter($found, static fn(array $r): bool => !Documents::isCoreOnly($r['id'])))
            : $found;
        $withheld = self::withheldDocuments($found, $results);

        // The prose and the hints are two corpora, and which one holds a
        // subject is this server's business, not the caller's. Site sets are a
        // hint, the Gerrit workflow is prose, and the question has the same
        // words either way. The hints transfer, so they come back on both sides
        // of the boundary.
        $hints = Hints::find([], $query, 3)['matchedHints'];

        // The boundary is only the reason for a miss where it withheld
        // something. Blame on it wherever a hint had matched printed "No
        // section that holds outside the core matched" inside the core. That
        // was on a call that held nothing back. A judge read that back as an
        // answer, `D-ANS-037`. Every other empty result is a miss and gets the
        // miss answer.
        if ($results === [] && $withheld === []) {
            return self::noMatch($query, $scope, $outsideCore, $hints, $targets);
        }

        $lines = [];
        if ($withheld !== []) {
            $lines[] = Scope::OUTSIDE_CORE_NOTICE;
            $lines[] = '';
            $lines[] = 'Withheld for that reason: ' . implode(', ', array_map(
                static fn(array $document): string => $document['title'],
                $withheld,
            )) . '. Each is readable in full as typo3://guides/<id> where the work really is a core patch.';
            $lines[] = '';
            // What stays back here has a counterpart outside the core, and it
            // is a tool rather than a second copy of the page. The commit
            // conventions of a repository of your own are what
            // typo3_commit_message_guide composes for workflow="project". That
            // is the one measure this repository writes by as well, D-DOC-013.
            $lines[] = 'For a repository of your own, the same subjects are answered by '
                . 'typo3_commit_message_guide with workflow="project" and by typo3_hint_lookup.';
            $lines[] = '';
        }
        // Where several matches are in one page, the page is the answer. The
        // cut is what a caller pays a second call to undo, and two sessions
        // have now paid it into the same document, `D-ANS-076`. One match is
        // below the floor and gets the section, `D-ANS-101`.
        $concentrated = self::oneDocument($results);
        $lines[] = match (true) {
            $results === [] => sprintf('No section that holds outside the core matched "%s".', $query),
            $concentrated !== null => Prose::wholePage($concentrated, $results, $outsideCore),
            default => Prose::sections($results, $outsideCore),
        };
        // The offer to read the page whole is `Prose::sections()`'s own line
        // now, so every tool that renders this corpus carries it. A second
        // block here said the same thing twice in one answer.
        if ($hints !== []) {
            $lines[] = "\n" . self::alsoInHints($hints);
        }

        return ToolResult::create(implode("\n", $lines), [
            'query' => $query,
            'matchCount' => $concentrated === null ? count($results) : 1,
            'matches' => $concentrated === null
                ? Prose::records($results)
                : Prose::pageRecords($concentrated, $results),
            'matchedHeadings' => $concentrated === null ? [] : Prose::matchedHeadings($results),
            'scope' => $scope->value,
            'withheldDocuments' => $withheld,
            'alsoInHints' => array_map(
                static fn(array $hint): array => ['id' => $hint['id'], 'title' => $hint['title']],
                $hints,
            ),
        ]);
    }

    /**
     * One document, whole, for a caller who cannot read the resource.
     *
     * A match carries `uri`, and a `typo3://guides` resource is only reachable
     * where the client lists resources at all. Three core sessions held that
     * uri and read none of them (`D-ANS-061`, `R-ANS-028`). No search and no
     * version filter. The caller named the document. A section left out for a
     * major it does not hold on is a hole in a page somebody asked to read
     * whole.
     */
    private static function wholeDocument(string $documentId): ToolResult
    {
        $known = array_column(Documents::documents(), 'id');
        if (!in_array($documentId, $known, true)) {
            return ToolResult::create(
                sprintf(
                    "No knowledge document is called \"%s\".\n\nThe ids are:\n%s",
                    $documentId,
                    implode("\n", array_map(
                        static fn(array $document): string => '- ' . $document['id'] . ': ' . $document['title'],
                        Documents::topics(),
                    )),
                ),
                [
                    'query' => $documentId,
                    'matchCount' => 0,
                    'matches' => [],
                    'matchedHeadings' => [],
                    'scope' => Scope::Any->value,
                    'withheldDocuments' => [],
                    'alsoInHints' => [],
                    'documents' => Documents::topics(),
                ],
            );
        }

        $title = '';
        $declared = [];
        foreach (Documents::documents() as $document) {
            if ($document['id'] === $documentId) {
                $title = (string) $document['title'];
                $declared = $document['hints'];
            }
        }
        $body = Documents::read($documentId);
        $scope = Documents::scopeOf($documentId);

        // The hints this document declares itself the long form of. That is the
        // other corpus on the same subject and the one place a reader of the
        // whole page still has somewhere to go.
        $alsoInHints = [];
        foreach ($declared as $hintId) {
            $hint = Hints::byId((string) $hintId);
            if ($hint !== null) {
                $alsoInHints[] = ['id' => $hint['id'], 'title' => $hint['title']];
            }
        }
        // In the text as well, as the call that reads them. As data and as raw
        // front matter a session read past them, `D-ANS-114`.
        if ($alsoInHints !== []) {
            $body .= "\n" . self::alsoInHints($alsoInHints);
        }

        return ToolResult::create($body, [
            'query' => $documentId,
            'matchCount' => 1,
            'matches' => Prose::namedPageRecords($documentId, $title),
            'matchedHeadings' => [],
            'scope' => $scope->value,
            'withheldDocuments' => [],
            'alsoInHints' => $alsoInHints,
        ]);
    }

    /**
     * What the knowledge base does cover, for a query that reached none of it.
     *
     * It carries the same fields a hit does, boundary included. So a client
     * that validates the answer never has to branch on which of the two it got.
     * The words that emptied it come first, because that is what the caller can
     * act on in the same call (`R-ANS-006`). The subsets count over the
     * documents this call may answer from.
     *
     * @param array<int, array<string, mixed>> $hints
     * @param array<int, int> $targets
     */
    private static function noMatch(string $query, Scope $scope, bool $outsideCore, array $hints, array $targets): ToolResult
    {
        $visible = array_values(array_filter(
            array_column(Documents::documents(), 'id'),
            static fn(string $id): bool => !$outsideCore || !Documents::isCoreOnly($id),
        ));

        $blocks = [sprintf('No knowledge section matched "%s".', $query)];
        $subsets = Documents::largestReachingSubsets($query, $visible, $targets);
        if ($subsets !== []) {
            $blocks[] = Miss::largestReaching($subsets, count(TermSearch::terms($query)), 'section', 'sections');
        }
        if ($hints !== []) {
            $blocks[] = self::alsoInHints($hints);
        }
        $blocks[] = "This knowledge base covers:\n" . implode("\n", array_map(
            static fn(array $document): string => '- ' . $document['title'] . ': ' . implode(', ', $document['topics']),
            Documents::topics()
        ));
        $blocks[] = 'For backend UI components use typo3_component_lookup, and call typo3_server_scope for what '
            . 'this server covers at all. '
            . 'If the topic should be covered here, leave a feedback with typo3_feedback_record.';

        return ToolResult::create(implode("\n\n", $blocks), [
            'query' => $query,
            'matchCount' => 0,
            'matches' => [],
            'matchedHeadings' => [],
            'scope' => $scope->value,
            'withheldDocuments' => [],
            'alsoInHints' => array_map(
                static fn(array $hint): array => ['id' => $hint['id'], 'title' => $hint['title']],
                $hints,
            ),
            'documents' => Documents::topics(),
        ]);
    }

    /**
     * The hints that answer the same question, as the call that returns them.
     *
     * @param array<int, array<string, mixed>> $hints
     */
    private static function alsoInHints(array $hints): string
    {
        return "The hints also cover this — call typo3_hint_lookup with the id:\n"
            . implode("\n", array_map(
                static fn(array $hint): string => '- ' . $hint['id'] . ' — ' . $hint['title'],
                $hints,
            ));
    }

    /**
     * The documents that lost a match, once each.
     *
     * Named rather than silently absent. An answer that is thinner than the
     * corpus and does not say so reads as "nobody wrote this down". That is the
     * one thing it does not mean.
     *
     * @param array<int, array<string, mixed>> $found
     * @param array<int, array<string, mixed>> $kept
     * @return array<int, array{id: string, title: string}>
     */
    private static function withheldDocuments(array $found, array $kept): array
    {
        $keptIds = array_column($kept, 'id');

        $withheld = [];
        foreach ($found as $result) {
            if (!in_array($result['id'], $keptIds, true)) {
                $withheld[$result['id']] = ['id' => $result['id'], 'title' => $result['title']];
            }
        }

        return array_values($withheld);
    }
}
