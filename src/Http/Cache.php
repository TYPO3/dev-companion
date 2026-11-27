<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Http;

/**
 * What a host outside this machine answered a moment ago, per process.
 *
 * Not an installation's read, which goes with the call it came for because the
 * caller writes to it between two (`D-DIS-011`). Nobody writes to a tracker or
 * a review server through this server, and every call there goes over somebody
 * else's network. A miss never goes into the cache. Where the caller can change
 * the answer, as it can with a push of a patch, "there is none" must not come
 * out of a store — `D-ANS-049`.
 */
final class Cache
{
    /** @var array<string, array{at: int, answer: mixed}> */
    private static array $entries = [];

    /** @var (\Closure(): int)|null */
    private static ?\Closure $clock = null;

    /**
     * The answer under this key where it is younger than the cache duration,
     * or null.
     *
     * The cache cannot say what it stores. So a caller that reads its own
     * answer back declares the shape it put in.
     */
    public static function get(string $key, int $cacheDuration): mixed
    {
        $entry = self::$entries[$key] ?? null;
        if ($entry === null) {
            return null;
        }
        if (self::now() - $entry['at'] >= $cacheDuration) {
            unset(self::$entries[$key]);

            return null;
        }

        return $entry['answer'];
    }

    /**
     * When the answer under this key was read, and now where there is none,
     * which is the moment of the fetch about to happen.
     */
    public static function storedAt(string $key): int
    {
        return self::$entries[$key]['at'] ?? self::now();
    }

    /** A moment as an answer states it: ISO 8601 in UTC, and now where null. */
    public static function timestamp(?int $at): string
    {
        return gmdate('Y-m-d\TH:i:s\Z', $at ?? self::now());
    }

    /** Stores one answer under a key. Which answers may go in is the caller's judgement. */
    public static function put(string $key, mixed $answer): void
    {
        self::$entries[$key] = ['at' => self::now(), 'answer' => $answer];
    }

    /** Empties the cache; for tests, and for a record that moves between sources. */
    public static function clear(): void
    {
        self::$entries = [];
    }

    /**
     * The clock, so a test can age an entry without a wait for it.
     *
     * @param (\Closure(): int)|null $clock
     */
    public static function useClock(?\Closure $clock): void
    {
        self::$clock = $clock;
    }

    private static function now(): int
    {
        return self::$clock === null ? time() : (self::$clock)();
    }
}
