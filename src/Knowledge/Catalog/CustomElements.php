<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge\Catalog;

use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Paths;

/**
 * The custom elements the core declares, and which of them a package may use.
 *
 * An element cannot attach to the wrong node, which is the whole of what went
 * wrong with a borrowed class name. So where one exists it is the answer and
 * the class is the way round it, `D-CAT-009`.
 *
 * Only what a styleguide demo writes goes out. The core declares 137 and
 * demonstrates twelve. The rest are the backend's own, and one of them in an
 * answer would be the mistake this catalog is otherwise careful about. A tag
 * survives that read where a class name does not. A demo builds class names in
 * a loop and never builds a tag name.
 */
final class CustomElements
{
    /** @var list<array<string, mixed>>|null */
    private static ?array $elements = null;

    /**
     * The elements the query names outright, offered for that version.
     *
     * Matched on the tag rather than on prose: `combobox` reaches
     * `typo3-backend-combobox`, and a sentence about something else reaches
     * nothing. The prefix every tag carries is not a term, so a query naming it
     * matches on the rest or not at all.
     *
     * @return list<array<string, mixed>>
     */
    public static function named(?string $query, ?int $target): array
    {
        $terms = self::terms($query ?? '');
        if ($terms === []) {
            return [];
        }

        $found = [];
        foreach (self::read() as $element) {
            $tag = (string) $element['tag'];
            if (!self::holdsOn($element, $target) || $element['demonstratedSince'] === null) {
                continue;
            }
            if (!Versions::holds((int) $element['demonstratedSince'], null, $target ?? (int) $element['demonstratedSince'])) {
                continue;
            }
            foreach ($terms as $term) {
                if (str_contains(self::withoutPrefix($tag), $term)) {
                    $found[] = [
                        'tag' => $tag,
                        'source' => (string) $element['source'],
                        'since' => (int) $element['since'],
                        'until' => $element['until'] ?? null,
                        'verifiedOn' => Versions::label((int) $element['since'], $element['until'] ?? null),
                    ];
                    break;
                }
            }
        }

        return $found;
    }

    /** @param array<string, mixed> $element */
    private static function holdsOn(array $element, ?int $target): bool
    {
        return $target === null || Versions::holds((int) $element['since'], $element['until'] ?? null, $target);
    }

    /** @return list<string> */
    private static function terms(string $query): array
    {
        $terms = [];
        foreach (preg_split('/[^a-z0-9-]+/i', strtolower(trim($query))) ?: [] as $term) {
            if (strlen($term) > 3 && !in_array($term, ['typo3', 'backend', 'element', 'component'], true)) {
                $terms[$term] = true;
            }
        }

        return array_keys($terms);
    }

    private static function withoutPrefix(string $tag): string
    {
        return (string) preg_replace('/^typo3-(backend-)?/', '', $tag);
    }

    /** @return list<array<string, mixed>> */
    private static function read(): array
    {
        if (self::$elements !== null) {
            return self::$elements;
        }

        $path = Paths::catalogFile('component', 'elements.json');
        $decoded = is_file($path) ? json_decode((string) file_get_contents($path), true) : [];
        $elements = [];
        foreach (is_array($decoded) ? $decoded : [] as $element) {
            if (is_array($element) && isset($element['tag'], $element['since'], $element['source'])) {
                $elements[] = $element;
            }
        }

        return self::$elements = $elements;
    }
}
