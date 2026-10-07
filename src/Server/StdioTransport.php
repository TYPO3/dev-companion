<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Server;

use Mcp\Schema\JsonRpc\Error;
use Mcp\Server\Stateless\StatelessProtocol;
use Mcp\Server\Transport\StatelessAwareTransportInterface;
use Mcp\Server\Transport\StdioTransport as SdkStdioTransport;
use Mcp\Server\Wire\InboundClassifier;
use Symfony\Component\Uid\Uuid;

/**
 * The SDK's stdio transport, serving the `2026-07-28` revision beside the
 * handshake ones.
 *
 * The client's first request settles the era for the life of the process, and
 * a later request from the other era is refused. That is what
 * modelcontextprotocol/php-sdk#537 does in the SDK's own transport, with the
 * same names, so its release replaces this class whole — `D-ANS-175`.
 */
final class StdioTransport extends SdkStdioTransport implements StatelessAwareTransportInterface
{
    private const CANCELLED_NOTIFICATION = 'notifications/cancelled';

    private StatelessProtocol $stateless;

    /** Null until the client's first request settles the era. */
    private ?bool $modern = null;

    /**
     * `subscriptions/listen` answers still open, by the id of the request.
     *
     * @var array<string|int, \Generator<mixed>>
     */
    private array $streams = [];

    /** How many messages arrived, so a tick knows whether it read one. */
    private int $received = 0;

    public function connectStateless(StatelessProtocol $protocol): void
    {
        $this->stateless = $protocol;
    }

    /**
     * The SDK pauses a listen stream between two polls by itself, which the
     * pull request turns off for stdio. So the streams are polled on a tick
     * that read nothing, where the loop would wait anyway.
     */
    protected function processInput(): void
    {
        $received = $this->received;
        parent::processInput();

        if ($this->received === $received) {
            $this->processStreams();
        }
    }

    protected function handleMessage(string $payload, ?Uuid $sessionId): void
    {
        ++$this->received;
        $classification = (new InboundClassifier())->classify('POST', $payload);

        if ($classification->isRejected()) {
            \assert($classification->error !== null);
            $this->writeError($classification->error);

            return;
        }

        $decoded = json_decode($payload, true);
        $request = is_array($decoded) && !array_is_list($decoded) && isset($decoded['id']) ? $decoded : null;

        if ($this->modern === null && $request !== null) {
            $this->modern = $classification->modern;
        }

        // A notification claims no revision. Before the first request the
        // handshake dispatcher answers it with an error that carries no id,
        // so it goes to the modern one, which acknowledges it with nothing.
        if ($this->modern !== false) {
            $this->routeModern($payload, $decoded, $request);

            return;
        }

        if ($classification->modern && $request !== null) {
            $this->writeError(Error::forInvalidRequest(
                'This connection opened with the "initialize" handshake; '
                . 'a request carrying a per-request protocol version cannot follow it.',
                $request['id'],
            ));

            return;
        }

        parent::handleMessage($payload, $sessionId);
    }

    /** @param array<string, mixed>|null $request the message where it is a request */
    private function routeModern(string $payload, mixed $decoded, ?array $request): void
    {
        // stdio has no stream per request to close, so this is how a client
        // stops one.
        if (is_array($decoded) && ($decoded['method'] ?? null) === self::CANCELLED_NOTIFICATION && $request === null) {
            $requestId = $decoded['params']['requestId'] ?? null;
            if (is_string($requestId) || is_int($requestId)) {
                unset($this->streams[$requestId]);
            }

            return;
        }

        // The SDK on v0.8.1 answers it as a method the revision removed. The
        // pull request names the revisions instead, since such a client has
        // no way to move to this one.
        if ($request !== null && $request['method'] === 'initialize'
            && !isset($request['params']['_meta']['io.modelcontextprotocol/protocolVersion'])
        ) {
            $offered = $request['params']['protocolVersion'] ?? null;
            $this->writeError(Error::forUnsupportedProtocolVersion(
                is_string($offered) ? $offered : '',
                $this->stateless->supportedVersions(),
                $request['id'],
            ));

            return;
        }

        $result = $this->stateless->handle($payload);

        if ($result->isEmpty()) {
            return;
        }

        if ($result->isStream()) {
            \assert($result->frames !== null && $request !== null);
            $this->streams[$request['id']] = ($result->frames)();
            $this->processStreams();

            return;
        }

        $this->send($result->toJson(), []);
    }

    /** Writes what each open stream has ready, up to its next idle poll. */
    private function processStreams(): void
    {
        foreach ($this->streams as $id => $frames) {
            while ($frames->valid()) {
                $frame = $frames->current();
                $frames->next();

                if ($frame === null) {
                    break;
                }

                $this->send((string) json_encode($frame, JSON_UNESCAPED_SLASHES), []);
            }

            if (!$frames->valid()) {
                unset($this->streams[$id]);
            }
        }
    }

    private function writeError(Error $error): void
    {
        $this->send((string) json_encode($error, JSON_UNESCAPED_SLASHES), []);
    }
}
