<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Manual\Documentation;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * The official, versioned TYPO3 manuals at docs.typo3.org, searched for what a
 * page says. Where a link points in them is `typo3_permalink_lookup`, which
 * reads the same inventories for the names the pages carry.
 */
final class DocumentationLookup extends ReadOnlyTool
{
    /** The manuals come from docs.typo3.org. */
    protected const OPEN_WORLD = true;

    /**
     * The share of a query a result carries before the answer warns about it.
     * It is the value `Documents::search()` drops a section below, so the two
     * corpora this server matches prose against say "half the query" in one
     * number.
     *
     * Here it drops nothing. Over a table of contents no value of it does both.
     * It cannot empty the collisions a six-word question returns and keep the
     * page that answers a three-word one, `D-ANS-051`.
     */
    private const COVERS_THE_QUESTION = 0.5;

    public static function name(): string
    {
        return 'typo3_documentation_lookup';
    }

    public static function title(): string
    {
        return 'Search the official manuals';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Network];
    }

    public static function description(): string
    {
        return 'Search or read the official live TYPO3 documentation for a covered TYPO3 line. It searches four manuals: TYPO3 Explained, TypoScript Explained, the TCA Reference and the Fluid ViewHelper Reference, by page title, heading, path and the names each declares. Search with several short English queries; every result carries a canonical URL, with the anchor of the heading where one answered. Pass one of those URLs back as page with the same targetVersion to receive that page as text, headings and code examples included. A query that names a Fluid tag such as f:if gets its answer from the ViewHelper reference alone. Ask without the prefix for the other manuals\' Fluid chapters. This reaches docs.typo3.org, unlike the bundled convention lookups.';
    }


    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'queries' => [
                    'type' => 'array',
                    'items' => ['type' => 'string', 'minLength' => 1],
                    'minItems' => 1,
                    'description' => 'Short search queries in English. Pass alternatives separately, for example ["page title event", "page title provider"]. A call carries queries or page, never both.',
                ],
                'page' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Canonical page URL returned by an earlier search, read as text. Pass it with the same targetVersion. A call carries queries or page, never both.',
                ],
                'targetVersion' => ['type' => 'string', 'minLength' => 1, 'description' => 'Covered TYPO3 version whose official manual must answer, for example "13.4" or "14". There is no fallback to another release.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10, 'default' => 6, 'description' => 'How many pages come back per query.'],
            ],
            'required' => ['targetVersion'],
            // The one tool here whose search argument is a plural, against five
            // that spell theirs `query`. A caller that guessed from those five
            // used to lose its property. It saw both `oneOf` branches fail and
            // read a message about two arguments it had not been about.
            // Declared, the validator answers `Additional object properties are
            // not allowed: ["query"]` instead, `D-ANS-053`. It stands on this
            // tool rather than on all of them, because a tool whose argument is
            // simply required already names the absent one.
            'additionalProperties' => false,
            'oneOf' => [
                ['required' => ['queries']],
                ['required' => ['page']],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'mode' => ['type' => 'string', 'enum' => ['search', 'page']],
            'status' => Schema::answerStatus(),
            'targetVersion' => Schema::string('The exact documentation release searched.'),
            'source' => Schema::string('The external documentation host.'),
            'queries' => Schema::listOf(Schema::string()),
            'insteadOf' => Schema::listOf(Schema::object([
                'query' => Schema::string('The query that reads as a code identifier.'),
                'ask' => Schema::listOf(Schema::string(), 'The bare names to ask with instead, most specific first.'),
            ], ['query', 'ask']), 'Present on a miss where a query has the shape of a PHP identifier. This index is '
                . 'page titles, their headings, section paths and what each manual declares by name: its properties, the classes, '
                . 'interfaces and methods it documents, and the console commands. A class the manual does not '
                . 'declare has no page with its title, while the property or ViewHelper it belongs to does.'),
            'results' => Schema::listOf(Schema::object([
                'title' => Schema::string(),
                'url' => Schema::string('Canonical URL of the documentation page that matched.'),
                'document' => Schema::string('Official document identifier.'),
                'documentTitle' => Schema::string(),
                'documentVersion' => Schema::string(),
                'section' => Schema::string('The heading that answered, where the question reached the page through one; then the URL carries its anchor. Otherwise the page title.'),
                'excerpt' => Schema::string('Short route into the source, empty only when the tool could not read the result page after its index matched.'),
                'content' => Schema::string('The selected page as text in page mode; empty in search mode.'),
                'coverage' => Schema::nullable([
                    'type' => 'number',
                    'description' => 'Share of the query\'s weight this page carries, 0 to 1, for the query it '
                        . 'answers. Below 0.5 the page carries some words of the question and not its subject, '
                        . 'and the answer says so above the results. The page comes back anyway. Over a table of '
                        . 'contents the page that answers a three-word question covers about a third of it. Null in '
                        . 'page mode, where the call searched for nothing.',
                ]),
                'matched' => Schema::listOf(Schema::object([
                    'term' => Schema::string('The query word, reduced to the stem the search used.'),
                    'field' => ['type' => 'string', 'enum' => ['title', 'path', 'manual', 'section'], 'description' => 'Where the word matched: the page title, the section path it sits in, the name of the manual, or a heading of the page.'],
                ], ['term', 'field']), 'What this page matched on. Every query word absent from it reached this page nowhere. So a result whose match consists of the words around the subject is an aimed answer rather than one about the subject. Ask again with the subject alone. Empty in page mode.'),
            ], ['title', 'url', 'document', 'documentTitle', 'documentVersion', 'section', 'excerpt', 'content', 'coverage', 'matched'])),
            'unavailable' => Schema::unavailable([
                'version-not-covered' => 'the release asked about is outside the ones this server knows the manuals '
                    . 'for, and a second call changes nothing.',
                'source-not-answering' => 'docs.typo3.org did not answer this time, and the same call may answer '
                    . 'the next.',
            ]),
        ], ['mode', 'status', 'targetVersion', 'source', 'queries', 'results', 'unavailable']);
    }

    public static function answer(array $args): ToolResult
    {
        $queries = is_array($args['queries'] ?? null)
            ? array_values(array_filter($args['queries'], is_string(...)))
            : [];
        $page = is_string($args['page'] ?? null) ? trim($args['page']) : '';
        $statedVersion = is_string($args['targetVersion'] ?? null) ? trim($args['targetVersion']) : '';
        $major = Versions::major($statedVersion);
        $branch = $major === null ? null : Versions::branch($major);
        $limit = is_int($args['limit'] ?? null) ? max(1, min(10, $args['limit'])) : 6;

        if ($statedVersion === '' || ($queries === []) === ($page === '')) {
            throw new \InvalidArgumentException('Pass targetVersion and exactly one of queries or page');
        }

        if ($branch === null) {
            $answer = [
                'mode' => $page === '' ? 'search' : 'page',
                'status' => 'unavailable',
                'targetVersion' => $statedVersion,
                'source' => 'https://docs.typo3.org',
                'queries' => $queries,
                'results' => [],
                'unavailable' => [
                    'cause' => 'version-not-covered',
                    'reason' => sprintf(
                        'TYPO3 %s is outside the covered versions: %s.',
                        $statedVersion,
                        implode(', ', array_map(static fn(array $version): string => $version['branch'], Versions::covered())),
                    ),
                ],
            ];
        } else {
            $documentation = new Documentation();
            $answer = $page === ''
                ? $documentation->lookup($queries, $branch, $limit)
                : $documentation->page($page, $branch);
        }

        $lines = [
            sprintf('Official TYPO3 documentation for %s.', $answer['targetVersion']),
            'Source: ' . $answer['source'],
        ];
        if ($answer['status'] === 'unavailable') {
            $lines[] = 'Could not answer: ' . $answer['unavailable']['reason'];
        } elseif ($answer['status'] === 'empty') {
            $lines[] = $answer['mode'] === 'page'
                ? 'The selected page answered without a readable main article.'
                : 'No matching section was found. The documentation service answered; narrow or rephrase the queries.';
            $insteadOf = $answer['mode'] === 'page' ? [] : self::insteadOf($answer['queries']);
            if ($insteadOf !== []) {
                $answer['insteadOf'] = $insteadOf;
                $lines[] = 'This index is page titles, their headings, section paths and what a manual declares by name: '
                    . 'properties, documented classes and methods, console commands. Nothing declares this name, '
                    . 'while the property or ViewHelper it belongs to may have a page. Ask again with:';
                foreach ($insteadOf as $instead) {
                    $lines[] = sprintf('- instead of "%s": %s', $instead['query'], implode(', ', $instead['ask']));
                }
            }
        } elseif ($answer['mode'] === 'page') {
            $result = $answer['results'][0];
            $lines[] = '';
            $lines[] = '## ' . $result['title'];
            $lines[] = sprintf('%s · %s · %s', $result['document'], $result['documentVersion'], $result['url']);
            $lines[] = '';
            $lines[] = $result['content'];
        } else {
            // The search ran over a table of contents. So a result matched on
            // everything except the word that names the subject is one of these
            // six and not an answer. Said per result, because that is where the
            // caller reads it (`R-DOC-002`).
            $lines[] = 'Matched against page titles, their headings, section paths and what a manual declares '
                . 'by name, never the text of a page. A declared property, class, method or console command is '
                . 'offered for a query word written the way code is, or for a query that is nothing but its name.';
            $covered = array_map(
                static fn(array $result): float => (float) ($result['coverage'] ?? 0.0),
                $answer['results'],
            );
            // The share is on every result; this is the sentence, because a
            // number in a payload is not a warning. The answer that reads as
            // "the manual has nothing on this" is the one of six results. Each
            // of them carries one word of the question (`D-ANS-051`).
            if ($covered !== [] && max($covered) < self::COVERS_THE_QUESTION) {
                $lines[] = sprintf(
                    'Nothing found covers half of a query asked: the best carries %d%% of its weight.',
                    (int) round(max($covered) * 100),
                );
                $lines[] = 'These pages carry words of the question rather than its subject; ask again with the subject alone.';
            }
            foreach ($answer['results'] as $result) {
                $lines[] = '';
                $lines[] = '## ' . $result['title'];
                $lines[] = sprintf('%s · %s · %s', $result['document'], $result['documentVersion'], $result['url']);
                $lines[] = sprintf(
                    'Matched on: %s — covers %d%% of the query.',
                    implode(', ', array_map(
                        static fn(array $matched): string => $matched['term'] . ' (' . $matched['field'] . ')',
                        $result['matched'],
                    )),
                    (int) round((float) ($result['coverage'] ?? 0.0) * 100),
                );
                if ($result['excerpt'] !== '') {
                    $lines[] = $result['excerpt'];
                }
            }
        }

        return ToolResult::create(implode("\n", $lines), $answer);
    }

    /**
     * The bare names behind a query that reads as code, per query that does.
     *
     * The manual titles a page after the thing it documents, a TypoScript
     * property, a ViewHelper. This index is those titles and the section paths
     * under them. A reporter writes the identifier instead, because that is
     * what the stack trace gave them. `stdWrap_override` is where the code says
     * it, `override` is where the manual does. A session settled Forge #81619
     * with that reduction itself and said so, the feedback of 2026-08-05.
     *
     * Only on a miss. A hit needs no advice, and a query that carries no
     * identifier gets nothing rather than a guess dressed as one.
     *
     * @param array<int, string> $queries
     * @return array<int, array{query: string, ask: array<int, string>}>
     */
    private static function insteadOf(array $queries): array
    {
        $insteadOf = [];
        foreach ($queries as $query) {
            // One word, and one that a sentence would not contain: a separator
            // the language uses, or a hump or underscore that joins two words.
            $word = trim($query);
            if (preg_match('/\s/', $word) === 1) {
                continue;
            }
            $word = (string) preg_replace('/\(\s*\)$/', '', $word);
            $tail = (string) preg_replace('/^.*[\\\\:>-]/', '', $word);
            if ($tail === '' || preg_match('/[a-z0-9]_[a-zA-Z]|[a-z0-9][A-Z]|[\\\\:>-]/', $word) !== 1) {
                continue;
            }

            // Every candidate is a substring of what the caller typed, never a
            // word derived from it. A split on humps as well would turn
            // `getByTag` into "tag" and a ViewHelper into "decode". That is a
            // suggestion nothing supports, in the voice of a read. The
            // underscore is the one join that carries a TYPO3 convention.
            // `stdWrap_override` is the method behind the `override` property,
            // and the manual titles the property. The underscore has to join a
            // method-shaped name to mean this. `stdWrap_override` is one,
            // `tt_content` is a table and its half is not a property anybody
            // documents.
            $property = preg_match('/^(.*[a-z0-9][A-Z][^_]*)_([^_]+)$/', $tail, $method) === 1 ? $method[2] : null;

            $ask = [];
            foreach ([$tail, $property] as $candidate) {
                $candidate = (string) $candidate;
                if ($candidate !== '' && $candidate !== $word && !in_array($candidate, $ask, true)) {
                    $ask[] = $candidate;
                }
            }
            if ($ask !== []) {
                $insteadOf[] = ['query' => $query, 'ask' => $ask];
            }
        }

        return $insteadOf;
    }
}
