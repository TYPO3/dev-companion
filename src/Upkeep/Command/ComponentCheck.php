<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Knowledge\Catalog\DemoMarkup;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Upkeep\Catalogs;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\ComponentDerivation;
use TYPO3\DevCompanion\Upkeep\RangeReport;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Whether the component catalog still says what the core checkouts say.
 *
 * Three readings over one subject: the range each entry is bound on, the demo
 * each one recorded, and the classes and elements `components:derive` wrote.
 * Nothing is written — a failure is an entry somebody has to reread, and the
 * new range is a judgement rather than a substitution (`D-CAT-001`).
 */
#[AsCommand(
    name: 'components:check',
    description: 'the versions each component entry holds on, the markup it was read off, and the derived classes and elements, against .checkouts/',
)]
final class ComponentCheck
{
    public function __invoke(OutputInterface $output): int
    {
        $checkouts = Checkouts::directory();

        return max(
            self::verifyBindings($output, $checkouts, Catalogs::read('component/entries')),
            self::verifyMarkup($output, $checkouts, Catalogs::read('component/entries')),
            self::verifyDerived($output, $checkouts),
        );
    }

    /**
     * Re-derives which majors each entry holds on and which its class list alone
     * holds on, and reports where either differs from what it records.
     *
     * An entry holds on a version when everything it describes is there: its Sass
     * sources, and every class and custom property it names that the newest covered
     * version has. Missing a custom property is not a detail — a caller pasting one
     * that does not exist gets CSS that silently does nothing.
     *
     * The class list is derived a second time without the custom properties, because
     * a caller asking about one class is not asking to paste the component
     * (`D-CAT-006`). It is the same reading over fewer names, so the two cannot
     * drift apart on what a checkout says.
     *
     * @param array<int, array<string, mixed>> $components
     */
    private static function verifyBindings(OutputInterface $output, string $checkouts, array $components): int
    {
        $covered = Versions::covered();
        $sources = [];
        foreach ($covered as $version) {
            $directory = $checkouts . '/' . $version['branch'] . '/Build/Sources';
            if (!is_dir($directory)) {
                Voice::problem($output, sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

                return 2;
            }
            // Two corpora, because the core writes the two kinds of name in
            // different places: a class or a custom property is in the Sass, and a
            // custom element's tag name is in the TypeScript that defines it.
            $sources[$version['major']] = [
                'scss' => self::readSources($directory . '/Sass', 'scss'),
                'ts' => self::readSources($directory . '/TypeScript', 'ts'),
            ];
        }

        $newest = end($covered)['major'];
        Voice::heading($output, 'Component bindings');
        $problems = 0;
        foreach ($components as $component) {
            $holds = [];
            $classesHold = [];
            foreach ($covered as $version) {
                $sourced = self::isSourcedOn($component, $sources, $version, $checkouts);
                $major = $version['major'];
                $holds[$major] = $sourced && self::writesEvery(self::contractOf($component), $sources, $major, $newest);
                $classesHold[$major] = $sourced && self::writesEvery(self::classesOf($component), $sources, $major, $newest);
            }

            $problems += self::reportBinding($output, $component, 'since', RangeReport::since($holds));
            $problems += self::reportBinding($output, $component, 'classesSince', RangeReport::since($classesHold));
        }
        Voice::row($output, sprintf('%d components against %s', count($components), implode(', ', array_column($covered, 'branch'))));

        return Voice::verdict($output, $problems, 'Every binding still says what the checkouts say.', Voice::count($problems, 'binding') . ' out of date.');
    }


    /**
     * One derived range against the one the entry records.
     *
     * @param array<string, mixed> $component
     */
    private static function reportBinding(OutputInterface $output, array $component, string $field, ?int $found): int
    {
        $recorded = isset($component[$field]) ? (int) $component[$field] : null;
        if ($found === $recorded) {
            return 0;
        }

        Voice::problem($output, sprintf(
            '%s: records %s%s, holds %s',
            $component['name'],
            $recorded === null ? 'no binding' : 'since v' . $recorded,
            $field === 'since' ? '' : ' for its class list',
            $found === null ? 'on every covered version' : 'from v' . $found,
        ));

        return 1;
    }

    /**
     * Whether the sources an entry was read off are on this version at all: its
     * Sass partials, or — for a custom element, which has none — the TypeScript
     * that defines its tag.
     *
     * @param array<string, mixed> $component
     * @param array<int, array{scss: string, ts: string}> $sources
     * @param array{major: int, branch: string, status: string} $version
     */
    private static function isSourcedOn(array $component, array $sources, array $version, string $checkouts): bool
    {
        foreach ($component['sassPaths'] ?? [] as $path) {
            if (!file_exists($checkouts . '/' . $version['branch'] . '/' . $path)) {
                return false;
            }
        }

        return ($component['sassPaths'] ?? []) !== []
            || self::carries($sources[$version['major']]['ts'], (string) $component['rootClass']);
    }

    /**
     * Whether this version's Sass writes every name the newest covered version
     * writes.
     *
     * Only what the newest covered version actually writes is asked about. A
     * Bootstrap class the core never spells out — btn-secondary comes from a state
     * map loop — is absent on every version and says nothing about which ones an
     * entry holds on.
     *
     * @param array<int, string> $named
     * @param array<int, array{scss: string, ts: string}> $sources
     */
    private static function writesEvery(array $named, array $sources, int $major, int $newest): bool
    {
        foreach ($named as $token) {
            if (self::carries($sources[$newest]['scss'], $token) && !self::carries($sources[$major]['scss'], $token)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Everything an entry describes, which is what a caller pastes: the custom
     * properties beside the classes.
     *
     * @param array<string, mixed> $component
     * @return array<int, string>
     */
    private static function contractOf(array $component): array
    {
        return array_merge($component['customProperties'] ?? [], self::classesOf($component));
    }

    /**
     * The class names an entry hands over, root class included — a class-shaped
     * query can name that one too.
     *
     * @param array<string, mixed> $component
     * @return array<int, string>
     */
    private static function classesOf(array $component): array
    {
        return array_merge(
            [(string) $component['rootClass']],
            $component['variants'] ?? [],
            $component['modifiers'] ?? [],
            $component['subComponents'] ?? [],
        );
    }

    private static function carries(string $haystack, string $token): bool
    {
        return preg_match('/(^|[^a-z0-9-])' . preg_quote($token, '/') . '([^a-z0-9-]|$)/', $haystack) === 1;
    }

    /** Every file of one extension below a directory, as one corpus to search. */
    private static function readSources(string $directory, string $extension): string
    {
        $sources = '';
        foreach (Finder::create()->files()->in($directory)->name('*.' . $extension)->sortByName() as $file) {
            $sources .= (string) file_get_contents($file->getPathname());
        }

        return $sources;
    }

    /**
     * Re-reads the markup each entry was read off, and reports every checkout
     * that no longer carries what the entry recorded.
     *
     * The binding above is derived from names, so a demo rewritten around the
     * same classes reads as unchanged — `D-CAT-001` named that as what would show
     * it wrong, and a digest per entry per checkout is what notices it. Nothing
     * is written: a failure is a demo somebody has to reread, and the new digest
     * is only true once the entry says what that demo now shows.
     *
     * @param array<int, array<string, mixed>> $components
     */
    private static function verifyMarkup(OutputInterface $output, string $checkouts, array $components): int
    {
        Voice::heading($output, 'Component markup');
        $covered = Versions::covered();
        $problems = 0;
        $withoutDemo = 0;
        $unnamed = 0;
        $suppressed = 0;
        foreach ($components as $component) {
            $demo = (string) ($component['demoPath'] ?? '');
            if ($demo === '') {
                ++$withoutDemo;
                continue;
            }

            if (($component['demoDerives'] ?? true) === false) {
                ++$suppressed;
            }

            $read = [];
            $names = false;
            foreach ($covered as $version) {
                $file = $checkouts . '/' . $version['branch'] . '/' . $demo;
                if (!is_dir($checkouts . '/' . $version['branch'])) {
                    Voice::problem($output, sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

                    return 2;
                }
                $file = self::demoFile($file);
                if ($file === null) {
                    continue;
                }
                $selector = (string) ($component['demoSelector'] ?? '');
                $template = (string) file_get_contents($file);
                // An entry that derives nothing is held to the whole file, for
                // the reason a demo with no example at all is: nothing is
                // handed over, so what a rewrite could change is whether the
                // judgment behind the entry still stands.
                $markup = ($component['demoDerives'] ?? true) === false
                    ? $template
                    : self::demoMarkup($template, (string) $component['rootClass'], $selector === '' ? null : $selector);
                $read[$version['major']] = substr(hash('sha256', $markup), 0, 12);
                $names = $names || DemoMarkup::carries($markup, (string) $component['rootClass']);
            }
            if ($read !== [] && !$names) {
                ++$unnamed;
            }

            $recorded = [];
            foreach ((array) ($component['markupDigests'] ?? []) as $major => $digest) {
                $recorded[(int) $major] = (string) $digest;
            }
            foreach (array_keys($read + $recorded) as $major) {
                $was = $recorded[$major] ?? null;
                $now = $read[$major] ?? null;
                if ($was === $now) {
                    continue;
                }
                Voice::problem($output, sprintf(
                    '%s: v%d records %s, and %s',
                    $component['name'],
                    $major,
                    $was ?? 'no markup',
                    $now === null ? $demo . ' is not there' : 'the demo reads ' . $now,
                ));
                ++$problems;
            }
        }
        Voice::row($output, sprintf(
            '%d demos against %s',
            count($components) - $withoutDemo,
            implode(', ', array_column($covered, 'branch')),
        ));
        Voice::row($output, sprintf(
            '%d of them name the component nowhere and %d entries name no demo, so their markup is held by nothing',
            $unnamed,
            $withoutDemo,
        ));
        Voice::row($output, sprintf(
            '%d show it nowhere copyable and say so, so the whole file is what moves under them',
            $suppressed,
        ));

        return Voice::verdict($output, $problems, 'Every demo still reads as its entry recorded it.', Voice::count($problems, 'demo') . ' no longer read as the entry records — reread them and record what they show.');
    }

    /**
     * The demo as this checkout spells it, or nothing where it has none.
     *
     * A branch older than the rename carries `.html` where the entry records
     * `.fluid.html`, and reading only the recorded spelling digested no demo at
     * all on that major — which read as four checkouts covered and was two.
     */
    private static function demoFile(string $path): ?string
    {
        foreach (DemoMarkup::spellings($path) as $spelling) {
            if (is_file($spelling)) {
                return $spelling;
            }
        }

        return null;
    }

    /**
     * What one checkout's demo says about one component.
     *
     * The examples carrying the component are the markup an installation would
     * hand a caller, so they are what the digest covers. Where a demo wraps none
     * in `sg:example`, the file itself is the demo: `Panels.fluid.html` and
     * `RecordSearchBox.fluid.html` are pages about one component rather than
     * galleries, so an edit anywhere in them is an edit to what the entry
     * describes.
     *
     * An entry's `demoSelector` narrows that the same way it narrows the
     * answer, because the two have to be the same reading: a digest over
     * examples nobody is handed would go on passing while the one example that
     * is handed over was rewritten underneath it.
     */
    private static function demoMarkup(string $template, string $rootClass, ?string $selector): string
    {
        $examples = DemoMarkup::examples($template, $rootClass, $selector);

        return $examples === [] ? $template : implode("\n", $examples);
    }

    /**
     * Whether the derived files still say what the checkouts say.
     *
     * `components:derive` writes them and nothing else does, so the only way
     * they go wrong is by not being run after a core release. Re-deriving and
     * comparing is the whole check, and it is cheap: the sources are committed
     * files — `D-CAT-008`.
     */
    private static function verifyDerived(OutputInterface $output, string $checkouts): int
    {
        Voice::heading($output, 'Derived classes and elements');
        $paths = [];
        foreach (Versions::covered() as $version) {
            $paths[$version['major']] = $checkouts . '/' . $version['branch'];
        }

        $derived = ComponentDerivation::from($paths);
        $stale = [];
        foreach (['classes' => 'component/classes', 'elements' => 'component/elements', 'listing' => 'component/styleguide'] as $key => $file) {
            if (Catalogs::read($file) !== json_decode((string) json_encode($derived[$key]), true)) {
                $stale[] = basename($file) . '.json';
            }
        }
        if ($stale !== []) {
            Voice::problem($output, sprintf(
                '%s no longer says what the checkouts say — run bin/cli components:derive',
                implode(', ', $stale),
            ));

            return 1;
        }

        $ships = ComponentDerivation::listing($paths);
        Voice::ok($output, sprintf(
            '%d classes and %d elements read as they are recorded, and %d components are listed by the styleguide on major %s.',
            count($derived['classes']),
            count($derived['elements']),
            count($derived['listing']),
            $ships === [] ? 'none' : implode(', ', $ships),
        ));

        return 0;
    }
}
