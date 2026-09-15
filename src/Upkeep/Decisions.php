<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

/**
 * Reads decisions/, where every decision is one file.
 *
 * It was one document of thirty entries, newest first, and by the end neither
 * half of that was true. Two entries had arrived at the foot of the file, and
 * the labels a reader navigates by had drifted into thirteen spellings of four
 * things. An id now decides the group directory and the file name, the listings
 * come from the files, and the fields are a fixed set. So the decision about
 * versions is a directory away rather than a search through prose.
 */
final class Decisions
{
    /**
     * The heading the revoked entries stand under, in every listing. They keep
     * a run of their own rather than a marker in the one above, which is what
     * the listing's own comment says why.
     */
    public const REVOKED = 'Revoked, and kept as the record';

    /**
     * The prefix of an id says what the decision is about, and that is the
     * directory it lives in. A new prefix is a new group and belongs here in
     * the same commit that writes the first entry under it.
     *
     * @var array<string, string>
     */
    public const GROUPS = [
        'AUD' => 'audience',
        'DIS' => 'discovery',
        'ANS' => 'answers',
        'KNW' => 'knowledge',
        'VER' => 'versions',
        'CAT' => 'catalog',
        'SCO' => 'scope',
        'GUI' => 'guides',
        'EVI' => 'evidence',
        'SKL' => 'task-skills',
        'FBK' => 'feedback',
        'DOC' => 'documentation',
        'COD' => 'code',
    ];

    /**
     * The labels an entry may carry, in the order they have to appear. The
     * evidence at hand comes before the decision on it, the assumptions it
     * rests on come after, and what would show it wrong follows them. What
     * would catch that closes the entry. Everything below that line arrived
     * later than the entry did.
     *
     * The tests that hold an entry are `coveredBy` in its front matter, because
     * nothing runs over them. Where a test would catch the **Wrong if**, its
     * name is what turns the promise into something the suite keeps. A renamed
     * test then fails a check instead of leaves the claim an orphan.
     *
     * @var array<int, string>
     */
    public const FIELDS = ['Evidence', 'Decided', 'Assumed', 'Wrong if'];

    /**
     * The lines a dated section runs to.
     *
     * It is there for the finding: a **Wrong if** that fired, a statement that
     * stopped to describe this server, a boundary that moved. A read that found
     * none of those is a date in `readings:` and no section at all. That keeps
     * an entry a decision rather than a journal of its own uses, `D-DOC-066`.
     */
    public const READING_MEASURE = 12;

    /**
     * The labels a later session adds. The dated ones belong to
     * `DecisionStatus`, which is what says whether a reader may still build on
     * the entry. `Since then` carries what followed without a date of its own
     * and says nothing about that.
     *
     * @return array<int, string>
     */
    public static function laterFields(): array
    {
        return [...DecisionStatus::lines(), 'Since then'];
    }

    /**
     * The dated lines an entry may carry, most recent last. The status names
     * the last of them.
     *
     * @param array<int, string> $fields
     * @return array<int, string>
     */
    public static function datedLines(array $fields): array
    {
        return array_values(array_filter(
            $fields,
            static fn(string $field): bool => in_array($field, DecisionStatus::lines(), true),
        ));
    }

    /**
     * The dated sections of one entry that run past the measure, longest
     * first.
     *
     * @return array<string, int>
     */
    public static function overTheMeasure(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];
        $pattern = '/^## (' . implode('|', self::laterFields()) . ')\b/';
        $sections = [];
        $label = '';
        foreach ($lines as $line) {
            if (preg_match($pattern, $line, $named) === 1) {
                $label = trim(substr($line, 3));
                $sections[$label] = 0;
                continue;
            }
            if ($label !== '' && trim($line) !== '') {
                ++$sections[$label];
            }
        }
        $over = array_filter($sections, static fn(int $count): bool => $count > self::READING_MEASURE);
        arsort($over);

        return $over;
    }

    /**
     * The decisions a test holds, which is what a red one prints.
     *
     * @return list<array{id: string, title: string, file: string}>
     */
    public static function restingOn(string $class, string $method): array
    {
        return Entry::restingOn(self::all(), 'decisions', $class, $method);
    }

    /**
     * Entries that point at this repository's code and that no test holds, most
     * references first.
     *
     * Not a defect and nothing fails on it. Most entries here are about process
     * and nothing runs over them. So an entry may name `Scope::of()` in its
     * evidence while it decides something no test could keep. A check that
     * demanded a `coveredBy` would get a test name chosen to satisfy it.
     *
     * What it reports is the one tie that holds an entry to the code. A test
     * named in `coveredBy` fails when the behaviour moves, and
     * `DecisionsTest::everyTestADecisionNamesExists` fails when the test goes
     * with it. Read on 2026-08-22: the three entries found stale that day
     * carried no such name. The two whose code had moved under them carried one
     * and were right.
     *
     * A revoked entry stays out. Its statement is not the case any more, so no
     * test may declare it, `D-DOC-052`. A count of it here would report as
     * absent what the checks forbid.
     *
     * The number that remains is not a backlog. The corpus was swept on
     * 2026-08-23 and what stayed uncovered stayed for a reason written in the
     * entry, which is what `D-DOC-053` records.
     *
     * @return array<int, array{id: string, names: int, status: string}>
     */
    public static function uncovered(): array
    {
        $classes = Sources::classes();

        $uncovered = [];
        foreach (self::all() as $decision) {
            if ($decision['tests'] !== [] || DecisionStatus::tryFrom($decision['status']) === DecisionStatus::Revoked) {
                continue;
            }
            $body = (string) file_get_contents(self::directory() . '/' . $decision['group'] . '/' . $decision['file']);
            preg_match_all('/`(\w+)::\w+/', $body, $matches);
            $named = array_filter(array_unique($matches[1]), static fn(string $class): bool => isset($classes[$class]));
            if ($named === []) {
                continue;
            }
            $uncovered[] = [
                'id' => $decision['id'],
                'names' => count($named),
                'status' => $decision['status'],
            ];
        }

        usort($uncovered, static fn(array $a, array $b): int => [$b['names'], $a['id']] <=> [$a['names'], $b['id']]);

        return $uncovered;
    }

    /**
     * A later label that opens a line in bold, which is a section in the form
     * of a paragraph.
     *
     * Derived from `laterFields()` rather than spelled out, so a fourth label
     * added there counts here too. Anchored to the start of a line: a **Since
     * then** named inside a sentence is a reference to a section and not one.
     */
    public static function labelAsAParagraph(): string
    {
        return '/^(- )?\*\*(' . implode('|', array_map(preg_quote(...), self::laterFields())) . ')\*\*/m';
    }

    public static function directory(): string
    {
        return Paths::root() . '/decisions';
    }

    /**
     * Every decision, keyed by id and newest first — which is the order the
     * file this replaces claimed to be in.
     *
     * @return array<string, array{id: string, group: string, file: string, heading: string, written: string, title: string, date: string, status: string, revokedBy: string, tests: list<string>, readings: list<string>, revisited: bool, statement: string, fields: array<int, string>}>
     */
    public static function all(): array
    {
        $decisions = [];
        foreach (self::files() as $path) {
            $decision = self::read($path);
            $decisions[$decision['id']] = $decision;
        }

        uasort($decisions, static fn(array $a, array $b): int => [$b['date'], $a['id']] <=> [$a['date'], $b['id']]);

        return $decisions;
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
            $decision = self::read($path);
            $claims[$decision['id']][] = basename(self::directory())
                . '/' . $decision['group'] . '/' . $decision['file'];
        }

        return array_filter($claims, static fn(array $paths): bool => count($paths) > 1);
    }

    /**
     * What the failure says, which is all a reader of it gets. `todo:home`
     * prints the tail of a red `composer ci` and adds nothing to it. So the id,
     * both files and the command that repairs it stand in the message. Not on
     * the page the reader would have to know to open, `D-FBK-046`.
     *
     * @param array<string, array<int, string>> $duplicates
     */
    public static function collision(array $duplicates): string
    {
        if ($duplicates === []) {
            return '';
        }

        $message = "two decision files claim the same id\n";
        foreach ($duplicates as $id => $paths) {
            $message .= "\n    " . $id . "\n";
            foreach ($paths as $path) {
                $message .= '        ' . $path . "\n";
            }
            $message .= '    bin/cli decisions:renumber <the file this branch added>' . "\n";
        }

        return $message . "\nName the file rather than the id: both carry it, so the id says which number\n"
            . 'is meant and not which entry moves. The command names what it cannot move.';
    }

    /**
     * The decisions of one group, or every one of them where the caller names
     * no group.
     *
     * @return array<string, array{id: string, group: string, file: string, heading: string, written: string, title: string, date: string, status: string, revokedBy: string, tests: list<string>, readings: list<string>, revisited: bool, statement: string, fields: array<int, string>}>
     */
    public static function group(string $group): array
    {
        if ($group === '') {
            return self::all();
        }

        return array_filter(self::all(), static fn(array $d): bool => $d['group'] === $group);
    }

    /**
     * The table under a readme. Generated rather than written, because a
     * listing maintained by hand is a second copy of the directory that only
     * says what was true once. The root listing carries the group as well, and
     * is the one place every decision is in one order.
     */
    public static function listing(string $group): string
    {
        // Two lists rather than one, because the first question a reader has of
        // a listing this long is which of it is still true. A revoked entry is
        // kept and read — the wrong assumption is the useful part — but it is
        // not something to build on, and mixed into one list it looked exactly
        // like something to build on. The whole of it goes by group rather than
        // as one run of every entry: an id names its group in the prefix, which
        // is what a commit and a requirement's `restsOn` arrive with. Inside a
        // group the order stays the same and newest first. The late decisions
        // across all of them are `bin/cli decisions:list`.
        $sections = $group === '' ? array_fill_keys(array_values(self::GROUPS), []) : ['' => []];
        $sections[self::REVOKED] = [];

        foreach (self::group($group) as $decision) {
            $status = DecisionStatus::tryFrom($decision['status']);
            $entry = [
                'ref' => $decision['id'],
                'path' => ($group === '' ? $decision['group'] . '/' : '') . $decision['file'],
                'says' => sprintf('%s · %s', $decision['title'], $decision['date'])
                    . ($status === DecisionStatus::Confirmed ? ' · confirmed' : '')
                    . ($decision['revokedBy'] === '' ? '' : ' → ' . $decision['revokedBy']),
            ];
            $sections[$status?->stillHolds() === false ? self::REVOKED : ($group === '' ? $decision['group'] : '')][] = $entry;
        }

        $listing = '';
        foreach ($sections as $heading => $entries) {
            if ($entries === []) {
                continue;
            }
            $listing .= ($heading === '' ? '' : '### ' . $heading . "\n\n") . Listing::render($entries) . "\n";
        }

        return rtrim($listing, "\n") . "\n";
    }

    /**
     * Every decision file, readmes excluded.
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
     * @return array{id: string, group: string, file: string, heading: string, written: string, title: string, date: string, status: string, revokedBy: string, tests: list<string>, readings: list<string>, revisited: bool, statement: string, fields: array<int, string>}
     */
    public static function read(string $path): array
    {
        $contents = (string) file_get_contents($path);
        $head = Entry::head($contents);
        $matter = $head['matter'];

        return [
            'id' => Entry::value($matter, 'id'),
            'group' => basename(dirname($path)),
            'file' => basename($path),
            'heading' => $head['heading'],
            'written' => $head['written'],
            'title' => Entry::value($matter, 'title'),
            'date' => Entry::value($matter, 'date'),
            'status' => Entry::value($matter, 'status'),
            // What replaced it, where a revoked entry has a successor. A reader
            // who reaches a dead entry needs somewhere to go next, and prose
            // said it on four of them and nowhere a listing could see.
            'revokedBy' => Entry::value($matter, 'revokedBy'),
            'tests' => Entry::names($matter, 'coveredBy'),
            // The dates of the readings that changed nothing. They are data
            // rather than a section each, because what a reader needs from one
            // is that it happened and when — `D-DOC-066`.
            'readings' => Entry::names($matter, 'readings'),
            'revisited' => self::revisited($contents, Entry::names($matter, 'readings')),
            'statement' => $head['statement'],
            'fields' => self::fields($contents),
        ];
    }

    /**
     * Whether somebody has been back to this entry since its commit.
     *
     * `status` cannot answer it. `confirmed` and `revoked` are the two reads
     * that settle a **Wrong if**. A read that settles neither leaves the entry
     * `open`, the same as one nobody has opened.
     *
     * Two things tell those apart, because such a read writes one or the other.
     * A **Since then** where it changed something, a date in `readings:` where
     * it changed nothing, `D-DOC-066`. A read of only the section counted the
     * second kind as unopened. It sent the next session to the entry it had
     * just come out of.
     *
     * One spelling of the section, since the 51 labels that still stood as a
     * bold paragraph changed. `bin/cli decisions:check` then began to fail on
     * that spelling. A **Since then** named inside a sentence is a reference to
     * one and not one, which is why the heading is what this matches.
     *
     * @param list<string> $readings
     */
    private static function revisited(string $contents, array $readings): bool
    {
        return $readings !== [] || preg_match('/^## Since then\b/m', $contents) === 1;
    }

    /**
     * The sections an entry carries, in the order it carries them, with the
     * date of a later addition folded away. `Revoked on 2026-07-31` is the
     * section `Revoked on`, and the date belongs to the entry rather than to
     * the shape.
     *
     * They were bullets with a bold label, and the label repeated once an entry
     * made more than one decision. A section says it once — `D-DOC-003`.
     *
     * @return array<int, string>
     */
    public static function fields(string $contents): array
    {
        preg_match_all('/^## (.+)$/m', $contents, $matches);

        return array_map(static function (string $label): string {
            foreach (self::laterFields() as $later) {
                if (str_starts_with($label, $later . ' ')) {
                    return $later;
                }
            }

            return $label;
        }, $matches[1]);
    }

    /**
     * Where a field sits in the order of an entry. Everything a later session
     * added ranks last and behind all of them, whichever of the three it is.
     */
    public static function rank(string $field): int
    {
        $rank = array_search($field, self::FIELDS, true);

        return $rank === false ? count(self::FIELDS) : $rank;
    }

    /** The dated line the named status promises, or '' where it names none. */
    public static function fieldFor(string $status): string
    {
        return DecisionStatus::tryFrom($status)?->line() ?? '';
    }

}
