<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Scenarios;
use TYPO3\DevCompanion\Upkeep\Voice;
use TYPO3\DevCompanion\Upkeep\Wrap;

/**
 * The targeted contract cases, which a session reads rather than runs forward.
 *
 * Named one case it hands that one over, the same way `scenarios:show` hands
 * over a forward review. A test holds what a case claims rather than a recorded
 * session, which is why no run records one.
 *
 * Named none it says which cases are still owed a read, and exits nonzero while
 * there are any. A case whose `Held by` says `not guarded` is the part no test
 * reaches. A session that stands in for it is the only evidence there is. That
 * is what the recurring todo is due on, `D-FBK-012`. So the question goes to
 * every case rather than to a list in a todo that ages.
 */
#[AsCommand(
    name: 'scenarios:contract',
    description: 'one targeted case to hand over, or which of them are still owed a reading',
)]
final class ScenarioContract extends ScenarioReport
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('the contract case to hand over, where one of them is wanted whole')]
        string $id = '',
    ): int {
        if ($id === '') {
            return self::unguarded($output);
        }

        $id = strtoupper($id);
        $case = Scenarios::contracts()[$id] ?? null;
        if ($case === null) {
            Voice::problem($output, isset(Scenarios::load()[$id])
                ? sprintf('%s is an open forward review: bin/cli scenarios:show %s', $id, $id)
                : sprintf('There is no contract case %s.', $id));

            return 2;
        }

        $this->report($output, $case, 'Contract');

        return self::owed($case) ? 1 : 0;
    }

    /**
     * Every case still owed a read, with what its own line says nothing holds.
     *
     * The sentence rather than the id alone, because that is what decides
     * whether the read is worth a session. Two of them name a crossing no run
     * can reach, and one names a step nothing makes a session take.
     */
    private static function unguarded(OutputInterface $output): int
    {
        $owed = array_filter(Scenarios::contracts(), static fn(array $case): bool => self::owed($case));
        foreach ($owed as $case) {
            $output->writeln(Wrap::text(sprintf('%s %s', Voice::key($case['id'], 9), str_replace('`', '', $case['heldBy'])), str_repeat(' ', 10)));
        }
        return Voice::verdict($output, count($owed), 'Every contract case is held by a test.', sprintf('%d of %d contract cases are owed a reading.', count($owed), count(Scenarios::contracts())));
    }

    /**
     * @param array{heldBy: string, ...} $case
     */
    private static function owed(array $case): bool
    {
        return str_contains($case['heldBy'], 'not guarded');
    }
}
