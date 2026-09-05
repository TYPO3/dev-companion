<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\OpenFeedback;
use TYPO3\DevCompanion\Upkeep\Todo;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Holds todo/ to the shape `bin/cli todo:next` reads it in.
 *
 * A todo is prose and stays prose — the next concrete step is a paragraph
 * somebody wrote for somebody else to start from, and nothing here shortens it.
 * What is checked is the front matter each file opens with, where the file sits,
 * and that what a todo claims to serve exists. A todo naming a feedback that was
 * closed two commits ago is the failure worth catching: the feedback is the
 * reason it is in the queue, and when it goes the todo is either done or needs
 * trimming to the part that is left. A todo in `waiting/` is held to the one
 * thing it exists to carry — the question it is blocked on, in the words it was
 * asked in, because no session is offered it to ask again.
 *
 * The last two things it says are about the other direction, where the fault is
 * in the relation between a feedback and the todos rather than in one file: an
 * open feedback that no todo answers for, and a card still asking for a
 * judgement another todo has already been given. Both are drift rather than a
 * state, both are repaired by hand — a card written where one is missing, and
 * the deletion this names for the other — and both are reported here because
 * this is what a session runs, while the cases that also hold them are in a
 * suite the session that recorded the feedback never runs.
 */
#[AsCommand(
    name: 'todo:check',
    description: 'hold every file to the head and the place that say what it is',
)]
final class TodoCheck
{
    /**
     * Every file says what it is by where it sits, and every todo says what it
     * answers for.
     */
    public function __invoke(OutputInterface $output): int
    {
        $problems = [];
        $reading = [];

        foreach (Todo::all() as $todo) {
            $where = $todo['path'];

            if ($todo['title'] === '') {
                $problems[] = $where . ' opens with no heading, so nothing says what it is about';
            }
            foreach ($todo['strays'] as $stray) {
                $problems[] = $where . ' carries `' . $stray . ':`, which is no field of a todo — '
                    . implode(', ', Todo::FIELDS);
            }

            if ($todo['kind'] === 'reference') {
                if ($todo['serves'] !== []) {
                    $problems[] = $where . ' is kept for reading and serves ' . implode(', ', $todo['serves']);
                }
                continue;
            }

            if ($todo['serves'] === []) {
                $problems[] = $where . ' opens with no `serves:`, so it is an idea rather than a todo';
            }
            if ($todo['body'] === '') {
                $problems[] = $where . ' does not say what the next concrete step is';
            }
            if ($todo['priority'] !== '' && !in_array($todo['priority'], Todo::PRIORITIES, true)) {
                $problems[] = $where . ' is ' . $todo['priority'] . ', and a priority is '
                    . implode(', ', Todo::PRIORITIES);
            }
            // A todo in a stage carries a priority and one that recurs does not.
            // The clock is what orders an appointment, and a word beside it
            // would be a second answer to the same question — while a stage
            // without one is a file that says nothing about where it stands,
            // which is exactly what could not be reported while absence meant
            // something.
            if (in_array($todo['kind'], ['queue', 'waiting'], true)) {
                if ($todo['priority'] === '') {
                    $problems[] = $where . ' carries no `priority:`, so nothing says where it stands';
                }
                // The id is the second half of the order and the only way to
                // cite one, so a todo in a stage named anything else sorts
                // wherever the file system puts it and is cited by nothing.
                if (preg_match(Todo::NAME, basename($where, '.md')) !== 1) {
                    $problems[] = $where . ' is not named `T-<yymmdd>-<hash>`, so nothing orders or cites it';
                }
            } elseif ($todo['priority'] !== '') {
                $problems[] = $where . ' recurs and carries a priority, where the cadence is what orders it';
            }
            foreach ($todo['serves'] as $what) {
                $unreadable = Todo::unreadable($what);
                if ($unreadable !== null) {
                    $problems[] = $where . ' serves ' . $what . ', ' . $unreadable;
                }
            }
            foreach ($todo['run'] as $command) {
                if (!Cli::knows($command)) {
                    $problems[] = $where . ' runs `' . $command . '`, which this command cannot do';
                }
                $reading[$command][] = $todo['title'];
            }

            if ($todo['kind'] === 'waiting') {
                // The question is the whole of what a waiting todo adds: it is
                // offered to no session, so nothing else will ask it again.
                if ($todo['waitingOn'] === '') {
                    $problems[] = $where . ' waits and does not say on what — `waitingOn:` is the question';
                }
                continue;
            }
            if ($todo['waitingOn'] !== '') {
                $problems[] = $where . ' says what it waits on and is not in todo/waiting/ — `bin/cli todo:park`';
            }

            if ($todo['kind'] === 'queue') {
                if ($todo['every'] !== '') {
                    $problems[] = $where . ' is queued and recurs every ' . $todo['every']
                        . ' — what comes round belongs in todo/recurring/';
                }
                continue;
            }

            if ($todo['every'] === '') {
                $problems[] = $where . ' comes round and does not say how often';
            } elseif ($todo['every'] !== 'session' && preg_match('/^\d+ days?$/', $todo['every']) !== 1) {
                $problems[] = $where . ' recurs every ' . $todo['every'] . ', and a cadence is ' . Todo::CADENCE;
            } elseif ($todo['every'] !== 'session' && strtotime($todo['checked']) === false) {
                $problems[] = $where . ' recurs on a clock and was last checked '
                    . ($todo['checked'] === '' ? 'never — `checked:` is what dates it' : $todo['checked']);
            }
        }

        // The readings `bin/cli todo:next` performs are the reason it can tell a
        // session there is nothing left to read: none and it silently stops
        // doing half its job, two and it does it twice.
        foreach (Todo::READINGS as $command) {
            $named = $reading[$command] ?? [];
            if ($named === []) {
                $problems[] = 'no todo runs `' . $command . '`';
            } elseif (count($named) > 1) {
                $problems[] = '`' . $command . '` is run by ' . implode(' and ', $named);
            }
        }

        // An open feedback nothing answers for is one no session will be
        // handed. `typo3_feedback_record` writes the card with the report, so
        // one missing means the feedback got here some other way — added by
        // hand, or its card deleted while it stayed open (`D-FBK-045`). Left to
        // the suite alone it would be found by whoever runs phpunit, which is
        // not the session that recorded the feedback.
        foreach (OpenFeedback::all() as $feedback) {
            if (!$feedback['judged']) {
                $problems[] = $feedback['file'] . ' is open and no todo answers for it — write it a card in todo/open/';
            }
        }

        // And the same relation from above. A feedback is given one card and
        // never a second, so the pair only ever arrives the other way round: a
        // judgement folds a feedback onto another todo's `Serves:` line and the
        // card it already had keeps asking for the judgement just made.
        // Nothing repairs this one — the fold deletes the card, and what is
        // named here is that deletion.
        foreach (Todo::folded() as $pair) {
            $problems[] = $pair['card'] . ' still asks for the judgement of ' . $pair['feedback']
                . ', which is already served by ' . implode(' and ', $pair['judged'])
                . ' — delete the card the judgement replaced';
        }
        foreach ($problems as $problem) {
            Voice::problem($output, $problem);
        }
        return Voice::verdict($output, count($problems), sprintf(
            '%d files: %d recurring, %d queued, %d of them in hand, %d waiting, %s',
            count(Todo::all()),
            count(Todo::recurring()),
            count(Todo::items()),
            count(Todo::held()),
            count(Todo::waiting()),
            Voice::count(count($problems), 'problem'),
        ));
    }

}
