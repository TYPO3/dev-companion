<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\DecisionStatus;
use TYPO3\DevCompanion\Upkeep\Entry;
use TYPO3\DevCompanion\Upkeep\Requirements;
use TYPO3\DevCompanion\Upkeep\Sources;

/**
 * The shape of decisions/, as far as one branch can be right about it.
 *
 * What every entry is on its own, its id, its group, its date, its status, its
 * fields, is here. A session on one todo can satisfy all of it. What only the
 * whole checkout can be right about is not. The listing at the foot of a group
 * readme derives from every file in that group. A branch that adds one may not
 * touch it (D-FBK-011). `bin/cli decisions:check` holds that half, and the
 * merge is what runs it.
 */
final class DecisionsTest extends TestCase
{
    /**
     * An id is the name a commit, a feedback and a later decision refer to this
     * one by. It decides the group directory and the file name, so two entries
     * cannot share one without a word. The single document this replaces had no
     * way to notice that.
     */
    #[Test]
    public function everyDecisionIsFoundUnderTheIdItGoesBy(): void
    {
        $decisions = Decisions::all();
        $duplicates = Decisions::duplicates();

        self::assertNotSame([], $decisions);
        // The ids rather than the whole map. What a reader of this failure gets
        // is the message. PHPUnit's diff under it would repeat every path the
        // message already names, in a tail `todo:home` cuts at 30 lines.
        self::assertSame([], array_keys($duplicates), Decisions::collision($duplicates));

        foreach ($decisions as $id => $decision) {
            self::assertSame($id, $decision['heading'], $id . ' has another id in its heading');
            self::assertStringStartsWith(
                strtolower(substr($id, 2)) . '-',
                $decision['file'],
                $id . ' is not the name of its file',
            );
            self::assertSame(
                Decisions::GROUPS[substr($id, 2, 3)] ?? null,
                $decision['group'],
                $id . ' sits in a group its prefix does not name',
            );
        }
    }

    /**
     * The collision is the one failure parallel work predicts, and the message
     * is all its reader gets. A size mismatch between two counts was what it
     * used to be. Held here rather than by a read of it, because the checkout
     * it fails on is the one checkout where nothing collides.
     */
    #[Decision('D-FBK-046')]
    #[Test]
    public function aDuplicateIdNamesBothFilesAndTheCommandThatMovesOne(): void
    {
        $collision = Decisions::collision([
            'D-FBK-046' => ['decisions/feedback/fbk-046-one.md', 'decisions/feedback/fbk-046-two.md'],
        ]);

        self::assertStringContainsString('decisions/feedback/fbk-046-one.md', $collision);
        self::assertStringContainsString('decisions/feedback/fbk-046-two.md', $collision);
        // The id is what the message cannot end on. The command refuses one two
        // files claim, because it says which number it means and not which of
        // them moves.
        self::assertStringContainsString('bin/cli decisions:renumber <the file this branch added>', $collision);
        self::assertStringNotContainsString('decisions:renumber D-FBK-046', $collision);
        self::assertSame('', Decisions::collision([]), 'a checkout without a collision says nothing');
    }

    /**
     * The number is the only part of an id a listing sorts on. Three digits is
     * what makes a sort as text the same as a sort as a number.
     * `Decisions::all()` compares ids as text, so unpadded it put `D-FBK-10`
     * between `D-FBK-1` and `D-FBK-2` in the generated readme as well —
     * `D-DOC-005`.
     */
    #[Decision('D-DOC-005')]
    #[Test]
    public function everyNumberIsThreeDigitsWideSoAGroupListsInOrder(): void
    {
        $groups = [];

        foreach (Decisions::all() as $id => $decision) {
            self::assertMatchesRegularExpression(
                '/^D-[A-Z]{3}-\d{3}[a-z]?$/',
                $id,
                $id . ' is numbered in something other than three digits',
            );
            $groups[$decision['group']][] = $decision['file'];
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
     * The bold first sentence is the decision, and a reader who stops there
     * knows what the entry settled. The date is what makes the entry findable a
     * year later, when the words of the title are not what anybody remembers.
     */
    #[Test]
    public function everyDecisionOpensWithWhatWasDecided(): void
    {
        foreach (Decisions::all() as $id => $decision) {
            self::assertNotSame('', $decision['title'], $id . ' has no title');
            self::assertNotSame('', $decision['statement'], $id . ' decides nothing');
            self::assertMatchesRegularExpression(
                '/^\d{4}-\d{2}-\d{2}$/',
                $decision['date'],
                $id . ' does not say when it was decided',
            );
            self::assertNotNull(DecisionStatus::tryFrom($decision['status']), $id . ' has no usable status');
        }
    }

    /**
     * The fields are what a reader navigates an entry by, and they had drifted
     * into thirteen spellings of four things before this. The order carries
     * meaning too: the evidence comes before the decision on it, and everything
     * below **Wrong if** arrived later than the entry did.
     */
    #[Test]
    public function everyDecisionIsWrittenInTheFieldsTheFormatHas(): void
    {
        $known = [...Decisions::FIELDS, ...Decisions::laterFields()];

        foreach (Decisions::all() as $id => $decision) {
            $rank = -1;
            foreach ($decision['fields'] as $field) {
                self::assertContains($field, $known, $id . ' carries a field nothing reads: ' . $field);
                self::assertGreaterThanOrEqual(
                    $rank,
                    Decisions::rank($field),
                    $id . ' has ' . $field . ' below a field that belongs under it',
                );
                $rank = max($rank, Decisions::rank($field));
            }
        }
    }

    /**
     * No dated section runs past the measure.
     *
     * It was a report while the sweep compacted the corpus onto the rule, and
     * the sweep ended on 2026-08-28. So the check fails on one now, which is
     * what keeps the next read from an account again (`D-DOC-066`).
     */
    #[Decision('D-DOC-066')]
    #[Test]
    public function noDatedSectionRunsPastTheMeasure(): void
    {
        $over = [];
        foreach (Decisions::files() as $path) {
            foreach (Decisions::overTheMeasure($path) as $label => $count) {
                $over[] = basename($path) . ' — ' . $label . ': ' . $count;
            }
        }

        self::assertSame([], $over, 'a reading is ' . Decisions::READING_MEASURE . ' lines');
    }

    /**
     * Every read a file records comes out of it as a date.
     *
     * A bare date is a `DateTimeImmutable` once the parser has been over it,
     * and the reader kept only scalars. So the field `D-DOC-066` introduced
     * read as nothing, and an entry recorded the new way still reported as one
     * nobody has opened.
     */
    #[Decision('D-DOC-066')]
    #[Test]
    public function everyReadingAnEntryRecordsIsReadAsADate(): void
    {
        $recorded = 0;
        foreach (Decisions::all() as $id => $decision) {
            $path = Decisions::directory() . '/' . $decision['group'] . '/' . $decision['file'];
            $written = preg_match_all(
                '/^  - (\d{4}-\d{2}-\d{2})$/m',
                self::frontMatter((string) file_get_contents($path)),
                $dates,
            );

            self::assertSame($written, count($decision['readings']), $id . ' records ' . $written . ' readings and reports ' . count($decision['readings']));
            self::assertSame($dates[1], $decision['readings'], $id . ' reports readings the file does not carry');
            $recorded += $written;
        }

        self::assertGreaterThan(0, $recorded, 'no entry records a reading, which the corpus would have to say instead');
    }

    /**
     * The front matter as typed, which is where a read stands.
     */
    private static function frontMatter(string $contents): string
    {
        return (string) preg_replace('/\A---\R(.*?)\R---\R.*/s', '$1', $contents);
    }

    /**
     * What an entry that points at this code owes comes out as a report, never
     * as a failure.
     *
     * A test named in `coveredBy` is the one tie that holds. It fails when the
     * behaviour moves, and `everyTestADecisionNamesExists` fails when the test
     * goes with the code. The three entries found stale on 2026-08-22 named no
     * such test, and the two whose code had moved under them named one and were
     * right. So the read is the absence of a test and not the age of the entry.
     *
     * Most entries here decide something about process and no test could keep
     * them, which is why nothing may fail on this. A demand for a `coveredBy`
     * would get a name chosen to satisfy it — `D-DOC-043`.
     */
    #[Decision('D-DOC-043')]
    #[Decision('D-DOC-053')]
    #[Test]
    public function anEntryNamingThisCodeWithNoTestIsReadOut(): void
    {
        $uncovered = Decisions::uncovered();

        self::assertNotSame([], $uncovered, 'every entry naming this code names a test, which the report would have to say instead');

        // No test may declare a revoked entry at all. So a report of it as
        // short of one would ask for what `D-DOC-052` forbids — `D-DOC-053`.
        foreach ($uncovered as $entry) {
            self::assertNotSame(
                DecisionStatus::Revoked,
                DecisionStatus::tryFrom($entry['status']),
                $entry['id'] . ' is revoked and is reported as naming no test',
            );
        }

        $named = array_column($uncovered, 'names');
        $sorted = $named;
        rsort($sorted);
        self::assertSame($sorted, $named, 'the entry naming the most of our classes is not first');

        $classes = Sources::classes();
        foreach ($uncovered as $entry) {
            $decision = Decisions::all()[$entry['id']];
            $body = (string) file_get_contents(Decisions::directory() . '/' . $decision['group'] . '/' . $decision['file']);
            self::assertSame([], $decision['tests'], $entry['id'] . ' names a test and is reported as naming none');
            preg_match_all('/`(\w+)::\w+/', $body, $matches);
            self::assertNotSame(
                [],
                array_filter(array_unique($matches[1]), static fn(string $class): bool => isset($classes[$class])),
                $entry['id'] . ' is reported as pointing at this code and points at none',
            );
        }
    }

    /**
     * The names read from the failure end: which entries a test held.
     *
     * This is what a session that made a test red goes to, so it has to answer
     * for every entry that names one. A test nothing names answers with
     * nothing, which is the ordinary case and the one that must stay quiet —
     * `D-DOC-043`. `D-DOC-044` is what prints it when a test fails.
     */
    #[Decision('D-DOC-043')]
    #[Decision('D-DOC-044')]
    #[Decision('D-DOC-045')]
    #[Test]
    public function everyEntryATestHoldsIsNamedFromTheFailingEnd(): void
    {
        // Read once and asked 866 times. `Decisions::restingOn()` reads all of
        // `decisions/` per call, which per named test is the corpus read
        // hundreds of times and 23 of the suite's 130 seconds.
        $all = Decisions::all();

        $missed = [];
        foreach ($all as $decision) {
            foreach ($decision['tests'] as $test) {
                // A name without a method is a class that holds the entry
                // throughout. The attribute allows that and the front matter
                // writes it as the bare class.
                [$class, $method] = array_pad(explode('::', $test), 2, '');
                $held = Entry::restingOn($all, 'decisions', $class, $method);
                if (!in_array($decision['id'], array_column($held, 'id'), true)) {
                    $missed[] = $decision['id'] . ' names ' . $test . ' and is not held from it';
                }
            }
        }

        self::assertSame([], $missed);
        self::assertSame([], Decisions::restingOn('NoSuchTest', 'noSuchMethod'), 'a test nothing names holds nothing');
    }

    /**
     * A revoked entry names no test, because its statement is no longer true.
     *
     * Nine of the eleven that did on 2026-08-23 named a test the successor
     * already declared, and one named a test that disproves it — `D-DOC-052`.
     */
    #[Decision('D-DOC-052')]
    #[Test]
    public function aRevokedEntryNamesNoTest(): void
    {
        $claimed = [];
        foreach (Decisions::all() as $id => $decision) {
            if (DecisionStatus::tryFrom($decision['status']) === DecisionStatus::Revoked && $decision['tests'] !== []) {
                $claimed[] = $id . ' is revoked and declared by ' . implode(', ', $decision['tests']);
            }
        }

        self::assertSame([], $claimed, 'a revoked statement is held by a test that would have to prove it');
    }

    /**
     * The two ends are one source: the attribute stands in the test and
     * `coveredBy` derives from it.
     *
     * Both stood by hand until 2026-08-23, and the corpus is what that costs.
     * 405 of the tests an entry named said nothing about the entry. So a
     * session that changed the behaviour and fixed the test never learned which
     * entry had rested on it — `D-DOC-048`.
     */
    #[Decision('D-DOC-048')]
    #[Test]
    public function everyEntrySaysWhatTheTestsHoldingItDeclare(): void
    {
        $held = Sources::held('Decision');
        $stale = [];
        foreach (Decisions::files() as $path) {
            $contents = (string) file_get_contents($path);
            if (Entry::withNames($contents, 'coveredBy', $held[Decisions::read($path)['id']] ?? []) !== $contents) {
                $stale[] = basename($path);
            }
        }

        self::assertSame([], $stale, 'a coveredBy the tests do not write — run bin/cli decisions:cover');
        self::assertSame(
            [],
            array_values(array_diff(array_keys($held), array_keys(Decisions::all()))),
            'a test declares it holds a decision no entry has',
        );
    }

    /**
     * A dated label is a section, and the form it had before `D-DOC-003` is
     * what nothing could read. 51 bold labels in 37 entries survived that move
     * because no check saw them. The field order could not place one, so it sat
     * wherever the writer put it. Four of them were bullets that
     * `Unresolved::decisions()` did not count as a read at all.
     */
    #[Test]
    public function noDatedLabelIsWrittenAsABoldParagraph(): void
    {
        $written = [];
        foreach (Decisions::files() as $path) {
            if (preg_match(Decisions::labelAsAParagraph(), (string) file_get_contents($path), $matches) === 1) {
                $written[] = basename($path) . ': ' . trim($matches[0]);
            }
        }

        self::assertSame([], $written, 'a dated label opens a line in bold, and a dated label is a section');
    }

    #[Test]
    public function everyDecisionSaysWhatWouldShowItToBeWrong(): void
    {
        foreach (Decisions::all() as $id => $decision) {
            self::assertContains(
                'Wrong if',
                $decision['fields'],
                $id . ' does not say what would show it to be wrong',
            );
        }
    }

    /**
     * `confirmed` and `revoked` are claims about a later read, and the line
     * that carries it has to be in the file. The status names the **last** of
     * them rather than the only one. One run may confirm an entry and the next
     * revoke it, and what a reader relies on is the latest — `D-DOC-003`.
     */
    #[Decision('D-DOC-003')]
    #[Test]
    public function aStatusNamesTheLastDatedLineInTheFile(): void
    {
        foreach (Decisions::all() as $id => $decision) {
            $dated = Decisions::datedLines($decision['fields']);
            $latest = $dated === [] ? '' : $dated[count($dated) - 1];

            self::assertSame(
                Decisions::fieldFor($decision['status']),
                $latest,
                $id . ' is ' . $decision['status'] . ' and its last dated line is '
                    . ($latest === '' ? 'none' : $latest),
            );
        }
    }

    /**
     * A test named in a decision is a claim that something would catch the
     * **Wrong if** when it comes. A test under a new name turns it into a claim
     * nobody answers for, which reads exactly like one that still holds.
     * `coveredBy` derives from the tests and cannot say a name they do not.
     * What this reaches is every test named by the way, whose claim goes stale
     * the same way — `D-DOC-003`.
     */
    #[Decision('D-DOC-003')]
    #[Test]
    public function everyTestADecisionNamesExists(): void
    {
        $methods = $this->testMethods();

        foreach (Decisions::all() as $id => $decision) {
            preg_match_all(
                '/\b([A-Z]\w*Test::\w+)/',
                (string) file_get_contents(Decisions::directory() . '/' . $decision['group'] . '/' . $decision['file']),
                $matches,
            );
            foreach (array_unique($matches[1]) as $named) {
                self::assertContains($named, $methods, $id . ' names ' . $named . ', which no test declares');
            }
        }
    }

    /**
     * A decision nobody has been back to names no command the console lost.
     *
     * `Cli::knows()` answered this for a todo's `run:` key and for nothing
     * else, so a deleted command stayed on record as the way to do the thing.
     * Nothing failed, because nothing asked — `D-DOC-037` has the case.
     *
     * The head only, the statement and the paragraphs above the first section.
     * Below it an entry is an account of the decision and the rejected options,
     * and the entry that removes a command names it there of necessity. And
     * only where no dated section stands: a head left under one is a question
     * about how to keep a record rather than a name nothing holds.
     */
    #[Decision('D-DOC-037')]
    #[Test]
    public function anUnvisitedDecisionNamesNoCommandTheConsoleLost(): void
    {
        $lost = [];
        foreach (Decisions::all() as $id => $decision) {
            if ($decision['status'] === DecisionStatus::Revoked->value
                || array_intersect(Decisions::laterFields(), $decision['fields']) !== []
            ) {
                continue;
            }

            $contents = (string) file_get_contents(Decisions::directory() . '/' . $decision['group'] . '/' . $decision['file']);
            $head = (preg_split('/^## /m', (string) preg_replace('/^---\R.*?\R---\R/s', '', $contents), 2) ?: [''])[0];
            preg_match_all('#bin/cli [a-z]+:[a-z]+#', $head, $matches);
            foreach (array_unique($matches[0]) as $named) {
                if (!Cli::knows($named)) {
                    $lost[] = $id . ' opens by naming `' . $named . '`, which the console does not have';
                }
            }
        }

        self::assertSame([], $lost);
    }

    /**
     * @return array<int, string>
     */
    private function testMethods(): array
    {
        $methods = [];
        foreach (['Unit', 'Contract', 'Smoke'] as $suite) {
            foreach (Finder::create()->files()->in(Paths::root() . '/tests/' . $suite)->depth(0)->name('*Test.php')->sortByName() as $file) {
                preg_match_all('/public function (\w+)\(/', (string) file_get_contents($file->getPathname()), $matches);
                foreach ($matches[1] as $method) {
                    $methods[] = $file->getBasename('.php') . '::' . $method;
                }
            }
        }

        return $methods;
    }

    /**
     * A decision that names a requirement reasons from it. One that names a
     * requirement nobody can read any more reasons from nothing.
     */
    #[Test]
    public function everyRequirementADecisionNamesExists(): void
    {
        $requirements = Requirements::all();

        foreach (Decisions::files() as $path) {
            preg_match_all('/`(R-[A-Z]{3}-\d+[a-z]?)`/', (string) file_get_contents($path), $matches);
            foreach ($matches[1] as $id) {
                self::assertArrayHasKey(
                    $id,
                    $requirements,
                    basename($path) . ' names ' . $id . ', which no requirement has',
                );
            }
        }
    }
}
