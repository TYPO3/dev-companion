<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\Fixture;

/**
 * Resolving one `type=flex` column through the installation that would resolve
 * it.
 *
 * There is no real TYPO3 here, so what the installations below carry is a
 * `FlexFormTools` that answers rather than one that resolves. The probe is the
 * real one, and this holds what it does with the answer. The record it got
 * reaches the resolution. The exception is the answer where nothing resolved.
 * The schema TYPO3 v14 wants goes to the signature that wants it, and the probe
 * never looks for it on the one that does not.
 */
final class FlexFormLookupTest extends TestCase
{
    private string $root = '';

    /** The structure the installations below hand back for the teaser. */
    private const TEASER = ['sheets' => ['sDEF' => ['ROOT' => [
        'sheetTitle' => 'Teaser',
        'type' => 'array',
        'el' => [
            'settings.headline' => ['label' => 'Headline', 'config' => ['type' => 'input', 'required' => true]],
            'settings.slides' => [
                'type' => 'array',
                'section' => '1',
                'title' => 'Slides',
                'el' => ['slide' => ['type' => 'array', 'title' => 'Slide', 'el' => [
                    'settings.slide.title' => ['label' => 'Title', 'config' => ['type' => 'input']],
                ]]],
            ],
        ],
    ]]]];

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

    #[Decision('D-ANS-095')]
    #[Test]
    public function theStructureIsTheOneTheInstallationResolved(): void
    {
        $this->discover(takesTheSchema: true);

        $result = Registry::call('typo3_flexform_lookup', [
            'table' => 'tt_content',
            'field' => 'pi_flexform',
            'record' => ['CType' => 'acme_teaser'],
        ]);

        self::assertTrue($result->data['resolved']);
        self::assertSame('installation', $result->data['answeredBy']);
        // The identifier the installation produced, and the record value in it:
        // the emulated row reached the resolution rather than came back as an
        // echo.
        self::assertSame('acme_teaser', $result->data['decoded']['dataStructureKey']);
        self::assertSame('tca', $result->data['decoded']['type']);
        self::assertSame($result->data['identifier'], json_encode($result->data['decoded']));

        $sheet = $result->data['sheets'][0];
        self::assertSame('sDEF', $sheet['sheet']);
        self::assertSame('Teaser', $sheet['title']);
        self::assertSame(['settings.headline', 'settings.slides'], array_column($sheet['fields'], 'field'));
        self::assertSame(['input', 'section'], array_column($sheet['fields'], 'type'));
        self::assertTrue($sheet['fields'][0]['required']);

        // A section carries its container types and their fields, which is the
        // one nested level a data structure has.
        $containers = $sheet['fields'][1]['containers'];
        self::assertSame(['slide'], array_column($containers, 'container'));
        self::assertSame(['settings.slide.title'], array_column($containers[0]['fields'], 'field'));

        self::assertStringContainsString('settings.slide.title', $result->text);
        self::assertStringContainsString('no row was read', $result->text);
    }

    #[Test]
    public function anotherRecordReachesAnotherStructure(): void
    {
        // The whole reason the record is an argument: the same column resolves
        // to a different structure for a different content element.
        $this->discover(takesTheSchema: true);

        $result = Registry::call('typo3_flexform_lookup', [
            'table' => 'tt_content',
            'field' => 'pi_flexform',
            'record' => ['CType' => 'acme_map'],
        ]);

        self::assertSame('acme_map', $result->data['decoded']['dataStructureKey']);
        self::assertSame(['settings.zoom'], array_column($result->data['sheets'][0]['fields'], 'field'));
    }

    #[Test]
    public function aRecordTypeNothingIsRegisteredForIsAnsweredWithTheException(): void
    {
        $this->discover(takesTheSchema: true);

        $result = Registry::call('typo3_flexform_lookup', [
            'table' => 'tt_content',
            'field' => 'pi_flexform',
            'record' => ['CType' => 'acme_nothing'],
        ]);

        // An answer rather than a breakage, and the schema's promise is intact:
        // there is no unsupported half, and the failure says what threw.
        self::assertArrayNotHasKey('unsupported', $result->data);
        self::assertFalse($result->data['resolved']);
        self::assertStringContainsString('1732198004', $result->data['failure']);
        self::assertStringContainsString('The resolution said:', $result->text);

        // And what to retry with: the keys the column declares and the column
        // that carries the record type.
        self::assertSame(['default', 'acme_teaser', 'acme_map'], $result->data['declaration']['keys']);
        self::assertSame('CType', $result->data['declaration']['recordTypeField']);
        self::assertStringContainsString('pass that column in record', $result->text);
    }

    #[Test]
    public function aColumnThatIsNotFlexIsAnsweredWithTheOnesThatAre(): void
    {
        $this->discover(takesTheSchema: true);

        $result = Registry::call('typo3_flexform_lookup', ['table' => 'tt_content', 'field' => 'bodytext']);

        self::assertFalse($result->data['resolved']);
        self::assertSame('text', $result->data['declaration']['type']);
        self::assertSame(['pi_flexform'], $result->data['declaration']['flexFields']);
        self::assertStringContainsString('not type=flex', $result->text);
    }

    #[Test]
    public function aTableTheInstallationHasNoTcaForIsSaidToBeOne(): void
    {
        $this->discover(takesTheSchema: true);

        $result = Registry::call('typo3_flexform_lookup', ['table' => 'tx_nothing', 'field' => 'pi_flexform']);

        self::assertFalse($result->data['resolved']);
        self::assertStringContainsString('is not a table this installation has TCA for', $result->text);
    }

    /**
     * The v14 signature wants a `TcaSchema` and throws on the default path
     * without one. So a table its factory does not have is the case that shows
     * the probe asked the factory and passed what it got.
     */
    #[Test]
    public function whereTheSignatureWantsASchemaTheInstallationsOwnIsPassed(): void
    {
        $this->discover(takesTheSchema: true, schemaFor: []);

        $result = Registry::call('typo3_flexform_lookup', [
            'table' => 'tt_content',
            'field' => 'pi_flexform',
            'record' => ['CType' => 'acme_teaser'],
        ]);

        self::assertFalse($result->data['resolved']);
        self::assertStringContainsString('1753182123', $result->data['failure']);
    }

    /**
     * And the two LTS lines, whose `FlexFormTools` has no such parameter and
     * whose installation has no `TcaSchemaFactory` to ask. Reaching for one
     * would be an `Error` here rather than a resolved structure.
     */
    #[Test]
    public function whereTheSignatureWantsNoSchemaNoneIsLookedFor(): void
    {
        $this->discover(takesTheSchema: false);

        $result = Registry::call('typo3_flexform_lookup', [
            'table' => 'tt_content',
            'field' => 'pi_flexform',
            'record' => ['CType' => 'acme_teaser'],
        ]);

        self::assertSame('', $result->data['failure']);
        self::assertTrue($result->data['resolved']);
        self::assertSame(['settings.headline', 'settings.slides'], array_column(
            $result->data['sheets'][0]['fields'],
            'field',
        ));
    }

    /**
     * `R-ANS-001`. An installation that did not boot does not read as a column
     * with no structure. The whole answer is the reason, and none of the fields
     * that state something about an installation is there.
     */
    #[Test]
    public function anInstallationThatCouldNotBeBootedIsNotAnEmptyColumn(): void
    {
        $this->discover(boots: false);

        $result = Registry::call('typo3_flexform_lookup', ['table' => 'tt_content', 'field' => 'pi_flexform']);

        self::assertArrayNotHasKey('resolved', $result->data);
        self::assertArrayNotHasKey('declaration', $result->data);
        self::assertNotSame('', $result->data['unsupported']['reason']);
    }

    /** @param array<int, string>|null $schemaFor the tables its schema factory has, or every one with TCA */
    private function discover(bool $takesTheSchema = true, ?array $schemaFor = null, bool $boots = true): void
    {
        $this->root = sys_get_temp_dir() . '/typo3-dev-companion-flexform-' . bin2hex(random_bytes(6));
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

        $flexForm = [
            'pointer' => 'CType',
            'structures' => [
                'acme_teaser' => self::TEASER,
                'acme_map' => ['sheets' => ['sDEF' => ['ROOT' => ['type' => 'array', 'el' => [
                    'settings.zoom' => ['label' => 'Zoom', 'config' => ['type' => 'number']],
                ]]]]],
            ],
        ];

        if ($boots) {
            Fixture::bootsInto(
                $this->root,
                tca: ['tt_content' => [
                    'ctrl' => ['type' => 'CType'],
                    'columns' => [
                        'bodytext' => ['config' => ['type' => 'text']],
                        'pi_flexform' => ['config' => [
                            'type' => 'flex',
                            'ds' => 'FILE:EXT:acme/Configuration/FlexForms/Default.xml',
                        ]],
                    ],
                    'types' => [
                        'acme_teaser' => ['columnsOverrides' => ['pi_flexform' => ['config' => [
                            'ds' => 'FILE:EXT:acme/Configuration/FlexForms/Teaser.xml',
                        ]]]],
                        'acme_map' => ['columnsOverrides' => ['pi_flexform' => ['config' => [
                            'ds' => 'FILE:EXT:acme/Configuration/FlexForms/Map.xml',
                        ]]]],
                    ],
                ]],
                flexForm: $schemaFor === null ? $flexForm : $flexForm + ['schemaTables' => $schemaFor],
                flexFormTakesTheSchema: $takesTheSchema,
            );
        }

        Instance::discoverFrom($this->root);
        Typo3Cli::forget();
        Typo3Runtime::forget();
    }
}
