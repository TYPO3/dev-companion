<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Links;

/**
 * The paths this repository writes between its own files are the one thing it
 * says about itself that nothing read back. One pass renamed nineteen decision
 * files and rewrote 58 references with them. A missed one is a link that goes
 * nowhere and reports nothing.
 */
final class LinksTest extends TestCase
{
    /**
     * One read over every corpus, because a path is a path whichever markup
     * wrote it. The four sections of `documentation/` link to each other in
     * reStructuredText and everything around them in markdown — `D-DOC-025`,
     * `D-DOC-029`.
     */
    #[Decision('D-DOC-025')]
    #[Decision('D-DOC-029')]
    #[Test]
    public function everyPathThisRepositoryWritesToItselfResolves(): void
    {
        $dead = Links::dead();

        self::assertSame(
            [],
            $dead,
            'dead links: ' . implode(', ', array_map(
                static fn(array $link): string => $link['file'] . ':' . $link['line'] . ' → ' . $link['link'],
                $dead,
            )),
        );
    }

    /**
     * The check above passes on a repository with no links at all, so this is
     * what says it reads them. One file, one link that resolves, one that does
     * not, and the anchors and URLs it leaves alone on purpose.
     */
    #[Test]
    public function aPathThatIsNotThereIsFoundAndTheRestIsLeftAlone(): void
    {
        $directory = sys_get_temp_dir() . '/links-' . bin2hex(random_bytes(6));
        mkdir($directory);
        file_put_contents($directory . '/there.md', '# there');
        file_put_contents($directory . '/reader.md', implode("\n", [
            '[a file that is here](there.md)',
            '[the same file, at a heading](there.md#a-heading-that-moved)',
            '[somebody else](https://docs.typo3.org/)',
            '[a file that is not](gone.md)',
            '[gone-by-reference]: also-gone.md',
        ]));

        $dead = Links::deadIn($directory . '/reader.md');

        self::assertSame(['gone.md', 'also-gone.md'], array_column($dead, 'link'));
        self::assertSame([4, 5], array_column($dead, 'line'));

        unlink($directory . '/reader.md');
        unlink($directory . '/there.md');
        rmdir($directory);
    }

    /**
     * The one move this repository makes to a file other files name, and the
     * one repair it has. A link to a report with an answer since moves to where
     * the answer put it. A link to a name the archive never held stays dead,
     * because nothing here knows where it meant to go.
     */
    #[Decision('D-DOC-064')]
    #[Test]
    public function aLinkToAFeedbackThatWasAnsweredIsRewrittenToTheArchive(): void
    {
        $directory = sys_get_temp_dir() . '/links-' . bin2hex(random_bytes(6));
        mkdir($directory . '/feedback/archive', 0775, true);
        mkdir($directory . '/decisions');
        file_put_contents($directory . '/feedback/still-open.md', '# open');
        file_put_contents($directory . '/feedback/archive/answered.md', '# answered');
        file_put_contents($directory . '/decisions/entry.md', implode("\n", [
            '[the feedback it came from](../feedback/answered.md)',
            '[at a heading in it](../feedback/answered.md#what-was-observed)',
            '[one that is still open](../feedback/still-open.md)',
            '[one nobody ever wrote](../feedback/never-there.md)',
        ]));

        $written = Links::repairIn($directory . '/decisions/entry.md');

        self::assertSame(
            ['../feedback/archive/answered.md#what-was-observed', '../feedback/archive/answered.md'],
            array_column($written, 'repair'),
        );
        self::assertSame(implode("\n", [
            '[the feedback it came from](../feedback/archive/answered.md)',
            '[at a heading in it](../feedback/archive/answered.md#what-was-observed)',
            '[one that is still open](../feedback/still-open.md)',
            '[one nobody ever wrote](../feedback/never-there.md)',
        ]), file_get_contents($directory . '/decisions/entry.md'));

        unlink($directory . '/decisions/entry.md');
        unlink($directory . '/feedback/archive/answered.md');
        unlink($directory . '/feedback/still-open.md');
        rmdir($directory . '/decisions');
        rmdir($directory . '/feedback/archive');
        rmdir($directory . '/feedback');
        rmdir($directory);
    }

    /**
     * A link in the wrong markup resolves, so the check above passes on it and
     * the reader is what finds it. Two pages carried a group listing written
     * that way from the conversion until 2026-08-12.
     */
    #[Test]
    public function noReStructuredTextPageWritesALinkInMarkdown(): void
    {
        $unrendered = Links::unrendered();

        self::assertSame(
            [],
            $unrendered,
            'written in markdown: ' . implode(', ', array_map(
                static fn(array $link): string => $link['file'] . ':' . $link['line'] . ' → ' . $link['link'],
                $unrendered,
            )),
        );
    }

    /**
     * What that reads, and what it may not read. The answer a tool page records
     * is markdown shown as itself, and every link in it is right where it
     * stands.
     */
    #[Test]
    public function aMarkdownLinkIsFoundInProseAndLeftAloneInACodeBlock(): void
    {
        $directory = sys_get_temp_dir() . '/links-' . bin2hex(random_bytes(6));
        mkdir($directory);
        file_put_contents($directory . '/reader.rst', implode("\n", [
            'A page',
            '======',
            '',
            '[a group](../../decisions/answers/readme.md)',
            '',
            '.. code-block:: markdown',
            '',
            '    [what the tool answered](knowledge/documents/core.md)',
            '',
            '`the right shape <../../decisions/readme.md>`__',
        ]));

        $unrendered = Links::unrenderedIn($directory . '/reader.rst');

        self::assertSame([4], array_column($unrendered, 'line'));

        unlink($directory . '/reader.rst');
        rmdir($directory);
    }

    /**
     * The same for the other markup, where a path has five shapes.
     *
     * Two of them are options rather than link syntax. A card and a teaser say
     * where they go with `:href:` and what they show with `:src:`. They reached
     * no check until the front page had six of them.
     */
    #[Test]
    public function aReStructuredTextPathIsFoundInEveryShapeThatCarriesOne(): void
    {
        $directory = sys_get_temp_dir() . '/links-' . bin2hex(random_bytes(6));
        mkdir($directory);
        file_put_contents($directory . '/there.rst', "There\n=====\n");
        file_put_contents($directory . '/drawing.svg', '<svg/>');
        file_put_contents($directory . '/reader.rst', implode("\n", [
            '`a file that is here <there.rst>`_',
            ':doc:`a page that is here <there>`',
            ':doc:`there`',
            '`somebody else <https://docs.typo3.org/>`_',
            ':ref:`a label, which no path answers <a-label>`',
            '',
            '.. image:: drawing.svg',
            '',
            '.. card:: One that goes somewhere',
            '    :href: there',
            '',
            '.. card:: One that shows something',
            '    :src: drawing.svg',
            '',
            '`a file that is not <gone.rst>`_',
            ':doc:`a page that is not <also-gone>`',
            '',
            '.. image:: missing.svg',
            '',
            '.. card:: One that goes nowhere',
            '    :href: no-page',
        ]));

        $dead = Links::deadIn($directory . '/reader.rst');

        self::assertSame(
            ['gone.rst', 'also-gone.rst', 'missing.svg', 'no-page.rst'],
            array_column($dead, 'link'),
        );

        unlink($directory . '/reader.rst');
        unlink($directory . '/drawing.svg');
        unlink($directory . '/there.rst');
        rmdir($directory);
    }
}
