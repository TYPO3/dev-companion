<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Knowledge\Hints;
use TYPO3\DevCompanion\Knowledge\Scope;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Result\MatchedHints;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\VersionScope;

/**
 * Hints for TYPO3 core paths or task topics, grouped by section.
 */
final class HintLookup extends ReadOnlyTool
{
    /**
     * The most hints one call answers with, and the ceiling `limit` is taken at.
     *
     * A brief matches a second time at this number to name what its own slice
     * dropped (`R-GUI-012`), so what it points at is what this tool would hand
     * back rather than a longer list nothing answers with.
     */
    public const MAX_HINTS = 10;

    public static function name(): string
    {
        return 'typo3_hint_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Knowledge];
    }

    public static function description(): string
    {
        return 'Return hints for TYPO3 core paths or task topics, grouped by section. A hint is a convention at the code itself — what to write at this path, which idiom a subsystem is written in, what a finding on it costs. A procedure carried out in steps is typo3_rule_lookup instead: the commit message conventions, the changelog entry a change owes, the Gerrit push and amend workflow, setting an extension manual up. Where the paths read as a project or third-party extension the hints still come back, because the conventions transfer. The "Backend CSS" and "Backend TypeScript and JavaScript" sections describe the TYPO3 backend interface and are withheld, with the reason, where the task names the frontend.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'paths' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'File paths related to the task, as they are in the repository they belong to. Each is placed on its own, so a core path and an extension path in one call are matched separately, and a statement is labelled where it obliges the other one.'],
                'task' => ['type' => 'string', 'description' => 'Short task description or topic, in English. A symptom is a query this takes as readily as a subject: a hint is searched by its own statements, and a phrase it was indexed under reaches it from the layer that explains the failure rather than the one the failure showed in. Matching is lexical against English text, so another language reaches only the loanwords.'],
                'id' => ['type' => 'string', 'description' => 'Ask for one hint by its id, for example language-files, instead of matching. Every answer lists the ids it did not return, so a subject a query missed can be requested by name rather than guessed at in other words.'],
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version the answer has to hold for, for example "13.4" or "14". Statements that do not hold there are left out, including those the repository needs for another major it declares. Defaults to every major this repository declares typo3/cms-core for, or to the installation this server was started in where there is no declaration; where there is neither, nothing is filtered and every statement carries the versions it holds for.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => self::MAX_HINTS, 'default' => 6, 'description' => 'Maximum number of hints.'],
                'availableHints' => ['type' => 'boolean', 'default' => false, 'description' => 'Ask for the index of neighbouring ids on a call that names an id. It is withheld there by default: a caller naming an id has already chosen, and the list was two thirds of what such an answer carried. A call that matches by paths or task carries it either way, and so does an id that matched nothing.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'task' => Schema::nullableString(),
            'paths' => Schema::listOf(Schema::string()),
            'scopes' => Schema::scopes('Which kind of work each path is. Paths of different scope are matched separately, so a hint that came back for one of them is about that path.'),
            'targetVersion' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major this repository runs — stated by the caller, or read from the installation. Null means nothing was filtered and every statement carries its own range. Where the repository serves several majors, targetVersions is what the answer holds for.'],
            'targetVersions' => Schema::listOf(['type' => 'integer'], 'Every TYPO3 major the answer holds for. One entry is the ordinary case. Several mean this repository declares typo3/cms-core for more than one of them, so a statement was kept when it holds on any — and where two statements about the same subject differ, the difference is the constraint the code lives under rather than drift. Empty when nothing was filtered by version.'),
            'domains' => Schema::listOf(Schema::string(), 'Hints outside these domains are returned only where the task spells out a phrase one of them was indexed under and no hint inside them claims it, which is how a symptom reaches the layer that explains it.'),
            'withheldCategories' => Schema::listOf(Schema::string(), 'Categories that matched the domains but were left out because the task names the frontend. "Backend CSS" and "Backend TypeScript and JavaScript" describe the TYPO3 backend interface and are wrong advice for what a website renders; see docs.typo3.org for frontend theming.'),
            'hints' => Schema::listOf(Schema::hintRecord()),
            'bestCoverage' => ['type' => ['number', 'null'], 'description' => 'How much of the task the closest hint above carries, between 0 and 1, where 1 is every word of it. A hint answers on its own from 0.5; below that it was returned because it claims one of the paths you named or a phrase somebody anticipated, which is a different thing from being about the question. Six well-formed hints that all got in that way read exactly like six that answer you, and this is what tells them apart. Null on a call that named an id, which is not a guess at anybody\'s words.'],
            'availableHints' => Schema::listOf(Schema::hintReference(), 'The hints that exist in the searched domains, minus the ones returned above, closest first: what the limit cut stands before what matched too little to return. That order is the matcher\'s, so it holds where a query was matched — which is every call except one that names an id. An id that matched nothing lists every id there is, in corpus order. An id that matched carries this empty unless the call asked for it, and availableHintsWithheld says how many were left out.'),
            'availableHintsWithheld' => ['type' => 'integer', 'description' => 'How many neighbouring ids were left out of availableHints. Non-zero only on a call that named an id and matched one without asking for the index; pass availableHints true to receive them.'],
            'documents' => Schema::listOf(Schema::object([
                'uri' => Schema::string(),
                'hint' => Schema::string('The returned hint this document is the long form of.'),
            ], ['uri', 'hint']), 'Knowledge documents declaring themselves the long form of a hint above. A hint is the convention in short; the document is the same subject at length, and where it hands over a file it is the file itself.'),
        ], ['paths', 'domains', 'withheldCategories', 'scopes', 'hints', 'bestCoverage', 'availableHints', 'availableHintsWithheld', 'documents']);
    }

    /**
     * How much of the query the closest returned hint carries, or null where
     * nothing was matched against words at all.
     *
     * Read off what the matcher already recorded per hit rather than scored
     * again — `D-ANS-115` put it there for the probe, and this is the same
     * number in the answer. The unmatched words of the query were the other
     * candidate and are worse: a term is weighed by how few hints carry it, so
     * the rarest unmatched word of a real query was "via".
     *
     * @param array<int, array<string, mixed>> $hints
     */
    private static function bestCoverage(array $hints): ?float
    {
        $best = null;
        foreach ($hints as $hint) {
            $on = $hint['matchedOn'] ?? null;
            if (!is_array($on) || !is_float($on['coverage'] ?? null)) {
                continue;
            }
            $best = max($best ?? 0.0, $on['coverage']);
        }

        return $best === null ? null : round($best, 2);
    }

    public static function answer(array $args): ToolResult
    {
        $paths = array_map('strval', $args['paths'] ?? []);
        $task = isset($args['task']) ? (string) $args['task'] : null;
        $limit = (int) ($args['limit'] ?? 6);
        $id = isset($args['id']) ? trim((string) $args['id']) : '';
        $index = (bool) ($args['availableHints'] ?? false);
        $stated = isset($args['targetVersion']) ? (string) $args['targetVersion'] : null;
        $target = Versions::target($stated);
        $targets = Versions::targets($stated);

        // Paths of different scope are asked separately, so a statement that
        // declares whose it is can be labelled against the paths it was matched
        // for. Matched together, an extension path would be answered under the
        // core path's scope.
        $scopes = Scope::ofEach($paths, $task ?? '');
        $groups = Scope::groups($paths, $scopes, $task ?? '');
        $outside = Scope::pathsOf($scopes, Scope::Project, Scope::Extension);
        $outsideCore = count($groups) === 1 && $groups[0]['scope']->isOutsideTheCore();

        $found = [];
        foreach ($groups as $group) {
            $matched = Hints::find($group['paths'], $task ?? '', $limit, $id, $targets, $index);
            $found[] = ['scope' => $group['scope'], 'paths' => $group['paths'], 'result' => $matched];
        }
        $result = MatchedHints::merged($found);

        $lines = [];
        if ($outsideCore) {
            $lines[] = Scope::OUTSIDE_CORE_NOTICE . ' The hints below are conventions that may transfer. '
                . 'typo3_server_scope states the boundary.';
            $lines[] = '';
        } elseif ($outside !== []) {
            $lines[] = Scope::outsideCoreAmong($outside) . ' The conventions matched there transfer.';
            $lines[] = '';
        }
        if (Scope::pathsOf($scopes, Scope::Uncertain) !== []) {
            $lines[] = Scope::UNCERTAIN_NOTICE;
            $lines[] = '';
        }
        if ($result['withheldCategories'] !== []) {
            $lines[] = sprintf(
                'This task names the frontend, so %s is withheld: it describes the TYPO3 backend interface — its '
                . 'Sass sources, its --typo3-* properties, its color schemes — and would be inverted advice for '
                . 'what a website renders. Frontend theming: https://docs.typo3.org. Name the backend in the task '
                . 'if you are styling a backend module, or the styleguide if the work is a backend component and '
                . 'its demo.',
                implode(' and ', $result['withheldCategories']),
            );
            $lines[] = '';
        }
        if ($id !== '') {
            $lines[] = 'Hint requested by id: ' . $id;
        }
        if ($task !== null && $task !== '') {
            $lines[] = 'Task: ' . $task;
        }
        if ($paths !== []) {
            $lines[] = "Paths:\n" . implode("\n", array_map(
                static fn(array $entry): string => '- ' . $entry['path']
                    . ($entry['scope'] === Scope::Core ? '' : ' (' . $entry['scope']->value . ')'),
                $scopes,
            ));
        }
        $lines[] = VersionScope::line($targets);
        if ($result['domains'] !== []) {
            $lines[] = 'Domains: ' . implode(', ', $result['domains'])
                . ' (a hint outside these domains is shown only where the task names its own vocabulary'
                . ($result['withheldCategories'] === []
                    ? ')'
                    : ', and ' . implode(' and ', $result['withheldCategories']) . ' was withheld inside them)');
        }
        $lines[] = '';
        $lines[] = 'Hints:';

        // Before the hints rather than after them, because it is what says how
        // to read them — `D-ANS-130`. A caller that got six adjacent hints and
        // no sign of the miss spent three calls establishing that none of them
        // was about the question.
        $coverage = self::bestCoverage($result['matchedHints']);
        if ($coverage !== null && $coverage < Hints::MIN_COVERAGE) {
            $lines[] = sprintf(
                'The closest of these carries %d%% of your question. Each got here by claiming a path you named or '
                . 'a phrase this corpus anticipated, rather than by being about what you asked — so read them as '
                . 'the nearest subjects rather than as the answer, and search the official manual with '
                . 'typo3_documentation_lookup where none of them is it.',
                (int) round($coverage * 100),
            );
            $lines[] = '';
        }

        if ($result['matchedHints'] !== []) {
            // One block per scope, and the heading only where there is more
            // than one of them: the caller asked about two repositories, and
            // which half of the answer is about which path is the answer.
            $sectionTexts = [];
            foreach ($found as $group) {
                if ($group['result']['matchedHints'] === []) {
                    continue;
                }
                if (count($found) > 1) {
                    $sectionTexts[] = sprintf(
                        '# For %s%s',
                        implode(' and ', $group['paths']),
                        $group['scope'] === Scope::Core ? '' : ' — ' . $group['scope']->value,
                    );
                }
                $sectionTexts[] = MatchedHints::sections(
                    $group['result']['matchedHints'],
                    $group['scope'],
                    $target,
                );
            }
            $lines[] = implode("\n\n", $sectionTexts);
        } elseif ($result['withheldCategories'] !== []) {
            $lines[] = 'Nothing is left to show: the only domain this task touched is one this server answers for '
                . 'the backend alone.';
        } elseif ($id !== '') {
            $lines[] = sprintf('There is no hint with the id "%s".', $id);
        } else {
            $lines[] = 'No hint matched. Name a path or a more specific topic, or ask for one of the ids below.';
        }

        // The long form of a returned hint, where a document declares itself as
        // one — `D-KNW-057`. Declared on the document rather than written into
        // every hint that has one, so the crossing `D-KNW-008` describes is one
        // statement instead of a sentence per cell.
        $expanding = [];
        foreach ($result['matchedHints'] as $hint) {
            foreach (Documents::forHint($hint['id']) as $document) {
                $expanding[$document['id']] = $hint['id'] . ' — ' . $document['title'];
            }
        }
        if ($expanding !== []) {
            $lines[] = '';
            $lines[] = 'Read at length, as a resource:';
            foreach ($expanding as $documentId => $label) {
                $lines[] = '- ' . Documents::uri($documentId) . ' (' . $label . ')';
            }
        }

        // The index is the difference between "nothing matched your words" and
        // "nobody wrote this down". Without it both answers read the same, and
        // the caller tries another phrasing for a subject that does not exist —
        // or gives up on one that does. It is carried on an answer that matched
        // as well, because a match is a guess at the caller's words: three
        // hints about something else read as a subject nobody wrote down, and
        // that answer has not even an empty result to be read as one.
        //
        // The order is the matcher's, so the first entry is the one the limit
        // cut, and the copy says so: a list read as a catalogue is a list
        // nobody reads past (`D-ANS-075`).
        if ($result['availableHints'] !== []) {
            $lines[] = '';
            $lines[] = match (true) {
                $result['matchedHints'] === [] && $id !== '' => 'The ids there are:',
                $result['matchedHints'] === [] => 'Hints that exist in these domains, closest first, requestable by id:',
                $id !== '' => 'The hints alongside it, requestable by id:',
                default => 'What matched above is a guess at your words. The rest of these domains, closest first, requestable by id:',
            };
            foreach ($result['availableHints'] as $entry) {
                $lines[] = '- ' . $entry['id'] . ' — ' . $entry['title'] . ' (' . $entry['category'] . ')';
            }
        }

        // What was left out is counted either way (`R-ANS-030`), because a
        // caller cannot ask for a list it was never told about.
        if ($result['availableHintsWithheld'] > 0) {
            $lines[] = '';
            $lines[] = sprintf(
                '%d more hints stand beside this one in its domains. They are left out because you named an id '
                . 'rather than searched; call again with availableHints true to list them.',
                $result['availableHintsWithheld'],
            );
        }

        return ToolResult::create(implode("\n", $lines), [
            'task' => $task === '' ? null : $task,
            'paths' => array_values($paths),
            'scopes' => $scopes,
            'targetVersion' => $target,
            'targetVersions' => $targets,
            'domains' => $result['domains'],
            'withheldCategories' => $result['withheldCategories'],
            'hints' => MatchedHints::records($result['matchedHints']),
            'bestCoverage' => $coverage,
            'availableHints' => $result['availableHints'],
            'availableHintsWithheld' => $result['availableHintsWithheld'],
            'documents' => array_map(
                static fn(string $documentId): array => [
                    'uri' => Documents::uri($documentId),
                    'hint' => strtok($expanding[$documentId], ' '),
                ],
                array_keys($expanding),
            ),
        ]);
    }
}
