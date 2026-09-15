<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Tool\Source;

/**
 * The tool surface: one page per tool, an index that reaches them, and the
 * server-level page defining their answer sources.
 *
 * A surface written out a second time by hand stops describing the answer at
 * the first change nobody carried across, so this renders it from
 * Registry::definitions() and `tools:check` fails where a page has gone stale.
 *
 * The rest of a page is `ToolAnswers`', under the page's `## Answered` heading,
 * and it is one of two things. What a filled answer looks like usually needs an
 * installation to call, so it is recorded and checked by nothing, and each of
 * the two commands carries the other's half over untouched — which is what lets
 * one file hold both. Where a tool's answers read nothing an installation
 * contains, that half is derived here instead and falls inside the same check
 * as the fields above it: `ToolCalls::derived()` is the set and what measured
 * it.
 */
final class ToolSurface
{
    /**
     * The page every tool page links its sources into, named without the
     * extension because that is how a reference addresses a document and the
     * file is the one place that has to add it back.
     */
    public const SOURCES_PAGE = 'answer-sources';

    /** The line above the generated cards, so the explanation survives a rewrite. */
    private const LISTING_STARTS = ".. The cards below are written by ``bin/cli tools:index``.\n";

    public static function index(): string
    {
        return Paths::root() . '/documentation/server/tools/readme.rst';
    }

    public static function directory(): string
    {
        return Paths::root() . '/documentation/server/tools';
    }

    public static function file(string $tool): string
    {
        return self::directory() . '/' . $tool . '.rst';
    }

    /** What is on disk, so a page for a tool that is gone can go with it. */
    public static function written(): Finder
    {
        return Finder::create()->files()->in(self::directory())->name('*.rst')->sortByName();
    }

    /**
     * Every page as it would be written now, keyed by its file.
     *
     * The recorded half is carried over as it stands. It came out of an
     * installation on a day, nothing here can derive it again, and a
     * regeneration that dropped it would make `tools:index` delete evidence.
     *
     * The derived half is the other case and is written here rather than
     * carried: eight tools read nothing an installation contains, so their
     * answers follow from the registry and `knowledge/` the way the fields
     * above them do. That is what puts them inside what `tools:check` holds —
     * `ToolCalls::derived()` is the set and why.
     *
     * @return array<string, string>
     */
    public static function pages(): array
    {
        $derived = ToolAnswers::derivedSections();

        $pages = self::standingPages();
        foreach (Registry::definitions() as $definition) {
            $name = $definition['name'];
            $pages[self::file($name)] = self::page(
                $definition,
                $derived[$name] ?? ToolAnswers::recordedIn(self::file($name)),
            );
        }

        return $pages;
    }

    /**
     * The pages generated with this surface that belong to no single tool.
     *
     * Both `tools:index` and `tools:record` write the surface and then delete
     * what is not in what they wrote, so a page only one of them knows about is
     * removed by the other. There is one list of them and both read it.
     *
     * @return array<string, string>
     */
    public static function standingPages(): array
    {
        return [
            self::index() => self::indexPage(),
            dirname(self::directory()) . '/' . self::SOURCES_PAGE . '.rst' => self::sourcesPage(),
        ];
    }

    /**
     * One page: the derived half, then whatever recording goes under it.
     *
     * A tool the recording table leaves out keeps no recorded half, whatever an
     * earlier table left on its page — the written reason and a recorded answer
     * would otherwise contradict each other on one page.
     *
     * @param array{name: string, description: string, answersFrom: array<int, string>, inputSchema: array<string, mixed>, annotations: array<string, bool>, outputSchema: array<string, mixed>|null} $definition
     */
    public static function page(array $definition, string $recorded): string
    {
        $head = self::head($definition);
        if (isset(ToolCalls::undriven()[$definition['name']])) {
            return $head;
        }

        return $recorded === '' ? $head : $head . "\n" . $recorded;
    }

    public static function indexPage(): string
    {
        $contents = (string) file_get_contents(self::index());
        $start = strpos($contents, self::LISTING_STARTS);
        if ($start === false) {
            throw new \RuntimeException('The tool index has no generated-card marker.');
        }

        return substr($contents, 0, $start) . self::LISTING_STARTS . "\n" . self::listing();
    }

    /**
     * One card per tool: its name, verb, page, and the sentence its description
     * opens with.
     *
     * The whole surface used to be here, and it was two thousand lines of field
     * list a reader arriving with one tool in hand scrolled past.
     */
    private static function listing(): string
    {
        $lines = ['.. grid:: wide', ''];
        foreach (self::alphabetical() as $definition) {
            $body = Rst::INDENT . Rst::INDENT;
            $verb = ucfirst((string) substr(strrchr($definition['name'], '_') ?: '', 1));
            $lines = [
                ...$lines,
                Rst::INDENT . '.. card:: ' . Rst::doc($definition['name'], $definition['name']),
                $body . ':label: ' . $verb,
                $body . ':action: Open reference',
                '',
                Wrap::indented(self::opening($definition['description']), $body),
                '',
            ];
        }

        // The rail of this section is the listing rather than a second list
        // beside it, so a tool that is added reaches the menu by being in the
        // registry and by nothing else. The server index carries the sources
        // page, because it defines a server-wide boundary rather than a tool.
        $pages = array_map(
            static fn(array $definition): string => $definition['name'],
            self::alphabetical(),
        );

        return implode("\n", $lines) . "\n.. toctree::\n" . Rst::INDENT . ":hidden:\n\n"
            . implode('', array_map(
                static fn(string $page): string => Rst::INDENT . $page . "\n",
                $pages,
            ));
    }

    /**
     * The tools by name, which is the order every list on these pages is read
     * in.
     *
     * The registry's own order is what a client is offered — orientation first,
     * then the guides and lookups — and a reader arriving with one tool name in
     * hand cannot reconstruct it, so on the site the name is the index.
     *
     * @return array<int, array{name: string, description: string, answersFrom: array<int, string>, inputSchema: array<string, mixed>, annotations: array<string, bool>, outputSchema: array<string, mixed>|null}>
     */
    private static function alphabetical(): array
    {
        $definitions = Registry::definitions();
        usort($definitions, static fn(array $one, array $other): int => strcmp($one['name'], $other['name']));

        return $definitions;
    }

    /**
     * The first sentence, and the colon or dash before an enumeration counts as
     * its end. What follows one of those is the detail the index is dropping,
     * and read whole `typo3_extension_describe` opens with ten lines of it.
     */
    private static function opening(string $description): string
    {
        preg_match('/^.*?(?:[.?!](?=\s|$)|:|\s—\s)/s', $description, $matched);
        $opening = (string) preg_replace('/\s+/', ' ', trim($matched[0] ?? $description, " :—\n"));

        return str_ends_with($opening, '.') ? $opening : $opening . '.';
    }

    /**
     * @param array{name: string, description: string, answersFrom: array<int, string>, inputSchema: array<string, mixed>, annotations: array<string, bool>, outputSchema: array<string, mixed>|null} $definition
     */
    private static function head(array $definition): string
    {
        $lines = [
            // The label is what every other page reaches this tool by, and it
            // is the tool's own name: a reference nobody has to look up.
            ...Rst::label($definition['name']),
            ...Rst::heading(Rst::literal($definition['name'])),
            self::wrap($definition['description']),
            '',
            self::annotations($definition['annotations']),
            '',
            self::wrap(self::answerSources($definition['answersFrom'])),
            '',
            ...Rst::heading('Takes', 1),
            ...self::schema($definition['inputSchema'], 'The call carries exactly one of these sets of arguments'),
            ...Rst::heading('Answers with', 1),
            ...self::schema($definition['outputSchema'] ?? [], 'The answer carries exactly one of these sets of fields'),
            ...self::unrecorded($definition['name']),
        ];

        return implode("\n", $lines);
    }

    /**
     * @param array<string, mixed> $schema
     * @return list<string>
     */
    private static function schema(array $schema, string $opening): array
    {
        $fields = self::fields($schema, '');
        $lines = $fields === [] ? ['Nothing.', ''] : Rst::code('yaml', implode("\n", $fields));

        return [...$lines, ...self::alternatives($schema, $opening)];
    }

    /**
     * Why this tool has no recorded answer, where it has none on purpose.
     *
     * The reasons are `ToolCalls::undriven()`'s, beside the table that leaves
     * those tools out, so an absence and the reason for it cannot come apart. A
     * tool with neither renders as saying so, which is a defect a reader can see
     * rather than a silence they cannot.
     *
     * @return list<string>
     */
    private static function unrecorded(string $name): array
    {
        if (!isset(ToolCalls::undriven()[$name])) {
            return [];
        }

        return [
            ...Rst::heading('Not answered', 1),
            self::wrap('And deliberately: ' . ToolCalls::undriven()[$name]),
            '',
        ];
    }

    /**
     * The sources, linked to what each one means.
     *
     * A reader who has not met the word cannot tell `packages` from
     * `installation`, and the difference is the whole point of the line: one
     * answers with the containers down and the other does not. So the page
     * carries the names and the page behind them carries the meanings, written
     * once from the enum.
     *
     * @param array<int, string> $sources
     */
    private static function answerSources(array $sources): string
    {
        return 'Answers from ' . implode(', ', array_map(
            static fn(string $source): string => Rst::ref($source, self::sourceLabel($source)),
            $sources,
        )) . '.';
    }

    /**
     * The label one source's section carries, written once for the page that
     * defines it and the pages that point at it.
     *
     * A reference is global in reStructuredText, so the name has to say which
     * page it belongs to: `knowledge` alone would collide with the first
     * heading anywhere else that calls itself that.
     */
    public static function sourceLabel(string $source): string
    {
        return self::SOURCES_PAGE . '-' . $source;
    }

    /** The page the sources on every tool page link into. */
    public static function sourcesPage(): string
    {
        $lines = [
            ...Rst::navigationTitle('Answer sources'),
            ...Rst::label('answer-sources'),
            ...Rst::heading('Where an answer comes from'),
            self::wrap(
                'Every tool declares which sources can answer it, and says so at the foot of its own description '
                . 'and on its page here. What that answers is not what a tool is about but whether it can be '
                . 'asked at all right now: with nothing running, the tools under knowledge and packages are the '
                . 'ones still worth calling. Which source answered one call is ' . Rst::literal('answeredBy')
                . ' in that answer, where the tool has two. This page is written by '
                . Rst::literal('bin/cli tools:index') . ' from the Source enum.',
            ),
            '',
            ...Rst::image(
                '../images/answer-sources.svg',
                'The five sources plotted against how much of the machine has to be running: bundled knowledge '
                . 'and this server\'s own checkout answer with nothing running, packages need files on disk, the '
                . 'installation source needs a booted installation, and network sources need outbound reach.',
            ),
        ];

        foreach (Source::cases() as $source) {
            $tools = [];
            foreach (self::alphabetical() as $definition) {
                if (in_array($source->value, $definition['answersFrom'], true)) {
                    $tools[] = Rst::doc($definition['name'], 'tools/' . $definition['name']);
                }
            }
            $lines = [
                ...$lines,
                ...Rst::label(self::sourceLabel($source->value)),
                ...Rst::heading($source->value, 1),
                self::wrap($source->meaning()),
                '',
                self::wrap($tools === [] ? 'No tool answers from it.' : implode(', ', $tools) . '.'),
                '',
            ];
        }

        return implode("\n", $lines);
    }

    /**
     * The names are the client's, so they are printed rather than translated.
     *
     * @param array<string, bool> $annotations
     */
    private static function annotations(array $annotations): string
    {
        $stated = [];
        foreach ($annotations as $hint => $value) {
            $stated[] = Rst::literal($hint . ': ' . ($value ? 'true' : 'false'));
        }

        return implode(' · ', $stated);
    }

    /**
     * The schema as the shape a client validates against.
     *
     * A bullet list said the same thing and could not say the nesting: every
     * field came out at one level however deep it sat, so `covers` and the
     * `topic` inside one of its entries read as two fields of the answer.
     *
     * @param array<string, mixed> $schema
     * @return list<string>
     */
    private static function fields(array $schema, string $indent): array
    {
        $required = (array) ($schema['required'] ?? []);

        $lines = [];
        foreach ((array) ($schema['properties'] ?? []) as $name => $field) {
            $field = (array) $field;
            $says = self::says($field);
            if ($says !== '') {
                array_push($lines, ...self::comment($says, $indent));
            }

            $optional = in_array($name, $required, true) ? '' : '  # optional';
            $below = self::below($field);
            if ($below === []) {
                $lines[] = $indent . $name . ': ' . self::type($field) . $optional;

                continue;
            }

            $lines[] = $indent . $name . ':' . $optional;
            array_push($lines, ...self::nested($below, $indent, ($field['type'] ?? '') === 'array'));
        }

        return $lines;
    }

    /**
     * The dash opens a list entry on whatever its first line is, comment
     * included. Moving it down to the first key would put it outside the entry
     * the comment belongs to.
     *
     * @param array<string, mixed> $schema
     * @return list<string>
     */
    private static function nested(array $schema, string $indent, bool $isList): array
    {
        if (!$isList) {
            return self::fields($schema, $indent . '  ');
        }

        $entry = self::fields($schema, $indent . '    ');
        $entry[0] = $indent . '  - ' . substr($entry[0], strlen($indent) + 4);

        return $entry;
    }

    /**
     * What a list holds, or the object itself. A field that is a value has
     * nothing below it.
     *
     * @param array<string, mixed> $field
     * @return array<string, mixed>
     */
    private static function below(array $field): array
    {
        $under = ($field['type'] ?? '') === 'array' ? (array) ($field['items'] ?? []) : $field;

        return (array) ($under['properties'] ?? []) === [] ? [] : $under;
    }

    /** @param array<string, mixed> $field */
    private static function type(array $field): string
    {
        $type = $field['type'] ?? 'object';
        if (is_array($type)) {
            return implode(' or ', $type);
        }
        if ($type === 'array') {
            return '[' . self::type((array) ($field['items'] ?? [])) . ']';
        }

        return (string) $type;
    }

    /**
     * The values a field is limited to first: a closed set is what a caller has
     * to pass, and the description behind it explains the cases rather than
     * listing them.
     *
     * @param array<string, mixed> $field
     */
    private static function says(array $field): string
    {
        $description = (string) ($field['description'] ?? '');
        if (!isset($field['enum'])) {
            return $description;
        }

        $values = array_map(
            static fn(mixed $value): string => (string) ($value ?? 'null'),
            (array) $field['enum'],
        );

        return trim('One of: ' . implode(', ', $values) . '. ' . $description);
    }

    /**
     * A `oneOf` on either schema, read out — `D-ANS-012`. A field list alone
     * reads as one answer carrying everything, and on the way in it refuses a
     * caller for both branches at once.
     *
     * @param array<string, mixed> $schema
     * @return list<string>
     */
    private static function alternatives(array $schema, string $opening): array
    {
        if (!isset($schema['oneOf'])) {
            return [];
        }

        $sets = array_map(
            static fn(array $branch): string => implode(', ', array_map(
                Rst::literal(...),
                (array) ($branch['required'] ?? []),
            )),
            (array) $schema['oneOf'],
        );

        $lines = [$opening . ':', ''];
        foreach ($sets as $set) {
            $lines[] = self::wrap('- ' . $set, '  ');
        }
        $lines[] = '';

        return $lines;
    }

    /**
     * Wrapped here rather than through `Wrap`, which only knows what a continued
     * line opens with and not what every line of a comment does.
     *
     * @return list<string>
     */
    private static function comment(string $text, string $indent): array
    {
        $prefix = $indent . '# ';
        $wrapped = wordwrap(
            (string) preg_replace('/\s+/', ' ', trim($text)),
            Wrap::COLUMN - strlen($prefix),
            "\n",
        );

        return array_map(static fn(string $line): string => rtrim($prefix . $line), explode("\n", $wrapped));
    }

    private static function wrap(string $text, string $continuation = ''): string
    {
        return Wrap::text($text, $continuation);
    }
}
