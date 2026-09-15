<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Checkouts;

/**
 * What a core checkout carries beyond what `checkouts:update` put there.
 *
 * `tools:record` refuses one that carries anything, because the record is
 * evidence about the checkout that command makes — `D-DOC-034`. What can be
 * wrong here is the read of git's answer, so that is what this holds. The git
 * call itself is the seam a test hands a runner to.
 */
final class CheckoutsTest extends TestCase
{
    #[After]
    protected function forgetTheRunner(): void
    {
        Checkouts::useRunner(null);
    }

    #[Decision('D-DOC-034')]
    #[Test]
    public function everyEntryGitReportsIsCarried(): void
    {
        // What `composer install` leaves in a core checkout, as git reported it
        // in .checkouts/14.3 on 2026-08-18. All six stand in the ignore list.
        // That is why the status call carries --ignored and why a plain one
        // calls this tree clean — `D-DOC-034`.
        $this->answering(0, "!! .cache/\n!! bin/\n!! index.php\n!! typo3/sysext/core/bin/\n!! typo3temp/\n!! vendor/\n");

        self::assertSame(
            ['.cache/', 'bin/', 'index.php', 'typo3/sysext/core/bin/', 'typo3temp/', 'vendor/'],
            Checkouts::beyondIndex('/checkouts/14.3'),
        );
    }

    #[Test]
    public function aCheckoutAsTheCommandMakesItCarriesNothing(): void
    {
        $this->answering(0, '');

        self::assertSame([], Checkouts::beyondIndex('/checkouts/14.3'));
    }

    #[Decision('D-DOC-034')]
    #[Test]
    public function aGitThatCannotAnswerReportsNoDifference(): void
    {
        // Not a repository, or no git at all. Either way git found nothing to
        // carry, and a refusal on that would stop a record over a question
        // nobody asked — `D-DOC-034`.
        $this->answering(128, "fatal: not a git repository\n");

        self::assertSame([], Checkouts::beyondIndex('/somewhere/else'));
    }

    #[Decision('D-DOC-034')]
    #[Test]
    public function bothKindsOfChangeAreCarried(): void
    {
        // A tracked file somebody edited breaks the record exactly as an
        // installed console does: neither is in what checkouts:update makes —
        // `D-DOC-034`.
        $this->answering(0, " M composer.json\n?? notes.md\n!! vendor/\n");

        self::assertSame(['composer.json', 'notes.md', 'vendor/'], Checkouts::beyondIndex('/checkouts/14.3'));
    }

    private function answering(int $exitCode, string $output): void
    {
        $git = self::createStub(CommandRunner::class);
        $git->method('run')->willReturn(
            ['ok' => $exitCode === 0, 'exitCode' => $exitCode, 'output' => $output, 'error' => ''],
        );
        Checkouts::useRunner($git);
    }
}
