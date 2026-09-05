<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Process\SystemRunner;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Site;

/**
 * The whole site on this machine, in one call, to look at.
 *
 * `documentation:prepare` is what a deployment needs and it stops at the copy —
 * `D-DOC-028`. This is the other half: it fetches the renderer into a gitignored
 * directory beside the build, renders, runs the theme's finish step and says
 * where to read the result. The renderer is fetched where it is missing and not
 * otherwise, because a preview is run again after every paragraph, and `.site/`
 * is deleted to resolve it fresh. Every step that leaves this process is
 * printed as the command a person could have typed, because a preview that dies
 * has to say which step.
 *
 * `--watch` is the same render, again after every save: the tree is looked at
 * once a second and a render is run when a file in it has moved. Polling
 * rather than inotify, because the extension is not on every PHP and a watcher
 * that needs one installed first is one more step in a recipe that was six.
 */
#[AsCommand(
    name: 'documentation:preview',
    description: 'render the whole site on this machine and say where to read it',
)]
final class DocumentationPreview
{
    /** The branch is named because the theme publishes no tagged release yet. */
    private const THEME = 'typo3/soul-guides-theme:dev-main';

    /** Where the renderer is fetched to, below the build directory it renders into. */
    private const RENDERER = 'renderer';

    private readonly CommandRunner $runner;

    /**
     * What a watch does between two renders: waits, then says which files
     * changed, or null once there is nothing left to wait for.
     *
     * The real one sleeps and never says null, so a watch ends with Ctrl-C. A
     * test hands in one that names a change without touching the checkout
     * (`R-COD-003`).
     *
     * @var \Closure(): ?list<string>
     */
    private readonly \Closure $look;

    /** @param ?\Closure(): ?list<string> $look */
    public function __construct(?CommandRunner $runner = null, ?\Closure $look = null)
    {
        $this->runner = $runner ?? new SystemRunner();
        $this->look = $look ?? self::looking();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument('where the site is built; `documentation/guides.xml` renders the default one')]
        string $into = Site::ROOT,
        #[Option('render again whenever a file below documentation/ or skills/ is saved, until Ctrl-C')]
        bool $watch = false,
    ): int {
        $rendered = $this->render($output, $into);
        if ($rendered) {
            // Over a server rather than by opening the file: the search fetches
            // its index beside the pages, which a browser refuses over `file://`.
            $output->writeln(sprintf('read it: php -S localhost:8000 -t %s', $into . '/html'));
        }
        if (!$watch) {
            return $rendered ? 0 : 1;
        }

        // A render that failed does not end the watch: a directive saved half
        // typed fails, and the save that finishes it is what the watch is for.
        $output->writeln(sprintf('watching %s/ and skills/', Site::SOURCE));
        while (($changed = ($this->look)()) !== null) {
            if ($changed === []) {
                continue;
            }
            $output->writeln(sprintf('changed: %s', implode(', ', $changed)));
            $this->render($output, $into);
        }

        return 0;
    }

    /** The copy, the renderer where there is none, the render and the finish, in that order. */
    private function render(OutputInterface $output, string $into): bool
    {
        if ((new DocumentationPrepare())($output, $into) !== 0) {
            return false;
        }

        $renderer = $into . '/' . self::RENDERER;
        $steps = [
            ...$this->fetch($renderer),
            [$renderer . '/vendor/bin/guides', '--no-progress', '-c', Site::SOURCE],
            ['node', $renderer . '/vendor/typo3/soul-guides-theme/resources/dist/soul-finish.js', $into . '/html'],
        ];
        foreach ($steps as $step) {
            if (!$this->step($output, $step)) {
                return false;
            }
        }

        return true;
    }

    /**
     * The look a watch takes when nothing hands one in: a second's sleep, then
     * every file whose stamp is not the one seen last time, a removed file
     * included.
     *
     * @return \Closure(): list<string>
     */
    private static function looking(): \Closure
    {
        $seen = Site::stamps();

        return static function () use (&$seen): array {
            sleep(1);
            $now = Site::stamps();
            $changed = array_keys(array_diff_assoc($now, $seen) + array_diff_key($seen, $now));
            $seen = $now;

            return $changed;
        };
    }

    /**
     * What it takes to have a renderer at that path, or nothing where there is
     * one already.
     *
     * Composer has no way to require into an empty directory, so the manifest
     * is written by `init` before the require that fills it in. It is not this
     * repository's to keep: what asks for the theme is here, and the theme
     * brings phpDocumentor Guides with it.
     *
     * @return list<list<string>>
     */
    private function fetch(string $renderer): array
    {
        // The build directory is an argument and may be anywhere, which is what
        // lets the suite drive this without writing into the checkout.
        $where = str_starts_with($renderer, '/') ? $renderer : Paths::root() . '/' . $renderer;
        if (is_file($where . '/vendor/bin/guides')) {
            return [];
        }

        if (!is_dir($where)) {
            mkdir($where, 0777, true);
        }
        $directory = '--working-dir=' . $renderer;

        return [
            ['composer', 'init', $directory, '--no-interaction', '--name=typo3/dev-companion-render'],
            ['composer', 'require', $directory, '--no-interaction', '--no-progress', self::THEME],
        ];
    }

    /**
     * One step, with whatever it said either way.
     *
     * A step that succeeded is not silent: the renderer's warnings and the
     * finish step's tally are what a person reads to see the render was the one
     * they meant.
     *
     * @param list<string> $command
     */
    private function step(OutputInterface $output, array $command): bool
    {
        $output->writeln(implode(' ', $command));
        $result = $this->runner->run($command, Paths::root());
        if ($result['exitCode'] !== 0) {
            Cli::errors($output)->writeln(sprintf(
                "%s failed:\n%s",
                implode(' ', $command),
                trim($result['output'] . $result['error']),
            ));

            return false;
        }

        $said = trim($result['output'] . $result['error']);
        if ($said !== '') {
            $output->writeln($said);
        }

        return true;
    }
}
