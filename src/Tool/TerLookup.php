<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Publication\Ter;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unreachable;

/**
 * What the TYPO3 Extension Repository already holds under an extension key.
 *
 * A release audit turns on it and no checkout can answer it: Tailor requires
 * `ext_emconf.php` to name the version being released, so the file still names
 * it after the upload and a published repository reads like an unreleased one.
 * A session that could not see the registry reported a release-blocking finding
 * and had it confirmed wrongly (`D-FBK-051`).
 */
final class TerLookup extends ReadOnlyTool
{
    /** The releases are read from the registry at extensions.typo3.org. */
    protected const OPEN_WORLD = true;

    /** Why nothing was answered, in the caller's terms rather than the transport's. */
    private const UNREACHABLE = [
        Unreachable::NOT_ANSWERING => 'The registry did not answer. It is reachable at ' . Ter::HOST
            . ' in a browser; nothing here can answer this offline, and no bundled list of releases could be right '
            . 'for it.',
        Unreachable::NOT_PARSEABLE => 'Something answered with a page rather than with the API, which is what a '
            . 'portal in front of the connection looks like from here.',
    ];

    public static function name(): string
    {
        return 'typo3_ter_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Network];
    }

    public static function description(): string
    {
        return 'Read what the TYPO3 Extension Repository has published under an extension key, live from extensions.typo3.org. Pass extension with the key — the one extra.typo3/cms.extension-key declares, not the Composer package name — and every published version comes back, highest number first: the number, the state, the day it was uploaded, the TYPO3 majors it declares and the constraints.depends.typo3 it was released with. This is the question a release audit cannot answer from the repository it is auditing: Tailor refuses to package unless ext_emconf.php names the version being released, so that file still names it after the upload and a checkout that has been published reads exactly like one that has not. Pass extensionVersion as well to be told whether the registry already holds that number; it reports what is published and judges no version free, and comparing it against the working tree is yours. A key nothing is published under is answered as such, which is not a statement that no such package exists — an extension distributed through Composer alone is never registered here. What publishing requires of the extension itself is typo3_hint_lookup with id="extension-ter-release". Reading only, and no credential: registering a key, uploading a version and transferring an extension stay yours, through Tailor and the token it carries.';
    }


    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'extension' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'The extension key, for example "news" or "bootstrap_package". That is the key extra.typo3/cms.extension-key declares in the package\'s composer.json, which every TYPO3 extension package has to carry to install at all — not the Composer package name, which the registry does not take: "georgringer/news" and "bootstrap-package" are the two shapes that reach it as a name of the wrong kind. Lowercase letters, digits and underscores, three to thirty characters.',
                ],
                'extensionVersion' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'One version number to be answered about, for example "14.0.1" — typically the one ext_emconf.php names. The answer says whether the registry holds it and, where it does, what that release declared. Compared as the registry writes it, which is exactly three numbers: a suffix of any kind belongs to no published version, because the upload route accepts none.',
                ],
                'limit' => [
                    'type' => 'integer',
                    'minimum' => 1,
                    'maximum' => 50,
                    'default' => 10,
                    'description' => 'How many versions come back, newest number first. The count of everything published comes with them, so a cut list says so. A widely maintained extension has a hundred, and what a release audit reads is the top of them.',
                ],
            ],
            'required' => ['extension'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'status' => Schema::answerStatus(),
            'source' => Schema::string('The registry the answer came from.'),
            'url' => Schema::string('What was read, so the same question can be asked again by hand. Empty where the key was answered without a read.'),
            'page' => Schema::string('Where a person reads the extension\'s own page in the registry. Empty where the key is not one the registry takes.'),
            'extension' => Schema::string('The key that was asked for, lowercased, as it was sent.'),
            'extensionVersion' => Schema::string('The version number the call asked about, as it was passed. Empty where none was.'),
            'held' => [
                'type' => ['boolean', 'null'],
                'description' => 'Whether the registry has published that exact number. Null where the call named no version, and null where nothing was read at all — a false here is the registry answering, never a question that failed. It is a fact about the registry and not a judgement that the number is free to release.',
            ],
            'total' => Schema::integer('How many versions are published under the key in total, of which versions carries at most limit. Zero where none is.'),
            'versions' => Schema::listOf(Schema::object([
                'number' => Schema::string('The version as it was published.'),
                'state' => Schema::string('What the uploader declared it as: stable, beta, alpha, experimental, test, obsolete.'),
                'uploaded' => Schema::string('The day it was uploaded, as YYYY-MM-DD in UTC. The registry writes the same moment in its own timezone, so a release made late in the day is dated one earlier here.'),
                'majors' => Schema::listOf(Schema::integer(), 'The TYPO3 majors the release declares it runs on, ascending.'),
                'constraint' => Schema::string('The constraints.depends.typo3 the release declared, as ext_emconf.php wrote it — for example ">=13.4.15 <=14.3.99". Empty where the release declared none, which the registry accepts on an upload made by a controller.'),
            ], ['number', 'state', 'uploaded', 'majors', 'constraint']), 'The published versions, highest number first. That is version order and not upload order: a maintenance release on an older line sits further down and may be the most recent upload of all, which is what the days beside the numbers say. Empty where nothing is published under the key.'),
            'unavailable' => Schema::unavailable([
                'source-not-answering' => 'the registry did not answer this time.',
                'source-not-parseable' => 'something answered with a page rather than with the API.',
            ], 'Why nothing was answered, where status says unavailable. Null otherwise, and null on a key nothing is published under — that one is an answer.'),
        ], ['status', 'source', 'url', 'page', 'extension', 'extensionVersion', 'held', 'total', 'versions', 'unavailable']);
    }

    /** @param array<string, mixed> $args */
    public static function answer(array $args): ToolResult
    {
        // Lowercased rather than refused: a key is lowercase by definition, so
        // this can turn no valid one into another, and what is left for the
        // check below is a name of the wrong kind.
        $key = mb_strtolower(is_string($args['extension'] ?? null) ? trim($args['extension']) : '');
        $version = is_string($args['extensionVersion'] ?? null) ? trim($args['extensionVersion']) : '';
        $limit = is_int($args['limit'] ?? null) ? max(1, min(50, $args['limit'])) : 10;

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
            'extensionVersion' => $version,
            'held' => $held,
            'total' => count($answer['versions']),
            'versions' => array_slice($answer['versions'], 0, $limit),
            'unavailable' => Unreachable::of($answer['cause'], self::UNREACHABLE),
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
     * The name is not one the registry takes, so nothing was read.
     *
     * A statement about the argument rather than about what is published, and
     * the one mistake this path exists to keep out of the answer: a Composer
     * package name reaches the API as a `400`, and reporting that as "nothing
     * is published" would tell a maintainer their release is missing.
     */
    private static function notAKey(string $key, string $version): ToolResult
    {
        return ToolResult::create(
            'TYPO3 Extension Repository: "' . $key . '" is not an extension key, so nothing was read. That is about '
            . "the name and not about what is published.\n"
            . 'A key is lowercase letters, digits and underscores, three to thirty characters, starting with a '
            . 'letter. A slash or a dash means a Composer package name — the key is what '
            . 'extra.typo3/cms.extension-key declares in that package\'s own composer.json.',
            [
                'status' => 'empty',
                'source' => Ter::HOST,
                'url' => '',
                'page' => '',
                'extension' => $key,
                'extensionVersion' => $version,
                'held' => null,
                'total' => 0,
                'versions' => [],
                'unavailable' => null,
            ],
        );
    }

    /**
     * Whether the registry holds the number that was asked about.
     *
     * Null where none was asked and null where nothing was read, because "the
     * registry does not have it" and "the registry did not answer" are the two
     * readings a release audit must not confuse.
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
     * What is published, with the asked version answered first.
     *
     * The number a caller passed is the whole reason for the call, so it leads
     * — and it is answered as what the registry holds rather than as what may
     * be released, which is the caller's own comparison against its working
     * tree.
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
     * The published version carrying one number.
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
