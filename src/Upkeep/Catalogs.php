<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use TYPO3\DevCompanion\Paths;

/**
 * The bundled catalogs below knowledge/catalog/, as they are on disk.
 *
 * Read rather than validated. `components:check`, `references:check` and
 * `system-extensions:check` hold what a catalog says to the core checkouts, and
 * `components:paths` to one checkout of the caller's own. All four start here.
 */
final class Catalogs
{
    /** @return array<int, array<string, mixed>> */
    public static function read(string $name): array
    {
        $path = Paths::root() . '/knowledge/catalog/' . $name . '.json';

        return json_decode((string) file_get_contents($path), true);
    }
}
