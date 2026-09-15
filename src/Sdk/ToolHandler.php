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
 * The official SDK passes the raw (validated) argument bag to execute(). It
 * goes straight to Registry::call(). So every behaviour and every render stays
 * in the tool that owns it, and nothing about a tool has its decision here.
 * Both halves of the answer come back. The text as the tool's content, the same
 * answer as `structuredContent` that matches the output schema that tool
 * declares.
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
            // What a tool refuses stands in the refusal, and an escape out of
            // this method loses it. The SDK answers a thrown exception with the
            // JSON-RPC error -32603 "Error while executing tool" and nothing
            // else. So a caller who can fix the argument hears only that
            // something failed. A session that hit the one refusal on the
            // feedback path spent five calls to establish which parameter it
            // was, `D-ANS-143`. The protocol's own place for this is a result
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
     * The message, because every refusal this server raises addresses the
     * caller and says what to send instead. A failure with none goes by its
     * class, so "no message" is still an answer somebody can search.
     */
    private static function said(\Throwable $failure): string
    {
        $message = trim($failure->getMessage());

        return $message === '' ? $failure::class : $message;
    }
}
