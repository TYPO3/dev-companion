<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Manual;

use TYPO3\DevCompanion\Paths;

/**
 * The manuals docs.typo3.org publishes, and the shortcode of each.
 *
 * The shortcode is what a permalink names a manual with, and nothing on the
 * host publishes the set of them. Each manual declares its own, in the
 * `interlink-shortcode` of its `guides.xml` and again as
 * `data-interlink-shortcode` in every page it renders. So the named ones are a
 * list here, kept in `knowledge/manuals.json` and verified per entry against
 * the host by `bin/cli manuals:check` — `D-ANS-120`.
 *
 * The manual of a Composer package is not one of them and needs no entry. Its
 * shortcode is the package name, and the host publishes the manual under that
 * name in the `c` collection.
 */
final class Manuals
{
    /** Where the host publishes every manual this reads. */
    public const HOST = 'https://docs.typo3.org';

    /**
     * The collection that holds the manual of a package the core ships.
     *
     * `p` is the one beside it and holds everybody else's, which this does not
     * reach. Those manuals have versions per the extension's own releases
     * rather than TYPO3's, so a TYPO3 version would select the wrong branch of
     * one — `D-ANS-120`.
     */
    private const PACKAGE_COLLECTION = 'c';

    /**
     * A shortcode that names a package the core ships, in either spelling.
     *
     * Both reach the same manual. `typo3/cms-felogin` is what the manual
     * declares and `typo3-cms-felogin` is what the permalink route also
     * accepts, and the core writes both into its own source.
     */
    private const PACKAGE_SHORTCODE = '#^typo3[/-]((?:cms|theme)-[a-z0-9-]+)$#';

    /** @var list<array{shortcode: string, collection: string, document: string, title: string, searched: bool}>|null */
    private static ?array $manuals = null;

    /**
     * What leaves this process for a manual read, and the seam a unit test
     * takes instead.
     *
     * One seam for the whole source. `Documentation` and `Permalink` both build
     * their own instance from a tool, so neither has anywhere else to take a
     * transport. Two seams would let a test silence one reader and reach the
     * host through the other — `R-COD-003`.
     *
     * @var (\Closure(string): ?string)|null
     */
    private static ?\Closure $transport = null;

    /**
     * What a test hands in, so nothing it drives reaches docs.typo3.org. Null
     * puts the host back.
     *
     * What this holds goes with it. Another reader is another host, and an
     * inventory kept across the two would answer for a manual nobody read.
     *
     * @param (\Closure(string): ?string)|null $reader
     */
    public static function useReader(?\Closure $reader): void
    {
        self::$transport = $reader;
        Inventory::forget();
    }

    /** @return (\Closure(string): ?string)|null */
    public static function reader(): ?\Closure
    {
        return self::$transport;
    }

    /** @return list<array{shortcode: string, collection: string, document: string, title: string, searched: bool}> */
    public static function all(): array
    {
        if (self::$manuals !== null) {
            return self::$manuals;
        }

        $decoded = json_decode((string) file_get_contents(Paths::knowledgeFile('manuals.json')), true);
        if (!is_array($decoded) || !isset($decoded['manuals']) || !is_array($decoded['manuals']) || $decoded['manuals'] === []) {
            throw new \RuntimeException('Invalid manuals.json');
        }

        return self::$manuals = array_map(static fn(array $entry): array => [
            'shortcode' => (string) $entry['shortcode'],
            'collection' => (string) $entry['collection'],
            'document' => (string) $entry['document'],
            'title' => (string) $entry['title'],
            'searched' => (bool) ($entry['searched'] ?? false),
        ], array_values($decoded['manuals']));
    }

    /**
     * The manuals `typo3_documentation_lookup` searches, keyed by document.
     *
     * Four rather than all of them, and each of the last two for a reason. The
     * TCA reference is its own manual and TYPO3 Explained does not repeat it.
     * So a question about `inline`, `foreign_field` or a column type reached
     * two manuals that describe everything around TCA and never TCA itself. No
     * manual above documents a ViewHelper either, so `f:if` got whatever prose
     * carries the word rather than `Global/If.html` (`D-ANS-023`).
     *
     * @return array<string, array{title: string, collection: string}>
     */
    public static function searched(): array
    {
        $searched = [];
        foreach (self::all() as $manual) {
            if ($manual['searched']) {
                $searched[$manual['document']] = ['title' => $manual['title'], 'collection' => $manual['collection']];
            }
        }

        return $searched;
    }

    /**
     * The manual a shortcode names, listed or derived, or null for one nothing
     * here places.
     *
     * The permalink route reads a shortcode case-insensitively, so this does
     * too: `T3COREAPI:OPCACHE-SAVE-COMMENTS` resolves on the host exactly as
     * its lowercase form does.
     *
     * A derived entry carries the package name as its title. What a package's
     * manual calls itself is in the inventory this is about to read, and there
     * is no second place to keep it.
     *
     * @return array{shortcode: string, collection: string, document: string, title: string, searched: bool}|null
     */
    public static function byShortcode(string $shortcode): ?array
    {
        $shortcode = mb_strtolower(trim($shortcode));
        foreach (self::all() as $manual) {
            if ($manual['shortcode'] === $shortcode) {
                return $manual;
            }
        }

        if (preg_match(self::PACKAGE_SHORTCODE, $shortcode, $package) !== 1) {
            return null;
        }
        $name = 'typo3/' . $package[1];

        return [
            'shortcode' => $name,
            'collection' => self::PACKAGE_COLLECTION,
            'document' => $name,
            'title' => $name,
            'searched' => false,
        ];
    }

    /** Every shortcode the list names, in the order of the list. */
    public static function shortcodes(): string
    {
        return implode(', ', array_column(self::all(), 'shortcode'));
    }

    /** Where the host publishes one manual, at one branch. */
    public static function base(string $collection, string $document, string $branch): string
    {
        return sprintf('%s/%s/%s/%s/en-us/', self::HOST, $collection, $document, rawurlencode($branch));
    }
}
