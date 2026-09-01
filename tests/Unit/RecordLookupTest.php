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
 * What the count answers, and the boundary it answers inside.
 *
 * The count exists because a session held the number 3101 twice and drew
 * nothing from it, so the answer says what the number means for where the
 * records are edited. What it may be asked about is the other half: a table a
 * project-owned extension registers, and no other — `D-AUD-016`.
 */
final class RecordLookupTest extends TestCase
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

    #[Decision('D-AUD-016')]
    #[Test]
    public function aTableFullEnoughToLeaveTheRecordListIsSaidToBe(): void
    {
        $this->reading([
            ['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 2997],
            ['pid' => 2, 'deleted' => false, 'hidden' => true, 'rows' => 104],
            ['pid' => 2, 'deleted' => true, 'hidden' => false, 'rows' => 7],
            ['pid' => 5, 'deleted' => false, 'hidden' => false, 'rows' => 3],
        ]);

        $result = Registry::call('typo3_record_lookup', ['table' => 'tx_acme_thing']);

        self::assertSame(3111, $result->data['matchCount']);
        self::assertSame(
            ['total' => 3111, 'live' => 3000, 'hidden' => 104, 'deleted' => 7],
            $result->data['rows'],
        );
        // The fullest page first, and a deleted row counted as deleted rather
        // than twice.
        self::assertSame(2, $result->data['pages'][0]['pid']);
        self::assertSame(3108, $result->data['pages'][0]['total']);
        self::assertSame(5, $result->data['pages'][1]['pid']);
        // The sentence the sighted session did not have.
        self::assertStringContainsString('156 pages of the record list', $result->text);
        self::assertStringContainsString('typo3_backend_module_lookup', $result->text);
    }

    #[Decision('D-AUD-016')]
    #[Test]
    public function aTableOneScreenLongAsksForNothing(): void
    {
        $this->reading([['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 11]]);

        $result = Registry::call('typo3_record_lookup', ['table' => 'tx_acme_thing']);

        self::assertSame(11, $result->data['matchCount']);
        self::assertStringContainsString('one page of the record list', $result->text);
        self::assertStringNotContainsString('backend module', $result->text);
    }

    #[Decision('D-AUD-016')]
    #[Test]
    public function everyCountSaysItWasReadWithoutBackendPermissions(): void
    {
        $this->reading([['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 11]]);

        $result = Registry::call('typo3_record_lookup', ['table' => 'tx_acme_thing']);

        self::assertStringContainsString('shell user\'s database access', $result->text);
        self::assertStringContainsString('shell user\'s database access', $result->data['readWith']);
    }

    #[Decision('D-AUD-016')]
    #[Test]
    public function aTableNoProjectExtensionRegistersIsRefusedRatherThanCounted(): void
    {
        $this->reading([['pid' => 1, 'deleted' => false, 'hidden' => false, 'rows' => 4]]);

        $result = Registry::call('typo3_record_lookup', ['table' => 'tt_content']);

        // Zero and no rows object, so nothing reads as an empty table.
        self::assertSame(0, $result->data['matchCount']);
        self::assertNull($result->data['rows']);
        self::assertStringContainsString('is not counted here', $result->text);
        self::assertStringContainsString('vendor/bin/typo3', $result->text);
        // And what it does count is in the same answer, so the refusal is not
        // the end of the call.
        self::assertSame(['tx_acme_thing'], array_column($result->data['countable'], 'table'));
    }

    #[Decision('D-AUD-016')]
    #[Test]
    public function theTablesItWillCountAreListedWithoutOneBeingNamed(): void
    {
        $this->reading([]);

        $result = Registry::call('typo3_record_lookup', []);

        self::assertNull($result->data['table']);
        self::assertSame(1, $result->data['matchCount']);
        self::assertSame(
            [['table' => 'tx_acme_thing', 'extension' => 'acme_thing']],
            $result->data['countable'],
        );
    }

    /**
     * A project installation with one extension of its own, and one reading of
     * it.
     *
     * `tt_content` stands in the TCA beside the project's table, which is what
     * makes the refusal a reading of the boundary rather than of the table
     * list: the installation has it and the tool will not count it.
     *
     * @param array<int, array{pid: int, deleted: bool, hidden: bool, rows: int}> $groups
     */
    private function reading(array $groups): void
    {
        $this->root = sys_get_temp_dir() . '/typo3-dev-companion-records-' . bin2hex(random_bytes(6));
        mkdir($this->root . '/packages/acme_thing', 0o777, true);
        mkdir($this->root . '/vendor/composer', 0o777, true);
        mkdir($this->root . '/bin');
        file_put_contents($this->root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        file_put_contents($this->root . '/composer.json', (string) json_encode(
            ['name' => 'acme/site', 'type' => 'project'],
            JSON_THROW_ON_ERROR,
        ));
        file_put_contents($this->root . '/vendor/composer/installed.json', (string) json_encode([
            'packages' => [[
                'name' => 'acme/acme-thing',
                'type' => 'typo3-cms-extension',
                'install-path' => '../../packages/acme_thing',
                'extra' => ['typo3/cms' => ['extension-key' => 'acme_thing']],
            ]],
        ], JSON_THROW_ON_ERROR));

        $ran = self::createStub(CommandRunner::class);
        $ran->method('run')->willReturn([
            'ok' => true,
            'exitCode' => 0,
            'output' => (string) json_encode([
                'state' => Typo3Runtime::STATE_FULL,
                'reason' => '',
                'topics' => [
                    'tables' => [
                        'tx_acme_thing' => 'LLL:EXT:acme_thing/Resources/Private/Language/locallang_db.xlf:thing',
                        'tt_content' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:tt_content',
                    ],
                    'recordCount' => [
                        'table' => 'tx_acme_thing',
                        'deleteField' => 'deleted',
                        'hiddenField' => 'hidden',
                        'groups' => $groups,
                    ],
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
