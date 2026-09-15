<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Contribution\Forge;
use TYPO3\DevCompanion\Contribution\Gerrit;
use TYPO3\DevCompanion\Http\Recent;
use TYPO3\DevCompanion\Tests\Support\Decision;

/**
 * What stays from a host outside this machine, and what a second call fetches
 * again.
 *
 * The two sources sit on opposite sides of one question: who can change the
 * answer. Nobody reaches the tracker through this server, so its answers stay.
 * The caller reaches the review server with its own git, and the answer it
 * changes there is the one it asks about next.
 */
final class RecentTest extends TestCase
{
    private int $now = 1_000_000;

    private int $reads = 0;

    #[After]
    public function forgetWhatWasHeld(): void
    {
        Recent::forget();
        Recent::useClock(null);
    }

    /**
     * The tracker stays the same way and by the same rule, because what this
     * spares is the round trip rather than the parse — `D-ANS-049`.
     */
    #[Decision('D-ANS-049')]
    #[Test]
    public function anAnsweredIssueIsReadFromTheTrackerOnce(): void
    {
        $forge = new Forge($this->transport('{"issue": {"id": 105403, "subject": "f:image and cache busting"}}'));

        $first = $forge->issue('105403');
        $second = $forge->issue('105403');

        self::assertSame('answered', $first['status']);
        self::assertSame($first, $second);
        // Two hosts, one round trip each: the tracker, and the review server a
        // single issue goes to since `D-ANS-125`. Both stay, so the second call
        // reads neither.
        self::assertSame(2, $this->reads, 'the second answer came from what was held');
    }

    /**
     * And a second read replaces what stays once it is old, which is what keeps
     * a triage off a state that has moved — `D-ANS-049`.
     */
    #[Decision('D-ANS-049')]
    #[Test]
    public function anIssueIsReadAgainOnceWhatWasHeldIsOld(): void
    {
        $forge = new Forge($this->transport('{"issue": {"id": 105403, "subject": "f:image and cache busting"}}'));

        $forge->issue('105403');
        $this->now += Forge::HELD_FOR;
        $forge->issue('105403');

        // Two reads per call, both of them stale by then.
        self::assertSame(4, $this->reads);
    }

    #[Decision('D-ANS-049')]
    #[Test]
    public function aBodyTheTrackerDidNotAnswerIsNotHeld(): void
    {
        // The challenge page a protection answers with is a state of this
        // minute. Held, one bad minute would be five — `D-ANS-049`.
        $forge = new Forge($this->transport('<html>are you a robot</html>'));

        $forge->issue('105403');
        $second = $forge->issue('105403');

        self::assertSame('unavailable', $second['status']);
        self::assertSame(4, $this->reads, 'both calls read, and each retried with the plain agent');
    }

    /**
     * A change the review server answered for stays, so a second call inside
     * the window costs nothing over the wire — `D-ANS-049`.
     */
    #[Decision('D-ANS-049')]
    #[Test]
    public function aChangeThatExistsIsReadFromTheReviewServerOnce(): void
    {
        $gerrit = new Gerrit($this->transport(")]}'\n" . '[{"_number": 90210, "subject": "[BUGFIX] Do the thing", "project": "Packages/TYPO3.CMS"}]'));

        $first = $gerrit->changesForIssue('105403');
        $second = $gerrit->changesForIssue('105403');

        self::assertSame('answered', $first['status']);
        self::assertSame($first, $second);
        self::assertSame(1, $this->reads);
    }

    #[Decision('D-ANS-049')]
    #[Test]
    public function noChangeForAnIssueIsAskedEveryTime(): void
    {
        // The caller falsifies this one itself with a push, and asks again
        // right afterwards. A held "there is none" is what sends somebody to
        // write a patch that is already up — `D-ANS-049`.
        $gerrit = new Gerrit($this->transport(")]}'\n[]"));

        $gerrit->changesForIssue('105403');
        $gerrit->changesForIssue('105403');

        self::assertSame(2, $this->reads);
    }

    /**
     * A transport that answers the same body every time and counts the calls.
     */
    private function transport(string $body): \Closure
    {
        Recent::useClock(fn(): int => $this->now);

        return function () use ($body): string {
            $this->reads++;

            return $body;
        };
    }
}
