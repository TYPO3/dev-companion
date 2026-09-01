<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Knowledge\Coverage;
use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Knowledge\Hints;
use TYPO3\DevCompanion\Knowledge\Scope;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Server\ExcludedTools;
use TYPO3\DevCompanion\Server\Installer;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\Registry;

#[Requirement('R-KNW-022')]
final class ScopeTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Instance::ROOT_VARIABLE);
        Instance::discoverFrom(null);
        putenv(ExcludedTools::VARIABLE);
    }

    #[Requirement('R-SCO-001')]
    #[Test]
    public function aPathKnownAsSomebodysExtensionIsOutsideTheCore(): void
    {
        // The wording gave nothing away — "bootstrap_package" is an extension
        // key and matches no phrase. The installation knows what it is, and an
        // extension key is a path a caller names when there is no file yet.
        Instance::discoverFrom($this->composerProject());

        $extension = Registry::call('typo3_task_guide', ['task' => 'Add a content element', 'paths' => ['my_sitepackage']]);
        self::assertTrue(Scope::from($extension->data['scope'])->isOutsideTheCore());

        $systemExtension = Registry::call('typo3_task_guide', ['task' => 'Add a content element', 'paths' => ['core']]);
        self::assertFalse(Scope::from($systemExtension->data['scope'])->isOutsideTheCore());
    }

    /**
     * The installation the session sits in is the weakest of the signals, so a
     * path into the core still overrules it — `D-SCO-005`.
     */
    #[Requirement('R-SCO-001')]
    #[Decision('D-SCO-005')]
    #[Test]
    public function inASiteInstallationTheWorkIsOutsideTheCore(): void
    {
        Instance::discoverFrom($this->composerProject());

        self::assertTrue(Scope::of('', 'Add a content element with a backend preview')->isOutsideTheCore());
        self::assertSame(Scope::Core, Scope::of('typo3/sysext/core/Classes/Utility/GeneralUtility.php'));
    }

    #[Requirement('R-SCO-001')]
    #[Test]
    public function inACoreCheckoutNothingIsPushedOutsideByTheInstallationAlone(): void
    {
        Instance::discoverFrom($this->coreCheckout());

        self::assertSame(Scope::Core, Scope::of('', 'Add a content element with a backend preview'));
    }

    #[Requirement('R-AUD-002')]
    #[Test]
    public function whereNothingPlacesTheWorkTheAnswerSaysSo(): void
    {
        // The third value. Without an installation, without a path with a shape
        // of its own and without a word either way, the old boolean answered
        // "not outside the core" — which every caller read as the core, and
        // half of them were in their own repository.
        Instance::discoverFrom(null);

        self::assertSame(Scope::Uncertain, Scope::of('', 'Improve the query performance'));
        self::assertStringContainsString(
            'Nothing here says which repository',
            Registry::call('typo3_task_guide', ['task' => 'Improve the query performance'])->text,
        );
    }

    #[Requirement('R-SCO-001')]
    #[Test]
    public function namingTheCoreInOrderToRuleItOutIsNotEvidenceOfCoreWork(): void
    {
        // "not TYPO3 core, a composer package under vendor bk2k" reads to a
        // substring search exactly like claiming to be the core. What decides
        // is the marker that describes the work, not the one that accompanies
        // it — so the order of the signals is the whole answer here.
        self::assertSame(Scope::Extension, Scope::of(
            '',
            'Raise the compatibility of the third-party extension bootstrap_package '
            . '(not TYPO3 core, a composer package under vendor bk2k) to TYPO3 v14'
        ));
    }

    #[Requirement('R-SCO-001')]
    #[Test]
    public function aPathInsideAnExtensionIsRecognisedByItsShape(): void
    {
        // No core file is named that way from the core root: everything there
        // is below typo3/sysext/<key>/, and what is not is below Build/Scripts/
        // or Build/Sources/ — a bare Build/ is any repository that compiles
        // something, so it decides nothing.
        self::assertSame(Scope::Extension, Scope::of('Classes/DataProcessing/CardGroupProcessor.php'));
        self::assertSame(Scope::Extension, Scope::of('Configuration/TCA/Overrides/200_content_element.php'));
        self::assertSame(Scope::Core, Scope::of('typo3/sysext/core/Classes/Utility/GeneralUtility.php'));
        self::assertSame(Scope::Core, Scope::of('Build/Sources/Sass/component/_card.scss'));
    }

    #[Requirement('R-AUD-002')]
    #[Test]
    public function whatTheCoreKeepsInBuildIsOnlyTheCores(): void
    {
        // Build/Sources/ is the backend's Sass and TypeScript from the core
        // root — and from a site package's root it is that package's build
        // setup. What says which is the manifest at the root, which is what a
        // checkout's kind is read from.
        Instance::discoverFrom($this->composerProject());

        self::assertTrue(Scope::of('Build/Sources/Sass/theme.scss')->isOutsideTheCore());
    }

    /**
     * A path in the core's own build setup routes the core's own workflow.
     *
     * `feedback/2026-08-25-114802` ended in a core patch to three files below
     * `Build/Scripts/` with no skill invoked at any point. The brief answered
     * `scope: "core"`, marked its checklist core-only and named the workflow for
     * extensions: the route reads `Scope::isCoreWork()`, whose only core path
     * was `typo3/sysext/` — `D-SKL-080`.
     */
    #[Decision('D-SKL-080')]
    #[Test]
    public function aPathInTheCoresOwnBuildSetupRoutesTheCoresOwnWorkflow(): void
    {
        Instance::discoverFrom($this->coreCheckout());

        $task = 'Add a test for the suite dispatcher';
        $scripts = Registry::call('typo3_task_guide', ['task' => $task, 'paths' => ['Build/Scripts/runTests.sh']]);
        $sysext = Registry::call('typo3_task_guide', [
            'task' => $task,
            'paths' => ['typo3/sysext/core/Classes/Utility/GeneralUtility.php'],
        ]);

        self::assertSame($sysext->data['skills'], $scripts->data['skills']);
        self::assertContains('typo3-core-patch-development', $scripts->data['skills']);

        // What the layout keeps that a marker of its own would not: from a root
        // that declares an extension, `Build/Scripts/` is that extension's own
        // build setup and the core's workflow is not the one to load.
        Instance::discoverFrom($this->extensionRepository());
        $extension = Registry::call('typo3_task_guide', ['task' => $task, 'paths' => ['Build/Scripts/runTests.sh']]);

        self::assertNotContains('typo3-core-patch-development', $extension->data['skills']);
    }

    #[Requirement('R-SCO-001')]
    #[Decision('D-SCO-005')]
    #[Test]
    public function namingAnInstallationToReadDoesNotMoveWhereTheWorkIs(): void
    {
        // What TYPO3_DEV_COMPANION_ROOT is for: a core contributor points it at
        // a site installation because that is where the registered icons and
        // the shipped labels are. Reading the last signal off the same value
        // made it move the boundary as well, and every unmarked question about
        // the core checkout they were standing in came back as project work —
        // `D-SCO-005`.
        $site = $this->composerProject();
        putenv(Instance::ROOT_VARIABLE . '=' . $site);
        Instance::discoverFrom($this->coreCheckout());

        // What it may move: the installation being read.
        self::assertSame(realpath($site), Instance::root());
        self::assertContains('my_sitepackage', array_keys(Instance::packages()));

        // What it may not: which repository the work is in.
        self::assertSame(Scope::Core, Scope::of('', 'Add a content element with a backend preview'));
        self::assertSame(Scope::Core, Scope::of('Build/Sources/Sass/component/_card.scss'));
    }

    /**
     * The back half of `D-SCO-005`'s first **Wrong if**: a contributor standing
     * in `typo3/sysext/backend/` names the file the way their shell does. The
     * package layout is what such a path looks like, and it was read as
     * somebody's extension before the checkout was consulted at all.
     */
    #[Requirement('R-SCO-001')]
    #[Test]
    public function aPackageShapedPathInACoreCheckoutIsCoreWork(): void
    {
        Instance::discoverFrom($this->coreCheckout());

        self::assertSame(Scope::Core, Scope::of('Classes/Controller/EditDocumentController.php'));
        self::assertSame(Scope::Core, Scope::of('Resources/Private/Templates/Page/Tree.html'));
        self::assertSame(Scope::Core, Scope::of('Configuration/TCA/tt_content.php'));

        // What still ends the question the other way, from the same checkout: a
        // path that names the container an extension lives in.
        self::assertSame(Scope::Extension, Scope::of('packages/my_sitepackage/Classes/Controller/Foo.php'));

        // And the same paths in a site installation, where the shape is what it
        // says it is.
        Instance::discoverFrom($this->composerProject());
        self::assertSame(Scope::Extension, Scope::of('Classes/Controller/EditDocumentController.php'));

        // With no installation to place the session, the shape is the only
        // evidence in the call and still answers — the rule couldBeTheCore()
        // already follows for Build/Sources/.
        Instance::discoverFrom(sys_get_temp_dir());
        self::assertSame(Scope::Extension, Scope::of('Classes/Controller/EditDocumentController.php'));
    }

    #[Requirement('R-SCO-001')]
    #[Decision('D-SCO-005')]
    #[Test]
    public function theNamedInstallationIsTheEvidenceWhereNothingElseIs(): void
    {
        // The other half of the same sentence. A client that starts this server
        // away from the session's own directory breaks the walk-up, and
        // D-DIS-006 says the variable is what such a setup states instead — so
        // where the walk reaches no installation it is the only evidence there
        // is, and it answers — `D-SCO-005`.
        putenv(Instance::ROOT_VARIABLE . '=' . $this->coreCheckout());
        Instance::discoverFrom(sys_get_temp_dir());

        self::assertSame(Scope::Core, Scope::of('', 'Add a content element with a backend preview'));
    }

    /**
     * The two states the same repository is in, so what places it is the
     * manifest rather than the directory below it.
     *
     * @return iterable<string, array{0: bool}>
     */
    public static function theTwoStatesAnExtensionRepositoryIsIn(): iterable
    {
        yield 'as it is cloned' => [false];
        yield 'once composer install has run' => [true];
    }

    /**
     * `feedback/2026-08-18-070358`: every path of an extension repository came
     * back `uncertain`, and the brief handed the core's own suites to a
     * repository that has no `Build/Scripts/`. Three rungs were silent until
     * `composer install` had run, and the root manifest answers all three
     * before it (`D-SCO-012`).
     */
    #[Requirement('R-SCO-001')]
    #[Decision('D-SCO-012')]
    #[Test]
    #[DataProvider('theTwoStatesAnExtensionRepositoryIsIn')]
    public function anExtensionRepositoryIsPlacedByItsRootManifest(bool $installed): void
    {
        Instance::discoverFrom($this->extensionRepository($installed));

        self::assertSame(Scope::Extension, Scope::of('blog'), 'the key the manifest declares');
        self::assertSame(Scope::Extension, Scope::of('composer.json'), 'the manifest that declares it');
        self::assertSame(Scope::Project, Scope::of('.ddev/config.yaml'), 'the container setup around it');
        // Build/Sources/ is the backend's own from the core root and this
        // extension's build setup here, which the root manifest has settled.
        self::assertSame(Scope::Extension, Scope::of('Build/Sources/Sass/theme.scss'));
    }

    #[Requirement('R-SCO-001')]
    #[Decision('D-SCO-012')]
    #[Test]
    public function theDeclaredExtensionKeyPlacesAPath(): void
    {
        // The affordance the `paths` parameter documents — an extension key
        // counts as a path — read off the manifest where there is no
        // installation to know the key. It sits at the rung it did, so the
        // contribution workflow in the task text is still weaker (`R-SCO-001`)
        // — `D-SCO-012`.
        Instance::discoverFrom($this->extensionRepository());

        self::assertSame(Scope::Extension, Scope::of('blog', 'Triage the core issue this was reported as'));
    }

    #[Requirement('R-SCO-001')]
    #[Decision('D-SCO-012')]
    #[Test]
    public function aDotfileKeepsItsDotWhenAPathIsNormalised(): void
    {
        // `ltrim($path, './')` took the dot of the dotfile with the "./" it was
        // meant to strip, so nothing could reach the `.ddev/` entry at all —
        // `D-SCO-012`.
        Instance::discoverFrom(null);

        self::assertSame(Scope::Project, Scope::of('.ddev/config.yaml'));
        self::assertSame(Scope::Project, Scope::of('./.ddev/config.yaml'));
        self::assertSame(Scope::Extension, Scope::of('./packages/my_sitepackage/Classes/Controller/Foo.php'));
    }

    /**
     * The call the feedback recorded, in the arguments it was made with.
     *
     * What it got back was four `runTests.sh` suites and a
     * `checkExtensionScannerRst`, into a repository whose `Build/` holds two
     * phpunit configurations and a phpstan baseline — `D-SCO-012`.
     */
    #[Requirement('R-SCO-002')]
    #[Decision('D-SCO-012')]
    #[Test]
    public function aBriefInAnExtensionRepositoryHandsBackNoCoreSuite(): void
    {
        Instance::discoverFrom($this->extensionRepository());

        $result = Registry::call('typo3_task_guide', [
            'task' => 'Boot the local DDEV development installation for the blog extension repository: '
                . 'composer install, TYPO3 setup, site and demo content, so the backend and frontend answer',
            'changeType' => 'operations',
            'paths' => ['.ddev/config.yaml', 'composer.json', 'blog'],
        ]);

        self::assertSame(Scope::Extension->value, $result->data['scope']);
        self::assertSame([], $result->data['checks']);
        self::assertSame([], $result->data['testSuites']);
        foreach ($result->data['checkoutDiscovery'] as $entry) {
            self::assertFalse(Scope::isCoreOnly($entry['establish'] . ' ' . $entry['how']), $entry['establish']);
        }
    }

    #[Test]
    public function theScopeNamesWhatIsCoveredAndWhatIsNot(): void
    {
        $scope = Coverage::read();

        self::assertNotSame('', $scope['purpose']);
        self::assertNotSame([], $scope['covers']);
        self::assertNotSame([], $scope['doesNotCover']);
        self::assertNotSame([], $scope['routing']);
        self::assertNotSame([], $scope['checkoutDiscovery']);
    }

    #[Test]
    public function theInstructionsStateTheCheckoutBoundary(): void
    {
        $instructions = Coverage::instructions();

        self::assertNotSame('', $instructions);
        self::assertStringContainsString('checkout', $instructions);
    }

    #[Decision('D-ANS-004')]
    #[Requirement('R-ANS-013')]
    #[Requirement('R-DIS-025')]
    #[Decision('D-AUD-011')]
    #[Decision('D-AUD-012')]
    #[Decision('D-DIS-013')]
    #[Test]
    public function theInstructionsFitWhatAClientKeeps(): void
    {
        // The sentence below this one is why the length is held at all: both
        // release runs of 2026-07-31 were handed instructions cut from 3662 to
        // 2048 characters, and the half that fell off ended with "in English" —
        // `D-AUD-011`, `D-DIS-013`.
        self::assertLessThanOrEqual(Coverage::INSTRUCTIONS_BUDGET, mb_strlen(Coverage::instructions()));

        // Re-measured on 2026-08-24: 2033 characters, of which the index entry
        // for typo3_commit_message_guide is 96 and the imperative `D-AUD-012`
        // put on the second call 2. The largest assembly is the last case
        // below and not this one, which is what `D-SKL-062` had to fit in.
        self::assertLessThanOrEqual(
            Coverage::INSTRUCTIONS_BUDGET,
            mb_strlen(Coverage::instructions(Installer::NOTICE)),
            'instructions where the skills are stale',
        );

        // The prefix naming what was excluded is measured too, because it grows
        // with the list and a caller may exclude most of the server. The last
        // of the four is the largest assembly there is — 1960 characters, and
        // 2045 where the notice comes too, measured on 2026-08-24 — because the
        // prefix naming every tool is longer than the index it replaces.
        putenv(ExcludedTools::VARIABLE . '=' . implode(',', array_column(Registry::definitions(), 'name')));
        self::assertLessThanOrEqual(
            Coverage::INSTRUCTIONS_BUDGET,
            mb_strlen(Coverage::instructions()),
            'instructions where the caller excluded everything it could',
        );
        self::assertLessThanOrEqual(
            Coverage::INSTRUCTIONS_BUDGET,
            mb_strlen(Coverage::instructions(Installer::NOTICE)),
            'instructions where the caller excluded everything and the skills are stale too',
        );
    }

    #[Requirement('R-AUD-006')]
    #[Test]
    public function theQueryLanguageIsStatedWhereTheCallingAgentReadsIt(): void
    {
        // The one limitation the server cannot answer its way out of: the
        // corpus is English and the matching is lexical, so a query in another
        // language reaches the loanwords and nothing else. Telling the agent to
        // translate is the whole mitigation, which makes the sentence
        // load-bearing rather than decorative — and a client is free not to
        // surface the initialize instructions, so the orientation tool says it
        // too.
        self::assertStringContainsString('in English', Coverage::instructions());
        self::assertStringContainsString('in English', Registry::call('typo3_server_scope', [])->text);
    }

    /**
     * The instructions name the tool per question rather than the tools, which
     * is what a client that shows a name and no schema can act on —
     * `D-AUD-011`.
     */
    #[Requirement('R-ANS-009')]
    #[Requirement('R-ANS-032')]
    #[Decision('D-AUD-011')]
    #[Test]
    public function theScopeInstructionsOrientTheClientBeforeItsFirstCall(): void
    {
        self::assertStringContainsString('backend markup or a CSS class: typo3_component_lookup', Coverage::instructions());
        self::assertStringContainsString('a backend icon identifier: typo3_icon_lookup', Coverage::instructions());
        self::assertStringContainsString('a label, added or reworded: typo3_label_lookup', Coverage::instructions());
    }

    /**
     * `R-ANS-032`, `D-AUD-011`. The question is what the client cannot show.
     *
     * Two sessions in two projects called nothing at all under a client that
     * lists the tools by name and defers their schemas, so a name was the whole
     * of what either of them had to choose on.
     *
     * The commit guide is the third of them, `feedback/2026-08-18-113357`: six
     * commit messages written for a sitepackage repository with the convention
     * derived from `git log`, because "TYPO3 commit message" read as the core's
     * Gerrit convention. So the entry says whose repository it is for.
     *
     * It names the branches for `feedback/2026-08-25-105141`, which read the
     * entry, judged the core checkout's own AGENTS.md sufficient and wrote a
     * Releases: set the guide would have corrected.
     */
    #[Requirement('R-ANS-032')]
    #[Decision('D-AUD-011')]
    #[Test]
    public function theInstructionsIndexTheQuestionEachToolAnswers(): void
    {
        $instructions = Coverage::instructions();

        self::assertStringContainsString('What to call for what:', $instructions);
        self::assertStringContainsString('typo3_changelog_lookup', $instructions);
        self::assertStringContainsString(
            "the commit message, yours as much as the core's, and its branches: typo3_commit_message_guide",
            $instructions,
        );
    }

    /**
     * `D-AUD-007`. The corpus is reached by a call, here as everywhere else.
     *
     * The clause this displaced named the resource scheme and warned that the
     * client may not list it, which is the whole of what six sessions were
     * given: `feedback/2026-08-18-113425` quotes it back and reports finishing
     * without learning that any guide exists.
     */
    #[Decision('D-AUD-007')]
    #[Test]
    public function theIndexNamesTheCallThatReadsAWholeProcedure(): void
    {
        self::assertStringContainsString(
            'the whole procedure, not one fact out of it: '
            . 'typo3_rule_lookup with a documentId typo3_project_describe lists',
            Coverage::instructions(),
        );
        // An address is delivery to a client that renders a resource list and
        // to no other, which is `D-ANS-061`. Nothing in the one statement every
        // client receives may rest on that.
        self::assertStringNotContainsString('typo3://', Coverage::instructions());

        // Both names carry the entry, so taking either away drops it rather
        // than leaving half a route.
        putenv(ExcludedTools::VARIABLE . '=typo3_project_describe');
        self::assertStringNotContainsString('the whole procedure, not one fact', Coverage::instructions());
    }

    /**
     * A caller holding two names that both take its question has the
     * descriptions and nothing else: the initialize index is six entries
     * against a fixed budget and carries almost none of these tools.
     *
     * Which two are such a pair is a reading rather than a property of the
     * declarations — `D-ANS-072` says what was measured against making it a
     * check — so the pairs somebody noticed are the rows below, one row per
     * direction, and noticing one is a step of adding a tool.
     */
    #[Decision('D-ANS-072')]
    #[Decision('D-AUD-017')]
    #[DataProvider('toolsACallerCannotChooseBetween')]
    #[Test]
    public function theToolsACallerCannotChooseBetweenNameEachOther(string $tool, string $other): void
    {
        $described = array_column(Registry::definitions(), 'description', 'name');

        self::assertStringContainsString($other, $described[$tool]);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function toolsACallerCannotChooseBetween(): iterable
    {
        // Both names read as "how does this codebase do X", and the session
        // that could not choose grepped its checkout instead — the pair
        // `D-ANS-072` was written on, carrying most of the corpus.
        yield 'the convention lookup names the procedure lookup' => ['typo3_hint_lookup', 'typo3_rule_lookup'];
        yield 'the procedure lookup names the convention lookup' => ['typo3_rule_lookup', 'typo3_hint_lookup'];

        // What a table is made of against what is in it, the pair this server
        // gained on 2026-09-01.
        yield 'the schema lookup names the record lookup' => ['typo3_schema_lookup', 'typo3_record_lookup'];
        yield 'the record lookup names the schema lookup' => ['typo3_record_lookup', 'typo3_schema_lookup'];

        // The third tool on that question, and where the sighted session was
        // standing: what an extension registers reads as "what is in this
        // extension" (`D-AUD-017`).
        yield 'the extension answer names the record lookup' => ['typo3_extension_describe', 'typo3_record_lookup'];

        // The two describes: the project lists the extensions, and one of them
        // is what the other answers for.
        yield 'the project answer names the extension answer' => ['typo3_project_describe', 'typo3_extension_describe'];
        yield 'the extension answer names the project answer' => ['typo3_extension_describe', 'typo3_project_describe'];

        // A type=flex column is one column in the schema answer and a data
        // structure in the other.
        yield 'the schema lookup names the flex lookup' => ['typo3_schema_lookup', 'typo3_flexform_lookup'];
        yield 'the flex lookup names the schema lookup' => ['typo3_flexform_lookup', 'typo3_schema_lookup'];

        // Both read as "how do I run the core's tests": the guide answers which
        // suite and what it does, the lookup the script's own notes.
        yield 'the suite guide names the script lookup' => ['typo3_test_run_guide', 'typo3_script_lookup'];
        yield 'the script lookup names the suite guide' => ['typo3_script_lookup', 'typo3_test_run_guide'];

        // Both read as "what about this label": one searches what is
        // registered, the other computes the reference from a path, including
        // for a file that is not there yet.
        yield 'the label lookup names the domain lookup' => ['typo3_label_lookup', 'typo3_translation_domain_lookup'];
        yield 'the domain lookup names the label lookup' => ['typo3_translation_domain_lookup', 'typo3_label_lookup'];

        // The scope exists to say what a component miss is worth, and the
        // lookup it reports on never named it.
        yield 'the component lookup names its scope' => ['typo3_component_lookup', 'typo3_snapshot_scope'];
        yield 'the snapshot scope names the component lookup' => ['typo3_snapshot_scope', 'typo3_component_lookup'];
    }

    /**
     * The room that entry was paid out of, and the reason the index is data.
     *
     * A caller that had excluded most of the server was still told which four
     * tools to call — the case that binds `R-ANS-013`, spending the budget on
     * an index of tools that caller does not have — `D-AUD-011`.
     */
    #[Decision('D-AUD-011')]
    #[Test]
    public function theIndexNamesNoToolTheCallerExcluded(): void
    {
        // Read before anything is excluded: the registry answers with the list
        // as it is filtered, so asking it afterwards would leave out the two
        // taken away here and hand them back in the second half.
        $everyTool = implode(',', array_column(Registry::definitions(), 'name'));

        putenv(ExcludedTools::VARIABLE . '=typo3_icon_lookup,typo3_commit_message_guide');
        $instructions = Coverage::instructions();

        // The questions rather than the tool names: the prefix in front of the
        // routing names both of them, which is what it is there for.
        self::assertStringNotContainsString('a backend icon identifier', $instructions);
        self::assertStringNotContainsString('the commit message, yours as much as', $instructions);
        // What is still offered keeps its entry, so the index is shorter rather
        // than gone.
        self::assertStringContainsString('backend markup or a CSS class: typo3_component_lookup', $instructions);

        // And with nothing left to name, the heading and the note go with the
        // entries: a list of none is worse than no list.
        putenv(ExcludedTools::VARIABLE . '=' . $everyTool);
        self::assertStringNotContainsString('What to call for what:', Coverage::instructions());
    }

    /**
     * The entry point claims the work that ends before a patch.
     *
     * It used to say "The coding agent writes the patch; this server supplies
     * the task knowledge and workflows around it", and a session asked whether a
     * 2006 bug still reproduces read that as addressed to work it was
     * deliberately not doing, skipping the guide and saying it would make the
     * same call again from the same wording (`D-AUD-009`). Triage, reproduction
     * and pricing a fix are three task shapes `scenarios/` holds cases for.
     */
    #[Decision('D-AUD-009')]
    #[Test]
    public function theEntryPointClaimsTheWorkThatEndsBeforeAPatch(): void
    {
        $instructions = Coverage::instructions();

        self::assertStringContainsString('Not every task ends in a patch', $instructions);
        self::assertStringNotContainsString('The coding agent writes the patch', $instructions);
        // What it says instead of who writes the patch, in the place where it
        // is about reading rather than about the subject.
        self::assertStringContainsString('yours to read in the checkout', $instructions);
    }

    /**
     * `D-AUD-012`. Both calls of the order are told, not described.
     *
     * Seventeen project tasks in one client called `typo3_project_describe`
     * eleven times and `typo3_task_guide` once, out of the same paragraph of the
     * same context window. What differed between the two was the mood, and step
     * 3 of `skills/base.md` has said "Run it in every session" all along.
     */
    #[Decision('D-AUD-003')]
    #[Decision('D-AUD-012')]
    #[Test]
    public function bothCallsOfTheEntryPointAreToldInTheImperative(): void
    {
        $instructions = Coverage::instructions();

        self::assertStringContainsString('Start every task with typo3_project_describe', $instructions);
        self::assertStringContainsString('Then call typo3_task_guide', $instructions);
    }

    /**
     * `D-SKL-062`. The same imperative covers the acts, not only the opening.
     *
     * A core patch was carried to green with zero calls to this server
     * (`feedback/2026-08-24-133515`), and the acts it took were named in
     * `TaskGuide::answer()` alone — which rides on an answer nobody asked for.
     * The instructions are the channel a session that calls nothing still
     * reads, so the acts are said here in the short form the budget allows and
     * in the brief in full.
     */
    #[Decision('D-AUD-012')]
    #[Decision('D-SKL-062')]
    #[Test]
    public function theSecondCallIsAskedAgainAtTheCallersOwnActs(): void
    {
        $instructions = Coverage::instructions();

        self::assertStringContainsString(
            'and again at the first test, check, commit or shipped file the task did not name',
            $instructions,
        );
    }

    #[Test]
    public function workOnAProjectExtensionIsRecognizedAsOutsideTheCore(): void
    {
        self::assertSame(Scope::Extension, Scope::of('', 'Create a new site set in a project extension'));
        self::assertSame(Scope::Extension, Scope::of('packages/my_sitepackage/Configuration/Sets/Main/config.yaml'));

        self::assertSame(Scope::Core, Scope::of('', 'Add a reusable site set to TYPO3 core'));
        self::assertSame(Scope::Core, Scope::of('typo3/sysext/frontend/Configuration/Sets/Fluid/config.yaml'));
        // A core path wins: a task naming both is core work that mentions the
        // other side, not the other way round.
        self::assertSame(Scope::Core, Scope::of(
            'typo3/sysext/core/Classes/Foo.php',
            'so that a project extension can override it'
        ));
    }

    #[Requirement('R-AUD-002')]
    #[Requirement('R-SCO-002')]
    #[Test]
    public function twoPathsOfDifferentAudienceInOneCallStayApart(): void
    {
        // META-03: an extension file and a core file in one session, because
        // the bug may be in either. Folded into one string the second path was
        // answered for by the first, and which one won was the order they
        // arrived in.
        $extension = 'packages/acme_events/Classes/Domain/Repository/EventRepository.php';
        $core = 'typo3/sysext/core/Classes/Database/Query/QueryBuilder.php';

        // Both orderings of one call rather than two cases: what follows is
        // about the same pair, and a provider would run it twice to hold a
        // symmetry that is two lines here.
        foreach ([[$extension, $core], [$core, $extension]] as $paths) {
            $decided = array_column(Scope::ofEach($paths), 'scope', 'path');
            self::assertSame(Scope::Extension, $decided[$extension], 'the core path answered for the other');
            self::assertSame(Scope::Core, $decided[$core], 'the extension path answered for the other');
        }

        // The suites are the core's own, so the core path keeps them and the
        // extension path is named as the one they are not for.
        $suites = Registry::call('typo3_test_run_guide', [
            'query' => 'which tests do I run for this change',
            'paths' => [$extension, $core],
        ]);
        self::assertNotSame([], $suites->data['suites']);
        self::assertStringContainsString($extension, $suites->text);
        self::assertStringNotContainsString($core, implode("\n", array_column($suites->data['suites'], 'command')));

        // The conventions transfer to both, the commands to one: a hint
        // returned for the extension path carries no core check.
        $hints = Registry::call('typo3_hint_lookup', [
            'task' => 'fix the query that reads the events',
            'paths' => [$extension, $core],
        ]);
        $decided = array_column($hints->data['scopes'], 'scope', 'path');
        self::assertSame(Scope::Extension, $decided[$extension]);
        self::assertSame(Scope::Core, $decided[$core]);
        self::assertStringContainsString('# For ' . $extension, $hints->text);
        self::assertStringContainsString('# For ' . $core, $hints->text);
    }

    /**
     * The third tool of `META-03`, and the one that could not answer it: the
     * brief was composed for one `area`, so the prompt reached it as one
     * question. It takes the paths now and places each of them, which is
     * `D-SCO-009` — the checklist, the checks and the discovery steps stay one
     * list, and the brief names the paths the core's own are not for.
     */
    #[Requirement('R-AUD-002')]
    #[Decision('D-SCO-009')]
    #[Test]
    public function aBriefForPathsOfDifferentAudienceSaysWhichStepsAreForWhich(): void
    {
        $extension = 'packages/acme_events/Classes/Domain/Repository/EventRepository.php';
        $core = 'typo3/sysext/core/Classes/Database/Query/QueryBuilder.php';

        $guide = Registry::call('typo3_task_guide', [
            'task' => 'Fix the query that reads the events, in whichever of the two the bug turns out to be',
            'paths' => [$extension, $core],
            'changeType' => 'bugfix',
        ]);

        $decided = array_column($guide->data['scopes'], 'scope', 'path');
        self::assertSame(Scope::Extension->value, $decided[$extension]);
        self::assertSame(Scope::Core->value, $decided[$core]);

        // The core path is in the call, so the core's own steps are still
        // stated — named as being for it and not for the other.
        self::assertStringContainsString($extension . ' is outside the TYPO3 core', $guide->text);
        self::assertNotSame([], $guide->data['checks']);
        self::assertStringContainsString('# For ' . $extension, $guide->text);
        self::assertStringContainsString('# For ' . $core, $guide->text);
    }

    /**
     * The same call with the core path taken out: nothing in it is the core's,
     * so the brief drops the checks rather than naming who they are for —
     * `D-SCO-009`.
     */
    #[Decision('D-SCO-009')]
    #[Test]
    public function aBriefForExtensionPathsAloneKeepsNoCoreStep(): void
    {
        $guide = Registry::call('typo3_task_guide', [
            'task' => 'Fix the query that reads the events',
            'paths' => [
                'packages/acme_events/Classes/Domain/Repository/EventRepository.php',
                'packages/acme_events/Classes/Controller/EventController.php',
            ],
            'changeType' => 'bugfix',
        ]);

        self::assertSame(Scope::Extension->value, $guide->data['scope']);
        self::assertSame([], $guide->data['checks']);
        self::assertSame([], $guide->data['testSuites']);
        foreach ($guide->data['checklist'] as $entry) {
            self::assertFalse(Scope::isCoreOnly($entry), $entry);
        }
    }

    /**
     * `typo3/sysext/` in the touched paths, and nothing else — the one signal
     * `D-AUD-001` names as the thing that would make the combining unnecessary.
     *
     * Two-valued, because that is the whole of the claim: a path carries the
     * marker or it does not. There is no third answer to be had from it, which
     * is the first of the two things the table below measures.
     */
    private static function theSysextSignalAlone(string $path): Scope
    {
        return str_contains(mb_strtolower(str_replace('\\', '/', $path)), 'typo3/sysext/')
            ? Scope::Core
            : Scope::Extension;
    }

    /**
     * Every audience decision the two recorded runs made, in the arguments they
     * made it with.
     *
     * Read on 2026-08-02 out of `scenarios/runs/REVIEW-01.json` (`E-SITE`,
     * server `66813e3`) and `REVIEW-02.json` (`E-EXT`, server `b5555cb`): each
     * path handed to a tool that decides an audience, with the task text of the
     * call it arrived in, because the text is a signal and a truncated one is a
     * different question. `config/system/settings.php` appears in two calls of
     * `REVIEW-01` and is listed under each.
     *
     * Both runs are reviews of somebody's own repository, so the expected
     * answer is `outside-core` throughout. That is the finding rather than a
     * simplification: no recorded run has ever been made in `E-CORE`.
     *
     * @return iterable<string, array{0: string, 1: string}>
     */
    public static function everyAudienceTheRecordedRunsDecided(): iterable
    {
        $project = 'Project repository layout for a composer TYPO3 project: composer.json, Build/, Tests/E2E, '
            . 'config/system, public/';
        $content = 'Content element registration, TCA overrides for tt_content, inline child tables, TypoScript '
            . 'per content element, Fluid templates and backend previews';
        $security = 'Frontend security: escaping user input in Fluid, query building, DTO from GET arguments, '
            . 'repository queries, secrets in configuration';
        $labels = 'XLF language files, source language, translation domains, icon registration and event '
            . 'listeners with Services.yaml';

        yield 'REVIEW-01 the brief for the audit' => [
            'printworks_sitepackage',
            'Audit a TYPO3 site package and the surrounding composer project for conformance, security and '
                . 'upgrade readiness',
        ];
        foreach ([
            'composer.json', 'Build/phpunit/UnitTests.xml', 'Tests/E2E/catalogue.spec.ts',
            'config/system/settings.php', 'public/index.php',
        ] as $path) {
            yield 'REVIEW-01 the project layout, ' . $path => [$path, $project];
        }
        foreach ([
            'extensions/printworks_sitepackage/Configuration/TCA/Overrides/tt_content_hero.php',
            'extensions/printworks_sitepackage/Configuration/TCA/tx_printworks_hero_slide.php',
            'extensions/printworks_sitepackage/Configuration/Sets/Printworks/TypoScript/ContentElement/Hero.typoscript',
            'extensions/printworks_sitepackage/Resources/Private/Templates/Content/PrintworksHero.fluid.html',
        ] as $path) {
            yield 'REVIEW-01 the content element, ' . $path => [$path, $content];
        }
        foreach ([
            'extensions/printworks_sitepackage/Classes/Controller/ProductController.php',
            'extensions/printworks_sitepackage/Classes/Domain/Repository/ProductRepository.php',
            'extensions/printworks_sitepackage/Classes/Domain/Model/Dto/ProductDemand.php',
            'config/system/settings.php',
        ] as $path) {
            yield 'REVIEW-01 the frontend security, ' . $path => [$path, $security];
        }
        foreach ([
            'extensions/printworks_sitepackage/Resources/Private/Language/messages.xlf',
            'extensions/printworks_sitepackage/Configuration/Icons.php',
            'extensions/printworks_sitepackage/Configuration/Services.yaml',
            'extensions/printworks_sitepackage/Classes/EventListener/PrefillProductRequestForm.php',
        ] as $path) {
            yield 'REVIEW-01 the labels and listeners, ' . $path => [$path, $labels];
        }

        yield 'REVIEW-02 the brief for the audit' => [
            'news',
            'Audit a widely used third-party TYPO3 extension for maintainability and supportability risks across '
                . 'package metadata, registration, TCA, TypoScript, Fluid, security and tests',
        ];
        foreach ([
            'Extension package metadata, composer.json and ext_emconf.php agreement, autoloading and supported '
                . 'TYPO3 version range for a third-party extension' => ['composer.json', 'ext_emconf.php'],
            'Dependency injection, Services.yaml, event listeners, hooks and console commands registration in an '
                . 'extension' => ['Configuration/Services.yaml', 'Classes/Hooks', 'Classes/Command', 'ext_localconf.php'],
            'Site sets, TypoScript constants and setup, TSconfig for a frontend extension'
                => ['Configuration/Sets/News/setup.typoscript', 'Configuration/page.tsconfig'],
            'Fluid templates, ViewHelpers, output escaping and safe rendering in a frontend extension'
                => ['Resources/Private/Templates', 'Resources/Private/Partials', 'Classes/ViewHelpers'],
            'Language files, XLF labels and translations shipped by an extension'
                => ['Resources/Private/Language/locallang.xlf', 'Resources/Private/Language/locallang_db.xlf'],
            'Extbase domain model, repositories, TCA and upgrade wizards for extension tables'
                => ['Configuration/TCA/tx_news_domain_model_news.php', 'Classes/Domain/Repository', 'Classes/Updates', 'ext_tables.sql'],
            'Backend module registration, controller, access permissions and request handling'
                => ['Configuration/Backend/Modules.php', 'Classes/Controller/AdministrationController.php'],
        ] as $text => $paths) {
            foreach ($paths as $path) {
                yield 'REVIEW-02 ' . $path => [$path, $text];
            }
        }
    }

    /**
     * The half of `D-AUD-001`'s **Wrong if** that holds: on everything that has
     * actually been recorded, the single signal answers what the combination
     * answers.
     *
     * It has to be said what that is worth. Both runs are reviews of somebody's
     * own repository, so every one of these decisions is `outside-core`, which
     * is what the sysext check returns for every path without the marker. The
     * agreement is the corpus having one side rather than the two signals
     * reading the same evidence, and the other half of the measurement is the
     * test below.
     */
    #[Test]
    #[DataProvider('everyAudienceTheRecordedRunsDecided')]
    public function theSysextSignalAloneAnsweredEveryDecisionTheRecordedRunsMade(
        string $path,
        string $text,
    ): void {
        Instance::discoverFrom($this->composerProject());

        self::assertTrue(Scope::of($path, $text)->isOutsideTheCore());
        self::assertSame(Scope::Extension, self::theSysextSignalAlone($path));
    }

    /**
     * The same question asked of a core checkout, which is where the runs are
     * silent.
     *
     * The first eleven are what `.checkouts/14.3` has at its root, read on
     * 2026-08-02 — its nine entries other than `typo3/`, and the two below
     * `Build/` that no other repository keeps there. That is everything a
     * contributor touches which is not below `typo3/sysext/`. The three calls
     * after them are the commoner shape: `SITE-*` and `CORE-*` prompts are
     * sentences, and a brief is asked for before there is a file to name — so
     * what is named is a system extension key, or nothing at all.
     *
     * @return iterable<string, array{0: string, 1: string}>
     */
    public static function everyAudienceACoreCheckoutDecides(): iterable
    {
        foreach ([
            'Build/Scripts/runTests.sh', 'Build/Sources/Sass/backend.scss', 'Build/',
            'CODE_OF_CONDUCT.md', 'CONTRIBUTING.md', 'INSTALL.md', 'LICENSE.txt', 'README.md', 'SECURITY.md',
            'composer.json', 'composer.lock',
        ] as $path) {
            yield 'the checkout root, ' . $path => [$path, ''];
        }

        yield 'a fix that goes to review' => ['core', 'Fix the DataHandler regression and push it for review'];
        yield 'a new state on a list row' => ['backend', 'Add a new state to a backend list row'];
        yield 'a deprecation' => ['', 'Deprecate a public API method'];
    }

    /**
     * The half that does not, and the reason the combining stays.
     *
     * Every row here is core work, and the sysext check calls every one of them
     * outside the core — the marker is absent from all of them, and absence is
     * the only thing that signal has to go on. What that costs is concrete:
     * `Build/Scripts/runTests.sh` is the script every suite in
     * `typo3_test_run_guide` invokes, and the contributor standing in the
     * repository that has it would be told it is not theirs to run. The three
     * path-less calls cost the same thing for the same reason, before there is
     * a file to name at all.
     *
     * Two signals carry the rows: `Build/Scripts/` and `Build/Sources/` are the
     * core's layout where the manifest allows it to be the core, and every
     * other row is the installation — twice through a path it knows as a system
     * extension key, otherwise through the kind of checkout it is. So this is
     * the test a simplification fails: collapse `audienceOf` to the marker and
     * all fourteen of these turn.
     */
    #[Test]
    #[DataProvider('everyAudienceACoreCheckoutDecides')]
    public function theSysextSignalAloneAnsweredNothingACoreCheckoutDecides(
        string $path,
        string $text,
    ): void {
        Instance::discoverFrom($this->coreCheckout());

        self::assertSame(Scope::Core, Scope::of($path, $text));
        self::assertSame(
            Scope::Extension,
            self::theSysextSignalAlone($path),
            'the single signal has caught up with the combination here, and D-AUD-001 is worth re-measuring',
        );
    }

    #[Test]
    public function aTaskOutsideTheCoreIsToldSoBeforeTheChecklist(): void
    {
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Create a new TYPO3 site set in a project extension with config.yaml and TypoScript',
        ]);

        self::assertTrue(Scope::from($result->data['scope'])->isOutsideTheCore());
        self::assertStringStartsWith('This reads as work outside the TYPO3 core', $result->text);
    }

    #[Requirement('R-SCO-003')]
    #[Decision('D-GUI-009')]
    #[Decision('D-SCO-002')]
    #[Test]
    public function maintainingAnExtensionIsNotSubmittingAPatchToTheCore(): void
    {
        // "push" and "submit" describe maintenance work as readily as they
        // describe Gerrit. Reading one of them as a patch submission put the
        // entire core contribution workflow into an answer about a third-party
        // extension. Nothing here says which side this is, so the intent is
        // offered under its condition rather than stated — `D-GUI-009`,
        // `D-SCO-002`.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Maintain and extend the third-party TYPO3 extension bk2k/bootstrap-package for '
                . 'TYPO3 13.4 and 14.3: check TCA, TypoScript, Fluid templates, data processors and '
                . 'upgrade wizards for compatibility, choose tests and push the next release',
        ]);

        $confidence = array_column($result->data['intents'], 'confidence', 'id');
        self::assertSame('weak', $confidence['submission'] ?? null);
        self::assertStringContainsString('Possibly also: Patch submission', $result->text);
    }

    /**
     * A path into the core is the positive evidence a core-only intent needs,
     * so the same words that were demoted without one match strongly with it —
     * `D-SCO-002`.
     */
    #[Requirement('R-SCO-003')]
    #[Decision('D-SCO-002')]
    #[Test]
    public function aCorePathStillMakesTheSameWordAPatchSubmission(): void
    {
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Push the fix for review',
            'paths' => ['typo3/sysext/core/Classes/Utility/GeneralUtility.php'],
        ]);

        $confidence = array_column($result->data['intents'], 'confidence', 'id');
        self::assertSame('strong', $confidence['submission'] ?? null);
    }

    #[Requirement('R-SCO-003')]
    #[Decision('D-SCO-002')]
    #[Test]
    public function inASitePackageThePatchSubmissionIntentIsNotOfferedAtAll(): void
    {
        // There is no Gerrit to submit to, so this is not a weaker match — it
        // is not one — `D-SCO-002`.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Push the fix for review',
            'paths' => ['packages/my_sitepackage/Classes/Controller/EventController.php'],
        ]);

        self::assertTrue(Scope::from($result->data['scope'])->isOutsideTheCore());
        self::assertNotContains('submission', array_column($result->data['intents'], 'id'));
        self::assertStringNotContainsString('Change-Id', implode("\n", $result->data['checklist']));
    }

    /**
     * `D-SCO-002`'s own cost, in the sharpest form it has: the session sits in
     * a core checkout, the brief is composed for the core, and the submission
     * intent is still offered rather than stated. That is `D-SCO-005`'s
     * ordering — the installation is the weakest evidence there is, and the
     * text says nothing about where this patch goes.
     *
     * What keeps it cheap is that nothing is withheld. Both Gerrit steps
     * arrive whole, each carrying a condition the contributor answers without
     * looking anything up, so the cost is a prefix rather than a lookup.
     */
    #[Requirement('R-SCO-003')]
    #[Decision('D-SCO-002')]
    #[Test]
    public function aCoreTaskNamingNoPathKeepsTheSubmissionRules(): void
    {
        Instance::discoverFrom($this->coreCheckout());

        $result = Registry::call('typo3_task_guide', [
            'task' => 'Fix the page tree losing drag and drop when a mount point is set, then push the fix for review',
            'changeType' => 'bugfix',
        ]);

        $confidence = array_column($result->data['intents'], 'confidence', 'id');
        self::assertSame(Scope::Core, Scope::from($result->data['scope']));
        self::assertSame('weak', $confidence['submission'] ?? null);

        // The core's own checklist is still stated as fact around it.
        self::assertContains('Confirm the target TYPO3 core branch and issue context.', $result->data['checklist']);

        // The two steps that turn on where the patch goes are prefixed, not
        // dropped: the rule is readable without asking a second time.
        $conditional = array_values(array_filter(
            $result->data['checklist'],
            static fn(string $entry): bool => str_starts_with($entry, 'If the patch goes to the TYPO3 core itself:'),
        ));
        self::assertCount(2, $conditional);
        self::assertStringContainsString('refs/for/', implode("\n", $conditional));
        self::assertStringContainsString('Change-Id', implode("\n", $conditional));
    }

    /**
     * The other half of what keeps it cheap: the condition is not something the
     * reader has to live with. Where nothing placed the work at all the brief
     * names the question it could not answer, and naming a path is what turns
     * the same words into a stated submission — which
     * `aCorePathStillMakesTheSameWordAPatchSubmission` holds from the far side
     * — `D-SCO-002`.
     */
    #[Requirement('R-SCO-003')]
    #[Decision('D-SCO-002')]
    #[Test]
    public function theBriefNamesWhatWouldTurnTheConditionIntoFact(): void
    {
        Instance::discoverFrom(null);

        $result = Registry::call('typo3_task_guide', [
            'task' => 'Fix the page tree losing drag and drop when a mount point is set, then push the fix for review',
        ]);

        self::assertSame(Scope::Uncertain, Scope::from($result->data['scope']));
        self::assertStringStartsWith(Scope::UNCERTAIN_NOTICE, $result->text);
        self::assertStringContainsString(
            'Possibly also: Patch submission, if the patch goes to the TYPO3 core itself',
            $result->text,
        );
    }

    /**
     * `SITE-01` asks for an honest boundary where the site configuration and
     * the installation steps are, and the first hand reading of it on
     * 2026-08-02 found that boundary stated nowhere a caller looks first: the
     * `site-sets` hint says configuring one installation "stops being a
     * convention and becomes that site's decision", and orientation named
     * neither. What is covered stays covered — the set, and how its settings
     * resolve against the site's own.
     */
    #[Test]
    public function decidingOneSitesConfigurationIsDeclinedInTheOrientation(): void
    {
        $scope = Coverage::read();

        $declined = array_values(array_filter(
            $scope['doesNotCover'],
            static fn(array $entry): bool => str_contains(mb_strtolower($entry['topic']), "site's configuration"),
        ));
        self::assertCount(1, $declined, 'nothing in the orientation declines writing a site configuration');
        self::assertStringContainsString('docs.typo3.org', $declined[0]['instead']);
        self::assertStringContainsString('typo3_project_describe', $declined[0]['instead']);

        // The neighbouring subject is not declined with it: a set is a
        // convention, and the boundary runs between the mechanism and the
        // values one installation puts in it.
        self::assertNotNull(Hints::byId('site-sets'));
        self::assertStringContainsString(
            'site set',
            mb_strtolower(implode("\n", array_column($scope['covers'], 'topic'))),
        );
    }

    /**
     * The declined subject holds one question this server does answer, and it
     * is the one asked first: the interpreter is written into the environment
     * file before anything is installed, and `php-versions` says which. The
     * entry sent the caller outside for all of it.
     *
     * `D-SKL-012` put the environment file a repository declares on the covered
     * side and the payload never followed; `R-KNW-072` then landed the answer
     * inside the excluded topic. What stays declined is the operating around
     * it, which is the reading `D-KNW-010` and `D-KNW-049` both took.
     */
    #[Decision('D-SKL-012')]
    #[Requirement('R-SCO-008')]
    #[Test]
    public function theDeclaredInterpreterIsNotDeclined(): void
    {
        $declined = array_values(array_filter(
            Coverage::read()['doesNotCover'],
            static fn(array $entry): bool => str_contains(mb_strtolower($entry['topic']), 'running an installation'),
        ));
        self::assertCount(1, $declined, 'nothing in the orientation declines operating an installation');

        self::assertNotNull(Hints::byId('php-versions'));
        self::assertStringContainsString('php-versions', $declined[0]['instead']);
        self::assertStringContainsString('typo3_hint_lookup', $declined[0]['instead']);
    }

    #[Requirement('R-AUD-001')]
    #[Requirement('R-SCO-008')]
    #[Test]
    public function aTopicWithAHintIsNotDeclined(): void
    {
        // The declared scope still ruled out project and extension work, and
        // upgrading an installation, while both had hints of their own. A
        // caller cannot tell a boundary from a gap by the size of an answer,
        // and the two ask for opposite reactions.
        $excluded = mb_strtolower(implode("\n", array_column(Coverage::read()['doesNotCover'], 'topic')));

        self::assertNotNull(Hints::byId('sitepackage-layout'));
        self::assertStringNotContainsString('extension development', $excluded);
        self::assertNotNull(Hints::byId('installation-upgrade'));
        self::assertStringNotContainsString('upgrading an installation', $excluded);

        // And the list says what it is worth read from the other side.
        self::assertStringContainsString(
            'gap in the knowledge base',
            Registry::call('typo3_server_scope', [])->text
        );
    }

    /**
     * Denials that are false about this server, in the words they were written
     * in.
     *
     * The half that reads the installation is what makes each of them false —
     * `typo3_project_describe` answers `kind: core-checkout` for a core checkout
     * and reads the commands out of its `composer.json`, and
     * `typo3_schema_lookup` answers a table as the container assembles it.
     *
     * @var array<string, string>
     */
    private const NOT_TRUE_OF_THIS_SERVER = [
        'never reads' => 'the installation half reads the directory it was started in',
        'cannot be pointed at' => 'typo3_project_describe is pointed at one and answers what kind it is',
        'answers from its own bundled knowledge and never' => 'two of the four sources are outside this package',
        'no index of it is bundled' => '',
    ];

    /**
     * A boundary is a statement about this server, and a false one is worse
     * than a thin answer: a caller reads it and stops asking.
     *
     * The first `doesNotCover` entry told every client this server "never
     * reads, inspects, or runs anything against a TYPO3 core checkout. It
     * cannot be pointed at one." Started in a core checkout,
     * `typo3_project_describe` answers with its kind, its TYPO3 version, its
     * PHP constraint and the commands it declares. The topic was the only field
     * anything read, and the sentence was in the `why` — `D-FBK-037`.
     */
    #[Requirement('R-SCO-008')]
    #[Test]
    public function noExclusionDeniesASourceTheServerReads(): void
    {
        $denied = [];
        foreach (Coverage::read()['doesNotCover'] as $entry) {
            $written = mb_strtolower(implode(' ', array_filter($entry, is_string(...))));
            foreach (self::NOT_TRUE_OF_THIS_SERVER as $claim => $why) {
                if ($why !== '' && str_contains($written, $claim)) {
                    $denied[] = sprintf('"%s" — %s: %s', $entry['topic'], $claim, $why);
                }
            }
        }

        self::assertSame([], $denied, 'a boundary denies something this server does');
    }

    /**
     * The exclusion a session had already acted on before it read it.
     *
     * `feedback/2026-08-18-080743` asks that this entry survive any rewrite: a
     * whole session went on establishing what a class declares on two majors,
     * and the boundary said that was not this server's. Praise tests an
     * exclusion in no way — the session did what the `instead` prescribes before
     * the answer arrived — so what holds it is an assertion, and the `why` was
     * read against `src/` rather than believed. `D-FBK-018`.
     */
    #[Test]
    public function theExclusionForPhpSourceKeepsItsQualification(): void
    {
        $excluded = array_values(array_filter(
            Coverage::read()['doesNotCover'],
            static fn(array $entry): bool => str_contains(mb_strtolower($entry['topic']), 'php source'),
        ));
        self::assertCount(1, $excluded, 'nothing says PHP source as code is outside what this server reads');

        // The half a core reviewer holds it for: whether a removed member was
        // public API is what decides whether the removal is breaking.
        self::assertStringContainsString('@internal', $excluded[0]['topic']);
        // The only half of an exclusion a caller acts on.
        self::assertStringContainsString('Read the class', $excluded[0]['instead']);
        // The qualification a summarising rewrite drops first, and the one that
        // keeps the sentence true: registration files are read, and tokenised —
        // Extension::declarationsIn() takes TCA tables, content elements and
        // plugin signatures out of them, PhpArray and FluidNamespaces take the
        // keys of a configuration file, Instance::typo3Version() one constant.
        self::assertStringContainsString('never for a signature or an annotation', $excluded[0]['why']);
        // What is answered beside it, so the exclusion turns a caller away from
        // one question rather than from this server.
        foreach (['typo3_schema_lookup', 'typo3_changelog_lookup', 'typo3_hint_lookup'] as $tool) {
            self::assertStringContainsString($tool, $excluded[0]['instead']);
        }
    }

    /**
     * How the claim reads when it names who it turns away: something is put
     * beyond this server, and what it names is one of the audiences below.
     *
     * @var array<int, string>
     */
    private const BEYOND_THIS_SERVER = [
        'out of scope', 'out of this server', 'outside the scope', 'outside this server',
        'not in scope', 'is not covered', 'are not covered', 'not covered here',
        'not covered by', 'does not cover', 'do not cover',
    ];

    /**
     * The two audiences it keeps arriving at the expense of, in the words they
     * are written in. R-AUD-001 names three and the core contributor is the one
     * nobody ever excludes, so the third is not here.
     *
     * @var array<int, string>
     */
    private const THE_OTHER_AUDIENCES = [
        'extension', 'project', 'site package', 'sitepackage', 'site developer', 'installation',
    ];

    /**
     * How it reads when it names nobody: the server is confined to the core and
     * who that leaves out is left to the reader. Two of the three sentences
     * D-SCO-006 found were written this way, which is why the audience words
     * alone would not have caught them.
     *
     * @var array<int, string>
     */
    private const CONFINED_TO_THE_CORE = [
        'scoped to', 'limited to', 'restricted to', 'confined to', 'only knows',
        'only covers', 'only answers', 'only about', 'nothing but', 'exclusively', 'solely',
    ];

    /**
     * The claim in the wording it was actually found in, so the matcher below
     * is tested against something rather than only against prose that is
     * already correct.
     *
     * @var array<int, string>
     */
    private const THE_CLAIM_AS_IT_WAS_FOUND = [
        'The knowledge base is scoped to contributing to the core.',
        'Configuring an installation with TypoScript is out of this server\'s scope.',
        'This server only knows the core\'s own conventions.',
    ];

    /**
     * The three audiences are named where the server describes itself.
     *
     * The other assertion says what may not be claimed, and that is all it can
     * say across three hundred surfaces: a hint about backend CSS names the
     * core and nothing else, correctly. Where a reader meets the server whole —
     * the purpose, the instructions a client is handed, the readme — the demand
     * is the affirmative one, and `R-AUD-001` is what it is.
     */
    #[Decision('D-SCO-006')]
    #[Requirement('R-AUD-001')]
    #[Test]
    public function everyDescriptionOfTheServerNamesAllThreeAudiences(): void
    {
        $whole = [
            'the purpose in server-scope.json' => Coverage::read()['purpose'],
            'the instructions' => Coverage::instructions(),
            'readme.md' => (string) file_get_contents(Paths::root() . '/readme.md'),
        ];

        foreach ($whole as $surface => $text) {
            $lowered = mb_strtolower($text);
            foreach (['core', 'extension', 'site'] as $audience) {
                self::assertStringContainsString(
                    $audience,
                    $lowered,
                    $surface . ' describes the server without naming ' . $audience . ' work',
                );
            }
        }
    }

    /**
     * The same assertion as above, in the surfaces the not-covered list is not.
     *
     * That list is where the claim was written down last, not where it lived:
     * the same sentence stood in a knowledge document and in the notice every
     * tool opens with, and each was corrected on its own. `D-SCO-006` named the
     * three surfaces no test reads, and this is that test. What is matched is
     * wording, which is as weak as wording always is, so the three sentences the
     * decision recorded are run through the matcher first.
     */
    #[Decision('D-SCO-006')]
    #[Requirement('R-AUD-001')]
    #[Test]
    public function noSurfaceClaimsTheCoreAlone(): void
    {
        foreach (self::THE_CLAIM_AS_IT_WAS_FOUND as $sentence) {
            self::assertNotSame(
                [],
                self::claimsTheCoreAlone($sentence),
                'the claim is no longer recognised in the wording it was found in: ' . $sentence,
            );
        }

        foreach (self::surfacesThatDescribeTheServer() as $surface => $text) {
            self::assertSame(
                [],
                self::claimsTheCoreAlone($text),
                $surface . ' puts the extension author or the site developer outside what this server answers',
            );
        }
    }

    /**
     * The sentences of one surface that say the core is all of it.
     *
     * Sentence by sentence rather than over the whole text, because every
     * surface here names the core and names an extension, and only a sentence
     * that does both in the one construction is the claim.
     *
     * @return array<int, string>
     */
    private static function claimsTheCoreAlone(string $text): array
    {
        $claims = [];
        foreach (preg_split('/(?<=[.!?;:])\s+|\n/', $text) ?: [] as $sentence) {
            $lowered = mb_strtolower($sentence);
            $turnsAwayAnAudience = self::names($lowered, self::BEYOND_THIS_SERVER)
                && self::names($lowered, self::THE_OTHER_AUDIENCES);
            $confinesTheServer = self::names($lowered, self::CONFINED_TO_THE_CORE)
                && str_contains($lowered, 'core');

            if ($turnsAwayAnAudience || $confinesTheServer) {
                $claims[] = trim($sentence);
            }
        }

        return $claims;
    }

    /** @param array<int, string> $markers */
    private static function names(string $sentence, array $markers): bool
    {
        foreach ($markers as $marker) {
            if (str_contains($sentence, $marker)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Everywhere this server states its own boundary in prose.
     *
     * The explanations under the not-covered list are left out and their topics
     * are not: a `why` is where a subject that really is outside gets its
     * reason, and the sentence that says so has to be allowed to say it. The
     * topic line is the claim itself, and it is the one field the test above
     * already reads.
     *
     * The knowledge documents are left out for the same reason — they are the
     * core's own contribution material, where "this holds for the core
     * repository" is true rather than the claim.
     *
     * @return array<string, string>
     */
    private static function surfacesThatDescribeTheServer(): array
    {
        $scope = Coverage::read();
        $surfaces = ['the purpose in server-scope.json' => $scope['purpose']];

        foreach ($scope['covers'] as $entry) {
            $surfaces['the covered topic "' . $entry['topic'] . '"'] = $entry['topic'] . ' ' . $entry['depth'];
        }
        foreach ($scope['doesNotCover'] as $entry) {
            $surfaces['the not-covered topic "' . $entry['topic'] . '"'] = $entry['topic'];
        }
        foreach ($scope['routing'] as $entry) {
            $surfaces['the routing entry "' . $entry['when'] . '"'] = $entry['when'] . ' ' . $entry['call'];
        }

        // With and without an exclusion, because the prefix naming what was
        // left out is the sentence closest to the claim of all of them. The
        // variable is forgotten again by forgetTheInstance().
        $surfaces['the instructions'] = Coverage::instructions();
        putenv(ExcludedTools::VARIABLE . '=typo3_rule_lookup,typo3_script_lookup,typo3_test_run_guide');
        $surfaces['the instructions where the core surface was excluded'] = Coverage::instructions();
        putenv(ExcludedTools::VARIABLE);

        foreach (Registry::definitions() as $definition) {
            $surfaces['the description of ' . $definition['name']] = $definition['description'];
        }

        $surfaces['readme.md'] = (string) file_get_contents(Paths::root() . '/readme.md');

        foreach (Hints::load() as $hint) {
            $surfaces['the ' . $hint['id'] . ' hint'] = $hint['title'] . ' '
                . implode(' ', array_column($hint['hints'], 'text'));
        }

        return $surfaces;
    }

    #[Requirement('R-GUI-003')]
    #[Test]
    public function theBriefPointsAtTheGuideForTheStepItEndsWith(): void
    {
        // The brief's last checklist item is the commit message in all but
        // name, and the tool that writes one was never in its next lookups. A
        // caller reads the routing table once, at the start, and commits hours
        // later out of this list.
        $core = Registry::call('typo3_task_guide', [
            'task' => 'Fix the DataHandler regression',
            'paths' => ['typo3/sysext/core/Classes/DataHandling/DataHandler.php'],
        ]);
        self::assertContains('typo3_commit_message_guide', array_column($core->data['nextTools'], 'tool'));
        self::assertStringContainsString(
            'typo3_commit_message_guide',
            implode("\n", $core->data['checklist'])
        );
        self::assertStringContainsString('workflow="core"', self::followUp($core, 'typo3_commit_message_guide'));

        // And outside the core the call is listed bare, because the default is
        // that repository's own case since `D-GUI-010`.
        $project = Registry::call('typo3_task_guide', [
            'task' => 'Add a search to the product plugin',
            'paths' => ['packages/my_sitepackage/Classes/Controller/ProductController.php'],
        ]);
        self::assertTrue(Scope::from($project->data['scope'])->isOutsideTheCore());
        self::assertStringContainsString('typo3_commit_message_guide', implode("\n", $project->data['checklist']));

        // Both halves of the same answer, because they are read at different
        // moments: the checklist while planning, the follow-up calls at the
        // step itself. A call listed without the workflow is the default one,
        // and the default is this repository's case — the core is what has to
        // be stated (`D-GUI-010`).
        self::assertStringNotContainsString(
            'workflow="core"',
            self::followUp($project, 'typo3_commit_message_guide')
        );
    }

    /** What a brief says about when to call one of the tools it suggests next. */
    private static function followUp(ToolResult $brief, string $tool): string
    {
        foreach ($brief->data['nextTools'] as $suggestion) {
            if ($suggestion['tool'] === $tool) {
                return (string) $suggestion['when'];
            }
        }

        self::fail($tool . ' is not among the next lookups of this brief.');
    }

    #[Requirement('R-GUI-004')]
    #[Test]
    public function theBriefRoutesToTheToolsItsOwnSubjectsAreAnsweredBy(): void
    {
        // Forty label keys were invented in one session while typo3_label_lookup
        // was never called: the pointer was in the routing table and in the
        // hint, both read once, and the moment of need was hours later. The
        // brief is what a caller comes back to, so it carries them.
        $labels = Registry::call('typo3_task_guide', [
            'task' => 'Write the XLF language files for the sitepackage backend labels',
            'targetVersion' => '14',
        ]);
        $tools = array_column($labels->data['nextTools'], 'tool');
        self::assertContains('typo3_label_lookup', $tools);

        // And the changelog, which is where a version one has not built on
        // recently differs from the one in memory — not a retrospective lookup.
        self::assertContains('typo3_changelog_lookup', $tools);
        self::assertStringContainsString('14', implode("\n", array_column($labels->data['nextTools'], 'when')));
    }

    /**
     * The brief carries the count where the count decides something.
     *
     * The tool shipped on 2026-09-01 named in `server-scope.json` and in no
     * intent, so `typo3_task_guide` routed to it from nothing — including the
     * two wordings the sighting itself was made of. Measured that day over
     * seven task shapes: not one reached it. What it decides is where records
     * are maintained, what renders them, and what default a new column takes on
     * the rows already there — `D-AUD-017`,
     * `feedback/archive/2026-08-31-233952`.
     */
    #[Decision('D-AUD-017')]
    #[Test]
    public function theBriefRoutesToTheCountWhereTheCountDecidesSomething(): void
    {
        // The sighting's own words, which matched no intent at all before the
        // three needles were added.
        $complaint = Registry::call('typo3_task_guide', [
            'task' => 'the editors complain the record list takes minutes to open',
            'changeType' => 'feature',
        ]);

        self::assertContains('backend-module', array_column($complaint->data['intents'], 'id'));
        self::assertContains('typo3_record_lookup', array_column($complaint->data['nextTools'], 'tool'));

        // And the task shape the sighting was worked as, where the count is
        // what the content model is decided on.
        $element = Registry::call('typo3_task_guide', [
            'task' => 'build a content element for the animal records',
            'changeType' => 'feature',
        ]);

        self::assertContains('typo3_record_lookup', array_column($element->data['nextTools'], 'tool'));
    }

    #[Requirement('R-SCO-002')]
    #[Test]
    public function aBriefOutsideTheCoreKeepsNothingThatOnlyTheCoreHas(): void
    {
        // Saying "this is outside the core" and then listing four runTests.sh
        // suites, a changelog file below typo3/sysext/ and the core branch
        // policy is not a partly right answer: the flag says the brief knew.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Add a data processor and an upgrade wizard to my site package',
            'paths' => ['packages/my_sitepackage/Classes/DataProcessing/CsvProcessor.php'],
            'changeType' => 'feature',
        ]);

        self::assertTrue(Scope::from($result->data['scope'])->isOutsideTheCore());
        self::assertSame([], $result->data['checks']);
        self::assertSame([], $result->data['testSuites']);
        self::assertSame([], $result->data['conditionalChecks']);
        foreach ($result->data['checklist'] as $entry) {
            self::assertFalse(Scope::isCoreOnly($entry), $entry . ' cannot be done outside the core');
        }
        foreach ($result->data['checkoutDiscovery'] as $entry) {
            self::assertFalse(Scope::isCoreOnly($entry['establish'] . ' ' . $entry['how']), $entry['establish']);
        }
        // The notice names the script once, to say it does not apply here.
        self::assertSame(1, substr_count($result->text, 'runTests.sh'));
    }

    #[Decision('D-SCO-016')]
    #[Requirement('R-SCO-002')]
    #[Test]
    public function aTestFileNothingPlacesIsInTheRepositoryTheCallIsIn(): void
    {
        // `Tests/Functional/` is no layout marker, so the file a session writes
        // is placed nowhere while the class it covers is placed in an
        // extension. The pair is one piece of work in one repository.
        Instance::discoverFrom(null);

        $paths = ['Classes/Parser/AbstractParser.php', 'Tests/Functional/Parser/ScssParserTest.php'];
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Add functional regression tests for the parser',
            'paths' => $paths,
            'changeType' => 'test',
        ]);

        self::assertSame(Scope::Extension, Scope::from($result->data['scope']));
        self::assertSame([], $result->data['checks']);
        self::assertSame([], $result->data['testSuites']);

        // What the answer used to hand over is four suites of a script the
        // caller's repository does not have.
        $suites = Registry::call('typo3_test_run_guide', ['query' => 'the parser tests', 'paths' => $paths]);
        self::assertSame([], $suites->data['suites']);
        self::assertStringContainsString('project-extension-tests', $suites->text);
    }

    #[Decision('D-SCO-016')]
    #[Requirement('R-SCO-002')]
    #[Test]
    public function aPathNothingPlacesIsStillAnsweredFromTheCore(): void
    {
        // The fallback the rule above narrows and may not take: where the call
        // places nothing at all, the core's answer is the only one there is,
        // and the notice says which question went unanswered — `D-SCO-008`.
        Instance::discoverFrom(null);

        $result = Registry::call('typo3_task_guide', [
            'task' => 'Add functional regression tests for the parser',
            'paths' => ['Tests/Functional/Parser/ScssParserTest.php'],
            'changeType' => 'test',
        ]);

        self::assertSame(Scope::Uncertain, Scope::from($result->data['scope']));
        self::assertNotSame([], $result->data['testSuites']);
        self::assertStringContainsString(Scope::UNCERTAIN_NOTICE, $result->text);
    }

    #[Decision('D-SCO-015')]
    #[Requirement('R-SCO-002')]
    #[Test]
    public function anExtensionTestBriefRoutesTheHarnessTheExtensionHas(): void
    {
        // The route is what the caller spends its next call on, so the first
        // entry decides. typo3_test_run_guide declines for a path outside the
        // core, and the intent's own line for it named no core artefact, so the
        // check that drops core-only routing read nothing (`D-SCO-015`).
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Add functional regression tests for the SCSS parser',
            'paths' => ['Classes/Parser/AbstractParser.php'],
            'changeType' => 'test',
        ]);

        self::assertTrue(Scope::from($result->data['scope'])->isOutsideTheCore());
        $first = $result->data['nextTools'][0];
        self::assertSame('typo3_hint_lookup', $first['tool']);
        self::assertStringContainsString('project-extension-tests', $first['when']);
        self::assertNotContains(
            'typo3_test_run_guide',
            array_column($result->data['nextTools'], 'tool'),
            'the suites it answers with are core commands, so it has nothing for these paths'
        );
        // The rendered rules carry a route of their own, so the whole answer is
        // read rather than the list.
        self::assertStringNotContainsString('typo3_test_run_guide', $result->text);
    }

    #[Decision('D-SCO-015')]
    #[Requirement('R-SCO-002')]
    #[Test]
    public function aRuleSectionOutsideTheCoreSaysWhereAConventionIsBound(): void
    {
        // Every rendered section opens with what a range is carried by where it
        // is not the section's own, and half of that line was a core command.
        $result = Registry::call('typo3_rule_lookup', [
            'query' => 'how do I write tests for my site package extension',
        ]);

        self::assertTrue(Scope::from($result->data['scope'])->isOutsideTheCore());
        self::assertStringContainsString('typo3_hint_lookup with targetVersion for a convention.', $result->text);
        self::assertStringNotContainsString('typo3_test_run_guide', $result->text);
    }

    #[Decision('D-SCO-015')]
    #[Requirement('R-SCO-002')]
    #[Test]
    public function anExtensionDeprecationIsCommittedUnderItsOwnRepositorysConvention(): void
    {
        // The core workflow demands a Forge issue and a Releases: trailer, and
        // the deprecation intent routed it by name. Dropping that line has to
        // leave the tool behind rather than take it: what carries the caller is
        // the generic candidate for the same tool, in the project wording.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Deprecate the old renderer method and keep a fallback',
            'paths' => ['Classes/Parser/AbstractParser.php'],
            'changeType' => 'feature',
        ]);

        $when = array_column($result->data['nextTools'], 'when', 'tool');
        self::assertArrayHasKey('typo3_commit_message_guide', $when);
        self::assertStringNotContainsString('workflow="core"', $when['typo3_commit_message_guide']);
    }

    #[Decision('D-SCO-015')]
    #[Requirement('R-SCO-002')]
    #[Test]
    public function anExtensionChangelogTaskIsRoutedAwayFromTheCoresOwnProcedure(): void
    {
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Write a changelog entry for the feature I just added',
            'paths' => ['Classes/Parser/AbstractParser.php'],
            'changeType' => 'feature',
        ]);

        self::assertSame(['typo3-extension-documentation'], $result->data['skills']);

        // Both of its routing lines are the core's: the procedure below
        // typo3/sysext/core/Documentation/Changelog/ and the hint whose own
        // declared scope is core.
        $when = array_column($result->data['nextTools'], 'when', 'tool');
        self::assertStringNotContainsString('core/contribution/changelog', $when['typo3_rule_lookup'] ?? '');
        self::assertStringNotContainsString('documentation-changelog', $when['typo3_hint_lookup'] ?? '');
    }

    #[Requirement('R-SCO-002')]
    #[Test]
    public function noRunTestsCommandIsHandedToARepositoryThatHasNoRunTests(): void
    {
        // Every suite this guide knows is a Build/Scripts/runTests.sh
        // invocation, and that script is part of the core repository. Handed to
        // a site package, every command in the answer is unrunnable — and it
        // looks copy-pasteable, which is worse than declining.
        $result = Registry::call('typo3_test_run_guide', [
            'query' => 'how do I test my sitepackage',
            'paths' => ['packages/my_sitepackage/Classes/Command/SeedCommand.php'],
        ]);

        self::assertSame(
            [Scope::Extension],
            array_values(array_unique(array_column($result->data['scopes'], 'scope'))),
        );
        self::assertSame([], $result->data['suites']);
        // The script is named in the sentence that explains why nothing is
        // returned; what must not appear is a command shaped to be run.
        self::assertStringNotContainsString('CI=true', $result->text);
        self::assertStringStartsWith('This reads as work outside the TYPO3 core', $result->text);
    }

    /**
     * `D-KNW-008`: the tooling row is crossed by routing, so the tool that owns
     * a cell names the ids of the others when the caller is not in its column.
     * `phpstan` and `cgl` are suites of this guide, which makes static analysis
     * one of the things an extension arrives here asking for — and until this
     * sentence named it, the decline sent every one of them to the two testing
     * cells, neither of which answers what goes into a phpstan.neon.
     */
    /** @param array<int, string> $paths */
    #[Decision('D-KNW-008')]
    #[Test]
    #[DataProvider('theWaysAnExtensionAsksAboutStaticAnalysis')]
    public function aStaticAnalysisQuestionFromOutsideTheCoreIsSentToItsOwnCell(array $paths): void
    {
        $result = Registry::call('typo3_test_run_guide', [
            'query' => 'set up phpstan for our extension',
            'paths' => $paths,
        ]);

        self::assertStringContainsString('id=extension-static-analysis', $result->text);
        self::assertStringContainsString('id=project-extension-tests', $result->text);
        self::assertStringContainsString('id=browser-tests', $result->text);

        // And the id it hands over is one the corpus has, which is the half a
        // sentence in this file cannot state for itself.
        self::assertNotNull(Hints::byId('extension-static-analysis'));
    }

    /** @return array<string, array{0: array<int, string>}> */
    public static function theWaysAnExtensionAsksAboutStaticAnalysis(): array
    {
        return [
            'the extension on its own' => [
                ['packages/my_sitepackage/Classes/Controller/EventController.php'],
            ],
            'the extension beside a core path, which used to send it to the core cells' => [
                [
                    'packages/my_sitepackage/Classes/Controller/EventController.php',
                    'typo3/sysext/core/Classes/Core/Bootstrap.php',
                ],
            ],
        ];
    }

    #[Requirement('R-ANS-007')]
    #[Test]
    public function aRuleQueryIsPointedAtTheHintCorpusItBelongsIn(): void
    {
        // Which of the two corpora holds a subject is this server's business:
        // site sets are an hint, the Gerrit workflow is prose, and
        // the question is phrased the same way either way.
        $result = Registry::call('typo3_rule_lookup', ['query' => 'site set settings definitions']);

        self::assertContains('site-sets', array_column($result->data['alsoInHints'], 'id'));
        self::assertStringContainsString('typo3_hint_lookup', $result->text);
    }

    /**
     * The prose corpus is the contribution process and the commit conventions
     * at once, and only the first half stops at the core repository. Dropping
     * the whole tool — which is what the project profile does — takes the
     * second half with it, and a caller writing a commit message in their own
     * repository needs exactly that.
     */
    #[Test]
    public function aRuleAnswerKeepsWhatTransfersAndWithholdsWhatDoesNot(): void
    {
        $result = Registry::call('typo3_rule_lookup', ['query' => 'commit message sitepackage']);

        self::assertTrue(Scope::from($result->data['scope'])->isOutsideTheCore());
        // The commit conventions were this corpus's transferable half until the
        // document was read: it is the core's process throughout — Resolves,
        // Change-Id, the changelog files — and it was labelled as holding
        // everywhere. Outside the core the prose now answers nothing here, and
        // what a project caller needs is a tool rather than a second copy of
        // this page.
        self::assertSame([], $result->data['matches']);
        self::assertStringContainsString('typo3_commit_message_guide', $result->text);
        // That a miss on the boundary is named is held by
        // whatARuleAnswerWithheldIsNamed, on a query that
        // still reaches both halves. This one no longer does: since D-ANS-037
        // put the document title among the searched fields, a query naming the
        // commit conventions is answered by that document's own sections
        // before a core-only one is a candidate.
        foreach ($result->data['withheldDocuments'] as $document) {
            self::assertTrue(
                Documents::isCoreOnly($document['id']),
                $document['id'] . ' was withheld and is not the core repository\'s own',
            );
        }
    }

    /**
     * A thinner answer that does not say what it left out reads as "nobody
     * wrote this down", which is the one thing it does not mean.
     *
     * The query is the one that clears the coverage floor with room —
     * `Code Style` covers 0.612 of it against a floor of 0.5 — and it is chosen
     * that way rather than for its wording. `review readiness for my site
     * package` stood here at 0.508, and the four sections `R-KNW-057` added
     * about the Gerrit push took it to 0.462 without touching the section it
     * depends on: coverage is a share of the query's weight, and a term is
     * weighed by how few sections carry it, so anything added anywhere moves it
     * — `D-ANS-040`.
     */
    #[Decision('D-ANS-040')]
    #[Test]
    public function whatARuleAnswerWithheldIsNamed(): void
    {
        $outside = Registry::call('typo3_rule_lookup', ['query' => 'code style rules for my site package']);

        self::assertContains('core/contribution/rules', array_column($outside->data['withheldDocuments'], 'id'));
        self::assertStringStartsWith('This reads as work outside the TYPO3 core', $outside->text);
        // The resource stays reachable: withholding is about what an answer
        // volunteers, not about what a caller may deliberately read.
        self::assertStringContainsString('typo3://guides/', $outside->text);
    }

    #[Test]
    public function insideTheCoreARuleAnswerWithholdsNothing(): void
    {
        $inside = Registry::call('typo3_rule_lookup', ['query' => 'review readiness for a typo3/sysext/core patch']);

        self::assertFalse(Scope::from($inside->data['scope'])->isOutsideTheCore());
        self::assertSame([], $inside->data['withheldDocuments']);
        self::assertContains('core/contribution/rules', array_column($inside->data['matches'], 'documentId'));
    }

    /**
     * Which of the two a document is comes from the scope rather than from a
     * second list, so a document the coverage does not announce has no scope to
     * read — and is served as a resource and searched by the rule lookup all
     * the same. `core/contribution/sources` was exactly that — `D-KNW-095`,
     * `D-VER-007`.
     */
    #[Decision('D-KNW-061')]
    #[Requirement('R-ANS-022')]
    #[Decision('D-KNW-095')]
    #[Decision('D-VER-007')]
    #[Test]
    public function everyKnowledgeDocumentIsAnnouncedByTheScope(): void
    {
        $named = [];
        foreach (Coverage::read()['covers'] as $entry) {
            if (preg_match_all('#typo3://guides/([a-z0-9/-]+)#', $entry['source'], $matches) === 0) {
                continue;
            }
            foreach ($matches[1] as $id) {
                $named[$id] = $entry['scope'];
            }
        }

        foreach (Documents::documents() as $document) {
            self::assertArrayHasKey(
                $document['id'],
                $named,
                $document['id'] . ' is served and searched, and no covered topic names it',
            );
            // Four of the five cases are legitimate for a document; `uncertain`
            // is what a path lands in when nothing placed it, and a topic that
            // cannot say who its document answers for has not been thought
            // through. What the scope decides is the sentence the resource is
            // offered under, and only one of them may claim the document holds
            // everywhere — that claim was the default until a document answered
            // for a package alone.
            $scope = $named[$document['id']];
            self::assertNotSame(Scope::Uncertain, $scope);
            self::assertSame(
                $scope === Scope::Any,
                str_contains(
                    (string) Documents::description($document['id']),
                    'core contribution, extension development and site work alike',
                ),
                $document['id'] . ' is offered under a sentence its scope does not support',
            );
        }
    }

    /**
     * The same obligation for the second resource family, and it carries more
     * here than it does for a document: what a skill is worth outside the core
     * is read off the coverage and nowhere else, so a published skill no topic
     * names is offered as core-only — to every extension author who picks it.
     */
    #[Requirement('R-ANS-022')]
    #[Test]
    public function everyPublishedSkillIsAnnouncedByTheScope(): void
    {
        $named = [];
        foreach (Coverage::read()['covers'] as $entry) {
            if (preg_match_all('#typo3://skill/([a-z0-9-]+)/SKILL\.md#', $entry['source'], $matches) === 0) {
                continue;
            }
            foreach ($matches[1] as $id) {
                $named[$id] = $entry['scope'];
            }
        }

        foreach (Installer::skills() as $skill) {
            self::assertArrayHasKey(
                $skill,
                $named,
                $skill . ' is published and served, and no covered topic names it',
            );
            self::assertContains($named[$skill], [Scope::Core, Scope::Any]);
        }
        self::assertSame(
            [],
            array_diff(array_keys($named), Installer::skills()),
            'a covered topic names a skill this server does not publish',
        );
    }

    /**
     * The scripts stay stored as the core's own and the crossing happens where
     * the answer is composed, so a project asking for them is answered with
     * none — `D-KNW-008`.
     */
    #[Requirement('R-SCO-005')]
    #[Decision('D-KNW-008')]
    #[Test]
    public function noCoreScriptIsHandedToARepositoryThatDoesNotHaveIt(): void
    {
        $result = Registry::call('typo3_script_lookup', [
            'task' => 'run the unit tests of my site package extension',
        ]);

        self::assertTrue(Scope::from($result->data['scope'])->isOutsideTheCore());
        self::assertSame([], $result->data['matches']);
        self::assertStringNotContainsString('CI=true', $result->text);
    }

    #[Requirement('R-SCO-005')]
    #[Test]
    public function aScriptAnswerSaysWhichRepositoryItsCommandsRunIn(): void
    {
        // Nothing in this query says either way, so the commands are offered
        // under their condition rather than stated as the answer.
        $unstated = Registry::call('typo3_script_lookup', ['task' => 'php-cs-fixer and phpstan']);
        self::assertFalse(Scope::from($unstated->data['scope'])->isOutsideTheCore());
        self::assertNotSame([], $unstated->data['matches']);
        self::assertStringContainsString('run in a TYPO3 core checkout', $unstated->text);

        $stated = Registry::call('typo3_script_lookup', ['task' => 'unit tests for a typo3/sysext/core patch']);
        self::assertStringNotContainsString('run in a TYPO3 core checkout', $stated->text);
    }

    #[Requirement('R-SCO-002')]
    #[Test]
    public function aHintKeepsItsAdviceOutsideTheCoreAndLosesItsCoreChecks(): void
    {
        // The conventions transfer — the commands do not.
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'add a console command to my site package',
            'paths' => ['packages/my_sitepackage/Classes/Command/SeedCommand.php'],
        ]);

        self::assertSame(
            [Scope::Extension],
            array_values(array_unique(array_column($result->data['scopes'], 'scope'))),
        );
        self::assertNotSame([], $result->data['hints']);
        self::assertStringNotContainsString('CI=true', $result->text);
    }

    /**
     * D-SCO-004's named loss, run: the contributor is inside the core, on a
     * sysext path, and the two backend UI sections go anyway. That is the
     * decision working rather than failing — the corpus behind those two is the
     * backend interface's Sass and its --typo3-* tokens, which
     * fluid_styled_content does not render — so what the case actually tests is
     * that the audience does not enter into it. `Scope::isOutsideCore` was the
     * rejected lever, and a core path is where a reinstated one would show —
     * `D-KNW-033`.
     */
    #[Requirement('R-SCO-004')]
    #[Decision('D-KNW-033')]
    #[Test]
    public function aCoreContributorOnFrontendLosesTheBackendUiSections(): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'Core contribution to the frontend rendering of fluid_styled_content: the CSS and the '
                . 'TypeScript conventions for its stylesheet and its JavaScript module',
            'paths' => ['typo3/sysext/fluid_styled_content/Resources/Public/Css/ContentElements.css'],
        ]);

        self::assertSame(
            [Hints::CATEGORY_TYPESCRIPT, Hints::CATEGORY_CSS],
            $result->data['withheldCategories'],
        );
        foreach ($result->data['hints'] as $hint) {
            self::assertNotContains(
                $hint['category'],
                [Hints::CATEGORY_CSS, Hints::CATEGORY_TYPESCRIPT],
                $hint['id'] . ' reached a backend UI section on a frontend task',
            );
        }
    }

    /**
     * The other half, and the one that decides whether the notice is an escape
     * or an apology. Withholding is only defensible while the caller it was
     * wrong for can undo it, so the words that undo it are asserted in the
     * notice and then used: a notice naming an escape nobody can act on reads
     * to the caller exactly like a refusal.
     */
    #[Requirement('R-SCO-004')]
    #[Test]
    public function theNoticeNamesTheWordsThatBringTheBackendUiSectionsBack(): void
    {
        $task = 'Core contribution to the frontend rendering of fluid_styled_content: the CSS and the TypeScript '
            . 'conventions for its stylesheet and its JavaScript module';
        // The clause, not the words: "backend" is in the sentence saying what
        // was withheld and why, so a bare substring passes on the apology.
        $withheld = Registry::call('typo3_hint_lookup', ['task' => $task]);
        self::assertStringContainsString('Name the backend in the task', $withheld->text);
        self::assertStringContainsString('or the styleguide', $withheld->text);

        // The two escapes the notice above named, checked against the answer
        // that notice came from — one scenario rather than two cases.
        foreach ([
            'backend' => $task . ', and the backend module that configures it',
            'styleguide' => $task . ', and the styleguide demo for the component',
        ] as $escape => $escaped) {
            $result = Registry::call('typo3_hint_lookup', ['task' => $escaped]);

            self::assertSame([], $result->data['withheldCategories'], $escape . ' withheld a section anyway');
            // Either of the two the notice named, because which one ranks into
            // the answer is the matcher's business and the escape's promise is
            // that they are candidates again.
            self::assertNotSame(
                [],
                array_intersect(
                    [Hints::CATEGORY_CSS, Hints::CATEGORY_TYPESCRIPT],
                    array_column($result->data['hints'], 'category'),
                ),
                $escape . ' brought nothing back',
            );
        }
    }

    #[Requirement('R-ANS-002')]
    #[Requirement('R-DIS-008')]
    #[Test]
    public function theInstallationDiagnosticIsData(): void
    {
        Instance::discoverFrom(sys_get_temp_dir());
        Typo3Cli::forget();

        try {
            $installation = Registry::call('typo3_server_scope', [])->data['installation'];
        } finally {
            Instance::discoverFrom(null);
            Typo3Cli::forget();
        }

        // A client that renders structuredContent and drops the text block saw
        // none of this, and read five empty results as five empty registries.
        self::assertFalse($installation['found']);
        self::assertNotSame([], $installation['searched']);
        self::assertFalse($installation['console']['reachable']);
        self::assertNotSame('', $installation['console']['reason']);
        self::assertSame(Instance::ROOT_VARIABLE, $installation['settings']['root']);
        self::assertSame(Typo3Cli::CONSOLE_VARIABLE, $installation['settings']['console']);
    }

    #[Requirement('R-ANS-002')]
    #[Test]
    public function anUnanswerableLookupCarriesItsReasonInTheData(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();

        $result = Registry::call('typo3_label_lookup', ['query' => 'save']);

        self::assertArrayNotHasKey('answeredBy', $result->data);
        self::assertNotSame('', $result->data['unsupported']['reason']);
        // Not an empty list of labels: an empty list is what an installation
        // that has none answers with, and none was asked.
        self::assertArrayNotHasKey('labels', $result->data);
    }

    #[Requirement('R-ANS-001')]
    #[Test]
    public function anUnconsultedConfigurationPathIsNotReportedAsAbsent(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();

        $result = Registry::call('typo3_configuration_lookup', ['path' => 'SYS/fluid']);

        // found: false says the installation has no value there, which is a
        // statement about an installation nothing asked.
        self::assertArrayNotHasKey('found', $result->data);
        self::assertSame('no-installation', $result->data['unsupported']['cause']);
    }

    #[Requirement('R-SCO-006')]
    #[Test]
    public function everyCoveredTopicSaysWhatItIsWorthOutsideTheCore(): void
    {
        // The boundary runs through the middle of this server: the installation
        // half is a property of TYPO3 installations, the conventions transfer,
        // and only the contribution process is core-only. A caller that has to
        // work that out per tool trusts either all of it or none of it.
        foreach (Coverage::read()['covers'] as $entry) {
            self::assertContains($entry['scope'], Scope::ofKnowledge(), $entry['topic']);
        }
    }

    /**
     * Routing a question shape at the tool that answers it is worth nothing if
     * the name has moved, and the scope is where a caller reads the name —
     * `D-ANS-010`.
     */
    #[Requirement('R-DOC-001')]
    #[Decision('D-ANS-010')]
    #[Test]
    public function everyToolNamedInTheScopeExists(): void
    {
        $known = $this->toolNames();
        $scope = Coverage::read();

        $mentioned = [];
        foreach ($scope['covers'] as $entry) {
            $mentioned = array_merge($mentioned, $entry['tools']);
        }
        foreach (array_merge($scope['routing'], $scope['doesNotCover'], $scope['checkoutDiscovery']) as $entry) {
            preg_match_all('/typo3_[a-z_]+/', implode(' ', $entry), $matches);
            $mentioned = array_merge($mentioned, $matches[0]);
        }

        foreach (array_unique($mentioned) as $tool) {
            self::assertContains($tool, $known, $tool . ' is named in the scope but not registered');
        }
    }

    #[Requirement('R-DOC-001')]
    #[Test]
    public function everyToolIsReachableThroughTheScope(): void
    {
        $mentioned = [];
        foreach (Coverage::read()['covers'] as $entry) {
            $mentioned = array_merge($mentioned, $entry['tools']);
        }

        foreach ($this->toolNames() as $tool) {
            if (in_array($tool, ['typo3_server_scope', 'typo3_feedback_record', 'typo3_feedback_list'], true)) {
                continue;
            }
            self::assertContains($tool, $mentioned, $tool . ' is registered but no covered topic points at it');
        }
    }

    /**
     * `D-ANS-088`. The narrow form of the orientation answer, in the call
     * `feedback/2026-08-17-205904` made and paid 11,000 tokens for.
     *
     * What that session was after is the binary question at the top of a build:
     * is there an installation, and does its console answer. It got the whole
     * catalogue, and reported the size as the cost rather than the answer as
     * wrong.
     */
    #[Decision('D-ANS-088')]
    #[Test]
    public function aCallThatNamesOneSectionIsAnsweredWithThatSectionAlone(): void
    {
        Instance::discoverFrom(null);

        $narrow = Registry::call('typo3_server_scope', ['sections' => ['installation']]);

        self::assertArrayHasKey('installation', $narrow->data);
        foreach (['covers', 'doesNotCover', 'checkoutDiscovery', 'routing', 'versions', 'answersFrom'] as $section) {
            // Absent rather than empty: an empty covers reads as a server that
            // covers nothing, and a caller cannot tell that from a narrowing.
            self::assertArrayNotHasKey($section, $narrow->data, $section . ' came back on a call that did not ask');
        }

        // And it is legibly a part rather than the whole, in both halves.
        self::assertSame(
            ['covers', 'doesNotCover', 'checkoutDiscovery', 'routing', 'versions', 'answersFrom'],
            array_column($narrow->data['withheld'], 'section'),
        );
        self::assertStringContainsString('Left out, because this call named sections', $narrow->text);

        // The whole answer stays the whole answer, and says it withheld nothing.
        $whole = Registry::call('typo3_server_scope', []);
        self::assertSame([], $whole->data['withheld']);
        self::assertLessThan(strlen($whole->text) / 10, strlen($narrow->text));
    }

    /**
     * What no selection may take away.
     *
     * `R-SCO-009` keeps this tool out of the exclusion list because it is what
     * tells a client why its tool list is shorter than the documentation says.
     * A selection that could hide the same thing would be that hole reopened
     * through the arguments, so the exclusion report is not one of the sections
     * — `D-ANS-088`.
     */
    #[Decision('D-ANS-088')]
    #[Test]
    public function noSelectionHidesWhatTheCallerExcludedOrWhatThisServerIsFor(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_icon_lookup');

        $narrow = Registry::call('typo3_server_scope', ['sections' => ['versions']]);

        self::assertSame(['typo3_icon_lookup'], $narrow->data['excludedTools']['names']);
        self::assertStringContainsString('typo3_icon_lookup', $narrow->text);
        // The purpose and the statement every client is handed at initialize
        // time: the smallest complete account of the boundary there is.
        self::assertNotSame('', $narrow->data['purpose']);
        self::assertStringContainsString('What to call for what:', $narrow->data['instructions']);
        // And the one mitigation for a corpus nothing else can answer for.
        self::assertStringContainsString('in English', $narrow->text);
    }

    /**
     * Naming none is the whole answer, which is the default because a caller
     * that does not know what this server covers cannot name the part it wants
     * — `D-ANS-087` — `D-ANS-088`.
     */
    #[Decision('D-ANS-088')]
    #[Test]
    public function namingNoSectionAnswersEverythingTheToolHas(): void
    {
        $whole = Registry::call('typo3_server_scope', []);
        $named = Registry::call('typo3_server_scope', ['sections' => [
            'covers', 'doesNotCover', 'checkoutDiscovery', 'routing', 'versions', 'answersFrom', 'installation',
        ]]);

        self::assertSame($whole->text, $named->text);
        self::assertSame(array_keys($whole->data), array_keys($named->data));
    }

    /** @return array<int, string> */
    private function toolNames(): array
    {
        // The feedback tools only exist in a standalone checkout, but the scope
        // may name them either way.
        return array_merge(
            array_column(Registry::definitions(), 'name'),
            ['typo3_feedback_record', 'typo3_feedback_list'],
        );
    }
}
