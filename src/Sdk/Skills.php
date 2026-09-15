<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Sdk;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use TYPO3\DevCompanion\Knowledge\Coverage;
use TYPO3\DevCompanion\Knowledge\Scope;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Server\Installer;

/**
 * The published task skills, as the resource surface offers them.
 *
 * They are here rather than in `Knowledge\` because this is the only thing that
 * reads a skill body. `Server\Installer` copies the directory and never opens
 * it, and no tool answers from one. What a client picks up is the workflow
 * itself. For a client that never ran the install this resource is the one
 * route there is (`R-ANS-022`). The offer is `Installer::skills()` and never
 * the directory listing, so a skill the list leaves out does not come from here
 * either, `D-SKL-013`.
 */
final class Skills
{
    /**
     * What a client picks up, said in front of the skill's own description.
     *
     * A resource list is a list of pages everywhere else, and this one is not.
     * The file is an order of steps for the agent that does the work. That is
     * the confusion the sentence stands against, which is the one place a line
     * on what something is not belongs.
     */
    private const WHAT_IT_IS = 'A workflow an agent follows while doing the task, not a page to read. '
        . 'The references it sends you to are served beside it, one URI each.';

    /**
     * The order every task starts in, which is a file in a skill only once the
     * skill goes out.
     */
    private const BASE = 'references/base.md';

    /** @return array<int, array{id: string, title: string, summary: string, path: string}> */
    public static function skills(): array
    {
        return array_map(static fn(string $id): array => [
            'id' => $id,
            'title' => self::title($id),
            'summary' => self::summary($id),
            'path' => self::published($id),
        ], Installer::skills());
    }

    /**
     * The name the skill declares, which is what a client that loads it knows
     * it by.
     */
    public static function name(string $id): string
    {
        return self::field($id, 'name');
    }

    /**
     * What a client reads to understand the offer.
     *
     * The skill's own front matter says what the workflow is for, and its
     * author already keeps it true to the body it introduces. So it serves as
     * it is rather than gets a second description here. What joins it is what
     * the front matter cannot say: that this is a workflow rather than a page,
     * and whom its steps oblige.
     */
    public static function description(string $id): ?string
    {
        $covered = self::covered($id);
        if ($covered === null) {
            return null;
        }

        return self::WHAT_IT_IS . ' ' . self::field($id, 'description') . ' ' . (self::isCoreOnly($id)
            ? "The TYPO3 core's own workflow, which does not transfer to extension or site work."
            : "Not the core's own workflow: it is followed in whichever repository the work is in.");
    }

    /**
     * Whether a skill is the core repository's own, read off the coverage for
     * the reason `Documents::isCoreOnly` is. `knowledge/server-scope.json`
     * already says what a topic's answers are worth outside the core, and a
     * second list here could disagree with it.
     */
    public static function isCoreOnly(string $id): bool
    {
        $covered = self::covered($id);

        return $covered === null || $covered['scope'] === Scope::Core;
    }

    public static function read(string $id): string
    {
        return (string) file_get_contents(self::published($id));
    }

    /**
     * What a skill's body links to, at the paths it links to them by.
     *
     * `references/base.md` is in every one of them and is a file in no skill.
     * `Installer` writes it at publication from `skills/base.md` (`D-SKL-001`).
     * The same copy is what comes from here, so the resource is the file the
     * client would have had after an install.
     *
     * @return array<int, string>
     */
    public static function references(string $id): array
    {
        $references = [self::BASE];

        $directory = dirname(self::published($id)) . '/references';
        if (is_dir($directory)) {
            foreach (Finder::create()->files()->in($directory)->depth(0)->name('*.md')->sortByName() as $file) {
                $references[] = 'references/' . $file->getFilename();
            }
        }

        return $references;
    }

    /** One reference of a skill, named the way its body links to it. */
    public static function reference(string $id, string $reference): string
    {
        if (!in_array($reference, self::references($id), true)) {
            throw new \RuntimeException(sprintf('Unknown reference of the %s skill: %s', $id, $reference));
        }

        return (string) file_get_contents($reference === self::BASE
            ? Paths::root() . '/skills/base.md'
            : dirname(self::published($id)) . '/' . $reference);
    }

    /** The body of a skill this server publishes, and of nothing else. */
    private static function published(string $id): string
    {
        if (!in_array($id, Installer::skills(), true)) {
            throw new \RuntimeException(sprintf('Unknown task skill: %s', $id));
        }

        return Paths::root() . '/skills/' . $id . '/SKILL.md';
    }

    /**
     * The covered topic that names this skill as its source, where one does.
     *
     * @return array{topic: string, depth: string, tools: array<int, string>, source: string, scope: Scope}|null
     */
    private static function covered(string $id): ?array
    {
        foreach (Coverage::read()['covers'] as $entry) {
            if (str_contains($entry['source'], ResourceHandler::skillUri($id))) {
                return $entry;
            }
        }

        return null;
    }

    private static function title(string $id): string
    {
        foreach (preg_split('/\R/', self::read($id)) ?: [] as $line) {
            if (str_starts_with($line, '# ')) {
                return trim(substr($line, 2));
            }
        }

        return $id;
    }

    /**
     * The task the skill declares itself for, as YAML readers receive it.
     *
     * This stands beside the title because the documentation index needs both.
     * A read from the front matter keeps that catalog on the declaration the
     * skill itself owns.
     */
    private static function summary(string $id): string
    {
        if (preg_match('/\A---\R(.*?)\R---\R/s', self::read($id), $block) !== 1) {
            throw new \RuntimeException(sprintf('The %s skill has no front matter.', $id));
        }

        try {
            $matter = Yaml::parse($block[1]);
        } catch (ParseException $exception) {
            throw new \RuntimeException(sprintf(
                'The %s skill has invalid front matter: %s',
                $id,
                $exception->getMessage(),
            ));
        }

        $summary = is_array($matter) ? ($matter['description'] ?? null) : null;
        if (!is_string($summary) || $summary === '') {
            throw new \RuntimeException(sprintf('The %s skill declares no description.', $id));
        }

        return $summary;
    }

    /**
     * One field of the front matter, which stands on one line in all of them.
     * The block comes out first, so a line of the body that opens with the same
     * word does not read as the declaration.
     */
    private static function field(string $id, string $field): string
    {
        if (preg_match('/\A---\R(.*?)\R---\R/s', self::read($id), $block) !== 1) {
            throw new \RuntimeException(sprintf('The %s skill has no front matter.', $id));
        }
        if (preg_match('/^' . $field . ':[ \t]*(\S.*)$/m', $block[1], $matches) !== 1) {
            throw new \RuntimeException(sprintf('The %s skill declares no %s.', $id, $field));
        }

        return trim($matches[1]);
    }
}
