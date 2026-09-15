<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Support;

/**
 * The decision a test holds, written where the test is.
 *
 * The names have to run both ways. The entry says which test would catch its
 * **Wrong if**, and the test says which entry rests on it. A session that
 * changes a behaviour stands in the test and nowhere else. Written as prose in
 * a docblock, the second half was a sentence anything could drop. Written as an
 * attribute it is data, and `bin/cli decisions:cover` generates the entry's
 * `coveredBy` from it, so the two halves cannot say different things.
 *
 * One per decision, repeated where a test holds more than one, and over the
 * class where the whole class is the answer. `BackendCssTest` is eleven methods
 * about one entry, and the name eleven times says nothing the class does not.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class Decision
{
    public function __construct(public string $id) {}
}
