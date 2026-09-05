<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Todo;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Giving a todo back: the worktree down, and the todo offered again.
 *
 * The other end of `todo:claim` for work that did not get anywhere — a session
 * that never started, one that was abandoned, a claim taken by mistake.
 * `todo:home` is the end for work that did: it rebases, checks and merges, and
 * none of that means anything for a branch carrying nothing.
 *
 * The todo needs no putting back. It never left the queue, so what offers it
 * again is the worktree coming down — `D-DOC-060`.
 *
 * What it decides is the branch, and it decides it by looking. One carrying
 * commits is left alone, because it is the only place that work exists; one
 * carrying none is deleted, because a branch nobody can take down is a todo
 * `todo:claim` passes over for good.
 *
 * A todo is named by its id. The worktree is how this repository holds one in
 * hand and not what a caller should have to know, so the directory name is
 * accepted too and neither is the one written down.
 */
#[AsCommand(
    name: 'todo:drop',
    description: 'take a worktree down without merging, so its todo is offered again',
)]
final class TodoDrop
{
    /**
     * @param array<int, string> $todo
     */
    public function __invoke(
        OutputInterface $output,
        #[Argument('the todos to give back, by id — or by the worktree standing on one')]
        array $todo = [],
    ): int {
        $root = Paths::root();
        if (Todo::linked()) {
            Voice::problem(
                $output,
                "This is a worktree, and one is taken down in the checkout it was cut from.\n"
                . 'Nothing here can remove the directory it is standing in.',
            );

            return 1;
        }

        $standing = Todo::worktrees($root);
        if ($todo === []) {
            return self::report($output, $standing);
        }

        $dropped = 0;
        foreach ($todo as $one) {
            $name = Todo::worktreeNamed($one, $root);
            if ($name === null) {
                Voice::problem($output, sprintf(
                    '%s is no todo anybody has in hand — `bin/cli todo:list` prints the ones that are.',
                    $one,
                ));

                return 1;
            }

            $dropped += self::drop($output, $root, $name, $standing[$name] ?? '') ? 1 : 0;
        }

        return $dropped === count($todo) ? 0 : 1;
    }

    /** One worktree down, and its branch kept or deleted by what it carries. */
    private static function drop(OutputInterface $output, string $root, string $name, string $branch): bool
    {
        Voice::heading($output, $name);

        // The same refusal `todo:home` makes, for the same reason: what nobody
        // committed is on no branch, and removing the worktree is what throws
        // it away.
        [, $dirty] = Checkouts::run(['git', '-C', $root . '/.worktrees/' . $name, 'status', '--porcelain']);
        if (trim($dirty) !== '') {
            Voice::problem(
                $output,
                "has changes nobody committed, and taking it down is what loses them:\n"
                . '    commit them on ' . ($branch === '' ? 'a branch' : $branch) . ' or throw them away, then ask again.',
            );

            return false;
        }

        [$carries, $said] = Checkouts::run(['git', '-C', $root, 'rev-list', '--count', 'main..' . $branch]);
        $commits = $branch === '' || $carries !== 0 ? -1 : (int) trim($said);

        [$removed, $said] = Checkouts::run(['git', '-C', $root, 'worktree', 'remove', '.worktrees/' . $name]);
        if ($removed !== 0) {
            Voice::problem($output, 'is still standing: ' . trim($said));

            return false;
        }
        Voice::row($output, 'worktree removed, and its todo is queued again');

        if ($commits !== 0) {
            Voice::row($output, $commits < 0
                ? ($branch === '' ? 'it stood on no branch' : $branch . ' carries what nothing here could count') . ', so nothing was deleted'
                : sprintf('%s carries %d commits nothing else does, so it stays', $branch, $commits));

            return true;
        }

        [$deleted, $said] = Checkouts::run(['git', '-C', $root, 'branch', '-d', $branch]);
        Voice::row($output, $deleted === 0
            ? $branch . ' carried nothing and is deleted'
            : $branch . ' carried nothing and is still there: ' . trim($said));

        return true;
    }

    /**
     * What is standing, for a caller who has not said which to take down.
     *
     * @param array<string, string> $standing
     */
    private static function report(OutputInterface $output, array $standing): int
    {
        if ($standing === []) {
            Voice::ok($output, 'No worktree is standing, so no todo is in hand.');

            return 0;
        }

        $held = Todo::held();
        foreach ($standing as $name => $branch) {
            $todo = $held[$branch] ?? null;
            $output->writeln(sprintf(
                '%s %s',
                Voice::key($todo === null ? '—' : Todo::identifier($todo), 16),
                $todo === null ? $name . ' stands on ' . ($branch === '' ? 'no branch' : $branch) . ' and holds no todo' : $todo['title'],
            ));
        }

        $output->writeln('');
        Voice::note($output, 'Name one to give it back: `bin/cli todo:drop <id>`.
'
            . 'One whose work is finished comes home instead: `bin/cli todo:home <id>`.');

        return 0;
    }
}
