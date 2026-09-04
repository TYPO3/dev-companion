<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge\Catalog;

use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Paths;

/**
 * Which extensions the TYPO3 core ships, and on which of the covered versions.
 *
 * "Is this extension part of the core" is asked in both directions and answered
 * from memory in both: a community package cited as evidence of core direction,
 * and a system extension nobody knew existed. Neither is answerable from an
 * installation — the interesting case is an extension that is not installed,
 * which is exactly when the question comes up.
 *
 * So it is a catalog rather than a lookup against the installation: read off one
 * checkout per covered version, with the range each key holds on, and
 * re-derivable by bin/cli system-extensions:check when a core release adds or drops one.
 */
final class SystemExtensions
{
    /**
     * @return array<int, array{key: string, package: string, description: string, forgeCategory: string, since: ?int, until: ?int}>
     */
    public static function load(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::catalogFile('system-extension', 'entries.json')), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid catalog/system-extensions.json');
        }

        return array_map(static fn(array $entry): array => [
            'key' => (string) $entry['key'],
            'package' => (string) $entry['package'],
            'description' => (string) ($entry['description'] ?? ''),
            // The area the core files this extension's issues under, and
            // only where the key does not reach it by its own spelling —
            // `D-ANS-142`. Empty for an extension whose issues land in the
            // general areas, which is most of them.
            'forgeCategory' => (string) ($entry['forgeCategory'] ?? ''),
            'since' => isset($entry['since']) ? (int) $entry['since'] : null,
            'until' => isset($entry['until']) ? (int) $entry['until'] : null,
        ], $decoded);
    }

    /**
     * The entries a query names, on the version it asks about.
     *
     * A query is matched against the extension key and the Composer package
     * name before the description, because that is what a caller has in hand:
     * "typo3/cms-content-blocks", "theme_camino", "impexp". Both spellings of
     * the same thing therefore find it — the key with underscores and the
     * package with dashes.
     *
     * @return array<int, array{key: string, package: string, description: string, forgeCategory: string, since: ?int, until: ?int}>
     */
    public static function find(string $query, ?int $target = null): array
    {
        $entries = array_values(array_filter(
            self::load(),
            static fn(array $entry): bool => Versions::holds($entry['since'], $entry['until'], $target),
        ));

        $query = trim(mb_strtolower($query));
        if ($query === '') {
            return $entries;
        }

        $needle = str_replace(['typo3/cms-', 'typo3/', '-'], ['', '', '_'], $query);

        $named = array_values(array_filter(
            $entries,
            static fn(array $entry): bool => $entry['key'] === $needle
                || mb_strtolower($entry['package']) === $query
                || str_contains($entry['key'], $needle),
        ));
        if ($named !== []) {
            return $named;
        }

        return array_values(array_filter(
            $entries,
            static fn(array $entry): bool => str_contains(mb_strtolower($entry['description']), $query),
        ));
    }

    /**
     * The issue tracker area a word names by being an extension key.
     *
     * A caller standing in `typo3/sysext/impexp/` holds that key and nothing
     * else, and half the keys reach no area of their own name — `D-ANS-142`.
     * The key and the Composer package are both accepted, matched whole,
     * because a substring here would answer "form" with the area of another
     * extension. Empty where the word names no extension or the extension has
     * no area of its own.
     */
    public static function forgeCategory(string $word): string
    {
        $word = trim(mb_strtolower($word));
        foreach (self::load() as $entry) {
            if ($word === $entry['key'] || $word === mb_strtolower($entry['package'])) {
                return $entry['forgeCategory'];
            }
        }

        return '';
    }
}
