<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\DecisionStatus;
use TYPO3\DevCompanion\Upkeep\Requirements;
use TYPO3\DevCompanion\Upkeep\Todo;
use TYPO3\DevCompanion\Upkeep\Unresolved;

final class UnresolvedTest extends TestCase
{
    /**
     * The read exists so that nothing unfinished can be invisible, which only
     * holds if it is the whole of the unfinished work. A read that shows some
     * of it is worse than none: it reads as a complete list.
     */
    #[Test]
    public function everyRequirementNothingAnswersForIsInTheReading(): void
    {
        $expected = [];
        foreach (Requirements::all() as $requirement) {
            if (!Requirements::state($requirement)->isGuarded()) {
                // The value rather than the case: the read prints, and it
                // declares `state` as a string. A comparison of the enum passed
                // for as long as every requirement had a guard. The two empty
                // arrays never said which side was right.
                $expected[$requirement['id']] = Requirements::state($requirement)->value;
            }
        }

        $reported = [];
        foreach (Unresolved::requirements() as $requirement) {
            $reported[$requirement['id']] = $requirement['state'];
        }

        self::assertSame($expected, $reported);
    }

    /**
     * A queued todo that names the id is the whole tie between what must be
     * true and the order the work happens in. An entry nobody has queued is the
     * case the read exists for. That flag backwards would hide exactly the
     * entries it is meant to show.
     *
     * It is what the *queue* names, not what `todo/` contains. The directory
     * also keeps the page that lists what stays out of the queue on purpose. An
     * id named there has a decision in the opposite direction.
     *
     * Read as two lists rather than entry by entry. The read is empty on any
     * day every requirement has a guard. A loop over nothing is a test that
     * reports a pass while it holds no such thing.
     */
    #[Test]
    public function anEntryIsQueuedWhenAnItemNamesIt(): void
    {
        $reading = Unresolved::requirements();

        $flagged = array_column(array_filter($reading, static fn(array $r): bool => $r['queued']), 'id');
        $named = array_values(array_intersect(array_column($reading, 'id'), Todo::serves()));

        self::assertSame($named, $flagged);
    }

    /**
     * The second answer, and the one the read could not see before this. A
     * requirement no test can hold is a legitimate state, so an entry whose
     * **Held by** says so stays in the listing for good. Every session that ran
     * `judge what nothing has answered for` derived the same judgement about
     * the same entries again.
     *
     * The date comes through rather than folds into a flag, because a session
     * can rewrite the entry under the stamp and nothing catches that. What the
     * read can do is print the day of the judgement — `D-DOC-038`.
     */
    #[Decision('D-DOC-038')]
    #[Test]
    public function aJudgedEntryCarriesTheDayItWasDecidedOn(): void
    {
        $reading = Unresolved::requirements();
        $requirements = Requirements::all();

        $judged = array_column(array_filter($reading, static fn(array $r): bool => $r['judged'] !== ''), 'id');
        self::assertNotSame([], $judged, 'no unguarded requirement has been judged, which the reading would have to say instead');

        foreach ($reading as $entry) {
            self::assertSame(
                $requirements[$entry['id']]['judged'],
                $entry['judged'],
                $entry['id'] . ' is read out with a judgement its file does not carry',
            );
        }
    }

    /**
     * `open` is two states and the report separates them, so the flag that
     * separates them has to be the file's own. A read that settles the **Wrong
     * if** neither way leaves a **Since then** where it changed something and a
     * `readings:` date where it changed nothing. Half the open entries carry
     * one of the two. Counted as unread, the pile reads as untouched and the
     * oldest named is one somebody has already been back to.
     *
     * Both spellings counted while the corpus had two. It has one since
     * `D-DOC-039`, which is the entry this holds and where the numbers are.
     */
    #[Decision('D-DOC-039')]
    #[Test]
    public function anOpenDecisionSomebodyHasBeenBackToIsToldApart(): void
    {
        $open = Unresolved::decisions();
        $decisions = Decisions::all();

        $revisited = array_filter($open, static fn(array $d): bool => $d['revisited']);
        self::assertNotSame([], $revisited, 'no open decision has been read again, which the report would have to say instead');
        self::assertNotSame($open, array_values($revisited), 'every open decision has been back-checked, which the report would have to say instead');

        foreach ($open as $decision) {
            $path = Decisions::directory() . '/' . $decisions[$decision['id']]['group']
                . '/' . $decisions[$decision['id']]['file'];
            $read = preg_match('/^(## |\*\*)Since then\b/m', (string) file_get_contents($path)) === 1
                || $decisions[$decision['id']]['readings'] !== [];
            self::assertSame(
                $read,
                $decision['revisited'],
                $decision['id'] . ' is reported as ' . ($decision['revisited'] ? 'read' : 'unread') . ', and its file says otherwise',
            );
        }
    }

    /**
     * Which open decisions still wait for a reader: the ones no test declares.
     *
     * Whoever makes a test fail reads the entry it holds, because the failure
     * prints it. So `held` is what narrows a listing of 155 to the 35 a session
     * can still owe — `D-DOC-054`.
     */
    #[Decision('D-DOC-054')]
    #[Test]
    public function anOpenDecisionATestHoldsIsNotWaitingForAReader(): void
    {
        $decisions = Decisions::all();
        $open = Unresolved::decisions();

        $held = array_filter($open, static fn(array $d): bool => $d['held']);
        self::assertNotSame([], $held, 'no open decision is held by a test, which the report would have to say instead');
        self::assertNotSame($open, array_values($held), 'every open decision is held, which the report would have to say instead');

        foreach ($open as $decision) {
            self::assertSame(
                $decisions[$decision['id']]['tests'] !== [],
                $decision['held'],
                $decision['id'] . ' is reported as ' . ($decision['held'] ? 'held' : 'unheld') . ', and its front matter says otherwise',
            );
        }
    }

    /**
     * The oldest open decision is the one the repository has moved furthest
     * away from, so it is the candidate the report names. Decisions::all() is
     * newest first for the listings, and this is the one caller that wants the
     * other end — `D-DOC-003`.
     */
    #[Decision('D-DOC-003')]
    #[Test]
    public function theOpenDecisionsAreReadOldestFirst(): void
    {
        $open = Unresolved::decisions();

        self::assertNotSame([], $open, 'no decision is open, which the report would have to say instead');

        $dates = array_column($open, 'date');
        $sorted = $dates;
        sort($sorted);
        self::assertSame($sorted, $dates);

        // Read once: the call reads all of `decisions/`, and per open decision
        // that is the corpus read four hundred times.
        $all = Decisions::all();
        foreach ($open as $decision) {
            self::assertSame(
                DecisionStatus::Open->value,
                $all[$decision['id']]['status'],
                $decision['id'] . ' has been back-checked and is still reported as waiting',
            );
        }
    }
}
