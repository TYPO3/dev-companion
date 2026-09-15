<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Sources;

/**
 * What the records claim about the code they name.
 *
 * A backticked name this repository owns is a claim that the thing exists now,
 * and four guards already say that of four kinds. A tool in `ToolNamingTest`, a
 * test method and a requirement id in `DecisionsTest`, a decision id and a test
 * in `bin/cli requirements:check`. This is the fifth, a member of one of this
 * repository's own classes, and nothing watched it. 24 of the 1673 such
 * references were false on 2026-08-22, every one of them in `decisions/`.
 *
 * The escape is `D-DOC-040`'s and needs no list. A name under discussion rather
 * than a pointer stands plain, which is what an entry that records a gone
 * member does.
 */
final class RecordsTest extends TestCase
{
    /** Where a name is a claim about the code as it is now. */
    private const CORPORA = ['decisions', 'requirements', 'documentation', 'todo', 'skills', 'knowledge'];

    /**
     * Members every class has without a declaration. A magic method is the
     * language's, and the language generates the three enum readers for a
     * backed case list. `D-KNW-005` names `Scope::from()`, and no file declares
     * it.
     */
    private const LANGUAGE_MEMBERS = ['from', 'tryFrom', 'cases', 'class'];

    /**
     * A member the records name in backticks exists on the class they name.
     *
     * The two the corpus proved necessary are both about a name that looks like
     * ours and is not. A magic method, and a class this repository shares a
     * name with: `Site::__construct()` in `D-KNW-097` is TYPO3's `Site` and not
     * the one below `src/Upkeep/`. Nothing tells those apart from the name, so
     * a class we share is a hole `D-DOC-042` states rather than closes.
     */
    #[Decision('D-DOC-042')]
    #[Test]
    public function everyMemberTheRecordsNameInBackticksExists(): void
    {
        $classes = Sources::classes();
        $members = Sources::members();

        $missing = [];
        foreach ($this->recordFiles() as $path) {
            preg_match_all('/`(\w+)::(\w+)\(?\)?`/', (string) file_get_contents($path), $matches, PREG_SET_ORDER);
            foreach ($matches as $named) {
                [, $class, $member] = $named;
                if (!isset($classes[$class]) || isset($members[$class . '::' . $member])) {
                    continue;
                }
                if (str_starts_with($member, '__') || in_array($member, self::LANGUAGE_MEMBERS, true)) {
                    continue;
                }
                $missing[] = basename($path) . ': ' . $class . '::' . $member;
            }
        }

        self::assertSame([], array_values(array_unique($missing)), 'named in backticks by a record, and the class has no such member');
    }

    /** @return array<int, string> */
    private function recordFiles(): array
    {
        $paths = [];
        foreach (self::CORPORA as $corpus) {
            foreach (Finder::create()->files()->in(Paths::root() . '/' . $corpus)->name(['*.md', '*.rst', '*.json'])->sortByName() as $file) {
                $paths[] = $file->getPathname();
            }
        }

        return $paths;
    }
}
