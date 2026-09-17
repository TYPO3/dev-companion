<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;

/**
 * Drives `bin/cli tools:proxy` the way a client does: a subprocess that
 * speaks JSON-RPC over stdin and stdout, with the server behind it. What
 * comes back is the server's answer with the named half gone, and the
 * handshake as the server gave it.
 */
#[Decision('D-EVI-011')]
final class ToolProxyRelayTest extends TestCase
{
    #[Test]
    public function theProxyRelaysTheServerWithTheNamedHalvesGone(): void
    {
        $responses = $this->call(['--without=structuredContent', '--without=outputSchema'], [
            $this->request(1, 'initialize', [
                'protocolVersion' => '2025-11-25',
                'capabilities' => new \stdClass(),
                'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
            ]),
            '{"jsonrpc":"2.0","method":"notifications/initialized"}',
            $this->request(2, 'tools/list'),
            $this->request(3, 'tools/call', ['name' => 'typo3_server_scope', 'arguments' => new \stdClass()]),
        ]);

        self::assertSame('typo3-dev-companion', $responses[1]['result']['serverInfo']['name']);
        // What the server said, as it said it: an object that stays one.
        self::assertSame([], $responses[1]['result']['capabilities']['logging']);

        self::assertNotSame([], $responses[2]['result']['tools']);
        foreach ($responses[2]['result']['tools'] as $tool) {
            self::assertArrayHasKey('inputSchema', $tool, $tool['name']);
            self::assertArrayNotHasKey('outputSchema', $tool, $tool['name']);
        }

        self::assertSame('text', $responses[3]['result']['content'][0]['type']);
        self::assertArrayNotHasKey('structuredContent', $responses[3]['result']);
    }

    #[Test]
    public function theProxyNamedNothingRelaysBothHalves(): void
    {
        $responses = $this->call([], [
            $this->request(1, 'initialize', [
                'protocolVersion' => '2025-11-25',
                'capabilities' => new \stdClass(),
                'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
            ]),
            '{"jsonrpc":"2.0","method":"notifications/initialized"}',
            $this->request(2, 'tools/list'),
            $this->request(3, 'tools/call', ['name' => 'typo3_server_scope', 'arguments' => new \stdClass()]),
        ]);

        self::assertArrayHasKey('outputSchema', $responses[2]['result']['tools'][0]);
        self::assertArrayHasKey('structuredContent', $responses[3]['result']);
    }

    #[Test]
    public function aHalfTheProxyDoesNotKnowIsRefused(): void
    {
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/cli', 'tools:proxy', '--without=isError'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            Paths::root(),
        );
        self::assertIsResource($process);
        fclose($pipes[0]);
        fclose($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        self::assertSame(1, proc_close($process));
        self::assertStringContainsString('structuredContent or outputSchema, not isError', $stderr);
    }

    /**
     * @param list<string> $options
     * @param list<string> $lines
     *
     * @return array<int, array<string, mixed>>
     */
    private function call(array $options, array $lines): array
    {
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/cli', 'tools:proxy', ...$options],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            Paths::root(),
        );
        self::assertIsResource($process);

        fwrite($pipes[0], implode("\n", $lines) . "\n");
        fclose($pipes[0]);

        $stdout = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $status = proc_close($process);
        self::assertSame(0, $status, 'the proxy exited with ' . $status . ': ' . $stderr);

        $responses = [];
        foreach (explode("\n", trim($stdout)) as $line) {
            if (trim($line) === '') {
                continue;
            }
            $decoded = json_decode($line, true);
            self::assertIsArray($decoded, 'the proxy wrote a non-JSON line: ' . $line);
            $responses[$decoded['id'] ?? 0] = $decoded;
        }

        return $responses;
    }

    /** @param array<string, mixed> $params */
    private function request(int $id, string $method, ?array $params = null): string
    {
        $request = ['jsonrpc' => '2.0', 'id' => $id, 'method' => $method];
        if ($params !== null) {
            $request['params'] = $params;
        }

        return (string) json_encode($request);
    }
}
