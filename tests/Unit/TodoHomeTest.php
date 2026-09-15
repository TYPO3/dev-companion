<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Command\TodoHome;

/**
 * What `bin/cli todo:home` is for is the order, so the order is what this
 * holds.
 *
 * The four steps are each correct on their own and only mean anything in one
 * sequence. A suite run before the rebase checked a tree that no longer exists.
 * A worktree removed before the merge takes the only checkout the merge could
 * have run in. Prose said all of that and could hold nobody to it.
 *
 * Every case here stops before the merge, on purpose. A run that merged would
 * fast-forward the checkout the suite runs in (`R-COD-003`). What a test can
 * hold without a write is the half that decides whether anything gets written
 * at all.
 */
final class TodoHomeTest extends TestCase
{
    /**
     * The worktree each case names, which exists only in what the git stub
     * says.
     */
    private const NAME = 'a-claim-that-has-reported';

    private const BRANCH = 'todo/a-claim-that-has-reported';

    /**
     * Every command the stub got, in the order of the calls.
     *
     * A property rather than a return value, because a case asserts on what ran
     * *after* it handed the stub over. An array given back would be the copy
     * that existed before the command ran.
     *
     * @var array<int, array<int, string>>
     */
    private array $ran = [];

    protected function setUp(): void
    {
        $this->ran = [];
    }

    protected function tearDown(): void
    {
        Checkouts::useRunner(null);
    }

    /**
     * A session that died mid-write leaves a tree nothing below would carry.
     * The rebase refuses it, and a merge that somehow got past would leave the
     * finished half on no branch at all. So the read comes before the sequence
     * starts rather than in the middle of it.
     */
    #[Test]
    public function aWorktreeWithUncommittedChangesIsRefusedBeforeAnythingMoves(): void
    {
        $this->gitThatAnswers(['status' => " M src/Upkeep/Todo.php\n"]);
        $output = new BufferedOutput();

        $exitCode = (new TodoHome())($output, Cli::application(), [self::NAME]);

        self::assertSame(1, $exitCode);
        self::assertStringContainsString('changes nobody committed', $output->fetch());
        self::assertSame([], $this->matching('rebase'), 'a dirty worktree was rebased anyway');
        self::assertSame([], $this->matching('merge'), 'a dirty worktree was merged anyway');
    }

    /**
     * A rebase that conflicts goes back rather than stays. Half a rebase is a
     * worktree in a state no session can start against and no later call can
     * recognise. The caller finds out about it from the next command rather
     * than from this one.
     */
    #[Test]
    public function aRebaseThatConflictsIsAborted(): void
    {
        $this->gitThatAnswers(['rebase' => [1, "CONFLICT (content): Merge conflict in src/Upkeep/Todo.php\n"]]);
        $output = new BufferedOutput();

        $exitCode = (new TodoHome())($output, Cli::application(), [self::NAME]);

        self::assertSame(1, $exitCode);
        self::assertStringContainsString('does not rebase onto main', $output->fetch());
        self::assertNotSame([], $this->matching('rebase --abort'), 'a conflicted rebase was left standing');
        self::assertSame([], $this->matching('merge'), 'a branch that does not rebase was merged');
    }

    /**
     * The suite runs on what `main` has become and decides the merge. A red
     * branch keeps its worktree, because the fix goes in there. A removal here
     * would cost a fresh `composer install` to get back to the same failure.
     */
    #[Test]
    public function aRedSuiteStopsBeforeTheMergeAndKeepsTheWorktree(): void
    {
        $this->gitThatAnswers(['composer' => [1, "FAILURES!\nTests: 1459, Failures: 1.\n"]]);
        $output = new BufferedOutput();

        $exitCode = (new TodoHome())($output, Cli::application(), [self::NAME]);

        self::assertSame(1, $exitCode);
        self::assertStringContainsString('rebased and red', $output->fetch());
        self::assertSame([], $this->matching('merge'), 'a red branch was merged');
        self::assertSame([], $this->matching('worktree remove'), 'a red branch lost the worktree its fix is written in');
    }

    /**
     * The suite runs after the rebase and never before it, which is the one
     * order a caller who read the page got wrong twice. A run from before is a
     * run against a tree that no longer exists, and it is green about nothing.
     */
    #[Test]
    public function theSuiteIsAskedAfterTheRebase(): void
    {
        $this->gitThatAnswers(['composer' => [1, "FAILURES!\n"]]);

        (new TodoHome())(new BufferedOutput(), Cli::application(), [self::NAME]);

        $order = array_values(array_filter(
            array_map(self::asked(...), $this->ran),
            static fn(string $line): bool => str_contains($line, 'rebase') || str_contains($line, 'composer'),
        ));

        self::assertCount(2, $order, 'the rebase and the suite did not each run once');
        self::assertStringContainsString('rebase', $order[0]);
        self::assertStringContainsString('composer', $order[1]);
    }

    /**
     * What the branch owes `main` goes into the branch's own commit, and the
     * two steps around it are what put it there. The rebase is what lets a
     * worktree write a group listing at all, and the suite then runs on the
     * tree that merges. Committed onto `main` afterwards instead, it was 37 of
     * the 200 commits before 2026-08-18 — `D-FBK-011`.
     */
    #[Test]
    public function whatTheBranchOwesMainIsAmendedAfterTheRebase(): void
    {
        $this->gitThatAnswers([
            '--untracked-files=all' => " M decisions/readme.md\n",
            'composer' => [1, "FAILURES!\n"],
        ]);

        (new TodoHome())(new BufferedOutput(), Cli::application(), [self::NAME]);

        $order = array_values(array_filter(
            array_map(self::asked(...), $this->ran),
            static fn(string $line): bool => (bool) preg_match('/rebase main|decisions:index|commit --amend|composer/', $line),
        ));

        self::assertCount(4, $order, 'the rebase, the index, the amend and the suite did not each run once');
        self::assertStringContainsString('rebase main', $order[0]);
        self::assertStringContainsString('decisions:index', $order[1]);
        self::assertStringContainsString('commit --amend', $order[2]);
        self::assertStringContainsString('composer', $order[3]);
        self::assertSame([], $this->matching('merge'), 'a red branch was merged');
    }

    /**
     * The link another branch wrote to a feedback this one archives is dead the
     * moment the two are on one tree. The tree that carries both is the rebased
     * one. So the repair runs there, and before the suite that would otherwise
     * fail the branch on a link its session never saw — `D-DOC-064`.
     */
    #[Decision('D-DOC-064')]
    #[Test]
    public function theArchiveRepairRunsOnTheRebasedTreeAndBeforeTheSuite(): void
    {
        $this->gitThatAnswers(['composer' => [1, "FAILURES!\n"]]);

        (new TodoHome())(new BufferedOutput(), Cli::application(), [self::NAME]);

        $order = array_values(array_filter(
            array_map(self::asked(...), $this->ran),
            static fn(string $line): bool => (bool) preg_match('/rebase main|links:repair|composer/', $line),
        ));

        self::assertCount(3, $order, 'the rebase, the repair and the suite did not each run once');
        self::assertStringContainsString('rebase main', $order[0]);
        self::assertStringContainsString('links:repair', $order[1]);
        self::assertStringContainsString('composer', $order[2]);
    }

    /**
     * A todo nobody has in hand is a caller on a stale listing. The four steps
     * would otherwise run against a directory git has never heard of. Nothing
     * runs, and the message names where the list of what is in hand comes from.
     */
    #[Test]
    public function aTodoNobodyHasInHandRunsNothing(): void
    {
        $this->gitThatAnswers([]);
        $output = new BufferedOutput();

        $exitCode = (new TodoHome())($output, Cli::application(), ['a-worktree-nobody-cut']);

        self::assertSame(1, $exitCode);
        self::assertStringContainsString('is no todo anybody has in hand', $output->fetch());
        self::assertSame([], $this->matching('rebase'));
    }

    /**
     * The seam every case above stands on, asserted directly because it has
     * gone wrong twice and neither time in a checkout that would notice. A key
     * is a word a command carries. So what the command carries and nobody
     * chose, where this checkout sits, is not part of it.
     */
    #[Test]
    public function whatACaseIsKeyedOnCarriesNoneOfTheCheckoutsOwnPath(): void
    {
        $root = Paths::root();

        self::assertSame('git status --porcelain', self::asked(['git', '-C', $root, 'status', '--porcelain']));
        self::assertSame(
            '/usr/bin/php /.worktrees/a-claim-that-has-reported/bin/cli requirements:index',
            self::asked(['/usr/bin/php', $root . '/.worktrees/a-claim-that-has-reported/bin/cli', 'requirements:index']),
        );
        self::assertSame('composer ci', self::asked(['composer', 'ci']));
    }

    /**
     * git, as far as this command can see it. A checkout that is not a
     * worktree, on `main`, with one worktree below `.worktrees/` whose branch
     * is the claim's and whose tree is clean.
     *
     * Each case overrides the one answer it is about, keyed by the word its
     * command carries. Everything it does not name is the run that would have
     * gone through. So a case says what is different rather than repeats the
     * eight calls that come before the difference.
     *
     * @param array<string, string|array{0: int, 1: string}> $answers
     */
    private function gitThatAnswers(array $answers): void
    {
        $root = Paths::root();
        $worktree = $root . '/.worktrees/' . self::NAME;

        $git = self::createStub(CommandRunner::class);
        $git->method('run')->willReturnCallback(
            /**
             * @param array<int, string> $command
             *
             * @return array{ok: bool, exitCode: int, output: string, error: string}
             */
            function (array $command) use ($answers, $root, $worktree): array {
                $this->ran[] = $command;
                $line = implode(' ', $command);
                $asked = self::asked($command);

                foreach ($answers as $carries => $answer) {
                    if (!str_contains($asked, $carries)) {
                        continue;
                    }
                    [$exitCode, $said] = is_array($answer) ? $answer : [0, $answer];

                    return ['ok' => $exitCode === 0, 'exitCode' => $exitCode, 'output' => $said, 'error' => ''];
                }

                $said = match (true) {
                    // `linked()`, which reads not-a-worktree from two answers
                    // that are the same directory.
                    str_contains($line, '--absolute-git-dir') => $root . "/.git\n",
                    str_contains($line, '--git-common-dir') => $root . "/.git\n",
                    str_contains($line, 'worktree list') => 'worktree ' . $worktree . "\nbranch refs/heads/" . self::BRANCH . "\n",
                    // The checkout stands on `main`, the worktree on its claim.
                    str_contains($line, '--abbrev-ref HEAD') => str_contains($line, $worktree) ? self::BRANCH . "\n" : "main\n",
                    default => '',
                };

                return ['ok' => true, 'exitCode' => 0, 'output' => $said, 'error' => ''];
            },
        );
        Checkouts::useRunner($git);
    }

    /**
     * @return array<int, string>
     */
    private function matching(string $carries): array
    {
        return array_values(array_filter(
            array_map(self::asked(...), $this->ran),
            static fn(string $line): bool => str_contains($line, $carries),
        ));
    }

    /**
     * What ran, with the checkout's own path taken out of it.
     *
     * A case keys on a word the command carries, and half of what a command
     * carries is where this checkout sits. git gets `-C <path>`, and the index
     * steps run as `<php> <path>/bin/cli requirements:index`. So a checkout
     * whose directory name contains a key answered every call keyed on it. In
     * `.worktrees/nothing-enumerates-what-a-composer-install` the cases keyed
     * on `composer` fired on `rev-parse --abbrev-ref HEAD`, which then reported
     * `FAILURES!` as the branch name.
     *
     * @param array<int, string> $command
     */
    private static function asked(array $command): string
    {
        $said = [];

        foreach ($command as $argument) {
            $argument = str_replace(Paths::root(), '', $argument);

            if ($argument === '') {
                // The whole argument was the path, so the flag in front of it
                // goes with it: `-C <root>` says nothing once the root is gone.
                if (end($said) === '-C') {
                    array_pop($said);
                }

                continue;
            }

            $said[] = $argument;
        }

        return implode(' ', $said);
    }
}
