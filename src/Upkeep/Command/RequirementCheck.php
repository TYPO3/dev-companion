<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\Entry;
use TYPO3\DevCompanion\Upkeep\Requirements;
use TYPO3\DevCompanion\Upkeep\RequirementState;
use TYPO3\DevCompanion\Upkeep\Sources;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Everything the format of requirements/ promises a reader, checked against the
 * files.
 *
 * An id that agrees with its file name, its heading and its group, a statement
 * to open with, a status, and tests that exist behind what claims to be held.
 * `composer test` runs the same check through `RequirementsTest`, the listing
 * apart: that one can only be true on a checkout that has every file in the
 * group, so it is held here alone — `D-FBK-011`.
 */
#[AsCommand(
    name: 'requirements:check',
    description: 'hold the files to the shape the readme describes',
)]
final class RequirementCheck
{
    public function __invoke(OutputInterface $output): int
    {
        $problems = [];
        $decisions = Decisions::all();
        $held = Sources::held('Requirement');
        $seen = [];

        foreach (Requirements::files() as $path) {
            $file = substr($path, strlen(Requirements::directory()) + 1);
            preg_match('/^([a-z]+)-(\d+[a-z]?)-/', basename($path, '.md'), $named);
            $expected = 'R-' . strtoupper($named[1] ?? '') . '-' . ($named[2] ?? '');

            $requirement = Requirements::read($path);
            if ($requirement['id'] === '') {
                $problems[] = $file . ' has no id';
                continue;
            }

            $id = $requirement['id'];
            if ($id !== $expected) {
                $problems[] = $file . ' is named after ' . $expected . ' and says it is ' . $id;
            }
            if (preg_match('/^\d{3}[a-z]?$/', $named[2] ?? '') !== 1) {
                $problems[] = $file . ' is numbered ' . ($named[2] ?? '(nothing)') . ' and a number is three digits, which is what lists the files in order';
            }
            if ($requirement['heading'] !== $id) {
                $problems[] = $id . ' has the heading of ' . $requirement['heading'];
            }
            // The title is the front matter's and the heading repeats it, so a
            // rewrite of one is a rewrite of both — `D-DOC-045`.
            if ($requirement['title'] === '') {
                $problems[] = $id . ' has no title in its front matter';
            } elseif ($requirement['written'] !== $requirement['title']) {
                $problems[] = $id . ' is titled "' . $requirement['title'] . '" and its heading says "' . $requirement['written'] . '"';
            } elseif (basename($path) !== Entry::fileName($id, $requirement['title'])) {
                // The file name is the title, so a title corrected in place
                // leaves a file claiming the old one — `D-DOC-047`.
                $problems[] = $id . ' is titled "' . $requirement['title'] . '" and filed as ' . basename($path)
                    . '; run bin/cli requirements:rename';
            }
            if (isset($seen[$id])) {
                $problems[] = $id . ' is claimed by ' . $seen[$id] . ' and by ' . $file;
            }
            $seen[$id] = $file;

            // `heldBy` is generated from the `#[Requirement]` attributes the
            // tests carry, so what is held here is that the copy still says
            // what the source does — `D-DOC-049`.
            $contents = (string) file_get_contents($path);
            if (Entry::withNames($contents, 'heldBy', $held[$id] ?? []) !== $contents) {
                $problems[] = $file . ' carries a heldBy the tests do not write — run bin/cli requirements:cover';
            }
            // A bullet that is only a test name is the front matter written a
            // second time. What the section is for is what is not a test — a
            // `bin/cli` command, a half nothing guards, or the clause on the
            // next line saying what one of the tests holds, which is a bullet
            // this leaves alone.
            if (preg_match('/^## Held by$\R(.*?)(?=^## |\z)/ms', $contents, $section) === 1) {
                preg_match_all('/^- `(\w+Test(?:::\w+)?)`$(?:\R(?!  \S)|\z)/m', $section[1], $bare);
                foreach ($bare[1] as $test) {
                    $problems[] = $id . ' names ' . $test . ' under Held by, and the tests are the front matter';
                }
            }

            $group = Requirements::GROUPS[substr($id, 2, 3)] ?? null;
            if ($group === null) {
                $problems[] = $id . ' has a prefix no group is named after';
            } elseif ($group !== $requirement['group']) {
                $problems[] = $id . ' belongs in ' . $group . '/ and sits in ' . $requirement['group'] . '/';
            }

            if (!in_array($requirement['status'], RequirementState::writtenValues(), true)) {
                $problems[] = $id . ' has the status ' . ($requirement['status'] === '' ? '(none)' : $requirement['status']);
            }
            // A date rather than a word, because a judgement is about the entry
            // as it read on the day it was made, and the entry can be rewritten
            // under it. Nothing catches that happening; the date is what lets a
            // reader notice.
            if ($requirement['judged'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $requirement['judged']) !== 1) {
                $problems[] = $id . ' is judged ' . $requirement['judged'] . ', and a judgement is the date it was made on';
            }
            foreach ($requirement['restsOn'] as $decision) {
                // Whether the decision still holds is a reading rather than a
                // failure, and bin/cli unresolved:list is where that is read out.
                // What fails here is a pointer at nothing.
                if (!isset($decisions[$decision])) {
                    $problems[] = $id . ' rests on ' . $decision . ', which no decision has';
                }
            }
            if ($requirement['title'] === '' || $requirement['statement'] === '') {
                $problems[] = $id . ' does not open with the sentence that has to hold';
            }

            if (RequirementState::tryFrom($requirement['status']) === RequirementState::Open) {
                continue;
            }
            if ($requirement['tests'] === [] && !str_contains($requirement['heldBy'], 'not guarded')) {
                $problems[] = $id . ' names neither a test nor that it is not guarded';
            }
        }

        foreach (array_keys($held) as $id) {
            if (!isset($seen[$id])) {
                $problems[] = 'a test declares it holds ' . $id . ', which no requirement has';
            }
        }

        foreach (Requirements::GROUPS as $group) {
            $readme = Requirements::directory() . '/' . $group . '/readme.md';
            if (!is_file($readme)) {
                $problems[] = $group . '/ has no readme';
                continue;
            }
            if (!str_ends_with((string) file_get_contents($readme), Requirements::listing($group))) {
                $problems[] = $group . '/readme.md is not the listing of its files — run bin/cli requirements:index';
            }
        }

        foreach (self::scenarioReferences() as $id => $where) {
            if (!isset($seen[$id])) {
                $problems[] = $where . ' names ' . $id . ', which no requirement has';
            }
        }
        foreach ($problems as $problem) {
            Voice::problem($output, $problem);
        }
        return Voice::verdict($output, count($problems), sprintf('%d requirements, %s', count($seen), Voice::count(count($problems), 'problem')));
    }


    /**
     * The requirement ids the scenario suite names, with the file that names them.
     * A scenario that holds itself to a withdrawn requirement is a claim about
     * something nobody can read any more.
     *
     * @return array<string, string>
     */
    private static function scenarioReferences(): array
    {
        $references = [];
        $root = Paths::root();
        foreach (Finder::create()->files()->in($root . '/scenarios')->name('*.md')->sortByName() as $file) {
            preg_match_all('/`(R-[A-Z]{3}-\d+[a-z]?)`/', (string) file_get_contents($file->getPathname()), $matches);
            foreach ($matches[1] as $id) {
                $references[$id] ??= substr($file->getPathname(), strlen($root) + 1);
            }
        }

        return $references;
    }
}
