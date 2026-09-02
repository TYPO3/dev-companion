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
 * project-owned extension registers, and no other — `D-AUD-017`.
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

    #[Decision('D-AUD-017')]
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
            $result->data['counts'],
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

    /**
     * A distribution in one call, where it was thirteen counted ones.
     *
     * A session established what 3,101 animals were by status and by which of
     * three columns were empty with one counted call per value, and said six
     * of the thirteen would have been this — `D-ANS-141`. The probe already
     * grouped by page and by the two state flags, so the column the caller
     * names is a third one on the same query.
     */
    #[Decision('D-ANS-141')]
    #[Test]
    public function oneColumnIsCountedPerValueInOneCall(): void
    {
        $this->reading([
            ['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 2997, 'value' => 'adopted'],
            ['pid' => 2, 'deleted' => false, 'hidden' => true, 'rows' => 3, 'value' => 'adopted'],
            ['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 69, 'value' => 'adoption'],
            ['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 1, 'value' => 'permanent'],
        ]);

        $result = Registry::call('typo3_record_lookup', [
            'table' => 'tx_acme_thing',
            'groupBy' => 'status',
            'count' => true,
        ]);

        self::assertSame(
            [
                ['value' => 'adopted', 'total' => 3000, 'live' => 2997, 'hidden' => 3, 'deleted' => 0],
                ['value' => 'adoption', 'total' => 69, 'live' => 69, 'hidden' => 0, 'deleted' => 0],
                ['value' => 'permanent', 'total' => 1, 'live' => 1, 'hidden' => 0, 'deleted' => 0],
            ],
            $result->data['groups'],
        );
        self::assertStringContainsString('By status, 3 value(s):', $result->text);
        self::assertStringContainsString('- adopted: 3000 rows', $result->text);

        // The pages are the same read and are answered as well.
        self::assertSame(3070, $result->data['counts']['total']);
    }

    #[Decision('D-AUD-017')]
    #[Test]
    public function aTableOneScreenLongAsksForNothing(): void
    {
        $this->reading([['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 11]]);

        $result = Registry::call('typo3_record_lookup', ['table' => 'tx_acme_thing']);

        self::assertSame(11, $result->data['matchCount']);
        self::assertStringContainsString('one page of the record list', $result->text);
        self::assertStringNotContainsString('backend module', $result->text);
    }

    #[Decision('D-AUD-017')]
    #[Test]
    public function everyCountSaysItWasReadWithoutBackendPermissions(): void
    {
        $this->reading([['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 11]]);

        $result = Registry::call('typo3_record_lookup', ['table' => 'tx_acme_thing']);

        self::assertStringContainsString('shell user\'s database access', $result->text);
        self::assertStringContainsString('shell user\'s database access', $result->data['readWith']);
    }

    #[Decision('D-AUD-017')]
    #[Test]
    public function aTableNoProjectExtensionRegistersIsRefusedRatherThanRead(): void
    {
        $this->reading([['pid' => 1, 'deleted' => false, 'hidden' => false, 'rows' => 4]]);

        $result = Registry::call('typo3_record_lookup', ['table' => 'tt_content']);

        // Zero and no rows object, so nothing reads as an empty table.
        self::assertSame(0, $result->data['matchCount']);
        self::assertNull($result->data['counts']);
        self::assertStringContainsString('is not read here', $result->text);
        self::assertStringContainsString('vendor/bin/typo3', $result->text);
        // And what it does count is in the same answer, so the refusal is not
        // the end of the call.
        self::assertSame(['tx_acme_thing'], array_column($result->data['countable'], 'table'));
    }

    #[Decision('D-AUD-017')]
    #[Test]
    public function theTablesItWillReadAreListedWithoutOneBeingNamed(): void
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

    #[Decision('D-AUD-017')]
    #[Test]
    public function theRowsComeBackBesideTheCountThatSaysHowManyThereAre(): void
    {
        $this->reading(
            [['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 3101]],
            [
                ['uid' => 1, 'pid' => 2, 'label' => 'Rex', 'changed' => 100, 'created' => 90, 'deleted' => false, 'hidden' => false],
                ['uid' => 2, 'pid' => 2, 'label' => 'Bella', 'changed' => 101, 'created' => 91, 'deleted' => false, 'hidden' => true],
            ],
        );

        $result = Registry::call('typo3_record_lookup', ['table' => 'tx_acme_thing']);

        // The count is the table's and the rows are the page of it that was
        // read, and the answer says which is which.
        self::assertSame(3101, $result->data['matchCount']);
        self::assertSame([1, 2], array_column($result->data['records'], 'uid'));
        self::assertStringContainsString('The first 2 of them by uid, labelled by name', $result->text);
        self::assertStringContainsString('- [2] Bella — hidden', $result->text);
    }

    #[Decision('D-AUD-017')]
    #[Test]
    public function askingForTheCountLeavesTheRowsUnread(): void
    {
        $this->reading(
            [['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 3101]],
            [],
        );

        $result = Registry::call('typo3_record_lookup', ['table' => 'tx_acme_thing', 'count' => true]);

        self::assertSame(3101, $result->data['matchCount']);
        self::assertSame([], $result->data['records']);
    }

    #[Decision('D-AUD-017')]
    #[Test]
    public function aFilterIsEchoedSoTheNumberSaysWhatItCounted(): void
    {
        $this->reading([['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 104]]);

        $result = Registry::call('typo3_record_lookup', [
            'table' => 'tx_acme_thing',
            'where' => ['status' => 'adopted'],
        ]);

        self::assertSame([['column' => 'status', 'value' => 'adopted']], $result->data['where']);
        self::assertStringContainsString("tx_acme_thing where status = 'adopted' holds 104 rows", $result->text);
        // And the sentence about the editing surface is withheld, because 104
        // of a filtered set says nothing about the page an editor opens.
        self::assertStringContainsString('That is the filtered set', $result->text);
        self::assertStringNotContainsString('backend module', $result->text);
    }

    #[Decision('D-AUD-017')]
    #[Test]
    public function aFilterOnAColumnTheTableHasNotIsAnsweredRatherThanRun(): void
    {
        $this->reading([['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 104]]);

        $result = Registry::call('typo3_record_lookup', [
            'table' => 'tx_acme_thing',
            'where' => ['quantumflux' => 1],
        ]);

        // Nothing was read, so nothing reads as an empty table.
        self::assertSame(0, $result->data['matchCount']);
        self::assertNull($result->data['counts']);
        self::assertStringContainsString('has no column quantumflux', $result->text);
        self::assertStringContainsString('typo3_schema_lookup', $result->text);
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
     * @param array<int, array<string, mixed>> $rows
     */
    private function reading(array $groups, array $rows = []): void
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
                    'derivedColumns' => ['tables' => ['tx_acme_thing' => [
                        'columns' => [
                            ['name' => 'uid', 'type' => 'integer', 'notnull' => true],
                            ['name' => 'pid', 'type' => 'integer', 'notnull' => true],
                            ['name' => 'status', 'type' => 'string', 'notnull' => false],
                        ],
                        'relationTable' => false,
                    ]]],
                    'records' => [
                        'table' => 'tx_acme_thing',
                        'deleteField' => 'deleted',
                        'hiddenField' => 'hidden',
                        'labelField' => 'name',
                        'groups' => $groups,
                        'rows' => $rows,
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
