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
 * The two sides of a table answer, and what stands in for the live one where
 * there is no schema to read.
 *
 * The derived side is the installation's and the live side is the database's,
 * and the answer has to stay usable where only the first is there — that is
 * the state the tool is asked in while the file creating the schema is being
 * written (`D-DIS-022`).
 */
final class SchemaLookupTest extends TestCase
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

    #[Decision('D-DIS-022')]
    #[Test]
    public function bothSidesStandBesideEachOtherAndAgreementIsSaidPlainly(): void
    {
        $this->reading([
            'tables' => ['tx_acme_thing'],
            'table' => 'tx_acme_thing',
            'present' => true,
            'columns' => [['name' => 'uid', 'type' => 'integer', 'notnull' => true, 'default' => null, 'length' => null]],
            'indexes' => [['name' => 'PRIMARY', 'columns' => ['uid'], 'unique' => true, 'primary' => true]],
            'statementCount' => 1,
            'suggestions' => [],
        ]);

        $result = Registry::call('typo3_schema_lookup', ['table' => 'tx_acme_thing']);

        self::assertTrue($result->data['actual']['present']);
        self::assertSame([], $result->data['updates']);
        self::assertStringContainsString('matches what this installation declares', $result->text);
    }

    #[Decision('D-DIS-022')]
    #[Test]
    public function whatTypo3WouldChangeIsNamedByItsOwnChangeType(): void
    {
        $this->reading([
            'tables' => ['tx_acme_thing'],
            'table' => 'tx_acme_thing',
            'present' => true,
            'columns' => [],
            'indexes' => [],
            'statementCount' => 1,
            'suggestions' => [['connection' => 'Default', 'change' => 'add', 'tables' => ['tx_acme_thing']]],
        ]);

        $result = Registry::call('typo3_schema_lookup', ['table' => 'tx_acme_thing']);

        self::assertSame('add', $result->data['updates'][0]['change']);
        self::assertStringContainsString('database:updateschema', $result->text);
    }

    #[Test]
    public function aTableTheDatabaseDoesNotHaveIsSaidRatherThanLeftOut(): void
    {
        $this->reading([
            'tables' => [],
            'table' => 'tx_acme_thing',
            'present' => false,
            'statementCount' => 1,
            'suggestions' => [],
        ]);

        $result = Registry::call('typo3_schema_lookup', ['table' => 'tx_acme_thing']);

        self::assertFalse($result->data['actual']['present']);
        self::assertStringContainsString('The database has no tx_acme_thing', $result->text);
    }

    /**
     * The state the derived side was bounded for: the file that creates the
     * schema is being written and there is no schema behind it yet. The answer
     * keeps the derived columns and says the other side was not readable.
     */
    #[Decision('D-DIS-022')]
    #[Test]
    public function anUnreadableDatabaseLeavesTheDerivedSideStanding(): void
    {
        $this->reading(['unavailable' => 'Doctrine\\DBAL\\Exception: could not connect']);

        $result = Registry::call('typo3_schema_lookup', ['table' => 'tx_acme_thing']);

        self::assertNull($result->data['actual']);
        self::assertNull($result->data['updates']);
        self::assertSame(1, $result->data['matchCount'], 'the derived columns are still the answer');
        self::assertStringContainsString('not readable from here', $result->text);
    }

    #[Test]
    public function aCallThatNamesNoTableOpensNoConnection(): void
    {
        $this->reading(['tables' => [], 'statementCount' => 0, 'suggestions' => []]);

        $result = Registry::call('typo3_schema_lookup', []);

        self::assertNull($result->data['actual']);
        self::assertNull($result->data['updates']);
    }

    /** @param array<string, mixed> $liveSchema */
    private function reading(array $liveSchema): void
    {
        $this->root = sys_get_temp_dir() . '/typo3-dev-companion-schema-' . bin2hex(random_bytes(6));
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
        $ran->method('run')->willReturn([
            'ok' => true,
            'exitCode' => 0,
            'output' => (string) json_encode([
                'state' => Typo3Runtime::STATE_FULL,
                'reason' => '',
                'topics' => [
                    'derivedColumns' => ['tables' => ['tx_acme_thing' => [
                        'columns' => [['name' => 'uid', 'type' => 'integer', 'notnull' => true, 'default' => null, 'length' => null]],
                        'relationTable' => false,
                    ]]],
                    'liveSchema' => $liveSchema,
                ],
            ], JSON_THROW_ON_ERROR),
            'error' => '',
        ]);
        $ran->method('locate')->willReturnCallback(
            static fn(string $name): ?string => $name === 'php' ? PHP_BINARY : null,
        );
        Typo3Cli::useRunner($ran);

        Instance::discoverFrom($this->root);
        Typo3Cli::forget();
        Typo3Runtime::forget();
    }
}
