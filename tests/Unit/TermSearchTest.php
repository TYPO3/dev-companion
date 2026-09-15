<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Search\TermSearch;
use TYPO3\DevCompanion\Search\Text;
use TYPO3\DevCompanion\Tests\Support\Decision;

/**
 * Which words of a query the search reads at all.
 *
 * The floor used to be three characters, and a ViewHelper named after an
 * English keyword fell under it. `Global/If.html` of the ViewHelper reference
 * has the title "if", and `f:if` left nothing to search for once the tokenizer
 * had run (`D-ANS-023`). What decided the floor is not what a short word is
 * worth but how it matches. Below four characters it matches as a whole word.
 */
final class TermSearchTest extends TestCase
{
    /**
     * The floor is two characters, so `f:if` leaves something to search for.
     * The stop list rather than the length answers what a two-letter word costs
     * — `D-ANS-028`.
     */
    #[Decision('D-ANS-028')]
    #[Test]
    public function aTwoLetterWordIsATerm(): void
    {
        self::assertSame(['if'], TermSearch::terms('f:if'));
        self::assertSame(['if', 'then', 'else', 'condit'], TermSearch::terms('f:if f:then f:else condition'));
    }

    /**
     * A word behind a namespace prefix is the name of a thing rather than the
     * English word it looks like. So the stopword list does not reach it.
     * `f:or` and `f:then` otherwise have no term at all (`D-ANS-047`).
     */
    #[Decision('D-ANS-047')]
    #[Test]
    public function aWordBehindANamespacePrefixIsNotAStopword(): void
    {
        self::assertSame(['or'], TermSearch::terms('f:or'));
        self::assertSame(['then'], TermSearch::terms('f:then'));
        self::assertSame(['ext', 'core'], TermSearch::terms('EXT:core'));
    }

    /**
     * The colon has to touch both sides. A sentence puts a space after it, and
     * that is what keeps the same word a stopword in prose. Seven of the 41
     * scenario prompts say "or" or "then" in a sentence — `D-ANS-047`.
     */
    #[Decision('D-ANS-047')]
    #[Test]
    public function theSameWordAfterTheColonOfASentenceIsNot(): void
    {
        self::assertSame(['note', 'label', 'wrong'], TermSearch::terms('Note: the label is wrong'));
        self::assertSame(['fix', 'that', 'take', 'throug', 'review'], TermSearch::terms('fix that, then take it through review'));
    }

    /**
     * One letter is a whole word wherever it stands, so it separates nothing.
     * The `f` of `f:if` is the `f` of every other tag in the book —
     * `D-ANS-028`.
     */
    #[Decision('D-ANS-028')]
    #[Test]
    public function oneLetterIsNot(): void
    {
        self::assertSame([], TermSearch::terms('f x 9'));
    }

    /**
     * The floor did the stopword list's work for words this short, and a move
     * of it means the list has to name them itself. "set it up from scratch"
     * otherwise reaches Setting up backend user groups — `D-ANS-028`.
     */
    #[Decision('D-ANS-028')]
    #[Test]
    public function aTwoLetterWordThatSaysNothingAboutTheSubjectIsStillDropped(): void
    {
        self::assertSame(['set', 'test'], TermSearch::terms('set up the tests so we can go'));
    }

    /**
     * Below four characters a term matches whole, which is the reason the floor
     * could move at all. None of the prefix noise `PREFIX_FROM_LENGTH` guards
     * against reaches a word this short — `D-ANS-028`.
     */
    #[Decision('D-ANS-028')]
    #[Test]
    public function aShortTermIsCarriedAsAWholeWord(): void
    {
        self::assertTrue(TermSearch::carries('if', 'if'));
        self::assertTrue(TermSearch::carries('be.security.if Authenticated', 'if'));
        self::assertFalse(TermSearch::carries('a different ifPresent check', 'if'));
    }

    /**
     * A stem gets its prefix and a word gets its word, and which of the two a
     * needle is, is what the caller knows (`D-ANS-050`).
     *
     * The same string proves both sides here. `stem()` cuts "testimonials" to
     * "testim", which reaches the word it came from and nothing else. `test`, a
     * word and the tests intent's own needle, does not reach it at all.
     */
    #[Decision('D-ANS-050')]
    #[Test]
    public function aStemRunsPastItsOwnEndAndACuratedWordDoesNot(): void
    {
        $text = 'build a testimonials content element';

        self::assertSame(['build', 'testim', 'conten', 'elemen'], TermSearch::terms($text));
        self::assertTrue(TermSearch::carries($text, 'testim'));
        self::assertFalse(TermSearch::carriesWord($text, 'test'));

        // What the open side is there for, and it is the same call.
        self::assertTrue(TermSearch::carriesWord('add tests for it', 'test'));
        self::assertTrue(TermSearch::carriesWord('deprecate the method', 'deprecat'));
        self::assertTrue(TermSearch::carriesWord('a deprecation in v14', 'deprecat'));
        // And a word it only starts, one layer down from the route: `boot` is
        // the name the extension boot files stand under.
        self::assertFalse(TermSearch::carriesWord('bootstrap 5 in the theme', 'boot'));
        self::assertTrue(TermSearch::carriesWord('booting the installation', 'boot'));
    }

    /**
     * The right side closes on a letter and not on a word character, so a
     * needle still reaches an identifier it is the head of. That is
     * `D-ANS-006`'s side of the same question — `D-ANS-050`.
     */
    #[Decision('D-ANS-050')]
    #[Test]
    public function aNeedleThatRunsIntoASeparatorIsLeftAsItWas(): void
    {
        self::assertTrue(TermSearch::carriesWord('resolve a sys_file_reference', 'sys_file'));
        self::assertTrue(Text::containsWord('what does f:if do', 'f:'));
        self::assertTrue(Text::containsWord('typo3/sysext/core/Classes', 'typo3/sysext/'));
    }
}
