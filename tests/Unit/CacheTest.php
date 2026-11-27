<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Contribution\Forge;
use TYPO3\DevCompanion\Contribution\Gerrit;
use TYPO3\DevCompanion\Contribution\IssueTracker;
use TYPO3\DevCompanion\Http\Cache;
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
final class CacheTest extends TestCase
{
    private int $now = 1_000_000;

    private int $reads = 0;

    #[After]
    public function emptyTheCache(): void
    {
        Cache::clear();
        Cache::useClock(null);
    }

    /**
     * The tracker stays the same way and by the same rule, because what this
     * spares is the round trip rather than the parse — `D-ANS-049`.
     */
    #[Decision('D-ANS-049')]
    #[Test]
    public function anAnsweredIssueIsReadFromTheTrackerOnce(): void
    {
        $forge = new IssueTracker([new Forge($this->transport('{"issue": {"id": 105403, "subject": "f:image and cache busting"}}'))]);

        $first = $forge->issue(105403);
        $second = $forge->issue(105403);

        self::assertSame('answered', $first['status']);
        self::assertSame($first, $second);
        // Two hosts, one round trip each: the tracker, and the review server a
        // single issue goes to since `D-ANS-125`. Both stay, so the second call
        // reads neither.
        self::assertSame(2, $this->reads, 'the second answer came from the cache');
    }

    /**
     * And a second read replaces what stays once it is old, which is what keeps
     * a triage off a state that has moved — `D-ANS-049`.
     */
    #[Decision('D-ANS-049')]
    #[Test]
    public function anIssueIsReadAgainOnceTheCachedAnswerIsOld(): void
    {
        $forge = new IssueTracker([new Forge($this->transport('{"issue": {"id": 105403, "subject": "f:image and cache busting"}}'))]);

        $forge->issue(105403);
        $this->now += Forge::CACHE_DURATION;
        $forge->issue(105403);

        // Two reads per call, both of them stale by then.
        self::assertSame(4, $this->reads);
    }

    #[Decision('D-ANS-049')]
    #[Test]
    public function aBodyTheTrackerDidNotAnswerIsNotCached(): void
    {
        // The challenge page a protection answers with is a state of this
        // minute. Cached, one bad minute would be five — `D-ANS-049`.
        $forge = new IssueTracker([new Forge($this->transport('<html>are you a robot</html>'))]);

        $forge->issue(105403);
        $second = $forge->issue(105403);

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

        $first = $gerrit->changesForIssue(105403);
        $second = $gerrit->changesForIssue(105403);

        self::assertSame('answered', $first['status']);
        self::assertSame($first, $second);
        self::assertSame(1, $this->reads);
    }

    #[Decision('D-ANS-049')]
    #[Test]
    public function noChangeForAnIssueIsAskedEveryTime(): void
    {
        // The caller falsifies this one itself with a push, and asks again
        // right afterwards. A cached "there is none" is what sends somebody to
        // write a patch that is already up — `D-ANS-049`.
        $gerrit = new Gerrit($this->transport(")]}'\n[]"));

        $gerrit->changesForIssue(105403);
        $gerrit->changesForIssue(105403);

        self::assertSame(2, $this->reads);
    }

    /**
     * A cached answer says when it was read rather than when it was asked for,
     * so a caller can tell the two apart — `D-ANS-049`.
     */
    #[Decision('D-ANS-049')]
    #[Test]
    public function aCachedAnswerSaysWhenTheTrackerWasRead(): void
    {
        $transport = $this->transport('{"issue": {"id": 105403, "subject": "f:image and cache busting"}}');
        $read = Cache::timestamp($this->now);

        $first = new IssueTracker([new Forge($transport)]);
        $first->issue(105403);
        $this->now += 60;
        $second = new IssueTracker([new Forge($transport)]);
        $second->issue(105403);

        self::assertSame($read, $first->readAt());
        self::assertSame($read, $second->readAt(), 'the second answer came from the cache');
    }

    #[Decision('D-ANS-049')]
    #[Test]
    public function aCachedAnswerSaysWhenTheReviewServerWasRead(): void
    {
        $transport = $this->transport(")]}'\n" . '[{"_number": 90210, "subject": "[BUGFIX] Do the thing", "project": "Packages/TYPO3.CMS"}]');
        $read = Cache::timestamp($this->now);

        (new Gerrit($transport))->changesForIssue(105403);
        $this->now += 10;
        $cached = new Gerrit($transport);
        $cached->changesForIssue(105403);
        $this->now += Gerrit::CACHE_DURATION;
        $fresh = new Gerrit($transport);
        $fresh->changesForIssue(105403);

        self::assertSame($read, Cache::timestamp($cached->readAt()));
        self::assertSame(Cache::timestamp($this->now), Cache::timestamp($fresh->readAt()));
    }

    /**
     * A transport that answers the same body every time and counts the calls.
     */
    private function transport(string $body): \Closure
    {
        Cache::useClock(fn(): int => $this->now);

        return function () use ($body): string {
            $this->reads++;

            return $body;
        };
    }
}
