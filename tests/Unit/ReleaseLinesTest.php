<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Knowledge\ReleaseLines;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;

/**
 * Which branches take a patch, and what the list is worth on a day nobody read
 * it.
 *
 * The states are dates that pass rather than a status somebody typed, which is
 * what the fixed days here are for. The same file has to answer differently in
 * 2026 and in 2031 with no edit — `D-ANS-058`.
 */
final class ReleaseLinesTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function branches(): array
    {
        return [
            'the development line' => ['main', '2026-08-05', ReleaseLines::DEVELOPMENT],
            'in regular support' => ['13.4', '2026-08-05', ReleaseLines::MAINTAINED],
            'out of regular support, still ELTS' => ['12.4', '2026-08-05', ReleaseLines::ELTS],
            'the day regular support ends is still support' => ['12.4', '2026-04-30', ReleaseLines::MAINTAINED],
            'the day after is not' => ['12.4', '2026-05-01', ReleaseLines::ELTS],
            'past the ELTS window' => ['9.5', '2026-08-05', ReleaseLines::ENDED],
            'a line yet to leave regular support' => ['13.4', '2028-01-01', ReleaseLines::ELTS],
            'a sprint branch is not a line' => ['14.1', '2026-08-05', ReleaseLines::UNKNOWN],
            'nor is a branch that does not exist' => ['13.5', '2026-08-05', ReleaseLines::UNKNOWN],
        ];
    }

    #[Test]
    #[DataProvider('branches')]
    public function aBranchIsWhatTheDayItIsAskedOnMakesIt(string $branch, string $on, string $expected): void
    {
        self::assertSame($expected, ReleaseLines::state($branch, new \DateTimeImmutable($on)));
    }

    /**
     * The list ages in one direction. What a stored window says will happen
     * happens with no second read of the file. Only a branch created after the
     * read is absent from it — `D-ANS-058`.
     */
    #[Decision('D-ANS-058')]
    #[Test]
    public function theLinesTakingAPatchNarrowAsTheirWindowsClose(): void
    {
        self::assertSame(
            ['main', '14.3', '13.4'],
            ReleaseLines::releasable(new \DateTimeImmutable('2026-08-05')),
        );
        self::assertSame(
            ['main', '14.3'],
            ReleaseLines::releasable(new \DateTimeImmutable('2028-01-01')),
        );
    }

    /**
     * A finding says which of the two it is, because each gets a different
     * answer. An ELTS line has releases somebody else makes, and an ended one
     * has none at all.
     */
    #[Test]
    public function theDescriptionNamesTheDateThatDecidedIt(): void
    {
        $on = new \DateTimeImmutable('2026-08-05');

        self::assertStringContainsString('ELTS until 2030-04-30', ReleaseLines::describe('12.4', $on));
        self::assertStringContainsString('2026-04-30', ReleaseLines::describe('12.4', $on));
        self::assertStringContainsString('end of its ELTS window on 2025-09-30', ReleaseLines::describe('9.5', $on));
    }

    /** A branch nothing knows about comes back as written, not dressed up. */
    #[Test]
    public function anUnknownBranchIsDescribedAsItself(): void
    {
        self::assertSame('15.0', ReleaseLines::describe('15.0', new \DateTimeImmutable('2026-08-05')));
    }

    /**
     * The date `describe()` puts in a sentence, as data. `R-ANS-035` answers
     * with both halves, and a reader of the second one would otherwise write
     * the regex that takes it back out.
     */
    #[Requirement('R-ANS-035')]
    #[Test]
    public function theDayRegularSupportEndsIsReadableWithoutTheSentence(): void
    {
        self::assertSame('2027-12-31', ReleaseLines::maintainedUntil('13.4'));
        // The development line and a branch this file never heard of: neither
        // has such a day, and null is what says so.
        self::assertNull(ReleaseLines::maintainedUntil('main'));
        self::assertNull(ReleaseLines::maintainedUntil('13.5'));
    }

    /**
     * The two are what a caller weighs an unknown branch against, so neither
     * may be absent without a word. `D-ANS-058` rests on a list a caller can
     * read again.
     */
    #[Test]
    public function theListSaysWhereItCameFromAndWhenItWasRead(): void
    {
        self::assertStringStartsWith('https://', ReleaseLines::source());
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', ReleaseLines::readAt());
    }
}
