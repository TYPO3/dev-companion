<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Manual;

use TYPO3\DevCompanion\Http\Fetch;

/**
 * The Sphinx inventory one manual publishes, read once and revalidated after.
 *
 * The format is four comment lines and then everything else compressed with
 * zlib, one object per line. That is the name, its `domain:role`, a priority,
 * the URI it resolves to and its display name. A `-` stands for a display name
 * equal to the object's own. Two readers take it apart differently. The manual
 * search reads the pages and the permalink lookup reads everything that is not
 * one. What they share is the fetch and the parse.
 *
 * The third comment line is the branch that answered, which is the whole of
 * what says the host widened a version. The host redirects a manual it has no
 * branch for to `main` and says nothing about it in the status.
 */
final class Inventory
{
    /** The table of contents of one manual, in the form its own builder writes. */
    private const FILE = 'objects.inv';

    /** One object, as the decompressed listing writes it. */
    private const OBJECT = '/^(.+?) +(\S+:\S+) +(-?\d+) +(\S+) +(.*)$/';

    /**
     * Each inventory as it was last read, under the URL it came from.
     *
     * Every artefact on this host carries an `ETag` and answers `If-None-Match`
     * with a 304 and a zero-byte body. So the second read of a session pays one
     * round trip and no payload for an inventory that is still current. That is
     * what makes it affordable: `D-ANS-065` has the sizes.
     *
     * It is not held in `Http\Recent`, which holds an answer for a chosen while
     * because its source cannot say whether it is still current. This one can,
     * so there is no while to choose.
     *
     * @var array<string, array{etag: string, inventory: array{title: string, branch: string, objects: list<array{name: string, role: string, uri: string, display: string}>}}>
     */
    private static array $held = [];

    public function __construct(private readonly Fetch $reader) {}

    /** What a reader handed in leaves behind: another reader is another host. */
    public static function forget(): void
    {
        self::$held = [];
    }

    /**
     * The inventory of the manual published at one base URL.
     *
     * A read that failed answers the same way a 304 does, with what this holds.
     * The objects are what this host published. A caller that reaches one of
     * them while the host is down gets a stale answer rather than a book that
     * is gone.
     *
     * @return array{title: string, branch: string, objects: list<array{name: string, role: string, uri: string, display: string}>}|null
     */
    public function of(string $base): ?array
    {
        $url = $base . self::FILE;
        $held = self::$held[$url] ?? null;
        $response = $this->reader->read($url, $held === null ? [] : ['If-None-Match: ' . $held['etag']]);
        if ($response['body'] === null) {
            return $held['inventory'] ?? null;
        }

        $inventory = self::parse($response['body']);
        if ($inventory === null) {
            return $held['inventory'] ?? null;
        }
        if ($response['etag'] !== null) {
            self::$held[$url] = ['etag' => $response['etag'], 'inventory' => $inventory];
        }

        return $inventory;
    }

    /**
     * One inventory taken apart.
     *
     * Null is a body that is not an inventory, which is what bot protection
     * answers with a 200 in front of it (`D-ANS-034`). An empty object list is
     * a manual that answered.
     *
     * @return array{title: string, branch: string, objects: list<array{name: string, role: string, uri: string, display: string}>}|null
     */
    private static function parse(string $body): ?array
    {
        $header = explode("\n", $body, 5);
        $listing = @zlib_decode($header[4] ?? '');
        if (!is_string($listing)) {
            return null;
        }

        $objects = [];
        foreach (explode("\n", $listing) as $line) {
            if (preg_match(self::OBJECT, $line, $object) !== 1) {
                continue;
            }
            [, $name, $role, , $uri, $display] = $object;
            $objects[] = [
                'name' => $name,
                'role' => $role,
                'uri' => ltrim($uri, '/'),
                'display' => $display === '-' ? $name : $display,
            ];
        }

        return [
            'title' => self::comment($header[1] ?? '', 'Project'),
            'branch' => self::comment($header[2] ?? '', 'Version'),
            'objects' => $objects,
        ];
    }

    /** What one of the header comments states, or an empty string where it states nothing. */
    private static function comment(string $line, string $field): string
    {
        return preg_match('/^# ' . $field . ': *(.*)$/', $line, $stated) === 1 ? trim($stated[1]) : '';
    }
}
