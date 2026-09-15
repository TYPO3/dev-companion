<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use TYPO3\DevCompanion\Paths;

/**
 * The links this repository writes between its own files, resolved.
 *
 * A decision cites the requirement it stands on, a todo cites the decision it
 * came from, a documentation page cites both. By path, so a reader can follow
 * it. Nothing read those paths back. A rename of nineteen decision files in one
 * pass rewrote 58 references across 32 files. A single one missed would have
 * been a link that silently goes nowhere. No check fails, no test runs over it,
 * and the reader who follows it is the first to find out.
 *
 * The check reads the path and never the anchor. A heading moves inside a file
 * often enough that a hold on `#a-heading` would fail on prose edits. A link to
 * the right file with a stale fragment still lands the reader on the right
 * page.
 */
final class Links
{
    /**
     * A relative markdown link: `](path)` inline, `[id]: path` as a reference
     * definition. Both forms are in use here, and the generated listings use
     * the second.
     */
    private const PATTERN = '/(?:\]\(\s*([^)\s#][^)\s]*)\s*\)|^\[[^\]]+\]:\s*(\S+))/m';

    /**
     * The same thing in reStructuredText, which `documentation/` uses and the
     * rest of this corpus does not, `D-DOC-029`.
     *
     * Five forms carry a path. An embedded URI, which is the form of a link
     * that leaves the published tree and the only one `Site` rewrites. A
     * `:doc:`, which names another page and carries no extension. A directive,
     * which takes its path as its argument. And a card's `:href:` and `:src:`,
     * which are options rather than arguments. `:ref:` names a label rather
     * than a path, so `deadLabels()` is what reads it.
     */
    private const RST_PATTERN = '/(?:`[^`<]*<\s*([^>\s]+)\s*>`__?|:doc:`(?:[^`<]*<\s*([^>\s]+)\s*>|([^`<>]+))`|^\s*\.\.\s+(?:image|figure|hero|include|literalinclude)::\s*(\S+)|^\s*:href:\s*(\S+)|^\s*:src:\s*(\S+))/m';

    /**
     * The one form that can point outside this corpus, and so the one form
     * `rewritten()` may touch.
     */
    private const RST_EMBEDDED = '/`[^`<]*<\s*([^>\s]+)\s*>`__?/';

    /** Where a `:ref:` points, and where a label that answers one stands. */
    private const RST_REFERENCE = '/:ref:`(?:[^`<]*<\s*([^>\s]+)\s*>|([^`<>]+))`/';
    private const RST_LABEL = '/^\.\.\s+_([^:]+):\s*$/m';

    /** Which of the two this repository writes a given file in. */
    private static function isRst(string $file): bool
    {
        return str_ends_with($file, '.rst');
    }

    /**
     * Every dead link in the prose this repository writes about itself.
     *
     * The corpus is `Prose`'s, for the same reason it excludes `feedback/`. A
     * feedback is a session's report from somewhere else, kept as it arrived.
     * So a path that never resolved here is a fact about that session rather
     * than a defect in this repository.
     *
     * @return list<array{file: string, link: string, line: int, repair: ?string}>
     */
    public static function dead(): array
    {
        $dead = [];
        foreach (Prose::documents() as $document) {
            $dead = array_merge($dead, self::deadIn($document));
        }

        return $dead;
    }

    /**
     * The dead links in one file, resolved against the directory that file sits
     * in, which is what a reader following the link does.
     *
     * @return list<array{file: string, link: string, line: int, repair: ?string}>
     */
    public static function deadIn(string $file): array
    {
        $contents = file_get_contents(str_starts_with($file, '/') ? $file : Paths::root() . '/' . $file);
        if ($contents === false) {
            return [];
        }

        // The corpus comes relative to the root, and a link resolves against
        // the directory of the file that writes it. So both become absolute
        // here rather than stay with the directory the command ran from.
        $root = Paths::root() . '/';
        $absolute = str_starts_with($file, '/') ? $file : $root . $file;
        $relative = str_starts_with($absolute, $root) ? substr($absolute, strlen($root)) : $absolute;
        $directory = dirname($absolute);

        $dead = [];
        $targets = self::isRst($file) ? self::rstTargets($contents) : self::targets($contents);
        foreach ($targets as $target => $offset) {
            $target = (string) $target;
            // A link to a heading in the same file names no path, and a
            // reference definition may take that form too.
            if (str_starts_with($target, '#') || self::isExternal($target)) {
                continue;
            }

            $path = strtok($target, '#');
            if ($path === false) {
                continue;
            }

            // A path written from the root of the published tree rather than
            // from the page, which is what a drawing shared by two directories
            // is written as.
            $against = str_starts_with($path, '/')
                ? Paths::root() . '/' . Site::SOURCE . $path
                : $directory . '/' . $path;

            if (file_exists($against) || file_exists(self::publishedFrom($against))) {
                continue;
            }

            $repair = self::archived($against, $path);
            $dead[] = [
                'file' => $relative,
                'link' => $target,
                'line' => substr_count(substr($contents, 0, $offset), "\n") + 1,
                // The fragment stays on whatever the path becomes: the heading
                // it names moved with the file.
                'repair' => $repair === null ? null : $repair . substr($target, strlen($path)),
            ];
        }

        return $dead;
    }

    /**
     * Where a link points once the feedback it names has its answer, and null
     * where the archive does not explain it.
     *
     * `feedback:archive` moves a report and cannot see a link another branch
     * writes to it in the same window. Whichever of the two merges second
     * carries a link to a file that moved. The repair is the same every time:
     * the path gains `archive/`, `D-DOC-064`.
     */
    private static function archived(string $against, string $path): ?string
    {
        $repaired = (string) preg_replace('#(^|/)feedback/(?!archive/)#', '$1feedback/archive/', $path, 1);
        if ($repaired === $path) {
            return null;
        }

        // The resolved path ends in the written one, whichever of the two forms
        // it resolved from, so the same rewrite lands on both.
        return file_exists(substr($against, 0, strlen($against) - strlen($path)) . $repaired) ? $repaired : null;
    }

    /**
     * Repoints every dead link the feedback archive explains, and says what it
     * rewrote.
     *
     * Written into the file rather than reported, because there is one right
     * answer and no read tells it from another. The report instead names the
     * link that stayed dead, which `links:check` fails on as before.
     *
     * @return list<array{file: string, link: string, repair: string}>
     */
    public static function repair(): array
    {
        $written = [];
        foreach (Prose::documents() as $document) {
            $written = array_merge($written, self::repairIn($document));
        }

        return $written;
    }

    /**
     * The same for one file, rewritten in a single pass so a target that occurs
     * twice moves twice.
     *
     * @return list<array{file: string, link: string, repair: string}>
     */
    public static function repairIn(string $file): array
    {
        $repairs = [];
        foreach (self::deadIn($file) as $link) {
            if ($link['repair'] !== null) {
                $repairs[$link['link']] = $link['repair'];
            }
        }
        if ($repairs === []) {
            return [];
        }

        $absolute = str_starts_with($file, '/') ? $file : Paths::root() . '/' . $file;
        $contents = (string) file_get_contents($absolute);

        // Longest first, so a target that is the tail of another — the same
        // feedback named at a heading and plain — is not rewritten inside it.
        uksort($repairs, static fn(string $one, string $other): int => strlen($other) <=> strlen($one));

        $written = [];
        foreach ($repairs as $link => $repair) {
            $contents = str_replace((string) $link, $repair, $contents);
            $written[] = ['file' => $file, 'link' => (string) $link, 'repair' => $repair];
        }
        file_put_contents($absolute, $contents);

        return $written;
    }

    /**
     * Every link a reStructuredText page writes in markdown.
     *
     * It resolves on disk, so `dead()` never sees it: the file is there and the
     * path is right. What is wrong is the syntax, and the reader is the check.
     * `[answers/](../../decisions/answers/readme.md)` renders as exactly those
     * characters, and the group listing on two of these pages had stood that
     * way since the corpus moved.
     *
     * A code block stays as it is. What the tool pages record is the answer a
     * tool gave, which is markdown and renders as itself.
     *
     * @return list<array{file: string, link: string, line: int}>
     */
    public static function unrendered(): array
    {
        $unrendered = [];
        foreach (Prose::documents() as $document) {
            if (self::isRst($document)) {
                $unrendered = array_merge($unrendered, self::unrenderedIn($document));
            }
        }

        return $unrendered;
    }

    /**
     * The same for one file, which is where a code block stands apart from the
     * prose around it. A directive opens one, and it holds for as long as the
     * lines under it are blank or indented past it.
     *
     * @return list<array{file: string, link: string, line: int}>
     */
    public static function unrenderedIn(string $file): array
    {
        $absolute = str_starts_with($file, '/') ? $file : Paths::root() . '/' . $file;
        $contents = file_get_contents($absolute);
        if ($contents === false) {
            return [];
        }

        $unrendered = [];
        $literal = null;
        foreach (explode("\n", $contents) as $number => $line) {
            if ($literal !== null && (trim($line) === '' || strlen($line) - strlen(ltrim($line)) > $literal)) {
                continue;
            }

            $literal = preg_match('/^(\s*)\.\.\s+(?:code-block|literalinclude)::/', $line, $match) === 1
                ? strlen($match[1])
                : null;

            if ($literal === null && preg_match(self::PATTERN, $line, $match) === 1) {
                $unrendered[] = ['file' => $file, 'link' => trim($match[0]), 'line' => $number + 1];
            }
        }

        return $unrendered;
    }

    /**
     * Every `:ref:` in the corpus that no label answers.
     *
     * A label is reachable from anywhere, so this is the one check here that
     * cannot run a file at a time. The answer to a reference in one page is a
     * line in another. That is also what the reference buys over the markdown
     * it replaces. That could only ever point at a file and hope the heading
     * was still in it.
     *
     * @return list<array{file: string, link: string, line: int}>
     */
    public static function deadLabels(): array
    {
        $labels = [];
        $referenced = [];
        foreach (Prose::documents() as $document) {
            if (!self::isRst($document)) {
                continue;
            }
            $contents = (string) file_get_contents(Paths::root() . '/' . $document);

            preg_match_all(self::RST_LABEL, $contents, $matches);
            foreach ($matches[1] as $label) {
                $labels[trim($label)] = true;
            }

            preg_match_all(self::RST_REFERENCE, $contents, $matches, PREG_OFFSET_CAPTURE);
            foreach ([1, 2] as $group) {
                foreach ($matches[$group] as $match) {
                    if ($match[0] !== '') {
                        $referenced[] = [$document, trim($match[0]), $match[1], $contents];
                    }
                }
            }
        }

        $dead = [];
        foreach ($referenced as [$document, $label, $offset, $contents]) {
            if (isset($labels[$label])) {
                continue;
            }
            $dead[] = [
                'file' => $document,
                'link' => ':ref:`' . $label . '`',
                'line' => substr_count(substr($contents, 0, (int) $offset), "\n") + 1,
            ];
        }

        return $dead;
    }

    /**
     * The same links, each put through a function that says what it becomes.
     *
     * `documentation:prepare` writes a copy of a tree whose links suit a
     * checkout. Some have to change and the rest stay exactly as they stand.
     * Which link is which is this class's question already. A second read of
     * what a link is would answer it differently the first time either one
     * changed.
     *
     * The function sees the internal ones alone. An external target and a bare
     * heading are no paths this repository keeps, and what rewrites paths has
     * no business with them.
     *
     * Only the embedded-URI form reaches it, because it is the only one that
     * can leave the tree. A `:doc:` and a `:ref:` name a page and a label of
     * this corpus. The renderer resolves those itself, and a rewrite here could
     * only break them. That is what the corpus gained by the move to
     * reStructuredText, and `Site` shrank by exactly that much.
     *
     * @param callable(string): string $rewrite
     */
    public static function rewritten(string $contents, callable $rewrite): string
    {
        return (string) preg_replace_callback(
            self::RST_EMBEDDED,
            static function (array $match) use ($rewrite): string {
                $target = $match[1];
                if (str_starts_with($target[0], '#') || self::isExternal($target[0])) {
                    return $match[0][0];
                }

                // The offsets are what puts the new target back exactly where
                // the old one stood, whatever the text in front of it was.
                return substr_replace($match[0][0], $rewrite($target[0]), $target[1] - $match[0][1], strlen($target[0]));
            },
            $contents,
            flags: PREG_OFFSET_CAPTURE,
        );
    }

    /**
     * The link targets in a reStructuredText document, each with where it
     * stands. A file answers a `:doc:`, so the report names the file rather
     * than the reference that named it.
     *
     * @return array<string, int>
     */
    private static function rstTargets(string $contents): array
    {
        if (preg_match_all(self::RST_PATTERN, $contents, $matches, PREG_OFFSET_CAPTURE) === false) {
            return [];
        }

        // What answers each form: a file as written, or a page the reference
        // names without its extension.
        $targets = [];
        foreach ([1 => '', 2 => '.rst', 3 => '.rst', 4 => '', 5 => '.rst', 6 => ''] as $group => $extension) {
            foreach ($matches[$group] as $match) {
                if ($match[0] !== '') {
                    $targets[$match[0] . $extension] = $match[1];
                }
            }
        }

        return $targets;
    }

    /**
     * The link targets in a document, each with where it stands. So the report
     * names a line rather than a file somebody then has to search.
     *
     * @return array<string, int>
     */
    private static function targets(string $contents): array
    {
        if (preg_match_all(self::PATTERN, $contents, $matches, PREG_OFFSET_CAPTURE) === false) {
            return [];
        }

        $targets = [];
        foreach ([1, 2] as $group) {
            foreach ($matches[$group] as $match) {
                if ($match[0] !== '') {
                    $targets[$match[0]] = $match[1];
                }
            }
        }

        return $targets;
    }

    /**
     * Where a link that a skill resolves only once published points in this
     * checkout.
     *
     * Each `SKILL.md` opens with a pointer to `references/base.md`, which
     * `Installer` writes as a copy of `skills/base.md` when it publishes the
     * skill
     * ([`D-SKL-001`](../../decisions/task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md)).
     * So the link is right and the file is absent here, and what holds it is
     * the source it will be a copy of.
     */
    private static function publishedFrom(string $path): string
    {
        if (preg_match('#/skills/[^/]+/references/base\.md$#', $path) === 1) {
            return Paths::root() . '/skills/base.md';
        }

        // A `:doc:` stands against the published copy, where a directory's own
        // page is `index`. In the checkout that same file is `readme`, and
        // `Site` is what renames it on the way over.
        return (string) preg_replace('#(^|/)index\.rst$#', '$1readme.rst', $path);
    }

    /**
     * Anything that leaves this checkout is somebody else's to keep true. A
     * network call is not what a check on a working copy may cost.
     */
    private static function isExternal(string $path): bool
    {
        return (bool) preg_match('#^(?:[a-z][a-z0-9+.-]*:|//)#i', $path);
    }
}
