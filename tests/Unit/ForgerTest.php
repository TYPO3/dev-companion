<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Contribution\ChangeProvider;
use TYPO3\DevCompanion\Contribution\Forger;
use TYPO3\DevCompanion\Http\Cache;
use TYPO3\DevCompanion\Tests\Support\Decision;

/**
 * Forger's changes in the shape the review server answers in, and the reads it
 * leaves to the review server — `D-ANS-198`.
 */
#[Decision('D-ANS-198')]
final class ForgerTest extends TestCase
{
    /** Change 96852 as Forger listed it on 2026-11-27, trimmed to what the test reads. */
    private const ITEM = [
        'number' => 96852,
        'changeId' => 'I74033609fc5529a5a5a5cd12b94ba3bd94e42fb0',
        'subject' => '[TASK] Add check whether a user may read a field',
        'state' => 'ready',
        'branch' => 'main',
        'mergeable' => true,
        'insertions' => 321,
        'deletions' => 4,
        'patchSets' => 6,
        'votes' => [
            'codeReview' => ['vote' => 0, 'state' => 'NEED', 'votes' => []],
            'verified' => ['vote' => 1, 'state' => 'NEED', 'votes' => [['account' => ['id' => 52386, 'name' => 'core-ci', 'username' => 'core-ci'], 'value' => 1, 'createdOn' => '2026-11-27T15:05:05Z', 'bot' => true]]],
        ],
        'commentCount' => 2,
        'unresolvedCommentCount' => 1,
        'owner' => ['id' => 51822, 'name' => 'Oli Bartsch', 'username' => 'obartsch'],
        'createdOn' => '2026-10-19T07:27:31Z',
        'updatedOn' => '2026-11-27T15:05:05Z',
        'url' => 'https://review.typo3.org/c/Packages/TYPO3.CMS/+/96852/',
    ];

    #[After]
    public function putTheHostBack(): void
    {
        Forger::useReader(null);
        Cache::clear();
    }

    #[Test]
    public function aListedChangeTakesTheShapeTheReviewServerAnswersIn(): void
    {
        $forger = new Forger(static fn(): string => (string) json_encode(['total' => 1, 'items' => [self::ITEM]]));

        $change = $forger->changesMatching('read a field', '')['changes'][0];

        self::assertSame('NEW', $change['status']);
        self::assertSame(6, $change['patchSet']);
        self::assertSame('refs/changes/52/96852/6', $change['fetch']['ref']);
        self::assertSame('https://review.typo3.org/c/Packages/TYPO3.CMS/+/96852', $change['url']);
        self::assertSame('2026-10-19 07:27:31.000000000', $change['created']);
        self::assertSame(['Code-Review', 'Verified'], array_column($change['labels'], 'label'));
        self::assertSame('core-ci', $change['labels'][1]['votes'][0]['voter']);
        // What Forger does not carry stays empty rather than guessed.
        self::assertSame('', $change['commit']);
        self::assertSame('', $change['owner']['email']);
        self::assertSame('Oli Bartsch', $change['owner']['name']);
    }

    /** The backlog leaves out what its author marked as work in progress. */
    #[Test]
    public function theBacklogDropsAChangeInProgress(): void
    {
        $wip = ['number' => 97596, 'state' => 'wip'] + self::ITEM;
        $forger = new Forger(static fn(): string => (string) json_encode(['total' => 2, 'items' => [$wip, self::ITEM]]));

        $answer = $forger->changesWaiting(self::filter([]), 10);

        self::assertSame([96852], array_column($answer['changes'], 'number'));
        self::assertSame(1, $answer['read']);
        self::assertTrue($answer['complete']);
    }

    /** Forger names no reviewer and no voter, so those filters are the review server's. */
    #[Test]
    public function aBacklogFilteredOnAPersonIsNotServed(): void
    {
        $asked = [];
        $forger = new Forger(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return '{}';
        });

        $answer = $forger->changesWaiting(self::filter(['reviewedBy' => 'Benjamin Kott']), 10);

        self::assertSame('unavailable', $answer['status']);
        self::assertSame(ChangeProvider::NOT_SERVED, $answer['cause']);
        self::assertSame([], $asked);
    }

    /** The read of the issue lists its changes, so a change of the same number is never taken for one. */
    #[Test]
    public function theChangesOfAnIssueComeFromTheIssue(): void
    {
        $forger = new Forger(static fn(string $url): string => str_contains($url, '/issues/')
            ? (string) json_encode(['id' => 111568, 'changes' => [['number' => 96852, 'state' => 'ready']]])
            : (string) json_encode(self::ITEM + ['commitMessage' => 'Releases: main, 13.4', 'releases' => ['main', '13.4']]));

        $answer = $forger->changesForIssue(111568);

        self::assertSame('answered', $answer['status']);
        self::assertSame([96852], array_column($answer['changes'], 'number'));
        self::assertSame(['main', '13.4'], $answer['changes'][0]['releases']);
    }

    /**
     * @param array<string, string> $set
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
            'reviewedBy' => $set['reviewedBy'] ?? '',
            'involving' => '',
            'reviewableBy' => '',
        ];
    }
}
