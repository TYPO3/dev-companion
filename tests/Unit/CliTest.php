<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\QueuedTodo;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Command\TodoClaim;
use TYPO3\DevCompanion\Upkeep\Todo;

final class CliTest extends TestCase
{
    use QueuedTodo;

    /**
     * A session works the queue before what recurs every session. This is the
     * test because the failure was silent for as long as it existed. A sighting
     * is due while anything waits for a judgement, and one session judges a
     * handful. So with the sightings first, every session opened on the same
     * one and never reached a queued item.
     *
     * The case writes the queue it needs rather than assumes one. An empty one
     * is the state the sightings are *for*. So a case about what happens while
     * items wait cannot also demand that the repository always have some —
     * `D-FBK-012`, `D-FBK-013`.
     */
    #[Decision('D-FBK-012')]
    #[Decision('D-FBK-013')]
    #[Test]
    public function theSightingsWaitForAnEmptyQueue(): void
    {
        $this->recurATodo();
        $this->queueATodo();

        self::assertNotSame([], Todo::sightings(), 'the fixture that recurs every session was not read back');

        $buffer = new BufferedOutput();
        Cli::application()->doRun(new StringInput('todo:next'), $buffer);
        $printed = $buffer->fetch();

        foreach (Todo::sightings() as $sighting) {
            self::assertStringNotContainsString(
                $sighting['title'],
                $printed,
                '"' . $sighting['title'] . '" is a sighting and came up while the queue still had ' . count(Todo::items()) . ' items',
            );
        }
    }

    /**
     * An appointment comes up on its clock and on its own command, and the
     * second half is the one that was absent.
     *
     * A recurring todo whose `Run:` names a command this console owns runs it
     * rather than assumes. The command exits nonzero when it found work, so the
     * todo stops the moment there is none. What that costs is a todo pointed at
     * a command that always exits 0. It never comes up, whatever its cadence
     * says, and `bin/cli todo:list` marks it due the whole time.
     * `read-the-contract-cases-no-test-can-hold` sat 16 days past its date that
     * way, and nothing failed, because nothing asks — `D-EVI-007`.
     */
    #[Decision('D-EVI-007')]
    #[Test]
    public function anAppointmentComesUpOnlyWhileItsCommandFindsWork(): void
    {
        // `todo:next` refuses a worktree on no claim before it reads any
        // cadence, and the suite runs in the worktrees. Git with the same
        // directory twice is the checkout the worktrees come from —
        // `R-COD-003`, rather than a real worktree to ask in.
        $this->ownQueue();
        $git = self::createStub(CommandRunner::class);
        $git->method('run')->willReturn(['ok' => true, 'exitCode' => 0, 'output' => "/repo/.git\n", 'error' => '']);
        Checkouts::useRunner($git);

        $queued = $this->queueATodo();
        $appointment = self::MARKER . '-appointment';
        // Long past its date, so the clock is not what either half turns on.
        $this->recurATodo('14 days', $appointment, 'bin/cli todo:waiting', '2026-01-01');

        $quiet = new BufferedOutput();
        Cli::application()->doRun(new StringInput('todo:next'), $quiet);

        $this->waitATodo();
        $working = new BufferedOutput();
        Cli::application()->doRun(new StringInput('todo:next'), $working);

        self::assertSame(
            $queued['title'],
            explode("\n", trim($quiet->fetch()))[0],
            'an appointment whose command found nothing was handed over ahead of the queue',
        );
        self::assertStringStartsWith(
            $appointment,
            trim($working->fetch()),
            'an appointment past its date whose command found work never came up',
        );
    }

    /**
     * A `Run:` line the console cannot take gets its name printed rather than a
     * run.
     *
     * A shell line starts with `bin/cli` too, and `todo:next` hands the console
     * a line rather than a shell. The pipe arrives as an argument and the
     * command refuses the lot. What the session reads at its first call is that
     * refusal instead of its todo. It happened on `bin/cli decisions:list |
     * grep revoked`.
     */
    #[Test]
    public function aRunLineTheConsoleCannotTakeIsNamed(): void
    {
        // `todo:next` refuses a worktree on no claim before it reads any
        // cadence, and the suite runs in the worktrees — as above.
        $this->ownQueue();
        $git = self::createStub(CommandRunner::class);
        $git->method('run')->willReturn(['ok' => true, 'exitCode' => 0, 'output' => "/repo/.git\n", 'error' => '']);
        Checkouts::useRunner($git);

        $piped = self::MARKER . '-piped';
        $this->recurATodo('14 days', $piped, 'bin/cli decisions:list | grep revoked', '2026-01-01');

        $buffer = new BufferedOutput();
        $status = Cli::application()->doRun(new StringInput('todo:next'), $buffer);
        $printed = $buffer->fetch();

        self::assertSame(0, $status);
        self::assertStringStartsWith($piped, trim($printed));
        self::assertStringContainsString('bin/cli decisions:list | grep revoked', $printed);
    }

    /**
     * The one directory listing a session should never have to make.
     *
     * A todo ends with a deletion or a trim of the file it is, and that file
     * has an id nobody retypes for a name. Handed the todo without its path, a
     * session goes and looks. The 82 sessions of 2026-08-02 spent 207 listings
     * of `todo/` on a file the command had just read. So the command prints the
     * path whichever of the three kinds of todo comes up — `D-FBK-020`.
     */
    #[Decision('D-FBK-020')]
    #[Test]
    public function theTodoItHandsOverNamesTheFileItIs(): void
    {
        $this->queueATodo();

        $buffer = new BufferedOutput();
        Cli::application()->doRun(new StringInput('todo:next'), $buffer);
        $printed = explode("\n", trim($buffer->fetch()));

        // The title is the first line and the meta the second, which is what
        // makes this readable at all. A todo named anywhere else in the output
        // is one a read mentioned, not the one in hand.
        foreach (array_merge(Todo::appointments(), Todo::items(), Todo::sightings()) as $todo) {
            if ($todo['title'] !== $printed[0]) {
                continue;
            }

            self::assertStringStartsWith(
                $todo['path'],
                $printed[1] ?? '',
                'the todo was handed over without the file it is, which is then looked for',
            );

            return;
        }

        // A worktree on no claim gets a refusal, and a refusal hands over no
        // todo to name a path for.
        self::assertTrue(Todo::linked(), 'nothing was handed over at all, and this checkout is not a worktree');
    }

    /**
     * Every recurring todo is one of the two, and the cadence is what says
     * which. A clock makes it an appointment, `session` makes it a sighting.
     * `bin/cli todo:next` asks the one group before the queue and the other
     * after it. So a todo in neither would come up at no point at all —
     * `D-FBK-012`.
     */
    #[Decision('D-FBK-012')]
    #[Test]
    public function whatRecursIsEitherAnAppointmentOrASighting(): void
    {
        $split = array_merge(Todo::appointments(), Todo::sightings());

        self::assertEqualsCanonicalizing(
            array_column(Todo::recurring(), 'title'),
            array_column($split, 'title'),
            'the two groups are not the recurring todos',
        );
    }

    /**
     * The message every session of a parallel run starts with, held to the one
     * property that makes it sendable. There is nothing in it to fill in.
     *
     * It is a test rather than a note because of how the failure looked. The
     * message went out with `<absolute path to the worktree>` still in it. The
     * session on the other end had no way to tell an unfilled blank from an
     * instruction. The placeholder is not safer here, it is absent. So the
     * message cannot name the todo, the branch or the directory, and the
     * session reads all three out of the checkout instead.
     */
    #[Test]
    public function theMessageASessionIsStartedWithHasNothingToFillIn(): void
    {
        self::assertDoesNotMatchRegularExpression(
            '/<[a-z][^>]*>/',
            Todo::BRIEFING,
            'the message carries a placeholder, and a caller who leaves it standing sends it',
        );
        self::assertStringContainsString(
            'bin/cli todo:next --worktree',
            Todo::BRIEFING,
            'the message names no way for the session to find out which todo is its own',
        );
    }

    /**
     * The other half of that output, held to the same property.
     *
     * The blanks out of the message left one above it. *With that worktree as
     * its working directory* is a property a person satisfies. The run of
     * 2026-08-02 satisfied it with the directory that was already open, the
     * checkout the worktrees come from. Every session it started got a refusal,
     * correctly, hours of setup later. So each worktree is a line to run,
     * absolute, and the message says where it starts. A caller with nothing to
     * compose composes nothing wrong.
     */
    #[Test]
    public function whereTheSessionsAreStartedIsPrinted(): void
    {
        $handover = TodoClaim::handover('/checkout', ['.worktrees/first', '.worktrees/second']);

        self::assertStringContainsString('cd /checkout/.worktrees/first', $handover);
        self::assertStringContainsString(
            'cd /checkout/.worktrees/second',
            $handover,
            'a session is left to work out its own directory, which is the blank this replaced',
        );
        self::assertStringContainsString(
            Todo::BRIEFING,
            (string) preg_replace('/^ {4}/m', '', $handover),
            'the message the sessions are sent is not in what the caller is handed',
        );
        self::assertStringContainsString(
            TodoClaim::LAUNCH,
            $handover,
            'nothing says the step can stop being the caller\'s, so it stays the caller\'s forever',
        );
    }

    /**
     * A session that says it is one of several is never handed the queue.
     *
     * Everything else `todo:next` does is right here and wrong by one step. The
     * front of the queue is a real todo, correctly read, and somebody else
     * already writes it. The run of 2026-08-01 produced two cases. A worktree
     * cut before the claim was on `main`, and a session started in the main
     * checkout at all. Neither is visible from inside the session, which is why
     * the answer is the command's rather than the prompt's.
     *
     * It holds wherever the suite runs, because it runs in the worktrees too.
     * On a claim the answer is that claim, on none it is a refusal, and the
     * queue is neither — `D-FBK-013`.
     */
    #[Decision('D-FBK-013')]
    #[Test]
    public function whatIsAskedForOneOfSeveralSessionsIsNeverTheQueue(): void
    {
        // The queue is what must not come up, so there has to be one to hold
        // back. The repository may have none of its own.
        $this->queueATodo();

        $buffer = new BufferedOutput();
        $exit = Cli::application()->doRun(new StringInput('todo:next --worktree'), $buffer);
        $printed = $buffer->fetch();

        $claim = Todo::claimed();
        if ($claim === null) {
            self::assertSame(1, $exit, 'a session standing on no claim was served rather than stopped');
            self::assertStringNotContainsString(Todo::items()[0]['title'], $printed, 'the queue was handed over anyway');

            return;
        }

        self::assertSame(0, $exit);
        self::assertStringContainsString($claim['title'], $printed, 'the claim under the session is not what it was handed');
        self::assertStringContainsString(
            Todo::branch($claim),
            $printed,
            'nothing says which branch the work is committed on',
        );
    }

    /**
     * A branch that carried nothing goes with its worktree, because one nobody
     * takes down is a todo `todo:claim` passes over for good.
     *
     * That is the failure this command exists to prevent. Nothing moves on a
     * claim, so what offers a todo again is the worktree that comes down. A
     * branch left beside it says the work is somewhere, which for a session
     * that never started is not true.
     */
    #[Decision('D-DOC-060')]
    #[Test]
    public function droppingAWorktreeDeletesABranchThatCarriedNothing(): void
    {
        $asked = [];
        Checkouts::useRunner($this->gitAnswering([
            'worktree' => [0, 'worktree ' . Paths::root() . "/.worktrees/alpha\nHEAD 0000\nbranch refs/heads/todo/alpha\n"],
            'rev-list' => [0, "0\n"],
            'status' => [0, ''],
            'branch' => [0, ''],
            'rev-parse' => [1, ''],
        ], $asked));

        $buffer = new BufferedOutput();
        $exit = Cli::application()->doRun(new StringInput('todo:drop alpha'), $buffer);
        $printed = $buffer->fetch();

        self::assertSame(0, $exit, $printed);
        self::assertStringContainsString('carried nothing and is deleted', $printed);
        self::assertContains('git -C ' . Paths::root() . ' branch -d todo/alpha', $asked);
    }

    /**
     * A todo's name is its id, and the worktree is how this repository holds
     * one in hand rather than something a caller has to know.
     *
     * The command takes both for the same reason the id is what the file says.
     * A caller who reads `bin/cli todo:list` has the id in front of them, and
     * one who has been in the directory has its name.
     */
    #[Decision('D-DOC-061')]
    #[Test]
    public function aTodoIsNamedByItsIdAndNotByTheWorktreeHoldingIt(): void
    {
        $queued = $this->queueATodo();
        $branch = Todo::branch($queued);
        Checkouts::useRunner($this->gitSaying($this->worktreeOn($branch, 'some-directory-name')));

        self::assertSame(
            'some-directory-name',
            Todo::worktreeNamed(Todo::identifier($queued), $this->ownQueue()),
            'the id a listing prints does not name the todo it prints it for',
        );
        self::assertSame('some-directory-name', Todo::worktreeNamed('some-directory-name', $this->ownQueue()));
        self::assertNull(Todo::worktreeNamed('T-260101-0000', $this->ownQueue()), 'an id nothing has resolved anyway');
    }

    /**
     * The branch is the todo's id, which is the whole of its file name.
     *
     * A claim reads a branch as a todo somebody has in hand. So a derivation
     * that dropped any of the name would hand two todos one branch. It would
     * take the second out of the queue while the first is in progress.
     */
    #[Decision('D-DOC-061')]
    #[Test]
    public function aBranchIsTheTodosIdAndNothingElse(): void
    {
        $one = $this->queueATodo();
        $other = $this->queueATodo();

        self::assertSame('todo/' . Todo::identifier($one), Todo::branch($one));
        self::assertSame('todo/' . Todo::identifier($other), Todo::branch($other));
        self::assertNotSame(Todo::branch($one), Todo::branch($other));
    }

    /**
     * A branch with commits stays where it is, because it is the only place
     * that work exists.
     */
    #[Decision('D-DOC-060')]
    #[Test]
    public function droppingAWorktreeKeepsABranchThatCarriesWork(): void
    {
        $asked = [];
        Checkouts::useRunner($this->gitAnswering([
            'worktree' => [0, 'worktree ' . Paths::root() . "/.worktrees/alpha\nHEAD 0000\nbranch refs/heads/todo/alpha\n"],
            'rev-list' => [0, "3\n"],
            'status' => [0, ''],
            'rev-parse' => [1, ''],
        ], $asked));

        $buffer = new BufferedOutput();
        $exit = Cli::application()->doRun(new StringInput('todo:drop alpha'), $buffer);
        $printed = $buffer->fetch();

        self::assertSame(0, $exit, $printed);
        self::assertStringContainsString('carries 3 commits nothing else does, so it stays', $printed);
        self::assertNotContains('git -C ' . Paths::root() . ' branch -d todo/alpha', $asked);
    }

    /**
     * What nobody committed is on no branch, so a worktree that comes down is
     * what throws it away — the same refusal `todo:home` makes.
     */
    #[Decision('D-DOC-060')]
    #[Test]
    public function aWorktreeWithUncommittedWorkIsNotDropped(): void
    {
        $asked = [];
        Checkouts::useRunner($this->gitAnswering([
            'worktree' => [0, 'worktree ' . Paths::root() . "/.worktrees/alpha\nHEAD 0000\nbranch refs/heads/todo/alpha\n"],
            'status' => [0, " M src/Upkeep/Todo.php\n"],
            'rev-parse' => [1, ''],
        ], $asked));

        $buffer = new BufferedOutput();
        $exit = Cli::application()->doRun(new StringInput('todo:drop alpha'), $buffer);

        self::assertSame(1, $exit);
        self::assertStringContainsString('changes nobody committed', $buffer->fetch());
        foreach ($asked as $command) {
            self::assertStringNotContainsString('worktree remove', $command, 'the worktree came down over uncommitted work');
        }
    }

    /**
     * What `knows()` answers is whether the console can run a todo's `Run:`
     * line. A todo that names a command nobody registered is a step no session
     * can take. The console answers rather than a list beside it, so a command
     * under a new name takes the answer with it.
     */
    #[Test]
    public function itKnowsTheCommandsTheConsoleHasAndNoOthers(): void
    {
        self::assertTrue(Cli::knows('bin/cli todo:next'));
        self::assertTrue(Cli::knows('bin/cli feedback:archive some-feedback.md'));
        self::assertFalse(Cli::knows('bin/cli todo:invented'));
        // The subject on its own is no command. It was one word of two before
        // the console named them `<subject>:<verb>`. A todo that still says so
        // is a todo whose step nothing runs.
        self::assertFalse(Cli::knows('bin/cli todo next'));
        // Anything that is not this command is somebody else's to run.
        self::assertTrue(Cli::knows('composer test'));
    }
}
