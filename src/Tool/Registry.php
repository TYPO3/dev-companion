<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Feedback\Channel;
use TYPO3\DevCompanion\Installation\Icons;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Server\ExcludedTools;

/**
 * Every tool this server has, and the only place that switches one on.
 *
 * A tool's name, what it takes, returns and answers lives in the class that
 * answers it. This is the list, in the order a client gets them. Two things
 * narrow that list: what the caller excluded, and the feedback channel, which
 * exists only in a standalone checkout. Nothing else does. Which repository the
 * server started in shapes what an answer says, never whether the tool that
 * says it is there.
 */
final class Registry
{
    /**
     * In the order a client sees them: orientation first, then the guides and
     * lookups, then what describes the repository at hand.
     *
     * @var array<int, class-string<Tool>>
     */
    private const TOOLS = [
        ServerScope::class,
        RuleLookup::class,
        ScriptLookup::class,
        TaskGuide::class,
        TestRunGuide::class,
        HintLookup::class,
        DocumentationLookup::class,
        PermalinkLookup::class,
        ForgeLookup::class,
        GerritLookup::class,
        ComponentLookup::class,
        SystemExtensionLookup::class,
        ReferenceList::class,
        TranslationDomainLookup::class,
        LabelLookup::class,
        FluidNamespaceList::class,
        ConfigurationLookup::class,
        SchemaLookup::class,
        RecordLookup::class,
        ServiceLookup::class,
        FlexFormLookup::class,
        BackendModuleLookup::class,
        IconLookup::class,
        ChangelogLookup::class,
        TerLookup::class,
        ProjectDescribe::class,
        ExtensionDescribe::class,
        SnapshotScope::class,
        CommitMessageGuide::class,
    ];

    /**
     * Offered from a standalone checkout alone — see the feedback channel.
     *
     * The exclusion list does not reach these two, which is `R-SCO-009`'s
     * second exception rather than an oversight. `typo3_feedback_record` writes
     * into that checkout and not into the installation the server read.
     * `D-FBK-042`, written because a reader took the two as one.
     *
     * @var array<int, class-string<Tool>>
     */
    private const FEEDBACK = [
        FeedbackRecord::class,
        FeedbackList::class,
    ];

    /**
     * @return array<int, array{
     *     name: string,
     *     title: string,
     *     description: string,
     *     answersFrom: array<int, string>,
     *     inputSchema: array<string, mixed>,
     *     annotations: array<string, bool>,
     *     outputSchema: array<string, mixed>|null
     * }>
     */
    public static function definitions(): array
    {
        // The sources join the description here rather than stand in each one.
        // So a tool that gains or loses a source cannot say the old thing in
        // the sentence a client reads.
        return array_map(static fn(string $tool): array => [
            'name' => $tool::name(),
            'title' => $tool::title(),
            'description' => rtrim($tool::description()) . ' ' . Source::clause($tool::answersFrom()),
            'answersFrom' => array_map(
                static fn(Source $source): string => $source->value,
                $tool::answersFrom(),
            ),
            // Closed against an argument nothing here knows, for every tool at
            // once. Open, the validator drops a misspelt parameter and the tool
            // answers as if it was never sent, `D-ANS-053`.
            'inputSchema' => $tool::inputSchema() + ['additionalProperties' => false],
            'annotations' => $tool::annotations(),
            'outputSchema' => $tool::outputSchema(),
        ], self::offered());
    }

    /**
     * The answer one tool gives, and the end of the read behind it.
     *
     * One boot answers every topic a call needs, so the read stays in memory
     * for the length of the call and goes with it. Kept past the answer it came
     * for, it makes an installation answer about itself as it was before the
     * caller's own edit. Between two calls the agent writes, and the icon it
     * registered a minute ago comes back unregistered from a registry read
     * before it existed. A boot costs time and no tokens, and a wrong answer
     * costs both.
     *
     * The console resolution does not go with them, for a narrower reason than
     * this carried before `4b43734`. A project stopped mid-session does not
     * fail at the boot. Where host PHP satisfies the bound the installation
     * pins, it resolves through that interpreter and answers with a caveat.
     * `resolve()` declines to remember that one, so nothing remains here to
     * drop. A drop per call would take the resolution without caveat with it,
     * and that one comes once per process, 0.492s cold against 0.002s warm.
     *
     * @param array<string, mixed> $args
     */
    public static function call(string $name, array $args): ToolResult
    {
        foreach (self::offered() as $tool) {
            if ($tool::name() === $name) {
                try {
                    return $tool::answer($args);
                } finally {
                    Typo3Runtime::forget();
                    Icons::forget();
                }
            }
        }

        throw new \InvalidArgumentException(sprintf('Unknown tool: %s', $name));
    }

    /**
     * The tools this client gets, in order.
     *
     * @return array<int, class-string<Tool>>
     */
    private static function offered(): array
    {
        $offered = array_values(array_filter(
            self::TOOLS,
            static fn(string $tool): bool => ExcludedTools::offers($tool::name()),
        ));

        // Past the filter, which is where R-SCO-009 says they belong.
        if (Channel::isAvailable()) {
            array_push($offered, ...self::FEEDBACK);
        }

        return $offered;
    }
}
