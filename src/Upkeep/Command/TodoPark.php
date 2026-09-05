<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Todo;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * The one move left in the queue: what waits, out of where it would be offered.
 *
 * A session that hits a question nothing here can answer writes it onto its todo
 * and ends. The file is in `open/`, where the next session is offered it as
 * ordinary work — and what it actually needs is a person, which no session can
 * supply by reading harder.
 *
 * It is asked for nothing. The question is the signal, so a todo carrying one is
 * parked wherever it was written, and a caller cannot name the wrong file.
 * Taking a todo on and finishing one write nothing for this to undo — the
 * worktree says the first and a deletion says the second (`D-DOC-060`).
 */
#[AsCommand(
    name: 'todo:park',
    description: 'move every queued todo that names a question into waiting, where no session is offered it',
)]
final class TodoPark
{
    public function __invoke(OutputInterface $output): int
    {
        $parked = 0;
        foreach (Todo::items() as $todo) {
            if ($todo['waitingOn'] === '') {
                continue;
            }

            Voice::ok($output, Todo::park($todo));
            Voice::row($output, 'It waits on ' . str_replace('
', '
  ', $todo['waitingOn']));
            ++$parked;
        }

        if ($parked === 0) {
            Voice::ok($output, 'Nothing queued names a question, so nothing moved.');
        }

        return 0;
    }
}
