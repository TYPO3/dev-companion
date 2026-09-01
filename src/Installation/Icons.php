<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

/**
 * The icon identifiers registered in the discovered installation.
 *
 * Unlike labels or backend modules there is no console command that exposes the
 * icon registry, so it is asked for twice over. `Typo3Runtime` boots the
 * installation in a subprocess and reads the registry itself, which is the only
 * source that knows what a package registers dynamically. Where that cannot be
 * done — no console, or a system without configuration, which is the ordinary
 * state of an extension repository — the three places TYPO3 assembles the
 * registry from are read instead: the T3Icons set shipped with the core, the
 * Configuration/Icons.php of every installed package, and the flag images.
 *
 * Nothing is ever included in this process. The registration files are parsed
 * here; where they are executed, it is TYPO3 executing them in a process of its
 * own, with its own autoloader and its own PHP.
 *
 * The two answers are not worth the same, and `limitation()` is what says so.
 */
final class Icons
{
    public const SOURCE_T3ICONS = 't3icons';
    public const SOURCE_FLAGS = 'flags';

    /** @var array<int, array{identifier: string, category: string, aliasOf: ?string, source: string}>|null */
    private static ?array $icons = null;

    /**
     * Every registered identifier with the package that registers it.
     *
     * @return array<int, array{identifier: string, category: string, aliasOf: ?string, source: string}>
     */
    public static function all(): array
    {
        if (self::$icons !== null) {
            return self::$icons;
        }

        $packages = Instance::packages();
        if ($packages === []) {
            // Not remembered: there being nothing to read is a state of the
            // machine, and the caller who reads that answer is the one likely
            // to change it before asking again.
            return [];
        }

        $icons = [];
        foreach (self::fromT3Icons($packages['core'] ?? null) as $icon) {
            $icons[$icon['identifier']] = $icon;
        }
        foreach ($packages as $key => $path) {
            foreach (self::fromRegistrationFile($path) as $identifier) {
                $icons[$identifier] = [
                    'identifier' => $identifier,
                    'category' => self::category($identifier),
                    'aliasOf' => null,
                    'source' => 'EXT:' . $key . '/Configuration/Icons.php',
                ];
            }
        }
        foreach (self::fromFlags($packages['core'] ?? null) as $identifier) {
            $icons[$identifier] = [
                'identifier' => $identifier,
                'category' => 'flags',
                'aliasOf' => null,
                'source' => self::SOURCE_FLAGS,
            ];
        }

        $icons = self::confirmed($icons);
        ksort($icons);

        return self::$icons = array_values($icons);
    }

    /**
     * The registry as the booted installation has it, where it could be asked.
     *
     * Reading the files gets the registration shapes a parser can follow. What
     * it cannot follow is a list built in a loop, an identifier assembled from
     * a variable, a `registerIcon()` call in ext_localconf.php, and the entries
     * TYPO3 derives from TCA — measured against a site with news installed, 25
     * of 1314 identifiers exist only after the boot, and none of the 1289 read
     * from files was wrong. So the runtime decides which identifiers there are,
     * and the files keep saying where each one comes from.
     *
     * @param array<string, array{identifier: string, category: string, aliasOf: ?string, source: string}> $parsed
     * @return array<string, array{identifier: string, category: string, aliasOf: ?string, source: string}>
     */
    private static function confirmed(array $parsed): array
    {
        $registered = Typo3Runtime::topic('icons');
        if (!is_array($registered) || $registered === []) {
            return $parsed;
        }

        $icons = [];
        foreach ($registered as $identifier => $source) {
            $identifier = strtolower((string) $identifier);
            $icons[$identifier] = $parsed[$identifier] ?? [
                'identifier' => $identifier,
                'category' => self::category($identifier),
                'aliasOf' => null,
                // The file the registry resolves it to, which is the only
                // attribution a runtime registration carries.
                'source' => is_string($source) && $source !== '' ? $source : 'runtime',
            ];
        }

        return $icons;
    }

    /**
     * The content elements an identifier is the icon of, in this installation.
     *
     * "Registered" and "free to use" are different questions and a lookup only
     * answers the first — the session that read a yes as the second put the
     * core HTML element's icon on a content element of its own
     * (`D-ANS-131`). What this adds is the one binding the installation can be
     * asked for without a further boot: the item icon of each CType, which the
     * probe already reads for `typo3_extension_describe`.
     *
     * Empty where the installation did not answer, which is the same silence as
     * a CType nothing binds.
     *
     * @return array<int, string>
     */
    public static function boundTo(string $identifier): array
    {
        $elements = Typo3Runtime::topic('contentElements');
        if (!is_array($elements)) {
            return [];
        }

        $bound = [];
        foreach ($elements as $value => $element) {
            $icon = is_array($element) ? ($element['icon'] ?? '') : '';
            if (is_string($icon) && strtolower($icon) === strtolower($identifier)) {
                $bound[] = 'tt_content.CType=' . $value;
            }
        }
        sort($bound);

        return $bound;
    }

    /**
     * Where the answer comes from, in the vocabulary every tool reports it in:
     * `installation` for the booted registry, `packages` for the files.
     *
     * Two answers of different worth must not read alike. A file-read registry
     * is complete for everything declared and silent about everything else, and
     * a caller comparing it against a directory of SVGs would report icons as
     * unregistered that are registered in a loop.
     */
    public static function answeredBy(): string
    {
        $registered = Typo3Runtime::topic('icons');

        return is_array($registered) && $registered !== [] ? 'installation' : 'packages';
    }

    /**
     * What a file-read registry leaves out, with why it was read that way.
     * Empty where the installation itself answered.
     */
    public static function limitation(): string
    {
        if (self::answeredBy() === 'installation') {
            return '';
        }

        $reason = Typo3Runtime::reason();

        return 'read from the package files rather than from the booted installation'
            . ($reason === '' ? '' : ' — ' . $reason)
            . '. Identifiers a package builds in a loop or registers from ext_localconf.php, and the ones '
            . 'TYPO3 derives from TCA, are not in it';
    }

    /**
     * Drops the memoized registry.
     *
     * Called at the end of every tool call, for the reason `Registry::call`
     * carries: it sits on the runtime reading, so it goes when that goes. Also
     * what a recording and a test move between two installations with.
     */
    public static function forget(): void
    {
        self::$icons = null;
    }

    /** @return array<int, string> */
    public static function categories(): array
    {
        $categories = array_values(array_unique(array_column(self::all(), 'category')));
        sort($categories);

        return $categories;
    }

    public static function has(string $identifier): bool
    {
        return self::find($identifier) !== null;
    }

    /**
     * The registered icon of that identifier, or null where none is.
     *
     * @return array{identifier: string, category: string, aliasOf: ?string, source: string}|null
     */
    public static function find(string $identifier): ?array
    {
        $identifier = strtolower(trim($identifier));
        foreach (self::all() as $icon) {
            if ($icon['identifier'] === $identifier) {
                return $icon;
            }
        }

        return null;
    }

    /**
     * Whether the query is shaped like an identifier rather than like a search
     * phrase. The distinction decides what a miss means: "passkey" finding
     * nothing is a search that came up empty, "status-reference-hard" finding
     * nothing is a validation result.
     */
    public static function looksLikeIdentifier(string $query): bool
    {
        $query = strtolower(trim($query));
        if (preg_match('/^([a-z][a-z0-9]*)-[a-z0-9]+(-[a-z0-9]+)*$/', $query, $matches) !== 1) {
            return false;
        }

        return in_array($matches[1], self::categories(), true);
    }

    /**
     * The curated concept map: which meaning is spelled by which shape.
     *
     * Identifiers name shapes, not intents — a warning is
     * actions-exclamation-triangle — so searching for the meaning finds
     * nothing without this. It is the one part of the old bundled catalog worth
     * keeping, because it is judgement rather than an inventory.
     *
     * @return array<string, array<int, string>>
     */
    public static function concepts(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::knowledgeFile('icon-concepts.json')), true);

        return is_array($decoded) ? ($decoded['concepts'] ?? []) : [];
    }

    /**
     * @return array<int, array{identifier: string, category: string, aliasOf: ?string, source: string}>
     */
    private static function fromT3Icons(?string $corePath): array
    {
        if ($corePath === null) {
            return [];
        }

        $file = $corePath . '/Resources/Public/Icons/T3Icons/icons.json';
        if (!is_file($file)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($file), true);
        if (!is_array($decoded)) {
            return [];
        }

        $icons = [];
        foreach (array_keys($decoded['icons'] ?? []) as $identifier) {
            $icons[] = [
                'identifier' => (string) $identifier,
                'category' => self::category((string) $identifier),
                'aliasOf' => null,
                'source' => self::SOURCE_T3ICONS,
            ];
        }
        foreach ($decoded['aliases'] ?? [] as $alias => $target) {
            $icons[] = [
                'identifier' => (string) $alias,
                'category' => self::category((string) $alias),
                'aliasOf' => (string) $target,
                'source' => self::SOURCE_T3ICONS,
            ];
        }

        return $icons;
    }

    /**
     * The identifiers a package registers, read out of its Configuration/Icons.php
     * by tokenising it: the file returns an array keyed by identifier, and the
     * keys are all that is needed.
     *
     * @return array<int, string>
     */
    private static function fromRegistrationFile(string $packagePath): array
    {
        return array_map(
            'strtolower',
            PhpArray::keys($packagePath . '/Configuration/Icons.php'),
        );
    }

    /** @return array<int, string> */
    private static function fromFlags(?string $corePath): array
    {
        if ($corePath === null) {
            return [];
        }

        $directory = $corePath . '/Resources/Public/Icons/Flags';
        if (!is_dir($directory)) {
            return [];
        }

        $flags = [];
        foreach (Finder::create()->files()->in($directory)->depth(0)->name('*.webp')->sortByName() as $file) {
            $flags[] = 'flags-' . strtolower($file->getBasename('.webp'));
        }

        return $flags;
    }

    private static function category(string $identifier): string
    {
        return strstr($identifier, '-', true) ?: 'default';
    }
}
