<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use TYPO3\DevCompanion\Knowledge\Coverage;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Server\Factory;

/**
 * The page on what a client gets at `initialize`, and in the lists it fetches
 * right after: the result as the server sends it, the instructions in their
 * wording, the prompts, and the tool list as a count.
 *
 * Rendered from a session run against `Factory::create()` in this process,
 * because the result is the SDK's and no class here spells it out. The
 * capabilities come from what the builder detects, the protocol version from
 * the negotiation, and both would be a copy anywhere else — `D-DOC-072`. The
 * prose above the marker is written by hand and carries over.
 */
final class Handshake
{
    /** The line above the generated half, so the explanation survives a rewrite. */
    private const GENERATED_STARTS = ".. What follows is written by ``bin/cli tools:index``.\n";

    /**
     * The revision the stdio transport speaks, which is what a client offers
     * to get the same answer `StdioServerTest` reads.
     */
    private const PROTOCOL_VERSION = '2025-11-25';

    public static function file(): string
    {
        return Paths::root() . '/documentation/server/initialize.rst';
    }

    public static function page(): string
    {
        $contents = (string) file_get_contents(self::file());
        $start = strpos($contents, self::GENERATED_STARTS);
        if ($start === false) {
            throw new \RuntimeException('The initialize page has no generated-half marker.');
        }

        // Decoded to objects rather than arrays, so what the SDK writes as
        // `{}` prints as one. As an array it would print as a list.
        $result = json_decode(self::session()[1])->result;
        $instructions = (string) $result->instructions;
        unset($result->instructions);
        // The icon bytes are the two signets the site itself draws, and a
        // page that printed them base64 would be two lines nobody reads.
        foreach ($result->serverInfo->icons ?? [] as $icon) {
            $icon->src = substr((string) $icon->src, 0, strpos((string) $icon->src, ',') + 1) . '…';
        }

        $lines = [
            ...Rst::heading('The initialize result', 1),
            ...Rst::code('json', (string) json_encode(
                $result,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            )),
            Wrap::rstText(
                'The two icon sources stand shortened. Each is one of the signets the site draws, '
                . Rst::literal('images/signet-s.svg') . ' and ' . Rst::literal('images/signet-l.svg')
                . ', as base64 in a ' . Rst::literal('data:') . ' URI.',
            ),
            '',
            Wrap::rstText(
                'The fourth member is ' . Rst::literal('instructions') . ', and it reads:',
            ),
            '',
            ...Rst::code('text', $instructions),
            Wrap::rstText(sprintf(
                '%s characters of the %s a client keeps, %s.',
                number_format(mb_strlen($instructions)),
                number_format(Coverage::INSTRUCTIONS_BUDGET),
                Rst::literal('Coverage::INSTRUCTIONS_BUDGET'),
            )),
            '',
            ...Rst::heading('The prompts', 1),
            ...self::prompts(),
            ...Rst::heading('The tool list', 1),
            ...self::tools(),
            ...Rst::heading('The resource template', 1),
            ...self::template(),
        ];

        return substr($contents, 0, $start) . self::GENERATED_STARTS . "\n" . implode("\n", $lines);
    }

    /**
     * @return list<string>
     */
    private static function prompts(): array
    {
        $lines = [];
        foreach (self::listed('prompts/list', 'prompts') as $prompt) {
            $required = [];
            $optional = [];
            foreach ($prompt['arguments'] ?? [] as $argument) {
                if ($argument['required'] ?? false) {
                    $required[] = Rst::literal((string) $argument['name']);
                } else {
                    $optional[] = Rst::literal((string) $argument['name']);
                }
            }
            $takes = match (true) {
                $required === [] && $optional === [] => 'Takes nothing.',
                $optional === [] => 'Takes ' . self::joined($required) . '.',
                $required === [] => 'Takes ' . self::joined($optional) . ', each optional.',
                default => 'Takes ' . self::joined($required) . ', and ' . self::joined($optional) . ' where given.',
            };
            $lines[] = Wrap::rstText(sprintf(
                '- %s — %s. %s %s',
                Rst::literal((string) $prompt['name']),
                (string) $prompt['title'],
                (string) $prompt['description'],
                $takes,
            ), '  ');
        }
        $lines[] = '';

        return $lines;
    }

    /**
     * The list as a count. Its weight is `tools:measure`'s — `D-DOC-072`.
     *
     * @return list<string>
     */
    private static function tools(): array
    {
        $tools = self::listed('tools/list', 'tools');

        return [
            Wrap::rstText(sprintf(
                '%s carries %d tools. Each entry is the %s, %s, %s, %s, %s and %s the tool\'s own page under %s '
                . 'states. %s prints what each entry weighs on the wire, and what all of them weigh together.',
                Rst::literal('tools/list'),
                count($tools),
                Rst::literal('name'),
                Rst::literal('title'),
                Rst::literal('description'),
                Rst::literal('inputSchema'),
                Rst::literal('outputSchema'),
                Rst::literal('annotations'),
                Rst::doc('tools/', 'tools/index'),
                Rst::literal('bin/cli tools:measure'),
            )),
            '',
        ];
    }

    /**
     * @return list<string>
     */
    private static function template(): array
    {
        $lines = [];
        foreach (self::listed('resources/templates/list', 'resourceTemplates') as $template) {
            $lines[] = Wrap::rstText(sprintf(
                '- %s — %s. %s',
                Rst::literal((string) $template['uriTemplate']),
                (string) $template['title'],
                (string) $template['description'],
            ), '  ');
        }
        $lines[] = '';

        return $lines;
    }

    /**
     * Every entry of one list, over as many pages as the server cuts it into.
     *
     * @return list<array<string, mixed>>
     */
    private static function listed(string $method, string $member): array
    {
        $entries = [];
        $cursor = null;
        do {
            $params = $cursor === null ? [] : ['cursor' => $cursor];
            /** @var array{result: array<string, mixed>} $answer */
            $answer = json_decode(self::session([self::request(2, $method, $params)])[2], true);
            $listed = $answer['result'][$member] ?? [];
            foreach (is_array($listed) ? $listed : [] as $entry) {
                if (is_array($entry)) {
                    $entries[] = $entry;
                }
            }
            $cursor = $answer['result']['nextCursor'] ?? null;
        } while (is_string($cursor));

        return $entries;
    }

    /**
     * One session: the handshake, then the requests given, each answer keyed
     * by the id of the request it answers.
     *
     * @param list<string> $requests
     * @return array<int, string>
     */
    private static function session(array $requests = []): array
    {
        $transport = new CapturingTransport([
            self::request(1, 'initialize', [
                'protocolVersion' => self::PROTOCOL_VERSION,
                'capabilities' => new \stdClass(),
                'clientInfo' => ['name' => 'bin/cli', 'version' => Factory::SERVER_VERSION],
            ]),
            (string) json_encode(['jsonrpc' => '2.0', 'method' => 'notifications/initialized']),
            ...$requests,
        ]);

        $answers = [];
        foreach (Factory::create()->run($transport) as $answer) {
            $decoded = json_decode($answer, true);
            if (is_array($decoded) && isset($decoded['id'])) {
                $answers[(int) $decoded['id']] = $answer;
            }
        }

        return $answers;
    }

    /**
     * @param array<string, mixed> $params
     */
    private static function request(int $id, string $method, array $params = []): string
    {
        $request = ['jsonrpc' => '2.0', 'id' => $id, 'method' => $method];
        if ($params !== []) {
            $request['params'] = $params;
        }

        return (string) json_encode($request);
    }


    /**
     * @param list<string> $items
     */
    private static function joined(array $items): string
    {
        if (count($items) < 2) {
            return implode('', $items);
        }
        $last = array_pop($items);

        return implode(', ', $items) . ' and ' . $last;
    }
}
