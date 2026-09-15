<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Project;
use TYPO3\DevCompanion\Paths;

/**
 * The TYPO3 versions this knowledge base covers, and what a statement bound to
 * one of them is worth to a given caller.
 *
 * A convention is not timeless. The one that is current on the development line
 * may not exist on the LTS a site runs. A handover of it anyway produces code
 * that fails at runtime, silently: a translation domain that resolves to
 * nothing, a content column nothing can address. So a statement carries the
 * majors it holds for, and the answer either drops it or renders the range
 * beside it.
 *
 * knowledge/versions.json declares the covered versions, and everything that
 * needs the list reads it from there.
 */
final class Versions
{
    /**
     * One comparator of a Composer constraint: an optional operator, a major,
     * and an optional minor that may be a wildcard.
     *
     * Shared by the two reads below so they cannot drift apart on a form. One
     * asks which majors a constraint serves, the other what its lowest version
     * is, and both are the same comparator read to a different depth.
     */
    private const COMPARATOR = '/^(\^|~|>=|<=|>|<|=|v)?\s*v?(\d+)(?:\.(\d+|\*|x))?/i';

    /**
     * Never empty. A server that covers no version has no answer to give, and
     * every read that asks for the newest or the oldest would get `false`. The
     * file is where that settles, so the failure is a wrong file rather than an
     * answer built on nothing.
     *
     * @return non-empty-list<array{major: int, branch: string, status: string}>
     */
    public static function covered(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::knowledgeFile('versions.json')), true);
        if (!is_array($decoded) || !isset($decoded['covers']) || !is_array($decoded['covers']) || $decoded['covers'] === []) {
            throw new \RuntimeException('Invalid versions.json');
        }

        $covered = array_map(static fn(array $entry): array => [
            'major' => (int) $entry['major'],
            'branch' => (string) $entry['branch'],
            'status' => (string) ($entry['status'] ?? ''),
        ], $decoded['covers']);
        usort($covered, static fn(array $a, array $b): int => $a['major'] <=> $b['major']);

        return $covered;
    }

    /** @return array<int, int> */
    public static function majors(): array
    {
        return array_column(self::covered(), 'major');
    }

    /**
     * The version an answer composes for: what the caller stated, else what the
     * installation at hand runs, else nothing.
     *
     * Nothing is a legitimate state and not an error. A knowledge base with no
     * installation around it still answers. Then every statement comes back
     * with the range it holds for instead of a filter by one.
     */
    public static function target(?string $stated = null): ?int
    {
        $major = self::major($stated);

        return $major ?? Instance::typo3Major();
    }

    /**
     * The majors an answer has to hold on at once.
     *
     * One installation runs one version, and for a site project that is the
     * whole question. An extension is the other case. It declares
     * `"typo3/cms-core": "^13.4 || ^14.3"` and one codebase serves both, so a
     * statement bound to either major is one the author needs. The difference
     * between them is not noise, it is the constraint the code lives under. A
     * filter of such a repository to the installed major answers as if the
     * other one did not exist. What comes back then reads as drift: the file
     * kept for the older major, the interface not yet replaced, the suppressed
     * deprecation.
     *
     * What the caller states still wins and stays a single major, because
     * somebody who says "14" asks about 14. Only where the caller stated
     * nothing does the declaration decide. Where there is no declaration this
     * is the installed version, exactly as before.
     *
     * @return array<int, int>
     */
    public static function targets(?string $stated = null): array
    {
        $major = self::major($stated);
        if ($major !== null) {
            return [$major];
        }

        $declared = self::declared(Project::coreConstraint());
        if (count($declared) > 1) {
            return $declared;
        }

        $installed = Instance::typo3Major();

        return $installed === null ? [] : [$installed];
    }

    /**
     * The covered majors a Composer constraint admits.
     *
     * A constraint this cannot read yields nothing, and the caller falls back
     * to the installed version. A wrong range would be worse than the single
     * version this has always used.
     *
     * @return array<int, int>
     */
    public static function declared(?string $constraint): array
    {
        return array_values(array_filter(
            self::majors(),
            static fn(int $major): bool => self::admits($constraint, $major),
        ));
    }

    /**
     * Whether a Composer constraint admits any release of a major.
     *
     * Read with a question to the constraint about one major rather than a
     * parse into a range. The question is only ever "does this serve v13", and
     * the answer is the same for every form that admits any 13.x. Which majors
     * are worth a question is the caller's: the covered ones for
     * `typo3/cms-core`, the ones a Fluid engine could stand for in `bin/cli
     * components:check`.
     */
    public static function admits(?string $constraint, int $major): bool
    {
        $constraint = trim((string) $constraint);
        if ($constraint === '') {
            return false;
        }

        foreach (preg_split('/\s*\|\|?\s*/', $constraint) ?: [] as $alternative) {
            if (self::alternativeAdmits(trim($alternative), $major)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether one alternative of a constraint admits any release of a major.
     *
     * Every comparator in it has to, because within one alternative they
     * combine with and. `>=13.4 <15` is one alternative, not two.
     */
    private static function alternativeAdmits(string $alternative, int $major): bool
    {
        if ($alternative === '') {
            return false;
        }
        if ($alternative === '*') {
            return true;
        }

        // Composer takes a space between an operator and its version, and a
        // package in the wild writes it that way. `georgringer/news` requires
        // php `>= 8.1 < 8.5`. A split on whitespace first would read that as
        // four comparators, none of them a version. The constraint would answer
        // for no major at all.
        $alternative = (string) preg_replace('/(>=|<=|>|<|=|\^|~)\s+/', '$1', $alternative);

        $comparators = preg_split('/[\s,]+/', $alternative) ?: [];
        foreach ($comparators as $comparator) {
            if (!self::comparatorAdmits(trim($comparator), $major)) {
                return false;
            }
        }

        return $comparators !== [];
    }

    /** Whether one comparator admits any release of a major. */
    private static function comparatorAdmits(string $comparator, int $major): bool
    {
        if ($comparator === '') {
            return true;
        }
        if (preg_match(self::COMPARATOR, $comparator, $matches) !== 1) {
            return false;
        }

        $operator = strtolower($matches[1]);
        $stated = (int) $matches[2];
        $minor = $matches[3] ?? null;

        return match ($operator) {
            // Both pin the major: a caret above 1.0, and a tilde because the
            // digit it lets grow is never the first one. TYPO3 has no 0.x.
            '^', '~' => $major === $stated,
            '>=' => $major >= $stated,
            '>' => $major >= $stated,
            // An exclusive upper bound on x.0 excludes that major, and on any
            // later minor it still admits it. 14.0 serves <14.3.
            '<' => $minor === null || $minor === '0' ? $major < $stated : $major <= $stated,
            '<=' => $major <= $stated,
            default => $major === $stated,
        };
    }

    /**
     * The lowest version a Composer constraint admits, as `major.minor`, or
     * null where it names none.
     *
     * `admits()` one level down, and for a subject that is not TYPO3. A PHP
     * constraint stands against another and against the interpreter an
     * environment runs, and `^8.3` against `^8.2` is a difference the major
     * does not carry. The minor is the whole of the depth, because a floor
     * stands against a DDEV `php_version`. Null rather than a number wherever
     * the read is not certain. A constraint with no lower bound, an alternative
     * that states none, a comparator this does not read. `D-ANS-082` is wrong
     * if this states the wrong relation with the answer's authority.
     */
    public static function floor(?string $constraint): ?string
    {
        $constraint = trim((string) $constraint);
        if ($constraint === '') {
            return null;
        }

        $floor = null;
        foreach (preg_split('/\s*\|\|?\s*/', $constraint) ?: [] as $alternative) {
            $lowest = self::alternativeFloor(trim($alternative));
            // An alternative that admits anything below is what the whole
            // constraint admits, so there is no floor left to name.
            if ($lowest === null) {
                return null;
            }
            $floor = $floor === null || version_compare($lowest, $floor, '<') ? $lowest : $floor;
        }

        return $floor;
    }

    /**
     * The lowest version one alternative admits.
     *
     * Its comparators combine with and, so the floor is the highest lower bound
     * among them. `>=7.1 <9.0` starts at 7.1, and the upper bound beside it
     * states no floor of its own.
     */
    private static function alternativeFloor(string $alternative): ?string
    {
        if ($alternative === '' || $alternative === '*') {
            return null;
        }
        $alternative = (string) preg_replace('/(>=|<=|>|<|=|\^|~)\s+/', '$1', $alternative);

        $floor = null;
        foreach (preg_split('/[\s,]+/', $alternative) ?: [] as $comparator) {
            $comparator = trim($comparator);
            if ($comparator === '') {
                continue;
            }
            if (preg_match(self::COMPARATOR, $comparator, $matches) !== 1) {
                return null;
            }
            // An upper bound reads and states nothing about the floor.
            if (strtolower($matches[1]) === '<' || strtolower($matches[1]) === '<=') {
                continue;
            }

            $minor = $matches[3] ?? '';
            $lowest = $matches[2] . '.' . (ctype_digit($minor) ? $minor : '0');
            $floor = $floor === null || version_compare($lowest, $floor, '>') ? $lowest : $floor;
        }

        return $floor;
    }

    /** The major in a version string, or null when there is none to read. */
    public static function major(?string $version): ?int
    {
        $version = trim((string) $version);
        if (preg_match('/^v?(\d+)/', $version, $matches) !== 1) {
            return null;
        }

        return (int) $matches[1];
    }

    /**
     * Whether a statement bound to [since, until] holds on the target version.
     *
     * Without a target nothing filters out. The caller gets the range instead,
     * which is the honest answer when nobody said which version this is for.
     */
    public static function holds(?int $since, ?int $until, ?int $target): bool
    {
        if ($target === null) {
            return true;
        }

        return ($since === null || $target >= $since) && ($until === null || $target <= $until);
    }

    /**
     * How a bound statement says what binds it, or an empty string when nothing
     * does.
     *
     * Rendered beside the statement rather than woven into it, so the sentence
     * stays the same sentence on every version it holds for.
     */
    public static function label(?int $since, ?int $until): string
    {
        if ($since === null && $until === null) {
            return '';
        }
        if ($until === null) {
            return sprintf('TYPO3 v%d and newer', $since);
        }
        if ($since === null) {
            return sprintf('up to TYPO3 v%d', $until);
        }
        if ($since === $until) {
            return sprintf('TYPO3 v%d only', $since);
        }

        return sprintf('TYPO3 v%d to v%d', $since, $until);
    }

    /**
     * The branch that covers a major, as a pointer at what to verify against.
     */
    public static function branch(int $major): ?string
    {
        foreach (self::covered() as $entry) {
            if ($entry['major'] === $major) {
                return $entry['branch'];
            }
        }

        return null;
    }
}
