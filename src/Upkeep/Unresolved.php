<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * What stands on record and nothing has answered for yet.
 *
 * Both directories carry a state that means unfinished, and neither of them was
 * ever read for it. A requirement is `open` when nobody has built it and `not
 * guarded` when nothing holds it. A decision is `open` when nobody has been
 * back to its "Wrong if". None of the three is an error, which is exactly why
 * none of them surfaced. feedback/ and the forward reviews feed the queue, so
 * an entry could sit in either directory for ever without a word from anything.
 * One of them sat there from the day the directory came to be.
 *
 * This is the read, and it reports rather than fails. Whether an entry is worth
 * the work is a judgement, and the judgement stays with whoever runs `bin/cli
 * unresolved:list`. What it cannot stay is invisible.
 */
final class Unresolved
{
    /**
     * Every requirement nothing answers for, in id order.
     *
     * Two answers take an entry out of what nobody has decided about, and
     * `queued` is only the first. A queued todo that names the id is what turns
     * it into work. `judged` is the day a session read it and decided it stays
     * as it is. Without the second, a requirement no test can hold could never
     * leave this read, and every session derived the same judgement again.
     * `writing-a-requirement.rst` names that as the honest answer, and the
     * **Held by** of three entries has carried it since July.
     *
     * `queued` comes from what the queue says it serves rather than from
     * `todo/` as a whole. A search over the directory answers yes for an id
     * named in the page that lists what stays out of the queue on purpose. That
     * is a decision somebody took, and the opposite of the one this flag
     * reports.
     *
     * @return array<int, array{id: string, state: string, title: string, queued: bool, judged: string}>
     */
    public static function requirements(): array
    {
        $queued = Todo::serves();

        $waiting = [];
        foreach (Requirements::all() as $requirement) {
            if (Requirements::state($requirement)->isGuarded()) {
                continue;
            }

            $waiting[] = [
                'id' => $requirement['id'],
                'state' => Requirements::state($requirement)->value,
                'title' => $requirement['title'],
                'queued' => in_array($requirement['id'], $queued, true),
                'judged' => $requirement['judged'],
            ];
        }

        return $waiting;
    }

    /**
     * Requirements that stand on a decision somebody has since revoked.
     *
     * The quiet one. A later read disproved a revoked decision, and the
     * requirement on top of it keeps its `held` status and its green test the
     * whole time. The test holds the requirement, and nothing holds the reasons
     * under it. Neither directory can see the other, so this is the one
     * crossing somebody has to read out.
     *
     * @return array<int, array{id: string, title: string, decision: string, revokedBy: string}>
     */
    public static function requirementsOnRevokedDecisions(): array
    {
        $decisions = Decisions::all();

        $resting = [];
        foreach (Requirements::all() as $requirement) {
            foreach ($requirement['restsOn'] as $id) {
                if (DecisionStatus::tryFrom($decisions[$id]['status'] ?? '') !== DecisionStatus::Revoked) {
                    continue;
                }
                $resting[] = [
                    'id' => $requirement['id'],
                    'title' => $requirement['title'],
                    'decision' => $id,
                    'revokedBy' => $decisions[$id]['revokedBy'],
                ];
            }
        }

        usort(
            $resting,
            static fn(array $a, array $b): int => [$a['id'], $a['decision']] <=> [$b['id'], $b['decision']],
        );

        return $resting;
    }

    /**
     * Every open decision, oldest first, and whether somebody has been back.
     *
     * An open decision is not a defect the way an open requirement is. Most of
     * them are simply still true. The oldest is worth a name because the
     * repository around it has moved furthest since.
     *
     * `open` is two states, and a report of them as one made the pile look
     * untouched. So the read carries both and the caller names the oldest
     * nobody has opened. `held` narrows that further: a decision a test
     * declares gets its read when the failure prints the entry, `D-DOC-044`.
     * What remains is the entry nothing fires on, `D-DOC-054`.
     *
     * @return array<int, array{id: string, date: string, title: string, revisited: bool, held: bool}>
     */
    public static function decisions(): array
    {
        $open = [];
        foreach (Decisions::all() as $decision) {
            if (DecisionStatus::tryFrom($decision['status']) !== DecisionStatus::Open) {
                continue;
            }

            $open[] = [
                'id' => $decision['id'],
                'date' => $decision['date'],
                'title' => $decision['title'],
                'revisited' => $decision['revisited'],
                'held' => $decision['tests'] !== [],
            ];
        }

        // Decisions::all() is newest first, and a reverse would leave the ids
        // of one day in the order that listing wants them read.
        usort($open, static fn(array $a, array $b): int => [$a['date'], $a['id']] <=> [$b['date'], $b['id']]);

        return $open;
    }
}
