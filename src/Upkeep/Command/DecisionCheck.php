<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\DecisionStatus;
use TYPO3\DevCompanion\Upkeep\Entry;
use TYPO3\DevCompanion\Upkeep\Requirements;
use TYPO3\DevCompanion\Upkeep\Sources;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Everything the format of decisions/ promises a reader, checked against the
 * files.
 *
 * An id that agrees with its file name, its heading and its group, a date, a
 * status, a sentence to open with, fields from the fixed set in the order they
 * belong in, and something under **Wrong if**. `composer test` runs the same
 * check through `DecisionsTest`, the listing apart: that one is a property of
 * the whole checkout rather than of one branch, so it is held here alone —
 * `D-FBK-011`.
 */
#[AsCommand(
    name: 'decisions:check',
    description: 'hold the files to the shape the readme describes',
)]
final class DecisionCheck
{
    public function __invoke(OutputInterface $output): int
    {
        $problems = [];
        $seen = [];
        $successors = [];
        $known = [...Decisions::FIELDS, ...Decisions::laterFields()];

        foreach (Decisions::files() as $path) {
            $file = substr($path, strlen(Decisions::directory()) + 1);
            preg_match('/^([a-z]+)-(\d+[a-z]?)-/', basename($path, '.md'), $named);
            $expected = 'D-' . strtoupper($named[1] ?? '') . '-' . ($named[2] ?? '');

            $decision = Decisions::read($path);
            if ($decision['id'] === '') {
                $problems[] = $file . ' has no id';
                continue;
            }

            $id = $decision['id'];
            if ($id !== $expected) {
                $problems[] = $file . ' is named after ' . $expected . ' and says it is ' . $id;
            }
            if (preg_match('/^\d{3}[a-z]?$/', $named[2] ?? '') !== 1) {
                $problems[] = $file . ' is numbered ' . ($named[2] ?? '(nothing)') . ' and a number is three digits, which is what lists the files in order';
            }
            if ($decision['heading'] !== $id) {
                $problems[] = $id . ' has the heading of ' . $decision['heading'];
            }
            // The title is the front matter's and the heading repeats it, so a
            // rewrite of one is a rewrite of both — `D-DOC-045`.
            if ($decision['title'] === '') {
                $problems[] = $id . ' has no title in its front matter';
            } elseif ($decision['written'] !== $decision['title']) {
                $problems[] = $id . ' is titled "' . $decision['title'] . '" and its heading says "' . $decision['written'] . '"';
            } elseif (basename($path) !== Entry::fileName($id, $decision['title'])) {
                // The file name is the title, so a title corrected in place
                // leaves a file claiming the old one — `D-DOC-047`.
                $problems[] = $id . ' is titled "' . $decision['title'] . '" and filed as ' . basename($path)
                    . '; run bin/cli decisions:rename';
            }
            if (isset($seen[$id])) {
                $problems[] = $id . ' is claimed by ' . $seen[$id] . ' and by ' . $file
                    . ' — run bin/cli decisions:renumber on whichever of the two this branch added';
            }
            $seen[$id] = $file;

            $group = Decisions::GROUPS[substr($id, 2, 3)] ?? null;
            if ($group === null) {
                $problems[] = $id . ' has a prefix no group is named after';
            } elseif ($group !== $decision['group']) {
                $problems[] = $id . ' belongs in ' . $group . '/ and sits in ' . $decision['group'] . '/';
            }

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $decision['date']) !== 1) {
                $problems[] = $id . ' was decided on ' . ($decision['date'] === '' ? '(no date)' : $decision['date']);
            }
            if (DecisionStatus::tryFrom($decision['status']) === null) {
                $problems[] = $id . ' has the status ' . ($decision['status'] === '' ? '(none)' : $decision['status']);
            }
            if ($decision['title'] === '' || $decision['statement'] === '') {
                $problems[] = $id . ' does not open with what was decided';
            }

            $rank = -1;
            foreach ($decision['fields'] as $field) {
                if (!in_array($field, $known, true)) {
                    $problems[] = $id . ' carries a field nothing reads: ' . $field;
                    continue;
                }
                if (Decisions::rank($field) < $rank) {
                    $problems[] = $id . ' has ' . $field . ' below a field that belongs under it';
                }
                $rank = max($rank, Decisions::rank($field));
            }
            if (!in_array('Wrong if', $decision['fields'], true)) {
                $problems[] = $id . ' does not say what would show it to be wrong';
            }
            if (preg_match(Decisions::labelAsAParagraph(), (string) file_get_contents($path)) === 1) {
                $problems[] = $id . ' opens a line with a dated label in bold, and a dated label is a section';
            }

            // The status names the last dated line, not the only one: an entry
            // may be confirmed by one run and revoked by the next, and both
            // belong in the file. What a reader relies on is the latest.
            if ($decision['revokedBy'] !== '') {
                if (DecisionStatus::tryFrom($decision['status']) !== DecisionStatus::Revoked) {
                    $problems[] = $id . ' names what revoked it and is not revoked';
                }
                // Resolved after the loop: an entry is regularly revoked by one
                // written later, so the successor is not read yet.
                $successors[$id] = $decision['revokedBy'];
            }

            // A revoked entry names no test: its statement no longer describes
            // this server, so a test declaring it claims to hold something the
            // repository says it stopped doing — `D-DOC-052`.
            if (DecisionStatus::tryFrom($decision['status']) === DecisionStatus::Revoked && $decision['tests'] !== []) {
                $problems[] = $id . ' is revoked and ' . count($decision['tests']) . ' tests declare they hold it'
                    . ($decision['revokedBy'] === '' ? '' : ' — the attribute belongs on ' . $decision['revokedBy']);
            }

            foreach (Decisions::overTheMeasure($path) as $label => $count) {
                $problems[] = $id . ' has a ' . $label . ' of ' . $count . ' lines, and a reading is '
                    . Decisions::READING_MEASURE . ' — what does not fit is a finding rather than prose to trim';
            }

            foreach ($decision['readings'] as $reading) {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $reading) !== 1) {
                    $problems[] = $id . ' carries a reading that is not a date: ' . $reading;
                }
            }

            $dated = Decisions::datedLines($decision['fields']);
            $latest = $dated === [] ? '' : $dated[count($dated) - 1];
            $later = Decisions::fieldFor($decision['status']);
            if ($later !== $latest) {
                $problems[] = $latest === ''
                    ? $id . ' is ' . $decision['status'] . ' and carries no ' . $later . ' line'
                    : $id . ' is ' . $decision['status'] . ' and its last dated line is ' . $latest;
            }
        }

        foreach ($successors as $id => $successor) {
            if (!isset($seen[$successor])) {
                $problems[] = $id . ' is revoked by ' . $successor . ', which no decision has';
            }
        }

        foreach (['', ...array_values(Decisions::GROUPS)] as $group) {
            $readme = Decisions::directory() . '/' . ($group === '' ? '' : $group . '/') . 'readme.md';
            if (!is_file($readme)) {
                $problems[] = $group . '/ has no readme';
                continue;
            }
            if (!str_ends_with((string) file_get_contents($readme), Decisions::listing($group))) {
                $problems[] = ($group === '' ? 'readme.md' : $group . '/readme.md')
                    . ' is not the listing of its files — run bin/cli decisions:index';
            }
        }

        $requirements = Requirements::all();
        $held = Sources::held('Decision');
        foreach (Decisions::files() as $path) {
            $contents = (string) file_get_contents($path);
            preg_match_all('/`(R-[A-Z]{3}-\d+[a-z]?)`/', $contents, $matches);
            foreach ($matches[1] as $requirement) {
                if (!isset($requirements[$requirement])) {
                    $problems[] = basename($path) . ' names ' . $requirement . ', which no requirement has';
                }
            }

            // `coveredBy` is generated from the `#[Decision]` attributes the
            // tests carry, so what is held here is that the copy still says
            // what the source does. A test renamed, moved or given another
            // entry leaves the front matter behind otherwise, which is the
            // drift `D-DOC-043` measured.
            if (Entry::withNames($contents, 'coveredBy', $held[Decisions::read($path)['id']] ?? []) !== $contents) {
                $problems[] = basename($path) . ' carries a coveredBy the tests do not write — run bin/cli decisions:cover';
            }
        }

        foreach (array_keys($held) as $id) {
            if (!isset($seen[$id])) {
                $problems[] = 'a test declares it holds ' . $id . ', which no decision has';
            }
        }

        // The other report, and the one that says which entries can go stale
        // without anything noticing: a test naming it in `coveredBy` is the
        // only thing that fails when the behaviour an entry describes moves.
        $uncovered = Decisions::uncovered();
        if ($uncovered !== []) {
            Voice::heading($output, sprintf(
                '%d entries point at this code and name no test that would catch it moving — '
                . '%d open, %d confirmed, and each one for a reason in the entry (D-DOC-053)',
                count($uncovered),
                count(array_filter($uncovered, static fn(array $e): bool => $e['status'] === 'open')),
                count(array_filter($uncovered, static fn(array $e): bool => $e['status'] === 'confirmed')),
            ));
            foreach (array_slice($uncovered, 0, 3) as $entry) {
                Voice::row($output, sprintf(
                    '%s %-10s names %d of our classes',
                    Voice::key($entry['id'], 11),
                    $entry['status'],
                    $entry['names'],
                ));
            }
        }
        foreach ($problems as $problem) {
            Voice::problem($output, $problem);
        }
        return Voice::verdict($output, count($problems), sprintf('%d decisions, %s', count($seen), Voice::count(count($problems), 'problem')));
    }
}
