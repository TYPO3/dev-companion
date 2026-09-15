<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge\Catalog;

/**
 * The markup a styleguide demo carries for one component.
 *
 * The styleguide wraps each copyable example in `sg:example`, so what a demo
 * says about a component is those blocks rather than the page around them. Two
 * callers read it. The component answer, where an installed styleguide replaces
 * the snapshot markup. And `bin/cli components:check`, which digests what each
 * checkout carries so a rewrite that leaves every class name in place shows up
 * (D-CAT-001).
 */
final class DemoMarkup
{
    /**
     * The names one demo file has, newest spelling first.
     *
     * The styleguide renamed its templates from `.html` to `.fluid.html`, so an
     * entry records one spelling and a checkout of an older major carries the
     * other. Said here once rather than as a field on each of the entries. The
     * rename is one fact about a release, not one fact per component.
     *
     * @return list<string>
     */
    public static function spellings(string $path): array
    {
        if (!str_ends_with($path, '.fluid.html')) {
            return [$path];
        }

        return [$path, substr($path, 0, -strlen('.fluid.html')) . '.html'];
    }

    /**
     * Every copyable example that carries this component, page chrome left out,
     * at most four. A demo whose examples name no component of that root class
     * answers with all of them; one that wraps nothing in `sg:example` answers
     * with none.
     *
     * `$selector` is the hand-kept index's say over which of them shows the
     * component. The root class does not decide that on its own, `D-CAT-003`. A
     * selection narrows, never widens. Where no example carries the selector,
     * none comes back rather than the first match as a stand-in. The caller
     * keeps the hand-kept markup and says it is a fallback.
     *
     * @return array<int, string>
     */
    public static function examples(string $template, string $rootClass, ?string $selector = null): array
    {
        preg_match_all('#<sg:example\b[^>]*>(.*?)</sg:example>#si', $template, $matches);
        $all = [];
        $matching = [];
        foreach ($matches[1] as $example) {
            $clean = self::withoutTheTemplatesIndentation($example);
            if ($clean === '') {
                continue;
            }
            $all[] = $clean;
            if (self::carries($clean, $rootClass)) {
                $matching[] = $clean;
            }
        }

        $chosen = array_values(array_unique($matching === [] ? $all : $matching));
        if ($selector !== null && $selector !== '') {
            $chosen = array_values(array_filter(
                $chosen,
                static fn(string $example): bool => self::carries($example, $selector),
            ));
        }

        return array_slice($chosen, 0, 4);
    }

    /**
     * Whether a snippet uses this component's root class, rather than merely
     * containing the word.
     *
     * A custom element goes by its tag and everything else by a class
     * attribute. So a demo that renders the component through a ViewHelper
     * carries none of it. That is what tells the digest apart from the markup
     * it holds.
     */
    public static function carries(string $markup, string $rootClass): bool
    {
        if (str_starts_with($rootClass, 'typo3-')) {
            return str_contains($markup, '<' . $rootClass);
        }

        return preg_match(
            '/class=["\'][^"\']*(?<![a-z0-9_-])'
                . preg_quote($rootClass, '/')
                . '(?![a-z0-9_-])[^"\']*["\']/i',
            $markup,
        ) === 1;
    }

    /** Pasteable as it stands: the indentation the template page gave it, gone. */
    private static function withoutTheTemplatesIndentation(string $example): string
    {
        $lines = preg_split('/\R/', trim($example, "\r\n")) ?: [];
        $indent = null;
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            preg_match('/^\s*/', $line, $leading);
            $width = strlen($leading[0] ?? '');
            $indent = $indent === null ? $width : min($indent, $width);
        }

        return trim(implode("\n", array_map(
            static fn(string $line): string => substr($line, $indent ?? 0),
            $lines,
        )));
    }
}
