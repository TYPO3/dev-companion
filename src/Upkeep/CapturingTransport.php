<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Mcp\Server\Transport\BaseTransport;

/**
 * A transport that hands requests to the server in this process and returns
 * what the server answered.
 *
 * The SDK's own `InMemoryTransport` drops every answer, because it exists to
 * drive a server rather than to read one. This one keeps them, and drains the
 * queue the protocol puts a session's answers into after each request. So a
 * generator reads the `initialize` result as a client gets it, with nothing
 * started on the machine — `D-DOC-072`.
 *
 * @extends BaseTransport<list<string>>
 */
final class CapturingTransport extends BaseTransport
{
    /** @var list<string> */
    private array $answers = [];

    /**
     * @param list<string> $requests the JSON-RPC messages, in the order a
     *     client would send them
     */
    public function __construct(private readonly array $requests)
    {
        parent::__construct();
    }

    /**
     * @param array<string, mixed> $context
     */
    public function send(string $data, array $context): void
    {
        $this->answers[] = $data;
    }

    /**
     * @return list<string>
     */
    public function listen(): array
    {
        foreach ($this->requests as $request) {
            $this->handleMessage($request, $this->sessionId);
            foreach ($this->getOutgoingMessages($this->sessionId) as $outgoing) {
                $this->answers[] = $outgoing['message'];
            }
        }
        $this->handleSessionEnd($this->sessionId);

        return $this->answers;
    }
}
