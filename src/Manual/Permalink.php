<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Manual;

use TYPO3\DevCompanion\Http\Fetch;

/**
 * A docs.typo3.org permalink identifier, resolved out of the inventory the
 * manual it names publishes.
 *
 * `https://docs.typo3.org/permalink/<shortcode>:<name>` answers 307 for a name
 * a manual registers and 404 for one it does not. That is one round trip per
 * question, and it reports a version fallback as a hit. The table behind the
 * redirect is the inventory this server already reads. So the validation of an
 * identifier, its resolution to a page and its recovery from an old URL are
 * three readings of one artefact. `D-ANS-118`.
 */
final class Permalink
{
    /**
     * The role of an inventory object that is a page rather than something on
     * one, and the one role the permalink route does not accept.
     *
     * `t3coreapi:ApiOverview/Assets/Index` is a page of TYPO3 Explained and
     * answers 404, while every other role of the same inventory resolves —
     * `D-ANS-119`. It is what `Documentation` reads and nothing else here does.
     */
    private const PAGE = 'std:doc';

    /**
     * A configuration value, whose own name is the identifier the manual
     * declares.
     *
     * Sphinx also writes a label of the same target named after the anchor, so
     * `columns-onchange` and `confval-columns-onchange` both resolve. Which one
     * a patch should use is the review question the session that reported it
     * had to invent a rule for. This is the rule: the declared one.
     */
    private const DECLARED = 'std:confval';

    /** The branch an identifier may pin for itself, after the name. */
    private const PINNED = '/^(?<name>.*)@(?<branch>[^@]+)$/';

    /** A manual URL of this host: the collection, the two-part document, the branch and the page. */
    private const MANUAL_URL = '#^/(?<collection>[a-z]+)/(?<document>[^/]+/[^/]+)/(?<branch>[^/]+)/en-us/(?<page>.*)$#';

    /** How many near names the answer gives a URL that resolves to nothing. */
    private const NEAREST = 5;

    /** What a page path contributes nothing as: the extension, and the name every chapter index carries. */
    private const NOT_A_WORD = ['index', 'html'];

    private readonly Inventory $inventory;

    /** @param (\Closure(string): ?string)|null $fetch */
    public function __construct(?\Closure $fetch = null)
    {
        $this->inventory = new Inventory(new Fetch($fetch ?? Manuals::reader()));
    }

    /**
     * @param list<string> $identifiers
     * @param list<string> $urls
     * @return array{
     *   status: 'answered'|'empty'|'unavailable',
     *   targetVersion: string,
     *   source: string,
     *   identifiers: list<array{identifier: string, shortcode: string, name: string, branch: string, resolved: bool, manual: string|null, manualTitle: string|null, url: string|null, page: string|null, anchor: string|null, roles: list<string>, alsoKnownAs: list<array{name: string, roles: list<string>}>, preferred: string|null, answeredBranch: string|null, reason: string|null}>,
     *   urls: list<array{url: string, shortcode: string|null, manual: string|null, branch: string, urlBranch: string|null, page: string|null, anchor: string|null, identifiers: list<array{name: string, roles: list<string>}>, nearest: list<array{name: string, roles: list<string>, url: string}>, answeredBranch: string|null, reason: string|null}>,
     *   unavailable: array{cause: string, reason: string}|null
     * }
     */
    public function lookup(array $identifiers, array $urls, string $branch): array
    {
        $answered = [];
        foreach ($identifiers as $identifier) {
            $answered[] = $this->identifier($identifier, $branch);
        }
        $reversed = [];
        foreach ($urls as $url) {
            $reversed[] = $this->url($url, $branch);
        }

        $read = array_merge(
            array_column($answered, 'answeredBranch'),
            array_column($reversed, 'answeredBranch'),
        );
        if (array_filter($read, static fn(?string $answeredBranch): bool => $answeredBranch !== null) === []) {
            return [
                'status' => 'unavailable',
                'targetVersion' => $branch,
                'source' => Manuals::HOST,
                'identifiers' => $answered,
                'urls' => $reversed,
                'unavailable' => [
                    'cause' => 'source-not-answering',
                    'reason' => 'No manual named by this call could be read from docs.typo3.org.',
                ],
            ];
        }

        $hits = count(array_filter($answered, static fn(array $entry): bool => $entry['resolved']))
            + count(array_filter($reversed, static fn(array $entry): bool => $entry['identifiers'] !== []));

        return [
            'status' => $hits === 0 ? 'empty' : 'answered',
            'targetVersion' => $branch,
            'source' => Manuals::HOST,
            'identifiers' => $answered,
            'urls' => $reversed,
            'unavailable' => null,
        ];
    }

    /**
     * One identifier, as the permalink route would read it.
     *
     * @return array{identifier: string, shortcode: string, name: string, branch: string, resolved: bool, manual: string|null, manualTitle: string|null, url: string|null, page: string|null, anchor: string|null, roles: list<string>, alsoKnownAs: list<array{name: string, roles: list<string>}>, preferred: string|null, answeredBranch: string|null, reason: string|null}
     */
    private function identifier(string $identifier, string $branch): array
    {
        $identifier = trim($identifier);
        $written = $identifier;
        if (preg_match(self::PINNED, $identifier, $pinned) === 1) {
            $identifier = $pinned['name'];
            $branch = $pinned['branch'];
        }

        $split = explode(':', $identifier, 2);
        $shortcode = $split[0];
        $name = $split[1] ?? '';
        $miss = [
            'identifier' => $written,
            'shortcode' => $shortcode,
            'name' => $name,
            'branch' => $branch,
            'resolved' => false,
            'manual' => null,
            'manualTitle' => null,
            'url' => null,
            'page' => null,
            'anchor' => null,
            'roles' => [],
            'alsoKnownAs' => [],
            'preferred' => null,
            'answeredBranch' => null,
            'reason' => null,
        ];

        if ($shortcode === '' || $name === '') {
            $miss['reason'] = 'A permalink identifier is written <shortcode>:<name>, optionally followed by @<branch>.';

            return $miss;
        }

        $manual = Manuals::byShortcode($shortcode);
        if ($manual === null) {
            $miss['reason'] = sprintf(
                'No manual is known for the shortcode "%s". The named ones are %s, and a system extension is '
                . 'addressed by its Composer package name, for example typo3/cms-felogin or typo3-cms-felogin.',
                $shortcode,
                Manuals::shortcodes(),
            );

            return $miss;
        }

        $base = Manuals::base($manual['collection'], $manual['document'], $branch);
        $inventory = $this->inventory->of($base);
        if ($inventory === null) {
            $miss['manual'] = $manual['document'];
            $miss['manualTitle'] = $manual['title'];
            $miss['reason'] = sprintf('%s publishes no manual at %s that could be read.', Manuals::HOST, $base);

            return $miss;
        }

        $miss['manual'] = $manual['document'];
        $miss['manualTitle'] = $inventory['title'] !== '' ? $inventory['title'] : $manual['title'];
        $miss['answeredBranch'] = $inventory['branch'];
        $base = Manuals::base($manual['collection'], $manual['document'], $inventory['branch']);

        $objects = array_values(array_filter(
            $inventory['objects'],
            static fn(array $object): bool => $object['role'] !== self::PAGE,
        ));
        $matched = array_values(array_filter(
            $objects,
            static fn(array $object): bool => mb_strtolower($object['name']) === mb_strtolower($name),
        ));
        if ($matched === []) {
            $miss['reason'] = sprintf(
                '%s registers no "%s" on %s.',
                $miss['manualTitle'],
                $name,
                $inventory['branch'],
            );

            return $miss;
        }

        $uri = $matched[0]['uri'];
        [$page, $anchor] = array_pad(explode('#', $uri, 2), 2, null);
        $sameTarget = array_values(array_filter(
            $objects,
            static fn(array $object): bool => $object['uri'] === $uri,
        ));
        $declared = array_column(array_filter(
            $sameTarget,
            static fn(array $object): bool => $object['role'] === self::DECLARED,
        ), 'name');
        $equivalent = array_values(array_filter(
            self::spellings($sameTarget),
            static fn(array $spelling): bool => mb_strtolower($spelling['name']) !== mb_strtolower($name),
        ));

        return [
            'identifier' => $written,
            'shortcode' => $manual['shortcode'],
            'name' => $matched[0]['name'],
            'branch' => $branch,
            'resolved' => true,
            'manual' => $manual['document'],
            'manualTitle' => $miss['manualTitle'],
            'url' => $base . $uri,
            'page' => $base . (string) $page,
            'anchor' => $anchor,
            'roles' => array_values(array_unique(array_column($matched, 'role'))),
            'alsoKnownAs' => $equivalent,
            'preferred' => $declared[0] ?? $matched[0]['name'],
            'answeredBranch' => $inventory['branch'],
            'reason' => null,
        ];
    }

    /**
     * The same table read the other way: the identifiers that reach one URL.
     *
     * The lookup reads the URL for the manual it belongs to and the page it
     * names. It then asks the inventory at the branch the caller works on
     * rather than at the one the URL points at. That is the question a link
     * under replacement asks: what the identifier is here, not what it was on
     * 11.5.
     *
     * @return array{url: string, shortcode: string|null, manual: string|null, branch: string, urlBranch: string|null, page: string|null, anchor: string|null, identifiers: list<array{name: string, roles: list<string>}>, nearest: list<array{name: string, roles: list<string>, url: string}>, answeredBranch: string|null, reason: string|null}
     */
    private function url(string $url, string $branch): array
    {
        $url = trim($url);
        $miss = [
            'url' => $url,
            'shortcode' => null,
            'manual' => null,
            'branch' => $branch,
            'urlBranch' => null,
            'page' => null,
            'anchor' => null,
            'identifiers' => [],
            'nearest' => [],
            'answeredBranch' => null,
            'reason' => null,
        ];

        $path = str_starts_with($url, Manuals::HOST . '/') ? substr($url, strlen(Manuals::HOST)) : null;
        if ($path === null || preg_match(self::MANUAL_URL, explode('?', $path, 2)[0], $parts) !== 1) {
            $miss['reason'] = sprintf(
                'Not a manual URL of this host. The form read here is %s/<collection>/<vendor>/<manual>/<branch>/en-us/<page>.html.',
                Manuals::HOST,
            );

            return $miss;
        }

        [$page, $anchor] = array_pad(explode('#', $parts['page'], 2), 2, null);
        $miss['manual'] = $parts['document'];
        $miss['urlBranch'] = $parts['branch'];
        $miss['page'] = $page;
        $miss['anchor'] = $anchor;

        $shortcode = self::shortcodeOf($parts['collection'], $parts['document']);
        if ($shortcode === null) {
            $miss['reason'] = sprintf(
                'No shortcode is known for the manual %s/%s, so no permalink can be written for it.',
                $parts['collection'],
                $parts['document'],
            );

            return $miss;
        }
        $miss['shortcode'] = $shortcode;

        $base = Manuals::base($parts['collection'], $parts['document'], $branch);
        $inventory = $this->inventory->of($base);
        if ($inventory === null) {
            $miss['reason'] = sprintf('%s publishes no manual at %s that could be read.', Manuals::HOST, $base);

            return $miss;
        }
        $miss['answeredBranch'] = $inventory['branch'];
        $base = Manuals::base($parts['collection'], $parts['document'], $inventory['branch']);

        $objects = array_values(array_filter(
            $inventory['objects'],
            static fn(array $object): bool => $object['role'] !== self::PAGE,
        ));
        $wanted = $anchor === null ? $page : $page . '#' . $anchor;
        $exact = array_values(array_filter($objects, static function (array $object) use ($anchor, $wanted): bool {
            return ($anchor === null ? explode('#', $object['uri'], 2)[0] : $object['uri']) === $wanted;
        }));
        if ($exact !== []) {
            $miss['identifiers'] = self::spellings($exact);

            return $miss;
        }

        $miss['nearest'] = self::nearest($objects, (string) $page, $anchor, $base);
        $miss['reason'] = sprintf(
            '%s at %s has no %s. The near names below are what its inventory carries.',
            $inventory['title'] !== '' ? $inventory['title'] : $parts['document'],
            $inventory['branch'],
            $anchor === null ? 'page ' . $page : 'target ' . $wanted,
        );

        return $miss;
    }

    /**
     * The manual's own shortcode where the list names it, and the package name
     * where the collection says the manual is a package's own.
     */
    private static function shortcodeOf(string $collection, string $document): ?string
    {
        foreach (Manuals::all() as $manual) {
            if ($manual['collection'] === $collection && $manual['document'] === $document) {
                return $manual['shortcode'];
            }
        }

        return Manuals::byShortcode($document) === null ? null : $document;
    }

    /**
     * One entry per name, with every role the manual registers that name under.
     *
     * An identifier is a name and the role is what the manual made of it. So a
     * name that is both a section and a PHP class is one entry and not two. A
     * caller that reads a list of names has no way to tell a repetition from a
     * second identifier.
     *
     * @param list<array{name: string, role: string, uri: string, display: string}> $objects
     * @return list<array{name: string, roles: list<string>}>
     */
    private static function spellings(array $objects): array
    {
        $roles = [];
        foreach ($objects as $object) {
            $roles[$object['name']][$object['role']] = true;
        }

        $spellings = [];
        foreach ($roles as $name => $carried) {
            $spellings[] = ['name' => (string) $name, 'roles' => array_keys($carried)];
        }

        return $spellings;
    }

    /**
     * What an inventory carries that a dead URL is about.
     *
     * Nothing derives the page a manual moved a subject to from the URL it left
     * behind. The words of that URL are all a caller holds.
     * `Columns/Properties/OnChange.html` is gone from the TCA reference and
     * `columns-onchange` is what replaced it. So the lookup matches the
     * segments of the path against the names. The answer says these are near
     * rather than right — the reader picks.
     *
     * @param list<array{name: string, role: string, uri: string, display: string}> $objects
     * @return list<array{name: string, roles: list<string>, url: string}>
     */
    private static function nearest(array $objects, string $page, ?string $anchor, string $base): array
    {
        $words = [];
        foreach ([...explode('/', $page), (string) $anchor] as $segment) {
            $word = mb_strtolower((string) preg_replace('/\.html$/', '', $segment));
            if ($word !== '' && !in_array($word, self::NOT_A_WORD, true)) {
                $words[$word] = true;
            }
        }
        if ($words === []) {
            return [];
        }

        $scored = [];
        foreach ($objects as $object) {
            $name = mb_strtolower($object['name']);
            $score = 0;
            foreach (array_keys($words) as $word) {
                $score += str_contains($name, $word) ? 1 : 0;
            }
            if ($score === 0) {
                continue;
            }
            $scored[$object['name']] ??= ['score' => $score, 'uri' => $object['uri'], 'roles' => []];
            $scored[$object['name']]['roles'][$object['role']] = true;
        }

        // The best score first, and the shortest name inside one score. A name
        // that carries the words and nothing else is the subject, and a longer
        // one that carries them is a section of it.
        uksort($scored, static fn(string $left, string $right): int => [$scored[$right]['score'], mb_strlen($left)]
            <=> [$scored[$left]['score'], mb_strlen($right)]);

        $nearest = [];
        foreach (array_slice($scored, 0, self::NEAREST, true) as $name => $entry) {
            $nearest[] = [
                'name' => (string) $name,
                'roles' => array_keys($entry['roles']),
                'url' => $base . $entry['uri'],
            ];
        }

        return $nearest;
    }
}
