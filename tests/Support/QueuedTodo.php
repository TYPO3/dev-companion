<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Support;

use PHPUnit\Framework\Attributes\After;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Todo;

/**
 * A queue of the test's own, for the cases that need a todo in it to hold.
 *
 * The queue empties, which is not the repository run dry but the state the
 * sightings start from. Three cases opened with `assertNotSame([],
 * Todo::items())` instead, which made the ordinary commit that finishes the
 * last todo the one commit that could not be green. A precondition that is a
 * state rather than a property of this checkout comes from here rather than
 * from an assertion. The suite may not skip itself past one either
 * (`D-FBK-013`). It sits below the system temporary directory, and `Todo`
 * points at it for the length of the case. A unit test writes into no directory
 * this repository keeps (`R-COD-003`).
 *
 * @phpstan-import-type Section from Todo
 */
trait QueuedTodo
{
    /**
     * The name of a fixture, so a case can pick its own out of what it wrote.
     */
    private const MARKER = 'phpunit-todo-fixture';

    /** The queue this case writes into, made on the first write. */
    private ?string $ownQueue = null;

    /**
     * How many this case has queued, which is what keeps two of them apart.
     *
     * An id derives from the todo's subject (`D-DOC-061`), and two fixtures of
     * one case have the same subject on the same day. The count stands in for
     * what the repository has, the instant a card or a hand written todo comes
     * into being. It is per case, so a name is the same on every run.
     */
    private int $queued = 0;

    /**
     * What this case wrote, by file name, in the order it wrote them.
     *
     * A todo's name is its id and nothing else — `D-DOC-061`. So the queue a
     * fixture sits in tells it apart rather than anything readable in its path.
     *
     * @var array<int, string>
     */
    private array $written = [];

    #[After]
    public function removeQueuedTodos(): void
    {
        // Both seams go back before anything else. So a case that fails halfway
        // still leaves the next one on this checkout's own queue and this
        // machine's own git.
        Todo::useDirectory(null);
        Checkouts::useRunner(null);

        if ($this->ownQueue === null) {
            return;
        }

        $queue = $this->ownQueue;
        $this->ownQueue = null;
        $this->queued = 0;
        $this->written = [];
        if (!is_dir($queue)) {
            return;
        }

        foreach (Finder::create()->files()->in($queue) as $file) {
            unlink($file->getPathname());
        }
        // Deepest first, so a directory is empty by the time the walk removes
        // it.
        $directories = [];
        foreach (Finder::create()->directories()->in($queue) as $directory) {
            $directories[] = $directory->getPathname();
        }
        usort($directories, static fn(string $a, string $b): int => substr_count($b, '/') <=> substr_count($a, '/'));
        foreach ($directories as $directory) {
            @rmdir($directory);
        }
        @rmdir($queue);
    }

    /**
     * The queue this case writes into, made once and pointed at.
     *
     * Empty rather than a copy of the real one. What these cases hold is what a
     * claim, a release and the order `todo:next` reads do to a queue. Every one
     * of them says which todos are in it first. A copy would make the case
     * depend on what this checkout happens to carry, which is the dependency
     * the fixtures exist to remove.
     */
    private function ownQueue(): string
    {
        if ($this->ownQueue !== null) {
            return $this->ownQueue;
        }

        // A root of its own with a `todo` in it. A todo's path is relative to
        // that root, and a move resolves the two against each other.
        $root = sys_get_temp_dir() . '/' . self::MARKER . '-' . getmypid() . '-' . bin2hex(random_bytes(6));
        foreach (['open', 'waiting', 'recurring', 'reference'] as $stage) {
            mkdir($root . '/todo/' . $stage, 0o777, true);
        }

        Todo::useDirectory($root . '/todo');

        return $this->ownQueue = $root;
    }

    /**
     * One queued todo, behind whatever this case has already queued.
     *
     * It is `low` and carries today's id, which is what puts it last. Below
     * everything judged higher and behind the other `low` ones because they are
     * older. A case about the order says which priority and which day it needs
     * instead.
     *
     * What it serves and what its step says are the same two parameters. A case
     * about one todo needs neither. A case about the relation between two of
     * them is about nothing else. That is the card a feedback arrived with and
     * the todo a judgement replaced it with.
     *
     * @return Section
     */
    private function queueATodo(
        string $priority = 'low',
        ?string $day = null,
        string $serves = 'todo/',
        string $step = 'The step this fixture stands for.',
        string $seed = self::MARKER,
    ): array {
        $name = Todo::id($seed . ++$this->queued, $day) . '.md';
        $this->written[] = $name;
        file_put_contents(
            $this->ownQueue() . '/todo/open/' . $name,
            "---\nserves: [" . $serves . "]\npriority: " . $priority
            . "\n---\n\n# " . self::MARKER . "\n\n" . $step . "\n",
        );

        return $this->ownTodos(Todo::items(), $name)[0];
    }

    /**
     * One queued todo a worktree stands on, as the git this case takes answers
     * for it.
     *
     * A claim is a worktree and nothing else — `D-DOC-060`. So what a case
     * about one arranges is the answer `git worktree list` gives, not a file.
     */
    private function worktreeOn(string $branch, string $directory = self::MARKER): string
    {
        return 'worktree ' . $this->ownQueue() . '/.worktrees/' . $directory
            . "\nHEAD 0000000000000000000000000000000000000000\nbranch refs/heads/" . $branch . "\n";
    }

    /**
     * One recurring todo of this case's own, at the cadence it names.
     *
     * `session` is the one the sightings come from, and a case about what comes
     * before the queue needs one to exist. It used to read whichever the
     * repository carried, which made the case pass or fail on a directory it
     * was not about.
     *
     * A cadence in days needs the other two lines with it, because an
     * appointment is due on both. The date of its last run, and what its own
     * command answers on the next one.
     */
    private function recurATodo(
        string $every = 'session',
        string $title = self::MARKER . '-recurring',
        string $run = '',
        string $checked = '',
    ): void {
        file_put_contents(
            $this->ownQueue() . '/todo/recurring/' . $title . '.md',
            "---\nserves: [todo/]\nevery: " . $every
            . ($checked === '' ? '' : "\nchecked: " . $checked)
            . ($run === '' ? '' : "\nrun: [" . $run . ']')
            . "\n---\n\n# " . $title . "\n\nThe reading this fixture stands for.\n",
        );
    }

    /**
     * A git that answers the same thing whatever the question.
     *
     * What these cases are about is what this repository does with the answer,
     * and every one of them asks git once. A real worktree would be a directory
     * and a branch made on the machine the suite runs on, which is what
     * `R-COD-003` is about.
     */
    private function gitSaying(string $output): CommandRunner
    {
        $git = self::createStub(CommandRunner::class);
        $git->method('run')->willReturn(['ok' => true, 'exitCode' => 0, 'output' => $output, 'error' => '']);

        return $git;
    }

    /**
     * A git that answers each command by its name, and records the lot.
     *
     * A case about a sequence of git calls needs different answers to different
     * questions, which the one-answer stub above cannot give. The key is the
     * first argument after the `-C <path>` pair, so a case says `worktree` or
     * `rev-list` rather than the whole line.
     *
     * @param array<string, array{0: int, 1: string}> $answers by the git subcommand
     * @param array<int, string>                      $asked   filled in with every command run
     */
    private function gitAnswering(array $answers, array &$asked): CommandRunner
    {
        $git = self::createStub(CommandRunner::class);
        $git->method('run')->willReturnCallback(
            static function (array $command) use ($answers, &$asked): array {
                $asked[] = implode(' ', $command);
                $subcommand = $command[3] ?? '';
                [$exitCode, $output] = $answers[$subcommand] ?? [0, ''];

                return ['ok' => $exitCode === 0, 'exitCode' => $exitCode, 'output' => $output, 'error' => ''];
            },
        );

        return $git;
    }

    /** One todo blocked on an answer, which is what `bin/cli todo:waiting` reports. */
    private function waitATodo(string $waitingOn = 'the answer this fixture stands for'): void
    {
        $name = Todo::id(self::MARKER . ++$this->queued) . '.md';
        $this->written[] = $name;
        file_put_contents(
            $this->ownQueue() . '/todo/waiting/' . $name,
            "---\nserves: [todo/]\nwaitingOn: >\n  " . $waitingOn
            . "\n---\n\n# " . self::MARKER . "\n\nThe step this question blocks.\n",
        );
    }

    /**
     * This case's own todos among what it queued, in the order the case handed
     * them over.
     *
     * @param array<int, Section> $todos
     * @param string|null         $named one file of them, where the case wrote more than one
     *
     * @return array<int, Section>
     */
    private function ownTodos(array $todos, ?string $named = null): array
    {
        $wanted = $named === null ? $this->written : [$named];

        return array_values(array_filter(
            $todos,
            static fn(array $todo): bool => in_array(basename($todo['path']), $wanted, true),
        ));
    }
}
