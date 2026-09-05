<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Scenarios;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * The empty run, ready to be filled in after the session.
 *
 * What this replaces is a judgment that was only ever in somebody's head. A
 * scenario carries a `Status today`, and that line was the whole record of the
 * last forward run — so it went stale the moment the server changed, and
 * nothing about the file looked any different.
 */
#[AsCommand(
    name: 'scenarios:record',
    description: 'write the empty run to scenarios/runs/, to be filled in after the session',
)]
final class ScenarioRecord
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('the forward review that was run')]
        string $id,
        #[Argument('the client it ran in')]
        string $client,
    ): int {
        $id = strtoupper($id);

        if (isset(Scenarios::contracts()[$id])) {
            // Not an oversight to be worked around: a case that names its own task
            // shape cannot be evidence that an agent found it.
            Voice::problem($output, sprintf('%s is a targeted contract case and is not run forward.', $id));

            return 2;
        }

        try {
            $run = Scenarios::skeleton($id, self::server(), $client, date('Y-m-d'));
        } catch (\InvalidArgumentException $exception) {
            Voice::problem($output, $exception->getMessage());

            return 2;
        }

        $directory = Scenarios::runsDirectory();
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            Voice::problem($output, sprintf('Cannot create %s.', $directory));

            return 1;
        }

        $file = $directory . '/' . $run['scenario'] . '.json';
        $existed = file_exists($file);
        file_put_contents($file, json_encode($run, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

        Voice::ok($output, sprintf(
            '%s the run of %s in scenarios/runs/%s.json',
            $existed ? 'Replaced' : 'Wrote',
            $run['scenario'],
            $run['scenario'],
        ));
        Voice::note($output, sprintf('Judge it against: bin/cli scenarios:show %s', $run['scenario']));

        return 0;
    }

    /** The server the run happened against, as precisely as this checkout can say. */
    private static function server(): string
    {
        [$exitCode, $output] = Checkouts::run(['git', 'rev-parse', '--short', 'HEAD'], Paths::root());
        $commit = trim($output);

        return $exitCode === 0 && $commit !== '' ? $commit : 'unknown';
    }
}
