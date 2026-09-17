<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Process\SystemRunner;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * What one call costs a Claude Code session, measured in the client: with
 * the answer as served, with `structuredContent` gone, with `outputSchema`
 * gone.
 *
 * `tools:measure` counts bytes and says nothing about which half reaches the
 * model. This runs `claude -p` three times through `tools:proxy` and reads
 * the trace: what the `tool_result` block carried, and what the request
 * after it cost above the request before it. So the number is the model's
 * own count, for the one client whose trace says both — `D-EVI-011`.
 *
 * It costs money and a few minutes, and it needs `claude` on the machine. So
 * it is a command a session runs on purpose and nothing in CI.
 */
#[AsCommand(
    name: 'tools:tokens',
    description: 'what one call costs a Claude Code session, in tokens, with either half of the answer and without the schema',
)]
final class ToolTokens
{
    /** What the client gets to hand the model, per mode, as `tools:proxy --without` spells it. */
    private const MODES = [
        'as served' => [],
        'without structuredContent' => ['structuredContent'],
        'without outputSchema' => ['outputSchema'],
    ];

    /** The name the client gives the server in its configuration, and so in every tool name. */
    private const SERVER = 'typo3';

    private readonly CommandRunner $runner;

    public function __construct(?CommandRunner $runner = null)
    {
        $this->runner = $runner ?? new SystemRunner();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument('the tool to call')]
        string $tool = 'typo3_hint_lookup',
        #[Argument('its arguments, as JSON')]
        string $arguments = '{"task":"backend module registration"}',
        #[Option('how many sessions per mode; the model varies a little between them')]
        int $runs = 3,
        #[Option('the model every session runs on')]
        string $model = 'claude-haiku-4-5-20251001',
    ): int {
        if ($this->runner->locate('claude') === null) {
            Voice::problem($output, 'claude is not on this machine, and this measures what it hands the model.');

            return 1;
        }
        if (!is_array(json_decode($arguments, true))) {
            Voice::problem($output, 'The arguments are not a JSON object: ' . $arguments);

            return 1;
        }

        $directory = self::directory();
        $configurations = self::configurations($directory);
        $prompt = self::prompt($tool, $arguments);
        $bar = Voice::progress($output, count(self::MODES) * $runs);
        $bar->start();
        $rows = [];
        foreach (array_keys(self::MODES) as $mode) {
            for ($run = 1; $run <= $runs; ++$run) {
                $bar->setMessage(sprintf('%s, run %d', $mode, $run));
                $trace = $this->runner->run(
                    ['env', '-u', 'CLAUDECODE', 'MAX_THINKING_TOKENS=0', 'claude', '-p', $prompt,
                        '--output-format', 'stream-json', '--verbose', '--max-turns', '8',
                        '--model', $model, '--mcp-config', $configurations[$mode], '--strict-mcp-config',
                        '--allowedTools', 'mcp__' . self::SERVER . '__' . $tool, '--disable-slash-commands'],
                    Paths::root(),
                    600,
                );
                file_put_contents(sprintf('%s/%s-%d.jsonl', $directory, str_replace(' ', '-', $mode), $run), $trace['output']);
                $rows[$mode][] = self::read($trace['output'], $tool);
                $bar->advance();
            }
        }
        $bar->finish();
        $bar->clear();

        Voice::heading($output, sprintf('%s %s on %s', $tool, $arguments, $model));
        foreach ($rows as $mode => $reads) {
            foreach ($reads as $run => $read) {
                $output->writeln(sprintf(
                    '%s run %d: %s',
                    Voice::key($mode, 28),
                    $run + 1,
                    $read['received'] === null
                        ? 'the tool was not called' . ($read['error'] !== '' ? ' — ' . $read['error'] : '')
                        : sprintf(
                            'the model got %s of %s characters for %s; the definition before it %s',
                            $read['received']['kind'],
                            number_format($read['received']['chars']),
                            $read['cost'] === null ? 'a cost the trace does not show' : number_format($read['cost']) . ' tokens',
                            $read['definition'] === null ? 'came with the list' : 'cost ' . number_format($read['definition']),
                        ),
                ));
            }
        }
        Voice::note(
            $output,
            'A cost is one request above the one before it, with thinking off. The result carries the '
            . 'tool_result block and the tool_use that asked for it. The definition carries what the client '
            . 'loaded on demand and the search that found it. The traces are below ' . $directory . '.'
        );

        return 0;
    }

    /**
     * One trace of `claude -p --output-format stream-json`, read for what the
     * model got and what it paid.
     *
     * `received` is the `tool_result` block the client put in front of the
     * model for this tool's call: JSON where the client handed over the data
     * half as a string, text where it handed over the text block. `cost` is
     * the input of the request after that block, less the input of the
     * request that asked for the call. A request's input is its three counts
     * together, because what the cache serves is still in front of the model.
     * The difference carries the block and the `tool_use` that asked for it,
     * which is a few dozen tokens, since the run thinks nothing. `definition`
     * is the same difference one request earlier: what the tool's definition
     * cost once the client loaded it, where the client loads a tool on demand.
     * A client that sends the whole list at once has no such request, and
     * the field is null.
     *
     * @return array{received: ?array{kind: string, chars: int}, cost: ?int, definition: ?int, error: string}
     */
    public static function read(string $trace, string $tool): array
    {
        $requests = [];
        $calls = [];
        $received = null;
        $error = '';
        foreach (preg_split('/\R/', $trace) ?: [] as $line) {
            $event = json_decode($line, true);
            if (!is_array($event)) {
                continue;
            }
            $type = $event['type'] ?? '';
            if ($type === 'assistant') {
                $message = $event['message'];
                $usage = $message['usage'] ?? [];
                $id = (string) ($message['id'] ?? count($requests));
                $requests[$id] = ['input' => (int) ($usage['input_tokens'] ?? 0)
                    + (int) ($usage['cache_creation_input_tokens'] ?? 0)
                    + (int) ($usage['cache_read_input_tokens'] ?? 0), 'calls' => $requests[$id]['calls'] ?? false];
                foreach ($message['content'] ?? [] as $block) {
                    if (($block['type'] ?? '') === 'tool_use' && str_ends_with((string) $block['name'], '__' . $tool)) {
                        $requests[$id]['calls'] = true;
                        $calls[] = (string) $block['id'];
                    }
                }
            }
            if ($type === 'user' && is_array($event['message']['content'] ?? null)) {
                foreach ($event['message']['content'] as $block) {
                    if (($block['type'] ?? '') !== 'tool_result' || !in_array($block['tool_use_id'] ?? '', $calls, true)) {
                        continue;
                    }
                    $content = $block['content'] ?? '';
                    if (is_array($content)) {
                        $content = (string) ($content[0]['text'] ?? '');
                    }
                    $received ??= ['kind' => str_starts_with((string) $content, '{') ? 'JSON' : 'text', 'chars' => mb_strlen((string) $content)];
                }
            }
            if ($type === 'result' && ($event['is_error'] ?? false)) {
                $error = (string) ($event['subtype'] ?? 'error');
            }
        }

        $cost = null;
        $definition = null;
        $sequence = array_values($requests);
        foreach ($sequence as $index => $request) {
            if (!$request['calls']) {
                continue;
            }
            if (isset($sequence[$index + 1])) {
                $cost = $sequence[$index + 1]['input'] - $request['input'];
            }
            if ($index > 0) {
                $definition = $request['input'] - $sequence[$index - 1]['input'];
            }
            break;
        }

        return ['received' => $received, 'cost' => $cost, 'definition' => $definition, 'error' => $error];
    }

    /** Where the configurations and the traces go, so a reader can open a run. */
    private static function directory(): string
    {
        $directory = sys_get_temp_dir() . '/typo3-dev-companion-tokens';
        if (!is_dir($directory)) {
            mkdir($directory, 0o777, true);
        }

        return $directory;
    }

    /**
     * One MCP configuration per mode, each starting `tools:proxy` with that
     * mode's `--without`.
     *
     * @return array<string, string> the file per mode
     */
    private static function configurations(string $directory): array
    {
        $files = [];
        foreach (self::MODES as $mode => $without) {
            $arguments = [Paths::root() . '/bin/cli', 'tools:proxy'];
            foreach ($without as $half) {
                $arguments[] = '--without=' . $half;
            }
            $file = $directory . '/' . str_replace(' ', '-', $mode) . '.json';
            file_put_contents($file, json_encode(
                ['mcpServers' => [self::SERVER => ['command' => PHP_BINARY, 'args' => $arguments]]],
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES,
            ));
            $files[$mode] = $file;
        }

        return $files;
    }

    /**
     * What the session is asked. One call with these arguments and nothing
     * else, so the request after it is the one with the result in it.
     */
    private static function prompt(string $tool, string $arguments): string
    {
        return sprintf(
            'Call the tool mcp__%s__%s exactly once with %s and nothing else. '
            . 'Then answer with one line: how many characters the tool result you received had, '
            . 'and whether it was JSON or Markdown text.',
            self::SERVER,
            $tool,
            $arguments,
        );
    }
}
