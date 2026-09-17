<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Result\ToolResult;

/**
 * One tool: what a client is told about it, and what it answers.
 *
 * Everything a caller can see of a tool stands in the class that answers it.
 * The description a client chooses it by, the arguments it takes, the shape of
 * the data it returns, and the answer itself. A description that stops to
 * describe the answer is then a change to one file rather than a drift between
 * three.
 *
 * What these are is the MCP primitive the protocol calls a tool — the SDK
 * declares it as Mcp\Schema\Tool, beside Prompt and Resource. So the word here
 * is the protocol's, and the qualifier that says which kind of tool is meant is
 * already the root namespace. Nothing is a "server tool". The protocol defines
 * a tool rather than the side that offers it, and both sides speak of the same
 * one.
 *
 * TYPO3\DevCompanion\Tool\Registry is the list of them, and the only place that
 * switches a tool on.
 */
interface Tool
{
    /** typo3_<subject>_<verb>, with the verb from the closed list ToolNamingTest holds. */
    public static function name(): string;

    /**
     * What a client shows a person for this tool, in plain words.
     *
     * A listing, a permission dialog and the Inspector show it; the model
     * reads the description and most clients never hand it the title. So it
     * says the subject in words somebody outside TYPO3 reads at a glance, and
     * it does not recite the name.
     */
    public static function title(): string;

    /** What a client chooses this tool by. It is the only documentation most of them read. */
    public static function description(): string;

    /**
     * Where this tool's answer can come from, first one first.
     *
     * Declared per tool rather than kept as a list somewhere, for the reason
     * every such list here derives. A list is what still names a tool after the
     * tool stopped to answer that way. Registry renders it into the description
     * a client reads and typo3_server_scope groups the tools by it.
     *
     * @return array<int, Source>
     */
    public static function answersFrom(): array;

    /** @return array<string, mixed> */
    public static function inputSchema(): array;

    /**
     * The contract of the data half: a field named here has to be present on
     * every path through the tool, misses included.
     *
     * @return array<string, mixed>
     */
    public static function outputSchema(): array;

    /**
     * What the tool does to the world, as the MCP annotations state it.
     *
     * @return array<string, bool>
     */
    public static function annotations(): array;

    /** @param array<string, mixed> $args */
    public static function answer(array $args): ToolResult;
}
