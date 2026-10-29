<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Publication;

use TYPO3\DevCompanion\Http\Fetch;

/**
 * The Composer registry, searched for the packages of type
 * `typo3-cms-extension`.
 *
 * A project installs an extension as such a package, and one can stand here
 * and nowhere else. So a search of the TER alone misses it (`D-ANS-193`). Read
 * only and without a credential, like `Ter`.
 */
final class Packagist
{
    public const HOST = 'https://packagist.org';

    /** @var (\Closure(string): ?string)|null */
    private static ?\Closure $transport = null;

    private readonly Fetch $fetch;

    /** @param (\Closure(string): ?string)|null $transport */
    public function __construct(?\Closure $transport = null)
    {
        $this->fetch = new Fetch($transport ?? self::$transport);
    }

    /**
     * What a test hands in, so nothing it drives reaches packagist.org. Null
     * puts the host back.
     *
     * @param (\Closure(string): ?string)|null $reader
     */
    public static function useReader(?\Closure $reader): void
    {
        self::$transport = $reader;
    }

    /**
     * The extension packages that match a query, in the order Packagist ranks
     * them.
     *
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, total: int, packages: list<array{package: string, description: string, downloads: int}>, cause: ?string}
     */
    public function search(string $query, int $limit): array
    {
        $url = self::HOST . '/search.json?' . http_build_query([
            'q' => $query,
            'type' => 'typo3-cms-extension',
            'per_page' => $limit,
        ], '', '&', PHP_QUERY_RFC3986);
        $body = $this->fetch->get($url, ['Accept: application/json']);
        if ($body === null) {
            return ['status' => 'unavailable', 'url' => $url, 'total' => 0, 'packages' => [], 'cause' => 'source-not-answering'];
        }
        $decoded = Fetch::decode($body);
        if ($decoded === null || !is_array($decoded['results'] ?? null)) {
            return ['status' => 'unavailable', 'url' => $url, 'total' => 0, 'packages' => [], 'cause' => 'source-not-parseable'];
        }

        $packages = [];
        foreach ($decoded['results'] as $result) {
            if (is_array($result) && is_string($result['name'] ?? null)) {
                $packages[] = [
                    'package' => $result['name'],
                    'description' => is_string($result['description'] ?? null) ? $result['description'] : '',
                    'downloads' => is_int($result['downloads'] ?? null) ? $result['downloads'] : 0,
                ];
            }
        }

        return [
            'status' => $packages === [] ? 'empty' : 'answered',
            'url' => $url,
            'total' => is_int($decoded['total'] ?? null) ? $decoded['total'] : count($packages),
            'packages' => $packages,
            'cause' => null,
        ];
    }
}
