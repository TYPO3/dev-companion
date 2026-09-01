<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Feedback\Channel;
use TYPO3\DevCompanion\Installation\Icons;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Server\ExcludedTools;

/**
 * Every tool this server has, and the only place one is switched on.
 *
 * What a tool is called, takes, returns and answers lives in the class that
 * answers it; this is the list, in the order a client is offered them. Two
 * things narrow that list: what the caller excluded, and the feedback channel,
 * which exists only in a standalone checkout. Nothing else does — which
 * repository the server was started in shapes what an answer says, never
 * whether the tool that says it is there.
 */
final class Registry
{
    /**
     * In the order a client sees them: orientation first, then the guides and
     * lookups, then what describes the repository being worked in.
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
     * The exclusion list does not reach these two, which is `R-SCO-009`'s second
     * exception rather than an oversight. `typo3_feedback_record` writes into
     * that checkout and not into the installation the server read — `D-FBK-042`,
     * written because the two were read as one.
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
     *     description: string,
     *     answersFrom: array<int, string>,
     *     inputSchema: array<string, mixed>,
     *     annotations: array<string, bool>,
     *     outputSchema: array<string, mixed>|null
     * }>
     */
    public static function definitions(): array
    {
        // The sources are appended to the description here rather than written
        // into each one, so a tool that gains or loses a source cannot say the
        // old thing in the sentence a client actually reads.
        return array_map(static fn(string $tool): array => [
            'name' => $tool::name(),
            'description' => rtrim($tool::description()) . ' ' . Source::clause($tool::answersFrom()),
            'answersFrom' => array_map(
                static fn(Source $source): string => $source->value,
                $tool::answersFrom(),
            ),
            'inputSchema' => $tool::inputSchema(),
            'annotations' => $tool::annotations(),
            'outputSchema' => $tool::outputSchema(),
        ], self::offered());
    }

    /**
     * The answer one tool gives, and the end of what was read for it.
     *
     * One boot answers every topic a call needs, so the reading is memoized for
     * the length of the call and dropped with it. Keeping it past the answer it
     * was taken for is what makes an installation answer about itself as it was
     * before the caller's own edit: between two calls the agent writes, and the
     * icon it registered a minute ago comes back unregistered from a registry
     * read before it existed. A boot costs time and no tokens, and a wrong
     * answer costs both.
     *
     * The console resolution is not dropped with them, for a narrower reason
     * than this carried before `4b43734`. A project stopped mid-session does
     * not fail at the boot: where host PHP satisfies the bound the installation
     * pins, it resolves through that interpreter and answers with a caveat.
     * `resolve()` declines to remember that one, so there is nothing left here
     * to drop. Dropping per call would take the uncaveated resolution with it,
     * and that one is discovered once per process — 0.492s cold against 0.002s
     * warm.
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
     * The tools this client is being offered, in order.
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
