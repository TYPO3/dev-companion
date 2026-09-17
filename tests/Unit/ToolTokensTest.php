<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Command\ToolTokens;

/**
 * How `tools:tokens` reads a trace, held on one written by hand in the shape
 * `claude -p --output-format stream-json` writes. The run itself costs money
 * and needs the client, so nothing here starts one — `R-COD-003`.
 */
#[Decision('D-EVI-011')]
final class ToolTokensTest extends TestCase
{
    /**
     * The shape of one session: a request that searches for the tool, the
     * request that calls it, the result the client put in front of the model,
     * and the request that read it. Each request's input is its three counts
     * together.
     */
    #[Test]
    public function aTraceIsReadForWhatTheModelGotAndPaid(): void
    {
        $trace = implode("\n", [
            self::assistant('m1', 10, 4000, 26000, [['type' => 'tool_use', 'id' => 't1', 'name' => 'ToolSearch']]),
            self::user([['type' => 'tool_result', 'tool_use_id' => 't1', 'content' => [['type' => 'tool_reference']]]]),
            self::assistant('m2', 10, 1000, 30000, [['type' => 'tool_use', 'id' => 't2', 'name' => 'mcp__typo3__typo3_hint_lookup']]),
            self::user([['type' => 'tool_result', 'tool_use_id' => 't2', 'content' => '{"task":"x","hints":[]}']]),
            self::assistant('m3', 8, 8500, 31000, [['type' => 'text', 'text' => 'JSON']]),
            json_encode(['type' => 'result', 'subtype' => 'success', 'is_error' => false]),
        ]);

        self::assertSame(
            ['received' => ['kind' => 'JSON', 'chars' => 23], 'cost' => 8498, 'definition' => 1000, 'error' => ''],
            ToolTokens::read($trace, 'typo3_hint_lookup'),
        );
    }

    /**
     * A text block is what a client hands over where it drops the data, and
     * the trace carries it as a list of blocks rather than a string.
     */
    #[Test]
    public function aTextBlockIsReadAsText(): void
    {
        $trace = implode("\n", [
            self::assistant('m1', 10, 1000, 30000, [['type' => 'tool_use', 'id' => 't2', 'name' => 'mcp__typo3__typo3_hint_lookup']]),
            self::user([['type' => 'tool_result', 'tool_use_id' => 't2', 'content' => [['type' => 'text', 'text' => 'Task: x — nothing matched']]]]),
            self::assistant('m2', 8, 7000, 31000, [['type' => 'text', 'text' => 'text']]),
        ]);

        $read = ToolTokens::read($trace, 'typo3_hint_lookup');

        self::assertSame(['kind' => 'text', 'chars' => 25], $read['received']);
        self::assertSame(6998, $read['cost']);
        self::assertNull($read['definition'], 'a first request has nothing before it to measure the definition against');
    }

    /**
     * The same request streams as several events with the same id, the
     * counts on each of them. One request is one request however many times
     * the stream says so.
     */
    #[Test]
    public function aRequestStreamedInSeveralEventsCountsOnce(): void
    {
        $call = [['type' => 'tool_use', 'id' => 't2', 'name' => 'mcp__typo3__typo3_hint_lookup']];
        $trace = implode("\n", [
            self::assistant('m1', 10, 1000, 30000, [['type' => 'thinking', 'thinking' => '']]),
            self::assistant('m1', 10, 1000, 30000, $call),
            self::user([['type' => 'tool_result', 'tool_use_id' => 't2', 'content' => '{}']]),
            self::assistant('m2', 8, 500, 31010, [['type' => 'text', 'text' => 'done']]),
            self::assistant('m2', 8, 500, 31010, [['type' => 'text', 'text' => 'done']]),
        ]);

        self::assertSame(508, ToolTokens::read($trace, 'typo3_hint_lookup')['cost']);
    }

    /**
     * A session that never called the tool measured nothing, and the trace
     * says why where the client said why.
     */
    #[Test]
    public function aSessionThatNeverCalledTheToolHasNothingToRead(): void
    {
        $trace = implode("\n", [
            self::assistant('m1', 10, 1000, 30000, [['type' => 'text', 'text' => 'I cannot']]),
            json_encode(['type' => 'result', 'subtype' => 'error_max_turns', 'is_error' => true]),
        ]);

        self::assertSame(
            ['received' => null, 'cost' => null, 'definition' => null, 'error' => 'error_max_turns'],
            ToolTokens::read($trace, 'typo3_hint_lookup'),
        );
    }

    #[Test]
    public function aMachineWithoutTheClientIsToldSo(): void
    {
        $runner = self::createStub(CommandRunner::class);
        $runner->method('locate')->willReturn(null);
        $output = new BufferedOutput();

        self::assertSame(1, (new ToolTokens($runner))($output));
        self::assertStringContainsString('claude is not on this machine', $output->fetch());
    }

    #[Test]
    public function argumentsThatAreNoJsonObjectAreRefused(): void
    {
        $runner = self::createStub(CommandRunner::class);
        $runner->method('locate')->willReturn('/usr/bin/claude');
        $output = new BufferedOutput();

        self::assertSame(1, (new ToolTokens($runner))($output, 'typo3_hint_lookup', 'task=x'));
        self::assertStringContainsString('not a JSON object', $output->fetch());
    }

    /** @param list<array<string, mixed>> $content */
    private static function assistant(string $id, int $input, int $created, int $read, array $content): string
    {
        return (string) json_encode(['type' => 'assistant', 'message' => [
            'id' => $id,
            'content' => $content,
            'usage' => ['input_tokens' => $input, 'cache_creation_input_tokens' => $created, 'cache_read_input_tokens' => $read, 'output_tokens' => 3],
        ]]);
    }

    /** @param list<array<string, mixed>> $content */
    private static function user(array $content): string
    {
        return (string) json_encode(['type' => 'user', 'message' => ['content' => $content]]);
    }
}
