<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Feedback;

use Composer\InstalledVersions;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Paths;

/**
 * Stores improvement feedback from agents that use this server, so a session
 * can work gaps in the knowledge base off later.
 *
 * What this writes into is this repository and never the TYPO3 installation the
 * server reads, which is the line "read only" is about here, `D-FBK-042`.
 * isAvailable() is what makes it a development tool rather than part of using
 * the server, and no exclusion reaches the two tools (`R-SCO-009`). One
 * feedback per file, so concurrent agents never touch the same one. `Redaction`
 * takes out what a session pasted and should not have on the way in, because
 * the file goes into a commit and a push.
 */
final class Channel
{
    public const PACKAGE_NAME = 'typo3/dev-companion';

    public const CATEGORIES = ['missing-knowledge', 'wrong-answer', 'tool-gap', 'bug', 'idea'];

    /**
     * The cut of a stored field and a title. Public because `FeedbackRecord`
     * states both numbers in the descriptions a caller writes to, and a cap in
     * prose beside a constant goes stale, `D-FBK-049`.
     */
    public const MAX_FIELD_LENGTH = 4000;

    public const MAX_SUBJECT_LENGTH = 100;

    private const MAX_SLUG_LENGTH = 48;
    private const MAX_MODEL_LENGTH = 80;

    /** What a feedback says about the model where the session named none. */
    public const UNATTRIBUTED = 'unknown';

    /**
     * How a field cut for length says so, the way Redaction marks a value it
     * took out of one.
     */
    private const CUT_MARKER = '[cut: %s]';

    /** Separates the commits in the log a feedback's own answer comes from. */
    private const COMMIT_MARKER = "\x00";

    /**
     * True only when this package is the Composer root package, i.e. a
     * standalone checkout rather than a dependency in someone's vendor/.
     */
    public static function isAvailable(): bool
    {
        if (!class_exists(InstalledVersions::class)) {
            return false;
        }

        return InstalledVersions::getRootPackage()['name'] === self::PACKAGE_NAME;
    }

    /**
     * Records one feedback and returns the path it went to, relative to the
     * project root.
     *
     * What a session pasted and should not have comes out on the way in, and
     * `$redacted` is what came out. The caller hears it, because an altered
     * report says so or it is not a report. See Redaction for what counts and
     * why the thresholds are where they are.
     *
     * A field too long for the store whole changes as well. `$cut` is what went
     * from it, reported for the same reason and beside it.
     *
     * @param array<string, mixed> $args
     * @param array<int, string>   $redacted what was removed, by field and shape
     * @param array<int, string>   $cut      what was cut for length, by field and how much
     */
    public static function record(array $args, array &$redacted = [], array &$cut = []): string
    {
        self::assertAvailable();

        $redacted = [];
        $cut = [];
        self::assertNoCallFrame('observation', $args['observation'] ?? null);
        self::assertNoCallFrame('query', $args['query'] ?? null);
        self::assertNoCallFrame('suggestion', $args['suggestion'] ?? null);
        $observation = self::text('observation', $args['observation'] ?? '', $cut);
        if ($observation === '') {
            throw new \InvalidArgumentException('An observation is required.');
        }

        $category = self::category($args['category'] ?? null);
        $tools = self::toolNames($args['tool'] ?? null);
        $observation = self::withoutSecrets('observation', $observation, $redacted);
        $query = self::withoutSecrets('query', self::text('query', $args['query'] ?? '', $cut), $redacted);
        // The third field for the same reason as the other two, though neither
        // the leak behind this nor the todo that carried it named it. It is
        // prose a session writes and it goes into the same file, so a gap there
        // would be a hole with nothing behind it.
        $suggestion = self::withoutSecrets('suggestion', self::text('suggestion', $args['suggestion'] ?? '', $cut), $redacted);
        $model = self::model($args['model'] ?? null);

        $directory = Paths::feedback();
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Cannot create the feedback directory: %s', $directory));
        }

        // The one field that is a name rather than a report. So it gets the
        // same redaction as the rest and carries no marker where it lost
        // length. A title with a marker in the middle of it is what a listing
        // shows. What it lost still comes back in words. That half of
        // `R-FBK-015` reaches the session that still has the line, and the
        // file's `...` does not, `D-FBK-049`.
        $subject = self::withoutSecrets('subject', trim((string) ($args['subject'] ?? '')), $redacted);
        if (mb_strlen($subject) > self::MAX_SUBJECT_LENGTH) {
            $cut[] = 'subject: ' . self::overrun(mb_strlen($subject), self::MAX_SUBJECT_LENGTH);
        }

        $file = self::uniquePath($directory, $subject === '' ? $observation : $subject);
        $title = self::title($subject === '' ? $observation : $subject);
        $document = self::render($observation, $category, $tools, $query, $suggestion, $model, self::origin(), $title);

        if (file_put_contents($file, $document) === false) {
            throw new \RuntimeException(sprintf('Cannot write the feedback feedback: %s', $file));
        }

        $path = 'feedback/' . basename($file);
        // The card the report brings with it, written here rather than by the
        // commit that brings the feedback in. A session that records one and
        // goes on with its work leaves the board right. Nothing has to run
        // afterwards for it to be, `D-FBK-045`.
        Card::write($path, $title);

        return $path;
    }

    /**
     * Moves a feedback a session has worked off into the archive, and returns
     * the path it now has, relative to the project root.
     *
     * Where a feedback stands is what says whether it has an answer, so this is
     * the whole of its close. The open directory holds the questions, the
     * archive holds the ones that have an answer. The feedback itself changes
     * no further than that. It is a session's report about this server, and the
     * report is what makes the answer readable later.
     */
    public static function archive(string $feedback): string
    {
        self::assertAvailable();

        $name = basename(trim($feedback));
        $source = Paths::feedback() . '/' . $name;
        if ($name === '' || !str_ends_with($name, '.md') || !is_file($source)) {
            throw new \InvalidArgumentException(sprintf('There is no open feedback named %s.', $name));
        }

        $directory = Paths::feedbackArchive();
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Cannot create the archive directory: %s', $directory));
        }

        $target = $directory . '/' . $name;
        if (file_exists($target)) {
            throw new \RuntimeException(sprintf('%s is already archived.', $name));
        }

        $contents = (string) preg_replace(
            '/^status: .*$/m',
            "status: closed\nclosed: " . date('Y-m-d'),
            (string) file_get_contents($source),
            1,
        );
        if (file_put_contents($target, $contents) === false) {
            throw new \RuntimeException(sprintf('Cannot write the archived feedback: %s', $target));
        }
        if (!unlink($source)) {
            throw new \RuntimeException(sprintf('Cannot remove the feedback it was archived from: %s', $source));
        }

        return 'feedback/archive/' . $name;
    }

    /**
     * Reads recorded feedback, newest first.
     *
     * Both halves are the same files read the same way. So a query that asks
     * for a category or a tool answers over all of them. That is what the
     * archive buys. A closed feedback used to be a filename in a commit, with
     * the front matter that says what it was about long gone.
     *
     * @return array<int, array{file: string, date: string, category: string, status: string, model: string, directory: string, tool: string, tools: array<int, string>, title: string, closedBy: ?array{commit: string, date: string, subject: string}}>
     */
    public static function all(
        ?string $status = 'open',
        ?string $category = null,
        int $limit = 20,
        ?string $tool = null,
    ): array {
        self::assertAvailable();

        $directories = array_values(array_filter([
            $status === 'closed' ? null : Paths::feedback(),
            $status === 'open' ? null : Paths::feedbackArchive(),
        ], static fn(?string $directory): bool => $directory !== null && is_dir($directory)));

        $files = $directories === [] ? [] : Finder::create()->files()->in($directories)->depth(0)->name('*.md')
            // The filename starts with the timestamp of the record, so this is
            // newest first across both halves. Which directory a feedback is in
            // says whether it has an answer, not when it arrived.
            ->sort(static fn(\SplFileInfo $left, \SplFileInfo $right): int => strcmp($right->getFilename(), $left->getFilename()));

        $wanted = $tool === null ? null : (self::comparable(self::toolNames($tool)[0] ?? '') ?: null);
        $answers = self::answers();

        $found = [];
        foreach ($files as $file) {
            $feedback = self::parse($file->getPathname(), $answers);
            if ($feedback === null) {
                continue;
            }
            if ($category !== null && $feedback['category'] !== $category) {
                continue;
            }
            if ($wanted !== null && !in_array($wanted, array_map(self::comparable(...), $feedback['tools']), true)) {
                continue;
            }

            $found[] = $feedback;
            if (count($found) >= $limit) {
                break;
            }
        }

        return $found;
    }

    /**
     * What became of each archived feedback, read from the commit that archived it.
     *
     * One commit implements the improvement and moves the feedback, so that
     * commit's subject is the sentence that answers it. The answer is the half
     * the agent that reported the gap cannot see for itself. A read back from
     * the history rather than a write into the feedback keeps it from a second
     * copy of what git already has. A feedback in the archive but not yet in a
     * commit simply has no answer yet.
     *
     * The feedback from before this archive existed carry their own commit in
     * the front matter, which wins. They all moved here in one commit, and that
     * move says nothing about any of them.
     *
     * @return array<string, array{commit: string, date: string, subject: string}>
     */
    private static function answers(): array
    {
        $log = self::git([
            'log',
            '--diff-filter=A',
            '--name-only',
            '--date=short',
            // %x00 and %x1f are git's own escapes. The argument carries them as
            // text, and git writes the separators the output splits on.
            '--format=%x00%h%x1f%ad%x1f%s',
            '--',
            'feedback/archive',
        ], self::root());
        if ($log === null) {
            return [];
        }

        $answers = [];
        foreach (explode(self::COMMIT_MARKER, $log) as $block) {
            $lines = preg_split('/\R/', trim($block)) ?: [];
            $header = array_shift($lines);
            if ($header === null || !str_contains($header, "\x1f")) {
                continue;
            }
            [$commit, $date, $subject] = array_pad(explode("\x1f", $header, 3), 3, '');

            foreach ($lines as $path) {
                $path = trim($path);
                if (str_starts_with($path, 'feedback/archive/') && str_ends_with($path, '.md')) {
                    $answers[$path] ??= ['commit' => $commit, 'date' => $date, 'subject' => $subject];
                }
            }
        }

        return $answers;
    }

    /**
     * One git command in the checkout the store belongs to, null where git could
     * not answer.
     *
     * The working directory is the store's own root rather than
     * `Paths::root()`. So a test that writes into a store of its own carries no
     * `.git`. This answers null before it starts anything, and no unit test
     * runs `git log`, `R-COD-003`.
     *
     * @param array<int, string> $command
     */
    private static function git(array $command, string $workingDirectory): ?string
    {
        if (!is_dir($workingDirectory . '/.git')) {
            return null;
        }

        // stdin closes for the child rather than passes down, for the reason
        // Typo3Cli::execute() states. This runs while the server answers a
        // client, and the client's next request sits in that stdin.
        $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = @proc_open(array_merge(['git'], $command), $descriptors, $pipes, $workingDirectory, null);
        if (!is_resource($process)) {
            return null;
        }

        fclose($pipes[0]);
        $output = (string) stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return proc_close($process) === 0 ? $output : null;
    }

    /**
     * One field with what looked like a credential taken out of it, and a line
     * on the list for each thing taken.
     *
     * The field stands by name in what comes back because the answer has to say
     * where the value was. A session that pasted a key into its observation and
     * its query has two things to look at. One sentence that names neither
     * sends it to read the whole file.
     *
     * @param array<int, string> $redacted
     */
    private static function withoutSecrets(string $field, string $text, array &$redacted): string
    {
        $redaction = Redaction::of($text);
        foreach ($redaction->removed as $what) {
            $redacted[] = $field . ': ' . $what;
        }

        return $redaction->text;
    }

    /**
     * The checkout the feedback store belongs to, which a path stands relative
     * to.
     *
     * Read off the store rather than from `Paths::root()`. So a test that
     * writes into a store of its own gets back the same relative path
     * `record()` gave it, `R-COD-003`.
     */
    public static function root(): string
    {
        return dirname(Paths::feedback());
    }

    private static function assertAvailable(): void
    {
        if (!self::isAvailable()) {
            throw new \RuntimeException(
                'Feedback is only available when the server runs from a standalone checkout, '
                . 'not when it is installed as a Composer dependency.',
            );
        }
    }

    /**
     * @param array<string, array{commit: string, date: string, subject: string}> $answers
     * @return array{file: string, date: string, category: string, status: string, model: string, directory: string, tool: string, tools: array<int, string>, title: string, closedBy: ?array{commit: string, date: string, subject: string}}|null
     */
    private static function parse(string $file, array $answers): ?array
    {
        $contents = file_get_contents($file);
        if ($contents === false) {
            return null;
        }

        $meta = [];
        if (preg_match('/^---\R(.*?)\R---\R/s', $contents, $matches) === 1) {
            foreach (preg_split('/\R/', $matches[1]) ?: [] as $line) {
                if (preg_match('/^([a-z]+):\s*(.*)$/', trim($line), $pair) === 1) {
                    $meta[$pair[1]] = trim($pair[2]);
                }
            }
        }

        // The first heading is the feedback's title.
        $title = '';
        if (preg_match('/^# (.+)$/m', $contents, $heading) === 1) {
            $title = trim($heading[1]);
        }

        $tools = self::toolNames($meta['tool'] ?? '');
        $relative = substr($file, strlen(self::root()) + 1);
        $archived = str_starts_with($relative, 'feedback/archive/');

        return [
            'file' => $relative,
            'date' => $meta['date'] ?? '',
            'category' => $meta['category'] ?? 'idea',
            // The directory says it, not the front matter. A feedback with an
            // answer is one that moved. A status somebody edited in place is
            // the one thing that could disagree with where it is.
            'status' => $archived ? 'closed' : 'open',
            // A feedback written before the field existed carries no model, which
            // is the same thing the field says when it was not answered.
            'model' => $meta['model'] ?? self::UNATTRIBUTED,
            // Where the session stood. It goes into every feedback and was
            // readable only inside one. So nothing could see that thirty
            // sessions out of one checkout reported a gap rather than one,
            // which is a different judgement (`D-FBK-025`).
            'directory' => $meta['directory'] ?? '',
            // Both. The string is what a feedback has always carried, the list
            // is what a caller can filter or group by without a parse back.
            'tool' => implode(', ', $tools),
            'tools' => $tools,
            'title' => $title,
            // An open feedback has nothing that closes it yet. The field is
            // present either way so a caller never has to ask which half it
            // has.
            'closedBy' => $archived ? self::answer($meta, $answers[$relative] ?? null) : null,
        ];
    }

    /**
     * What the feedback itself says became of it, and otherwise what the history
     * says. Only the feedback restored from before the archive existed carry it
     * themselves — see answers().
     *
     * @param array<string, string> $meta
     * @param array{commit: string, date: string, subject: string}|null $fromHistory
     * @return array{commit: string, date: string, subject: string}|null
     */
    private static function answer(array $meta, ?array $fromHistory): ?array
    {
        $subject = trim($meta['subject'] ?? '', '"');
        if ($subject === '') {
            return $fromHistory;
        }

        return [
            'commit' => $meta['commit'] ?? '',
            'date' => $meta['closed'] ?? '',
            'subject' => $subject,
        ];
    }

    /**
     * Where the feedback came from: the working directory of the session that
     * left it.
     *
     * A feedback reads long after the session that produced it has ended. "The
     * icon lookup returned nothing" means something different according to the
     * project of the question. The directory is the one thing that says which.
     * It is how the feedback gets its check against the installation it came
     * from instead of against whatever is at hand.
     *
     * Only ever the directory an entrypoint handed to Instance. So a feedback
     * left through an endpoint that has no caller directory simply carries none
     * rather than that endpoint's own.
     */
    private static function origin(): string
    {
        $startedFrom = Instance::startedFrom();

        return $startedFrom === null ? '' : $startedFrom;
    }

    /**
     * @param array<int, string> $tools
     */
    private static function render(
        string $observation,
        string $category,
        array $tools,
        string $query,
        string $suggestion,
        string $model,
        string $origin,
        string $title,
    ): string {
        $frontMatter = [
            'date: ' . date('c'),
            'category: ' . $category,
            'status: open',
            // Always present, "unknown" included. A feedback that carries no
            // model at all looks the same as one from before the field existed.
            // The whole point of the field is that a feedback without an
            // attribution says so.
            'model: ' . $model,
        ];
        if ($tools !== []) {
            $frontMatter[] = 'tool: ' . implode(', ', $tools);
        }
        if ($origin !== '') {
            $frontMatter[] = 'directory: ' . $origin;
        }

        $document = "---\n" . implode("\n", $frontMatter) . "\n---\n\n";
        $document .= '# ' . $title . "\n\n";
        $document .= "## Observation\n\n" . $observation . "\n";

        if ($query !== '') {
            $document .= "\n## Query\n\n" . $query . "\n";
        }
        if ($suggestion !== '') {
            $document .= "\n## Suggestion\n\n" . $suggestion . "\n";
        }

        return $document;
    }

    /**
     * Builds the filename from a timestamp plus a slug of the observation. The
     * agent never supplies the name, so it cannot escape the directory.
     *
     * The slug begins at the first word that tells this feedback apart from one
     * already named after the same opening. A session files one feedback per
     * subject and files them in one breath. So eight of them open on the
     * sentence that says which session this is, "Debrief of the … session,
     * missed item: …". Those are exactly the 48 characters a name has room for.
     * Eight files then differ by their timestamp alone, which is the one thing
     * about a feedback nobody looks for. What goes in the name has to be what
     * only this feedback says.
     */
    private static function uniquePath(string $directory, string $observation): string
    {
        $words = self::words($observation);
        $slug = self::slug($words);

        // Only the feedback whose name this one would take need a read, and
        // their own first line is where the shared start measures.
        $shared = 0;
        foreach (Finder::create()->files()->in($directory)->depth(0)->name('*-' . $slug . '.md') as $taken) {
            $shared = max($shared, self::opening($words, self::words(self::heading($taken->getPathname()))));
        }
        if ($shared > 0 && $shared < count($words)) {
            $slug = self::slug(array_slice($words, $shared));
        }

        $base = date('Y-m-d-His') . ($slug === '' ? '' : '-' . $slug);

        $file = $directory . '/' . $base . '.md';
        $counter = 2;
        while (file_exists($file)) {
            $file = $directory . '/' . $base . '-' . $counter . '.md';
            ++$counter;
        }

        return $file;
    }

    /**
     * How many words two observations open with in common, which is where the
     * one under write starts to say something of its own.
     *
     * @param array<int, string> $left
     * @param array<int, string> $right
     */
    private static function opening(array $left, array $right): int
    {
        $shared = 0;
        while (isset($left[$shared], $right[$shared]) && $left[$shared] === $right[$shared]) {
            ++$shared;
        }

        return $shared;
    }

    /** A feedback's own first line, as it stands in the file. */
    private static function heading(string $file): string
    {
        $contents = (string) file_get_contents($file);

        return preg_match('/^# (.+)$/m', $contents, $heading) === 1 ? trim($heading[1]) : '';
    }

    /** @return array<int, string> */
    private static function words(string $text): array
    {
        $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($text)), '-');

        return $slug === '' ? [] : explode('-', $slug);
    }

    /** @param array<int, string> $words */
    private static function slug(array $words): string
    {
        $slug = implode('-', $words);
        if (strlen($slug) <= self::MAX_SLUG_LENGTH) {
            return $slug;
        }

        // Cut on a word boundary so the filename stays readable.
        $slug = substr($slug, 0, self::MAX_SLUG_LENGTH);
        $lastDash = strrpos($slug, '-');

        return $lastDash === false ? $slug : substr($slug, 0, $lastDash);
    }

    /**
     * The first line, cut to a length by characters rather than by bytes.
     *
     * `substr()` counts bytes, and a feedback is prose an agent wrote. One that
     * arrived with a dash or a quotation mark past the ninety-seventh byte got
     * a cut through the middle of it. What the heading then carried was half a
     * character. It is not only ugly. The file stops as valid UTF-8, so `grep`
     * treats it as binary and silently matches nothing in it, which is how this
     * turned up.
     */
    private static function title(string $observation): string
    {
        $firstLine = trim((string) strtok($observation, "\n"));
        if (mb_strlen($firstLine) <= self::MAX_SUBJECT_LENGTH) {
            return $firstLine;
        }

        // The `...` stands inside the cap rather than past it, which is where
        // text() puts its own marker. A reader meets a title in a listing,
        // where the three characters are what says it goes on.
        return mb_substr($firstLine, 0, self::MAX_SUBJECT_LENGTH - 3) . '...';
    }

    /**
     * One field as the store has it: cut where a stored field ends, with a mark
     * where the cut fell.
     *
     * Marked for the reason a redaction has a mark. An altered report says so,
     * or a reader cannot tell it from one that ended there. The cut needs it
     * more. A redaction leaves the name of what it took beside its marker. This
     * falls on a character count, mid-word, and takes with it every sign that
     * the sentence went anywhere. `feedback/2026-08-03-144316` lost the
     * sentence that named what it reported, and neither the file nor the answer
     * said a word had gone.
     *
     * The marker stands past the cap rather than inside it, which is where
     * title() puts its own. The cap is what a session's prose holds to. The
     * marker's characters out of that prose would cut the field twice over in
     * order to say once that it had a cut.
     *
     * @param array<int, string> $cut
     */
    private static function text(string $field, mixed $value, array &$cut): string
    {
        if (!is_string($value)) {
            return '';
        }

        $text = trim($value);
        $length = mb_strlen($text);
        if ($length <= self::MAX_FIELD_LENGTH) {
            return $text;
        }

        $what = self::overrun($length, self::MAX_FIELD_LENGTH);
        $cut[] = $field . ': ' . $what;

        return mb_substr($text, 0, self::MAX_FIELD_LENGTH) . ' ' . sprintf(self::CUT_MARKER, $what);
    }

    /**
     * What a cut took, in the one form the answer and the marker both use.
     */
    private static function overrun(int $length, int $cap): string
    {
        return sprintf('%d characters past the %d-character limit', $length - $cap, $cap);
    }

    /**
     * A field that arrives with the call it came in meets a refusal.
     *
     * A parameter closed with a tag of its own name swallows everything behind
     * it. So the arguments never arrive and the report lands with its proposal
     * mid-paragraph, `D-FBK-044`. The two shapes are structural rather than
     * topical, because a session that reports *about* this failure quotes the
     * markers inline.
     */
    private static function assertNoCallFrame(string $field, mixed $value): void
    {
        if (!is_string($value)) {
            return;
        }

        $text = trim($value);
        if (!str_ends_with($text, '</invoke>') && preg_match('/\n<parameter name="[^"]+">/', $text) !== 1) {
            return;
        }

        throw new \InvalidArgumentException(sprintf(
            'The %s carries the frame of the call it arrived in. A parameter closed with a tag named after itself '
            . 'swallows everything after it, arguments included, so what reached this server is one field holding '
            . 'the rest of the call. Close every parameter with the closing tag its opening declared and send the '
            . 'call again.',
            $field,
        ));
    }

    private static function category(mixed $value): string
    {
        return is_string($value) && in_array($value, self::CATEGORIES, true) ? $value : 'idea';
    }

    /**
     * The model that left the feedback, as it named itself.
     *
     * Half the feedback this server receives are about what a session did
     * rather than about what an answer said. A skill whose steps got a read and
     * no run, a tool nothing reached for. That is behaviour, and behaviour
     * belongs to one model. Without the name, two models' habits arrive as one
     * report and nobody can work off either.
     *
     * The schema asks for it and the write never fails on it. A feedback is
     * worth more than its attribution, and an absent name lands as "unknown".
     * That is also what a model that does not know its own identifier should
     * send, because an invented one is worse than none.
     */
    private static function model(mixed $value): string
    {
        $model = is_string($value) ? trim((string) preg_replace('/\s+/', ' ', $value)) : '';
        if ($model === '') {
            return self::UNATTRIBUTED;
        }

        return mb_strlen($model) > self::MAX_MODEL_LENGTH ? mb_substr($model, 0, self::MAX_MODEL_LENGTH) : $model;
    }

    /**
     * The tools a feedback is about.
     *
     * An observation is regularly about several tools at once, so a string
     * splits on what separates names in it. What remains of a name stays as the
     * session wrote it, the hyphen (`R-FBK-013`) and the case (`D-FBK-039`),
     * because `comparable()` is where two forms meet. A list still passes for a
     * caller inside this package and no longer for one on the wire, which
     * `D-ANS-017` traded away.
     *
     * @return array<int, string>
     */
    private static function toolNames(mixed $value): array
    {
        $candidates = is_array($value)
            ? $value
            : (preg_split('/[\s,;]+/', is_string($value) ? $value : '') ?: []);

        $names = [];
        foreach ($candidates as $candidate) {
            if (!is_string($candidate)) {
                continue;
            }
            $name = (string) preg_replace('/[^A-Za-z0-9_-]/', '', trim($candidate));
            if ($name !== '') {
                $names[] = $name;
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * One name in the form two versions of it can meet in.
     *
     * A tool is `typo3_documentation_lookup` and a skill is
     * `typo3-extension-health`, and a session names either with whichever
     * separator it has in front of it. The store has what the session wrote, so
     * the filter is where the forms meet. That is the rule `D-ANS-006` already
     * applies to an identifier a caller looks up, applied to the one thing this
     * store filters by.
     */
    private static function comparable(string $name): string
    {
        return (string) preg_replace('/[^a-z0-9]/', '', strtolower($name));
    }
}
