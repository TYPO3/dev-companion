<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Knowledge\CommitMessage;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * A TYPO3 commit message, drafted from parts or checked and corrected.
 */
final class CommitMessageGuide extends ReadOnlyTool
{
    /**
     * The page that writes this tool's whole subject up, named where the caller
     * already stands in the answer.
     *
     * Four sessions wanted `core/contribution/commit-messages` and none read
     * it. One was inside this answer when it gave up. It assembled the page out
     * of `AGENTS.md`, the checkout's hook and three `git log` statistics runs,
     * `D-ANS-061`, `R-ANS-028`. Named as the call rather than as the
     * `typo3://guides` address. That is delivery to a client that lists
     * resources, and none of the four had one.
     */
    private const CORE_GUIDE = 'The rules this was checked against are one page, and reading it whole is one call '
        . '— typo3_rule_lookup with documentId "core/contribution/commit-messages", which needs no resource list. '
        . "What it carries beside the checks above:\n"
        . '- What the commit hook writes into the message afterwards, and how a subject marks a patch that is '
        . "still work in progress.\n"
        . '- What a breaking change, a deprecation or a changed signature owes beside the message: the changelog '
        . 'file it announces, and the extension scanner entry a removed member takes.';

    /**
     * The workflow this commit is one step of, named in the answer a session
     * under momentum still asks for (`D-ANS-117`).
     *
     * The imperative to open a task at `typo3_task_guide` was in one session's
     * context from the first token and produced no call in five turns. The two
     * calls it did make were this tool, in the last turn, under challenge. So
     * the pointer sits at the phase rather than at the start, in the shape
     * `GerritLookup::workflow()` took. `workflow` is a parameter, and the two
     * answers are different work. It is a record rather than a sentence, for
     * the client that drops the text block, `R-ANS-002`.
     *
     * @return array{tool: string, when: string}
     */
    public static function workflowGuide(string $workflow): array
    {
        return [
            'tool' => 'typo3_task_guide',
            'when' => $workflow === CommitMessage::WORKFLOW_CORE
                ? 'with the paths this commit touches, before pushing. This commit is one step of the core patch '
                    . 'workflow, and the brief names what the patch owes beside the message: the deprecation '
                    . 'sweep, the test coverage and the suites to run.'
                : 'with the paths this commit touches. This commit is one step of work in your own repository, '
                    . 'and the brief names the core conventions that transfer to it and the hints for those paths.',
        ];
    }

    public static function name(): string
    {
        return 'typo3_commit_message_guide';
    }

    public static function title(): string
    {
        return 'Draft or check a commit message';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Knowledge];
    }

    public static function description(): string
    {
        return 'Draft and check a TYPO3 commit message. A person who wants to know what the commit did reads the message. So write it in plain English and only as long as that answer needs; the diff carries the detail. Either assemble one from parts (keyword plus summary) or pass a message you have to check and correct it. The returned draft is ready to commit. Its body wraps at 72 characters. The checks name every run of lines the wrap joined and every line it could not bring under the width. Defaults to a repository of your own. There the subject and body conventions apply, and nothing demands a Forge issue, a Releases: trailer or a changelog. The draft still writes the issues you pass as Resolves: and Related: trailers there. That is the form a TYPO3 repository on GitHub links a commit to what it closes by. Pass workflow="core" for a patch against the TYPO3 core, which requires the Forge issue and the Releases: trailer. Where the call carries none, the answer names the branches for that trailer. That is the lines that take a patch today, and the ones a change of this shape goes to. A change still in work says so with workInProgress or with [WIP] in its own subject. The Forge issue is no error there, since the merge is what requires the trailer. A core body that counts what the change touched, so many files or so many spellings, gets a check that says so. The core\'s own bodies do not count.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'message' => ['type' => 'string', 'minLength' => 1, 'description' => 'A complete commit message to check, subject and trailers included. The draft keeps unknown trailers such as Change-Id, so an amended patch set stays valid. The exception is workflow="core", which takes Co-Authored-By and an agent\'s own session trailer off the draft and says so: a core commit message carries neither. A core message without Signed-off-by is an error there. The core requires the certificate, and the draft carries a placeholder because only whoever commits can sign it.'],
                'workflow' => ['type' => 'string', 'enum' => ['core', 'project'], 'default' => 'project', 'description' => 'Which rules to apply. "project", the default, is any repository of your own. The checks read the keyword, the 52/72 character limits and the wrap. They demand and invent no trailer, the draft writes the issues you pass out all the same, and they allow [SECURITY]. "core": a patch against the TYPO3 core, with the Forge issue and the Releases: trailer required.'],
                'keyword' => ['type' => 'string', 'enum' => ['BUGFIX', 'FEATURE', 'TASK', 'DOCS', 'SECURITY'], 'description' => 'TYPO3 commit message keyword. [SECURITY] belongs to the TYPO3 Security Team, and only workflow="project" accepts it.'],
                'summary' => ['type' => 'string', 'minLength' => 1, 'description' => 'Summary text without the TYPO3 keyword prefix. Say what the commit did, in words a reader understands from the log alone.'],
                'issue' => ['type' => 'string', 'description' => 'The issue this commit resolves, with or without # in front. That is the Forge issue number for a core patch, the number in your own tracker otherwise. The draft writes it as a Resolves: trailer in either workflow. For more than one, write the message out and pass it as message, which keeps every trailer it carries.'],
                'relatedIssues' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'Issues this commit relates to and does not resolve, read as issue is and written as Related: trailers.'],
                'releases' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Target releases, for example main or 13.4. Left out, the draft carries a RELEASE_TARGET placeholder and the checks name the lines that take a patch today. The tool guesses no release branch. It holds each one you pass against those lines. A branch out of regular support is an error, since ELTS releases come from the ELTS partners rather than from a patch to that branch.'],
                'body' => ['type' => 'string', 'description' => 'Optional commit body, for what the diff does not say: why you made the change, what it rests on. The draft wraps it at 72 characters. Indent a block to keep the line breaks you wrote, and keep those lines under the width yourself.'],
                'isBreaking' => ['type' => 'boolean', 'description' => 'Whether this is a breaking change that requires [!!!]. Left out, the checks say the classification is an assumption: it is a property of the diff, which this tool never sees.'],
                'isDeprecation' => ['type' => 'boolean', 'description' => 'Whether this is a deprecation. Left out, it is an assumption the same way and the checks say so.'],
                'workInProgress' => ['type' => 'boolean', 'description' => 'Whether the change is still in work and not up for merge. True writes [WIP] before the keyword, where [!!!] goes, and the Forge issue is no error then: the merge is what requires the trailer. The sign-off stays required whatever the state. A message passed as message says this in its own subject and needs no argument; the checks read [WIP] and [PoC] there the same way.'],
            ],
            // `anyOf` rather than `oneOf`: a call may carry a message and the
            // arguments that amend it. A client that offers the branches as a
            // choice labels each by its title, and "Option 1" without one.
            'anyOf' => [
                ['title' => 'Check a message', 'required' => ['message']],
                ['title' => 'Draft from keyword and summary', 'required' => ['keyword', 'summary']],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'message' => Schema::string('The commit message, ready to use.'),
            'checks' => Schema::listOf(Schema::object([
                'level' => ['type' => 'string', 'enum' => ['error', 'warning', 'info']],
                'code' => Schema::string('Stable identifier of the check, for example summary-too-long.'),
                'message' => Schema::string(),
            ], ['level', 'code', 'message'])),
            'workflow' => [
                'type' => 'string',
                'enum' => ['core', 'project'],
                'description' => 'Which rules the draft follows and the checks read it against. "core" adds the Forge '
                    . 'issue and the Releases: trailer and demands them. "project" applies the subject and body '
                    . 'rules and writes only the trailers the call carried.',
            ],
            'nextTools' => Schema::listOf(
                Schema::nextTool(),
                'The workflow this commit is one step of, named as the call that answers it. A message is the '
                . 'last act of a piece of work rather than the whole of it. What the work owes beside the '
                . 'message is not in this answer.',
            ),
        ], ['message', 'checks', 'workflow', 'nextTools']);
    }

    public static function answer(array $args): ToolResult
    {
        $existing = isset($args['message']) ? trim((string) $args['message']) : '';
        $workflow = CommitMessage::workflow($args['workflow'] ?? null);

        $parseChecks = [];
        if ($existing !== '') {
            $parsed = CommitMessage::parse($existing, $workflow);
            // Explicit arguments still win, so one call can check and amend a
            // message: pass the message plus issue=12345.
            $input = array_merge($parsed['input'], array_intersect_key($args, array_flip([
                'keyword', 'summary', 'issue', 'relatedIssues', 'releases', 'isBreaking', 'isDeprecation',
            ])));
            $parseChecks = $parsed['checks'];
        } else {
            $input = $args;
        }
        $input['workflow'] = $workflow;
        // A subject that says [WIP] itself needs no argument; the parse has
        // already read it. The argument is for the caller who composes a fresh
        // message out of a keyword and a summary. That is the shape the session
        // that reported this used.
        if (($args['workInProgress'] ?? false) === true && ($input['draftPrefixes'] ?? []) === []) {
            $input['draftPrefixes'] = ['WIP'];
        }

        if (!isset($input['summary']) || trim((string) $input['summary']) === '') {
            throw new \InvalidArgumentException(
                'Provide either a complete message, or keyword and summary.'
            );
        }

        /** @var array{keyword: string, summary: string} $input */
        $result = CommitMessage::create($input);

        $checks = $result['checks'];
        if ($parseChecks !== []) {
            // "Nothing to complain about" only holds when nothing complained.
            // Dropped by code rather than by level, because what the wrap did
            // to the body reports at info too and is not a complaint.
            $checks = array_values(array_filter(
                $checks,
                static fn(array $check): bool => $check['code'] !== 'no-issues-found'
            ));
        }

        $checks = array_merge($parseChecks, $checks);

        $heading = $existing === '' ? 'Commit message draft:' : 'Commit message, corrected:';
        $lines = [$heading, '```text', $result['message'], '```', '', 'Checks:'];
        foreach ($checks as $check) {
            $lines[] = '- ' . strtoupper($check['level']) . ': ' . $check['message'];
        }

        // Which rules applied belongs in the answer, because the two sets
        // differ in what they demand rather than in how strict they are. A
        // caller who did not know about the other one reads an absent Forge
        // issue as a defect in their commit message.
        $lines[] = '';
        $lines[] = $workflow === CommitMessage::WORKFLOW_PROJECT
            ? 'Checked without the core workflow: keyword, 52/72 limits and wrapping apply, no Forge issue and no '
                . 'Releases: trailer are demanded, and the Resolves: and Related: lines carry the issues this call '
                . 'passed. workflow="core" for a patch against the TYPO3 core.'
            : 'Checked against the core contribution rules, trailers included. workflow="project" applies the same '
                . 'subject and body rules without the Forge issue and the Releases: trailer.';

        // Above the page and not under it. The pointer is an act the caller has
        // not taken yet, and the page is a read of the subject they are already
        // in. The one line under the longest block in the answer is the failure
        // this placement stands against (`D-ANS-117`).
        $next = self::workflowGuide($workflow);
        $lines[] = '';
        $lines[] = $next['tool'] . ' — ' . $next['when'];

        // The core answer alone, because the page describes the core repository
        // and says so in its own whenToUse. A project commit checks against the
        // subject and body rules the page shares, and owes none of what the
        // rest of it demands.
        if ($workflow === CommitMessage::WORKFLOW_CORE) {
            $lines[] = '';
            $lines[] = self::CORE_GUIDE;
        }

        return ToolResult::create(implode("\n", $lines), [
            'message' => $result['message'],
            'checks' => $checks,
            'workflow' => $workflow,
            'nextTools' => [$next],
        ]);
    }
}
