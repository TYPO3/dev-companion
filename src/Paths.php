<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion;

/**
 * Resolves the paths this checkout keeps things at. The project root is the
 * parent of the src/ directory, and everything below takes its name from there.
 */
final class Paths
{
    public static function root(): string
    {
        return dirname(__DIR__);
    }

    public static function knowledge(): string
    {
        return self::root() . '/knowledge';
    }

    public static function knowledgeFile(string ...$segments): string
    {
        return self::knowledge() . '/' . implode('/', $segments);
    }

    /**
     * The prose corpus: the markdown documents searched by typo3_rule_lookup
     * and served as typo3://guides resources.
     *
     * They have a directory of their own because a place in it is what
     * publishes them. A readme beside the knowledge base became
     * `typo3://guides/readme` without a decision by anybody. What publishes one
     * today is the shape below this directory rather than the directory alone.
     * `<scope>/<topic>/<name>.md`, so nobody reads a file at any other depth.
     */
    public static function documents(): string
    {
        return self::$documents ?? self::knowledge() . '/documents';
    }

    /**
     * A prose corpus other than this checkout's, which only a test asks for.
     *
     * `R-COD-003`: a unit test writes into no directory this repository keeps.
     * What the section range of `D-VER-005` does has no hold against the corpus
     * itself. A document that carries a `**Since:**` for a test would be a
     * statement in the knowledge base whose purpose is the test. Every guard
     * over that directory would have to make an exception for it.
     */
    private static ?string $documents = null;

    /** Where the prose corpus lives, for as long as a test says so. */
    public static function useDocuments(?string $directory): void
    {
        self::$documents = $directory;
    }

    public static function catalogFile(string ...$segments): string
    {
        return self::knowledge() . '/catalog/' . implode('/', $segments);
    }

    /**
     * A feedback store somewhere other than this checkout's, which only a test
     * asks for.
     *
     * `R-COD-003`: a unit test writes into no directory this repository keeps.
     * The cases that hold a record, a filter and an archive have to write a
     * feedback to have one. They used to write it into the real `feedback/`,
     * which leaves a fixture in the corpus whenever a run does not finish. The
     * archive follows it, because the two are one store.
     */
    private static ?string $feedback = null;

    /** Where feedback lives, for as long as a test says so. */
    public static function useFeedback(?string $directory): void
    {
        self::$feedback = $directory;
    }

    /**
     * The same redirect for a server started as a subprocess, which a static
     * setter cannot reach. The stdio smoke test records through the real
     * binary, and without this the file lands in the corpus this repository
     * keeps.
     */
    public const FEEDBACK_VARIABLE = 'TYPO3_DEV_COMPANION_FEEDBACK_DIR';

    /**
     * Improvement feedback recorded by agents. Only written to in a standalone
     * checkout; see Feedback.
     */
    public static function feedback(): string
    {
        return self::$feedback
            ?? (($stated = getenv(self::FEEDBACK_VARIABLE)) === false || $stated === '' ? self::root() . '/feedback' : $stated);
    }

    /**
     * The feedback a session has worked off. They stay readable rather than go.
     * What a session reported about this server is evidence about it, and the
     * answer to it is the half nobody else can reconstruct.
     */
    public static function feedbackArchive(): string
    {
        return self::feedback() . '/archive';
    }

    /**
     * The questions a finished session answers in its debrief, which the
     * `debrief` prompt hands over and
     * `documentation/records/asking-for-a-debrief.rst` includes. One text, so
     * what somebody pastes is what the server offers (`D-FBK-048`).
     *
     * It sits in the published tree rather than below `knowledge/` because the
     * page is what includes it. A renderer resolves an include against the
     * directory it publishes and nothing outside it.
     */
    public static function debrief(): string
    {
        return self::root() . '/documentation/records/debrief.txt';
    }

    /**
     * The mark at one of its three optical sizes, `s`, `m` or `l`, which the
     * site draws and `initialize` sends a client as the server's icon.
     */
    public static function signet(string $size): string
    {
        return self::root() . '/documentation/images/signet-' . $size . '.svg';
    }
}
