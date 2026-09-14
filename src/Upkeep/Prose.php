<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Knowledge\Coverage;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * The prose this repository writes about itself, measured.
 *
 * AGENTS.md writes in ASD-STE100 and asks for one point per sentence. A test or
 * a check holds every other rule that file states; a reader who rereads the
 * paragraph held this one. A lead sentence of 96 words is what that cost, and
 * that is where this class started.
 *
 * This class reports and does not judge. A sentence over the measure can be
 * the right sentence, and a rewrite that satisfies a counter produces two
 * short sentences that say what one said. `bin/cli prose:check` fails on one
 * thing: the bold opening of a requirement or a decision, because a reader who
 * stops after it must know what was settled, and nobody stops after 96 words.
 */
final class Prose
{
    /**
     * The STE measure for a descriptive sentence — `D-DOC-070`.
     */
    public const MEASURE = 25;

    /**
     * The STE measure for a sentence in a procedure. `skills/` and
     * `knowledge/documents/` are the procedures a caller follows — `D-DOC-070`.
     */
    public const PROCEDURE = 20;

    /**
     * Where a lead still fails. 402 of 924 leads run past `MEASURE` on the day
     * `D-DOC-070` set it, so the check fails at the old number until the sweep
     * of the records lands and its todo moves this to `MEASURE`.
     */
    public const HELD = 30;

    /**
     * A passive form, as an upper bound. "is read" can be the passive or the
     * adjective, and the count says so where it prints — `D-DOC-070`.
     */
    private const PASSIVE = '/\b(?:is|are|was|were|be|been|being)\s+(?:\w+ed|held|written|read|kept|taken|given|done|made|said|known|seen|left|put|set|run|built|found|shown|sent|met|split|cut|bound|thrown|begun|drawn|torn|worn)\b/i';

    /**
     * An -ing form of a verb, as an upper bound. The nouns that end the same
     * way are named out, and a capitalised word is a name STE permits.
     */
    private const ING = '/\b(?!(?:nothing|something|anything|everything|thing|things|during|string|strings|bring|ring|sing|king|wing|sibling|siblings|ceiling|meaning|morning|evening|building|heading|headings|setting|settings|listing|listings|mapping|mappings|according|regarding|following)\b)[a-z]+ing\b/';

    /**
     * Where a comment has stopped naming its reason and started retelling it.
     *
     * AGENTS.md asks a comment that rests on a decision to name its id instead
     * of repeating what it settled, and ten lines of prose is more than saying
     * which claim this line carries. Ten lines of a comment is not, which is
     * what this used to count — `D-DOC-035`.
     */
    public const RETOLD = 10;

    /**
     * Where a title stops being a name and starts being the statement.
     *
     * Twelve rather than the thirty a lead is held to, because a title is read
     * in a list beside four hundred others and a lead is read once, at the top
     * of the file it belongs to. The requirement corpus is what says twelve is
     * writable: its titles mean nine words and fourteen of 222 run past it.
     */
    public const TITLE_WORDS = 12;

    /**
     * How many words a name carries before a reader has to take it apart. The
     * corpus reads at ten, and what is reported is what runs past that by two.
     */
    public const NAME_WORDS = 12;

    /**
     * What puts two claims in one name. A name states what has to hold; the
     * case it is being told apart from is the docblock's — `D-DOC-051`.
     *
     * @var array<int, string>
     */
    private const JOINED = ['RatherThan', 'AndNot', 'ButNot', 'NotOnly', 'AsWellAs'];

    /**
     * The files this repository writes about itself.
     *
     * `feedback/` is deliberately absent. A feedback is a session's report
     * written somewhere else, and measuring somebody else's prose against this
     * repository's rule would be a report about the wrong thing.
     *
     * @return list<string>
     */
    public static function documents(): array
    {
        $files = array_filter(
            ['AGENTS.md', 'readme.md'],
            static fn(string $file): bool => is_file(Paths::root() . '/' . $file),
        );

        $directories = array_values(array_filter(
            array_map(
                static fn(string $directory): string => Paths::root() . '/' . $directory,
                ['decisions', 'requirements', 'todo', 'documentation', 'scenarios', 'skills', 'knowledge/documents'],
            ),
            is_dir(...),
        ));
        if ($directories !== []) {
            // Two markups, because `documentation/` is reStructuredText and the
            // working directories around it are markdown — `D-DOC-029`. What
            // reads this corpus asks the file which it is.
            foreach (Finder::create()->files()->in($directories)->name('/\.(md|rst)$/') as $file) {
                $files[] = substr($file->getPathname(), strlen(Paths::root()) + 1);
            }
        }

        sort($files);

        return $files;
    }

    /** The measure a file is read against: the procedure's or the description's. */
    public static function measureOf(string $file): int
    {
        return str_starts_with($file, 'skills/') || str_starts_with($file, 'knowledge/documents/')
            ? self::PROCEDURE
            : self::MEASURE;
    }

    /**
     * What one file measures: its sentences, the ones over its measure, and
     * how many carry a passive or an -ing form.
     *
     * @return array{file: string, measure: int, sentences: int, over: list<array{words: int, text: string}>, passive: int, ing: int}
     */
    public static function measure(string $file): array
    {
        $over = [];
        $passive = 0;
        $ing = 0;
        $measure = self::measureOf($file);
        $sentences = self::sentences((string) file_get_contents(Paths::root() . '/' . $file));
        foreach ($sentences as $sentence) {
            $words = count(explode(' ', $sentence));
            if ($words > $measure) {
                $over[] = ['words' => $words, 'text' => $sentence];
            }
            $passive += preg_match(self::PASSIVE, $sentence);
            $ing += preg_match(self::ING, $sentence);
        }

        usort($over, static fn(array $a, array $b): int => $b['words'] <=> $a['words']);

        return ['file' => $file, 'measure' => $measure, 'sentences' => count($sentences), 'over' => $over, 'passive' => $passive, 'ing' => $ing];
    }

    /**
     * A title that carries more than one thing, worst first.
     *
     * A title is the name an entry is read by in a listing of hundreds, and
     * what it is not is the statement — that has a measure of its own and a
     * check that fails on it, which is why the title was the half that grew.
     * Measured on 2026-08-23: a decision's title reuses 46% of its own words
     * from its statement, and 89 of the 446 join two claims with a comma-and, a
     * dash or a semicolon.
     *
     * Reported and never failed on. 227 entries ran past twelve words that day,
     * and a rule 227 entries break is a rewrite wearing a rule's clothes —
     * `D-DOC-002` took the same road for the sentence count.
     *
     * @return list<array{id: string, words: int, joined: bool, title: string}>
     */
    public static function titles(): array
    {
        $titles = [];
        foreach ([...array_values(Decisions::all()), ...array_values(Requirements::all())] as $entry) {
            $joined = preg_match('/, and |, which | — |; /u', $entry['title']) === 1;
            $words = str_word_count($entry['title']);
            if (!$joined && $words <= self::TITLE_WORDS) {
                continue;
            }

            $titles[] = [
                'id' => $entry['id'],
                'words' => $words,
                'joined' => $joined,
                'title' => $entry['title'],
            ];
        }

        usort($titles, static fn(array $a, array $b): int => [$b['joined'], $b['words']] <=> [$a['joined'], $a['words']]);

        return $titles;
    }

    /**
     * The test names a reader has to take apart, the two-claim ones first.
     *
     * A name is read in a failure list, where it is all there is: the file and
     * the line say where, and the name says what was supposed to hold. One that
     * joins two claims with `RatherThan` or `AndNot` states a case and its
     * counter-case at once, and the second half is what the docblock is for.
     *
     * Reported and never failed on, like the titles above: a long name can be
     * the honest one, and what the count is for is a corpus drifting back.
     *
     * @return list<array{name: string, file: string, words: int, joined: string}>
     */
    public static function names(): array
    {
        $names = [];
        foreach (Finder::create()->files()->in(Paths::root() . '/tests')->name('*.php')->sortByName() as $file) {
            preg_match_all('/\n    (?:public|private|protected) function (\w+)\(/', (string) file_get_contents($file->getPathname()), $found);
            foreach ($found[1] as $name) {
                $joined = '';
                foreach (self::JOINED as $join) {
                    if (str_contains($name, $join)) {
                        $joined = $join;
                        break;
                    }
                }
                preg_match_all('/[A-Z][a-z0-9]*|^[a-z0-9]+/', $name, $words);
                if ($joined === '' && count($words[0]) <= self::NAME_WORDS) {
                    continue;
                }
                $names[] = [
                    'name' => $name,
                    'file' => $file->getFilename(),
                    'words' => count($words[0]),
                    'joined' => $joined,
                ];
            }
        }

        usort($names, static fn(array $a, array $b): int => [$b['joined'] !== '', $b['words']] <=> [$a['joined'] !== '', $a['words']]);

        return $names;
    }

    /**
     * Every markdown table this repository writes, widest cell first.
     *
     * The cell is what `D-DOC-001` measures — one that will not fit on a line
     * means the content is a list rather than a table, because what a table
     * buys over a list is a column that can be scanned and a cell nobody can
     * read on one line takes exactly that away. The row width is what the
     * table takes once `bin/cli prose:format` has padded it.
     *
     * Reported and never failed on: whether a cell can be shortened is a
     * judgement, and the exception has to say so where it is taken.
     *
     * Markdown alone. A table in reStructuredText is drawn rather than piped,
     * and the two lines in `documentation/` that open with a pipe are a shell
     * continuation and a line block.
     *
     * @return list<array{file: string, line: int, rows: int, width: int, cell: int}>
     */
    public static function tables(): array
    {
        $tables = [];
        foreach (self::documents() as $file) {
            if (!str_ends_with($file, '.md')) {
                continue;
            }

            $lines = explode("\n", (string) file_get_contents(Paths::root() . '/' . $file));
            $fence = null;
            $at = null;
            $rows = [];
            foreach ([...$lines, ''] as $index => $line) {
                if ($fence !== null) {
                    if (preg_match('/^\s{0,3}' . preg_quote($fence, '/') . '/', $line) === 1) {
                        $fence = null;
                    }

                    continue;
                }
                if (preg_match('/^\s{0,3}(```+|~~~+)/', $line, $match) === 1) {
                    $fence = $match[1];

                    continue;
                }
                if (str_starts_with(trim($line), '|')) {
                    $at ??= $index + 1;
                    $rows[] = $line;

                    continue;
                }
                if ($rows !== [] && count($rows) > 1 && preg_match('/^\s*\|[\s:|-]+\|\s*$/', $rows[1]) === 1) {
                    $cells = array_merge(...array_map(Wrap::cells(...), $rows));
                    $tables[] = [
                        'file' => $file,
                        'line' => (int) $at,
                        'rows' => count($rows) - 1,
                        'width' => max(array_map(mb_strlen(...), Wrap::padded($rows))),
                        'cell' => max(array_map(mb_strlen(...), $cells)),
                    ];
                }
                $at = null;
                $rows = [];
            }
        }

        usort($tables, static fn(array $a, array $b): int => $b['cell'] <=> $a['cell']);

        return $tables;
    }

    /**
     * The bold opening of every requirement and decision, split into sentences.
     *
     * Two sentences there are legitimate — a decision that removes something
     * and says what replaced it is two points and reads as two. The check holds
     * each of them, not their sum, and holds them at `HELD` rather than at
     * `MEASURE` until the records sweep lands.
     *
     * @return list<array{id: string, words: int, text: string}>
     */
    public static function leadsOverTheMeasure(): array
    {
        $over = [];
        $entries = [...array_values(Requirements::all()), ...array_values(Decisions::all())];
        foreach ($entries as $entry) {
            foreach (self::sentences($entry['statement']) as $sentence) {
                $words = count(explode(' ', $sentence));
                if ($words > self::HELD) {
                    $over[] = ['id' => $entry['id'], 'words' => $words, 'text' => $sentence];
                }
            }
        }

        return $over;
    }

    /**
     * The prose a client is handed before it has asked anything.
     *
     * `documents()` reaches no file in `src/`, so this is the half a caller pays
     * for and nobody counted — `R-COD-002`, `D-DOC-002`. It is not a budget,
     * which `R-ANS-013` is, and what it counts is the prose alone rather than
     * the schemas around it.
     *
     * @return list<array{where: string, text: string}>
     */
    public static function payload(): array
    {
        $prose = [['where' => 'instructions', 'text' => Coverage::instructions()]];
        foreach (Registry::definitions() as $definition) {
            $name = (string) $definition['name'];
            $prose[] = ['where' => $name . ' description', 'text' => (string) $definition['description']];
            foreach ([['inputSchema', 'input'], ['outputSchema', 'output']] as [$key, $side]) {
                foreach (self::descriptions(is_array($definition[$key] ?? null) ? $definition[$key] : []) as $field => $text) {
                    $prose[] = ['where' => $name . ' ' . $side . ' ' . $field, 'text' => $text];
                }
            }
        }

        return $prose;
    }

    /**
     * Every `description` in a JSON Schema, by the path that carries it.
     *
     * A schema nests — properties inside items inside properties — and a field
     * three levels down is read by the same client as the one at the top, so
     * the walk is the whole tree rather than one level of it.
     *
     * @param array<string, mixed> $schema
     * @return array<string, string>
     */
    private static function descriptions(array $schema, string $path = ''): array
    {
        $found = [];
        foreach ($schema as $key => $value) {
            if ($key === 'description' && is_string($value)) {
                $found[$path === '' ? '(self)' : $path] = $value;
                continue;
            }
            if (!is_array($value)) {
                continue;
            }
            // `properties` and `items` are the schema's own structure and not
            // part of what a field is called, so they do not go into the path.
            $step = in_array($key, ['properties', 'items', 'oneOf', 'anyOf', 'allOf'], true)
                ? $path
                : trim($path . '.' . $key, '.');
            $found = [...$found, ...self::descriptions($value, $step)];
        }

        return $found;
    }

    /**
     * The payload's sentences that run past the measure, worst first.
     *
     * @return list<array{where: string, words: int, text: string}>
     */
    public static function payloadOverTheMeasure(): array
    {
        $over = [];
        foreach (self::payload() as $entry) {
            foreach (self::sentences($entry['text']) as $sentence) {
                $words = count(explode(' ', $sentence));
                if ($words > self::MEASURE) {
                    $over[] = ['where' => $entry['where'], 'words' => $words, 'text' => $sentence];
                }
            }
        }

        usort($over, static fn(array $a, array $b): int => $b['words'] <=> $a['words']);

        return $over;
    }

    /** What a client is handed at connect, in characters. */
    public static function payloadWeight(): int
    {
        return array_sum(array_map(
            static fn(array $entry): int => strlen($entry['text']),
            self::payload(),
        ));
    }

    /**
     * The PHP this repository writes, which is where the comment rule applies.
     *
     * Both binaries are left out. They locate an autoloader and hand their
     * arguments to a class, and neither carries the reasoning this counts.
     *
     * @return list<string>
     */
    public static function code(): array
    {
        $directories = array_values(array_filter(
            array_map(static fn(string $directory): string => Paths::root() . '/' . $directory, ['src', 'tests']),
            is_dir(...),
        ));
        if ($directories === []) {
            return [];
        }

        $files = [];
        foreach (Finder::create()->files()->in($directories)->name('*.php') as $file) {
            $files[] = substr($file->getPathname(), strlen(Paths::root()) + 1);
        }

        sort($files);

        return $files;
    }

    /**
     * Every comment in that corpus, with the entries it names.
     *
     * The lexer rather than a pattern, because a `//` inside a string literal
     * is not a comment and this repository writes regular expressions.
     *
     * @return list<array{file: string, line: int, lines: int, prose: int, names: list<string>}>
     */
    public static function comments(): array
    {
        $comments = [];
        foreach (self::code() as $file) {
            $tokens = token_get_all((string) file_get_contents(Paths::root() . '/' . $file));
            foreach ($tokens as $token) {
                if (!is_array($token) || !in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
                    continue;
                }

                preg_match_all('/\b[DR]-[A-Z]{3}-\d{3}\b/', $token[1], $named);
                $comments[] = [
                    'file' => $file,
                    'line' => $token[2],
                    'lines' => substr_count($token[1], "\n") + 1,
                    'prose' => self::proseLines($token[1]),
                    'names' => array_values(array_unique($named[0])),
                ];
            }
        }

        return $comments;
    }

    /**
     * The lines of a comment somebody wrote, without the ones the markup costs.
     *
     * A docblock spends its two delimiters, the blank line under its summary
     * and every annotation on being a docblock, so an annotated one had seven
     * of ten lines gone before a sentence was written — the floor the count
     * could not see past, and what `D-DOC-035` replaced. An annotation runs to
     * the next blank line, because a `@return` naming an array shape wraps.
     */
    public static function proseLines(string $comment): int
    {
        $lines = 0;
        $annotation = false;
        foreach (preg_split('/\R/', $comment) ?: [] as $line) {
            $line = trim((string) preg_replace('#^(/\*\*?|//|\*/|\*)#', '', trim($line)));
            $line = trim((string) preg_replace('#\*/$#', '', $line));
            if ($line === '') {
                $annotation = false;
                continue;
            }
            if ($annotation || str_starts_with($line, '@')) {
                $annotation = true;
                continue;
            }
            $lines++;
        }

        return $lines;
    }

    /**
     * What the comments cost, against the lines of code they stand in.
     *
     * A share rather than a count, because the corpus grows and a count of
     * something that grows is true on the day it is written. What it is for is
     * the direction: nothing counted this until 2026-08-18, when 37% of the
     * non-blank lines below `src/` were comment.
     *
     * @return array{comment: int, lines: int}
     */
    public static function commentWeight(): array
    {
        $lines = 0;
        foreach (self::code() as $file) {
            foreach (file(Paths::root() . '/' . $file) ?: [] as $line) {
                if (trim($line) !== '') {
                    $lines++;
                }
            }
        }

        return [
            'comment' => array_sum(array_column(self::comments(), 'lines')),
            'lines' => $lines,
        ];
    }

    /**
     * The comments that name an entry and retell it anyway, longest first.
     *
     * Reported and not failed on. A long comment naming an entry can be the
     * right comment — it may rest on that decision while explaining something
     * else — and only the reader of the block can tell the two apart.
     *
     * @return list<array{file: string, line: int, lines: int, prose: int, names: list<string>}>
     */
    public static function retellings(): array
    {
        $retold = array_values(array_filter(
            self::comments(),
            static fn(array $comment): bool => $comment['names'] !== [] && $comment['prose'] > self::RETOLD,
        ));

        usort($retold, static fn(array $a, array $b): int => $b['prose'] <=> $a['prose']);

        return $retold;
    }

    /**
     * The sentences of a markdown file, as a reader meets them.
     *
     * A list item is a sentence of its own even where it has no full stop, and
     * a paragraph's line breaks are the wrapping rather than the writing, so
     * they are joined before anything is split. What is skipped is everything
     * that is not prose: front matter, code, tables, headings, quoted material
     * and the link definitions at the foot of a generated listing.
     *
     * @return list<string>
     */
    private static function sentences(string $markdown): array
    {
        $markdown = (string) preg_replace('/^---\R.*?\R---\R/s', '', $markdown);
        $markdown = (string) preg_replace('/^ {0,3}```.*?^ {0,3}```/ms', '', $markdown);

        $sentences = [];
        foreach (preg_split('/\R\s*\R/', $markdown) ?: [] as $paragraph) {
            // Four spaces is a code block, and a paragraph is either all of one
            // or none of it.
            if (preg_match('/^ {4}/', $paragraph) === 1) {
                continue;
            }
            foreach (preg_split('/\R(?=\s*(?:[-*]\s|\d+\.\s))/', $paragraph) ?: [] as $block) {
                $line = trim((string) preg_replace('/\s+/', ' ', $block));
                $line = trim((string) preg_replace('/^[-*]\s|^\d+\.\s/', '', $line));
                // A heading, a table row, quoted material, and the link
                // definitions a generated listing ends with.
                if ($line === '' || in_array($line[0], ['#', '|', '>'], true) || preg_match('/^\[[^\]]+\]:\s/', $line) === 1) {
                    continue;
                }
                // A bold sentence ends inside its markers, so `read.** A` is
                // two sentences and not one of 33 words.
                foreach (preg_split('/(?<=[.!?]|[.!?]\*\*)\s+/', $line) ?: [] as $sentence) {
                    $sentence = trim($sentence);
                    // Four words is a heading in disguise, a label line, or the
                    // remains of one that was split on an abbreviation.
                    if (count(explode(' ', $sentence)) > 4) {
                        $sentences[] = $sentence;
                    }
                }
            }
        }

        return $sentences;
    }
}
