<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge\Catalog;

use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Paths;

/**
 * Where one backend class sits, and the majors that holds on.
 *
 * `bin/cli components:derive` derives it from the `backend.css` each covered
 * branch commits, and this reads it rather than keeps it by hand. The lists in
 * an entry name a class and cannot place it, which is how `table-fit` landed on
 * the wrong node, `D-CAT-008`.
 *
 * A position is what the core's own selectors write and never a promise that
 * the class is a must. Where no selector places a class, this answers nothing,
 * which is the honest reading for a modifier that carries no position to get
 * wrong.
 */
final class ComponentClasses
{
    /** @var array<string, array<string, mixed>>|null */
    private static ?array $byClass = null;

    /**
     * Where the class sits relative to its component's root on that major:
     * `around` it, `on` it, or `below` it. Null where the stylesheet places it
     * nowhere, and null where the caller named no major. A position that moved
     * between two majors has no one answer for both.
     */
    public static function position(string $class, ?int $target): ?string
    {
        if ($target === null) {
            return null;
        }
        foreach (self::read()[$class]['positions'] ?? [] as $position) {
            if (Versions::holds($position['since'] ?? null, $position['until'] ?? null, $target)) {
                return (string) $position['position'];
            }
        }

        return null;
    }

    /**
     * What the core styles inside the class on that major, out of the names the
     * catalog knows. Inventory rather than structure: a progress bar below a
     * wrapper gets its style if it is there and belongs there by nothing.
     *
     * @return list<string>
     */
    public static function stylesWithin(string $class, ?int $target): array
    {
        if ($target === null) {
            return [];
        }
        $within = [];
        foreach (self::read()[$class]['stylesWithin'] ?? [] as $name) {
            if (Versions::holds($name['since'] ?? null, $name['until'] ?? null, $target)) {
                $within[] = (string) $name['name'];
            }
        }

        return $within;
    }

    /**
     * The majors the class itself stands on, as `since` and `until`. This is
     * per class where the entry binds its whole list at once. So a class that
     * was there all along answers on a major its entry stays back for, the
     * range `D-CAT-006` reached for.
     *
     * @return array{since: ?int, until: ?int}
     */
    public static function range(string $class): array
    {
        $entry = self::read()[$class] ?? [];

        return [
            'since' => isset($entry['since']) ? (int) $entry['since'] : null,
            'until' => isset($entry['until']) ? (int) $entry['until'] : null,
        ];
    }

    public static function knows(string $class): bool
    {
        return isset(self::read()[$class]);
    }

    /** @return array<string, array<string, mixed>> */
    private static function read(): array
    {
        if (self::$byClass !== null) {
            return self::$byClass;
        }

        $path = Paths::catalogFile('component', 'classes.json');
        $decoded = is_file($path) ? json_decode((string) file_get_contents($path), true) : [];
        $byClass = [];
        foreach (is_array($decoded) ? $decoded : [] as $entry) {
            if (is_array($entry) && isset($entry['class']) && is_string($entry['class'])) {
                $byClass[$entry['class']] = $entry;
            }
        }

        return self::$byClass = $byClass;
    }
}
