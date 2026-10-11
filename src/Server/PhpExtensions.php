<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Server;

use TYPO3\DevCompanion\Paths;

/**
 * The PHP extensions `composer.json` requires, and which of them the PHP that
 * runs this server lacks, `D-ANS-176`.
 *
 * Composer checks them where it installs. An install with the platform check
 * off passes that, and so does a client that starts another PHP than the one
 * Composer resolved against.
 */
final class PhpExtensions
{
    /** @var list<string>|null */
    private static ?array $missing = null;

    private static ?\Closure $loaded = null;

    /** Seam for a test: what counts as loaded, in place of `extension_loaded()`. */
    public static function useLoaded(?\Closure $loaded): void
    {
        self::$loaded = $loaded;
        self::$missing = null;
    }

    /** @return list<string> the names, without `ext-` */
    public static function missing(): array
    {
        return self::$missing ??= array_values(array_filter(
            self::required(),
            static fn(string $name): bool => !(self::$loaded ?? extension_loaded(...))($name),
        ));
    }

    /** The sentence every call answers with while one is missing, or null. */
    public static function refusal(): ?string
    {
        $missing = self::missing();
        if ($missing === []) {
            return null;
        }

        return sprintf(
            'The PHP that runs this server lacks %s, which composer.json requires. That PHP is %s (%s). '
                . 'Install the extension for it, or start the server with a PHP that has it, then restart the MCP server.',
            implode(', ', array_map(static fn(string $name): string => 'ext-' . $name, $missing)),
            PHP_BINARY,
            PHP_VERSION,
        );
    }

    /** @return list<string> */
    private static function required(): array
    {
        $manifest = json_decode((string) file_get_contents(Paths::root() . '/composer.json'), true);
        $required = is_array($manifest) && is_array($manifest['require'] ?? null) ? array_keys($manifest['require']) : [];

        return array_values(array_map(
            static fn(string $key): string => substr($key, 4),
            array_filter($required, static fn(int|string $key): bool => str_starts_with((string) $key, 'ext-')),
        ));
    }
}
