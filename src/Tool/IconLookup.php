<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Icons;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * Icon identifiers registered in the installation.
 *
 * The only instance question with no console command behind it, so the registry
 * is read from the three places TYPO3 assembles it from. An identifier-shaped
 * query is a validation and is answered as one: fuzzy results for it are
 * suggestions, and saying so is the whole point — a confident wrong substitute
 * is worse than a miss.
 */
final class IconLookup extends ReadOnlyTool
{
    /**
     * Where the identifiers this tool answers with may be used.
     *
     * It travels with every answer rather than with the ones that look like
     * frontend work, because the tool is handed a query and not a task: nothing
     * in "product package box" says which half of TYPO3 it is for. An
     * identifier is resolved by IconFactory and rendered by <core:icon>, and a
     * frontend template reaches neither — so an answer without this sentence is
     * usable in a place where it is wrong.
     */
    /**
     * How many neighbours a missing identifier is offered.
     *
     * Few, because a validation of several names is one answer: the reported
     * case was three identifiers, and one of them alone had come back with 22.
     */
    private const SUGGESTIONS_PER_MISS = 5;

    private const SCOPE = 'These identifiers address the backend icon registry. They are resolved by '
        . 'IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, '
        . 'and needs its own inline SVG or asset file.';

    public static function name(): string
    {
        return 'typo3_icon_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation, Source::Packages];
    }

    public static function description(): string
    {
        return 'Validate or find icon identifiers in the TYPO3 backend icon registry of the installation you are working in. Pass identifiers to confirm several at once — each comes back registered or not, in one call, which is what to use when you already read them out of a template; pass query to search for one by name or by what it means. It is read from the running installation, so what a package registers in a loop or from ext_localconf.php is in the answer as well as what its Configuration/Icons.php declares; where the installation cannot be booted — no console, or a checkout with no configuration yet — the T3Icons set, the package registration files and the flag images are read instead, answeredBy says \'packages\', and the answer states what that leaves out. Identifiers spell shapes rather than intents, so concept words are mapped: "warning" finds actions-exclamation-triangle. Backend only: the identifiers are resolved by IconFactory and rendered by <core:icon>, and a frontend template can use neither.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Identifier, identifier fragment, or concept, for example "actions-open", "delete", or "warning". Omit to list the categories and concept words.'],
                'identifiers' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'Complete identifiers to check in one call, for example ["actions-open", "actions-cog"]. Each is answered registered or not on its own, with no ranking behind the ones that are — that is what to pass when you already read the identifiers out of a template and only need them confirmed. A miss still carries suggestions.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 40, 'description' => 'Maximum number of identifiers to return.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'query' => Schema::string(),
            'matchCount' => Schema::integer('For a complete identifier, 1 only when that exact identifier is registered and otherwise 0. For a concept search, the number of matching icons.'),
            'suggestionCount' => Schema::integer('Related identifiers returned beside an identifier validation. The actions-/content- usage prefix alone never makes an icon a suggestion.'),
            'exactMatch' => ['type' => 'boolean', 'description' => 'Whether the query was a registered identifier. False for a query shaped like one that is not registered — the listed icons are then suggestions, not the answer.'],
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
            'icons' => Schema::listOf(Schema::object([
                'identifier' => Schema::string(),
                'category' => Schema::string(),
                'aliasOf' => Schema::nullableString('The identifier this one is an alias of.'),
                'source' => Schema::string('Where it is registered: t3icons, flags, or the EXT:<key>/Configuration/Icons.php that declares it.'),
                'matched' => Schema::integer('Query terms it matched.'),
                'score' => Schema::integer(),
                'why' => Schema::listOf(Schema::string()),
            ], ['identifier', 'category', 'aliasOf', 'source'])),
            'validated' => Schema::listOf(Schema::object([
                'identifier' => Schema::string('As it was passed.'),
                'registered' => ['type' => 'boolean', 'description' => 'Whether this exact identifier is registered. False is the answer, not an empty result.'],
                'category' => Schema::string(),
                'aliasOf' => Schema::nullableString('The identifier this one is an alias of.'),
                'source' => Schema::string('Where it is registered. Empty where it is not.'),
                'usedBy' => Schema::listOf(Schema::string(), 'What this identifier is already the icon of in this installation, as "tt_content.CType=<value>". Registered says the identifier resolves; this says whose picture it is, which is the question a caller borrowing one is actually asking. Empty means nothing binds it here — or that the installation did not answer, which answeredBy is what says.'),
                'suggestions' => Schema::listOf(Schema::string(), 'Related identifiers, for a miss only. A registered identifier carries none, because its neighbours are not an answer to it.'),
            ], ['identifier', 'registered', 'category', 'aliasOf', 'source', 'usedBy', 'suggestions']), 'One entry per identifier passed in, in that order. Returned when identifiers were given.'),
            'categories' => Schema::listOf(Schema::string(), 'Returned when no query was given.'),
            'concepts' => Schema::listOf(Schema::string(), 'Concept words that map to a shape. Returned when no query was given.'),
            'scope' => Schema::string('Where these identifiers may be used: the backend registry, not frontend rendering. Carried by every answered lookup.'),
        ], ['query', 'matchCount', 'suggestionCount', 'exactMatch', 'answeredBy', 'icons'], ['query']);
    }

    public static function answer(array $args): ToolResult
    {
        $query = trim((string) ($args['query'] ?? ''));
        $limit = (int) ($args['limit'] ?? 40);
        $scope = self::SCOPE;
        // Where the registry could not be read from the booted installation,
        // the scope sentence says so. Every answered path below carries that
        // sentence, and a line of its own is one a caller reading the matches
        // would skip.
        $limitation = Icons::limitation();
        if ($limitation !== '') {
            $scope .= ' This list is ' . $limitation . '.';
        }

        if (!Instance::isAvailable()) {
            return Unsupported::because(
                'no TYPO3 installation was found from the directory this server was started in',
                ['query' => $query],
            );
        }

        $concepts = Icons::concepts();

        // The validation mode. It comes before everything below because the
        // question is a different one: "do these exist" is answered per name,
        // and the ranking that serves "what icon means X" is noise behind a
        // name that was already correct — `D-ANS-078`.
        $validated = self::validate($args['identifiers'] ?? null, $concepts);
        if ($validated !== []) {
            $registered = array_filter($validated, static fn(array $entry): bool => $entry['registered']);

            $lines = [$scope, ''];
            $lines[] = sprintf(
                '%d of %d identifier(s) are registered in %s:',
                count($registered),
                count($validated),
                Instance::root() ?? 'the installation',
            );
            foreach ($validated as $entry) {
                $lines[] = '- ' . $entry['identifier'] . ($entry['registered'] ? ': registered' : ': NOT registered');
                if ($entry['registered'] && $entry['aliasOf'] !== null) {
                    $lines[] = '  alias of ' . $entry['aliasOf'];
                }
                if ($entry['registered'] && $entry['source'] !== Icons::SOURCE_T3ICONS) {
                    $lines[] = '  registered in ' . $entry['source'];
                }
                // Whose picture it already is, which is what a caller asking
                // whether they may borrow one is actually asking — `D-ANS-131`.
                if ($entry['usedBy'] !== []) {
                    $lines[] = '  already the icon of ' . implode(', ', $entry['usedBy'])
                        . ' — registered says it resolves, not that it is free to describe something else';
                }
                if ($entry['suggestions'] !== []) {
                    $lines[] = '  did you mean: ' . implode(', ', $entry['suggestions']);
                }
            }

            return ToolResult::create(implode("\n", $lines), [
                'query' => $query,
                'matchCount' => count($registered),
                'suggestionCount' => array_sum(array_map(
                    static fn(array $entry): int => count($entry['suggestions']),
                    $validated,
                )),
                // One name that is not registered makes the answer no, which is
                // what a caller about to emit all of them has to act on.
                'exactMatch' => count($registered) === count($validated),
                'icons' => [],
                'validated' => $validated,
                'scope' => $scope,
                'answeredBy' => Icons::answeredBy(),
            ]);
        }

        if ($query === '') {
            $lines = [$scope, ''];
            $lines[] = 'Icon categories in this installation: ' . implode(', ', Icons::categories()) . '.';
            $lines[] = '';
            $lines[] = 'Concept words that map to a shape: ' . implode(', ', array_keys($concepts)) . '.';

            return ToolResult::create(implode("\n", $lines), [
                'query' => $query,
                'matchCount' => 0,
                'suggestionCount' => 0,
                'exactMatch' => false,
                'icons' => [],
                'categories' => Icons::categories(),
                'concepts' => array_keys($concepts),
                'scope' => $scope,
                'answeredBy' => Icons::answeredBy(),
            ]);
        }

        $isIdentifier = Icons::looksLikeIdentifier($query);
        $exactMatch = $isIdentifier && Icons::has($query);
        $matches = self::rank($query, $concepts);

        $total = count($matches);
        $suggestionCount = $isIdentifier
            ? count(array_filter(
                $matches,
                static fn(array $icon): bool => $icon['identifier'] !== mb_strtolower($query)
            ))
            : 0;
        $shown = array_slice($matches, 0, $limit);
        $root = Instance::root() ?? 'the installation';

        if ($shown === []) {
            return ToolResult::create(
                ($isIdentifier
                    ? sprintf('"%s" is not registered in %s.', $query, $root)
                    : sprintf(
                        'No icon in %s matches "%s". Identifiers spell the shape, not the intent — try a concept '
                        . 'word such as %s.',
                        $root,
                        $query,
                        implode(', ', array_slice(array_keys($concepts), 0, 8))
                    )) . "\n" . $scope,
                [
                    'query' => $query,
                    'matchCount' => 0,
                    'suggestionCount' => 0,
                    'exactMatch' => false,
                    'icons' => [],
                    'scope' => $scope,
                    'answeredBy' => Icons::answeredBy(),
                ],
            );
        }

        $header = $isIdentifier && !$exactMatch
            ? sprintf(
                '"%s" is not registered in %s. It is shaped like an identifier, so the %d below merely share a '
                . 'name part with it — suggestions, not the answer',
                $query,
                $root,
                $suggestionCount
            )
            : ($isIdentifier
                ? sprintf(
                    '"%s" is registered in %s%s',
                    $query,
                    $root,
                    $suggestionCount === 0
                        ? ''
                        : sprintf('; %d related identifier(s) follow as suggestions', $suggestionCount)
                )
                : sprintf('%d icon identifier(s) in %s match "%s"', $total, $root, $query));
        if ($total > count($shown)) {
            $header .= sprintf(' — showing the top %d', count($shown));
        }

        $lines = [$scope, '', $header . ':'];
        foreach ($shown as $icon) {
            $lines[] = '- ' . $icon['identifier'];
            if ($icon['aliasOf'] !== null) {
                $lines[] = '  alias of ' . $icon['aliasOf'];
            }
            if ($icon['source'] !== Icons::SOURCE_T3ICONS) {
                $lines[] = '  registered in ' . $icon['source'];
            }
            $lines[] = '  matched: ' . implode(', ', $icon['why']);
        }

        return ToolResult::create(implode("\n", $lines), [
            'query' => $query,
            // A complete identifier is a validation question. Its only match is
            // the identifier itself; same-category or same-name results are
            // suggestions and travel under their own count. In particular,
            // the usage prefixes actions-/content- must not turn one missing
            // identifier into hundreds of apparent matches.
            'matchCount' => $isIdentifier ? ($exactMatch ? 1 : 0) : $total,
            'suggestionCount' => $suggestionCount,
            'exactMatch' => $exactMatch,
            'icons' => $shown,
            'scope' => $scope,
            'answeredBy' => Icons::answeredBy(),
        ]);
    }

    /**
     * One verdict per identifier passed in, in the order they were passed.
     *
     * A miss keeps suggestions and a hit gets none: neighbours of a correct
     * identifier are noise, and neighbours of a wrong one are the next step —
     * `D-ANS-016`. Empty where nothing was passed.
     *
     * @param array<string, array<int, string>> $concepts
     * @return array<int, array{identifier: string, registered: bool, category: string, aliasOf: ?string, source: string, usedBy: array<int, string>, suggestions: array<int, string>}>
     */
    private static function validate(mixed $identifiers, array $concepts): array
    {
        $validated = [];
        foreach (is_array($identifiers) ? $identifiers : [] as $identifier) {
            if (!is_string($identifier) || trim($identifier) === '') {
                continue;
            }
            $identifier = trim($identifier);
            $icon = Icons::find($identifier);
            $validated[] = [
                'identifier' => $identifier,
                'registered' => $icon !== null,
                'category' => $icon['category'] ?? '',
                'aliasOf' => $icon['aliasOf'] ?? null,
                'source' => $icon['source'] ?? '',
                'usedBy' => $icon === null ? [] : Icons::boundTo($identifier),
                'suggestions' => $icon !== null ? [] : array_slice(
                    array_column(self::rank($identifier, $concepts), 'identifier'),
                    0,
                    self::SUGGESTIONS_PER_MISS,
                ),
            ];
        }

        return $validated;
    }

    /**
     * Ranks the registered identifiers against a query.
     *
     * A concept only contributes a term the identifier's own name did not
     * already carry, so a vague identifier cannot outrank a precise one by
     * matching the same word twice.
     *
     * @param array<string, array<int, string>> $concepts
     * @return array<int, array<string, mixed>>
     */
    private static function rank(string $query, array $concepts): array
    {
        $terms = array_values(array_unique(array_filter(
            preg_split('/[\s_-]+/', mb_strtolower(trim($query))) ?: [],
            static fn(string $term): bool => $term !== ''
        )));
        if ($terms === []) {
            return [];
        }
        $normalized = implode('-', $terms);
        if (Icons::looksLikeIdentifier($query)) {
            // actions-, content-, status- and the other leading categories say
            // where/how an icon is used. They are not a distinguishing shape
            // and therefore contribute nothing to related suggestions.
            array_shift($terms);
        }

        $suggested = [];
        foreach ($terms as $term) {
            foreach ($concepts[$term] ?? [] as $identifier) {
                $suggested[$identifier][$term] = true;
            }
        }

        $scored = [];
        foreach (Icons::all() as $icon) {
            $segments = explode('-', $icon['identifier']);
            $matched = [];
            $score = 0;
            $why = [];

            foreach ($terms as $term) {
                if (in_array($term, $segments, true)) {
                    $matched[] = $term;
                    $score += 4;
                    $why[] = 'name part "' . $term . '"';
                } elseif (str_contains($icon['identifier'], $term)) {
                    $matched[] = $term;
                    $score += 2;
                    $why[] = 'substring "' . $term . '"';
                }
            }
            foreach (array_keys($suggested[$icon['identifier']] ?? []) as $concept) {
                if (!in_array($concept, $matched, true)) {
                    $matched[] = $concept;
                }
                $score += 3;
                $why[] = 'concept "' . $concept . '"';
            }
            if ($matched === []) {
                continue;
            }
            if ($icon['identifier'] === $normalized) {
                $score += 1000;
                $why[] = 'exact identifier';
            }

            $scored[] = $icon + ['matched' => count($matched), 'score' => $score, 'why' => $why];
        }

        usort($scored, static function (array $a, array $b): int {
            return $b['matched'] <=> $a['matched']
                ?: $b['score'] <=> $a['score']
                ?: strcmp($a['identifier'], $b['identifier']);
        });

        return $scored;
    }
}
