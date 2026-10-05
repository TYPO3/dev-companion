<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Server;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

/**
 * Whether the code this process runs is still the code in its checkout.
 *
 * A client starts the server once per session, and the checkout moves under it
 * with every pull. The knowledge, the guides and the skills are read at the
 * call, so they move at once. The tools a client was offered and the PHP that
 * answers them were fixed at the start. So a guide could name a tool this
 * process does not serve, and nothing said why, `D-DIS-030`.
 */
final class CodeAge
{
    public const NOTICE = 'This server process is older than its code: the checkout changed after it started, so '
        . 'a tool a guide names may be missing here and an answer may come from the code before the change. '
        . 'Restart the MCP server, which for most clients is a new session.';

    /** The fingerprint at the start, or null where no start was marked. */
    private static ?string $started = null;

    /** Where a test lays out a checkout of its own; null is this one. */
    private static ?string $root = null;

    public static function useRoot(?string $root): void
    {
        self::$root = $root;
    }

    /** Called once, where the server starts. */
    public static function markStart(): void
    {
        self::$started = self::fingerprint();
    }

    public static function forget(): void
    {
        self::$started = null;
        self::$root = null;
    }

    /**
     * Whether the checkout carries other code than it did at the start.
     *
     * One stat per file below `src/`, which is a few milliseconds and cheaper
     * than a cache that could itself go stale.
     */
    public static function isStale(): bool
    {
        return self::$started !== null && self::$started !== self::fingerprint();
    }

    /**
     * Every PHP file below `src/` by path, size and change time, and the lock
     * that pins the dependencies.
     */
    private static function fingerprint(): string
    {
        $parts = [];
        $root = self::$root ?? Paths::root();
        $source = $root . '/src';
        if (is_dir($source)) {
            foreach (Finder::create()->files()->in($source)->name('*.php')->sortByName() as $file) {
                $parts[] = $file->getRelativePathname() . ':' . $file->getSize() . ':' . $file->getMTime();
            }
        }
        $lock = $root . '/composer.lock';
        if (is_file($lock)) {
            $parts[] = 'composer.lock:' . (string) filemtime($lock) . ':' . (string) filesize($lock);
        }

        return hash('xxh128', implode("\n", $parts));
    }
}
