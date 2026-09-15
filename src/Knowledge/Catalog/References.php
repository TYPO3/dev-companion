<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge\Catalog;

use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Paths;

/**
 * Where the TYPO3 core keeps its own worked examples.
 *
 * A hint is a summary of an answer that exists somewhere in full, currently
 * passing and maintained by the people who wrote the subsystem. Three times in
 * one session the real answer was such a directory, and all three times the
 * session reached it by accident. A hint per subject fixes the subject it is
 * for, and the next one repeats it.
 *
 * So the index is its own thing rather than a field on the hints. A subject
 * with no hint yet still has a reference, and "read X" is a better answer than
 * a thin hint. Paths are relative to a core checkout. What a Composer
 * installation has of it follows from `package`, and an entry with none exists
 * only in the repository.
 */
final class References
{
    /**
     * @return array<int, array{id: string, path: string, package: ?string, reference: string, caveat: ?string, hint: ?string, since: ?int, until: ?int}>
     */
    public static function load(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::catalogFile('reference', 'entries.json')), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid catalog/reference/entries.json');
        }

        return array_map(static fn(array $entry): array => [
            'id' => (string) $entry['id'],
            'path' => (string) $entry['path'],
            'package' => isset($entry['package']) ? (string) $entry['package'] : null,
            'reference' => (string) $entry['reference'],
            'caveat' => isset($entry['caveat']) ? (string) $entry['caveat'] : null,
            'hint' => isset($entry['hint']) ? (string) $entry['hint'] : null,
            'since' => isset($entry['since']) ? (int) $entry['since'] : null,
            'until' => isset($entry['until']) ? (int) $entry['until'] : null,
        ], $decoded);
    }

    /**
     * The entries that exist on the version in question.
     *
     * Held back rather than qualified, for the same reason the component
     * catalog does it. A path that is not on that branch wastes the read it
     * asks for. The caller has no way to tell that from a look in the wrong
     * place.
     *
     * @return array<int, array{id: string, path: string, package: ?string, reference: string, caveat: ?string, hint: ?string, since: ?int, until: ?int}>
     */
    public static function on(?int $target): array
    {
        return array_values(array_filter(
            self::load(),
            static fn(array $entry): bool => Versions::holds($entry['since'], $entry['until'], $target),
        ));
    }
}
