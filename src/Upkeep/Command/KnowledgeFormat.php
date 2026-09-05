<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Json;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * `composer cgl` for the half of this repository that is not PHP.
 *
 * It rewrites rather than reports, and what it changed is left in the working
 * tree, where `git diff` is the report — a second mode that only says what
 * would change is a mode nobody runs when the rewriting one is one keystroke
 * away.
 *
 * A path narrows it to part of the corpus, which is what a knowledge change
 * wants: the file that was edited comes out formatted, and the rest of the
 * corpus stays out of the commit.
 */
#[AsCommand(
    name: 'knowledge:format',
    description: 'write the JSON below knowledge/ in one form, and say which files that changed',
)]
final class KnowledgeFormat
{
    /**
     * @param list<string> $paths
     */
    public function __invoke(
        OutputInterface $output,
        #[Argument('the files or directories to format; the whole knowledge base when none is named')]
        array $paths = [],
    ): int {
        $files = self::targets($paths);
        if ($files === []) {
            Voice::problem($output, $paths === []
                ? 'knowledge/ holds no JSON file.'
                : sprintf('No JSON file below knowledge/ matches %s.', implode(', ', $paths)));

            return 1;
        }

        $rewritten = [];
        $unreadable = [];
        foreach ($files as $file) {
            $contents = (string) file_get_contents(Paths::root() . '/' . $file);
            try {
                $formatted = Json::format($contents);
            } catch (\JsonException $exception) {
                // Left as it is. A file the formatter cannot read is a file
                // somebody is halfway through editing, and rewriting it from
                // what could still be parsed of it would lose the other half.
                $unreadable[$file] = $exception->getMessage();

                continue;
            }

            if ($formatted !== $contents) {
                file_put_contents(Paths::root() . '/' . $file, $formatted);
                $rewritten[] = $file;
            }
        }

        Voice::ok($output, sprintf('%d of %d files rewritten.', count($rewritten), count($files)));
        foreach ($rewritten as $file) {
            Voice::row($output, $file);
        }

        if ($unreadable === []) {
            return 0;
        }
        Voice::problem($output, sprintf('%d are not JSON and were left alone:', count($unreadable)));
        foreach ($unreadable as $file => $message) {
            Voice::row($output, sprintf('%s: %s', $file, $message));
        }

        return 1;
    }

    /**
     * The files a run works on: the corpus, or the part of it that was named.
     *
     * A path is matched against the corpus rather than resolved into a file to
     * format, so this formats what it holds and nothing else. A caller who
     * names something outside it gets no match rather than a rewritten
     * composer.lock.
     *
     * @param list<string> $paths
     *
     * @return list<string>
     */
    private static function targets(array $paths): array
    {
        $files = Json::files();
        if ($paths === []) {
            return $files;
        }

        $named = [];
        foreach ($paths as $path) {
            $resolved = realpath($path) ?: realpath(Paths::root() . '/' . $path);
            if ($resolved === false) {
                continue;
            }

            $relative = substr($resolved, strlen(Paths::root()) + 1);
            foreach ($files as $file) {
                if ($file === $relative || str_starts_with($file, $relative . '/')) {
                    $named[$file] = $file;
                }
            }
        }

        return array_values($named);
    }
}
