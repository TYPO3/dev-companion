<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Knowledge\Catalog\References;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * The worked examples the core ships, so "read X" can be the answer.
 */
final class ReferenceList extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_reference_list';
    }

    public static function title(): string
    {
        return 'List the core\'s worked examples';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Knowledge];
    }

    public static function description(): string
    {
        return 'List the worked examples the TYPO3 core ships of its own conventions, and what each one is a reference for. That is the theme extension, the styleguide, the Extbase fixture extension, the content element render, the browser test suite, the static analysis setup. Read one of these before you invent a layout or a test harness. They are the version-correct form of what a convention describes, and they pass today. Every hint here is a summary of one. Paths are relative to a core checkout; where the answer names a Composer package, an installation that has it holds the same files below vendor/.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version to list for, for example "13.4" or "14". The answer leaves out an example that branch does not have rather than qualifies it. Defaults to the version of the installation this server started in. Where there is none, every entry comes back with the range it exists on.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'targetVersion' => Schema::nullable(['type' => 'integer', 'description' => 'The TYPO3 major the list is for, stated by the caller or read from the installation. Null means every covered version is in it and each entry carries its own range.']),
            'matchCount' => Schema::integer('How many worked examples exist on the version asked about.'),
            'references' => Schema::listOf(Schema::object([
                'id' => Schema::string('Stable identifier of the example.'),
                'path' => Schema::string('Where it is, relative to the root of a core checkout.'),
                'package' => Schema::nullable(['type' => 'string', 'description' => 'The Composer package that ships it, so an installation can read it below vendor/. Null means it exists only in the core repository, as everything below Build/ does.']),
                'reference' => Schema::string('What it is a worked example of.'),
                'caveat' => Schema::nullable(['type' => 'string', 'description' => 'What not to conclude from it: that you read it rather than depend on it, or which part of it is the core\'s own. Null where there is nothing to warn about.']),
                'hint' => Schema::nullable(['type' => 'string', 'description' => 'The hint whose conventions it demonstrates, for typo3_hint_lookup. Null where no hint covers the subject yet, which is exactly when a read of the example is worth most.']),
                'since' => Schema::nullable(['type' => 'integer', 'description' => 'First covered major that has it. Null means every covered major does.']),
                'until' => Schema::nullable(['type' => 'integer', 'description' => 'Last covered major that has it. Null means the newest one still does.']),
                'existsOn' => Schema::string('The range in words, empty when every covered version has it.'),
            ], ['id', 'path', 'package', 'reference', 'caveat', 'hint', 'since', 'until', 'existsOn'])),
            'coveredVersions' => Schema::listOf(Schema::integer(), 'The TYPO3 majors this answer derives from.'),
        ], ['targetVersion', 'matchCount', 'references', 'coveredVersions']);
    }

    public static function answer(array $args): ToolResult
    {
        $target = Versions::target(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);
        $entries = References::on($target);

        $lines = [
            $target === null
                ? 'Worked examples in the TYPO3 core, with the versions each exists on.'
                : sprintf('Worked examples in the TYPO3 core, as TYPO3 v%d has them.', $target),
            'Paths are relative to a core checkout. Where none is at hand, they are also the paths in '
                . 'github.com/TYPO3/typo3 on the matching branch.',
            '',
        ];
        foreach ($entries as $entry) {
            $range = Versions::label($entry['since'], $entry['until']);
            $lines[] = '- ' . $entry['path'] . ($range === '' ? '' : ' — ' . $range);
            $lines[] = '  ' . $entry['reference'];
            if ($entry['caveat'] !== null) {
                $lines[] = '  ' . $entry['caveat'];
            }
            $lines[] = '  ' . ($entry['package'] === null
                // Build/ is the repository's own; a Composer installation has
                // none of it. A line that says so beats a caller's search of
                // vendor/ for a directory nobody published.
                ? 'Only in the core repository — no Composer package ships it.'
                : 'In an installation: vendor/' . $entry['package'] . '/, below the same path with the '
                    . 'typo3/sysext/<key>/ prefix removed.');
            if ($entry['hint'] !== null) {
                $lines[] = '  Conventions: typo3_hint_lookup id="' . $entry['hint'] . '"';
            }
        }

        return ToolResult::create(implode("\n", $lines), [
            'targetVersion' => $target,
            'matchCount' => count($entries),
            'references' => array_map(static fn(array $entry): array => [
                'id' => $entry['id'],
                'path' => $entry['path'],
                'package' => $entry['package'],
                'reference' => $entry['reference'],
                'caveat' => $entry['caveat'],
                'hint' => $entry['hint'],
                'since' => $entry['since'],
                'until' => $entry['until'],
                'existsOn' => Versions::label($entry['since'], $entry['until']),
            ], $entries),
            'coveredVersions' => Versions::majors(),
        ]);
    }
}
