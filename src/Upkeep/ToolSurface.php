<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Tool\Source;

/**
 * The tool surface: one page per tool, an index that reaches them, and the
 * server-level page that defines their answer sources.
 *
 * A surface written out a second time by hand stops to describe the answer at
 * the first change nobody carried across. So this renders it from
 * Registry::definitions() and `tools:check` fails where a page has gone stale.
 *
 * The rest of a page is `ToolAnswers`', under the page's `## Answered` heading,
 * and it is one of two things. What a filled answer looks like usually needs an
 * installation to call, so it comes from a record and no check reads it. Each
 * of the two commands carries the other's half over untouched, which is what
 * lets one file hold both. Where a tool's answers read nothing an installation
 * contains, that half derives here instead and falls inside the same check as
 * the fields above it. `ToolCalls::derived()` is the set and what measured it.
 */
final class ToolSurface
{
    /**
     * The page every tool page links its sources into, named without the
     * extension. That is how a reference addresses a document, and the file is
     * the one place that has to add it back.
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
     * What each tool's definition weighs on `tools/list`, the output schema
     * apart, largest schema first.
     *
     * Bytes of compact JSON, for the reason `ToolAnswers::measured()` gives.
     * The two halves count apart because a client hands the model one of
     * them. The name, the description and the input schema are the tool
     * definition the model chooses by. The output schema is the contract the
     * client validates the data half against, and neither client read on
     * 2026-09-17 sends it on — `D-EVI-011`.
     *
     * @return list<array{tool: string, declared: int, outputSchema: int}>
     */
    public static function measured(): array
    {
        $measured = [];
        foreach (Registry::definitions() as $definition) {
            $measured[] = [
                'tool' => $definition['name'],
                'declared' => strlen(self::compact([
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'inputSchema' => $definition['inputSchema'],
                ])),
                'outputSchema' => strlen(self::compact($definition['outputSchema'] ?? [])),
            ];
        }

        usort($measured, static fn(array $a, array $b): int => $b['outputSchema'] <=> $a['outputSchema']);

        return $measured;
    }

    /**
     * A value as a client hands it to the model: compact, the slashes and the
     * unicode as they stand, which is what `JSON.stringify` produces. mcp/sdk
     * escapes both on the wire, and the client decodes that before anything
     * reads it. So the wire is not the number that costs.
     *
     * @param array<string, mixed> $value
     */
    public static function compact(array $value): string
    {
        return (string) json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Every page as it would come out now, keyed by its file.
     *
     * The recorded half carries over as it stands. It came out of an
     * installation on a day, and nothing here can derive it again. A
     * regeneration that dropped it would make `tools:index` delete evidence.
     *
     * The derived half is the other case and comes from here rather than
     * carries over. Eight tools read nothing an installation contains, so their
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
     * what is not in what they wrote. So the other removes a page only one of
     * them knows about. There is one list of them and both read it. The
     * initialize page is here because the same check holds it, and because a
     * change to a description moves the weight it states.
     *
     * @return array<string, string>
     */
    public static function standingPages(): array
    {
        return [
            self::index() => self::indexPage(),
            dirname(self::directory()) . '/' . self::SOURCES_PAGE . '.rst' => self::sourcesPage(),
            Handshake::file() => Handshake::page(),
        ];
    }

    /**
     * One page: the derived half, then whatever record goes under it.
     *
     * A tool the record table leaves out keeps no recorded half, whatever an
     * earlier table left on its page. The written reason and a recorded answer
     * would otherwise contradict each other on one page.
     *
     * @param array{name: string, title: string, description: string, answersFrom: array<int, string>, inputSchema: array<string, mixed>, annotations: array<string, bool>, outputSchema: array<string, mixed>|null} $definition
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
     * The whole surface used to be here. It was two thousand lines of field
     * list a reader with one tool in hand scrolled past.
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
        // beside it, so a new tool reaches the menu because it is in the
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
     * The tools by name, which is the order of every list on these pages.
     *
     * The registry's own order is what a client gets, orientation first, then
     * the guides and lookups. A reader with one tool name in hand cannot
     * reconstruct it, so on the site the name is the index.
     *
     * @return array<int, array{name: string, title: string, description: string, answersFrom: array<int, string>, inputSchema: array<string, mixed>, annotations: array<string, bool>, outputSchema: array<string, mixed>|null}>
     */
    private static function alphabetical(): array
    {
        $definitions = Registry::definitions();
        usort($definitions, static fn(array $one, array $other): int => strcmp($one['name'], $other['name']));

        return $definitions;
    }

    /**
     * The first sentence, and the colon or dash before an enumeration counts as
     * its end. What follows one of those is the detail the index drops, and
     * read whole `typo3_extension_describe` opens with ten lines of it.
     */
    private static function opening(string $description): string
    {
        preg_match('/^.*?(?:[.?!](?=\s|$)|:|\s—\s)/s', $description, $matched);
        $opening = (string) preg_replace('/\s+/', ' ', trim($matched[0] ?? $description, " :—\n"));

        return str_ends_with($opening, '.') ? $opening : $opening . '.';
    }

    /**
     * @param array{name: string, title: string, description: string, answersFrom: array<int, string>, inputSchema: array<string, mixed>, annotations: array<string, bool>, outputSchema: array<string, mixed>|null} $definition
     */
    private static function head(array $definition): string
    {
        $lines = [
            // The label is what every other page reaches this tool by, and it
            // is the tool's own name: a reference nobody has to look up.
            ...Rst::label($definition['name']),
            ...Rst::heading(Rst::literal($definition['name'])),
            '*' . $definition['title'] . '*',
            '',
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
     * tool with neither renders with a line that says so, which is a defect a
     * reader can see rather than a silence they cannot.
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
     * `installation`. The difference is the whole point of the line: one
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
     * page it belongs to. `knowledge` alone would collide with the first
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
                                . 'and on its page here. What that answers is whether a caller can ask a tool at all right now, '
                . 'not what the tool is about. With nothing up, the tools under knowledge and packages are the '
                . 'ones still worth a call. Which source answered one call is ' . Rst::literal('answeredBy')
                . ' in that answer, where the tool has two. ' . Rst::literal('bin/cli tools:index')
                . ' writes this page from the Source enum.',
            ),
            '',
            ...Rst::image(
                '../images/answer-sources.svg',
                'The five sources against how much of the machine has to run. Bundled knowledge and this '
                . 'server\'s own checkout answer with nothing up. Packages need files on disk, the installation '
                . 'source needs a booted installation, and network sources need outbound reach.',
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
     * The names are the client's, so they print as they are rather than in
     * translation.
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
     * A bullet list said the same thing and could not say the depth. Every
     * field came out at one level however deep it sat. So `covers` and the
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
            $field = self::branch((array) $field);
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
        if (isset($field['anyOf'])) {
            return implode(' or ', array_map(static fn(mixed $branch): string => self::type((array) $branch), (array) $field['anyOf']));
        }
        $type = (string) ($field['type'] ?? 'object');

        return $type === 'array' && isset($field['items']) ? '[' . self::type((array) $field['items']) . ']' : $type;
    }

    /**
     * A field with `anyOf` branches, read as its first branch with the
     * description beside it. That is the type of a nullable field
     * (`Schema::nullable()`) and the string of a scalar one. The `anyOf` stays
     * on it, so `type()` still prints every branch.
     *
     * @param array<string, mixed> $field
     * @return array<string, mixed>
     */
    private static function branch(array $field): array
    {
        $branches = (array) ($field['anyOf'] ?? []);

        return $branches === [] ? $field : (array) $branches[0] + $field;
    }

    /**
     * The values a field permits first. A closed set is what a caller has to
     * pass, and the description behind it explains the cases rather than lists
     * them.
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
     * reads as one answer with everything in it. On the way in it refuses a
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
