<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * The extension a made `E-SITE` carries as the project's own.
 *
 * TYPO3's base distribution installs no package of its own, so an installation
 * made here answers every question about what this project registers with
 * nothing — and the one `typo3_record_lookup` exists for, the rows of a table a
 * project-owned extension registers, could be recorded nowhere: the fixture
 * below `.fixtures/` has the extension and no database to open, and this
 * environment had the database and no extension. This is that package, and
 * `D-EVI-010` is why it is written rather than left to whoever runs a case.
 *
 * It is written below `packages/`, where the distribution's own path repository
 * already looks, and it carries one table with a label column, rows enough that
 * the answer says where the records are edited, and the two states a count
 * separates — hidden and deleted.
 *
 * What attributes the table to this project is the `EXT:` reference in the
 * ctrl title and nothing else, which is what `Typo3Runtime::extensionIn` reads.
 */
final class SiteExtension
{
    /** The extension key, which is what the ctrl title attributes the table through. */
    public const KEY = 'acme_events';

    /** The Composer package, required out of the distribution's path repository. */
    public const PACKAGE = 'acme/acme-events';

    /** The table the recorded call reads, and the fixture registers under the same key. */
    public const TABLE = 'tx_acme_events_event';

    /** Where the seed sits inside the project, as the container reaches it. */
    public const SEED = 'packages/' . self::KEY . '/Build/seed.php';

    /**
     * The page the rows sit on: the root page the setup's `--create-site` makes.
     *
     * One page rather than several, because what the answer is read for is how
     * full the page an editor opens is, and rows spread thin over a tree say
     * nothing about that.
     */
    public const PAGE = 1;

    /**
     * Live rows, which is more pages of the record list than one.
     *
     * The sighting `D-AUD-017` rests on was a storage folder nobody could
     * maintain through the generic list, and a recorded answer showing twenty
     * rows on one page would be the case that needs no tool.
     */
    public const LIVE = 120;

    /** Rows the disable field hides, so the count has that state in it. */
    public const HIDDEN = 3;

    /** Rows the delete field marks, which are still in the table. */
    public const DELETED = 2;

    /** Writes it into an installation, replacing whatever an earlier run left. */
    public static function write(string $installation): void
    {
        $root = rtrim($installation, '/') . '/packages/' . self::KEY;
        foreach (self::files() as $path => $contents) {
            $file = $root . '/' . $path;
            $directory = dirname($file);
            if (!is_dir($directory)) {
                mkdir($directory, 0o777, true);
            }
            file_put_contents($file, $contents);
        }
    }

    /**
     * Every file of it, by the path below the extension.
     *
     * @return array<string, string>
     */
    public static function files(): array
    {
        return [
            'composer.json' => self::manifest(),
            'readme.md' => self::readme(),
            'ext_tables.sql' => self::schema(),
            'Configuration/TCA/' . self::TABLE . '.php' => self::tca(),
            'Resources/Private/Language/locallang_db.xlf' => self::labels(),
            'Build/seed.php' => self::seed(),
        ];
    }

    /**
     * A version is stated because a path repository has no other source for one.
     *
     * The directory is not a checkout, so Composer has no tag to read and no
     * branch to derive a `dev-` version from, and the requirement below would
     * be resolved against nothing.
     */
    private static function manifest(): string
    {
        return self::json([
            'name' => self::PACKAGE,
            'type' => 'typo3-cms-extension',
            'description' => 'The project-owned extension the E-SITE environment carries.',
            'license' => 'GPL-2.0-or-later',
            'version' => '1.0.0',
            'require' => ['typo3/cms-core' => '*'],
            'extra' => ['typo3/cms' => ['extension-key' => self::KEY]],
        ]);
    }

    private static function readme(): string
    {
        return <<<TEXT
            # acme_events

            Written by `bin/cli environment:create E-SITE`, and ignored by git the way
            everything below `.environments/` is. It exists so this installation has an
            extension of its own: one table with rows in it, which is what
            `typo3_record_lookup` answers for and what no base distribution has.

            Nothing in it is worked on. A case run in this environment meets it the way it
            meets the rest of the installation this repository made.

            TEXT;
    }

    /**
     * The columns TYPO3 does not derive.
     *
     * `uid`, `pid`, the timestamps and the two flags come from the ctrl below,
     * because that is what `DefaultTcaSchema` enriches every TCA table with.
     */
    private static function schema(): string
    {
        return 'CREATE TABLE ' . self::TABLE . " (\n"
            . "    title varchar(255) DEFAULT '' NOT NULL,\n"
            . "    venue varchar(255) DEFAULT '' NOT NULL,\n"
            . "    starts int(11) DEFAULT '0' NOT NULL\n"
            . ");\n";
    }

    private static function tca(): string
    {
        $label = 'LLL:EXT:' . self::KEY . '/Resources/Private/Language/locallang_db.xlf:';

        return "<?php\n\nreturn [\n"
            . "    'ctrl' => [\n"
            . "        'title' => '" . $label . self::TABLE . "',\n"
            . "        'label' => 'title',\n"
            . "        'tstamp' => 'tstamp',\n"
            . "        'crdate' => 'crdate',\n"
            . "        'delete' => 'deleted',\n"
            . "        'enablecolumns' => ['disabled' => 'hidden'],\n"
            . "    ],\n"
            . "    'columns' => [\n"
            . "        'hidden' => ['config' => ['type' => 'check']],\n"
            . "        'title' => [\n"
            . "            'label' => '" . $label . self::TABLE . ".title',\n"
            . "            'config' => ['type' => 'input', 'size' => 40, 'required' => true],\n"
            . "        ],\n"
            . "        'venue' => [\n"
            . "            'label' => '" . $label . self::TABLE . ".venue',\n"
            . "            'config' => ['type' => 'input', 'size' => 40],\n"
            . "        ],\n"
            . "        'starts' => [\n"
            . "            'label' => '" . $label . self::TABLE . ".starts',\n"
            . "            'config' => ['type' => 'datetime', 'format' => 'date'],\n"
            . "        ],\n"
            . "    ],\n"
            . "    'types' => ['1' => ['showitem' => 'hidden, title, venue, starts']],\n"
            . "];\n";
    }

    private static function labels(): string
    {
        $units = '';
        foreach ([
            self::TABLE => 'Event',
            self::TABLE . '.title' => 'Title',
            self::TABLE . '.venue' => 'Venue',
            self::TABLE . '.starts' => 'Starts',
        ] as $id => $text) {
            $units .= '            <trans-unit id="' . $id . "\">\n"
                . '                <source>' . $text . "</source>\n"
                . "            </trans-unit>\n";
        }

        return "<?xml version=\"1.0\" encoding=\"utf-8\" standalone=\"yes\"?>\n"
            . "<xliff version=\"1.0\">\n"
            . "    <file source-language=\"en\" datatype=\"plaintext\" original=\"messages\">\n"
            . "        <body>\n"
            . $units
            . "        </body>\n"
            . "    </file>\n"
            . "</xliff>\n";
    }

    /**
     * The script that fills the table, run inside the installation.
     *
     * It boots TYPO3 the way `src/Installation/probe.php` does and for the same
     * reason: rows go in through the installation's own connection, at its own
     * PHP version, on the other side of a process boundary. It writes nothing
     * where the table already holds a row, so asking for the environment again
     * costs a subprocess rather than a second set of rows.
     */
    private static function seed(): string
    {
        $table = self::TABLE;
        $page = self::PAGE;
        $live = self::LIVE;
        $hidden = self::HIDDEN;
        $deleted = self::DELETED;

        return <<<PHP
            <?php

            // Written by bin/cli environment:create E-SITE, and run by it through
            // `ddev exec php`. Nothing in the installation calls it.

            \$classLoader = require __DIR__ . '/../../../vendor/autoload.php';
            TYPO3\\CMS\\Core\\Core\\SystemEnvironmentBuilder::run(
                0,
                TYPO3\\CMS\\Core\\Core\\SystemEnvironmentBuilder::REQUESTTYPE_CLI
            );
            TYPO3\\CMS\\Core\\Core\\Bootstrap::init(\$classLoader);

            \$table = '{$table}';
            \$connection = TYPO3\\CMS\\Core\\Utility\\GeneralUtility::makeInstance(
                TYPO3\\CMS\\Core\\Database\\ConnectionPool::class
            )->getConnectionForTable(\$table);

            \$already = (int)\$connection->count('uid', \$table, []);
            if (\$already > 0) {
                echo \$already . ' rows are in ' . \$table . ' already, and none were written.' . PHP_EOL;
                return;
            }

            \$now = time();
            \$written = 0;
            foreach ([['live', {$live}], ['hidden', {$hidden}], ['deleted', {$deleted}]] as [\$state, \$count]) {
                for (\$number = 1; \$number <= \$count; \$number++) {
                    \$written++;
                    \$connection->insert(\$table, [
                        'pid' => {$page},
                        'tstamp' => \$now,
                        'crdate' => \$now,
                        'hidden' => \$state === 'hidden' ? 1 : 0,
                        'deleted' => \$state === 'deleted' ? 1 : 0,
                        'title' => sprintf('%s event %03d', ucfirst(\$state), \$number),
                        'venue' => ['Main hall', 'Side room', 'Courtyard'][\$written % 3],
                        // One a day from today, so the column carries a spread
                        // rather than one value repeated.
                        'starts' => \$now + \$written * 86400,
                    ]);
                }
            }

            echo \$written . ' rows were written to ' . \$table . '.' . PHP_EOL;

            PHP;
    }

    /** @param array<string, mixed> $manifest */
    private static function json(array $manifest): string
    {
        return json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";
    }
}
