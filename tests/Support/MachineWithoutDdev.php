<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Support;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Process\SystemRunner;

/**
 * Every test of the class runs on this machine as it is, except that it has no
 * `ddev`.
 *
 * A layout with a `.ddev/config.yaml` makes the project answer ask
 * `ddev describe -j`. Where `ddev` is on the `PATH`, that answer belongs to
 * this machine, and the test would read whatever runs here, `R-COD-003`. A
 * test about a DDEV state hands `Typo3Cli` a runner of its own after this.
 */
trait MachineWithoutDdev
{
    #[Before]
    public function hideDdevFromTypo3Cli(): void
    {
        $system = new SystemRunner();
        $runner = self::createStub(CommandRunner::class);
        $runner->method('locate')->willReturnCallback(
            static fn(string $name): ?string => $name === 'ddev' ? null : $system->locate($name),
        );
        $runner->method('run')->willReturnCallback(
            static function (array $command, ?string $directory = null, ?int $timeout = null, bool $stdin = false) use ($system): array {
                /** @var list<string> $command */
                return $system->run($command, $directory, $timeout, $stdin);
            },
        );
        Typo3Cli::useRunner($runner);
    }

    #[After]
    public function giveTypo3CliTheRealMachine(): void
    {
        Typo3Cli::useRunner(null);
    }
}
