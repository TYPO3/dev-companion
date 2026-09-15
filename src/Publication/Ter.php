<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Publication;

use TYPO3\DevCompanion\Http\Fetch;

/**
 * The registry a TYPO3 extension goes out to, read over its public API.
 *
 * What it answers is not in the repository under audit, because
 * `ext_emconf.php` reads the same before and after a release (`D-FBK-051`).
 * Read only and without a credential. The versions endpoint declares `security:
 * []` in the TER's own OpenAPI schema. A publication, a transfer and a deletion
 * stay the maintainer's, through Tailor and the token it carries.
 *
 * Nothing stays between calls. This one is a single read per key, at the moment
 * the caller is about to change what it reports. So a store could only ever
 * answer somebody who has just published.
 */
final class Ter
{
    public const HOST = 'https://extensions.typo3.org';

    /**
     * What the registry accepts as an extension key, in its own words. The
     * pattern and the two lengths the `/extension/{key}/versions` route
     * declares. Held here so a composer package name, which carries a slash and
     * a dash, gets the answer "wrong kind of name". Not a read the host answers
     * `400` to.
     */
    public const KEY = '~^[a-z][a-z0-9_]{2,29}$~';

    /**
     * What leaves this process, and the seam a unit test takes instead.
     *
     * `TerLookup` builds its own instance, so a test that drives the tool
     * itself has nowhere else to hand a transport in. That is its text half,
     * which is where a caller reads whether a version is out. `R-COD-003`.
     *
     * @var (\Closure(string): ?string)|null
     */
    private static ?\Closure $transport = null;

    private readonly Fetch $fetch;

    /** @param (\Closure(string): ?string)|null $transport */
    public function __construct(?\Closure $transport = null)
    {
        $this->fetch = new Fetch($transport ?? self::$transport);
    }

    /**
     * What a test hands in, so nothing it drives reaches extensions.typo3.org.
     * Null puts the host back.
     *
     * @param (\Closure(string): ?string)|null $reader
     */
    public static function useReader(?\Closure $reader): void
    {
        self::$transport = $reader;
    }

    /**
     * Every version published under one key, highest number first.
     *
     * A key the registry does not know answers `404` with a JSON body, which is
     * an answer and not a failure. Nothing stands under it. What that does not
     * say is that no such package exists. An extension that ships through
     * Composer alone never registers here at all.
     *
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, versions: list<array<string, mixed>>, cause: ?string}
     */
    public function versions(string $key): array
    {
        $url = self::HOST . '/api/v1/extension/' . rawurlencode($key) . '/versions';
        $response = $this->fetch->read($url, ['Accept: application/json']);

        if ($response['status'] === 404) {
            return ['status' => 'empty', 'url' => $url, 'versions' => [], 'cause' => null];
        }
        if ($response['body'] === null) {
            return ['status' => 'unavailable', 'url' => $url, 'versions' => [], 'cause' => 'source-not-answering'];
        }

        $decoded = Fetch::decode($response['body']);
        if ($decoded === null) {
            return ['status' => 'unavailable', 'url' => $url, 'versions' => [], 'cause' => 'source-not-parseable'];
        }

        // The list arrives inside an array of one, so the versions are a level
        // down from where a reader of the endpoint's name expects them.
        $entries = is_array($decoded[0] ?? null) && !isset($decoded[0]['number']) ? $decoded[0] : $decoded;

        $versions = [];
        foreach ($entries as $entry) {
            if (is_array($entry) && is_string($entry['number'] ?? null)) {
                $versions[] = self::version($entry);
            }
        }
        // By version number here rather than by the order it came in. That
        // order is the registry's and it is not the upload order. So a
        // maintenance release on an older line sits below a newer major and
        // came after it.
        usort($versions, static fn(array $one, array $other): int => version_compare($other['number'], $one['number']));

        return [
            'status' => $versions === [] ? 'empty' : 'answered',
            'url' => $url,
            'versions' => $versions,
            'cause' => null,
        ];
    }

    /**
     * One published version, in the fields a release audit turns on.
     *
     * @param array<string, mixed> $entry
     * @return array<string, mixed>
     */
    private static function version(array $entry): array
    {
        $majors = [];
        foreach (is_array($entry['typo3_versions'] ?? null) ? $entry['typo3_versions'] : [] as $major) {
            if (is_numeric($major)) {
                $majors[] = (int) $major;
            }
        }
        $dependencies = is_array($entry['dependencies'] ?? null) ? $entry['dependencies'] : [];
        $uploaded = $entry['upload_date'] ?? null;

        return [
            'number' => (string) $entry['number'],
            'state' => is_string($entry['state'] ?? null) ? $entry['state'] : '',
            // A unix timestamp on the wire, and a day is what it reads as.
            'uploaded' => is_numeric($uploaded) && (int) $uploaded > 0 ? gmdate('Y-m-d', (int) $uploaded) : '',
            'majors' => $majors,
            'constraint' => is_string($dependencies['typo3'] ?? null) ? $dependencies['typo3'] : '',
        ];
    }
}
