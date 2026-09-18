<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Sdk;

use Mcp\Exception\InvalidArgumentException;
use Mcp\Schema\JsonRpc\Request;

/**
 * `skills/get` of the Skills extension: one skill's entry, by the URI of its
 * body. A host calls it for a URI it holds without the list, and to refresh an
 * entry whose manifest a read no longer matches.
 */
final class SkillGetRequest extends Request
{
    public function __construct(
        public readonly string $uri,
    ) {}

    public static function getMethod(): string
    {
        return 'skills/get';
    }

    protected static function fromParams(?array $params): static
    {
        if (!isset($params['uri']) || !is_string($params['uri'])) {
            throw new InvalidArgumentException('Missing or invalid "uri" parameter for skills/get.');
        }

        return new self($params['uri']);
    }

    /** @return array{uri: string} */
    protected function getParams(): array
    {
        return ['uri' => $this->uri];
    }
}
