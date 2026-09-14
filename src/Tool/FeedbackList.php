<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Feedback\Channel;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * The improvement feedback recorded so far, so it can be worked off — and read
 * back once it was.
 */
final class FeedbackList extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_feedback_list';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Checkout];
    }

    public static function description(): string
    {
        return 'List the feedback typo3_feedback_record recorded, newest first, so a session can work them off. Filter by status, by category, or by the tool a feedback is about. The archive keeps a feedback a session worked off, so status="closed" answers "what became of what I reported". That is the feedback as it arrived, plus the commit that closed it.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'status' => ['type' => 'string', 'enum' => ['open', 'closed', 'all'], 'default' => 'open', 'description' => 'open: the feedback nobody has worked off yet. closed: the ones a session worked off, each with the commit subject that says what came of it. all: both. The category and tool filters apply to either.'],
                'category' => ['type' => 'string', 'enum' => Channel::CATEGORIES, 'description' => 'Restrict the list to one category.'],
                'tool' => ['type' => 'string', 'description' => 'Restrict the list to the feedback about one tool, for example typo3_label_lookup. A feedback that names several tools matches each of them.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 20, 'description' => 'Maximum number of feedback to return.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'count' => Schema::integer(),
            'notes' => Schema::listOf(Schema::object([
                'file' => Schema::string(),
                'date' => Schema::string(),
                'category' => Schema::string(),
                'status' => Schema::string('open while the feedback waits for a session, closed once a session moved it to the archive.'),
                'model' => Schema::string('The model that left the feedback. "unknown" where it named none or predates the field.'),
                'tool' => Schema::string('The tools the feedback is about, comma-separated. Empty when it names none.'),
                'tools' => Schema::listOf(Schema::string(), 'The same names as a list, to filter or group by without a parse.'),
                'title' => Schema::string(),
                'closedBy' => [
                    'type' => ['object', 'null'],
                    'description' => 'The commit that worked the feedback off. Null while the feedback is open.',
                    'properties' => [
                        'commit' => Schema::string(),
                        'date' => Schema::string(),
                        'subject' => Schema::string('The commit subject: what came of the feedback.'),
                    ],
                ],
            ], ['file', 'date', 'category', 'status', 'model', 'tool', 'tools', 'title', 'closedBy'])),
        ], ['count', 'notes']);
    }

    public static function answer(array $args): ToolResult
    {
        $status = is_string($args['status'] ?? null) ? $args['status'] : 'open';
        $category = is_string($args['category'] ?? null) ? $args['category'] : null;
        $limit = is_int($args['limit'] ?? null) ? $args['limit'] : 20;
        $tool = is_string($args['tool'] ?? null) && trim($args['tool']) !== '' ? trim($args['tool']) : null;

        $feedback = Channel::all($status, $category, $limit, $tool);

        if ($feedback === []) {
            return ToolResult::create(
                sprintf(
                    '%s%s',
                    match ($status) {
                        'open' => 'No open improvement feedback',
                        'closed' => 'No improvement feedback has been worked off yet',
                        default => 'No improvement feedback recorded yet',
                    },
                    $tool === null ? '.' : ' about ' . $tool . '.'
                ),
                ['count' => 0, 'notes' => []],
            );
        }

        $lines = array_map(static function (array $feedback): string {
            $date = substr($feedback['date'], 0, 10);
            $about = $feedback['tool'] === '' ? '' : ' — ' . $feedback['tool'];
            // Named even when it is "unknown": a feedback nobody can attribute is a
            // different thing from one whose model simply is not shown, and the
            // list is where the difference is acted on.
            $by = $feedback['model'] === '' ? '' : ' · ' . $feedback['model'];

            $entry = sprintf(
                "- %s%s%s%s\n  %s\n  %s",
                $feedback['category'] === '' ? '' : '[' . $feedback['category'] . '] ',
                $date,
                $about,
                $by,
                $feedback['title'],
                $feedback['file'],
            );
            if ($feedback['closedBy'] !== null) {
                $entry .= sprintf(
                    "\n  closed %s in %s: %s",
                    $feedback['closedBy']['date'],
                    $feedback['closedBy']['commit'],
                    $feedback['closedBy']['subject'],
                );
            }

            return $entry;
        }, $feedback);

        return ToolResult::create(
            sprintf("%d improvement feedback(s):\n\n%s", count($feedback), implode("\n", $lines)),
            ['count' => count($feedback), 'notes' => $feedback],
        );
    }
}
