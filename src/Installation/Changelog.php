<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

use Symfony\Component\Finder\Finder;

/**
 * The changelog entries of the installation this server was started in.
 *
 * Every TYPO3 package ships the core's own changelog, one RST file per breaking
 * change, deprecation, feature and important note. Nothing is bundled here: a
 * snapshot would answer for the version it was taken from, while the
 * installation's own copy answers for the version the caller runs. The scan
 * reads file names and not files, and what a name does not carry is read out of
 * the file only where the names reached nothing — the stated title
 * (`D-ANS-041`), and the identifiers a removed method is asked for by
 * (`D-ANS-042`).
 */
final class Changelog
{
    /** @var array<int, string> */
    public const TYPES = ['Breaking', 'Deprecation', 'Feature', 'Important'];

    /** Where the installed core keeps them, or null when there is none to read. */
    public static function directory(): ?string
    {
        $core = Instance::packages()['core'] ?? null;
        if ($core === null) {
            return null;
        }

        $directory = $core . '/Documentation/Changelog';

        return is_dir($directory) ? $directory : null;
    }

    /**
     * The versions the installed changelog covers, newest first.
     *
     * @return array<int, string>
     */
    public static function versions(): array
    {
        $directory = self::directory();
        if ($directory === null) {
            return [];
        }

        $versions = [];
        foreach (Finder::create()->directories()->in($directory)->depth(0) as $path) {
            $versions[] = $path->getFilename();
        }
        usort($versions, static fn(string $a, string $b): int => version_compare($b, $a));

        return $versions;
    }

    /**
     * Every entry, as far as its file name says: type, issue, version, and the
     * title the name spells in CamelCase.
     *
     * @return array<int, array{type: string, issue: string, version: string, key: string, source: string, file: string}>
     */
    public static function entries(string $type = '', string $version = ''): array
    {
        $directory = self::directory();
        if ($directory === null) {
            return [];
        }

        $type = ucfirst(strtolower(trim($type)));
        $entries = [];
        foreach (self::versions() as $inVersion) {
            if ($version !== '' && !str_starts_with($inVersion, $version)) {
                continue;
            }
            foreach (Finder::create()->files()->in($directory . '/' . $inVersion)->depth(0)->name('*.rst')->sortByName() as $file) {
                $name = $file->getBasename('.rst');
                if (preg_match('/^(Breaking|Deprecation|Feature|Important)-(\d+)-(.+)$/', $name, $matches) !== 1) {
                    continue;
                }
                if ($type !== '' && $matches[1] !== $type) {
                    continue;
                }
                $entries[] = [
                    'type' => $matches[1],
                    'issue' => $matches[2],
                    'version' => $inVersion,
                    // The two fields a label search works on, so the same
                    // "carries every word" rule applies here without a second
                    // matcher: the title as words, and the file name as it is.
                    'key' => $name,
                    'source' => self::words($matches[3]),
                    'file' => $file->getPathname(),
                ];
            }
        }

        return $entries;
    }

    /**
     * The same entries, each carrying the title its file states.
     *
     * A file name spells a title of its own and the two differ. This is a read
     * per entry, which is why a search reaches for it only after the names —
     * `D-ANS-041`.
     *
     * @param array<int, array{type: string, issue: string, version: string, key: string, source: string, file: string}> $entries
     * @return array<int, array{type: string, issue: string, version: string, key: string, source: string, file: string, title: string}>
     */
    public static function titled(array $entries): array
    {
        return array_map(static function (array $entry): array {
            $entry['title'] = self::read($entry)['title'];

            return $entry;
        }, $entries);
    }

    /**
     * The title, the index tags and the stated removal a matched entry carries,
     * read from the file.
     *
     * @param array{file: string, version: string, type: string} $entry
     * @return array{title: string, tags: array<int, string>, removal: string, migration: string}
     */
    public static function read(array $entry): array
    {
        return self::parse((string) file_get_contents($entry['file']), $entry);
    }

    /**
     * The same three fields out of an entry's RST, whatever delivered it.
     *
     * The host publishes the source of every entry byte for byte under
     * `_sources`, so what the manual answers with is the file this parses on
     * disk and there is one parser rather than two — `D-ANS-067`.
     *
     * @param array{version: string, type: string} $entry
     * @return array{title: string, tags: array<int, string>, removal: string, migration: string}
     */
    public static function parse(string $contents, array $entry): array
    {
        $title = '';
        $tags = [];
        foreach (preg_split('/\R/', $contents) ?: [] as $line) {
            // "Deprecation: #107208 - <f:debug.render> ViewHelper" — the type
            // and the issue are fields of their own, so the title is what is
            // left of the line.
            if ($title === '' && preg_match('/^(Breaking|Deprecation|Feature|Important):\s*(?:#\d+\s*-\s*)?(.+)$/', trim($line), $matches) === 1) {
                $title = trim($matches[2]);
            }
            // ".. index::" and "..  index::" are both in the wild.
            if (preg_match('/^\.\.\s+index::\s*(.*)$/', trim($line), $matches) === 1) {
                $tags = array_values(array_filter(array_map('trim', explode(',', $matches[1]))));
            }
        }

        return [
            'title' => $title,
            'tags' => $tags,
            'removal' => self::removal($contents, $entry),
            'migration' => self::section($contents, 'Migration'),
        ];
    }

    /**
     * One section of an entry's reStructuredText, whole.
     *
     * A title says what stopped working and the migration says what to write
     * instead, and a session that got the first read the file for the second —
     * `D-ANS-139`. The file is already open here, so what this costs is the
     * lines between two headings.
     *
     * A heading is its own line with an underline of at least its length; the
     * section runs to the next one or to the end.
     */
    public static function section(string $contents, string $heading): string
    {
        $lines = preg_split('/\R/', $contents) ?: [];
        $section = [];
        $inside = false;
        $skipUnderline = false;
        foreach ($lines as $index => $line) {
            if ($skipUnderline) {
                $skipUnderline = false;
                continue;
            }
            $next = trim($lines[$index + 1] ?? '');
            $underlined = trim($line) !== ''
                && preg_match('/^[=\-~^"\x27`#*+]{3,}$/', $next) === 1
                && mb_strlen($next) >= mb_strlen(trim($line));
            if ($underlined) {
                if ($inside) {
                    break;
                }
                $inside = trim($line) === $heading;
                $skipUnderline = true;
                continue;
            }
            // The index directive closes the file rather than the section, and
            // its tags are a field of their own.
            if ($inside && preg_match('/^\.\.\s+index::/', trim($line)) === 1) {
                break;
            }
            if ($inside) {
                $section[] = $line;
            }
        }

        return trim(implode("\n", $section));
    }

    /**
     * The identifiers an entry names, as its body spells them.
     *
     * Every inline literal is read whatever markup it is written in, because the
     * `:php:` role postdates 9.0. Only a word carrying a hump or an underscore
     * is one — `D-ANS-042`.
     *
     * @param array{file: string} $entry
     * @return array<int, string>
     */
    public static function identifiers(array $entry): array
    {
        return self::named((string) file_get_contents($entry['file']));
    }

    /** @return array<int, string> */
    public static function named(string $contents): array
    {
        preg_match_all('/``[^`]+``|`[^`]+`/', $contents, $literals);
        $names = [];
        foreach ($literals[0] as $literal) {
            preg_match_all('/[A-Za-z_][A-Za-z0-9_]{2,}/', $literal, $words);
            foreach ($words[0] as $word) {
                if (preg_match('/[a-z][A-Z]|[A-Z][A-Z][a-z]|_/', $word) === 1) {
                    $names[$word] = true;
                }
            }
        }

        return array_keys($names);
    }

    /**
     * The version this entry states its subject stops working in, empty where
     * it states none.
     *
     * There is no field to read: the removal is a clause in the Description,
     * written freely — "will be removed in TYPO3 v15.0", "will be removed with
     * v15", "marked for removal in v15" are all in `.checkouts/14.3`, and the
     * sentence wraps, so the whole text is matched rather than a line. 44 of
     * the 75 deprecations of 14 state one that way and 31 state none, which is
     * why an empty answer here is the ordinary case rather than a failure.
     *
     * Only a Deprecation states one about itself. Ten entries of the other
     * three types carry the same clause about something else: `14.2`
     * Feature-109412 announces the replacement and says the mechanism it
     * replaces will be removed in v15.0, which is the deprecation's removal
     * and not the feature's.
     *
     * What a number has to survive is being later than the version the entry
     * was released in. Two things in the corpus are not this entry's removal
     * and read like one: a 13.3 deprecation says its subject "will be removed
     * with v5", which is Fluid standalone, and an entry recounting what an
     * earlier release already removed carries that release's number.
     *
     * @param array{version: string, type: string} $entry
     */
    private static function removal(string $contents, array $entry): string
    {
        if ($entry['type'] !== 'Deprecation') {
            return '';
        }

        preg_match_all(
            '/\b(?:will\s+be\s+)?(?:removed|removal)\s+(?:in|with|for|from)\s+(?:TYPO3\s+)?v?(\d+(?:\.\d+)?)\b/i',
            $contents,
            $matches,
        );
        foreach ($matches[1] as $stated) {
            if (version_compare($stated, $entry['version'], '>')) {
                return $stated;
            }
        }

        return '';
    }

    /** "ExperimentalBackendViewHelpers" as the words it is made of. */
    public static function words(string $camelCase): string
    {
        $spaced = preg_replace('/(?<=[a-z0-9])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/', ' ', $camelCase);

        return str_replace('-', ' ', $spaced ?? $camelCase);
    }
}
