<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * Whether a requirement stands in the code, and whether anything holds it
 * there.
 *
 * Two of the three stand in the front matter and the third derives, which is
 * why this is not simply the `status` field. A requirement that says it is
 * `held` and names no test has nothing that holds it. From a listing it looks
 * exactly like one that has. None of the three is an error, because whether an
 * entry is worth the work is a judgement, `D-FBK-001`.
 */
enum RequirementState: string
{
    /** Written down and not built. */
    case Open = 'open';

    /** Built, and nothing would catch it when it goes wrong. */
    case NotGuarded = 'not guarded';

    /** Built, and the tests it names hold it there. */
    case Held = 'held';

    /**
     * The states the front matter may carry. `not guarded` is never one of
     * them. It is what a claim of `held` turns out to be when the entry names
     * no test. An entry may not claim it of itself.
     *
     * @return array<int, self>
     */
    public static function written(): array
    {
        return [self::Open, self::Held];
    }

    /**
     * @return array<int, string>
     */
    public static function writtenValues(): array
    {
        return array_map(static fn(self $state): string => $state->value, self::written());
    }

    /** Whether something would fail if the sentence stopped to be true. */
    public function isGuarded(): bool
    {
        return $this === self::Held;
    }
}
