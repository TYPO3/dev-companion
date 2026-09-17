<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;

/**
 * The server with one half of what it says taken away, for a client to start
 * in its place.
 *
 * This stands between the client and `bin/typo3-dev-companion`, relays every
 * line both ways, and strips `structuredContent` from an answer or
 * `outputSchema` from a definition on the way back. What the model still gets
 * is then the client's doing, and `tools:tokens` reads it — `D-EVI-011`.
 *
 * A rewrite rather than a switch on the server, because a measurement that
 * changes the thing it measures is not one.
 */
#[AsCommand(
    name: 'tools:proxy',
    description: 'relay a client to the server with a half of the answer or the schema taken away, for a measurement',
)]
final class ToolProxy
{
    /** What `--without` takes. Anything else is a typo the caller hears about. */
    public const HALVES = ['structuredContent', 'outputSchema'];

    /**
     * @param list<string> $without
     */
    public function __invoke(
        OutputInterface $output,
        #[Option('what to strip on the way back: structuredContent, outputSchema, or both')]
        array $without = [],
    ): int {
        $unknown = array_diff($without, self::HALVES);
        if ($unknown !== []) {
            fwrite(STDERR, sprintf("tools:proxy strips %s, not %s\n", implode(' or ', self::HALVES), implode(', ', $unknown)));

            return 1;
        }

        // The server starts where the client started this, so discovery reads
        // the same directory it would read without the proxy. Its stderr is
        // this one, so a diagnostic still reaches the client's log.
        $server = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/typo3-dev-companion'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => STDERR],
            $pipes,
        );
        if (!is_resource($server)) {
            fwrite(STDERR, "tools:proxy could not start the server\n");

            return 1;
        }

        self::relay($pipes[0], $pipes[1], $without);

        return proc_close($server);
    }

    /**
     * Every line the client writes goes to the server, every line the server
     * writes comes back rewritten, until the server's stdout closes.
     *
     * @param resource $toServer
     * @param resource $fromServer
     * @param list<string> $without
     */
    private static function relay($toServer, $fromServer, array $without): void
    {
        $streams = [STDIN, $fromServer];
        while (true) {
            $read = $streams;
            $write = null;
            $except = null;
            if (stream_select($read, $write, $except, null) === false) {
                return;
            }
            foreach ($read as $stream) {
                $line = fgets($stream);
                if ($stream === $fromServer) {
                    if ($line === false) {
                        return;
                    }
                    fwrite(STDOUT, self::rewritten($line, $without));
                    fflush(STDOUT);
                    continue;
                }
                if ($line === false) {
                    // The client is done. The server ends when its stdin does.
                    fclose($toServer);
                    $streams = [$fromServer];
                    continue;
                }
                fwrite($toServer, $line);
                fflush($toServer);
            }
        }
    }

    /**
     * One line of the server's, the halves stripped. A line that is no JSON
     * message passes as it came.
     *
     * Decoded as objects rather than arrays, because an empty object decoded
     * as an array comes back as `[]`. The capabilities of the handshake are
     * three of those, and a client reads `[]` as a list it did not ask for.
     *
     * @param list<string> $without
     */
    public static function rewritten(string $line, array $without): string
    {
        $message = json_decode($line);
        if (!$message instanceof \stdClass || $without === []) {
            return $line;
        }

        return json_encode(self::without($message, $without), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    }

    /**
     * One JSON-RPC message with the named halves gone: `structuredContent`
     * from a `tools/call` result, `outputSchema` from every tool of a
     * `tools/list` result. A message that carries neither comes back as it is.
     *
     * @param list<string> $without
     */
    public static function without(\stdClass $message, array $without): \stdClass
    {
        $result = $message->result ?? null;
        if (!$result instanceof \stdClass) {
            return $message;
        }
        if (in_array('structuredContent', $without, true)) {
            unset($result->structuredContent);
        }
        if (in_array('outputSchema', $without, true) && is_array($result->tools ?? null)) {
            foreach ($result->tools as $tool) {
                if ($tool instanceof \stdClass) {
                    unset($tool->outputSchema);
                }
            }
        }

        return $message;
    }
}
