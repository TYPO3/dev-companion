<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Todo;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * The one thing to do now, for whoever is starting a session.
 *
 * It prints a single todo and nothing else. Everything it could print instead
 * was already written down somewhere — the feedback that arrived from outside,
 * the two directories that say what is unfinished, the file that says in which
 * order — and a session that is handed all of it reads for ten minutes before
 * it does anything. Context is not free: an agent given the queue reads it as a
 * plan and works in the wrong order, and one given five paragraphs of why a
 * todo is where it is starts by summarising them. `bin/cli todo:list` is where
 * the overview lives, for whoever wants it.
 *
 * Whether a todo is due is two questions. Has the clock come round — cheap, and
 * answered by the cadence. And is there anything to do — expensive, and
 * answered by running the todo's own `run:` command: a command this repository
 * owns exits nonzero when it found work, so the feedback stop being the next
 * thing the moment the last one is judged, without anybody editing a todo to
 * say so. A command it does not own is named rather than run; `next` starts no
 * process that needs the network, and the cadence is what keeps it from being
 * asked twice in an afternoon.
 *
 * Three groups, in this order, because for a while the first of them ate the
 * other two. A sighting that recurs every session is due for as long as
 * anything is unjudged, and the feedback arrive faster than a session closes
 * them — so every session started by sighting, the queue was never reached,
 * and items sat in it for as long as feedback/ was not empty. What it means to
 * take something on is that it is now ahead of the deciding whether to:
 *
 * - What has a clock has an appointment. A cadence in days is a date that
 *   comes round, and missing it is missing the day, not losing a place in
 *   an order.
 * - Then the queue, in the order the queue has. It is work somebody has
 *   already judged to be worth doing, which is exactly what a sighting
 *   produces — so leaving it standing to sight more is deciding twice and
 *   doing nothing.
 * - Then, with the queue empty, what recurs every session: the feedback and
 *   the unresolved, whose whole output is new entries for the queue that just
 *   ran dry.
 */
#[AsCommand(
    name: 'todo:next',
    description: 'the one todo that is due now, and nothing else',
)]
final class TodoNext
{
    public function __invoke(
        OutputInterface $output,
        Application $application,
        #[Option('this session is one of several and works the claim its worktree stands on')]
        bool $worktree = false,
    ): int {
        // A worktree standing on a claim has one todo and it is not the front
        // of the queue. Asked there, this command would hand over work
        // somebody else is already doing, and nothing about the answer would
        // look wrong — which is why the branch is read before anything else.
        $claim = Todo::claimed();
        if ($claim !== null) {
            return self::present($output, $claim, self::perform($application, $claim['run'])[0], null, Todo::standing());
        }

        // A worktree exists because somebody took a todo on, so one standing on
        // no claim is a setup that went wrong rather than a session with
        // nothing to do. Everything below this line would answer it with real
        // work that belongs to somebody else, and the check above cannot tell
        // the two apart: a worktree on the wrong branch and the main checkout
        // on `main` are both on no claim.
        //
        // The flag is the same refusal for the case git cannot see: a session
        // started as one of several that is standing in the main checkout. That
        // one is not a worktree at all, so every check below it passes and the
        // queue it is then handed is real work belonging to somebody else. The
        // prompt says the session is one of several, because the prompt is the
        // only thing that knows.
        if ($worktree || Todo::linked()) {
            return self::astray($output, $worktree);
        }

        foreach (Todo::appointments() as $todo) {
            if (!Todo::due($todo['every'], $todo['checked'])) {
                continue;
            }
            [$reading, $working] = self::perform($application, $todo['run']);
            if ($working) {
                return self::present($output, $todo, $reading);
            }
        }

        // What somebody has in hand stays in the queue and is offered to nobody
        // else — the worktree standing on its branch is what says so, and it is
        // the whole of what `todo/progress/` used to say (`D-DOC-060`,
        // `R-FBK-010`).
        $held = Todo::held();
        $free = array_values(array_filter(
            Todo::items(),
            static fn(array $todo): bool => !isset($held[Todo::branch($todo)]),
        ));
        if ($free !== []) {
            return self::present($output, $free[0], self::perform($application, $free[0]['run'])[0], count($free) - 1);
        }

        foreach (Todo::sightings() as $todo) {
            [$reading, $working] = self::perform($application, $todo['run']);
            if ($working) {
                return self::present($output, $todo, $reading);
            }
        }

        Voice::ok($output, 'Nothing is due and nothing is queued.');
        Voice::note($output, 'What is waiting is in `bin/cli unresolved:list`, and taking one on is a todo in todo/.');
        // An empty queue with todos in hand is not an empty repository, and the
        // difference matters here more than anywhere: this is the one branch
        // that invites a session to go find work of its own.
        $inHand = count($held);
        if ($inHand > 0) {
            Voice::note($output, sprintf(
                '%d todos are in hand elsewhere and are nobody else\'s to start — `bin/cli todo:list`.',
                $inHand,
            ));
        }
        if (Todo::waiting() !== []) {
            Voice::note($output, sprintf(
                '%d todos are blocked on an answer nothing here can give — `bin/cli todo:list`.',
                count(Todo::waiting()),
            ));
        }

        return 0;
    }

    /**
     * A worktree that is on no claim, told what is wrong instead of handed
     * work.
     *
     * Both causes leave the same trace, which is why they are named together:
     * the branch is not the one the claim was taken for, or the claim was not
     * on `main` when the worktree was cut from it and this checkout therefore
     * has no file that could match. Neither is visible from inside the session,
     * and the second one is the ordering the page warns about, seen from the
     * far end.
     *
     * It refuses rather than falling through to the queue, and that is the
     * whole point of the case. Handing over the front of the queue here would
     * be right in every way a session can check — a real todo, correctly read,
     * commit rights on a branch of its own — and wrong in the only way that
     * matters, which is that somebody else is already writing it.
     *
     * A third cause reaches the same refusal from the other direction, and only
     * because the session said so: `--worktree` in a checkout that is not one.
     * There the setup did not go wrong halfway, it never happened — no worktree
     * was cut, or the session was started in the wrong directory — and the
     * queue standing ready in front of it is the trap, not the fix.
     */
    private static function astray(OutputInterface $output, bool $declared): int
    {
        if ($declared && !Todo::linked()) {
            Cli::errors($output)->writeln(sprintf(
                "This session was started as one of several, and it is not standing in a worktree:\n"
                . "this is the checkout the worktrees are cut from, which is where somebody else is\n"
                . "working right now.\n"
                . "\n"
                . "Either no worktree was made for this claim, or the session was started in the\n"
                . "wrong directory. Both are set up from outside the session, so this is where it\n"
                . "ends — say so rather than working here.\n"
                . "\n"
                . 'How the worktrees are made and the sessions started: %s.',
                Todo::PARALLEL,
            ));

            return 1;
        }

        $branch = Todo::standing();
        Cli::errors($output)->writeln(sprintf(
            "This is a worktree, so it was made for a claim, and it is standing on none:\n"
            . "no todo in `todo/open/` derives %s.\n"
            . "\n"
            . "Either it is not the branch the worktree was cut for — `bin/cli todo:list` prints\n"
            . "each todo in hand with its own — or the todo it was cut for has been finished or\n"
            . "renamed since, and no file here derives that name any more.\n"
            . "\n"
            . "The queue is not the answer either way. What is at the front of it is work the\n"
            . 'session that took it on is already doing: %s.',
            $branch === '' ? 'the branch it is on' : '`' . $branch . '`',
            Todo::PARALLEL,
        ));

        return 1;
    }

    /**
     * One todo, how it is worked, and what it leaves behind.
     *
     * The closing block is the only thing here that is not the todo: the two
     * halves a session gets no second chance at, and which of the three
     * handovers applies, which the page itself cannot know. It opens by naming
     * the file the todo is, because that is the one fact a session needs before
     * it can finish and can read nowhere — left unsaid it is looked for, and
     * every call costs the whole context again (`D-FBK-020`).
     *
     * @param array{title: string, kind: string, every: string, path: string, serves: array<int, string>, body: string, ...} $todo
     * @param string                                                                                                          $branch the branch this session is working it on, where it is one of several
     */
    private static function present(OutputInterface $output, array $todo, string $reading, ?int $after = null, string $branch = ''): int
    {
        $meta = [$todo['path'], 'serves ' . implode(', ', $todo['serves'])];
        $meta[] = match (true) {
            $branch !== '' => 'in hand on ' . $branch,
            $todo['every'] === '' => 'queued',
            default => 'every ' . $todo['every'],
        };
        if ($after !== null && $after > 0) {
            $meta[] = $after . ' more after it — `bin/cli todo:list`';
        }
        // What somebody else has in hand is named because this command cannot
        // otherwise be told apart from the one it was before: it hands over the
        // first queued todo, and a session that does not know it is one of
        // several reads that as "nothing else is happening". Its own claim is
        // not one of them — a session counting itself among the others would
        // read one claim as two sessions at work.
        $inHand = count(Todo::held()) - ($branch === '' ? 0 : 1);
        if ($inHand > 0) {
            $meta[] = $inHand . ' in hand elsewhere — `bin/cli todo:list`';
        }
        // What waits is named by a count and nothing else. A blocked todo is
        // addressed to whoever can answer it, and if no output ever mentions
        // one it is a file nobody opens again; the paragraph still belongs to
        // the one todo this command exists to hand over.
        $waiting = count(Todo::waiting());
        if ($waiting > 0) {
            $meta[] = $waiting . ' waiting on an answer';
        }

        Voice::heading($output, $todo['title']);
        Voice::note($output, implode(' · ', $meta));
        if (trim($reading) !== '') {
            $output->writeln('');
            $output->write(self::indent($reading));
        }
        $output->writeln('');
        $output->writeln($todo['body']);
        $output->writeln('');
        Voice::note($output, sprintf(
            'Read what it serves and what the code does now before changing either; settle what
'
            . "the step turns on rather than recalling it, and ask where nothing here can answer:\n"
            . '%s.',
            Todo::PROCEDURE,
        ));
        Voice::note($output, match (true) {
            // The three rules a session working one of several claims cannot
            // read anywhere in time. They are printed with the claim rather
            // than put in the prompt because two of them name the branch, and
            // the branch is the one thing the prompt is not allowed to carry:
            // what is filled in per session is what gets filled in wrong.
            $branch !== '' => sprintf(
                "Commit it on the branch it was claimed for, never on `main` — this file included:\n"
                . "    %s\n"
                . "Leave the listing at the foot of a `requirements/` or `decisions/` group readme\n"
                . "alone, by hand and by command; whoever merges generates it once, across every\n"
                . "branch at once. Merge nothing yourself, and do not remove this worktree.\n"
                . "Done means the file says so, on this branch: deleted, or left here with the\n"
                . 'question in `waitingOn:` and the work behind it. %s is the rest.',
                $branch,
                Todo::PARALLEL,
            ),
            $todo['every'] === '' => 'Done means the file says so: deleted, or trimmed to the part that is left.',
            $todo['every'] === 'session' => 'It stands, so nothing is deleted. What it settles belongs where that is kept.',
            default => "It stands, so nothing is deleted — write today's date into `checked:`.",
        });

        return 0;
    }

    /**
     * A todo's own commands, run where this repository owns them.
     *
     * Whether there is work is the command's answer rather than a guess: the
     * listings a recurring todo starts from exit nonzero when they found
     * something. Anything else is printed for the session to run, and counts as
     * work because nothing here can tell whether it is.
     *
     * @param array<int, string> $run
     *
     * @return array{0: string, 1: bool}
     */
    private static function perform(Application $application, array $run): array
    {
        $reading = '';
        $working = $run === [];
        foreach ($run as $command) {
            // A shell line starts with `bin/cli` too, and what the console is
            // handed is a line rather than a shell: the pipe in
            // `bin/cli decisions:list | grep revoked` arrives as an argument,
            // the command refuses the lot, and the session's first call prints
            // that refusal where its todo was. So a plain invocation is run and
            // anything a shell would have to read is named, like every other
            // command this repository does not own.
            if (!str_starts_with($command, 'bin/cli ') || preg_match('/[|;&<>`$]/', $command) === 1) {
                $reading .= $command . "\n";
                $working = true;
                continue;
            }

            // One buffer for both streams, because what a check writes to
            // stderr is the half a session has to read, and it is placed under
            // the todo rather than beside it.
            $buffer = new BufferedOutput();
            $status = $application->doRun(new StringInput(substr($command, strlen('bin/cli '))), $buffer);
            $reading .= $buffer->fetch();
            $working = $working || $status !== 0;
        }

        return [$reading, $working];
    }

    private static function indent(string $block): string
    {
        return (string) preg_replace('/^(?!$)/m', '    ', rtrim($block) . "\n");
    }
}
