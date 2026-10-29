<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Publication\Packagist;
use TYPO3\DevCompanion\Publication\Ter;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unreachable;

/**
 * What the TYPO3 Extension Repository already holds under an extension key,
 * and which published extensions match a subject.
 *
 * A release audit turns on the first and no checkout can answer it. Tailor requires
 * `ext_emconf.php` to name the version under release. So the file still names
 * it after the upload and a published repository reads like an unreleased one.
 * A session that could not see the registry reported a finding that blocks a
 * release, and a reviewer confirmed it wrongly (`D-FBK-051`). A feature task
 * turns on the second, before its first file (`D-ANS-193`).
 */
final class TerLookup extends ReadOnlyTool
{
    /** The releases come from extensions.typo3.org, a search from packagist.org as well. */
    protected const OPEN_WORLD = true;

    /**
     * Why no answer came, in the caller's terms rather than the transport's.
     */
    private const UNREACHABLE = [
        Unreachable::NOT_ANSWERING => 'The registry did not answer. It is reachable at %s in a browser; nothing '
            . 'here can answer this offline, and no bundled list of releases could be right for it.',
        Unreachable::NOT_PARSEABLE => 'Something answered with a page rather than with the API, which is what a '
            . 'portal in front of the connection looks like from here.',
    ];

    /** Where a search sends the caller next, in the text and in `owed`. */
    private const AFTER_SEARCH = [
        'A version range here is what the author declared, not evidence that the release was tested on it.',
        'Read every version of one with typo3_ter_lookup and extension set to its key.',
        'Whether to take one or build your own is typo3_hint_lookup with id="extension-reuse".',
    ];

    public static function name(): string
    {
        return 'typo3_ter_lookup';
    }

    public static function title(): string
    {
        return 'Find extensions and their published versions';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Network];
    }

    public static function description(): string
    {
        return 'Read what the TYPO3 Extension Repository has published under an extension key, live from extensions.typo3.org, or find the published extensions for a subject. Pass query with the subject, for example "news", "calendar" or "cookie consent", before you build a feature yourself. It searches extensions.typo3.org and the TYPO3 extension packages on packagist.org, because an extension can stand on Packagist alone. Each match names its Composer package, its extension key, its description, its downloads, the registries that found it, and from the TER the current version and the TYPO3 majors it declares. Pass extension with the key, the one extra.typo3/cms.extension-key declares and not the Composer package name. Every published version comes back, highest number first. That is the number, the state, the upload day, the TYPO3 majors it declares and the constraints.depends.typo3 of its release. This is the question a release audit cannot answer from the repository under audit. Tailor refuses to package unless ext_emconf.php names the version of the release. So that file still names it after the upload, and a published checkout reads exactly like one nobody has published. Pass extensionVersion as well to learn whether the registry already holds that number. It reports what the registry holds and judges no version free; the comparison against the working tree is yours. A key with nothing published under it gets that answer, which does not say that no such package exists. An extension that ships through Composer alone never registers here. What a release requires of the extension itself is typo3_hint_lookup with id="extension-ter-release". This tool reads only, with no credential. You register a key, upload a version and transfer an extension yourself, through Tailor and the token it carries.';
    }


    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'extension' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Instead of query. The extension key, for example "news" or "bootstrap_package". That is the key extra.typo3/cms.extension-key declares in the package\'s composer.json, which every TYPO3 extension package has to carry to install at all. It is not the Composer package name, which the registry does not take. "georgringer/news" and "bootstrap-package" are the two shapes that reach it as a name of the wrong kind. Lowercase letters, digits and underscores, three to thirty characters.',
                ],
                'query' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Instead of extension. The subject a feature is about, in English, for example "news", "event calendar" or "cookie consent". A Composer package name works too. Both registries match it against their own index, and the answer lists what either of them found.',
                ],
                'extensionVersion' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Beside extension only. One version number to ask about, for example "14.0.1", which is usually the one ext_emconf.php names. The answer says whether the registry holds it and, where it does, what that release declared. The comparison reads it as the registry writes it, which is exactly three numbers. A suffix of any kind belongs to no published version, because the upload route accepts none.',
                ],
                'limit' => [
                    'type' => 'integer',
                    'minimum' => 1,
                    'maximum' => 50,
                    'default' => 10,
                    'description' => 'How many versions come back, newest number first, or how many matches a search returns. The count of everything published or found comes with them, so a cut list says so. A widely maintained extension has a hundred versions, and what a release audit reads is the top of them.',
                ],
            ],
            'oneOf' => [
                ['title' => 'Read the versions under a key', 'required' => ['extension']],
                ['title' => 'Search by subject', 'required' => ['query']],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'status' => Schema::answerStatus(),
            'source' => Schema::string('The registry the answer came from, or the two a search read.'),
            'url' => Schema::string('What the call read, so you can ask the same question again by hand. Empty where the key got its answer without a read, and empty in a search, whose reads searches names.'),
            'page' => Schema::string('Where a person reads the extension\'s own page in the registry. Empty where the key is not one the registry takes, and empty in a search.'),
            'extension' => Schema::string('The key the call named, lowercased, as the call sent it. Empty in a search.'),
            'query' => Schema::string('The subject the call searched for, as the call passed it. Empty where it named a key.'),
            'extensionVersion' => Schema::string('The version number the call asked about, as the call passed it. Empty where it passed none.'),
            'held' => Schema::nullable([
                'type' => 'boolean',
                'description' => 'Whether the registry has published that exact number. Null where the call named no version, and null where the call read nothing at all. A false here is the registry\'s answer, never a question that failed. It is a fact about the registry and not a judgement that the number is free to release.',
            ]),
            'total' => Schema::integer('How many versions the registry holds under the key in total, of which versions carries at most limit. Zero where it holds none, and zero in a search, whose counts searches carries.'),
            'versions' => Schema::listOf(Schema::object([
                'number' => Schema::string('The version as the registry has it.'),
                'state' => Schema::string('What the uploader declared it as: stable, beta, alpha, experimental, test, obsolete.'),
                'uploaded' => Schema::string('The upload day, as YYYY-MM-DD in UTC. The registry writes the same moment in its own timezone, so a release made late in the day carries the day before here.'),
                'majors' => Schema::listOf(Schema::integer(), 'The TYPO3 majors the release declares it runs on, lowest first.'),
                'constraint' => Schema::string('The constraints.depends.typo3 the release declared, as ext_emconf.php wrote it — for example ">=13.4.15 <=14.3.99". Empty where the release declared none, which the registry accepts on an upload made by a controller.'),
            ], ['number', 'state', 'uploaded', 'majors', 'constraint']), 'The published versions, highest number first. That is version order and not upload order. A maintenance release on an older line sits further down and may be the most recent upload of all. The days beside the numbers say so. Empty where the registry holds nothing under the key, and empty in a search.'),
            'matches' => Schema::listOf(Schema::object([
                'package' => Schema::string('The Composer package name, which composer require takes. Empty where the TER knows the extension under its key alone.'),
                'key' => Schema::string('The extension key the TER holds it under, which extension takes in the next call. Empty where only Packagist found it, which does not say that the TER holds no key for it.'),
                'description' => Schema::string('What the package says it does.'),
                'version' => Schema::string('The newest version on the TER. Empty where the TER search did not find it.'),
                'uploaded' => Schema::string('The upload day of that version, as YYYY-MM-DD in UTC. Empty with version.'),
                'majors' => Schema::listOf(Schema::integer(), 'The TYPO3 majors that version declares, lowest first. A declaration by the author, not a test result.'),
                'constraint' => Schema::string('The constraints.depends.typo3 that version declared. Empty with version.'),
                'terDownloads' => Schema::integer('Downloads the TER counts. Zero where the TER search did not find it.'),
                'packagistDownloads' => Schema::integer('Installs Packagist counts. Zero where the Packagist search did not find it.'),
                'foundIn' => Schema::listOf(['type' => 'string', 'enum' => [Ter::HOST, Packagist::HOST]], 'The searches that found it.'),
            ], ['package', 'key', 'description', 'version', 'uploaded', 'majors', 'constraint', 'terDownloads', 'packagistDownloads', 'foundIn']), 'What a search found: the Packagist matches in its order, then what the TER found and Packagist did not. Empty where the call named a key.'),
            'searches' => Schema::listOf(Schema::object([
                'source' => ['type' => 'string', 'enum' => [Ter::HOST, Packagist::HOST]],
                'url' => Schema::string('What the search read.'),
                'status' => Schema::answerStatus(),
                'total' => Schema::integer('How many matches that registry counts, of which matches carries at most limit.'),
            ], ['source', 'url', 'status', 'total']), 'Each registry a search read, and what it answered. A registry that is unavailable here is a registry whose matches are missing from matches. Empty where the call named a key.'),
            'unavailable' => Schema::unavailable([
                'source-not-answering' => 'the registry did not answer this time.',
                'source-not-parseable' => 'something answered with a page rather than with the API.',
            ], 'Why the registry answered nothing, where status says unavailable. Null otherwise, and null on a key with nothing published under it; that one is an answer. In a search, why neither registry answered.'),
        ], ['status', 'source', 'url', 'page', 'extension', 'query', 'extensionVersion', 'held', 'total', 'versions', 'matches', 'searches', 'unavailable']);
    }

    /** @param array<string, mixed> $args */
    public static function answer(array $args): ToolResult
    {
        // Lowercase rather than a refusal. A key is lowercase by definition, so
        // this can turn no valid one into another. What remains for the check
        // below is a name of the wrong kind.
        $key = mb_strtolower(is_string($args['extension'] ?? null) ? trim($args['extension']) : '');
        $version = is_string($args['extensionVersion'] ?? null) ? trim($args['extensionVersion']) : '';
        $limit = is_int($args['limit'] ?? null) ? max(1, min(50, $args['limit'])) : 10;
        $query = is_string($args['query'] ?? null) ? trim($args['query']) : '';

        if ($key === '' && $query !== '') {
            return self::search($query, $limit);
        }
        if (preg_match(Ter::KEY, $key) !== 1) {
            return self::notAKey($key, $version);
        }

        $answer = (new Ter())->versions($key);
        $held = self::held($answer, $version);
        $data = [
            'status' => $answer['status'],
            'source' => Ter::HOST,
            'url' => $answer['url'],
            'page' => Ter::HOST . '/extension/' . $key,
            'extension' => $key,
            'query' => '',
            'extensionVersion' => $version,
            'held' => $held,
            'total' => count($answer['versions']),
            'versions' => array_slice($answer['versions'], 0, $limit),
            'matches' => [],
            'searches' => [],
            'unavailable' => Unreachable::of($answer['cause'], self::reasons(Ter::HOST)),
        ];

        if ($answer['status'] === 'unavailable') {
            return ToolResult::create(
                'TYPO3 Extension Repository: ' . $answer['url'] . "\nCould not answer: " . $data['unavailable']['reason'],
                $data,
            );
        }
        if ($answer['status'] === 'empty') {
            return ToolResult::create(
                'TYPO3 Extension Repository: nothing is published under the key "' . $key . '" at ' . Ter::HOST . ".\n"
                . 'The registry holds no extension by that key. That is not a statement that no such package exists: '
                . 'one distributed through Composer alone is never registered here, and a key can be registered '
                . 'without a version having been uploaded to it yet.',
                $data,
            );
        }

        return ToolResult::create(self::published($key, $version, $held, $answer['versions'], $data['versions']), $data);
    }

    /**
     * The name is not one the registry takes, so no read happened.
     *
     * A statement about the argument rather than about the published set, and
     * the one mistake this path exists to keep out of the answer. A Composer
     * package name reaches the API as a `400`. A report of that as "nothing is
     * published" would tell a maintainer their release is absent.
     */
    private static function notAKey(string $key, string $version): ToolResult
    {
        return ToolResult::create(
            'TYPO3 Extension Repository: "' . $key . '" is not an extension key, so nothing was read. That is about '
            . "the name and not about what is published.\n"
            . 'A key is lowercase letters, digits and underscores, three to thirty characters, starting with a '
            . 'letter. A slash or a dash means a Composer package name — the key is what '
            . 'extra.typo3/cms.extension-key declares in that package\'s own composer.json. To find an '
            . 'extension by its package name or by its subject, pass query instead.',
            [
                'status' => 'empty',
                'source' => Ter::HOST,
                'url' => '',
                'page' => '',
                'extension' => $key,
                'query' => '',
                'extensionVersion' => $version,
                'held' => null,
                'total' => 0,
                'versions' => [],
                'matches' => [],
                'searches' => [],
                'unavailable' => null,
            ],
        );
    }

    /**
     * The extensions both registries find for a subject.
     *
     * Packagist leads. A project installs what Composer can resolve, and its
     * ranking puts the package everybody uses first, where the TER ranked a
     * package with 363 downloads second for "news" on 2026-10-29. A TER entry
     * joins the Packagist match with its Composer name.
     */
    private static function search(string $query, int $limit): ToolResult
    {
        $packagist = (new Packagist())->search($query, $limit);
        $ter = (new Ter())->search($query, $limit);

        $matches = [];
        foreach ($packagist['packages'] as $package) {
            $matches[$package['package']] = [
                'package' => $package['package'],
                'key' => '',
                'description' => $package['description'],
                'version' => '',
                'uploaded' => '',
                'majors' => [],
                'constraint' => '',
                'terDownloads' => 0,
                'packagistDownloads' => $package['downloads'],
                'foundIn' => [Packagist::HOST],
            ];
        }
        foreach ($ter['extensions'] as $extension) {
            $id = $extension['package'] !== '' ? $extension['package'] : 'ter:' . $extension['key'];
            $match = $matches[$id] ?? [
                'package' => $extension['package'],
                'description' => $extension['description'],
                'packagistDownloads' => 0,
                'foundIn' => [],
            ];
            $current = $extension['current'];
            $matches[$id] = [
                'package' => $match['package'],
                'key' => $extension['key'],
                'description' => $match['description'] !== '' ? $match['description'] : $extension['description'],
                'version' => $current['number'] ?? '',
                'uploaded' => $current['uploaded'] ?? '',
                'majors' => $current['majors'] ?? [],
                'constraint' => $current['constraint'] ?? '',
                'terDownloads' => $extension['downloads'],
                'packagistDownloads' => $match['packagistDownloads'],
                'foundIn' => [...$match['foundIn'], Ter::HOST],
            ];
        }
        $matches = array_slice(array_values($matches), 0, $limit);

        $searches = [
            ['source' => Ter::HOST, 'url' => $ter['url'], 'status' => $ter['status'], 'total' => $ter['total']],
            ['source' => Packagist::HOST, 'url' => $packagist['url'], 'status' => $packagist['status'], 'total' => $packagist['total']],
        ];
        $neither = $ter['status'] === 'unavailable' && $packagist['status'] === 'unavailable';
        $data = [
            'status' => $matches !== [] ? 'answered' : ($neither ? 'unavailable' : 'empty'),
            'source' => Ter::HOST . ' and ' . Packagist::HOST,
            'url' => '',
            'page' => '',
            'extension' => '',
            'query' => $query,
            'extensionVersion' => '',
            'held' => null,
            'total' => 0,
            'versions' => [],
            'matches' => $matches,
            'searches' => $searches,
            'unavailable' => $neither ? Unreachable::of($ter['cause'], self::reasons(Ter::HOST)) : null,
        ];

        $lines = [];
        foreach ([[$ter, Ter::HOST], [$packagist, Packagist::HOST]] as [$answer, $host]) {
            if ($answer['status'] === 'unavailable') {
                $lines[] = 'Not searched: ' . (Unreachable::of($answer['cause'], self::reasons($host))['reason'] ?? '')
                    . ' What it holds is missing from this answer.';
            }
        }
        if ($matches === []) {
            $lines[] = $neither
                ? 'Neither registry answered, so nothing says whether an extension for "' . $query . '" exists.'
                : 'No published extension matches "' . $query . '". Another word for the subject may still find one.';

            return ToolResult::create(implode("\n", $lines), $data);
        }

        array_unshift($lines, sprintf(
            'Published extensions for "%s": %d on %s, %d extension packages on %s',
            $query,
            $ter['total'],
            Ter::HOST,
            $packagist['total'],
            Packagist::HOST,
        ));
        $lines[] = '';
        foreach ($matches as $match) {
            $lines[] = '- ' . implode(' · ', array_filter([
                $match['package'],
                $match['key'] !== '' ? 'key ' . $match['key'] : '',
                $match['version'] !== '' ? $match['version'] . ', uploaded ' . $match['uploaded'] : '',
                $match['majors'] !== [] ? 'TYPO3 ' . implode(', ', $match['majors']) : '',
                $match['packagistDownloads'] > 0 ? $match['packagistDownloads'] . ' installs' : '',
            ]));
            if ($match['description'] !== '') {
                $lines[] = '  ' . $match['description'];
            }
        }
        $lines[] = '';
        $lines[] = implode(' ', self::AFTER_SEARCH);

        return ToolResult::create(implode("\n", $lines), $data, self::AFTER_SEARCH);
    }

    /**
     * Why one registry did not answer, with its host in the sentence.
     *
     * @return array<string, string>
     */
    private static function reasons(string $host): array
    {
        return array_map(static fn(string $reason): string => sprintf($reason, $host), self::UNREACHABLE);
    }

    /**
     * Whether the registry holds the number in question.
     *
     * Null where the caller asked for none and null where no read happened.
     * "The registry does not have it" and "the registry did not answer" are the
     * two reads a release audit must not confuse.
     *
     * @param array{status: string, versions: list<array<string, mixed>>} $answer
     */
    private static function held(array $answer, string $version): ?bool
    {
        if ($version === '' || $answer['status'] === 'unavailable') {
            return null;
        }

        return in_array($version, array_column($answer['versions'], 'number'), true);
    }


    /**
     * The published set, with the asked version first.
     *
     * The number a caller passed is the whole reason for the call, so it leads.
     * It answers as what the registry holds rather than as what may go out,
     * which is the caller's own comparison against its working tree.
     *
     * @param list<array<string, mixed>> $all
     * @param list<array<string, mixed>> $shown
     */
    private static function published(string $key, string $version, ?bool $held, array $all, array $shown): string
    {
        $latest = $all[0];
        $lines = [sprintf(
            'TYPO3 Extension Repository: %s — %d published %s, highest %s (%s, uploaded %s)',
            $key,
            count($all),
            count($all) === 1 ? 'version' : 'versions',
            $latest['number'],
            $latest['state'],
            $latest['uploaded'],
        )];
        $lines[] = Ter::HOST . '/extension/' . $key;

        if ($version !== '') {
            $lines[] = '';
            $lines[] = $held === true
                ? 'The registry holds ' . self::describe(self::numbered($all, $version)) . '.'
                : sprintf(
                    'The registry holds no version %s of %s. That is what is published, not a judgement that the '
                    . 'number is free — what the release process requires of the extension is typo3_hint_lookup with '
                    . 'id="extension-ter-release".',
                    $version,
                    $key,
                );
        }

        $lines[] = '';
        $lines[] = count($shown) < count($all)
            ? sprintf('## The newest %d of %d, by version number', count($shown), count($all))
            : sprintf('## All %d, by version number', count($all));
        $lines[] = 'Version order and not upload order: a release on an older line sits further down and may have been '
            . 'uploaded after the ones above it, which the days say.';
        foreach ($shown as $entry) {
            $lines[] = '- ' . self::describe($entry);
        }

        return implode("\n", $lines);
    }

    /**
     * One published version, on one line.
     *
     * @param array<string, mixed> $entry
     */
    private static function describe(array $entry): string
    {
        return implode(' · ', array_filter([
            $entry['number'],
            $entry['state'],
            $entry['uploaded'] !== '' ? 'uploaded ' . $entry['uploaded'] : '',
            $entry['majors'] !== [] ? 'TYPO3 ' . implode(', ', $entry['majors']) : '',
            $entry['constraint'],
        ]));
    }

    /**
     * The published version with one number.
     *
     * @param list<array<string, mixed>> $all
     * @return array<string, mixed>
     */
    private static function numbered(array $all, string $version): array
    {
        foreach ($all as $entry) {
            if ($entry['number'] === $version) {
                return $entry;
            }
        }

        return ['number' => $version, 'state' => '', 'uploaded' => '', 'majors' => [], 'constraint' => ''];
    }
}
