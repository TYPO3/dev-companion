<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Server;

use TYPO3\DevCompanion\Tool\Registry;

/**
 * The tools a caller has asked to keep out of the offer.
 *
 * The only subtraction this server makes, and the caller makes it. What was
 * here before decided per checkout instead, which is a reason that holds for
 * the repository and not for the task, `R-AUD-002`. Which half of an answer is
 * worth its place stands in the answer, per topic and per path. A tool held
 * back costs the caller the doorway and leaves it the knowledge.
 */
final class ExcludedTools
{
    /** Comma-separated tool names the caller does not want offered. */
    public const VARIABLE = 'TYPO3_DEV_COMPANION_EXCLUDE_TOOLS';

    /**
     * The one tool no caller can exclude. It is what tells a client why the
     * list is shorter than the documentation says. A client that has lost it
     * cannot tell a configured server from a broken one.
     */
    private const ALWAYS_OFFERED = 'typo3_server_scope';

    /**
     * Whether the question to the registry is what it would offer with nothing
     * excluded, in which case the filter answers empty. See partition(), which
     * is the only thing that sets it.
     */
    private static bool $asking = false;

    /**
     * The partition, against the variable it came from.
     *
     * The computation asks the registry twice, and the registry asks every tool
     * for its schemas. `all()` runs several times per answer, and from a
     * variable a test changes between two of them. So it stays in memory
     * against the raw string rather than against nothing.
     *
     * @var array{raw: string, excluded: array<int, string>, unknown: array<int, string>, offeredAnyway: array<int, string>}|null
     */
    private static ?array $partition = null;

    /** Whether the caller left a tool by that name in the list. */
    public static function offers(string $tool): bool
    {
        return !in_array($tool, self::filter(), true);
    }

    /**
     * The tools that are really gone, so an answer can name them rather than
     * let a client wonder where a tool went.
     *
     * What the caller wrote is not that list. A name no tool answers to and one
     * that names a tool the filter does not reach both came back here as
     * excluded. That told a client it has lost a capability it has, out of the
     * instruction budget `R-ANS-013` holds.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return self::partition()['excluded'];
    }

    /**
     * The names in the list that no tool of this server answers to.
     *
     * Such a name takes nothing away, and nothing in the list itself tells it
     * from one that does. A renamed tool leaves its old name behind, which
     * looks exactly like a name that was never right. The caller gets the tool
     * back with no word from either side. That is what `a4470ee` did to
     * `typo3_project_scope` and `typo3_extension_scope`.
     *
     * @return array<int, string>
     */
    public static function unknown(): array
    {
        return self::partition()['unknown'];
    }

    /**
     * The names in the list that this server offers anyway.
     *
     * The three `R-SCO-009` names as what a caller cannot take away, reached
     * from the other side. `typo3_server_scope`, which drops out here, and the
     * two feedback tools, which `Registry::offered()` adds past the filter
     * (`D-FBK-042`). Nothing computes from that list.
     *
     * @return array<int, string>
     */
    public static function offeredAnyway(): array
    {
        return self::partition()['offeredAnyway'];
    }

    /**
     * Every name the caller wrote, in one of three states.
     *
     * The registry answers twice and the two lists stand against each other.
     * What it offers with the filter, and what it offers with this class as an
     * empty answer. A name absent from the second is one no tool answers to. A
     * name in the first is one the filter did not reach. The rest are gone,
     * which is the only claim a client can act on.
     *
     * @return array{raw: string, excluded: array<int, string>, unknown: array<int, string>, offeredAnyway: array<int, string>}
     */
    private static function partition(): array
    {
        $raw = self::raw();
        if (self::$partition !== null && self::$partition['raw'] === $raw) {
            return self::$partition;
        }

        $partition = ['raw' => $raw, 'excluded' => [], 'unknown' => [], 'offeredAnyway' => []];
        $configured = self::configured();
        if ($configured !== []) {
            self::$asking = true;
            try {
                $everything = array_column(Registry::definitions(), 'name');
            } finally {
                self::$asking = false;
            }
            $offered = array_column(Registry::definitions(), 'name');
            foreach ($configured as $tool) {
                $state = match (true) {
                    !in_array($tool, $everything, true) => 'unknown',
                    in_array($tool, $offered, true) => 'offeredAnyway',
                    default => 'excluded',
                };
                $partition[$state][] = $tool;
            }
        }

        return self::$partition = $partition;
    }

    /**
     * What filters the tool list, which is what the caller wrote.
     *
     * `typo3_server_scope` comes out here rather than in the report. The caller
     * who named it hears it stayed in the offer anyway, and the filter never
     * sees it.
     *
     * @return array<int, string>
     */
    private static function filter(): array
    {
        if (self::$asking) {
            return [];
        }

        return array_values(array_filter(
            self::configured(),
            static fn(string $tool): bool => $tool !== self::ALWAYS_OFFERED,
        ));
    }

    /**
     * The names the caller wrote, in the caller's order.
     *
     * @return array<int, string>
     */
    private static function configured(): array
    {
        $names = array_filter(
            preg_split('/[,\s]+/', strtolower(trim(self::raw()))) ?: [],
            static fn(string $tool): bool => $tool !== '',
        );

        return array_values(array_unique($names));
    }

    private static function raw(): string
    {
        $configured = getenv(self::VARIABLE);

        return is_string($configured) ? $configured : '';
    }
}
