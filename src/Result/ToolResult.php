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
 *
 * `owed` is what the caller has to do that no field states beside the value
 * it is about. It stands in the data on every answer, because Claude Code
 * hands the model that half alone (`D-EVI-011`, `R-ANS-002`).
 */
final class ToolResult
{
    /** @param array<string, mixed> $data */
    private function __construct(
        public readonly string $text,
        public readonly array $data,
    ) {}

    /**
     * @param array<string, mixed> $data
     * @param array<int, string> $owed each sentence the text also says
     */
    public static function create(string $text, array $data, array $owed = []): self
    {
        return new self($text, $data + ['owed' => array_values(array_filter(
            $owed,
            static fn(string $sentence): bool => $sentence !== '',
        ))]);
    }

    /**
     * The same answer with notices in front of both halves, which is what the
     * handler adds after the tool has answered.
     *
     * @param array<int, string> $notices
     */
    public function withNotices(array $notices): self
    {
        if ($notices === []) {
            return $this;
        }
        $data = $this->data;
        $data['owed'] = [...$notices, ...(array) ($data['owed'] ?? [])];

        return new self(implode("\n", $notices) . "\n\n" . $this->text, $data);
    }
}
