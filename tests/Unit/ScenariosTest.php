<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Knowledge\TaskIntents;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Directory;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Scenarios;

#[Requirement('R-FBK-003')]
final class ScenariosTest extends TestCase
{
    private string $runs = '';

    protected function tearDown(): void
    {
        if ($this->runs === '') {
            return;
        }
        Directory::remove($this->runs);
    }

    #[Test]
    public function everyForwardReviewIsReadableAsData(): void
    {
        $reviews = Scenarios::load();

        self::assertNotSame([], $reviews);
        self::assertSame(
            $this->headings('/scenarios/forward'),
            array_keys($reviews),
            'a forward review heading was not parsed, or was parsed twice',
        );
        $this->assertReadable($reviews, Scenarios::vocabulary('Mark'));
    }

    #[Test]
    public function everyContractCaseIsReadableAsData(): void
    {
        $cases = Scenarios::contracts();

        self::assertNotSame([], $cases);
        self::assertSame(
            $this->headings('/scenarios/contracts'),
            array_keys($cases),
            'a contract case heading was not parsed, or was parsed twice',
        );
        $this->assertReadable($cases, Scenarios::vocabulary('Contract'));
    }

    /**
     * One case is one file. A file holding a second prompt is a file where
     * nobody can tell which criteria a judgment answered — and the runner would
     * print two prompts under one id.
     */
    #[Requirement('R-FBK-004')]
    #[Test]
    public function everyCaseHasAFileOfItsOwn(): void
    {
        $files = [];
        foreach ([...Scenarios::load(), ...Scenarios::contracts()] as $id => $scenario) {
            $files[$scenario['file']][] = $id;
        }

        self::assertSame(
            [],
            array_filter($files, static fn(array $ids): bool => count($ids) > 1),
        );
    }

    /**
     * A contract case is never run, so its state is a claim no session ever
     * answers. What holds it therefore has to be named beside it — and a test
     * named there that does not exist is worth less than saying nothing.
     */
    #[Requirement('R-FBK-004')]
    #[Test]
    public function everyContractCaseNamesWhatHoldsIt(): void
    {
        $tests = $this->testMethods();

        foreach (Scenarios::contracts() as $id => $case) {
            self::assertNotSame('', $case['heldBy'], $id . ' does not say what holds it');

            preg_match_all('/`(\w+Test::\w+)`/', $case['heldBy'], $matches);
            self::assertTrue(
                $matches[1] !== [] || str_contains($case['heldBy'], 'not guarded'),
                $id . ' names neither a test nor that it is not guarded',
            );
            foreach ($matches[1] as $test) {
                self::assertContains($test, $tests, $id . ' names ' . $test . ', which no test declares');
            }
        }
    }

    /**
     * Every test method in this suite as `Class::method`, read from the files
     * rather than from reflection: a case may name a test in any of the three
     * directories, and loading them all to ask would be the heavier half.
     *
     * @return array<int, string>
     */
    private function testMethods(): array
    {
        $methods = [];
        foreach (['Unit', 'Contract', 'Smoke'] as $suite) {
            foreach (Finder::create()->files()->in(Paths::root() . '/tests/' . $suite)->depth(0)->name('*Test.php')->sortByName() as $file) {
                preg_match_all('/public function (\w+)\(/', (string) file_get_contents($file->getPathname()), $matches);
                foreach ($matches[1] as $method) {
                    $methods[] = $file->getBasename('.php') . '::' . $method;
                }
            }
        }

        return $methods;
    }

    /**
     * The cases `D-GUI-015` measured, each held by the words it is written in
     * rather than by a brief that names the answer. Adding this before the
     * needles were curated would have fixed the miss into the suite, which is
     * why the entry deferred it; the needles are curated, so the arrival is
     * what is asserted from here.
     *
     * One case may claim two rows. `SKILL-07` is a task that crosses from one
     * workflow to another, and a row per half is what says the second one
     * arrives too — `D-SKL-066` — `D-GUI-018`.
     */
    #[Decision('D-GUI-015')]
    #[Decision('D-GUI-018')]
    #[Decision('D-SKL-066')]
    #[Test]
    #[DataProvider('theCasesWhoseOwnWordsHaveToReachAnIntent')]
    public function aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout(string $id, string $intent): void
    {
        $case = Scenarios::contracts()[$id] ?? null;
        self::assertNotNull($case, $id . ' is not a contract case');

        self::assertContains(
            $intent,
            array_column(TaskIntents::confirmed(TaskIntents::detect($case['prompt'])), 'id'),
            $id . ' is written in words that do not reach ' . $intent,
        );
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function theCasesWhoseOwnWordsHaveToReachAnIntent(): array
    {
        return [
            'a backend module, and not the audit its subject spells' => ['SKILL-07', 'backend-module'],
            'and the documentation half of the same sentence' => ['SKILL-07', 'documentation'],
            'a manual held to the package it describes' => ['SKILL-03', 'documentation'],
            'a security review in a maintainer\'s words' => ['SKILL-11', 'audit'],
            'the goal, where the needles were the mechanism' => ['EXT-08', 'event-listener'],
            'a setting a site set defines' => ['SITE-09', 'site-setting'],
            'the majors a package declares, in the words a maintainer states them' => ['EXT-01', 'compatibility'],
        ];
    }

    /**
     * @param array<string, array{title: string, prompt: string, environment: string, status: string, outcomes: array<int, string>, failures: array<int, string>}> $scenarios
     * @param array<int, string> $states
     */
    private function assertReadable(array $scenarios, array $states): void
    {
        $environments = Scenarios::vocabulary('Id');

        foreach ($scenarios as $id => $scenario) {
            self::assertNotSame('', $scenario['title'], $id . ' has no title');
            self::assertNotSame('', $scenario['prompt'], $id . ' has no prompt');
            self::assertNotSame([], $scenario['outcomes'], $id . ' says nothing about what has to come out of it');
            self::assertNotSame([], $scenario['failures'], $id . ' says nothing about how it fails');
            self::assertContains($scenario['environment'], $environments, $id . ' runs in no environment the readme names');
            self::assertContains($scenario['status'], $states, $id . ' carries no state its readme names');
        }
    }

    #[Test]
    public function everyRecordedRunHoldsUpToItsScenario(): void
    {
        $problems = [];
        foreach (Scenarios::runs() as $recorded) {
            $problems = [...$problems, ...$recorded['problems']];
        }

        self::assertSame([], $problems);
    }

    /**
     * The two cases read alike from here, which is why the command reports them
     * rather than failing: a judgment quoting a call the session never made,
     * and one naming a tool in order to say it was never called.
     *
     * @param array<string, mixed> $run
     * @param array<int, string> $expected
     */
    #[Decision('D-EVI-009')]
    #[DataProvider('quotedTools')]
    #[Test]
    public function aRunIsReadAgainstItsOwnTrace(array $run, array $expected): void
    {
        self::assertSame($expected, Scenarios::unbackedTools($run));
    }

    /**
     * @return array<string, array{array<string, mixed>, array<int, string>}>
     */
    public static function quotedTools(): array
    {
        $trace = [['tool' => 'typo3_task_guide', 'arguments' => []]];

        return [
            'a tool the trace carries' => [
                ['toolTrace' => $trace, 'outcomes' => [['met' => true, 'evidence' => 'It read what `typo3_task_guide` returned.']]],
                [],
            ],
            'a tool the trace does not carry' => [
                ['toolTrace' => $trace, 'outcomes' => [['met' => true, 'evidence' => 'Two hints are quoted as `typo3_hint_lookup`.']]],
                ['typo3_hint_lookup'],
            ],
            'a tool quoted in a failure' => [
                ['toolTrace' => $trace, 'failures' => [['avoided' => false, 'evidence' => 'It reported `typo3_icon_lookup` as the source.']]],
                ['typo3_icon_lookup'],
            ],
            'a run with no trace at all' => [
                ['outcomes' => [['met' => true, 'evidence' => 'It read what `typo3_task_guide` returned.']]],
                ['typo3_task_guide'],
            ],
        ];
    }

    #[Requirement('R-FBK-004')]
    #[Test]
    public function aTargetedContractCaseIsNotSomethingARunCanAnswer(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Scenarios::skeleton('SKILL-07', 'testing', 'phpunit', '2026-07-30');
    }

    /**
     * The exit code says whether the case still needs reading by hand, because
     * that is what the recurring todo reading them is due on — `D-FBK-012`. A
     * contract case claims its state on a test, so one whose `Held by` says
     * `not guarded` is the part no test reaches and a session has to stand in
     * for; one that is fully held stops asking without anybody editing the todo.
     *
     * Every case rather than two named ones, because a list of ids in a test is
     * the same thing that went stale in the todo: it was written against the
     * cases of its day and read for months afterwards as though it still named
     * them — `D-EVI-007`.
     */
    #[Decision('D-EVI-007')]
    #[Test]
    public function aContractCaseNoTestHoldsSaysSoWithItsExitCode(): void
    {
        $application = Cli::application();
        $answered = [];
        $expected = [];
        foreach (Scenarios::contracts() as $id => $case) {
            $answered[$id] = $application->doRun(new StringInput('scenarios:contract ' . $id), new BufferedOutput());
            $expected[$id] = str_contains($case['heldBy'], 'not guarded') ? 1 : 0;
        }

        self::assertSame($expected, $answered);
        // Named no case the command answers for all of them at once, which is
        // what the todo runs: one case standing in for the rest goes quiet the
        // day that case is guarded and the others are not.
        self::assertSame(
            in_array(1, $expected, true) ? 1 : 0,
            $application->doRun(new StringInput('scenarios:contract'), new BufferedOutput()),
            'the answer for every case at once disagrees with the cases',
        );
    }

    #[Test]
    public function aRunAddsUpToTheVerdictItsJudgmentsMake(): void
    {
        $missed = $this->record('REVIEW-01', static function (array $run): array {
            $run['outcomes'][0]['met'] = false;

            return $run;
        });

        self::assertSame('covered', $this->record('REVIEW-01', static fn(array $run): array => $run)['verdict']);
        self::assertSame('partial', $missed['verdict']);
    }

    #[Test]
    public function aHalfJudgedRunIsNotAResult(): void
    {
        $recorded = $this->record('REVIEW-01', static function (array $run): array {
            $run['outcomes'] = array_map(static fn(): array => ['met' => null, 'evidence' => ''], $run['outcomes']);

            return $run;
        });

        self::assertSame('', $recorded['verdict']);
        self::assertContains('scenarios/runs/REVIEW-01.json leaves outcomes 1 unjudged', $recorded['problems']);
        self::assertContains('scenarios/runs/REVIEW-01.json gives no evidence for outcomes 1', $recorded['problems']);
    }

    #[Test]
    public function aRunWhoseSessionHasNotHappenedYetIsOpen(): void
    {
        // What `bin/cli scenarios:record` writes. A checker that fails on it stops
        // the repository for as long as a run is open — which is the one time
        // it has to stay usable.
        $skeleton = Scenarios::skeleton('REVIEW-01', 'testing', 'phpunit', '2026-07-30');
        $recorded = $this->record('REVIEW-01', static fn(): array => $skeleton);

        self::assertTrue(Scenarios::isOpen($recorded['run']));
        self::assertSame('', $recorded['verdict']);
        self::assertSame([], $recorded['problems']);
    }

    #[Test]
    public function aRecordedCallSaysWhatItAsked(): void
    {
        // A bare name cannot answer what the runs are judged on: whether the
        // conventions lookup was asked once per surface or once broadly,
        // which version it was given, whether a returned id was followed. All
        // three are in the arguments and nowhere else in the record.
        $recorded = $this->record('REVIEW-01', static function (array $run): array {
            $run['toolTrace'] = [
                ['tool' => 'typo3_project_describe', 'arguments' => []],
                ['tool' => 'typo3_hint_lookup'],
                ['arguments' => ['query' => 'icons']],
            ];

            return $run;
        });

        self::assertSame([
            'scenarios/runs/REVIEW-01.json tool call 2, typo3_hint_lookup, does not say what it was called with',
            'scenarios/runs/REVIEW-01.json tool call 3 does not name the tool it called',
        ], $recorded['problems']);

        // Recorded as a bare name, the way every run was written before the
        // arguments were part of one.
        $named = $this->record('REVIEW-01', static function (array $run): array {
            $run['toolTrace'] = ['typo3_project_describe'];

            return $run;
        });

        self::assertSame(
            ['scenarios/runs/REVIEW-01.json tool call 1 does not name the tool it called'],
            $named['problems'],
        );
    }

    #[Test]
    public function aRunContradictsAReviewWhoseMarkIsNotWhatTheJudgmentsAddUpTo(): void
    {
        $review = Scenarios::load()['REVIEW-01'];
        // Judged into whichever result REVIEW-01 does not currently claim, so
        // this stays a contradiction whatever its mark says today.
        $met = !in_array($review['status'], ['covered', 'boundary'], true);
        $recorded = $this->record('REVIEW-01', static function (array $run) use ($met): array {
            foreach (['outcomes' => 'met', 'failures' => 'avoided'] as $section => $key) {
                $run[$section] = array_map(
                    static fn(array $entry): array => [$key => $met, 'evidence' => $entry['evidence']],
                    is_array($run[$section]) ? $run[$section] : [],
                );
            }

            return $run;
        });

        self::assertContains(
            sprintf(
                'scenarios/runs/REVIEW-01.json says REVIEW-01 is `%s`, and %s stands at `%s`',
                $met ? 'covered' : 'gap',
                $review['file'],
                $review['status'],
            ),
            $recorded['problems'],
        );
    }

    #[Test]
    public function aRunJudgedAgainstOlderCriteriaIsNotReadAsCurrent(): void
    {
        $recorded = $this->record('REVIEW-01', static function (array $run): array {
            $run['criteria'] = 'aaaaaaaaaaaa';

            return $run;
        });

        self::assertNotSame([], $recorded['problems']);
        self::assertStringContainsString('was judged against criteria aaaaaaaaaaaa', $recorded['problems'][0]);
    }

    #[Test]
    public function aRunOfNoForwardReviewIsNotARun(): void
    {
        $recorded = $this->record('REVIEW-01', static function (array $run): array {
            $run['scenario'] = 'REVIEW-99';

            return $run;
        });

        self::assertSame(
            ['scenarios/runs/REVIEW-99.json records a run of no forward review in scenarios/forward/'],
            $recorded['problems'],
        );
    }

    /**
     * One recorded run, written to a directory of this test's own: a fixture
     * below scenarios/runs/ would be read as a real result of a real session.
     *
     * @param callable(array<string, mixed>): array<string, mixed> $spoil
     * @return array{file: string, run: array<string, mixed>, verdict: string, problems: array<int, string>}
     */
    private function record(string $id, callable $spoil): array
    {
        $run = Scenarios::skeleton($id, 'testing', 'phpunit', '2026-07-30');
        foreach (['outcomes' => 'met', 'failures' => 'avoided'] as $section => $key) {
            $run[$section] = array_map(
                static fn(): array => [$key => true, 'evidence' => 'what the session did'],
                is_array($run[$section]) ? $run[$section] : [],
            );
        }

        $run = $spoil($run);

        $this->runs = sys_get_temp_dir() . '/typo3-dev-companion-runs-' . getmypid();
        if (!is_dir($this->runs)) {
            mkdir($this->runs, 0775, true);
        }
        file_put_contents(
            $this->runs . '/' . (is_string($run['scenario'] ?? null) ? $run['scenario'] : $id) . '.json',
            json_encode($run, JSON_PRETTY_PRINT),
        );

        $recorded = Scenarios::runs($this->runs);

        return $recorded[array_key_first($recorded)] ?? self::fail('nothing was read back');
    }

    /**
     * The ids as the markdown writes them, found without the parser under test.
     *
     * @return array<int, string>
     */
    private function headings(string $directory): array
    {
        $ids = [];
        $files = Finder::create()->files()->in(Paths::root() . $directory)->depth('< 2')
            ->name('*.md')->notName('readme.md')->sortByName();
        foreach ($files as $file) {
            preg_match_all('/^#{1,2} ([A-Z]+-\d+)\b/m', (string) file_get_contents($file->getPathname()), $matches);
            $ids = array_merge($ids, $matches[1]);
        }
        sort($ids);

        return $ids;
    }
}
