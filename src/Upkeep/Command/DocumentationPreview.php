<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Process\SystemRunner;
use TYPO3\DevCompanion\Upkeep\Site;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * The whole site on this machine, in one call, to look at.
 *
 * `documentation:prepare` is what a deployment needs and it stops at the copy —
 * `D-DOC-028`. This is the other half: it fetches the renderer into a gitignored
 * directory beside the build, renders, runs the theme's finish step and says
 * where to read the result. The renderer is fetched where it is missing and not
 * otherwise, because a preview is run again after every paragraph, and `.site/`
 * is deleted to resolve it fresh.
 *
 * What is printed is one row per step with what it produced, counted here
 * rather than read out of what the step said, and a bar over the steps while
 * one of them runs. What a step said is kept for `-v` and for the step that
 * failed, with one exception: what a step wrote to its error stream is shown
 * every time, because that is where the renderer puts its warnings and a
 * warning is what an author is rendering to see.
 *
 * `--watch` serves the site and is the same render again after every save: the
 * tree is looked at once a second and a render is run when a file in it has
 * moved. Polling rather than inotify, because the extension is not on every
 * PHP and a watcher that needs one installed first is one more step in a
 * recipe that was six. The server is PHP's own, as a child that Ctrl-C takes
 * down with the watch.
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

    /** The width of the step column, so what a step produced lines up below the heading. */
    private const STEP = 8;

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
        #[Option('serve the site and render again whenever a file below documentation/ or skills/ is saved, until Ctrl-C')]
        bool $watch = false,
        #[Option('the port the watch serves on')]
        int $port = 8000,
    ): int {
        Voice::heading($output, sprintf('Rendering %s/ into %s', Site::SOURCE, $into . '/html'));
        $rendered = $this->render($output, $into);
        if (!$watch) {
            // Over a server rather than by opening the file: the search fetches
            // its index beside the pages, which a browser refuses over `file://`.
            if ($rendered) {
                Voice::note($output, sprintf('read it: php -S localhost:%d -t %s', $port, $into . '/html'));
            }

            return $rendered ? 0 : 1;
        }

        $stop = $this->runner->start([PHP_BINARY, '-S', 'localhost:' . $port, '-t', $into . '/html'], Paths::root());
        if (is_string($stop)) {
            Voice::problem($output, sprintf('Nothing serves on port %d: %s', $port, $stop));

            return 1;
        }

        // Ctrl-C reaches the server through the terminal's process group; a
        // kill reaches this process alone, and the server would outlive it.
        if (function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);
            $down = static function () use ($stop): void {
                $stop();
                exit(130);
            };
            pcntl_signal(SIGINT, $down);
            pcntl_signal(SIGTERM, $down);
        }

        // A render that failed does not end the watch: a directive saved half
        // typed fails, and the save that finishes it is what the watch is for.
        Voice::heading($output, sprintf('Serving http://localhost:%d/ and watching %s/ and skills/ — Ctrl-C stops both', $port, Site::SOURCE));
        while (($changed = ($this->look)()) !== null) {
            if ($changed === []) {
                continue;
            }
            $output->writeln(sprintf('%s %s', Voice::dim(date('H:i:s')), implode(', ', $changed)));
            $this->render($output, $into);
        }
        $stop();

        return 0;
    }

    /**
     * The copy, the renderer where there is none, the render and the finish,
     * in that order, with the bar standing on the step that is running.
     *
     * The rendered pages are taken away before the renderer writes new ones:
     * it removes nothing itself, and a page renamed since the last render is
     * otherwise served on — and read by the finish step, which looped over
     * three stale ones for two minutes before this was cleared.
     */
    private function render(OutputInterface $output, string $into): bool
    {
        $started = microtime(true);
        $renderer = $into . '/' . self::RENDERER;
        $fetch = $this->fetch($renderer);
        $bar = Voice::progress($output, 3 + ($fetch === [] ? 0 : 1));
        $bar->setMessage(str_pad('copy', self::STEP));
        $bar->start();

        $built = Site::build($into . '/source');
        $bar->clear();
        foreach ($built['removed'] as $removed) {
            Voice::row($output, sprintf('%s %s', Voice::key('removed', self::STEP), sprintf('%s, which %s/ no longer has', $removed, Site::SOURCE)));
        }
        self::step($output, 'copy', sprintf('%d files into %s', count($built['written']), $into . '/source'));
        $bar->advance();

        if ($fetch !== []) {
            $bar->setMessage(str_pad('fetch', self::STEP));
            $bar->display();
            foreach ($fetch as $command) {
                if (!$this->run($output, $bar, 'fetch', $command)) {
                    return false;
                }
            }
            $bar->clear();
            self::step($output, 'fetch', sprintf('the renderer, once, into %s', $renderer));
            $bar->advance();
        }

        $bar->setMessage(str_pad('render', self::STEP));
        $bar->display();
        Site::clear($into . '/html');
        if (!$this->run($output, $bar, 'render', [$renderer . '/vendor/bin/guides', '--no-progress', '-c', Site::SOURCE])) {
            return false;
        }
        $bar->clear();
        self::step($output, 'render', sprintf('%d pages', self::pages($into . '/html')));
        $bar->advance();

        $bar->setMessage(str_pad('finish', self::STEP));
        $bar->display();
        $finish = ['node', $renderer . '/vendor/typo3/soul-guides-theme/resources/dist/soul-finish.js', $into . '/html'];
        if (!$this->run($output, $bar, 'finish', $finish)) {
            return false;
        }
        $bar->clear();
        self::step($output, 'finish', 'the search index and the theme beside them');
        $bar->finish();
        $bar->clear();
        Voice::ok($output, sprintf('rendered in %.1fs', microtime(true) - $started));

        return true;
    }

    /** One row: the step that ran, and what it produced. */
    private static function step(OutputInterface $output, string $name, string $produced): void
    {
        Voice::row($output, sprintf('%s %s', Voice::key($name, self::STEP), $produced));
    }

    /** How many pages the renderer left, read off the directory rather than out of what it said. */
    private static function pages(string $html): int
    {
        $where = str_starts_with($html, '/') ? $html : Paths::root() . '/' . $html;

        return is_dir($where) ? count(Finder::create()->files()->in($where)->name('*.html')) : 0;
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
     * One command of a step: quiet where it succeeded, quoted whole where it
     * failed.
     *
     * The command is printed under `-v`, as what a person could have typed, and
     * a failure prints it whatever the verbosity, because a preview that dies
     * has to say which step.
     *
     * @param list<string> $command
     */
    private function run(OutputInterface $output, ProgressBar $bar, string $name, array $command): bool
    {
        $typed = implode(' ', $command);
        if ($output->isVerbose()) {
            $bar->clear();
            Voice::row($output, Voice::dim($typed));
            $bar->display();
        }
        $result = $this->runner->run($command, Paths::root());
        if ($result['exitCode'] !== 0) {
            $bar->clear();
            Voice::problem($output, sprintf('%s failed: %s', $name, $typed));
            Voice::problem($output, trim($result['output'] . $result['error']));

            return false;
        }

        $said = $output->isVerbose() ? $result['output'] . $result['error'] : $result['error'];
        if (trim($said) !== '') {
            $bar->clear();
            Voice::row($output, Voice::dim(str_replace("\n", "\n  ", trim($said))));
            $bar->display();
        }

        return true;
    }
}
