<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Knowledge\CommitMessage;
use TYPO3\DevCompanion\Knowledge\ReleaseLines;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;

final class CommitMessageTest extends TestCase
{
    #[Test]
    public function theDraftCarriesKeywordIssueAndReleases(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Show hidden records in impexp import preview',
            'issue' => '106123',
            'releases' => ['main', '13.4'],
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        self::assertStringStartsWith('[BUGFIX] Show hidden records', $result['message']);
        self::assertStringContainsString("\nResolves: #106123", $result['message']);
        self::assertStringContainsString("\nReleases: main, 13.4", $result['message']);
    }

    #[Test]
    public function proseIsWrappedAtSeventyTwoCharacters(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Fix it',
            'issue' => '1',
            'body' => 'The import preview filtered hidden records because the query applied '
                . 'the default restrictions, which the preview never asked for.',
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        foreach ($this->bodyLines($result['message']) as $line) {
            self::assertLessThanOrEqual(72, mb_strlen($line), 'unwrapped line: ' . $line);
        }
        self::assertSame([], $this->checksWithCode($result['checks'], 'body-line-too-long'));
    }

    #[Test]
    public function aLongUrlIsKeptIntactAndReported(): void
    {
        $url = 'https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html';

        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Document it',
            'issue' => '1',
            'body' => 'See ' . $url . ' for details.',
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        self::assertStringContainsString($url, $result['message']);
        self::assertNotSame([], $this->checksWithCode($result['checks'], 'body-line-too-long'));
    }

    /**
     * The body of `feedback/2026-08-02-144315`: four command lines at column 0,
     * returned as one running paragraph with nothing saying so.
     */
    #[Requirement('R-GUI-007')]
    #[Test]
    public function aRunOfLinesTheWrappingJoinedIsNamed(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Hint at public URIs passed to f:image src',
            'issue' => '105403',
            'releases' => ['main'],
            'body' => "Executed commands:\nCI=true ./Build/Scripts/runTests.sh -s cgl -n\n"
                . 'CI=true ./Build/Scripts/runTests.sh -s phpstan',
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        $reflowed = $this->checksWithCode($result['checks'], 'body-lines-reflowed');
        self::assertCount(1, $reflowed);
        self::assertStringContainsString('Lines 1 to 3', $reflowed[0]['message']);
        self::assertNotContains('no-issues-found', array_column($result['checks'], 'code'));
    }

    #[Requirement('R-GUI-007')]
    #[Test]
    public function aBodyTheWrappingLeftAloneReportsNoReflow(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Keep structure',
            'issue' => '1',
            'releases' => ['main'],
            'body' => "Executed commands:\n\n    ./Build/Scripts/runTests.sh -s cgl -n",
            'extraTrailers' => ['Signed-off-by: A <a@b.c>'],
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        self::assertSame(
            ['no-issues-found', 'line-length-boundary', 'breaking-not-assessed'],
            array_column($result['checks'], 'code')
        );
        self::assertStringContainsString("\n    ./Build/Scripts/runTests.sh -s cgl -n", $result['message']);
    }

    /**
     * Each block is its own run, so the caller reads which lines went where
     * rather than one report covering the whole body.
     */
    #[Decision('D-GUI-003')]
    #[Requirement('R-GUI-007')]
    #[Test]
    public function eachJoinedRunIsReportedOnItsOwn(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Do a thing',
            'issue' => '1',
            'releases' => ['main'],
            'body' => "First paragraph,\nbroken by hand.\n\nSecond paragraph,\nbroken as well.",
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        $reflowed = $this->checksWithCode($result['checks'], 'body-lines-reflowed');
        self::assertCount(2, $reflowed);
        self::assertStringContainsString('Lines 1 to 2', $reflowed[0]['message']);
        self::assertStringContainsString('Lines 4 to 5', $reflowed[1]['message']);
    }

    /**
     * `Build/git-hooks/commit-msg`, `checkForLineLength()`, is
     * `grep -q -E '^[^#].{72}'`: under the core workflow a line the guide left
     * over the width is a commit the hook refuses — D-GUI-003.
     */
    #[Requirement('R-GUI-007')]
    #[Test]
    public function aLineOverTheWidthIsAnErrorForTheCoreAndAWarningOutsideIt(): void
    {
        $body = "Executed commands:\n"
            . '    CI=true ./Build/Scripts/runTests.sh -s functional -- '
            . 'typo3/sysext/fluid/Tests/Functional/ViewHelpers/ImageViewHelperTest.php';

        $core = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Fix it',
            'issue' => '1',
            'releases' => ['main'],
            'body' => $body,
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);
        $project = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Fix it',
            'workflow' => CommitMessage::WORKFLOW_PROJECT,
            'body' => $body,
        ]);

        self::assertSame(['error'], array_column(
            $this->checksWithCode($core['checks'], 'body-line-too-long'),
            'level'
        ));
        self::assertSame(['warning'], array_column(
            $this->checksWithCode($project['checks'], 'body-line-too-long'),
            'level'
        ));
    }

    #[Test]
    public function fencedCodeAndIndentedBlocksAreNotReflowed(): void
    {
        $body = "Explanation.\n\n```php\n\$queryBuilder->getRestrictions()->removeAll();\n```\n\n    indented output stays indented";

        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Keep structure',
            'issue' => '1',
            'body' => $body,
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        self::assertStringContainsString("```php\n\$queryBuilder->getRestrictions()->removeAll();\n```", $result['message']);
        self::assertStringContainsString("\n    indented output stays indented", $result['message']);
    }

    #[Test]
    public function aListItemKeepsItsMarkerAndGetsAHangingIndent(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Wrap lists',
            'issue' => '1',
            'body' => '- the first item is long enough that it has to be wrapped across two lines to fit',
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        $lines = $this->bodyLines($result['message']);
        self::assertStringStartsWith('- the first item', $lines[0]);
        self::assertStringStartsWith('  ', $lines[1], 'a wrapped list item continues indented');
    }

    #[Test]
    public function aMissingIssueIsAnError(): void
    {
        $result = CommitMessage::create(['changeType' => 'TASK', 'summary' => 'Do a thing', 'workflow' => CommitMessage::WORKFLOW_CORE]);

        self::assertContains('missing-issue', array_column($result['checks'], 'code'));
        self::assertStringContainsString('Resolves: #ISSUE_NUMBER', $result['message']);
    }

    #[Test]
    public function summaryLengthIsCheckedAgainstBothLimits(): void
    {
        $long = CommitMessage::create(['changeType' => 'TASK', 'summary' => str_repeat('a', 80), 'issue' => '1', 'workflow' => CommitMessage::WORKFLOW_CORE]);
        self::assertContains('summary-too-long', array_column($long['checks'], 'code'));

        $preferred = CommitMessage::create(['changeType' => 'TASK', 'summary' => str_repeat('a', 60), 'issue' => '1', 'workflow' => CommitMessage::WORKFLOW_CORE]);
        self::assertContains('summary-length-preferred', array_column($preferred['checks'], 'code'));
    }

    /**
     * The body that carries its argument as bullets, and the one that lists
     * what the change touched.
     *
     * `D-GUI-026`. A list is how a body enumerates classes, paths or rule
     * names, and the core writes one that way in about a tenth of its bodies —
     * so the shape reported is the item long enough to be a sentence, and the
     * list of names beside a paragraph passes.
     */
    #[Decision('D-GUI-026')]
    #[Test]
    public function aBodyWritingItsArgumentAsBulletsIsReported(): void
    {
        $bulleted = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Rework the thing',
            'body' => "- moves the service into its own class\n"
                . "- drops the singleton marker\n- adds a functional test for it",
            'workflow' => CommitMessage::WORKFLOW_PROJECT,
        ]);

        self::assertContains('body-written-as-a-list', array_column($bulleted['checks'], 'code'));

        $enumerating = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Drop the marker from the classes that do not need it',
            'body' => "The tag makes every service shared, so the marker has no effect\n"
                . "where the service is reachable without it. It is removed from:\n\n"
                . "* Scheduler\n* SessionService",
            'workflow' => CommitMessage::WORKFLOW_PROJECT,
        ]);

        self::assertNotContains('body-written-as-a-list', array_column($enumerating['checks'], 'code'));
    }

    #[Test]
    public function deprecationRulesAreEnforced(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Deprecate something',
            'issue' => '1',
            'isDeprecation' => true,
            'isBreaking' => true,
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        $codes = array_column($result['checks'], 'code');
        self::assertContains('deprecation-breaking-prefix', $codes);
        self::assertContains('deprecation-keyword', $codes);
        self::assertContains('changelog-required', $codes);
    }

    #[Test]
    public function aCleanDraftReportsThatNothingIsWrong(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Do a thing',
            'issue' => '1',
            'releases' => ['main'],
            'extraTrailers' => ['Signed-off-by: A <a@b.c>'],
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        self::assertSame(
            ['no-issues-found', 'line-length-boundary', 'breaking-not-assessed'],
            array_column($result['checks'], 'code')
        );
    }

    #[Requirement('R-GUI-001')]
    #[Test]
    public function theDraftNeverCarriesAReleaseTheCallerDidNotName(): void
    {
        $result = CommitMessage::create(['changeType' => 'TASK', 'summary' => 'Do a thing', 'issue' => '1', 'workflow' => CommitMessage::WORKFLOW_CORE]);

        self::assertStringContainsString('Releases: RELEASE_TARGET', $result['message']);
        self::assertContains('missing-releases', array_column($result['checks'], 'code'));
    }

    #[Requirement('R-GUI-001')]
    #[Test]
    public function neitherPlaceholderCouldBeReadAsAnAnswer(): void
    {
        $message = CommitMessage::create(['changeType' => 'TASK', 'summary' => 'Do a thing', 'workflow' => CommitMessage::WORKFLOW_CORE])['message'];

        if (preg_match('/^Resolves: (.*)$/m', $message, $resolves) !== 1
            || preg_match('/^Releases: (.*)$/m', $message, $releases) !== 1
        ) {
            self::fail('the draft carries neither trailer, so there is no placeholder to read');
        }

        // What the real values look like: a Forge issue is digits, a release
        // target is main or a minor. A placeholder that fits either shape reads
        // as a decision somebody made, which is what D-GUI-001 rejected "main" for.
        self::assertDoesNotMatchRegularExpression('/^#\d+$/', $resolves[1]);
        self::assertDoesNotMatchRegularExpression('/^(main|\d+\.\d+)$/', $releases[1]);
        self::assertSame(CommitMessage::ISSUE_PLACEHOLDER, $resolves[1]);
        self::assertSame(CommitMessage::RELEASE_PLACEHOLDER, $releases[1]);
    }

    #[Requirement('R-GUI-001')]
    #[Test]
    public function aPlaceholderHandedBackIsStillAnUnansweredField(): void
    {
        $parsed = CommitMessage::parse("[TASK] Do a thing\n\nBody.\n\nResolves: #ISSUE_NUMBER\nReleases: RELEASE_TARGET\n", CommitMessage::WORKFLOW_CORE);
        $result = CommitMessage::create($parsed['input'] + ['workflow' => CommitMessage::WORKFLOW_CORE]);

        $codes = array_column(array_merge($parsed['checks'], $result['checks']), 'code');
        self::assertContains('missing-issue', $codes, 'the placeholder is not a Forge issue');
        self::assertContains('missing-releases', $codes, 'the placeholder is not a release target');
        self::assertNotContains('no-issues-found', $codes);
        self::assertStringContainsString('Resolves: #ISSUE_NUMBER', $result['message']);
        self::assertStringContainsString('Releases: RELEASE_TARGET', $result['message']);
    }

    #[Requirement('R-GUI-001')]
    #[Test]
    public function aTrailerTheDraftCarriesIsNotAlsoReportedAsMissing(): void
    {
        $parsed = CommitMessage::parse("Fix the thing\n\nBody.\n\nResolves: #1\n", CommitMessage::WORKFLOW_CORE);
        $result = CommitMessage::create($parsed['input'] + ['workflow' => CommitMessage::WORKFLOW_CORE]);

        $codes = array_column(array_merge($parsed['checks'], $result['checks']), 'code');
        self::assertContains('missing-releases', $codes, 'the draft has no release target either');
        self::assertStringNotContainsString('Releases: main', $result['message']);
    }

    /**
     * The case of `feedback/2026-08-03-144432`: a whole core message checked
     * with no `isBreaking`, whose subject carries no `[!!!]` — R-GUI-011. The
     * subject cannot say which of "not breaking" and "nobody looked" it means,
     * so `parse()` hands the field back unanswered.
     */
    #[Requirement('R-GUI-011')]
    #[Test]
    public function aClassificationNobodyGaveIsNamedInTheChecks(): void
    {
        $parsed = CommitMessage::parse(
            "[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main\nSigned-off-by: A <a@b.c>\n",
            CommitMessage::WORKFLOW_CORE,
        );
        $result = CommitMessage::create($parsed['input'] + ['workflow' => CommitMessage::WORKFLOW_CORE]);

        self::assertNull($parsed['input']['isBreaking']);

        $codes = array_column($result['checks'], 'code');
        self::assertContains('breaking-not-assessed', $codes);
        self::assertContains('no-issues-found', $codes, 'the caveat stands beside the clearance, not inside it');

        $check = $this->checksWithCode($result['checks'], 'breaking-not-assessed')[0];
        self::assertSame('info', $check['level']);
        self::assertStringContainsString('isDeprecation', $check['message'], 'the same field owes the same sentence');
    }

    /** @param array<string, mixed> $answered */
    #[Requirement('R-GUI-011')]
    #[Test]
    #[DataProvider('theWaysACallerAnswersTheClassification')]
    public function aClassificationTheCallerGaveIsNotAskedAboutAgain(array $answered): void
    {
        $result = CommitMessage::create(array_merge([
            'changeType' => 'TASK',
            'summary' => 'Do a thing',
            'issue' => '1',
            'releases' => ['main'],
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ], $answered));

        self::assertNotContains('breaking-not-assessed', array_column($result['checks'], 'code'));
    }

    #[Decision('D-KNW-123')]
    #[Test]
    public function theAssumedClassificationBindsWideningToTheSignature(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Do a thing',
            'issue' => '1',
            'releases' => ['main'],
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        $message = $this->checksWithCode($result['checks'], 'breaking-not-assessed')[0]['message'];

        self::assertStringContainsString('whose signature it narrows or widens', $message);
        self::assertStringContainsString('A widened visibility is not one of them', $message);
        // What each move owes is the corpus's to say, so the check enumerates
        // the members and hands the obligations on — R-GUI-011 asks for the
        // classification to be named and for no paragraph beside it.
        self::assertStringNotContainsString('extension scanner matcher', $message);
        self::assertStringContainsString('typo3_rule_lookup(query "breaking change")', $message);
        // And the branch that decides the type rather than the classification.
        // Two sessions were stopped by this one paragraph: one read a widened
        // visibility as unsubmittable, and the next widened a signature on an
        // `@internal` class, settled it from the docblock with `sed`, and filed
        // no entry at all where an Important is owed — `D-KNW-123`.
        self::assertStringContainsString('a member marked @internal owes an Important entry', $message);
        self::assertStringContainsString('an entry is still owed', $message);
    }

    /** @return array<string, array{0: array<string, mixed>}> */
    public static function theWaysACallerAnswersTheClassification(): array
    {
        return [
            'the subject carries the breaking marker' => [['isBreaking' => true]],
            'the caller passed isBreaking false' => [['isBreaking' => false]],
            'the caller named a deprecation, which is a classification too' => [['isDeprecation' => true]],
            'outside the core there is no changelog and no matcher to owe' => [
                ['workflow' => CommitMessage::WORKFLOW_PROJECT],
            ],
        ];
    }

    #[Test]
    public function everyCheckCarriesALevelACodeAndAMessage(): void
    {
        $result = CommitMessage::create(['changeType' => 'TASK', 'summary' => 'do a thing', 'workflow' => CommitMessage::WORKFLOW_CORE]);

        foreach ($result['checks'] as $check) {
            self::assertContains($check['level'], ['error', 'warning', 'info']);
            self::assertMatchesRegularExpression('/^[a-z][a-z-]+$/', $check['code']);
            self::assertNotSame('', $check['message']);
        }
    }

    /**
     * And nothing is invented for a project that named none: no trailer, and no
     * check asking for one — `D-GUI-017`.
     */
    #[Requirement('R-AUD-003')]
    #[Decision('D-GUI-017')]
    #[Test]
    public function outsideTheCoreNoTrailerIsAddedAndNoneIsDemanded(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Add a sponsor logo field',
            'workflow' => CommitMessage::WORKFLOW_PROJECT,
        ]);

        self::assertStringNotContainsString('Resolves:', $result['message']);
        self::assertStringNotContainsString('Releases:', $result['message']);
        self::assertSame(['no-issues-found'], array_column($result['checks'], 'code'));
    }

    #[Requirement('R-AUD-003')]
    #[Test]
    public function outsideTheCoreTheSubjectAndBodyRulesStillHold(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => str_repeat('a', 80),
            'workflow' => CommitMessage::WORKFLOW_PROJECT,
            'body' => 'The sponsor records need an additional logo field because the '
                . 'rendering has to fall back to the organisation logo otherwise.',
        ]);

        self::assertContains('summary-too-long', array_column($result['checks'], 'code'));
        foreach ($this->bodyLines($result['message']) as $line) {
            self::assertLessThanOrEqual(72, mb_strlen($line), 'unwrapped line: ' . $line);
        }
    }

    /**
     * An issue a project caller passed is carried as `Resolves:` the way the
     * core's own is, because the trailer belongs to the issue rather than to
     * the workflow — `D-GUI-017`.
     */
    #[Requirement('R-AUD-003')]
    #[Decision('D-GUI-017')]
    #[Test]
    public function outsideTheCoreATrailerTheCallerWroteIsStillKept(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Add a sponsor logo field',
            'issue' => '66',
            'workflow' => CommitMessage::WORKFLOW_PROJECT,
        ]);

        self::assertStringContainsString("\nResolves: #66", $result['message']);
    }

    #[Requirement('R-AUD-003')]
    #[Test]
    public function theSecurityKeywordIsTheRepositoryOwnOutsideTheCore(): void
    {
        $parsed = CommitMessage::parse(
            "[SECURITY] Update typo3/cms-* to 13.4.33\n",
            CommitMessage::WORKFLOW_PROJECT
        );

        self::assertSame('SECURITY', $parsed['input']['changeType']);
        self::assertNotContains('security-keyword', array_column($parsed['checks'], 'code'));
    }

    #[Decision('D-GUI-010')]
    #[Test]
    public function aSecurityCommitAssembledForTheCoreIsRefusedToo(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'SECURITY',
            'summary' => 'Fix the thing',
            'issue' => '1',
            'releases' => ['main'],
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        self::assertContains('security-keyword', array_column($result['checks'], 'code'));
    }

    #[Decision('D-GUI-010')]
    #[Test]
    public function aWorkflowNobodyKnowsIsTheProjectOne(): void
    {
        self::assertSame(CommitMessage::WORKFLOW_PROJECT, CommitMessage::workflow('githubb'));
        self::assertSame(CommitMessage::WORKFLOW_PROJECT, CommitMessage::workflow(null));
        self::assertSame(CommitMessage::WORKFLOW_CORE, CommitMessage::workflow('Core'));
    }

    #[Test]
    public function anExistingMessageIsSplitBackIntoItsParts(): void
    {
        $parsed = CommitMessage::parse(
            "[!!!][FEATURE] Add a thing\n\nWhy it was added.\n\n"
            . "Resolves: #1\nResolves: #2\nRelated: #3\nReleases: main, 13.4\nChange-Id: I1234\n"
        );

        self::assertSame('FEATURE', $parsed['input']['changeType']);
        self::assertTrue($parsed['input']['isBreaking']);
        self::assertSame('Add a thing', $parsed['input']['summary']);
        self::assertSame(['#1', '#2'], array_map(
            static fn(string $issue): string => CommitMessage::normalizeIssue($issue) ?? '',
            $parsed['input']['issues']
        ));
        self::assertSame(['main', '13.4'], $parsed['input']['releases']);
        self::assertSame('Why it was added.', $parsed['input']['body']);
        self::assertSame(['Change-Id: I1234'], $parsed['input']['extraTrailers']);
    }

    #[Test]
    public function anAmendKeepsTheChangeId(): void
    {
        $parsed = CommitMessage::parse("[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main\nChange-Id: I1234\n");
        $result = CommitMessage::create($parsed['input']);

        self::assertStringEndsWith("Releases: main\nChange-Id: I1234", $result['message']);
    }

    #[Test]
    public function aMissingKeywordIsReported(): void
    {
        $parsed = CommitMessage::parse("Fix the thing\n\nBody.\n\nResolves: #1\n");

        self::assertContains('missing-keyword', array_column($parsed['checks'], 'code'));
    }

    #[Test]
    public function theSecurityKeywordIsRejected(): void
    {
        $parsed = CommitMessage::parse("[SECURITY] Fix the thing\n\nResolves: #1\nReleases: main\n");

        self::assertContains('security-keyword', array_column($parsed['checks'], 'code'));
        self::assertSame('', $parsed['input']['changeType']);
    }

    /**
     * A subject the review server is full of — 36 of 200 open changes on
     * 2026-08-03 — and the one this tool used to answer with `[KEYWORD]
     * [BUGFIX] …`, having read `[WIP]` as the keyword and rewritten a correct
     * subject into one that does not exist.
     */
    #[Test]
    public function aDraftPrefixIsNotAKeywordAndIsKept(): void
    {
        $parsed = CommitMessage::parse("[WIP][BUGFIX] Parse User TSConfig\n\nBody.\n\nResolves: #1\nReleases: main\n");
        $result = CommitMessage::create($parsed['input']);

        self::assertSame('BUGFIX', $parsed['input']['changeType']);
        self::assertSame(['WIP'], $parsed['input']['draftPrefixes']);
        self::assertNotContains('unknown-keyword', array_column($parsed['checks'], 'code'));
        self::assertContains('not-merge-ready', array_column($parsed['checks'], 'code'));
        self::assertStringStartsWith('[WIP][BUGFIX] Parse User TSConfig', $result['message']);
    }

    #[Test]
    public function aProofOfConceptIsTheSameMarkerHoweverItIsSpelled(): void
    {
        $parsed = CommitMessage::parse("[PoC][FEATURE] Bind a form to TCA\n\nResolves: #1\nReleases: main\n");

        self::assertSame(['POC'], $parsed['input']['draftPrefixes']);
        self::assertSame('FEATURE', $parsed['input']['changeType']);
    }

    /** The order is the subject's, and the breaking marker is read either way round. */
    #[Test]
    #[DataProvider('theTwoOrdersADraftAndABreakingMarkerCanBeWrittenIn')]
    public function aDraftPrefixStandsBesideTheBreakingMarker(string $subject): void
    {
        $parsed = CommitMessage::parse($subject . " Change the resource API\n\nResolves: #1\nReleases: main\n");

        self::assertTrue($parsed['input']['isBreaking']);
        self::assertSame(['WIP'], $parsed['input']['draftPrefixes']);
        self::assertSame('FEATURE', $parsed['input']['changeType']);
    }

    /** @return array<string, array{0: string}> */
    public static function theTwoOrdersADraftAndABreakingMarkerCanBeWrittenIn(): array
    {
        return [
            'the draft prefix first' => ['[WIP][!!!][FEATURE]'],
            'the breaking marker first' => ['[!!!][WIP][FEATURE]'],
        ];
    }

    /** A marker on its own is a subject without a keyword, and says so. */
    #[Test]
    public function aSubjectThatIsOnlyADraftPrefixIsMissingItsKeyword(): void
    {
        $parsed = CommitMessage::parse("[WIP] Livesearch\n\nResolves: #1\nReleases: main\n");

        self::assertContains('missing-keyword', array_column($parsed['checks'], 'code'));
        self::assertContains('not-merge-ready', array_column($parsed['checks'], 'code'));
        self::assertSame('Livesearch', $parsed['input']['summary']);
    }

    #[Test]
    public function aMissingBlankLineAfterTheSummaryIsReported(): void
    {
        $parsed = CommitMessage::parse("[TASK] Do a thing\nBody right below.\n\nResolves: #1\nReleases: main\n");

        self::assertContains('body-blank-line', array_column($parsed['checks'], 'code'));
    }

    #[Test]
    public function aColonSentenceAtTheEndOfTheBodyIsNotATrailer(): void
    {
        $parsed = CommitMessage::parse("[TASK] Do a thing\n\nNote: this stays body text.\n\nResolves: #1\nReleases: main\n");

        self::assertSame('Note: this stays body text.', $parsed['input']['body']);
        self::assertSame([], $parsed['input']['extraTrailers']);
    }

    /**
     * The two an agent writes about itself, and the workflow that keeps them.
     *
     * A refused trailer comes off the draft rather than being reported beside
     * it, because the draft is committed as it stands. The sign-off stood among
     * them until the certificate became required — `D-KNW-125`.
     */
    #[Decision('D-KNW-125')]
    #[Test]
    public function aCoreDraftRefusesTheTrailersTheProjectDoesNotSet(): void
    {
        $message = "[BUGFIX] Keep the identifier out of the client\n\nBody.\n\nResolves: #1\nReleases: main\n"
            . "Signed-off-by: A <a@b.c>\nCo-Authored-By: B <b@b.c>\nClaude-Session: https://example.test/x\n";

        $core = CommitMessage::parse($message, CommitMessage::WORKFLOW_CORE);
        self::assertSame(
            ['refused-trailer', 'refused-trailer'],
            array_values(array_filter(
                array_column($core['checks'], 'code'),
                static fn(string $code): bool => $code === 'refused-trailer',
            )),
        );
        self::assertSame(['Signed-off-by: A <a@b.c>'], $core['input']['extraTrailers'], 'a refused trailer reaches the draft');

        $redrafted = CommitMessage::create($core['input'] + ['workflow' => CommitMessage::WORKFLOW_CORE]);
        self::assertStringContainsString('Signed-off-by: A <a@b.c>', $redrafted['message'], 'the certificate is struck off the draft');
        self::assertNotContains('missing-sign-off', array_column($redrafted['checks'], 'code'));

        $project = CommitMessage::parse($message, CommitMessage::WORKFLOW_PROJECT);
        self::assertNotContains('refused-trailer', array_column($project['checks'], 'code'));
        self::assertNotContains('missing-sign-off', array_column($project['checks'], 'code'), 'the certificate is asked of a core patch');
        self::assertCount(3, $project['input']['extraTrailers']);
    }

    /**
     * The certificate is an attestation about provenance, so the draft names the
     * obligation and leaves the identity to whoever commits — `D-KNW-125`. It is
     * the shape `Resolves:` already has: a placeholder in the draft, an error
     * beside it, and the placeholder read back as the unanswered field it was.
     */
    #[Decision('D-KNW-125')]
    #[Test]
    public function aCoreDraftAsksForTheSignOffItCannotWrite(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Do a thing',
            'issue' => '1',
            'releases' => ['main'],
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        self::assertStringEndsWith("\nSigned-off-by: " . CommitMessage::SIGN_OFF_PLACEHOLDER, $result['message']);
        $check = $this->checksWithCode($result['checks'], 'missing-sign-off')[0];
        self::assertSame('error', $check['level']);
        self::assertStringContainsString('git commit -s', $check['message'], 'nothing says how the line is written');
        self::assertStringContainsString('GPL v2', $check['message'], 'nothing says what signing it claims');

        // Read back, the placeholder is an unsigned message rather than a signed
        // one — otherwise the one moment somebody checks the message they are
        // about to commit is the moment it reports clean.
        $parsed = CommitMessage::parse($result['message'], CommitMessage::WORKFLOW_CORE);
        self::assertSame([], $parsed['input']['extraTrailers']);
        $rechecked = CommitMessage::create($parsed['input'] + ['workflow' => CommitMessage::WORKFLOW_CORE]);
        self::assertContains('missing-sign-off', array_column($rechecked['checks'], 'code'));

        $project = CommitMessage::create(['changeType' => 'TASK', 'summary' => 'Do a thing', 'workflow' => CommitMessage::WORKFLOW_PROJECT]);
        self::assertStringNotContainsString('Signed-off-by', $project['message'], 'the certificate belongs to the core workflow');
    }

    #[Test]
    public function anEmptyMessageIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        CommitMessage::parse("   \n");
    }

    /**
     * The caller passes `summary` and the rule measures the subject, so the
     * message says which of the two it counted and what the limit leaves. The
     * session that reported this shortened by the nine characters it was over,
     * twice, before the arithmetic became visible.
     */
    #[Test]
    public function theLengthCheckSaysWhatMadeTheSubjectLong(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Implode array placeholder values on every recursion level',
            'issue' => '76536',
            'releases' => ['main'],
            'isBreaking' => false,
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        $checks = $this->checksWithCode($result['checks'], 'summary-length-preferred');
        self::assertCount(1, $checks);
        self::assertStringContainsString('66 characters', $checks[0]['message']);
        self::assertStringContainsString('57-character summary', $checks[0]['message']);
        self::assertStringContainsString('9 for the keyword prefix', $checks[0]['message']);
        self::assertStringContainsString('leaves the summary 43', $checks[0]['message']);
    }

    /**
     * The budget on its own left a session writing four candidate subjects and
     * measuring them in a shell — `D-GUI-021`.
     */
    #[Decision('D-GUI-021')]
    #[Test]
    public function theLengthCheckNamesWhatToCutAndWhoCutsIt(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Make the git based CGL suites work in worktrees',
            'issue' => '110534',
            'releases' => ['main'],
            'isBreaking' => false,
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        $checks = $this->checksWithCode($result['checks'], 'summary-length-preferred');
        self::assertCount(1, $checks);
        self::assertStringContainsString('4 fewer than the one you passed', $checks[0]['message']);
        self::assertStringContainsString('summary="..."', $checks[0]['message']);
        self::assertStringStartsWith(
            '[BUGFIX] Make the git based CGL suites work in worktrees',
            $result['message'],
            'the draft carries the summary that was passed',
        );
    }

    /**
     * The trailer the feedback was filed about came back clean, and a long dead
     * branch would have come back the same way — `D-ANS-058`.
     */
    #[Decision('D-ANS-058')]
    #[Test]
    public function aBranchOutOfSupportIsAnErrorNamingTheLinesThatTake(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Keep the line breaks',
            'issue' => '88556',
            'releases' => ['main', '9.5'],
            'isBreaking' => false,
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        $checks = $this->checksWithCode($result['checks'], 'unmaintained-release-line');
        self::assertCount(1, $checks);
        self::assertSame('error', $checks[0]['level']);
        self::assertStringContainsString('2025-09-30', $checks[0]['message']);
        self::assertStringContainsString('main', $checks[0]['message']);
        self::assertNotContains('no-issues-found', array_column($result['checks'], 'code'));
    }

    /**
     * A branch the list has never heard of is a warning, because the list ages
     * in one direction: a line that ends does so on a date it already carries,
     * and a line that opens is a branch created after it was read —
     * `D-ANS-058`.
     */
    #[Decision('D-ANS-073')]
    #[Decision('D-ANS-058')]
    #[Test]
    public function aBranchTheListDoesNotCarryIsAWarningSayingWhenItWasRead(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Keep the line breaks',
            'issue' => '88556',
            'releases' => ['14.1'],
            'isBreaking' => false,
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        $checks = $this->checksWithCode($result['checks'], 'unknown-release-line');
        self::assertCount(1, $checks);
        self::assertSame('warning', $checks[0]['level']);
        self::assertStringContainsString(ReleaseLines::readAt(), $checks[0]['message']);
        self::assertStringContainsString(ReleaseLines::source(), $checks[0]['message']);
    }

    /**
     * `D-ANS-073`. A maintained line further back than the ordinary reach is
     * neither an error nor nothing: a bug fix and a task go to the development
     * line and the one back from it, and an older one is earned by the severity
     * of the defect rather than by the defect being present there.
     *
     * The change type and the lines it does go to are asserted because that is
     * the half a session acts on. Warning that 13.4 claims a severity says what
     * to drop; "a BUGFIX is released on main, 14.3" says what to write instead,
     * and a trim keeping the severity sentence alone leaves a caller told their
     * trailer is wrong and not what the right one is.
     */
    #[Decision('D-ANS-073')]
    #[Test]
    public function aMaintainedLineFurtherBackSaysWhatItClaims(): void
    {
        $older = ReleaseLines::releasable()[2] ?? null;
        self::assertNotNull($older, 'the list carries no line beyond the ordinary reach to hold this against');

        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Keep the line breaks',
            'issue' => '88556',
            'releases' => [...ReleaseLines::ordinary(), $older],
            'isBreaking' => false,
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        $checks = $this->checksWithCode($result['checks'], 'older-release-line');
        self::assertCount(1, $checks, 'the lines within the ordinary reach are not held against the trailer');
        self::assertSame('warning', $checks[0]['level']);
        self::assertStringContainsString($older, $checks[0]['message']);
        self::assertStringContainsString('priority bug fix', $checks[0]['message']);
        self::assertStringContainsString('BUGFIX', $checks[0]['message']);
        self::assertStringContainsString(implode(', ', ReleaseLines::ordinary()), $checks[0]['message']);

        // A feature is the release managers' call and never this warning.
        $feature = CommitMessage::create([
            'changeType' => 'FEATURE',
            'summary' => 'Keep the line breaks',
            'issue' => '88556',
            'releases' => [$older],
            'isBreaking' => false,
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);
        self::assertSame([], $this->checksWithCode($feature['checks'], 'older-release-line'));
    }

    /**
     * A check that only refuses arrives too late: the session that filed this
     * had already counted the trailers on 40 commits by then — `D-ANS-058`.
     */
    #[Decision('D-ANS-058')]
    #[Test]
    public function theMissingTrailerNamesTheLinesThatTakeAPatch(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Keep the line breaks',
            'issue' => '88556',
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ]);

        $checks = $this->checksWithCode($result['checks'], 'missing-releases');
        self::assertCount(1, $checks);
        foreach (ReleaseLines::releasable() as $branch) {
            self::assertStringContainsString($branch, $checks[0]['message']);
        }
    }

    /**
     * The trailer belongs to the core repository alone, so a project that writes
     * one is naming its own releases and there is nothing here to hold it
     * against.
     */
    #[Test]
    public function outsideTheCoreNoBranchIsHeldAgainstTheLines(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Keep the line breaks',
            'releases' => ['9.5', '14.1'],
            'workflow' => CommitMessage::WORKFLOW_PROJECT,
        ]);

        self::assertSame(['no-issues-found'], array_column($result['checks'], 'code'));
        self::assertStringContainsString("\nReleases: 9.5, 14.1", $result['message']);
    }

    /** @return array<int, string> */
    private function bodyLines(string $message): array
    {
        $lines = explode("\n", $message);
        array_shift($lines); // subject
        $body = [];
        foreach ($lines as $line) {
            if (preg_match('/^(Resolves|Related|Releases):/', $line) === 1) {
                break;
            }
            if (trim($line) !== '') {
                $body[] = $line;
            }
        }

        return $body;
    }

    /**
     * @param array<int, array{level: string, code: string, message: string}> $checks
     * @return array<int, array{level: string, code: string, message: string}>
     */
    private function checksWithCode(array $checks, string $code): array
    {
        return array_values(array_filter(
            $checks,
            static fn(array $check): bool => $check['code'] === $code
        ));
    }
}
