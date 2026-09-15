<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Manual\CoreChangelog;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * Which field of a changelog entry a query matches.
 *
 * The answer prints the title stated inside the file and the matcher never saw
 * it. So a caller could read `Deprecation: #46770 - Deprecate
 * LocalImageProcessor::getTemporaryImageWithText` off an answer and not ask for
 * it — `D-ANS-030`. Where that read happens is `D-ANS-041`.
 */
final class ChangelogTest extends TestCase
{
    use TemporaryInstallation;

    /** The entry the reported miss was after, as its file states it. */
    private const DEPRECATION_46770 = [
        '7.1',
        'Deprecation-46770-LocalImageProcessorGraphicalFunctions',
        'Deprecation: #46770 - Deprecate LocalImageProcessor::getTemporaryImageWithText',
    ];

    /** One whose stated title carries words its file name spells nowhere. */
    private const FEATURE_2 = ['13.0', 'Feature-2-ImagingRework', 'Feature: #2 - Rework of the graphical functions'];

    /**
     * Nothing here reaches docs.typo3.org. The changelog lookup reads the
     * versions above the installed major from the manual. A unit test that let
     * it would measure the host — `R-COD-003`.
     */
    #[Before]
    public function sealTheManual(): void
    {
        CoreChangelog::useReader(static fn(string $url): ?string => null);
    }

    #[After]
    public function forgetTheInstance(): void
    {
        CoreChangelog::useReader(null);
        putenv(Instance::ROOT_VARIABLE);
        Instance::discoverFrom(null);
    }

    /**
     * The file name carries the class and the title stated inside the file
     * carries the method. A scan over file names alone reaches neither —
     * `D-ANS-041`.
     */
    #[Decision('D-ANS-030')]
    #[Decision('D-ANS-041')]
    #[Test]
    public function aMethodNameOnlyTheStatedTitleSpellsReachesTheEntry(): void
    {
        $this->changelog([self::DEPRECATION_46770, self::FEATURE_2]);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'getTemporaryImageWithText']);

        self::assertSame(1, $result->data['matchCount']);
        self::assertSame(
            'EXT:core/Documentation/Changelog/7.1/Deprecation-46770-LocalImageProcessorGraphicalFunctions.rst',
            $result->data['entries'][0]['file']
        );
        self::assertSame(
            'Deprecate LocalImageProcessor::getTemporaryImageWithText',
            $result->data['entries'][0]['title']
        );
    }

    /**
     * The read costs 818 ms cold against the 48 ms the names cost. So it buys
     * an answer where there was none, and the lookup spends it on no answer
     * there already is. What that gives up is the entry a hit leaves out —
     * `D-ANS-041`.
     */
    #[Decision('D-ANS-041')]
    #[Test]
    public function theTitlesAreReadOnlyWhereTheFileNamesCarryNothing(): void
    {
        $this->changelog([self::DEPRECATION_46770, self::FEATURE_2]);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'graphical functions']);

        self::assertSame(1, $result->data['matchCount'], 'the entry whose name carries both words, and it alone');
        self::assertSame('46770', $result->data['entries'][0]['issue']);
    }

    /**
     * The per-word counts a miss offers come from the titles the search read.
     * So the word that reaches nothing is the one the caller drops —
     * `D-ANS-041`.
     */
    #[Decision('D-ANS-041')]
    #[Test]
    public function aMissCountsEachWordOverTheTitlesItSearched(): void
    {
        $this->changelog([self::DEPRECATION_46770, self::FEATURE_2]);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'getTemporaryImageWithText quantumflux']);

        self::assertSame(0, $result->data['matchCount']);
        self::assertStringContainsString('"gettemporaryimagewithtext" reaches 1 entry', $result->text);
        self::assertStringNotContainsString('"quantumflux" reaches', $result->text);
    }

    /**
     * A changelog of entries given as version, file name and the title line the
     * file states, in the installation this server then reads.
     *
     * @param array<int, array{0: string, 1: string, 2: string}> $entries
     */
    private function changelog(array $entries): void
    {
        $root = $this->coreCheckout();
        foreach ($entries as [$version, $name, $title]) {
            $directory = $root . '/typo3/sysext/core/Documentation/Changelog/' . $version;
            if (!is_dir($directory)) {
                mkdir($directory, 0o777, true);
            }
            file_put_contents(
                $directory . '/' . $name . '.rst',
                ".. include:: /Includes.rst.txt\n\n"
                . str_repeat('=', strlen($title)) . "\n" . $title . "\n" . str_repeat('=', strlen($title)) . "\n\n"
                . "Description\n===========\n\nThe change this entry is about.\n"
            );
        }
        Instance::discoverFrom($root);
    }
}
