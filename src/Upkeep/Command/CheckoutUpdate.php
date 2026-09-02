<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\PinnedPackage;

/**
 * Keeps one TYPO3 core checkout per covered version below .checkouts/, so that
 * verifying what a statement holds for is done against this repository's own
 * sources rather than against whatever checkout happens to be on the machine.
 *
 * The versions come from knowledge/versions.json and from nowhere else. One
 * treeless clone carries the history; each covered branch is a worktree of it,
 * so four lines cost one object store.
 *
 * The packages the core pins rather than contains are kept here too, because a
 * statement about one of them is verified against a tag of that package rather
 * than against a core branch — `D-KNW-106`, and `Upkeep\PinnedPackage` for
 * which line pairs with which major.
 */
#[AsCommand(
    name: 'checkouts:update',
    description: 'create what is missing, update what is there',
)]
final class CheckoutUpdate
{
    /** One worktree per covered branch, fetched to its tip. */
    public function __invoke(OutputInterface $output): int
    {
        $checkouts = Checkouts::directory();
        $mirror = $checkouts . '/typo3.git';

        if (!is_dir($checkouts) && !mkdir($checkouts, 0o777, true) && !is_dir($checkouts)) {
            Cli::errors($output)->writeln(sprintf('Cannot create %s', $checkouts));

            return 2;
        }

        if (!self::mirror($output, $mirror, Checkouts::REPOSITORY, false)) {
            return 1;
        }

        $failed = 0;
        foreach (Versions::covered() as $version) {
            $branch = $version['branch'];
            $path = $checkouts . '/' . $branch;
            $output->writeln(sprintf('%s (TYPO3 v%d, %s)', $branch, $version['major'], $version['status']));

            if (self::worktree($output, $mirror, $path, $branch) !== 0) {
                ++$failed;
                continue;
            }

            $output->writeln(sprintf('    %s', Checkouts::revision($path)));
        }

        foreach (PinnedPackage::all() as $package) {
            $failed += self::updatePinned($output, $checkouts, $package);
        }

        if ($failed > 0) {
            $output->writeln('');
            $output->writeln(sprintf('%d checkout(s) failed.', $failed));

            return 1;
        }

        $output->writeln('');
        $output->writeln('Ready. Verify a statement against .checkouts/<branch>, on both sides of its boundary.');

        return 0;
    }

    /**
     * One worktree per release line the covered majors pin, checked out at that
     * line's newest tag.
     *
     * Two majors pinning the same line share one worktree — the core pins 9.x on
     * both 13.4 and 14.3 — so what is created follows the pins rather than the
     * version list.
     */
    private static function updatePinned(OutputInterface $output, string $checkouts, PinnedPackage $package): int
    {
        $output->writeln('');
        $output->writeln(sprintf('%s (%s)', $package->package, $package->subject));
        $mirror = $package->mirror($checkouts);
        if (!self::mirror($output, $mirror, $package->repository, true)) {
            return 1;
        }

        $failed = 0;
        $created = [];
        foreach ($package->pairing($checkouts) as $pair) {
            $output->writeln(sprintf(
                '  %-6s %-9s %s',
                $pair['branch'],
                $pair['constraint'] === '' ? 'no pin' : $pair['constraint'],
                $pair['ref'] ?? 'names no single release line',
            ));
            if ($pair['ref'] === null) {
                ++$failed;
                continue;
            }
            if (isset($created[$pair['ref']])) {
                continue;
            }

            $created[$pair['ref']] = true;
            if (self::worktree($output, $mirror, $pair['path'], $pair['ref']) !== 0) {
                ++$failed;
            }
        }

        return $failed;
    }

    /** One treeless bare clone, created where it is missing and fetched to its refs. */
    private static function mirror(OutputInterface $output, string $path, string $repository, bool $tags): bool
    {
        if (!is_dir($path)) {
            $output->writeln(sprintf('Cloning %s (treeless; blobs are fetched as they are read)', $repository));
            // --filter=blob:none keeps the history and the trees and leaves the file
            // contents on the server until something asks for them. A full clone of the
            // core is several gigabytes; this is a fraction of it, and the worktrees
            // below still read like ordinary checkouts.
            if (self::git($output, ['git', 'clone', '--bare', '--filter=blob:none', $repository, $path]) !== 0) {
                Cli::errors($output)->writeln('Clone failed.');

                return false;
            }
        }

        // A bare clone keeps the branches as its own refs and configures no refspec, so
        // a later fetch would update nothing. This is what makes the mirror updatable.
        Checkouts::run(['git', '-C', $path, 'config', 'remote.origin.fetch', '+refs/heads/*:refs/heads/*']);

        $output->writeln(sprintf('Fetching %s', basename($path)));
        $command = ['git', '-C', $path, 'fetch', '--quiet', '--force', '--prune', 'origin'];
        if ($tags) {
            // The release lines are read at their tags, so a line that gained one
            // since the last update is only there if the tags come along.
            $command[] = '--tags';
        }

        return self::git($output, $command) === 0;
    }

    /** One worktree of a mirror, detached on a ref. */
    private static function worktree(OutputInterface $output, string $mirror, string $path, string $ref): int
    {
        if (!is_dir($path)) {
            return self::git($output, ['git', '-C', $mirror, 'worktree', 'add', '--quiet', '--detach', '--force', $path, $ref]);
        }

        // Detached on the ref: these are read, never committed to, so there is
        // nothing to merge and nothing to lose.
        return self::git($output, ['git', '-C', $path, 'checkout', '--quiet', '--force', '--detach', $ref]);
    }

    /**
     * One git command, with whatever it had to say placed under the step it
     * belongs to.
     *
     * @param list<string> $command
     */
    private static function git(OutputInterface $output, array $command): int
    {
        [$exitCode, $said] = Checkouts::run($command);
        if (trim($said) !== '') {
            $output->writeln('    ' . str_replace("\n", "\n    ", rtrim($said)));
        }

        return $exitCode;
    }
}
