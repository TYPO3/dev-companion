<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * A core checkout this repository writes, which holds nothing but what it is.
 *
 * Eight tools read nothing an installation contains. What reaches their answer
 * is `knowledge/` and two declarations about the root. That it is the core
 * monorepo rather than a project, and which TYPO3 major that is. So this writes
 * those and nothing else, and the emptiness is the point. A root with content
 * would put that content into a page that claims a derivation, which is
 * `D-DOC-012`'s first **Wrong if**. `Fixture` answers the opposite question,
 * and neither replaces the core checkout below `.checkouts/`.
 */
final class CoreFixture
{
    /**
     * Where it stands: beside the other fixture, ignored by git, rewritten
     * whole.
     */
    public static function root(): string
    {
        return Fixture::directory() . '/checkout';
    }

    /**
     * The version it states it is: the newest covered line with a release, at
     * its first patch.
     *
     * The patch level stands here rather than as a guess because nothing
     * derived from here may carry it. `typo3_translation_domain_lookup` prints
     * the installation's exact version into its answer and is out of the
     * derived set for that reason, measured on 2026-08-04.
     */
    public static function typo3Version(): string
    {
        return Environments::branch() . '.0';
    }

    /** Writes it whole and hands back the root it went to. */
    public static function write(): string
    {
        $root = self::root();
        $core = $root . '/typo3/sysext/core';

        self::put($root . '/composer.json', self::json([
            'name' => 'typo3/cms',
            'description' => 'Written by TYPO3\\DevCompanion\\Upkeep\\CoreFixture so an answer that reads no installation '
                . 'can be derived. It is not a TYPO3 checkout and holds none of one.',
            'type' => 'typo3-cms-core',
        ]));
        self::put($core . '/composer.json', self::json([
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'core']],
        ]));
        self::put($core . '/Classes/Information/Typo3Version.php', sprintf(
            "<?php\n\nnamespace TYPO3\\CMS\\Core\\Information;\n\nclass Typo3Version\n{\n"
            . "    protected const VERSION = '%s';\n}\n",
            self::typo3Version(),
        ));

        return $root;
    }

    /** @param array<string, mixed> $value */
    private static function json(array $value): string
    {
        return (string) json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private static function put(string $path, string $contents): void
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0o777, true);
        }
        file_put_contents($path, $contents);
    }
}
