<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Scenarios;

/**
 * Every recorded run against the scenario it claims to answer: judged in full,
 * evidenced, run in the right environment, judged against the criteria as they
 * read now, and adding up to the status the scenario claims. `composer test`
 * runs the same check through ScenarioTest; this is the readable half.
 */
#[AsCommand(
    name: 'scenarios:check',
    description: 'hold every recorded run to the scenario it claims to answer',
)]
final class ScenarioCheck
{
    public function __invoke(OutputInterface $output): int
    {
        $runs = Scenarios::runs();
        $problems = 0;
        $unbacked = [];

        foreach ($runs as $id => $recorded) {
            $state = $recorded['verdict'];
            if ($state === '' && Scenarios::isOpen($recorded['run'])) {
                $state = 'open';
            }
            $output->writeln(sprintf(
                '%-10s %-8s %-10s %s',
                $id,
                $state === '' ? '—' : $state,
                is_string($recorded['run']['date'] ?? null) ? $recorded['run']['date'] : '',
                $recorded['problems'] === [] ? 'ok' : '',
            ));
            foreach ($recorded['problems'] as $problem) {
                ++$problems;
                $output->writeln(sprintf('  %s', $problem));
            }
            $quoted = Scenarios::unbackedTools($recorded['run']);
            if ($quoted !== []) {
                $unbacked[$id] = $quoted;
            }
        }

        foreach ($unbacked as $id => $quoted) {
            $output->writeln('');
            $output->writeln(sprintf('%s quotes %s, and its trace carries no such call.', $id, implode(', ', $quoted)));
        }

        // Not a failure. Most scenarios have never been run forward, and a suite
        // that fails for that would be a suite nobody could add a scenario to.
        $unrun = array_values(array_diff(array_keys(Scenarios::load()), array_keys($runs)));
        $output->writeln('');
        $output->writeln(sprintf('%d of %d forward reviews have a recorded run.', count($runs), count($runs) + count($unrun)));
        if ($unrun !== []) {
            $output->writeln(sprintf('Never run forward: %s', implode(', ', $unrun)));
        }

        return $problems === 0 ? 0 : 1;
    }
}
