<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Manual;

use TYPO3\DevCompanion\Http\Fetch;
use TYPO3\DevCompanion\Installation\Changelog;
use TYPO3\DevCompanion\Knowledge\Versions;

/**
 * The changelog docs.typo3.org publishes, which is the source of an entry.
 *
 * docs.typo3.org renders the changelog after every merge, so it is ahead of
 * what a package ships and ahead of a checkout nobody pulled today. There is one
 * manual and it has no versions: every version in the URL of
 * `/c/typo3/cms-core/` redirects to `main`. What it publishes beside the pages
 * is one listing per major, as JSON, and every page as the Markdown the build
 * rendered from the RST (`D-ANS-165`). One read per covered major, held under
 * its entity tag and revalidated per call, and one page read per entry an
 * answer shows, held the same way. The class index is the third read, and only
 * a search that the names and titles did not answer makes it (`D-ANS-168`).
 */
final class CoreChangelog
{
    /**
     * The manual docs.typo3.org publishes the core's changelog as. It is the
     * `cms-core` extension manual under `/c/`, not one of the books under `/m/`
     * — TYPO3 Explained indexes no changelog entry at all.
     */
    private const BASE = 'https://docs.typo3.org/c/typo3/cms-core/main/en-us/';

    /**
     * Each major's listing as it was last read, with the tag it came under.
     *
     * Static because the tool builds an instance per call and the manual is one
     * artefact for the process. `Documentation` holds its indexes the same way
     * and for the same reason.
     *
     * @var array<int, array{etag: string, entries: list<array{type: string, issue: string, version: string, key: string, source: string, stated: string, tags: list<string>, url: string, path: string}>}>
     */
    private static array $index = [];

    /**
     * Each page as it was last read, under its URL, with the tag it came under.
     *
     * @var array<string, array{etag: string, body: string}>
     */
    private static array $pages = [];

    /**
     * The identifiers the class index names per page, as it was last read.
     *
     * @var array{etag: string, names: array<string, list<string>>}|null
     */
    private static ?array $classes = null;

    /**
     * Whether docs.typo3.org has already failed to answer in this process.
     *
     * A changelog lookup is the tool a session calls most. A call to a server
     * that is not there costs the connect timeout, so this asks once. A session
     * that starts offline pays three seconds once rather than three seconds a
     * question, and answers from the installation for the rest of it. A 404
     * for one major is an answer, and the next major is asked for.
     */
    private static bool $unreachable = false;

    private readonly Fetch $reader;

    /** @param (\Closure(string): ?string)|null $fetch */
    public function __construct(?\Closure $fetch = null)
    {
        $this->reader = new Fetch($fetch ?? self::$transport);
    }

    /** @var (\Closure(string): ?string)|null */
    private static ?\Closure $transport = null;

    /**
     * What a test hands in, so nothing it drives reaches docs.typo3.org.
     *
     * What this holds goes with it. Another reader is another server, and
     * entries kept across the two would answer for a changelog nobody read.
     *
     * @param (\Closure(string): ?string)|null $reader
     */
    public static function useReader(?\Closure $reader): void
    {
        self::$transport = $reader;
        self::$index = [];
        self::$pages = [];
        self::$classes = null;
        self::$unreachable = false;
    }

    /**
     * Every entry docs.typo3.org publishes, per covered major.
     *
     * The majors are the ones `knowledge/versions.json` covers, because nothing
     * on docs.typo3.org lists them for less than the whole table of contents. A
     * major it did not answer for is null under its number, which is a
     * different thing from a major it publishes nothing for. The answer tells
     * the caller that the gap is unread rather than that there is none.
     *
     * @return array<int, list<array{type: string, issue: string, version: string, key: string, source: string, stated: string, tags: list<string>, url: string, path: string}>|null>
     */
    public function entries(): array
    {
        $read = [];
        foreach (Versions::majors() as $major) {
            $read[$major] = $this->major($major);
        }

        return $read;
    }

    /**
     * The listing of one major, in the shape the installation's own entries
     * have.
     *
     * A listing this holds is asked for again under its tag, and a 304 is the
     * held one. So the second call of a session pays one round trip and no
     * payload for a listing that is still current, and a render after a merge
     * arrives with the next call.
     *
     * @return list<array{type: string, issue: string, version: string, key: string, source: string, stated: string, tags: list<string>, url: string, path: string}>|null
     */
    private function major(int $major): ?array
    {
        $held = self::$index[$major] ?? null;
        if ($held === null && self::$unreachable) {
            return null;
        }

        $response = $this->reader->read(
            self::BASE . 'Changelog-' . $major . '.json',
            $held === null ? [] : ['If-None-Match: ' . $held['etag']],
        );

        if ($held !== null && $response['status'] === 304) {
            return $held['entries'];
        }

        // A server that gave nothing at all is not asked for the next major.
        // One that answered with something that is no listing is there, and
        // has no listing for this major: the knowledge covers a major
        // docs.typo3.org has not opened, or the other way round.
        if ($response['body'] === null) {
            self::$unreachable = $held === null;

            return $held['entries'] ?? null;
        }
        $entries = self::listed(Fetch::decode($response['body']));
        if ($entries === null) {
            return $held['entries'] ?? null;
        }
        if (is_string($response['etag']) && $response['etag'] !== '') {
            self::$index[$major] = ['etag' => $response['etag'], 'entries' => $entries];
        }

        return $entries;
    }

    /**
     * The stated removal and the migration of one entry, out of the Markdown
     * docs.typo3.org renders, or null where it did not answer.
     *
     * The page is the body, rendered: every include resolved and every role a
     * link, where the RST on disk is the source the build ran on. So it is the
     * body of every shown entry, whichever side listed it, and the RST on disk
     * is what remains offline.
     *
     * @param array{version: string, key: string, type: string} $entry
     * @return array{removal: string, migration: string}|null
     */
    public function rendered(array $entry): ?array
    {
        if (self::$unreachable) {
            return null;
        }
        $body = $this->page(self::BASE . 'Changelog/' . $entry['version'] . '/' . $entry['key'] . '.md');

        return $body === null ? null : [
            'removal' => Changelog::removal($body, $entry),
            'migration' => self::section($body, 'Migration'),
        ];
    }

    /**
     * The PHP classes and members each page names, under its path, or null
     * where docs.typo3.org published no class index.
     *
     * The index lists a class once per page that writes it, as a role or in a
     * `use` line, with the member where one follows. Each is read as one
     * inline literal. So the rule `Changelog::named()` applies to a body
     * applies here: a word with a hump or an underscore, and the case merged
     * that the index keeps apart in `\TYPO3\CMS\core\…` — `D-ANS-168`.
     *
     * @return array<string, list<string>>|null
     */
    public function identifiers(): ?array
    {
        $held = self::$classes;
        if ($held === null && self::$unreachable) {
            return null;
        }

        $response = $this->reader->read(
            self::BASE . 'classes.json',
            $held === null ? [] : ['If-None-Match: ' . $held['etag']],
        );
        if ($held !== null && $response['status'] === 304) {
            return $held['names'];
        }

        $names = self::indexed(Fetch::decode($response['body']));
        if ($names === null) {
            return $held['names'] ?? null;
        }
        if (is_string($response['etag']) && $response['etag'] !== '') {
            self::$classes = ['etag' => $response['etag'], 'names' => $names];
        }

        return $names;
    }

    /**
     * @param array<mixed>|null $index
     * @return array<string, list<string>>|null
     */
    private static function indexed(?array $index): ?array
    {
        if (!is_array($index['classes'] ?? null)) {
            return null;
        }

        $literals = [];
        foreach ($index['classes'] as $class => $entry) {
            foreach (is_array($entry['places'] ?? null) ? $entry['places'] : [] as $place) {
                if (is_array($place) && is_string($place['path'] ?? null)) {
                    $literals[$place['path']][] = '`' . $class . (string) ($place['member'] ?? '') . '`';
                }
            }
        }

        return array_map(
            static fn(array $written): array => array_values(array_unique(array_map(
                'strtolower',
                Changelog::named(implode(' ', $written)),
            ))),
            $literals,
        );
    }

    /** Where a caller reads the entry itself. */
    public static function url(string $version, string $key): string
    {
        return self::BASE . 'Changelog/' . $version . '/' . $key . '.html';
    }

    /** One page, held under its tag and asked for again the way a listing is. */
    private function page(string $url): ?string
    {
        $held = self::$pages[$url] ?? null;
        $response = $this->reader->read($url, $held === null ? [] : ['If-None-Match: ' . $held['etag']]);
        if ($held !== null && $response['status'] === 304) {
            return $held['body'];
        }
        if ($response['body'] === null) {
            return $held['body'] ?? null;
        }
        if (is_string($response['etag']) && $response['etag'] !== '') {
            self::$pages[$url] = ['etag' => $response['etag'], 'body' => $response['body']];
        }

        return $response['body'];
    }

    /**
     * One section of a rendered page, whole.
     *
     * A heading is `## Migration {#migration}`, and the section runs to the
     * next heading of the same level or to the end. The anchor the renderer
     * appends is not part of the name.
     */
    private static function section(string $markdown, string $heading): string
    {
        $section = [];
        $inside = false;
        foreach (preg_split('/\R/', $markdown) ?: [] as $line) {
            if (preg_match('/^## +(.+?)(?: *\{#[^}]*\})? *$/', $line, $named) === 1) {
                if ($inside) {
                    break;
                }
                $inside = trim($named[1]) === $heading;
                continue;
            }
            if ($inside) {
                $section[] = $line;
            }
        }

        return trim(implode("\n", $section));
    }

    /**
     * The entries of one major's listing.
     *
     * The type, the issue, the version and the tags are fields of the listing.
     * The stated title is the page title, which is the same title
     * `Changelog::read()` finds inside the file on disk. So a title and a tag
     * cost no read here, which is what lets a search and a tag filter compose
     * an answer without one entry read.
     *
     * @param array<mixed>|null $listing
     * @return list<array{type: string, issue: string, version: string, key: string, source: string, stated: string, tags: list<string>, url: string, path: string}>|null
     */
    private static function listed(?array $listing): ?array
    {
        if (!is_array($listing['entries'] ?? null)) {
            return null;
        }

        $entries = [];
        foreach ($listing['entries'] as $entry) {
            if (!is_array($entry)) {
                continue;
            }
            $type = ucfirst(strtolower((string) ($entry['type'] ?? '')));
            $path = (string) ($entry['path'] ?? '');
            $version = (string) ($entry['typo3-version'] ?? '');
            if (!in_array($type, Changelog::TYPES, true) || $version === '' || preg_match('#^Changelog/[^/]+/([^/]+)$#', $path, $page) !== 1) {
                continue;
            }
            $key = $page[1];
            $entries[] = [
                'type' => $type,
                'issue' => (string) ($entry['issue'] ?? ''),
                'version' => $version,
                'key' => $key,
                'source' => Changelog::words(explode('-', $key, 3)[2] ?? $key),
                // "Deprecation: #110148 - Experimental backend ViewHelpers" is
                // what the title carries, and the two fields in front of it are
                // fields of their own here. It is `stated` and not `title`
                // because a search reads `title` and the installation's entries
                // gain theirs only from a file read. Under that name it would
                // match a manual entry in the pass where the search still reads
                // the installed one by its file name alone.
                'stated' => trim((string) preg_replace('/^(?:Breaking|Deprecation|Feature|Important):\s*(?:#\d+\s*-\s*)?/', '', (string) ($entry['title'] ?? ''))),
                'tags' => array_values(array_filter(array_map('strval', is_array($entry['tags'] ?? null) ? $entry['tags'] : []))),
                'url' => self::BASE . $path . '.html',
                'path' => $path,
            ];
        }

        return $entries;
    }
}
