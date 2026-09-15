<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use TYPO3\DevCompanion\Feedback\Channel;

/**
 * The feedback that arrived from outside this repository and are still open,
 * each with whether a todo already names it.
 *
 * That flag is the whole difference between a feedback that waits and one
 * somebody has taken on. `bin/cli todo:check` reports the feedback no todo
 * answers for, and `feedback:list` marks the ones that have. Two reads of one
 * relation, kept here so they cannot disagree about it.
 */
final class OpenFeedback
{
    /** Every feedback there is: the size of the directory is what this is about. */
    private const ALL = PHP_INT_MAX;

    /**
     * Every open feedback, oldest first.
     *
     * @return array<int, array{file: string, date: string, category: string, status: string, model: string, directory: string, tool: string, tools: array<int, string>, title: string, closedBy: ?array{commit: string, date: string, subject: string}, judged: bool}>
     */
    public static function all(): array
    {
        $queued = Todo::serves();

        return array_map(
            static fn(array $feedback): array => $feedback + ['judged' => in_array($feedback['file'], $queued, true)],
            array_reverse(Channel::all('open', null, self::ALL)),
        );
    }
}
