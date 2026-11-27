<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Contribution\Forge;
use TYPO3\DevCompanion\Contribution\Forger;
use TYPO3\DevCompanion\Contribution\IssueProvider;
use TYPO3\DevCompanion\Contribution\IssueTracker;
use TYPO3\DevCompanion\Http\Cache;
use TYPO3\DevCompanion\Tests\Support\Decision;

/**
 * The providers in their order: Forger first, the tracker where Forger does not
 * answer, and one shape whichever answered — `D-ANS-197`.
 */
#[Decision('D-ANS-197')]
final class IssueTrackerTest extends TestCase
{
    #[After]
    public function emptyTheCache(): void
    {
        Cache::clear();
    }

    /** Issue 105403 as Forger answered it on 2026-11-27, trimmed to what the test reads. */
    private const DETAIL = [
        'id' => 105403,
        'subject' => 'f:image and cache busting issue',
        'tracker' => 'Bug',
        'status' => 'Under Review',
        'priority' => 'Should have',
        'category' => 'Fluid',
        'targetVersion' => null,
        'typo3Version' => '13',
        'phpVersion' => '',
        'author' => ['id' => 52, 'name' => 'Frank Nägler'],
        'assignedTo' => null,
        'createdOn' => '2024-10-23T08:42:11Z',
        'updatedOn' => '2026-11-14T18:22:49Z',
        'url' => 'https://forge.typo3.org/issues/105403',
        'description' => 'Since 13.4 the URI carries a timestamp, as in #99203.',
        'relations' => [['type' => 'relates', 'issue' => 105953]],
        'duplicates' => [],
        'duplicatedBy' => [101234],
        'changes' => [['number' => 95067, 'subject' => '[BUGFIX] Cache busting', 'state' => 'abandoned', 'relation' => 'resolves', 'url' => 'https://review.typo3.org/c/Packages/TYPO3.CMS/+/95067/']],
        'comments' => [
            ['author' => ['id' => 1, 'name' => 'Gerrit Code Review'], 'createdOn' => '2025-01-02T10:00:00Z', 'bot' => true, 'text' => 'Patch set 4 for change 95067 https://review.typo3.org/c/Packages/TYPO3.CMS/+/95067'],
            ['author' => ['id' => 128, 'name' => 'Georg Ringer'], 'createdOn' => '2025-02-03T10:00:00Z', 'bot' => false, 'text' => 'Why not the native img tag?'],
        ],
        'attachments' => [['filename' => 'shot.png', 'contentType' => 'image/png', 'size' => 4800, 'description' => '', 'author' => null, 'createdOn' => '2024-10-23T08:42:11Z', 'url' => 'https://forge.typo3.org/attachments/download/1/shot.png']],
    ];

    /** @return \Closure(string): ?string */
    private static function forger(): \Closure
    {
        return static function (string $url): ?string {
            if (str_ends_with($url, '/issues/105403')) {
                return (string) json_encode(self::DETAIL);
            }
            if (preg_match('~/issues/(\d+)$~', $url, $number) === 1) {
                return (string) json_encode(['id' => (int) $number[1], 'subject' => 'Issue ' . $number[1], 'tracker' => 'Bug', 'status' => 'Closed', 'comments' => []]);
            }

            return null;
        };
    }

    #[Test]
    public function anIssueReadFromForgerCarriesWhatTheTrackerAnswerCarries(): void
    {
        $tracker = new IssueTracker([new Forger(self::forger())]);

        $answer = $tracker->issue(105403);
        $issue = $answer['issue'];

        self::assertSame('answered', $answer['status']);
        self::assertSame(Forger::HOST, $tracker->source());
        self::assertSame('https://forge.typo3.org/issues/105403', $issue['url']);
        self::assertSame([105953, 101234], array_column($issue['relations'], 'issue'));
        self::assertSame(['relates', 'duplicated'], array_column($issue['relations'], 'relation'));
        self::assertSame('Issue 105953', $issue['relations'][0]['subject']);
        self::assertSame([99203], array_column($issue['mentioned'], 'issue'));
        // The handle the bot's note names and the change Forger holds are one
        // change, in the review server's own words.
        self::assertSame([95067], array_column($issue['reviews'], 'change'));
        self::assertSame('ABANDONED', $issue['reviews'][0]['status']);
        self::assertSame(4, $issue['reviews'][0]['patchSet']);
        self::assertSame(1, $issue['botNoteCount']);
        self::assertSame(['shot.png'], array_column($issue['attachments'], 'filename'));
    }

    #[Test]
    public function theTrackerAnswersWhereForgerDoesNot(): void
    {
        $tracker = new IssueTracker([
            new Forger(static fn(): ?string => null),
            new Forge(static fn(string $url): string => str_contains($url, 'review.typo3.org')
                ? ")]}'\n[]"
                : (string) json_encode(['issue' => ['id' => 105403, 'subject' => 'f:image and cache busting issue']])),
        ]);

        $answer = $tracker->issue(105403);

        self::assertSame('answered', $answer['status']);
        self::assertSame(Forge::HOST, $tracker->source());
        self::assertSame('f:image and cache busting issue', $answer['issue']['subject']);
    }

    /** An index may be minutes behind, so a number it lacks asks the tracker too. */
    #[Test]
    public function aNumberForgerDoesNotHoldIsAskedOfTheTracker(): void
    {
        $forger = $this->createStub(IssueProvider::class);
        $forger->method('issue')->willReturn(['status' => 'empty', 'url' => Forger::HOST, 'row' => null, 'cause' => null]);
        $forger->method('host')->willReturn(Forger::HOST);
        $tracker = new IssueTracker([
            $forger,
            new Forge(static fn(string $url): string => str_contains($url, 'review.typo3.org')
                ? ")]}'\n[]"
                : (string) json_encode(['issue' => ['id' => 111700, 'subject' => 'Filed a minute ago']])),
        ]);

        self::assertSame('Filed a minute ago', $tracker->issue(111700)['issue']['subject']);
        self::assertSame(Forge::HOST, $tracker->source());
    }

    #[Test]
    public function noProviderAnsweringIsUnavailableAndNotEmpty(): void
    {
        $tracker = new IssueTracker([new Forger(static fn(): ?string => null), new Forge(static fn(): ?string => null)]);

        $answer = $tracker->search('cache busting');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('source-not-answering', $answer['cause']);
    }

    /**
     * Forger takes one area per read. A word naming two is two reads, and an
     * issue sits in one area, so the totals add up and the rows merge in order.
     */
    #[Test]
    public function aWordNamingTwoAreasIsOneReadPerAreaOnForger(): void
    {
        $asked = [];
        $tracker = new IssueTracker([new Forger(static function (string $url) use (&$asked): string {
            $asked[] = $url;
            if (str_contains($url, 'breakdown=1')) {
                return (string) json_encode(['total' => 3, 'items' => [], 'breakdown' => ['category' => [
                    ['name' => 'Backend API', 'count' => 2],
                    ['name' => 'Backend User Interface', 'count' => 1],
                    ['name' => 'none', 'count' => 9],
                ]]]);
            }
            if (preg_match('~/issues/(\d+)$~', $url, $number) === 1) {
                return (string) json_encode(['id' => (int) $number[1], 'subject' => 'Issue ' . $number[1], 'createdOn' => '2026-0' . ($number[1] - 100) . '-01T00:00:00Z', 'comments' => []]);
            }
            $api = str_contains($url, 'category=Backend+API');

            return (string) json_encode(['total' => $api ? 2 : 1, 'items' => $api
                ? [['id' => 101, 'createdOn' => '2026-01-01T00:00:00Z'], ['id' => 103, 'createdOn' => '2026-03-01T00:00:00Z']]
                : [['id' => 102, 'createdOn' => '2026-02-01T00:00:00Z']]]);
        })]);

        $answer = $tracker->backlog('oldest', category: 'backend');

        self::assertSame(['Backend API', 'Backend User Interface'], $answer['categoriesUsed']);
        self::assertSame(3, $answer['total']);
        self::assertSame([101, 102, 103], array_column($answer['results'], 'issue'));
        self::assertCount(2, array_filter($asked, static fn(string $url): bool => str_contains($url, 'category=')));
    }
}
