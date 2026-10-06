<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Contribution\Forge;
use TYPO3\DevCompanion\Contribution\IssueReport;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * A new TYPO3 Core issue on forge.typo3.org, drafted, checked and handed over
 * as a link to the form with every field filled in.
 */
final class IssueReportGuide extends ReadOnlyTool
{
    protected const OPEN_WORLD = true;

    public static function name(): string
    {
        return 'typo3_issue_report_guide';
    }

    public static function title(): string
    {
        return 'Draft a TYPO3 Core issue';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Knowledge, Source::Network];
    }

    public static function description(): string
    {
        return 'Draft a new issue for the TYPO3 Core project on forge.typo3.org, for a bug or a task found on the way. Core only. The answer is the draft, the checks, and a link that opens the tracker\'s new-issue form with every field filled in: tracker, subject, description, TYPO3 Version, PHP Version, category, priority, target version, complexity, Is Regression, tags and parent task. Submitting stays yours: the form needs an account, and this tool files nothing. Pass the description as Textile, or as its parts — problem, stepsToReproduce, cause and suggestedFix — and the draft writes them in the order the core\'s own reports use. The checks name what the tracker would render wrong, a Bug without its TYPO3 Version, and a category or target version the project does not have. The same call searches the tracker for issues that match searchFor, the subject by default, and lists them as possible duplicates. Every draft carries the tag dev-companion. typo3_forge_lookup reads issues that already exist, and settles a negative this search leaves open.';
    }

    public static function inputSchema(): array
    {
        $properties = [
            'subject' => ['type' => 'string', 'minLength' => 1, 'description' => 'The title. Name the subsystem and what it does wrong; a triage reads this line instead of the report.'],
            'tracker' => ['type' => 'string', 'enum' => array_keys(IssueReport::TRACKERS), 'default' => 'Bug', 'description' => 'Bug for something broken. The core project offers these five and no other.'],
            'description' => ['type' => 'string', 'description' => 'The whole description as Textile. Left out, the draft composes it from problem, stepsToReproduce, cause and suggestedFix.'],
            'problem' => ['type' => 'string', 'description' => 'The symptom, in the words it showed in: an exception message, a wrong value, a rendering that differs from the expected one. Textile.'],
            'stepsToReproduce' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'One step per entry, from a state somebody else can reach. The draft writes them as an ordered list.'],
            'cause' => ['type' => 'string', 'description' => 'The class and method the defect is in, and what it does wrong. Textile.'],
            'suggestedFix' => ['type' => 'string', 'description' => 'A diff. The draft wraps it in <pre><code class="diff"> so the tracker keeps the removed lines.'],
            'typo3Version' => ['type' => 'string', 'description' => 'The TYPO3 major you saw it on, such as 14. A full release number is cut to its major. A Bug does not go without it.'],
            'phpVersion' => ['type' => 'string', 'description' => 'The PHP major and minor you saw it on, such as 8.4. Optional.'],
            'category' => ['type' => 'string', 'description' => 'The area, in the tracker\'s own spelling. typo3_forge_lookup with category in your own words answers that spelling.'],
            'priority' => ['type' => 'string', 'enum' => array_keys(IssueReport::PRIORITIES), 'description' => 'Left out, the form keeps its default, Should have, which nearly every report carries.'],
            'fixedVersion' => ['type' => 'string', 'description' => 'The form\'s Target version, by the name the form shows, such as next-patchlevel. It is the schedule\'s call, so set it where you take the fix on yourself.'],
            'isRegression' => ['type' => 'boolean', 'description' => 'Whether an earlier release did not have the defect.'],
            'complexity' => ['type' => 'string', 'enum' => IssueReport::COMPLEXITIES, 'description' => 'A guess at the fix. Optional.'],
            'tags' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'Free tags. The draft adds dev-companion to every report.'],
            'parentIssue' => ['type' => 'integer', 'minimum' => 1, 'description' => 'The issue this one is a part of.'],
            'searchFor' => ['type' => 'string', 'description' => 'Two or three words of the symptom for the duplicate search. Every word has to be in the same issue, so a whole subject rarely matches. Defaults to the subject.'],
        ];

        return [
            'type' => 'object',
            'properties' => $properties,
            'required' => ['subject'],
            'anyOf' => [
                Schema::branch('With a written description', $properties, ['description']),
                Schema::branch('With the description in parts', $properties, ['problem']),
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'subject' => Schema::string('The title as the form gets it.'),
            'tracker' => Schema::string('Bug, Feature, Task, Story or Epic.'),
            'description' => Schema::string('The description as Textile, as the form gets it unless descriptionInLink is false.'),
            'form' => Schema::listOf(Schema::object([
                'field' => Schema::string('The label the form shows.'),
                'value' => Schema::string('What the link fills in.'),
            ], ['field', 'value']), 'Every field the link fills, in the order the form shows them. A field that is not here stays as the form leaves it: Assignee and Sprint Focus are set by whoever takes the issue on.'),
            'url' => Schema::string('The tracker\'s new-issue form with these fields filled in. An anonymous reader passes the login first, and the form arrives filled after it.'),
            'descriptionInLink' => ['type' => 'boolean', 'description' => 'False where the description is too long for a link the tracker accepts. The link then fills every other field, and the description is pasted by hand.'],
            'duplicateSearch' => Schema::object([
                'query' => Schema::string('The words the search took.'),
                'status' => ['type' => 'string', 'enum' => ['answered', 'empty', 'unavailable']],
                'total' => Schema::integer('How many issues matched, which may be more than the list carries.'),
            ], ['query', 'status', 'total']),
            'possibleDuplicates' => Schema::listOf(Schema::object([
                'issue' => Schema::integer('The issue number; typo3_forge_lookup with issue reads it whole.'),
                'subject' => Schema::string(),
                'tracker' => Schema::string(),
                'status' => Schema::string(),
                'url' => Schema::string('Where a person reads it.'),
            ], ['issue', 'subject', 'tracker', 'status', 'url']), 'Issues that carry every word of the search. Empty is not a negative: a wording reaches only the issues worded that way.'),
            'checks' => Schema::listOf(Schema::object([
                'level' => ['type' => 'string', 'enum' => ['error', 'warning', 'info']],
                'code' => Schema::string('Stable identifier of the check, for example missing-typo3-version.'),
                'message' => Schema::string(),
            ], ['level', 'code', 'message'])),
            'nextTools' => Schema::listOf(Schema::nextTool(), 'What follows the draft, named as the call that answers it.'),
        ], ['subject', 'tracker', 'description', 'form', 'url', 'descriptionInLink', 'duplicateSearch', 'possibleDuplicates', 'checks', 'nextTools']);
    }

    public static function answer(array $args): ToolResult
    {
        $draft = IssueReport::draft($args, new Forge());
        $next = [
            ['tool' => 'typo3_forge_lookup', 'when' => 'with issue and the number of each possible duplicate, and with backlog="newest" and createdSince where the search matched nothing.'],
            ['tool' => 'typo3_commit_message_guide', 'when' => 'with workflow="core" and the number the tracker gives the issue, for the Resolves: line of the patch that fixes it.'],
        ];

        $lines = ['Forge issue draft for the TYPO3 Core project. Nothing was filed: open the link, read the form, submit.', ''];
        foreach ($draft['form'] as $field) {
            $lines[] = '- ' . $field['field'] . ': ' . $field['value'];
        }
        $lines[] = '';
        $lines[] = $draft['descriptionInLink'] ? 'Description, which the link fills in:' : 'Description, to paste into the form:';
        $lines[] = '```textile';
        $lines[] = $draft['description'];
        $lines[] = '```';
        $lines[] = '';
        $lines[] = 'The form, filled in: ' . $draft['url'];
        $lines[] = '';
        $lines[] = 'Checks:';
        foreach ($draft['checks'] as $check) {
            $lines[] = '- ' . strtoupper($check['level']) . ': ' . $check['message'];
        }
        if ($draft['possibleDuplicates'] !== []) {
            $lines[] = '';
            $lines[] = 'Possible duplicates:';
            foreach ($draft['possibleDuplicates'] as $hit) {
                $lines[] = sprintf('- #%d · %s · %s · %s · %s', $hit['issue'], $hit['tracker'], $hit['status'], $hit['subject'], $hit['url']);
            }
        }
        $lines[] = '';
        foreach ($next as $tool) {
            $lines[] = $tool['tool'] . ' — ' . $tool['when'];
        }

        return ToolResult::create(implode("\n", $lines), $draft + ['nextTools' => $next]);
    }
}
