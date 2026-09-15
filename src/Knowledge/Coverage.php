<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge;

use TYPO3\DevCompanion\Feedback\Channel;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Server\ExcludedTools;

/**
 * The server's own description of what it knows and which tool answers what.
 *
 * Without it an agent has to discover the shape of the server with calls until
 * one answers. Four prose lookups over one corpus look interchangeable from the
 * outside. The data lives in knowledge/server-scope.json, next to the knowledge
 * it describes, so a new document or catalog announces itself where it lands.
 *
 * `typo3_server_scope` is what hands this to a client. The word this class does
 * not take is `Scope`, which is the one vocabulary that says which kind of work
 * an answer is for. That includes, per topic, the answers described here.
 */
final class Coverage
{
    /**
     * How much of the statement below a client can reliably keep.
     *
     * Measured rather than agreed. The client the recorded runs use cuts at
     * 2048 characters and says so only in its own debug output. So a server
     * that writes more loses the end of what it wrote to nobody's error. The
     * budget covers everything assembled, the exclusion prefix and the write
     * sentence included, because the client counts what it receives.
     */
    public const INSTRUCTIONS_BUDGET = 2048;

    /**
     * The statement clients get at initialize time, so the boundary is clear
     * before the first tool call rather than after a fruitless one.
     *
     * The write sentence comes at the end rather than from the store. Whether
     * this server can write at all depends on the checkout it runs from. A
     * client told "read only" must not then get a tool that creates a file. The
     * statement takes the form this caller gets it in, so the index names no
     * tool the caller excluded. Everything assembled here has to fit what a
     * client keeps, and `R-ANS-013` holds the whole of it, prefix and suffix
     * included, to that budget.
     */
    /**
     * @param string $notice what is wrong with the project this server was
     *     started in, which nothing here can work out; `Installer::NOTICE`
     */
    public static function instructions(string $notice = ''): string
    {
        $instructions = self::offered()['instructions'];
        if (Channel::isAvailable()) {
            $instructions .= ' Every tool here is read-only except typo3_feedback_record, '
                . 'which creates a new markdown feedback under feedback/ and writes nothing else.';
        }

        // In front of it, not behind. What a client does not get belongs before
        // the routing it gets. A client told where to start and only then that
        // a tool is absent hears a claim and then a correction. The notice goes
        // in front of both. It is about files in the project rather than about
        // this server's own surface, and "Otherwise:" has to stay next to the
        // routing it qualifies.
        return $notice . self::exclusionPrefix($notice . $instructions) . $instructions;
    }

    /**
     * What stayed out, said in front of the routing.
     *
     * The names are the useful form and the one that does not fit every time.
     * The list is the caller's and can be most of the server, while the budget
     * does not move. Past that the count stands instead and typo3_server_scope
     * holds the names, which is the one tool no caller can exclude.
     */
    private static function exclusionPrefix(string $rest): string
    {
        $excluded = ExcludedTools::all();
        if ($excluded === []) {
            return '';
        }

        $named = sprintf(
            '%s %s left out of your tool list, and so is anything that routed to %s. Otherwise: ',
            implode(', ', $excluded),
            count($excluded) === 1 ? 'is' : 'are',
            count($excluded) === 1 ? 'it' : 'them',
        );

        return mb_strlen($named . $rest) <= self::INSTRUCTIONS_BUDGET ? $named : sprintf(
            '%d tools are left out of your tool list, and so is anything that routed to them; '
            . 'typo3_server_scope names them. Otherwise: ',
            count($excluded),
        );
    }

    /**
     * The coverage as this client is actually offered it.
     *
     * The stored file describes the whole server, and by default that is what a
     * client gets. Where the caller excluded a tool, no entry may still point
     * at it. A map that routes to something that is not in the list is a broken
     * server as far as a client can tell. The index inside the instructions is
     * such a map, and the one a client reads without asking for it.
     *
     * @return array{
     *     purpose: string,
     *     instructions: string,
     *     covers: array<int, array{topic: string, depth: string, tools: array<int, string>, source: string, scope: Scope}>,
     *     doesNotCover: array<int, array{topic: string, why: string, instead: string}>,
     *     checkoutDiscovery: array<int, array{establish: string, how: string}>,
     *     routing: array<int, array{when: string, call: string}>
     * }
     */
    public static function offered(): array
    {
        $coverage = self::read();
        if (ExcludedTools::all() === []) {
            return $coverage;
        }

        $coverage['instructions'] = self::assembled(self::decoded()['instructions'] ?? null, true);

        $covers = [];
        foreach ($coverage['covers'] as $entry) {
            // The topic stays as long as something still answers it. Which kind
            // of work its answers are for stands per topic and does not decide
            // whether the topic is here. That was the profile's reason, and it
            // read the repository where the caller meant the task.
            $entry['tools'] = array_values(array_filter($entry['tools'], ExcludedTools::offers(...)));
            if ($entry['tools'] !== []) {
                $covers[] = $entry;
            }
        }
        $coverage['covers'] = $covers;

        $offered = static fn(array $entry): bool => !self::namesExcludedTool(implode(' ', $entry));
        $coverage['doesNotCover'] = array_values(array_filter($coverage['doesNotCover'], $offered));
        $coverage['checkoutDiscovery'] = array_values(array_filter($coverage['checkoutDiscovery'], $offered));
        $coverage['routing'] = array_values(array_filter($coverage['routing'], $offered));

        return $coverage;
    }

    /** Whether a rendered entry sends the caller to a tool it is not offered. */
    private static function namesExcludedTool(string $text): bool
    {
        foreach (ExcludedTools::all() as $tool) {
            if (str_contains($text, $tool)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{
     *     purpose: string,
     *     instructions: string,
     *     covers: array<int, array{topic: string, depth: string, tools: array<int, string>, source: string, scope: Scope}>,
     *     doesNotCover: array<int, array{topic: string, why: string, instead: string}>,
     *     checkoutDiscovery: array<int, array{establish: string, how: string}>,
     *     routing: array<int, array{when: string, call: string}>
     * }
     */
    public static function read(): array
    {
        $decoded = self::decoded();

        return [
            'purpose' => (string) ($decoded['purpose'] ?? ''),
            'instructions' => self::assembled($decoded['instructions'] ?? null, false),
            'covers' => array_map(static fn(array $entry): array => [
                'topic' => (string) $entry['topic'],
                'depth' => (string) ($entry['depth'] ?? ''),
                'tools' => array_map('strval', $entry['tools'] ?? []),
                'source' => (string) ($entry['source'] ?? ''),
                // Which kind of work the answers are for: the boundary runs
                // through the middle of this server, not around it.
                'scope' => Scope::from((string) $entry['scope']),
            ], $decoded['covers']),
            'doesNotCover' => array_map(static fn(array $entry): array => [
                'topic' => (string) $entry['topic'],
                'why' => (string) ($entry['why'] ?? ''),
                'instead' => (string) ($entry['instead'] ?? ''),
            ], $decoded['doesNotCover'] ?? []),
            'checkoutDiscovery' => array_map(static fn(array $entry): array => [
                'establish' => (string) $entry['establish'],
                'how' => (string) ($entry['how'] ?? ''),
            ], $decoded['checkoutDiscovery'] ?? []),
            'routing' => array_map(static fn(array $entry): array => [
                'when' => (string) $entry['when'],
                'call' => (string) $entry['call'],
            ], $decoded['routing']),
        ];
    }

    /**
     * The statement, with the index of which tool answers what rendered into it.
     *
     * The index is data rather than a paragraph of the stored prose. So an
     * entry that names an excluded tool can drop out the way a `routing` entry
     * already does. It was the case that bound `R-ANS-013`. A caller that had
     * excluded everything it could still heard which four tools to call, out of
     * the budget. `D-AUD-011` had nothing left to displace for the entry a
     * session had asked for.
     *
     * The heading and the note under the entries travel with them. A caller
     * left with none of them is told nothing about a list it does not have.
     */
    private static function assembled(mixed $stored, bool $offered): string
    {
        $stored = is_array($stored) ? $stored : [];
        $index = array_map(
            static fn(array $entry): string => '- ' . (string) $entry['question'] . ': ' . (string) $entry['call'],
            is_array($stored['index'] ?? null) ? $stored['index'] : [],
        );
        if ($offered) {
            $index = array_filter($index, static fn(string $entry): bool => !self::namesExcludedTool($entry));
        }

        $paragraphs = [(string) ($stored['start'] ?? '')];
        if ($index !== []) {
            // The note is a paragraph of its own. A line under the last index
            // entry reads as part of that entry, and measures as one sentence.
            $paragraphs[] = implode("\n", ['What to call for what:', ...$index]);
            $paragraphs[] = (string) ($stored['note'] ?? '');
        }
        $paragraphs[] = (string) ($stored['then'] ?? '');

        return implode("\n\n", $paragraphs);
    }

    /**
     * The file, held to the two lists nothing here assembles without.
     *
     * @return array<string, mixed>
     */
    private static function decoded(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::knowledgeFile('server-scope.json')), true);
        if (!is_array($decoded) || !isset($decoded['covers'], $decoded['routing'])) {
            throw new \RuntimeException('Invalid server-scope.json');
        }

        return $decoded;
    }
}
