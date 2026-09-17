<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Result;

/**
 * What a tool answers: the text a human (or a model) reads, and the same answer
 * as data.
 *
 * Both halves describe one result and come from the same values, so they cannot
 * drift apart. The data is the answer a session acts on: identifiers, commands,
 * paths, scores and levels, with no parse of headings and code fences back out
 * of prose. A client hands the model one half, and the one that hands over the
 * data hands over the preferred one — `D-ANS-162`. The text is the same answer
 * for a client that hands over text, with the emphasis and the caveats a
 * reader wants in prose.
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
