<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Fixture;
use TYPO3\DevCompanion\Upkeep\ToolAnswers;
use TYPO3\DevCompanion\Upkeep\ToolSurface;

/**
 * Calls every tool once and writes down what came back.
 *
 * `tools:index` renders the surface, which is derivable and therefore checked.
 * This is the other half — what a filled answer looks like — and half of those
 * answers belong to the installation being read, so it is evidence rather than
 * a derivation. Two roots, because neither the newest covered core checkout
 * below `.checkouts/` nor the installation `Fixture` writes fills the surface
 * alone, and neither of them is somebody's own site, which only the machine
 * holding it could record again — `D-DOC-006`. A checkout carrying anything
 * `checkouts:update` did not put there is refused — `D-DOC-034`.
 */
#[AsCommand(
    name: 'tools:record',
    description: 'call every tool against a checkout and write what came back to documentation/usage/',
)]
final class ToolRecord
{
    /** @param list<string> $tools */
    public function __invoke(
        OutputInterface $output,
        #[Argument('the installation to answer from, defaulting to the newest core checkout below .checkouts/')]
        ?string $root = null,
        #[Argument('the date the recording carries, defaulting to today in UTC')]
        ?string $today = null,
        #[Argument('the tools to answer for, defaulting to all of them')]
        array $tools = [],
    ): int {
        $root ??= self::newestCheckout();
        if (!is_dir($root)) {
            Cli::errors($output)->writeln(sprintf('%s is not a directory — run bin/cli checkouts:update, or name an installation.', $root));

            return 2;
        }

        Instance::discoverFrom($root);
        Typo3Cli::forget();
        $found = Instance::root();
        if ($found === null) {
            Cli::errors($output)->writeln(sprintf('No TYPO3 installation was found from %s, so there is nothing to record against.', $root));

            return 2;
        }

        $carried = self::carriedBeyondTheIndex($found);
        if ($carried !== []) {
            Cli::errors($output)->writeln(sprintf(
                "%s carries what bin/cli checkouts:update did not put there: %s.\n"
                . 'A recording is evidence about the checkout that command makes, and this is no longer it — an '
                . "installed console answers from a database nothing here creates.\n"
                . 'Take it back with "git -C %s clean -xdff", or name an installation to record from.',
                $found,
                self::shortly($carried),
                $found,
            ));

            return 2;
        }

        $output->writeln(sprintf('Answering from %s (TYPO3 %s)', $found, Instance::typo3Version() ?? 'unknown'));

        $installation = $this->consoleAnswering($output, $found);
        // Trimmed rather than defaulted on null alone: naming the tools means
        // passing this argument, and the empty string that gets a caller past
        // it wrote a page saying "Recorded on ".
        $day = trim((string) $today) === '' ? ToolAnswers::day() : trim((string) $today);
        $pages = ToolAnswers::rendered($day, $found, $installation, $tools);
        if (!is_dir(ToolSurface::directory())) {
            mkdir(ToolSurface::directory(), 0777, true);
        }
        foreach ($pages as $file => $contents) {
            file_put_contents($file, $contents);
        }

        // Only where the whole surface was written. Named tools leave every
        // other page alone, and a page nothing wrote this run is not a page
        // nothing writes any more.
        if ($tools === []) {
            foreach (ToolSurface::written() as $written) {
                if (!isset($pages[$written->getPathname()])) {
                    unlink($written->getPathname());
                    $output->writeln(sprintf('removed %s, which the registry no longer offers', $written->getFilename()));
                }
            }
        }

        $output->writeln(sprintf(
            '%s — %d pages',
            substr(ToolSurface::directory(), strlen(Paths::root()) + 1),
            count($pages) - count(ToolSurface::standingPages()),
        ));

        return 0;
    }

    /**
     * The fixture installation, written and then asked whether its console
     * answers, and null with a reason where it does not.
     *
     * Asked rather than assumed: what resolves the console is an interpreter on
     * this machine satisfying what the installation declares, and a machine
     * that has none is one where this root answers nothing. A silent absence is
     * the failure that guards against — the pages would come back with one
     * answer per call, and a reader would take the console-answering shape as
     * still missing rather than as not recorded today.
     */
    private function consoleAnswering(OutputInterface $output, string $primary): ?string
    {
        $path = Fixture::write();
        if (realpath($path) === realpath($primary)) {
            return null;
        }

        Instance::discoverFrom($path);
        Typo3Cli::forget();
        if (!Typo3Cli::isAvailable()) {
            $output->writeln(sprintf(
                "The fixture installation is written and its console does not answer here: %s\n"
                . '    Nothing records what a booted TYPO3 answers, so those pages carry one answer per call.',
                Typo3Cli::reason(),
            ));

            return null;
        }

        $output->writeln(sprintf('Answering the installation-backed tools a second time from %s', $path));

        return $path;
    }

    /**
     * What a core checkout below `.checkouts/` carries beyond its index, and
     * nothing at all for any other root.
     *
     * The first root is one of ours, and the recording is evidence about the
     * checkout `checkouts:update` makes: run `composer install` in it and the
     * same calls record a Doctrine exception about a database nothing here
     * creates, which is an answer no reader can produce again — `D-DOC-034`. A
     * root somebody named is theirs, and this says nothing about it.
     *
     * @return array<int, string>
     */
    private static function carriedBeyondTheIndex(string $root): array
    {
        $checkouts = (string) realpath(Checkouts::directory());
        $root = (string) (realpath($root) ?: $root);
        // Resolved on both sides: a worktree reaches the checkouts through a
        // symlink, so the recording made in one would otherwise ask nothing.
        if ($checkouts === '' || !str_starts_with($root, $checkouts . '/')) {
            return [];
        }

        return Checkouts::beyondIndex($root);
    }

    /**
     * A list of paths, short enough to sit in one sentence. What it names is
     * where to look, and a reader who wants all of it runs git.
     *
     * @param array<int, string> $carried
     */
    private static function shortly(array $carried): string
    {
        $shown = array_slice($carried, 0, 5);
        $rest = count($carried) - count($shown);

        return implode(', ', $shown) . ($rest > 0 ? sprintf(', and %d more', $rest) : '');
    }

    /**
     * The newest covered branch that is released, which is the version a client
     * is most likely to be on. `main` is covered too and is a development line:
     * recording against it would make the sample say `15.0.0-dev`, which is
     * true of nobody's installation.
     */
    private static function newestCheckout(): string
    {
        $released = array_values(array_filter(
            Versions::covered(),
            static fn(array $version): bool => $version['status'] !== 'development',
        ));
        $newest = $released === [] ? Versions::covered() : $released;

        return Checkouts::directory() . '/' . ($newest[count($newest) - 1]['branch'] ?? '');
    }
}
