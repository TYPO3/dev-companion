<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Manual;

use TYPO3\DevCompanion\Http\Fetch;
use TYPO3\DevCompanion\Installation\Changelog;

/**
 * The changelog docs.typo3.org publishes, which is where the installation's own
 * copy stops.
 *
 * A package ships every changelog down to 7.0 and nothing above its own major,
 * so the entries a caller is upgrading *to* are the ones its installation cannot
 * show it. There is one manual and it is not versioned — every version in the
 * URL of `/c/typo3/cms-core/` redirects to `main` — so the version a caller asks
 * for is applied to the entry names here and never to the URL, and nothing is
 * read per version (`D-ANS-067`). What it costs is one inventory read, held
 * under its entity tag, and one `_sources` read per entry an answer shows.
 */
final class CoreChangelog
{
    /**
     * The manual the core's changelog is published as. It is the `cms-core`
     * extension manual under `/c/`, not one of the books under `/m/` — TYPO3
     * Explained indexes no changelog entry at all.
     */
    private const BASE = 'https://docs.typo3.org/c/typo3/cms-core/main/en-us/';

    private const INVENTORY = 'objects.inv';

    /**
     * The RST of a rendered page, byte for byte. Sphinx writes it beside the
     * HTML from the same build, `.. index::` and all, so the entries this hands
     * on are parsed by what parses the files on disk.
     */
    private const SOURCES = '_sources/';

    /**
     * The inventory as it was last read, with the tag it came under.
     *
     * Static because the tool builds an instance per call and the manual is one
     * artefact for the process. `Documentation` holds its indexes the same way
     * and for the same reason.
     *
     * @var array{etag: string, entries: list<array{type: string, issue: string, version: string, key: string, source: string, stated: string, url: string, path: string}>}|null
     */
    private static ?array $index = null;

    /**
     * Whether the host has already failed to answer in this process.
     *
     * A changelog lookup is the tool a session calls most, and it used to touch
     * nothing outside the machine. Asking a host that is not there costs the
     * connect timeout on every call, so it is asked once: a session that starts
     * offline pays three seconds once rather than three seconds a question, and
     * answers from the installation for the rest of it.
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
     * What is held goes with it: another reader is another host, and entries
     * kept across the two would answer for a changelog that was never read.
     *
     * @param (\Closure(string): ?string)|null $reader
     */
    public static function useReader(?\Closure $reader): void
    {
        self::$transport = $reader;
        self::$index = null;
        self::$unreachable = false;
    }

    /**
     * Every entry the host publishes, in the shape the installation's own
     * entries have.
     *
     * The type, the issue and the version are in the page name, and the stated
     * title is the other half of the inventory line — the same title
     * `Changelog::read()` finds inside the file, so it costs no read here. Both
     * are searchable, which is what lets an answer be composed without opening
     * a single entry.
     *
     * Null is a host that did not answer, which is a different thing from a
     * host that published nothing: the caller is told the gap could not be
     * read rather than that there is none.
     *
     * @return list<array{type: string, issue: string, version: string, key: string, source: string, stated: string, url: string, path: string}>|null
     */
    public function entries(): ?array
    {
        $held = self::$index;
        if ($held === null && self::$unreachable) {
            return null;
        }

        $response = $this->reader->read(
            self::BASE . self::INVENTORY,
            $held === null ? [] : ['If-None-Match: ' . $held['etag']],
        );

        if ($held !== null && $response['status'] === 304) {
            return $held['entries'];
        }
        if ($response['body'] === null) {
            self::$unreachable = $held === null;

            return $held['entries'] ?? null;
        }

        $entries = self::listed($response['body']);
        if ($entries === null) {
            self::$unreachable = $held === null;

            return $held['entries'] ?? null;
        }
        if (is_string($response['etag']) && $response['etag'] !== '') {
            self::$index = ['etag' => $response['etag'], 'entries' => $entries];
        }

        return $entries;
    }

    /**
     * The title, the tags and the stated removal of one entry, out of the RST
     * the host publishes beside its page.
     *
     * @param array{path: string, version: string, type: string} $entry
     * @return array{title: string, tags: array<int, string>, removal: string, migration: string}
     */
    public function read(array $entry): array
    {
        $source = $this->reader->get(self::BASE . self::SOURCES . preg_replace('/\.html$/', '', $entry['path']) . '.rst.txt');

        return $source === null
            ? ['title' => '', 'tags' => [], 'removal' => '', 'migration' => '']
            : Changelog::parse($source, $entry);
    }

    /** Where a caller reads the entry itself. */
    public static function url(string $version, string $key): string
    {
        return self::BASE . 'Changelog/' . $version . '/' . $key . '.html';
    }

    /**
     * The changelog pages of a Sphinx inventory, as entries.
     *
     * Only `std:doc` and only under `Changelog/`: the manual carries a handful
     * of pages that are not entries, and every other role is an addressable
     * object inside a page rather than a page.
     *
     * @return list<array{type: string, issue: string, version: string, key: string, source: string, stated: string, url: string, path: string}>|null
     */
    private static function listed(string $inventory): ?array
    {
        $listing = @zlib_decode(explode("\n", $inventory, 5)[4] ?? '');
        if (!is_string($listing)) {
            return null;
        }

        $entries = [];
        foreach (explode("\n", $listing) as $line) {
            if (preg_match('/^(.+?) +std:doc +-?\d+ +(\S+) +(.*)$/', $line, $object) !== 1) {
                continue;
            }
            [, $name, $path, $title] = $object;
            if (preg_match('#^Changelog/([^/]+)/(Breaking|Deprecation|Feature|Important)-(\d+)-(.+)$#', $name, $page) !== 1) {
                continue;
            }
            [, $version, $type, $issue, $spelled] = $page;
            $key = $type . '-' . $issue . '-' . $spelled;
            $entries[] = [
                'type' => $type,
                'issue' => $issue,
                'version' => $version,
                'key' => $key,
                'source' => Changelog::words($spelled),
                // "Deprecation: #110148 - Experimental backend ViewHelpers" is
                // what the line carries, and the two fields in front of it are
                // fields of their own here. It is `stated` and not `title`
                // because a search reads `title` and the installation's entries
                // gain theirs only from a file read: carried under that name it
                // would match a manual entry in the pass where the installed
                // one it should have found is still searched by its file name
                // alone.
                'stated' => $title === '-' ? '' : trim((string) preg_replace('/^(?:Breaking|Deprecation|Feature|Important):\s*(?:#\d+\s*-\s*)?/', '', $title)),
                'url' => self::url($version, $key),
                'path' => $path,
            ];
        }

        return $entries;
    }
}
