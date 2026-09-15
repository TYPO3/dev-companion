<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Sdk\Skills;

/**
 * The catalog and readable workflows added to the published copy, rendered
 * from the same files the installer writes and the descriptions clients select
 * by.
 *
 * The explanatory head stays hand-written. The listing, workflow bodies and
 * references are the repeated facts. Kept by hand, a renamed, drafted or newly
 * published skill would read differently from what the next install puts into a
 * project.
 */
final class SkillSurface
{
    /** The line above the generated list, so the explanation survives a rewrite. */
    private const LISTING_STARTS = ".. The list and its pages are written into the published copy by\n"
        . "   ``bin/cli documentation:prepare``.\n";

    public static function directory(): string
    {
        return Paths::root() . '/documentation/usage/task-skills';
    }

    public static function index(): string
    {
        return self::directory() . '/readme.rst';
    }

    /**
     * Every wrapper and exact Markdown copy the site build adds now.
     *
     * @return array<string, string>
     */
    public static function files(): array
    {
        $files = [self::index() => self::indexPage()];
        foreach (Skills::skills() as $skill) {
            $id = $skill['id'];
            $files[self::skillFile($id)] = self::skillPage($skill);
            $files[self::skillMarkdownFile($id)] = Skills::read($id);
            foreach (Skills::references($id) as $reference) {
                $files[self::referenceMarkdownFile($id, $reference)] = Skills::reference($id, $reference);
            }
        }

        return $files;
    }

    /** The catalog head is prose; only the listing and its tree change. */
    private static function indexPage(): string
    {
        $contents = (string) file_get_contents(self::index());
        $start = strpos($contents, self::LISTING_STARTS);
        if ($start === false) {
            throw new \RuntimeException('The task-skill page has no generated-list marker.');
        }

        return substr($contents, 0, $start) . self::LISTING_STARTS . "\n" . self::listing();
    }

    /** One card per skill the ordinary install publishes, in publication order. */
    private static function listing(): string
    {
        $lines = ['.. grid:: wide', ''];
        foreach (Skills::skills() as $skill) {
            $body = Rst::INDENT . Rst::INDENT;
            $lines = [
                ...$lines,
                Rst::INDENT . '.. card:: ' . Rst::doc($skill['title'], $skill['id'] . '/index'),
                $body . ':label: Task skill',
                $body . ':action: Read workflow',
                '',
                $body . Rst::literal($skill['id']),
                '',
                Wrap::indented($skill['summary'], $body),
                '',
            ];
        }

        $tree = array_map(
            static fn(array $skill): string => Rst::INDENT . $skill['id'] . "/index\n",
            Skills::skills(),
        );

        return implode("\n", $lines) . "\n.. toctree::\n" . Rst::INDENT . ":hidden:\n\n"
            . implode('', $tree);
    }

    /** @param array{id: string, title: string, summary: string, path: string} $skill */
    private static function skillPage(array $skill): string
    {
        $id = $skill['id'];
        $references = Skills::references($id);
        $included = [];
        foreach ($references as $reference) {
            $included = [
                ...$included,
                '',
                ...Rst::heading(self::markdownTitle(Skills::reference($id, $reference)), 2),
                ...self::markdownInclude($reference),
            ];
        }

        return self::page([
            ...Rst::navigationTitle($skill['title']),
            ...Rst::heading($skill['title']),
            '**Skill:** ' . Rst::literal($id),
            '',
            Wrap::text($skill['summary']),
            '',
            ...Rst::heading('Markdown source', 1),
            ...self::markdownInclude('SKILL.md'),
            '',
            ...Rst::heading('References', 1),
            ...$included,
        ]);
    }

    /**
     * The copied file rather than a conversion of it. Indentation belongs to
     * the directive and the rendered code block drops it again, so every byte
     * of the Markdown shows as the skill carries it.
     *
     * @return list<string>
     */
    private static function markdownInclude(string $file): array
    {
        return [
            '.. literalinclude:: ' . $file,
            Rst::INDENT . ':language: markdown',
            '',
        ];
    }

    private static function markdownTitle(string $markdown): string
    {
        if (preg_match('/^#\s+(.+)$/m', $markdown, $match) !== 1) {
            throw new \RuntimeException('A published skill file has no title.');
        }

        return trim($match[1]);
    }

    private static function skillFile(string $id): string
    {
        return self::directory() . '/' . $id . '/readme.rst';
    }

    private static function skillMarkdownFile(string $id): string
    {
        return self::directory() . '/' . $id . '/SKILL.md';
    }

    private static function referenceMarkdownFile(string $id, string $reference): string
    {
        return self::directory() . '/' . $id . '/' . $reference;
    }

    /** @param list<string> $lines */
    private static function page(array $lines): string
    {
        return rtrim(Wrap::rst(implode("\n", $lines))) . "\n";
    }
}
