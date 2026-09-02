<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

/**
 * The globally registered Fluid namespaces, read from the packages that declare
 * them.
 *
 * On TYPO3 v14 and later, `fluid:namespaces` is the better answer because it
 * reports what the container actually assembled. But it needs a console that
 * boots, and the declaration itself is a file: every package may ship a
 * Configuration/Fluid/Namespaces.php returning prefix => list of PHP namespaces,
 * and the core merges them across all active packages.
 *
 * The files are parsed, never included — the same rule as for the icon registry.
 * They are ordinary PHP that would run in this process. Earlier TYPO3 versions
 * register these namespaces in TYPO3_CONF_VARS and have no such file.
 */
final class FluidNamespaces
{
    /**
     * Prefix to the PHP namespaces behind it, merged across packages.
     *
     * @return array<string, array<int, string>>
     */
    public static function all(): array
    {
        $namespaces = [];
        foreach (Instance::packages() as $path) {
            foreach (self::declaredBy($path) as $prefix => $phpNamespaces) {
                foreach ($phpNamespaces as $phpNamespace) {
                    $namespaces[$prefix][] = $phpNamespace;
                }
            }
        }

        foreach ($namespaces as $prefix => $phpNamespaces) {
            $namespaces[$prefix] = array_values(array_unique($phpNamespaces));
        }
        ksort($namespaces);

        return $namespaces;
    }

    /**
     * One package's declaration, by tokenising it: the file returns an array of
     * prefix => list of namespace strings, and both levels are strings.
     *
     * @return array<string, array<int, string>>
     */
    public static function declaredBy(string $packagePath): array
    {
        $file = $packagePath . '/Configuration/Fluid/Namespaces.php';
        if (!is_file($file)) {
            return [];
        }

        $declared = [];
        $prefix = null;
        $depth = 0;
        $tokens = @token_get_all((string) file_get_contents($file));
        foreach ($tokens as $index => $token) {
            if ($token === '[') {
                ++$depth;
                continue;
            }
            if ($token === ']') {
                --$depth;
                continue;
            }
            if (!is_array($token) || $token[0] !== T_CONSTANT_ENCAPSED_STRING) {
                continue;
            }

            $value = self::literal($token[1]);
            if ($depth === 1 && self::isKey($tokens, $index)) {
                $prefix = $value;
                $declared[$prefix] ??= [];
                continue;
            }
            if ($depth === 2 && $prefix !== null) {
                $declared[$prefix][] = $value;
            }
        }

        return $declared;
    }

    /** Whether the string at $index is an array key: the next token is "=>". */
    private static function isKey(mixed $tokens, int $index): bool
    {
        for ($next = $index + 1; isset($tokens[$next]); ++$next) {
            $following = $tokens[$next];
            if (is_array($following) && in_array($following[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }

            return is_array($following) && $following[0] === T_DOUBLE_ARROW;
        }

        return false;
    }

    /** The value of a quoted PHP string, with its escaped backslashes resolved. */
    private static function literal(string $token): string
    {
        $value = trim($token, "'\"");

        return str_replace('\\\\', '\\', $value);
    }
}
