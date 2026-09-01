<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Entry;
use TYPO3\DevCompanion\Upkeep\Requirements;
use TYPO3\DevCompanion\Upkeep\RequirementState;
use TYPO3\DevCompanion\Upkeep\Sources;

/**
 * The shape of requirements/, as far as one branch can be right about it.
 *
 * The listing at the foot of a group readme is deliberately absent: it is
 * generated from every file in the group, a branch adding one may not touch it,
 * and holding it here made a session choose between its own work and a green
 * suite (D-FBK-011). `bin/cli requirements:check` holds it, on the checkout
 * where it can be true.
 */
final class RequirementsTest extends TestCase
{
    /**
     * An id is the name a commit, a feedback and a scenario refer to a requirement
     * by. It decides the group directory and the file name, so two entries
     * cannot quietly share one — which five of them did, unnoticed, for as long
     * as the whole list was a single document.
     */
    #[Test]
    public function everyRequirementIsFoundUnderTheIdItGoesBy(): void
    {
        $requirements = Requirements::all();
        $duplicates = Requirements::duplicates();

        self::assertNotSame([], $requirements);
        // The ids rather than the whole map: what a reader of this failure gets
        // is the message, and PHPUnit's diff under it would repeat every path
        // the message already names, in a tail `todo:home` cuts at 30 lines.
        self::assertSame([], array_keys($duplicates), Requirements::collision($duplicates));

        foreach ($requirements as $id => $requirement) {
            self::assertSame($id, $requirement['heading'], $id . ' has another id in its heading');
            self::assertStringStartsWith(
                strtolower(substr($id, 2)) . '-',
                $requirement['file'],
                $id . ' is not the name of its file',
            );
            self::assertSame(
                Requirements::GROUPS[substr($id, 2, 3)] ?? null,
                $requirement['group'],
                $id . ' sits in a group its prefix does not name',
            );
        }
    }

    /**
     * The message is all the reader of that failure gets, and there is no
     * command to send them to — so it names both files and says that the move
     * is by hand. Held here rather than by reading it, because the checkout it
     * fails on is the one checkout where nothing collides — `D-FBK-046`.
     */
    #[Decision('D-FBK-046')]
    #[Test]
    public function aDuplicateIdNamesBothFilesAndThatNothingMovesOne(): void
    {
        $collision = Requirements::collision([
            'R-COD-003' => ['requirements/code/cod-003-one.md', 'requirements/code/cod-003-two.md'],
        ]);

        self::assertStringContainsString('requirements/code/cod-003-one.md', $collision);
        self::assertStringContainsString('requirements/code/cod-003-two.md', $collision);
        self::assertStringContainsString('by hand', $collision);
        self::assertSame('', Requirements::collision([]), 'a checkout without a collision says nothing');
    }

    /**
     * The number is the only part of an id a listing sorts on, and three digits
     * is what makes sorting it as text the same as sorting it as a number.
     * Unpadded, `dis-10` sat between `dis-1` and `dis-2` in every directory
     * listing and in every generated index — `D-DOC-005`.
     */
    #[Decision('D-DOC-005')]
    #[Test]
    public function everyNumberIsThreeDigitsWideSoAGroupListsInOrder(): void
    {
        $groups = [];

        foreach (Requirements::all() as $id => $requirement) {
            self::assertMatchesRegularExpression(
                '/^R-[A-Z]{3}-\d{3}[a-z]?$/',
                $id,
                $id . ' is numbered in something other than three digits',
            );
            $groups[$requirement['group']][] = $requirement['file'];
        }

        self::assertNotSame([], $groups);

        foreach ($groups as $group => $files) {
            $asText = $files;
            sort($asText, SORT_STRING);
            $asNumbers = $files;
            usort($asNumbers, static fn(string $a, string $b): int => strnatcmp($a, $b));

            self::assertSame($asNumbers, $asText, $group . '/ lists in another order than it is numbered');
        }
    }

    /**
     * The bold first sentence is the requirement, and a reader who stops there
     * has read the whole demand. Everything else in the file explains it.
     */
    #[Test]
    public function everyRequirementOpensWithTheSentenceThatHasToHold(): void
    {
        foreach (Requirements::all() as $id => $requirement) {
            self::assertNotSame('', $requirement['title'], $id . ' has no title');
            self::assertNotSame('', $requirement['statement'], $id . ' states nothing');
            self::assertContains($requirement['status'], RequirementState::writtenValues(), $id . ' has no usable status');
        }
    }

    /**
     * A judgement is what takes an unguarded entry out of
     * `bin/cli unresolved:list` without anything being built for it, so what it
     * says has to be the day it was made: the entry can be rewritten under a
     * stamp, and the date is the only thing that lets a reader notice —
     * `D-DOC-038`.
     */
    #[Decision('D-DOC-038')]
    #[Test]
    public function aJudgementIsTheDateItWasMadeOn(): void
    {
        foreach (Requirements::all() as $id => $requirement) {
            if ($requirement['judged'] === '') {
                continue;
            }

            self::assertMatchesRegularExpression(
                '/^\d{4}-\d{2}-\d{2}$/',
                $requirement['judged'],
                $id . ' is judged ' . $requirement['judged'] . ', which is not a date',
            );
        }
    }

    /**
     * The sections are what a reader navigates an entry by, and the order
     * carries meaning: where the demand came from is evidence, and what holds
     * it there is the claim the suite keeps — `D-DOC-004`.
     */
    #[Decision('D-DOC-004')]
    #[Test]
    public function everyRequirementIsWrittenInTheSectionsTheFormatHas(): void
    {
        foreach (Requirements::files() as $path) {
            $requirement = Requirements::read($path);
            $rank = -1;
            foreach (Requirements::fields((string) file_get_contents($path)) as $field) {
                self::assertContains(
                    $field,
                    Requirements::FIELDS,
                    $requirement['id'] . ' carries a section nothing reads: ' . $field,
                );
                $position = (int) array_search($field, Requirements::FIELDS, true);
                self::assertGreaterThan(
                    $rank,
                    $position,
                    $requirement['id'] . ' has ' . $field . ' below a section that belongs under it',
                );
                $rank = $position;
            }
        }
    }

    /**
     * A requirement that is not open says what holds it: the tests that declare
     * it, or a section saying nothing does — `D-DOC-004`, `D-DOC-049`.
     */
    #[Decision('D-DOC-004')]
    #[Decision('D-DOC-049')]
    #[Test]
    public function everyRequirementNamesWhatHoldsIt(): void
    {
        foreach (Requirements::all() as $id => $requirement) {
            if (RequirementState::tryFrom($requirement['status']) === RequirementState::Open) {
                continue;
            }

            self::assertTrue(
                $requirement['tests'] !== [] || str_contains($requirement['heldBy'], 'not guarded'),
                $id . ' names neither a test nor that it is not guarded',
            );
        }
    }

    /**
     * The two ends are one source: the test declares `#[Requirement]` and
     * `heldBy` is generated from it — `D-DOC-049`, which is `D-DOC-048` for the
     * corpus that says what must be true.
     */
    #[Decision('D-DOC-049')]
    #[Test]
    public function everyRequirementSaysWhatTheTestsHoldingItDeclare(): void
    {
        $held = Sources::held('Requirement');
        $stale = [];
        foreach (Requirements::files() as $path) {
            $contents = (string) file_get_contents($path);
            if (Entry::withNames($contents, 'heldBy', $held[Requirements::read($path)['id']] ?? []) !== $contents) {
                $stale[] = basename($path);
            }
        }

        self::assertSame([], $stale, 'a heldBy the tests do not write — run bin/cli requirements:cover');
        self::assertSame(
            [],
            array_values(array_diff(array_keys($held), array_keys(Requirements::all()))),
            'a test declares it holds a requirement no entry has',
        );
    }

    /**
     * A scenario holds itself to the requirements it names. One that names a
     * requirement nobody can read any more is a claim about nothing.
     */
    #[Test]
    public function everyRequirementAScenarioNamesExists(): void
    {
        $requirements = Requirements::all();

        foreach (Finder::create()->files()->in(Paths::root() . '/scenarios')->name('*.md')->sortByName() as $file) {
            preg_match_all('/`(R-[A-Z]{3}-\d+[a-z]?)`/', (string) file_get_contents($file->getPathname()), $matches);
            foreach ($matches[1] as $id) {
                self::assertArrayHasKey(
                    $id,
                    $requirements,
                    $file->getFilename() . ' names ' . $id . ', which no requirement has',
                );
            }
        }
    }

}
