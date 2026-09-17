<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Manual\Manuals;
use TYPO3\DevCompanion\Manual\Permalink;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * Whether a docs.typo3.org permalink identifier resolves, what it lands on, and
 * which identifier replaces an old documentation URL.
 */
final class PermalinkLookup extends ReadOnlyTool
{
    /** The inventories come from docs.typo3.org. */
    protected const OPEN_WORLD = true;

    public static function name(): string
    {
        return 'typo3_permalink_lookup';
    }

    public static function title(): string
    {
        return 'Validate documentation permalinks';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Network];
    }

    public static function description(): string
    {
        return 'Validate docs.typo3.org permalink identifiers and turn old documentation URLs into the identifiers '
            . 'that replace them. Pass identifiers such as t3coreapi:extension-scanner, or a system extension by '
            . 'its Composer package name in either form the core writes, typo3/cms-felogin:start. Pass '
            . 'urls such as https://docs.typo3.org/m/typo3/reference-tca/11.5/en-us/Columns/Properties/OnChange.html. '
            . 'Pass as many at a time as you hold. One manual inventory answers every identifier of that manual. So a '
            . 'sweep over a whole checkout costs a call per manual rather than a request per link. Each identifier '
            . 'comes back with the page and anchor it reaches. It comes with the other forms that reach the same '
            . 'target, and which of them the manual declares. Each URL comes back with the identifiers that point '
            . 'at it, or the near names where the page is gone. It says which branch answered, because the host '
            . 'redirects a manual it has no branch for to main and does not say so. This answers where a link points and not what a '
            . 'manual says about a subject — for the prose, ask typo3_documentation_lookup.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'identifiers' => [
                    'type' => 'array',
                    'items' => ['type' => 'string', 'minLength' => 1],
                    'description' => 'Permalink identifiers written <shortcode>:<name>, as they appear in code — '
                        . 't3coreapi:extension-scanner, t3tca:columns-onchange, typo3/cms-felogin:start. An '
                        . '@<branch> at the end pins that identifier to a branch of its own and overrides targetVersion for it. '
                        . 'A call carries identifiers, urls, or both.',
                ],
                'urls' => [
                    'type' => 'array',
                    'items' => ['type' => 'string', 'minLength' => 1],
                    'description' => 'docs.typo3.org page URLs to read the other way, anchor included where there is '
                        . 'one. They resolve at targetVersion rather than at the branch they name, because a link '
                        . 'up for replacement asks what the identifier is now. A call carries identifiers, '
                        . 'urls, or both.',
                ],
                'targetVersion' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Covered TYPO3 version whose manuals answer, for example "13.4" or "14". There '
                        . 'is no fallback to another release.',
                ],
            ],
            'required' => ['targetVersion'],
            'additionalProperties' => false,
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'status' => Schema::answerStatus(),
            'targetVersion' => Schema::string('The documentation release the manuals answered at.'),
            'source' => Schema::string('The external documentation host.'),
            'identifiers' => Schema::listOf(Schema::object([
                'identifier' => Schema::string('The identifier as the call passed it, @<branch> included.'),
                'shortcode' => Schema::string('The manual it names, in the form that manual declares.'),
                'name' => Schema::string('The name inside that manual, in the form its inventory carries.'),
                'branch' => Schema::string('The branch the lookup ran on: its own @<branch>, else targetVersion.'),
                'resolved' => ['type' => 'boolean', 'description' => 'Whether the manual registers this name. False '
                    . 'is a legitimate answer and reason says which of the three it is. The identifier is not '
                    . 'written as one, this server knows no manual for the shortcode, or the manual has no such name.'],
                'manual' => Schema::nullableString('The manual on docs.typo3.org, as its path names it.'),
                'manualTitle' => Schema::nullableString('What that manual calls itself.'),
                'url' => Schema::nullableString('The page and anchor this identifier reaches. Null on a miss.'),
                'page' => Schema::nullableString('The same URL without the anchor.'),
                'anchor' => Schema::nullableString('The fragment on that page, absent for a name that is the page.'),
                'roles' => Schema::listOf(Schema::string(), 'What this name is in the manual, in Sphinx\'s own '
                    . 'vocabulary. std:label is a section, std:confval a configuration value, php:class and '
                    . 'php:method API, std:console:command a command. std:doc is not among them — a page is '
                    . 'not addressable as a permalink.'),
                'alsoKnownAs' => Schema::listOf(Schema::object([
                    'name' => Schema::string(),
                    'roles' => Schema::listOf(Schema::string()),
                ], ['name', 'roles']), 'Every other name that reaches the same target. A configuration value carries two: '
                    . 'the std:confval the manual declares, and the std:label Sphinx generates from its anchor with '
                    . 'a confval- prefix. An old anchor survives here beside the current one.'),
                'preferred' => Schema::nullableString('Which of the equivalent names to write. The std:confval one '
                    . 'where the target has one, because that is what the manual declares and the rest derives '
                    . 'from it. Otherwise the name asked for.'),
                'answeredBranch' => Schema::nullableString('The branch the manual that answered says it is, read off '
                    . 'its inventory. Different from branch means the host has no such branch and served main '
                    . 'instead, so the identifier is not proven for the release asked about. Null where no manual '
                    . 'answered.'),
                'reason' => Schema::nullableString('Why it did not resolve. Null on a hit.'),
            ], ['identifier', 'shortcode', 'name', 'branch', 'resolved', 'manual', 'manualTitle', 'url', 'page',
                'anchor', 'roles', 'alsoKnownAs', 'preferred', 'answeredBranch', 'reason'])),
            'urls' => Schema::listOf(Schema::object([
                'url' => Schema::string('The URL as the call passed it.'),
                'shortcode' => Schema::nullableString('The manual it belongs to, as a permalink names it.'),
                'manual' => Schema::nullableString('That manual on docs.typo3.org, as its path names it.'),
                'branch' => Schema::string('The branch its manual answered at, which is targetVersion.'),
                'urlBranch' => Schema::nullableString('The branch the URL itself names, which is what a link left in '
                    . 'code points at.'),
                'page' => Schema::nullableString('The page inside the manual.'),
                'anchor' => Schema::nullableString('The fragment the URL names, if any.'),
                'identifiers' => Schema::listOf(Schema::object([
                    'name' => Schema::string(),
                    'roles' => Schema::listOf(Schema::string()),
                ], ['name', 'roles']), 'The names that reach this exact target, which is what replaces the URL. Empty '
                    . 'means the manual at this version has no such page or anchor.'),
                'nearest' => Schema::listOf(Schema::object([
                    'name' => Schema::string(),
                    'roles' => Schema::listOf(Schema::string()),
                    'url' => Schema::string(),
                ], ['name', 'roles', 'url']), 'Names with the words of the URL, best first, where nothing reaches '
                    . 'it exactly. They are candidates a reader picks from, not the answer: a manual that moved a '
                    . 'subject leaves nothing behind that says where it went.'),
                'answeredBranch' => Schema::nullableString('The branch the manual that answered says it is. Null '
                    . 'where none answered.'),
                'reason' => Schema::nullableString('Why nothing exact came back. Null where something did.'),
            ], ['url', 'shortcode', 'manual', 'branch', 'urlBranch', 'page', 'anchor', 'identifiers', 'nearest',
                'answeredBranch', 'reason'])),
            'unavailable' => Schema::unavailable([
                'version-not-covered' => 'the release asked about is outside the ones this server knows the manuals '
                    . 'for, and a second call changes nothing.',
                'source-not-answering' => 'docs.typo3.org did not answer this time, and the same call may answer '
                    . 'the next.',
            ]),
        ], ['status', 'targetVersion', 'source', 'identifiers', 'urls', 'unavailable']);
    }

    public static function answer(array $args): ToolResult
    {
        $identifiers = self::strings($args['identifiers'] ?? null);
        $urls = self::strings($args['urls'] ?? null);
        $statedVersion = is_string($args['targetVersion'] ?? null) ? trim($args['targetVersion']) : '';
        if ($statedVersion === '' || ($identifiers === [] && $urls === [])) {
            throw new \InvalidArgumentException('Pass targetVersion and at least one of identifiers or urls');
        }

        $major = Versions::major($statedVersion);
        $branch = $major === null ? null : Versions::branch($major);
        if ($branch === null) {
            $answer = [
                'status' => 'unavailable',
                'targetVersion' => $statedVersion,
                'source' => Manuals::HOST,
                'identifiers' => [],
                'urls' => [],
                'unavailable' => [
                    'cause' => 'version-not-covered',
                    'reason' => sprintf(
                        'TYPO3 %s is outside the covered versions: %s.',
                        $statedVersion,
                        implode(', ', array_column(Versions::covered(), 'branch')),
                    ),
                ],
            ];
        } else {
            $answer = (new Permalink())->lookup($identifiers, $urls, $branch);
        }

        return ToolResult::create(implode("\n", self::lines($answer)), $answer);
    }

    /**
     * @param array<string, mixed> $answer
     * @return array<int, string>
     */
    private static function lines(array $answer): array
    {
        $lines = [
            sprintf('docs.typo3.org permalinks for TYPO3 %s.', $answer['targetVersion']),
            'Source: ' . $answer['source'],
        ];
        if ($answer['unavailable'] !== null) {
            $lines[] = 'Could not answer: ' . $answer['unavailable']['reason'];

            return $lines;
        }

        foreach ($answer['identifiers'] as $entry) {
            $lines[] = '';
            if (!$entry['resolved']) {
                $lines[] = sprintf('## %s — does not resolve', $entry['identifier']);
                $lines[] = (string) $entry['reason'];
                continue;
            }

            $lines[] = sprintf('## %s — resolves', $entry['identifier']);
            $lines[] = sprintf('%s · %s · %s', $entry['manualTitle'], $entry['answeredBranch'], $entry['url']);
            $lines[] = sprintf('Registered as: %s', implode(', ', $entry['roles']));
            if ($entry['alsoKnownAs'] !== []) {
                $lines[] = sprintf(
                    'The same target is also %s. Write %s.',
                    implode(', ', array_map(self::spelling(...), $entry['alsoKnownAs'])),
                    $entry['shortcode'] . ':' . $entry['preferred'],
                );
            }
            $lines = [...$lines, ...self::branchNotice($entry['branch'], $entry['answeredBranch'])];
        }

        foreach ($answer['urls'] as $entry) {
            $lines[] = '';
            $lines[] = sprintf('## %s', $entry['url']);
            if ($entry['identifiers'] === []) {
                $lines[] = (string) $entry['reason'];
                foreach ($entry['nearest'] as $near) {
                    $lines[] = sprintf('- %s:%s — %s', $entry['shortcode'], self::spelling($near), $near['url']);
                }
            } else {
                $lines[] = 'Replaced by:';
                foreach ($entry['identifiers'] as $identifier) {
                    $lines[] = sprintf('- %s:%s', $entry['shortcode'], self::spelling($identifier));
                }
            }
            $lines = [...$lines, ...self::branchNotice($entry['branch'], $entry['answeredBranch'])];
        }

        return $lines;
    }

    /**
     * What the caller asked for against what the host served.
     *
     * The host answers a branch it does not publish with a redirect to `main`
     * and a 200. So an identifier can come back as resolved for a release whose
     * manual nobody reached. That is what `R-DOC-001` forbids to pass on, and
     * the inventory's own version line is where it shows.
     *
     * @return array<int, string>
     */
    private static function branchNotice(string $asked, ?string $answered): array
    {
        if ($answered === null || $answered === $asked) {
            return [];
        }

        return [sprintf(
            'Read on %s: this manual has no %s branch, so nothing here is proven for %s.',
            $answered,
            $asked,
            $asked,
        )];
    }

    /**
     * One name with what the manual registers it as. That says whether a
     * reference to it is a link to a section, to a configuration value or to
     * API.
     *
     * @param array{name: string, roles: array<int, string>} $spelling
     */
    private static function spelling(array $spelling): string
    {
        return $spelling['name'] . ' (' . implode(', ', $spelling['roles']) . ')';
    }

    /**
     * @return list<string>
     */
    private static function strings(mixed $values): array
    {
        if (!is_array($values)) {
            return [];
        }

        return array_values(array_filter(
            array_map(trim(...), array_filter($values, is_string(...))),
            static fn(string $value): bool => $value !== '',
        ));
    }
}
