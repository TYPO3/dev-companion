<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Directory;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\Fixture;

/**
 * Asking the installation itself, and what happens on the three ways that fail.
 *
 * There is no real TYPO3 here — this repository has none and never will — so the
 * installations below carry an autoloader shaped like one. The probe is not
 * simulated for that: it is delivered to a real interpreter, boots what the
 * autoloader gives it, and answers as data, which is how the payload, the
 * declared autoloader path, the three states and the attribution evidence all
 * end up held by the same mechanism they run through.
 */
final class Typo3RuntimeTest extends TestCase
{
    private string $root = '';

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Typo3Cli::CONSOLE_VARIABLE);
        Instance::discoverFrom(null);
        Typo3Cli::useRunner(null);
        Typo3Cli::forget();
        Typo3Runtime::forget();
        // And the installation this test wrote. A unique name is not
        // overwritten by the next run, so nothing but this takes one away —
        // `D-COD-006`, whose third **Wrong if** this fired.
        if ($this->root !== '') {
            Directory::remove($this->root);
            $this->root = '';
        }
    }

    #[Requirement('R-DIS-019')]
    #[Test]
    public function theProbeReachesAnInterpreterAndAnswersAsData(): void
    {
        // The whole delivery in one assertion: the payload is base64-encoded
        // into `php -r`, the opening tag is stripped, the subprocess starts in
        // the installation root and prints one JSON object. What it reports
        // here is the missing autoloader, because a fixture has no TYPO3 — that
        // it reports anything at all is the mechanism working.
        $this->discover($this->installationWithAConsole());

        $answer = Typo3Runtime::ask();

        self::assertSame(Typo3Runtime::STATE_UNREACHABLE, $answer['state']);
        self::assertStringContainsString('no autoloader at vendor/autoload.php', $answer['reason']);
        self::assertStringContainsString($this->root, $answer['reason'], 'it ran in the installation root');
    }

    #[Requirement('R-DIS-019')]
    #[Test]
    public function theAutoloaderIsTheOneTheInstallationDeclares(): void
    {
        // Relative, and from the declared vendor directory: the extension
        // testing setup puts it below .Build/, and inside a DDEV container no
        // absolute path of this machine exists at all.
        $this->discover($this->installationWithAConsole(['config' => ['vendor-dir' => '.Build/vendor']]));

        self::assertStringContainsString('.Build/vendor/autoload.php', Typo3Runtime::ask()['reason']);
    }

    #[Requirement('R-DIS-019')]
    #[Test]
    public function aStatedConsoleIsKeptAsTheWayInAndPointedAtPhp(): void
    {
        // A stated console is a transport plus a binary. The transport is the
        // part this server could never have worked out, so it is kept and only
        // the binary is exchanged.
        $this->root = $this->installationWithAConsole();
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=' . PHP_BINARY . ' /some/where/typo3');
        $this->discover($this->root);

        self::assertStringContainsString('no autoloader at', Typo3Runtime::ask()['reason']);
    }

    #[Requirement('R-DIS-019')]
    #[Test]
    public function aStatedConsoleNoInterpreterCanBeDerivedFromIsSaidOutLoud(): void
    {
        // `env` is a program, and this case is about the answer for a stated
        // console whose first word is one and still names no interpreter.
        // Whether this machine has an `env` is not what it holds — read off
        // the real `PATH` it passed here and failed on a machine carrying
        // only PHP, with the answer for a program that does not exist.
        $ran = self::createStub(CommandRunner::class);
        $ran->method('locate')->willReturnCallback(
            static fn(string $name): ?string => $name === 'env' ? '/usr/bin/env' : null,
        );
        Typo3Cli::useRunner($ran);

        $this->root = $this->installationWithAConsole();
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=env /some/where/cli');
        $this->discover($this->root);

        $answer = Typo3Runtime::ask();

        self::assertSame(Typo3Runtime::STATE_UNREACHABLE, $answer['state']);
        self::assertStringContainsString('no interpreter can be derived', $answer['reason']);
    }

    #[Test]
    public function aBootedContainerAnswersWithTheTopicsAndTheirAttribution(): void
    {
        // A TYPO3 shaped like the real one, so the real probe boots it: the
        // registry answers with EXT: sources and the TCA with LLL:EXT: titles,
        // which is what an entry is attributed by on the way back.
        $root = $this->installationWithAConsole();
        Fixture::bootsInto(
            $root,
            ['ext-acme-teaser' => 'EXT:acme/Resources/Public/Icons/teaser.svg', 'actions-add' => 'EXT:core/Resources/Public/Icons/T3Icons/actions/add.svg'],
            ['tx_acme_event' => 'LLL:EXT:acme/Resources/Private/Language/locallang_db.xlf:tx_acme_event'],
            ['acme_teaser' => ['LLL:EXT:acme/Resources/Private/Language/locallang_be.xlf:teaser', 'ext-acme-teaser']],
        );
        $this->discover($root);

        $answer = Typo3Runtime::ask();

        self::assertSame(Typo3Runtime::STATE_FULL, $answer['state']);
        self::assertSame('', $answer['reason']);
        self::assertSame(
            ['ext-acme-teaser' => 'EXT:acme/Resources/Public/Icons/teaser.svg', 'actions-add' => 'EXT:core/Resources/Public/Icons/T3Icons/actions/add.svg'],
            $answer['topics']['icons'],
        );
        self::assertArrayHasKey('tx_acme_event', $answer['topics']['tables']);
        self::assertArrayHasKey('acme_teaser', $answer['topics']['contentElements']);
    }

    /**
     * A form data group is a graph, and what the caller wants is what it
     * resolves to. The fixture's ordering service reverses rather than
     * resolves, so what is held here is that the probe reports the service's
     * answer and not the order the registry was written in — a passthrough
     * could not tell those apart, and an ordering written into the probe would
     * be the second implementation it exists to avoid.
     */
    #[Test]
    public function aFormDataGroupComesBackInTheOrderTheInstallationResolved(): void
    {
        $root = $this->installationWithAConsole();
        Fixture::bootsInto($root, formDataGroups: [
            'tcaDatabaseRecord' => [
                'Acme\\First' => [],
                'Acme\\Second' => ['depends' => ['Acme\\First']],
                'Acme\\Third' => ['depends' => ['Acme\\Second'], 'before' => ['Acme\\Fourth']],
            ],
        ]);
        $this->discover($root);

        $groups = Typo3Runtime::topic('formDataGroups');

        self::assertIsArray($groups);
        self::assertArrayNotHasKey('unavailable', $groups);
        self::assertSame(
            ['Acme\\Third', 'Acme\\Second', 'Acme\\First'],
            array_column($groups['groups']['tcaDatabaseRecord'], 'provider'),
        );
        self::assertSame(
            [['Acme\\Second'], ['Acme\\First'], []],
            array_column($groups['groups']['tcaDatabaseRecord'], 'depends'),
        );
        self::assertSame(
            [['Acme\\Fourth'], [], []],
            array_column($groups['groups']['tcaDatabaseRecord'], 'before'),
        );
    }

    /**
     * The navigation component a module resolves to is inherited from its
     * parent, and the registry is what resolves it. What is held here is that
     * the probe reports that value: `web_list` declares none and comes back
     * page-tree navigated, which is the answer no reading of a
     * `Configuration/Backend/Modules.php` gives — `D-ANS-077`.
     */
    #[Test]
    public function aModuleComesBackWithItsNavigationComponent(): void
    {
        $root = $this->installationWithAConsole();
        Fixture::bootsInto($root, modules: [
            'web' => ['path' => '/module/web', 'navigationComponent' => '@typo3/backend/tree/page-tree-element'],
            'web_list' => ['parent' => 'web', 'path' => '/module/web/list', 'routes' => [
                '_default' => ['target' => 'Acme\\Records::main'],
                'detail' => ['target' => 'Acme\\Records::detail'],
            ]],
            'site_configuration' => ['parent' => 'site', 'path' => '/module/site/configuration', 'inherit' => false],
        ]);
        $this->discover($root);

        $topic = Typo3Runtime::topic('modules');

        self::assertIsArray($topic);
        self::assertArrayNotHasKey('unavailable', $topic);
        $components = array_column($topic['modules'], 'navigationComponent', 'identifier');
        self::assertSame('@typo3/backend/tree/page-tree-element', $components['web_list']);
        self::assertSame('', $components['site_configuration']);

        $routes = $topic['modules'][1]['routes'];
        self::assertSame(['web_list', 'web_list.detail'], array_column($routes, 'identifier'));
        self::assertSame(['/module/web/list', '/module/web/list/detail'], array_column($routes, 'path'));
        self::assertSame([], $topic['modules'][0]['routes'], 'a first-level module registers none');
    }

    /**
     * The half a registration file can be checked in without being installed.
     *
     * Five registration mistakes in one session were each caught by an
     * installation that had already been rebuilt, and the cycle was: edit,
     * flush the cache, ask the registry — `D-FBK-055`. Parent and icon fail
     * when a user opens the module and never when the file is read.
     */
    #[Decision('D-FBK-055')]
    #[Test]
    public function aRegistrationFileIsCheckedBeforeItIsInstalled(): void
    {
        $root = $this->installationWithAConsole();
        Fixture::bootsInto($root, modules: [
            'content' => ['path' => '/module/content'],
        ], icons: ['module-shelter' => 'EXT:animalshelter/Resources/Public/Icons/shelter.svg']);
        $this->discover($root);

        $file = $root . '/Modules.php';
        file_put_contents($file, "<?php\n\nreturn [\n"
            . "    'animalshelter_animals' => [\n"
            . "        'parent' => 'content',\n"
            . "        'iconIdentifier' => 'module-shelter',\n"
            . "        'labels' => 'animalshelter.modules.animals',\n"
            . "    ],\n"
            . "    'animalshelter_stray' => [\n"
            . "        'parent' => 'nothing_registers_this',\n"
            . "        'iconIdentifier' => 'module-nothing-registers-this',\n"
            . "    ],\n"
            . "];\n");

        $result = Registry::call('typo3_backend_module_lookup', ['file' => $file]);
        $checked = array_column($result->data['checked'], null, 'identifier');

        self::assertTrue($checked['animalshelter_animals']['parentRegistered']);
        self::assertTrue($checked['animalshelter_animals']['iconRegistered']);
        self::assertSame('animalshelter.modules.animals', $checked['animalshelter_animals']['labels']);

        self::assertFalse($checked['animalshelter_stray']['parentRegistered']);
        self::assertFalse($checked['animalshelter_stray']['iconRegistered']);
        self::assertStringContainsString('NOT registered here', $result->text);
        self::assertStringContainsString('nothing in the file was executed', $result->text);
    }

    #[Test]
    public function aFailsafeContainerIsAReason(): void
    {
        // It answers — with core packages and nothing else, which is the state
        // every extension repository is in and the one that looks complete.
        $root = $this->installationWithAConsole();
        Fixture::bootsInto($root, failsafe: true);
        $this->discover($root);

        $answer = Typo3Runtime::ask();

        self::assertSame(Typo3Runtime::STATE_FAILSAFE, $answer['state']);
        self::assertStringContainsString('no essential configuration', $answer['reason']);
        self::assertNull(Typo3Runtime::topic('icons'), 'a subset that looks whole is never handed on');
        self::assertSame($answer['reason'], Typo3Runtime::reason());
    }

    #[Test]
    public function anExtensionIsNamedByTheReferenceAnEntryCarries(): void
    {
        self::assertSame('news', Typo3Runtime::extensionIn('EXT:news/Resources/Public/Icons/list.svg'));
        self::assertSame('news', Typo3Runtime::extensionIn('LLL:EXT:news/Resources/Private/Language/locallang.xlf:plugin'));
        self::assertSame('my_sitepackage', Typo3Runtime::extensionIn('EXT:my_sitepackage/Configuration/Icons.php'));
        // Nothing names an extension: it belongs to the installation.
        self::assertNull(Typo3Runtime::extensionIn('content-news'));
        self::assertNull(Typo3Runtime::extensionIn('impexp.db:tx_impexp_presets'));
    }

    #[Requirement('R-DIS-019')]
    #[Test]
    public function withoutAConsoleTheReasonIsTheConsolesOwn(): void
    {
        // Nothing is invented here: the console said why it could not be
        // invoked, and that sentence is what the caller gets.
        $this->discover($this->installation());

        $answer = Typo3Runtime::ask();

        self::assertSame(Typo3Runtime::STATE_UNREACHABLE, $answer['state']);
        self::assertSame(Typo3Cli::reason(), $answer['reason']);
        self::assertNull(Typo3Runtime::topic('icons'), 'no topic is a topic of its own');
    }

    #[Test]
    public function withoutAnInstallationThereIsNothingToBoot(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();
        Typo3Runtime::forget();

        self::assertStringContainsString('no TYPO3 installation', Typo3Runtime::ask()['reason']);
    }

    #[Test]
    public function aTopicWithAnArgumentIsReadOnlyWhereACallerAskedForIt(): void
    {
        // What keeps the whole of TYPO3_CONF_VARS — around 50 kB of JSON on a
        // 13.4 before an extension has added to it, measured on 2026-08-18
        // against `.environments/e-site-13.4` — out of every reading taken for
        // an icon or a module, and the resolution of one flex column out of the
        // same readings.
        $root = $this->installationWithAConsole();
        Fixture::bootsInto(
            $root,
            configuration: ['SYS' => ['devIPmask' => '10.0.0.1']],
            tca: ['tt_content' => ['columns' => ['pi_flexform' => ['config' => [
                'type' => 'flex',
                'ds' => 'FILE:EXT:acme/Configuration/FlexForms/Default.xml',
            ]]]]],
            flexForm: ['structures' => ['default' => ['sheets' => ['sDEF' => ['ROOT' => ['el' => []]]]]]],
        );
        $this->discover($root);

        $topics = Typo3Runtime::ask()['topics'];
        self::assertArrayNotHasKey('configuration', $topics);
        self::assertArrayNotHasKey('flexForm', $topics);
        self::assertArrayNotHasKey('liveSchema', $topics);
        self::assertArrayNotHasKey('services', $topics);

        self::assertSame(
            ['found' => true, 'value' => '10.0.0.1'],
            Typo3Runtime::configuration('SYS/devIPmask'),
            'asking discards the reading that did not carry the path and takes another',
        );
        self::assertSame(['found' => false, 'value' => null], Typo3Runtime::configuration('SYS/nothingHere'));

        // The second one is asked after the first, which is the ordering no
        // caller has to keep: the reading taken for the path is discarded again
        // rather than answered out of.
        $flexForm = Typo3Runtime::flexForm('tt_content', 'pi_flexform', []);
        self::assertIsArray($flexForm);
        self::assertSame('', $flexForm['failure']);
        self::assertSame(['default'], $flexForm['keys']);
        self::assertNull(Typo3Runtime::topic('configuration'), 'the path went with the reading it was asked in');
    }

    /**
     * The probe and the callers that read it are two files, and a topic name is
     * the only thing between them: one that nothing asks for is dead weight in
     * a payload every reading carries, and one nothing writes makes a tool say
     * the installation could not answer, which is what a caller acts on.
     *
     * Nothing else here can hold the probe. It runs in the installation, so no
     * test loads it — `D-COD-004` — and the readings above exercise it through
     * a TYPO3 shaped like the real one rather than reading the file.
     */
    #[Test]
    public function everyTopicTheProbeWritesIsOneSomethingAsksFor(): void
    {
        $probe = (string) file_get_contents(Paths::root() . '/src/Installation/probe.php');
        preg_match_all('/\$answer\[.topics.\]\[.([a-zA-Z]+).\]/', $probe, $found);
        $written = array_unique($found[1]);
        sort($written);

        $asked = [];
        foreach (Finder::create()->files()->in(Paths::root() . '/src')->name('*.php') as $file) {
            preg_match_all(
                "/(?:Typo3Runtime::|self::)(?:topic|asked)\\('([a-zA-Z]+)'/",
                (string) file_get_contents($file->getPathname()),
                $calls,
            );
            $asked = [...$asked, ...$calls[1]];
        }
        $asked = array_unique($asked);
        sort($asked);

        self::assertSame($written, $asked);
    }

    /**
     * It is delivered to the other side and run there, where nothing of this
     * package exists. A reference to one of its classes is a fatal error in
     * somebody else's installation rather than a failure here.
     */
    #[Test]
    public function theProbeReachesNothingOfThisPackage(): void
    {
        $probe = (string) file_get_contents(Paths::root() . '/src/Installation/probe.php');

        self::assertStringNotContainsString('TYPO3\\DevCompanion', $probe);
        self::assertDoesNotMatchRegularExpression('/^\s*(use|require|include)\b/m', $probe);
    }

    /** @param array<string, mixed> $manifest */
    private function installation(array $manifest = []): string
    {
        $root = sys_get_temp_dir() . '/typo3-dev-companion-runtime-' . bin2hex(random_bytes(6));
        mkdir($root . '/typo3/sysext/core', 0o777, true);
        file_put_contents($root . '/composer.json', json_encode(
            $manifest + ['name' => 'typo3/cms', 'type' => 'typo3-cms-core'],
            JSON_THROW_ON_ERROR
        ));
        file_put_contents($root . '/typo3/sysext/core/composer.json', json_encode([
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'core']],
        ], JSON_THROW_ON_ERROR));

        return $this->root = $root;
    }

    /** @param array<string, mixed> $manifest */
    private function installationWithAConsole(array $manifest = []): string
    {
        $root = $this->installation($manifest);
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");

        return $root;
    }

    private function discover(string $root): void
    {
        Instance::discoverFrom($root);
        Typo3Cli::forget();
        Typo3Runtime::forget();
    }
}
