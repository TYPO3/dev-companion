<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Command\ProseFormat;
use TYPO3\DevCompanion\Upkeep\Prose;

/**
 * `D-DOC-063`. What a sweep may reach is what nobody holds.
 *
 * Every case here leaves the sweep with nothing to rewrap, on purpose. A run
 * that had targets would rewrite the corpus of the checkout the suite runs in
 * (`R-COD-003`). What that holds is the half the decision is about, which files
 * a run considers. The rewrap itself is `ProseTest`'s.
 */
#[Decision('D-DOC-063')]
final class ProseFormatTest extends TestCase
{
    private const NAME = 'a-claim-that-is-being-worked';

    private const BRANCH = 'todo/a-claim-that-is-being-worked';

    /** @var array<int, string> */
    private array $ran = [];

    protected function setUp(): void
    {
        $this->ran = [];
    }

    protected function tearDown(): void
    {
        Checkouts::useRunner(null);
    }

    /**
     * The collision this closes from the branch's side. A claim that rewraps a
     * card another claim deletes meets that deletion on the rebase, and
     * `todo:home` stops on it — twice on 2026-08-27.
     */
    #[Test]
    public function aWorktreeSweepsOnlyWhatItsBranchChanged(): void
    {
        $this->gitThatAnswers(true, ['diff --name-only' => "src/Upkeep/Command/ProseFormat.php\n"]);
        $output = new BufferedOutput();

        $exitCode = (new ProseFormat())($output, []);
        $said = $output->fetch();

        self::assertSame(0, $exitCode);
        self::assertStringContainsString(self::BRANCH . "'s own files", $said);
        self::assertStringContainsString('0 of 0 files rewrapped.', $said);
        self::assertNotSame([], $this->matching('main...HEAD'), 'nothing asked what the branch changed');
    }

    /**
     * The same collision from the checkout's side, which is the half a rule
     * about worktrees alone would leave open. `main` rewraps a card, the claim
     * that holds it deletes it, and the rebase is a modify/delete conflict.
     */
    #[Test]
    public function whatAClaimIsHoldingIsLeftToTheClaim(): void
    {
        $this->gitThatAnswers(false, ['diff --name-only' => implode("\n", Prose::documents()) . "\n"]);
        $output = new BufferedOutput();

        $exitCode = (new ProseFormat())($output, []);
        $said = $output->fetch();

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('left to the claims holding them', $said);
        self::assertStringContainsString('0 of 0 files rewrapped.', $said);
        self::assertNotSame([], $this->matching('.worktrees/' . self::NAME), 'the standing claim was never read');
    }

    /**
     * A path is the caller's word and the command takes it as one. It narrows
     * the run to what it matches, and a match of nothing means the caller named
     * something this repository writes no prose in.
     */
    #[Test]
    public function aPathOutsideTheCorpusIsRefusedRatherThanSwept(): void
    {
        $this->gitThatAnswers(false, []);
        $output = new BufferedOutput();

        $exitCode = (new ProseFormat())($output, ['composer.json']);

        self::assertSame(1, $exitCode);
        self::assertStringContainsString('No prose file', $output->fetch());
    }

    /**
     * git, as far as this command can see it. One worktree below `.worktrees/`
     * on a claim, and this checkout as that worktree or the one it came from.
     *
     * @param array<string, string> $answers
     */
    private function gitThatAnswers(bool $linked, array $answers): void
    {
        $root = Paths::root();

        $git = self::createStub(CommandRunner::class);
        $git->method('run')->willReturnCallback(
            /**
             * @param array<int, string> $command
             *
             * @return array{ok: bool, exitCode: int, output: string, error: string}
             */
            function (array $command) use ($answers, $linked, $root): array {
                $line = str_replace($root, '', implode(' ', $command));
                $this->ran[] = $line;

                foreach ($answers as $carries => $said) {
                    if (str_contains($line, $carries)) {
                        return ['ok' => true, 'exitCode' => 0, 'output' => $said, 'error' => ''];
                    }
                }

                $said = match (true) {
                    // `linked()` reads a worktree from two answers that are
                    // different directories.
                    str_contains($line, '--absolute-git-dir') => $linked ? "/.git/worktrees/x\n" : "/.git\n",
                    str_contains($line, '--git-common-dir') => "/.git\n",
                    str_contains($line, '--abbrev-ref HEAD') => self::BRANCH . "\n",
                    str_contains($line, 'worktree list') => 'worktree ' . $root . '/.worktrees/' . self::NAME
                        . "\nbranch refs/heads/" . self::BRANCH . "\n",
                    default => '',
                };

                return ['ok' => true, 'exitCode' => 0, 'output' => $said, 'error' => ''];
            },
        );
        Checkouts::useRunner($git);
    }

    /**
     * @return array<int, string>
     */
    private function matching(string $carries): array
    {
        return array_values(array_filter(
            $this->ran,
            static fn(string $line): bool => str_contains($line, $carries),
        ));
    }
}
