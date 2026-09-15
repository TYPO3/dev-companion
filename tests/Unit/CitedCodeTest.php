<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Contribution\CitedCode;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;

/**
 * The code a ten-year-old report names, and where the installed packages put
 * it.
 *
 * This holds both halves. The shapes a report writes a name in, which is the
 * side a stale issue is unreadable without. And the three verdicts a name comes
 * back with. A wrong "gone" is the one failure this may not have, since it
 * discards a valid candidate unread.
 */
#[Decision('D-ANS-122')]
final class CitedCodeTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Instance::ROOT_VARIABLE);
        Instance::discoverFrom(null);
    }

    /**
     * The forms the page of 25 stale Bugs read on 2026-08-27 writes a name in,
     * one case per form.
     *
     * @return array<string, array{0: string, 1: array{name: string, kind: string, method: string}}>
     */
    public static function namedCode(): array
    {
        return [
            'a namespace with the leading backslash' => [
                'It fails in \TYPO3\CMS\Core\Utility\GeneralUtility already.',
                ['name' => 'TYPO3\CMS\Core\Utility\GeneralUtility', 'kind' => CitedCode::QUALIFIED, 'method' => ''],
            ],
            'a namespace without it' => [
                'See TYPO3\CMS\Core\Utility\GeneralUtility for the cause.',
                ['name' => 'TYPO3\CMS\Core\Utility\GeneralUtility', 'kind' => CitedCode::QUALIFIED, 'method' => ''],
            ],
            'a namespace doubled inside a PHP string' => [
                '$class = \'\\\\TYPO3\\\\CMS\\\\Core\\\\Utility\\\\GeneralUtility\';',
                ['name' => 'TYPO3\CMS\Core\Utility\GeneralUtility', 'kind' => CitedCode::QUALIFIED, 'method' => ''],
            ],
            'a method on a qualified class' => [
                '\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable() is called twice.',
                [
                    'name' => 'TYPO3\CMS\Core\Utility\ExtensionManagementUtility',
                    'kind' => CitedCode::QUALIFIED,
                    'method' => 'makeCategorizable',
                ],
            ],
            'a method on a class named in a subject' => [
                'Triggering BackendUtility::setUpdateSignal has no effect',
                ['name' => 'BackendUtility', 'kind' => CitedCode::UNQUALIFIED, 'method' => 'setUpdateSignal'],
            ],
            'a method called on an instance' => [
                'The result of $result->forProperty() is wrong, Result->forProperty() returns an array.',
                ['name' => 'Result', 'kind' => CitedCode::UNQUALIFIED, 'method' => 'forProperty'],
            ],
            'a core file out of a pasted stack trace' => [
                '#7 /var/www/html/typo3/sysext/core/Classes/DataHandling/DataMapProcessor.php(631): call()',
                [
                    'name' => 'typo3/sysext/core/Classes/DataHandling/DataMapProcessor.php',
                    'kind' => CitedCode::FILE,
                    'method' => '',
                ],
            ],
            'a bare class name the report says is one' => [
                'The ObjectStorage class is empty afterwards.',
                ['name' => 'ObjectStorage', 'kind' => CitedCode::UNQUALIFIED, 'method' => ''],
            ],
            'a name the tracker wrote its code markup around' => [
                'The @PropertyMappingConfiguration@ is never asked.',
                ['name' => 'PropertyMappingConfiguration', 'kind' => CitedCode::UNQUALIFIED, 'method' => ''],
            ],
            'a name the tracker underlined' => [
                'It lands in _\TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRecordTypeValue_ instead.',
                [
                    'name' => 'TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRecordTypeValue',
                    'kind' => CitedCode::QUALIFIED,
                    'method' => '',
                ],
            ],
        ];
    }

    /** @param array{name: string, kind: string, method: string} $expected */
    #[DataProvider('namedCode')]
    #[Test]
    public function aNameIsFoundHoweverTheReportWroteIt(string $text, array $expected): void
    {
        Instance::discoverFrom(null);

        self::assertContains($expected + ['state' => CitedCode::UNPLACED, 'in' => []], CitedCode::in($text));
    }

    #[Test]
    public function aClassConstantAndTheClassKeywordAreTheClassAndNoMethod(): void
    {
        Instance::discoverFrom(null);

        // A search for either in the class file would report a name that is
        // there as gone. That is the one verdict this may not get wrong.
        self::assertSame(
            [['name' => 'ReferenceIndex', 'kind' => CitedCode::UNQUALIFIED, 'method' => '']],
            array_map(
                static fn(array $cited): array => ['name' => $cited['name'], 'kind' => $cited['kind'], 'method' => $cited['method']],
                CitedCode::in('ReferenceIndex::class is registered, ReferenceIndex::TABLE is not.'),
            ),
        );
    }

    #[Test]
    public function aReportNamingNoCodeCitesNothing(): void
    {
        Instance::discoverFrom(null);

        // Eleven of the 25 stale Bugs read on 2026-08-27 are about a TCA key, a
        // TypoScript path or a table column. A name as the answer to those
        // would be an extraction that invents its own evidence.
        self::assertSame([], CitedCode::in('The TypoScript setting config.no_cache is ignored on this page.'));
    }

    #[Test]
    public function aNameIsNotRepeatedBecauseTheReportShortenedItLater(): void
    {
        Instance::discoverFrom(null);

        self::assertSame(
            ['TYPO3\CMS\Extbase\Reflection\ObjectAccess'],
            array_column(
                CitedCode::in('\TYPO3\CMS\Extbase\Reflection\ObjectAccess is the class. ObjectAccess fails on bools.'),
                'name',
            ),
        );
    }

    #[Test]
    public function withNothingToPlaceAgainstEveryNameIsUnplaced(): void
    {
        Instance::discoverFrom(null);

        $cited = CitedCode::in('\TYPO3\CMS\Core\Database\DatabaseConnection is gone.');

        self::assertSame(CitedCode::UNPLACED, $cited[0]['state']);
        self::assertSame([], $cited[0]['in']);
    }

    #[Test]
    public function aQualifiedNameIsPlacedInThePackageItsNamespaceNames(): void
    {
        $root = $this->coreCheckoutShipping(['core' => [
            'Classes/Utility/GeneralUtility.php' => "<?php\nclass A { public static function makeInstance() {} }\n",
        ]]);
        Instance::discoverFrom($root);

        $cited = CitedCode::in('\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance() is called.');

        self::assertSame(CitedCode::SHIPPED, $cited[0]['state']);
        self::assertSame(
            [['extension' => 'core', 'path' => 'typo3/sysext/core/Classes/Utility/GeneralUtility.php']],
            $cited[0]['in'],
        );
    }

    #[Test]
    public function aClassItsOwnPackageNoLongerShipsIsNotShipped(): void
    {
        $root = $this->coreCheckoutShipping(['core' => ['Classes/Utility/GeneralUtility.php' => '']]);
        Instance::discoverFrom($root);

        // #72962 names DatabaseConnection and core removed it. That is the
        // verdict a triage is after, and it is still worded as not shipped.
        $cited = CitedCode::in('\TYPO3\CMS\Core\Database\DatabaseConnection::exec_SELECTquery() is used.');

        self::assertSame(CitedCode::NOT_SHIPPED, $cited[0]['state']);
        self::assertSame([], $cited[0]['in']);
    }

    #[Test]
    public function aNamespaceNoInstalledPackageOwnsIsUnplaced(): void
    {
        $root = $this->coreCheckoutShipping(['core' => ['Classes/Utility/GeneralUtility.php' => '']]);
        Instance::discoverFrom($root);

        // #78546 and #63810 both write a placeholder namespace. Reporting one
        // as gone would discard a candidate on evidence about nothing.
        $cited = CitedCode::in('Register \Vendor\Ext\Domain\Model\Blog as the model.');

        self::assertSame(CitedCode::UNPLACED, $cited[0]['state']);
    }

    #[Test]
    public function aMethodTheClassNoLongerDeclaresSaysWhereTheClassStands(): void
    {
        $root = $this->coreCheckoutShipping(['core' => [
            'Classes/Utility/ExtensionManagementUtility.php' => "<?php\nclass A { public static function addTca() {} }\n",
        ]]);
        Instance::discoverFrom($root);

        // #61923 is exactly this. makeCategorizable is gone on both branches
        // while the class it sat on stands. So the row that says the class is
        // there says nothing a triage can use.
        $cited = CitedCode::in('\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable() is called.');

        self::assertSame(CitedCode::NOT_SHIPPED, $cited[0]['state']);
        self::assertSame('core', $cited[0]['in'][0]['extension']);
    }

    #[Test]
    public function aCapitalisedWordNothingSaysIsCodeIsNotCited(): void
    {
        $root = $this->coreCheckoutShipping(['core' => ['Classes/Utility/GeneralUtility.php' => '']]);
        Instance::discoverFrom($root);

        // The RTE button list of one CKEditor configuration read on 2026-08-27
        // is eleven such words. An answer of "no installed package ships it" to
        // each is a page of verdicts about English.
        self::assertSame([], CitedCode::in("removeButtons:\n  - PasteFromWord\n  - HorizontalRule\n  - ShowBlocks"));
    }

    #[Test]
    public function aCapitalisedWordAnInstalledPackageShipsIsCited(): void
    {
        $root = $this->coreCheckoutShipping(['core' => ['Classes/Persistence/ObjectStorage.php' => '']]);
        Instance::discoverFrom($root);

        // The other half of the same rule: the package that ships one under
        // that name is the report's word, and #79571 gives no other handle.
        $cited = CitedCode::in('The ObjectStorage is empty afterwards.');

        self::assertSame('ObjectStorage', $cited[0]['name']);
        self::assertSame(CitedCode::SHIPPED, $cited[0]['state']);
    }

    #[Test]
    public function aBareNameMatchingTwoPackagesNamesBothOfThem(): void
    {
        $root = $this->coreCheckoutShipping([
            'core' => ['Classes/Mvc/ActionController.php' => ''],
            'backend' => ['Classes/Controller/ActionController.php' => ''],
        ]);
        Instance::discoverFrom($root);

        // A pick of one of them is where a verdict that looks right lands on
        // the wrong class. Three of the ten bare names read on 2026-08-27
        // matched two packages.
        $cited = CitedCode::in('The ActionController never reaches it.');

        self::assertSame(CitedCode::SHIPPED, $cited[0]['state']);
        self::assertSame(['backend', 'core'], array_column($cited[0]['in'], 'extension'));
    }

    #[Test]
    public function aBareNameNoInstalledPackageShipsIsNotShipped(): void
    {
        $root = $this->coreCheckoutShipping(['core' => ['Classes/Utility/GeneralUtility.php' => '']]);
        Instance::discoverFrom($root);

        // Core removed DatabaseConnection, and a name of an extension the
        // caller never installed reads the same from here. #72962 is where the
        // words come from.
        $cited = CitedCode::in('DB transactions via the DatabaseConnection class are instantiated per query.');

        self::assertSame(CitedCode::NOT_SHIPPED, $cited[0]['state']);
    }

    #[Test]
    public function aCoreFilePathIsPlacedInThePackageItNames(): void
    {
        $root = $this->coreCheckoutShipping(['core' => ['Classes/DataHandling/DataMapProcessor.php' => '']]);
        Instance::discoverFrom($root);

        $cited = CitedCode::in('#7 /var/www/typo3/sysext/core/Classes/DataHandling/DataMapProcessor.php(631): x()');

        self::assertSame(CitedCode::FILE, $cited[0]['kind']);
        self::assertSame(CitedCode::SHIPPED, $cited[0]['state']);
        self::assertSame('typo3/sysext/core/Classes/DataHandling/DataMapProcessor.php', $cited[0]['in'][0]['path']);
    }

    /**
     * A core checkout whose packages ship the files named here.
     *
     * @param array<string, array<string, string>> $files extension key to path below the package and its content
     */
    private function coreCheckoutShipping(array $files): string
    {
        $root = $this->coreCheckout();
        foreach ($files as $key => $shipped) {
            foreach ($shipped as $path => $content) {
                $file = $root . '/typo3/sysext/' . $key . '/' . $path;
                if (!is_dir(dirname($file))) {
                    mkdir(dirname($file), 0o777, true);
                }
                file_put_contents($file, $content === '' ? "<?php\n" : $content);
            }
        }

        return $root;
    }
}
