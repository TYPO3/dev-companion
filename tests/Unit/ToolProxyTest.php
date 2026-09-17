<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Upkeep\Command\ToolProxy;

/**
 * What `tools:proxy` takes out of a line on its way back to the client, and
 * what it leaves as it came.
 *
 * The relay itself is a subprocess with two pipes, and `tests/Smoke/` drives
 * that. This holds the rewrite, which is the half a measurement rests on.
 */
final class ToolProxyTest extends TestCase
{
    #[Test]
    public function theDataHalfGoesFromAnAnswer(): void
    {
        $line = '{"jsonrpc":"2.0","id":3,"result":{"content":[{"type":"text","text":"hello"}],"structuredContent":{"matchCount":1}}}';

        $message = json_decode(ToolProxy::rewritten($line, ['structuredContent']));

        self::assertSame('hello', $message->result->content[0]->text);
        self::assertObjectNotHasProperty('structuredContent', $message->result);
    }

    #[Test]
    public function theOutputSchemaGoesFromEveryToolOfTheList(): void
    {
        $line = '{"jsonrpc":"2.0","id":2,"result":{"tools":[{"name":"a","inputSchema":{"type":"object"},"outputSchema":{"type":"object"}},{"name":"b","inputSchema":{"type":"object"},"outputSchema":{"type":"object"}}]}}';

        $message = json_decode(ToolProxy::rewritten($line, ['outputSchema']));

        foreach ($message->result->tools as $tool) {
            self::assertObjectHasProperty('inputSchema', $tool, $tool->name);
            self::assertObjectNotHasProperty('outputSchema', $tool, $tool->name);
        }
    }

    /**
     * The one half named goes and the other stays, whichever is named. A
     * measurement that took both when asked for one would read the same
     * answer under two labels.
     */
    #[Test]
    public function onlyTheHalfNamedGoes(): void
    {
        $answer = '{"jsonrpc":"2.0","id":3,"result":{"content":[],"structuredContent":{}}}';
        $list = '{"jsonrpc":"2.0","id":2,"result":{"tools":[{"name":"a","outputSchema":{}}]}}';

        self::assertObjectHasProperty('structuredContent', json_decode(ToolProxy::rewritten($answer, ['outputSchema']))->result);
        self::assertObjectHasProperty('outputSchema', json_decode(ToolProxy::rewritten($list, ['structuredContent']))->result->tools[0]);
    }

    /**
     * An empty object stays an object. The handshake's capabilities are
     * three of them, and a client reads `[]` as a list it did not ask for.
     */
    #[Test]
    public function anEmptyObjectSurvivesTheRewrite(): void
    {
        $line = '{"jsonrpc":"2.0","id":1,"result":{"capabilities":{"logging":{},"tools":{}},"serverInfo":{"name":"x"}}}';

        self::assertSame(
            '{"jsonrpc":"2.0","id":1,"result":{"capabilities":{"logging":{},"tools":{}},"serverInfo":{"name":"x"}}}' . "\n",
            ToolProxy::rewritten($line, ['structuredContent', 'outputSchema']),
        );
    }

    /**
     * Named nothing, the line passes byte for byte. So `as served` in
     * `tools:tokens` measures the server and not the proxy's re-encoding.
     */
    #[Test]
    public function aLineNamedNothingToStripPassesAsItCame(): void
    {
        $line = '{"jsonrpc":"2.0","id":3,"result":{"content":[],"structuredContent":{"a":"b\/c"}}}' . "\n";

        self::assertSame($line, ToolProxy::rewritten($line, []));
    }

    #[Test]
    public function aLineThatIsNoMessagePassesAsItCame(): void
    {
        self::assertSame("not json\n", ToolProxy::rewritten("not json\n", ['structuredContent']));
    }
}
