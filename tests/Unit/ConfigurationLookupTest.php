<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\Fixture;

/**
 * Where the effective configuration value comes from, and what stands in its
 * place where there was none to read.
 *
 * The source is the booted container on every covered line. `configuration:show`
 * arrived in TYPO3 14.2, so a console answer would leave 12.4 and 13.4 holding
 * "command is not defined" — which is the answer `D-ANS-077` rules out and what
 * these three cases exist to keep out of the tool.
 */
final class ConfigurationLookupTest extends TestCase
{
    private string $root = '';

    #[After]
    public function forgetTheInstance(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();
        Typo3Runtime::forget();
        if ($this->root !== '' && is_dir($this->root)) {
            exec('rm -rf ' . escapeshellarg($this->root));
        }
    }

    #[Test]
    public function theEffectiveValueComesFromTheBootedInstallation(): void
    {
        $this->discover(['SYS' => ['fluid' => ['namespaces' => ['acme' => ['Acme\\Site\\ViewHelpers']]]]]);

        $result = Registry::call('typo3_configuration_lookup', ['configurationPath' => 'SYS/fluid/namespaces']);

        self::assertTrue($result->data['found']);
        self::assertSame(['acme' => ['Acme\\Site\\ViewHelpers']], $result->data['value']);
        self::assertSame('installation', $result->data['answeredBy']);
    }

    #[Test]
    public function aPathTheInstallationDoesNotHaveIsAnswered(): void
    {
        $this->discover(['SYS' => ['fluid' => []]]);

        $result = Registry::call('typo3_configuration_lookup', ['configurationPath' => 'SYS/thereIsNoSuchThing']);

        self::assertFalse($result->data['found']);
        self::assertNull($result->data['value']);
    }

    #[Test]
    public function anInstallationThatCouldNotBeBootedIsNotReportedAsEmpty(): void
    {
        // R-ANS-001. The boot fails because no autoloader was written, and the
        // answer has to stay distinguishable from the case above it: `found`
        // is a statement about an installation, and none was consulted.
        $this->discover(null);

        $result = Registry::call('typo3_configuration_lookup', ['configurationPath' => 'SYS/fluid']);

        self::assertArrayNotHasKey('found', $result->data);
        self::assertStringNotContainsString('has no configuration at', $result->text);
    }

    /** @param array<string, mixed>|null $configuration null writes no autoloader, so the boot fails */
    private function discover(?array $configuration): void
    {
        $this->root = sys_get_temp_dir() . '/typo3-dev-companion-configuration-' . bin2hex(random_bytes(6));
        mkdir($this->root . '/typo3/sysext/core', 0o777, true);
        mkdir($this->root . '/bin');
        file_put_contents($this->root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        file_put_contents($this->root . '/composer.json', json_encode(
            ['name' => 'typo3/cms', 'type' => 'typo3-cms-core'],
            JSON_THROW_ON_ERROR
        ));
        file_put_contents($this->root . '/typo3/sysext/core/composer.json', json_encode([
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'core']],
        ], JSON_THROW_ON_ERROR));

        if ($configuration !== null) {
            Fixture::bootsInto($this->root, configuration: $configuration);
        }

        Instance::discoverFrom($this->root);
        Typo3Cli::forget();
        Typo3Runtime::forget();
    }
}
