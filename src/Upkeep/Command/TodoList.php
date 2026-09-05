<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Todo;
use TYPO3\DevCompanion\Upkeep\Voice;
use TYPO3\DevCompanion\Upkeep\Wrap;

/**
 * The overview `bin/cli todo:next` deliberately does not give, for whoever wants it.
 *
 * Titles only, because that is what an overview is. What a todo asks for is a
 * paragraph, and five paragraphs are what `next` exists to spare a session that
 * only has to start one of them.
 */
#[AsCommand(
    name: 'todo:list',
    description: 'every todo by title: what recurs, the queue in order, what is in hand, and what waits',
)]
final class TodoList
{
    public function __invoke(OutputInterface $output): int
    {
        foreach (Todo::recurring() as $todo) {
            $output->writeln(sprintf(
                '%s %s%s',
                Voice::key($todo['every'], 12),
                $todo['title'],
                Todo::due($todo['every'], $todo['checked']) ? '' : Voice::dim(' — not due, last ' . $todo['checked']),
            ));
        }

        // The queue comes out in the order it is worked, so the column says
        // what put each one where it is rather than repeating the order as a
        // count. A blank there is a todo carrying no priority, which
        // `bin/cli todo:check` reports — the gap is the point.
        // What somebody has in hand is in the queue like everything else, and is
        // marked rather than listed apart: it is the worktree that says so, and a
        // todo whose worktree came down is workable again with nothing rewritten.
        $held = [];
        foreach (Todo::held() as $branch => $todo) {
            $held[$todo['path']] = $branch;
        }
        $items = Todo::items();
        foreach ($items as $item) {
            $branch = $held[$item['path']] ?? '';
            $output->writeln(sprintf(
                '%-12s %s %s',
                $branch === '' ? $item['priority'] : 'in hand',
                Voice::key(Todo::identifier($item), 16),
                $item['title'],
            ));
        }
        if ($items === []) {
            Voice::note($output, 'The queue is empty.');
        }

        foreach (Todo::waiting() as $todo) {
            // The lead is the two columns every row has, and the question is
            // wrapped under it rather than run off the screen.
            $lead = sprintf('%-12s %s ', 'waiting', Voice::key(Todo::identifier($todo), 16));
            $wrapped = Wrap::indented($todo['title'] . ' — ' . $todo['waitingOn'], str_repeat(' ', 30));
            $output->writeln($lead . substr($wrapped, 30));
        }

        foreach (Todo::references() as $reference) {
            $output->writeln(sprintf('%s %s', Voice::key('read only', 12), $reference['title']));
        }

        return 0;
    }
}
