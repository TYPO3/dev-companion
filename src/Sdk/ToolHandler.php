<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Sdk;

use Mcp\Schema\Content\TextContent;
use Mcp\Schema\Result\CallToolResult;
use Mcp\Server\ClientGateway;
use Mcp\Server\Handler\ToolHandlerInterface;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * Bridges one registered tool to TYPO3\DevCompanion\Tool\Registry.
 *
 * The official SDK passes the raw (validated) argument bag to execute(); it is
 * handed straight to Registry::call(), so every behaviour and every rendering
 * stays in the tool that owns it and nothing about a tool is decided here. Both
 * halves of the answer are returned: the text as the tool's content, the same
 * answer as `structuredContent` matching the output schema that tool declares.
 */
final class ToolHandler implements ToolHandlerInterface
{
    public function __construct(private readonly string $name) {}

    /**
     * @param array<string, mixed> $arguments
     */
    public function execute(array $arguments, ClientGateway $gateway): CallToolResult
    {
        try {
            $result = Registry::call($this->name, $arguments);
        } catch (\Throwable $failure) {
            // What a tool refuses is said in the refusal, and letting it out of
            // this method loses it: the SDK answers a thrown exception with the
            // JSON-RPC error -32603 "Error while executing tool" and nothing
            // else, so a caller who can fix the argument is told only that
            // something failed. A session that hit the one refusal on the
            // feedback path spent five calls establishing which parameter it
            // was — `D-ANS-143`. The protocol's own place for this is a result
            // marked as an error, which reaches the model that made the call.
            return new CallToolResult(
                [new TextContent(self::said($failure))],
                isError: true,
            );
        }

        return new CallToolResult(
            [new TextContent($result->text)],
            structuredContent: $result->data,
        );
    }

    /**
     * What the caller is told a failure was.
     *
     * The message, because every refusal this server raises is written for the
     * caller and says what to send instead. A failure carrying none is named by
     * its class, so "nothing was said" is still an answer somebody can search.
     */
    private static function said(\Throwable $failure): string
    {
        $message = trim($failure->getMessage());

        return $message === '' ? $failure::class : $message;
    }
}
