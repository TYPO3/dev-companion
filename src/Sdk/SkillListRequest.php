<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Sdk;

use Mcp\Exception\InvalidArgumentException;
use Mcp\Schema\JsonRpc\Request;

/**
 * `skills/list` of the Skills extension: every skill this server serves, with
 * its front matter and its manifest.
 *
 * The cursor is taken and never needed. One page holds every published skill,
 * and the spec forbids a manifest split across two.
 */
final class SkillListRequest extends Request
{
    public function __construct(
        public readonly ?string $cursor = null,
    ) {}

    public static function getMethod(): string
    {
        return 'skills/list';
    }

    protected static function fromParams(?array $params): static
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new InvalidArgumentException('Invalid "cursor" parameter for skills/list.');
        }

        return new self($params['cursor'] ?? null);
    }

    /** @return array{cursor: string}|null */
    protected function getParams(): ?array
    {
        return $this->cursor === null ? null : ['cursor' => $this->cursor];
    }
}
