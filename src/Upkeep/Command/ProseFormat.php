<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Prose;
use TYPO3\DevCompanion\Upkeep\Todo;
use TYPO3\DevCompanion\Upkeep\Voice;
use TYPO3\DevCompanion\Upkeep\Wrap;

/**
 * `knowledge:format` for the half of this repository that is prose.
 *
 * What it is for is the paragraph a rename left ragged. A word swept out of a
 * hundred files leaves a hundred short lines behind it, and rewrapping them was
 * a throwaway script every time — one that nobody reviewed, that knew nothing
 * about a code fence, and that was written again from memory on the next
 * rename.
 *
 * It rewrites rather than reports, like `knowledge:format`: what it changed is
 * in the working tree, where `git diff` is the report. `prose:check` is the
 * other half and stays a report, because a long sentence can be the right one
 * and no formatter can tell.
 *
 * A path narrows it to the files a change touched, which is what a commit
 * wants. Named nothing it sweeps, and a sweep reaches what nobody is holding:
 * in a worktree the branch's own files, and in the checkout `main` stands on
 * the corpus minus what the standing claims changed — `D-DOC-063`.
 */
#[AsCommand(
    name: 'prose:format',
    description: 'rewrap the prose this repository writes at column ' . Wrap::COLUMN . ', and say which files that changed',
)]
final class ProseFormat
{
    /**
     * @param list<string> $paths
     */
    public function __invoke(
        OutputInterface $output,
        #[Argument('the files or directories to rewrap; the whole prose corpus when none is named')]
        array $paths = [],
    ): int {
        $corpus = array_values(array_filter(Prose::documents(), self::isWrittenByHand(...)));
        if ($paths === []) {
            $files = self::sweep($output, $corpus);
        } else {
            $files = self::named($paths, $corpus);
            if ($files === []) {
                Voice::problem($output, sprintf(
                    'No prose file this repository writes about itself matches %s.',
                    implode(', ', $paths),
                ));

                return 1;
            }
        }

        $rewritten = [];
        foreach ($files as $file) {
            $contents = (string) file_get_contents(Paths::root() . '/' . $file);
            // Which markup it is, asked of the file rather than of the
            // directory: `documentation/` is reStructuredText and every
            // other working directory is markdown — `D-DOC-029`.
            $wrapped = str_ends_with($file, '.rst') ? Wrap::rst($contents) : Wrap::document($contents);
            if ($wrapped !== $contents) {
                file_put_contents(Paths::root() . '/' . $file, $wrapped);
                $rewritten[] = $file;
            }
        }

        Voice::ok($output, sprintf('%d of %d files rewrapped.', count($rewritten), count($files)));
        foreach ($rewritten as $file) {
            Voice::row($output, $file);
        }

        return 0;
    }

    /**
     * The part of the corpus a caller named.
     *
     * Matched against `Prose::documents()` rather than resolved into a file to
     * rewrite, so this touches the prose this repository writes about itself
     * and nothing else. `feedback/` is outside it on purpose — a feedback is a
     * session's report, and reformatting somebody else's report is an edit to
     * evidence.
     *
     * @param list<string> $paths
     * @param list<string> $corpus
     *
     * @return list<string>
     */
    private static function named(array $paths, array $corpus): array
    {
        $named = [];
        foreach ($paths as $path) {
            $resolved = realpath($path) ?: realpath(Paths::root() . '/' . $path);
            if ($resolved === false) {
                continue;
            }

            $relative = substr($resolved, strlen(Paths::root()) + 1);
            foreach ($corpus as $file) {
                if ($file === $relative || str_starts_with($file, $relative . '/')) {
                    $named[$file] = $file;
                }
            }
        }

        return array_values($named);
    }

    /**
     * The corpus, minus every file somebody has in hand — `D-DOC-063`.
     *
     * A rewrap of a file another branch is holding is a conflict whichever side
     * lands first: the branch that only rewrapped it meets the card `main`
     * deleted, and the branch that deleted its card meets the sweep that
     * rewrapped it. So a sweep reaches what nobody is holding, and a claim
     * rewraps its own files and leaves the rest to the checkout `main` stands
     * on.
     *
     * @param list<string> $corpus
     *
     * @return list<string>
     */
    private static function sweep(OutputInterface $output, array $corpus): array
    {
        $root = Paths::root();
        if (Todo::linked($root)) {
            $branch = Todo::standing($root);
            Voice::note($output, sprintf(
                'A worktree rewraps what it changed, so this is %s and not the corpus.',
                $branch === '' ? 'what is in hand here' : $branch . "'s own files",
            ));

            return array_values(array_intersect($corpus, self::changed($root)));
        }

        $held = [];
        foreach (array_keys(Todo::worktrees($root)) as $name) {
            $held = [...$held, ...self::changed($root . '/.worktrees/' . $name)];
        }

        $files = array_values(array_diff($corpus, $held));
        if ($files !== $corpus) {
            Voice::note($output, sprintf(
                '%d files are in hand and left to the claims holding them.',
                count($corpus) - count($files),
            ));
        }

        return $files;
    }

    /**
     * What a checkout has changed against `main`, committed or not.
     *
     * The uncommitted half is what makes this the answer while a session is
     * still working: a decision file it has written and not added is one nobody
     * else may rewrap either, and it arrives with the porcelain.
     *
     * @return list<string>
     */
    private static function changed(string $path): array
    {
        $files = [];
        [$read, $said] = Checkouts::run(['git', '-C', $path, 'diff', '--name-only', 'main...HEAD']);
        if ($read === 0) {
            $files = preg_split('/\R/', trim($said)) ?: [];
        }

        [$read, $said] = Checkouts::run(['git', '-C', $path, 'status', '--porcelain']);
        foreach ($read === 0 ? preg_split('/\R/', trim($said)) ?: [] : [] as $line) {
            $file = trim(substr($line, 2));
            // A rename says `old -> new`, and the file that is there is the second.
            $files[] = str_contains($file, ' -> ') ? substr($file, (int) strpos($file, ' -> ') + 4) : $file;
        }

        return array_values(array_unique(array_filter($files, static fn(string $file): bool => $file !== '')));
    }

    /**
     * Whether a file has a writer already.
     *
     * Not because it is generated — the column is no longer the difference,
     * since `ToolSurface` wraps through `Wrap` like everything else here. It is
     * that a generator decides what a line is: `tools:index` keeps the
     * annotations of a tool on one line however wide, because they are one
     * fact. A formatter cannot know that, so it wraps them, and the next
     * `tools:index` puts them back — the file then changes in every commit and
     * says nothing by changing.
     *
     * A recording is out for a second reason. Every block below a page's
     * `## Answered` heading is what a client received, and rewrapping it makes
     * the page claim an answer arrived in lines it did not.
     */
    private static function isWrittenByHand(string $file): bool
    {
        if (str_starts_with($file, 'documentation/server/tools/')) {
            return false;
        }

        return basename($file) !== 'readme.md'
            || (!str_starts_with($file, 'decisions/') && !str_starts_with($file, 'requirements/'));
    }
}
