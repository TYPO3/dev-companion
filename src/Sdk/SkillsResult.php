<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Sdk;

use Mcp\Schema\Enum\CacheScope;
use Mcp\Schema\Enum\ResultType;
use Mcp\Schema\JsonRpc\ResultInterface;

/**
 * The envelope both methods of the Skills extension answer in.
 *
 * `resultType`, `ttlMs` and `cacheScope` are required on both, and the SDK's
 * 2026 codec stamps them on its own methods alone. So the result carries them
 * itself, on every revision. Everything listed is a file this package ships,
 * the same for every client, so the scope is public. The ttl is the spec's own
 * example, since nothing here changes between two releases of the package and
 * a host that finds a digest wrong refreshes the entry anyway.
 */
final class SkillsResult implements ResultInterface
{
    private const TTL_MS = 300_000;

    /** @param array<string, mixed> $payload `skills` for the list, `skill` for one entry */
    public function __construct(
        private readonly array $payload,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return [
            'resultType' => ResultType::Complete->value,
            ...$this->payload,
            'ttlMs' => self::TTL_MS,
            'cacheScope' => CacheScope::Public->value,
        ];
    }
}
