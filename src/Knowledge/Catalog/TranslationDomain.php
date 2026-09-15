<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge\Catalog;

/**
 * Derives the translation domain of an XLF label file from its EXT: path.
 *
 * Since the translation-domain API landed, the canonical way to reference a
 * label is the domain form "backend.alt_doc:buttons.confirm.save_and_close",
 * not the file path "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf:...".
 * The domain works in TCA labels and descriptions, LanguageService::sL(),
 * f:translate (as separate domain and key attributes), and registration configs.
 *
 * This is a port of the core's own path-to-domain rules, kept here so the
 * catalog needs no core checkout. The class holding them below
 * TYPO3\CMS\Core\Localization\ has been both TranslationDomainMapper and
 * TranslationDomainResolver, so verify against whichever the branch has.
 *
 * - domain format: package[.subdir...].resource
 * - "Resources/Private/Language/" comes off the path
 * - "Configuration/Sets/{Name}/labels.xlf" becomes "sets.{name}"
 * - directories turn from UpperCamelCase into snake_case
 * - "locallang.xlf" becomes "messages", "locallang_{suffix}.xlf" becomes "{suffix}"
 * - a locale prefix such as "de.locallang.xlf" does not count
 */
final class TranslationDomain
{
    /**
     * Returns the domain for an "EXT:key/..." reference, or null when the
     * reference is not an extension path.
     */
    public static function fromReference(string $reference): ?string
    {
        if (!str_starts_with($reference, 'EXT:')) {
            return null;
        }

        $withoutPrefix = substr($reference, 4);
        $slash = strpos($withoutPrefix, '/');
        if ($slash === false) {
            return null;
        }

        $extensionKey = substr($withoutPrefix, 0, $slash);
        $path = substr($withoutPrefix, $slash + 1);

        return $extensionKey . '.' . self::resource($path);
    }

    /**
     * Returns the domain for an XLF file written either as an "EXT:key/..."
     * reference or as a path inside a core checkout
     * ("typo3/sysext/backend/Resources/Private/Language/locallang.xlf"), or
     * null when the path names no extension.
     *
     * The derivation is a computation, so it also answers for a file the
     * catalog does not contain. That includes one a patch is about to add,
     * which is exactly when no lookup anywhere finds the domain.
     */
    public static function fromPath(string $path): ?string
    {
        $path = ltrim(trim($path), './');
        if (str_starts_with($path, 'EXT:')) {
            return self::fromReference($path);
        }

        if (preg_match('#(?:^|/)typo3/sysext/([^/]+)/(.+)$#', $path, $matches) === 1) {
            return self::fromReference('EXT:' . $matches[1] . '/' . $matches[2]);
        }

        return null;
    }

    private static function resource(string $path): string
    {
        $fileName = self::stripLocale(basename($path));
        $path = dirname($path) === '.' ? $fileName : dirname($path) . '/' . $fileName;

        if (preg_match('#Configuration/Sets/([^/]+)/labels\.xlf$#', $path, $matches) === 1) {
            return 'sets.' . self::snakeCase($matches[1]);
        }

        if (str_starts_with($path, 'Resources/Private/Language/')) {
            $path = substr($path, strlen('Resources/Private/Language/'));
        }

        $parts = explode('/', $path);
        $fileName = array_pop($parts);
        $resourceParts = array_map(self::snakeCase(...), $parts);

        $withoutExtension = self::withoutExtension($fileName);
        if ($withoutExtension === 'locallang') {
            $resourceParts[] = 'messages';
        } elseif (str_starts_with($withoutExtension, 'locallang_')) {
            $resourceParts[] = self::snakeCase(substr($withoutExtension, strlen('locallang_')));
        } else {
            $resourceParts[] = self::snakeCase($withoutExtension);
        }

        return implode('.', $resourceParts);
    }

    /** Drops a locale prefix such as "de." or "de_AT." from a file name. */
    private static function stripLocale(string $fileName): string
    {
        if (substr_count($fileName, '.') > 1 && preg_match('/^[a-z]{2}([_-][A-z]{2,3})?\./', $fileName) === 1) {
            return substr($fileName, (int) strpos($fileName, '.') + 1);
        }

        return $fileName;
    }

    private static function withoutExtension(string $fileReference): string
    {
        return preg_replace('/\.[a-z0-9]+$/i', '', $fileReference) ?? $fileReference;
    }

    private static function snakeCase(string $input): string
    {
        $result = preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $input) ?? $input;

        return str_replace('-', '_', strtolower($result));
    }
}
