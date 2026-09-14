<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Feedback\Card;
use TYPO3\DevCompanion\Feedback\Channel;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * This server's only write: one markdown feedback per call, in its own
 * checkout, never touching an existing one.
 *
 * In its own checkout is the load-bearing half. Nothing here reaches the TYPO3
 * installation the server was reading, so this tool is not an exception to the
 * read-only posture — D-FBK-042, where reading it as one is the recorded
 * mistake.
 */
final class FeedbackRecord implements Tool
{
    public static function name(): string
    {
        return 'typo3_feedback_record';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Checkout];
    }

    public static function description(): string
    {
        return 'Leave feedback about a gap, wrong answer, or missing capability of this knowledge server. Leave it about what it did well too, because what worked is what nobody may break later. The server stores it as markdown in its own checkout, and typo3_feedback_list reads it back. It is not in the project you work in, so do not look for the file there. One feedback per subject. A session works a feedback with three complaints off three times over or not at all. Write every text in Simplified Technical English (ASD-STE100): the active voice, one point per sentence, at most 20 words in a sentence. That is the form this server\'s own records keep, and a feedback in it goes into the queue as it is.';
    }

    public static function annotations(): array
    {
        return [
            'readOnlyHint' => false,
            'destructiveHint' => false,
            'idempotentHint' => false,
            'openWorldHint' => false,
        ];
    }

    public static function inputSchema(): array
    {
        // The cap each field is cut at, in the one place a caller reads before
        // writing to it. A session that knows the budget writes to it; one that
        // does not writes past it and finds out from the answer which sentences
        // it cost — D-FBK-049. Interpolated rather than typed out, so the
        // sentence cannot say one number while the channel applies another.
        $storedCap = sprintf(
            ' At most %d characters; the channel cuts a longer text there rather than refuses it.',
            Channel::MAX_FIELD_LENGTH,
        );

        return [
            'type' => 'object',
            'properties' => [
                'subject' => ['type' => 'string', 'description' => 'One short line that says what only this feedback reports, in English: "a release branch\'s log answers about the history from before its cut". It becomes the title and the file name, which is all a maintainer reads to decide what to open. Left out, both come from the first line of the observation. A session that files several at once then gets several that begin on the same words. The observation opens with the task, so every feedback from one session opens alike. Say what this one says and nothing the others do.' . sprintf(' At most %d characters; the channel cuts a longer line to fit rather than refuses it.', Channel::MAX_SUBJECT_LENGTH)],
                'observation' => ['type' => 'string', 'minLength' => 1, 'description' => 'What was missing, wrong, or unhelpful, specific enough to act on later, in English. Open with one line that names the task you got, so a reader traces the feedback back to what exposed it. A finding is the path of a value, the shape of what came back, and where it came from. It is never the value itself where the installation keeps it secret: an encryption key, a password, a token, the credentials in a connection string. A commit pushes this into a checkout that installation\'s owner does not watch, so a secret pasted here as proof has left it for good. The finding is "the key at SYS/encryptionKey is the active one, hardcoded in config/system/settings.php"; the 96 characters after it establish nothing further.' . $storedCap],
                'model' => ['type' => 'string', 'minLength' => 1, 'description' => 'The model that records this feedback, as it identifies itself, for example claude-opus-5 or gpt-5.3-codex. Read it where it stands: what your client reports for the current session, or the person who runs you. Do not read it from what you remember about yourself. A feedback is evidence about one model\'s behaviour, and nobody can tell one filed as "unknown" apart from another model\'s. That fallback is for a session that looked and could not find out; an invented identifier is worse than none.'],
                'category' => ['type' => 'string', 'enum' => Channel::CATEGORIES, 'default' => 'idea', 'description' => 'missing-knowledge: the knowledge base lacks the answer. wrong-answer: the answer was incorrect. tool-gap: no tool covers the need. bug: the server misbehaved. idea: anything else.'],
                // One declared type, not the string-or-array it was. That union
                // was the only one in any input schema this server offers, and
                // the one model that has ever recorded feedback without naming a
                // tool is the one that reported being unable to send the
                // argument at all — D-ANS-017. The several still travel: the
                // recorder splits on commas and spaces, so nothing about what a
                // feedback can say was given up, and a client that sends a list
                // anyway is told which type was expected before the tool runs.
                'tool' => ['type' => 'string', 'description' => 'The tool the observation is about, for example typo3_component_lookup, or the skill it activated, for example typo3-extension-health. Name several in one string, with commas between them.'],
                'query' => ['type' => 'string', 'description' => 'The arguments that produced the poor result, or the task text where a whole session produced it. So somebody can re-run the feedback against a later version of the server instead of a read. The rule from observation holds: the arguments and the path they named, never a value the installation keeps secret. A re-run needs to know that the call asked for SYS/encryptionKey and that a key came back, not what the key was. Name a password or a token that was itself an argument rather than quote it.' . $storedCap],
                'suggestion' => ['type' => 'string', 'description' => 'What the server should have answered or should be able to do instead.' . $storedCap],
            ],
            'required' => ['observation', 'model'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'file' => Schema::string('Path of the recorded feedback, relative to this server\'s own checkout.'),
            'path' => Schema::string('The same feedback as an absolute path. It is in the server\'s checkout, not in the project the feedback came from.'),
            'todo' => Schema::string('Path of the todo this feedback queued, relative to this server\'s own checkout. Every feedback arrives with one, so the report is on the board and does not wait for somebody to notice the file.'),
            'redacted' => Schema::listOf(
                Schema::string(),
                'What the channel removed before it wrote the feedback, one entry per value, with the field it stood in and the shape it had. Empty where it removed nothing, which is the ordinary case. Each removal stands in the file as a [redacted: ...] marker, so the report says of itself that it differs from the text you gave.',
            ),
            'cut' => Schema::listOf(
                Schema::string(),
                'What the channel cut for length before it wrote the feedback, one entry per field, with the field and how much of it went. Empty where it cut nothing, which is the ordinary case. A cut field stands in the file as a [cut: ...] marker where it stops, so the report says of itself that it is short. A cut subject stands as the ... its title ends in, because a listing shows a marker inside a title.',
            ),
        ], ['file', 'path', 'todo', 'redacted', 'cut']);
    }

    public static function answer(array $args): ToolResult
    {
        $redacted = [];
        $cut = [];
        $file = Channel::record($args, $redacted, $cut);
        // The absolute path, because the relative one is relative to somewhere
        // the caller has never been. A feedback recorded from a site package was
        // reported back as feedback/<name>.md, looked for under that project,
        // not found, and written off as a failed write — the file was there the
        // whole time, one checkout over.
        // Against the store rather than the checkout: the two are the same
        // thing in a real installation and differ where a test writes into one
        // of its own, and what this reports has to be where the file is.
        $path = Channel::root() . '/' . $file;
        // The card the feedback was written with, which Channel::record() has
        // already put in the queue — reported so the answer says the report is
        // waiting to be judged rather than lying in a directory.
        $todo = Card::path($file);

        return ToolResult::create(
            sprintf(
                "Thanks — noted in %s and queued as %s.\n\nThat is this server's own checkout, not the project you "
                . "are working in: nothing was written there, so neither file will be found under it.\n\n"
                . 'It will be picked up when the knowledge base is next improved; '
                . 'nothing about the current answer changes.',
                $path,
                $todo,
            ) . self::redactionNotice($redacted) . self::truncationNotice($cut),
            ['file' => $file, 'path' => $path, 'todo' => $todo, 'redacted' => $redacted, 'cut' => $cut],
        );
    }

    /**
     * What was taken out of the feedback, said back to whoever wrote it.
     *
     * A report that was altered says so, or it stops being a report — the same
     * reason the marker in the file is visible rather than a silent
     * substitution. It is also the only moment the value can still be discussed:
     * the session is standing in the installation the value came from and knows
     * what it was, and a reader of the archive three weeks later does not.
     *
     * @param array<int, string> $redacted
     */
    private static function redactionNotice(array $redacted): string
    {
        if ($redacted === []) {
            return '';
        }

        return "\n\n" . sprintf(
            '%s taken out before it was written — %s. Each stands in the file as a `[redacted: ...]` marker, '
            . 'so a reader can see that something was removed and come and ask. This checkout is committed and '
            . 'pushed, and a value the installation keeps secret would leave it that way: what a finding needs is '
            . 'the path and the shape of the value, never the value. Everything else was stored as you wrote it.',
            count($redacted) === 1 ? 'One value was' : sprintf('%d values were', count($redacted)),
            implode('; ', $redacted),
        );
    }

    /**
     * What was cut off the end of a field, said back to whoever wrote it.
     *
     * The same ground as the redaction notice, and the half nothing else can
     * report. A redacted value leaves a name beside its marker; a cut leaves
     * mid-word, so the file gives a later reader no sign that the sentence was
     * going somewhere — and this answer reaches the one session that still has
     * the rest of it.
     *
     * @param array<int, string> $cut
     */
    private static function truncationNotice(array $cut): string
    {
        if ($cut === []) {
            return '';
        }

        return "\n\n" . sprintf(
            '%s longer than a stored field and cut — %s. A cut field stands in the file as a `[cut: ...]` marker '
            . 'where it stops and a cut subject as the `...` its title ends in, so a reader can see the report is '
            . 'short of what was written. Where what went carried the '
            . 'finding, it is worth recording again in fewer words: this answer is the last moment anything still '
            . 'has the rest of it.',
            count($cut) === 1 ? 'One field was' : sprintf('%d fields were', count($cut)),
            implode('; ', $cut),
        );
    }
}
