<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge;

use TYPO3\DevCompanion\Paths;

/**
 * Which TYPO3 branches take a patch today, and what each of the others is.
 *
 * The `Releases:` trailer names branches rather than versions, and a core
 * checkout supplies no list of them. `git branch -r` reaches back to
 * `TYPO3_3-6` and says nothing about which of them still has maintenance,
 * `D-ANS-058`. This is not `knowledge/versions.json`, which declares which
 * majors this knowledge base is for, and the two lists may differ. The store
 * holds the windows rather than the state they imply, so only a branch that did
 * not exist on the file's day is unknown.
 */
final class ReleaseLines
{
    /** The line every core change goes against first. */
    public const DEVELOPMENT = 'development';

    /** In regular support: a patch pushed to Gerrit goes out here. */
    public const MAINTAINED = 'maintained';

    /** Out of regular support. Releases come from the ELTS partners, not from this branch. */
    public const ELTS = 'elts';

    /** Past its ELTS window: nothing goes out for it at all. */
    public const ENDED = 'ended';

    /** Not a line this file carries, which is a question rather than a verdict. */
    public const UNKNOWN = 'unknown';

    /**
     * What a branch named in a `Releases:` trailer is on the given day. The day
     * defaults to today because every one of these states is a date that
     * passes.
     */
    public static function state(string $branch, ?\DateTimeImmutable $on = null): string
    {
        $data = self::read();
        $branch = trim($branch);
        $on = ($on ?? new \DateTimeImmutable())->format('Y-m-d');

        if ($branch === $data['development']['branch']) {
            return self::DEVELOPMENT;
        }

        foreach ($data['released'] as $line) {
            if ($line['branch'] !== $branch) {
                continue;
            }
            if ($on <= $line['maintainedUntil']) {
                return self::MAINTAINED;
            }

            return $on <= $line['eltsUntil'] ? self::ELTS : self::ENDED;
        }

        return self::UNKNOWN;
    }

    /**
     * The branches a patch can go out on, newest first.
     *
     * What a caller gets where it named none, and what a finding names as the
     * answer. A check that only refuses is one the session has to leave the
     * server to satisfy, which is what `D-ANS-058` counted the trailers to
     * avoid.
     *
     * @return array<int, string>
     */
    public static function releasable(?\DateTimeImmutable $on = null): array
    {
        $data = self::read();
        $branches = [$data['development']['branch']];
        foreach ($data['released'] as $line) {
            if (self::state($line['branch'], $on) === self::MAINTAINED) {
                $branches[] = $line['branch'];
            }
        }

        return $branches;
    }

    /**
     * The branches an ordinary change goes out on: the development line and the
     * one release line back from it.
     *
     * Not the same question as `releasable()`, and a read of the two as one is
     * what `D-ANS-073` corrects. An older maintained line takes a patch the
     * severity earns rather than one that is merely present there. A trailer
     * that names it anyway asks a merger to cherry-pick onto a line the change
     * was never meant for.
     *
     * @return array<int, string>
     */
    public static function ordinary(?\DateTimeImmutable $on = null): array
    {
        return array_slice(self::releasable($on), 0, 2);
    }

    /**
     * What is the case with one branch, as a sentence a check can carry.
     *
     * The dates are in it because the trailer is the author's claim and this is
     * only the part of it a list can settle. A line that left regular support
     * last week reads as an oversight, and one that ended in 2021 as a typo.
     */
    public static function describe(string $branch, ?\DateTimeImmutable $on = null): string
    {
        $line = self::released($branch);
        if ($line === null) {
            return $branch;
        }

        return match (self::state($branch, $on)) {
            self::MAINTAINED => sprintf('%s is in regular support until %s', $branch, $line['maintainedUntil']),
            self::ELTS => sprintf(
                '%s left regular support on %s and is ELTS until %s, which the ELTS partners release rather '
                    . 'than this branch',
                $branch,
                $line['maintainedUntil'],
                $line['eltsUntil'],
            ),
            default => sprintf('%s reached the end of its ELTS window on %s', $branch, $line['eltsUntil']),
        };
    }

    /**
     * The day a branch leaves regular support, or null where this file carries
     * no such line. That is the development branch, and anything it never heard
     * of.
     *
     * The date as data beside `describe()`'s sentence. An answer with it in
     * prose alone makes every reader of the data half write the regex that
     * takes it out again.
     */
    public static function maintainedUntil(string $branch): ?string
    {
        return self::released($branch)['maintainedUntil'] ?? null;
    }

    /**
     * Where the list came from, so a caller can read it again rather than trust
     * this one.
     */
    public static function source(): string
    {
        return self::read()['source'];
    }

    /**
     * The day of the read, which is what a caller weighs an unknown branch
     * against.
     */
    public static function readAt(): string
    {
        return self::read()['readAt'];
    }

    /** @return array{major: int, branch: string, maintainedUntil: string, eltsUntil: string}|null */
    private static function released(string $branch): ?array
    {
        foreach (self::read()['released'] as $line) {
            if ($line['branch'] === $branch) {
                return $line;
            }
        }

        return null;
    }

    /**
     * @return array{
     *     source: string,
     *     readAt: string,
     *     development: array{major: int, branch: string},
     *     released: array<int, array{major: int, branch: string, maintainedUntil: string, eltsUntil: string}>
     * }
     */
    private static function read(): array
    {
        /** @var array<string, mixed>|null $decoded */
        $decoded = json_decode((string) file_get_contents(Paths::knowledgeFile('release-lines.json')), true);
        if (!is_array($decoded) || !isset($decoded['development'], $decoded['released'])) {
            throw new \RuntimeException('Invalid release-lines.json');
        }

        /** @var array{source: string, readAt: string, development: array{major: int, branch: string}, released: array<int, array{major: int, branch: string, maintainedUntil: string, eltsUntil: string}>} $decoded */
        return $decoded;
    }
}
