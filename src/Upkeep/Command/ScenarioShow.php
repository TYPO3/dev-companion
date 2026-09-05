<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Scenarios;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * One open forward review, in the only sense a command can hand it over: what
 * has to be pasted, and what the session that comes of it is judged against.
 * The session itself happens in a client, in the environment the scenario
 * names, and no part of that is automated here.
 */
#[AsCommand(
    name: 'scenarios:show',
    description: 'the environment, the prompt to paste verbatim, and the numbered criteria',
)]
final class ScenarioShow extends ScenarioReport
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('the forward review to hand over')]
        string $id,
    ): int {
        $id = strtoupper($id);
        $scenario = Scenarios::load()[$id] ?? null;
        if ($scenario === null) {
            Voice::problem($output, isset(Scenarios::contracts()[$id])
                ? sprintf('%s is a targeted contract case: bin/cli scenarios:contract %s', $id, $id)
                : sprintf('There is no forward review %s.', $id));

            return 2;
        }

        $this->report($output, $scenario, 'Status today');

        return 0;
    }
}
