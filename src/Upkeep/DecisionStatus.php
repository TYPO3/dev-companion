<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * What has come back about a decision since its commit.
 *
 * Not a workflow: the commit that implements a decision writes it, so every
 * entry here is already in the code. What the status answers is the one
 * question a reader has when they open a file that may be a year old. Has
 * anybody been back to the **Wrong if**, and what did they find.
 *
 * `corrected` used to answer that and three other things besides. On
 * `D-DIS-003` it stood for a measurement where the **Wrong if** had explicitly
 * not fired. So a reader could not tell from the status whether to rely on the
 * entry.
 */
enum DecisionStatus: string
{
    /** Recorded, and nobody has been back to it. Most entries, and legitimately so. */
    case Open = 'open';

    /** Somebody went back, and it held. */
    case Confirmed = 'confirmed';

    /** Somebody went back, and it does not hold. The entry stays; the record is the point. */
    case Revoked = 'revoked';

    /**
     * The dated line a status promises is further down the file, or '' where it
     * promises none. A status without its line is a claim about nothing.
     */
    public function line(): string
    {
        return match ($this) {
            self::Open => '',
            self::Confirmed => 'Confirmed on',
            self::Revoked => 'Revoked on',
        };
    }

    /** Whether a reader may still build on this entry. */
    public function stillHolds(): bool
    {
        return $this !== self::Revoked;
    }

    /**
     * The dated lines, in the order a file may carry them.
     *
     * An entry may carry several, because a decision has a history. A run on
     * the morning of 2026-08-02 confirmed `D-KNW-003`, and the evidence that
     * arrived the same day revoked it. The status names the most recent one, so
     * what a reader relies on is the last line rather than the only one.
     *
     * @return array<int, string>
     */
    public static function lines(): array
    {
        return array_values(array_filter(array_map(
            static fn(self $status): string => $status->line(),
            self::cases(),
        )));
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn(self $status): string => $status->value, self::cases());
    }
}
