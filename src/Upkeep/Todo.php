<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Feedback\Card;
use TYPO3\DevCompanion\Paths;

/**
 * Reads todo/, where one todo is one file and the order is in the names.
 *
 * It was one document, the way requirements/ and decisions/ each were before
 * they became directories, and it failed in the same way twice over. To finish
 * a todo meant a load of 30 kB to delete a paragraph. Every session that added,
 * moved or dropped work wrote the same file.
 *
 * Where a file sits is what it is and the front matter it opens with says the
 * rest. `todo/readme.md` is that form, the stages, the six keys, the one
 * paragraph under the title, and this reads it rather than restates it.
 *
 * @phpstan-type Section array{title: string, kind: string, priority: string, path: non-empty-string, every: string, checked: string, waitingOn: string, serves: array<int, string>, run: array<int, string>, strays: array<int, string>, body: string}
 */
final class Todo
{
    /** What a cadence can say, for the check that has to name it. */
    public const CADENCE = 'session, or a number of days';

    /**
     * What a todo's front matter carries, and the whole of it.
     *
     * The head is data, so it stands where a requirement and a decision already
     * write theirs, and the same `Entry` reads it, `D-DOC-062`. A key that is
     * none of these goes into the report rather than the void: a misspelled
     * `waiting_on` is a question nothing asks again.
     */
    public const FIELDS = ['serves', 'priority', 'every', 'checked', 'run', 'waitingOn'];

    /**
     * What a priority can say, highest first, and the whole of it.
     *
     * A closed list rather than a number, which is the difference between this
     * and the queue positions it replaced. Two sessions that queued work at
     * once both read the same last number and both took it. Two todos that are
     * `normal` are simply both normal. Nothing has to change its name to put
     * one between two others, because there is no between.
     *
     * Every todo in a stage carries one, and that is what makes it checkable. A
     * priority somebody forgot and one left off on purpose are the same file,
     * and while absence meant something no check could tell them apart. What a
     * card is for is readable without it, a judging card is the one that serves
     * a `feedback/` file. So `low` says what absence used to, and `bin/cli
     * todo:check` says when nothing does.
     */
    public const PRIORITIES = ['high', 'normal', 'low'];

    /**
     * The name of a todo in a stage: its id, and nothing beside it.
     *
     * The day is what a listing sorts by. Within a priority the older one comes
     * first, and that is the whole of the order below the three words. The
     * digest beside it is what two writers in one second cannot both produce,
     * which the day and the time on their own could.
     *
     * A slug behind the id would be a second name for the same todo, read by
     * nothing and shareable by two of them — `D-DOC-061`. What the work is is
     * the title inside the file, which every listing prints.
     */
    public const NAME = '/^T-\d{6}-[0-9a-f]{4}$/';

    /**
     * The id that cites a todo, which is also what its file name opens with.
     *
     * Derived from what the todo takes its name from rather than counted off
     * what exists, `D-DOC-061`. Two branches cut from one `main` each allocated
     * `D-ANS-114` by a count. Sessions claim todos in batches, so the same
     * count would collide more often. The seed is the feedback a card serves,
     * or the title and the instant where somebody wrote one by hand. The same
     * todo derives the same id everywhere.
     */
    public static function id(string $seed, ?string $day = null): string
    {
        return 'T-' . ($day ?? date('ymd')) . '-' . substr(sha1($seed), 0, 4);
    }

    /**
     * How a session works a todo, handed over with every todo.
     *
     * A todo prints as an instruction, and the shortest way to act on one is to
     * start an edit. What that skips is the half nothing here can see. That the
     * session read the step against what the repository does today. That it
     * settled a question it turns on from a source rather than from memory.
     * Both are invisible afterwards, the diff is identical. So the pointer
     * travels with the todo instead of waits on the page for whoever thinks to
     * look.
     */
    public const PROCEDURE = 'documentation/records/working-a-todo.rst';

    /**
     * How several sessions work todos at once, handed over with every claim.
     *
     * `bin/cli todo:claim` moves files and names branches, which is the half of
     * the arrangement this repository owns. The other half is not something a
     * command can carry out. The worktree that holds the branch, what a session
     * does with a question it cannot settle, and who merges. A claim taken
     * without it is a lock nobody knows how to release.
     */
    public const PARALLEL = 'documentation/records/working-todos-in-parallel.rst';

    /**
     * What a session started from a command line has to receive, handed over
     * with the message that starts one. It is the same launch a forward run
     * uses, which is why it is not on the page about claims.
     */
    public const LAUNCH = 'documentation/contributing/driving-a-session.rst';

    /**
     * What one of several sessions starts with, and the whole of it.
     *
     * Nothing in it is per-session, so there is no blank for anybody to fill in
     * and no template to send as it stands. Which todo is in hand comes out of
     * the checkout instead, by `bin/cli todo:next --worktree`. How to read is a
     * copy from `AGENTS.md` on purpose. Of the 82 sessions of 2026-08-02 every
     * one opened the procedure page and 13 opened `AGENTS.md` (`D-FBK-020`).
     */
    public const BRIEFING = <<<'TEXT'
        You work in the git worktree you were started in, and only there. Check with
        `git rev-parse --show-toplevel` that you are standing in one: the main checkout
        is worked by somebody else at the same time, and nothing in it is yours to
        change. Use paths below your own directory, or change into it first.

        Your work is not in this message. Fetch it:

            bin/cli todo:next --worktree

        That names the one todo that is yours, the branch you commit it on, and what
        that branch may not carry. Asked anywhere else it refuses, and a refusal ends
        the session: report it rather than looking for something to do.

        If you hit a question this repository cannot answer and that would change what
        you build, do not ask and do not wait. Write it into the `waitingOn:` of your
        claim's front matter, commit what you have, and end.

        You are charged one context per call and not one per token, so read the same
        in fewer of them: send the calls that do not depend on each other together,
        reach for a file with your own file and search tools rather than through
        `cat`, `sed`, `grep` and `ls`, and open a file once rather than in windows.

        `composer ci` before every commit. Report at the end what you read, what you
        changed, whether it is green, and what state your claim is in.
        TEXT;

    /**
     * The readings `bin/cli todo:next` exists to perform. Exactly one recurring
     * todo has to name each: none and the command silently drops half its job,
     * two and it does it twice.
     */
    public const READINGS = ['bin/cli unresolved:list', 'bin/cli todo:waiting'];

    /**
     * Whether the clock has come round for a recurring todo. Nothing here
     * knows whether there is anything to do — that is the `Run:` command's
     * answer, and it costs a process to ask.
     *
     * An unreadable cadence is due. A todo nobody can date is one that gets a
     * look, and the check below says so out loud in the same run.
     */
    public static function due(string $every, string $checked, ?string $today = null): bool
    {
        if (preg_match('/^(\d+) days?$/', $every, $matches) !== 1 || $checked === '') {
            return true;
        }

        $next = strtotime($checked . ' +' . $matches[1] . ' days');
        $now = strtotime($today ?? 'today');

        return $next === false || $now === false || $next <= $now;
    }

    /**
     * A queue somewhere other than this checkout's, which only a test asks
     * for.
     *
     * `R-COD-003`: a unit test writes into no directory this repository keeps.
     * The cases that hold a claim and a release have to write a todo to have
     * one. They used to write it into the real `todo/`. That was a fixture in
     * the queue a session reads. A marker in its name removed it afterwards,
     * and any run that died in between left it behind.
     */
    private static ?string $directory = null;

    /**
     * Where the queue lives, for as long as a test says so.
     *
     * The directory has to carry the name `todo` below a root of its own. A
     * todo's `path` is relative to that root, `todo/open/....md`, and a move
     * resolves the two against each other.
     */
    public static function useDirectory(?string $directory): void
    {
        self::$directory = $directory;
    }

    public static function directory(): string
    {
        return self::$directory ?? Paths::root() . '/todo';
    }

    /**
     * The checkout the queue belongs to, which a relative path resolves
     * against.
     */
    private static function root(): string
    {
        return self::$directory === null ? Paths::root() : dirname(self::$directory);
    }

    /**
     * The branch a todo's work happens on, which is the id and nothing else.
     *
     * Two sessions that name their own branches produce two names for one piece
     * of work, and nothing then says which is which. A derived name means the
     * branch follows from the todo and the todo from the branch. That is what
     * lets the worktree that stands on it say the todo is in hand, `D-DOC-060`.
     *
     * The id is the whole of the name a todo carries, so the branch is that
     * name under `todo/` — `D-DOC-061`. Nothing has to come off it, and a
     * retitle moves neither the branch nor the worktree that stands on it.
     *
     * @param Section $todo
     */
    public static function branch(array $todo): string
    {
        return 'todo/' . self::identifier($todo);
    }

    /**
     * A todo whose next step is an answer nobody here can give, moved to where
     * no session gets it.
     *
     * The one move left. To take a todo on is to cut a worktree and to finish
     * it is to delete the file. So neither writes anything for the other to
     * undo. A todo nobody works is in `open/` because the worktree is gone,
     * rather than because a command put it back.
     *
     * The question comes off the file rather than from a prompt. The session
     * that hit it wrote it. A todo whose next step is "wait for somebody to
     * answer" parked among the workable ones reads as ordinary work. All the
     * while it waits on a person.
     *
     * @param Section $todo
     *
     * @return string the path it has from now on
     */
    public static function park(array $todo): string
    {
        $to = 'todo/waiting/' . basename($todo['path']);
        $directory = dirname(self::root() . '/' . $to);
        if (!is_dir($directory) && !mkdir($directory) && !is_dir($directory)) {
            throw new \RuntimeException($directory . ' is not there and cannot be made');
        }

        // Moved rather than rewritten from the read. A move changes where the
        // file is and nothing in it, and a rewrite would re-emit somebody's
        // question as whatever a dumper makes of it.
        rename(self::root() . '/' . $todo['path'], self::root() . '/' . $to);

        return $to;
    }

    /**
     * Every todo there is: the queue in its order, then what recurs, then what
     * waits, then what is none of the three.
     *
     * What a session has in hand is not a stage of its own. It is a todo in the
     * queue with a worktree on its branch, which is what `inHand()` answers,
     * `D-DOC-060`.
     *
     * @return array<int, Section>
     */
    public static function all(): array
    {
        return array_merge(self::items(), self::recurring(), self::waiting(), self::references());
    }

    /**
     * The queue: the work to do once, in the order to do it, by priority, and
     * within one by age.
     *
     * Both halves come off the file rather than from a store. The word in the
     * front matter, and the stamp in the name that `read()` has already sorted
     * by. PHP's sort holds equal elements in the order they came in, which is
     * what makes the second half free.
     *
     * @return array<int, Section>
     */
    public static function items(): array
    {
        $items = self::read('open', 'queue');
        usort($items, static fn(array $left, array $right): int => self::rank($left) <=> self::rank($right));

        return $items;
    }

    /**
     * Where a todo's priority puts it. One with none is a file `bin/cli
     * todo:check` already reports, and it goes last rather than first, so a
     * defect cannot promote itself.
     *
     * @param Section $todo
     */
    private static function rank(array $todo): int
    {
        $at = array_search($todo['priority'], self::PRIORITIES, true);

        return $at === false ? count(self::PRIORITIES) : $at;
    }

    /**
     * What comes round and is never deleted.
     *
     * @return array<int, Section>
     */
    public static function recurring(): array
    {
        return self::read('recurring', 'recurring');
    }

    /**
     * What a session has in hand, as the branch each worktree stands on and the
     * directory it stands in.
     *
     * The queue is an order, not an assignment, and `bin/cli todo:next` reads
     * the same first item for everybody who asks. That is right while one
     * session works at a time and wrong the moment two do. What says a todo is
     * in hand is the worktree cut for it, `D-DOC-060`. A file in
     * `todo/progress/` was a third copy of that, and the one that could go
     * stale.
     *
     * Read as one call rather than one per worktree, because every caller here
     * wants the whole set.
     *
     * @return array<string, string> the branch of each, by the directory it is checked out in
     */
    public static function inHand(?string $root = null): array
    {
        return array_filter(self::worktrees($root));
    }

    /**
     * Every worktree below `.worktrees/`, and the branch it stands on.
     *
     * Read off git rather than off the directory. A `.worktrees/` entry git has
     * forgotten is not one anything here can merge, and the logs `todo:claim`
     * writes live in there beside the real ones.
     *
     * A detached one answers with the empty string and holds no todo, which is
     * the difference between this and `inHand()`. It is still a worktree
     * somebody has to be able to name, so it is not left out here.
     *
     * @return array<string, string> the branch of each, by the directory it is checked out in
     */
    public static function worktrees(?string $root = null): array
    {
        $root ??= Paths::root();
        [$listed, $said] = Checkouts::run(['git', '-C', $root, 'worktree', 'list', '--porcelain']);
        if ($listed !== 0) {
            return [];
        }

        $standing = [];
        $name = '';
        foreach (preg_split('/\R/', trim($said)) ?: [] as $line) {
            if (str_starts_with($line, 'worktree ')) {
                $path = substr($line, strlen('worktree '));
                $name = str_starts_with($path, $root . '/.worktrees/') ? basename($path) : '';
                if ($name !== '') {
                    $standing[$name] = '';
                }
                continue;
            }
            // A detached worktree says `detached` here instead, and stands on
            // no branch that leads to a todo.
            if ($name !== '' && str_starts_with($line, 'branch refs/heads/')) {
                $standing[$name] = substr($line, strlen('branch refs/heads/'));
            }
        }

        return $standing;
    }

    /**
     * The id that cites a todo, which is the whole of its file name.
     *
     * @param Section $todo
     */
    public static function identifier(array $todo): string
    {
        $name = basename($todo['path'], '.md');

        return preg_match(self::NAME, $name) === 1 ? $name : '';
    }

    /**
     * The worktree a caller named, or null where nothing here is that.
     *
     * A todo goes by its id and a worktree is an implementation of one in hand.
     * So both pass and the id is the one on record. Anything a caller pastes
     * resolves: the id, the path whose file name it is, or the worktree's own
     * directory.
     */
    public static function worktreeNamed(string $reference, ?string $root = null): ?string
    {
        $named = basename(rtrim($reference, '/'), '.md');
        $standing = self::worktrees($root);
        if (array_key_exists($named, $standing)) {
            return $named;
        }

        foreach (self::items() as $todo) {
            if (self::identifier($todo) !== $named) {
                continue;
            }

            $branch = self::branch($todo);
            $at = array_search($branch, $standing, true);

            return $at === false ? null : (string) $at;
        }

        return null;
    }

    /**
     * The todos somebody has in hand, by the branch each one's work happens on.
     *
     * @return array<string, Section>
     */
    public static function held(?string $root = null): array
    {
        $branches = array_flip(self::inHand($root));

        $held = [];
        foreach (self::items() as $todo) {
            $branch = self::branch($todo);
            if (isset($branches[$branch])) {
                $held[$branch] = $todo;
            }
        }

        return $held;
    }

    /**
     * The claim this checkout stands on, or null where it is on none.
     *
     * `bin/cli todo:next` is where a session starts, and that sentence has to
     * stay true in a worktree. A session on one of several claims would
     * otherwise be the one session here that starts differently. Whoever set it
     * up would tell it its file name. The command everything else points it at
     * would hand it the front of the queue. Getting that wrong is silent: it
     * reads a todo, it is a real todo, and it is somebody else's.
     *
     * The branch is what answers, because the branch derives from the todo and
     * from nothing else. A checkout on `main` is on no claim and gets the
     * queue, which is every session this repository had before there were two.
     *
     * @return Section|null
     */
    public static function claimed(): ?array
    {
        $branch = self::standing();
        if ($branch === '') {
            return null;
        }

        foreach (self::items() as $todo) {
            if (self::branch($todo) === $branch) {
                return $todo;
            }
        }

        return null;
    }

    /**
     * The branch this checkout stands on, or the empty string where git cannot
     * say.
     *
     * A detached head answers with `HEAD`, which is no branch and matches no
     * claim. That is the same as no answer, and it needs no case of its own.
     *
     * The root is a parameter so that a test can ask about a checkout other
     * than the one it runs in. Nothing else passes it: a session asks about
     * where it stands, and where it stands is where this file is.
     */
    public static function standing(?string $root = null): string
    {
        [$exitCode, $branch] = Checkouts::run(['git', '-C', $root ?? Paths::root(), 'rev-parse', '--abbrev-ref', 'HEAD']);

        return $exitCode === 0 ? trim($branch) : '';
    }

    /**
     * Whether this checkout is a worktree of another one rather than the
     * checkout itself.
     *
     * It is the one question that tells a session set up for a claim apart from
     * one meant to read the queue. The branch cannot answer it. A worktree on
     * the wrong branch and the main checkout on `main` both stand on no claim,
     * and only one of them is a mistake.
     *
     * git answers it with two directories. A linked worktree keeps its own
     * under the checkout they share, so `--git-dir` is that one and
     * `--git-common-dir` the shared one. In the checkout itself they are the
     * same directory. Both come as absolute paths. Git answers the shared one
     * relatively often enough that a comparison of what it prints would call
     * every main checkout a worktree.
     */
    public static function linked(?string $root = null): bool
    {
        $root ??= Paths::root();
        [$own, $ownDir] = Checkouts::run(['git', '-C', $root, 'rev-parse', '--absolute-git-dir']);
        [$shared, $sharedDir] = Checkouts::run(['git', '-C', $root, 'rev-parse', '--path-format=absolute', '--git-common-dir']);
        if ($own !== 0 || $shared !== 0) {
            return false;
        }

        return rtrim(trim($ownDir), '/') !== rtrim(trim($sharedDir), '/');
    }

    /**
     * What waits on an answer nothing here can produce, and so goes to no
     * session.
     *
     * A todo nobody can start used to go to the end of the queue. There `next`
     * would hand it to every session once the ones ahead of it had gone. It
     * read as the lowest priority in the repository while it waited on
     * somebody. Here it says what it waits on, and the answer is what moves it
     * back into the queue.
     *
     * @return array<int, Section>
     */
    public static function waiting(): array
    {
        return self::read('waiting', 'waiting');
    }

    /**
     * What recurs on a clock: an appointment, whose day either has come or has
     * not, and which is therefore asked before the queue. A miss is a missed
     * day, not a lost place in an order.
     *
     * @return array<int, Section>
     */
    public static function appointments(): array
    {
        return array_values(array_filter(self::recurring(), static fn(array $s): bool => $s['every'] !== 'session'));
    }

    /**
     * What recurs every session. A sighting of what arrived from outside, the
     * feedback and what nothing answers for, and a decision on what of it
     * becomes work.
     *
     * Asked last, once the queue is empty, because that decision is what puts
     * entries into the queue. While the queue still has any, a second sighting
     * is a second decision and no work. It is the group that would win every
     * session forever if it came first. Feedback arrive from every session
     * everywhere, and one session judges a handful.
     *
     * @return array<int, Section>
     */
    public static function sightings(): array
    {
        return array_values(array_filter(self::recurring(), static fn(array $s): bool => $s['every'] === 'session'));
    }

    /**
     * What a session would otherwise rediscover and mistake for work: the
     * environment table, the answers that stand rather than wait.
     *
     * @return array<int, Section>
     */
    public static function references(): array
    {
        return self::read('reference', 'reference');
    }

    /**
     * Everything the queue answers for. That is what turns an entry in
     * requirements/ or decisions/, or a feedback in feedback/, into work
     * somebody has taken on.
     *
     * Read from the queue and from what waits, which are the two states of work
     * somebody has taken on. One in hand is in the queue with a worktree on it.
     * What `reference/` keeps names ids too, and one of those pages is the list
     * of what is on purpose *not* in the queue. That is the opposite of in
     * hand. A recurring todo is not a take-on either: every session owes it.
     *
     * @return array<int, string>
     */
    public static function serves(): array
    {
        $served = [];
        foreach (array_merge(self::items(), self::waiting()) as $item) {
            foreach ($item['serves'] as $what) {
                $served[$what] = true;
            }
        }

        return array_keys($served);
    }

    /**
     * The cards a judgement folded into another todo and nothing took away.
     *
     * A feedback arrives with one card and never gets a second. So a session
     * that judges a cluster writes one todo with two feedback while the card
     * the first already had stays in the queue, `D-FBK-040`. The constant step
     * is what tells them apart, and the repair is a deletion, so this reports.
     *
     * @return array<int, array{card: string, feedback: string, judged: array<int, string>}>
     */
    public static function folded(): array
    {
        $todos = array_merge(self::items(), self::waiting());

        $serving = [];
        foreach ($todos as $todo) {
            foreach ($todo['serves'] as $what) {
                $serving[$what][] = $todo['path'];
            }
        }

        $folded = [];
        foreach ($todos as $todo) {
            if ($todo['body'] !== Card::STEP) {
                continue;
            }
            foreach ($todo['serves'] as $what) {
                $judged = array_values(array_diff($serving[$what], [$todo['path']]));
                if ($judged === []) {
                    continue;
                }

                $folded[] = ['card' => $todo['path'], 'feedback' => $what, 'judged' => $judged];
            }
        }

        return $folded;
    }

    /**
     * Why what a todo names is unreadable, or null where it reads.
     *
     * Five things are legitimate to serve, and each one checks against the
     * place that owns it rather than against a list kept here. A feedback is
     * the one worth the catch. It is the reason the todo is in the queue, and
     * the commit that closes it deletes the file. So a todo that still names
     * one is either finished or has a part left that nobody has trimmed it down
     * to.
     *
     * A decision goes by its id rather than by the directory it sits in. The
     * work such a todo carries is one entry's **Wrong if** gone back to, and
     * `decisions/` says only that somebody sorts the pile.
     */
    public static function unreadable(string $what): ?string
    {
        if (preg_match('/^R-[A-Z]{3}-\d+[a-z]?$/', $what) === 1) {
            return isset(Requirements::all()[$what]) ? null : 'which no requirement has';
        }

        if (preg_match('/^D-[A-Z]{3}-\d+[a-z]?$/', $what) === 1) {
            return isset(Decisions::all()[$what]) ? null : 'which no decision has';
        }

        if (preg_match('/^[A-Z]+-\d+$/', $what) === 1) {
            $scenarios = Scenarios::load() + Scenarios::contracts();

            return isset($scenarios[$what]) ? null : 'which no scenario has';
        }

        if (str_ends_with($what, '/')) {
            return is_dir(Paths::root() . '/' . $what) ? null : 'which is not a directory of this repository';
        }

        if (str_starts_with($what, 'feedback/')) {
            // A feedback in the archive has its answer, whether the todo names
            // it where it was or where it now is.
            return is_file(Paths::root() . '/' . $what) && !str_starts_with($what, 'feedback/archive/')
                ? null
                : 'and that feedback is closed — the todo is done, or trims to the part that is left';
        }

        return 'which is none of a requirement, a decision, a scenario, a feedback, or a directory of this repository';
    }

    /**
     * One directory of todos, in the order its file names have them, which for
     * the queue is the order of the work.
     *
     * The readme is the only file here that is not a todo. It is what the
     * directory says about itself rather than something to do.
     *
     * @return array<int, Section>
     */
    private static function read(string $group, string $kind): array
    {
        $directory = self::directory() . ($group === '' ? '' : '/' . $group);
        if (!is_dir($directory)) {
            return [];
        }

        $todos = [];
        foreach (Finder::create()->files()->in($directory)->depth(0)->name('*.md')->notName('readme.md')->sortByName() as $file) {
            $todos[] = self::parse($file->getPathname(), $kind);
        }

        return $todos;
    }

    /**
     * One file: its name, what it declares, and the step itself.
     *
     * @return Section
     */
    private static function parse(string $path, string $kind): array
    {
        $opening = Entry::opening((string) file_get_contents($path));
        $matter = $opening['matter'];

        return [
            'title' => $opening['heading'],
            'kind' => $kind,
            'priority' => Entry::value($matter, 'priority'),
            'path' => 'todo/' . basename(dirname($path)) . '/' . basename($path),
            'every' => Entry::value($matter, 'every'),
            'checked' => Entry::value($matter, 'checked'),
            'waitingOn' => Entry::value($matter, 'waitingOn'),
            'serves' => Entry::names($matter, 'serves'),
            'run' => Entry::names($matter, 'run'),
            'strays' => array_values(array_diff(array_keys($matter), self::FIELDS)),
            'body' => trim($opening['body']),
        ];
    }
}
