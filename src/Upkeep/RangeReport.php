<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Knowledge\Versions;

/**
 * The drift between a range an entry records and the one the checkouts carry.
 *
 * Shared by the two checks that verify a recorded range — the system extensions
 * and the worked examples — because one wording for a drift is what makes two
 * reports readable as one kind of finding.
 */
final class RangeReport
{
    /**
     * Whether the recorded since/until still says which versions ship an extension.
     * A range with a hole in it is reported as such: it cannot be expressed, and an
     * extension that came back is a different statement from one that never left.
     *
     * @param array<string, mixed> $entry
     * @param array<int, int> $majors
     * @param array<int, int> $covered
     */
    public static function of(OutputInterface $output, string $key, array $entry, array $majors, array $covered): int
    {
        if ($majors !== range((int) $majors[0], (int) end($majors))) {
            Voice::problem($output, sprintf('%s: shipped on v%s, which no range can express', $key, implode(', v', $majors)));

            return 1;
        }

        $since = $majors[0] === $covered[0] ? null : $majors[0];
        $until = end($majors) === end($covered) ? null : end($majors);
        $recordedSince = isset($entry['since']) ? (int) $entry['since'] : null;
        $recordedUntil = isset($entry['until']) ? (int) $entry['until'] : null;
        if ($since === $recordedSince && $until === $recordedUntil) {
            return 0;
        }

        Voice::problem($output, sprintf(
            '%s: records %s, ships %s',
            $key,
            self::label($recordedSince, $recordedUntil),
            self::label($since, $until),
        ));

        return 1;
    }

    private static function label(?int $since, ?int $until): string
    {
        $label = Versions::label($since, $until);

        return $label === '' ? 'on every covered version' : $label;
    }

    /**
     * The first covered major from which an entry holds without a gap up to the
     * newest, or null when it holds everywhere. An entry with a hole in it binds
     * on the newest unbroken run, so what is older than the hole is withheld — a
     * range cannot express a gap, and such an entry needs splitting rather than
     * a number.
     *
     * @param array<int, bool> $holds
     */
    public static function since(array $holds): ?int
    {
        $since = null;
        foreach (array_reverse($holds, true) as $major => $holdsHere) {
            if (!$holdsHere) {
                break;
            }
            $since = $major;
        }

        return $since === array_key_first($holds) ? null : $since;
    }
}
