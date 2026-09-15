<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Entries;
use TYPO3\DevCompanion\Upkeep\Entry;

/**
 * What the records say about a piece of this code, read before the change.
 *
 * The attributes answer from the failure end and this is the other one, so it
 * has to be honest about its own reach. A name it does not find is a name
 * nobody wrote in backticks, never a statement that nobody decided.
 */
final class EntriesTest extends TestCase
{
    /** The corpora are one list here, and an id is in exactly one of them. */
    #[Decision('D-DOC-050')]
    #[Test]
    public function bothCorporaAreOneListKeyedById(): void
    {
        $entries = Entries::all();

        self::assertNotSame([], $entries);
        foreach ($entries as $id => $entry) {
            self::assertSame($id, $entry['id']);
            self::assertNotSame('', $entry['title'], $id . ' is listed without its title');
            self::assertStringStartsWith(
                str_starts_with($id, 'D-') ? 'decisions/' : 'requirements/',
                $entry['file'],
                $id . ' is filed in the other corpus',
            );
        }
    }

    /**
     * The generated list replaces whatever the key came with, in every shape an
     * entry has ever carried it.
     *
     * `not guarded` is the one the writer missed. The writer matched the empty
     * and the `[]` forms alone. So an entry that says in words that nothing
     * holds it fell through to the branch that adds a key. It came out with two
     * `coveredBy:`, which is one document with two answers to one question. It
     * reached `main` on 2026-08-27 in `D-DOC-060`, written `not guarded` and
     * covered in the same run.
     */
    #[Decision('D-DOC-048')]
    #[DataProvider('keysAnEntryCarries')]
    #[Test]
    public function aGeneratedListReplacesTheValueTheKeyAlreadyCarried(string $already): void
    {
        $entry = "---\nid: D-XXX-001\n" . $already . "---\n\n# D-XXX-001 — A title\n";

        $written = Entry::withNames($entry, 'coveredBy', ['SomeTest::aCaseThatHoldsIt']);

        self::assertSame(1, substr_count($written, 'coveredBy:'), 'the entry answers the question twice');
        self::assertStringContainsString("coveredBy:\n  - SomeTest::aCaseThatHoldsIt\n", $written);
        self::assertStringContainsString('# D-XXX-001 — A title', $written, 'the entry itself was rewritten');
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function keysAnEntryCarries(): array
    {
        return [
            'nothing holds it, said in words' => ["coveredBy: not guarded\n"],
            'nothing holds it, said as a list' => ["coveredBy: []\n"],
            'one test holds it' => ["coveredBy:\n  - OldTest::whatUsedToHoldIt\n"],
            'nobody has been asked' => [''],
        ];
    }

    /**
     * What a person wrote to say nothing holds an entry stays in their words.
     *
     * `not guarded` and `[]` say the same thing to every reader here, and only
     * one of them says somebody got the question and answered. So the generated
     * half may not replace it with the other without a word.
     */
    #[Decision('D-DOC-048')]
    #[Test]
    public function anEntryNothingHoldsKeepsTheWordsItSaidSoIn(): void
    {
        $entry = "---\nid: D-XXX-001\ncoveredBy: not guarded\n---\n\n# D-XXX-001 — A title\n";

        self::assertSame($entry, Entry::withNames($entry, 'coveredBy', []));
    }

    /** A path answers with what it declares, a file or a directory. */
    #[Decision('D-DOC-050')]
    #[Test]
    public function aPathAnswersWithTheClassesItDeclares(): void
    {
        self::assertSame(['Entries'], Entries::declaredBelow('src/Upkeep/Entries.php'));
        self::assertContains('Entries', Entries::declaredBelow('src/Upkeep'));
        self::assertSame([], Entries::declaredBelow('src/Upkeep/NoSuchFile.php'));
    }

    /**
     * Every entry in the answer for a class names it, and a class nothing names
     * gets nothing rather than a guess.
     */
    #[Decision('D-DOC-050')]
    #[Test]
    public function anEntryIsAnsweredForTheClassItNames(): void
    {
        $entries = Entries::all();
        $naming = Entries::naming(['Wrap', 'Entries']);

        self::assertNotSame([], $naming, 'nothing is written about the prose the repository rewraps');
        foreach ($naming as $id => $named) {
            self::assertArrayHasKey($id, $entries);
            $body = (string) file_get_contents(Paths::root() . '/' . $entries[$id]['file']);
            foreach ($named as $class) {
                self::assertStringContainsString('`' . $class, $body, $id . ' is answered for ' . $class);
            }
        }

        self::assertSame([], Entries::naming(['NoSuchClassOfOurs']));
        self::assertSame([], Entries::naming([]));
    }

    /** A test class appears where it names the class and holds an entry. */
    #[Decision('D-DOC-050')]
    #[Test]
    public function aTestIsListedWhereItNamesTheClassAndHoldsAnEntry(): void
    {
        $tests = Entries::testsNaming(['Wrap']);

        self::assertArrayHasKey('ProseTest', $tests);
        self::assertContains('D-DOC-035', $tests['ProseTest']);
        self::assertArrayNotHasKey('ForgeTest', $tests, 'a test that does not name it is listed');
        self::assertSame([], Entries::testsNaming([]));
    }
}
