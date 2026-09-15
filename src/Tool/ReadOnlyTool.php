<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

/**
 * A tool that only reads bundled knowledge or the installation it started in:
 * same arguments, same answer, no side effect, nothing outside this package.
 *
 * That is every tool but typo3_feedback_record, which is why the annotations
 * stand once here rather than twenty-one times. A tool that reaches outside
 * says so in the one hint that changes and inherits the rest.
 */
abstract class ReadOnlyTool implements Tool
{
    /**
     * Whether the answer comes from a host this package does not own, which is
     * the one annotation that varies. The tools that reach one are the manuals
     * and their permalinks, the tracker, the review server, the registry, and
     * the changelog above the installed major.
     */
    protected const OPEN_WORLD = false;

    public static function annotations(): array
    {
        return [
            'readOnlyHint' => true,
            'destructiveHint' => false,
            'idempotentHint' => true,
            'openWorldHint' => static::OPEN_WORLD,
        ];
    }

    /**
     * The input schema of a tool that takes no arguments.
     *
     * @return array<string, mixed>
     */
    protected static function noArguments(): array
    {
        return ['type' => 'object', 'properties' => new \stdClass()];
    }
}
