<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use TYPO3\DevCompanion\Knowledge\Versions;

/**
 * What `components:derive` writes, built from a set of core checkouts.
 *
 * Apart from the command so that `components:check` can build the same thing
 * and compare it against the committed copy. Derived data nothing reads again
 * is hand-kept data with a longer path to stale, `D-CAT-008`.
 */
final class ComponentDerivation
{
    /**
     * @param array<int, string> $checkouts one core checkout per covered major
     * @return array{classes: list<array<string, mixed>>, elements: list<array<string, mixed>>, listing: list<array<string, mixed>>}
     */
    public static function from(array $checkouts): array
    {
        $styles = [];
        $elements = [];
        $listings = [];
        foreach ($checkouts as $major => $checkout) {
            $css = BackendCss::of($checkout);
            if ($css === null) {
                continue;
            }
            $styles[$major] = $css;
            $elements[$major] = CustomElements::of($checkout);
            $listing = StyleguideListing::of($checkout);
            if ($listing !== null) {
                $listings[$major] = $listing;
            }
        }

        $components = Catalogs::read('component/entries');
        $known = self::known($components, $elements);

        $classes = [];
        foreach ($components as $component) {
            $root = $component['rootClass'] ?? null;
            if (!is_string($root) || $root === '') {
                continue;
            }
            foreach (self::classesOf($component) as $class) {
                $classes[] = self::derive($class, $root, (string) $component['name'], $styles, $known);
            }
        }

        return [
            'classes' => $classes,
            'elements' => self::derivedElements($elements, $listings),
            'listing' => self::derivedListing($listings),
        ];
    }

    /**
     * Which majors ship a styleguide, which is where a reader finds the
     * boundary.
     *
     * @param array<int, string> $checkouts
     * @return list<int>
     */
    public static function listing(array $checkouts): array
    {
        $majors = [];
        foreach ($checkouts as $major => $checkout) {
            if (StyleguideListing::of($checkout) !== null) {
                $majors[] = $major;
            }
        }

        return $majors;
    }

    /**
     * @param array<string, mixed> $component
     * @return list<string>
     */
    private static function classesOf(array $component): array
    {
        $names = [];
        foreach (['variants', 'modifiers', 'subComponents'] as $field) {
            foreach ($component[$field] ?? [] as $name) {
                if (is_string($name) && $name !== '') {
                    $names[$name] = true;
                }
            }
        }

        return array_keys($names);
    }

    /**
     * @param array<int, array<string, mixed>> $components
     * @param array<int, array<string, string>> $elements
     * @return list<string>
     */
    private static function known(array $components, array $elements): array
    {
        $known = [];
        foreach ($components as $component) {
            if (isset($component['rootClass']) && is_string($component['rootClass'])) {
                $known['.' . $component['rootClass']] = true;
            }
            foreach (self::classesOf($component) as $class) {
                $known['.' . $class] = true;
            }
        }
        foreach ($elements as $tags) {
            foreach (array_keys($tags) as $tag) {
                $known[$tag] = true;
            }
        }
        ksort($known);

        return array_keys($known);
    }

    /**
     * @param array<int, BackendCss> $styles
     * @param list<string> $known
     * @return array<string, mixed>
     */
    private static function derive(string $class, string $root, string $component, array $styles, array $known): array
    {
        $present = [];
        $positions = [];
        $within = [];
        foreach ($styles as $major => $css) {
            if (!$css->carries($class)) {
                continue;
            }
            $present[] = $major;
            $position = $css->position($class, $root);
            if ($position !== null) {
                $positions[$position][] = $major;
            }
            foreach ($css->stylesWithin($class, $known) as $name) {
                $within[$name][] = $major;
            }
        }

        $entry = [
            'class' => $class,
            'component' => $component,
            'since' => $present === [] ? null : min($present),
            'positions' => self::ranged($positions, 'position'),
            'stylesWithin' => self::ranged($within, 'name'),
        ];
        $until = self::until($present);
        if ($until !== null) {
            $entry['until'] = $until;
        }

        return $entry;
    }

    /**
     * @param array<string, non-empty-list<int>> $byValue
     * @return list<array<string, mixed>>
     */
    private static function ranged(array $byValue, string $key): array
    {
        $out = [];
        foreach ($byValue as $value => $majors) {
            $entry = [$key => $value, 'since' => min($majors)];
            $until = self::until($majors);
            if ($until !== null) {
                $entry['until'] = $until;
            }
            $out[] = $entry;
        }

        return $out;
    }

    /**
     * The last major a fact holds on, or null where it reaches the newest one.
     * An open range stands as an absent `until` throughout `knowledge/`, so a
     * fact that is still true says nothing rather than names today's major.
     *
     * @param list<int> $majors
     */
    private static function until(array $majors): ?int
    {
        $covered = Versions::majors();
        if ($majors === [] || $covered === []) {
            return null;
        }

        return max($majors) < max($covered) ? max($majors) : null;
    }

    /**
     * What the styleguide of each major lists, which is the public API
     * boundary. A caller may use a component it lists and not one it does not
     * list. Only the listing goes out. Whether a demo shows a single class is
     * not readable out of the templates. A demo renders its markup through a
     * ViewHelper or a web component as often as it writes it. What it writes is
     * as often the styleguide's own page furniture.
     *
     * @param array<int, StyleguideListing> $listings
     * @return list<array<string, mixed>>
     */
    private static function derivedListing(array $listings): array
    {
        $seen = [];
        foreach ($listings as $major => $listing) {
            foreach ($listing->components() as $component) {
                $seen[$component][] = $major;
            }
        }
        ksort($seen);

        $out = [];
        foreach ($seen as $component => $majors) {
            $entry = ['component' => $component, 'since' => min($majors)];
            $until = self::until($majors);
            if ($until !== null) {
                $entry['until'] = $until;
            }
            $out[] = $entry;
        }

        return $out;
    }

    /**
     * @param array<int, array<string, string>> $elements
     * @param array<int, StyleguideListing> $listings
     * @return list<array<string, mixed>>
     */
    private static function derivedElements(array $elements, array $listings): array
    {
        $seen = [];
        foreach ($elements as $major => $tags) {
            foreach ($tags as $tag => $source) {
                $seen[$tag]['majors'][] = $major;
                $seen[$tag]['source'] = $source;
                if (isset($listings[$major]) && $listings[$major]->demonstrates($tag)) {
                    $seen[$tag]['demonstrated'][] = $major;
                }
            }
        }
        ksort($seen);

        $out = [];
        foreach ($seen as $tag => $found) {
            $entry = ['tag' => $tag, 'source' => $found['source'], 'since' => min($found['majors'])];
            $until = self::until($found['majors']);
            if ($until !== null) {
                $entry['until'] = $until;
            }
            // What the styleguide writes is what a package may use — `D-CAT-009`.
            // Null where no demo names it, which is most of them.
            $entry['demonstratedSince'] = isset($found['demonstrated']) ? min($found['demonstrated']) : null;
            $out[] = $entry;
        }

        return $out;
    }
}
