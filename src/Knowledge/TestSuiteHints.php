<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge;

use TYPO3\DevCompanion\Paths;

/**
 * Loads and ranks runTests.sh suite hints from test-suite-hints.json, plus the
 * invocation notes (CI=true, targeted runs, option flags) that apply to every
 * suite.
 *
 * @phpstan-type Suite array{suite: string, command: string, runs: string, description: string, whenToUse: string, domains: array<int, string>, base: bool, targeted: ?string, since: ?int, until: ?int}
 */
final class TestSuiteHints
{
    /** Generic words that appear in task descriptions but carry no suite signal. */
    private const STOPWORDS = [
        'the', 'and', 'for', 'with', 'add', 'new', 'change', 'changes', 'update',
        'fix', 'fixes', 'core', 'typo3', 'file', 'files', 'code', 'support',
    ];

    /**
     * @return array{
     *     invocation: array{preconditions: array<int, string>, beforeYouRun: string, notes: array<int, string>, options: array<int, array{option: string, description: string}>, examples: array<int, array{purpose: string, command: string}>},
     *     suites: array<int, Suite>
     * }
     */
    private static function data(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::knowledgeFile('test-suite-hints.json')), true);
        if (!is_array($decoded) || !isset($decoded['suites'], $decoded['invocation'])) {
            throw new \RuntimeException('Invalid test-suite-hints.json');
        }

        $invocation = $decoded['invocation'];

        return [
            'invocation' => [
                // What has to be true before any suite runs, apart from what
                // one of them takes: the container the script starts, and the
                // `vendor/` the checkout may not have. A session poking at
                // `runTests.sh` with `ls` and `command -v` was holding exactly
                // these two questions (`D-AUD-009`).
                'preconditions' => array_map('strval', $invocation['preconditions'] ?? []),
                // The one note that is about work being lost rather than about
                // a run failing, kept apart so the brief handing over a command
                // can carry it too — `D-ANS-145`.
                'beforeYouRun' => (string) ($invocation['beforeYouRun'] ?? ''),
                'notes' => array_map('strval', $invocation['notes'] ?? []),
                'options' => array_map(static fn(array $o): array => [
                    'option' => (string) $o['option'],
                    'description' => (string) $o['description'],
                ], $invocation['options'] ?? []),
                'examples' => array_map(static fn(array $e): array => [
                    'purpose' => (string) $e['purpose'],
                    'command' => (string) $e['command'],
                ], $invocation['examples'] ?? []),
            ],
            'suites' => array_map(static fn(array $entry): array => [
                'suite' => (string) $entry['suite'],
                'command' => (string) $entry['command'],
                'runs' => (string) $entry['runs'],
                'description' => (string) $entry['description'],
                'whenToUse' => (string) $entry['whenToUse'],
                'domains' => array_map('strval', $entry['domains'] ?? []),
                'base' => (bool) ($entry['base'] ?? false),
                'targeted' => isset($entry['targeted']) ? (string) $entry['targeted'] : null,
                'since' => isset($entry['since']) ? (int) $entry['since'] : null,
                'until' => isset($entry['until']) ? (int) $entry['until'] : null,
            ], $decoded['suites']),
        ];
    }

    /** @return array<int, Suite> */
    public static function load(?int $target = null): array
    {
        $suites = self::data()['suites'];
        if ($target === null) {
            return $suites;
        }

        return array_values(array_filter(
            $suites,
            static fn(array $suite): bool => Versions::holds($suite['since'], $suite['until'], $target),
        ));
    }

    /**
     * The suites that exist on a given major, by name.
     *
     * This is what makes a check filterable without repeating the range on
     * every one of them. A check is a runTests.sh invocation, the script
     * belongs to one branch of the core, and the suites it offers change
     * between majors — so the range is declared once, on the suite, and every
     * hint or intent that names it in `-s <suite>` inherits it.
     *
     * @return array<int, string>
     */
    public static function availableOn(int $target): array
    {
        return array_column(self::load($target), 'suite');
    }

    /**
     * The same checks with every command dropped whose suite does not exist on
     * the target version.
     *
     * A check that names a suite the caller's runTests.sh does not have is not
     * a weaker answer than none, it is a wrong one: it sends them to debug
     * their checkout for a command this server invented for another branch.
     *
     * @param array<int, string> $checks
     * @return array<int, string>
     */
    public static function checksFor(array $checks, ?int $target): array
    {
        if ($target === null) {
            return array_values($checks);
        }

        $available = self::availableOn($target);

        return array_values(array_filter($checks, static function (string $check) use ($available): bool {
            if (preg_match('/\s-s\s+([A-Za-z0-9_-]+)/', $check, $matches) !== 1) {
                return true;
            }

            return in_array($matches[1], $available, true);
        }));
    }

    /**
     * The suites any change in one of these domains runs, whatever the task
     * says about itself.
     *
     * This is what the hints used to carry as a `checks` list on
     * every entry: twenty-eight of them named nothing but the functional suite
     * and phpstan, which is not a property of the subject but of the domain it
     * is written in. Declared once here, a task in a domain gets them without a
     * matcher having to recognise the words "test" or "suite" in a sentence
     * about TSconfig field labels.
     *
     * @param array<int, string> $domains
     * @return array<int, string>
     */
    public static function baseFor(array $domains, ?int $target): array
    {
        $commands = [];
        foreach (self::load($target) as $suite) {
            if ($suite['base'] && array_intersect($suite['domains'], $domains) !== []) {
                $commands[] = $suite['command'];
            }
        }

        return $commands;
    }

    /**
     * The other half of a narrowing: the domains no given path reached, and how
     * many suites they hold on the target version.
     *
     * Counted rather than listed, because the list is what the narrowing exists
     * to avoid — `D-ANS-074`. Nothing narrowed means nothing withheld.
     *
     * @param array<int, string> $domains The domains the answer was narrowed to.
     * @return array{domains: array<int, string>, suites: int}
     */
    public static function withheld(array $domains, ?int $target): array
    {
        if ($domains === []) {
            return ['domains' => [], 'suites' => 0];
        }

        $withheld = [];
        $count = 0;
        foreach (self::load($target) as $suite) {
            if (array_intersect($suite['domains'], $domains) !== []) {
                continue;
            }
            ++$count;
            foreach ($suite['domains'] as $domain) {
                $withheld[$domain] = true;
            }
        }

        return ['domains' => array_keys($withheld), 'suites' => $count];
    }

    /**
     * The invocation guidance that applies regardless of the chosen suite.
     *
     * @return array{preconditions: array<int, string>, beforeYouRun: string, notes: array<int, string>, options: array<int, array{option: string, description: string}>, examples: array<int, array{purpose: string, command: string}>}
     */
    public static function invocation(): array
    {
        return self::data()['invocation'];
    }

    /**
     * Matched suites as data, with the range each one exists on.
     *
     * @param array<int, Suite> $hints
     * @return array<int, array<string, mixed>>
     */
    public static function records(array $hints): array
    {
        return array_map(static fn(array $hint): array => [
            'suite' => $hint['suite'],
            'command' => $hint['command'],
            'runs' => $hint['runs'],
            'targeted' => $hint['targeted'],
            'description' => $hint['description'],
            'whenToUse' => $hint['whenToUse'],
            'domains' => $hint['domains'],
            // Rendered the same way a statement's range is: beside the entry
            // rather than inside it, so an unfiltered listing still says which
            // branches actually have the suite.
            'versions' => Versions::label($hint['since'], $hint['until']),
        ], array_values($hints));
    }

    /**
     * Ranks suites against a query. When $domains is given, only suites touching
     * one of those domains are considered — so a PHP-only task never gets a
     * Sass build recommended.
     *
     * @param array<int, string> $domains
     * @return array<int, Suite>
     */
    public static function find(?string $query, array $domains = [], ?int $target = null): array
    {
        $hints = self::load($target);
        $narrowed = false;

        if ($domains !== []) {
            $hints = array_values(array_filter(
                $hints,
                static fn(array $hint): bool => array_intersect($hint['domains'], $domains) !== []
            ));
            $narrowed = true;
        }

        $terms = self::meaningfulTerms(trim($query ?? ''));

        // No query (or only stopwords): list everything for browsing.
        if ($terms === []) {
            return $hints;
        }

        $scored = [];
        foreach ($hints as $hint) {
            $score = self::scoreHint($hint, $terms);
            if ($score > 0) {
                $scored[] = ['hint' => $hint, 'score' => $score];
            }
        }

        // A query phrased as a request rather than a suite name ("recommend the
        // narrow iteration check") scores nothing. Once the domains have
        // narrowed the list, that list is still the right answer; only an
        // unnarrowed miss is a real miss.
        if ($scored === []) {
            return $narrowed ? $hints : [];
        }

        usort($scored, static function (array $a, array $b): int {
            return $b['score'] <=> $a['score']
                ?: strcmp($a['hint']['suite'], $b['hint']['suite']);
        });

        return array_map(static fn(array $entry): array => $entry['hint'], $scored);
    }

    /** @return array<int, string> */
    private static function meaningfulTerms(string $query): array
    {
        $terms = [];
        foreach (preg_split('/\s+/', mb_strtolower($query)) ?: [] as $term) {
            $term = preg_replace('/[^a-z0-9-]/', '', $term) ?? '';
            if (strlen($term) >= 3 && !in_array($term, self::STOPWORDS, true)) {
                $terms[] = $term;
            }
        }

        return $terms;
    }

    /**
     * Matches in the suite name weigh more than matches in the prose.
     *
     * @param array{suite: string, description: string, whenToUse: string} $hint
     * @param array<int, string> $terms
     */
    private static function scoreHint(array $hint, array $terms): int
    {
        $suite = mb_strtolower($hint['suite']);
        $prose = mb_strtolower($hint['description'] . ' ' . $hint['whenToUse']);

        $score = 0;
        foreach ($terms as $term) {
            if (str_contains($suite, $term)) {
                $score += 2;
            } elseif (str_contains($prose, $term)) {
                $score += 1;
            }
        }

        return $score;
    }
}
