<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * Markdown rewrapped to the column this repository already writes at.
 *
 * A rename sweeps a word out of a hundred paragraphs and leaves every one of
 * them ragged. The reflow that follows was a throwaway script each time. The
 * script is the part worth a keep. What it does is the same every time. What it
 * must not touch, a fence, a table, a code span, is the same every time too.
 *
 * Only the wrapping moves. Every word, in order, comes out the same, which is
 * what `ProseTest` asserts rather than trusts. A formatter that can drop a word
 * is worse than no formatter, because the loss reads as an edit somebody made.
 */
final class Wrap
{
    /**
     * Where a line ends.
     *
     * Read off the corpus rather than chosen. A greedy wrap of every paragraph
     * and a count of the ones that came out unchanged puts the minimum at 80.
     * That is by a margin over every column on either side of it. That is the
     * column whoever wrapped these files by hand aimed at.
     */
    public const COLUMN = 80;

    /** What stands in for a space that may not be broken at. */
    private const KEPT = "\x00";

    /**
     * What opens a list item: a bullet or a number, and text for it to carry.
     *
     * A full stop or a bracket closes the number, both of which markdown reads
     * as a marker. It runs to nine digits, which is where markdown stops to
     * read one. Captured are the indent, the marker and the gap after it,
     * because together they are what the item's first line opens with.
     */
    private const MARKER = '/^(\s*)([-*+]|\d{1,9}[.)])([ \t]+)(?=\S)/';

    /** Spans a line break would break: a code span, and a markdown link. */
    private const UNBREAKABLE = '/`[^`\n]*`|\[[^\]\n]*\]\([^)\n]*\)/';

    /**
     * The same in reStructuredText: a literal, a role, an embedded link.
     *
     * The literal comes first because it has two backticks and the role one.
     * Read the other way round every literal looks like an empty role followed
     * by loose words.
     */
    private const RST_UNBREAKABLE = '/``[^`]*``|:[a-z:]+:`[^`\n]*`|`[^`\n]*`__?/';

    /**
     * The rule a simple table consists of, above its head and at its foot.
     *
     * Two runs at least, because one run of `=` under a line of text is a
     * heading's underline. Read as the start of a table, that swallowed every
     * line to the next heading and the file came out unwrapped rather than
     * wrong. That is the failure that does not announce itself.
     */
    private const RST_RULE = '/^\s*={2,}(\s+={2,})+\s*$/';

    /**
     * What underlines a heading. Any of them, not only the four this repository
     * writes, because a file it did not write is still not to be broken.
     */
    private const RST_UNDERLINE = '/^([=\-~"\'#*+^`_:.]){2,}\s*$/';

    /**
     * A markdown document, rewrapped.
     *
     * What stays as it is is everything a line break means something in. The
     * front matter, a fenced or indented code block, a table row, a heading, a
     * quote, a link definition. The rest is a paragraph, and a list item starts
     * one of its own so the marker stays on the line the item begins with.
     */
    public static function document(string $markdown): string
    {
        $lines = explode("\n", $markdown);
        $out = [];
        $paragraph = [];
        $open = null;
        /** @var array<int, string> the marker of each list still open, by the indent it stands at */
        $lists = [];

        $fence = null;
        $verbatim = false;
        $blank = true;
        /** @var list<string> the rows of the table being read, none of which is wrapped */
        $table = [];

        // The front matter is data in a markdown file's clothes: its lines are
        // neither prose nor a code block, and a join of two of them would lose
        // a key rather than reflow a sentence.
        $matter = $lines[0] === '---';

        foreach ($lines as $index => $line) {
            if ($matter) {
                $out[] = $line;
                $matter = $index === 0 || $line !== '---';

                continue;
            }

            if ($fence !== null) {
                $out[] = $line;
                if (preg_match('/^\s{0,3}' . preg_quote($fence, '/') . '/', $line) === 1) {
                    $fence = null;
                }

                continue;
            }

            // A table gets its padding rather than stays as it stands. Both
            // forms render the same, and the compact one meets many readers in
            // the state its writer left it in, where an unaligned row is a run
            // of words with pipes in it and a reader has to count out which
            // cell a value belongs to (`D-DOC-001`).
            $row = str_starts_with(trim($line), '|');
            if ($table !== [] && !$row) {
                $out = [...$out, ...self::padded($table)];
                $table = [];
            }
            if ($row) {
                $out = [...$out, ...self::flush($paragraph, $open !== null)];
                $paragraph = [];
                $table[] = $line;
                $blank = false;

                continue;
            }

            if (preg_match('/^\s{0,3}(```+|~~~+)/', $line, $match) === 1) {
                $out = [...$out, ...self::flush($paragraph, $open !== null)];
                $paragraph = [];
                $fence = $match[1];
                $out[] = $line;
                $blank = false;

                continue;
            }

            $marker = self::marker($line);
            $item = $marker !== null && ($paragraph === [] || self::interrupts($marker, $lists));

            // An indented code block opens after a blank line and nowhere else.
            // Inside a list, four spaces is the continuation of the item above.
            if ($verbatim && !$item && preg_match('/^(?: {4}|\t)/', $line) === 1) {
                $out[] = $line;

                continue;
            }
            $verbatim = false;

            if (self::isVerbatim($line) || ($blank && !$item && preg_match('/^(?: {4}|\t)/', $line) === 1)) {
                $out = [...$out, ...self::flush($paragraph, $open !== null)];
                $paragraph = [];
                $out[] = $line;
                $verbatim = preg_match('/^(?: {4}|\t)/', $line) === 1;
                $blank = trim($line) === '';

                continue;
            }

            // A field is one line, or one line and what hangs under it. Joined
            // to the field below it, a scenario's `**Environment:**` and
            // `**Contract:**` become one line that reads as neither.
            $field = preg_match('/^\*\*[^*]+:\*\*/', trim($line)) === 1;
            $under = $paragraph !== []
                && preg_match('/^\*\*[^*]+:\*\*/', trim($paragraph[0])) === 1
                && self::indent($line) <= self::indent($paragraph[0]);

            if ($item || $field || $under) {
                $out = [...$out, ...self::flush($paragraph, $open !== null)];
                $paragraph = [];
            }

            // What the open paragraph is: the marker it began with, or nothing
            // where it began with prose. `flush()` is told rather than asked,
            // because the same line reads as a marker or not according to what
            // stands above it.
            if ($paragraph === []) {
                $open = $item ? $marker : null;
            }
            // An item opens a list at its own indent and closes every list
            // deeper than it; a paragraph that is nobody's item closes the ones
            // at its indent too. What remains is the lists this line stands
            // inside, which is what the next marker reads against.
            if ($item || $paragraph === []) {
                $indent = self::indent($line);
                $lists = array_filter(
                    $lists,
                    static fn(int $at): bool => $at < $indent,
                    ARRAY_FILTER_USE_KEY,
                );
                if ($item) {
                    $lists[$indent] = $marker[1];
                }
            }
            $paragraph[] = $line;
            $blank = false;
        }

        return implode("\n", [...$out, ...self::padded($table), ...self::flush($paragraph, $open !== null)]);
    }

    /**
     * One markdown table, padded to the width of each column's widest cell.
     *
     * A row is only a row of a table where a separator stands under the head.
     * So anything else that opens with a pipe comes back as it was. A line of a
     * drawn diagram, a quotation of a table, a row somebody has half written.
     *
     * @param list<string> $rows
     * @return ($rows is non-empty-list<string> ? non-empty-list<string> : list<string>)
     */
    public static function padded(array $rows): array
    {
        if (count($rows) < 2 || preg_match('/^\s*\|[\s:|-]+\|\s*$/', $rows[1]) !== 1) {
            return $rows;
        }

        $cells = array_map(self::cells(...), $rows);
        $columns = max(array_map('count', $cells));
        $written = $cells;
        unset($written[1]);

        $widths = [];
        foreach (range(0, $columns - 1) as $column) {
            // Three at the least, so the separator row is still a rule where a
            // column holds one letter.
            $widths[$column] = max(3, ...array_map(
                static fn(array $row): int => mb_strlen($row[$column] ?? ''),
                $written,
            ));
        }

        $indent = str_repeat(' ', self::indent($rows[0]));
        $padded = [];
        foreach ($cells as $index => $row) {
            $line = [];
            foreach ($widths as $column => $width) {
                $cell = $row[$column] ?? '';
                $line[] = $index === 1 ? str_repeat('-', $width) : $cell . str_repeat(' ', $width - mb_strlen($cell));
            }
            $padded[] = $indent . '| ' . implode(' | ', $line) . ' |';
        }

        return $padded;
    }

    /**
     * What one row holds, by the pipes that are not part of a cell.
     *
     * @return non-empty-list<string>
     */
    public static function cells(string $row): array
    {
        return array_map(trim(...), preg_split('/(?<!\\\\)\|/', trim(trim($row), '|')) ?: ['']);
    }

    /**
     * A reStructuredText document, rewrapped.
     *
     * The markup is different enough for a reader of its own rather than
     * markdown with exceptions. A line break or an indent carries almost
     * everything structural here. A heading is a line and the rule under it, a
     * directive owns whatever stands indented below it, a table consists of
     * drawn rules. So what may rewrap is a smaller set than in markdown rather
     * than a larger one, and this errs towards a line left alone.
     */
    public static function rst(string $rst): string
    {
        $lines = explode("\n", $rst);
        $out = [];
        $paragraph = [];
        $open = null;
        $lists = [];

        // What a block owns: everything indented past it, until something
        // stands at or left of the column it opened at.
        $owned = null;
        $table = -1;

        foreach ($lines as $index => $line) {
            $trimmed = trim($line);
            $indent = self::indent($line);

            if ($owned !== null) {
                if ($trimmed === '' || $indent > $owned) {
                    $out[] = $line;

                    continue;
                }
                $owned = null;
            }

            // A drawn table is verbatim, rules and rows alike: every column
            // boundary in it is a character position, so one rewrapped row
            // moves the text out of the column it belongs to and the parser
            // reports content that stands in a gap. Where it ends comes from a
            // search rather than a toggle. A simple table has three rules and
            // not two, above the head, under it, and at the foot. So a state
            // each one flips is inside the table exactly when it is not.
            if ($index > $table && preg_match(self::RST_RULE, $line) === 1) {
                $out = [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)];
                $paragraph = [];
                $table = self::tableEnds($lines, $index);
                for ($row = $index; $row <= $table; $row++) {
                    $out[] = $lines[$row];
                }

                continue;
            }
            if ($index <= $table) {
                continue;
            }

            // A directive, a comment, a label: the line itself and everything
            // indented under it.
            if (preg_match('/^\s*\.\.(\s|$)/', $line) === 1) {
                $out = [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)];
                $paragraph = [];
                $out[] = $line;
                $owned = $indent;

                continue;
            }

            // A heading is the text and the rule under it, and a rewrap of the
            // text would leave the rule the wrong length.
            if (isset($lines[$index + 1]) && $trimmed !== ''
                && preg_match(self::RST_UNDERLINE, $lines[$index + 1]) === 1
                && mb_strlen(trim($lines[$index + 1])) >= mb_strlen($trimmed)
            ) {
                $out = [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)];
                $paragraph = [];
                $out[] = $line;

                continue;
            }
            // A rule the branch above did not claim, which is a heading whose
            // underline is shorter than its text. It is still a heading and
            // still nobody's paragraph: written out without a flush first, it
            // would land above the line it underlines.
            if (preg_match(self::RST_UNDERLINE, $line) === 1) {
                $out = [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)];
                $paragraph = [];
                $out[] = $line;

                continue;
            }

            // A literal block is what an indented run after `::` is, and a
            // field list is one line each.
            if ($trimmed === '' || preg_match('/^\s*:[^:\s][^:]*:(\s|$)/', $line) === 1) {
                $out = [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)];
                $paragraph = [];
                $out[] = $line;
                if ($trimmed !== '') {
                    $owned = $indent;
                }

                continue;
            }
            if ($paragraph === [] && str_ends_with(rtrim((string) end($out)), '::')) {
                $out[] = $line;
                $owned = $indent - 1;

                continue;
            }

            $marker = self::marker($line);
            $item = $marker !== null && ($paragraph === [] || self::interrupts($marker, $lists));
            if ($item) {
                $out = [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)];
                $paragraph = [];
            }
            if ($paragraph === []) {
                $open = $item ? $marker : null;
            }
            if ($item || $paragraph === []) {
                $lists = array_filter($lists, static fn(int $at): bool => $at < $indent, ARRAY_FILTER_USE_KEY);
                if ($item) {
                    $lists[$indent] = $marker[1];
                }
            }
            $paragraph[] = $line;
        }

        return implode("\n", [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)]);
    }

    /**
     * Where the table that opens at $from runs to.
     *
     * The foot is the rule with a blank line or the end of the file after it.
     * That tells it from the rule under the head, which always carries a row.
     *
     * @param list<string> $lines
     */
    private static function tableEnds(array $lines, int $from): int
    {
        for ($at = $from + 1; $at < count($lines); $at++) {
            if (preg_match(self::RST_RULE, $lines[$at]) !== 1) {
                continue;
            }
            if (!isset($lines[$at + 1]) || trim($lines[$at + 1]) === '') {
                return $at;
            }
        }

        // A table nothing closes stays as it stands rather than rewraps.
        return count($lines) - 1;
    }

    /**
     * What a line opens a list item with, where it opens one at all: its indent
     * and its marker.
     *
     * @return array{string, string}|null
     */
    private static function marker(string $line): ?array
    {
        if (preg_match(self::MARKER, $line, $match) !== 1) {
            return null;
        }

        return [$match[1], $match[2]];
    }

    /**
     * Whether an item may open where a paragraph already runs.
     *
     * Markdown lets a bullet and a `1.` interrupt a paragraph and no other
     * number. That would otherwise open an item wherever a wrapped line happens
     * to begin with a figure, `D-KNW-049` has one that starts `5432.`. The
     * exception is a list that already runs, and it reads against any of the
     * ones still open rather than the paragraph directly above. A step after a
     * nested bullet is the outer list's next item. Read against the bullet
     * alone it is a figure at the head of a line.
     *
     * @param array{string, string}  $marker
     * @param array<int, string>     $lists
     */
    private static function interrupts(array $marker, array $lists): bool
    {
        if (!ctype_digit($marker[1][0]) || in_array($marker[1], ['1.', '1)'], true)) {
            return true;
        }

        $open = $lists[mb_strlen($marker[0])] ?? null;

        return $open !== null
            && ctype_digit($open[0])
            && substr($open, -1) === substr($marker[1], -1);
    }

    /** How far a line stands in. */
    private static function indent(string $line): int
    {
        return mb_strlen($line) - mb_strlen(ltrim($line));
    }

    /**
     * A line no wrap may join to another.
     *
     * The blank line ends a paragraph, and the rest carry their meaning as
     * lines of their own. A heading, a table row, a quote, the `---` of a front
     * matter or a rule. The link definitions a generated listing ends with, and
     * a line of HTML.
     */
    private static function isVerbatim(string $line): bool
    {
        $trimmed = trim($line);

        return $trimmed === ''
            // One code span or one link and nothing else is an entry rather
            // than a sentence — what a contract case lists under **Held by:**,
            // one test per line. Packed two to a line they stop as a list.
            || preg_match('/^(?:`[^`]+`|\[[^\]]+\]\([^)]+\))[.,;:]?$/', $trimmed) === 1
            || str_starts_with($trimmed, '#')
            || str_starts_with($trimmed, '|')
            || str_starts_with($trimmed, '>')
            || str_starts_with($trimmed, '<')
            || preg_match('/^(-{3,}|\*{3,}|_{3,})$/', $trimmed) === 1
            || preg_match('/^\[[^\]]+\]:\s/', $trimmed) === 1;
    }

    /**
     * One paragraph, wrapped: the first line keeps what it opens with, and
     * every line after it lines up under the first word.
     *
     * Whether it is a list item is what `document()` decided rather than what
     * the first line looks like from here. The same line reads as one or not
     * according to what stood above it.
     *
     * @param list<string> $paragraph
     *
     * @return list<string>
     */
    private static function flush(array $paragraph, bool $item, string $unbreakable = self::UNBREAKABLE): array
    {
        if ($paragraph === []) {
            return [];
        }

        // A field on one line stays on one line, however wide. A label and the
        // name after it are one field, and split over two lines the label is all
        // a reader of it finds — a long name is a long line, and that is the
        // cheaper of the two.
        if (!isset($paragraph[1]) && preg_match('/^\*\*[^*]+:\*\*/', trim($paragraph[0])) === 1) {
            return [$paragraph[0]];
        }

        $first = preg_match('/^(\s*)/', $paragraph[0], $match) === 1 ? $match[1] : '';
        if ($item && preg_match(self::MARKER, $paragraph[0], $match) === 1) {
            // The first prefix carries the marker, so the text it belongs to
            // starts after it.
            $first = $match[1] . $match[2] . $match[3];
            $paragraph[0] = mb_substr($paragraph[0], mb_strlen($first));
        }
        $continuation = str_repeat(' ', mb_strlen($first));

        // A hanging indent is the block's own, not an accident of the wrap: it
        // is what makes a scenario's `**Held by:**` one field over several
        // lines rather than a field and a paragraph. Where the second line sits
        // further in than the first, that is where the rest of them go.
        if (isset($paragraph[1]) && self::indent($paragraph[1]) > mb_strlen($continuation)) {
            $continuation = str_repeat(' ', self::indent($paragraph[1]));
        }

        $text = implode(' ', array_map(
            static fn(string $line): string => trim($line),
            $paragraph,
        ));

        return self::lines($text, $first, $continuation, $unbreakable);
    }

    /**
     * One run of text, wrapped, for whatever writes markdown without a document
     * to hand, which is every generator here.
     *
     * It is the same wrap the corpus holds to, and that is the point of its
     * reach. A generator with a column of its own writes a file `prose:format`
     * then disagrees with, and the two rewrite each other.
     */
    public static function text(string $text, string $continuation = ''): string
    {
        return implode("\n", self::lines($text, '', $continuation));
    }

    /** A run of text whose first and following lines stand at the same indent. */
    public static function indented(string $text, string $indent): string
    {
        return implode("\n", self::lines($text, $indent, $indent));
    }

    /**
     * A greedy wrap, with the spans that may not break kept whole.
     *
     * A word longer than what remains of the column goes on the next line. One
     * longer than the column itself goes on a line of its own rather than in
     * two parts. A broken URL is worse than a long line.
     *
     * @return list<string>
     */
    private static function lines(string $text, string $first, string $continuation, string $unbreakable = self::UNBREAKABLE): array
    {
        $masked = (string) preg_replace_callback(
            $unbreakable,
            static fn(array $match): string => str_replace(' ', self::KEPT, $match[0]),
            $text,
        );

        $lines = [];
        $current = $first;
        $empty = true;
        foreach (preg_split('/\s+/', trim($masked)) ?: [] as $word) {
            if ($word === '') {
                continue;
            }
            if (!$empty && mb_strlen($current) + 1 + mb_strlen($word) > self::COLUMN) {
                $lines[] = $current;
                $current = $continuation;
                $empty = true;
            }
            $current .= $empty ? $word : ' ' . $word;
            $empty = false;
        }
        if (!$empty) {
            $lines[] = $current;
        }

        return array_map(static fn(string $line): string => str_replace(self::KEPT, ' ', $line), $lines);
    }
}
