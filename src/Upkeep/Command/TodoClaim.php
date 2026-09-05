<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Todo;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Taking the front of the queue on, and putting the sessions in front of it.
 *
 * `bin/cli todo:next` hands the same first todo to everybody who asks, because
 * the queue is an order rather than an assignment. That is what has to change
 * before two sessions can work at once, and the worktree is the change: a todo
 * whose branch one stands on is one somebody has in hand, so the second session
 * is offered the item behind it rather than the one somebody is already writing.
 *
 * Nothing is moved and nothing is committed — `D-DOC-060`.
 *
 * It carries the setup out rather than printing it, and the reason is the order
 * rather than the typing: a worktree apiece with its own `composer install`, and
 * the message the sessions are started with.
 *
 * The branch is derived from the todo and the worktree named after the branch,
 * so a todo somebody has in hand and one whose branch nobody took down are both
 * visible before anything is cut. Both are passed over rather than given a
 * second name, because a worktree that quietly attaches to an old branch is the
 * one failure here that looks like success.
 *
 * The overlap it reports is the only one it can see. Two todos that serve the
 * same entry are two sessions likely to edit one file, and nothing here knows
 * which files a step will touch — so it is a warning to read before the
 * sessions start, not a refusal.
 *
 * @phpstan-import-type Section from Todo
 */
#[AsCommand(
    name: 'todo:claim',
    description: 'take the next todos out of the queue, one branch each, for sessions working at once',
)]
final class TodoClaim
{
    /**
     * Where this machine says how a session is started on it.
     *
     * Ignored by git, because it is a property of the machine rather than of
     * the repository — the same reason `.checkouts/` is. What it holds is one
     * command line, run once per worktree: that worktree is its working
     * directory, the message arrives on standard input, and `TODO_SESSION_ID`
     * is in its environment.
     */
    public const LAUNCH = '.session-command';

    public function __invoke(
        OutputInterface $output,
        #[Argument('how many sessions are going to work at once')]
        int $count = 1,
    ): int {
        // Worktrees are cut where `main` is. Asked in a worktree, every one it
        // made would be cut from somebody's half-finished branch.
        if (Todo::linked()) {
            Voice::problem(
                $output,
                "This is a worktree, and claims are taken in the checkout it was cut from.\n"
                . 'Everything cut here would stand on this branch, which is somebody else\'s work.',
            );

            return 1;
        }

        $items = Todo::items();
        if ($items === []) {
            Voice::problem($output, 'The queue is empty, so there is nothing to take on.');

            return 1;
        }
        if ($count < 1) {
            Voice::problem($output, 'A claim is for at least one session.');

            return 1;
        }

        $root = Paths::root();
        $free = self::untaken($output, $root, $items);
        if ($free === []) {
            Voice::problem($output, 'Every queued todo is in hand or has a branch standing, so there is none to take.');

            return 1;
        }
        if ($count > count($free)) {
            Voice::note($output, sprintf(
                'There are %d nobody has, which is what %d sessions get.',
                count($free),
                count($free),
            ));
        }

        $taken = array_slice($free, 0, $count);
        $claims = [];
        foreach ($taken as $todo) {
            $branch = Todo::branch($todo);
            $claims[] = [
                'title' => $todo['title'],
                'branch' => $branch,
                'directory' => '.worktrees/' . basename($branch),
            ];
            Voice::heading($output, $todo['title']);
            Voice::row($output, Voice::dim(sprintf('%s · %s', $todo['path'], $branch)));
        }
        $output->writeln('');

        foreach (self::overlapping($taken) as $saying => $shared) {
            foreach ($shared as $what => $titles) {
                Voice::problem($output, sprintf('%s ' . $saying . '.', $what, implode(' and ', $titles)));
                $output->writeln('');
            }
        }

        $standing = self::worktrees($output, $root, $claims);
        $byHand = self::start($output, $root, $standing);
        if ($byHand !== []) {
            $output->writeln('');
            $output->writeln(self::handover($root, $byHand));
        }

        return count($standing) === count($claims) ? 0 : 1;
    }

    /**
     * The queued todos nobody has, which is what there is to take on.
     *
     * Two ways a todo is not free, and they are named apart because they are
     * different things to do about it. One with a worktree is being worked, and
     * that is the arrangement doing its job. One whose branch is standing with no
     * worktree on it is work somebody left: the branch still holds the half that
     * is done, so it is neither reused nor deleted here.
     *
     * The branches are read in one call rather than one per todo, which is the
     * same reading `Todo::inHand()` makes of the worktrees.
     *
     * @param array<int, Section> $items
     *
     * @return array<int, Section>
     */
    private static function untaken(OutputInterface $output, string $root, array $items): array
    {
        $held = array_flip(Todo::inHand($root));
        [$read, $said] = Checkouts::run(['git', '-C', $root, 'for-each-ref', '--format=%(refname:short)', 'refs/heads/todo/']);
        $branches = $read === 0 ? array_flip(array_filter(array_map(trim(...), preg_split('/\R/', trim($said)) ?: []))) : [];

        $free = [];
        foreach ($items as $todo) {
            $branch = Todo::branch($todo);
            if (isset($held[$branch])) {
                continue;
            }
            if (isset($branches[$branch])) {
                Voice::note($output, sprintf('%s is left on %s, which nothing here reuses or deletes.', $todo['title'], $branch));
                continue;
            }

            $free[] = $todo;
        }
        if ($free !== $items) {
            $output->writeln('');
        }

        return $free;
    }

    /**
     * One worktree per claim, each with the install that makes `bin/cli` in it
     * this checkout rather than the one next door.
     *
     * `vendor/` cannot be shared and is not worth trying to: `Paths::root()` is
     * the directory above `src/`, Composer resolves `src/` from where the
     * autoloader physically sits, and a symlinked one therefore points every
     * path in this repository back here. `.checkouts/` is the opposite case and
     * is linked on purpose — 861 MB a session only ever reads.
     *
     * A worktree that could not be finished is named and the rest go on. What
     * it must not do is stay half-made in silence, because the session started
     * against it fails at its first command with nothing saying why.
     *
     * @param array<int, array{title: string, branch: string, directory: string, ...}> $claims
     *
     * @return array<int, string> the directory of each one ready to be worked
     */
    private static function worktrees(OutputInterface $output, string $root, array $claims): array
    {
        $standing = [];
        foreach ($claims as $claim) {
            $path = $root . '/' . $claim['directory'];
            [$cut, $said] = Checkouts::run(['git', '-C', $root, 'worktree', 'add', '--quiet', $claim['directory'], '-b', $claim['branch']]);
            if ($cut !== 0) {
                Voice::problem($output, sprintf('%s could not be made: %s', $claim['directory'], trim($said)));
                continue;
            }

            [$installed, $said] = Checkouts::run(['composer', 'install', '--no-interaction', '--quiet'], $path);
            if ($installed !== 0) {
                Voice::problem($output, sprintf(
                    "%s has no `vendor/`, so `bin/cli` in it is nothing to start a session against:\n%s",
                    $claim['directory'],
                    trim($said),
                ));
                continue;
            }

            if (is_dir($root . '/.checkouts') && !file_exists($path . '/.checkouts')) {
                symlink('../../.checkouts', $path . '/.checkouts');
            }

            Voice::row($output, $claim['directory']);
            $standing[] = $claim['directory'];
        }

        return $standing;
    }

    /**
     * The sessions themselves, where this machine has said how one is started.
     *
     * The fourth step, and it is here for the reason the other three are: a
     * claim, the commit carrying it and a worktree apiece already happen in one
     * move, and the launch is what was left over for somebody to carry out by
     * reading. It broke the way the left-over step always breaks — the run of
     * 2026-08-02 started every session in the directory that was already open,
     * and the claims sat untouched while three worktrees stood there.
     *
     * What the client is called is not this repository's business, and
     * `documentation/contributing/driving-a-session.rst` says so: clients differ in what the
     * flags are named, not in what has to be true. So the command line is the
     * machine's, in an ignored file, and the three things a person gets wrong
     * are this command's — the working directory, the message, and a session id
     * per session. Where the file is absent nothing is started and the handover
     * prints as it always did, so a checkout that never configures one loses
     * nothing.
     *
     * Started detached rather than waited on. `Checkouts::run` captures and
     * waits, which is right for the git it was named for and wrong for three
     * agent sessions: waiting serialises them and holds the terminal for as
     * long as the work takes. Each gets a log instead, named here, because a
     * launch that fails does it in the client rather than in the shell.
     *
     * @param array<int, string> $standing
     *
     * @return array<int, string> the worktrees nothing was started for
     */
    private static function start(OutputInterface $output, string $root, array $standing): array
    {
        $file = $root . '/' . self::LAUNCH;
        $command = is_file($file) ? trim((string) file_get_contents($file)) : '';
        if ($standing === [] || $command === '') {
            return $standing;
        }

        $logs = $root . '/.worktrees/.sessions';
        if (!is_dir($logs) && !mkdir($logs, 0o777, true)) {
            Voice::problem($output, $logs . ' could not be made, so no session has anywhere to report.');

            return $standing;
        }

        // One file for every session there has ever been, because nothing in the
        // briefing is per-session: which todo is whose is read out of the
        // worktree. Written per worktree it left a copy of one constant behind
        // for each, and 484 of them had accumulated by 2026-08-27.
        $message = $logs . '/briefing.message';
        if (file_put_contents($message, Todo::BRIEFING . "\n") === false) {
            Voice::problem($output, $message . ' could not be written, so no session has anything to be sent.');

            return $standing;
        }

        $output->writeln('');
        $byHand = [];
        foreach ($standing as $directory) {
            $name = basename($directory);
            $log = $logs . '/' . $name . '.log';
            $started = proc_open(
                ['sh', '-c', sprintf('%s < %s > %s 2>&1 &', $command, escapeshellarg($message), escapeshellarg($log))],
                [],
                $pipes,
                $root . '/' . $directory,
                array_merge(getenv(), ['TODO_SESSION_ID' => self::identifier()]),
            );
            if (!is_resource($started) || proc_close($started) !== 0) {
                Voice::problem($output, $name . ' was not started, and this is the command that did not run: ' . $command);
                $byHand[] = $directory;
                continue;
            }

            Voice::ok($output, sprintf('%s is working, and reports into %s', $name, $log));
        }

        if ($byHand === []) {
            $output->writeln('');
            Voice::note($output, sprintf(
                'Started from %s, one session per worktree, each in its own directory and each
'
                . "sent the same message. One that reports comes home with `bin/cli todo:home\n"
                . '<id>`, whenever it reports and without waiting for the rest: %s.',
                self::LAUNCH,
                Todo::PARALLEL,
            ));
        }

        return $byHand;
    }

    /**
     * A session id per session, so a transcript can be found afterwards.
     *
     * The flag it is passed by belongs to the client, so it is handed over as
     * `TODO_SESSION_ID` in the environment and the launch command names it. One
     * per session and never one per claim: three sessions sharing an id are
     * three transcripts nobody can tell apart.
     */
    private static function identifier(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr(ord($bytes[6]) & 0x0F | 0x40);
        $bytes[8] = chr(ord($bytes[8]) & 0x3F | 0x80);

        return vsprintf('%s-%s-%s-%s-%s', array_map('bin2hex', [
            substr($bytes, 0, 4),
            substr($bytes, 4, 2),
            substr($bytes, 6, 2),
            substr($bytes, 8, 2),
            substr($bytes, 10, 6),
        ]));
    }

    /**
     * What is left for the caller: where each session is started, and what
     * every one of them is sent.
     *
     * Both halves are printed filled in, and the second is why. The message was
     * a template once, `<absolute path to the worktree>` and all, and the run
     * that broke sent it as it stood — so the blanks came out of it and the
     * session reads its todo off the worktree instead. The half above it kept
     * one: *with that worktree as its working directory* is a property somebody
     * satisfies, which is a blank wearing prose, and the run of 2026-08-02
     * satisfied it with the directory that was already open — the checkout the
     * worktrees are cut from. So the directories are lines to run, one per
     * session, and the sentence over the message says the rest of this output
     * is not part of it. A caller with nothing to compose composes nothing
     * wrong.
     *
     * `todo:next --worktree` catches what still gets through, which is what it
     * caught that day. It costs a session to find out, so it is the net rather
     * than the answer.
     *
     * @param array<int, string> $standing the worktree directories below the checkout
     */
    public static function handover(string $root, array $standing): string
    {
        $lines = ['One session per worktree, and each is started in the directory that is its own:', ''];
        foreach ($standing as $directory) {
            $lines[] = '    cd ' . $root . '/' . $directory;
        }

        $lines[] = '';
        $lines[] = "Send each of them the message below and nothing else off this output. It is the\n"
            . "same for every session, and there is nothing in it to fill in: which todo is\n"
            . 'whose is read out of the worktree it stands in.';
        $lines[] = '';
        $lines[] = self::indent(Todo::BRIEFING);
        $lines[] = '';
        $lines[] = sprintf(
            "Put the command line that starts a session on this machine into %s and this step\n"
            . 'stops being yours: the next claim runs it per worktree.',
            self::LAUNCH,
        );
        $lines[] = '';
        $lines[] = sprintf(
            "What a session started from a command line has to be given: %s.\n"
            . "One that reports comes home with `bin/cli todo:home <id>`, and what\n"
            . 'that carries out is %s.',
            Todo::LAUNCH,
            Todo::PARALLEL,
        );

        return implode("\n", $lines);
    }

    private static function indent(string $block): string
    {
        return (string) preg_replace('/^(?!$)/m', '    ', rtrim($block));
    }

    /**
     * What two of the claimed todos both stand on, in the three ways they can.
     *
     * `serves:` alone missed the collision that cost the most: two todos with
     * different `serves:` keys each added a handler for one token to one
     * function, and both had named `R-ANS-012` and the class they were about to
     * edit — a session saying where it is about to work, in the file the claim
     * reads anyway. So three readings, reported apart because they are worth
     * different things, and none of them a refusal: nothing here can know which
     * lines a step will touch.
     *
     * @param array<int, array{title: string, serves: array<int, string>, body: string, ...}> $taken
     *
     * @return array<string, array<string, array<int, string>>>
     */
    private static function overlapping(array $taken): array
    {
        $served = [];
        foreach ($taken as $todo) {
            foreach ($todo['serves'] as $what) {
                // A directory is what most of the queue serves, so counting one
                // would put a line under every claim ever taken — and a warning
                // that is always there is one nobody reads by the third time.
                // It would also be saying nothing: `decisions/` names the place
                // a step reports to rather than the file it edits.
                if (!str_ends_with($what, '/')) {
                    $served[$what][] = $todo['title'];
                }
            }
        }
        $served = array_filter($served, static fn(array $titles): bool => count($titles) > 1);

        $classes = self::classFiles();
        $named = [];
        $standing = [];
        foreach ($taken as $todo) {
            foreach (self::classesNamedIn($todo['body'], $classes) as $class) {
                $named[$class][] = $todo['title'];
            }
            foreach (self::entriesNamedIn($todo['body']) as $entry) {
                // What it serves is already said above, and saying it twice
                // reads as two findings.
                if (!isset($served[$entry])) {
                    $standing[$entry][] = $todo['title'];
                }
            }
        }

        $twice = static fn(array $by): array => array_filter(
            array_map('array_unique', $by),
            static fn(array $titles): bool => count($titles) > 1,
        );

        return [
            'is answered for twice, by %s — one entry two sessions will edit' => $served,
            'is named by %s — one class two sessions are about to open' => $twice($named),
            'is behind %s — one judgement, two steps that have to agree' => $twice($standing),
        ];
    }

    /**
     * The classes of this package, by the name a todo would call one.
     *
     * @return array<string, string>
     */
    private static function classFiles(): array
    {
        $directory = Paths::root() . '/src';
        if (!is_dir($directory)) {
            return [];
        }

        $classes = [];
        foreach (Finder::create()->files()->in($directory)->name('*.php') as $file) {
            $classes[$file->getBasename('.php')] = substr($file->getPathname(), strlen(Paths::root()) + 1);
        }

        return $classes;
    }

    /**
     * Which of them a todo names, however it spelled it.
     *
     * A step names the class it is going to change as the path, as the class
     * with the method on it, or as the bare name in backticks, and all three
     * are the same statement. What is compared is the file each resolves to.
     *
     * @param array<string, string> $classes
     *
     * @return array<int, string>
     */
    private static function classesNamedIn(string $body, array $classes): array
    {
        $found = [];
        if (preg_match_all('/\b[A-Z][A-Za-z0-9]*\b/', $body, $words) === false) {
            return [];
        }
        foreach ($words[0] as $word) {
            if (isset($classes[$word])) {
                $found[$classes[$word]] = true;
            }
        }

        if (preg_match_all('#\bsrc/[A-Za-z0-9/]+\.php\b#', $body, $paths) !== false) {
            foreach ($paths[0] as $path) {
                $found[$path] = true;
            }
        }

        return array_keys($found);
    }

    /**
     * The requirements and decisions a todo's own paragraph stands on.
     *
     * @return array<int, string>
     */
    private static function entriesNamedIn(string $body): array
    {
        if (preg_match_all('/\b[DR]-[A-Z]{3}-\d{3}\b/', $body, $matches) === false) {
            return [];
        }

        return array_values(array_unique($matches[0]));
    }
}
