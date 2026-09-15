<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Result\Unsupported;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;

/**
 * What the answer to a question this server cannot take here says.
 *
 * The cause says what stopped the call. Whether that is a precondition the
 * session is about to satisfy or a dead end is the repository's state. This is
 * where the two come apart.
 */
final class UnsupportedTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Instance::ROOT_VARIABLE);
        Instance::discoverFrom(null);
        Typo3Cli::useRunner(null);
        Typo3Cli::forget();
    }

    #[Decision('D-ANS-105')]
    #[Test]
    public function aRefusalBeforeTheInstallSaysThatTheStateEnds(): void
    {
        // The state behind `feedback/2026-08-24-140259`: an extension
        // repository that requires `typo3/cms-core`, before `composer install`
        // had run in it. The session read `cause: no-installation` as
        // permanent, and a core checkout it happened to have answered the two
        // hours after it.
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/usercentrics',
            'type' => 'typo3-cms-extension',
            'require' => ['typo3/cms-core' => '^13.4 || ^14.0'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        $answer = Unsupported::because('no TYPO3 installation was found whose core package ships the changelog');

        self::assertSame(Unsupported::NO_INSTALLATION, $answer->data['unsupported']['cause']);
        self::assertSame(Unsupported::REPOSITORY_NOT_INSTALLED, $answer->data['unsupported']['repositoryState']);
        self::assertStringContainsString('once composer install has run', $answer->text);
    }

    #[Decision('D-ANS-105')]
    #[Test]
    public function aRepositoryDeclaringNoTypo3IsNotToldAnInstallIsPending(): void
    {
        // The walk goes up twelve directories, so whatever PHP repository a
        // session stands below is one this may not report as short of an
        // install.
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/toolkit',
            'type' => 'library',
            'require' => ['symfony/finder' => '^7.4'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        $answer = Unsupported::because('no TYPO3 installation was found to ask');

        self::assertSame(Unsupported::NO_INSTALLATION, $answer->data['unsupported']['cause']);
        self::assertSame(Unsupported::REPOSITORY_UNDECLARED, $answer->data['unsupported']['repositoryState']);
        self::assertStringNotContainsString('composer install', $answer->text);
    }

    #[Decision('D-ANS-105')]
    #[Test]
    public function anInstalledRepositoryIsNotToldAnInstallIsPending(): void
    {
        // A console that does not answer is a state that ends with no second
        // install. A refusal that read as a prescription would have the caller
        // install a repository that is already installed.
        $runner = self::createStub(CommandRunner::class);
        $runner->method('locate')->willReturn(null);
        Typo3Cli::useRunner($runner);
        Instance::discoverFrom($this->composerProject());
        Typo3Cli::forget();

        $answer = Unsupported::because('the console answered with something other than JSON');

        self::assertSame(Unsupported::NOT_ANSWERING, $answer->data['unsupported']['cause']);
        self::assertSame(Unsupported::REPOSITORY_INSTALLED, $answer->data['unsupported']['repositoryState']);
        self::assertStringNotContainsString('composer install', $answer->text);
    }

    #[Decision('D-ANS-105')]
    #[Test]
    public function aNamedRootThatCouldNotBeUsedClaimsNothingAboutTheRepository(): void
    {
        // The walk searched nothing, so it saw nothing — the directory the
        // session stands in included, whatever it declares.
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/usercentrics',
            'type' => 'typo3-cms-extension',
        ], JSON_THROW_ON_ERROR));
        putenv(Instance::ROOT_VARIABLE . '=' . $root . '/somewhere-else');
        Instance::discoverFrom($root);

        $answer = Unsupported::because('no TYPO3 installation was found to ask');

        self::assertSame(Unsupported::MISCONFIGURED, $answer->data['unsupported']['cause']);
        self::assertNull($answer->data['unsupported']['repositoryState']);
    }
}
