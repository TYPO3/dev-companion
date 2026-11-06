<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;

/**
 * The launcher a DDEV project's entry runs, with the `ddev` it calls replaced.
 * A real project measured the same behaviour on 2026-11-06 — `D-DIS-037`.
 */
final class DdevLauncherTest extends TestCase
{
    private const REQUESTS = '{"jsonrpc":"2.0","id":7,"method":"initialize","params":{}}' . "\n"
        . '{"jsonrpc":"2.0","method":"notifications/initialized"}' . "\n"
        . '{"jsonrpc":"2.0","method":"tools/list","id":"a"}' . "\n";

    private function launched(string $ddev): string
    {
        $process = new Process(
            ['sh', Paths::root() . '/bin/typo3-dev-companion-ddev', 'vendor/bin/typo3-dev-companion'],
            null,
            ['TYPO3_DEV_COMPANION_DDEV' => $ddev],
            self::REQUESTS,
        );
        $process->mustRun();

        return $process->getOutput();
    }

    #[Decision('D-DIS-037')]
    #[Test]
    public function aStoppedProjectAnswersEveryRequestWithTheCommandOnACleanStream(): void
    {
        $lines = explode("\n", trim($this->launched('false')));

        self::assertCount(2, $lines, 'a notification takes no answer');
        $first = json_decode($lines[0], true, flags: JSON_THROW_ON_ERROR);
        $second = json_decode($lines[1], true, flags: JSON_THROW_ON_ERROR);
        self::assertSame(7, $first['id']);
        self::assertSame('a', $second['id']);
        self::assertStringContainsString('ddev start', $first['error']['message']);
    }

    #[Decision('D-DIS-037')]
    #[Test]
    public function aRunningProjectHandsTheServerTheClientsFirstRequest(): void
    {
        self::assertStringStartsWith(
            'server vendor/bin/typo3-dev-companion {"jsonrpc":"2.0","id":7,',
            $this->launched(Paths::root() . '/tests/Support/ddev-running'),
        );
    }
}
