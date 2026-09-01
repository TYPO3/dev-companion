<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * What the container answer says, read from a payload rather than from a
 * container.
 *
 * The probe assembles the installation's own container, which the fixture root
 * cannot stand in for — `D-DIS-023`. So the seam here is the runner: the
 * reading is handed in whole, and what is held is the half that goes wrong
 * without an installation noticing, which is how the answer is built out of it.
 */
final class ServiceLookupTest extends TestCase
{
    private string $root = '';

    #[After]
    public function forgetTheInstance(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::useRunner(null);
        Typo3Cli::forget();
        Typo3Runtime::forget();
        if ($this->root !== '' && is_dir($this->root)) {
            exec('rm -rf ' . escapeshellarg($this->root));
        }
    }

    #[Test]
    public function aDefinitionCarriesItsClassItsTagsAndWhatItIsHanded(): void
    {
        $this->reading([[
            'id' => 'Acme\\Site\\Formatter',
            'class' => 'Acme\\Site\\Formatter',
            'aliasFor' => '',
            'public' => false,
            'shared' => true,
            'autowired' => true,
            'abstract' => false,
            'synthetic' => false,
            'tags' => ['event.listener'],
            'arguments' => [['position' => 0, 'resolves' => 'Psr\\EventDispatcher\\EventDispatcherInterface']],
        ]]);

        $result = Registry::call('typo3_service_lookup', ['query' => 'Formatter']);

        self::assertSame(1, $result->data['matchCount']);
        self::assertSame('Acme\\Site\\Formatter', $result->data['services'][0]['id']);
        self::assertSame(['event.listener'], $result->data['services'][0]['tags']);
        self::assertStringContainsString('0: Psr\\EventDispatcher\\EventDispatcherInterface', $result->text);
    }

    /**
     * An interface reaches its implementation through an alias, and the lookup
     * read definitions alone: fifteen of the thirty-nine ids carrying
     * "interface" in `.environments/e-site-14.3` are aliases, and every one of
     * them answered "nothing matches".
     *
     * What this holds is the half on this side — that an alias entry keeps the
     * class behind it all the way into the answer. Collecting them is the
     * probe's, and no test loads that file.
     */
    #[Decision('D-DIS-023')]
    #[Test]
    public function anAliasCarriesTheClassBehindIt(): void
    {
        $this->reading([[
            'id' => 'Acme\\Site\\FormatterInterface',
            'class' => 'Acme\\Site\\Formatter',
            'aliasFor' => 'Acme\\Site\\Formatter',
            'public' => true,
            'shared' => false,
            'autowired' => false,
            'abstract' => false,
            'synthetic' => false,
            'tags' => [],
            'arguments' => [],
        ]]);

        $result = Registry::call('typo3_service_lookup', ['query' => 'FormatterInterface']);

        self::assertSame(1, $result->data['matchCount']);
        self::assertSame('Acme\\Site\\Formatter', $result->data['services'][0]['aliasFor']);
        self::assertStringContainsString('→ Acme\\Site\\Formatter', $result->text);
    }

    /**
     * The second defect: a container that will not assemble was thrown on, and
     * the one try wrapping the probe turned it into a reading that never
     * happened — every other topic went with it, and the caller was told the
     * installation could not be read rather than what is wrong with it.
     */
    #[Decision('D-DIS-023')]
    #[Test]
    public function aContainerThatDoesNotAssembleIsTheAnswer(): void
    {
        $this->payload([
            'definitionCount' => 0,
            'aliasCount' => 0,
            'compilationFailure' => 'RuntimeException: Cannot autowire service "Acme\\Site\\Formatter": argument "$missing".',
            'services' => [],
        ]);

        $result = Registry::call('typo3_service_lookup', ['query' => 'Formatter']);

        self::assertArrayNotHasKey('unsupported', $result->data);
        self::assertStringContainsString('argument "$missing"', $result->data['compilationFailure']);
        self::assertStringContainsString('does not assemble', $result->text);
    }

    #[Test]
    public function nothingMatchingIsAnAnswerAndSaysWhatWasSearched(): void
    {
        $this->payload(['definitionCount' => 1212, 'aliasCount' => 230, 'compilationFailure' => '', 'services' => []]);

        $result = Registry::call('typo3_service_lookup', ['query' => 'NothingLikeThis']);

        self::assertSame(0, $result->data['matchCount']);
        self::assertSame(1212, $result->data['definitionCount']);
        self::assertStringContainsString('Nothing among the 1212 services', $result->text);
    }

    #[Test]
    public function anInstallationThatCouldNotBeBootedIsNotReportedAsEmpty(): void
    {
        $this->runner(['ok' => false, 'exitCode' => 1, 'output' => '', 'error' => 'the project is stopped']);

        $result = Registry::call('typo3_service_lookup', ['query' => 'Formatter']);

        self::assertArrayHasKey('unsupported', $result->data);
        self::assertArrayNotHasKey('matchCount', $result->data);
    }

    /** @param array<int, array<string, mixed>> $services */
    private function reading(array $services): void
    {
        $this->payload([
            'definitionCount' => count($services),
            'aliasCount' => 0,
            'compilationFailure' => '',
            'services' => $services,
        ]);
    }

    /** @param array<string, mixed> $topic */
    private function payload(array $topic): void
    {
        $this->runner([
            'ok' => true,
            'exitCode' => 0,
            'output' => (string) json_encode(
                ['state' => Typo3Runtime::STATE_FULL, 'reason' => '', 'topics' => ['services' => $topic]],
                JSON_THROW_ON_ERROR,
            ),
            'error' => '',
        ]);
    }

    /** @param array{ok: bool, exitCode: int, output: string, error: string} $result */
    private function runner(array $result): void
    {
        $this->root = sys_get_temp_dir() . '/typo3-dev-companion-services-' . bin2hex(random_bytes(6));
        mkdir($this->root . '/typo3/sysext/core', 0o777, true);
        mkdir($this->root . '/bin');
        file_put_contents($this->root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        file_put_contents($this->root . '/composer.json', (string) json_encode(
            ['name' => 'typo3/cms', 'type' => 'typo3-cms-core'],
            JSON_THROW_ON_ERROR,
        ));
        file_put_contents($this->root . '/typo3/sysext/core/composer.json', (string) json_encode([
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'core']],
        ], JSON_THROW_ON_ERROR));

        $ran = self::createStub(CommandRunner::class);
        $ran->method('run')->willReturn($result);
        $ran->method('locate')->willReturnCallback(
            static fn(string $name): ?string => $name === 'php' ? PHP_BINARY : null,
        );
        Typo3Cli::useRunner($ran);

        Instance::discoverFrom($this->root);
        Typo3Cli::forget();
        Typo3Runtime::forget();
    }
}
