<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

/**
 * Reads requirements/, where every requirement is one file.
 *
 * The file used to be one document, and five ids went out twice before anybody
 * noticed. That is what a list nobody can index looks like from the inside. An
 * id now decides the group directory and the file name, so the check below is a
 * comparison rather than a search. The group listing comes from the files
 * instead of a hand beside them.
 */
final class Requirements
{
    /**
     * The prefix of an id says what the requirement is about, and that is the
     * directory it lives in. A new prefix is a new group and belongs here in
     * the same commit that writes the first entry under it.
     *
     * @var array<string, string>
     */
    public const GROUPS = [
        'AUD' => 'audience',
        'DIS' => 'discovery',
        'ANS' => 'answers',
        'DOC' => 'documentation',
        'SKL' => 'task-skills',
        'PRJ' => 'project',
        'SCO' => 'scope',
        'GUI' => 'guides',
        'FBK' => 'feedback',
        'KNW' => 'knowledge',
        'COD' => 'code',
    ];

    /**
     * The sections an entry consists of, in the order they have to appear.
     * Where the demand came from is evidence and comes first; what holds it
     * there is the claim the suite keeps, and closes the entry.
     *
     * @var array<int, string>
     */
    public const FIELDS = ['From', 'Held by'];

    /**
     * The sections a file carries, in the order it carries them.
     *
     * @return array<int, string>
     */
    public static function fields(string $contents): array
    {
        preg_match_all('/^## (.+)$/m', $contents, $matches);

        return $matches[1];
    }

    public static function directory(): string
    {
        return Paths::root() . '/requirements';
    }

    /**
     * Every requirement, keyed and sorted by id.
     *
     * @return array<string, array{id: string, group: string, file: string, heading: string, written: string, title: string, status: string, judged: string, restsOn: array<int, string>, statement: string, heldBy: string, tests: list<string>}>
     */
    public static function all(): array
    {
        $requirements = [];
        foreach (self::files() as $path) {
            $requirement = self::read($path);
            $requirements[$requirement['id']] = $requirement;
        }

        uksort($requirements, static fn(string $a, string $b): int => strnatcmp($a, $b));

        return $requirements;
    }

    /**
     * The ids more than one file claims, each with the files that claim it,
     * relative to the root. `all()` keys by id and keeps whichever of the two
     * it read last, so the collision is only visible from the files.
     *
     * @return array<string, array<int, string>>
     */
    public static function duplicates(): array
    {
        $claims = [];
        foreach (self::files() as $path) {
            $requirement = self::read($path);
            $claims[$requirement['id']][] = basename(self::directory())
                . '/' . $requirement['group'] . '/' . $requirement['file'];
        }

        return array_filter($claims, static fn(array $paths): bool => count($paths) > 1);
    }

    /**
     * What the failure says, which is all a reader of it gets. `todo:home`
     * prints the tail of a red `composer ci` and adds nothing to it. Nothing
     * renumbers a requirement, so the message names the files and says that the
     * move is by hand. `D-FBK-046`, and `D-DOC-015` for what by hand costs.
     *
     * @param array<string, array<int, string>> $duplicates
     */
    public static function collision(array $duplicates): string
    {
        if ($duplicates === []) {
            return '';
        }

        $message = "two requirement files claim the same id\n";
        foreach ($duplicates as $id => $paths) {
            $message .= "\n    " . $id . "\n";
            foreach ($paths as $path) {
                $message .= '        ' . $path . "\n";
            }
        }

        return $message . "\nNothing renumbers a requirement: the entry this branch added is moved by hand,"
            . "\nand every file naming its id is read against `git diff main -- <file>`.";
    }

    /**
     * The requirements of one group, in the order its listing shows them.
     *
     * @return array<string, array{id: string, group: string, file: string, heading: string, written: string, title: string, status: string, judged: string, restsOn: array<int, string>, statement: string, heldBy: string, tests: list<string>}>
     */
    public static function group(string $group): array
    {
        if ($group === '') {
            return self::all();
        }

        return array_filter(self::all(), static fn(array $r): bool => $r['group'] === $group);
    }

    /**
     * The table under a group readme: what must hold, and what state it is in.
     * Generated rather than written, because a listing maintained by hand is a
     * second copy of the directory that only says what was true once.
     */
    public static function listing(string $group): string
    {
        if ($group !== '') {
            return Listing::render(self::entries($group, ''));
        }

        // The whole of it goes by group rather than as one run of 184 lines:
        // the id already sorts that way, and the heading is what says where the
        // run the reader is in stops.
        $listing = '';
        foreach (self::GROUPS as $name) {
            $entries = self::entries($name, $name . '/');
            if ($entries === []) {
                continue;
            }

            $listing .= '### ' . $name . "\n\n" . Listing::render($entries) . "\n";
        }

        return rtrim($listing, "\n") . "\n";
    }

    /**
     * One group as a listing renders it.
     *
     * The prefix is what the readme under construction stands in: nothing from
     * the group's own, the group's directory from the one above it.
     *
     * @return array<int, array{ref: string, path: string, says: string}>
     */
    private static function entries(string $group, string $prefix): array
    {
        $entries = [];
        foreach (self::group($group) as $requirement) {
            $state = self::state($requirement);
            $entries[] = [
                'ref' => $requirement['id'],
                'path' => $prefix . $requirement['file'],
                'says' => sprintf(
                    '%s · %s',
                    $requirement['title'],
                    $state === RequirementState::Open ? '**open**' : $state->value,
                ),
            ];
        }

        return $entries;
    }

    /**
     * `open` stands in the file and not in the code. Everything else has the
     * tests it names, or says outright that nothing holds it. The third state
     * is the one worth a look in a listing, because it looks exactly like the
     * first from afar.
     *
     * The three words are the state itself, and a reader that wants one of them
     * emphasised says so where it renders. Bolding `open` in here is what made
     * every caller strip the asterisks back off.
     *
     * @param array{status: string, heldBy: string, tests: list<string>} $requirement
     */
    public static function state(array $requirement): RequirementState
    {
        if (RequirementState::tryFrom($requirement['status']) === RequirementState::Open) {
            return RequirementState::Open;
        }

        return $requirement['tests'] === [] ? RequirementState::NotGuarded : RequirementState::Held;
    }

    /**
     * The requirements a test holds, which is what a red one prints.
     *
     * @return list<array{id: string, title: string, file: string}>
     */
    public static function restingOn(string $class, string $method): array
    {
        return Entry::restingOn(self::all(), 'requirements', $class, $method);
    }

    /**
     * Every requirement file, group readmes excluded.
     *
     * @return array<int, string>
     */
    public static function files(): array
    {
        $directories = array_values(array_filter(
            array_map(static fn(string $group): string => self::directory() . '/' . $group, self::GROUPS),
            is_dir(...),
        ));
        if ($directories === []) {
            return [];
        }

        $paths = [];
        foreach (Finder::create()->files()->in($directories)->depth(0)->name('*.md')->notName('readme.md')->sortByName() as $file) {
            $paths[] = $file->getPathname();
        }

        return $paths;
    }

    /**
     * One file. Read on its own rather than through all(), which keys by id and
     * would hide the second file that claims one.
     *
     * @return array{id: string, group: string, file: string, heading: string, written: string, title: string, status: string, judged: string, restsOn: array<int, string>, statement: string, heldBy: string, tests: list<string>}
     */
    public static function read(string $path): array
    {
        $contents = (string) file_get_contents($path);
        $head = Entry::head($contents);
        $matter = $head['matter'];

        // What holds the entry and is not a test: a `bin/cli` command, a half
        // nothing guards, a clause that says what one of the tests holds. The
        // tests themselves are the front matter, generated from the
        // `#[Requirement]` attributes they carry — `D-DOC-049`.
        $heldBy = self::field($contents, 'Held by');

        return [
            'id' => Entry::value($matter, 'id'),
            'group' => basename(dirname($path)),
            'file' => basename($path),
            'heading' => $head['heading'],
            'written' => $head['written'],
            'title' => Entry::value($matter, 'title'),
            'status' => Entry::value($matter, 'status'),
            // The day a session read this entry, found nothing holds it, and
            // decided it stays that way. `bin/cli unresolved:list` names what
            // nobody has answered for, and a todo that names the id was the
            // only answer it could see — so a requirement no test can hold,
            // which is a legitimate state, could never leave the read.
            'judged' => Entry::value($matter, 'judged'),
            // The decisions this requirement stands on. A decision can be
            // revoked without any notice that a requirement rested on it, which
            // is the silent case decisions/ exists to prevent.
            'restsOn' => Entry::names($matter, 'restsOn'),
            'statement' => $head['statement'],
            'heldBy' => $heldBy,
            // A test method where one holds it, a whole test class where the
            // class is the answer — `VersionsTest` in full is a claim about
            // every method in it, and a list of them one at a time would go
            // stale on the next one written.
            'tests' => Entry::names($matter, 'heldBy'),
        ];
    }


    /**
     * A section's body, to the next heading or the end of the file.
     *
     * They were bold labels on a paragraph until 2026-08-02. `Held by` names
     * more than one test on 60 of the 123 entries and nine on one of them. A
     * comma-separated sentence is not what that is, see `D-DOC-004`.
     */
    private static function field(string $contents, string $label): string
    {
        if (preg_match('/^## ' . preg_quote($label, '/') . '$\R(.*?)(?=^## |\z)/ms', $contents, $matches) !== 1) {
            return '';
        }

        return trim((string) preg_replace('/\s+/', ' ', $matches[1]));
    }
}
