<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Result;

/**
 * The answer where a source outside this process did not answer.
 *
 * The other shape of "not answered", and `Unsupported` is the first,
 * `D-ANS-007`. That one replaces the result, because a question about an
 * installation there is none of has no place at all. This one stands beside a
 * status. A tracker or a registry is reachable from anywhere or from nowhere,
 * and the same call may answer the next time.
 *
 * What varies per source is which causes it can have and how each one reads to
 * a caller, so the caller passes them. `Schema::unavailable()` is the same map
 * for the schema. It serves a reader who decides whether to call rather than
 * one with a failed call in hand.
 */
final class Unreachable
{
    /** The host did not answer this time, and the same call may answer the next. */
    public const NOT_ANSWERING = 'source-not-answering';

    /** Something answered and it was not the API — a proxy, a portal, a bot check. */
    public const NOT_PARSEABLE = 'source-not-parseable';

    /**
     * @param array<string, string> $reasons what to say for each cause this source can have
     * @return array{cause: string, reason: string}|null null where the source did answer
     */
    public static function of(?string $cause, array $reasons): ?array
    {
        if ($cause === null) {
            return null;
        }

        return ['cause' => $cause, 'reason' => $reasons[$cause] ?? ''];
    }
}
