<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

use Symfony\Component\Yaml\Yaml;

/**
 * The JSON and YAML an installation ships, decoded — or nothing where the file
 * will not decode.
 *
 * A manifest or a configuration file mid-edit is a state a repository is in for
 * real, and one unreadable file must not cost the rest of the answer. The
 * caller reads a key out of what comes back and gets the same nothing it gets
 * for a key that was never there.
 *
 * Here rather than three times over, which is where it was. `Node`, `Project`
 * and `Extension` each had both readers, and the copies had already drifted.
 * One of the three guarded the YAML with `is_file()` and two left it to the
 * parser to throw.
 */
final class Data
{
    /** @return array<string, mixed> */
    public static function json(string $file): array
    {
        if (!is_file($file)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($file), true);

        return is_array($decoded) ? $decoded : [];
    }

    /** @return array<string, mixed> */
    public static function yaml(string $file): array
    {
        if (!is_file($file)) {
            return [];
        }

        try {
            $parsed = Yaml::parseFile($file);
        } catch (\Throwable) {
            return [];
        }

        return is_array($parsed) ? $parsed : [];
    }
}
