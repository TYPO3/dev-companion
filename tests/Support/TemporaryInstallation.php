<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Support;

use PHPUnit\Framework\Attributes\After;

/**
 * Builds the two installation layouts on disk and takes them away again.
 *
 * This server reads what an installation is from the files Composer and the
 * core monorepo leave behind. So a test about installations has to have those
 * files, and there are only two shapes of them.
 */
trait TemporaryInstallation
{
    /** @var array<int, string> Every root this test built, so all of them go. */
    private array $temporaryRoots = [];

    #[After]
    public function removeTemporaryInstallation(): void
    {
        // A test needs two of them as soon as it reads one installation while
        // the session sits in another. A record of only the last one left the
        // other in the temporary directory for good.
        foreach ($this->temporaryRoots as $root) {
            Directory::remove($root);
        }
        $this->temporaryRoots = [];
    }

    /**
     * Hands a root a test laid out itself to the same cleanup, and gives it
     * back. The two layouts below are not every shape a test needs.
     */
    private function removeAfterwards(string $root): string
    {
        $this->temporaryRoots[] = $root;

        return $root;
    }

    /**
     * A core monorepo checkout that holds the core and backend system
     * extensions.
     */
    private function coreCheckout(string $version = ''): string
    {
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', '{"name": "typo3/cms", "type": "typo3-cms-core"}');
        foreach (['core', 'backend'] as $key) {
            $path = $root . '/typo3/sysext/' . $key;
            mkdir($path . '/Classes/Controller', 0o777, true);
            file_put_contents($path . '/composer.json', json_encode([
                'name' => 'typo3/cms-' . $key,
                'type' => 'typo3-cms-framework',
                'extra' => ['typo3/cms' => ['extension-key' => $key]],
            ], JSON_THROW_ON_ERROR));
        }
        if ($version !== '') {
            $this->typo3Version($root . '/typo3/sysext/core', $version);
        }

        return $root;
    }

    /** The class the core states its own version in. */
    private function typo3Version(string $corePackage, string $version): void
    {
        $path = $corePackage . '/Classes/Information';
        mkdir($path, 0o777, true);
        file_put_contents(
            $path . '/Typo3Version.php',
            sprintf("<?php\nclass Typo3Version\n{\n    protected const VERSION = '%s';\n}\n", $version)
        );
    }

    /**
     * The repository of a distributed extension. A root manifest that declares
     * the key, the container setup beside it, and, where the caller asks for
     * it, what `composer install` leaves behind.
     */
    private function extensionRepository(bool $installed = false): string
    {
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 't3g/blog',
            'type' => 'typo3-cms-extension',
            'extra' => ['typo3/cms' => ['extension-key' => 'blog']],
        ], JSON_THROW_ON_ERROR));
        mkdir($root . '/.ddev', 0o777, true);
        file_put_contents($root . '/.ddev/config.yaml', "name: blog\n");
        if ($installed) {
            $this->installPackagesInto($root);
        }

        return $root;
    }

    /** What Composer leaves behind at a root that carried only its manifest. */
    private function installPackagesInto(string $root): void
    {
        mkdir($root . '/vendor/typo3/cms-core', 0o777, true);
        mkdir($root . '/vendor/composer', 0o777, true);
        file_put_contents($root . '/vendor/composer/installed.json', json_encode(['packages' => [[
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'install-path' => '../typo3/cms-core',
            'extra' => ['typo3/cms' => ['extension-key' => 'core']],
        ]]], JSON_THROW_ON_ERROR));
    }

    /** A Composer project with one system extension and one of its own. */
    private function composerProject(string $vendorDirectory = 'vendor', string $version = ''): string
    {
        $root = $this->temporaryDirectory();
        $vendor = $root . '/' . $vendorDirectory;
        if ($vendorDirectory !== 'vendor') {
            file_put_contents($root . '/composer.json', json_encode(
                ['name' => 'acme/extension', 'config' => ['vendor-dir' => $vendorDirectory]],
                JSON_THROW_ON_ERROR
            ));
        }
        mkdir($vendor . '/typo3/cms-core', 0o777, true);
        mkdir($root . '/packages/my_sitepackage', 0o777, true);
        mkdir($vendor . '/composer', 0o777, true);
        file_put_contents($vendor . '/composer/installed.json', json_encode(['packages' => [
            [
                'name' => 'typo3/cms-core',
                'type' => 'typo3-cms-framework',
                'install-path' => '../typo3/cms-core',
                'extra' => ['typo3/cms' => ['extension-key' => 'core']],
            ],
            [
                'name' => 'acme/my-sitepackage',
                'type' => 'typo3-cms-extension',
                // Composer writes install-path relative to the vendor
                // directory it actually used, so a moved one is deeper.
                'install-path' => str_repeat('../', substr_count($vendorDirectory, '/') + 2)
                    . 'packages/my_sitepackage',
                'extra' => ['typo3/cms' => ['extension-key' => 'my_sitepackage']],
            ],
            ['name' => 'symfony/console', 'type' => 'library', 'install-path' => '../symfony/console'],
        ]], JSON_THROW_ON_ERROR));

        if ($version !== '') {
            $this->typo3Version($vendor . '/typo3/cms-core', $version);
        }

        return $root;
    }

    /**
     * The platform check `composer install` writes, in the form Composer 2.9.5
     * generates it. The whole file, because the reader takes a line among
     * others out of it rather than the file's only content.
     */
    private function installedRequiring(string $root, int $versionId): void
    {
        if (!is_dir($root . '/vendor/composer')) {
            mkdir($root . '/vendor/composer', 0o777, true);
        }
        file_put_contents($root . '/vendor/composer/platform_check.php', <<<PHP
            <?php

            // platform_check.php @generated by Composer

            \$issues = array();

            if (!(PHP_VERSION_ID >= {$versionId})) {
                \$issues[] = 'Your Composer dependencies require a PHP version ">= 99.0.0". You are running ' . PHP_VERSION . '.';
            }

            if (\$issues) {
                throw new \RuntimeException(
                    'Composer detected issues in your platform: ' . implode(' ', \$issues)
                );
            }

            PHP);
    }

    private function temporaryDirectory(): string
    {
        $root = sys_get_temp_dir() . '/typo3-dev-companion-instance-' . bin2hex(random_bytes(6));
        mkdir($root, 0o777, true);
        $this->temporaryRoots[] = $root;

        return $root;
    }
}
