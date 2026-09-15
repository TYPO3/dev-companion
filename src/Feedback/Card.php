<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Feedback;

use TYPO3\DevCompanion\Upkeep\Todo;

/**
 * The card a feedback brings with it: one todo in the queue that asks for the
 * judgement nobody has made yet.
 *
 * The record writes it rather than a step afterwards, and it lives here rather
 * than in `Upkeep/`, `D-FBK-045`. The card points and does not copy. `serves:`
 * names the file, and the heading is the feedback's own so that a listing says
 * which one it is. The step is the same on every card because it is the same
 * step.
 */
final class Card
{
    /**
     * What a card is worth before anybody has said so.
     *
     * The lowest of the three rather than no priority at all. Every todo in a
     * stage carries one, so that a check can report a forgotten priority. The
     * word has to say the same thing absence used to: below everything somebody
     * has decided about. A raise of it is a judgement, which is the work the
     * card asks for.
     */
    public const UNJUDGED = 'low';

    /**
     * The step every card carries, because the judgement of one feedback is the
     * same work whichever it is. What differs is the heading and what it
     * serves.
     *
     * Read from outside as well as written from here, which is what makes a
     * card that still asks for a judgement findable. `Todo::folded()` compares
     * a body against this constant rather than against a phrase somebody would
     * have to keep in step with it, `D-FBK-040`.
     */
    public const STEP = <<<'TEXT'
        Judge this feedback before fixing what it reports: re-run the query that
        produced it against the server as it is now, then close it, trim it to the half
        that is still open, or write the todo that takes it on. Where the judgement
        leaves nothing to establish, make the change in the same run rather than queuing
        it — a lookup this run already made is not a reason to send the next one back to
        the same files. Write the judgement into `decisions/` — the entry it was made
        against, or a new one where nothing says it yet — because the commit that closes
        a feedback is the one place nobody can search afterwards.
        `documentation/records/judging.rst` is the ladder, the line the change may not
        cross, and the one question it opens with; what this feedback actually says is in
        the file it serves rather than here.
        TEXT;

    /**
     * Where the card for one feedback sits, relative to the checkout both are
     * in. The feedback goes by the name it gives itself, `feedback/<name>.md`.
     *
     * The id derives from the feedback and from nothing else. So a second write
     * of the card writes one file, and either end leads to the pair without a
     * lookup of either name, `D-DOC-061`. The day it carries is the day the
     * report arrived rather than the day somebody got round to it. That is what
     * a listing sorts the queue by.
     */
    public static function path(string $feedback): string
    {
        $name = basename($feedback, '.md');
        preg_match('/^\d{2}(\d{2})-(\d{2})-(\d{2})-/', $name, $arrived);

        return 'todo/open/' . Todo::id($name, implode('', array_slice($arrived, 1)) ?: null) . '.md';
    }

    /**
     * Writes the card for one feedback, and returns the path it now has,
     * relative to the checkout that stores the feedback.
     *
     * That checkout rather than `Paths::root()`, for the reason
     * `Channel::git()` states. The two are the same directory in an
     * installation and differ where a test writes into a store of its own. A
     * card beside a feedback belongs where the feedback is, `R-COD-003`.
     */
    public static function write(string $feedback, string $title): string
    {
        $path = self::path($feedback);
        $file = Channel::root() . '/' . $path;

        // The queue is a directory git does not keep when it is empty. So a run
        // that empties it takes it with it and the next card has nowhere to
        // land.
        $directory = dirname($file);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Cannot create the queue directory: %s', $directory));
        }

        // Written as a template rather than through a dumper. Both values are a
        // file name and a word from a closed list, and neither can need the
        // quotes a title needed in `D-DOC-045`.
        $written = file_put_contents(
            $file,
            sprintf(
                "---\nserves: [%s]\npriority: %s\n---\n\n# %s\n\n%s\n",
                $feedback,
                self::UNJUDGED,
                $title,
                self::STEP,
            ),
        );
        if ($written === false) {
            throw new \RuntimeException(sprintf('Cannot write the card: %s', $file));
        }

        return $path;
    }
}
