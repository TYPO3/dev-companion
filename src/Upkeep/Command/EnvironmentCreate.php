<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Environments;
use TYPO3\DevCompanion\Upkeep\SiteExtension;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Makes the working directory a scenario names, where this repository makes it.
 *
 * `E-SITE` is a DDEV project with a TYPO3 installation in it, which is to say a
 * directory in which `ddev exec vendor/bin/typo3 …` answers — the half of this
 * server no test reaches, and where `D-DIS-007` and `R-DIS-018` were both found
 * by a real run. One per covered line, with the version as the second argument
 * (`D-EVI-006`). It carries one extension of its own, because a base
 * distribution registers nothing this project owns and half of what this server
 * answers is about what it does (`D-EVI-010`). What it is not is a repository to
 * review, because a scaffold of one would be this repository writing the defects
 * it then measures itself on finding (`D-EVI-001`). Every step is printed before it runs and quoted in full
 * when it fails, because a build that dies on step four has to say which four.
 */
#[AsCommand(
    name: 'environment:create',
    description: 'make the working directory a scenario is run in, where this repository makes it',
)]
final class EnvironmentCreate
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('which environment, as `scenarios/readme.md` names it')]
        string $environment = 'E-SITE',
        #[Argument('which covered version to install it on, where the environment has one')]
        string $version = '',
        #[Argument('which database to install it on: sqlite, mariadb, mysql or postgres')]
        string $database = Environments::DEFAULT_DRIVER,
    ): int {
        $id = strtoupper(trim($environment));
        $sources = Environments::sources();
        if (!isset($sources[$id])) {
            Voice::problem($output, sprintf(
                "%s is no environment this repository knows. `scenarios/readme.md` names these:\n    %s",
                $environment,
                implode(', ', Environments::ids()),
            ));

            return 1;
        }

        if ($sources[$id] !== Environments::MADE) {
            Voice::wrong($output, $id . ' is not made here.');
            Voice::note($output, Environments::reason($id));

            return 1;
        }

        if ($id === 'E-NONE') {
            return $this->nothing($output);
        }

        $driver = strtolower(trim($database));
        if (!in_array($driver, Environments::drivers(), true)) {
            Voice::problem($output, sprintf(
                "%s is no database an installation is made on. There is:\n    %s",
                $database,
                implode(', ', Environments::drivers()),
            ));

            return 1;
        }

        return $this->site($output, trim($version) === '' ? Environments::branch() : trim($version), $driver);
    }

    /**
     * `E-NONE` is a directory with no installation above it, which this
     * checkout is, so making one is a `mkdir` and a note saying so.
     *
     * It is here rather than left to the caller for the reason the rest is: an
     * environment somebody makes by hand is one that differs per person. The
     * note is what stops it from being an empty directory somebody deletes for
     * tidiness.
     */
    private function nothing(OutputInterface $output): int
    {
        $path = Environments::path('E-NONE');
        if (!is_dir($path) && !mkdir($path, 0o777, true) && !is_dir($path)) {
            Voice::problem($output, 'Cannot create ' . $path);

            return 2;
        }

        $written = file_put_contents($path . '/readme.md', <<<'TEXT'
            # E-NONE

            A directory with no TYPO3 installation anywhere above it, which is what
            `scenarios/readme.md` says this environment is. Nothing is in it on purpose:
            what a case run here measures is what this server answers when discovery
            finds nothing, so a file that made it look like a project would take the
            case away.

            Made by `bin/cli environment:create E-NONE`, and ignored by git.

            TEXT);
        if ($written === false) {
            Voice::problem($output, 'Cannot write the note in ' . $path);

            return 2;
        }

        Voice::ok($output, 'E-NONE is ' . $path);

        return 0;
    }

    /** The DDEV project with a TYPO3 installation of one covered line in it. */
    private function site(OutputInterface $output, string $branch, string $driver): int
    {
        $refusal = Environments::refusal($branch);
        if ($refusal !== '') {
            Voice::problem($output, $refusal);

            return 1;
        }

        $path = Environments::path('E-SITE', $branch, $driver);
        $project = Environments::project($branch, $driver);
        if (!Environments::ddev()) {
            Voice::problem(
                $output,
                "There is no `ddev` on this machine, and an E-SITE is a DDEV project.\n"
                . 'https://ddev.com/get-started/ is where it comes from.',
            );

            return 2;
        }

        // The directory is per checkout and the project name is per machine, so
        // two checkouts asking for an E-SITE ask for one name. Taking it over
        // would stop the other one's environment without saying so — but only
        // while there is a checkout to take it from. A worktree that made an
        // environment and was then removed leaves the name held on behalf of a
        // directory nobody can visit, and refusing in its name is a dead end.
        $registered = Environments::projects()[$project] ?? null;
        if ($registered !== null && Environments::abandoned($registered)) {
            Voice::note($output, sprintf(
                'DDEV still holds %s for %s, which is not there any more.',
                $project,
                $registered['approot'] === '' ? 'a directory it no longer names' : $registered['approot'],
            ));
            $discard = Environments::discard($project);
            Voice::row($output, Voice::dim(implode(' ', $discard)));

            [$exitCode, $said] = Environments::run($discard);
            if ($exitCode !== 0) {
                Voice::problem($output, rtrim($said));
                Voice::problem($output, 'That registration stands in the way and this could not clear it.');

                return 2;
            }

            $output->writeln('');
        } elseif ($registered !== null && rtrim($registered['approot'], '/') !== rtrim($path, '/')) {
            Voice::problem($output, sprintf(
                "DDEV already knows %s, and it is the one in %s.\nThat checkout's environment would be taken over by making this one.",
                $project,
                $registered['approot'],
            ));

            return 1;
        }

        if (Environments::installed($branch, $driver)) {
            return $this->resume($output, $branch, $driver, $registered['status'] ?? null);
        }

        if (!is_dir($path) && !mkdir($path, 0o777, true) && !is_dir($path)) {
            Voice::problem($output, 'Cannot create ' . $path);

            return 2;
        }

        $stopped = $this->steps($output, Environments::build($branch, $driver), $path);
        if ($stopped !== null) {
            Voice::problem($output, sprintf(
                'Stopped at "%s". What is there stays, and this command carries on from it.',
                $stopped,
            ));
            // The one failure that never finishes by carrying on. `--force`
            // covers the settings file, and no option of the setup gets
            // past tables an earlier installation left in the database.
            Voice::note(
                $output,
                "A database an earlier installation populated is the exception: its tables\n"
                . "are refused by the setup whatever is passed. Taking the project and the\n"
                . 'directory away is what clears it, on a file and on a service alike:',
            );
            Voice::row($output, Voice::dim(sprintf('%s && rm -rf %s', implode(' ', Environments::discard($project)), $path)));

            return 1;
        }

        // DDEV owns config/system/additional.php until this, and what it writes
        // there is wrong for the driver this environment was built on. Left
        // alone, the installation talks to a database container that is not
        // running and the backend refuses every login without saying why.
        $takenOver = Environments::takeOverGeneratedSettings($path);
        if ($takenOver !== null) {
            Voice::note($output, $takenOver);
        }

        if (!$this->ownExtension($output, $path)) {
            return 1;
        }

        $output->writeln('');
        $this->where($output, $branch, $driver);

        return 0;
    }

    /**
     * An installation that is already there is started, never built again.
     *
     * The build is minutes and a hundred packages and the containers are
     * seconds, so an environment kept between runs is only worth keeping if
     * asking for it again costs the seconds. DDEV pauses an idle project by
     * itself, which is the state this meets most of the time.
     */
    private function resume(OutputInterface $output, string $branch, string $driver, ?string $status): int
    {
        $path = Environments::path('E-SITE', $branch, $driver);
        $resume = Environments::resume($status);
        if ($resume !== null) {
            Voice::note($output, sprintf('The installation is at %s, and its containers are %s.', $path, $status ?? 'not registered'));
            Voice::row($output, Voice::dim(implode(' ', $resume)));
            [$exitCode, $said] = Environments::run($resume, $path);
            if ($exitCode !== 0) {
                Voice::problem($output, rtrim($said));
                Voice::problem($output, 'The installation is there and its containers did not come up.');

                return 2;
            }

            $output->writeln('');
        }

        if (!$this->ownExtension($output, $path)) {
            return 1;
        }

        $this->where($output, $branch, $driver);

        return 0;
    }

    /**
     * Writes the project's own extension in and installs it.
     *
     * Here rather than in the build, because the distribution's
     * `composer create-project` runs into this directory and refuses one that
     * already holds `packages/`. What that costs is a second `extension:setup`
     * after the build's own, which is seconds — `D-EVI-010`.
     */
    private function ownExtension(OutputInterface $output, string $path): bool
    {
        Voice::heading($output, sprintf('The extension of the project\'s own, written into %s/packages/', $path));
        SiteExtension::write($path);

        $stopped = $this->steps($output, Environments::ownExtension(), $path);
        if ($stopped === null) {
            return true;
        }

        Voice::problem($output, sprintf(
            'Stopped at "%s". The installation is there and has no table of this project in it, which is'
            . ' what typo3_record_lookup answers for; asking for this environment again runs the step again.',
            $stopped,
        ));

        return false;
    }

    /**
     * Runs steps in the project, printing each one before it runs.
     *
     * A build that dies on step four has to say which four, so the name and
     * the command are printed first and what the command said is quoted whole.
     *
     * @param array<string, list<string>> $steps
     * @return string|null the step that stopped it, and null where none did
     */
    private function steps(OutputInterface $output, array $steps, string $path): ?string
    {
        $bar = Voice::progress($output, count($steps));
        $bar->start();
        foreach ($steps as $what => $command) {
            $bar->clear();
            Voice::heading($output, $what);
            Voice::row($output, Voice::dim(implode(' ', $command)));
            $bar->setMessage('running');
            $bar->display();
            [$exitCode, $said] = Environments::run($command, $path);
            $bar->clear();
            $bar->advance();
            $bar->clear();
            if ($exitCode !== 0) {
                Voice::problem($output, rtrim($said));

                return $what;
            }
        }

        return null;
    }

    /** Where the environment is, and what it takes to open it. */
    private function where(OutputInterface $output, string $branch, string $driver): void
    {
        $project = Environments::project($branch, $driver);
        Voice::ok($output, sprintf(
            'E-SITE on TYPO3 %s, on %s, is %s, as DDEV project %s.',
            $branch,
            $driver,
            Environments::path('E-SITE', $branch, $driver),
            $project,
        ));
        Voice::row($output, sprintf('https://%s.ddev.site/typo3 — admin / %s', $project, Environments::ADMIN_PASSWORD));
        Voice::note($output, 'Start an MCP client in that directory to run a case in it.');
        Voice::row($output, sprintf(
            'ddev delete --omit-snapshot -y %s && rm -rf %s',
            $project,
            Environments::path('E-SITE', $branch, $driver),
        ));
        Voice::note($output, 'is what takes it away, and this command then makes it again.');
    }
}
