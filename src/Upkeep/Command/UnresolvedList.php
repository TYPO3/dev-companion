<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\Todo;
use TYPO3\DevCompanion\Upkeep\Unresolved;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Reads what requirements/ and decisions/ say is unfinished.
 *
 * The two checks hold those files to their shape, and a file can be perfectly
 * shaped while saying that nobody has built the requirement or been back to
 * the decision. That is a legitimate state, so no check may fail on it — which
 * is why nothing read it at all, and why an entry sat in requirements/ unbuilt
 * from the day the directory was created. This command is that reading, and it
 * is its own command rather than a line in `bin/cli repository:check` because a session
 * that wants to know what is waiting should not have to run three checks to
 * find out.
 */
#[AsCommand(
    name: 'unresolved:list',
    description: 'the requirements nothing answers for, and the decisions nobody has been back to',
)]
final class UnresolvedList
{
    /**
     * What is waiting, and whether the pipeline knows about it.
     *
     * A requirement is named with the state it is in and with whichever of the
     * two answers it has had: a todo in the queue that takes it on, or the day
     * a session decided it stays as it is. The decisions are counted
     * rather than listed — most of them are standing because they are still
     * true, and a report that prints all of them every time is one nobody
     * reads twice. The count is split, because half the open ones have been
     * read and could be neither confirmed nor revoked, and reported as one
     * number they read as a pile nobody has touched. The oldest named is the
     * oldest nobody has opened.
     *
     * Nonzero says there is something to do, which is how `bin/cli todo:next` knows
     * the todo that starts here is still the next thing. Not a failure: a
     * repository nobody owes anything to is the good outcome and exits 0.
     */
    public function __invoke(OutputInterface $output): int
    {
        $requirements = Unresolved::requirements();
        foreach ($requirements as $requirement) {
            $output->writeln(sprintf(
                '%s %-12s %s%s',
                Voice::key($requirement['id'], 10),
                $requirement['state'],
                $requirement['title'],
                self::answer($requirement),
            ));
        }
        if ($requirements === []) {
            Voice::ok($output, 'Every requirement is met and guarded.');
        }

        // What is owed here is the judgement, not the work: a requirement some
        // todo names has had it, one carrying a `judged:` date has had it the
        // other way, and the open decisions have had it as soon as a todo takes
        // on sorting them. Everything else would make a list that is
        // legitimately long the only thing a session is ever offered.
        $unjudged = array_filter(
            $requirements,
            static fn(array $r): bool => !$r['queued'] && $r['judged'] === '',
        );
        $sorting = in_array('decisions/', Todo::serves(), true);

        // Before the count of what nobody has been back to, because this one is
        // not one of them: the reasoning under a held requirement is gone and
        // its test is still green, so nothing else in this report would say so.
        foreach (Unresolved::requirementsOnRevokedDecisions() as $resting) {
            Voice::problem($output, sprintf(
                '%s rests on %s, which is revoked%s.',
                $resting['id'],
                $resting['decision'],
                $resting['revokedBy'] === '' ? '' : ' — see ' . $resting['revokedBy'],
            ));
        }

        $standing = Unresolved::decisions();
        $total = count(Decisions::all());
        $unread = array_values(array_filter($standing, static fn(array $d): bool => !$d['revisited']));
        // The ones a reader is still owed. A decision a test declares is read
        // when somebody changes the behaviour, because the failure prints the
        // entry — so what waits for a session is the entry nothing fires on
        // (`D-DOC-054`).
        $waiting = array_values(array_filter($unread, static fn(array $d): bool => !$d['held']));
        if ($unread === []) {
            Voice::ok($output, sprintf('All %d decisions have been back-checked.', $total));

            return $unjudged === [] ? 0 : 1;
        }

        Voice::note($output, sprintf(
            '%d of %d decisions are open, %d of those nobody has been back to, and %d of those are held by no test.
'
            . '%s bin/cli decisions:list has them all.',
            count($standing),
            $total,
            count($unread),
            count($waiting),
            $waiting === []
                ? 'Every one of them is read when its behaviour moves.'
                : sprintf('The oldest waiting on a reader is %s (%s).', $waiting[0]['id'], $waiting[0]['date']),
        ));

        return $unjudged === [] && $sorting ? 0 : 1;
    }

    /**
     * What has been decided about one entry, as the line says it.
     *
     * A todo is the answer that turns it into work and a date is the answer
     * that leaves it alone, so the date is printed: a judgement made before the
     * entry was last rewritten is one somebody can disagree with, and a bare
     * word would not say which.
     *
     * @param array{queued: bool, judged: string} $requirement
     */
    private static function answer(array $requirement): string
    {
        if ($requirement['queued']) {
            return '';
        }

        return $requirement['judged'] === ''
            ? ' — no todo names it'
            : ' — judged on ' . $requirement['judged'];
    }
}
