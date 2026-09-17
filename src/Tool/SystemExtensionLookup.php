<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Knowledge\Catalog\SystemExtensions;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * Whether an extension is part of the core, and since when.
 *
 * Answered from the catalog rather than from the installation at hand, because
 * the case that matters is the extension that is not installed. That is when
 * the question comes, and an answer from memory is how a community package gets
 * cited as evidence of what the core does.
 */
final class SystemExtensionLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_system_extension_lookup';
    }

    public static function title(): string
    {
        return 'List the system extensions';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Knowledge];
    }

    public static function description(): string
    {
        return 'Answer whether an extension is part of the TYPO3 core, and on which versions. It lists the system extensions of every covered TYPO3 line, by extension key and Composer package name. Each comes with what it is for and the range the core ships it on. Independent of any installation, which is the point. The question comes up for a package that is not installed, and otherwise memory answers "is this core". A miss means the name is not a system extension on the covered versions, never that it does not exist.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'An extension key ("theme_camino"), a Composer package name ("typo3/cms-impexp"), or a word from what it does ("redirects"). Omit to list everything the core ships.'],
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version to answer for, for example "13.4" or "14". Restricts the answer to what that line ships. Defaults to the version of the installation this server started in. Where there is none, every entry comes back with the range the core ships it on.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'query' => Schema::string(),
            'targetVersion' => Schema::nullable(['type' => 'integer', 'description' => 'The TYPO3 major the answer is for, stated by the caller or read from the installation. Null means every covered version is in the answer and each entry carries its own range.']),
            'matchCount' => Schema::integer('How many system extensions matched. Zero means the name is not one of them on the versions asked about, not that no such package exists.'),
            'extensions' => Schema::listOf(Schema::object([
                'key' => Schema::string('The extension key, which is the name of the directory below typo3/sysext.'),
                'package' => Schema::string('The Composer package name to require it by, where an installation does not have it already.'),
                'description' => Schema::string('What it is for.'),
                'since' => Schema::nullable(['type' => 'integer', 'description' => 'First covered major that ships it. Null means every covered major does.']),
                'until' => Schema::nullable(['type' => 'integer', 'description' => 'Last covered major that ships it. Null means it is still shipped on the newest one.']),
                'shippedOn' => Schema::string('The range in words, empty when the core ships it everywhere this knowledge base reaches.'),
            ], ['key', 'package', 'description', 'since', 'until', 'shippedOn'])),
            'coveredVersions' => Schema::listOf(Schema::integer(), 'The TYPO3 majors this answer derives from.'),
        ], ['query', 'matchCount', 'extensions', 'coveredVersions']);
    }

    public static function answer(array $args): ToolResult
    {
        $query = trim((string) ($args['query'] ?? ''));
        $target = Versions::target(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);
        $matches = SystemExtensions::find($query, $target);

        $covered = implode(', ', array_map(
            static fn(array $version): string => $version['branch'],
            Versions::covered(),
        ));
        $shipped = $target === null
            ? sprintf('The core ships these on %s.', $covered)
            : sprintf('The core ships these on TYPO3 v%d.', $target);

        if ($matches === []) {
            return ToolResult::create(
                sprintf(
                    '"%s" is not a system extension on %s. That is an answer about the core, not about the '
                    . 'package: it may well exist on Packagist or in the TER, where it is a third-party extension '
                    . 'with its own release cycle. Call this without a query for everything the core does ship.',
                    $query,
                    $target === null ? $covered : 'TYPO3 v' . $target,
                ),
                [
                    'query' => $query,
                    'targetVersion' => $target,
                    'matchCount' => 0,
                    'extensions' => [],
                    'coveredVersions' => Versions::majors(),
                ],
            );
        }

        $lines = [$shipped, ''];
        foreach ($matches as $entry) {
            $range = Versions::label($entry['since'], $entry['until']);
            $lines[] = sprintf(
                '- %s (%s)%s',
                $entry['key'],
                $entry['package'],
                $range === '' ? '' : ' — ' . $range,
            );
            if ($entry['description'] !== '') {
                $lines[] = '  ' . $entry['description'];
            }
        }

        return ToolResult::create(implode("\n", $lines), [
            'query' => $query,
            'targetVersion' => $target,
            'matchCount' => count($matches),
            'extensions' => array_map(static fn(array $entry): array => [
                'key' => $entry['key'],
                'package' => $entry['package'],
                'description' => $entry['description'],
                'since' => $entry['since'],
                'until' => $entry['until'],
                'shippedOn' => Versions::label($entry['since'], $entry['until']),
            ], $matches),
            'coveredVersions' => Versions::majors(),
        ]);
    }
}
