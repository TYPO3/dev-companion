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
 * records are edited. What it may be asked about is the other half: a table this
 * installation has TCA for, and the columns of a row beyond its fixed shape
 * are the caller's to name — `D-AUD-018`.
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

    #[Decision('D-AUD-018')]
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

    #[Decision('D-AUD-018')]
    #[Test]
    public function aTableOneScreenLongAsksForNothing(): void
    {
        $this->reading([['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 11]]);

        $result = Registry::call('typo3_record_lookup', ['table' => 'tx_acme_thing']);

        self::assertSame(11, $result->data['matchCount']);
        self::assertStringContainsString('one page of the record list', $result->text);
        self::assertStringNotContainsString('backend module', $result->text);
    }

    #[Decision('D-AUD-018')]
    #[Test]
    public function everyCountSaysItWasReadWithoutBackendPermissions(): void
    {
        $this->reading([['pid' => 2, 'deleted' => false, 'hidden' => false, 'rows' => 11]]);

        $result = Registry::call('typo3_record_lookup', ['table' => 'tx_acme_thing']);

        self::assertStringContainsString('shell user\'s database access', $result->text);
        self::assertStringContainsString('shell user\'s database access', $result->data['readWith']);
    }

    #[Decision('D-AUD-018')]
    #[Test]
    public function aTableTheInstallationHasNoTcaForIsRefusedRatherThanRead(): void
    {
        $this->reading([['pid' => 1, 'deleted' => false, 'hidden' => false, 'rows' => 4]]);

        $result = Registry::call('typo3_record_lookup', ['table' => 'cache_pages']);

        // Zero and no rows object, so nothing reads as an empty table.
        self::assertSame(0, $result->data['matchCount']);
        self::assertNull($result->data['counts']);
        self::assertStringContainsString('is not a table this installation has TCA for', $result->text);
        // And what it does read is in the same answer, so the refusal is not
        // the end of the call.
        self::assertSame(
            ['be_users', 'tt_content', 'tx_acme_thing'],
            array_column($result->data['countable'], 'table'),
        );
    }

    /**
     * A table the core registers is read like a project's own.
     *
     * Six `ddev mysql` queries over `tt_content` and `pages` decided the markup
     * of a replacement layout and the tool was never tried, because it refused
     * both — `feedback/archive/2026-09-04-053618`, `D-AUD-018`.
     */
    #[Decision('D-AUD-018')]
    #[Test]
    public function aTableTheCoreRegistersIsReadLikeAProjectsOwn(): void
    {
        $this->reading([['pid' => 3, 'deleted' => false, 'hidden' => false, 'rows' => 137]]);

        $result = Registry::call('typo3_record_lookup', ['table' => 'tt_content']);

        self::assertSame(137, $result->data['matchCount']);
        self::assertStringNotContainsString('is not a table', $result->text);
    }

    #[Decision('D-AUD-018')]
    #[Test]
    public function theTablesItWillReadAreListedWithoutOneBeingNamed(): void
    {
        $this->reading([]);

        $result = Registry::call('typo3_record_lookup', []);

        self::assertNull($result->data['table']);
        self::assertSame(3, $result->data['matchCount']);
        self::assertSame(
            [
                ['table' => 'be_users', 'extension' => 'core'],
                ['table' => 'tt_content', 'extension' => 'frontend'],
                ['table' => 'tx_acme_thing', 'extension' => 'acme_thing'],
            ],
            $result->data['countable'],
        );
    }

    /**
     * The columns a row carries beyond its fixed shape are the caller's.
     *
     * A session read ten `tt_content` columns out of the database by hand
     * because nothing here would hand them over — `D-AUD-018`. Each name is
     * checked against the table the way a filter's columns are, which is what
     * lets it go into the SQL as an identifier.
     */
    #[Decision('D-AUD-018')]
    #[Test]
    public function theColumnsARowCarriesBeyondItsFixedShapeAreTheCallers(): void
    {
        $this->reading(
            [['pid' => 3, 'deleted' => false, 'hidden' => false, 'rows' => 2]],
            [
                [
                    'uid' => 12, 'pid' => 3, 'label' => 'Stage', 'changed' => 1, 'created' => 1,
                    'deleted' => false, 'hidden' => false,
                    'values' => [['column' => 'CType', 'value' => 'textmedia'], ['column' => 'header_layout', 'value' => 1]],
                ],
            ],
        );

        $result = Registry::call('typo3_record_lookup', [
            'table' => 'tt_content',
            'columns' => ['CType', 'header_layout'],
        ]);

        self::assertSame(
            [['column' => 'CType', 'value' => 'textmedia'], ['column' => 'header_layout', 'value' => 1]],
            $result->data['records'][0]['values'],
        );
        self::assertStringContainsString('- [12] Stage — CType = textmedia, header_layout = 1', $result->text);
    }

    #[Decision('D-AUD-018')]
    #[Test]
    public function aNamedColumnTheTableHasNotIsAnsweredRatherThanRead(): void
    {
        $this->reading([['pid' => 3, 'deleted' => false, 'hidden' => false, 'rows' => 2]]);

        $result = Registry::call('typo3_record_lookup', [
            'table' => 'tt_content',
            'columns' => ['quantumflux'],
        ]);

        self::assertSame(0, $result->data['matchCount']);
        self::assertStringContainsString('has no column quantumflux', $result->text);
    }

    /**
     * The one row that departs from the default, beside the distribution.
     *
     * A distribution says what the table holds; which of it is the exception is
     * what a cleanup turns on, and the reported session's one `header_layout`
     * row out of 137 was the site's only h1 — `D-AUD-018`.
     */
    #[Decision('D-AUD-018')]
    #[Test]
    public function theRowsDepartingFromTheColumnsDefaultAreNamedBesideIt(): void
    {
        $this->reading(
            [
                ['pid' => 3, 'deleted' => false, 'hidden' => false, 'rows' => 136, 'value' => 0],
                ['pid' => 3, 'deleted' => false, 'hidden' => false, 'rows' => 1, 'value' => 1],
            ],
            [],
            0,
            [['uid' => 412, 'pid' => 3, 'value' => 1]],
        );

        $result = Registry::call('typo3_record_lookup', [
            'table' => 'tt_content',
            'groupBy' => 'header_layout',
            'count' => true,
        ]);

        self::assertSame(0, $result->data['groupDefault']);
        self::assertSame([['uid' => 412, 'pid' => 3, 'value' => 1]], $result->data['departing']);
        self::assertStringContainsString('The TCA default is 0. All 1 of them by uid depart from it', $result->text);
        self::assertStringContainsString('- [412] on pid 3: 1', $result->text);
    }

    /**
     * A column with no default has nothing to depart from, and says so.
     *
     * An empty departure list and a column TCA declares no default for read
     * alike otherwise, and only one of them means every row is the convention.
     */
    #[Decision('D-AUD-018')]
    #[Test]
    public function aColumnDeclaringNoDefaultSaysSoRatherThanNamingNoRow(): void
    {
        $this->reading(
            [['pid' => 3, 'deleted' => false, 'hidden' => false, 'rows' => 137, 'value' => 'text']],
            [],
            null,
        );

        $result = Registry::call('typo3_record_lookup', [
            'table' => 'tt_content',
            'groupBy' => 'CType',
            'count' => true,
        ]);

        self::assertNull($result->data['groupDefault']);
        self::assertSame([], $result->data['departing']);
        self::assertStringContainsString('CType declares no default in TCA', $result->text);
    }

    #[Decision('D-AUD-018')]
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

    #[Decision('D-AUD-018')]
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

    #[Decision('D-AUD-018')]
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

    #[Decision('D-AUD-018')]
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
     * Three tables stand in the TCA and `cache_pages` stands in none, which is
     * what makes the refusal a reading of the boundary: what TCA describes is
     * read, and what it does not is the caches and the queues.
     *
     * @param array<int, array{pid: int, deleted: bool, hidden: bool, rows: int}> $groups
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, array{uid: int, pid: int, value: mixed}> $departing
     */
    private function reading(array $groups, array $rows = [], mixed $default = null, array $departing = []): void
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
                        'be_users' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:be_users',
                    ],
                    'derivedColumns' => ['tables' => [
                        'tx_acme_thing' => [
                            'columns' => [
                                ['name' => 'uid', 'type' => 'integer', 'notnull' => true],
                                ['name' => 'pid', 'type' => 'integer', 'notnull' => true],
                                ['name' => 'status', 'type' => 'string', 'notnull' => false],
                            ],
                            'relationTable' => false,
                        ],
                        'tt_content' => [
                            'columns' => [
                                ['name' => 'uid', 'type' => 'integer', 'notnull' => true],
                                ['name' => 'pid', 'type' => 'integer', 'notnull' => true],
                                ['name' => 'CType', 'type' => 'string', 'notnull' => true],
                                ['name' => 'header_layout', 'type' => 'string', 'notnull' => true],
                            ],
                            'relationTable' => false,
                        ],
                    ]],
                    'records' => [
                        'table' => 'tx_acme_thing',
                        'deleteField' => 'deleted',
                        'hiddenField' => 'hidden',
                        'labelField' => 'name',
                        'groups' => $groups,
                        'groupDefault' => $default,
                        'departing' => $departing,
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
