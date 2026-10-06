<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Server;

use Mcp\Schema\Enum\ProtocolVersion;
use Mcp\Schema\JsonRpc\Error;
use Mcp\Server\Transport\StdioTransport;
use Mcp\Server\Wire\InboundClassifier;
use Symfony\Component\Uid\Uuid;

/**
 * The SDK's stdio transport, with an answer for a request in the
 * `2026-07-28` envelope.
 *
 * The SDK serves that revision from `StreamableHttpTransport` alone. Over stdio
 * it hands such a request to the handshake dispatcher, which answers
 * `server/discover` with an error that carries no id. A client then has nothing
 * to fall back from. This answers what HTTP answers where nothing serves the
 * modern era: the unsupported revision, the handshake revisions, and the
 * request's own id — `D-ANS-171`.
 */
final class HandshakeStdioTransport extends StdioTransport
{
    protected function handleMessage(string $payload, ?Uuid $sessionId): void
    {
        $classification = (new InboundClassifier())->classify('POST', $payload);

        if (!$classification->modern && !$classification->isRejected()) {
            parent::handleMessage($payload, $sessionId);

            return;
        }

        // A batch the classifier refused carries no id of its own. A single
        // message without one is a notification, which gets no answer.
        $message = (array) json_decode($payload, true);
        $id = $message['id'] ?? null;
        if (!array_is_list($message) && !is_string($id) && !is_int($id)) {
            return;
        }

        $error = $classification->error ?? Error::forUnsupportedProtocolVersion(
            (string) $classification->claimedVersion,
            ProtocolVersion::handshakeVersions(),
            is_string($id) || is_int($id) ? $id : null,
        );
        $this->send((string) json_encode($error, JSON_UNESCAPED_SLASHES), []);
    }
}
