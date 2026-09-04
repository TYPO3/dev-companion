<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Knowledge\Domains;
use TYPO3\DevCompanion\Knowledge\Scope;
use TYPO3\DevCompanion\Knowledge\TestSuiteHints;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * Which Build/Scripts/runTests.sh suites a change actually needs.
 */
final class TestRunGuide extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_test_run_guide';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Knowledge];
    }

    public static function description(): string
    {
        return 'Say what this core checkout needs before a test can run at all, and which Build/Scripts/runTests.sh commands to run once it can. Ask it before checking for vendor/bin/phpunit by hand: the suites run in containers, so the shell\'s PHP is not the interpreter they run under and a missing vendor directory means considerably less than it looks like. Pass the changed paths and the answer is narrowed to the suites that can actually fail on them — a Sass-only change gets the CSS suites, not the PHP ones. Every suite comes back marked by what running it does to the checkout: a check that hands it back as it was, a change that rewrites files, git where the suite runs `git add *` over the working tree, or unknown where the body does not say. A task told not to change files reads that before it pastes a command, and the frontend build is a change rather than a check. Which suites the script offers changes between majors, so a suite that branch does not have is left out rather than handed over as a command. The script belongs to the core repository, so paths that read as a project or third-party extension get no suite at all rather than commands that cannot run there. The script\'s own notes are typo3_script_lookup: the commands it offers per subject, and what the pre-commit hook does to a commit.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Test or script topic, for example functional, phpstan, TypeScript, composer, or CGL.'],
                'paths' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'The changed file paths, as they are in the repository they belong to. Given, only suites touching their domains are returned. Each path is placed on its own: one outside the core narrows nothing and is named in the answer, because runTests.sh is not in its repository. One no suite covers is named too, so a path nothing checks is read off the answer rather than out of its silence.'],
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version the commands have to run on, for example "13.4" or "14". Suites that branch\'s runTests.sh does not have are left out. Defaults to the version of the installation this server was started in; where there is none, every suite is listed.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'query' => Schema::nullableString(),
            'paths' => Schema::listOf(Schema::string(), 'The paths the answer was narrowed by, given ones and ones named in the query.'),
            'scopes' => Schema::scopes('Which kind of work each path is. Only core paths can run a suite: runTests.sh is not in a project or an extension repository, so the others are named in the answer and narrow nothing.'),
            'domains' => Schema::listOf(Schema::string(), 'Domains those paths touch. Empty means nothing was narrowed.'),
            'uncoveredPaths' => Schema::listOf(Schema::string(), 'Given paths no suite covers. A suite is reached '
                . 'through the domain of a path, and these reach none, so nothing in suites is about them and '
                . 'nothing in it fails on them.'),
            'withheld' => Schema::object([
                'domains' => Schema::listOf(Schema::string(), 'Domains no given path reached. A path landing in one of them means calling again, because this answer holds for the path set it was given.'),
                'suites' => Schema::integer('How many suites those domains hold on the target version. Counted rather than listed: the list is what the narrowing exists to avoid.'),
            ], ['domains', 'suites'], 'What the narrowing left out. Both empty where nothing was narrowed.'),
            'suites' => Schema::listOf(Schema::testSuiteRecord(), 'Every suite of the domains above, and where query '
                . 'scores on some of them, those alone, strongest first. This is the list typo3_task_guide narrows '
                . 'two ways: its checks is what a task in these domains runs whatever it turns out to be, and its '
                . 'testSuites the strongest few against the task text.'),
            'invocation' => Schema::object([
                'preconditions' => Schema::listOf(Schema::string(), 'What has to be true before any suite runs: the container the script starts, and the vendor/ and bin/ the checkout may not have. This is the question a caller holds at the moment it starts checking for vendor/bin/phpunit by hand, and the shell\'s PHP is not the interpreter the answer is about.'),
                'beforeYouRun' => Schema::string('The one note that is about losing work rather than about running '
                    . 'a suite: what a run can take with it, and what to do before starting one. Carried by '
                    . 'typo3_task_guide as well, because that is the call that hands over a command first.'),
                'notes' => Schema::listOf(Schema::string()),
                'options' => Schema::listOf(Schema::object([
                    'option' => Schema::string(),
                    'description' => Schema::string(),
                ], ['option', 'description'])),
                'examples' => Schema::listOf(Schema::object([
                    'purpose' => Schema::string(),
                    'command' => Schema::string(),
                ], ['purpose', 'command'])),
            ], ['preconditions', 'beforeYouRun', 'notes', 'options', 'examples']),
        ], ['scopes', 'uncoveredPaths', 'withheld', 'suites', 'invocation']);
    }

    public static function answer(array $args): ToolResult
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;

        /** @var array<int, string> $paths */
        $paths = array_map('strval', $args['paths'] ?? []);
        $paths = array_values(array_unique(array_merge($paths, Domains::pathsIn((string) $query))));
        $target = Versions::target(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);

        // Every suite this guide knows is a Build/Scripts/runTests.sh
        // invocation, and that script is part of the core repository. Handing
        // it to a site package is worse than declining: the commands look
        // copy-pasteable and none of them exists there. Which paths those are
        // is decided one by one — the other half of a call is not evidence
        // about this path.
        $scopes = Scope::ofEach($paths, (string) $query);
        $outside = Scope::pathsOf($scopes, Scope::Project, Scope::Extension);
        // A path nothing placed is answered from the core, which is where a
        // call that says nothing is answered from at all — unless the call has
        // been placed outside it already, and then the unplaced path is in that
        // repository too rather than in the one with the script (`D-SCO-016`).
        $runnable = Scope::everyPlacedPathIsOutsideTheCore(Scope::groups($paths, $scopes, (string) $query))
            ? []
            : Scope::pathsOf($scopes, Scope::Core, Scope::Uncertain);
        $domains = Domains::fromPaths($runnable);

        // A suite is selected by the domains of the paths, so a path that
        // reaches none selected nothing and is not what any command below runs
        // over. Nothing said so, and the session that reported it worked out by
        // hand that no suite covers an XML reference file (`D-GUI-022`).
        $uncovered = array_values(array_filter(
            $runnable,
            static fn(string $path): bool => Domains::fromPaths([$path]) === [],
        ));

        // Nothing here can run a suite: every path given is outside the core,
        // or none was and what the query says is.
        $nothingRunnable = $paths === []
            ? Scope::of('', (string) $query)->isOutsideTheCore()
            : $runnable === [];
        if ($nothingRunnable) {
            return ToolResult::create(
                Scope::OUTSIDE_CORE_NOTICE . ' Build/Scripts/runTests.sh is part of the core repository, so the '
                . 'suites this guide knows cannot be run from here and are left out rather than handed over. '
                . 'What such a repository needs instead — assembling a phpunit suite of its own, the browser '
                . 'tests that go with it, and the static analysis the phpstan suite is the core\'s half of — is '
                . 'typo3_hint_lookup with id=project-extension-tests, id=browser-tests and '
                . 'id=extension-static-analysis. typo3_server_scope states the boundary.',
                [
                    'query' => $query,
                    'paths' => $paths,
                    'scopes' => $scopes,
                    'domains' => $domains,
                    'uncoveredPaths' => $uncovered,
                    'withheld' => TestSuiteHints::withheld($domains, $target),
                    'suites' => [],
                    'invocation' => ['preconditions' => [], 'beforeYouRun' => '', 'notes' => [], 'options' => [], 'examples' => []],
                ],
            );
        }

        $hints = TestSuiteHints::find($query, $domains, $target);

        $blocks = [];
        // The other half of the same answer: the suites below are for the paths
        // that can run them, and the paths that cannot are named rather than
        // left to read the answer as theirs.
        if ($outside !== []) {
            $blocks[] = Scope::outsideCoreAmong($outside) . ' Build/Scripts/runTests.sh is not there, so no suite '
                . 'below is about ' . (count($outside) === 1 ? 'that path' : 'those paths') . '. What such a '
                . 'repository needs instead is typo3_hint_lookup with id=project-extension-tests, '
                . 'id=browser-tests and id=extension-static-analysis.';
        }
        // A call that named no path is answered from the core root, and every
        // command below says so itself. A path that was named and could not be
        // placed is the case worth a sentence: the caller believes it said
        // which repository this is.
        if (Scope::pathsOf($scopes, Scope::Uncertain) !== []) {
            $blocks[] = Scope::UNCERTAIN_NOTICE;
        }
        // Before the suites rather than after them, because a caller reaching
        // for `ls` and `command -v` has not got as far as choosing one
        // (`D-AUD-009`).
        $blocks[] = self::preconditionBlock();
        $withheld = TestSuiteHints::withheld($domains, $target);
        if ($domains !== []) {
            $narrowing = sprintf(
                'Narrowed to the %s domain(s) the given paths touch. Suites outside them cannot fail on this change; '
                . 'call again without paths to see all of them.',
                self::named($domains)
            );
            // The half a session holding this answer through a rework needs: a
            // path set grows, and an answer that names only what it kept reads
            // as the suites for the change rather than for the paths given
            // (`D-ANS-074`).
            if ($withheld['domains'] !== []) {
                $narrowing .= sprintf(
                    ' No given path reached %s, which leaves %s out. A path landing in one of those domains means '
                    . 'calling again.',
                    self::named($withheld['domains']),
                    $withheld['suites'] === 1 ? 'one suite' : $withheld['suites'] . ' suites',
                );
            }
            $blocks[] = $narrowing;
        }
        // Beside the narrowing rather than after the suites, because both
        // sentences answer the same question: which of the paths given this
        // list of commands is about (`D-GUI-022`).
        if ($uncovered !== []) {
            $blocks[] = sprintf(
                'No runTests.sh suite covers %s. A suite is reached through the domain of a path and %s, so '
                . 'nothing below runs over %s.',
                self::named($uncovered),
                count($uncovered) === 1 ? 'that one reaches none' : 'those reach none',
                count($uncovered) === 1 ? 'it' : 'them',
            );
        }
        if ($hints === []) {
            $blocks[] = sprintf(
                'No runTests.sh suite matched "%s". Try "unit", "functional", "phpstan", "checkRst", "build", "composer", or "npm".',
                (string) $query
            );
        } else {
            foreach ($hints as $hint) {
                $block = [
                    '## ' . $hint['suite'],
                    'Command from the TYPO3 core root:',
                    '`' . $hint['command'] . '`',
                    self::runsLine($hint['runs']),
                ];
                if ($hint['targeted'] !== null) {
                    $block[] = 'Targeted run while iterating:';
                    $block[] = '`' . $hint['targeted'] . '`';
                }
                $block[] = '';
                $block[] = $hint['description'];
                $block[] = $hint['whenToUse'];
                $blocks[] = implode("\n", $block);
            }

            // An e2e suite puts a browser in front of the change and stops
            // there. What to do with it, and what to do when that styleguide
            // instance has not got the content the defect needs, is the page
            // named below (`D-KNW-069`).
            $browser = array_filter($hints, static fn(array $hint): bool => str_starts_with($hint['suite'], 'e2e'));
            if ($browser !== []) {
                $blocks[] = self::BROWSER_CHECK_GUIDE;
            }

            // A functional suite says whether it passed and not what it
            // rendered, and the page that says how to see the second one is
            // gated on TypoScript everywhere else (`D-KNW-122`).
            $functional = array_filter($hints, static fn(array $hint): bool => str_starts_with($hint['suite'], 'functional'));
            if ($functional !== []) {
                $blocks[] = self::RENDERING_PROBE_GUIDE;
            }
        }

        $blocks[] = self::invocationBlock();
        $blocks[] = self::SCRIPTS_GUIDE;

        return ToolResult::create(implode("\n\n", $blocks), [
            'query' => $query,
            'paths' => $paths,
            'scopes' => $scopes,
            'domains' => $domains,
            'uncoveredPaths' => $uncovered,
            'withheld' => $withheld,
            'suites' => TestSuiteHints::records($hints),
            'invocation' => TestSuiteHints::invocation(),
        ]);
    }

    /**
     * What running the command above does to the checkout, beside the command
     * rather than under the description.
     *
     * A review told to change nothing was offered `-s build` first and worked
     * out by hand that it regenerates the committed JavaScript (`R-ANS-034`).
     * The word is the one the data carries, so a caller reading the text filters
     * on the same value as one reading `runs`.
     */
    private static function runsLine(string $runs): string
    {
        return 'Running it: ' . match ($runs) {
            'check' => 'check — it reports and hands the checkout back as it was.',
            'change' => 'change — it rewrites files in the checkout.',
            'git' => 'git — it runs git over the working tree, so the index and uncommitted edits are at stake.',
            default => 'unknown — the body does not say what it does to the checkout.',
        };
    }

    /**
     * A list of domains as a sentence reads it: commas, and "and" before the
     * last. The withheld line names five of them on an ordinary call.
     *
     * @param array<int, string> $words
     */
    private static function named(array $words): string
    {
        if (count($words) < 3) {
            return implode(' and ', $words);
        }

        $last = array_pop($words);

        return implode(', ', $words) . ' and ' . $last;
    }

    /**
     * The document this answer is the short form of, named where it is used.
     *
     * A session that had every `runTests.sh` question answered here never
     * reached `typo3_script_lookup`, whose description reads as a subset of this
     * one, and so never saw the guide either (`D-ANS-061`). The moment a caller
     * is about to run something is the one moment they are certainly reading,
     * which is why the pointer sits here rather than waiting for somebody to ask
     * what exists.
     */
    public const SCRIPTS_GUIDE = "## The whole procedure\n"
        . 'This is the suites and how to invoke them. The rest is one call away — typo3_rule_lookup with '
        . "documentId \"core/testing/scripts\", which needs no resource list.\n"
        . '- Why a suite runs against the `vendor/` and `bin/` of the directory it was started from, and what '
        . "`exec: line 9: bin/phpunit: not found` means — it names phpunit rather than the directory.\n"
        . '- Why `-s cglGit` reports SUCCESS having read no file from a git worktree, and that `-s cgl` is the '
        . 'one that works from either.';

    /**
     * The page the e2e suites hand the caller over to, named where they are.
     *
     * A session reviewing a backend CSS patch held `any/testing/browser-check`,
     * never opened it, and told its reader five times it could not judge the
     * change visually (`D-KNW-069`). The suite was already in front of it; the
     * page saying what to do with a styleguide instance that has not got the
     * content was not.
     */
    public const BROWSER_CHECK_GUIDE = "## Looking at it rather than asserting it\n"
        . 'The suites above start a browser and stop there. The rest is one call away — typo3_rule_lookup with '
        . "documentId \"any/testing/browser-check\", which needs no resource list.\n"
        . '- The instance `-s e2e-prepare` installs is a styleguide and carries no content beyond the components '
        . 'it demonstrates. Where the defect needs content, the installation that has it is the one to look at, '
        . 'and a browser in a container reaches a running DDEV site over `ddev_default` rather than over '
        . "`host.docker.internal`.\n"
        . '- Where the harness and its screenshots go: `Build/typo3temp/` is not ignored, so one written there '
        . 'lands in the next commit.';

    /**
     * The page a functional suite hands the caller over to, named where that
     * suite is.
     *
     * A review of a PHP diff read the gate that names this page for TypoScript,
     * skipped it, and then built the same harness by hand over six container
     * rounds (`D-KNW-122`). A suite answers whether it passed; a finding about a
     * rendering turns on what came out, which is the other question and the one
     * a caller holds at the moment it writes the test.
     */
    public const RENDERING_PROBE_GUIDE = "## Seeing what it rendered rather than whether it passed\n"
        . 'A functional suite answers pass or fail, and a finding that turns on what a rendering contains needs '
        . 'the output itself. The procedure is one call away — typo3_rule_lookup with documentId '
        . "\"core/testing/proving-a-rendering\", which needs no resource list.\n"
        . '- How the output is got out of a run that would otherwise print nothing, and why what a content object '
        . "echoes may never arrive.\n"
        . '- One marker per region of the response, so a rendering says which part of it changed rather than that '
        . 'it changed, and how what a service holds mid-request is printed.';

    /**
     * What has to be true before any of the suites below can run.
     *
     * A session establishing whether a bug reproduced reached for `ls` and
     * `command -v`, found no `vendor/bin/phpunit`, and reported the functional
     * suite as not executed — with this tool in its list and its schema never
     * loaded. Both facts it needed were in this answer and both were below
     * every suite block (`D-AUD-009`).
     */
    private static function preconditionBlock(): string
    {
        $lines = ['## Before a suite can run'];
        foreach (TestSuiteHints::invocation()['preconditions'] as $note) {
            $lines[] = '- ' . $note;
        }

        return implode("\n", $lines);
    }

    /**
     * The invocation rules that apply to every suite. Emitted with every answer:
     * without CI=true and the passthrough form, a suite command alone is rarely
     * what a patch actually needs.
     */
    private static function invocationBlock(): string
    {
        $invocation = TestSuiteHints::invocation();

        $lines = ['## Invoking runTests.sh'];
        // First, because it is the only one about work that can be lost rather
        // than about a run that can fail — `D-ANS-145`.
        $lines[] = '- ' . $invocation['beforeYouRun'];
        foreach ($invocation['notes'] as $note) {
            $lines[] = '- ' . $note;
        }

        $lines[] = '';
        $lines[] = 'Options:';
        foreach ($invocation['options'] as $option) {
            $lines[] = '- `' . $option['option'] . '` — ' . $option['description'];
        }

        $lines[] = '';
        $lines[] = 'Examples:';
        foreach ($invocation['examples'] as $example) {
            $lines[] = '- ' . $example['purpose'] . ':';
            $lines[] = '  `' . $example['command'] . '`';
        }

        return implode("\n", $lines);
    }
}
