<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tool\CommitMessageGuide;

/**
 * What the caller reads, rather than what the class behind it returns: the
 * answer drops "no issues found" where something complained, and it may drop
 * nothing else — R-GUI-007.
 */
final class CommitMessageGuideTest extends TestCase
{
    #[Requirement('R-GUI-007')]
    #[Test]
    public function aCheckedMessageStillSaysWhatTheWrappingJoined(): void
    {
        $result = CommitMessageGuide::answer([
            'message' => "Fix the thing\n\nExecuted commands:\n./Build/Scripts/runTests.sh -s cgl -n\n\n"
                . "Resolves: #1\nReleases: main\n",
        ]);

        $codes = array_column($result->data['checks'], 'code');
        self::assertContains('missing-keyword', $codes, 'the subject carries no keyword');
        self::assertContains('body-lines-reflowed', $codes);
        self::assertNotContains('no-issues-found', $codes);
    }

    /**
     * The caveat survives the drop, because the answer it qualifies is the one
     * with something else in it — R-GUI-011.
     */
    #[Requirement('R-GUI-011')]
    #[Test]
    public function aCheckedMessageSaysTheClassificationWasAssumed(): void
    {
        $result = CommitMessageGuide::answer([
            'message' => "Fix the thing\n\nBody.\n\nResolves: #1\nReleases: main\n",
            // The changelog obligation this is about is the core's, and the
            // core is stated since `D-GUI-010`.
            'workflow' => 'core',
        ]);

        $codes = array_column($result->data['checks'], 'code');
        self::assertContains('missing-keyword', $codes);
        self::assertNotContains('no-issues-found', $codes);
        self::assertContains('breaking-not-assessed', $codes);
    }

    /**
     * The line under the draft and the draft itself say the same thing: the
     * project workflow demands no trailer, and writes the one the call passed —
     * `D-GUI-017`.
     */
    #[Test]
    public function theProjectAnswerSaysTheIssueItWroteIsTheOneThatWasPassed(): void
    {
        $result = CommitMessageGuide::answer([
            'keyword' => 'TASK',
            'summary' => 'Update the frontend build to current dependencies',
            'issue' => '348',
        ]);

        self::assertSame('project', $result->data['workflow']);
        self::assertStringContainsString("\nResolves: #348", $result->data['message']);
        self::assertStringContainsString('Resolves: and Related: lines carry the issues this call passed', $result->text);
    }

    /**
     * What the length check sends the caller to do, done: the shorter summary
     * takes the subject and the message it arrived beside keeps the rest, which
     * is the assembly one session did by hand — `D-GUI-021`.
     */
    #[Decision('D-GUI-021')]
    #[Test]
    public function aSummaryPassedBesideAMessageReplacesTheSubjectAlone(): void
    {
        $result = CommitMessageGuide::answer([
            'message' => "[BUGFIX] Make the git based CGL suites work in worktrees\n\n"
                . "The suites read .git as a directory, which is a file in a worktree.\n\n"
                . "Resolves: #110534\nReleases: main\nChange-Id: I0123456789abcdef\n"
                . "Signed-off-by: Somebody <somebody@example.com>\n",
            'summary' => 'Make CGL suites work in git worktrees',
            'workflow' => 'core',
        ]);

        $message = $result->data['message'];
        self::assertStringStartsWith('[BUGFIX] Make CGL suites work in git worktrees', $message);
        self::assertStringContainsString('which is a file in a worktree', $message);
        self::assertStringContainsString("\nChange-Id: I0123456789abcdef", $message);
        self::assertStringContainsString("\nSigned-off-by: Somebody <somebody@example.com>", $message);
        self::assertNotContains('summary-length-preferred', array_column($result->data['checks'], 'code'));
    }

    /**
     * The width is what the draft is wrapped to, and the boundary is what
     * refuses a commit. Only the second settles a checkout whose own rule is one
     * character stricter than the hook it cites — `D-GUI-020`.
     */
    #[Decision('D-GUI-020')]
    #[Test]
    public function theCoreAnswerNamesWhereTheHooksLengthBoundaryRuns(): void
    {
        $call = [
            'keyword' => 'BUGFIX',
            'summary' => 'Show hidden records in the import preview',
            'issue' => '106123',
            'releases' => ['main'],
        ];

        $core = array_column(
            CommitMessageGuide::answer($call + ['workflow' => 'core'])->data['checks'],
            'message',
            'code'
        );
        $project = array_column(CommitMessageGuide::answer($call)->data['checks'], 'message', 'code');

        self::assertArrayHasKey('line-length-boundary', $core);
        self::assertStringContainsString(
            'accepts a line of 72 characters and refuses one of 73',
            $core['line-length-boundary']
        );
        // Outside the core no hook runs, so there is no boundary to state —
        // `D-GUI-003`.
        self::assertArrayNotHasKey('line-length-boundary', $project);
    }

    /** The check that fired says it, so the answer states the boundary once. */
    #[Decision('D-GUI-020')]
    #[Test]
    public function theOverlongLineCheckCarriesTheBoundaryItself(): void
    {
        $result = CommitMessageGuide::answer([
            'keyword' => 'TASK',
            'summary' => 'Document it',
            'issue' => '106123',
            'releases' => ['main'],
            'body' => 'See https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/'
                . 'Appendix/CommitMessage.html for details.',
            'workflow' => 'core',
        ]);

        $checks = array_column($result->data['checks'], 'message', 'code');
        self::assertArrayHasKey('body-line-too-long', $checks);
        self::assertStringContainsString(
            'accepts a line of 72 characters and refuses one of 73',
            $checks['body-line-too-long']
        );
        self::assertArrayNotHasKey('line-length-boundary', $checks);
    }

    /**
     * The tool checks one message against a page it never named, and four
     * sessions wanting that page read none of it — `R-ANS-028`. The pointer is
     * the call, because a client that lists no resources cannot act on the
     * address.
     */
    #[Requirement('R-ANS-028')]
    #[Test]
    public function theCoreAnswerNamesThePageItsRulesAreWrittenIn(): void
    {
        $call = [
            'keyword' => 'BUGFIX',
            'summary' => 'Show hidden records in the import preview',
            'issue' => '106123',
            'releases' => ['main'],
        ];

        $core = CommitMessageGuide::answer($call + ['workflow' => 'core'])->text;

        self::assertStringContainsString(
            'typo3_rule_lookup with documentId "core/contribution/commit-messages"',
            $core
        );
        self::assertStringNotContainsString('typo3://guides', $core);
        // The page describes the core repository and says so in its own
        // whenToUse, so a project commit is not sent to it.
        self::assertStringNotContainsString(
            'core/contribution/commit-messages',
            CommitMessageGuide::answer($call)->text
        );
    }

    /**
     * The draft is the last act of a piece of work, and the session that reads
     * it under momentum asked for nothing else — so the answer names the call
     * that owns the rest of the work, for the workflow it was asked with.
     */
    #[Decision('D-ANS-117')]
    #[Test]
    public function theDraftNamesTheGuideThatOwnsTheWorkflowItWasAskedWith(): void
    {
        $call = [
            'keyword' => 'BUGFIX',
            'summary' => 'Show hidden records in the import preview',
            'issue' => '106123',
            'releases' => ['main'],
        ];

        $core = CommitMessageGuide::answer($call + ['workflow' => 'core'])->text;
        $project = CommitMessageGuide::answer($call)->text;

        self::assertStringContainsString('typo3_task_guide — with the paths this commit touches', $core);
        self::assertStringContainsString('one step of the core patch workflow', $core);
        // The other workflow is other work, so it is named as what it is.
        self::assertStringContainsString('typo3_task_guide — with the paths this commit touches', $project);
        self::assertStringContainsString('one step of work in your own repository', $project);
        self::assertStringNotContainsString('core patch workflow', $project);
        // Under the pointer rather than over it: the page is a reading of the
        // subject the caller is already in.
        self::assertLessThan(
            strpos($core, 'core/contribution/commit-messages'),
            strpos($core, 'typo3_task_guide'),
        );
    }

    /**
     * A client that renders structuredContent and drops the text block reads
     * every pointer this answer carries — `R-ANS-002`.
     */
    #[Requirement('R-ANS-002')]
    #[Test]
    public function theGuideTheDraftNamesIsInTheDataToo(): void
    {
        $data = CommitMessageGuide::answer([
            'keyword' => 'TASK',
            'summary' => 'Update the frontend build to current dependencies',
        ])->data;

        self::assertSame(['typo3_task_guide'], array_column($data['nextTools'], 'tool'));
        self::assertStringContainsString('your own repository', $data['nextTools'][0]['when']);
    }

    /** An answer the caller gave in the call wins over the one the subject withholds. */
    #[Test]
    public function anIsBreakingTheCallerPassedAnswersItEvenWhenItIsFalse(): void
    {
        $result = CommitMessageGuide::answer([
            'message' => "[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main\n",
            'isBreaking' => false,
        ]);

        self::assertNotContains('breaking-not-assessed', array_column($result->data['checks'], 'code'));
    }
}
