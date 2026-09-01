<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Server;

use Mcp\Server\Transport\StdioTransport;
use TYPO3\DevCompanion\Installation\Instance;

/**
 * What `bin/typo3-dev-companion` runs.
 *
 * With no argument it speaks MCP over stdin and stdout, which is how a client
 * launches it. Everything else is somebody at a terminal, and starting the
 * transport for them would look like a hang — so every other word ends in a
 * message and an exit code.
 *
 * This is also the one place that hands a working directory to `Instance`: the
 * client starts this process inside the session it is working in, so an
 * installation found from it is the one being worked on. Nothing else may do it
 * — `R-DIS-001`.
 */
final class Entrypoint
{
    /** Set this to `off` and a stale publication is reported rather than put back. */
    public const REFRESH = 'TYPO3_DEV_COMPANION_SKILL_REFRESH';

    /** @param array<int, string> $arguments what the shell passed, without the binary */
    public static function run(array $arguments, string $binary = 'typo3-dev-companion'): int
    {
        $command = $arguments[0] ?? null;

        if (in_array($command, ['install', 'update'], true)) {
            return self::setUp($command, array_slice($arguments, 1), $binary);
        }

        if ($command === null) {
            Instance::discoverFrom(getcwd() ?: null);
            self::reportExclusionsThatTookNothingAway();
            Factory::create(self::refreshSkillsNobodyHasUpdated($binary))->run(new StdioTransport());

            return 0;
        }

        if (in_array($command, ['help', '--help', '-h'], true)) {
            fwrite(STDOUT, self::usage());

            return 0;
        }

        fwrite(STDERR, 'typo3-dev-companion: no such command "' . $command . "\".\n\n" . self::usage());

        return 2;
    }

    /**
     * An excluded name that took no tool away, said on stderr before the
     * transport starts.
     *
     * stdout is the protocol from the next line on, so stderr is the one channel
     * a started server has left, and `src/bootstrap.php` says the other startup
     * problem there too. It is a warning and the server starts: the list is read
     * once out of an environment nobody validates it against, and a name gone
     * stale under a rename would otherwise take every tool down with it —
     * `D-AUD-005`. The two reasons are said apart because what somebody has to
     * change differs, and `typo3_server_scope` says the same thing in-band.
     */
    private static function reportExclusionsThatTookNothingAway(): void
    {
        $unknown = ExcludedTools::unknown();
        if ($unknown !== []) {
            fwrite(STDERR, sprintf(
                'typo3-dev-companion: %s names %s, which this server does not offer, so %s excluded nothing. '
                . "typo3_server_scope lists the tools it does offer.\n",
                ExcludedTools::VARIABLE,
                implode(', ', $unknown),
                count($unknown) === 1 ? 'it' : 'they',
            ));
        }

        $offeredAnyway = ExcludedTools::offeredAnyway();
        if ($offeredAnyway !== []) {
            fwrite(STDERR, sprintf(
                'typo3-dev-companion: %s names %s, which this server offers whatever the variable says, so %s '
                . "excluded nothing. typo3_server_scope says which tools are really gone.\n",
                ExcludedTools::VARIABLE,
                implode(', ', $offeredAnyway),
                count($offeredAnyway) === 1 ? 'it' : 'they',
            ));
        }
    }

    /**
     * The task skills in this project that are no longer the ones this server
     * publishes, put back — and said on both channels a starting server has.
     *
     * Saying it was the whole of this and it was not enough: every mechanism
     * that answered the notice needed somebody to act on it, and on the machine
     * that prompted `D-DIS-021` twelve projects had drifted with nobody
     * noticing. A server starting is the one thing that happens in a project
     * without anybody deciding to, so it is what carries the refresh.
     *
     * Both channels still speak, because both readers still have something to
     * do. stderr gets the long form for whoever is at the terminal; the
     * instructions get the one sentence the budget has room for, because a
     * skill the client loaded when the session opened is the copy that was
     * there before this ran.
     *
     * A refresh that fails leaves the notice exactly as it was. It writes into
     * somebody else's project, so it may not be the thing that stops a server
     * from starting.
     *
     * The directory is the one this process was started in, which is where
     * `install` writes and therefore where the record is. Walking up for one
     * would find a parent project's, and the entry a client starts this from
     * names the project it belongs to.
     */
    private static function refreshSkillsNobodyHasUpdated(string $binary): string
    {
        $project = getcwd() ?: '';
        $outdated = Installer::outdated($project);
        if ($outdated === null) {
            return '';
        }
        fwrite(STDERR, 'typo3-dev-companion: ' . $outdated . "\n");

        if (!self::refreshIsWanted()) {
            return Installer::NOTICE;
        }

        try {
            $refreshed = (new Installer($project, $binary))->refresh();
        } catch (\RuntimeException $exception) {
            fwrite(STDERR, 'typo3-dev-companion: the refresh failed, so they are still stale: '
                . $exception->getMessage() . ".\n");

            return Installer::NOTICE;
        }

        fwrite(STDERR, "typo3-dev-companion: refreshed them.\n" . $refreshed . "\n");

        return Installer::REFRESHED;
    }

    /**
     * Whether a stale publication is put back or only reported.
     *
     * Off is for whoever wants the copies in their project to move when they
     * say so and not before — a review of what a release changed, or a project
     * where the skills are read as part of a diff. The notice is what they keep.
     */
    private static function refreshIsWanted(): bool
    {
        $wanted = getenv(self::REFRESH) ?: '';

        return !in_array(strtolower(trim($wanted)), ['off', '0', 'false', 'no'], true);
    }

    /**
     * Writes the client configuration and the task skills into the directory
     * this was run in, which is the project being set up.
     *
     * @param array<int, string> $arguments
     */
    private static function setUp(string $command, array $arguments, string $binary): int
    {
        $directory = getcwd();
        if ($directory === false) {
            fwrite(STDERR, "typo3-dev-companion: cannot determine the current directory.\n");

            return 1;
        }

        $agent = null;
        foreach ($arguments as $argument) {
            if (str_starts_with($argument, '--agent=')) {
                $agent = substr($argument, strlen('--agent='));
            }
        }

        try {
            $installer = new Installer($directory, $binary);
            $message = $command === 'install'
                ? $installer->install($agent)
                : $installer->update($agent);
        } catch (\RuntimeException $exception) {
            fwrite(STDERR, 'typo3-dev-companion: ' . $exception->getMessage() . ".\n");

            return 1;
        }

        fwrite(STDOUT, $message . "\n");

        return 0;
    }

    private static function usage(): string
    {
        return "Usage: typo3-dev-companion [command]\n\n"
            . "With no command this speaks MCP over stdin and stdout, which is how a\n"
            . "client launches it. The commands below set that up, in the directory\n"
            . "they are run in.\n\n"
            . "  install [--agent=<client>]  Write the client configuration and the\n"
            . "                              task skills. Without --agent, the entry\n"
            . "                              in .mcp.json and the skills in\n"
            . "                              .agents/skills, which every client that\n"
            . "                              reads those two finds on its own.\n"
            . "  update [--agent=<client>]   Republish the skills and rewrite the\n"
            . "                              client entry, which a project that has\n"
            . "                              since required this server or gained a\n"
            . "                              DDEV configuration has outgrown.\n"
            . "                              Without --agent, for every client\n"
            . "                              installed here.\n"
            . "  help                        This text.\n\n"
            . 'Clients: ' . implode(', ', Installer::agents()) . "\n";
    }
}
