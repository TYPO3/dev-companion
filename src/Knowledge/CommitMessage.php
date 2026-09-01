<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge;

/**
 * Drafts a TYPO3 commit message and checks it against the rules of the workflow
 * it was written for. Touches no checkout, and the one thing it reads is the
 * release lines a `Releases:` trailer is held against.
 *
 * The draft is emitted ready to use, so everything the rules demand of a commit
 * message has to hold for what this class returns: an agent copies the block
 * verbatim, and a defect in it lands in the patch. That is why the body is
 * wrapped at 72 characters here instead of only being complained about.
 *
 * For the same reason the checks judge the draft rather than the input it was
 * built from. Whatever the draft is missing, it says so as a placeholder — and
 * a trailer this class fills in is never also reported as absent.
 */
final class CommitMessage
{
    /** Column the body is wrapped at, per the TYPO3 commit message rules. */
    public const BODY_WIDTH = 72;

    /**
     * What the draft writes where an answer belongs, and what it refuses to
     * read back as one.
     *
     * A message handed in for checking carries whatever the caller left in it,
     * and a placeholder that survived that far is still the unanswered field it
     * was drafted as — so it is dropped before the checks run and the field
     * reports as missing again. Otherwise the one moment a caller asks about
     * the message they are actually going to commit is the moment this class
     * calls it clean. No value is one anybody could have meant: no Forge issue
     * is numbered ISSUE_NUMBER, no branch is called RELEASE_TARGET, and nobody
     * signs off as YOUR_NAME.
     */
    public const ISSUE_PLACEHOLDER = '#ISSUE_NUMBER';
    public const RELEASE_PLACEHOLDER = 'RELEASE_TARGET';
    public const SIGN_OFF_PLACEHOLDER = 'YOUR_NAME <YOUR_EMAIL>';

    /**
     * The core's own workflow: Forge issues, release targets, Gerrit.
     *
     * What a commit message looks like and what a commit message has to be
     * accompanied by are two different rules, and only the first one travels.
     * The subject keyword, the 52/72 limits and the wrapping are used in TYPO3
     * projects and extensions throughout, and so are `Resolves:` and
     * `Related:`. What the core owns is the Forge issue behind them, the
     * `Releases:` trailer and the changelog — `D-GUI-017`.
     */
    public const WORKFLOW_CORE = 'core';

    /** Any other repository: the same message rules, and only the trailers the caller passed. */
    public const WORKFLOW_PROJECT = 'project';

    /** Keywords a contributor may use; [SECURITY] belongs to the Security Team. */
    private const KEYWORDS = ['BUGFIX', 'FEATURE', 'TASK', 'DOCS'];

    /**
     * [SECURITY] is a keyword the core uses — it is the Security Team that
     * writes those commits, which is why a contributor may not. Outside the
     * core nobody holds that reservation, and a repository that updates a
     * vulnerable dependency has the same word for it.
     */
    private const PROJECT_KEYWORDS = ['BUGFIX', 'FEATURE', 'TASK', 'DOCS', 'SECURITY'];

    /** Trailers this class understands; anything else is carried through as written. */
    private const KNOWN_TRAILERS = ['resolves', 'related', 'releases'];

    /**
     * Trailers a core commit message may not carry.
     *
     * What an agent adds about itself, which the author field already says. The
     * sign-off was refused beside them until the Association's board recommended
     * the Developer Certificate of Origin and the maintainer made it required —
     * `D-KNW-125`.
     */
    private const REFUSED_TRAILERS = ['co-authored-by', 'claude-session'];

    /**
     * Prefixes that say the change is not offered for merge yet.
     *
     * They stand before the keyword where `[!!!]` stands and are not keywords:
     * a subject reads `[WIP][BUGFIX] …`. `[POC]` is written `[PoC]` as often as
     * not, which is why they are matched without regard to case. What they mark
     * is a state rather than a kind of change, so they come off before the patch
     * is merged and no merged commit carries one.
     */
    private const DRAFT_PREFIXES = ['WIP', 'POC'];

    /**
     * @param array{
     *   changeType?: string,
     *   summary: string,
     *   issue?: ?string,
     *   issues?: array<int, string>,
     *   relatedIssues?: array<int, string>,
     *   releases?: array<int, string>,
     *   body?: ?string,
     *   draftPrefixes?: array<int, string>,
     *   isBreaking?: ?bool,
     *   isDeprecation?: ?bool,
     *   extraTrailers?: array<int, string>,
     *   workflow?: string
     * } $input
     * @return array{message: string, checks: array<int, array{level: string, code: string, message: string}>}
     */
    public static function create(array $input): array
    {
        $workflow = self::workflow($input['workflow'] ?? null);
        $changeType = trim((string) ($input['changeType'] ?? ''));
        $summary = self::normalizeSummary((string) $input['summary']);
        $isBreaking = self::classification($input['isBreaking'] ?? null);
        $isDeprecation = self::classification($input['isDeprecation'] ?? null);

        $issues = [];
        foreach (array_merge([$input['issue'] ?? null], $input['issues'] ?? []) as $issue) {
            $normalized = self::normalizeIssue(is_string($issue) ? $issue : null);
            if ($normalized !== null && $normalized !== self::ISSUE_PLACEHOLDER) {
                $issues[$normalized] = true;
            }
        }
        $issues = array_keys($issues);

        $relatedIssues = [];
        foreach ($input['relatedIssues'] ?? [] as $related) {
            $normalized = self::normalizeIssue($related);
            if ($normalized !== null) {
                $relatedIssues[$normalized] = true;
            }
        }
        $relatedIssues = array_keys($relatedIssues);

        $releases = array_values(array_filter(
            $input['releases'] ?? [],
            static fn(string $release): bool => $release !== self::RELEASE_PLACEHOLDER,
        ));

        // A draft prefix the caller wrote is kept rather than corrected away:
        // the answer is the message they are about to commit, and stripping the
        // one word that says "not yet" would hand back a subject that offers the
        // patch for merge. That it has to go before it is merged is a check.
        $drafts = '';
        foreach ($input['draftPrefixes'] ?? [] as $marker) {
            $drafts .= '[' . $marker . ']';
        }
        $prefix = $drafts . ($isBreaking === true ? '[!!!]' : '') . '[' . ($changeType === '' ? 'KEYWORD' : $changeType) . ']';
        $subject = $prefix . ' ' . $summary;
        $wrapped = self::wrapBody(isset($input['body']) ? (string) $input['body'] : '');
        $body = $wrapped['body'];

        $parts = [$subject];
        if ($body !== '') {
            $parts[] = "\n" . $body;
        }

        $isCore = $workflow === self::WORKFLOW_CORE;
        $trailers = [];
        if ($isCore && $issues === []) {
            $trailers[] = 'Resolves: ' . self::ISSUE_PLACEHOLDER;
        }
        foreach ($issues as $issue) {
            $trailers[] = 'Resolves: ' . $issue;
        }
        foreach ($relatedIssues as $related) {
            $trailers[] = 'Related: ' . $related;
        }
        // A placeholder rather than a plausible default: which branches a change
        // is released on is a decision, and a draft that quietly says "main" is
        // one the caller cannot tell from one they made. Outside the core there
        // is nothing to place it against, so it is left out entirely unless the
        // caller named the releases themselves.
        if ($isCore) {
            $trailers[] = 'Releases: ' . ($releases === [] ? self::RELEASE_PLACEHOLDER : implode(', ', $releases));
        } elseif ($releases !== []) {
            $trailers[] = 'Releases: ' . implode(', ', $releases);
        }
        foreach ($input['extraTrailers'] ?? [] as $trailer) {
            $trailers[] = $trailer;
        }

        // Last, where `git commit -s` writes it. A placeholder rather than the
        // author field: the certificate is an attestation about provenance and
        // this class cannot make one on somebody's behalf — `D-KNW-125`.
        $signedOff = preg_grep('/^Signed-off-by:/i', $trailers) !== [];
        if ($isCore && !$signedOff) {
            $trailers[] = 'Signed-off-by: ' . self::SIGN_OFF_PLACEHOLDER;
        }

        if ($trailers !== []) {
            $parts[] = '';
            $parts = array_merge($parts, $trailers);
        }

        return [
            'message' => implode("\n", $parts),
            'checks' => self::checks(
                $changeType,
                $subject,
                $summary,
                $body,
                $wrapped['joined'],
                $issues,
                $isBreaking,
                $isDeprecation,
                $releases,
                $workflow,
                $signedOff,
            ),
        ];
    }

    /**
     * What the caller said about the change, or null where nobody said.
     *
     * `false` and "not supplied" are different answers to a classification the
     * tool cannot derive, so the second is carried through rather than
     * collapsed into the first — R-GUI-011.
     */
    private static function classification(mixed $value): ?bool
    {
        return $value === null ? null : (bool) $value;
    }

    /**
     * The workflow a message is written for, defaulting to the core's.
     *
     * An unknown value is the core's too rather than an exception: the argument
     * exists to take rules away, and a typo must not be a way to end up with
     * fewer of them than the caller asked for.
     */
    public static function workflow(mixed $workflow): string
    {
        // Core is stated rather than fallen back to — `D-GUI-010`. Three
        // audiences reach this server and one of them has a Forge issue, so the
        // unstated call is the project one, and the core routes name the
        // argument where a contributor already is.
        return strtolower(trim((string) $workflow)) === self::WORKFLOW_CORE
            ? self::WORKFLOW_CORE
            : self::WORKFLOW_PROJECT;
    }

    /**
     * Splits an existing commit message into the parts create() works on, so a
     * message written by hand (or amended on an existing patch set) can be
     * checked as a whole instead of being reassembled from fields.
     *
     * Trailers this class does not know — `Change-Id:` above all, which the
     * commit hook owns and an amend must keep — are carried through untouched.
     *
     * @return array{
     *     input: array{
     *         changeType: string,
     *         summary: string,
     *         issues: array<int, string>,
     *         relatedIssues: array<int, string>,
     *         releases: array<int, string>,
     *         body: string,
     *         draftPrefixes: array<int, string>,
     *         isBreaking: ?bool,
     *         extraTrailers: array<int, string>
     *     },
     *     checks: array<int, array{level: string, code: string, message: string}>
     * }
     */
    public static function parse(string $message, string $workflow = self::WORKFLOW_CORE): array
    {
        $workflow = self::workflow($workflow);
        $lines = preg_split('/\R/', trim($message)) ?: [];
        $checks = [];

        $subject = trim(array_shift($lines) ?? '');
        if ($subject === '') {
            throw new \InvalidArgumentException('The commit message is empty.');
        }

        $changeType = '';
        // A subject without [!!!] answers nothing: the caller may have
        // classified the change as not breaking, or never have classified it.
        $isBreaking = null;
        $draftPrefixes = [];
        $summary = $subject;
        // The markers in front of the keyword are taken off one at a time, in
        // whatever order the subject wrote them: [WIP][!!!][FEATURE] says what
        // [!!!][WIP][FEATURE] says. Peeling them first is also what lets a
        // subject that is nothing but a marker — [WIP] Livesearch — be reported
        // as the missing keyword it is rather than as an unknown one.
        $rest = $subject;
        $markers = '/^\[(' . implode('|', array_merge(['!!!'], self::DRAFT_PREFIXES)) . ')\]\s*/i';
        while (preg_match($markers, $rest, $marker) === 1) {
            if ($marker[1] === '!!!') {
                $isBreaking = true;
            } else {
                $draftPrefixes[] = strtoupper($marker[1]);
            }
            $rest = substr($rest, strlen($marker[0]));
        }
        $summary = $rest;

        if ($draftPrefixes !== []) {
            $checks[] = [
                'level' => 'warning',
                'code' => 'not-merge-ready',
                'message' => sprintf(
                    '%s says the change is still being worked on and is not offered for merge. It comes off '
                        . 'before the patch is merged, where [!!!] is the only prefix a subject may carry.',
                    implode('', array_map(
                        static fn(string $marker): string => '[' . $marker . ']',
                        $draftPrefixes,
                    )),
                ),
            ];
        }

        if (preg_match('/^\[([A-Za-z]+)\]\s*(.*)$/', $rest, $matches) === 1) {
            $changeType = strtoupper($matches[1]);
            $summary = trim($matches[2]);

            $keywordCheck = self::keywordCheck($changeType, $workflow);
            if ($keywordCheck !== null) {
                $checks[] = $keywordCheck;
                $changeType = '';
            }
        } else {
            $checks[] = [
                'level' => 'error',
                'code' => 'missing-keyword',
                'message' => 'The summary line must start with a TYPO3 keyword, for example "[BUGFIX] Fix ...".',
            ];
        }

        if ($lines !== [] && trim($lines[0]) !== '') {
            $checks[] = [
                'level' => 'warning',
                'code' => 'body-blank-line',
                'message' => 'Separate the summary line and the body with a blank line.',
            ];
        }

        // Everything from the last trailer block to the end belongs to the
        // trailers. A line only counts as one when it carries a known trailer
        // name or a hyphenated git-style one (Change-Id, Reviewed-by), so a
        // body sentence like "Note: ..." stays body text.
        $trailerLines = [];
        while ($lines !== []) {
            $last = trim((string) end($lines));
            if ($last === '') {
                array_pop($lines);
                continue;
            }
            if (!self::isTrailer($last)) {
                break;
            }
            array_unshift($trailerLines, $last);
            array_pop($lines);
        }

        $issues = [];
        $relatedIssues = [];
        $releases = [];
        $extraTrailers = [];
        foreach ($trailerLines as $trailer) {
            [$name, $value] = array_map('trim', explode(':', $trailer, 2));
            $key = strtolower($name);
            if (!in_array($key, self::KNOWN_TRAILERS, true)) {
                if ($workflow === self::WORKFLOW_CORE && in_array($key, self::REFUSED_TRAILERS, true)) {
                    $checks[] = [
                        'level' => 'error',
                        'code' => 'refused-trailer',
                        'message' => sprintf(
                            'The %s: line is off the draft. A core commit message carries %s and the Change-Id the '
                                . 'hook writes, and nothing else — whatever the checkout you are working in says.',
                            $name,
                            implode(', ', array_map(ucfirst(...), self::KNOWN_TRAILERS)),
                        ),
                    ];
                    // Dropped rather than carried through: the draft this
                    // returns is committed as it stands, so a refused trailer
                    // left in it would be the answer contradicting its own
                    // check.
                    continue;
                }
                if ($key === 'signed-off-by' && $value === self::SIGN_OFF_PLACEHOLDER) {
                    // The draft's own placeholder read back: an unsigned message
                    // rather than a signed one, so it reports as missing again.
                    continue;
                }
                $extraTrailers[] = $trailer;
                continue;
            }
            match ($key) {
                'resolves' => $issues[] = $value,
                'related' => $relatedIssues[] = $value,
                'releases' => $releases = array_values(array_filter(array_map(
                    'trim',
                    explode(',', $value)
                ), static fn(string $release): bool => $release !== '')),
            };
        }

        return [
            'input' => [
                'changeType' => $changeType,
                'summary' => $summary,
                'issues' => $issues,
                'relatedIssues' => $relatedIssues,
                'releases' => $releases,
                'body' => trim(implode("\n", $lines)),
                'draftPrefixes' => $draftPrefixes,
                'isBreaking' => $isBreaking,
                'extraTrailers' => $extraTrailers,
            ],
            'checks' => $checks,
        ];
    }

    /**
     * What is wrong with the keyword, or null when nothing is.
     *
     * Written once because both entry points need the same verdict: a message
     * that is parsed carries its keyword in the subject, one that is assembled
     * carries it as an argument, and a keyword the core reserves has to be
     * refused on both paths.
     *
     * @return array{level: string, code: string, message: string}|null
     */
    private static function keywordCheck(string $changeType, string $workflow): ?array
    {
        $allowed = $workflow === self::WORKFLOW_PROJECT ? self::PROJECT_KEYWORDS : self::KEYWORDS;
        if (in_array($changeType, $allowed, true)) {
            return null;
        }

        if ($changeType === 'SECURITY') {
            return [
                'level' => 'error',
                'code' => 'security-keyword',
                'message' => '[SECURITY] is reserved for the TYPO3 Security Team. Use [BUGFIX] or [TASK]. '
                    . 'In a repository of your own, pass workflow="project" — there the keyword is yours to use.',
            ];
        }

        return [
            'level' => 'error',
            'code' => 'unknown-keyword',
            'message' => sprintf(
                'Unknown keyword [%s]. Use %s.',
                $changeType,
                implode(', ', array_map(static fn(string $keyword): string => '[' . $keyword . ']', $allowed)),
            ),
        ];
    }

    /**
     * How long the subject is, what made it that long, what the limit leaves
     * the summary, and who writes the shorter one.
     *
     * The caller passes `summary` and the rule measures the subject the keyword
     * makes of it, so a message naming one and counting the other reads as a
     * claim about what was passed. A session shortened by the nine characters
     * it was over, twice, before the arithmetic became visible — the feedback
     * of 2026-08-05. The room is stated so the first answer is enough.
     *
     * The draft keeps the summary it was given, and the check says so and names
     * the call that measures a replacement — `D-GUI-021`.
     */
    private static function subjectLength(int $length, string $summary, int $limit, string $verdict): string
    {
        $prefix = $length - mb_strlen($summary);

        return sprintf(
            'The subject line is %d characters long: a %d-character summary plus %d for the keyword prefix. '
                . '%s in total, which leaves the summary %d — %d fewer than the one you passed. '
                . 'The draft carries your summary as you wrote it: a shorter one makes a different claim about '
                . 'the change, and that claim is yours. '
                . 'Call again with the shortened text as summary="..." and this call measures the subject it makes. '
                . 'Passed beside a message it replaces the subject alone, keeping the body and every trailer, '
                . 'Change-Id included.',
            $length,
            $length - $prefix,
            $prefix,
            $verdict,
            $limit - $prefix,
            $length - $limit,
        );
    }

    /**
     * What is wrong with one branch in the `Releases:` trailer, or null when
     * nothing is.
     *
     * The check says the line takes a patch and no more than that, and whether
     * the defect is on it stays the author's reading — `D-ANS-058`. A branch
     * nothing is known about is a warning rather than an error, because the list
     * ages in one direction only. A maintained line further back than the
     * ordinary reach is legitimate exactly where the severity earns it, so the
     * check names what the trailer is then claiming rather than refusing it —
     * `D-ANS-073`.
     *
     * @return array{level: string, code: string, message: string}|null
     */
    private static function releaseLineCheck(string $release, string $changeType): ?array
    {
        $state = ReleaseLines::state($release);
        if ($state === ReleaseLines::DEVELOPMENT) {
            return null;
        }
        if ($state === ReleaseLines::MAINTAINED) {
            $ordinary = ReleaseLines::ordinary();
            if (
                in_array($release, $ordinary, true)
                || !in_array($changeType, ['BUGFIX', 'TASK'], true)
            ) {
                return null;
            }

            return [
                'level' => 'warning',
                'code' => 'older-release-line',
                'message' => sprintf(
                    '%s is maintained, and a %s is released on %s. An older line takes a priority bug fix and a '
                        . 'grave or security-relevant defect, so naming it claims the severity earns it — say so in '
                        . 'the body, or leave the line out.',
                    $release,
                    $changeType,
                    implode(', ', $ordinary),
                ),
            ];
        }

        if ($state === ReleaseLines::UNKNOWN) {
            return [
                'level' => 'warning',
                'code' => 'unknown-release-line',
                'message' => sprintf(
                    '"%s" is not a release line this server knows. The lines taking a patch today are %s, read from '
                        . '%s on %s — a branch opened since then is one this list is missing rather than one you may '
                        . 'not name.',
                    $release,
                    implode(', ', ReleaseLines::releasable()),
                    ReleaseLines::source(),
                    ReleaseLines::readAt(),
                ),
            ];
        }

        return [
            'level' => 'error',
            'code' => 'unmaintained-release-line',
            'message' => sprintf(
                '%s, so a patch pushed to Gerrit is not released there. The lines taking one today are %s.',
                ReleaseLines::describe($release),
                implode(', ', ReleaseLines::releasable()),
            ),
        ];
    }

    private static function isTrailer(string $line): bool
    {
        if (preg_match('/^([A-Za-z][A-Za-z-]*):\s*\S/', $line, $matches) !== 1) {
            return false;
        }

        return in_array(strtolower($matches[1]), self::KNOWN_TRAILERS, true)
            || str_contains($matches[1], '-');
    }

    public static function normalizeIssue(?string $issue): ?string
    {
        $trimmed = trim((string) $issue);
        if ($trimmed === '') {
            return null;
        }

        return str_starts_with($trimmed, '#') ? $trimmed : '#' . $trimmed;
    }

    /**
     * @param array<int, array{first: int, last: int}> $joined
     * @param array<int, string> $issues
     * @param array<int, string> $releases
     * @return array<int, array{level: string, code: string, message: string}>
     */
    private static function checks(
        string $changeType,
        string $subject,
        string $summary,
        string $body,
        array $joined,
        array $issues,
        ?bool $isBreaking,
        ?bool $isDeprecation,
        array $releases,
        string $workflow,
        bool $signedOff
    ): array {
        $checks = [];
        $isCore = $workflow === self::WORKFLOW_CORE;

        if ($changeType !== '') {
            $keywordCheck = self::keywordCheck($changeType, $workflow);
            if ($keywordCheck !== null) {
                $checks[] = $keywordCheck;
            }
        }

        if ($isCore && $issues === []) {
            $checks[] = ['level' => 'error', 'code' => 'missing-issue', 'message' => 'A Forge issue is required. Add a Resolves: #12345 line.'];
        }

        if ($isCore && !$signedOff) {
            $checks[] = [
                'level' => 'error',
                'code' => 'missing-sign-off',
                'message' => sprintf(
                    'The draft carries "Signed-off-by: %s". Replace it by committing with git commit -s, which '
                        . 'writes the line from your git identity. The trailer is the Developer Certificate of '
                        . 'Origin: it says you may publish the contribution under GPL v2 and that it violates '
                        . 'nobody else\'s rights, which is yours to state and stays yours where an AI tool wrote '
                        . 'the code.',
                    self::SIGN_OFF_PLACEHOLDER,
                ),
            ];
        }

        if ($isCore && $releases === []) {
            $checks[] = [
                'level' => 'warning',
                'code' => 'missing-releases',
                'message' => sprintf(
                    'The draft carries "Releases: %s". Replace it with the branches this change is released on. '
                        . 'The lines that can take a patch at all are %s, which is not the list this change belongs '
                        . 'on: a bug fix and a task go to %s, and an older line is named only where the severity '
                        . 'earns it — a priority bug fix, a grave or security-relevant defect. Which lines carry the '
                        . 'defect is the other half and is your reading, verified on each branch you name.',
                    self::RELEASE_PLACEHOLDER,
                    implode(', ', ReleaseLines::releasable()),
                    implode(', ', ReleaseLines::ordinary()),
                ),
            ];
        }

        foreach ($isCore ? $releases : [] as $release) {
            $releaseCheck = self::releaseLineCheck($release, $changeType);
            if ($releaseCheck !== null) {
                $checks[] = $releaseCheck;
            }
        }

        $length = mb_strlen($subject);
        if ($length > 72) {
            $checks[] = [
                'level' => 'error',
                'code' => 'summary-too-long',
                'message' => self::subjectLength($length, $summary, 72, 'Keep it under 72 characters'),
            ];
        } elseif ($length > 52) {
            $checks[] = [
                'level' => 'warning',
                'code' => 'summary-length-preferred',
                'message' => self::subjectLength($length, $summary, 52, 'Under 52 characters is preferred'),
            ];
        }

        if (!preg_match('/^[A-Z]/', $summary)) {
            $checks[] = ['level' => 'warning', 'code' => 'summary-capitalization', 'message' => 'Start the summary text with a capital letter after the keyword.'];
        }

        if ($isCore && str_contains($summary, 'EXT:')) {
            $checks[] = ['level' => 'warning', 'code' => 'summary-extension-prefix', 'message' => 'Avoid EXT:... in the summary when the changed files already show the system extension context.'];
        }

        // Which half of the wrapping conflict this draft took, both ways round:
        // what was joined here, what was left over the width below — R-GUI-007.
        foreach ($joined as $run) {
            $checks[] = [
                'level' => 'info',
                'code' => 'body-lines-reflowed',
                'message' => sprintf(
                    'Lines %d to %d of the body you passed were joined into one paragraph and rewrapped at '
                    . '%d characters. Indent them to keep the line breaks you wrote.',
                    $run['first'],
                    $run['last'],
                    self::BODY_WIDTH,
                ),
            ];
        }

        // The level follows the tooling that enforces it: the core's
        // `Build/git-hooks/commit-msg` refuses the commit, and outside it no
        // hook runs — D-GUI-003.
        $overlong = self::overlongBodyLines($body);
        foreach ($overlong as $line) {
            $checks[] = [
                'level' => $isCore ? 'error' : 'warning',
                'code' => 'body-line-too-long',
                'message' => sprintf(
                    'Body line %d is %d characters long and could not be wrapped at %d characters '
                    . '(a URL, a code line, or another unbreakable token). %s',
                    $line['number'],
                    $line['length'],
                    self::BODY_WIDTH,
                    $isCore
                        ? 'Shorten or break it yourself: ' . self::lineLengthBoundary()
                            . ', indented, fenced and URL alike.'
                        : 'Shorten it if it is prose.',
                ),
            ];
        }

        if (self::bodyIsWrittenAsAList($body)) {
            $checks[] = [
                'level' => 'warning',
                'code' => 'body-written-as-a-list',
                'message' => 'The body is written as a list. Write it as short, precise prose; a list '
                    . 'belongs there to name what the change touched, not to carry the argument.',
            ];
        }

        if ($isDeprecation === true && $isBreaking === true) {
            $checks[] = ['level' => 'error', 'code' => 'deprecation-breaking-prefix', 'message' => 'Deprecations must not use the [!!!] breaking prefix.'];
        }

        if ($isDeprecation === true && !in_array($changeType, ['TASK', 'FEATURE'], true)) {
            $checks[] = ['level' => 'error', 'code' => 'deprecation-keyword', 'message' => 'Deprecations may only use [TASK] or [FEATURE].'];
        }

        // The changelog and the release targets are the core's process, not the
        // message's shape: both name a file and a role that exist in the core
        // repository alone.
        if ($isCore && ($isBreaking === true || $isDeprecation === true)) {
            $checks[] = [
                'level' => 'warning',
                'code' => 'changelog-required',
                'message' => 'Breaking changes and deprecations require a changelog RST file below typo3/sysext/core/Documentation/Changelog/. Validate it with ./Build/Scripts/runTests.sh -s checkRst.',
            ];
        }

        if ($isCore && $isBreaking === true) {
            foreach ($releases as $release) {
                if ($release !== 'main') {
                    $checks[] = ['level' => 'warning', 'code' => 'breaking-release-target', 'message' => 'Breaking changes should usually target main. Confirm older release targets with the release managers.'];
                    break;
                }
            }
        }

        if ($checks === []) {
            $checks[] = ['level' => 'info', 'code' => 'no-issues-found', 'message' => 'No commit message readiness issues found by the local checks.'];
        }

        // The boundary rather than the width, and beside the clearance rather
        // than inside it. Where `body-line-too-long` fired it carries the same
        // sentence, so the answer states the boundary once — `D-GUI-020`.
        if ($isCore && $overlong === []) {
            $checks[] = [
                'level' => 'info',
                'code' => 'line-length-boundary',
                'message' => self::lineLengthBoundary()
                    . '. It measures every line, not the body alone: the subject and the trailers are read the '
                    . 'same way. Where the checkout you are working in states the rule more strictly, that is the '
                    . 'boundary the commit runs through.',
            ];
        }

        // Beside whatever else the checks found rather than inside the
        // clearance, because the message this was reported on returned five
        // reflow infos and no clearance at all — R-GUI-011. A caller who named
        // a deprecation has read the diff, so nothing is owed there.
        if ($isCore && $isBreaking === null && $isDeprecation !== true) {
            $checks[] = [
                'level' => 'info',
                'code' => 'breaking-not-assessed',
                'message' => 'The subject carries no [!!!] and the call passed no isBreaking, so the '
                    . 'classification was assumed rather than checked. It is a property of the diff, which this '
                    . 'tool never sees. Enumerate the public and protected members the diff removes, and the '
                    . 'ones whose signature it narrows or widens: a parameter added to a method widens that '
                    . 'signature whether or not the parameter is optional. A widened visibility is not one of '
                    . 'them. Read the class and member docblocks while you are at it: a member marked '
                    . '@internal owes an Important entry rather than a Breaking one, and an entry is still owed '
                    . '— only its type changes, which is what lets such a change reach a maintained release '
                    . 'line. isDeprecation is assumed the same way. Confirm all of it against the diff and call '
                    . 'again with what you found; typo3_rule_lookup(query "breaking change") has what each move '
                    . 'owes.',
            ];
        }

        return $checks;
    }

    private static function normalizeSummary(string $summary): string
    {
        return preg_replace('/\s+/', ' ', trim($summary)) ?? trim($summary);
    }

    /**
     * Wraps the body at 72 characters, the width the core rules ask for, and
     * says which runs of the caller's lines it joined to do it.
     *
     * Only prose is reflowed: fenced code, indented blocks and list items keep
     * their structure, and a word longer than the width goes on a line of its
     * own and is reported by the checks instead. A block is recognised by its
     * indentation alone, and what lines a `...:` lead-in gathers is deliberately
     * not guessed — `D-GUI-003`.
     *
     * @return array{body: string, joined: array<int, array{first: int, last: int}>}
     */
    private static function wrapBody(string $body): array
    {
        $lines = preg_split('/\R/', trim($body)) ?: [];

        $output = [];
        $joined = [];
        $paragraph = [];
        $inFence = false;

        foreach ($lines as $index => $line) {
            $line = rtrim($line);

            if (str_starts_with(ltrim($line), '```')) {
                self::flushParagraph($output, $paragraph, $joined);
                $inFence = !$inFence;
                $output[] = $line;
                continue;
            }

            if ($inFence || preg_match('/^\s/', $line) === 1) {
                self::flushParagraph($output, $paragraph, $joined);
                $output[] = $line;
                continue;
            }

            if (trim($line) === '') {
                self::flushParagraph($output, $paragraph, $joined);
                $output[] = '';
                continue;
            }

            if (preg_match('/^([-*+]\s+|\d+[.)]\s+)(.*)$/', $line, $matches) === 1) {
                self::flushParagraph($output, $paragraph, $joined);
                $output[] = self::wrapParagraph(
                    $matches[2],
                    $matches[1],
                    str_repeat(' ', mb_strlen($matches[1])),
                );
                continue;
            }

            $paragraph[] = ['number' => $index + 1, 'text' => $line];
        }

        self::flushParagraph($output, $paragraph, $joined);

        return ['body' => rtrim(implode("\n", $output)), 'joined' => $joined];
    }

    /**
     * Wraps the collected prose lines as one paragraph, appends them, and notes
     * the run where more than one line went into it.
     *
     * @param array<int, string> $output
     * @param array<int, array{number: int, text: string}> $paragraph
     * @param array<int, array{first: int, last: int}> $joined
     */
    private static function flushParagraph(array &$output, array &$paragraph, array &$joined): void
    {
        if ($paragraph === []) {
            return;
        }

        if (count($paragraph) > 1) {
            $joined[] = [
                'first' => $paragraph[0]['number'],
                'last' => $paragraph[array_key_last($paragraph)]['number'],
            ];
        }

        $output[] = self::wrapParagraph(implode(' ', array_column($paragraph, 'text')), '', '');
        $paragraph = [];
    }

    /** Greedy word wrap that never splits a word. */
    private static function wrapParagraph(string $text, string $firstPrefix, string $continuationPrefix): string
    {
        $lines = [];
        $current = null;

        foreach (preg_split('/\s+/', trim($text)) ?: [] as $word) {
            if ($word === '') {
                continue;
            }
            if ($current === null) {
                $current = $firstPrefix . $word;
                continue;
            }
            if (mb_strlen($current) + 1 + mb_strlen($word) <= self::BODY_WIDTH) {
                $current .= ' ' . $word;
                continue;
            }
            $lines[] = $current;
            $current = $continuationPrefix . $word;
        }

        if ($current !== null) {
            $lines[] = $current;
        }

        return implode("\n", $lines);
    }

    /**
     * Where the hook's length gate runs, in the words both checks state it in.
     *
     * The width alone settles nothing for a caller whose own checkout writes
     * the rule one character stricter, which is the reading three sessions
     * worked from — `D-GUI-020`.
     */
    private static function lineLengthBoundary(): string
    {
        return sprintf(
            'Build/git-hooks/commit-msg accepts a line of %d characters and refuses one of %d',
            self::BODY_WIDTH,
            self::BODY_WIDTH + 1,
        );
    }

    /**
     * Whether the body carries its argument as a list rather than as prose.
     *
     * An item of four words or more is a sentence somebody wrote as a bullet;
     * a shorter one names a class, a path or a rule the change touched, which
     * is what a list in a body is for. Half of the lines, and only the first
     * kind counted: over the thousand merged core commits carrying a body that
     * `D-GUI-026` measured, that fires on none of them.
     */
    private static function bodyIsWrittenAsAList(string $body): bool
    {
        $lines = array_values(array_filter(
            preg_split('/\R/', $body) ?: [],
            static fn(string $line): bool => trim($line) !== '',
        ));

        $sentences = array_filter($lines, static function (string $line): bool {
            $item = preg_replace('/^\s*([-*+]\s|\d+[.)]\s)/', '', $line, 1, $replaced);

            return $replaced === 1 && count(preg_split('/\s+/', trim((string) $item)) ?: []) >= 4;
        });

        return $sentences !== [] && count($sentences) * 2 >= count($lines);
    }

    /**
     * Lines the wrapping could not bring below the width, with their position in
     * the body.
     *
     * @return array<int, array{number: int, length: int}>
     */
    private static function overlongBodyLines(string $body): array
    {
        $overlong = [];
        foreach (preg_split('/\R/', $body) ?: [] as $index => $line) {
            $length = mb_strlen($line);
            if ($length > self::BODY_WIDTH) {
                $overlong[] = ['number' => $index + 1, 'length' => $length];
            }
        }

        return $overlong;
    }
}
