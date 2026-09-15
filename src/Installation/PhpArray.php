<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

/**
 * Reads the keys of the array a TYPO3 declaration file returns, and runs
 * nothing.
 *
 * Configuration/Icons.php, Configuration/Backend/Modules.php and
 * Configuration/RequestMiddlewares.php all have the same shape. A file whose
 * whole content is `return [ 'identifier' => [...], ... ];`, and the
 * identifiers are what a caller asks for. An include of such a file would load
 * somebody else's code into this process, which is the one thing this server
 * never does. So a tokenizer reads the file and the keys at the wanted level
 * come out of the token stream.
 *
 * The keys of *that* literal and of no other. A file is free to hold more than
 * one array, and the extra ones sit at the same bracket depth as the returned
 * one. An `Icons.php` that builds its list in a `foreach` writes `['provider'
 * => …, 'source' => …]` once. A read of every literal at a depth reports
 * `provider` as an icon. Where the file returns something a reader cannot
 * resolve, a variable the loop filled, nothing comes back. An empty list reads
 * as "not determinable" and a wrong identifier reads as a registration.
 */
final class PhpArray
{
    /**
     * The array keys at $level of the array literal $file returns.
     *
     * Level 1 is the outermost array, the icon identifiers, the module
     * identifiers. Level 2 is the entries below each of them, which is where a
     * middleware identifier sits under its request scope.
     *
     * @return array<int, string>
     */
    public static function keys(string $file, int $level = 1): array
    {
        $tokens = self::tokens($file);
        $start = $tokens === null ? null : self::returnedLiteral($tokens);
        if ($tokens === null || $start === null) {
            return [];
        }

        $keys = [];
        $depth = 0;
        for ($index = $start; isset($tokens[$index]); ++$index) {
            $token = $tokens[$index];
            if ($token === '[') {
                ++$depth;
                continue;
            }
            if ($token === ']') {
                // The literal has closed; whatever follows it is another array.
                if (--$depth === 0) {
                    break;
                }
                continue;
            }
            if ($depth !== $level || !is_array($token) || $token[0] !== T_CONSTANT_ENCAPSED_STRING) {
                continue;
            }
            if (self::isKey($tokens, $index)) {
                $keys[] = trim($token[1], "'\"");
            }
        }

        return $keys;
    }

    /**
     * Whether the file is there and returns something other than a literal. Its
     * entries then do not stand in its text and exist only once it has run.
     *
     * The empty list `keys()` answers with says two different things. The file
     * is not there, or it is there and assembles its list while it runs. A
     * caller that omits an empty section reports both as silence. A reader
     * cannot tell "this extension registers no icons" from "its Icons.php is a
     * foreach". This separates them, and is false for a file that returns `[]`
     * outright. That one had its read, and what it says is that there is
     * nothing.
     */
    public static function assembledAtRuntime(string $file): bool
    {
        $tokens = self::tokens($file);

        return $tokens !== null && self::returnedLiteral($tokens) === null;
    }

    /**
     * The file's tokens, or null where there is no such file.
     *
     * @return array<int, array{0: int, 1: string, 2: int}|string>|null
     */
    private static function tokens(string $file): ?array
    {
        return is_file($file) ? @token_get_all((string) file_get_contents($file)) : null;
    }

    /**
     * The index of the `[` the file returns, or null when it returns anything
     * else.
     *
     * The return that counts is the one at the top level of the file. A return
     * inside a closure or a function belongs to that closure, and the literal
     * next to it is not what the file is worth.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     */
    private static function returnedLiteral(array $tokens): ?int
    {
        $braces = 0;
        foreach ($tokens as $index => $token) {
            // The two interpolation tokens open a brace a plain `}` closes, so
            // a count of them keeps the depth balanced across "{$a['b']}".
            if ($token === '{' || (is_array($token) && in_array($token[0], [T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES], true))) {
                ++$braces;
                continue;
            }
            if ($token === '}') {
                --$braces;
                continue;
            }
            if ($braces !== 0 || !is_array($token) || $token[0] !== T_RETURN) {
                continue;
            }

            $next = self::meaningful($tokens, $index);

            return ($next !== null && $tokens[$next] === '[') ? $next : null;
        }

        return null;
    }

    /**
     * Whether the string at $index is a key: the next meaningful token is "=>".
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     */
    private static function isKey(array $tokens, int $index): bool
    {
        $next = self::meaningful($tokens, $index);
        $following = $next === null ? null : $tokens[$next];

        return is_array($following) && $following[0] === T_DOUBLE_ARROW;
    }

    /**
     * The index of the first token after $index that is neither whitespace nor
     * a comment.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     */
    private static function meaningful(array $tokens, int $index): ?int
    {
        for ($next = $index + 1; isset($tokens[$next]); ++$next) {
            $token = $tokens[$next];
            if (is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }

            return $next;
        }

        return null;
    }
}
