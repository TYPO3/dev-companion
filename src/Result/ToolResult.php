<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Result;

/**
 * What a tool answers: the text a human (or a model) reads, and the same answer
 * as data.
 *
 * Both halves describe one result and come from the same values, so they cannot
 * drift apart. The text stays the primary answer, because it carries the
 * emphasis, the caveats and the words that make a lookup usable. The data is
 * what a client composes with. Identifiers, commands, paths, scores and levels,
 * with no parse of headings and code fences back out of prose.
 */
final class ToolResult
{
    /** @param array<string, mixed> $data */
    private function __construct(
        public readonly string $text,
        public readonly array $data,
    ) {}

    /** @param array<string, mixed> $data */
    public static function create(string $text, array $data): self
    {
        return new self($text, $data);
    }
}
