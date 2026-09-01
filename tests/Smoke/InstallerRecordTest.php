<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Server\Installer;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Directory;
use TYPO3\DevCompanion\Tests\Support\Requirement;

/**
 * What a project records about the clients installed in it.
 *
 * A project is worked on by more than one client, and which ones is knowledge
 * only the project has. It keeps it in `.typo3-dev-companion/state.json`, so that an
 * update needs no list from whoever runs it, and so that a skill this package
 * has stopped shipping can be taken out of every client it reached.
 */
#[Requirement('R-DIS-020')]
final class InstallerRecordTest extends TestCase
{
    private const SKILL = 'typo3-backend-module-development';

    #[Test]
    public function updateWithoutAnAgentRefreshesEveryClientInstalledHere(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install', '--agent=claude'], $stdout, $stderr), $stderr);
            self::assertSame(0, $this->execute($directory, ['install', '--agent=copilot'], $stdout, $stderr), $stderr);
            self::assertSame(
                ['claude', 'copilot'],
                $this->state($directory)['agents'],
            );

            $skills = [
                $directory . '/.claude/skills/' . self::SKILL . '/SKILL.md',
                $directory . '/.github/skills/' . self::SKILL . '/SKILL.md',
            ];
            foreach ($skills as $skill) {
                file_put_contents($skill, "User change.\n");
            }

            self::assertSame(0, $this->execute($directory, ['update'], $stdout, $stderr), $stderr);
            foreach ($skills as $skill) {
                self::assertFileEquals(Paths::root() . '/skills/' . self::SKILL . '/SKILL.md', $skill);
            }
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function clientsSharingASkillDirectoryArePublishedOnce(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install', '--agent=codex'], $stdout, $stderr), $stderr);
            self::assertSame(0, $this->execute($directory, ['install', '--agent=amp'], $stdout, $stderr), $stderr);

            self::assertSame(0, $this->execute($directory, ['update'], $stdout, $stderr), $stderr);
            self::assertSame(
                1,
                substr_count($stdout, 'Published ' . self::SKILL . ' in ' . $directory . '/.agents/skills'),
                $stdout,
            );
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function namingNoClientInstallsTheSkillsEveryClientFindsOnItsOwn(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install'], $stdout, $stderr), $stderr);

            $skill = $directory . '/.agents/skills/' . self::SKILL . '/SKILL.md';
            self::assertFileEquals(Paths::root() . '/skills/' . self::SKILL . '/SKILL.md', $skill);
            self::assertFileExists($directory . '/.mcp.json');
            // It is recorded like any other client, so it is refreshed like
            // one — the setup that names nobody needs no case of its own.
            self::assertSame(['generic'], $this->state($directory)['agents']);
            self::assertSame(
                "*\n",
                file_get_contents($directory . '/.agents/skills/' . self::SKILL . '/.gitignore'),
            );

            file_put_contents($skill, "User change.\n");
            self::assertSame(0, $this->execute($directory, ['update'], $stdout, $stderr), $stderr);
            self::assertFileEquals(Paths::root() . '/skills/' . self::SKILL . '/SKILL.md', $skill);
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function generalIsNotAClientOptionOfItsOwn(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            self::assertSame(1, $this->execute($directory, ['install', '--agent=whatever'], $stdout, $stderr));
            self::assertStringContainsString('unsupported agent "whatever"', $stderr);
            self::assertStringNotContainsString('generic', $stderr);
        } finally {
            Directory::remove($directory);
        }
    }

    /**
     * Said, and not a failure: this is the command a project wires into
     * Composer's `post-update-cmd`, where a non-zero exit fails the run, and
     * the record ignores itself — so a colleague who never installed would have
     * their `composer update` fail over a dev tool they do not use —
     * `D-DIS-014`.
     */
    #[Decision('D-DIS-014')]
    #[Test]
    public function updateSaysSoWhereNothingIsInstalledAtAll(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['update'], $stdout, $stderr), $stderr);
            self::assertStringContainsString('Nothing is installed here', $stdout);
            self::assertStringContainsString('install --agent=', $stdout);
            self::assertFileDoesNotExist($directory . '/.typo3-dev-companion/state.json');
        } finally {
            Directory::remove($directory);
        }
    }

    /**
     * The project's `.gitignore` is the project's, on a run that has every
     * reason to touch it.
     *
     * This is the one file an install used to write into, and the case that
     * would show it doing so again is an `update` in a project that has one:
     * nine skills are republished, the record is rewritten, and what the
     * project wrote stays byte for byte what it was — `D-DIS-010`.
     */
    #[Requirement('R-DIS-024')]
    #[Decision('D-DIS-010')]
    #[Test]
    public function neitherCommandWritesIntoTheProjectsGitignore(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            file_put_contents($directory . '/.gitignore', "/vendor/\n\n/.idea/\n");
            self::assertSame(0, $this->execute($directory, ['install', '--agent=claude'], $stdout, $stderr), $stderr);
            self::assertSame(0, $this->execute($directory, ['update'], $stdout, $stderr), $stderr);

            self::assertSame("/vendor/\n\n/.idea/\n", file_get_contents($directory . '/.gitignore'));
            self::assertSame(
                "*\n",
                file_get_contents($directory . '/.claude/skills/' . self::SKILL . '/.gitignore'),
            );
            self::assertSame("*\n", file_get_contents($directory . '/.typo3-dev-companion/.gitignore'));
        } finally {
            Directory::remove($directory);
        }
    }

    /**
     * What the record is read for once the install is over: whether the copies
     * down there are still the ones this server publishes.
     *
     * The four cases are the four ways a project drifts. A publication that was
     * never made is silence, because this package has nothing to say about a
     * project it never wrote into. The rest each name themselves, so the line
     * says which of them happened rather than only that something did —
     * `D-DIS-013`.
     */
    #[Requirement('R-DIS-025')]
    #[Decision('D-DIS-013')]
    #[Test]
    public function aPublicationThatIsNoLongerTheCurrentOneSaysWhichWayItDrifted(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            self::assertNull(Installer::outdated($directory), 'a project this never installed into');

            self::assertSame(0, $this->execute($directory, ['install', '--agent=claude'], $stdout, $stderr), $stderr);
            self::assertNull(Installer::outdated($directory), 'the run that just published them');
            self::assertSame(Installer::digest(), $this->state($directory)['digest']);

            // The skills this package ships have moved on since the install.
            // Nothing in the project changes when they do — the names are the
            // names — which is the whole reason the digest is recorded.
            $this->rewriteState($directory, ['digest' => str_repeat('0', 64)]);
            $moved = (string) Installer::outdated($directory);
            self::assertStringContainsString('publishes something other than what was published here', $moved);
            self::assertStringContainsString('Run typo3-dev-companion update.', $moved);

            self::assertSame(0, $this->execute($directory, ['update'], $stdout, $stderr), $stderr);
            self::assertNull(Installer::outdated($directory), 'the update that put them back');

            // What `git clean -xdf` does to a directory that ignores itself:
            // the record still names twelve skills and none of them is there.
            Directory::remove($directory . '/.claude/skills');
            self::assertStringContainsString(
                'nothing is published at .claude/skills',
                (string) Installer::outdated($directory),
            );

            self::assertSame(0, $this->execute($directory, ['update'], $stdout, $stderr), $stderr);
            $this->rewriteState($directory, ['digest' => null]);
            self::assertStringContainsString(
                'the record predates this check',
                (string) Installer::outdated($directory),
                'a state file written before anything but the names was recorded',
            );
        } finally {
            Directory::remove($directory);
        }
    }

    /**
     * The state file with those keys set, and null taking one out.
     *
     * Written rather than installed, because what is being reproduced is a
     * record no run of this version would write: one from a build before the
     * digest, and one whose skills have moved under it.
     *
     * @param array<string, string|null> $keys
     */
    private function rewriteState(string $directory, array $keys): void
    {
        $path = $directory . '/.typo3-dev-companion/state.json';
        $state = $this->state($directory);
        foreach ($keys as $key => $value) {
            if ($value === null) {
                unset($state[$key]);

                continue;
            }
            $state[$key] = $value;
        }
        file_put_contents($path, json_encode($state, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . "\n");
    }

    /** @return array{agents: list<string>, skills: list<string>, digest: string} */
    private function state(string $directory): array
    {
        return json_decode(
            (string) file_get_contents($directory . '/.typo3-dev-companion/state.json'),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
    }

    /** @param list<string> $arguments */
    private function execute(string $directory, array $arguments, string &$stdout, string &$stderr): int
    {
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/typo3-dev-companion', ...$arguments],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $directory,
        );
        self::assertIsResource($process);
        fclose($pipes[0]);
        $stdout = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return proc_close($process);
    }

    private function directory(): string
    {
        $directory = sys_get_temp_dir() . '/typo3-dev-companion-record-' . bin2hex(random_bytes(8));
        self::assertTrue(mkdir($directory));

        return $directory;
    }

}
