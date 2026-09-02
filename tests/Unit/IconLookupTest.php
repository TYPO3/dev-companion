<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Icons;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Knowledge\Coverage;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * Where the identifiers this tool answers with may be used.
 *
 * The registry is the backend's. An answer that does not say so is usable in a
 * frontend template, where none of it resolves.
 */
final class IconLookupTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Instance::ROOT_VARIABLE);
        Instance::discoverFrom(null);
        Icons::forget();
        Typo3Cli::forget();
        Typo3Runtime::forget();
    }

    #[Requirement('R-KNW-010')]
    #[Test]
    #[DataProvider('theThreeShapesAnIconAnswerTakes')]
    public function everyAnswerSaysTheIdentifiersAreTheBackendRegistrys(string $query): void
    {
        Instance::discoverFrom($this->installationWithItsOwnIcon());

        $result = Registry::call('typo3_icon_lookup', ['query' => $query]);

        self::assertStringContainsString('backend icon registry', $result->text);
        self::assertStringContainsString('backend icon registry', (string) ($result->data['scope'] ?? ''));
    }

    /** @return array<string, array{0: string}> */
    public static function theThreeShapesAnIconAnswerTakes(): array
    {
        return [
            'a hit' => ['acme-product'],
            'a miss' => ['quantumflux'],
            'the browsing answer, with no query at all' => [''],
        ];
    }

    #[Requirement('R-KNW-010')]
    #[Test]
    public function theRoutingEntrySendsCallersThereForBackendWorkOnly(): void
    {
        // "About to reference an icon identifier" read as if it held for any
        // icon in any context, which is where the wrong use came from.
        $entries = array_values(array_filter(
            Coverage::read()['routing'],
            static fn(array $entry): bool => $entry['call'] === 'typo3_icon_lookup'
        ));

        self::assertCount(1, $entries);
        self::assertStringContainsString('backend', $entries[0]['when']);
    }

    /**
     * A ranked list matched on one word is not an answer to three of them.
     *
     * A concept query for "paw animal pet" came back as eleven icons the
     * caller's own extensions register, every one of them on the substring
     * "animal", and the session read that as the tool answering by substring
     * and stopped asking it concept questions — `D-ANS-139`.
     */
    #[Decision('D-ANS-139')]
    #[Test]
    public function aConceptQuerySaysWhichOfItsWordsReachedNothing(): void
    {
        Instance::discoverFrom($this->installationWithItsOwnIcon());

        $partly = Registry::call('typo3_icon_lookup', ['query' => 'paw edit pet']);
        self::assertSame(
            [
                ['term' => 'paw', 'matchCount' => 0],
                ['term' => 'edit', 'matchCount' => 1],
                ['term' => 'pet', 'matchCount' => 0],
            ],
            $partly->data['terms'],
        );
        self::assertStringContainsString('No registered identifier carries "paw" or "pet"', $partly->text);

        // A miss says it too: the words are the whole of what there was to say.
        $nothing = Registry::call('typo3_icon_lookup', ['query' => 'paw pet']);
        self::assertSame(0, $nothing->data['matchCount']);
        self::assertSame(
            [['term' => 'paw', 'matchCount' => 0], ['term' => 'pet', 'matchCount' => 0]],
            $nothing->data['terms'],
        );

        // An identifier validation is a different question and carries none.
        self::assertSame([], Registry::call('typo3_icon_lookup', ['query' => 'actions-open'])->data['terms']);
    }

    #[Requirement('R-KNW-038')]
    #[Test]
    public function aMissingIdentifierHasNoMatchesEvenWhenRelatedIconsExist(): void
    {
        Instance::discoverFrom($this->installationWithItsOwnIcon());

        $categoryOnly = Registry::call('typo3_icon_lookup', [
            'query' => 'actions-definitely-does-not-exist',
        ]);
        self::assertSame(0, $categoryOnly->data['matchCount']);
        self::assertSame(0, $categoryOnly->data['suggestionCount']);

        $missing = Registry::call('typo3_icon_lookup', [
            'query' => 'actions-open-definitely-does-not-exist',
        ]);

        self::assertFalse($missing->data['exactMatch']);
        self::assertSame(0, $missing->data['matchCount']);
        self::assertGreaterThan(0, $missing->data['suggestionCount']);
        self::assertStringContainsString('suggestions, not the answer', $missing->text);

        $exact = Registry::call('typo3_icon_lookup', ['query' => 'actions-open']);
        self::assertTrue($exact->data['exactMatch']);
        self::assertSame(1, $exact->data['matchCount']);
    }

    /**
     * Several identifiers are one call, and each keeps its own verdict.
     *
     * The cost this answers is ours: the initialize instructions say to
     * validate every identifier before emitting it, so three icons in one
     * template were three round trips — `D-ANS-078`.
     */
    #[Decision('D-ANS-078')]
    #[Test]
    public function severalIdentifiersAreAnsweredOneByOneInOneCall(): void
    {
        Instance::discoverFrom($this->installationWithItsOwnIcon());

        $result = Registry::call('typo3_icon_lookup', [
            'identifiers' => ['actions-open', 'actions-open-definitely-does-not-exist', 'acme-product'],
        ]);

        self::assertSame(
            ['actions-open', 'actions-open-definitely-does-not-exist', 'acme-product'],
            array_column($result->data['validated'], 'identifier'),
            'in the order they were passed',
        );
        self::assertSame([true, false, true], array_column($result->data['validated'], 'registered'));
        self::assertSame(2, $result->data['matchCount']);
        self::assertFalse($result->data['exactMatch'], 'one that is not registered makes the answer no');
        self::assertSame([], $result->data['icons'], 'a validation is not a list of matches');
    }

    #[Test]
    public function onlyTheIdentifierThatMissedIsOfferedNeighbours(): void
    {
        // Neighbours of a correct identifier are noise; neighbours of a wrong
        // one are the next step. One reported answer carried 22 of them behind
        // a name that was already right.
        Instance::discoverFrom($this->installationWithItsOwnIcon());

        $validated = Registry::call('typo3_icon_lookup', [
            'identifiers' => ['actions-open', 'actions-open-definitely-does-not-exist'],
        ])->data['validated'];

        self::assertSame([], $validated[0]['suggestions']);
        self::assertNotSame([], $validated[1]['suggestions']);
        self::assertNotContains(
            'actions-open-definitely-does-not-exist',
            $validated[1]['suggestions'],
            'what is missing is never its own suggestion',
        );
    }

    #[Requirement('R-DIS-019')]
    #[Test]
    public function aRegistryReadFromTheFilesSaysThatInTheAnswerItself(): void
    {
        // The registry the installation assembles is the one that knows what a
        // package builds in a loop. This project has no console to boot it
        // with, so the files are read — and an answer that does not say so is
        // read as the whole registry by a review that then reports icons as
        // unregistered because it could not see them.
        Instance::discoverFrom($this->installationWithItsOwnIcon());

        $result = Registry::call('typo3_icon_lookup', ['query' => 'acme-product']);

        self::assertSame('packages', $result->data['answeredBy']);
        self::assertStringContainsString('read from the package files', $result->text);
        self::assertStringContainsString('has no TYPO3 console', $result->text, 'the reason travels with it');
        self::assertStringContainsString('builds in a loop', $result->text);
        // The answer itself still stands: what was read is right, not complete.
        self::assertTrue($result->data['exactMatch']);
    }

    #[Decision('D-DIS-011')]
    #[Test]
    public function anIdentifierRegisteredSinceTheLastCallIsFound(): void
    {
        // The registry is read once per call, not once per process. A caller
        // registers an icon and asks about it in the same session, and a
        // reading kept from before that edit answers that it is not registered
        // — which is the one answer this tool exists to prevent — `D-DIS-011`.
        $root = $this->installationWithItsOwnIcon();
        Instance::discoverFrom($root);

        $before = Registry::call('typo3_icon_lookup', ['query' => 'acme-teaser']);
        file_put_contents(
            $root . '/packages/my_sitepackage/Configuration/Icons.php',
            "<?php\nreturn ['acme-teaser' => ['provider' => 'x', 'source' => 'y']];\n"
        );
        $after = Registry::call('typo3_icon_lookup', ['query' => 'acme-teaser']);

        self::assertFalse($before->data['exactMatch'] ?? false);
        self::assertTrue($after->data['exactMatch'] ?? false);
    }

    /** A Composer project whose own extension registers an icon. */
    private function installationWithItsOwnIcon(): string
    {
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        mkdir($extension . '/Configuration', 0o777, true);
        file_put_contents(
            $extension . '/Configuration/Icons.php',
            "<?php\nreturn [\n"
            . "    'acme-product' => ['provider' => 'x', 'source' => 'y'],\n"
            . "    'actions-open' => ['provider' => 'x', 'source' => 'y'],\n"
            . "    'actions-close' => ['provider' => 'x', 'source' => 'y'],\n"
            . "];\n"
        );
        Icons::forget();

        return $root;
    }
}
