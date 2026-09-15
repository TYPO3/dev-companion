<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Support;

use TYPO3\DevCompanion\Paths;

/**
 * What `.editorconfig` states for a file, for the tests that hold something to
 * it.
 *
 * That file is what every editor that opens this checkout obeys, so it is where
 * the indentation of a file type stands. Two things here write files of their
 * own, php-cs-fixer and `bin/cli knowledge:format`. Each would undo whoever
 * typed the last line by hand if it disagreed with it.
 */
final class Editorconfig
{
    /**
     * The indent a file of this name takes, or null where nothing states one. A
     * later section wins, which is editorconfig's own rule.
     */
    public static function indentFor(string $filename): ?int
    {
        $indent = null;
        foreach (preg_split('/^(?=\[)/m', (string) file_get_contents(Paths::root() . '/.editorconfig')) ?: [] as $section) {
            if (preg_match('/^\[([^]]+)]/', trim($section), $header) !== 1 || !self::matches($header[1], $filename)) {
                continue;
            }
            if (preg_match('/^indent_size\s*=\s*(\d+)/m', $section, $stated) === 1) {
                $indent = (int) $stated[1];
            }
        }

        return $indent;
    }

    /**
     * The section headers this checkout writes: a glob, with one set of braces
     * where several extensions share a rule.
     */
    private static function matches(string $pattern, string $filename): bool
    {
        $alternatives = [$pattern];
        if (preg_match('/^(.*)\{([^}]+)}(.*)$/', $pattern, $braces) === 1) {
            $alternatives = array_map(
                static fn(string $one): string => $braces[1] . $one . $braces[3],
                explode(',', $braces[2]),
            );
        }

        foreach ($alternatives as $alternative) {
            if (fnmatch($alternative, $filename)) {
                return true;
            }
        }

        return false;
    }
}
