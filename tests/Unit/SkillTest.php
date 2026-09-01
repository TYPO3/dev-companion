<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use TYPO3\DevCompanion\Installation\Icons;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Knowledge\Hints;
use TYPO3\DevCompanion\Knowledge\TaskIntents;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Server\Installer;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Tool\TaskGuide;
use TYPO3\DevCompanion\Upkeep\Fixture;
use TYPO3\DevCompanion\Upkeep\Scenarios;

#[Requirement('R-DIS-014')]
#[Requirement('R-SKL-001')]
#[Requirement('R-SKL-002')]
final class SkillTest extends TestCase
{
    /**
     * What each skill adds to the base, in the order it adds it. The four calls
     * the base already fixes are deliberately not repeated here: a skill that
     * restates them is a skill that can drift from them, and five hand-written
     * copies of one order is what the base replaced.
     */
    private const ROUTING_SKILLS = [
        'typo3-backend-module-development' => [
            'typo3_server_scope',
            'typo3_backend_module_lookup',
            'typo3_icon_lookup',
            'typo3_label_lookup',
            'typo3_translation_domain_lookup',
            'typo3_component_lookup',
            'typo3_documentation_lookup',
            'typo3_commit_message_guide',
        ],
        'typo3-content-element-development' => [
            'typo3_documentation_lookup',
            'typo3_label_lookup',
            'typo3_icon_lookup',
            'typo3_component_lookup',
            'typo3_rule_lookup',
            'typo3_commit_message_guide',
        ],
        'typo3-extension-testing' => [
            'typo3_documentation_lookup',
            'typo3_commit_message_guide',
        ],
        // What the build's output owes the backend, in the order it is read:
        // the module contract the manual states, then the core surface the
        // output borrows — the class first, because it is answered per declared
        // major, the identifier after it, which is answered for the installed
        // one alone, and the changelog last, for the surface the change drops
        // rather than takes on.
        'typo3-extension-asset-build' => [
            'typo3_documentation_lookup',
            'typo3_component_lookup',
            'typo3_icon_lookup',
            'typo3_changelog_lookup',
            'typo3_commit_message_guide',
        ],
        // `typo3_server_scope` is not among them: the step that named it says
        // the call is discharged by the base's `typo3_project_describe`
        // (`D-ANS-083`), which `DISCHARGED_TOOLS` below is where it is recorded.
        'typo3-development-installation' => [
            'typo3_rule_lookup',
            'typo3_documentation_lookup',
            'typo3_configuration_lookup',
            'typo3_commit_message_guide',
        ],
        // The review server comes first because the patch is established from
        // it: the changed paths every later call takes as its argument, the
        // target branch, the commit message and the issue it names all come
        // back from that one call, and the review reached them by fetching the
        // change into a checkout until `D-ANS-112`.
        'typo3-core-patch-review' => [
            'typo3_gerrit_lookup',
            'typo3_hint_lookup',
            'typo3_forge_lookup',
            'typo3_rule_lookup',
            'typo3_changelog_lookup',
            'typo3_documentation_lookup',
            'typo3_test_run_guide',
            'typo3_script_lookup',
            'typo3_commit_message_guide',
        ],
        'typo3-core-issue-triage' => [
            'typo3_forge_lookup',
            'typo3_gerrit_lookup',
            'typo3_changelog_lookup',
            'typo3_test_run_guide',
            'typo3_script_lookup',
            'typo3_rule_lookup',
        ],
        'typo3-core-patch-checkout' => [
            'typo3_gerrit_lookup',
            'typo3_rule_lookup',
            'typo3_test_run_guide',
        ],
        'typo3-core-patch-development' => [
            'typo3_rule_lookup',
            'typo3_forge_lookup',
            'typo3_gerrit_lookup',
            'typo3_test_run_guide',
            'typo3_hint_lookup',
            'typo3_script_lookup',
            'typo3_commit_message_guide',
        ],
        // The commit is the last of them because the audit half routes to
        // neither: what is left for this workflow to route to once the owners
        // have their items is the message each commit carries — `D-SKL-016`.
        // The rule lookup stands after the two that answer a surface, because
        // what it reads is a whole procedure and only where the constraint the
        // base reported names a major the installation does not supply
        // (`D-GUI-012`).
        'typo3-extension-health' => [
            'typo3_hint_lookup',
            'typo3_documentation_lookup',
            'typo3_rule_lookup',
            'typo3_commit_message_guide',
        ],
        // The order the research behind `D-SKL-063` put the five checks in: the
        // conventions for the paths the diff touches, then the two whole
        // procedures that settle a declared major nobody can run here, then
        // what the changelog and the manual add to that, then the extension a
        // proposed alternative would pull in, and the message last.
        'typo3-extension-patch-review' => [
            'typo3_hint_lookup',
            'typo3_rule_lookup',
            'typo3_changelog_lookup',
            'typo3_documentation_lookup',
            'typo3_system_extension_lookup',
            'typo3_commit_message_guide',
        ],
        'typo3-extension-documentation' => [
            'typo3_documentation_lookup',
            'typo3_label_lookup',
            'typo3_translation_domain_lookup',
            'typo3_commit_message_guide',
        ],
        'typo3-extension-upgrade' => [
            'typo3_changelog_lookup',
            'typo3_system_extension_lookup',
            'typo3_hint_lookup',
            'typo3_documentation_lookup',
            'typo3_commit_message_guide',
        ],
        'typo3-distribution-content' => [
            'typo3_documentation_lookup',
            'typo3_configuration_lookup',
            'typo3_commit_message_guide',
        ],
    ];

    /**
     * What a skill names in order *not* to call it, and the words it is written
     * in. A routing is asserted by finding the tool's name in the body, so a
     * sentence saying the call is already answered satisfies that assertion
     * while telling the caller the opposite — which is why the discharge is a
     * construct rather than a wording, and why a routing is the first mention
     * outside one (`D-SKL-055`).
     */
    private const DISCHARGE = '` is discharged by';

    /**
     * Which tool each skill discharges, kept beside the routings because that
     * is the list a mention cannot be on twice: a tool here is not among that
     * skill's routings above, and is named nowhere else in its body.
     *
     * @var array<string, list<string>>
     */
    private const DISCHARGED_TOOLS = [
        'typo3-development-installation' => ['typo3_server_scope'],
        // The constraint the package declares is what every version answer in
        // that review turns on, and it comes back with the base's first step
        // rather than from a call the skill makes.
        'typo3-extension-patch-review' => ['typo3_project_describe'],
        // The manifests, the commands each one declares and the Node they are
        // run on come back with the base's first step, and reading package.json
        // by hand is the first act of a session that does not know it.
        'typo3-extension-asset-build' => ['typo3_project_describe'],
    ];

    /**
     * The skills whose workflow ends in a change to a repository that is not the
     * core's, read off each body — `D-SKL-014`. The two core skills are not among
     * them: both name the guide already and both commit in the core, where the
     * argument's default is the right one. `typo3-extension-patch-review` is not
     * either, because it is pure analysis and a commit line in a review's answer
     * is what `R-GUI-006` exists to keep out of one. Its own half of that fork
     * is the one skill that carries both: `typo3-extension-health` reports
     * before it changes anything, and the commit belongs to the second half.
     */
    private const COMMITTING_SKILLS = [
        'typo3-backend-module-development',
        'typo3-extension-asset-build',
        'typo3-content-element-development',
        'typo3-development-installation',
        'typo3-distribution-content',
        'typo3-extension-documentation',
        'typo3-extension-health',
        'typo3-extension-testing',
        'typo3-extension-upgrade',
    ];

    /**
     * The skills whose product is a report somebody carries elsewhere, read off
     * each body on 2026-08-14 — `D-SKL-042`. Each of the three specifies what
     * the report contains and in which order, and the skill is what makes it
     * long enough for the form to matter.
     *
     * Which those are is not readable off a file, like the sides a description
     * names: `typo3-core-patch-checkout` ends in a checkout and
     * `typo3-extension-upgrade` in a change, and neither closes on a document.
     */
    private const REPORTING_SKILLS = [
        'typo3-core-patch-review',
        'typo3-extension-health',
        'typo3-core-issue-triage',
        // The fourth closes on a document for the same reason the first does,
        // and the place it is carried into is a pull request thread.
        'typo3-extension-patch-review',
    ];

    #[Decision('D-SKL-001')]
    #[Decision('D-SKL-083')]
    #[Decision('D-SKL-084')]
    #[Requirement('R-SKL-005')]
    #[Test]
    public function theBaseFixesTheOrderEveryTaskStartsIn(): void
    {
        // Three REVIEW-01 runs measured what an order that is merely stated is
        // worth. The third read its skill's checklist in the first twenty
        // seconds, then listed the file tree and spent five minutes reading it
        // before calling task_guide or a single conventions lookup. Whatever a
        // skill leaves after the reading is what the reading swallows, so the
        // four owning calls come first and the checkout comes after all of them.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $position = -1;
        foreach (['typo3_project_describe', 'typo3_extension_describe', 'typo3_task_guide', 'typo3_hint_lookup'] as $tool) {
            $next = strpos($base, $tool);
            self::assertNotFalse($next, $tool . ' is not part of the base');
            self::assertGreaterThan($position, $next, $tool . ' is stated out of order in the base');
            $position = $next;
        }
        self::assertGreaterThan(
            $position,
            strpos($base, '**Then** read the checkout'),
            'the base sends the session into the checkout before its own calls',
        );

        // Step 1 says what its answer ends with, because a session that follows
        // the order literally reads the step before it reads the payload — and
        // four sessions in a week finished without learning the guides exist
        // (`feedback/2026-08-07-233512`).
        self::assertStringContainsString(
            'the whole procedures this server carries, as ids',
            self::flat($base),
        );

        // The near miss, not the omission: a runtime lookup answers what is
        // registered, never whether it is right.
        self::assertStringContainsString(
            'confirmed by its own runtime lookup can still break every rule that governs it',
            self::flat($base),
        );
        self::assertStringContainsString(
            'settled into the opposite of a rule is a finding, not a local style',
            self::flat($base),
        );

        // And the direction that sentence invites if it stands alone. REVIEW-02
        // reported five of six priorities against mechanisms the package ships
        // on purpose — the compile step a setting drives, the vendored copy that
        // makes a non-Composer install work, the download that keeps a font on
        // the site's own host.
        self::assertMatchesRegularExpression(
            '/A mechanism that costs something is not a defect for costing it/',
            $base,
        );
        self::assertMatchesRegularExpression(
            '/trade-off to name with its cost/',
            $base,
        );
        // And what the answer owes about its own evidence. Three recorded
        // REVIEW-02 runs in two repositories ran not one project-owned command
        // — ten were offered in the first checkout, five in the second — and
        // said nothing about it, so findings read out of a CI file stood beside
        // findings with a verified path and line at the same confidence.
        // What a client that defers tool schemas answers a search for the bare
        // name with, which is what an absent server answers too — and the
        // bullet under this one turns that into an absence (`D-SKL-084`).
        self::assertStringContainsString(
            'Look for the qualified form before reading an empty result as an answer about the server',
            self::flat($base),
        );

        // Which side of the sweep's exemption a test file falls on. The session
        // that wrote one calling FunctionalTestCase and GeneralUtility::writeFile
        // read itself as exempt and skipped in silence, which the rule already
        // decided against and no illustration said (`D-SKL-083`).
        self::assertStringContainsString(
            'A test file is one of those wherever it sits',
            self::flat($base),
        );
        self::assertStringContainsString('What a finding rests on is part of the finding', $base);
        self::assertStringContainsString(
            'a file that was read, at its path and its line; a command that was run, with what it printed; a mechanism traced into an installed package',
            self::flat($base),
        );
        // And what it owes to the commands the repository already declares.
        // The same three runs were told not to change files and read that as
        // permission to run nothing, while two of the checks on offer change
        // nothing by declaration and would have settled two of the findings.
        self::assertMatchesRegularExpression(
            '/Where one of the project\'s own commands would settle it, run\s+it/',
            $base,
        );
        self::assertStringContainsString(
            'a check reports and hands the code back as it was, so even a task told not to change files runs it',
            self::flat($base),
        );
        self::assertStringContainsString(
            'an unknown — a test suite, a shell pipeline, a console command — is named in the answer as evidence that is available rather than run unasked',
            self::flat($base),
        );

        // One hop, like every other reference: the base is read, not followed
        // onward.
        self::assertStringNotContainsString('(references/', $base);
    }

    /**
     * Every call the base fixes, held to what it sends the session to read out
     * of the answer.
     *
     * The rest of the authoring contract is read off the file, which makes it a
     * proxy: the wording stays where it is while the answer behind it moves. A
     * tool that stopped reporting one of the keys the base sends a session to
     * fails nothing — the skill still names it, and the session is sent to a key
     * that is not there. So the four are called, in the order the base fixes
     * them and threaded the way a session reads them, and the two that need an
     * installation are asked of the one this repository writes (`D-SKL-025`).
     */
    #[Decision('D-SKL-025')]
    #[Test]
    public function everyCallTheBaseFixesAnswersWithWhatItSendsTheSessionToRead(): void
    {
        $base = self::flat((string) file_get_contents(Paths::root() . '/skills/base.md'));

        Instance::discoverFrom(Fixture::write());
        Typo3Cli::forget();
        Typo3Runtime::forget();
        Icons::forget();

        // Step 1: the installation, its two versions, the extensions that are
        // the project's own, its sites, and the commands it declares.
        $project = Registry::call('typo3_project_describe', [])->data;
        self::assertArrayNotHasKey('unsupported', $project, 'the written installation could not be described');
        foreach (['typo3Version', 'phpConstraint', 'extensions', 'sites', 'commands', 'guides'] as $key) {
            self::assertArrayHasKey($key, $project, 'step 1 sends the session to read ' . $key);
        }

        // The marking a task told not to change files reads before it runs
        // anything. The three words are the base's own, so what is held is that
        // every command carries one of them: a fourth marking, or one renamed,
        // makes that sentence false in every published copy of the base.
        self::assertStringContainsString(
            'marks each command it lists **check**, **change** or **unknown**',
            $base,
        );
        self::assertNotSame([], $project['commands'], 'the installation declares no command to be marked');
        foreach ($project['commands'] as $command) {
            self::assertContains(
                $command['runs'],
                ['check', 'change', 'unknown'],
                $command['command'] . ' is marked nothing the base names',
            );
        }

        // What step 1 ends with, and what the base says each entry is worth:
        // one typo3_rule_lookup by documentId, which is the only route to a
        // whole procedure where the client renders no resource list. An id
        // that stopped resolving there names a procedure this server carries
        // and cannot hand over.
        self::assertNotSame([], $project['guides'], 'step 1 ends without the procedures it says it ends with');
        foreach ($project['guides'] as $guide) {
            $document = Registry::call('typo3_rule_lookup', ['documentId' => $guide['id']])->data;
            self::assertSame(
                [$guide['id']],
                array_column($document['matches'], 'documentId'),
                $guide['id'] . ' is named as a whole procedure and is no documentId',
            );
        }

        // Step 2: what the extension registers, and what it ships beside that.
        $own = array_values(array_filter(
            $project['extensions'],
            static fn(array $extension): bool => $extension['origin'] === 'project',
        ));
        self::assertNotSame([], $own, 'step 1 reports no extension of the project\'s own for step 2 to describe');

        $extension = Registry::call('typo3_extension_describe', ['extension' => $own[0]['key']])->data;
        self::assertArrayNotHasKey('unsupported', $extension, $own[0]['key'] . ' could not be described');
        // All four, present or absent: this installation ships no manual, no
        // README and no test layer, so the keys being there on it is the half
        // the base means by what an extension does *not* ship being answered
        // too — the half no file listing gives you.
        foreach (['manual', 'readme', 'tests', 'languageFiles'] as $artifact) {
            self::assertArrayHasKey(
                $artifact,
                $extension['artifacts'],
                'step 2 sends the session to read ' . $artifact,
            );
        }
        self::assertNotSame([], $extension['artifacts']['languageFiles'], 'the installation ships no XLF to read');
        foreach ($extension['artifacts']['languageFiles'] as $file) {
            self::assertArrayHasKey(
                'sourceLanguage',
                $file,
                $file['path'] . ' is reported without the source language the base has the session read off it',
            );
        }

        // Step 3: the brief, and the sentence step 4 is owed or not on the
        // strength of. This one stopped short of what the lookup matched.
        $paths = ['typo3/sysext/backend/Classes/Controller/PageLayoutController.php'];
        $brief = Registry::call('typo3_task_guide', [
            'task' => 'Add a backend module with icons and labels',
            'paths' => $paths,
            'changeType' => 'feature',
        ]);
        foreach (['skills', 'checks', 'checklist', 'hints', 'omittedHints'] as $key) {
            self::assertArrayHasKey($key, $brief->data, 'step 3 sends the session to read ' . $key);
        }
        self::assertNotSame([], $brief->data['hints'], 'the paths this is measured on match no hint');
        self::assertNotSame(
            [],
            $brief->data['omittedHints'],
            'the paths this is measured on stopped truncating the brief',
        );
        // Named rather than counted, because naming them is what the base sends
        // the session to fetch by id instead of repeating the query.
        foreach ($brief->data['omittedHints'] as $omitted) {
            self::assertStringContainsString(
                $omitted['id'],
                $brief->text,
                $omitted['id'] . ' was left out of the brief and is named nowhere in it',
            );
        }

        // The other branch of the same sentence, quoted off the base rather
        // than off the class that prints it: the two have to be one sentence,
        // or the base sends the session to read something it cannot find.
        $carried = Registry::call('typo3_task_guide', [
            'task' => 'Fix a bug in the data handler',
            'paths' => ['typo3/sysext/core/Classes/DataHandling/DataHandler.php'],
            'changeType' => 'bugfix',
        ]);
        self::assertSame([], $carried->data['omittedHints'], 'the paths this is measured on stopped carrying them all');
        $sentence = 'everything typo3_hint_lookup matches for these paths';
        self::assertStringContainsString($sentence, $base, 'the base quotes no sentence for a brief that carried them all');
        self::assertStringContainsString($sentence, $carried->text, 'a brief that carried them all does not say so');
        self::assertStringNotContainsString($sentence, $brief->text, 'a brief that stopped short says it carried them all');

        // And the same signal as data, which the step points at since
        // `D-GUI-013`: two sessions read `"omittedHints": []` beside that
        // sentence and asked for a machine-readable form of it, because the step
        // named the key it warns off and not the one that answers.
        self::assertStringContainsString('`omittedHints` is that sentence as data', $base);

        // Step 4: one query per subsystem with its concrete paths, and the id
        // route step 3 sends the session down where the brief already spent the
        // query.
        $hints = Registry::call('typo3_hint_lookup', ['paths' => $paths])->data;
        self::assertNotSame([], $hints['hints'], 'the subsystem step 4 is measured on matches no hint');

        $left = $brief->data['omittedHints'][0]['id'];
        $one = Registry::call('typo3_hint_lookup', ['id' => $left])->data;
        self::assertSame(
            [$left],
            array_column($one['hints'], 'id'),
            $left . ' is named as left behind and cannot be fetched by id',
        );
    }

    /**
     * The copy a project has of a skill the brief names, said where the file is
     * about to be loaded.
     *
     * The same thing is said once at initialize, before a task is known, and
     * the session that reported this read it there and then worked four such
     * skills — so it could not tell afterwards which of its findings were about
     * a copy this server has moved past (`D-SKL-086`).
     */
    #[Decision('D-SKL-086')]
    #[Test]
    public function theBriefSaysWhichOfTheSkillsItNamesThisProjectIsBehindOn(): void
    {
        $project = sys_get_temp_dir() . '/typo3-dev-companion-stale-' . bin2hex(random_bytes(4));
        $skill = 'typo3-extension-testing';
        $published = $project . '/.agents/skills/' . $skill;
        self::copyDirectory(Paths::root() . '/skills/' . $skill, $published);
        file_put_contents($published . '/references/base.md', file_get_contents(Paths::root() . '/skills/base.md'));
        @mkdir($project . '/.typo3-dev-companion', 0o777, true);
        file_put_contents($project . '/.typo3-dev-companion/state.json', json_encode([
            'version' => 1,
            'agents' => ['generic'],
            'skills' => [$skill],
            'digest' => Installer::digest(),
        ]));
        Instance::discoverFrom($project);

        $arguments = [
            'task' => 'add functional tests for the parser',
            'paths' => ['Classes/Parser/AbstractParser.php'],
            'changeType' => 'test',
        ];

        // In a finally, because a failing assertion is exactly when the
        // directory is left behind and `sys_get_temp_dir()` is shared by every
        // worktree running the suite — `D-COD-006`.
        try {
            // A copy of what this package publishes is not behind on anything,
            // and the field is a subset of `skills` rather than a second list.
            $current = Registry::call('typo3_task_guide', $arguments);
            self::assertSame([$skill], $current->data['skills']);
            self::assertSame([], $current->data['staleSkills']);

            file_put_contents($published . '/SKILL.md', "\nwhat an older publication said\n", FILE_APPEND);
            $stale = Registry::call('typo3_task_guide', $arguments);

            self::assertSame([$skill], $stale->data['staleSkills']);
            self::assertStringContainsString('typo3-dev-companion update', $stale->text);
        } finally {
            self::removeDirectory($project);
        }
    }

    private static function copyDirectory(string $source, string $target): void
    {
        @mkdir($target, 0o777, true);
        foreach (Finder::create()->files()->in($source) as $file) {
            $path = $target . '/' . $file->getRelativePathname();
            @mkdir(dirname($path), 0o777, true);
            copy($file->getPathname(), $path);
        }
    }

    private static function removeDirectory(string $directory): void
    {
        foreach (Finder::create()->in($directory)->depth('< 100')->sortByName()->reverseSorting() as $entry) {
            $entry->isDir() ? @rmdir($entry->getPathname()) : @unlink($entry->getPathname());
        }
        @rmdir($directory);
    }

    /** Nothing after this reads the written installation, and nothing before it did. */
    #[After]
    public function forgetTheInstallation(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();
        Typo3Runtime::forget();
        Icons::forget();
    }

    #[Requirement('R-SKL-008')]
    #[Test]
    public function theBaseStopsTheTaskWhenTheServerIsNotConnected(): void
    {
        // Sessions have run these skills more than once with the server not
        // connected at all, and produced the shape that is worst of the three
        // available: a review in the skill's own order and voice, built out of
        // general TYPO3 knowledge, with nothing in it saying so. The skill is a
        // published file, so it loads whether the tools do or not — which makes
        // the connection a precondition of the task rather than a step in it,
        // and puts it above the order in the one file every skill starts from.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $precondition = strpos($base, '## Nothing starts until the server answers');
        self::assertNotFalse($precondition, 'the base lets a task start without the server');
        self::assertLessThan(
            (int) strpos($base, 'typo3_project_describe'),
            $precondition,
            'the base reaches for its first call before it establishes there is one',
        );
        // The two shapes a missing server has, and neither reports itself: the
        // tools are absent from the session, or the first call comes back an
        // error.
        self::assertMatchesRegularExpression(
            '/No `typo3_` tool in this session, or a first call that errors: stop/',
            $base,
        );
        // And the fallback that produced the sessions above.
        self::assertMatchesRegularExpression(
            '/Do not fall back to general TYPO3 knowledge or start reading the checkout/',
            $base,
        );
        self::assertStringContainsString('Continue only when asked to after saying so', $base);
    }

    #[Requirement('R-SKL-005')]
    #[Requirement('R-SKL-017')]
    #[Decision('D-SKL-034')]
    #[Test]
    public function theWorkflowStepRunsInEverySession(): void
    {
        // The step carried a condition from 2026-08-04 to 2026-08-11: skipped
        // where the guide's own answer had named this skill, because that
        // session holds the brief already — `D-SKL-015`. It was skipped twice by
        // sessions the condition did not cover, `feedback/2026-08-04-055715` and
        // the 2026-08-10 core patch review `D-SKL-033` names, and neither of
        // them said so. What the condition asked was which route activated the
        // skill, which is not something a session establishes about itself, so
        // it came off — `D-SKL-034`.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $step = strpos($base, '**`typo3_task_guide`**');
        self::assertNotFalse($step, 'the base no longer carries the workflow step');
        $unconditional = strpos($base, 'Run it in every session');
        self::assertNotFalse($unconditional, 'the base no longer says the workflow step is run');
        self::assertGreaterThan($step, $unconditional);
        self::assertLessThan(
            (int) strpos($base, '**`typo3_hint_lookup`**'),
            $unconditional,
            'the workflow step is stated as unconditional at another step',
        );
        self::assertStringNotContainsString('Skip it only where', $base);

        // The sweep's is the one condition the order carries, because emptiness
        // is answered by the files the session is holding.
        self::assertSame(
            1,
            substr_count(self::flat($base), 'only where'),
            'the order carries a condition on a step other than the sweep',
        );

        // The broad reading, rejected in `D-SKL-015` and not revived by taking
        // its condition off: a skill that covers the task end to end still does
        // not know the caller's paths.
        self::assertStringNotContainsString('end to end', $base);
        self::assertStringContainsString(
            'brief is built from the paths as well as the task text, and no skill knows which paths the caller is holding',
            self::flat($base),
        );
        // What a skip costs is the path-specific brief and nothing else. The
        // commit step was named here as the other half of that cost while
        // `D-SKL-014` was queued, and stopped being one on the commit that put
        // it into the bodies of the skills that own extension work — the fourth
        // **Wrong if** of `D-SKL-015`, fired 2026-08-04. The base is copied into
        // all nine skills, the review-only ones included, which is why the step
        // is theirs and not this file's.
        self::assertStringNotContainsString('typo3_commit_message_guide', $base);

        // A condition that reads as an invitation is taken as one, so the order
        // says once what a skipped prescription costs the steps around it.
        self::assertStringContainsString(
            'a prescription that gets skipped teaches the next reader to skip the ones that matter too',
            self::flat($base),
        );
    }

    #[Requirement('R-SKL-017')]
    #[Decision('D-SKL-014')]
    #[Decision('D-SKL-064')]
    #[Test]
    public function theCommitStepIsNamedWhereASkillsWorkflowEndsInAChange(): void
    {
        // A session in `/home/benji/projects/syntax` was told to reproduce a
        // frontend defect in the extension it stood in, fix it and commit it. It
        // made 37 calls, all of them Bash, Read, Edit or Write, activated no
        // skill and called none of the 26 tools — `feedback/2026-08-04-012644`.
        // The two skills that named the commit guide were the core ones, and the
        // seven an extension author reaches for named no commit step at all,
        // which is the fourth and worst of the channels `D-GUI-002` counted.
        // `D-SKL-014` is the placement; which bodies get it was read off each
        // one.
        foreach (self::COMMITTING_SKILLS as $name) {
            $skill = (string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md');
            self::assertStringContainsString(
                'typo3_commit_message_guide',
                $skill,
                $name . ' ends in a change and names no commit step',
            );
            self::assertStringContainsString(
                'workflow="project"',
                self::flat($skill),
                $name . ' names the commit guide without the workflow it commits in',
            );
        }

        // The other side, and the one that would make this wrong: a review
        // changes nothing and commits nothing, so a commit line in it is the
        // patch checklist `R-GUI-006` exists to keep out of a review's answer.
        // `typo3-core-patch-review` reads the message a patch already carries,
        // which is why it names the guide at all, and it reads it against the
        // core's rules.
        $review = (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        );
        self::assertStringNotContainsString('workflow="project"', self::flat($review));

        // The audit was that absence until 2026-08-19, when it and the work
        // that answers it became one skill — `D-SKL-064`. What holds in its
        // place is the order: the report half says where a review ends, and the
        // commit stands after that gate rather than in the review's answer,
        // which is what `R-GUI-006` keeps out of one.
        $health = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-health/SKILL.md',
        ));
        $gate = '**A request that asked for a review ends here.**';
        self::assertStringContainsString($gate, $health);
        self::assertGreaterThan(
            (int) mb_strpos($health, $gate),
            (int) mb_strpos($health, 'typo3_commit_message_guide'),
            'the commit step stands before the gate a review ends at',
        );
        // And the description it is chosen on, which is the half a body cannot
        // correct: while it offered to improve a repository, the skill was
        // loaded for change requests whatever the body said.
        self::assertStringNotContainsString('improve', self::description('typo3-extension-health'));
    }

    #[Decision('D-SKL-003')]
    #[Requirement('R-SKL-004')]
    #[Requirement('R-SKL-005')]
    #[Test]
    public function theDeprecationSweepRunsFromTheExtensionsSurface(): void
    {
        // REVIEW-02 against an extension declaring two majors on an
        // installation a major behind: 24 $GLOBALS['TSFE'] call sites across 11
        // files, the deprecation on the installed controller, and the frontend
        // surface reported as carrying no superglobal access at all. The run
        // called changelog_lookup four times and never once with
        // type: deprecation — the one deprecated API it named, it reached
        // because a ViewHelper finding walked it there. So the sweep is a step
        // of the order, and what the extension ships is what bounds it rather
        // than what the reading happens to pass.
        //
        // What it bounds it *with* is the changelog's own axes and not the
        // extension's words — D-SKL-003. Two models swept one sitepackage on
        // 2026-07-31 with the query set this step used to prescribe and both
        // got nothing; re-run on 2026-08-02, type: deprecation with version 14
        // and no query returns all 75, and tag: ext:form returns the 6 that
        // carry #109412, which the words missed at 39th place — past the
        // default limit of 20.
        //
        // What bounds the sweep is those two axes and not the tag. The session
        // that composed it out of one call per tag paid eleven of them to reach
        // 72 of those 75, at 1.7 times the payload of the one call that lists
        // the major (`feedback/2026-08-19-094403`, `D-ANS-093`), so `limit`
        // carries the largest covered set — 128 — and the tags are read off the
        // entries the one answer returns.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $sweep = strpos($base, 'typo3_changelog_lookup');
        self::assertNotFalse($sweep, 'the base never sweeps the deprecations of the installed core');
        self::assertGreaterThan((int) strpos($base, 'typo3_hint_lookup'), $sweep);
        self::assertLessThan(
            (int) strpos($base, '**Then** read the checkout'),
            $sweep,
            'the sweep is left until after the checkout has been read',
        );
        self::assertStringContainsString('`type: deprecation`', $base);
        self::assertStringContainsString(
            'with the query omitted and `limit` raised to carry that major whole',
            self::flat($base),
        );
        // One call per major, and the round trips the tag composition cost are
        // gone rather than reordered.
        self::assertStringContainsString('That is one call per declared major', self::flat($base));
        self::assertStringNotContainsString('per declared major per tag', self::flat($base));
        // The extension's surface picks the entries out of that answer, which
        // is the half of "from the extension's surface" that survives.
        self::assertStringContainsString(
            "Step 2 picks the package's entries out of that answer by those tags",
            self::flat($base),
        );
        self::assertStringContainsString('name the system extension a change is **in**', $base);
        self::assertStringContainsString(
            'An extension key of your own is not among them',
            self::flat($base),
        );
        // And the wording that cost the two sweeps is gone rather than softened.
        self::assertStringNotContainsString('query set', $base);
        // And what the caller does with the answer: an identifier the checkout
        // does not use is not a finding, and the tag decides who has to read
        // the remaining call sites.
        self::assertStringContainsString('Verify each identifier that comes back in the', $base);
        self::assertStringContainsString('`FullyScanned` / `PartiallyScanned`', $base);

        // Written once. The conformance skill carried the weaker copy — the
        // sweep "when an upgrade or a deprecated API is in scope" — which is
        // the escape hatch that run took: nothing had put a deprecated API in
        // scope, so nothing swept.
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-health/SKILL.md',
        );
        self::assertStringNotContainsString('typo3_changelog_lookup', $skill);

        // A sweep that is visible only when it produces a finding is
        // indistinguishable from one that never ran, which is what made that
        // run's clean frontend surface writable.
        self::assertStringContainsString('the sweep ran and came back empty', $skill);
    }

    #[Requirement('R-SKL-005')]
    #[Decision('D-SKL-034')]
    #[Decision('D-SKL-037')]
    #[Test]
    public function theDeprecationSweepIsSkippedWhereNoTypo3ApiIsTouched(): void
    {
        // The second half of `feedback/2026-08-04-055741`: the sweep was
        // prescribed and skipped on a change that added a fixer, an
        // `.editorconfig` and two CI commands. A deprecation is a statement
        // about API the package calls, so that sweep was empty before it ran —
        // at one call per declared major carrying that major whole, which is
        // the largest answer the order asks for and what makes this step the
        // expensive one to leave prescribed and unrun. It is the one condition
        // the order carries since step 3's came off, and it survives for the
        // reason that one did not: what a change touches is in front of the
        // session, and how the skill was activated is not (`D-SKL-034`) —
        // `D-SKL-015`.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $condition = strpos($base, 'Skip the sweep only where the change touches no TYPO3 API');
        self::assertNotFalse($condition, 'the base states no condition on the deprecation sweep');
        self::assertGreaterThan((int) strpos($base, 'typo3_changelog_lookup'), $condition);
        self::assertLessThan(
            (int) strpos($base, '**Then** read the checkout'),
            $condition,
            'the condition on the sweep stands after the reading it bounds',
        );
        // Empty rather than merely unlikely to find anything, which is the
        // whole of what makes the call skippable.
        self::assertStringContainsString(
            'a change that calls none has nothing for the sweep to land on',
            self::flat($base),
        );
        // And what the condition is worth stating against, now that the cost is
        // one answer rather than eleven calls (`D-ANS-093`).
        self::assertStringContainsString(
            'this step is the largest answer the order asks for',
            self::flat($base),
        );
        // And what keeps a tooling task that ends up editing one PHP file from
        // reading the condition off the task it was described as.
        self::assertStringContainsString(
            'read off the files it touches and never off the task it started as',
            self::flat($base),
        );

        // The other side of the same step, and the one a read-only task falls
        // outside of: it matches neither "touches API" nor the three examples,
        // so a session following the order faithfully would have run the sweep
        // across seven tags on a triage of one issue (`D-AUD-009`,
        // `feedback/2026-08-07-233512`).
        self::assertStringContainsString(
            'A task that produces no change does not reach this step at all',
            self::flat($base),
        );
        // Written as the three examples it was, that exemption held for the
        // shape it was written for and let the next one through: a review of a
        // patch was in none of them, skipped the sweep on a diff touching
        // TYPO3 API, and read the distance from "a review of a report" as
        // deliberate (`feedback/2026-08-11-055337`, `D-SKL-037`). So what the
        // exemption states is the property and the examples illustrate it.
        self::assertStringContainsString(
            'illustrations of it rather than the list it is read off',
            self::flat($base),
        );
        // The property's own boundary, which the enumeration never had to
        // carry: a review asked to make the change is a workflow that produces
        // one, and it reaches this step holding files.
        self::assertStringContainsString(
            'The exemption ends where the workflow produces a change',
            self::flat($base),
        );
    }

    #[Requirement('R-SKL-005')]
    #[Decision('D-SKL-074')]
    #[Test]
    public function theReportNamesTheStepsOfTheOrderItDidNotReach(): void
    {
        // Three sessions from three task shapes took an exemption and named the
        // step nowhere — a DDEV boot, a change that skipped steps 2, 4 and 5,
        // and the patch review of `feedback/2026-08-24-110949`. The obligation
        // was stated twice and both copies stood inside a step it exempts,
        // which is the paragraph a session taking the exemption reads least
        // carefully (`D-SKL-074`).
        $base = self::flat((string) file_get_contents(Paths::root() . '/skills/base.md'));

        $obligation = strpos($base, 'the report names every step of this order it did not reach');
        self::assertNotFalse($obligation, 'the base never asks a report to name the steps it skipped');

        // Where it stands is the whole of the change: after the reading the
        // order ends on, so a session meets it once the work it reports about
        // is done rather than in the middle of the step it is about.
        self::assertGreaterThan(
            (int) strpos($base, '**Then** read the checkout'),
            $obligation,
            'the obligation to name a skipped step stands inside the order rather than after it',
        );

        // And what it covers is every step, not the one exemption it was
        // written under: step 2 has nothing to call where step 1 reported no
        // extension, and step 4 is discharged by a brief that carried
        // everything the lookup matched.
        self::assertStringContainsString(
            'That is an answer already in the session, a condition that made the step empty, or an exemption',
            $base,
        );
        self::assertStringContainsString(
            'A step passed over in silence cannot be told from one that was dropped',
            $base,
        );

        // Written once. Both earlier copies stood in a step, and a reader who
        // met one of them had no reason to look for the other.
        self::assertSame(1, substr_count($base, 'names every step of this order it did not reach'));
        self::assertStringNotContainsString('under either exemption', $base);
    }

    #[Requirement('R-SKL-005')]
    #[Decision('D-ANS-010')]
    #[Decision('D-SKL-085')]
    #[Test]
    public function theChangelogsSilenceIsNotAnAnswerAboutWhatStillWorks(): void
    {
        // Two findings of one bootstrap_package review ended in "I had to read
        // installed vendor core". Both carried a "does this still work in 14"
        // question, both asked the changelog, and an empty result was read as
        // the answer — while typo3_documentation_lookup at targetVersion 14
        // with the query "backend layout" returns the two pages that settle one
        // of them, first and second, in one call. So the sweep's own step says
        // what its silence is worth, rather than a sixth step: the sweep is
        // writable before a file is opened because step 2 supplies its tags,
        // and this question has nothing to bound it until the reading raises
        // it.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $sweep = (int) strpos($base, 'typo3_changelog_lookup');
        $manual = strpos($base, 'typo3_documentation_lookup', $sweep);
        self::assertNotFalse($manual, 'the base never sends a version-behaviour question to the manual');
        self::assertLessThan(
            (int) strpos($base, '**Then** read the checkout'),
            $manual,
            'the manual is offered only after the checkout has been read',
        );
        self::assertStringContainsString(
            'A changelog records change events, so a pattern nothing has touched for ten majors has no entry at all',
            self::flat($base),
        );
        self::assertStringContainsString('"Does this still work in version N"', $base);

        // And the half that keeps the routing honest. The same review's second
        // instance — an ext_localconf.php content-rendering registration — is a
        // key the manual has no page for, so a miss there is a result rather
        // than a licence to reconstruct the contract from the installed core.
        self::assertStringContainsString('that is a result and not an answer', self::flat($base));
        self::assertStringContainsString('Undocumented is not unsupported', $base);

        // And what that half is worth is bounded by what the manual can be
        // asked. `feedback/2026-08-03-164805` followed this routing and read
        // `PageRenderer.php` by hand anyway: re-run from
        // `/home/benji/projects/ext-guidedtour` on 2026-08-03,
        // `Infobox ViewHelper state` at `targetVersion: "14"` returns the
        // ViewHelper reference page first, carrying the deprecation whole,
        // while `addInlineLanguageLabelFile` and `inline language labels`
        // return the label reference and TCA pages that spell "label" and
        // "add" in their titles and name the method nowhere. The tool's own
        // header says why, and the identifier route is the one that answers:
        // `typo3_changelog_lookup` with `addInlineLanguageLabelFile` returns
        // the 7.5 Feature entry that introduced it and no deprecation, which
        // is `D-ANS-042` — so the miss above is a result for a surface and the
        // wrong corpus for an identifier (`D-ANS-010`).
        self::assertStringContainsString(
            'The manual matches page titles and section paths, never the text of a page',
            self::flat($base),
        );
        self::assertStringContainsString('a PHP identifier has no page to be titled after', self::flat($base));
        $identifier = strpos($base, 'An identifier goes to');
        self::assertNotFalse($identifier, 'the base leaves a PHP identifier pointed at the manual');
        self::assertLessThan(
            (int) strpos($base, '**Then** read the checkout'),
            $identifier,
            'the identifier route is offered after the reading it exists to save',
        );
        // The limit that carries the second half now stands where the reading
        // it limits is ordered rather than here, where nobody is reading the
        // core — the section below, which `D-SKL-004` earned.
        self::assertStringContainsString(
            'what this installation does and never what TYPO3 supports',
            self::flat($base),
        );

        // Written once. The conformance skill's own entry carried the narrower
        // condition the failing session read past — an official API or
        // configuration detail decides the finding — which a session holding a
        // behaviour question does not match itself against.
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-health/SKILL.md',
        );
        self::assertStringContainsString('does this still work here', $skill);
        // And the one step of the base an audit legitimately skips, said where
        // this workflow starts writing. A review that took the exemption
        // re-entered here and worked every item without the sweep, because the
        // sentence closing the exemption is in the paragraph granting it
        // (`D-SKL-085`).
        self::assertStringContainsString(
            'was exempt while nothing was being written and is owed now',
            self::flat($skill),
        );
        self::assertStringNotContainsString('A changelog records change events', $skill);
        // The bound on the same routing is written in the same one place. The
        // conformance skill defers to the base for why the changelog cannot
        // answer, and the upgrade skill starts from the same sweep, so a copy
        // here is the second hand-written order `D-SKL-001` exists to prevent.
        self::assertStringNotContainsString('page titles and section paths', $skill);
    }

    #[Requirement('R-SKL-005')]
    #[Decision('D-SKL-004')]
    #[Test]
    public function theInstalledSourceIsTheStepAfterTheLookups(): void
    {
        // The base's one sentence for an exhausted question was written for a
        // review — "the finding says the question could not be settled" — and
        // `feedback/2026-08-01-003933` is a session building a content element
        // in `site-new`, which had no finding to write and a template that had
        // to render. It guessed at the `f:if` branch contract and changed the
        // markup until the user corrected it, while the installed source sat in
        // the checkout: `IfViewHelper` ships in `typo3fluid/fluid` rather than
        // in `typo3/cms-fluid`, and its docblock carries `<f:then>` explicitly
        // in every `f:else` example it gives (`D-SKL-004`).
        //
        // The reading is bounded rather than licensed. `D-ANS-010` refused the
        // installed core as a substitute for the manual, so what the step
        // settles is this installation and the answer says so — "read the
        // source before guessing", as the feedback proposed it, would license
        // the reconstruction that entry turned down.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $step = strpos($base, '## When the lookups run out');
        self::assertNotFalse($step, 'the base names no step for a question the lookups leave open');
        // After the lookups and after the checkout, or it is the reverse of the
        // workflow rather than the end of it.
        self::assertGreaterThan(
            (int) strpos($base, '**Then** read the checkout'),
            $step,
            'the base sends the session into the installed source before its own calls',
        );
        self::assertStringContainsString(
            'A behaviour question that survives the lookups above is read out of the installed source',
            self::flat($base),
        );
        // An act with an object, because the same position has already cost a
        // rule that was present and read past (`D-SKL-009`).
        self::assertStringContainsString(
            'the class that implements the behaviour and the one it inherits from',
            self::flat($base),
        );
        // What it replaces is what the filing session actually did, named in
        // those words rather than left to be inferred from a prohibition.
        self::assertStringContainsString('what it replaces is changing the code until it works', self::flat($base));

        // `D-ANS-010`'s boundary, carried in the sentence that orders the
        // reading: one installation's implementation is not what TYPO3
        // supports.
        self::assertStringContainsString(
            'What it settles is what this installation does and never what TYPO3 supports',
            self::flat($base),
        );
        // And both dispositions, because naming only the first is what this
        // step was queued to repair: a session that reports and a session that
        // has to produce something that works.
        self::assertStringContainsString(
            'a finding says the question could not be settled beyond the version installed',
            self::flat($base),
        );
        self::assertStringContainsString(
            'an answer built on the reading names the version it holds for',
            self::flat($base),
        );
    }

    /**
     * `R-SKL-026`. The pairing the section drew was runtime against
     * conventions, and read in the order the base fixes it describes a call the
     * session has already made: a v14 release audit of a blog extension called
     * none of the five and says why — `typo3_extension_describe` had returned
     * the four backend modules, 24 icon identifiers, the XLF files with their
     * source languages and the site sets, and the run "treated the describe
     * output as the runtime half for every one of those surfaces"
     * (`feedback/2026-08-19-094432`, `D-SKL-069`). So each of them says what it
     * adds after step 2 rather than what kind of lookup it is.
     */
    #[Requirement('R-SKL-026')]
    #[Test]
    public function everyRuntimeLookupSaysWhatItAddsAfterTheExtensionAnswer(): void
    {
        $base = self::flat((string) file_get_contents(Paths::root() . '/skills/base.md'));

        $section = mb_strpos($base, '## What each runtime lookup adds after the extension answer');
        self::assertNotFalse($section, 'the base distinguishes the runtime lookups from nothing');
        self::assertGreaterThan(
            (int) mb_strpos($base, 'typo3_extension_describe'),
            $section,
            'the five are measured against an answer the order has not reached yet',
        );

        // What each adds, read off the tool's own declaration rather than off
        // the kind of lookup it is: the inheritance a declaration cannot show,
        // the identifiers of every package at once, the overrides applied, what
        // is global rather than this package's own, and the value step 2 is not
        // about at all — which is the one the run says it could have settled in
        // a call and judged off `ext_localconf.php` instead.
        $adds = [
            'typo3_backend_module_lookup' => 'the navigation component the parent module supplies',
            'typo3_icon_lookup' => 'across every installed package',
            'typo3_label_lookup' => 'the labels as the installation resolves them',
            'typo3_fluid_namespace_list' => 'from every package at once',
            'typo3_configuration_lookup' => 'after every extension has had its say',
        ];

        $lines = [];
        foreach (array_keys($adds) as $tool) {
            $at = mb_strpos($base, '- `' . $tool . '`', $section);
            self::assertNotFalse($at, $tool . ' has no line of its own in the section that distinguishes it');
            $lines[] = $at;
        }
        // The five were skipped together because one sentence covered all of
        // them, so what each adds is held on its own line and not on the set.
        $ends = array_slice($lines, 1);
        $ends[] = (int) mb_strpos($base, 'None of the five says whether', $section);

        foreach (array_values($adds) as $index => $sentence) {
            $line = mb_substr($base, $lines[$index], $ends[$index] - $lines[$index]);
            $tool = array_keys($adds)[$index];
            self::assertStringContainsString(
                $sentence,
                $line,
                $tool . ' is named without what it adds after the extension answer',
            );
            self::assertStringContainsString(
                'Step 2',
                $line,
                $tool . ' says what it adds without saying what the caller already has',
            );
        }

        // The half that survives the correction: what is registered is not a
        // verdict on it, whichever call established it.
        self::assertStringContainsString(
            'None of the five says whether what it reports is right',
            $base,
        );

        // Written once. The audit skill carried the same sentence in the same
        // words, and that is the copy the run was reading — so what it states
        // is what an audit adds, which is the call per surface in scope.
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-health/SKILL.md',
        ));
        self::assertStringNotContainsString('runtime lookup reports what is registered', $skill);
        foreach (array_keys($adds) as $tool) {
            self::assertStringNotContainsString(
                $tool,
                $skill,
                'the audit names ' . $tool . ' beside a surface the base already pairs it with',
            );
        }
        self::assertStringContainsString('The runtime lookup that owns the surface, where one exists', $skill);
        self::assertStringContainsString('per surface in scope', $skill);
    }

    #[Requirement('R-SKL-004')]
    #[Test]
    public function aSecurityFindingIsNotEstablishedUntilItsSinkIs(): void
    {
        // The same REVIEW-02 run reported an editor-supplied field rendered
        // unescaped as its one finding with an active security consequence.
        // Every citation under it was correct and the output is escaped anyway:
        // the six call sites sit in a ViewHelper that emits nothing, and the
        // core wraps the resolved title in htmlspecialchars() two classes
        // further on — neither of which the run opened, while it did open the
        // core ViewHelper that confirmed what it already believed.
        // Read with the line breaks collapsed: what is asserted is that the
        // sentence is there, and where it wraps is the formatter's business.
        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-health/references/checklist.md',
        ));

        self::assertStringContainsString(
            'finding about a user-controlled value is a claim about a **sink** rather than about a call site',
            $checklist,
        );
        // Escaping is one sink and a query is another, so the gate is written
        // once for both: the run that earned it condemned a template line, and
        // a value concatenated into a statement needs the same reading.
        self::assertStringContainsString('escaping and injection are the same claim about', $checklist);
        // The half that decides that case: the opt-out the finding condemned is
        // on the path to the sink rather than the end of it, and it is there
        // because the sink escapes.
        self::assertStringContainsString('is on the path rather than at the end of it', $checklist);
        self::assertStringContainsString('report the finding as unverified', $checklist);
        // The sinks themselves are a tool's to answer, so the checklist asks
        // rather than carrying a list that goes stale in a published copy.
        self::assertStringContainsString('`typo3_hint_lookup` for the sinks', $checklist);
    }

    #[Requirement('R-SKL-011')]
    #[Decision('D-SKL-007')]
    #[Test]
    public function aReviewReportsWhatItDroppedAndWhatDroppedIt(): void
    {
        // What a review let go is the half nothing recorded: a candidate dropped
        // in silence and a surface nobody opened leave the same trace in the
        // report. The conformance checklist already states the bar for one
        // subject — a security verdict has to be disproved before it can be
        // dismissed — and what makes it a bar is not the subject but who pays
        // for it being wrong, which is the reader either way (`D-SKL-007`).
        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/references/checklist.md',
        ));

        self::assertStringContainsString('## What a dropped candidate owes', $checklist);
        self::assertStringContainsString('dropping is the step nothing records', $checklist);
        // The asymmetry is the whole of it: raising a candidate costs a reading,
        // dropping one costs the author a finding and announces nothing.
        self::assertStringContainsString('dropped only where something concretely disproves it', $checklist);
        self::assertStringContainsString('neither established nor disproved is reported as open', $checklist);
        // The two dismissals that go wrong: the docblock read in place of the
        // implementation, and "unlikely" standing in for "impossible".
        self::assertStringContainsString('read the implementation it describes', $checklist);
        self::assertStringContainsString('Unlikely is not disproved', $checklist);

        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));
        self::assertStringContainsString(
            'what was raised while reading and dropped, with what dropped it',
            $skill,
        );

        // The audit is held to it too, measured rather than assumed: the two
        // recorded conformance runs write four dismissals each into the answer
        // with nothing asking for them, which is a section a reader sits
        // through and not the flood the narrower scope was written against.
        $audit = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-health/references/checklist.md',
        ));

        self::assertStringContainsString('## What a dropped candidate owes', $audit);
        self::assertStringContainsString('dropping is the step nothing records', $audit);
        self::assertStringContainsString('dropped only where something concretely disproves it', $audit);
        self::assertStringContainsString('neither established nor disproved is reported as open', $audit);
        self::assertStringContainsString('read the implementation it describes', $audit);
        self::assertStringContainsString('Unlikely is not disproved', $audit);
        // What the audit adds and the patch review has no use for: six surfaces
        // enumerated whole mean most of them are absent in any one package, and
        // an absence already answered as not applicable would fill this section
        // with subsystems nobody entertained.
        self::assertStringContainsString(
            'A subsystem the package does not ship never enters this list',
            $audit,
        );
        // The bar came into the checklist for a security verdict alone, and what
        // makes it a bar is who pays for a wrong dismissal.
        self::assertStringContainsString('the bar is not that subject\'s', $audit);

        $auditSkill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-health/SKILL.md',
        ));
        self::assertStringContainsString(
            'what was raised while reading and dropped, with what dropped it',
            $auditSkill,
        );
    }

    /**
     * `R-SKL-023`, and `D-SKL-040` revoked with it: what the correction asked
     * for is the form rather than a path. A file stays the caller's to ask for,
     * and where one is written it goes outside the checkout the skill just
     * assessed.
     */
    #[Requirement('R-SKL-023')]
    #[Test]
    public function aReportIsCopyableMarkdownAndTheAnswerIsWhereItGoes(): void
    {
        foreach (self::REPORTING_SKILLS as $name) {
            $skill = self::flat((string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md'));

            self::assertStringContainsString(
                'is markdown the reader can copy, and the answer is where it goes',
                $skill,
                $name . ' specifies a report and names no form for it',
            );
            // The property being asked for, in the words that say what breaks
            // without it: a form is what a long report has to survive being
            // moved in.
            self::assertStringContainsString(
                'rendered output is what does not survive being moved',
                $skill,
                $name . ' states the form without what makes it the form',
            );
            // The path is the caller's, so nothing decides a name or a
            // directory and the assessed checkout cannot be dirtied by
            // accident.
            self::assertStringContainsString(
                'only where the caller asks for one',
                $skill,
                $name . ' prescribes a path the caller did not ask for',
            );
            self::assertStringContainsString(
                'outside the checkout',
                $skill,
                $name . ' writes the report where the checkout it assessed is',
            );
        }
    }

    #[Decision('D-SKL-009')]
    #[Test]
    public function aReviewNamesTheSuitesItDidNotRun(): void
    {
        // Three REVIEW-03 runs in a row reported green suites and named none of
        // the ones `typo3_test_run_guide` had returned beside them, at three
        // skill lengths including the shortest — so the rule was present and
        // delivered every time. It read as a ban on claiming an unrun suite
        // passed, and an omission claims nothing, which is why every run was
        // compliant with the sentence and none with the demand. The rewrite
        // makes it an act with an object and puts the omission itself in the
        // example (`D-SKL-009`).
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));

        self::assertStringContainsString(
            '**It then writes out, by name, the suites on that list it did not run.**',
            $skill,
        );
        // The consequence names what the omission does to the reader, which the
        // banned sentence never said.
        self::assertStringContainsString('read as a finished verification', $skill);
        // And it ties the omission to the claim the reader already rejects.
        self::assertStringContainsString(
            'an unnamed suite is the same sentence with the words taken out',
            $skill,
        );

        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/references/checklist.md',
        ));
        self::assertStringContainsString(
            'answered with both halves: what ran, and which of the suites the guide returned nobody started',
            $checklist,
        );
    }

    #[Decision('D-SKL-071')]
    #[Test]
    public function aProbeIsPutBackToTheStateItFound(): void
    {
        // `feedback/2026-08-24-100329` followed the restore literally while the
        // probed file also carried the change the session had just been asked
        // for, and `git checkout --` took both, because the index held the
        // patch set. Re-run in a scratch repository on 2026-08-24: unstaged
        // work plus a probe restores to the commit, the same work staged first
        // restores to the work, and `git stash push <path>` leaves the file at
        // the commit as well — so the stash the report offers restores nothing
        // either (`D-SKL-071`).
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));

        self::assertStringContainsString(
            '**Put the tree back to what the probe found, which is not always what is committed.**',
            $skill,
        );
        // Where the restore lands, which is what makes the two routes under it
        // one rule rather than a preference between two commands.
        self::assertStringContainsString(
            '`git checkout -- <path>` restores the file from the index, '
                . 'and in a review the index holds the patch set',
            $skill,
        );
        // And the check, which is the half that pointed the wrong way: the file
        // was clean afterwards, exactly as the sentence below asked for.
        self::assertStringContainsString('Verify with `git diff --stat <path>`', $skill);
        self::assertStringNotContainsString('`git status` to confirm it is clean', $skill);
    }

    #[Decision('D-SKL-029')]
    #[Test]
    public function aPrecedentIsListedByTypeAndVersionBeforeItIsAskedForInWords(): void
    {
        // `feedback/2026-08-01-115716` credits `typo3_changelog_lookup` with the
        // decisive finding of that review, and `feedback/2026-08-01-115112`,
        // filed four seconds earlier by the same session, reports that the same
        // lookup could not reach it and that grepping `Documentation/Changelog`
        // did. Re-run from that checkout on 2026-08-03: `getTemporaryImageWithText`
        // reaches nothing, the session's own `GifBuilder placeholder preview
        // thumbnail` at version 15 reaches nothing, and `image generation`
        // reaches `13.0 Breaking-101955` alone, in one call — the matcher runs
        // over the file name, and the removed method is in a list inside the
        // file (`D-ANS-030`). So the step this order opens on is the one the
        // feedback got wrong, and an order that opens with a miss in the case
        // the review needed it would ship that miss into somebody's project.
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));

        // The words are the second step and not the first, because both reviews
        // that followed this bullet lost the entry their finding turned on to
        // them and found it by hand: `feedback/2026-08-08-224429` asked
        // `stdWrap override` and settled the review with `ls` over
        // `Documentation/Changelog/13.4.x`, where `type: important` and
        // `version: 13.4` with no query at all returns the same 20 entries in
        // one call, titles included (`D-SKL-029`).
        self::assertStringContainsString(
            '**List the kind before you search for words: `type` and `version`, and no query at all.**',
            $skill,
        );
        // Which line to list, since a filter set to the branch under review is
        // the one the paragraph below forbids.
        self::assertStringContainsString('the line the precedent would sit on', $skill);
        // And the second bound, without which a major still collecting entries
        // answers with a page of the default. Read in `.checkouts/14.3` on
        // 2026-08-21: 14.0 through 14.3.x hold 99 breaking and 36 important
        // entries, which one answer carries since the cap moved to 200
        // (`D-ANS-093`) and the default of 20 does not.
        self::assertStringContainsString('holds more of a type than the default answer carries', $skill);
        self::assertStringContainsString('so raise `limit` there', $skill);
        self::assertStringContainsString(
            '**Ask it in the words the entry is titled in, not in the identifier the diff removes.**',
            $skill,
        );
        // Why the identifier is what the reviewer is holding, and where it
        // actually sits in the entry.
        self::assertStringContainsString('carries the identifiers in a list inside the file', $skill);
        // The empty answer is the trap: it reads as "no precedent exists" from
        // both the identifier and the version filter, and it is neither.
        self::assertStringContainsString('coming back empty has established nothing', $skill);
        self::assertStringContainsString(
            'a precedent is filed under the version it landed in',
            $skill,
        );
        // And the source that answered when the lookup did not, which the server
        // cannot reach itself.
        self::assertStringContainsString('`Documentation/Changelog`, which this server does not read', $skill);
        self::assertStringContainsString('Say which of the two answered', $skill);
        // What a listed entry was is the reading a precedent argument is made
        // of, and the tracker does not answer it. Measured on 2026-08-09 over
        // 128 entries of all four types from `13.4.x` and `14.0`–`14.3`: the
        // Forge tracker and the keyword of the commit that added the entry
        // agree on 101 of them, and on 20 of the 26 important ones. It misses in
        // both directions — #103140 is filed as a Feature and was added by
        // `[BUGFIX] Allow to configure RateLimiters in message consumer`,
        // #105653 as a Bug and was added by a `[TASK]` — so `D-SKL-029`'s
        // assumption does not hold and its third **Wrong if** is what the step
        // now says.
        self::assertStringContainsString(
            '**What kind of change an entry came out of is two readings rather than one.**',
            $skill,
        );
        self::assertStringContainsString('The two disagree in both directions', $skill);
        self::assertStringContainsString('`git log --diff-filter=A` over the entry\'s own file', $skill);
        // #108604 and #109585 are important entries of a security release, and
        // the tracker answers 401 for both.
        self::assertStringContainsString('The issue behind a security entry is not public', $skill);
    }

    #[Test]
    public function anIdiomPrecedentIsSweptFromTheCheckout(): void
    {
        // `feedback/2026-08-03-144457` reviewed a core commit and settled three
        // questions by grep. Two of them name a class, which the base's step
        // after the lookups reaches; "is this idiom established in core" reaches
        // nothing, because it is a sweep for call sites with no class to start
        // at — and a reviewer asks it of every alternative it proposes, which is
        // the count that feedback carries. The boundary was already written, in
        // `knowledge/server-scope.json`, where `typo3_server_scope` alone
        // returns it and no review order calls it (`D-SKL-004`).
        //
        // Read in `.checkouts/main` on 2026-08-03 rather than recalled: the lazy
        // autowire attribute stands at `core/Classes/Site/Set/SetRegistry.php:43`,
        // `form/Classes/EventListener/DataStructureIdentifierListener.php:68` and
        // `form/Classes/Domain/Configuration/PersistenceConfigurationService.php:41`,
        // while `knowledge/hints/di.json` carries the plain attribute and
        // nothing about the lazy form. So no lookup here answered it and the
        // checkout did.
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));

        // An act with an object, because the same position has already cost a
        // rule that was present and read past (`D-SKL-009`).
        self::assertStringContainsString(
            '**Sweep the checkout for the call sites before proposing an alternative.**',
            $skill,
        );
        // The bar that makes it a step of the review rather than a nicety.
        self::assertStringContainsString('needs precedent rather than taste', $skill);
        // Why the base's own step does not reach it, said where a reviewer who
        // has just read the base would otherwise apply it anyway.
        self::assertStringContainsString(
            'starts at the class that implements a behaviour; this question has none',
            $skill,
        );
        self::assertStringContainsString('PHP source as code is outside what this server reads', $skill);
        // What the answer is, so that "I checked" is not the report.
        self::assertStringContainsString('the call sites at their paths and lines', $skill);
        self::assertStringContainsString(
            'one is a coincidence and a spread across system extensions is a convention',
            $skill,
        );
        // And the identifier stays out: the attribute this was measured on is a
        // core fact, and a published skill is what no release of this server
        // corrects.
        self::assertStringNotContainsString('Autowire', $skill);
    }

    #[Decision('D-SKL-043')]
    #[Test]
    public function aRuleQueryCarriesTwoSubjectsAndAThirdIsACallOfItsOwn(): void
    {
        // The bound is the count, measured over every combination of headings
        // the prose corpus carries: two subjects emptied none of 234 queries, a
        // third emptied 123 of 516 and a fourth 377 of 830. "Length is the
        // limit rather than the count" was read as permission by a review that
        // asked four subjects at once and got nothing — `D-SKL-043` —
        // `D-SKL-011`.
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));

        self::assertStringContainsString(
            '**Two subjects at most in one call, and a third is a call of its own.**',
            $skill,
        );
        // The pair it was measured on, so the rule is checkable rather than
        // taken on the skill's word.
        self::assertStringContainsString(
            '`breaking change changelog entry` returns both sections whole',
            $skill,
        );
        // And the query that emptied, which is what the bound is against.
        self::assertStringContainsString(
            '`changelog entry testing review readiness` returns nothing at all',
            $skill,
        );
        // The two claims about the ranker that were read as permission are gone.
        self::assertStringNotContainsString('a query that names two reaches neither', $skill);
        self::assertStringNotContainsString('Length is the limit rather than the count', $skill);
    }

    #[Requirement('R-SKL-014')]
    #[Decision('D-SKL-008')]
    #[Test]
    public function aReviewReadsTheReviewThePatchIsAlreadyIn(): void
    {
        // Both tools existed and no skill routed to either. The third recorded
        // REVIEW-03 run reviewed change 95070 without asking for it: the issue
        // it resolves is called "Avoid calling ImageService methods - part 2",
        // carries no description, and its part 1 is already in origin/main —
        // so the run judged a series as a patch standing alone, and its own
        // report closed by saying the issue had not been fetched (`D-SKL-008`).
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));

        self::assertStringContainsString('## What the project already says about this patch', $skill);
        self::assertStringContainsString('`typo3_forge_lookup` with the issue number', $skill);
        // The call moved into the step that establishes the patch when the
        // answer began carrying the paths and the message (`D-ANS-112`), so
        // what is held is the handle rather than the sentence around it.
        self::assertStringContainsString('`typo3_gerrit_lookup` with the `Change-Id`', $skill);
        // The issue is where a series announces itself, which is what makes the
        // set rule reachable at all.
        self::assertStringContainsString('an issue calling itself a part tells you the patch is not', $skill);
        // An unanswered comment is the finding this step exists for.
        self::assertStringContainsString('nobody answered is a finding of its own', $skill);
        // The trap, measured on 2026-08-03 rather than assumed: the Forge issue
        // and the Gerrit change are different numbers, and swapping them does
        // not fail. `typo3_gerrit_lookup` given 95070 as an issue answers with
        // change 70860, a MERGED acceptance-test cleanup from 2021, because it
        // searches commit messages for the string; `typo3_forge_lookup` given
        // the same number answers with issue 95070, a closed 11.4 task. Both
        // report `answered` and neither payload says the number was the other
        // one's, so a review can read a 2021 change believing it read this one.
        self::assertStringContainsString('Both arguments come out of the commit message', $skill);
        self::assertStringContainsString(
            'carries the subject of the commit under review, or the number was wrong',
            $skill,
        );
        // The Change-Id is the one that survives an amend, so it is what a
        // review of a patch that will come back is told to hold.
        self::assertStringContainsString('still names it after an amend', $skill);
        // An answer of nothing is a result rather than a silence, which is what
        // keeps a not-yet-pushed patch from reading as unchecked.
        self::assertStringContainsString('an answer of nothing is a result', $skill);
        // Reading only: the server holds no credential and the review does not
        // vote on the caller's behalf.
        self::assertStringContainsString('Voting, commenting and uploading stay with the person', $skill);

        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/references/checklist.md',
        ));
        self::assertStringContainsString('**The review this patch is already in.**', $checklist);
        self::assertStringContainsString('The issue is read for that, not inferred from the message', $checklist);
    }

    /**
     * A triage looks for the test the core already wrote and switched off.
     *
     * `D-KNW-064` priced the tool this could have been and did not build it: a
     * core checkout carries nine such assertions, in four files of one
     * subsystem, which is one grep rather than an index. What the skill owes
     * instead is the step and the pattern — and the warning that
     * `markTestSkipped` is mostly the machine, since fifty of those against two
     * about a defect is a ratio that sends a session reading the wrong fifty.
     */
    #[Decision('D-KNW-064')]
    #[Test]
    public function aTriageLooksForTheAssertionTheSuiteAlreadyCarries(): void
    {
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-issue-triage/SKILL.md',
        ));

        self::assertStringContainsString(
            'look for the one the core already wrote and switched off',
            $skill,
        );
        self::assertStringContainsString('grep -rn "@todo" <sysext>/Tests', $skill);
        self::assertStringContainsString('`markTestSkipped` is a different thing', $skill);
        // Before the test-writing it replaces, or it is advice arriving after
        // the work it would have saved.
        self::assertLessThan(
            strpos($skill, 'That test is a throwaway until a patch adopts it'),
            strpos($skill, 'look for the one the core already wrote'),
            'the search stands after the test it exists to avoid',
        );
    }

    /**
     * The triage skill's description promises "deciding whether a report is
     * worth taking on, and for saying what a maintainer would need before it
     * can move", and its body stopped at the verdict.
     *
     * The session that reported it had worked the procedure out and would have
     * worked it out again. What makes it a step rather than a note is that the
     * trigger is in the answer now: a relation carries its subject and `reviews`
     * names the changes the journal mentions, so a merged-then-reverted history
     * is visible before the checkout is opened (`R-ANS-029`).
     */
    #[Decision('D-SKL-028')]
    #[Test]
    public function aTriageSaysWhatThePreviousAttemptCostBeforeItHandsOver(): void
    {
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-issue-triage/SKILL.md',
        ));

        // The general form, which is the part that transfers off this issue.
        self::assertStringContainsString(
            'A reverted core fix becomes re-attemptable when the shared consumer that made it expensive has been '
                . 'rebuilt, or when the caller set has shrunk to the one site the fix needs',
            $skill,
        );
        // The trigger, read off the answer rather than out of the reading.
        self::assertStringContainsString('A relation marked `precedes` or `duplicates` carries its subject', $skill);
        self::assertStringContainsString('`reviews` names every change the journal mentions', $skill);
        // And the boundary, because the step sits directly above the handoff
        // and a skill that starts designing here has taken the next one's work.
        self::assertStringContainsString('It is not a design and not a patch', $skill);
        // What the attempt is reached by. A second lookup of a change the issue
        // answer already carried returns its state a second time and never the
        // diff the step says it is after, so the step routes to the page the
        // fetch is on instead (`D-SKL-028`).
        self::assertGreaterThan(
            strpos($skill, 'What a previous attempt cost'),
            strpos($skill, 'typo3://guides/core/contribution/gerrit-workflow'),
            'the step does not route to the page the fetch is on',
        );
        self::assertLessThan(
            strpos($skill, 'Where the triage ends and the patch begins'),
            strpos($skill, 'What a previous attempt cost'),
            'the step stands after the handoff it feeds',
        );
    }

    /**
     * The three rules that decide whether a measurement measured anything, and
     * the sentence that sends a reproduction to be shown red first.
     *
     * Five reports credit them and none of them rested on anything:
     * `2026-08-08-224426`, which reported every suite result with what it
     * inspected rather than with the SUCCESS banner and wrote its functional
     * test red before touching `GifBuilder.php`, and `2026-08-05-033954`,
     * `2026-08-07-065401`, `2026-08-07-130037` and `2026-08-07-233418` in the
     * archive. The third bullet is itself the answer to the last of those, and
     * a rewrite could have taken all four out without a failure.
     *
     * The block is one point in three costumes — an operation that silently did
     * nothing, followed by a result that reads as evidence — so it is held as
     * one test. `2026-08-08-224426` credits `references/base.md` for two of
     * them; the installed copy is byte-identical to `skills/base.md` and
     * carries neither, which is why they are looked for here.
     */
    #[Test]
    public function aTriageIsHeldToWhatItsMeasurementsActuallyMeasured(): void
    {
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-issue-triage/SKILL.md',
        ));

        self::assertStringContainsString('It has to be seen failing before it is believed', $skill);
        self::assertStringContainsString('A green that ran over no files is not a green.', $skill);
        self::assertStringContainsString(
            'confirm it inspected something — the count of tests or files it names',
            $skill,
            'the green is refused without what makes one real',
        );
        self::assertStringContainsString('Once the change is committed, `git stash` measures nothing.', $skill);
    }

    /**
     * A triage that ends an issue hands over the comment it is closed with.
     *
     * What the wording states was read off the tracker and the core's own
     * contribution guide on 2026-08-24, not recalled. Forge's status list
     * carries no resolution field and three statuses that close — Resolved,
     * Closed and Rejected — the guide ties Resolved to a patch merged under the
     * issue's own number and Closed to a report that no longer reproduces, and
     * `Resolves:` closes on merge for a feature and a task alone, which is why a
     * fixed bug is still open and needs the comment at all. The maintainer's own
     * closings that day name the change, the commit and the change number per
     * branch, and the first release each is in. `body[data-text-formatting]` on
     * forge.typo3.org says `textile`, so the block that is pasted there is not
     * the markdown the report around it is.
     */
    #[Decision('D-SKL-073')]
    #[Test]
    public function aVerdictThatEndsTheIssueCarriesTheCommentThatClosesIt(): void
    {
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-issue-triage/SKILL.md',
        ));

        self::assertStringContainsString(
            '**A verdict that ends the issue carries the comment it is closed with.**',
            $skill,
        );
        // The markup, because the answer's own form is markdown and the block
        // that leaves it is read by something else.
        self::assertStringContainsString('Forge is not markdown', $skill);
        // The boundary the deliverable does not move: the text is the triage's
        // and the act is not.
        self::assertStringContainsString('Filing it is the maintainer\'s act', $skill);

        $checklist = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-issue-triage/references/checklist.md',
        ));

        self::assertStringContainsString('# The comment that closes it', $checklist);
        // The three that owe it, and the three that do not — a closing written
        // for a verdict that should have asked the reporter a question is the
        // trap this deliverable brings with it.
        self::assertStringContainsString(
            '**Gone**, **Superseded** and **Not a defect** end the issue',
            $checklist,
        );
        self::assertStringContainsString('The other three hand over nothing', $checklist);
        self::assertStringContainsString(
            '**A security defect** owes the tracker nothing',
            $checklist,
            'the verdict that withholds the answer is offered a closing comment',
        );

        // What the comment carries that the status cannot, and where the release
        // it names comes from.
        self::assertStringContainsString('Forge carries no resolution field', $checklist);
        self::assertStringContainsString('`git tag --contains <commit>`', $checklist);
        self::assertStringContainsString(
            'The `Releases:` trailer names the branches the change was written for',
            $checklist,
        );
        self::assertStringContainsString('**Resolved** where the merged patch was filed under this issue', $checklist);
        self::assertStringContainsString('**Rejected** where the branch behaves as the project intends', $checklist);
        // Why an issue whose fix is merged and named is still sitting there.
        self::assertStringContainsString(
            'A merged patch closes its own issue for a feature and a task, and not for a bugfix',
            $checklist,
        );
        self::assertStringContainsString('renders Textile rather than Markdown', $checklist);

        // The sentence that was read as a stop before the text as well as
        // before the act, gone rather than softened.
        self::assertStringNotContainsString('the triage supplies what it rests on and stops', $checklist);
        self::assertStringContainsString('Whether the verdict ends the issue', $checklist);
    }

    /**
     * `R-SKL-020`. Both core workflows end in public and neither carried a branch
     * for the case where the finding is a security defect: the failure is not
     * that the session judges wrong, it is that nothing asks the question, so
     * the finding is disclosed by the step that was meant to report it.
     *
     * What the process asks for is read rather than recalled: `SECURITY.md` is
     * identical on all four branches apart from its supported-version matrix,
     * and it names one address for a core defect and an extension defect alike.
     * That address is what a published skill may not carry, because a contact
     * route is the fact that moves.
     */
    #[Requirement('R-SKL-020')]
    #[Test]
    public function aWorkflowThatEndsInPublicationStopsAtAVulnerability(): void
    {
        foreach (['typo3-core-issue-triage', 'typo3-core-patch-development'] as $name) {
            $skill = self::flat((string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md'));

            // Asked of every finding, because a rule that fires on how alarming
            // something looks fires on the findings that were never the danger.
            self::assertStringContainsString(
                '## Where the finding is a vulnerability',
                $skill,
                $name . ' names no stopping point for a finding that is a vulnerability',
            );
            self::assertStringContainsString(
                'happens to look alarming',
                $skill,
                $name . ' leaves the question to whether a finding looks alarming',
            );
            // The crossing in the sense `R-SKL-003` fixes: the verified stopping
            // point named, and the public step not taken.
            self::assertStringContainsString(
                'The stopping point is the verified reproduction',
                $skill,
                $name . ' stops without saying what has been established',
            );
            // And where it goes instead, as a call rather than as a fact.
            self::assertStringContainsString(
                '`documentId="any/security/reporting-a-vulnerability"`',
                $skill,
                $name . ' names no procedure for the report it hands over to',
            );
            self::assertStringNotContainsString(
                'security@',
                $skill,
                $name . ' carries the contact route the lookup owns',
            );
        }

        self::assertContains(
            'any/security/reporting-a-vulnerability',
            array_column(Documents::documents(), 'id'),
            'both skills route to a procedure this server does not carry',
        );

        // The triage judges, so the question is one of its verdicts rather than
        // a paragraph beside them — and it is the one asked first, because it
        // decides where the answer goes rather than what it says.
        $checklist = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-issue-triage/references/checklist.md',
        ));
        self::assertStringContainsString('## A security defect', $checklist);
        self::assertStringContainsString('The seventh is asked before the other six', $checklist);
        self::assertStringContainsString(
            'decides where the answer goes rather than what it says',
            $checklist,
        );
        // The trap this verdict has and the six others do not: waiting until the
        // finding is certain is itself the disclosure.
        self::assertStringContainsString('A finding that might be exploitable is one the team rates', $checklist);
    }

    /**
     * The mirror of the one above, on the skill that writes the patch instead
     * of judging it. `D-SKL-008` put both calls into the review and recorded, as
     * its own evidence, that development routed to neither — and the session
     * that can still be spared the work is the one about to write the code
     * (`D-SKL-010`).
     */
    #[Requirement('R-SKL-016')]
    #[Decision('D-SKL-010')]
    #[Test]
    public function theAssessmentBeforeAPatchReadsTheIssueAndTheReviewServer(): void
    {
        // Four sessions in one week ran both calls by hand
        // (`feedback/2026-08-02-144511`, `144848`, `145217`, `145230`), and the
        // fifth filed the assessment method it had to rediscover
        // (`feedback/2026-08-02-145128`). Measured again through this branch's
        // server on 2026-08-03: `typo3_forge_lookup` with issue 105403 answers
        // `Under Review` at `next-patchlevel` against the "closing as lack of
        // feedback" the notes carry, and its relations name #99203, whose entry
        // is what gave the resource ViewHelper its cache-busting argument. The
        // route the feedback took to that fact was a Forge search on the feature
        // wording, and a `typo3_changelog_lookup` for it misses, because the
        // entry is titled for something else (`D-ANS-030`).
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-development/SKILL.md',
        ));

        self::assertStringContainsString('`typo3_forge_lookup` with the issue number', $skill);
        // What the description does not carry, which is the reason the call is
        // here rather than a reading of the report.
        self::assertStringContainsString('status and target version as they stand today', $skill);
        self::assertStringContainsString('**relations**, which are one hop from the change that introduced', $skill);
        self::assertStringContainsString('**notes**, where a maintainer said why', $skill);

        // `typo3_gerrit_lookup` with issue 105403 answers empty from a checkout
        // that holds a patch for exactly that issue, because it was pushed
        // unlisted — so the empty answer is about the review server and not
        // about the world (`D-ANS-033`), and the order that reads it otherwise
        // has been misled by a true statement.
        self::assertStringContainsString('`typo3_gerrit_lookup` with the same issue number', $skill);
        self::assertStringContainsString('**before any code is written**', $skill);
        self::assertStringContainsString('nothing public names the issue rather than that nobody has fixed it', $skill);
        // Before the code, because the outcome that cancels the work is worth
        // nothing once the work is done.
        self::assertLessThan(
            strpos($skill, '## Make the change'),
            strpos($skill, 'typo3_gerrit_lookup'),
        );

        // The three rungs. Each one changed what the filing session concluded,
        // and none is carried by the order the skill had before.
        self::assertStringContainsString('check that blocker against what the branch has today', $skill);
        self::assertStringContainsString(
            'The argument that carries a bugfix is the same inconsistency inside one version',
            $skill,
        );
        self::assertStringContainsString('Establish the blast radius here rather than meeting it while working', $skill);
        // It is an assessment step because it decides the change type, which
        // everything downstream is built on.
        self::assertStringContainsString('a change that has to announce itself, or a breaking one', $skill);
    }

    /**
     * The scope half of the same assessment. `R-SKL-016` has the notes read for
     * the status, the relations and the maintainer's reason, and the list of
     * what the issue requires is the fourth thing they carry (`D-SKL-075`).
     */
    #[Requirement('R-SKL-027')]
    #[Decision('D-SKL-075')]
    #[Test]
    public function aPatchCoversEveryPointItsIssueLists(): void
    {
        // Forge #106584 with this skill active: its subject names two
        // ViewHelpers, note 3 names three and note 5 confirms them, and the
        // session shipped `href` and `src` and reported `f:image` `alt` as a
        // follow-up "needing its own issue". Re-run on 2026-08-25,
        // `typo3_forge_lookup issue=106584 notes=people` answers all three, so
        // the third point reached the caller in one call and did — what was
        // missing is what the order says to do with it, and the correction came
        // from the user (`feedback/2026-08-24-162543`).
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-development/SKILL.md',
        ));

        // The list is made where it is read, as a third act of the step that
        // reads the issue rather than as a rung of its own: `D-SKL-010`'s
        // **Wrong if** is that a skill growing a sentence per feedback stops
        // being an order.
        self::assertStringContainsString(
            '**Enumerate the points the issue requires, the ones only a comment names included.**',
            $skill,
        );
        // Both outcomes, because only one of the two was the failure.
        self::assertStringContainsString(
            'One patch covers all of them, or each point it leaves is given an issue of its own here, '
            . 'before any code',
            $skill,
        );
        // Why the split cannot wait until the patch is written: each part needs
        // a number, and `core/contribution/commit-messages` and
        // `core/contribution/changelog` each demand one.
        self::assertStringContainsString(
            'the `Resolves:` trailer and the changelog file name each take one',
            $skill,
        );
        // Risk is what the filing session dropped the third point for.
        self::assertStringContainsString(
            'riskier to change is an argument for giving it its own issue rather than for dropping it',
            $skill,
        );
        self::assertLessThan(
            strpos($skill, '## Make the change'),
            strpos($skill, '**Enumerate the points the issue requires'),
        );

        // "Keep the patch one change" is read at the moment the narrowing is
        // decided and argues for it there, so the other direction stands beside
        // it and not in the assessment alone.
        $narrowing = strpos($skill, 'That narrows the work and never the points the issue lists');
        self::assertNotFalse($narrowing, 'the skill does not say what keeping the patch one change narrows');
        self::assertGreaterThan((int) strpos($skill, 'Keep the patch one change'), $narrowing);
        // What a silent narrowing costs, which nothing outside the session sees.
        self::assertStringContainsString(
            'closes the issue on every point it names and nobody reopens a closed one',
            $skill,
        );
    }

    /**
     * The same section read for the other direction. `R-SKL-027` holds the
     * patch to the list its issue already carries; this one is for the list the
     * request grows once the patch is under way (`D-SKL-079`).
     */
    #[Requirement('R-SKL-028')]
    #[Decision('D-SKL-079')]
    #[Test]
    public function aWidenedRequestReEstablishesWhatThePatchIsAndWhatItOwes(): void
    {
        // Forge #93177 with this skill active: a task that grew eight times,
        // four re-derivations of its own scope, and two rounds of client-side
        // work plus a changelog entry thrown away. Both rules that would have
        // carried it are here and both are written for the assessment
        // (`feedback/2026-08-24-225243`).
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-development/SKILL.md',
        ));

        // The three, in the skill's own vocabulary rather than restated.
        self::assertStringContainsString(
            're-establish three things: what kind of change this is now, which branches it reaches, '
            . 'and what it owes',
            $skill,
        );
        // Before the widened part, because the cost is work already written.
        self::assertStringContainsString(
            'Do it before writing the widened part, and say which of the three moved',
            $skill,
        );
        // Pointers to where each was settled the first time: step 2, the
        // blast-radius paragraph, the changelog section.
        self::assertStringContainsString(
            'Step 2 settled the first, the blast radius the second and the changelog section the third',
            $skill,
        );
        self::assertStringContainsString('Carrying on re-derives none of them', $skill);
        // What a widening costs where it gains a subsystem, which is the two
        // discarded rounds in one sentence.
        self::assertStringContainsString(
            "gains that subsystem's build, its checks and its backport constraint with it",
            $skill,
        );

        // Beside "Keep the patch one change" rather than at the foot of the
        // section: a widening is a session deciding again what this patch is,
        // and the reporting session names that sentence as the one that fired.
        $widening = strpos($skill, 'Where the request widens after the patch is under way');
        self::assertNotFalse($widening, 'the skill does not say what a widened request re-establishes');
        self::assertGreaterThan(
            (int) strpos($skill, 'That narrows the work and never the points the issue lists'),
            $widening,
        );
        self::assertLessThan((int) strpos($skill, 'Find out whether the area is moving'), $widening);
    }

    #[Requirement('R-SKL-013')]
    #[Decision('D-SKL-007')]
    #[Test]
    public function aSurfaceReportedAsAssessedNamesWhatWasRead(): void
    {
        // The third disposition was the one that certified itself. Reporting a
        // finding and dropping a candidate both cost a reading somebody can
        // check; assessed cost one word, and a surface somebody glanced at read
        // exactly like one somebody worked through (`D-SKL-007`).
        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/references/checklist.md',
        ));

        self::assertStringContainsString('A review disposes of a thing in three ways', $checklist);
        self::assertStringContainsString('all three carry what backs them', $checklist);
        // Unassessed is the cheaper honest answer and costs the same line, which
        // is what keeps the demand from being answered with a fabricated one.
        self::assertStringContainsString('where the reading did not happen the word is unassessed', $checklist);
        // The clean verdict in the rubric is held to the same bar as a finding,
        // and says so where a reader ranking one would look.
        self::assertStringContainsString(
            'It names what was read, for the same reason a finding names what it collides with',
            $checklist,
        );
    }

    #[Decision('D-SKL-030')]
    #[Requirement('R-SKL-022')]
    #[Test]
    public function aReviewSurfaceNamesTheLookupThatCanAnswerIt(): void
    {
        // The surface was named for two things and listed one: every item under
        // **Documentation and changelog** was the changelog's, so a session
        // disposing of it found no manual in it and no lookup holding one. It
        // shipped the claim that a stdWrap property's page lives outside the
        // repository as the reason nothing was owed, and the page is one call
        // away (`D-SKL-030`).
        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/references/checklist.md',
        ));
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));

        self::assertStringContainsString('typo3_documentation_lookup', $checklist);
        // The half that is in the checkout and the half that is not, because the
        // two are disposed of differently and the surface names one word.
        self::assertStringContainsString(
            'a system extension\'s own `Documentation/` is in this checkout and changes in the patch',
            $checklist,
        );
        // What the shipped claim got wrong, in the words that answer it.
        self::assertStringContainsString(
            'outside is where the follow-up goes, not a reason none is owed',
            $checklist,
        );
        self::assertStringContainsString(
            'A review said the wording lived elsewhere and concluded that no documentation change was owed',
            $skill,
        );
        // The obligation itself stays with the document that owns it, so the
        // skill routes to it instead of restating what a patch owes a manual.
        self::assertStringContainsString('`typo3_rule_lookup` asked for `documentation`', $skill);
    }

    #[Requirement('R-SKL-012')]
    #[Decision('D-SKL-007')]
    #[Test]
    public function aFindingSaysWhetherThePatchIntroducedIt(): void
    {
        // A finding about a line the diff only moved past sends the author to
        // repair something they did not change, and a report that does it reads
        // exactly like one that meant to (`D-SKL-007`).
        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/references/checklist.md',
        ));

        self::assertStringContainsString('Every finding carries five things', $checklist);
        self::assertStringContainsString('**whether this patch introduced it**', $checklist);
        self::assertStringContainsString('What the patch did not introduce is reported in those words', $checklist);
        // The other half of attributing a finding: a diff is the weakest
        // evidence there is about who reaches a path, so what it shows may raise
        // a rank and never lower one.
        self::assertStringContainsString('raises a rank and never lowers one', $checklist);

        // A patch that is one of a set is read against the end of the set, by
        // opening the later patch rather than by believing a message about it.
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));
        self::assertStringContainsString('read against the state at the end of the set', $skill);
        self::assertStringContainsString('rather than of what a message promises about it', $skill);
    }

    #[Requirement('R-SKL-015')]
    #[Test]
    public function aRuleQuotedAtTheIssueIsVerifiedInTheCheckout(): void
    {
        // `feedback/2026-08-02-144814`: Forge #105403 was answered with "you
        // *must not* use f:image for anything but FAL resources", and the
        // session repeated it as correct in its own assessment until the user
        // asked what it made of the statement. The checkout says something
        // weaker on 12.4, 13.4, 14.3 and `main` alike — `ImageViewHelper`'s own
        // first example is an `EXT:` path, `SvgImageViewHelperTest` renders that
        // form with width, height, `crop` and `fileExtension`, and what both
        // docblocks warn about is stability rather than support (`D-KNW-043`).
        // The instructions sent a session to the checkout for what changed, for
        // the branch and for whether a path or an identifier still exists. A
        // behavioural rule was on none of those lists, and it is the claim that
        // closed the issue.
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-development/SKILL.md',
        ));

        // An act with an object rather than a disposition to be sceptical, which
        // is what the same position already cost once (`D-SKL-009`).
        self::assertStringContainsString('**Verify in the checkout every rule the issue quotes.**', $skill);
        // The claim is the same kind of thing as the two the base already sends
        // to the checkout, which is what makes it checkable there at all.
        self::assertStringContainsString('a claim, the way a path or an identifier is', $skill);
        // Named surfaces, because the docblock and the test are where the two
        // neighbouring ViewHelpers of that report differ.
        self::assertStringContainsString("the class it names, its docblock and the core's own tests", $skill);
        // The three strengths, and the report saying which one it found.
        self::assertStringContainsString('say which of the three carries the rule', $skill);
        self::assertStringContainsString('Carry it at the strength its own source puts on it', $skill);
        // Before the reproduction: a rule read as a prohibition ends the
        // assessment before anything is reproduced, which is what happened.
        self::assertLessThan(
            strpos($skill, '**Reproduce against the branch you are fixing**'),
            strpos($skill, '**Verify in the checkout every rule the issue quotes.**'),
        );
    }

    #[Test]
    public function aClosedIssueIsReadForWhatTheConversationDecided(): void
    {
        // `feedback/2026-08-02-144800` is a session that read Forge #105403 as
        // settled because a maintainer had closed it, called the report
        // "teilweise valide", and committed a better exception message — a
        // politer way of telling the reporter they were holding it wrong. The
        // user rejected that framing twice before the work moved. Both misreads
        // were of the same two things: a closure for lack of feedback over
        // sixteen months, which says what the exchange did rather than what the
        // need is worth, and a maintainer's alternative that drops width, height
        // and cropping, which is why the reporter had wrapped one ViewHelper in
        // the other. The step already said the comments can be product judgement
        // and the session read it, so what is added is stated the way
        // `D-SKL-009` holds a rule that gets read and not followed: an act with
        // an object, producing something the assessment carries.
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-development/SKILL.md',
        ));

        self::assertStringContainsString(
            '**Read the closure reason and the target version for what the conversation decided, '
            . 'and write that down rather than what the report is worth.**',
            $skill,
        );
        // The reading that was actually available in the report: silence is
        // evidence about the answer as much as about the reporter.
        self::assertStringContainsString(
            'as consistent with an answer the reporter could not use as with the reporter giving up',
            $skill,
        );
        self::assertStringContainsString('a closed issue is not a finding that the need is absent', $skill);

        self::assertStringContainsString(
            '**Where a comment names an alternative, write out what the alternative drops '
            . 'against what the reported code did.**',
            $skill,
        );
        // What the writing-out is over, so that "it is not the same thing" is
        // not an answer to it.
        self::assertStringContainsString(
            'Name the arguments and the behaviour the reported code had and the replacement does not',
            $skill,
        );
        self::assertStringContainsString('closes an issue only if it does the same work', $skill);

        // The step is the wording of the reading and not a section of its own,
        // so it stays inside "Establish the issue before you believe it" and
        // ahead of the reproduction.
        $establish = strpos($skill, '## Establish the issue before you believe it');
        $closure = strpos($skill, '**Read the closure reason and the target version');
        $reproduce = strpos($skill, '**Reproduce against the branch you are fixing**');
        self::assertNotFalse($establish);
        self::assertNotFalse($closure);
        self::assertNotFalse($reproduce);
        self::assertLessThan($closure, $establish);
        self::assertLessThan($reproduce, $closure);
    }

    #[Requirement('R-SKL-005')]
    #[Requirement('R-SKL-007')]
    #[Decision('D-SKL-055')]
    #[Test]
    public function everySkillStartsFromTheBaseBeforeItsOwnEvidence(): void
    {
        foreach (self::skills() as $name => $skill) {
            $body = self::flat($skill);
            $base = strpos($body, '[references/base.md](references/base.md)');
            self::assertNotFalse($base, $name . ' does not route through the base');

            $first = self::ROUTING_SKILLS[$name][0] ?? null;
            self::assertNotNull($first, $name . ' has no routing of its own recorded');
            // The first routing and not the first mention: a step that
            // discharges a call stands where a routing would, and taking it for
            // one is what let the base be read as established by the sentence
            // saying the base had already answered — `D-SKL-055`.
            $routing = self::routing($body, $first);
            self::assertNotFalse($routing, $name . ' routes to ' . $first . ' nowhere');
            self::assertLessThan(
                $routing,
                $base,
                $name . ' reaches for its own tools before the base is established',
            );
        }
    }

    #[Requirement('R-SKL-006')]
    #[Test]
    public function theAuthoringContractIsWrittenDownAndNamesWhatHoldsIt(): void
    {
        // How a skill is written was the half nothing held: the order a task
        // runs in is one file since 2026-07-31, while the rules that hold for a
        // skill because it *is* one lived in these assertions and in five
        // skills restating them in their own words. The page is the written
        // form; this holds the two to each other in both directions, so a rule
        // stated there with nothing behind it and an assertion added here that
        // nobody wrote down each fail. A skill is published into somebody
        // else's project, so the rules it is written under are the half no
        // forward run can measure — a run grades the answer, never the file.
        $page = (string) file_get_contents(Paths::root() . '/documentation/contributing/writing-a-skill.rst');

        self::assertNotSame(
            0,
            preg_match_all('/`SkillTest::(\w+)`/', $page, $matches),
            'the authoring contract names no test that holds it',
        );
        $named = array_unique($matches[1]);

        foreach ($named as $test) {
            self::assertTrue(
                method_exists(self::class, $test),
                'the authoring contract names ' . $test . ', which does not exist',
            );
        }

        $source = file(__FILE__) ?: [];
        $itself = __FUNCTION__;
        foreach ((new \ReflectionClass(self::class))->getMethods() as $method) {
            if ($method->getName() === $itself || $method->getFileName() !== __FILE__) {
                continue;
            }
            $body = implode('', array_slice(
                $source,
                $method->getStartLine() - 1,
                $method->getEndLine() - $method->getStartLine() + 1,
            ));
            // The assertions that run over the directory rather than over one
            // named skill are exactly the ones a skill written later is held to
            // without ever seeing them, which is why they are the ones the page
            // has to carry.
            if (!str_contains($body, 'self::skills()') && !str_contains($body, 'self::ROUTING_SKILLS')) {
                continue;
            }
            self::assertContains(
                $method->getName(),
                $named,
                $method->getName() . ' holds every skill and is written down nowhere',
            );
        }
    }

    /**
     * What holds for a skill because it is one, rather than because of what it
     * is about. These run over the directory, so a skill added later is held to
     * them without anybody adding it to a list here — which is the point: the
     * list is what a new skill is written without ever seeing. They are the
     * ones [documentation/contributing/writing-a-skill.rst](../../documentation/contributing/writing-a-skill.rst)
     * states, and the test above holds that page and this set to each other.
     */
    #[Test]
    public function everySkillIsPublishedUnderTheNameItCallsItself(): void
    {
        foreach (self::skills() as $name => $skill) {
            self::assertStringContainsString("\nname: " . $name . "\n", $skill, $name . ' is filed under another name');
            self::assertMatchesRegularExpression(
                '/\ndescription: \S.{40,}\n/',
                $skill,
                $name . ' has no description a client could route on',
            );
        }
    }

    /**
     * A skill copied out of this repository arrives without the server it
     * routes to. `references/base.md` is written at publication rather than
     * kept here, so the first instruction of a copied skill is a link to
     * nothing and every lookup under it is a tool the session does not have.
     * The guard in the base is written for a session whose tools do not answer;
     * this is a session whose base was never delivered, and nothing in the file
     * it holds says so.
     *
     * `compatibility` is where the standard has a skill state an environment
     * requirement — optional, one to 500 characters, read on agentskills.io on
     * 2026-08-08. One line, and the same line in every skill, because what it
     * states is a fact about this package rather than about a workflow.
     *
     * It is read out of parsed front matter rather than matched in the file,
     * because a field a reader cannot parse is stated to nobody: three
     * descriptions carried an unquoted `: ` and broke the whole block for
     * every reader but this repository's own patterns.
     */
    #[Test]
    public function everySkillSaysWhichServerItNeeds(): void
    {
        $stated = [];
        foreach (self::skills() as $name => $skill) {
            $compatibility = self::frontMatter($name, $skill)['compatibility'] ?? null;
            self::assertIsString($compatibility, $name . ' does not say which server it needs');
            $stated[$name] = $compatibility;
        }
        self::assertNotSame([], $stated);

        $one = (string) reset($stated);
        foreach ($stated as $name => $compatibility) {
            self::assertSame($one, $compatibility, $name . ' says it in words of its own');
            // The standard's own bound, and a reader refuses the file over it
            // rather than truncating the line.
            self::assertLessThanOrEqual(500, strlen($compatibility), $name . ' says more than the field holds');
            self::assertStringContainsString(
                'typo3-dev-companion install',
                $compatibility,
                $name . ' names the server without saying how it is installed',
            );
            self::assertStringContainsString(
                'references/base.md',
                $compatibility,
                $name . ' leaves out the file a copied tree does not carry',
            );
        }
    }

    /**
     * The front matter carries the standard's fields and nothing else.
     *
     * `ALLOWED_FIELDS` in the reference validator is exactly `name`,
     * `description`, `license`, `compatibility`, `metadata` and
     * `allowed-tools`, and a key outside them is an error rather than a
     * warning: "Unexpected fields in frontmatter" — read on 2026-08-08. So a
     * key invented here is not a field a client ignores, it is a file a client
     * refuses, in somebody else's project where no release of this server
     * corrects it.
     *
     * The set is closed rather than checked one field at a time because the
     * failure is the key nobody thought about. `status` was the one that got
     * in, and it got in beside a test that read one field out of the block and
     * let every other one through — `D-SKL-087`, which is where that decision
     * went when the key it was made for came out.
     */
    #[Decision('D-SKL-087')]
    #[Test]
    public function everyFrontMatterFieldIsOneTheStandardDefines(): void
    {
        $defined = ['name', 'description', 'license', 'compatibility', 'metadata', 'allowed-tools'];

        foreach (self::skills() as $name => $skill) {
            $matter = self::frontMatter($name, $skill);
            self::assertSame(
                [],
                array_diff(array_keys($matter), $defined),
                $name . ' carries a field the standard does not define',
            );

            // The one field the standard leaves open is a map of strings, so a
            // client that reads it gets what this server wrote there.
            $metadata = $matter['metadata'] ?? [];
            self::assertIsArray($metadata, $name . ' has a metadata field that is not a mapping');
            foreach ($metadata as $key => $value) {
                self::assertIsString($key, $name . ' has a metadata key that is not a string');
                self::assertIsString($value, $name . ' has a metadata value that is not a string');
            }
        }
    }

    /**
     * A skill that reads a project's pins checks them against the day's
     * release.
     *
     * A version written into a skill is not corrected by the next release of
     * this server: the file sits in somebody else's project, and a project
     * following it stays pinned to whatever was current when the skill was
     * published — `R-SKL-029`. So the check is made on the day, against where
     * the release is published, and what the project itself declares is what
     * can speak against the raise.
     */
    #[Requirement('R-SKL-029')]
    #[Test]
    public function everySkillThatReadsAPinChecksItAgainstTheDaysRelease(): void
    {
        $reading = [
            'typo3-extension-asset-build' => 'SKILL.md',
            'typo3-development-installation' => 'SKILL.md',
            'typo3-extension-health' => 'references/checklist.md',
        ];

        foreach ($reading as $skill => $file) {
            $body = self::flat((string) file_get_contents(
                Paths::root() . '/skills/' . $skill . '/' . $file,
            ));
            self::assertStringContainsString(
                'against the release current on the day',
                $body,
                $skill . ' reads a pin and never says what it is measured against',
            );
            // The raise is offered rather than taken, and the project's own
            // bound is what can refuse it.
            self::assertStringContainsString('a finding carrying the raise', $body, $skill . ' raises rather than reports');
            self::assertStringContainsString('speaks against', $body, $skill . ' names nothing that can refuse the raise');
        }
    }

    /**
     * A skill exists for its readers as soon as its directory does.
     *
     * `Installer` carried a list of the published names once, and a declaration
     * in each file holding one back after that. Both are a second place one
     * fact lives, and both disagree in the direction nobody notices: a workflow
     * loadable by nobody, or one published while it reads as unfinished. This
     * holds what is left — `D-SKL-087`.
     */
    /**
     * `skills/base.md` is the shared start of a task, and nothing is the shared
     * ending: what holds wherever a workflow closes — the stop at a
     * vulnerability, the form of a report — is written into each body that
     * needs it. Two copies are cheaper than every other skill paying for a
     * rule that holds for two, and a third is where that stops being true and
     * the paragraph gets a home — `D-SKL-088`.
     *
     * The pointer at the base is excluded, because every skill carries it by
     * contract.
     */
    #[Decision('D-SKL-088')]
    #[Test]
    public function aParagraphThreeSkillsShareStopsBeingCopied(): void
    {
        $carriers = [];
        foreach (self::skills() as $name => $skill) {
            foreach (preg_split('/\R\s*\R/', $skill) ?: [] as $paragraph) {
                $flat = trim(self::flat($paragraph));
                if (strlen($flat) < 120 || str_contains($flat, 'references/base.md')) {
                    continue;
                }
                $carriers[$flat][$name] = $name;
            }
        }

        foreach ($carriers as $paragraph => $names) {
            self::assertLessThan(
                3,
                count($names),
                'the same paragraph stands in ' . implode(', ', $names) . ': ' . substr($paragraph, 0, 80),
            );
        }
    }

    #[Decision('D-SKL-087')]
    #[Test]
    public function everySkillInTheDirectoryIsPublished(): void
    {
        self::assertSame(array_keys(self::skills()), Installer::skills());
    }

    /**
     * The description is the only part of a skill read before it is chosen, so
     * a domain named by one of its sides leaves the other side reading as
     * somebody else's work — and the body that covers it is never loaded. This
     * is the one collision that has been measured; the shape it stands for is
     * written down in the authoring contract rather than assertable over the
     * directory, because which sides a skill owns is not in the file.
     */
    #[Decision('D-SKL-024')]
    #[Requirement('R-SKL-010')]
    #[Test]
    public function aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement(): void
    {
        // A session in `site-new` wrote a custom backend preview for a content
        // element on 2026-08-01, activated no skill and called no tool, and did
        // the work by reading vendor code — a day after the entry point reached
        // the instructions, so the channel that failed was the descriptions.
        // The task matched one word in each: the content-element skill opened on
        // "frontend content elements" with `previews` ninth of the eleven things
        // it listed, and the module skill promised "backend UI work". It belongs
        // wholly to the first, which covers it in as many words and which
        // `knowledge/task-intents.json` has matched on `backend preview` since
        // 51e5e5a.
        $element = self::description('typo3-content-element-development');
        self::assertStringNotContainsString('frontend content elements', $element);
        self::assertStringContainsString('backend preview', $element);
        self::assertStringContainsString('page module', $element);

        // And the other half of the collision: the module skill claimed the
        // whole backend and owns one room of it.
        $module = self::description('typo3-backend-module-development');
        self::assertStringNotContainsString('other TYPO3 backend UI work', $module);
        self::assertStringContainsString('is not a module', $module);
        // The crossing in its body says the same, or the file contradicts its
        // own description in somebody else's project. Read flat, because what is
        // asserted is the sentence and `prose:format` decides where its lines
        // break.
        self::assertStringContainsString(
            'before implementing a content element or its backend preview',
            self::flat((string) file_get_contents(
                Paths::root() . '/skills/typo3-backend-module-development/SKILL.md',
            )),
        );
    }

    /**
     * The same shape, measured here rather than borrowed. A session asked to
     * review a change in a git worktree read `typo3-core-patch-checkout`'s
     * description as another skill's case and did the work by hand
     * (`D-SKL-024`). A step clause does not only summarise the body, it narrows
     * what the description names, and a budget trim is what cut this one before
     * it was written back (`D-SKL-026`). So both halves are held: the word a
     * user types, and the body that has to answer once it did.
     */
    #[Decision('D-SKL-024')]
    #[Test]
    public function aWorktreeTaskMatchesTheSkillThatOwnsTheCheckout(): void
    {
        $checkout = self::description('typo3-core-patch-checkout');
        self::assertStringContainsString('git worktree beside it', $checkout);
        self::assertStringNotContainsString('find the change, fetch the patch set', $checkout);

        $body = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-checkout/SKILL.md',
        ));
        self::assertStringContainsString('## Four ways in', $body);
        // What the worktree path costs and the branch path does not, which is
        // the half a trigger alone would route a task into a body without.
        self::assertStringContainsString('no suite runs in it until they are installed there', $body);
    }

    /**
     * `D-KNW-129`. Two published files disagreed about whether a patch set may
     * be opened on somebody else's change: `typo3-core-patch-development`
     * routed the case and this skill's checklist ruled it out, and the session
     * of 2026-08-25 read the second and worked the whole task out alone.
     *
     * The four are held together because they were one gap seen from four
     * places — the way in, the precondition that read the local work as an
     * obstacle, the file rule the carry needs, and the ending the mandatory
     * undo allowed for nowhere. A route whose checklist still forbids it is the
     * same contradiction in a longer file.
     */
    #[Decision('D-KNW-129')]
    #[Test]
    public function extendingSomebodyElsesChangeIsAWayIntoTheCheckout(): void
    {
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-checkout/SKILL.md',
        ));
        $checklist = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-checkout/references/checklist.md',
        ));

        // Without the apostrophe: the description is read as the YAML scalar it
        // is written as, where a single quote stands doubled.
        self::assertStringContainsString('the base for extending somebody else', self::description(
            'typo3-core-patch-checkout',
        ));
        self::assertStringContainsString('## Carry your own work onto the patch set', $skill);
        self::assertStringContainsString('On the fourth way in it is the material instead', $skill);
        self::assertStringContainsString('There is one ending it does not run for', $skill);

        self::assertStringContainsString('## Taking your own work onto the patch', $checklist);
        self::assertStringNotContainsString(
            'opening a patch set in somebody else\'s name',
            $checklist,
            'the clause that stopped the session still forbids what the skill now routes',
        );
        self::assertStringContainsString('it is a normal move', $checklist);
    }

    /**
     * The same mechanism a third time, and the premise rather than a step clause
     * — `D-SKL-061`. So both halves are held here too: the premise a defect
     * inside a declared range meets, and the step the body owes it once it did.
     */
    #[Requirement('R-SKL-007')]
    #[Decision('D-SKL-061')]
    #[Test]
    public function aDefectInsideTheDeclaredRangeMatchesTheRemovalSkill(): void
    {
        $upgrade = self::description('typo3-extension-upgrade');
        self::assertStringNotContainsString('from the TYPO3 and PHP versions it supports today', $upgrade);
        self::assertStringContainsString('working on the TYPO3 and PHP versions it declares', $upgrade);
        self::assertStringContainsString('code broken by what a supported major deprecated or removed', $upgrade);

        // A description widened alone would route the defect into a body whose
        // opening reads as somebody else's case and whose third step asks which
        // range to cross to.
        $body = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-upgrade/SKILL.md',
        ));
        self::assertStringContainsString('A package is broken by what a major it already supports removed', $body);
        self::assertStringContainsString('Where nothing is being crossed, that range is the declared one', $body);
    }

    /**
     * The fourth sighting, and a job rather than a step or a premise. Three
     * trims read "find the candidates in the backlog" as a summary of the body
     * and took it out, and a session that searched the backlog six times opened
     * nothing (`D-SKL-076`). The body hands that list over as a deliverable of
     * its own, so both halves are held: the words a backlog request arrives in,
     * and the section that has to answer once they did.
     */
    #[Decision('D-SKL-076')]
    #[Test]
    public function aBacklogSearchMatchesTheSkillThatOwnsTheCandidates(): void
    {
        $triage = self::description('typo3-core-issue-triage');
        self::assertStringContainsString('backlog', $triage);
        self::assertStringContainsString('worth working on', $triage);

        // Named after the job it was read as a case of, the backlog request
        // meets a premise it does not hold — one issue somebody already has,
        // which is `D-SKL-061`'s failure and not a wording.
        self::assertLessThan(
            (int) mb_strpos($triage, 'still true'),
            (int) mb_strpos($triage, 'backlog'),
            'the triage description names the backlog after the issue it is read as a case of',
        );

        $body = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-issue-triage/SKILL.md',
        ));
        self::assertStringContainsString('## Find the candidates', $body);
        // What makes that section a job rather than a first step, and the half
        // a trigger alone would route a backlog request into a body without.
        self::assertStringContainsString(
            'Triaging a backlog and triaging an issue are two different jobs',
            $body,
        );
    }

    /**
     * The same request in the words a contributor uses for the tracker, which
     * has two gates to clear rather than one.
     *
     * `Scope::CORE_WORK` carried the word with a trailing space, so "from
     * Forge," was no core work at all, and the needle was a weak one, which
     * names no skill (`D-SKL-023`). What routes it is taking work off the
     * tracker rather than naming it: bare `forge` in `match` reads an issue
     * somebody already chose as a triage too, and the second call is what that
     * would have cost — a brief for work that changes nothing, over work that
     * ends in a patch (`D-SKL-078`).
     */
    #[Decision('D-SKL-078')]
    #[Test]
    public function takingAnIssueOffTheTrackerReachesTheTriageSkill(): void
    {
        $triage = Registry::call('typo3_task_guide', [
            'task' => 'fetch another old issue from Forge, create a branch, work it off',
        ])->data;

        self::assertSame('core', $triage['scope']);
        self::assertContains('typo3-core-issue-triage', $triage['skills']);

        $patch = Registry::call('typo3_task_guide', [
            'task' => 'fix Forge 15984 in the FormEngine',
        ])->data;

        self::assertContains('Keep the patch focused on the stated task.', $patch['checklist']);
    }

    /**
     * A core change names its workflow however the task sentence is worded.
     *
     * `skills` is read off confirmed intents, and a caller who describes the
     * defect confirms none: with `changeType="bugfix"` on a `typo3/sysext/`
     * path, *add the missing language parameter to getMovedRecordsFromPages*
     * named no workflow at all, while *fix a bug in WorkspaceService* named the
     * patch one. The session that reported it had both core skills in its
     * listing and activated neither.
     */
    #[Decision('D-SKL-082')]
    #[Test]
    public function aCoreChangeNamesThePatchWorkflowWhateverTheSentenceSays(): void
    {
        $core = ['typo3/sysext/workspaces/Classes/Service/WorkspaceService.php'];

        foreach (['bugfix', 'task', 'feature'] as $changeType) {
            $brief = Registry::call('typo3_task_guide', [
                'task' => 'add the missing language parameter to getMovedRecordsFromPages',
                'changeType' => $changeType,
                'paths' => $core,
            ]);

            self::assertSame([], array_column($brief->data['intents'], 'id'), 'the sentence names no intent');
            self::assertSame(['typo3-core-patch-development'], $brief->data['skills'], $changeType);
        }

        // The two change types that read rather than write keep their own
        // workflow: both confirm an intent, so the fallback is never reached.
        foreach (['audit' => 'typo3-core-patch-review', 'triage' => 'typo3-core-issue-triage'] as $changeType => $skill) {
            self::assertSame([$skill], Registry::call('typo3_task_guide', [
                'task' => 'add the missing language parameter to getMovedRecordsFromPages',
                'changeType' => $changeType,
                'paths' => $core,
            ])->data['skills']);
        }

        // And nothing is claimed outside the core, where which workflow owns a
        // change to a package depends on what the package is.
        self::assertSame([], Registry::call('typo3_task_guide', [
            'task' => 'add the missing language parameter to getMovedRecordsFromPages',
            'changeType' => 'bugfix',
            'paths' => ['packages/blog/Classes/Domain/Repository/PostRepository.php'],
        ])->data['skills']);
    }

    /**
     * The same request when it says what it is going to do with the issue.
     *
     * "Please find 1 old forge issue and fix it" reached neither skill: the
     * needles that carry the tracker were weak, and the one brief on that list
     * that did match — `D-SKL-078`'s own worked example — was answered as work
     * that changes nothing and dropped the six items a patch owes, the commit
     * message among them (`D-SKL-081`).
     */
    #[Decision('D-SKL-081')]
    #[Test]
    public function aBriefThatTriagesAndThenFixesCarriesBothWorkflows(): void
    {
        $spanning = Registry::call('typo3_task_guide', [
            'task' => 'please find 1 old forge issue and fix it',
        ]);

        // Both, and in the order the work runs in: the front half establishes
        // what is still true and the second is where the patch is written.
        self::assertSame(
            ['typo3-core-issue-triage', 'typo3-core-patch-development'],
            $spanning->data['skills'],
        );

        // The preposition is the caller's and the needle list carries four of
        // them now. `feedback/2026-08-27-145332` wrote "in forge" and reached
        // the patch skill alone, having triaged seven issues by hand.
        self::assertSame(
            ['typo3-core-issue-triage', 'typo3-core-patch-development'],
            Registry::call('typo3_task_guide', [
                'task' => 'please search for 1 workspace bug in forge and fix it',
            ])->data['skills'],
        );
        self::assertStringContainsString(
            'typo3-core-issue-triage, typo3-core-patch-development — in that order',
            $spanning->text,
        );

        // The patch skeleton stays whatever else was recognized, and what the
        // reading half knows arrives as its own items beside it.
        foreach ([
            'Keep the patch focused on the stated task.',
            'Add or update the narrowest useful test coverage.',
        ] as $owed) {
            self::assertContains($owed, $spanning->data['checklist'], 'the brief dropped what the change owes');
        }
        self::assertStringContainsString(
            'Write the commit message with typo3_commit_message_guide',
            implode("\n", $spanning->data['checklist']),
        );
        self::assertStringContainsString(
            'A report is about the version it was filed against',
            implode("\n", $spanning->data['checklist']),
        );
        // And the line a brief that changes nothing opens with is gone, or the
        // answer tells the caller the steps above are not for this task.
        self::assertStringNotContainsString('This is a brief for work that changes nothing', $spanning->text);

        // `D-SKL-078`'s own worked example, which matched already and lost the
        // patch half to the fork.
        $worked = Registry::call('typo3_task_guide', [
            'task' => 'fetch another old issue from Forge, create a branch, work it off',
        ])->data;

        self::assertSame(['typo3-core-issue-triage', 'typo3-core-patch-development'], $worked['skills']);
        self::assertContains('Keep the patch focused on the stated task.', $worked['checklist']);

        // The second session's brief, which ends before any change: the backlog
        // search alone still routes the triage workflow and nothing else.
        $backlog = Registry::call('typo3_task_guide', [
            'task' => 'search forge issues in the asset renderer area',
        ])->data;

        self::assertSame(['typo3-core-issue-triage'], $backlog['skills']);
        self::assertNotContains('Keep the patch focused on the stated task.', $backlog['checklist']);
    }

    /**
     * The closing sentence of the triage description, which a session working a
     * task that ended in a patch read as an exclusion of the whole of it.
     *
     * "Writing or reviewing a patch is other work" stated in the description the
     * boundary `D-SKL-022` made an instruction in the body, and a description is
     * read before the body is loaded, so it arrived as a refusal
     * (`D-SKL-076`, entry of 2026-08-27).
     */
    #[Decision('D-SKL-076')]
    #[Test]
    public function aTaskEndingInAPatchIsNotSentAwayByTheTriageDescription(): void
    {
        $triage = self::description('typo3-core-issue-triage');

        self::assertStringNotContainsString('is other work', $triage);
        self::assertStringContainsString('A task that ends in a patch starts here', $triage);
        // Named, or the sentence takes the task off this skill without saying
        // where it goes — which is what the refusal did.
        self::assertStringContainsString('typo3-core-patch-development', $triage);

        // The body owes the same crossing at the moment it happens, or the
        // description promises a handover the file does not perform.
        $body = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-issue-triage/SKILL.md',
        ));
        self::assertStringContainsString(
            'invoke `typo3-core-patch-development` before making the change',
            $body,
        );
    }

    /**
     * What `typo3_task_guide`'s description opens with, against the file the
     * caller already has.
     *
     * A session in a core checkout loaded this tool's schema in its first call
     * and never made one, and names the opening as the reason: it promised a
     * checklist, and the core's own `AGENTS.md` was in context with the
     * test-first rule, the `runTests.sh` invocations and the commit conventions
     * already. What that file leaves general is what the opening now names —
     * `D-AUD-014`.
     */
    #[Decision('D-AUD-014')]
    #[Test]
    public function theBriefOpensWithWhatTheCheckoutsOwnConventionsCannotSay(): void
    {
        $description = TaskGuide::description();
        $opening = mb_substr($description, 0, (int) mb_strpos($description, '.') + 1);

        self::assertStringNotContainsString('Build a task checklist', $opening);
        self::assertStringContainsString('conventions file cannot', $opening);

        // The two the session had to guess, and each answered rather than
        // claimed: the changelog obligation in the brief a bugfix gets, the
        // release branches in the call that brief routes to.
        $bugfix = Registry::call('typo3_task_guide', [
            'task' => 'fix a null pointer in the data handler',
            'paths' => ['typo3/sysext/core/Classes/DataHandling/DataHandler.php'],
            'changeType' => 'bugfix',
        ])->data;

        $checklist = implode("\n", $bugfix['checklist']);
        self::assertStringContainsString('A bugfix owes a changelog entry only where', $checklist);
        self::assertStringContainsString('core/contribution/changelog', $checklist);
        self::assertContains('typo3_commit_message_guide', array_column($bugfix['nextTools'], 'tool'));

        $trailer = Registry::call('typo3_commit_message_guide', [
            'workflow' => 'core',
            'summary' => 'Fix a null pointer in the data handler',
        ])->text;
        self::assertStringContainsString('The lines that can take a patch at all are', $trailer);
    }

    /**
     * A description is held to a length of its own, and the listing is not held
     * to a total.
     *
     * The total was a ratchet, and `D-SKL-064` said what it could not do: no
     * fixed sum absorbs another skill, so every publication spent itself
     * arguing about the descriptions written before it. Twice it was raised
     * instead, which made it a record of what had happened. What it was for is
     * kept here — a description that grows without bound is what crowds a
     * listing — and what it could not do is given up: how many skills this
     * server publishes is decided by which domains earn one.
     *
     * The client still drops whole descriptions where they do not fit, least
     * used first (`D-SKL-026`), so the sum is a real limit somewhere. Nobody
     * has measured where since 2026-08-08, and this cap does not pretend to.
     */
    #[Decision('D-SKL-033')]
    #[Requirement('R-SKL-021')]
    #[Decision('D-SKL-026')]
    #[Decision('D-SKL-054')]
    #[Decision('D-SKL-061')]
    #[Decision('D-SKL-064')]
    #[Decision('D-SKL-070')]
    #[Test]
    public function everyDescriptionIsWrittenToALengthOfItsOwn(): void
    {
        // What thirteen of the fourteen were already written to on 2026-08-24,
        // and what the fourteenth came down to in order to be published.
        $longest = 360;

        foreach (Installer::skills() as $name) {
            $description = self::description($name);
            self::assertLessThanOrEqual(
                $longest,
                mb_strlen($description),
                $name . ' describes itself in ' . mb_strlen($description) . ' characters, and a client that runs out drops whole descriptions',
            );
        }
    }

    /**
     * A skill exists for its readers once its directory does, so a skill this
     * server names in an answer is one the caller can actually load
     * (`D-SKL-013`, `D-SKL-087`).
     */
    #[Decision('D-SKL-013')]
    #[Test]
    public function everySkillNamedInKnowledgeIsPublished(): void
    {
        $intents = json_decode(
            (string) file_get_contents(Paths::knowledgeFile('task-intents.json')),
            true,
        );
        self::assertIsArray($intents);

        $named = [];
        foreach ($intents as $intent) {
            foreach (['skill', 'skillCore'] as $key) {
                if (($intent[$key] ?? '') !== '') {
                    $named[] = [$intent['id'], $intent[$key]];
                }
            }
        }
        self::assertNotSame([], $named, 'no task routes to the skill that owns it');

        foreach ($named as [$intent, $skill]) {
            self::assertContains(
                $skill,
                Installer::skills(),
                $intent . ' routes to ' . $skill . ', which this server does not publish',
            );
        }
    }

    /**
     * The same question for the skills a tool writes rather than routes to.
     *
     * `typo3_gerrit_lookup` names two in the answer it ends a change lookup on
     * (`D-SKL-038`) and `typo3_feedback_record` names one as the example a
     * session reports a skill by, and both are prose in a class: a renamed skill
     * leaves them pointing at nothing. Read off the source rather than off a
     * rendered answer, because the answers that carry them are a review server
     * away and an offline suite would scan nothing.
     */
    #[Test]
    public function everySkillNamedByAToolIsPublished(): void
    {
        $named = [];
        foreach (Finder::create()->files()->in(Paths::root() . '/src/Tool')->name('*.php')->sortByName() as $file) {
            preg_match_all('/typo3-[a-z0-9]+(?:-[a-z0-9]+)+/', (string) $file->getContents(), $matches);
            foreach (array_unique($matches[0]) as $skill) {
                // This package's own binary has the shape of a skill name and
                // is not one: `typo3_task_guide` names the update command
                // beside a stale copy (`D-SKL-086`).
                if ($skill === 'typo3-dev-companion') {
                    continue;
                }
                $named[] = [$file->getFilename(), $skill];
            }
        }

        // Or the scan matched nothing and the loop below holds nothing.
        self::assertNotSame([], $named, 'no tool names a skill at all');

        foreach ($named as [$tool, $skill]) {
            self::assertContains(
                $skill,
                Installer::skills(),
                $tool . ' names ' . $skill . ', which this server does not publish',
            );
        }
    }

    /**
     * The call the hole was found on, with the session's own arguments.
     *
     * Two things had to be true for it to answer `typo3-extension-health`:
     * no intent named `typo3-core-issue-triage`, and the task carried none of
     * the markers that say core, so every intent that did match answered with
     * its extension side (`D-SKL-023`). The task names the core as a tracker
     * rather than as a patch, which is what work ending before a patch does.
     */
    #[Requirement('R-SKL-019')]
    #[Test]
    public function aCoreTriageReachesTheSkillThatOwnsItWithoutNamingAPath(): void
    {
        $answer = Registry::call('typo3_task_guide', [
            'task' => 'Triage an old open core bug report: establish whether it still reproduces against this checkout',
            'changeType' => 'audit',
            'targetVersion' => '15',
            'paths' => [],
        ])->data;

        self::assertSame('core', $answer['scope']);
        self::assertContains('typo3-core-issue-triage', $answer['skills']);
        self::assertNotContains('typo3-extension-health', $answer['skills']);
    }

    /**
     * A brief is a task, not a subject, and a task names as many units as it
     * names.
     *
     * A session asked for three units of work and was routed to the content
     * element alone. Nothing in the answer shape was in the way — the skills of
     * every confirmed intent are collected — what was in the way is that
     * `installation-setup` matched on the act of setting one up and on no word a
     * brief names the installation with (`D-SKL-051`). Two of the three, because
     * the third has no intent yet: that workflow is `D-SKL-050`'s card.
     */
    #[Decision('D-SKL-051')]
    #[Test]
    public function aBriefThatNamesSeveralUnitsRoutesToTheSkillOfEach(): void
    {
        $answer = Registry::call('typo3_task_guide', [
            'task' => 'Build a TYPO3 site from scratch: development installation, a sitepackage extension '
                . 'with custom content elements, and a distribution extension carrying the content',
            'changeType' => 'feature',
        ])->data;

        self::assertContains('typo3-development-installation', $answer['skills']);
        self::assertContains('typo3-content-element-development', $answer['skills']);
    }

    /**
     * The other direction, which is the one that fails without saying so.
     *
     * A client selects a skill on its description and `typo3_task_guide`
     * selects one on the intents, so a skill in the first and absent from the
     * second is reachable only by a caller who already knew it existed. What
     * the guide answers such a task with is the nearest intent that did match —
     * a different workflow, confidently named (`D-SKL-023`). The set is every
     * skill this server publishes and the check has no exemption — `D-SKL-064`,
     * `D-SKL-087`.
     */
    #[Requirement('R-SKL-019')]
    #[Decision('D-SKL-023')]
    #[Decision('D-SKL-064')]
    #[Test]
    public function everyPublishedSkillIsNamedByAnIntent(): void
    {
        $named = [];
        foreach (TaskIntents::load() as $intent) {
            foreach ([$intent['skill'], $intent['skillCore']] as $skill) {
                if ($skill !== '') {
                    $named[$skill] = true;
                }
            }
        }

        // All of them, because one at a time names the skill somebody added
        // last and the list says how far the routing has fallen behind the
        // directory.
        self::assertSame(
            [],
            array_values(array_diff(Installer::skills(), array_keys($named))),
            'published and named by no intent, so typo3_task_guide cannot route a task to it',
        );
    }

    /**
     * And it says what it owns, which is what ends a workflow at the outcome it
     * is responsible for instead of in the middle of somebody else's —
     * `D-SKL-021`.
     */
    #[Decision('D-SKL-021')]
    #[Test]
    public function everySkillStatesWhatItOwns(): void
    {
        foreach (self::skills() as $name => $skill) {
            self::assertStringContainsString('This skill owns ', $skill, $name . ' does not say what it owns');
        }
    }

    /**
     * A skill is a copy in somebody else's project, so a uri it names is a
     * promise no release of this server corrects. It is also the only address a
     * session gets where the client renders no resource list, which is the case
     * `D-AUD-007` was reopened by — a dead one there is worse than none.
     */
    #[Test]
    public function everyResourceASkillNamesIsOneTheServerServes(): void
    {
        $served = array_map(
            static fn(array $document): string => Documents::uri($document['id']),
            Documents::topics(),
        );

        foreach (self::skills() as $name => $skill) {
            foreach (self::published($name, $skill) as $file => $text) {
                preg_match_all('#typo3://[a-zA-Z0-9/_-]+#', $text, $matches);
                foreach (array_unique($matches[0]) as $uri) {
                    self::assertContains(
                        $uri,
                        $served,
                        $name . '/' . $file . ' names ' . $uri . ', which this server does not serve',
                    );
                }
            }
        }
    }

    /**
     * An address is delivery to a client that renders a resource list and to no
     * other, so a step that tells a session to read a page whole hands it the
     * call instead: `typo3_rule_lookup` with that `documentId`. `D-ANS-070` is
     * the session this was read off — it held the guide ids, read the
     * `documentId` parameter description, and searched anyway. The uri may
     * stand beside the call, which is why this asserts the call is there rather
     * than that the address is gone.
     */
    #[Test]
    public function everyGuideASkillNamesIsHandedOverByTheCallThatReadsIt(): void
    {
        foreach (self::skills() as $name => $skill) {
            foreach (self::published($name, $skill) as $file => $text) {
                preg_match_all('#typo3://guides/([a-zA-Z0-9/_-]+)#', $text, $matches);
                foreach (array_unique($matches[1]) as $id) {
                    self::assertStringContainsString(
                        'documentId="' . $id . '"',
                        $text,
                        $name . '/' . $file . ' names ' . $id . ' as an address a client that lists no '
                        . 'resources cannot act on; name the call, typo3_rule_lookup with documentId="' . $id . '"',
                    );
                }
            }
        }
    }

    #[Decision('D-SKL-052')]
    #[Test]
    public function noSkillKeepsASecondCopyOfWhatAToolOwns(): void
    {
        foreach (self::skills() as $name => $skill) {
            self::assertStringContainsString('Keep this skill as routing', self::flat($skill), $name);
            // A version number in a permanently loaded instruction is the one
            // fact that cannot be re-asked when the installation is a different
            // one, and no answer says it came from here.
            self::assertDoesNotMatchRegularExpression('/TYPO3 v?\d+/', $skill, $name);
            self::assertStringNotContainsString('<core:', $skill, $name . ' carries backend markup');
            // The other two kinds a body can be held to — D-SKL-052. An
            // environment variable is what a session that has just looked the
            // set up writes in beside the hint that names them, and a package
            // name is one word no release of this server corrects. A reference
            // is where either may stand, which is why this reads SKILL.md.
            self::assertDoesNotMatchRegularExpression(
                '/TYPO3_[A-Z_]+/',
                $skill,
                $name . ' names an environment variable rather than the hint that owns the set',
            );
            self::assertDoesNotMatchRegularExpression(
                '#\btypo3/[a-z0-9-]+#',
                $skill,
                $name . ' names a package every task then carries',
            );
        }
    }

    #[Test]
    public function everyReferenceIsOneHopAwayAndLoadedOnDemand(): void
    {
        foreach (self::skills() as $name => $skill) {
            $directory = Paths::root() . '/skills/' . $name . '/references';
            if (!is_dir($directory)) {
                continue;
            }
            foreach (Finder::create()->files()->in($directory)->depth(0)->name('*.md')->sortByName() as $reference) {
                $file = $reference->getFilename();
                self::assertStringContainsString(
                    '[references/' . $file . '](references/' . $file . ')',
                    $skill,
                    $name . ' ships references/' . $file . ' without saying when to read it',
                );
                // One hop: a reference that loads a reference is a body the
                // skill no longer decides the size of.
                self::assertStringNotContainsString(
                    '(references/',
                    (string) file_get_contents($reference->getPathname()),
                    $name . '/references/' . $file . ' sends the reader on to another reference',
                );
            }
        }
    }

    /**
     * A skill names the tool that owns each fact, in the order the work needs
     * them, rather than restating what the tool answers — `D-SKL-046`,
     * `D-SKL-055`, `D-SKL-021`.
     */
    #[Requirement('R-SKL-007')]
    #[Requirement('R-SKL-017')]
    #[Requirement('R-SKL-022')]
    #[Decision('D-SKL-021')]
    #[Decision('D-SKL-046')]
    #[Decision('D-SKL-055')]
    #[Test]
    public function everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder(): void
    {
        foreach (self::ROUTING_SKILLS as $name => $tools) {
            $skill = self::flat((string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md'));
            $position = -1;
            foreach ($tools as $tool) {
                $next = self::routing($skill, $tool);
                self::assertNotFalse(
                    $next,
                    $tool . ' is not routed from ' . $name . ', or is named there only to discharge the call',
                );
                self::assertGreaterThan($position, $next, $tool . ' is routed in the wrong order in ' . $name);
                $position = $next;
            }
        }
    }

    /**
     * A tool a skill names in order not to call it, held as that rather than as
     * a routing — over the directory, so the next skill to discharge one is
     * held without anybody having seen this.
     *
     * `D-ANS-083` settled that a caller holding the unsupported answer already
     * has what the orientation would add, while `ROUTING_SKILLS` went on naming
     * the tool and was satisfied by the very sentence telling the caller to skip
     * it. What separates the two is the construct and not the prose around it: a
     * discharge names what answers instead, in the words `DISCHARGE` carries,
     * and a routing is any other mention — `D-SKL-055`.
     */
    #[Decision('D-ANS-083')]
    #[Decision('D-SKL-055')]
    #[Test]
    public function everyDischargedCallIsWrittenAsOneAndRoutedNowhere(): void
    {
        foreach (self::skills() as $name => $skill) {
            $body = self::flat($skill);
            $discharged = self::DISCHARGED_TOOLS[$name] ?? [];

            // Both directions, so neither half can go stale on its own: a
            // discharge nobody recorded is one the routing assertion can still
            // be satisfied by, and a recorded one nobody wrote is a routing
            // this test would then hold out of the skill for no reason.
            preg_match_all('/`(typo3_\w+)' . preg_quote(self::DISCHARGE, '/') . '/', $body, $matches);
            self::assertSame(
                $discharged,
                array_values(array_unique($matches[1])),
                $name . ' discharges other calls than the ones recorded for it',
            );

            foreach ($discharged as $tool) {
                self::assertNotContains(
                    $tool,
                    self::ROUTING_SKILLS[$name] ?? [],
                    $name . ' routes to ' . $tool . ' and says in its body that the call is discharged',
                );
                self::assertFalse(
                    self::routing($body, $tool),
                    $name . ' names ' . $tool . ' a second time, which is a routing to what it discharges',
                );
            }
        }
    }

    /**
     * Judgment is what a checklist is for, and it is also the thing a skill
     * grows a body around: the ones that carry it keep it beside them rather
     * than in the instruction every session pays for.
     *
     * The ones without one are construction, and each hands what it would
     * otherwise judge to the evidence or to the checklist one directory away:
     * the registries a backend module needs, the sweep an upgrade produces, the
     * findings a review hands a patch, the cold start an installation answers
     * to, and the install that says whether a distribution is complete —
     * `D-SKL-021`.
     */
    #[Decision('D-SKL-021')]
    #[Test]
    public function judgmentHeavySkillsKeepTheirChecklistBesideThem(): void
    {
        $judging = array_diff(
            array_keys(self::ROUTING_SKILLS),
            [
                'typo3-backend-module-development',
                'typo3-extension-upgrade',
                'typo3-core-patch-development',
                'typo3-development-installation',
                'typo3-distribution-content',
                // The seventh is construction too: what it would otherwise
                // judge is answered by the build it runs and by the lookups
                // that say which majors a borrowed surface holds on.
                'typo3-extension-asset-build',
            ],
        );

        foreach ($judging as $name) {
            self::assertFileExists(Paths::root() . '/skills/' . $name . '/references/checklist.md');
        }
    }

    /**
     * The workflow routed every lookup by subject and at planning time, which
     * is the moment a subject is the one thing a debugging session does not
     * have. A build read core source through nine debugging cycles with the
     * index in its context throughout, and three of them had a hint titled
     * after what it was staring at (`D-SKL-048`). The queries are made rather
     * than described: the line promises a caller that a symptom reaches, and
     * curation is what can make that false without touching the skill.
     */
    #[Decision('D-SKL-048')]
    #[Test]
    public function theBuildWorkflowSaysASymptomIsALookupTrigger(): void
    {
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-content-element-development/SKILL.md',
        ));

        self::assertStringContainsString('A symptom is a lookup trigger, and not only a task is.', $skill);
        self::assertStringContainsString('`typo3_hint_lookup` takes the observation as its `task`', $skill);
        // Before the installed source, which is the order the session inverted:
        // the base fixes that reading as the step after the lookups.
        self::assertStringContainsString('before reading the installed source', $skill);

        $symptoms = [
            'the content elements render in reverse order' => 'datahandler-placement',
            'the child rows exist, uid_foreign is 0, tablename is empty' => 'datahandler-relations',
        ];
        foreach ($symptoms as $symptom => $id) {
            $answer = Registry::call('typo3_hint_lookup', ['task' => $symptom])->data;
            self::assertContains(
                $id,
                array_column($answer['hints'], 'id'),
                '"' . $symptom . '" is the shape the workflow promises reaches, and it misses ' . $id,
            );
        }
    }

    /**
     * The bullet named the moment and no way out of it. A build held both guide
     * ids from `typo3_project_describe`, reached the point where six backend
     * previews had to be seen, gave up on a scripted backend login and shipped
     * them unverified — calling `typo3_rule_lookup` at no point in the session
     * (`D-SKL-045`). Both halves are named here rather than one, because a
     * caller holding the looking answer must not read the step as discharged
     * (`D-SKL-044`).
     */
    #[Requirement('R-SKL-024')]
    #[Decision('D-SKL-045')]
    #[Test]
    public function theBrowserStepNamesTheGuidesThatAnswerIt(): void
    {
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-content-element-development/SKILL.md',
        ));

        self::assertStringContainsString('`documentId="any/testing/browser-check"`', $skill);
        self::assertStringContainsString('`documentId="project/testing/playwright"`', $skill);
        // What each one alone answers: the installation that can show it, and
        // the suite a repository does not have yet.
        self::assertStringContainsString('in an installation that already holds the content', $skill);
        self::assertStringContainsString('a repository that has no browser suite yet', $skill);
        // Only the second crosses a boundary, and the skill that owns the
        // infrastructure is named where the crossing is — `R-SKL-003`.
        self::assertStringContainsString('Establishing that suite is `typo3-extension-testing`', $skill);

        // A named id that stopped resolving is a step routing to nothing.
        foreach (['any/testing/browser-check', 'project/testing/playwright'] as $id) {
            $document = Registry::call('typo3_rule_lookup', ['documentId' => $id])->data;
            self::assertSame(
                [$id],
                array_column($document['matches'], 'documentId'),
                $id . ' is named at the browser step and is no documentId',
            );
        }
    }

    #[Test]
    public function extensionTestingVerifiesItsHarnessBeforeAddingCoverage(): void
    {
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-testing/SKILL.md',
        );

        $verify = strpos($skill, 'Verify that the harness');
        $establish = strpos($skill, '## Establish or repair the required harness');
        $add = strpos($skill, '## Add or extend tests');
        self::assertNotFalse($verify);
        self::assertNotFalse($establish);
        self::assertNotFalse($add);
        self::assertLessThan($establish, $verify);
        self::assertLessThan($add, $establish);
        self::assertStringContainsString('for a review-only request, report the defect without changing it', $skill);
        self::assertStringContainsString('Keep unit and functional infrastructure with the extension', $skill);
        self::assertStringContainsString('Keep browser infrastructure with the runnable project', $skill);
        self::assertStringNotContainsString('Classify the work as setup', $skill);
    }

    #[Test]
    public function extensionTestingLoadsOnlyTheSelectedLayerGuide(): void
    {
        $directory = Paths::root() . '/skills/typo3-extension-testing';
        $skill = (string) file_get_contents($directory . '/SKILL.md');

        foreach (['phpunit', 'playwright'] as $guide) {
            $guidance = (string) file_get_contents($directory . '/references/' . $guide . '.md');
            self::assertStringContainsString('## Choose the folders', $guidance);
        }
        self::assertStringContainsString('read only its implementation guide', $skill);
        self::assertStringContainsString(
            'FunctionalTests.xml',
            (string) file_get_contents($directory . '/references/phpunit.md'),
        );
        self::assertStringContainsString(
            'playwright.config.ts',
            (string) file_get_contents($directory . '/references/playwright.md'),
        );
    }

    #[Test]
    public function extensionTestingKeepsCheckingApartFromFixing(): void
    {
        // Two recorded REVIEW-02 runs bound this from both sides. Against an
        // extension whose PHPStan and baseline exist, the review read them and
        // found gaps inside them; against one with a fixer, a lint step and no
        // analyser at all, static analysis was never named — the missing
        // workflow surfaced as a missing test workflow and landed here, where
        // the one sentence on the subject sent it back.
        $directory = Paths::root() . '/skills/typo3-extension-testing';
        $skill = (string) file_get_contents($directory . '/SKILL.md');
        $guidance = (string) file_get_contents($directory . '/references/static-quality.md');

        self::assertStringNotContainsString('only when the project already uses them', $skill);
        self::assertStringContainsString(
            'establishes them whether or not the project already runs them',
            self::flat($skill),
        );
        self::assertStringContainsString(
            '[references/static-quality.md](references/static-quality.md)',
            $skill,
        );

        // What the branch is worth is decided by four answers, and each of them
        // is a way the work goes wrong when it is left unsaid: a fixer wired
        // into the check, a new error parked in the baseline, formatting that
        // walks into vendored files, and a core suite translated by analogy.
        // And the run that never named static analysis needs the expectation to
        // measure the checkout against, or "what is missing" has no answer: the
        // leading finding there was a 2×4 matrix of version-independent steps,
        // which is the same evidence read from the other end.
        self::assertStringContainsString('This is the expectation the checkout is measured against', $guidance);
        self::assertStringContainsString('every cell runs only', $guidance);

        // The expectation names its tools, or "establish static analysis" is
        // advice the reader still has to source. They sit in the reference
        // rather than in the skill: a name every session carries is a name that
        // cannot be re-asked, and this list is read once per task that needs it.
        foreach (['phpstan/phpstan', 'php-cs-fixer', 'typo3/coding-standards', 'phplint', 'typoscript-lint', 'composer validate', 'eslint', 'stylelint'] as $tool) {
            self::assertStringContainsString($tool, $guidance, $tool . ' is not named where a project without a check starts');
        }
        // A package name in a published skill is the one thing no release of
        // this server corrects, and the analyser extension for TYPO3 is where
        // that bites: the core runs phpstan on itself without one, because
        // makeInstance() carries the @template annotation that used to be the
        // extension's job — checked on 12.4, 13.4, 14.3 and main.
        self::assertStringNotContainsString('saschaegerer', $guidance);
        self::assertStringContainsString('still maintained before adding', $guidance);
        // And the sentence that keeps the list from becoming the requirement:
        // it is the default where nothing covers the check, and it loses to
        // whatever the project already runs for the same one.
        self::assertStringContainsString(
            'default per check where the checkout covers it with nothing, never as a replacement for what it already runs',
            self::flat($guidance),
        );

        // The list above stops at which packages to require. What goes inside
        // the analyser's configuration is a cell of the corpus — `D-KNW-012`
        // judged a session that wrote it from recall — and this page reaches it
        // by name rather than restating it, because a skill is a file no
        // release of this server corrects.
        self::assertStringContainsString(
            '`typo3_hint_lookup` with `id=extension-static-analysis`',
            $guidance,
        );
        self::assertStringNotContainsString('phpstan-baseline.neon', $guidance);
        self::assertStringNotContainsString('tmpDir', $guidance);
        self::assertNotNull(
            Hints::byId('extension-static-analysis'),
            'the skill defers to an id the corpus does not have',
        );

        self::assertStringContainsString('Keep checking and fixing apart', $guidance);
        self::assertStringContainsString('never receives an error the change in hand introduced', $guidance);
        self::assertStringContainsString('first-party paths the project intends it', $guidance);

        // Splitting the commits is half the rule and the order is the other
        // half. `feedback/2026-08-04-055741` worked it out on its own: tooling
        // first would have landed `ci:editorconfig` on a tree whose XLF files
        // still held tabs, so the session inverted the split and ran the checks
        // at the new HEAD.
        self::assertStringContainsString(
            'the conformance commits come first and the commit that adds the check comes last, so no commit fails the check it introduces',
            self::flat($guidance),
        );
        self::assertStringContainsString('running the check at the new HEAD', self::flat($guidance));

        // The core's own build script is named once, in the skill, where the
        // harness step it belongs to is. Repeating it in an extension-facing
        // reference gives a tool that exists only in the core mono repository
        // the weight of a thing an extension might have.
        self::assertStringNotContainsString('runTests.sh', $guidance);
        self::assertSame(
            1,
            substr_count($skill, 'runTests.sh'),
            'the core build script is named more than once in an extension skill',
        );
    }

    #[Requirement('R-SKL-007')]
    #[Test]
    public function anUpgradeIsOrderedWorkAndStopsWhereAnotherSkillStarts(): void
    {
        // The REVIEW-02 run in an extension declaring two majors against an
        // installation a major behind moved the feedback that asked for this skill:
        // the shared-versus-version-specific decisions were not the gap, because
        // the review made them — it argued the older major's registration shapes
        // as required rather than as debt, and refused the same excuse for a
        // deprecated ViewHelper shape that works on both. What it never did was
        // work in an order: the sweep ran where a finding walked into it, and
        // the Extension Scanner was not reached at all in a checkout that has
        // one.
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-upgrade/SKILL.md',
        );

        $order = [
            '[references/base.md](references/base.md)',
            '## Widen the sweep into a work list',
            '## Settle the range, rather than assert it',
            '## The boundary of what may change',
            '## Prove it on every version it claims',
        ];
        $position = -1;
        foreach ($order as $step) {
            $next = strpos($skill, $step);
            self::assertNotFalse($next, $step . ' is not part of the upgrade workflow');
            self::assertGreaterThan($position, $next, $step . ' is stated out of order');
            $position = $next;
        }

        // It starts from the base's sweep and states only what it adds, so the
        // two scope calls that order already fixes appear nowhere here.
        self::assertStringContainsString('starts from the result of that sweep rather than restating it', $skill);
        self::assertStringNotContainsString('typo3_project_describe', $skill);
        self::assertStringNotContainsString('typo3_extension_describe', $skill);

        // What it adds to the sweep is the two sources a changelog query cannot
        // reach. The scanner because the run never touched it, and the
        // annotations because the deprecation that decided that package's next
        // major sat on the installed class rather than in an entry any of the
        // four queries matched.
        self::assertStringContainsString('`type: breaking`', $skill);
        self::assertStringContainsString('**The Extension Scanner**', $skill);
        self::assertStringContainsString('`FullyScanned` / `PartiallyScanned`', $skill);
        self::assertStringContainsString(
            'A clean scan for a partially scanned entry is not a result',
            self::flat($skill),
        );
        self::assertStringContainsString('**The deprecation annotations on what this package actually calls**', $skill);

        // And the boundary both of those inherit: they answer from the core that
        // is installed, so the target's own changes are documentation until the
        // installation is on it.
        self::assertStringContainsString(
            'they do not know what the target major changed until the installation is on it',
            self::flat($skill),
        );
        self::assertStringContainsString('never from memory', $skill);

        // The decision the review already made correctly, which is why it is
        // stated here as the boundary of the work rather than as a judgement to
        // arrive at.
        self::assertStringContainsString('lowest declared major decides every shape', $skill);
        self::assertStringContainsString('not debt to clean up', $skill);

        // A range is resolved by the solver, and a matrix cell that nobody ran
        // or that will not resolve is a result rather than a gap in the report.
        self::assertMatchesRegularExpression('/Let the dependency solver answer, and quote what it printed/', $skill);
        self::assertStringContainsString('as a result — it is the finding', $skill);
        self::assertStringContainsString('named as unrun', $skill);

        // What it does not own, and the skill that hands it the sweep whole.
        self::assertStringContainsString('This skill owns what a package owes the TYPO3 majors', $skill);
        foreach ([
            'typo3-extension-health',
            'typo3-extension-testing',
            'typo3-extension-documentation',
        ] as $owner) {
            self::assertStringContainsString($owner, $skill, $owner . ' is not named where the upgrade stops');
        }
        // Read flat: what is asserted is the sentence, and `prose:format`
        // decides where its lines break.
        self::assertStringContainsString(
            'What the sweep returned goes to `typo3-extension-upgrade` whole',
            self::flat((string) file_get_contents(
                Paths::root() . '/skills/typo3-extension-health/SKILL.md',
            )),
        );
    }

    /**
     * Seven feedback from two projects on one day, and `D-SKL-012` is where they
     * were read. The two projects are the two shapes this holds the skill to:
     * one repository had no installation and had to produce one, the other
     * declared its own environment and had to boot it, and a skill that carries
     * only the first sends the second back to the patch workflow.
     *
     * A third declared an environment and no procedure at all, so the fork asks
     * which procedure is declared, and what the closing section owes follows
     * what the install left the repository carrying. Both discriminants are
     * wording and nothing else, which is why they are held here — `D-SKL-056` —
     * `D-KNW-105`.
     */
    #[Decision('D-SKL-059')]
    #[Requirement('R-KNW-072')]
    #[Decision('D-KNW-092')]
    #[Decision('D-KNW-105')]
    #[Decision('D-SKL-044')]
    #[Decision('D-SKL-047')]
    #[Decision('D-SKL-056')]
    #[Decision('D-SKL-058')]
    #[Test]
    public function anInstallationIsBuiltInDependencyOrder(): void
    {
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-development-installation/SKILL.md',
        );
        $flat = self::flat($skill);

        // The entry condition, and the one place this skill contradicts the
        // base's first instruction: `typo3_project_describe` answers `installed:
        // false` in a repository nobody has installed and `unsupported:
        // no-installation` where there is no repository either, and both read
        // like the disconnected server the base stops for while being the task.
        self::assertStringContainsString('reports nothing installed in it is the task', $flat);
        self::assertStringContainsString('Neither is the disconnected server', $flat);

        $order = [
            '[references/base.md](references/base.md)',
            '## Boot what the repository already declares',
            '## Create one where none is declared',
            '## Prove it, and how far depends on what the run wrote',
            '## The installation that already answers',
            '## Where this stops',
        ];
        $position = -1;
        foreach ($order as $step) {
            $next = strpos($skill, $step);
            self::assertNotFalse($next, $step . ' is not part of the installation workflow');
            self::assertGreaterThan($position, $next, $step . ' is stated out of order');
            $position = $next;
        }

        // What the fork asks, and the shape that has no side to fall on until
        // it does: an environment configuration on its own is a trace, and
        // `070448` entered the boot branch on it and found nothing to run.
        self::assertStringContainsString(
            'the boot procedure it declares rather than the traces an installation has left in it',
            $flat,
        );
        // Stated in the fork and not in a section of its own, which is what the
        // file's size is paid with — `D-SKL-052`.
        $third = strpos($flat, 'One that declares an environment and no procedure is both');
        self::assertNotFalse($third, 'the fork carries no shape for a repository that declares only an environment');
        self::assertLessThan(
            (int) strpos($flat, '## Boot what the repository already declares'),
            $third,
            'the third shape is stated below the branches it sends the session through',
        );

        // The five steps in the order their dependencies force, which is what
        // `162745` numbered after inventing it once.
        $steps = [
            '**Make the package\'s own manifest the Composer root package.**',
            '**Declare the container.**',
            '**Install non-interactively.**',
            '**Seed the content the package is to be developed against**',
            '**Decide what the install wrote into the repository.**',
        ];
        $position = -1;
        foreach ($steps as $step) {
            $next = strpos($skill, $step);
            self::assertNotFalse($next, $step . ' is not one of the steps');
            self::assertGreaterThan($position, $next, $step . ' is stated out of order');
            $position = $next;
        }

        // The environment that generates settings knows only the services it
        // provides itself, which is the collision `162858` paid a debugging
        // cycle for. The hint owns the boundary; this owns the case.
        self::assertStringContainsString('id=project-configuration-files', $skill);
        self::assertStringContainsString('knows only the services it provides itself', $flat);

        // Step 1 names the two sources that answer before anything exists, and
        // neither is the installation: the hint that owns the installer keys,
        // and Composer for its own plugin allowance. What it named before was
        // the manual, which carries none of the keys at 14.3, and the installer
        // package the manifest is being written to pull in — D-SKL-047.
        self::assertMatchesRegularExpression(
            '/`id=extension-repository-installation` for \w/',
            $flat,
            'the Composer root step names no lookup for the installer keys',
        );
        self::assertNotNull(Hints::byId('extension-repository-installation'));
        self::assertStringNotContainsString('installed installer package', $flat);
        self::assertStringContainsString('the installation itself answers nothing at this step', $flat);

        // Step 2 is where the interpreter is decided and nothing later asks
        // again, so the step that declares the container carries the lookup
        // rather than leaving the number to whatever the machine has —
        // `R-KNW-072`.
        self::assertMatchesRegularExpression(
            '/`id=php-versions` for \w/',
            $flat,
            'the container step names no interpreter lookup',
        );
        self::assertNotNull(Hints::byId('php-versions'));

        // Step 5 names two ids, and `211118` fetched one: the shared clause
        // described only the first, so the step read as discharged once that
        // answer landed. Each id carries what it alone answers — D-SKL-044.
        foreach (['project-configuration-files', 'project-build-and-scripts'] as $hint) {
            self::assertMatchesRegularExpression(
                '/`id=' . preg_quote($hint, '/') . '` for \w/',
                $flat,
                'step 5 names ' . $hint . ' without saying what it alone answers',
            );
            self::assertNotNull(
                Hints::byId($hint),
                'the skill defers to an id the corpus does not have',
            );
        }
        // The step closes on both answers, so holding one cannot satisfy it.
        self::assertStringContainsString('follow from both answers', $flat);

        // Named once in the description, which is what a client routes on, and
        // nowhere in the body: what that product does by default is the fact
        // that moves after this file is published into somebody else's project.
        self::assertStringContainsString('DDEV where it declares one', self::description('typo3-development-installation'));
        self::assertStringNotContainsString('DDEV', substr($skill, (int) strpos($skill, "\n---\n") + 5));

        // The boot branch routed to no hint of its own, which is `D-KNW-054`'s
        // fourth **Wrong if** and how `212702` came to rule `installation-boot`
        // out by its title while standing in exactly that case.
        self::assertMatchesRegularExpression(
            '/`id=installation-boot` owns \w/',
            $flat,
            'the boot branch names no lookup for what a declared clone is missing',
        );
        self::assertNotNull(Hints::byId('installation-boot'));

        // The layout hint is routed by what the repository is, so the branch
        // that boots one names it too: `071435` took this branch, read the id
        // filed under the other and probed `typo3conf/ext` by hand instead —
        // `D-SKL-058`.
        self::assertMatchesRegularExpression(
            '/`id=extension-repository-installation` owns \w/',
            $flat,
            'the boot branch names no lookup for the layout it is looking at',
        );
        self::assertLessThan(
            (int) strpos($flat, '## Create one where none is declared'),
            (int) strpos($flat, '`id=extension-repository-installation` owns'),
            'the layout lookup is named in the create branch alone',
        );

        // Proving is where a session that has no browser stands when the site
        // does not answer, and the same session scraped four rendered error
        // pages for one line of message each — D-KNW-092.
        $proving = (int) strpos($skill, '## Prove it,');
        $exceptions = (int) strpos($skill, 'id=installation-exception-output');
        self::assertGreaterThan($proving, $exceptions, 'the exception lookup is named before the site is proved');
        self::assertNotNull(Hints::byId('installation-exception-output'));
        self::assertStringContainsString('which codes are shown and never written at all', $flat);

        // What the closing section owes is read off the repository and not off
        // the session: `070448` authored every step and committed nothing, so
        // the re-run would have destroyed the installation it had just been
        // asked for and the message had no subject — `D-SKL-056`.
        self::assertStringContainsString(
            'follows from what the install wrote into the repository',
            $flat,
        );
        self::assertStringContainsString('Where every path it wrote is ignored', $flat);
        self::assertStringContainsString('the report names the two and says why', $flat);

        // Both directions of the crossing, because the feedback asked for both.
        // Going out stands at the moment it happens rather than in the closing
        // section: `074245` read that section while it held a 404 and no test,
        // and wrote the tests here forty minutes later — `R-SKL-018`.
        self::assertStringContainsString('typo3-extension-testing', $skill);
        self::assertStringContainsString('The moment this task grows a test, invoke', $flat);
        self::assertStringContainsString('stops before editing that owner\'s files', $flat);
        self::assertStringContainsString(
            'a suite that needs a served site and has none is this workflow first',
            $flat,
        );

        // The running half — `D-SKL-059`. An installation that is up is neither
        // created nor booted, so the fork has a shape for it or the section
        // below it is reachable only by finishing a build.
        self::assertStringContainsString('One that is already up is none of the three', $flat);

        // What `074606` paid a wrong turn for: it read the exception routing as
        // covering a served 404, found a log of zero bytes, and the one line
        // that decided the diagnosis was in the page it had been told not to
        // fetch. So the detour is bounded by what was thrown, and the empty log
        // is stated as the finding rather than as a missing file.
        self::assertStringContainsString('the detour this replaces wherever something was thrown', $flat);
        self::assertStringContainsString('A log that stayed empty is itself the finding', $flat);
        self::assertStringContainsString('fetching it is right here where it was the detour above', $flat);

        // The bullet above separates a request that matched a site from one
        // that matched none, and only the second half had a lookup. The first
        // is routed before the site configuration paragraph, because a page
        // this site holds and refuses is not read off a base.
        $page = (int) strpos($flat, '`id=page-not-found-within-a-site` owns that half');
        self::assertGreaterThan(0, $page, 'the half where a site did answer is routed nowhere');
        self::assertNotNull(Hints::byId('page-not-found-within-a-site'));
        self::assertLessThan(
            (int) strpos($flat, 'the request reached the wrong site or none'),
            $page,
            'the page lookup is named after the site configuration it comes before',
        );

        // And the ownership sentence, which gave the same half away in the
        // clause `071526` quoted back at the server.
        self::assertStringContainsString('what a running one answers', $flat);
        self::assertStringNotContainsString('what runs against the installation once it answers', $flat);
    }

    /**
     * The step that runs a declared clone up names the document that carries
     * the run, in the form `D-SKL-045` set: the id, and what it alone answers.
     *
     * `070538` read the guides list, found no installation entry among the
     * eleven, and assembled the procedure out of a skill and two hint ids —
     * `D-KNW-095`. The order is the document's from that entry on, so a step
     * that names only the hint sends the caller to the facts and to no run.
     */
    #[Decision('D-KNW-095')]
    #[Test]
    public function theBootStepNamesTheGuideThatCarriesTheRun(): void
    {
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-development-installation/SKILL.md',
        ));

        self::assertStringContainsString('`documentId="project/installation/booting-a-clone"`', $skill);
        self::assertStringContainsString('carries the order the steps go in', $skill);

        // A named id that stopped resolving is a step routing to nothing.
        $document = Registry::call('typo3_rule_lookup', ['documentId' => 'project/installation/booting-a-clone'])->data;
        self::assertSame(
            ['project/installation/booting-a-clone'],
            array_unique(array_column($document['matches'], 'documentId')),
        );
    }

    /**
     * The install step asked the manual for the whole question, and a boot of a
     * 14.3.6 installation ran the console's own help instead
     * (`feedback/2026-08-18-070611`): `--distribution` came back marked as
     * disabled, which
     * `.checkouts/14.3/typo3/sysext/install/Classes/Command/SetupCommand.php:151`
     * composes from whether the package that imports one is active, so it is a
     * fact about that installation and no version-bound page carries it. Both
     * halves are held here, because a step routed back to one source is what
     * `D-SKL-057` split.
     *
     * The bound is held with them. `.checkouts/13.4` declares the option
     * nowhere, so a caller there given it unbounded looks for an option its own
     * help does not print.
     */
    #[Decision('D-SKL-057')]
    #[Test]
    public function theSetupOptionsAreReadFromTheConsole(): void
    {
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-development-installation/SKILL.md',
        ));

        $console = strpos($skill, 'option set is read off the installed console');
        self::assertNotFalse($console, 'the setup step reads its option set from somewhere other than the console');
        self::assertStringContainsString('reports an option as disabled where a package it needs is inactive', $skill);
        self::assertStringContainsString('From 14 on', $skill);
        self::assertStringContainsString('`--distribution`', $skill);

        // What the console's help does not print, kept where it was: the step
        // that reads the option set off the binary still asks the manual what an
        // option means.
        self::assertNotFalse(
            strpos($skill, 'typo3_documentation_lookup', $console),
            'the setup step reads the option set off the console and asks the manual nothing',
        );
        self::assertStringContainsString('not necessarily the value written into the settings afterwards', $skill);
        self::assertStringContainsString('refuses a database that already holds tables', $skill);
    }

    #[Test]
    public function coreTestGuidanceIsGuardedByTheWork(): void
    {
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-backend-module-development/SKILL.md',
        );

        // The tool is offered everywhere, so being able to call it says nothing
        // about being able to follow the answer: runTests.sh exists in the core
        // repository alone, and that is what the skill has to gate on.
        self::assertStringContainsString('only for an actual core patch', self::flat($skill));
        self::assertStringContainsString('Never present it as a project', self::flat($skill));
        self::assertStringNotContainsString('profile', $skill);
        self::assertLessThan(
            strpos($skill, 'typo3_test_run_guide'),
            strpos($skill, 'typo3_server_scope'),
        );
    }

    #[Requirement('R-SKL-003')]
    #[Test]
    public function backendModuleDocumentationIsAnExplicitSkillTransition(): void
    {
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-backend-module-development/SKILL.md',
        );

        $flat = self::flat($skill);
        $verified = strpos($flat, 'implementation is verified');
        $stop = strpos($flat, 'stop this workflow');
        $activate = strpos($flat, 'invoke `typo3-extension-documentation` before editing documentation');
        self::assertNotFalse($verified);
        self::assertNotFalse($stop);
        self::assertNotFalse($activate);
        self::assertLessThan($stop, $verified);
        self::assertLessThan($activate, $stop);
        self::assertStringContainsString(
            'belongs to that extension, not to the project around it',
            self::flat($skill),
        );
    }

    #[Requirement('R-SKL-004')]
    #[Test]
    public function theBaseIsEstablishedBeforeTheCheckoutIsOpened(): void
    {
        // A base that is stated but reachable in any order is not a base. Three
        // runs of REVIEW-01 established that the reading phase swallows
        // whatever the skill left after it: the third read the checklist, then
        // listed the file tree and spent five minutes in it before calling
        // task_guide or a single conventions lookup. So the four owning calls
        // and the surface list come first here, in one block, and the sentence
        // that sends the session into the files comes after all of them —
        // `D-SKL-002`.
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-health/SKILL.md',
        );

        $base = [
            'references/base.md',
            'references/checklist.md',
            'Write the surface list down before opening a single file',
        ];

        $position = -1;
        foreach ($base as $step) {
            $next = strpos($skill, $step);
            self::assertNotFalse($next, $step . ' is not part of the conformance base');
            self::assertGreaterThan($position, $next, $step . ' is stated out of order');
            $position = $next;
        }

        // The file tree is a trap where a surface has no files, so the list is
        // derived from the surfaces and never from what a listing happens to
        // show.
        self::assertStringContainsString(
            'A surface is in scope because the checklist names it, not because the file tree shows it',
            self::flat($skill),
        );
        self::assertGreaterThan(
            $position,
            strpos($skill, 'Read the checkout for what none of those can know'),
            'the skill sends the session into the checkout before its base is established',
        );
    }

    #[Requirement('R-SKL-004')]
    #[Requirement('R-SKL-005')]
    #[Test]
    public function anAssessmentAsksBeforeItJudgesAndSaysWhatItDidNotAsk(): void
    {
        // The order is the whole requirement. A conventions lookup that happens
        // after the view has formed confirms it instead of testing it, and the
        // run that established this read three XLF files, judged them sound and
        // never asked what governs them — so the rule that calls a non-English
        // source file a defect was in the corpus, one query away, unread —
        // `D-SKL-002`.
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-health/SKILL.md',
        );

        $ask = strpos($skill, 'asked for **before** a view of the subsystem is formed');
        $lookup = strpos($skill, 'typo3_hint_lookup');
        self::assertNotFalse($ask, 'the conformance skill does not say when the conventions are asked for');
        self::assertNotFalse($lookup);
        self::assertLessThan($lookup, $ask, 'the skill asks for conventions after naming what to read');

        // Read in both directions: the rule judges the checkout that exists,
        // not only the code about to be written.
        self::assertMatchesRegularExpression(
            '/settled into the opposite of a rule is a finding, not a local style/',
            $skill,
        );

        // The runtime lookup is the near miss, not the omission: the third run
        // reached for a translation tool and picked the one that reports what a
        // path resolves to, then filed the surface as clean. What each of the
        // five adds is the base's since `D-SKL-069`, so what this skill states
        // is the call an audit makes on top of it.
        self::assertStringContainsString(
            'The runtime lookup that owns the surface, where one exists',
            self::flat($skill),
        );

        // And a surface nobody asked about is named, because silence about it
        // is indistinguishable from a clean result — read off the written list
        // rather than off what the session remembers having skipped.
        self::assertStringContainsString('**unassessed**, and unassessed is', $skill);
        self::assertStringContainsString('every entry marked assessed, unassessed or not requested', $skill);
        self::assertStringContainsString('not a recollection at the end', $skill);

        // "Do not change files" had been read as "run nothing": the audit
        // branch named no command at all, and only the improvement branch did.
        self::assertMatchesRegularExpression(
            '/Stopping at findings is not stopping at reading/',
            $skill,
        );
        self::assertStringContainsString(
            'marks as checks hand the code back as it was, and an audit told not to change files runs them',
            self::flat($skill),
        );
    }

    #[Requirement('R-SKL-004')]
    #[Decision('D-SKL-002')]
    #[Test]
    public function aFocusedRequestNarrowsTheReadingAndNeverTheSurfaceList(): void
    {
        // The permission to scope a review existed one clause deep in the
        // checklist while the two steps that build and close the work list
        // never mentioned it, so a security-only review was told to write the
        // whole list and answer every entry on it anyway. The narrowing is
        // stated where the list is built now, and it reaches the reading only —
        // `D-SKL-002`.
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-health/SKILL.md',
        );

        $list = strpos($skill, 'Write the surface list down before opening a single file');
        $narrow = strpos($skill, 'The request narrows the reading, never the');
        self::assertNotFalse($narrow, 'the conformance skill does not say what a focused request narrows');
        self::assertGreaterThan((int) $list, $narrow, 'the request narrows the list before the list exists');
        self::assertLessThan(
            strpos($skill, 'Read the checkout for what none of those can know'),
            $narrow,
            'the narrowing is stated after the checkout is open, where the reading it saves is already done',
        );

        // What the list is narrowed by stays the kind of checkout, and the
        // entries the request left out stay on it under a state of their own.
        self::assertStringContainsString('narrowed to the ones this kind of checkout can have', $skill);
        self::assertStringContainsString('mark the rest **not', $skill);
        self::assertStringContainsString(
            'A request that names no surface is not a focused one',
            self::flat($skill),
        );

        // The report is where the two states are told apart, and the number is
        // read off the step that writes the list: it said step 5 for two days
        // after the block was renumbered to three.
        self::assertStringContainsString('the surface list written in step 3', $skill);
        self::assertStringContainsString(
            'Unassessed and not requested both mean nothing was established there, and they are not the same thing',
            self::flat($skill),
        );
        self::assertStringContainsString('let neither read as clean', $skill);

        // And the clause that was outranked now points at the same list, so a
        // session reading the reference alone does not narrow it there.
        self::assertStringContainsString(
            'the surface list below is written whole',
            (string) file_get_contents(
                Paths::root() . '/skills/typo3-extension-health/references/checklist.md',
            ),
        );
    }

    /**
     * `R-SKL-025`. A v14 release audit mapped 23 open pull requests against its
     * 17 items and told the maintainer that item 2 was untouched; thirteen
     * branches had been pushed without a pull request, and one of them carried
     * item 2 already fixed. The list is the thing that gets agreed, so the
     * answer sits on the item and before it is shown.
     */
    #[Requirement('R-SKL-025')]
    #[Test]
    public function anAuditsListSaysWhatTheRepositoryAlreadyCarriesUnmerged(): void
    {
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-health/SKILL.md',
        );
        $flat = self::flat($skill);

        $write = strpos($skill, 'Write one item per finding');
        $flight = strpos($skill, 'Establish what the repository already carries against each item');
        $show = strpos($skill, 'Show that list whole');
        self::assertNotFalse($flight, 'the conformance skill never asks what is already in flight');
        self::assertGreaterThan((int) $write, $flight, 'the question is asked before the list it annotates exists');
        self::assertLessThan((int) $show, $flight, 'the list is shown before what is in flight against it is known');

        // The surface, because the obvious reading is a third of it. The branch
        // with no pull request is the one that was missed.
        self::assertStringContainsString('wider than the open pull requests', $flat);
        self::assertStringContainsString('branches pushed without one, and the maintained release lines', $flat);

        // One answer per item, beside the state step 5 already gives it.
        foreach (['**untouched**', '**carried**', '**colliding**'] as $state) {
            self::assertStringContainsString($state, $skill, 'the item cannot come back ' . $state);
        }

        // The method, measured on 2026-08-21 against a fixture carrying a
        // squash-merged, a rebase-merged and a partly landed branch. Only the
        // empty answer settles anything: a branch whose fix landed and whose
        // files the base edited afterwards diffs exactly like an outstanding
        // one, which is the reading the run that reported this got to in four
        // attempts and the next one would repeat.
        self::assertStringContainsString('git diff --name-only <base>...<branch>', $skill);
        self::assertStringContainsString('git diff <base> <branch> -- <those files>', $skill);
        self::assertStringContainsString('the base already holds what the branch has in those files', $flat);
        self::assertStringContainsString('Non-empty is not the opposite of that', $flat);
        self::assertStringContainsString(
            '`git cherry` compares patch ids and calls a squash-merged branch fully outstanding',
            $flat,
        );

        // The forge half is not assumable, and the git half does not stand in
        // for it: a pull request from a fork is in no branch listing.
        self::assertStringContainsString('reachable only through the forge', $flat);
        self::assertStringContainsString('Assume none of the three', $flat);
        self::assertStringContainsString('say the pull requests were not read and ask the maintainer', $flat);

        // And an item a branch claims is not thereby off the list.
        self::assertStringContainsString('An unmerged branch holds a claim about the finding rather than the fix', $flat);
        self::assertStringContainsString('*What a dropped candidate owes* is', $flat);
    }

    #[Requirement('R-SKL-004')]
    #[Test]
    public function theCheckLayerIsMeasuredAgainstACompleteOne(): void
    {
        // The file-tree trap again, one surface further in. Two REVIEW-02 runs
        // in a checkout with no analyser, no analysis step and no baseline
        // produced no finding about static analysis, and the second of them had
        // run both declared checks and reported their ceiling: the surface read
        // "declared validation commands", so what the repository does not
        // declare was not a surface and its absence could not be a finding.
        $checklist = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-health/references/checklist.md',
        );

        self::assertStringNotContainsString('declared validation commands', $checklist);
        self::assertStringContainsString('## The check layer', $checklist);
        self::assertStringContainsString(
            'commands a repository declares are where this surface is read, never what it is',
            self::flat($checklist),
        );

        // The expectation is the same one the skill that establishes a missing
        // check measures against, named by what each check establishes rather
        // than by the tool behind it.
        foreach ([
            'Syntax',
            'Static analysis',
            'Coding standards',
            'Manifests and dependencies',
            'Shipped configuration and data',
            'Shipped frontend assets',
        ] as $check) {
            self::assertStringContainsString(
                '- **' . $check . '**',
                $checklist,
                $check . ' is not part of what a complete check layer covers',
            );
        }

        // What decides whether a check applies is what the package ships, not
        // what it declares a command for — otherwise the surface is back where
        // it was, and the missing one reads as an optional subsystem the
        // opening line already excuses.
        self::assertStringContainsString(
            'no command covers is a gap in the layer rather than an optional subsystem, and that absence is the finding',
            self::flat($checklist),
        );
        self::assertStringContainsString('the ceiling of what', $checklist);

        // The routing b0eded4 established stays: the review names the gap and
        // hands it on. The tool per check is not repeated here — it is one
        // package name in two published skills otherwise, and the review does
        // not need it to see that a check is missing.
        self::assertStringContainsString('`typo3-extension-testing`', $checklist);
        foreach (['phpstan', 'php-cs-fixer', 'eslint', 'stylelint'] as $tool) {
            self::assertStringNotContainsString(
                $tool,
                $checklist,
                $tool . ' is named where the checklist only has to see the check is missing',
            );
        }
    }

    #[Test]
    public function contractCasesExerciseTaskSkillBehavior(): void
    {
        $cases = Scenarios::contracts();

        $ids = ['SKILL-01', 'SKILL-02', 'SKILL-03', 'SKILL-04', 'SKILL-05', 'SKILL-06', 'SKILL-07', 'SKILL-08', 'SKILL-09'];
        foreach ($ids as $id) {
            self::assertArrayHasKey($id, $cases);
            self::assertStringStartsWith('scenarios/contracts/task-skills/', $cases[$id]['file']);
            self::assertNotSame([], $cases[$id]['outcomes'], $id . ' says nothing about what has to come out of it');
            self::assertNotSame([], $cases[$id]['failures'], $id . ' says nothing about how it fails');

            // A case names the task a user brings, never the tool or workflow
            // the answer is supposed to reach for.
            $text = implode(' ', [$cases[$id]['prompt'], ...$cases[$id]['outcomes'], ...$cases[$id]['failures']]);
            self::assertStringNotContainsString('typo3_', $text, $id . ' names a tool of this server');
        }
    }

    /**
     * `R-SKL-018`. A crossing between two skills is a step, not a paragraph.
     *
     * Each of the three core crossings named its successor in prose about
     * ownership, and each was read by a session that then did the successor's
     * work itself, reconstructing an order that was one call away (`D-SKL-022`).
     * The extension crossings came in on the report that falsified the reading
     * which had left them out: a skill closed on an imperative naming three
     * successors and none of the three fired (`D-SKL-053`), so the imperative is
     * not the property — what the two crossings that fired carry beside it is
     * the moment, and every crossing here is written at the point it happens.
     * That the moment is the right one is a reading of the workflow rather than
     * a property of a file.
     */
    #[Decision('D-SKL-022')]
    #[Requirement('R-SKL-018')]
    #[Decision('D-SKL-053')]
    #[Test]
    public function aSkillThatHandsOverSaysToInvokeTheSuccessor(): void
    {
        $crossings = [
            'typo3-core-issue-triage' => ['typo3-core-patch-development'],
            // The second one is new with `D-ANS-112`: a review establishes the
            // patch from the review server now, so needing it on disk is a
            // moment rather than the premise the workflow opened on.
            'typo3-core-patch-review' => ['typo3-core-patch-development', 'typo3-core-patch-checkout'],
            // The second one closes the edge the sweep of 2026-08-09 missed by
            // direction: it read the crossings running out of the three core
            // skills, and the one running into the checkout was counted
            // nowhere. `feedback/2026-08-24-205158` then fetched three patches
            // while holding these two skills and opened it for none of them.
            'typo3-core-patch-development' => ['typo3-core-patch-review', 'typo3-core-patch-checkout'],
            // The way back out of the fourth way in — `D-KNW-129`. A patch set
            // carried onto somebody else's change is pushed by the workflow
            // that owns the amend, and the checkout skill had no crossing at
            // all until the case it hands over existed.
            'typo3-core-patch-checkout' => ['typo3-core-patch-development'],
            // The fourth is the one the count decides: a table an editor
            // maintains at four figures is a module rather than a record list,
            // and that is settled with the TCA — `feedback/2026-08-31-233952`
            // reported 3101 records maintained through the generic list.
            'typo3-content-element-development' => [
                'typo3-backend-module-development',
                'typo3-extension-testing',
                'typo3-extension-documentation',
                'typo3-extension-health',
            ],
            'typo3-backend-module-development' => [
                'typo3-development-installation',
                'typo3-extension-documentation',
            ],
            // A change against the core arrives on Gerrit rather than as a
            // pull request against a package, so this workflow has no case
            // that reaches `typo3-core-patch-review` — the reviewer of
            // 2026-08-19 could name none, and the crossing came out.
            'typo3-extension-patch-review' => ['typo3-extension-health'],
            // The deprecation log a first boot writes is about the package's
            // code and not about the installation, and `071526` read it,
            // reported it and stopped there because no workflow owned it. The
            // testing crossing was listed nowhere here, which is how it stayed
            // in the closing paragraph through the pass that rewrote the other
            // seven — `074245` then read that paragraph and wrote tests anyway.
            'typo3-development-installation' => [
                'typo3-extension-health',
                'typo3-extension-testing',
            ],
            // The build workflow reaches all three, and each at a different
            // moment: the harness where nothing asserts the build, the range
            // where the change is a major being added or dropped, and the audit
            // where the request was never one agreed change.
            'typo3-extension-asset-build' => [
                'typo3-extension-testing',
                'typo3-extension-upgrade',
                'typo3-extension-health',
            ],
        ];

        foreach ($crossings as $name => $successors) {
            $body = self::flat((string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md'));

            foreach ($successors as $successor) {
                self::assertMatchesRegularExpression(
                    '/[Ii]nvoke `' . preg_quote($successor, '/') . '`/',
                    $body,
                    $name . ' names ' . $successor . ' without telling the session to invoke it',
                );
            }
        }

        // The crossing whose successor is decided per case is an instruction
        // too. `typo3-extension-health` picks the owner per finding and
        // then per item, and the other three per what the reading turned up —
        // none of them can name one skill, and all of them can say what the
        // session does.
        foreach ([
            'typo3-extension-health',
            'typo3-extension-documentation',
            'typo3-extension-testing',
            'typo3-extension-upgrade',
        ] as $name) {
            self::assertMatchesRegularExpression(
                '/[Ii]nvoke the (skill|workflow) that owns/',
                self::flat((string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md')),
                $name . ' hands over to an owner it decides per case and does not say to invoke it',
            );
        }

        // And the position, which is the half the imperative alone did not
        // hold: the closing paragraph is where a workflow is being left, and a
        // crossing that stands only there is the sentence three sessions read
        // and did not act on. The ownership paragraph stays — it is what tells
        // a reader where the boundary is — and it carries no instruction.
        //
        // Both verbs, because reading for one held the crossing where its
        // author happened to pick that word: `typo3-development-installation`
        // closed on "activate it" and passed this until `074245` reported the
        // paragraph unacted on.
        foreach (self::skills() as $name => $skill) {
            $paragraphs = preg_split('/\R{2,}/', trim($skill));
            self::assertIsArray($paragraphs);

            foreach (['nvoke', 'ctivate'] as $imperative) {
                self::assertStringNotContainsString(
                    $imperative,
                    (string) end($paragraphs),
                    $name . ' leaves a crossing in the paragraph the workflow is being left in',
                );
            }
        }

        // And the one that reads as somebody else's patch says where a commit
        // of your own belongs, because that description is why a session asked
        // to rebase its own work correctly did not open it.
        self::assertStringContainsString(
            'typo3-core-patch-development',
            self::description('typo3-core-patch-checkout'),
        );
    }

    /**
     * The crossing out of a review is the one moment the base has to run a
     * second time, and the session arrives at it having walked that base once
     * and discharged it — `D-SKL-072`. A session that crossed correctly then
     * wrote a core patch without the sweep and without hints on the paths it
     * edited, because the paragraph it read named a skill and no call.
     *
     * Three, and each with the argument the review has just established: the
     * successor's own step 1 already says to work through the base, and what
     * that sentence cannot carry is which arguments changed at the crossing.
     */
    #[Decision('D-SKL-072')]
    #[Test]
    public function theCrossingOutOfAReviewNamesTheCallsTheOrderRestartsWith(): void
    {
        $skill = (string) file_get_contents(Paths::root() . '/skills/typo3-core-patch-review/SKILL.md');
        $section = strstr($skill, '## Where the review ends and the rework begins');
        self::assertIsString($section, 'the review skill has no crossing section to restart the order in');
        $crossing = self::flat($section);

        foreach ([
            'typo3_task_guide' => 'change type about to be written',
            'typo3_hint_lookup' => 'paths about to be edited',
            'typo3_changelog_lookup' => 'deprecation sweep',
        ] as $tool => $argument) {
            self::assertStringContainsString(
                '`' . $tool . '`',
                $crossing,
                'the crossing out of the review does not name ' . $tool,
            );
            self::assertStringContainsString(
                $argument,
                $crossing,
                'the crossing names ' . $tool . ' without the argument the review established',
            );
        }

        // Three calls and not a checklist, which is what the count is for: a
        // list is skipped as one item, and the three here are the ones the
        // crossing changes the answer to rather than the order it restarts.
        preg_match_all('/`(typo3_\w+)`/', $crossing, $matches);
        self::assertSame(
            ['typo3_task_guide', 'typo3_hint_lookup', 'typo3_changelog_lookup'],
            array_values(array_unique($matches[1])),
            'the crossing out of the review names other calls than the three the order restarts with',
        );
    }

    /**
     * The crossing is written to be recognised in what the reader says, and
     * both failures on record are readings of that sentence — one crossed on a
     * remark about a finding's weight, and one stayed under review rules
     * through a sentence the enumeration does not reach (`D-SKL-077`).
     *
     * So the act is named beside it: the first edit to a file meant to survive
     * is observable from inside the session, where a sentence to recognise is
     * not. The probe is what that act is not, and the boundary between them
     * stands in the verification section, which the crossing points at rather
     * than copies.
     */
    #[Decision('D-SKL-077')]
    #[Test]
    public function theCrossingOutOfAReviewNamesTheEditThatBeginsTheRework(): void
    {
        $skill = (string) file_get_contents(Paths::root() . '/skills/typo3-core-patch-review/SKILL.md');
        $section = strstr($skill, '## Where the review ends and the rework begins');
        self::assertIsString($section, 'the review skill has no crossing section to name the act in');
        $crossing = self::flat($section);

        self::assertMatchesRegularExpression(
            '/first edit[^.]*meant to survive[^.]*`typo3-core-patch-development`/',
            $crossing,
            'the crossing out of the review names no act that asks whether the successor should be running',
        );
        self::assertMatchesRegularExpression(
            '/probe is not[^.]*no diff/',
            $crossing,
            'the crossing names the act without telling a scratch probe from it',
        );

        // Pointed at, not copied: what a probe owes is the verification
        // section's, and a second copy of it here is the one that goes stale.
        self::assertStringContainsString(
            'the restoration is verified rather than assumed',
            self::flat(strstr($skill, '## Where the review ends and the rework begins', true) ?: ''),
            'the boundary the crossing points at has left the verification section',
        );

        // The enumerated phrases stay. One session crossed on "fertigstellen",
        // which is "finish it" in the reader's own words, and a trigger that
        // has run is not traded for one that has not.
        foreach (['finish it', 'fix it', 'amend it', 'write the test'] as $phrase) {
            self::assertStringContainsString(
                '"' . $phrase . '"',
                $crossing,
                'the crossing out of the review no longer names the instruction "' . $phrase . '"',
            );
        }
    }

    /**
     * One skill's front matter, as a reader of the standard gets it rather than
     * as a pattern here finds it.
     *
     * @return array<string, mixed>
     */
    private static function frontMatter(string $name, string $skill): array
    {
        self::assertSame(
            1,
            preg_match('/\A---\R(.*?)\R---\R/s', $skill, $block),
            $name . ' has no front matter',
        );

        try {
            $matter = Yaml::parse($block[1] ?? '');
        } catch (ParseException $exception) {
            self::fail($name . ' has front matter no reader of the standard can parse: ' . $exception->getMessage());
        }
        self::assertIsArray($matter, $name . ' has front matter that is not a mapping');

        /** @var array<string, mixed> $matter */
        return $matter;
    }

    private static function description(string $name): string
    {
        $skill = (string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md');
        if (preg_match('/\ndescription: (.+)\n/', $skill, $matches) !== 1) {
            self::fail($name . ' has no description');
        }

        return $matches[1];
    }

    /**
     * A skill read with its wrapping taken out.
     *
     * What these assert is the wording, not where the line ends. Matched
     * against the file as it stands, a sentence that moves one word breaks a
     * test about something the change never touched — which is what a rewrap
     * of the corpus did to six of them.
     */
    private static function flat(string $skill): string
    {
        return (string) preg_replace('/\s+/', ' ', $skill);
    }


    /**
     * Where a skill routes to a tool: the first mention that is not the
     * sentence discharging the call.
     *
     * The body is read flat, because what is skipped is a sentence and a rewrap
     * moves its line break.
     */
    private static function routing(string $body, string $tool): int|false
    {
        $at = strpos($body, $tool);
        while ($at !== false) {
            if (!str_starts_with(substr($body, $at + strlen($tool)), self::DISCHARGE)) {
                return $at;
            }
            $at = strpos($body, $tool, $at + 1);
        }

        return false;
    }

    /**
     * Everything one skill installs into another project: its body and every
     * reference beside it.
     *
     * @return array<string, string>
     */
    private static function published(string $name, string $skill): array
    {
        $files = ['SKILL.md' => $skill];

        $directory = Paths::root() . '/skills/' . $name . '/references';
        if (!is_dir($directory)) {
            return $files;
        }
        foreach (Finder::create()->files()->in($directory)->depth(0)->name('*.md')->sortByName() as $reference) {
            $files['references/' . $reference->getFilename()] = (string) file_get_contents($reference->getPathname());
        }

        return $files;
    }

    /**
     * Every published skill, read from the directory the installer publishes.
     *
     * @return array<string, string>
     */
    private static function skills(): array
    {
        $skills = [];
        foreach (Finder::create()->files()->in(Paths::root() . '/skills')->depth(1)->name('SKILL.md')->sortByName() as $path) {
            $skills[$path->getRelativePath()] = (string) file_get_contents($path->getPathname());
        }

        self::assertNotSame([], $skills);

        return $skills;
    }
    /**
     * A copy of `skills/` taken by hand is unsupported, and the readme says so.
     *
     * The base is written into each skill by the installer, and every skill
     * opens on it — so a hand-copied tree loses the order every task starts in
     * and the session never learns that. `D-SKL-036` decided against making the
     * copy fail louder, which leaves this sentence as the whole of the warning
     * and puts it where somebody about to copy the directory is reading.
     */
    #[Decision('D-SKL-036')]
    #[Test]
    public function theReadmeSaysAHandCopiedSkillIsUnsupported(): void
    {
        $readme = (string) file_get_contents(Paths::root() . '/readme.md');

        self::assertStringContainsString('only supported way to get the skills', $readme);
        self::assertStringContainsString('references/base.md', $readme);

        // And what the sentence rests on: the file it says is missing is one
        // the installer writes rather than one the directory carries.
        self::assertFileDoesNotExist(Paths::root() . '/skills/typo3-core-patch-review/references/base.md');
        self::assertFileExists(Paths::root() . '/skills/base.md');
    }

}
