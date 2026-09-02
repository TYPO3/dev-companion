<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use TYPO3\DevCompanion\Knowledge\Versions;

/**
 * A package the core pins rather than contains, and which release of it each
 * covered TYPO3 major is read against.
 *
 * `.checkouts/` holds the core alone, so a statement whose subject is one of
 * these packages would otherwise be verified against whichever vendor tree
 * happens to be on the machine — evidence the next session cannot reproduce.
 * Each is kept beside the core checkouts instead, at a tag rather than at a
 * branch, and which tag that is is derived from the covered branch's own
 * constraint rather than recorded here — `D-KNW-106`.
 */
final class PinnedPackage
{
    private function __construct(
        public readonly string $package,
        public readonly string $repository,
        /** The manifest section the core pins it in. */
        public readonly string $section,
        /** What it is called below `.checkouts/`, mirror and worktrees alike. */
        public readonly string $directory,
        /** What it is to a caller of this server, for the line a command prints. */
        public readonly string $subject,
    ) {}

    public static function testingFramework(): self
    {
        return new self(
            'typo3/testing-framework',
            'https://github.com/TYPO3/testing-framework.git',
            'require-dev',
            'testing-framework',
            'the harness a project extension tests in',
        );
    }

    public static function fluid(): self
    {
        return new self(
            'typo3fluid/fluid',
            'https://github.com/TYPO3/Fluid.git',
            'require',
            'fluid',
            'the engine a template is parsed by',
        );
    }

    /** @return array<int, self> */
    public static function all(): array
    {
        return [self::testingFramework(), self::fluid()];
    }

    /**
     * The release line a core branch pins itself to: a major as a string, or the
     * branch name where the pin is a development one.
     *
     * Asked over a window rather than parsed into a range, the way the Fluid
     * engine is asked in `bin/cli versions:check`: the question is only ever
     * "does this pin line 9", and a pin that answers for two lines is the case
     * this exists to catch.
     */
    public static function line(string $constraint): ?string
    {
        $constraint = trim($constraint);
        if (str_starts_with($constraint, 'dev-')) {
            return substr($constraint, 4);
        }

        $majors = array_values(array_filter(
            range(1, 30),
            static fn(int $major): bool => Versions::admits($constraint, $major),
        ));

        return count($majors) === 1 ? (string) $majors[0] : null;
    }

    /** The commit a ref points at, in the mirror or in a worktree. */
    public static function revision(string $repository, string $ref): string
    {
        return trim((string) shell_exec(sprintf(
            'git -C %s rev-parse %s 2>/dev/null',
            escapeshellarg($repository),
            escapeshellarg($ref . '^{commit}'),
        )));
    }

    /** The bare clone the lines are worktrees of, beside the core's own. */
    public function mirror(string $checkouts): string
    {
        return $checkouts . '/' . $this->directory . '.git';
    }

    /** Where one release line is checked out. */
    public function worktree(string $checkouts, string $line): string
    {
        return $checkouts . '/' . $this->directory . '/' . $line;
    }

    /**
     * The release each covered major pairs with, as the core checkouts say it.
     *
     * `constraint` is empty where the branch pins nothing, and `line` and `ref`
     * are null where the pin names no single release line — both are reported
     * rather than guessed at, because a pin that spans two lines means the core
     * major no longer says which release a statement was read in.
     *
     * @return array<int, array{major: int, branch: string, constraint: string, line: ?string, ref: ?string, path: string}>
     */
    public function pairing(string $checkouts): array
    {
        $pairs = [];
        foreach (Versions::covered() as $version) {
            $manifest = $checkouts . '/' . $version['branch'] . '/composer.json';
            $constraint = is_file($manifest)
                ? (json_decode((string) file_get_contents($manifest), true)[$this->section][$this->package] ?? null)
                : null;
            $constraint = is_string($constraint) ? $constraint : '';
            $line = self::line($constraint);
            $pairs[] = [
                'major' => $version['major'],
                'branch' => $version['branch'],
                'constraint' => $constraint,
                'line' => $line,
                'ref' => $line === null ? null : $this->ref($checkouts, $line),
                'path' => $this->worktree($checkouts, (string) $line),
            ];
        }

        return $pairs;
    }

    /**
     * The ref a line is read at: its newest tag, or the branch itself where the
     * line is a development one.
     */
    public function ref(string $checkouts, string $line): ?string
    {
        if (!ctype_digit($line)) {
            return $line;
        }

        $tags = (string) shell_exec(sprintf(
            'git -C %s tag --list %s --sort=-v:refname 2>/dev/null',
            escapeshellarg($this->mirror($checkouts)),
            escapeshellarg($line . '.*'),
        ));
        $newest = strtok($tags, "\n");

        return $newest === false ? null : $newest;
    }
}
