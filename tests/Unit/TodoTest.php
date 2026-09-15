<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Feedback\Card;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\QueuedTodo;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\OpenFeedback;
use TYPO3\DevCompanion\Upkeep\Todo;

/**
 * @phpstan-import-type Section from Todo
 */
final class TodoTest extends TestCase
{
    use QueuedTodo;

    /**
     * A session that has read nothing else reads a todo, and the files look
     * identical from the outside. Where one sits is what keeps "not queued, and
     * on purpose" from a read as the next piece of work. The front matter it
     * opens with is the rest of what a reader needs.
     */
    #[Decision('D-DOC-062')]
    #[Test]
    public function everyTodoSaysWhatItIsBeforeItSaysAnythingElse(): void
    {
        $todos = Todo::all();

        self::assertNotSame([], $todos);
        foreach ($todos as $todo) {
            self::assertNotSame('', $todo['title'], $todo['path'] . ' opens with no heading');
            self::assertSame([], $todo['strays'], $todo['path'] . ' carries keys that are no field of a todo');
            self::assertContains($todo['kind'], ['queue', 'recurring', 'progress', 'waiting', 'reference'], $todo['path']);
        }
    }

    /**
     * `bin/cli todo:next` performs the reads a session owes rather than names
     * them, so exactly one todo has to run each. None and the command drops
     * half its job without a word; two and it does it twice.
     */
    #[Test]
    public function theStandingReadingsAreRunOnce(): void
    {
        $run = array_merge(...array_column(Todo::recurring(), 'run'));

        foreach (Todo::READINGS as $reading) {
            self::assertSame([$reading], array_values(array_filter($run, static fn(string $r): bool => $r === $reading)));
        }
    }

    /**
     * The board is where a feedback waits, so one that is on none is one no
     * session will get. Nothing prints the pile any more. Such a feedback is
     * invisible rather than merely far down a list.
     *
     * It holds the state of the board rather than what writes it. A feedback
     * arrives with its card, so what this catches is one that came in some
     * other way. The answer is a card written into `todo/open/` by hand. One
     * assertion over the set rather than one per feedback. An empty `feedback/`
     * is a state the board is legitimately in (`D-FBK-013`) and a loop asserts
     * nothing there — `D-FBK-045`, `D-FBK-016`, `D-FBK-017`, `D-FBK-022`.
     */
    #[Requirement('R-FBK-007')]
    #[Decision('D-FBK-016')]
    #[Decision('D-FBK-017')]
    #[Decision('D-FBK-045')]
    #[Test]
    public function everyOpenFeedbackIsOnTheBoard(): void
    {
        $served = Todo::serves();

        self::assertSame(
            [],
            array_values(array_diff(array_column(OpenFeedback::all(), 'file'), $served)),
            'a feedback is open and no todo answers for it — write it a card in todo/open/',
        );
    }

    /**
     * The same relation from above, which is the half nothing that writes a
     * card can see. A feedback gets exactly one, at the moment it arrives.
     * Nothing looks back at it when a later judgement folds that feedback onto
     * another todo. What remains is a card that asks for a judgement somebody
     * has made, ten lines away in a listing and with no word in common. That is
     * one claimed session, spent to arrive where the repository already was
     * (`D-FBK-040`).
     */
    #[Decision('D-FBK-040')]
    #[Requirement('R-FBK-014')]
    #[Test]
    public function noJudgementLeavesBehindTheCardItReplaced(): void
    {
        self::assertSame(
            [],
            array_column(Todo::folded(), 'card'),
            'a card still asks for a judgement another todo serves — delete the card the judgement replaced',
        );
    }

    /**
     * What tells the two apart is the step, and the step is a constant. Every
     * card carries the same sentence, because the judgement of one feedback is
     * the same work whichever it is. A judgement is what replaces it. So a body
     * still equal to `Card::STEP` is a card nobody has judged. Beside a todo
     * that serves the same feedback it is the one to delete.
     *
     * Both other readings are here because both are legitimate and neither may
     * reach the report. A card alone is the ordinary state of the board. A
     * feedback a judgement split across two todos is two pieces of work rather
     * than a pair. What is wrong is an unjudged card beside a judged one, not
     * two cards.
     */
    #[Decision('D-FBK-040')]
    #[Requirement('R-FBK-014')]
    #[Test]
    public function theCardAJudgementReplacedIsFoundByTheStepItStillCarries(): void
    {
        $feedback = 'feedback/' . self::MARKER . '.md';
        $card = $this->queueATodo('low', '260802', $feedback, Card::STEP);

        self::assertSame([], Todo::folded(), 'a card nobody has judged yet is one somebody has');

        $judged = $this->queueATodo('normal', '260803', $feedback);

        self::assertSame(
            [['card' => $card['path'], 'feedback' => $feedback, 'judged' => [$judged['path']]]],
            Todo::folded(),
        );

        $split = $this->queueATodo('normal', '260803', $feedback);

        self::assertSame(
            [['card' => $card['path'], 'feedback' => $feedback, 'judged' => [$judged['path'], $split['path']]]],
            Todo::folded(),
            'a feedback split across two judged todos is reported as a card nobody judged',
        );
    }

    /**
     * A cadence in days is what keeps five sessions in an afternoon from the
     * same question five times. It can only do that if the date it counts from
     * is one PHP can read. A todo in the queue carries no cadence at all: what
     * comes round is never deleted, and the queue is what a commit empties.
     */
    #[Test]
    public function whatRecursOnAClockCarriesADateItCanBeCountedFrom(): void
    {
        foreach (Todo::items() as $item) {
            self::assertSame('', $item['every'], $item['path'] . ' is queued and recurs');
        }

        foreach (Todo::recurring() as $todo) {
            if ($todo['every'] === 'session') {
                self::assertSame('', $todo['checked'], $todo['path'] . ' recurs every session and is dated');
                self::assertTrue(Todo::due($todo['every'], $todo['checked']));
                continue;
            }

            self::assertMatchesRegularExpression('/^\d+ days?$/', $todo['every'], $todo['path']);
            self::assertIsInt(strtotime($todo['checked']), $todo['path'] . ' was last checked ' . $todo['checked']);
        }

        self::assertFalse(Todo::due('7 days', '2026-07-01', '2026-07-05'));
        self::assertTrue(Todo::due('7 days', '2026-07-01', '2026-07-08'));
        self::assertTrue(Todo::due('7 days', '', '2026-07-05'), 'a todo nobody has dated is one that gets looked at');
    }

    /**
     * A todo that serves nothing is an idea. One without a next concrete step
     * is worse than no todo at all: a session that reads it cannot start. What
     * it names has to be readable too. The commit that closes a feedback
     * deletes it. A todo that still names one is either finished or has a part
     * left that nobody has trimmed it down to — `D-FBK-013`, `D-FBK-016`,
     * `D-FBK-017`.
     */
    #[Decision('D-FBK-002')]
    #[Decision('D-FBK-013')]
    #[Decision('D-FBK-016')]
    #[Decision('D-FBK-017')]
    #[Test]
    public function everyTodoAnswersForSomethingThatCanStillBeRead(): void
    {
        $todos = array_merge(Todo::recurring(), Todo::items(), Todo::waiting());

        // Not the queue, which empties and is meant to. What is never empty is
        // what recurs, because nobody deletes a recurring todo.
        self::assertNotSame([], $todos, 'no todo of any kind is readable, and one of them comes round every session');
        foreach ($todos as $todo) {
            self::assertNotSame([], $todo['serves'], $todo['path'] . ' serves nothing');
            self::assertNotSame('', $todo['body'], $todo['path'] . ' has no next concrete step');
            foreach ($todo['serves'] as $what) {
                self::assertNull(
                    Todo::unreadable($what),
                    $todo['path'] . ' serves ' . $what . ', ' . Todo::unreadable($what),
                );
            }
        }
    }

    /**
     * The five kinds a `serves:` key may name, each checked against the place
     * that owns it rather than against a list kept in `Todo`. The pair that
     * matters is the id whose shape is right and whose entry is not there. A
     * session goes to read what the todo serves, and an id nothing answers to
     * is a read that does not happen and says nothing.
     *
     * A decision is one of the five because the work it carries is a return to
     * that entry's **Wrong if**. `decisions/` says only that somebody sorts the
     * pile — `D-DOC-036`.
     *
     * @param string|null $unreadable why it cannot be read, or null where it can
     */
    #[Decision('D-DOC-036')]
    #[Test]
    #[DataProvider('whatATodoMayServe')]
    public function whatATodoServesIsCheckedAgainstThePlaceThatOwnsIt(string $what, ?string $unreadable): void
    {
        self::assertSame($unreadable, Todo::unreadable($what));
    }

    /**
     * @return array<string, array{0: string, 1: string|null}>
     */
    public static function whatATodoMayServe(): array
    {
        return [
            'a requirement' => ['R-COD-003', null],
            'an id no requirement has' => ['R-ZZZ-999', 'which no requirement has'],
            'a decision' => ['D-FBK-017', null],
            'an id no decision has' => ['D-ZZZ-999', 'which no decision has'],
            'a scenario' => ['SKILL-13', null],
            'an id no scenario has' => ['SKILL-999', 'which no scenario has'],
            'a directory of this repository' => ['decisions/feedback/', null],
            'a directory that is not one' => ['nowhere/', 'which is not a directory of this repository'],
            'a feedback that is archived or gone' => [
                'feedback/2026-01-01-000000-nothing-was-ever-recorded-here.md',
                'and that feedback is closed — the todo is done, or trims to the part that is left',
            ],
            'none of the five' => [
                'the component catalog',
                'which is none of a requirement, a decision, a scenario, a feedback, or a directory of this repository',
            ],
        ];
    }

    /**
     * A todo that waits is out of the queue and says what it waits on, which is
     * the whole of what the state adds. `bin/cli todo:next` offers it to
     * nobody, so no session asks the question it waits on again. What it took
     * on still counts as taken on. A todo in `waiting/` that no longer answered
     * for its requirement would put that requirement back among the unresolved.
     * The next session would queue it a second time.
     */
    #[Test]
    public function whatWaitsCarriesTheQuestionItWaitsOn(): void
    {
        $served = Todo::serves();

        foreach (Todo::waiting() as $todo) {
            self::assertNotSame('', $todo['waitingOn'], $todo['path'] . ' waits and does not say on what');
            self::assertSame('', $todo['every'], $todo['path'] . ' waits and recurs');
            foreach ($todo['serves'] as $what) {
                self::assertContains($what, $served, $todo['path'] . ' waits and answers for nothing');
            }
        }
    }

    /**
     * The queue is an order, not an assignment. `bin/cli todo:next` reads the
     * same first item for everybody who asks. That is right while one session
     * works at a time and wrong the moment two do. What is in hand is out of
     * the queue, so the second session gets the item behind it rather than the
     * same one. It still answers for what it took on. A requirement that fell
     * back among the unresolved while somebody worked on it is one a second
     * session queues all over again.
     */
    #[Requirement('R-FBK-010')]
    #[Decision('D-DOC-060')]
    #[Test]
    public function whatIsInHandIsTheTodoAWorktreeStandsOn(): void
    {
        $queued = $this->queueATodo();
        $branch = Todo::branch($queued);
        Checkouts::useRunner($this->gitSaying($this->worktreeOn($branch)));

        $held = Todo::held($this->ownQueue());

        self::assertSame([$branch], array_keys($held), 'the worktree standing on it is what says a todo is in hand');
        self::assertSame($queued['path'], $held[$branch]['path']);
        self::assertSame([$queued], $this->ownTodos(Todo::items()), 'a todo in hand is still the file it was');
        self::assertContains('todo/', Todo::serves(), 'a todo in hand answers for nothing it took on');
    }

    /**
     * A worktree on no branch of a todo holds none.
     *
     * The two cases that tell it are the ones that reach the same line. A
     * detached worktree, which stands on no branch at all. And one on a branch
     * no queued todo derives: a todo finished, or under a new name since the
     * cut.
     */
    #[Decision('D-DOC-060')]
    #[Test]
    public function aWorktreeOnNoTodosBranchHoldsNothing(): void
    {
        $this->queueATodo();
        Checkouts::useRunner($this->gitSaying(
            'worktree ' . $this->ownQueue() . "/.worktrees/detached\nHEAD 0000\ndetached\n\n"
            . 'worktree ' . $this->ownQueue() . "/.worktrees/gone\nHEAD 0000\nbranch refs/heads/todo/nothing-derives-this\n",
        ));

        self::assertSame(['gone' => 'todo/nothing-derives-this'], Todo::inHand($this->ownQueue()));
        self::assertSame([], Todo::held($this->ownQueue()), 'a branch no todo derives holds no todo');
    }

    /**
     * The one move left in the queue, and the state it exists for.
     *
     * A session that hits a question nothing here can answer writes it onto its
     * todo and ends. Left in `open/`, the next session gets it as ordinary
     * work, and what it needs is a person.
     *
     * A claim on a todo and the end of one write nothing this has to undo. The
     * worktree says the first and a deletion says the second. So a todo nobody
     * works on is back in the queue because the worktree came down. No command
     * put it back — `D-DOC-060`.
     */
    #[Decision('D-DOC-060')]
    #[Decision('D-FBK-014')]
    #[Requirement('R-FBK-010')]
    #[Test]
    public function aTodoThatNamesAQuestionIsParkedWhereNobodyIsOfferedIt(): void
    {
        $queued = $this->queueATodo();
        file_put_contents(
            $this->ownQueue() . '/' . $queued['path'],
            "---\nserves: [todo/]\npriority: low\nwaitingOn: >\n  which of the two shapes is wanted.\n---\n\n"
            . '# ' . self::MARKER . "\n\n" . $queued['body'] . "\n",
        );
        $carrying = $this->ownTodos(Todo::items())[0];

        $parked = Todo::park($carrying);

        self::assertSame('todo/waiting/' . basename($queued['path']), $parked, 'a parked todo keeps its id');
        $waiting = $this->ownTodos(Todo::waiting())[0];
        self::assertSame('which of the two shapes is wanted.', $waiting['waitingOn'], 'the question is what the state carries');
        self::assertSame($queued['body'], $waiting['body'], 'the step is what somebody else has to start from');
        self::assertSame($queued['priority'], $waiting['priority']);
        self::assertSame([], $this->ownTodos(Todo::items()), 'a todo blocked on a person is offered as ordinary work');
    }

    /**
     * `bin/cli todo:next` is where a session starts, and that has to stay true
     * where several sessions start at once. A worktree on a claim gets that
     * claim. A checkout on no claim gets the queue, which is every session this
     * repository had before there were two.
     *
     * The failure this holds off is a quiet one. A session that gets the front
     * of the queue instead of its own claim reads a real todo and starts real
     * work. It is the second person on it.
     *
     * git is a stub rather than a call. So the case says the same on `main` as
     * in a worktree on a real claim — `D-COD-004`.
     */
    #[Requirement('R-FBK-010')]
    #[Decision('D-COD-004')]
    #[Test]
    public function aWorktreeStandingOnAClaimIsHandedThatClaim(): void
    {
        // The queue comes before the first read, so `claimed()` answers from
        // this case's own rather than from whatever the checkout carries.
        $this->ownQueue();

        Checkouts::useRunner($this->gitSaying("todo/nothing-derives-this\n"));
        $before = Todo::claimed();

        $queued = $this->queueATodo();
        Checkouts::useRunner($this->gitSaying(Todo::branch($queued) . "\n"));
        $onTheClaim = Todo::claimed();

        self::assertNull($before, 'a todo was matched on a branch none of them derives');
        self::assertNotNull($onTheClaim, 'a checkout standing on a todo\'s branch is handed the queue');
        self::assertSame($queued['path'], $onTheClaim['path']);
    }

    /**
     * A worktree on no claim is the setup that went wrong. The only thing that
     * can say so is that it is a worktree at all.
     *
     * What `linked()` does with git's two answers is the part that can be wrong
     * and the part this holds. The comparison reads the directories with the
     * trailing slashes off, and either call's failure reads as "not a worktree"
     * rather than as one. It used to answer with a real worktree and a real
     * branch in whichever checkout the suite ran in — `R-COD-003`. What a stub
     * cannot say is whether the local git has `--path-format=absolute` at all,
     * which is a property of the machine — `D-COD-004`.
     *
     * @param array{0: int, 1: string} $own
     * @param array{0: int, 1: string} $shared
     */
    #[Decision('D-COD-004')]
    #[Test]
    #[DataProvider('whatGitAnswersAboutTwoDirectories')]
    public function aWorktreeIsToldApartFromTheCheckoutItWasCutFrom(array $own, array $shared, bool $linked): void
    {
        $root = '/somewhere/checkout';
        $git = self::createStub(CommandRunner::class);
        // Two calls, in the order `linked()` makes them: its own git dir, then
        // the one it shares. `willReturnOnConsecutiveCalls` is what says that
        // without a repeat of the argument lists the method signature has.
        $git->method('run')->willReturnOnConsecutiveCalls(
            ['ok' => $own[0] === 0, 'exitCode' => $own[0], 'output' => $own[1], 'error' => ''],
            ['ok' => $shared[0] === 0, 'exitCode' => $shared[0], 'output' => $shared[1], 'error' => ''],
        );
        Checkouts::useRunner($git);

        self::assertSame($linked, Todo::linked($root));
    }

    /**
     * @return array<string, array{0: array{0: int, 1: string}, 1: array{0: int, 1: string}, 2: bool}>
     */
    public static function whatGitAnswersAboutTwoDirectories(): array
    {
        return [
            'a worktree keeps its own git dir under the one they share' => [
                [0, "/repo/.git/worktrees/claim\n"],
                [0, "/repo/.git\n"],
                true,
            ],
            'the checkout they were cut from answers the same directory twice' => [
                [0, "/repo/.git\n"],
                [0, "/repo/.git\n"],
                false,
            ],
            'a trailing slash on one of them is not a difference' => [
                [0, "/repo/.git\n"],
                [0, "/repo/.git/\n"],
                false,
            ],
            'the shared one answered nothing, so nothing is claimed' => [
                [0, "/repo/.git/worktrees/claim\n"],
                [128, "fatal: unknown option\n"],
                false,
            ],
            'both answered nothing, which is a git without the flag' => [
                [128, "fatal: unknown option\n"],
                [128, "fatal: unknown option\n"],
                false,
            ],
        ];
    }

    /**
     * The priority decides, and the age decides the rest. Written as the one
     * case where the two disagree. The newest todo is the highest one, so a
     * queue read by age alone would hand it over last. One read by priority
     * alone could not tell the two `low` ones apart — `D-FBK-015`.
     */
    #[Requirement('R-FBK-007')]
    #[Decision('D-FBK-015')]
    #[Test]
    public function theQueueIsReadByPriorityAndThenByAge(): void
    {
        $older = $this->queueATodo('low', '260701');
        $newer = $this->queueATodo('low', '260702');
        $ordinary = $this->queueATodo('normal', '260703');
        $urgent = $this->queueATodo('high', '260704');

        $queued = array_column($this->ownTodos(Todo::items()), 'path');

        self::assertSame(
            [$urgent['path'], $ordinary['path'], $older['path'], $newer['path']],
            $queued,
            'the queue is read in an order its priorities and stamps do not have',
        );
    }

    /**
     * Every todo in a stage says where it stands, and one that recurs does not.
     * The clock is what orders an appointment, so a word beside it would answer
     * the same question twice.
     *
     * The point of a required one is that a check can then miss it. While
     * absence meant "nobody has judged this", a priority somebody forgot and
     * one left off on purpose were the same file. No check could name either.
     */
    #[Requirement('R-FBK-007')]
    #[Test]
    public function everyTodoInAStageSaysWhereItStands(): void
    {
        foreach (array_merge(Todo::items(), Todo::waiting()) as $todo) {
            self::assertContains($todo['priority'], Todo::PRIORITIES, $todo['path'] . ' says nothing about where it stands');
        }

        foreach (Todo::recurring() as $todo) {
            self::assertSame('', $todo['priority'], $todo['path'] . ' recurs and is prioritised as well');
        }
    }

    /**
     * What the queue answers for comes from the queue alone. The page that
     * lists what stays out of the queue on purpose names ids too. A count of
     * those makes an entry nobody has taken on look taken on. That is the one
     * thing `bin/cli unresolved:list` exists to say out loud. Nor does a
     * recurring todo take anything on. It watches a directory. The same
     * directory in a queued todo is the difference between a notice that
     * decisions stand and a sort of them.
     */
    #[Test]
    public function onlyTheQueueAnswersForAnything(): void
    {
        $served = Todo::serves();

        foreach (Todo::references() as $reference) {
            self::assertSame([], $reference['serves'], $reference['path'] . ' is not a todo and serves something');
        }
        foreach (Todo::recurring() as $todo) {
            foreach ($todo['serves'] as $what) {
                self::assertStringEndsWith('/', $what, $todo['path'] . ' recurs and takes on ' . $what);
            }
        }

        self::assertSame($served, array_unique($served));
    }

    /**
     * A todo prints as an imperative paragraph, and the two things that decide
     * whether the change is right happen before its first sentence. A read of
     * what it serves against what the code does now, and a question settled
     * from a source instead of from recall. Neither leaves a trace, since the
     * diff of a todo worked from the checkouts is the diff of one worked from
     * memory. So what a test can hold is that the procedure exists. And that
     * the command hands it over with the work rather than leaves it for a
     * lookup. `R-FBK-009` says why; `D-FBK-007` says what it bets on —
     * `D-DOC-025`.
     */
    #[Decision('D-FBK-007')]
    #[Requirement('R-FBK-009')]
    #[Decision('D-DOC-025')]
    #[Test]
    public function everyTodoIsHandedWithThePageThatSaysHowOneIsWorked(): void
    {
        $page = Paths::root() . '/' . Todo::PROCEDURE;

        self::assertFileExists($page, Todo::PROCEDURE . ' is handed over with every todo and does not exist');
        // Listed on the page of the section it sits in, as the reference a
        // reader there would follow. The map above those sections names the
        // four and not the pages inside them.
        self::assertStringContainsString(
            ':doc:`' . basename(Todo::PROCEDURE, '.rst') . '`',
            (string) file_get_contents(Paths::root() . '/' . dirname(Todo::PROCEDURE) . '/readme.rst'),
            Todo::PROCEDURE . ' is not listed with the other procedures',
        );
        self::assertStringContainsString(
            'Todo::PROCEDURE',
            (string) file_get_contents(Paths::root() . '/src/Upkeep/Command/TodoNext.php'),
            '`bin/cli todo:next` hands over no todo with ' . Todo::PROCEDURE,
        );
    }

    /**
     * The same for the page that comes with a claim. `bin/cli todo:claim` moves
     * files and names branches, which is the half this repository owns. The
     * worktree, what a question that arrives mid-work leaves behind and who
     * merges are not things a command can carry out. A claim taken without them
     * is a lock nobody knows how to release — `D-DOC-025`.
     */
    #[Decision('D-DOC-025')]
    #[Test]
    public function everyClaimIsHandedWithThePageThatSaysHowSeveralAreWorked(): void
    {
        self::assertFileExists(
            Paths::root() . '/' . Todo::PARALLEL,
            Todo::PARALLEL . ' is handed over with every claim and does not exist',
        );
        self::assertStringContainsString(
            ':doc:`' . basename(Todo::PARALLEL, '.rst') . '`',
            (string) file_get_contents(Paths::root() . '/' . dirname(Todo::PARALLEL) . '/readme.rst'),
            Todo::PARALLEL . ' is not listed with the other procedures',
        );
        self::assertStringContainsString(
            'Todo::PARALLEL',
            (string) file_get_contents(Paths::root() . '/src/Upkeep/Command/TodoClaim.php'),
            '`bin/cli todo:claim` hands over no claim with ' . Todo::PARALLEL,
        );
    }
}
