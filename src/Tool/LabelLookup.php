<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Labels;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Result\Miss;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;
use TYPO3\DevCompanion\Search\LabelSearch;

/**
 * Labels registered in the installation or kept in project site configuration.
 *
 * The console searches the packages it has active, which is what makes the
 * answer right: a project extension's labels are in it, and so are the resource
 * overrides the installation applies. Neither follows from a core checkout, and
 * neither could be shipped as a snapshot.
 */
final class LabelLookup extends ReadOnlyTool
{
    /**
     * What `language:domain:search` prints instead of a payload when nothing
     * matched — the one exit-0-without-JSON that is an answer.
     *
     * Read in `.checkouts/14.3` and `.checkouts/main`, where
     * `TranslationDomainSearchCommand` warns with this text and returns
     * SUCCESS; the command does not exist before 14.0, and there the console
     * exits non-zero. Matching the text rather than the exit code fails in the
     * safe direction: if the wording moves, an empty result reads as nothing
     * established, which costs a fallback rather than an answer that is wrong.
     */
    private const NOTHING_MATCHED = 'No language resource files found';

    /**
     * The source-language rule, carried here because this is where the caller
     * still is when it decides — `R-ANS-015`.
     *
     * Both sessions that wrote German into a source XLF had called this tool.
     * Neither called `typo3_task_guide`, whose `labels` intent holds the same
     * rule, and neither asked for a hint: every other route opens on the word
     * label, which a session describing its work as a content element has no
     * reason to type.
     */
    private const SOURCE_LANGUAGE = "\n\nWrite a new trans-unit in English in the unprefixed source file, and put any "
        . 'other wording in the locale-prefixed file beside it — de.locallang.xlf for locallang.xlf — under the same '
        . 'unit id. A source file that is not English is a defect to correct in place rather than a convention to '
        . 'continue, and adding an en.-prefixed file is not that correction: typo3_hint_lookup with '
        . 'id=language-files has what it is.';

    public static function name(): string
    {
        return 'typo3_label_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation, Source::Packages];
    }

    public static function description(): string
    {
        return 'Search the labels registered in the TYPO3 installation you are working in and the XLF files below project config/sites. Reuse is local to the translation resource already used at the consuming code: pass resource whenever it is known, and do not reference a match from another module or package merely because its text is identical. The console answers with the resource overrides the installation applies; the files supply an answer when it cannot be reached and report non-standard names or resources with no static reference. Every match comes back as a translation domain reference; computing that reference for a file this installation does not have, one a patch is about to add, is typo3_translation_domain_lookup.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'minLength' => 1, 'description' => 'Words from the label text or its trans-unit id, for example "save document" or "labels.title". Several words are matched independently, ignoring case and order: a label has to carry every one of them, in its text or in its id. When none carries all of them, the answer says how far each word reaches on its own.'],
                'extension' => ['type' => 'string', 'description' => 'Restrict the search to the extension that owns the consuming code.'],
                'resource' => ['type' => 'string', 'description' => 'Restrict the search to the exact XLF resource already used at the consuming code, for example "EXT:my_sitepackage/Resources/Private/Language/Backend/Import.xlf". A match from another resource is not a reuse candidate. Where no label in it reaches the query, the answer names the resources that do hold one, so a path that was guessed can be replaced by one that exists.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 200, 'default' => 25, 'description' => 'Maximum number of labels to return.'],
            ],
            'required' => ['query'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'query' => Schema::string(),
            'matchCount' => Schema::integer(),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
            'terms' => Schema::termCounts('How many labels each word of the query reaches on its own, inside the extension and the resource that were asked for — where to narrow when the query as a whole reaches none. A label answers the query only by carrying every word.'),
            'termCountsWithoutTheNarrowing' => Schema::termCounts('The same words counted outside the resource, inside the extension that was asked for or derived from it. Returned only where a word reaches there and nothing inside the resource, which makes the resource what emptied this answer rather than the words.'),
            'resources' => Schema::listOf(Schema::string(), 'The resources holding a label that carries every word of the query. Returned where a resource was asked for and no label at all in it reaches the query, so a path that was guessed can be replaced by one that exists. Empty means no resource holds such a label.'),
            'resourceDiagnostics' => Schema::listOf(Schema::object([
                'resource' => Schema::string('The XLF resource this diagnosis describes.'),
                'location' => Schema::string('Where it was found: package, site-set, or project-site.'),
                'conventionalName' => ['type' => 'boolean', 'description' => 'Whether the file follows the naming convention for its location.'],
                'referenced' => ['type' => 'boolean', 'description' => 'Whether an implicit or static reference was found.'],
                'references' => Schema::listOf(Schema::string(), 'Source files that name the resource. A conventional site-set labels.xlf names its adjacent config.yaml as an implicit reference.'),
                'warnings' => Schema::listOf(Schema::string(), 'Naming, discovery, and static-reference warnings for this resource.'),
            ], ['resource', 'location', 'conventionalName', 'referenced', 'references', 'warnings'])),
            'labels' => Schema::listOf(Schema::object([
                'ref' => Schema::string('The reusable label reference: a translation domain for package labels or an LLL file reference for project-site labels.'),
                'domain' => Schema::string('The translation domain, empty for a project-site XLF that TYPO3 does not register as a package resource.'),
                'key' => Schema::string('The trans-unit id.'),
                'source' => Schema::string('The label text in the searched locale.'),
                'resource' => Schema::string('The XLF file it lives in.'),
            ], ['ref', 'domain', 'key', 'source'])),
        ], ['query', 'resource', 'matchCount', 'answeredBy', 'terms', 'labels'], ['query']);
    }

    public static function answer(array $args): ToolResult
    {
        $query = trim((string) ($args['query'] ?? ''));
        $extension = trim((string) ($args['extension'] ?? ''));
        $resource = trim((string) ($args['resource'] ?? ''));
        $limit = (int) ($args['limit'] ?? 25);
        $terms = LabelSearch::terms($query);

        if ($extension === '' && preg_match('#^EXT:([^/]+)/#', $resource, $matches) === 1) {
            $extension = $matches[1];
        }
        $arguments = ['language:domain:search', LabelSearch::consoleOption($terms), '--json', '--crop=0'];
        if ($extension !== '') {
            $arguments[] = '--extension=' . $extension;
        }

        $answer = Typo3Cli::json($arguments);

        // The console prints a warning instead of a payload when nothing
        // matched, and exits successfully while doing it. That is an
        // installation that answered "none", not one that could not be asked —
        // and the difference decides whether the caller refines the query or
        // goes looking for a console that is not broken.
        //
        // The exit code cannot draw that line on its own, and reading it alone
        // put every other exit-0-without-payload on the "none" side. Nobody has
        // established what else lands there — the two obvious carriers do not:
        // Xdebug's connection warning goes to stderr, and a PHP deprecation is
        // swallowed by TYPO3's own error handler before it can print. What is
        // measured is the tool's side of it: four different stdouts, one of
        // them carrying an intact payload the decoder missed, all answered "no
        // label carries these words". An exit code of 0 certifies nothing about
        // the stream, so the warning is what says "none", and everything else
        // without a payload is nothing established.
        $establishedNone = $answer['exitCode'] === 0
            && str_contains($answer['output'], self::NOTHING_MATCHED);

        $fileCandidates = Labels::all($extension);
        $fileResources = [];
        foreach ($fileCandidates as $label) {
            $fileResources[$label['resource']] = $label;
        }

        $answeredBy = 'installation';
        $candidates = [];
        if (!is_array($answer['data']) && !$establishedNone) {
            // The labels are in the packages' files whether or not the console
            // boots, and it needs a migrated database to boot. A weaker answer
            // beats none, as long as it says which one it is.
            $candidates = $fileCandidates;
            if ($candidates === []) {
                return Unsupported::because(
                    self::whyNothingWasEstablished($answer),
                    ['query' => $query],
                );
            }
            $answeredBy = 'packages';
        }

        /** @var array<string, mixed> $data */
        $data = is_array($answer['data']) ? $answer['data'] : [];
        foreach ($data['items'] ?? [] as $item) {
            foreach ($item['labels'] ?? [] as $label) {
                $candidate = [
                    'ref' => (string) $label['domain'] . ':' . (string) $label['reference'],
                    'domain' => (string) $label['domain'],
                    'key' => (string) $label['reference'],
                    'source' => (string) $label['label'],
                    'resource' => (string) ($item['resource'] ?? ''),
                    'origin' => 'installation',
                ];
                $metadata = $fileResources[$candidate['resource']] ?? null;
                if (is_array($metadata)) {
                    $candidate += [
                        'conventionalName' => $metadata['conventionalName'],
                        'references' => $metadata['references'],
                        'location' => $metadata['location'],
                    ];
                }
                $candidates[] = $candidate;
            }
        }
        if (is_array($answer['data']) || $establishedNone) {
            foreach ($fileCandidates as $label) {
                if ($label['location'] === 'project-site') {
                    $label['origin'] = 'packages';
                    $candidates[] = $label;
                }
            }
        }
        $candidates = self::uniqueLabels($candidates);

        // Kept, because a miss cannot say whether the resource or the words
        // emptied it once the resource has taken the labels away — `D-ANS-016`.
        $beforeTheResource = $candidates;
        if ($resource !== '') {
            $candidates = array_values(array_filter(
                $candidates,
                static fn(array $label): bool => $label['resource'] === $resource,
            ));
        }

        // The console returned everything carrying any of the words; the query
        // asked for the labels carrying all of them.
        $labels = LabelSearch::carryingEvery($candidates, $terms);
        $termCounts = LabelSearch::perTermCounts($candidates, $terms);

        $total = count($labels);
        $shown = array_slice($labels, 0, $limit);
        if ($shown !== [] && array_filter(
            $shown,
            static fn(array $label): bool => ($label['origin'] ?? 'packages') === 'installation',
        ) === []) {
            $answeredBy = 'packages';
        }
        $diagnostics = self::resourceDiagnostics($shown !== [] ? $shown : ($resource !== '' ? $candidates : []));
        $instance = Instance::describe();

        $fromFiles = $answeredBy === 'packages' ? sprintf(
            "\n\nRead from the XLF files of the installed packages and project site configuration: %s (%s). "
            . 'What that leaves out is the assembled runtime state — a label an installation replaces through '
            . 'LANG/resourceOverrides is shown here as its package ships it.',
            $answer['exitCode'] !== 0 ? 'the console could not be asked' : 'the console settled nothing',
            self::whyNothingWasEstablished($answer),
        ) : '';
        $fromProjectSites = $answeredBy === 'installation'
            && array_filter(
                $shown,
                static fn(array $label): bool => ($label['location'] ?? '') === 'project-site',
            ) !== []
            ? "\n\nProject-site XLF files were read beside the console result because TYPO3 does not enumerate config/sites as package labels."
            : '';
        $diagnosticText = self::diagnosticText($diagnostics);
        $reuseBoundary = $resource === ''
            ? "\n\nA match is reusable only when its resource is the one already used at the consuming code. "
                . 'A label from another module or package is not a shared vocabulary merely because its text matches; '
                . 'call again with resource once that usage context is known.'
            : "\n\nSearch restricted to the translation resource used at the consuming code: " . $resource . '.';

        if ($shown === []) {
            $lines = [sprintf(
                'No label in %s %s. This is an answer about your installation rather than about TYPO3 in general.',
                $resource !== '' ? $resource : ($instance['root'] ?? 'the installation'),
                count($terms) > 1 ? 'carries all of ' . LabelSearch::quoted($terms) : sprintf('matches "%s"', $query)
            )];

            // Every count above was taken after the resource narrowed the
            // labels, so a path that names nothing at all reports every word at
            // 0 — which is what `perTermCounts()` reserves for a word that was
            // misspelled. A session read that as "this resource holds no such
            // label", concluded the label had to be written, and the file it
            // had guessed the name of held it. So where a word reaches outside
            // the resource and nothing inside it, the resource is what emptied
            // this and that is the first sentence — `D-ANS-016`, one corpus
            // over from the version filter of typo3_changelog_lookup.
            $outside = [];
            $elsewhere = [];
            if ($resource !== '' && $terms !== []) {
                $inside = array_column($termCounts, 'matchCount', 'term');
                $reaching = array_values(array_filter(
                    LabelSearch::perTermCounts($beforeTheResource, $terms),
                    static fn(array $term): bool => $term['matchCount'] > 0,
                ));
                $emptied = array_filter(
                    $reaching,
                    static fn(array $term): bool => ($inside[$term['term']] ?? 0) === 0,
                );
                $outside = $emptied === [] ? [] : $reaching;
                $elsewhere = array_values(array_unique(array_filter(array_column(
                    LabelSearch::carryingEvery($beforeTheResource, $terms),
                    'resource',
                ))));
                sort($elsewhere);
            }
            if ($outside !== []) {
                $lines[] = '';
                $lines[] = sprintf(
                    'Narrowed to that resource — it is what emptied this, not the words: %s%s. %s',
                    $extension === '' ? 'without it, ' : sprintf('in extension "%s" without it, ', $extension),
                    Miss::reaching($outside, 'label', 'labels'),
                    $extension === '' ? 'Ask again without it.' : sprintf(
                        'Ask again with extension "%s" and no resource.',
                        $extension,
                    ),
                );
            }
            // The resources that do hold what was asked for, where the one
            // named holds nothing at all. A guessed path is a segment or a
            // plural away from a file that is there, so what replaces it is a
            // list of the ones that are — `D-ANS-016`.
            if ($resource !== '' && $candidates === []) {
                $lines[] = '';
                $lines[] = $elsewhere === []
                    ? 'No other resource holds one either.'
                    : 'The resources that do hold one:';
                foreach ($elsewhere as $held) {
                    $lines[] = '- ' . $held;
                }
                $lines[] = 'A path that exists nowhere answers exactly like a resource holding no match, so check the '
                    . 'one you named before adding a label to it.';
            }

            $narrowing = self::narrowing($extension, $resource);
            $reached = array_values(array_filter($termCounts, static fn(array $t): bool => $t['matchCount'] > 0));
            if (count($terms) > 1 && $reached !== []) {
                $lines[] = '';
                $lines[] = ($narrowing === [] ? 'On its own, ' : sprintf('Inside %s, on its own, ', implode(' and ', $narrowing)))
                    . Miss::reaching($reached, 'label', 'labels')
                    . ($outside === [] ? ' — ask again with the one that narrows best.' : '.');
            }

            $data = [
                'query' => $query,
                'resource' => $resource === '' ? null : $resource,
                'matchCount' => 0,
                'labels' => [],
                'terms' => $termCounts,
                'resourceDiagnostics' => $diagnostics,
                'answeredBy' => $answeredBy,
            ];
            // Each field is present where it was computed and absent where
            // there was nothing to compute it against, so which of the two
            // carries a count is what says which side of the resource it was
            // taken on — `R-ANS-002`, for the client that renders the data and
            // drops the text.
            if ($outside !== []) {
                $data['termCountsWithoutTheNarrowing'] = $outside;
            }
            if ($resource !== '' && $candidates === []) {
                $data['resources'] = $elsewhere;
            }

            return ToolResult::create(
                implode("\n", $lines) . $reuseBoundary . self::SOURCE_LANGUAGE . $fromFiles
                    . $fromProjectSites . $diagnosticText,
                $data,
            );
        }

        $lines = [sprintf(
            '%d label(s) in %s match "%s"%s:',
            $total,
            $resource !== '' ? $resource : ($instance['root'] ?? '?'),
            $query,
            $total > count($shown) ? sprintf(' — showing the first %d', count($shown)) : ''
        )];
        foreach ($shown as $label) {
            $lines[] = '- ' . $label['ref'];
            $lines[] = '  "' . $label['source'] . '"';
            $lines[] = '  ' . $label['resource'];
        }
        $lines[] = '';
        $lines[] = 'Reference a label by the ref shown first. Package resources use a translation domain; '
            . 'project-site resources use the full LLL file reference.';

        return ToolResult::create(implode("\n", $lines) . $reuseBoundary . self::SOURCE_LANGUAGE . $fromFiles
            . $fromProjectSites . $diagnosticText, [
                'query' => $query,
                'resource' => $resource === '' ? null : $resource,
                'matchCount' => $total,
                'labels' => array_map(self::publicLabel(...), $shown),
                'terms' => $termCounts,
                'resourceDiagnostics' => $diagnostics,
                'answeredBy' => $answeredBy,
            ]);
    }

    /**
     * @param array<int, array<string, mixed>> $labels
     * @return array<int, array<string, mixed>>
     */
    private static function uniqueLabels(array $labels): array
    {
        $unique = [];
        foreach ($labels as $label) {
            $identity = (string) ($label['resource'] ?? '') . "\0" . (string) ($label['key'] ?? '');
            $unique[$identity] ??= $label;
        }

        return array_values($unique);
    }

    /**
     * @param array<string, mixed> $label
     * @return array{ref: string, domain: string, key: string, source: string, resource: string}
     */
    private static function publicLabel(array $label): array
    {
        return [
            'ref' => (string) $label['ref'],
            'domain' => (string) $label['domain'],
            'key' => (string) $label['key'],
            'source' => (string) $label['source'],
            'resource' => (string) $label['resource'],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $labels
     * @return array<int, array{
     *     resource: string,
     *     location: string,
     *     conventionalName: bool,
     *     referenced: bool,
     *     references: array<int, string>,
     *     warnings: array<int, string>
     * }>
     */
    private static function resourceDiagnostics(array $labels): array
    {
        $diagnostics = [];
        foreach ($labels as $label) {
            if (!isset($label['conventionalName'], $label['references'], $label['location'])) {
                continue;
            }
            $resource = (string) $label['resource'];
            if (isset($diagnostics[$resource])) {
                continue;
            }
            $references = array_values(array_map('strval', (array) $label['references']));
            $location = (string) $label['location'];
            $warnings = [];
            if ($location === 'project-site') {
                $warnings[] = 'TYPO3 does not register XLF files below config/sites automatically; keep an explicit LLL reference to this resource.';
            }
            if (!$label['conventionalName']) {
                $warnings[] = $location === 'package'
                    ? 'The conventional package language file name is locallang.xlf or locallang_<subject>.xlf.'
                    : 'The conventional site label file name is labels.xlf.';
            }
            if ($references === []) {
                $warnings[] = 'No static reference to this resource was found; references assembled at runtime are outside this scan.';
            }
            $diagnostics[$resource] = [
                'resource' => $resource,
                'location' => $location,
                'conventionalName' => (bool) $label['conventionalName'],
                'referenced' => $references !== [],
                'references' => $references,
                'warnings' => $warnings,
            ];
        }
        ksort($diagnostics);

        return array_values($diagnostics);
    }

    /** @param array<int, array{resource: string, warnings: array<int, string>}> $diagnostics */
    private static function diagnosticText(array $diagnostics): string
    {
        $lines = [];
        foreach ($diagnostics as $diagnostic) {
            foreach ($diagnostic['warnings'] as $warning) {
                $lines[] = '- ' . $diagnostic['resource'] . ': ' . $warning;
            }
        }

        return $lines === [] ? '' : "\n\nResource warnings:\n" . implode("\n", $lines);
    }

    /**
     * The axes a count was taken inside, as a miss names them back.
     *
     * Both of them, because the console is asked for one extension and the
     * resource narrows what it answered: a number taken inside either reads as
     * a fact about the installation otherwise.
     *
     * @return array<int, string>
     */
    private static function narrowing(string $extension, string $resource): array
    {
        $narrowing = [];
        if ($extension !== '') {
            $narrowing[] = sprintf('extension "%s"', $extension);
        }
        if ($resource !== '') {
            $narrowing[] = sprintf('resource "%s"', $resource);
        }

        return $narrowing;
    }

    /**
     * Why the console settled neither the labels nor their absence.
     *
     * Two failures arrive here and they send the caller to different places.
     * One is a console that could not be run, and its own message is the
     * reason. The other ran, exited 0 and printed something that is neither
     * the payload nor the warning it prints when nothing matched — there is no
     * message to pass on, and "could not be asked" would send the caller after
     * a console that is working.
     *
     * @param array{error: string, exitCode: int, output: string, ok: bool, data: mixed} $answer
     */
    private static function whyNothingWasEstablished(array $answer): string
    {
        return $answer['exitCode'] !== 0 ? $answer['error'] : 'the console exited successfully with neither a '
            . 'JSON payload nor the warning it prints when nothing matched, so nothing it printed says whether '
            . 'these labels exist';
    }
}
