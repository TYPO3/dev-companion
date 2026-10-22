<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Server;

use Mcp\Server\Authorization\AccessToken;
use Mcp\Server\Transport\StdioTransport as SdkStdioTransport;
use Symfony\Component\Uid\Uuid;

/**
 * The SDK's stdio transport, minus its answer to a notification that arrives
 * before the connection has a session.
 *
 * The handshake dispatcher answers such a line with an error that carries no
 * id, and JSON-RPC answers no notification. An SDK release that stops doing so
 * replaces this class whole — `D-ANS-184`.
 */
final class StdioTransport extends SdkStdioTransport
{
    protected function handleMessage(string $payload, ?Uuid $sessionId, ?AccessToken $accessToken = null): void
    {
        $decoded = json_decode($payload, true);
        $isNotification = is_array($decoded) && !array_is_list($decoded)
            && is_string($decoded['method'] ?? null) && !array_key_exists('id', $decoded);

        if ($sessionId === null && $isNotification) {
            return;
        }

        parent::handleMessage($payload, $sessionId, $accessToken);
    }
}
