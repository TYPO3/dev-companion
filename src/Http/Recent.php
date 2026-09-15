<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Http;

/**
 * What a host outside this machine answered a moment ago.
 *
 * Not an installation's read, which goes with the call it came for because the
 * caller writes to it between two (`D-DIS-011`). Nobody writes to a tracker or
 * a review server through this server, and every call there goes over somebody
 * else's network. A miss never stays in the hold. Where the caller can change
 * the answer, as it can with a push of a patch, "there is none" must not come
 * out of a store.
 */
final class Recent
{
    /** @var array<string, array{at: int, answer: mixed}> */
    private static array $held = [];

    /** @var (\Closure(): int)|null */
    private static ?\Closure $clock = null;

    /**
     * What stood under this key and is still young enough, or null.
     *
     * The store holds whatever it got and cannot say what that was. So a caller
     * that reads its own answer back declares the shape it put in.
     *
     * @return mixed the held answer, null where there is none to give
     */
    public static function held(string $key, int $seconds): mixed
    {
        $entry = self::$held[$key] ?? null;
        if ($entry === null) {
            return null;
        }
        if (self::now() - $entry['at'] >= $seconds) {
            unset(self::$held[$key]);

            return null;
        }

        return $entry['answer'];
    }

    /**
     * Holds one answer under a key. What may stand in the hold is the caller's
     * judgement.
     */
    public static function hold(string $key, mixed $answer): void
    {
        self::$held[$key] = ['at' => self::now(), 'answer' => $answer];
    }

    /**
     * Drops everything in the hold; for tests, and for a record that moves
     * between sources.
     */
    public static function forget(): void
    {
        self::$held = [];
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
