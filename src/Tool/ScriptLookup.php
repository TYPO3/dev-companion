<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Knowledge\Scope;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Result\Prose;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * The TYPO3 core's own scripts and commands, by task.
 */
final class ScriptLookup extends ReadOnlyTool
{
    /** The one document this tool answers from. */
    private const DOCUMENT = 'core/testing/scripts';

    public static function name(): string
    {
        return 'typo3_script_lookup';
    }

    public static function title(): string
    {
        return 'Find notes for core scripts';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Knowledge];
    }

    public static function description(): string
    {
        return 'Find notes for TYPO3 core scripts and commands. That is how you start Build/Scripts/runTests.sh and what it needs first, what an argument after -- reaches and which options one run takes. It is the commands per subject, and what the pre-commit hook does to a commit. They are the core checkout\'s own. A query that reads as a project or third-party extension gets the boundary as its answer instead of commands that do not exist there. Which suite a change needs, and what one of them does when it runs, is typo3_test_run_guide, which filters the suites by version. That is what it provisions, what it passes through, and which environment variables change it.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'task' => ['type' => 'string', 'minLength' => 1, 'description' => 'The TYPO3 core task, in English, for example unit tests, functional tests, CGL, npm, or dependency install.'],
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version the answer has to hold on, for example "13.4" or "14". The answer leaves out a section bound to another major. Defaults to every major this repository declares typo3/cms-core for, or to the installation this server started in. Where there is neither, every section comes back with the range it holds for.'],
            ],
            'required' => ['task'],
        ];
    }

    /**
     * The knowledge lookup shape plus the boundary, because every command in
     * the script note is a command in the core repository.
     */
    public static function outputSchema(): array
    {
        $schema = Schema::knowledgeLookup();
        $schema['properties']['scope'] = Schema::scope();
        $schema['required'][] = 'scope';

        return $schema;
    }

    public static function answer(array $args): ToolResult
    {
        $task = (string) ($args['task'] ?? '');
        $targets = Versions::targets(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);

        // Every command in these feedback runs in a core checkout. Handing them
        // to a repository that has none is the same mistake
        // typo3_test_run_guide used to make, and the same answer applies. This
        // tool answers about a task rather than about paths, so the call has
        // one scope.
        $scope = Scope::of('', $task);
        if ($scope->isOutsideTheCore()) {
            return ToolResult::create(
                Scope::OUTSIDE_CORE_NOTICE . ' The scripts these notes describe are the core checkout\'s own, so '
                . 'none is returned. What to run here is declared in this repository: its composer.json scripts, '
                . 'its package.json, its CI configuration.',
                ['query' => $task, 'matchCount' => 0, 'matches' => [], 'scope' => $scope->value],
            );
        }

        $results = Documents::search($task, [self::DOCUMENT], 6, $targets);

        if ($results !== []) {
            $text = Prose::sections($results, $scope->isOutsideTheCore());
            // Where nothing said which repository this is, the commands come
            // under their condition rather than as the answer.
            if (!Scope::isCoreWork([], $task)) {
                $text .= "\n\nThese commands run in a TYPO3 core checkout. In any other repository, what to run is "
                    . 'declared in its own composer.json, package.json and CI configuration.';
            }

            return ToolResult::create($text, [
                'query' => $task,
                'matchCount' => count($results),
                'matches' => Prose::records($results),
                'scope' => $scope->value,
            ]);
        }

        // Nothing about scripts matched. Say so, and route to the documents
        // that do cover the topic instead of an answer with the nearest script
        // prose.
        $message = sprintf(
            'No section of the TYPO3 core script notes matched "%s". They cover: %s.',
            $task,
            Prose::topics(self::DOCUMENT)
        );

        $elsewhere = Documents::search($task, [], 6, $targets);
        $titles = array_values(array_unique(array_map(
            static fn(array $result): string => $result['title'],
            $elsewhere
        )));
        if ($titles !== []) {
            $message .= sprintf(
                "\n\nOther knowledge documents do match this query — call typo3_rule_lookup for: %s.",
                implode(', ', $titles)
            );
        }

        return ToolResult::create($message, [
            'query' => $task,
            'matchCount' => 0,
            'matches' => [],
            'elsewhere' => $titles,
            'scope' => $scope->value,
        ]);
    }
}
