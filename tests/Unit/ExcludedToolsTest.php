<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Feedback\Channel;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Knowledge\Coverage;
use TYPO3\DevCompanion\Knowledge\Scope;
use TYPO3\DevCompanion\Server\ExcludedTools;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\Registry;

#[Requirement('R-SCO-007')]
final class ExcludedToolsTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheExclusionsAndTheInstance(): void
    {
        putenv(ExcludedTools::VARIABLE);
        Instance::discoverFrom(null);
    }

    #[Test]
    #[DataProvider('everyKindOfRepositoryAToolListIsAskedFrom')]
    public function theKindOfRepositoryNeverShortensTheToolList(string $kind): void
    {
        // What the profile used to do here, and the reason it is gone. A
        // Composer project cannot follow the core's contribution process, but
        // whether a task is core work is a property of the task. A caller who
        // wrote a core patch from a site installation got an answer that routed
        // to a tool the profile had taken away.
        Instance::discoverFrom($this->rootOf($kind));

        $offered = $this->toolNames();
        self::assertContains('typo3_rule_lookup', $offered);
        self::assertContains('typo3_script_lookup', $offered);
        self::assertContains('typo3_test_run_guide', $offered);
    }

    /** @return array<string, array{0: string}> */
    public static function everyKindOfRepositoryAToolListIsAskedFrom(): array
    {
        return [
            'a Composer project' => ['project'],
            'a core checkout' => ['core'],
            'no installation at all' => ['none'],
        ];
    }

    /**
     * Each case builds its roots, so the provider names one rather than makes
     * it.
     */
    private function rootOf(string $kind): ?string
    {
        return match ($kind) {
            'project' => $this->composerProject(),
            'core' => $this->coreCheckout(),
            default => null,
        };
    }

    #[Test]
    public function aCoreShapedTaskFromAProjectIsAnswered(): void
    {
        Instance::discoverFrom($this->composerProject());

        $guide = Registry::call('typo3_task_guide', [
            'task' => 'Fix a QueryBuilder bug in typo3/sysext/core and push it to Gerrit',
            'changeType' => 'bugfix',
        ]);

        // The failure D-AUD-002 recorded from E-SITE. The answer is core work,
        // names the targeted runTests.sh invocation, and the client could not
        // call the tool the answer sent it to.
        self::assertStringContainsString('typo3_test_run_guide', $guide->text);
        self::assertContains('typo3_test_run_guide', $this->toolNames());
    }

    #[Requirement('R-SCO-009')]
    #[Test]
    public function onlyTheCallerShortensTheList(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_icon_lookup, typo3_label_lookup');

        $offered = $this->toolNames();
        self::assertNotContains('typo3_icon_lookup', $offered);
        self::assertNotContains('typo3_label_lookup', $offered);
        self::assertContains('typo3_hint_lookup', $offered);
    }

    #[Requirement('R-SCO-009')]
    #[Test]
    public function theScopeNamesWhatTheCallerExcluded(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_icon_lookup, typo3_label_lookup');

        $scope = Registry::call('typo3_server_scope', []);

        self::assertSame(['typo3_icon_lookup', 'typo3_label_lookup'], $scope->data['excludedTools']['names']);
        self::assertSame(ExcludedTools::VARIABLE, $scope->data['excludedTools']['variable']);
        self::assertStringContainsString('typo3_icon_lookup', $scope->text);
        self::assertStringContainsString(ExcludedTools::VARIABLE, $scope->text);
    }

    #[Test]
    public function theScopeSaysNothingAboutExclusionsWhereThereAreNone(): void
    {
        $scope = Registry::call('typo3_server_scope', []);

        self::assertSame([], $scope->data['excludedTools']['names']);
        self::assertSame([], $scope->data['excludedTools']['ignored']);
        self::assertStringNotContainsString(ExcludedTools::VARIABLE, $scope->text);
    }

    /**
     * What both client surfaces said before this: the name the caller wrote,
     * whether or not it took a tool away. Measured on 2026-08-04 with
     * `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS=typo3_project_scope`. The instructions
     * opened "typo3_project_scope is left out of your tool list" out of the
     * budget `R-ANS-013` holds, while `typo3_project_describe` was in the list
     * — `D-AUD-006`.
     */
    #[Requirement('R-SCO-009')]
    #[Decision('D-AUD-006')]
    #[Test]
    #[DataProvider('everyNameThatTakesNoToolAway')]
    public function neitherSurfaceCallsAToolExcludedThatIsInTheList(string $name): void
    {
        putenv(ExcludedTools::VARIABLE . '=' . $name);

        self::assertContains($name, $this->toolNames(), 'the premise: the tool is offered');
        self::assertSame([], ExcludedTools::all());
        self::assertStringNotContainsString('left out of your tool list', Coverage::instructions());

        $scope = Registry::call('typo3_server_scope', []);
        self::assertSame([], $scope->data['excludedTools']['names']);
        self::assertStringNotContainsString('is left out', $scope->text);
        self::assertStringNotContainsString('missing from the tool list', $scope->text);
    }

    /** @return array<string, array{0: string}> */
    public static function everyNameThatTakesNoToolAway(): array
    {
        return [
            'the tool that explains a short list' => ['typo3_server_scope'],
            'a feedback tool the channel decides' => ['typo3_feedback_record'],
        ];
    }

    /**
     * The other half: a name that took nothing away is still said, where the
     * client reads it. `D-AUD-005` put the stderr line in and left this open. A
     * client is free to capture that stream and show it to nobody —
     * `D-AUD-006`.
     */
    #[Decision('D-AUD-006')]
    #[Test]
    public function theScopeNamesWhatTookNothingAwayAsIgnored(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_project_scope, typo3_feedback_record, typo3_icon_lookup');

        $scope = Registry::call('typo3_server_scope', []);

        self::assertSame(['typo3_icon_lookup'], $scope->data['excludedTools']['names']);
        self::assertSame(
            ['typo3_project_scope', 'typo3_feedback_record'],
            $scope->data['excludedTools']['ignored'],
        );
        self::assertStringContainsString('took nothing away', $scope->text);
        self::assertStringContainsString('no tool of this server answers to that name', $scope->text);
    }

    #[Test]
    public function theScopeNeverPointsAtAToolTheCallerExcluded(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_test_run_guide, typo3_rule_lookup');

        $rendered = json_encode(Coverage::offered(), JSON_THROW_ON_ERROR);

        foreach (ExcludedTools::all() as $tool) {
            self::assertStringNotContainsString($tool, $rendered, $tool . ' is not offered but still routed to');
        }
    }

    #[Test]
    public function aTopicStaysAsLongAsSomethingStillAnswersIt(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_test_run_guide, typo3_rule_lookup, typo3_script_lookup');

        // The scope of a topic says which kind of work its answers are for. It
        // never decided whether the topic is on offer — that was the profile,
        // which read the repository where the task was meant.
        self::assertContains(Scope::Any, array_column(Coverage::offered()['covers'], 'scope'));
    }

    /**
     * The hole `a4470ee` fell into. `typo3_project_scope` got a new name, and
     * the caller who had excluded it got the tool back under it with nothing
     * said on either side. The server reports the name now, and the exclusion
     * beside it still takes its tool away — `D-AUD-005`.
     */
    #[Decision('D-AUD-005')]
    #[Test]
    public function aNameNoToolAnswersToLeavesTheRealOneExcluding(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_project_scope, typo3_icon_lookup');

        self::assertSame(['typo3_project_scope'], ExcludedTools::unknown());

        $offered = $this->toolNames();
        self::assertNotContains('typo3_icon_lookup', $offered);
        self::assertContains('typo3_project_describe', $offered, 'the renamed tool is what the caller now gets');
    }

    #[Test]
    public function aListEveryToolAnswersToIsReportedAsNothing(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_icon_lookup, typo3_server_scope');

        self::assertSame([], ExcludedTools::unknown());
    }

    #[Requirement('R-SCO-009')]
    #[Test]
    public function theToolThatExplainsAShortListCannotBeExcluded(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_server_scope');

        self::assertSame([], ExcludedTools::all());
        self::assertContains('typo3_server_scope', $this->toolNames());
    }

    /**
     * The other exception, and the one `453e439` read as a hole. The feedback
     * tools are on offer wherever the channel is, and the variable does not
     * reach them — R-SCO-009, D-FBK-042. What they write is this checkout, not
     * the installation the server read.
     */
    #[Requirement('R-SCO-009')]
    #[Decision('D-FBK-042')]
    #[Test]
    public function theFeedbackToolsFollowTheChannelAndNoExclusionReachesThem(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_feedback_record, typo3_feedback_list, typo3_icon_lookup');

        $offered = $this->toolNames();
        self::assertNotContains('typo3_icon_lookup', $offered);
        self::assertSame(
            Channel::isAvailable(),
            in_array('typo3_feedback_record', $offered, true),
            'the channel decides whether the feedback tools are offered, and nothing else does',
        );
        self::assertSame(Channel::isAvailable(), in_array('typo3_feedback_list', $offered, true));
    }

    /** @return array<int, string> */
    private function toolNames(): array
    {
        return array_column(Registry::definitions(), 'name');
    }
}
