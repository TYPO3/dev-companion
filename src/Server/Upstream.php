<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Server;

use TYPO3\DevCompanion\Http\Fetch;
use TYPO3\DevCompanion\Paths;

/**
 * Whether this server's checkout is behind its upstream repository.
 *
 * The server is a rolling release. Guides, hints, skills and tools land on the
 * upstream `main` all day, and a checkout learns of none of it by itself. So
 * the start asks GitHub once, the answer stays an hour in a file every session
 * of the checkout shares, and nothing that answers a tool waits on the network,
 * `D-DIS-031`. It reports and never pulls: a pull changes the guides a session
 * is already following.
 */
final class Upstream
{
    public const REPOSITORY = 'TYPO3/dev-companion';
    public const BRANCH = 'main';

    /** Set it to `off` and the start asks nothing. */
    public const VARIABLE = 'TYPO3_DEV_COMPANION_UPSTREAM_CHECK';

    public const STATES = ['current', 'behind', 'unknown', 'unavailable', 'off', 'not-a-checkout'];

    private const HELD_FOR = 3600;

    /** The server start waits on this read, so it gets two seconds and not the default eight. */
    private const TIMEOUT = 2;

    private static ?Fetch $fetch = null;
    private static ?string $root = null;
    private static ?string $directory = null;

    /** @var array{state: string, revision: ?string, behind: ?int, checkedAt: ?string, lastAnswered: ?string}|null */
    private static ?array $known = null;

    /** Seams for a test: the reader, the checkout, and where the answer is kept. */
    public static function useFetch(?Fetch $fetch): void
    {
        self::$fetch = $fetch;
    }

    public static function useRoot(?string $root): void
    {
        self::$root = $root;
    }

    public static function useDirectory(?string $directory): void
    {
        self::$directory = $directory;
    }

    public static function forget(): void
    {
        self::$fetch = null;
        self::$root = null;
        self::$directory = null;
        self::$known = null;
    }

    /**
     * Asks the upstream, where the kept answer is older than an hour or about
     * another commit. Called once, where the server starts.
     */
    public static function check(): void
    {
        self::$known = self::read(true);
    }

    /**
     * What is known now, without the network. The kept answer is read again on
     * every call, so another session's newer check reaches this one.
     *
     * @return array{state: string, revision: ?string, behind: ?int, checkedAt: ?string, lastAnswered: ?string}
     */
    public static function report(): array
    {
        if (self::$known === null) {
            return ['state' => 'unknown', 'revision' => null, 'behind' => null, 'checkedAt' => null, 'lastAnswered' => null];
        }
        if (in_array(self::$known['state'], ['off', 'not-a-checkout'], true)) {
            return self::$known;
        }

        return self::$known = self::read(false);
    }

    /** The sentence every answer opens with while the checkout is behind, or empty. */
    public static function notice(): string
    {
        if (self::$known === null) {
            return '';
        }
        $report = self::report();
        if ($report['state'] !== 'behind') {
            return '';
        }

        return sprintf(
            'This server\'s checkout is %d %s behind github.com/%s, read at %s. To update: git -C %s pull, then '
                . 'restart the MCP server.',
            (int) $report['behind'],
            $report['behind'] === 1 ? 'commit' : 'commits',
            self::REPOSITORY,
            (string) $report['checkedAt'],
            self::root(),
        );
    }

    /**
     * @return array{state: string, revision: ?string, behind: ?int, checkedAt: ?string, lastAnswered: ?string}
     */
    private static function read(bool $mayAsk): array
    {
        $none = ['revision' => null, 'behind' => null, 'checkedAt' => null, 'lastAnswered' => null];
        $off = strtolower(trim((string) getenv(self::VARIABLE)));
        if (in_array($off, ['off', '0', 'false', 'no'], true)) {
            return ['state' => 'off'] + $none;
        }
        $revision = self::revision(self::root());
        if ($revision === null) {
            return ['state' => 'not-a-checkout'] + $none;
        }

        $kept = self::kept();
        $fresh = $kept !== null && $kept['revision'] === $revision
            && $kept['state'] !== 'unavailable'
            && time() - (int) strtotime((string) $kept['checkedAt']) < self::HELD_FOR;
        if ($fresh || !$mayAsk) {
            return $kept !== null && $kept['revision'] === $revision
                ? $kept
                : ['state' => 'unknown', 'revision' => $revision, 'behind' => null, 'checkedAt' => null, 'lastAnswered' => null];
        }

        $now = gmdate('Y-m-d H:i') . ' UTC';
        $answer = self::ask($revision);
        $report = $answer === null
            ? [
                'state' => 'unavailable',
                'revision' => $revision,
                'behind' => null,
                'checkedAt' => $now,
                'lastAnswered' => $kept['lastAnswered'] ?? null,
            ]
            : ['revision' => $revision, 'checkedAt' => $now, 'lastAnswered' => $now] + $answer;
        self::keep($report);

        return $report;
    }

    /**
     * What GitHub's compare says of this commit against the upstream branch.
     * Null where it did not answer. A commit the upstream does not have, a
     * local one, is `unknown`: the compare has nothing to count against.
     *
     * @return array{state: string, behind: ?int}|null
     */
    private static function ask(string $revision): ?array
    {
        $fetch = self::$fetch ?? new Fetch(null, self::TIMEOUT);
        $answer = $fetch->read(
            'https://api.github.com/repos/' . self::REPOSITORY . '/compare/' . $revision . '...' . self::BRANCH,
            ['Accept: application/vnd.github+json'],
        );
        if ($answer['status'] === 404 || $answer['status'] === 422) {
            return ['state' => 'unknown', 'behind' => null];
        }
        $decoded = Fetch::decode($answer['body']);
        if (!is_array($decoded) || !is_numeric($decoded['ahead_by'] ?? null)) {
            return null;
        }
        $behind = (int) $decoded['ahead_by'];

        return ['state' => $behind > 0 ? 'behind' : 'current', 'behind' => $behind];
    }

    /**
     * The commit the checkout stands on, read from its git directory without
     * git. A worktree's `.git` is a file that names it, and a branch may sit in
     * `packed-refs` rather than a file of its own.
     */
    private static function revision(string $root): ?string
    {
        $git = $root . '/.git';
        if (is_file($git)) {
            $line = trim((string) file_get_contents($git));
            if (!str_starts_with($line, 'gitdir:')) {
                return null;
            }
            $git = trim(substr($line, 7));
            $git = str_starts_with($git, '/') ? $git : $root . '/' . $git;
        }
        if (!is_file($git . '/HEAD')) {
            return null;
        }
        $head = trim((string) file_get_contents($git . '/HEAD'));
        if (preg_match('/^[0-9a-f]{40}$/', $head) === 1) {
            return $head;
        }
        if (!str_starts_with($head, 'ref: ')) {
            return null;
        }
        $ref = substr($head, 5);
        $common = $git;
        if (is_file($git . '/commondir')) {
            $named = trim((string) file_get_contents($git . '/commondir'));
            $common = str_starts_with($named, '/') ? $named : $git . '/' . $named;
        }
        foreach ([$git, $common] as $directory) {
            if (is_file($directory . '/' . $ref)) {
                return trim((string) file_get_contents($directory . '/' . $ref));
            }
        }
        $packed = $common . '/packed-refs';
        if (is_file($packed)) {
            foreach (file($packed) ?: [] as $line) {
                if (preg_match('/^([0-9a-f]{40}) (\S+)$/', trim($line), $matched) === 1 && $matched[2] === $ref) {
                    return $matched[1];
                }
            }
        }

        return null;
    }

    /** @return array{state: string, revision: ?string, behind: ?int, checkedAt: ?string, lastAnswered: ?string}|null */
    private static function kept(): ?array
    {
        $file = self::file();
        if (!is_file($file)) {
            return null;
        }
        $kept = json_decode((string) file_get_contents($file), true);
        if (!is_array($kept) || !in_array($kept['state'] ?? null, self::STATES, true)) {
            return null;
        }

        return [
            'state' => (string) $kept['state'],
            'revision' => is_string($kept['revision'] ?? null) ? $kept['revision'] : null,
            'behind' => is_int($kept['behind'] ?? null) ? $kept['behind'] : null,
            'checkedAt' => is_string($kept['checkedAt'] ?? null) ? $kept['checkedAt'] : null,
            'lastAnswered' => is_string($kept['lastAnswered'] ?? null) ? $kept['lastAnswered'] : null,
        ];
    }

    /** @param array{state: string, revision: ?string, behind: ?int, checkedAt: ?string, lastAnswered: ?string} $report */
    private static function keep(array $report): void
    {
        // A failed write keeps nothing, and the next start asks again.
        @file_put_contents(self::file(), (string) json_encode($report));
    }

    private static function file(): string
    {
        return (self::$directory ?? sys_get_temp_dir()) . '/typo3-dev-companion-upstream-'
            . substr(hash('xxh128', self::root()), 0, 12) . '.json';
    }

    private static function root(): string
    {
        return self::$root ?? Paths::root();
    }
}
