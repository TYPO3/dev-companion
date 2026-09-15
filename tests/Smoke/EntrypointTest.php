<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;

/**
 * What the entrypoint does with an argument that is not a command.
 *
 * The server is what runs on no argument, so every other word has to end in a
 * message and an exit code. Falling through to the transport instead leaves
 * whoever typed it in front of a process that reads stdin forever.
 *
 * What it says when the server does start belongs here for the same reason.
 * stdout is the protocol from the first line on, so a diagnostic goes to
 * stderr, and only a subprocess shows that the two are apart.
 */
final class EntrypointTest extends TestCase
{
    #[Requirement('R-COD-001')]
    #[Test]
    public function helpNamesTheCommandsAndTheClientsTheyTake(): void
    {
        $stderr = '';
        $stdout = '';
        self::assertSame(0, $this->execute(['help'], $stdout, $stderr), $stderr);
        self::assertStringContainsString('Usage: typo3-dev-companion', $stdout);
        self::assertStringContainsString('install', $stdout);
        self::assertStringContainsString('update', $stdout);
        self::assertStringContainsString('codex', $stdout);
        self::assertSame('', $stderr);
    }

    #[Test]
    public function unknownCommandFailsInsteadOfStartingTheServer(): void
    {
        $stderr = '';
        $stdout = '';
        self::assertSame(2, $this->execute(['bogus'], $stdout, $stderr));
        self::assertStringContainsString('no such command "bogus"', $stderr);
        self::assertStringContainsString('Usage: typo3-dev-companion', $stderr);
        self::assertSame('', $stdout);
    }

    /**
     * A tool name the caller excluded that no tool answers to, over the two
     * streams a client actually reads.
     *
     * `a4470ee` renamed `typo3_project_scope`, and the caller who had excluded
     * it got the tool back under its new name. Nothing on either side said so,
     * because nothing reads the list against the registry. stderr says it now,
     * and this holds the half that matters more. stdout carries the protocol
     * and nothing else, whatever stands beside it — `D-AUD-005`.
     */
    #[Decision('D-AUD-005')]
    #[Test]
    public function anExcludedNameNoToolAnswersToIsSaidOnStderr(): void
    {
        $stderr = '';
        $stdout = '';
        $status = $this->execute([], $stdout, $stderr, [
            'TYPO3_DEV_COMPANION_EXCLUDE_TOOLS' => 'typo3_project_scope, typo3_icon_lookup',
        ], implode("\n", [
            '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-11-25",'
                . '"capabilities":{},"clientInfo":{"name":"phpunit","version":"1"}}}',
            '{"jsonrpc":"2.0","method":"notifications/initialized"}',
            '{"jsonrpc":"2.0","id":2,"method":"tools/list"}',
        ]) . "\n");

        self::assertSame(0, $status, 'a stale exclusion took the server down: ' . $stderr);
        self::assertStringContainsString('TYPO3_DEV_COMPANION_EXCLUDE_TOOLS', $stderr);
        self::assertStringContainsString('typo3_project_scope', $stderr);
        self::assertStringNotContainsString('typo3_icon_lookup', $stderr, 'a real exclusion is not a problem');

        $offered = [];
        foreach (explode("\n", trim($stdout)) as $line) {
            $decoded = json_decode(trim($line), true);
            self::assertIsArray($decoded, 'the server wrote a non-JSON line: ' . $line);
            foreach ($decoded['result']['tools'] ?? [] as $tool) {
                $offered[] = $tool['name'];
            }
        }

        self::assertNotContains('typo3_icon_lookup', $offered);
        self::assertContains('typo3_project_describe', $offered, 'the renamed tool is what the caller now gets');
    }

    /**
     * The other name that takes nothing away: one of the three `R-SCO-009` says
     * a caller cannot exclude. It said nothing at all before, on either stream,
     * while the instructions claimed the tool was gone. Measured 2026-08-04
     * with `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS=typo3_feedback_record`: 26 tools
     * offered, that one among them — `D-AUD-006`.
     */
    #[Decision('D-AUD-006')]
    #[Test]
    public function anExcludedNameThisServerOffersAnywayIsSaidOnStderrToo(): void
    {
        $stderr = '';
        $stdout = '';
        $status = $this->execute([], $stdout, $stderr, [
            'TYPO3_DEV_COMPANION_EXCLUDE_TOOLS' => 'typo3_server_scope',
        ], implode("\n", [
            '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-11-25",'
                . '"capabilities":{},"clientInfo":{"name":"phpunit","version":"1"}}}',
            '{"jsonrpc":"2.0","method":"notifications/initialized"}',
            '{"jsonrpc":"2.0","id":2,"method":"tools/list"}',
        ]) . "\n");

        self::assertSame(0, $status, $stderr);
        self::assertStringContainsString('offers whatever the variable says', $stderr);
        self::assertStringContainsString('typo3_server_scope', $stderr);

        $instructions = [];
        foreach (explode("\n", trim($stdout)) as $line) {
            $decoded = json_decode(trim($line), true);
            self::assertIsArray($decoded, 'the server wrote a non-JSON line: ' . $line);
            $instructions[] = $decoded['result']['instructions'] ?? '';
        }

        self::assertStringNotContainsString(
            'left out of your tool list',
            implode('', $instructions),
            'the instructions claimed a tool was gone that the same server had just offered',
        );
    }

    /**
     * @param array<int, string> $arguments
     * @param array<string, string> $environment
     */
    private function execute(
        array $arguments,
        string &$stdout,
        string &$stderr,
        array $environment = [],
        string $input = '',
    ): int {
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/typo3-dev-companion', ...$arguments],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            Paths::root(),
            array_merge(getenv(), $environment),
        );
        self::assertIsResource($process);
        if ($input !== '') {
            fwrite($pipes[0], $input);
        }
        fclose($pipes[0]);
        $stdout = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return proc_close($process);
    }
}
