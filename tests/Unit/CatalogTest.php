<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Knowledge\Catalog\Components;
use TYPO3\DevCompanion\Knowledge\Catalog\DemoMarkup;
use TYPO3\DevCompanion\Knowledge\Catalog\Meta;
use TYPO3\DevCompanion\Knowledge\Catalog\References;
use TYPO3\DevCompanion\Knowledge\Catalog\SystemExtensions;
use TYPO3\DevCompanion\Knowledge\Catalog\TranslationDomain;
use TYPO3\DevCompanion\Knowledge\Hints;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\Catalogs;
use TYPO3\DevCompanion\Upkeep\Command\VersionCheck;

final class CatalogTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        Instance::discoverFrom(null);
    }

    #[Requirement('R-ANS-003')]
    #[Requirement('R-AUD-004')]
    #[Test]
    public function theCatalogSaysHowItRelatesToTheInstallationBeingRead(): void
    {
        // Both numbers were known and never contrasted, so v15 markup and a
        // v15 custom-property contract were handed to a v13 backend as fact.
        Instance::discoverFrom($this->composerProject('vendor', '13.4.33'));

        $result = Registry::call('typo3_snapshot_scope', []);
        self::assertSame('13.4.33', $result->data['catalog']['installedVersion']);
        self::assertStringContainsString('13.4.33', (string) $result->data['catalog']['skew']);
        self::assertStringContainsString('13.4.33', $result->text);

        // A component answer carries the pin, so it carries the gap too.
        self::assertStringContainsString('13.4.33', Registry::call('typo3_component_lookup', ['query' => 'badge'])->text);
    }

    #[Requirement('R-AUD-004')]
    #[Test]
    public function anInstallationWithoutDomainsIsGivenTheFileReference(): void
    {
        // The domain string is syntactically fine on a version that cannot
        // resolve it, and every label written with it renders empty. This is
        // the one answer that is withheld rather than qualified.
        Instance::discoverFrom($this->composerProject('vendor', '13.4.33'));

        $result = Registry::call('typo3_translation_domain_lookup', [
            'path' => 'EXT:my_ext/Resources/Private/Language/locallang_db.xlf',
        ]);

        self::assertNull($result->data['domain']);
        self::assertSame('my_ext.db', $result->data['domainOnNewerVersions']);
        self::assertStringContainsString('LLL:EXT:my_ext/Resources/Private/Language/locallang_db.xlf', $result->text);
    }

    #[Test]
    public function anInstallationThatResolvesDomainsIsGivenTheDomain(): void
    {
        Instance::discoverFrom($this->composerProject('vendor', '14.3.0'));

        $result = Registry::call('typo3_translation_domain_lookup', [
            'path' => 'EXT:my_ext/Resources/Private/Language/locallang_db.xlf',
        ]);

        self::assertSame('my_ext.db', $result->data['domain']);
        self::assertNull($result->data['domainOnNewerVersions']);
    }

    /**
     * `D-DIS-004`'s second **Wrong if**: a caller working on a version other
     * than the installation the server found. The answer here is one string
     * that either works on a version or silently renders nothing, so which
     * version it is composed for has to be the caller's to state — a backport
     * branch read from a 14 installation gets the domain form and every label
     * written with it renders empty.
     */
    #[Test]
    public function theStatedVersionDecidesTheDomain(): void
    {
        $path = 'EXT:my_ext/Resources/Private/Language/locallang_db.xlf';

        Instance::discoverFrom($this->composerProject('vendor', '13.4.33'));
        $onFourteen = Registry::call('typo3_translation_domain_lookup', ['path' => $path, 'targetVersion' => '14']);

        self::assertSame(14, $onFourteen->data['targetVersion']);
        self::assertSame('my_ext.db', $onFourteen->data['domain']);

        Instance::discoverFrom($this->composerProject('vendor', '14.3.0'));
        $onThirteen = Registry::call('typo3_translation_domain_lookup', ['path' => $path, 'targetVersion' => '13.4']);

        self::assertSame(13, $onThirteen->data['targetVersion']);
        self::assertNull($onThirteen->data['domain']);
        self::assertSame('my_ext.db', $onThirteen->data['domainOnNewerVersions']);
        self::assertStringContainsString('TYPO3 13, which you asked about', $onThirteen->text);
    }

    #[Test]
    public function nothingIsSaidAboutASkewThatIsNotThere(): void
    {
        Instance::discoverFrom($this->composerProject('vendor', Meta::read()['source']['version'] . '.0'));

        $result = Registry::call('typo3_snapshot_scope', []);
        self::assertNull($result->data['catalog']['skew']);
    }

    #[Requirement('R-ANS-003')]
    #[Test]
    public function theInstalledComponentContractWinsOverTheBundledSnapshot(): void
    {
        $root = $this->coreCheckout('14.3.5');
        $backendCss = $root . '/typo3/sysext/backend/Resources/Public/Css';
        mkdir($backendCss, 0o777, true);
        file_put_contents(
            $backendCss . '/backend.css',
            '.badge { --typo3-badge-bg: green; } .badge-success {} .badge-installed {} .panel {}',
        );

        $styleguide = $root . '/typo3/sysext/styleguide';
        mkdir($styleguide . '/Resources/Private/Templates/Backend/Components', 0o777, true);
        file_put_contents($styleguide . '/composer.json', json_encode([
            'name' => 'typo3/cms-styleguide',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'styleguide']],
        ], JSON_THROW_ON_ERROR));
        file_put_contents(
            $styleguide . '/Resources/Private/Templates/Backend/Components/Badges.fluid.html',
            '<sg:example><span class="badge badge-installed">Installed</span></sg:example>',
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_component_lookup', ['query' => 'badge-installed']);
        $badge = $result->data['components'][0];

        self::assertSame('installation', $result->data['componentSource']);
        self::assertSame('14.3.5', $badge['contractVersion']);
        self::assertSame('14.3.5', $badge['describesVersion']);
        self::assertSame('installation', $badge['markupSource']);
        self::assertContains('badge-installed', $badge['classes']);
        self::assertSame(['badge-success'], $badge['variants']);
        self::assertSame(['--typo3-badge-bg'], $badge['customProperties']);
        self::assertSame('<span class="badge badge-installed">Installed</span>', $badge['markup']);
        self::assertContains(
            'EXT:styleguide/Resources/Private/Templates/Backend/Components/Badges.fluid.html',
            $badge['sourceFiles'],
        );
        self::assertNull($result->data['catalog']['skew']);
        self::assertStringContainsString('Other installed classes: badge-installed', $result->text);
        self::assertStringContainsString('installed TYPO3 14.3.5 packages', $result->text);

        $fallback = Registry::call('typo3_component_lookup', ['query' => 'panel']);
        self::assertSame('installation', $fallback->data['componentSource']);
        self::assertSame('14.3.5', $fallback->data['components'][0]['contractVersion']);
        self::assertSame('catalog', $fallback->data['components'][0]['markupSource']);
        self::assertSame(Meta::read()['source']['version'], $fallback->data['components'][0]['describesVersion']);
        self::assertStringContainsString('bundled TYPO3 15.0 fallback', $fallback->text);

        $scope = Registry::call('typo3_snapshot_scope', []);
        self::assertSame('installation', $scope->data['componentSource']);
        self::assertStringContainsString('Installed component contract', $scope->text);

        $broad = Registry::call('typo3_component_lookup', [
            'query' => 'backend component markup for the installed TYPO3 version',
        ]);
        self::assertGreaterThan(0, $broad->data['matchCount']);
        self::assertSame('installation', $broad->data['componentSource']);
    }

    #[Requirement('R-ANS-003')]
    #[Test]
    public function anInstalledContractDoesNotAnswerForAnotherTargetMajor(): void
    {
        $root = $this->coreCheckout('14.3.5');
        $backendCss = $root . '/typo3/sysext/backend/Resources/Public/Css';
        mkdir($backendCss, 0o777, true);
        file_put_contents($backendCss . '/backend.css', '.badge {} .badge-installed {}');
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_component_lookup', [
            'query' => 'badge',
            'targetVersion' => '13.4',
        ]);

        self::assertSame('catalog', $result->data['componentSource']);
        self::assertSame(Meta::read()['source']['version'], $result->data['components'][0]['contractVersion']);
        self::assertNotContains('badge-installed', $result->data['components'][0]['classes']);
    }

    #[Test]
    public function aComponentQueryIsAnsweredByTheComponentItself(): void
    {
        $components = Components::find('badge');

        self::assertSame('badge', $components[0]['name']);
        self::assertContains('name', $components[0]['matchedIn']);
        self::assertNotSame('', $components[0]['markup']);
    }

    #[Test]
    public function aComponentMatchedByItsOwnNameOutranksOneInAClassList(): void
    {
        $components = Components::find('card');

        self::assertSame('card', $components[0]['name']);
        foreach ($components as $component) {
            self::assertNotSame(['sub-component classes'], $component['matchedIn']);
        }
    }

    #[Test]
    public function anEmptyQueryListsTheWholeCatalogAlphabetically(): void
    {
        $names = array_column(Components::find(null), 'name');

        self::assertSame(count(Components::load()), count($names));
        $sorted = $names;
        sort($sorted);
        self::assertSame($sorted, $names);
    }

    #[Test]
    public function anUnknownComponentMatchesNothing(): void
    {
        self::assertSame([], Components::find('quantumflux'));
    }

    #[Requirement('R-ANS-010')]
    #[Test]
    #[DataProvider('theQueriesThatUsedToReturnSomethingElseFirst')]
    public function aComponentNamedOutrightWinsOverOneThatMerelyMentionsIt(string $query): void
    {
        self::assertSame(
            str_replace(' ', '-', $query),
            Components::find($query)[0]['name'] ?? null,
        );
    }

    /** @return array<string, array{0: string}> */
    public static function theQueriesThatUsedToReturnSomethingElseFirst(): array
    {
        return [
            // Two more cases stood here, `dropzone` and `note`. Both entries
            // came out with `D-CAT-009` — the styleguide demonstrates neither —
            // and what they used to hold is asserted below as the miss it is
            // now.
            'status indicator, which returned Badge' => ['status indicator'],
        ];
    }

    /**
     * A surface the styleguide does not demonstrate is one the core keeps to
     * itself, and the catalog answers nothing about it rather than answering
     * with a warning — a marking that says "not public" and hands the class
     * over anyway is read as the class.
     */
    #[Decision('D-CAT-009')]
    #[Test]
    #[DataProvider('theComponentsTheStyleguideDoesNotDemonstrate')]
    public function aComponentNoStyleguidePageDemonstratesIsNotAnswered(string $name): void
    {
        self::assertNotContains($name, array_column(Components::find($name), 'name'));
    }

    /** @return array<string, array{0: string}> */
    public static function theComponentsTheStyleguideDoesNotDemonstrate(): array
    {
        return [
            'dropzone' => ['dropzone'],
            'module, the chrome around a backend module' => ['module'],
            'note' => ['note'],
            'popover' => ['popover'],
            'recordsearchbox' => ['recordsearchbox'],
        ];
    }

    /**
     * An entry is in the catalog because the styleguide lists it, so it may not
     * answer for a major the styleguide had not listed it on yet — that would
     * be offering as public what nothing had said was — `D-CAT-009`.
     *
     * The floor is the oldest major any styleguide ships on. Below it the
     * listing cannot be read at all, so what applies there is the selection
     * made above it, and a class that exists on the older major is still
     * answered: the caller borrowing it is the one this catalog was repaired
     * for.
     */
    #[Decision('D-CAT-009')]
    #[Test]
    public function noEntryAnswersForAMajorItsStyleguideDidNotListItOn(): void
    {
        $listed = [];
        foreach (Catalogs::read('component/styleguide') as $action) {
            $listed[$action['component']] = (int) $action['since'];
        }
        self::assertNotSame([], $listed, 'no styleguide listing was derived at all');
        $floor = PHP_INT_MAX;
        foreach ($listed as $since) {
            $floor = min($floor, $since);
        }

        foreach (Catalogs::read('component/entries') as $entry) {
            self::assertNotSame([], $entry['styleguideActions'], $entry['name'] . ' names no styleguide action');
            $earliest = PHP_INT_MAX;
            foreach ($entry['styleguideActions'] as $action) {
                $earliest = min($earliest, $listed[$action] ?? PHP_INT_MAX);
            }
            $answers = max((int) ($entry['since'] ?? 0), $floor);
            self::assertGreaterThanOrEqual(
                $earliest,
                $answers,
                $entry['name'] . ' answers from v' . $answers . ' and is listed from v' . $earliest,
            );
        }
    }

    /**
     * The styleguide renamed its templates, so a branch older than the rename
     * spells a demo `.html` where the entry records `.fluid.html`. Reading only
     * the recorded spelling digested no demo at all on that major, and the
     * check read as covering four checkouts while covering two.
     */
    #[Decision('D-CAT-001')]
    #[Test]
    public function aDemoIsFoundUnderEitherSpelling(): void
    {
        self::assertSame(
            ['a/Avatar.fluid.html', 'a/Avatar.html'],
            DemoMarkup::spellings('a/Avatar.fluid.html'),
        );
        self::assertSame(['a/Avatar.html'], DemoMarkup::spellings('a/Avatar.html'));
    }

    /**
     * And every entry records what it read on every covered major that has the
     * demo, rather than on the newest two.
     */
    #[Decision('D-CAT-001')]
    #[Test]
    public function everyDemoIsDigestedOnEveryMajorThatCarriesIt(): void
    {
        $onOlderMajors = 0;
        foreach (Catalogs::read('component/entries') as $entry) {
            foreach (array_keys($entry['markupDigests'] ?? []) as $major) {
                if ((int) $major < 14) {
                    ++$onOlderMajors;
                }
            }
        }
        self::assertGreaterThan(0, $onOlderMajors, 'no entry records what a demo said before the rename');
    }

    /**
     * Where the backend declares an element, that is the answer: an element
     * carries its own position and cannot be attached to the wrong node, which
     * is the whole of what went wrong with a borrowed class — `D-CAT-009`.
     */
    #[Decision('D-CAT-009')]
    #[Test]
    public function anElementTheQueryNamesIsOfferedAsTheWayIn(): void
    {
        $result = Registry::call('typo3_component_lookup', ['query' => 'combobox', 'targetVersion' => '14.3']);

        self::assertContains('typo3-backend-combobox', array_column($result->data['elements'], 'tag'));
        self::assertStringContainsString('declares a custom element for this', $result->text);
        self::assertStringContainsString('combobox-element.ts', $result->text);
    }

    /**
     * The core declares many more elements than it demonstrates, and the ones
     * no demo writes are the backend's own. Handing one over is the mistake the
     * listing exists to prevent.
     */
    #[Decision('D-CAT-009')]
    #[Test]
    public function anElementNoDemoWritesIsNotOffered(): void
    {
        $result = Registry::call('typo3_component_lookup', ['query' => 'grid editor', 'targetVersion' => '14.3']);

        self::assertSame([], $result->data['elements']);
        self::assertStringNotContainsString('typo3-backend-grid-editor', $result->text);
    }

    /** An element that arrived late is not offered on a version that does not have it. */
    #[Decision('D-CAT-009')]
    #[Test]
    public function anElementIsNotOfferedBeforeItsDemoWroteIt(): void
    {
        $later = Registry::call('typo3_component_lookup', ['query' => 'combobox', 'targetVersion' => '13.4']);

        self::assertSame([], $later->data['elements']);
    }

    /**
     * A class belongs to one component, because what is derived about it is
     * derived relative to that component's root: the same name under two
     * entries would be placed twice and differently, and the answer keyed by
     * the class would carry whichever reading came last — `D-CAT-008`.
     */
    #[Decision('D-CAT-008')]
    #[Test]
    public function noClassIsListedByTwoEntries(): void
    {
        $owners = [];
        foreach (Catalogs::read('component/entries') as $entry) {
            $classes = array_merge($entry['variants'], $entry['modifiers'], $entry['subComponents']);
            foreach ($classes as $class) {
                $owners[$class][] = $entry['name'];
            }
        }
        foreach ($owners as $class => $entries) {
            self::assertCount(1, $entries, $class . ' is listed by ' . implode(' and ', $entries));
        }
    }

    /**
     * Every entry names the styleguide actions that demonstrate it, because
     * that listing is why it is in the catalog at all — `D-CAT-009`.
     */
    #[Decision('D-CAT-009')]
    #[Test]
    public function everyEntryNamesTheActionsThatDemonstrateIt(): void
    {
        $listed = array_column(Catalogs::read('component/styleguide'), 'component');
        foreach (Catalogs::read('component/entries') as $entry) {
            $actions = $entry['styleguideActions'] ?? [];
            self::assertNotSame([], $actions, $entry['name'] . ' names no styleguide action');
            foreach ($actions as $action) {
                self::assertContains($action, $listed, $entry['name'] . ' names ' . $action . ', which no styleguide lists');
            }
        }
    }

    /**
     * The classes are what a component is styled by and the data attributes are
     * what it is driven by, and only the first was answered.
     *
     * A session wrote `data-bs-content` on a modal that reads `data-content`
     * and shipped a `data-on-change` that one extension's own module
     * implements. Both failed silently in a browser — `D-ANS-139`.
     *
     * The module is laid out here rather than read out of `.checkouts/`, which
     * is gitignored and on no machine but the one that created it — `R-COD-003`.
     * That the core's own modal reads these two is the decision's evidence;
     * what is held here is the derivation, on both the module and its absence.
     */
    #[Decision('D-ANS-139')]
    #[Test]
    public function aComponentCarriesTheAttributesItsOwnModuleReads(): void
    {
        $root = $this->coreCheckout('14.3.5');
        $backend = $root . '/typo3/sysext/backend/Resources/Public';
        mkdir($backend . '/Css', 0o777, true);
        mkdir($backend . '/JavaScript', 0o777, true);
        file_put_contents($backend . '/Css/backend.css', '.modal {} .badge {}');
        file_put_contents(
            $backend . '/JavaScript/modal.js',
            'const c = element.dataset.content; const t = element.dataset.buttonOkText;',
        );
        Instance::discoverFrom($root);

        $modal = Registry::call('typo3_component_lookup', ['query' => 'modal', 'targetVersion' => '14.3']);
        $entry = $modal->data['components'][0];

        self::assertSame('modal', $entry['name']);
        self::assertContains('data-content', $entry['dataAttributes']);
        // The DOM maps dataset.buttonOkText to this, and the attribute is what
        // a template writes.
        self::assertContains('data-button-ok-text', $entry['dataAttributes']);
        self::assertStringContainsString('data-content', $modal->text);

        // A component no module drives carries none rather than the module's
        // attributes under another name.
        $badge = Registry::call('typo3_component_lookup', ['query' => 'badge', 'targetVersion' => '14.3']);
        self::assertSame([], $badge->data['components'][0]['dataAttributes']);
    }

    #[Requirement('R-ANS-010')]
    #[Test]
    public function aQueryTheCatalogWasNotWrittenForIsAMiss(): void
    {
        // Three components came back for this, each on one word out of five:
        // Dropdown and Form Inputs through a keyword, Card because its summary
        // is written in the words any question about content is written in.
        self::assertSame([], Components::find('content element preview heading text'));

        // What the query names is still the answer, however the rest of the
        // sentence reads — the coverage rule is for what nobody named. The
        // module chrome was catalogued after this case was written
        // (`D-CAT-004`) and came out again with `D-CAT-009`, because the
        // styleguide does not demonstrate it, so the sentence names one
        // component now.
        $named = array_column(Components::find('add a badge to the module header'), 'name');
        self::assertContains('badge', $named);
        self::assertNotContains('module', $named);
        // And a class is a way in of its own: it is what the miss suggests.
        self::assertNotSame([], Components::find('input-group'));
    }

    /**
     * A miss that says "not in this snapshot" says how to check the snapshot in
     * the same breath, rather than naming a second call for it — `D-CAT-010`.
     * The catalog scope keeps the questions no answer carries: what each
     * catalog holds, how many entries it has, and the system extension catalog,
     * which no component answer reports on at all.
     */
    #[Decision('D-CAT-010')]
    #[Test]
    public function aCatalogMissCarriesItsOwnRecheck(): void
    {
        $result = Registry::call('typo3_component_lookup', ['query' => 'a component nothing here is called']);

        self::assertSame(0, $result->data['matchCount']);
        self::assertNotSame('', $result->data['catalog']['verifyCommand']);
        self::assertStringNotContainsString(
            'typo3_snapshot_scope',
            $result->text,
            'the miss sends the caller on a round trip for what it carries',
        );

        // What the scope answers that no component answer does. The tool was
        // proposed for retirement on the reading that it adds only a command
        // and a count, which measured it against the component catalog alone.
        $scope = Registry::call('typo3_snapshot_scope', []);
        self::assertArrayHasKey('systemExtensions', $scope->data['scope']);
        self::assertArrayHasKey('systemExtensions', $scope->data['counts']);
    }

    #[Requirement('R-ANS-003')]
    #[Test]
    public function aStatedVersionSaysWhatItDidToTheAnswer(): void
    {
        $result = Registry::call('typo3_component_lookup', ['query' => 'badge', 'targetVersion' => '14.3']);

        self::assertStringContainsString('Answered for TYPO3 v14', $result->text);
        self::assertStringContainsString('not which versions an entry holds on', $result->text);
        self::assertStringNotContainsString(
            'Answered for TYPO3',
            Registry::call('typo3_component_lookup', ['query' => 'badge'])->text,
            'nobody stated a version, so nothing is claimed about one',
        );
    }

    #[Requirement('R-AUD-004')]
    #[Test]
    public function aComponentNotVerifiedOnTheTargetIsDeclined(): void
    {
        // The skew sentence named the difference without acting on it. Markup
        // taken from one revision either holds on the stated version or it does
        // not, and the answer for "does not" is to decline it.
        $result = Registry::call('typo3_component_lookup', ['query' => 'status indicator', 'targetVersion' => '13.4']);

        self::assertNotContains(
            'status-indicator',
            array_column($result->data['components'], 'name'),
            'a v14 custom-property contract is not handed to a 13.4 caller',
        );
        self::assertSame(['status-indicator'], array_column($result->data['withheld'], 'name'));
        self::assertSame(13, $result->data['targetVersion']);

        // Silently dropping it would read as "this component does not exist",
        // so the withholding names itself and what to check instead.
        self::assertStringContainsString('Withheld for TYPO3 v13', $result->text);
        self::assertStringContainsString('_status-indicator.scss', $result->text);
        self::assertStringContainsString('13.4 branch', $result->text);
    }

    #[Requirement('R-AUD-004')]
    #[Test]
    public function aComponentVerifiedOnTheTargetCarriesItsRange(): void
    {
        $result = Registry::call('typo3_component_lookup', ['query' => 'status indicator', 'targetVersion' => '14.3']);

        $described = $result->data['components'][0];
        self::assertSame('status-indicator', $described['name']);
        self::assertSame(14, $described['since']);
        self::assertSame('TYPO3 v14 and newer', $described['verifiedOn']);
        self::assertSame([], $result->data['withheld']);
        self::assertStringContainsString('Verified on: TYPO3 v14 and newer', $result->text);
    }

    #[Requirement('R-AUD-004')]
    #[Test]
    public function withoutATargetEachEntryCarriesItsRange(): void
    {
        // Nobody said which version this is for, so nothing is withheld and the
        // caller is told the range instead — the same rule the hints follow.
        $result = Registry::call('typo3_component_lookup', ['query' => 'status indicator']);

        self::assertNull($result->data['targetVersion']);
        self::assertSame('status-indicator', $result->data['components'][0]['name']);
        self::assertSame([], $result->data['withheld']);
    }

    #[Requirement('R-AUD-004')]
    #[Test]
    public function theCatalogSaysHowMuchOfItWasVerifiedOnAStatedVersion(): void
    {
        $result = Registry::call('typo3_snapshot_scope', ['targetVersion' => '14']);

        self::assertSame(14, $result->data['targetVersion']);
        self::assertSame(count(Components::load()), $result->data['verifiedCount']);
        self::assertSame([], $result->data['withheld']);

        // The custom-property contract the catalog describes arrived after
        // 12.4, so most of it is not verified there and the scope says so.
        $onTwelve = Registry::call('typo3_snapshot_scope', ['targetVersion' => '12.4']);
        self::assertLessThan(count(Components::load()), $onTwelve->data['verifiedCount']);
        self::assertStringContainsString('Withheld for TYPO3 v12', $onTwelve->text);
    }

    #[Requirement('R-ANS-003')]
    #[Test]
    public function theSnapshotScopeSeparatesEntryValidityFromItsSourceCheckout(): void
    {
        $result = Registry::call('typo3_snapshot_scope', ['targetVersion' => '14.3']);

        self::assertStringContainsString('For TYPO3 v14', $result->text);
        self::assertStringContainsString('Each component entry owns this validity range', $result->text);
        self::assertStringContainsString('Checkout branch: main', $result->text);
        self::assertLessThan(
            strpos($result->text, 'Checkout branch: main'),
            strpos($result->text, 'For TYPO3 v14'),
            'the requested version is stated before the provenance version',
        );
    }

    #[Test]
    public function aComponentCarriesEverySassFileItSpans(): void
    {
        $input = array_values(array_filter(Components::load(), static fn(array $c): bool => $c['name'] === 'input'))[0];

        // The form controls are one component split across four files; naming
        // only the first made the rest look like they were not part of it.
        self::assertContains('Build/Sources/Sass/component/forms/_form-text.scss', $input['sassPaths']);
        self::assertSame($input['sassPaths'][0], $input['sassPath'], 'sassPath stays the primary one');
    }

    #[Requirement('R-AUD-004')]
    #[Test]
    public function everyRecordedBindingNamesACoveredVersion(): void
    {
        // A binding outside the covered range withholds an entry from every
        // caller or from none, and both are silent — bin/cli components:check is what
        // holds the numbers to the checkouts, this holds them to versions.json.
        $majors = Versions::majors();
        foreach (Components::load() as $component) {
            foreach (['since', 'until', 'classesSince', 'classesUntil'] as $bound) {
                if ($component[$bound] !== null) {
                    self::assertContains($component[$bound], $majors, $component['name'] . ' is bound to a version this knowledge base does not cover');
                }
            }
        }
    }

    #[Decision('D-CAT-006')]
    #[Test]
    public function theClassListReachesAtLeastAsFarBackAsTheEntryItBelongsTo(): void
    {
        // The class list is what the entry names minus its custom properties,
        // so it cannot start later than the entry does. A recorded range that
        // says otherwise is a derivation nobody re-ran, and it would withhold a
        // class on a version the entry itself is handed over on — `D-CAT-006`.
        foreach (Components::load() as $component) {
            if ($component['classesSince'] === null || $component['since'] === null) {
                continue;
            }
            self::assertLessThanOrEqual(
                $component['since'],
                $component['classesSince'],
                $component['name'] . ' binds its classes later than the whole entry',
            );
        }
    }

    #[Decision('D-CAT-006')]
    #[Test]
    public function aClassIsAnsweredOnAVersionItsOwnEntryIsWithheldOn(): void
    {
        // What `feedback/2026-08-19-090231` shipped unverified: a backend class
        // borrowed by an extension's asset build. The entry is withheld because
        // one of its eleven custom properties arrived later, and the caller was
        // asking about the class rather than about the component — `D-CAT-006`.
        $result = Registry::call('typo3_component_lookup', ['query' => 'table-fit', 'targetVersion' => '13.4']);

        self::assertSame(0, $result->data['matchCount'], 'the component itself is still not handed over');
        self::assertSame(['table'], array_column($result->data['withheld'], 'name'));
        self::assertSame(['table-fit'], array_column($result->data['coveredClasses'], 'class'));
        self::assertSame('TYPO3 v12 and newer', $result->data['coveredClasses'][0]['verifiedOn']);
        self::assertStringContainsString('Still answered for TYPO3 v13, one class at a time', $result->text);

        // And where it goes, which the name alone never said: the session that
        // reported this attached the wrapper to the table — `D-CAT-008`.
        self::assertSame('around', $result->data['coveredClasses'][0]['position']);
        self::assertStringContainsString('written on the element wrapping the table', $result->text);

        // And the two cannot be read as one another: what comes back is the name
        // and where the core writes it, never something to paste.
        self::assertStringNotContainsString('--typo3-table', $result->text);
        self::assertStringNotContainsString('<table', $result->text);
    }

    #[Decision('D-CAT-006')]
    #[Test]
    public function aQueryThatNamesNoClassOfAWithheldEntryIsAnsweredWithNothing(): void
    {
        // The whole class list is not the answer to a question about one class:
        // handing it over would be the entry again, minus what withheld it —
        // `D-CAT-006`.
        $topic = Registry::call('typo3_component_lookup', ['query' => 'table', 'targetVersion' => '13.4']);
        self::assertSame(['table'], array_column($topic->data['coveredClasses'], 'class'), 'only the root class was named');

        $unstated = Registry::call('typo3_component_lookup', ['query' => 'table-fit']);
        self::assertSame([], $unstated->data['coveredClasses'], 'nothing is withheld, so the entry answers');
    }

    #[Decision('D-CAT-008')]
    #[Test]
    public function aClassIsAnsweredOnAMajorItsEntrysListDoesNotReach(): void
    {
        // `table-fit` is written on 12.4 and four of the table entry's other
        // classes are not, so the list binds at 13 and the aggregate range said
        // nothing to the caller who asked on 12 — which is the caller
        // `feedback/2026-08-19-090231` was. The range is the class's own now.
        $result = Registry::call('typo3_component_lookup', ['query' => 'table-fit', 'targetVersion' => '12.4']);

        self::assertSame(['table-fit'], array_column($result->data['coveredClasses'], 'class'));
        self::assertSame('around', $result->data['coveredClasses'][0]['position']);
        self::assertSame('TYPO3 v12 and newer', $result->data['coveredClasses'][0]['verifiedOn']);
    }

    #[Decision('D-CAT-008')]
    #[Test]
    public function aWrapperIsNotListedAmongTheModifiersOfWhatItWraps(): void
    {
        // `table-fit` was curated into `modifiers` beside `table-striped`, and
        // one goes on the table while the other goes around it. The lists keep
        // every class the derivation has no opinion on.
        $result = Registry::call('typo3_component_lookup', ['query' => 'table', 'targetVersion' => '14.3']);
        $table = $result->data['components'][0];

        self::assertSame(['table-fit'], $table['wrapping']);
        self::assertNotContains('table-fit', $table['modifiers']);
        self::assertContains('table-striped', $table['modifiers']);
        self::assertStringContainsString('Wrapping the component: table-fit', $result->text);
    }

    #[Decision('D-CAT-008')]
    #[Test]
    public function whatIsStyledWithinAClassIsNotWhatItRequires(): void
    {
        // A progress bar is styled below `.table-fit` from v14 and belongs
        // there by nothing, so the answer says which of the two it is.
        $result = Registry::call('typo3_component_lookup', ['query' => 'table-fit', 'targetVersion' => '14.3']);
        $covered = $result->data['coveredClasses'];

        if ($covered !== []) {
            self::assertContains('.table', $covered[0]['stylesWithin']);
            self::assertStringContainsString('what it may hold and not what it needs', $result->text);

            return;
        }
        self::assertSame(1, $result->data['matchCount'], 'the entry answers whole on the version it binds to');
    }

    #[Test]
    public function everyEntryWithADemoRecordsWhatItRead(): void
    {
        // The binding is derived from names, so a demo rewritten around the same
        // classes reads as unchanged — bin/cli components:check compares these
        // digests against the checkouts, this holds them to versions.json and to
        // the entries that have a demo to digest at all.
        $majors = Versions::majors();
        foreach (Catalogs::read('component/entries') as $entry) {
            $digests = $entry['markupDigests'] ?? [];
            if (($entry['demoPath'] ?? '') === '') {
                self::assertSame([], $digests, $entry['name'] . ' digests a demo it does not name');
                continue;
            }

            self::assertNotSame([], $digests, $entry['name'] . ' names a demo and records nothing it read there');
            foreach ($digests as $major => $digest) {
                self::assertContains((int) $major, $majors, $entry['name'] . ' digests a version this knowledge base does not cover');
                self::assertMatchesRegularExpression('/^[0-9a-f]{12}$/', (string) $digest, $entry['name'] . ' records something no digest produced');
            }
        }
    }

    #[Test]
    public function aDemoIsDigestedByWhatItSaysAboutTheComponent(): void
    {
        // What the digest has to notice: the same class names, arranged
        // differently. The names alone say nothing about the second template.
        $one = '<sg:example><span class="badge badge-default">Badge</span></sg:example>';
        $two = '<sg:example><div><span class="badge badge-default">Badge</span></div></sg:example>';

        self::assertNotSame(DemoMarkup::examples($one, 'badge'), DemoMarkup::examples($two, 'badge'));

        // And what it may not notice: a demo that renders the component through
        // a ViewHelper names it nowhere, so nothing there holds its markup.
        $viewHelper = '<sg:example><f:flashMessages queueIdentifier="styleguide.default" /></sg:example>';
        self::assertFalse(DemoMarkup::carries(implode("\n", DemoMarkup::examples($viewHelper, 'alert')), 'alert'));
        self::assertTrue(DemoMarkup::carries(implode("\n", DemoMarkup::examples($one, 'badge')), 'badge'));
    }

    #[Test]
    public function aCuratedSelectorDecidesWhichExampleIsTheComponent(): void
    {
        // The failure D-CAT-003 named as what would show it wrong: a demo page
        // that opens with scaffolding. Both examples below carry `card`, so the
        // root class cannot tell them apart — the first is a settings form
        // built out of a card, the second is the card. Handing over the first
        // is handing over the page, and no more permissive extractor fixes
        // that, because there is nothing about it to be permissive towards.
        $template = <<<'HTML'
            <sg:example>
                <form>
                    <div class="card card-size-large">
                        <div class="card-header"><h4>Headline</h4></div>
                        <div class="card-body"><input type="checkbox" class="form-check-input"></div>
                    </div>
                </form>
            </sg:example>
            <sg:example>
                <div class="card card-size-medium">
                    <div class="card-header"><h3 class="card-title">Headline</h3></div>
                    <div class="card-body"><p class="card-text">Card body text.</p></div>
                </div>
            </sg:example>
            HTML;

        $unselected = DemoMarkup::examples($template, 'card');
        self::assertStringContainsString('<form>', $unselected[0], 'the first match is the scaffolding');

        $selected = DemoMarkup::examples($template, 'card', 'card-title');
        self::assertStringContainsString('card-title', $selected[0]);
        self::assertStringNotContainsString('<form>', implode("\n", $selected), 'the selected example, not the first one');

        // Narrowing only. A selector nothing carries derives nothing, so the
        // caller keeps the curated markup and labels it a fallback rather than
        // silently falling back to the scaffolding the selector was written
        // against.
        self::assertSame([], DemoMarkup::examples($template, 'card', 'card-image-badge'));
    }

    #[Test]
    public function theCardEntrySelectsPastItsDemosOpeningForm(): void
    {
        // Read on 2026-08-02 out of .checkouts/14.3 and .checkouts/main:
        // Cards.fluid.html has three examples carrying `card`, and the first is
        // the `<form>` of switches. The entry's own markup spells card-title,
        // which is the sub-component that example does not have.
        $card = array_values(array_filter(Catalogs::read('component/entries'), static fn(array $c): bool => $c['name'] === 'card'))[0];

        self::assertSame('card-title', $card['demoSelector'] ?? null);
        self::assertStringContainsString($card['demoSelector'], $card['markup'], 'the selector names something the entry itself shows');
    }

    /**
     * The other four demos D-CAT-003 read on 2026-08-02. They build the page
     * out of the component, so every example carries the root class and none of
     * them is the component — there is nothing better to select, and selecting
     * would only move which scaffolding is handed over. The entry says so, and
     * the demo is then not read at all rather than read and filtered.
     */
    #[Test]
    public function anEntryWhoseDemoShowsNothingCopyableKeepsItsCuratedMarkup(): void
    {
        $root = $this->coreCheckout('14.3.5');
        $backendCss = $root . '/typo3/sysext/backend/Resources/Public/Css';
        mkdir($backendCss, 0o777, true);
        file_put_contents($backendCss . '/backend.css', '.form-control {} .form-label {}');

        $styleguide = $root . '/typo3/sysext/styleguide';
        mkdir($styleguide . '/Resources/Private/Templates/Backend/Components', 0o777, true);
        file_put_contents($styleguide . '/composer.json', json_encode([
            'name' => 'typo3/cms-styleguide',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'styleguide']],
        ], JSON_THROW_ON_ERROR));
        file_put_contents(
            $styleguide . '/Resources/Private/Templates/Backend/Components/Input.fluid.html',
            '<sg:example><div class="example-container"><div class="example-item">'
                . '<input type="text" class="form-control"></div></div></sg:example>',
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_component_lookup', ['query' => 'form-control']);
        $input = array_values(array_filter(
            $result->data['components'],
            static fn(array $entry): bool => $entry['name'] === 'input',
        ))[0];

        self::assertSame('installation', $result->data['componentSource'], 'the contract is still the installed one');
        self::assertSame('catalog', $input['markupSource']);
        self::assertStringNotContainsString('example-container', $input['markup']);
        self::assertStringContainsString('form-group', $input['markup'], 'the curated markup, unchanged');
        self::assertNotContains(
            'EXT:styleguide/Resources/Private/Templates/Backend/Components/Input.fluid.html',
            $input['sourceFiles'],
            'a file nothing was read from is not a source',
        );
    }

    /**
     * The two curated fields are alternatives: one picks the example the
     * component is shown in, the other says there is none. An entry carrying
     * both says which was meant to nobody.
     */
    #[Test]
    public function anEntryThatDerivesNothingNamesADemoAndSelectsNothingInIt(): void
    {
        $suppressed = [];
        foreach (Catalogs::read('component/entries') as $entry) {
            if (($entry['demoDerives'] ?? true) !== false) {
                continue;
            }
            $suppressed[] = $entry['name'];
            self::assertNotSame('', (string) ($entry['demoPath'] ?? ''), $entry['name'] . ' derives nothing from a demo it does not name');
            self::assertArrayNotHasKey('demoSelector', $entry, $entry['name'] . ' both selects an example and says there is none');
        }

        self::assertSame(
            ['button-group', 'dropdown', 'input', 'status-indicator'],
            $suppressed,
            'the entries read as showing the component nowhere copyable, on 14.3 and main',
        );
    }

    #[Test]
    public function onlyAnEntryWithADemoSelectsWithinIt(): void
    {
        // A selector without a demo selects nothing and reads as a rule that is
        // being applied; a selector no example could carry withholds the
        // derived markup on every version at once, and that is the failure this
        // field's fallback makes quiet.
        foreach (Catalogs::read('component/entries') as $entry) {
            $selector = $entry['demoSelector'] ?? null;
            if ($selector === null) {
                continue;
            }
            self::assertNotSame('', (string) ($entry['demoPath'] ?? ''), $entry['name'] . ' selects within a demo it does not name');
            self::assertMatchesRegularExpression('/^[a-z][a-z0-9-]*$/', (string) $selector, $entry['name'] . ' selects by something no class or custom element is named');
        }
    }

    #[Requirement('R-KNW-012')]
    #[Test]
    public function whetherAnExtensionIsPartOfTheCoreIsAnswerable(): void
    {
        // It was answered from memory in both directions in one session: a
        // community package cited as evidence of what the core does, and a
        // system extension nobody knew was there.
        $camino = Registry::call('typo3_system_extension_lookup', ['query' => 'typo3/theme-camino']);
        self::assertSame(1, $camino->data['matchCount']);
        self::assertSame('theme_camino', $camino->data['extensions'][0]['key']);
        self::assertNotSame('', $camino->data['extensions'][0]['shippedOn'], 'it is not shipped on every covered line');

        $contentBlocks = Registry::call('typo3_system_extension_lookup', ['query' => 'typo3/cms-content-blocks']);
        self::assertSame(0, $contentBlocks->data['matchCount']);
        self::assertStringContainsString('third-party', $contentBlocks->text, 'a miss is about the core, not about the package');
    }

    #[Requirement('R-KNW-012')]
    #[Test]
    public function aTargetVersionDecidesWhichExtensionsAreShipped(): void
    {
        $onThirteen = Registry::call('typo3_system_extension_lookup', ['query' => 'theme_camino', 'targetVersion' => '13.4']);
        self::assertSame(0, $onThirteen->data['matchCount'], 'the theme is not part of that line');

        $everything = Registry::call('typo3_system_extension_lookup', []);
        self::assertGreaterThan($onThirteen->data['matchCount'], $everything->data['matchCount']);
        foreach ($everything->data['extensions'] as $extension) {
            self::assertStringStartsWith('typo3/', $extension['package'], $extension['key'] . ' has no package to require it by');
        }
    }

    #[Requirement('R-KNW-012')]
    #[Test]
    public function everyShippedRangeNamesACoveredVersion(): void
    {
        $majors = Versions::majors();
        foreach (SystemExtensions::load() as $extension) {
            self::assertNotSame('', $extension['description'], $extension['key'] . ' says nothing about itself');
            foreach (['since', 'until'] as $bound) {
                if ($extension[$bound] !== null) {
                    self::assertContains($extension[$bound], $majors, $extension['key'] . ' is bound to a version this knowledge base does not cover');
                }
            }
        }
    }

    #[Requirement('R-KNW-019')]
    #[Test]
    public function theCoresOwnWorkedExamplesAreIndexed(): void
    {
        // Three times in one session the real answer was a directory inside the
        // core repository, and all three times it was reached by accident. A
        // hint per subject fixes the subject it was written for; the index is
        // for the next one.
        $everything = Registry::call('typo3_reference_list', []);
        self::assertGreaterThan(0, $everything->data['matchCount']);

        $paths = array_column($everything->data['references'], 'path');
        self::assertContains('typo3/sysext/theme_camino', $paths);
        self::assertContains('typo3/sysext/extbase/Tests/Functional/Fixtures/Extensions/blog_example', $paths);

        // And the version decides, because a path that is not on that branch
        // costs a read and answers nothing.
        $onThirteen = Registry::call('typo3_reference_list', ['targetVersion' => '13.4']);
        self::assertNotContains('typo3/sysext/theme_camino', array_column($onThirteen->data['references'], 'path'));
    }

    #[Requirement('R-KNW-019')]
    #[Test]
    public function aWorkedExampleIsNamedBesideTheHintItIsAnExampleOf(): void
    {
        // The layout hint and the theme it was written from were two answers
        // that never met: the hint was read, the extension was found later by
        // being told about it.
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'directory structure of a sitepackage extension',
            'targetVersion' => '14',
        ]);

        self::assertStringContainsString('typo3/sysext/theme_camino', $result->text);
        self::assertStringContainsString('typo3_reference_list', $result->text);
    }

    #[Requirement('R-KNW-019')]
    #[Test]
    public function everyIndexedExampleSaysWhatItIsAnExampleOfAndWhereItIs(): void
    {
        $majors = Versions::majors();
        $hintIds = array_column(Hints::load(), 'id');
        foreach (References::load() as $entry) {
            self::assertNotSame('', $entry['reference'], $entry['id'] . ' says nothing about itself');
            self::assertStringNotContainsString('..', $entry['path'], $entry['id'] . ' does not name a path in a checkout');
            if ($entry['hint'] !== null) {
                self::assertContains($entry['hint'], $hintIds, $entry['id'] . ' points at a hint that does not exist');
            }
            foreach (['since', 'until'] as $bound) {
                if ($entry[$bound] !== null) {
                    self::assertContains($entry[$bound], $majors, $entry['id'] . ' is bound to a version this knowledge base does not cover');
                }
            }
        }
    }

    #[Test]
    public function everyComponentCarriesItsSassSource(): void
    {
        foreach (Components::load() as $component) {
            self::assertNotSame('', $component['rootClass'], $component['name'] . ' has no root class');

            if ($component['sassPath'] === null) {
                // Only a web component may have no Sass source: its styles live
                // in the element itself.
                self::assertStringStartsWith('typo3-', $component['rootClass'], $component['name'] . ' has no Sass source');
                continue;
            }
            self::assertStringEndsWith('.scss', $component['sassPath'], $component['name'] . ' has no Sass source');
        }
    }

    /**
     * The cases are the ones TranslationDomainMapperTest states in the core, so
     * this port is held to the same rules as the original.
     */
    #[Test]
    public function theTranslationDomainIsDerivedByTheCoreRules(): void
    {
        $expected = [
            'EXT:test_translation_domain/Resources/Private/Language/locallang.xlf' => 'test_translation_domain.messages',
            'EXT:test_translation_domain/Resources/Private/Language/locallang_toolbar.xlf' => 'test_translation_domain.toolbar',
            'EXT:test_translation_domain/Resources/Private/Language/locallang_sudo_mode.xlf' => 'test_translation_domain.sudo_mode',
            'EXT:test_translation_domain/Resources/Private/Language/Form/locallang_tabs.xlf' => 'test_translation_domain.form.tabs',
            'EXT:test_translation_domain/Resources/Private/Language/SudoMode/locallang.xlf' => 'test_translation_domain.sudo_mode.messages',
            'EXT:test_translation_domain/Resources/Private/Language/de.locallang.xlf' => 'test_translation_domain.messages',
            'EXT:core/Resources/Private/Language/locallang.xlf' => 'core.messages',
        ];

        foreach ($expected as $reference => $domain) {
            self::assertSame($domain, TranslationDomain::fromPath($reference), $reference);
        }
    }

    #[Test]
    public function aDomainIsDerivedForAFileThatDoesNotExistYet(): void
    {
        // The point of computing rather than looking up: a file in any
        // extension, and one a patch is about to add, both get an answer —
        // which is exactly when it cannot be looked up anywhere.
        $result = Registry::call('typo3_translation_domain_lookup', [
            'path' => 'packages/my_extension/Resources/Private/Language/NotYetWritten.xlf',
        ])->data;

        self::assertSame(null, $result['domain'], 'a project path is not an EXT: reference');

        $result = Registry::call('typo3_translation_domain_lookup', [
            'path' => 'EXT:my_extension/Resources/Private/Language/NotYetWritten.xlf',
        ])->data;

        self::assertSame('my_extension.not_yet_written', $result['domain']);
    }

    #[Test]
    public function aPathThatNamesNoExtensionDerivesNoDomain(): void
    {
        $result = Registry::call('typo3_translation_domain_lookup', ['path' => 'somewhere/else.xlf'])->data;

        self::assertNull($result['domain']);
    }

    #[Test]
    public function theCatalogsSayWhichCoreRevisionTheyDescribe(): void
    {
        $meta = Meta::read();

        self::assertMatchesRegularExpression('/^[0-9a-f]{40}$/', $meta['source']['commit']);
        self::assertNotSame('', $meta['source']['branch']);
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $meta['verifiedAt']);
        self::assertSame(count(Components::load()), $meta['counts']['components'], 'the component count drifted from the catalog');
    }

    #[Test]
    public function theProvenanceLineNamesTheSnapshot(): void
    {
        $line = Meta::line();

        self::assertStringContainsString(Meta::read()['source']['version'], $line);
        self::assertStringContainsString('not in this snapshot', $line);
    }

    /**
     * A suite the script runs but does not advertise is offered.
     *
     * 13.4 accepts `-s e2e-prepare` and names it only inside the `e2e` line of
     * its usage text, so reading the usage block alone reported the hint that
     * says the suite arrives with v13 as the thing that was wrong.
     */
    #[Test]
    public function aSuiteTheScriptDispatchesWithoutListingItIsOffered(): void
    {
        $script = <<<'SCRIPT'
                Specifies the test suite to run
                    - functional: PHP functional tests
                    - e2e: end to end tests (use e2e-prepare for manual execution)
                    - unit (default): PHP unit tests

                -b <docker|podman>
            SCRIPT;
        $script .= "\n" . implode("\n", [
            '    e2e)',
            '        runPlaywright',
            '        ;;',
            '    e2e-prepare)',
            '        runPlaywright',
            '        ;;',
            '    build*)',
            '        ;;',
            '    *)',
            '        ;;',
        ]);

        $offered = VersionCheck::suitesIn($script);

        self::assertContains('e2e-prepare', $offered, 'a dispatched suite the usage text does not list is read as absent');
        self::assertContains('e2e', $offered);
        self::assertContains('functional', $offered, 'the usage block is still read');
        self::assertContains('unit', $offered, 'the default marker still yields the suite name');
        self::assertNotContains('build*', $offered, 'a glob label names no suite');
        self::assertNotContains('*', $offered);
    }
}
