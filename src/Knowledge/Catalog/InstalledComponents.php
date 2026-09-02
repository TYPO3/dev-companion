<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge\Catalog;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Installation\Instance;

/**
 * Re-reads the component contract from the packages of the active installation.
 *
 * The bundled catalog remains the curated index — names, summaries, keywords,
 * and the mapping from one component to its styleguide demo — while the
 * installed backend CSS decides whether that component and its recorded class
 * and custom-property names actually exist. Where the styleguide package is
 * installed, the example the index selects replaces the snapshot markup
 * (`D-CAT-003`, `demoSelector`), and where no example shows it copyably the
 * entry says so with `demoDerives` and the curated markup stays.
 */
final class InstalledComponents
{
    public static function isAvailable(?int $target): bool
    {
        $installedVersion = Instance::typo3Version();
        $installedMajor = Instance::typo3Major();
        $backend = Instance::packages()['backend'] ?? null;

        return $installedVersion !== null
            && $installedMajor !== null
            && $target === $installedMajor
            && $backend !== null
            && is_file($backend . '/Resources/Public/Css/backend.css');
    }

    /**
     * @param array<int, array<string, mixed>> $components
     * @return array<int, array<string, mixed>>|null
     */
    public static function derive(array $components, ?int $target): ?array
    {
        if (!self::isAvailable($target)) {
            return null;
        }

        $installedVersion = (string) Instance::typo3Version();
        $packages = Instance::packages();
        $cssFile = $packages['backend'] . '/Resources/Public/Css/backend.css';
        $css = (string) file_get_contents($cssFile);
        $cssClasses = self::tokens($css, '/\.([a-z][a-z0-9_-]*)/i');
        $customProperties = self::tokens($css, '/(--[a-z][a-z0-9_-]*)/i');
        $javascript = self::javascript($packages);

        foreach ($components as &$component) {
            $rootClass = (string) $component['rootClass'];
            $customElementSource = self::sourceCarrying($javascript, $rootClass);
            $component['_installedPresent'] = isset($cssClasses[$rootClass]) || $customElementSource !== null;
            $component['_installed'] = true;
            $component['contractVersion'] = $installedVersion;
            $component['classes'] = self::componentClasses($component, $cssClasses);
            foreach (['variants', 'modifiers', 'subComponents'] as $field) {
                $component[$field] = array_values(array_filter(
                    $component[$field],
                    static fn(string $class): bool => isset($cssClasses[$class]),
                ));
            }
            $component['customProperties'] = self::componentProperties($component, $customProperties);
            $component['dataAttributes'] = self::dataAttributes($component, $packages);

            $sources = ['EXT:backend/Resources/Public/Css/backend.css'];
            if ($customElementSource !== null) {
                $sources[] = $customElementSource;
            }
            if ($component['dataAttributes'] !== [] && $component['jsModule'] !== null) {
                $sources[] = (string) $component['jsModule'];
            }
            foreach ($component['sassPaths'] as $path) {
                if (Instance::describe()['kind'] === Instance::KIND_CORE_CHECKOUT
                    && is_file((string) Instance::root() . '/' . $path)
                ) {
                    $sources[] = $path;
                }
            }

            $component['markupSource'] = 'catalog';
            // An entry that says its demo shows the component nowhere copyable
            // is not read at all: the file is a page built out of the component
            // rather than a gallery of it, so there is no example to select and
            // taking one would only move which scaffolding is handed over. The
            // curated markup stays, labelled as the fallback it is.
            $demo = ($component['demoDerives'] ?? true) === false
                ? null
                : self::installedPath((string) ($component['demoPath'] ?? ''), $packages);
            if ($demo !== null) {
                [$file, $reference] = $demo;
                $sources[] = $reference;
                $examples = DemoMarkup::examples(
                    (string) file_get_contents($file),
                    $rootClass,
                    isset($component['demoSelector']) ? (string) $component['demoSelector'] : null,
                );
                if ($examples !== []) {
                    $component['markup'] = array_shift($examples);
                    $component['examples'] = $examples;
                    $component['markupSource'] = 'installation';
                }
            }
            $component['sourceFiles'] = array_values(array_unique($sources));
        }
        unset($component);

        return $components;
    }

    /**
     * @param array<string, mixed> $component
     * @param array<string, true> $classes
     * @return array<int, string>
     */
    private static function componentClasses(array $component, array $classes): array
    {
        $roots = [(string) $component['rootClass'], (string) $component['name']];
        $recorded = array_merge(
            $component['variants'],
            $component['modifiers'],
            $component['subComponents'],
        );

        $found = [];
        foreach (array_keys($classes) as $class) {
            foreach ($roots as $root) {
                if ($class === $root || str_starts_with($class, $root . '-')) {
                    $found[$class] = true;
                }
            }
        }
        foreach ($recorded as $class) {
            if (isset($classes[$class])) {
                $found[$class] = true;
            }
        }
        ksort($found);

        return array_keys($found);
    }

    /**
     * @param array<string, mixed> $component
     * @param array<string, true> $properties
     * @return array<int, string>
     */
    private static function componentProperties(array $component, array $properties): array
    {
        $needles = array_filter([
            str_replace('typo3-backend-', '', (string) $component['rootClass']),
            (string) $component['name'],
        ]);
        $variantNames = [];
        foreach ($component['variants'] as $variant) {
            foreach ($needles as $needle) {
                if (str_starts_with($variant, $needle . '-')) {
                    $variantNames[] = substr($variant, strlen($needle) + 1);
                }
            }
        }
        $found = [];
        foreach (array_keys($properties) as $property) {
            foreach ($variantNames as $variant) {
                if (str_contains($property, '-' . $variant . '-')) {
                    continue 2;
                }
            }
            foreach ($needles as $needle) {
                if (str_contains($property, $needle)) {
                    $found[$property] = true;
                }
            }
        }
        foreach ($component['customProperties'] as $property) {
            if (isset($properties[$property])) {
                $found[$property] = true;
            }
        }
        ksort($found);

        return array_keys($found);
    }

    /**
     * @return array<string, true>
     */
    private static function tokens(string $source, string $pattern): array
    {
        preg_match_all($pattern, $source, $matches);
        $tokens = array_fill_keys(array_map('strtolower', $matches[1] ?? []), true);
        ksort($tokens);

        return $tokens;
    }

    /**
     * The data attributes a component's own module reads off its markup.
     *
     * The classes are the contract a component is styled by and these are the
     * contract it is driven by, and only the first was answered: a session
     * wrote `data-bs-content` on a modal that reads `data-content`, and copied
     * a `data-on-change` that one extension's own module implements. Both
     * failed silently in a browser — `D-ANS-139`.
     *
     * Which module belongs to the component is curated, because a file named
     * after the component is the wrong rule: `pagination.js` in the search
     * extension is not the backend's pagination. What it reads is derived from
     * the installed file, so it moves with the version the way the classes do.
     *
     * @param array<string, mixed>  $component
     * @param array<string, string> $packages
     * @return array<int, string>
     */
    private static function dataAttributes(array $component, array $packages): array
    {
        $module = $component['jsModule'] ?? null;
        if (!is_string($module) || preg_match('#^EXT:([^/]+)/(.+)$#', $module, $matches) !== 1) {
            return [];
        }
        $package = $packages[$matches[1]] ?? null;
        if ($package === null || !is_file($package . '/' . $matches[2])) {
            return [];
        }

        preg_match_all(
            '/dataset\.([a-zA-Z][a-zA-Z0-9]*)/',
            (string) file_get_contents($package . '/' . $matches[2]),
            $reads,
        );
        $attributes = [];
        foreach (array_unique($reads[1]) as $property) {
            // dataset.buttonOkText is data-button-ok-text: the DOM maps one to
            // the other, and the attribute is what a template writes.
            $attributes[] = 'data-' . mb_strtolower((string) preg_replace('/([A-Z])/', '-$1', $property));
        }
        sort($attributes);

        return $attributes;
    }

    /**
     * @param array<string, string> $packages
     * @return array<string, string> source contents to EXT: reference
     */
    private static function javascript(array $packages): array
    {
        $sources = [];
        foreach ($packages as $key => $package) {
            $directory = $package . '/Resources/Public/JavaScript';
            if (!is_dir($directory)) {
                continue;
            }
            foreach (Finder::create()->files()->in($directory)->name('*.js')->sortByName() as $file) {
                $relative = substr($file->getPathname(), strlen($package) + 1);
                $sources[(string) file_get_contents($file->getPathname())] = 'EXT:' . $key . '/' . $relative;
            }
        }

        return $sources;
    }

    /**
     * @param array<string, string> $sources
     */
    private static function sourceCarrying(array $sources, string $token): ?string
    {
        if (!str_starts_with($token, 'typo3-')) {
            return null;
        }
        foreach ($sources as $source => $reference) {
            if (str_contains($source, $token)) {
                return $reference;
            }
        }

        return null;
    }

    /**
     * @param array<string, string> $packages
     * @return array{0: string, 1: string}|null
     */
    private static function installedPath(string $corePath, array $packages): ?array
    {
        if (preg_match('#^typo3/sysext/([^/]+)/(.+)$#', $corePath, $matches) !== 1) {
            return null;
        }
        $package = $packages[$matches[1]] ?? null;
        if ($package === null) {
            return null;
        }
        foreach (DemoMarkup::spellings($matches[2]) as $inside) {
            if (is_file($package . '/' . $inside)) {
                return [$package . '/' . $inside, 'EXT:' . $matches[1] . '/' . $inside];
            }
        }

        return null;
    }
}
