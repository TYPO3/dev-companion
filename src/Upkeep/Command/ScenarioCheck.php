<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Scenarios;
use TYPO3\DevCompanion\Upkeep\Voice;

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
                '%s %-8s %s %s',
                Voice::key($id, 10),
                $state === '' ? '—' : $state,
                Voice::dim(str_pad(is_string($recorded['run']['date'] ?? null) ? $recorded['run']['date'] : '', 10)),
                $recorded['problems'] === [] ? 'ok' : '',
            ));
            foreach ($recorded['problems'] as $problem) {
                ++$problems;
                Voice::problem($output, $problem);
            }
            $quoted = Scenarios::unbackedTools($recorded['run']);
            if ($quoted !== []) {
                $unbacked[$id] = $quoted;
            }
        }

        foreach ($unbacked as $id => $quoted) {
            Voice::note($output, sprintf('%s quotes %s, and its trace carries no such call.', $id, implode(', ', $quoted)));
        }

        // Not a failure. Most scenarios have never been run forward, and a suite
        // that fails for that would be a suite nobody could add a scenario to.
        $unrun = array_values(array_diff(array_keys(Scenarios::load()), array_keys($runs)));
        Voice::note($output, sprintf('%d of %d forward reviews have a recorded run.', count($runs), count($runs) + count($unrun)));
        if ($unrun !== []) {
            Voice::note($output, sprintf('Never run forward: %s', implode(', ', $unrun)));
        }

        return Voice::verdict($output, $problems, 'Every recorded run answers the scenario it claims to.', Voice::count($problems, 'recorded run') . ' no longer read as the scenario does.');
    }
}
