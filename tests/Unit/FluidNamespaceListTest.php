<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\Registry;

/** Which Fluid namespace source each TYPO3 version exposes. */
final class FluidNamespaceListTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstallation(): void
    {
        putenv(Typo3Cli::CONSOLE_VARIABLE);
        Instance::discoverFrom(null);
        Typo3Cli::useRunner(null);
        Typo3Cli::forget();
        Typo3Runtime::forget();
    }

    #[DataProvider('versionsBeforeFourteen')]
    #[Test]
    public function aVersionBeforeFourteenReadsTheRuntimeConfiguration(string $version): void
    {
        $root = $this->coreCheckout($version);
        Instance::discoverFrom($root);
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=' . PHP_BINARY . ' ' . $root . '/bin/typo3');
        Typo3Cli::forget();

        $commands = [];
        $runner = self::createStub(CommandRunner::class);
        $runner->method('run')->willReturnCallback(static function (array $command) use (&$commands): array {
            $commands[] = $command;

            return [
                'ok' => true,
                'exitCode' => 0,
                'output' => json_encode([
                    'state' => Typo3Runtime::STATE_FULL,
                    'reason' => '',
                    'topics' => ['configuration' => [
                        'found' => true,
                        'value' => ['acme' => ['Acme\\ViewHelpers']],
                    ]],
                ], JSON_THROW_ON_ERROR),
                'error' => '',
            ];
        });
        Typo3Cli::useRunner($runner);

        $result = Registry::call('typo3_fluid_namespace_list', []);

        self::assertSame('installation', $result->data['answeredBy']);
        self::assertSame([[
            'prefix' => 'acme',
            'phpNamespaces' => ['Acme\\ViewHelpers'],
        ]], $result->data['namespaces']);
        self::assertCount(1, $commands);
        self::assertSame('-r', $commands[0][1]);
        if (preg_match('/base64_decode\("([A-Za-z0-9+\/=]+)"\)/', $commands[0][2], $payload) !== 1) {
            self::fail('The runtime probe was not passed as a base64-encoded PHP payload.');
        }
        self::assertStringContainsString(
            "'configurationPath' => 'SYS/fluid/namespaces'",
            (string) base64_decode($payload[1], true),
        );
    }

    /** @return iterable<string, array{string}> */
    public static function versionsBeforeFourteen(): iterable
    {
        yield 'TYPO3 v12' => ['12.4.42'];
        yield 'TYPO3 v13' => ['13.4.22'];
    }

    #[Test]
    public function fourteenUsesTheFluidNamespaceCommand(): void
    {
        $root = $this->coreCheckout('14.0.0');
        Instance::discoverFrom($root);
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=' . PHP_BINARY . ' ' . $root . '/bin/typo3');
        Typo3Cli::forget();

        $commands = [];
        $runner = self::createStub(CommandRunner::class);
        $runner->method('run')->willReturnCallback(static function (array $command) use (&$commands): array {
            $commands[] = $command;

            return [
                'ok' => true,
                'exitCode' => 0,
                'output' => '{"core":["TYPO3\\\\CMS\\\\Core\\\\ViewHelpers"]}',
                'error' => '',
            ];
        });
        Typo3Cli::useRunner($runner);

        $result = Registry::call('typo3_fluid_namespace_list', []);

        self::assertSame('installation', $result->data['answeredBy']);
        self::assertSame([
            PHP_BINARY,
            $root . '/bin/typo3',
            'fluid:namespaces',
            '--json',
            '--no-interaction',
            '--no-ansi',
        ], $commands[0]);
        self::assertSame('core', $result->data['namespaces'][0]['prefix']);
    }
}
