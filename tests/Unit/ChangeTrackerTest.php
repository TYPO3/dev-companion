<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Contribution\ChangeProvider;
use TYPO3\DevCompanion\Contribution\ChangeTracker;
use TYPO3\DevCompanion\Contribution\Forger;
use TYPO3\DevCompanion\Contribution\Gerrit;
use TYPO3\DevCompanion\Http\Cache;
use TYPO3\DevCompanion\Tests\Support\Decision;

/**
 * The change providers in their order: Forger first, the review server where
 * Forger does not answer, and the review server alone for a change read by
 * name — `D-ANS-198`.
 */
#[Decision('D-ANS-198')]
final class ChangeTrackerTest extends TestCase
{
    /** One change the review server answers, with the XSSI prefix it sends. */
    private const REVIEW = ")]}'\n" . '[{"_number": 95437, "subject": "[TASK] Enable strict mode", "status": "MERGED", "project": "Packages/TYPO3.CMS", "current_revision": "0d3cd2f38cbd638c516430d41e581cd65464b71c"}]';

    #[After]
    public function emptyTheCache(): void
    {
        Cache::clear();
    }

    #[Test]
    public function aSearchForgerAnswersNeverReachesTheReviewServer(): void
    {
        $asked = [];
        $tracker = new ChangeTracker([
            $this->provider(Forger::HOST, 'answered'),
            new Gerrit(function (string $url) use (&$asked): string {
                $asked[] = $url;

                return self::REVIEW;
            }),
        ]);

        $answer = $tracker->changesMatching('strict mode', '');

        self::assertSame('answered', $answer['status']);
        self::assertSame(Forger::HOST, $tracker->source());
        self::assertSame([], $asked);
    }

    #[Test]
    public function aFilterForgerDoesNotServeIsAskedOfTheReviewServer(): void
    {
        $tracker = new ChangeTracker([
            $this->provider(Forger::HOST, 'unavailable', ChangeProvider::NOT_SERVED),
            new Gerrit(static fn(): string => self::REVIEW),
        ]);

        $answer = $tracker->changesWaiting(self::filter(['reviewedBy' => 'Benjamin Kott']), 5);

        self::assertSame('answered', $answer['status']);
        self::assertSame(Gerrit::HOST, $tracker->source());
    }

    /** Forger can be minutes behind a push, and the question right after a push is this one. */
    #[Test]
    public function anIssueForgerHasNoChangeForIsAskedOfTheReviewServer(): void
    {
        $tracker = new ChangeTracker([
            $this->provider(Forger::HOST, 'empty'),
            new Gerrit(static fn(): string => ")]}'\n"
                . '[{"_number": 97998, "subject": "[TASK] Enable strict mode", "project": "Packages/TYPO3.CMS", "revisions": {"a": {"commit": {"message": "[TASK] Enable strict mode\n\nResolves: #111568"}}}, "current_revision": "a"}]'),
        ]);

        $answer = $tracker->changesForIssue(111568);

        self::assertSame('answered', $answer['status']);
        self::assertSame(97998, $answer['changes'][0]['number']);
        self::assertSame(Gerrit::HOST, $tracker->source());
    }

    /** An empty answer outranks a host that did not answer at all. */
    #[Test]
    public function anEmptyAnswerOutranksAReviewServerThatDidNotAnswer(): void
    {
        $tracker = new ChangeTracker([
            $this->provider(Forger::HOST, 'empty'),
            new Gerrit(static fn(): ?string => null),
        ]);

        $answer = $tracker->changesForIssue(111568);

        self::assertSame('empty', $answer['status']);
        self::assertSame(Forger::HOST, $tracker->source());
    }

    /** Only the review server carries the current commit and the owner's address. */
    #[Test]
    public function aChangeReadByNameComesFromTheReviewServer(): void
    {
        $forger = $this->createMock(ChangeProvider::class);
        $forger->expects(self::never())->method('changesMatching');
        $forger->method('host')->willReturn(Forger::HOST);
        $tracker = new ChangeTracker([$forger, new Gerrit(static fn(): string => self::REVIEW)]);

        $answer = $tracker->change('95437');

        self::assertSame('0d3cd2f38cbd638c516430d41e581cd65464b71c', $answer['changes'][0]['commit']);
        self::assertSame(Gerrit::HOST, $tracker->source());
    }

    private function provider(string $host, string $status, ?string $cause = null): ChangeProvider
    {
        $answer = [
            'status' => $status,
            'query' => $host,
            'changes' => $status === 'answered' ? [['number' => 95437]] : [],
            'dropped' => 0,
            'more' => false,
            'cause' => $cause,
        ];
        $provider = $this->createStub(ChangeProvider::class);
        $provider->method('host')->willReturn($host);
        $provider->method('changesForIssue')->willReturn($answer);
        $provider->method('changesMatching')->willReturn($answer);
        $provider->method('changesWaiting')->willReturn([...$answer, 'read' => 0, 'complete' => false]);

        return $provider;
    }

    /**
     * @param array<string, mixed> $set
     * @return array{order: string, maxSize: int, minCodeReview: int, negativeVotes: bool, mergeable: bool, branch: string, updatedBefore: string, owner: string, reviewedBy: string, involving: string, reviewableBy: string}
     */
    private static function filter(array $set): array
    {
        return [
            'order' => 'oldest',
            'maxSize' => 0,
            'minCodeReview' => 0,
            'negativeVotes' => true,
            'mergeable' => false,
            'branch' => '',
            'updatedBefore' => '',
            'owner' => '',
            'reviewedBy' => is_string($set['reviewedBy'] ?? null) ? $set['reviewedBy'] : '',
            'involving' => '',
            'reviewableBy' => '',
        ];
    }
}
