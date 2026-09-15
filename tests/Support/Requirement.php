<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Support;

/**
 * The requirement a test holds, written where the test is.
 *
 * The same tie `Decision` carries, for the corpus that says what must be true.
 * `bin/cli requirements:cover` writes the entry's `heldBy` from these, so a
 * renamed test rewrites the entry rather than orphans a name in it.
 *
 * On the class where the class as a whole is the answer. `VersionsTest` in full
 * is a claim about every method in it, and a name per method would go stale on
 * the next one written.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class Requirement
{
    public function __construct(public string $id) {}
}
