<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Knowledge\TestSuiteHints;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Tool\TranslationDomainLookup;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\PinnedPackage;
use TYPO3\DevCompanion\Upkeep\RangeReport;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Whether the version statements this repository binds still hold.
 *
 * None of these reads the catalog: they are claims about what a branch pins,
 * what a harness releases, what a script offers and when an API arrived, and
 * each is wrong by a release rather than by an edit — which no test here can
 * see.
 */
#[AsCommand(
    name: 'versions:check',
    description: 'the Fluid engine each branch pins, the testing-framework release, the suites runTests.sh offers, and where a translation domain arrived, against .checkouts/',
)]
final class VersionCheck
{
    /** How many uncurated suites the report names before it stops naming them. */
    private const NAMED_UNCURATED = 12;

    /**
     * What the hints about typo3/testing-framework rest on, per file of the
     * package, so a release changing one of them fails here rather than ageing
     * quietly into a wrong answer (`D-KNW-106`). Existence carries the statement
     * that the four boilerplate files are there to be copied; each needle
     * carries one sentence of `project-extension-tests`, named beside it, and
     * what no needle covers is not guarded.
     *
     * @var array<string, array<int, string>>
     */
    private const TESTING_FRAMEWORK_EVIDENCE = [
        // "the files say in their own header that extensions should copy them"
        'Resources/Core/Build/UnitTests.xml' => ['copy it to an own place'],
        'Resources/Core/Build/FunctionalTests.xml' => ['copy it to an own place'],
        'Resources/Core/Build/UnitTestsBootstrap.php' => [],
        'Resources/Core/Build/FunctionalTestsBootstrap.php' => [],
        'Classes/Core/Testbase.php' => [
            // "a functional run needs database credentials in the environment",
            // and the message that does not name the variables it is missing
            'typo3DatabaseDriver',
            'typo3DatabaseHost',
            'typo3DatabaseName',
            'typo3DatabaseUsername',
            'typo3DatabasePassword',
            'Database credentials for tests are neither set through environment',
            // "$testExtensionsToLoad takes paths relative to the document root"
            'ORIGINAL_ROOT . $extensionPath',
            'Test extension path ',
        ],
        // "thrown by the testing framework's own package collection"
        'Classes/Core/PackageCollection.php' => ['depends on package '],
        // "it writes a sys_template row with the clear flag set"
        'Classes/Core/Functional/FunctionalTestCase.php' => ["'clear' => 3"],
    ];

    public function __invoke(OutputInterface $output): int
    {
        $checkouts = Checkouts::directory();

        return max(
            self::verifyFluidEngine($output, $checkouts),
            self::verifyTestingFramework($output, $checkouts),
            self::verifyTestSuites($output, $checkouts, TestSuiteHints::load()),
            self::verifyTranslationDomains($output, $checkouts),
        );
    }

    /**
     * Which Fluid engine each covered branch pins itself to.
     *
     * `D-VER-003` gave Fluid no version axis of its own, which holds only while a
     * branch admits exactly one engine major: the day one loosens its constraint
     * to span two, a `since:` on a Fluid statement stops naming an engine and
     * nothing about the statement changes to say so. The reading is printed
     * whether or not it fails, because this is where that number is looked up.
     */
    private static function verifyFluidEngine(OutputInterface $output, string $checkouts): int
    {
        Voice::heading($output, 'Fluid engine');
        $problems = 0;
        foreach (Versions::covered() as $version) {
            $manifest = $checkouts . '/' . $version['branch'] . '/composer.json';
            if (!is_file($manifest)) {
                Voice::problem($output, sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

                return 2;
            }

            $constraint = json_decode((string) file_get_contents($manifest), true)['require']['typo3fluid/fluid'] ?? null;
            if (!is_string($constraint)) {
                Voice::problem($output, sprintf('%s requires no typo3fluid/fluid', $version['branch']));
                ++$problems;
                continue;
            }

            // Asked over a window rather than parsed into a range, which is what
            // makes an open constraint visible: `^5.3.1` answers for one major
            // and `>=4` for every one in the window, and the second is the case
            // this exists to catch. The window is wide enough that a Fluid major
            // reaching its edge would be news in itself.
            $majors = array_values(array_filter(
                range(1, 20),
                static fn(int $major): bool => Versions::admits($constraint, $major),
            ));

            Voice::row($output, sprintf('%s %-10s %s', Voice::key($version['branch'], 5), $constraint, $majors === [] ? 'unreadable' : 'Fluid v' . implode(', v', $majors)));
            if (count($majors) !== 1) {
                ++$problems;
            }
        }

        return Voice::verdict($output, $problems, 'Every branch pins one Fluid engine major, so the TYPO3 major still carries it.', Voice::count($problems, 'branch', 'branches') . ' no longer pin one Fluid engine major — D-VER-003 says the engine needs a field of its own.');
    }

    /**
     * Whether the testing-framework release each branch pins still says what the
     * hints about it say.
     *
     * Which release each covered major is read against is derived rather than
     * recorded (`D-KNW-106`), and the needles below are what closes the gap
     * `D-KNW-002` named in reading against a tag at all: a release that changes
     * one of the statements inside a line.
     */
    private static function verifyTestingFramework(OutputInterface $output, string $checkouts): int
    {
        $harness = PinnedPackage::testingFramework();
        Voice::heading($output, $harness->package);
        $mirror = $harness->mirror($checkouts);
        if (!is_dir($mirror)) {
            Voice::problem($output, sprintf('No %s clone below %s — run bin/cli checkouts:update.', $harness->package, $checkouts));

            return 2;
        }

        $problems = 0;
        $read = [];
        foreach ($harness->pairing($checkouts) as $pair) {
            Voice::row($output, sprintf(
                '%s %-9s %s',
                Voice::key($pair['branch'], 5),
                $pair['constraint'] === '' ? 'no pin' : $pair['constraint'],
                $pair['ref'] ?? 'names no single release line',
            ));
            if ($pair['ref'] === null) {
                ++$problems;
                continue;
            }
            if (isset($read[$pair['ref']])) {
                continue;
            }

            $read[$pair['ref']] = true;
            $problems += self::readTestingFramework($output, $mirror, $pair);
        }
        Voice::row($output, sprintf('%s against %s', Voice::count(count($read), 'release line'), implode(', ', array_column(Versions::covered(), 'branch'))));

        return Voice::verdict($output, $problems, 'Every statement about the harness still reads as project-extension-tests states it.', Voice::count($problems, 'statement') . ' about the harness no longer read as project-extension-tests states them.');
    }

    /**
     * One release line, read where it is checked out.
     *
     * A worktree behind the line's newest tag is reported rather than read: the
     * release that moved it is precisely what this is looking for, and reading
     * the older one would answer for a version nobody installs any more.
     *
     * @param array{major: int, branch: string, constraint: string, line: ?string, ref: ?string, path: string} $pair
     */
    private static function readTestingFramework(OutputInterface $output, string $mirror, array $pair): int
    {
        $ref = (string) $pair['ref'];
        $checkedOut = PinnedPackage::revision($pair['path'], 'HEAD');
        if ($checkedOut === '') {
            Voice::problem($output, sprintf('%s is not checked out — run bin/cli checkouts:update', $ref));

            return 1;
        }
        if ($checkedOut !== PinnedPackage::revision($mirror, $ref)) {
            Voice::problem($output, sprintf('%s is checked out at %s — run bin/cli checkouts:update', $ref, substr($checkedOut, 0, 12)));

            return 1;
        }

        $problems = 0;
        foreach (self::TESTING_FRAMEWORK_EVIDENCE as $file => $needles) {
            $source = is_file($pair['path'] . '/' . $file) ? (string) file_get_contents($pair['path'] . '/' . $file) : null;
            if ($source === null) {
                Voice::problem($output, sprintf('%s: %s is gone', $ref, $file));
                ++$problems;
                continue;
            }
            foreach ($needles as $needle) {
                if (!str_contains($source, $needle)) {
                    Voice::problem($output, sprintf('%s: %s no longer says %s', $ref, $file, $needle));
                    ++$problems;
                }
            }
        }

        return $problems;
    }

    /**
     * The range each suite hint declares, against the script that has to have it.
     *
     * `since`/`until` on an entry in `knowledge/test-suite-hints.json` is a claim
     * about `Build/Scripts/runTests.sh`, and the whole of `R-KNW-024` rests on
     * it. The suite is taken from the command rather than from the `suite` field,
     * because the command is what has to run: `build-css` is an npm script, so
     * what the script needs to have is `npm`.
     *
     * @param array<int, array{suite: string, command: string, since: ?int, until: ?int}> $suites
     */
    private static function verifyTestSuites(OutputInterface $output, string $checkouts, array $suites): int
    {
        Voice::heading($output, 'Test suites');
        $covered = Versions::covered();
        $offered = [];
        foreach ($covered as $version) {
            $script = $checkouts . '/' . $version['branch'] . '/Build/Scripts/runTests.sh';
            if (!is_file($script)) {
                Voice::problem($output, sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

                return 2;
            }
            $offered[$version['major']] = self::suitesIn((string) file_get_contents($script));
        }

        $majors = array_column($covered, 'major');
        $problems = 0;
        $named = [];
        foreach ($suites as $entry) {
            $suite = self::suiteOf($entry['command'], $entry['suite']);
            $named[$suite] = true;
            $on = array_values(array_filter(
                $majors,
                static fn(int $major): bool => in_array($suite, $offered[$major], true),
            ));
            if ($on === []) {
                Voice::problem($output, sprintf(
                    '%s runs -s %s, which no covered branch offers',
                    $entry['suite'],
                    $suite,
                ));
                ++$problems;
                continue;
            }

            $read = [
                min($on) === min($majors) ? null : min($on),
                max($on) === max($majors) ? null : max($on),
            ];
            if ([$entry['since'], $entry['until']] !== $read) {
                Voice::problem($output, sprintf(
                    '%s declares %s and the script offers it on %s',
                    $entry['suite'],
                    Versions::label($entry['since'], $entry['until']) ?: 'every covered version',
                    implode(', ', $on),
                ));
                ++$problems;
            }
        }

        // Not a problem, and the reason it is printed: the hints are a curated
        // subset with a description and a domain each, so a suite nobody wrote
        // one for is a gap somebody may want to close rather than an error.
        $uncurated = array_values(array_diff(
            array_unique(array_merge(...array_values($offered))),
            array_keys($named),
        ));
        sort($uncurated);
        Voice::row($output, sprintf(
            '%d suites against %s, %d of them curated',
            count(array_unique(array_merge(...array_values($offered)))),
            implode(', ', array_column($covered, 'branch')),
            count($named),
        ));
        Voice::row($output, sprintf(
            'no hint names: %s',
            $uncurated === []
                ? 'nothing'
                : implode(', ', array_slice($uncurated, 0, self::NAMED_UNCURATED))
                    . (count($uncurated) > self::NAMED_UNCURATED ? sprintf(' and %d more', count($uncurated) - self::NAMED_UNCURATED) : ''),
        ));

        return Voice::verdict($output, $problems, 'Every suite hint holds on the versions runTests.sh offers it.', Voice::count($problems, 'suite hint') . ' no longer read as the script does.');
    }

    /**
     * The suites one `runTests.sh` offers: what its `-s` usage block lists, and
     * what its own `case` over the suite name accepts.
     *
     * The usage block alone was the reading until 2026-08-12, and it is the
     * documentation rather than the dispatch. 13.4 accepts `-s e2e-prepare` and
     * mentions it only inside the `e2e` line — "use e2e-prepare for manual
     * execution" — so a suite that runs there was read as absent, and the hint
     * saying it arrives with v13 was reported as the error. The same holds for
     * `accessibility-prepare`.
     *
     * A label carrying a `*` is left out: `build*` and `accessibility*` name no
     * suite that can be enumerated, and no hint runs a command they are the only
     * route to.
     *
     * Public because it is the one reading in this command a test can hold
     * without a checkout on disk.
     *
     * @return array<int, string>
     */
    public static function suitesIn(string $script): array
    {
        $names = [];
        if (preg_match('/Specifies the test suite to run\n(.*?)\n {4}-\S/s', $script, $block) === 1) {
            preg_match_all('/^\s+- ([A-Za-z][A-Za-z0-9_-]*)(?: \(default\))?:/m', $block[1], $listed);
            $names = $listed[1];
        }
        preg_match_all('/^ {4}([A-Za-z][A-Za-z0-9_-]*)\)$/m', $script, $dispatched);

        return array_values(array_unique(array_merge($names, $dispatched[1])));
    }

    /** What a hint's command asks the script for, falling back to what the entry calls itself. */
    private static function suiteOf(string $command, string $suite): string
    {
        return preg_match('/-s ([A-Za-z][A-Za-z0-9_-]*)/', $command, $named) === 1 ? $named[1] : $suite;
    }

    /**
     * Whether the major the translation domain answer is withheld below is still
     * the major the checkouts say the API arrived in.
     *
     * This is `D-DIS-004`'s first **Wrong if**, and it is read here because the
     * domain API backported into a 13.x patch makes the constant wrong by a
     * release rather than by an edit, which no test in this repository can see.
     * The class carrying the rules has been both `TranslationDomainMapper` and
     * `TranslationDomainResolver`, so the branch is asked for either. The reading
     * is printed whether or not it fails, because this is where that number is
     * looked up.
     */
    private static function verifyTranslationDomains(OutputInterface $output, string $checkouts): int
    {
        Voice::heading($output, 'Translation domains');
        $resolves = [];
        foreach (Versions::covered() as $version) {
            $directory = $checkouts . '/' . $version['branch'] . '/typo3/sysext/core/Classes/Localization';
            if (!is_dir($directory)) {
                Voice::problem($output, sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

                return 2;
            }

            $classes = [];
            foreach (Finder::create()->files()->in($directory)->depth(0)->name('TranslationDomain*.php')->sortByName() as $file) {
                $classes[] = $file->getFilename();
            }
            $resolves[$version['major']] = $classes !== [];
            Voice::row($output, sprintf(
                '%s %s',
                Voice::key($version['branch'], 5),
                $classes === [] ? 'no TranslationDomain* class' : implode(', ', $classes),
            ));
        }

        $found = RangeReport::since($resolves);
        Voice::row($output, sprintf(
            'withheld below v%d, resolved %s',
            TranslationDomainLookup::SINCE,
            $found === null ? 'on every covered version' : 'from v' . $found,
        ));

        return Voice::verdict(
            $output,
            $found === TranslationDomainLookup::SINCE ? 0 : 1,
            'Translation domains still arrive where TranslationDomainLookup withholds them.',
            sprintf(
                'TranslationDomainLookup::SINCE says v%d and the checkouts say %s — D-DIS-004 named this the release that makes it wrong.',
                TranslationDomainLookup::SINCE,
                $found === null ? 'every covered version resolves them' : 'v' . $found,
            ),
        );
    }
}
