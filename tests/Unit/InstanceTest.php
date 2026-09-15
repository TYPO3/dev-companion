<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Project;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;

/**
 * The one place this server reads something other than its own knowledge base,
 * and the rules that keep that from an accident.
 */
final class InstanceTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Instance::ROOT_VARIABLE);
        Instance::discoverFrom(null);
    }

    #[Requirement('R-DIS-001')]
    #[Test]
    public function withoutAnEntrypointHandingInADirectoryThereIsNoInstance(): void
    {
        // The HTTP case. An endpoint that serves requests never calls
        // discoverFrom(), so no caller gets an answer from whatever
        // installation the document root happens to sit in.
        Instance::discoverFrom(null);

        self::assertFalse(Instance::isAvailable());
        self::assertNull(Instance::root());
        self::assertSame([], Instance::packages());
    }

    #[Test]
    public function aCoreCheckoutIsFoundFromAnyDirectoryInsideIt(): void
    {
        $root = $this->coreCheckout();
        Instance::discoverFrom($root . '/typo3/sysext/backend/Classes/Controller');

        $instance = Instance::describe();
        self::assertNotNull($instance);
        self::assertSame(realpath($root), $instance['root']);
        self::assertSame(Instance::KIND_CORE_CHECKOUT, $instance['kind']);
        self::assertSame(['backend', 'core'], array_keys(Instance::packages()));
    }

    #[Test]
    public function aComposerProjectIsFoundThroughItsInstalledPackages(): void
    {
        $root = $this->composerProject();
        Instance::discoverFrom($root);

        $instance = Instance::describe();
        self::assertNotNull($instance);
        self::assertSame(Instance::KIND_COMPOSER_PROJECT, $instance['kind']);

        // A project's own extension registers icons and ships labels exactly
        // like a system extension does, so it has to be in the list.
        self::assertSame(['core', 'my_sitepackage'], array_keys(Instance::packages()));
    }

    #[Decision('D-DIS-004')]
    #[Requirement('R-AUD-004')]
    #[Test]
    public function theTypo3VersionIsReadFromTheCorePackage(): void
    {
        // It has to be available exactly when the console is not. An
        // installation whose database has no schema still has a version, and
        // the version is what decides whether an answer holds for it.
        Instance::discoverFrom($this->composerProject('vendor', '13.4.33'));

        self::assertSame('13.4.33', Instance::typo3Version());
        self::assertSame(13, Instance::typo3Major());
    }

    #[Test]
    public function anInstallationThatStatesNoVersionIsNotGuessedAt(): void
    {
        Instance::discoverFrom($this->composerProject());

        self::assertNull(Instance::typo3Version());
        self::assertNull(Instance::typo3Major());
    }

    #[Requirement('R-DIS-002')]
    #[Test]
    public function aProjectThatMovedItsVendorDirectoryIsStillFound(): void
    {
        // The layout the TYPO3 extension test setup produces. Reading the
        // default vendor/ instead walked past the installation entirely, and
        // every question only it can answer came back as if nothing existed.
        $root = $this->composerProject('.build/vendor');
        Instance::discoverFrom($root);

        self::assertSame(Instance::KIND_COMPOSER_PROJECT, Instance::describe()['kind']);
        self::assertSame(['core', 'my_sitepackage'], array_keys(Instance::packages()));
    }

    #[Requirement('R-DIS-004')]
    #[Test]
    public function theExtensionBeingWorkedOnIsAmongThePackages(): void
    {
        $root = $this->composerProject('.build/vendor');
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/bootstrap-package',
            'type' => 'typo3-cms-extension',
            'config' => ['vendor-dir' => '.build/vendor'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        // Without this the one package the agent edits is the only one absent
        // from the answers about its own installation.
        self::assertSame(realpath($root), Instance::packages()['bootstrap_package'] ?? null);
    }

    #[Requirement('R-DIS-017')]
    #[Test]
    public function aPackageBelowTestsBelongsToTheTestSetup(): void
    {
        $root = $this->composerProject('.build/vendor');
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/bootstrap-package',
            'type' => 'typo3-cms-extension',
            'config' => ['vendor-dir' => '.build/vendor'],
            'repositories' => ['tests' => ['type' => 'path', 'url' => 'Tests/Packages/*']],
        ], JSON_THROW_ON_ERROR));
        mkdir($root . '/Tests/Packages/demo_package', 0o777, true);
        file_put_contents($root . '/Tests/Packages/demo_package/composer.json', json_encode([
            'name' => 'acme/demo-package',
            'type' => 'typo3-cms-extension',
            'extra' => ['typo3/cms' => ['extension-key' => 'demo_package']],
        ], JSON_THROW_ON_ERROR));
        $this->alsoInstalled($root . '/.build/vendor', [
            'name' => 'acme/demo-package',
            'type' => 'typo3-cms-extension',
            'install-path' => '../../../Tests/Packages/demo_package',
            'extra' => ['typo3/cms' => ['extension-key' => 'demo_package']],
        ]);
        Instance::discoverFrom($root);

        $origins = array_column(Project::describe()['extensions'], 'origin', 'key');

        // The fixture as the project's own says "this is the work in hand"
        // about a package that exists for a suite to load. A review then audits
        // it as if it were a release.
        self::assertSame(Project::ORIGIN_FIXTURE, $origins['demo_package'] ?? null);
        self::assertSame(Project::ORIGIN_PROJECT, $origins['bootstrap_package'] ?? null);
        self::assertSame(Project::ORIGIN_THIRD_PARTY, Project::origin('/app/.build/vendor/b13/container'));
    }

    #[Decision('D-DIS-001')]
    #[Test]
    public function aRootAlsoInstalledIntoVendorIsOnePackage(): void
    {
        // The extension checkout that requires itself through a path
        // repository. Composer symlinks the root into the vendor directory and
        // lists it there, so the same extension arrives twice. Both entries
        // resolve to one realpath under one key, which is what makes them
        // collapse. The vendor path would report the extension under edit as a
        // dependency of the repository it is.
        $root = $this->composerProject();
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/bootstrap-package',
            'type' => 'typo3-cms-extension',
            'repositories' => ['self' => ['type' => 'path', 'url' => '.']],
        ], JSON_THROW_ON_ERROR));
        mkdir($root . '/vendor/acme', 0o777, true);
        symlink($root, $root . '/vendor/acme/bootstrap-package');
        $this->alsoInstalled($root . '/vendor', [
            'name' => 'acme/bootstrap-package',
            'type' => 'typo3-cms-extension',
            'install-path' => '../acme/bootstrap-package',
        ]);
        Instance::discoverFrom($root);

        self::assertSame(['bootstrap_package', 'core', 'my_sitepackage'], array_keys(Instance::packages()));
        self::assertSame(realpath($root), Instance::packages()['bootstrap_package']);

        $origins = array_column(Project::describe()['extensions'], 'origin', 'key');
        self::assertSame(Project::ORIGIN_PROJECT, $origins['bootstrap_package'] ?? null);
    }

    #[Decision('D-DIS-001')]
    #[Test]
    public function aMonorepoRootIsCountedBesideThePackagesItHolds(): void
    {
        // A repository that holds extensions rather than is one, and still
        // declares a TYPO3 package type at its root. Nothing in the metadata
        // says the root is a container, so it counts like any other root. But
        // under its own key, and the extension under edit keeps the directory
        // Composer installed it in.
        $root = $this->composerProject();
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/typo3-extensions',
            'type' => 'typo3-cms-extension',
            'repositories' => ['packages' => ['type' => 'path', 'url' => 'packages/*']],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        self::assertSame(['core', 'my_sitepackage', 'typo3_extensions'], array_keys(Instance::packages()));
        self::assertSame(realpath($root) . '/packages/my_sitepackage', Instance::packages()['my_sitepackage']);
    }

    #[Requirement('R-DIS-005')]
    #[Decision('D-DIS-019')]
    #[Decision('D-SCO-012')]
    #[Test]
    public function aRepositoryWithNoInstallationAroundItIsNotReportedAsOne(): void
    {
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', json_encode(
            ['name' => 'acme/bootstrap-package', 'type' => 'typo3-cms-extension'],
            JSON_THROW_ON_ERROR
        ));
        Instance::discoverFrom($root);

        // An extension checkout without its dependencies has nothing to answer
        // from. To say so beats a report of an installation that holds a single
        // package and no console — `D-DIS-019`.
        self::assertFalse(Instance::isAvailable());
        self::assertSame([], Instance::packages());

        // What the same manifest is enough for: to say which extension this
        // repository is, which places the work and reports nothing
        // (`D-SCO-012`). The key derives from the name, because this one
        // declares none.
        self::assertSame('bootstrap_package', Instance::startedInPackage());
        self::assertSame(Instance::KIND_EXTENSION_REPOSITORY, Instance::startedIn());
    }

    /**
     * @param array<string, mixed> $manifest what the root's composer.json declares
     */
    #[Decision('D-ANS-085')]
    #[Requirement('R-PRJ-011')]
    #[Decision('D-DIS-019')]
    #[Test]
    #[DataProvider('manifests')]
    public function aProjectRootIsRecognisedByWhatItsOwnManifestDeclares(array $manifest, bool $isProjectRoot): void
    {
        // The state the installation workflow starts in: a clone nobody has run
        // composer install in. Everything the project answer reads is in these
        // files, and the walk goes up twelve directories. So what identifies a
        // root has to be a declaration of TYPO3 rather than the presence of a
        // composer.json. Otherwise the answer reports a TYPO3 project for
        // whatever PHP repository the caller happens to stand below
        // (`D-ANS-085`) — `D-DIS-019`.
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', json_encode($manifest, JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        self::assertSame($isProjectRoot ? realpath($root) : null, Instance::project()['root'] ?? null);

        // And in neither case is it an installation. Nothing stands installed
        // below it. So the icon, label and package answers keep that up rather
        // than speak for a checkout with no packages and no console.
        self::assertFalse(Instance::isAvailable());
    }

    /**
     * @return array<string, array{0: array<string, mixed>, 1: bool}>
     */
    public static function manifests(): array
    {
        return [
            'an extension repository' => [[
                'name' => 't3g/blog',
                'type' => 'typo3-cms-extension',
                'require' => ['php' => '^8.2', 'typo3/cms-core' => '^13.4.15 || ^14.3'],
                'extra' => ['typo3/cms' => ['extension-key' => 'blog']],
            ], true],
            // The base distribution every environment below .environments/
            // starts from. No package type and no extra block, and the packages
            // it requires are the whole of what says TYPO3 at all.
            'a site distribution' => [[
                'name' => 'typo3/cms-base-distribution',
                'type' => 'project',
                'require' => ['typo3/cms-backend' => '^13.4', 'typo3/cms-core' => '^13.4'],
            ], true],
            'a package that installs TYPO3 for its tests alone' => [[
                'name' => 'acme/fluid-components',
                'type' => 'library',
                'require-dev' => ['typo3/cms-core' => '^14.3'],
            ], true],
            'a project that declares only the installer keys' => [[
                'name' => 'acme/site',
                'type' => 'project',
                'extra' => ['typo3/cms' => ['web-dir' => 'public']],
            ], true],
            'a PHP library' => [[
                'name' => 'acme/toolkit',
                'type' => 'library',
                'require' => ['php' => '>=8.2', 'symfony/finder' => '^7.4'],
            ], false],
            // A requirement on one of TYPO3's tools is not a requirement on
            // TYPO3. It is the shape a rule on the vendor name alone would
            // claim.
            'a repository that requires TYPO3 tooling' => [[
                'name' => 'acme/toolkit',
                'type' => 'library',
                'require-dev' => ['typo3/coding-standards' => '^0.8', 'typo3/tailor' => '^1.5'],
            ], false],
        ];
    }

    #[Requirement('R-PRJ-011')]
    #[Decision('D-DIS-019')]
    #[Test]
    public function aPackageInsideAnInstalledProjectIsNotTheProjectRoot(): void
    {
        // The walk looks for the installation whole before it looks for any
        // declaration. So the project a session stands in stays the answer from
        // inside one of its own packages. There the manifest declares a TYPO3
        // package type, and the environment, the sites and the document root
        // are one directory up — `D-DIS-019`.
        $root = $this->composerProject();
        file_put_contents($root . '/packages/my_sitepackage/composer.json', json_encode(
            ['name' => 'acme/my-sitepackage', 'type' => 'typo3-cms-extension'],
            JSON_THROW_ON_ERROR
        ));
        Instance::discoverFrom($root . '/packages/my_sitepackage');

        self::assertSame(realpath($root), Instance::project()['root'] ?? null);
    }

    #[Requirement('R-PRJ-011')]
    #[Decision('D-DIS-019')]
    #[Test]
    public function aNamedInstallationThatIsNotThereIsNotWalkedPast(): void
    {
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', json_encode(
            ['name' => 't3g/blog', 'type' => 'typo3-cms-extension'],
            JSON_THROW_ON_ERROR
        ));
        putenv(Instance::ROOT_VARIABLE . '=' . $root . '/nowhere');
        Instance::discoverFrom($root);

        // A description of the repository the walk finds would answer about
        // something other than what the caller named. That is the failure the
        // answer reports through the variable rather than searches past —
        // `D-DIS-019`.
        self::assertNull(Instance::project());
    }

    #[Requirement('R-DIS-007')]
    #[Test]
    public function anInstallationNamedOutrightIsReadWithoutAnySearch(): void
    {
        // The way out of every layout this server cannot walk to. A stack it
        // has never heard of, an installation in a subdirectory, a client that
        // starts the server beside the checkout rather than inside it.
        $root = $this->composerProject();
        putenv(Instance::ROOT_VARIABLE . '=' . $root);
        Instance::discoverFrom(sys_get_temp_dir());

        $instance = Instance::describe();
        self::assertSame(realpath($root), $instance['root']);
        self::assertSame(Instance::VIA_ENVIRONMENT, $instance['via']);
        self::assertSame('', Instance::misconfiguration());
    }

    #[Requirement('R-DIS-007')]
    #[Test]
    public function aNamedInstallationThatIsNotThereIsReported(): void
    {
        $root = $this->composerProject();
        putenv(Instance::ROOT_VARIABLE . '=' . $root . '/nowhere');
        Instance::discoverFrom($root);

        // A fall back to the discoverable one would answer about an
        // installation other than the one the caller named. Nothing would
        // mention the ignored setting again.
        self::assertFalse(Instance::isAvailable());
        self::assertStringContainsString(Instance::ROOT_VARIABLE, Instance::misconfiguration());
    }

    #[Requirement('R-DIS-009')]
    #[Test]
    public function anInstallationThatAppearsDuringTheSessionIsFound(): void
    {
        // The stdio process lives as long as the agent session. An agent that
        // hears there is nothing to read runs composer install or starts the
        // containers, because of that answer. A kept "nothing" outlives its
        // reason. The caller would have to restart the client to get an answer
        // that has been true for ten minutes.
        $root = $this->temporaryDirectory();
        Instance::discoverFrom($root);
        self::assertFalse(Instance::isAvailable());

        $this->installPackagesInto($root);

        self::assertTrue(Instance::isAvailable());
        self::assertSame(['core'], array_keys(Instance::packages()));
    }

    #[Test]
    public function aDirectoryOutsideAnyInstallationFindsNothing(): void
    {
        Instance::discoverFrom(sys_get_temp_dir());

        self::assertFalse(Instance::isAvailable());
    }

    #[Test]
    public function theAnswerSaysWhereItLookedSoAWrongInstanceIsVisible(): void
    {
        $root = $this->coreCheckout();
        $startedFrom = $root . '/typo3/sysext/core';
        Instance::discoverFrom($startedFrom);

        self::assertSame(realpath($startedFrom), Instance::describe()['startedFrom']);
    }

    /**
     * Adds one more package to what a vendor directory reports as installed.
     *
     * @param array<string, mixed> $package as Composer writes it into installed.json
     */
    private function alsoInstalled(string $vendor, array $package): void
    {
        $file = $vendor . '/composer/installed.json';
        $installed = json_decode((string) file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);
        $installed['packages'][] = $package;
        file_put_contents($file, json_encode($installed, JSON_THROW_ON_ERROR));
    }
}
