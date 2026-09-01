<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Knowledge\Catalog\TranslationDomain;

/**
 * The labels shipped by the packages and site configuration of the discovered
 * installation, read from their XLF files.
 *
 * `language:domain:search` is the better answer and stays the first one asked:
 * it knows the assembled runtime state, including the overrides an installation
 * applies through `LANG/resourceOverrides`. But it needs a console that boots,
 * and booting needs a migrated database — while a fresh clone, a colleague
 * onboarding, and CI all spend their time not having one. The labels are in the
 * files either way, next to the icons that are already read from there.
 *
 * Only the source files are read, never a locale variant: `de.locallang.xlf`
 * is a translation of a file that is itself the label.
 */
final class Labels
{
    /** Where a package keeps general label files, relative to its root. */
    private const LANGUAGE_DIRECTORY = 'Resources/Private/Language';

    /** Where a project keeps its site configuration, relative to its root. */
    private const SITE_DIRECTORY = 'config/sites';

    /**
     * Every label of every installed package and project site, or of one
     * package where an extension key narrows the call.
     *
     * @return array<int, array{
     *     ref: string,
     *     domain: string,
     *     key: string,
     *     source: string,
     *     resource: string,
     *     conventionalName: bool,
     *     references: array<int, string>,
     *     location: string
     * }>
     */
    public static function all(string $extension = ''): array
    {
        $instance = Instance::describe();
        if ($instance === null) {
            return [];
        }

        $packages = Instance::packages();
        $files = [];
        foreach ($packages as $key => $path) {
            if ($extension === '' || $key === $extension) {
                array_push($files, ...self::packageFiles($key, $path));
            }
        }
        if ($extension === '') {
            array_push($files, ...self::siteFiles($instance['root']));
        }

        $references = LabelReference::find($files, $instance['root'], $packages);
        $labels = [];
        foreach ($files as $file) {
            foreach (self::units($file['absolute']) as $id => $source) {
                $labels[] = [
                    'ref' => $file['domain'] === ''
                        ? 'LLL:' . $file['resource'] . ':' . $id
                        : $file['domain'] . ':' . $id,
                    'domain' => $file['domain'],
                    'key' => $id,
                    'source' => $source,
                    'resource' => $file['resource'],
                    'conventionalName' => $file['conventionalName'],
                    'references' => $references[$file['resource']] ?? [],
                    'location' => $file['location'],
                ];
            }
        }

        return $labels;
    }

    /**
     * The label files of one package: everything below the language and site-set
     * directories TYPO3's LabelFileResolver searches.
     *
     * @return array<int, array{
     *     absolute: string,
     *     resource: string,
     *     domain: string,
     *     conventionalName: bool,
     *     implicitReferences: array<int, string>,
     *     location: string
     * }>
     */
    private static function packageFiles(string $key, string $packagePath): array
    {
        $language = $packagePath . '/' . self::LANGUAGE_DIRECTORY;
        $sets = $packagePath . '/Configuration/Sets';

        $found = [];
        if (is_dir($language)) {
            $found[] = Finder::create()->files()->in($language)->name('*.xlf')->sortByName();
        }
        if (is_dir($sets)) {
            $found[] = Finder::create()->files()->in($sets)->name('*.xlf')->sortByName();
        }

        $files = [];
        foreach ($found as $finder) {
            foreach ($finder as $file) {
                // A locale prefix means a translation of a file that is itself
                // in this list, and its trans-unit ids are the same ones.
                if (preg_match('/^[a-z]{2}([_-][A-Za-z]{2,3})?\./', $file->getFilename()) === 1) {
                    continue;
                }
                $relative = substr($file->getPathname(), strlen($packagePath) + 1);
                $resource = 'EXT:' . $key . '/' . $relative;
                $domain = TranslationDomain::fromReference($resource);
                if ($domain === null) {
                    continue;
                }
                $implicit = [];
                if (str_starts_with($relative, 'Configuration/Sets/')
                    && $file->getFilename() === 'labels.xlf'
                    && self::usesImplicitLabels($file->getPath() . '/config.yaml')) {
                    $implicit[] = 'EXT:' . $key . '/' . dirname($relative) . '/config.yaml (implicit labels.xlf)';
                }
                $files[] = [
                    'absolute' => $file->getPathname(),
                    'resource' => $resource,
                    'domain' => $domain,
                    'conventionalName' => self::isConventional($relative),
                    'implicitReferences' => $implicit,
                    'location' => str_starts_with($relative, 'Configuration/Sets/') ? 'site-set' : 'package',
                ];
            }
        }

        return $files;
    }

    /**
     * XLF resources kept with project site configuration. TYPO3 does not
     * enumerate this directory as package labels, so every one needs an explicit
     * reference and the lookup reports that boundary.
     *
     * @return array<int, array{
     *     absolute: string,
     *     resource: string,
     *     domain: string,
     *     conventionalName: bool,
     *     implicitReferences: array<int, string>,
     *     location: string
     * }>
     */
    private static function siteFiles(string $root): array
    {
        $directory = $root . '/' . self::SITE_DIRECTORY;
        if (!is_dir($directory)) {
            return [];
        }

        $files = [];
        foreach (Finder::create()->files()->in($directory)->name('*.xlf')->sortByName() as $file) {
            if (preg_match('/^[a-z]{2}([_-][A-Za-z]{2,3})?\./', $file->getFilename()) === 1) {
                continue;
            }
            $relative = substr($file->getPathname(), strlen($root) + 1);
            $files[] = [
                'absolute' => $file->getPathname(),
                'resource' => $relative,
                'domain' => '',
                'conventionalName' => $file->getFilename() === 'labels.xlf',
                'implicitReferences' => [],
                'location' => 'project-site',
            ];
        }

        return $files;
    }

    private static function isConventional(string $relative): bool
    {
        if (str_starts_with($relative, 'Configuration/Sets/')) {
            return basename($relative) === 'labels.xlf';
        }

        return preg_match('/^locallang(?:_[^.]+)?\.xlf$/', basename($relative)) === 1;
    }

    private static function usesImplicitLabels(string $configuration): bool
    {
        if (!is_file($configuration)) {
            return false;
        }
        $content = file_get_contents($configuration);

        return is_string($content) && preg_match('/^labels\s*:/m', $content) !== 1;
    }

    /**
     * The trans-unit ids and source texts of one XLF file.
     *
     * Read with local-name(), because the same file is written with and without
     * the XLIFF namespace depending on how old it is, and a namespace-aware
     * query would silently return nothing for half of them.
     *
     * @return array<string, string>
     */
    private static function units(string $file): array
    {
        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string((string) file_get_contents($file));
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($xml === false) {
            return [];
        }

        $units = [];
        foreach ($xml->xpath('//*[local-name()="trans-unit"]') ?: [] as $unit) {
            $id = (string) ($unit['id'] ?? '');
            $source = $unit->xpath('*[local-name()="source"]');
            if ($id === '' || $source === [] || $source === null) {
                continue;
            }
            $units[$id] = trim((string) $source[0]);
        }

        return $units;
    }
}
