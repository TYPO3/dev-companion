<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Feedback\Card;
use TYPO3\DevCompanion\Feedback\Channel;
use TYPO3\DevCompanion\Feedback\Redaction;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\RecordedFeedback;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\Todo;

/**
 * Feedback is the one part of the server that writes, so these tests write too.
 * tearDown removes every feedback recorded here again; the marker in the
 * observation makes a leftover recognizable.
 */
final class FeedbackTest extends TestCase
{
    use RecordedFeedback;

    protected function tearDown(): void
    {
        Instance::discoverFrom(null);
    }

    #[Test]
    public function aNoteBecomesOneMarkdownFileWithFrontMatter(): void
    {
        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' the lookup found nothing',
            'category' => 'missing-knowledge',
            'tool' => 'typo3_component_lookup',
            'query' => 'query=badge',
            'suggestion' => 'add the component',
        ]);

        self::assertStringStartsWith('feedback/', $file);
        $contents = (string) file_get_contents($this->inStore($file));
        self::assertStringContainsString('category: missing-knowledge', $contents);
        self::assertStringContainsString('status: open', $contents);
        self::assertStringContainsString('tool: typo3_component_lookup', $contents);
        self::assertStringContainsString('## Observation', $contents);
        self::assertStringContainsString('## Query', $contents);
        self::assertStringContainsString('## Suggestion', $contents);
    }

    /**
     * A feedback is prose an agent wrote, so the cut that makes a heading out
     * of its first line has to count characters. `substr()` counts bytes. An em
     * dash across the boundary went out as its first byte or two, and the file
     * was no longer valid UTF-8. `grep` then treated it as binary and matched
     * nothing in it. That is how the one in the store came to light, three
     * weeks after its record.
     */
    #[Test]
    public function aHeadingIsCutBetweenCharacters(): void
    {
        // The dash sits where a 97-byte cut goes through the middle of it.
        $observation = self::MARKER . ' ' . str_repeat('a', 94 - strlen(self::MARKER)) . '— and the rest of the line';

        $contents = (string) file_get_contents($this->inStore($this->recordFeedback(['observation' => $observation])));

        if (preg_match('/^# (.*)$/m', $contents, $heading) !== 1) {
            self::fail('the feedback was written without the heading it is read by');
        }
        self::assertSame($heading[1], mb_convert_encoding($heading[1], 'UTF-8', 'UTF-8'), 'the heading was cut through a character');
        self::assertStringEndsWith('...', $heading[1]);
    }

    #[Test]
    public function theAgentNeverControlsWhereTheNoteIsWritten(): void
    {
        $file = $this->recordFeedback([
            'observation' => '../../' . self::MARKER . " escape attempt\nsecond line",
        ]);

        self::assertSame('feedback/' . basename($file), $file);
        self::assertStringNotContainsString('..', $file);
        self::assertFileExists(Paths::feedback() . '/' . basename($file));
    }

    #[Test]
    public function aNoteSaysWhichDirectoryItWasWrittenFrom(): void
    {
        // What the stdio entrypoint does at startup: the session's own working
        // directory, which is what makes the feedback checkable later.
        Instance::discoverFrom('/home/somebody/projects/a-site');

        $file = $this->recordFeedback(['observation' => self::MARKER . ' asked in a project']);

        self::assertStringContainsString(
            'directory: /home/somebody/projects/a-site',
            (string) file_get_contents($this->inStore($file))
        );
    }

    #[Decision('D-FBK-025')]
    #[Test]
    public function theDirectoryAFeedbackWasWrittenInIsReadBackWithIt(): void
    {
        // In every feedback since the channel existed and readable only from
        // inside one. That is how 35 reports out of one checkout got judged as
        // 35 unrelated reports — `D-FBK-025`. `feedback:list` groups by it.
        Instance::discoverFrom('/home/somebody/projects/a-site');

        $file = $this->recordFeedback(['observation' => self::MARKER . ' read back with its directory']);

        $listed = array_values(array_filter(
            Channel::all('open', null, PHP_INT_MAX),
            static fn(array $feedback): bool => $feedback['file'] === $file,
        ));

        self::assertCount(1, $listed);
        self::assertSame('/home/somebody/projects/a-site', $listed[0]['directory']);
    }

    #[Test]
    public function aNoteWithoutACallerDirectoryClaimsNone(): void
    {
        // The HTTP case: no entrypoint handed a directory in, so the server's
        // own working directory is not passed off as the caller's.
        Instance::discoverFrom(null);

        $file = $this->recordFeedback(['observation' => self::MARKER . ' asked over http']);

        self::assertStringNotContainsString(
            'directory:',
            (string) file_get_contents($this->inStore($file))
        );
    }

    #[Requirement('R-FBK-005')]
    #[Test]
    public function aNoteSaysWhichModelLeftIt(): void
    {
        // Half the feedback are about what a session did rather than about what an
        // answer said, and that is one model's behaviour. Unattributed, two
        // models' habits arrive as one report.
        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' the skill was read and its lookups were not run',
            'model' => 'claude-opus-5',
        ]);

        self::assertStringContainsString(
            'model: claude-opus-5',
            (string) file_get_contents($this->inStore($file))
        );
        self::assertSame('claude-opus-5', self::noteFor($file)['model']);
    }

    #[Requirement('R-FBK-005')]
    #[Test]
    public function aNoteWithoutAModelSaysSo(): void
    {
        // The write never fails on the attribution, since the feedback is worth
        // more than the name. But one without an attribution says so, which an
        // absent front-matter line cannot.
        $file = $this->recordFeedback(['observation' => self::MARKER . ' recorded by nobody in particular']);

        self::assertStringContainsString(
            'model: unknown',
            (string) file_get_contents($this->inStore($file))
        );
        self::assertSame(Channel::UNATTRIBUTED, self::noteFor($file)['model']);
    }

    #[Requirement('R-FBK-006')]
    #[Test]
    public function theRecordedNoteIsReportedWhereItActuallyIs(): void
    {
        // A relative path is relative to somewhere the caller has never been.
        // One recorded from a site package came back as feedback/<name>.md. The
        // caller looked for it under that project, found nothing, and reported
        // a failed write.
        $this->ownFeedbackStore();
        $result = Registry::call('typo3_feedback_record', [
            'observation' => self::MARKER . ' recorded through the tool',
            'model' => 'claude-opus-5',
        ]);

        $path = $result->data['path'] ?? '';
        self::assertIsString($path);
        self::assertStringStartsWith($this->ownFeedbackStore() . '/feedback/', $path);
        self::assertFileExists($path);
        self::assertStringContainsString($path, $result->text);
        // And it says whose checkout that is, because the caller cannot tell.
        self::assertStringContainsString('not the project you are working in', $result->text);
    }

    /**
     * The card is what puts a report in front of the next session, and a
     * command run from a pre-commit hook used to write it. So a feedback
     * recorded from anywhere else was on disk and on nobody's board until
     * somebody committed in this checkout (`D-FBK-045`).
     */
    #[Decision('D-FBK-045')]
    #[Decision('D-DOC-062')]
    #[Test]
    public function aRecordedFeedbackArrivesWithTheCardThatAsksForItsJudgement(): void
    {
        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' the lookup found nothing',
            'subject' => self::MARKER . ' the lookup found nothing',
        ]);

        // The card's id derives from the feedback and from nothing else, so
        // either end finds the pair with no lookup of either name —
        // `D-DOC-061`. It carries the day the report arrived, which is what
        // orders the queue. A feedback's name is `<date>-<time>-<slug>`, so the
        // day comes off it by position rather than by a pattern the card does
        // not use.
        $name = basename($file, '.md');
        $day = substr($name, 2, 2) . substr($name, 5, 2) . substr($name, 8, 2);

        $card = Card::path($file);
        self::assertSame('todo/open/' . Todo::id($name, $day) . '.md', $card);

        $contents = (string) file_get_contents($this->inStore($card));
        self::assertStringContainsString('# ' . self::MARKER . ' the lookup found nothing', $contents);
        // It points at the feedback rather than repeating it, and asks for the
        // one thing a fresh card asks for.
        self::assertStringContainsString('serves: [' . $file . ']', $contents);
        self::assertStringContainsString('priority: ' . Card::UNJUDGED, $contents);
        self::assertStringContainsString(Card::STEP, $contents);
    }

    /**
     * The caller learns where its report waits, not only where the write landed
     * — `D-FBK-045`.
 */
    #[Decision('D-FBK-045')]
    #[Test]
    public function theToolReportsTheCardTheFeedbackWasQueuedAs(): void
    {
        $this->ownFeedbackStore();
        $result = Registry::call('typo3_feedback_record', [
            'observation' => self::MARKER . ' recorded through the tool',
            'model' => 'claude-opus-5',
        ]);

        $todo = $result->data['todo'] ?? '';
        self::assertIsString($todo);
        self::assertFileExists($this->inStore($todo));
        self::assertStringContainsString($todo, $result->text);
    }

    #[Test]
    public function anUnknownCategoryFallsBackToIdea(): void
    {
        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' something',
            'category' => 'nonsense',
        ]);

        self::assertStringContainsString(
            'category: idea',
            (string) file_get_contents($this->inStore($file))
        );
    }

    #[Test]
    public function anEmptyObservationIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->recordFeedback(['observation' => '   ']);
    }

    /**
     * The fourteen feedback of 2026-08-04 17:58 to 18:02, which arrived with
     * the suggestion parameter inside the observation. Each one ended in a tag
     * named after itself — `D-FBK-044`.
     */
    #[Requirement('R-FBK-016')]
    #[Decision('D-FBK-044')]
    #[Test]
    public function aFieldCarryingTheCallItArrivedInIsRefused(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->recordFeedback([
            'observation' => self::MARKER . " the lookup found nothing</observation>\n"
                . "<parameter name=\"suggestion\">say what it should have answered</suggestion>\n</invoke>",
        ]);
    }

    /**
     * The report about that failure is the one report this check must not
     * refuse. It is the only kind that names those markers at all —
     * `D-FBK-044`.
     */
    #[Requirement('R-FBK-016')]
    #[Decision('D-FBK-044')]
    #[Test]
    public function aReportQuotingTheMarkersIsStillRecorded(): void
    {
        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' the observation came back holding `</invoke>` and a '
                . '`<parameter name="suggestion">` block, so the argument behind it never arrived.',
        ]);

        self::assertStringContainsString('</invoke>', (string) file_get_contents($this->inStore($file)));
    }

    /**
     * The feedback of 2026-07-31 praised typo3_configuration_lookup because it
     * returns the effective runtime value. It proved that with the live
     * encryption key of the audited site pasted into a repository under commit
     * and push. The path and the shape were the finding; the 96 characters
     * established nothing further and left the installation that owns them.
     */
    #[Requirement('R-FBK-011')]
    #[Test]
    public function aValueThatLooksLikeACredentialNeverReachesTheFile(): void
    {
        $key = str_repeat('9f2b7c4d', 12);
        $redacted = [];

        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' called typo3_configuration_lookup with configurationPath SYS/encryptionKey. '
                . 'The key ' . $key . ' is the active value, hardcoded in config/system/settings.php.',
        ], $redacted);

        $contents = (string) file_get_contents($this->inStore($file));
        self::assertStringNotContainsString($key, $contents);
        self::assertStringContainsString('[redacted: a 96-character hexadecimal value]', $contents);
        self::assertSame(['observation: a 96-character hexadecimal value'], $redacted);
        // What the finding was actually made of stays, or the guard costs more
        // than the leak it prevents.
        self::assertStringContainsString('SYS/encryptionKey', $contents);
        self::assertStringContainsString('config/system/settings.php', $contents);
        // The filename derives from the observation too, and it is the copy
        // that would have survived every grep of the file itself.
        self::assertStringNotContainsString($key, $file);
    }

    #[Requirement('R-FBK-011')]
    #[Test]
    public function everyFieldAFeedbackIsWrittenFromIsRead(): void
    {
        // The same value in the second field is how a session works around a
        // guard on the first without the intent to. The query is where it says
        // what it called, and the suggestion is where it says what to do about
        // it. All three are prose, and all three land in one file.
        $key = str_repeat('9f2b7c4d', 12);
        $redacted = [];

        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' the key ' . $key . ' came back from the installation',
            'query' => 'path=SYS/encryptionKey returned ' . $key,
            'suggestion' => 'say that ' . $key . ' is hardcoded rather than generated',
        ], $redacted);

        self::assertStringNotContainsString($key, (string) file_get_contents($this->inStore($file)));
        self::assertSame([
            'observation: a 96-character hexadecimal value',
            'query: a 96-character hexadecimal value',
            'suggestion: a 96-character hexadecimal value',
        ], $redacted);
    }

    #[Requirement('R-FBK-011')]
    #[Test]
    public function whatASessionQuotesAboutTheCoreIsLeftAlone(): void
    {
        // A rule that redacts a revision or a class name costs more than the
        // leak it prevents. Those are what feedback about core patches consist
        // of, and 64 characters is where the threshold clears the longest of
        // them.
        $quoted = 'reviewed 4c8b38b2dd07856c3e2666fbdfd77beead87ffe0 against '
            . 'typo3/sysext/core/Classes/Localization/TranslationDomainMapper, see '
            . 'Feature-109444-AddDefaultValueSupportForCountrySelectFormElement, and the '
            . 'recovery is `install:password:set` rather than guessing — the install tool '
            . 'password: it never prints the value it used';

        $redaction = Redaction::of($quoted);

        self::assertSame($quoted, $redaction->text);
        self::assertSame([], $redaction->removed);
    }

    #[Decision('D-FBK-019')]
    #[Requirement('R-FBK-011')]
    #[Test]
    public function aValueGoesAndTheNameThatSaysWhatItWasStays(): void
    {
        // The half a length rule cannot see. A password is short, and the only
        // thing that says it is one is the name written next to it.
        $redaction = Redaction::of(
            '$GLOBALS[\'TYPO3_CONF_VARS\'][\'BE\'][\'installToolPassword\'] = \'$argon2i$v=19$m=65536$b0RTZ1p6\';',
        );

        self::assertStringNotContainsString('argon2i', $redaction->text);
        self::assertStringContainsString('installToolPassword', $redaction->text);
        self::assertStringContainsString('[redacted: the value of `installToolPassword`]', $redaction->text);
        self::assertSame(['the value of `installToolPassword`'], $redaction->removed);
    }

    #[Decision('D-FBK-019')]
    #[Requirement('R-FBK-011')]
    #[Test]
    public function aPasswordInADatabaseUrlGoesWithoutTheHostGoingWithIt(): void
    {
        $redaction = Redaction::of('the DSN is mysqli://typo3:s3cr3tpw@db:3306/typo3');

        self::assertSame(
            'the DSN is mysqli://typo3:[redacted: the password in a URL]@db:3306/typo3',
            $redaction->text,
        );
        self::assertSame(['the password in a URL'], $redaction->removed);
    }

    #[Decision('D-FBK-019')]
    #[Requirement('R-FBK-011')]
    #[Test]
    public function aLongBase64ValueIsTakenOutAndAWordIsNot(): void
    {
        $token = 'TXlTdXBlclNlY3JldFRva2VuVmFsdWVXaXRoUGFkZGluZzEyMzQ1Njc4OTBBQkNERUZHSElKS0xNTk9Q';

        self::assertSame(
            ['a 80-character base64 value'],
            Redaction::of('the header carried ' . $token . ' verbatim')->removed,
        );
        // Same alphabet, same length, one long camel-cased word: what the
        // corpus is full of and what a shorter threshold took out with it.
        self::assertSame([], Redaction::of(str_repeat('AnotherLongIdentifierName', 3))->removed);
    }

    #[Requirement('R-FBK-011')]
    #[Test]
    public function theToolSaysWhatItTookOutOfWhatItWasHanded(): void
    {
        // Marked rather than silent, and said rather than only marked. The
        // archive keeps a session's report because the report is the evidence,
        // so an altered one has to say so. The session that wrote it is the
        // only reader who still knows what stood there.
        $this->ownFeedbackStore();
        $result = Registry::call('typo3_feedback_record', [
            'observation' => self::MARKER . ' the key ' . str_repeat('9f2b7c4d', 12) . ' is the active one',
            'model' => 'claude-opus-5',
        ]);

        self::assertSame(['observation: a 96-character hexadecimal value'], $result->data['redacted']);
        self::assertStringContainsString('One value was taken out', $result->text);
        self::assertStringContainsString('a 96-character hexadecimal value', $result->text);
        self::assertStringContainsString('[redacted: ...]', $result->text);

        // And an ordinary feedback says nothing about redaction at all.
        $this->ownFeedbackStore();
        $ordinary = Registry::call('typo3_feedback_record', [
            'observation' => self::MARKER . ' the icon lookup found nothing for content-accordion',
            'model' => 'claude-opus-5',
        ]);
        self::assertSame([], $ordinary->data['redacted']);
        self::assertStringNotContainsString('redacted', $ordinary->text);
    }

    /**
     * The observation of `feedback/2026-08-03-144316` is exactly 4000
     * characters and ends `the skill fixed the or`. What the cut took was the
     * sentence that named the shape the session reported, the half the
     * judgement of it turned on. Nothing said it had happened: not the file,
     * not the answer. A cut is blinder than a redaction, which leaves the name
     * of what it took standing beside its marker.
     */
    #[Requirement('R-FBK-015')]
    #[Test]
    public function aFieldCutForLengthSaysSoInTheFileAndInTheAnswer(): void
    {
        $observation = self::observationOfLength(4200);
        $this->ownFeedbackStore();

        $result = Registry::call('typo3_feedback_record', [
            'observation' => $observation,
            'query' => self::observationOfLength(4100),
            'model' => 'claude-opus-5',
        ]);

        // One entry per cut field, in the order of the fields, and each says
        // how much of it went rather than only that some did.
        self::assertSame([
            'observation: 200 characters past the 4000-character limit',
            'query: 100 characters past the 4000-character limit',
        ], $result->data['cut']);
        self::assertStringContainsString('2 fields were longer than a stored field and cut', $result->text);
        self::assertStringContainsString('observation: 200 characters', $result->text);
        self::assertStringContainsString('[cut: ...]', $result->text);

        // And the file says it of itself, for the reader who was not there.
        $contents = (string) file_get_contents((string) $result->data['path']);
        $stored = self::sectionOf($contents, 'Observation');
        self::assertStringEndsWith('[cut: 200 characters past the 4000-character limit]', $stored);
        self::assertLessThan(mb_strlen($observation), mb_strlen($stored));
        self::assertStringContainsString('[cut: 100 characters past the 4000-character limit]', $contents);
    }

    #[Decision('D-FBK-049')]
    #[Requirement('R-FBK-015')]
    #[Test]
    public function aFieldExactlyOnTheCapIsNotMarked(): void
    {
        // The case the marker must not reach. The cut took nothing, so a marker
        // would say something happened that did not. A reader has no way to
        // check it against what the session wrote.
        $this->ownFeedbackStore();

        $result = Registry::call('typo3_feedback_record', [
            'observation' => self::observationOfLength(4000),
            'model' => 'claude-opus-5',
        ]);

        self::assertSame([], $result->data['cut']);
        self::assertStringNotContainsString('cut', $result->text);
        self::assertStringNotContainsString('[cut:', (string) file_get_contents((string) $result->data['path']));
    }

    /**
     * The other cap, and the one the corpus hits. 115 of 440 recorded feedback
     * got a shorter subject, and nothing told any of them — `D-FBK-049`. The
     * file cannot say it, because the `...` a listing shows is all a title may
     * carry. So the answer is the only place that says it at all.
     */
    #[Decision('D-FBK-049')]
    #[Requirement('R-FBK-015')]
    #[Test]
    public function aSubjectShortenedForLengthSaysSoInTheAnswer(): void
    {
        $subject = mb_substr(self::MARKER . ' ' . str_repeat('a line saying what only this one reports. ', 4), 0, 120);
        $this->ownFeedbackStore();

        $result = Registry::call('typo3_feedback_record', [
            'subject' => $subject,
            'observation' => self::MARKER . ' the icon lookup found nothing for content-accordion',
            'model' => 'claude-opus-5',
        ]);

        self::assertSame(['subject: 20 characters past the 100-character limit'], $result->data['cut']);
        self::assertStringContainsString('One field was longer than a stored field and cut', $result->text);

        // The title gets its cut where it always did, and gains no marker.
        $contents = (string) file_get_contents((string) $result->data['path']);
        if (preg_match('/^# (.*)$/m', $contents, $heading) !== 1) {
            self::fail('the feedback was written without the heading it is read by');
        }
        self::assertStringEndsWith('...', $heading[1]);
        self::assertSame(100, mb_strlen($heading[1]));
        self::assertStringNotContainsString('[cut:', $contents);
    }

    /**
     * A fixture observation of exactly that many characters, in prose. A run of
     * one repeated character is what Redaction takes for a hexadecimal value.
     * This case is about the length rule rather than that one.
     */
    private static function observationOfLength(int $characters): string
    {
        $opening = self::MARKER . ' the session reported at length. ';
        $filler = 'It went on for another sentence about what it had seen. ';
        $text = $opening . str_repeat($filler, (int) ceil($characters / strlen($filler)));

        // Ends on a full stop rather than wherever the count falls, so that
        // trailing whitespace is never what the trim in `text()` removes.
        return mb_substr($text, 0, $characters - 1) . '.';
    }

    /** What one `## ` section of a recorded feedback holds. */
    private static function sectionOf(string $contents, string $heading): string
    {
        preg_match('/^## ' . $heading . '\R\R(.*?)(?=\R## |\z)/ms', $contents, $section);

        return trim($section[1] ?? '');
    }

    /**
     * The corpus is what settled the thresholds, so it is what says whether
     * they still hold. 207 recorded feedback, one value among them that had to
     * go, and every git revision, class path and changelog identifier beside it
     * still there.
     *
     * A second file here means one of two things, and both are worth a stop. A
     * rule got greedier, or a feedback with a live value came in by hand rather
     * than through the channel that guards it.
     */
    #[Requirement('R-FBK-011')]
    #[Test]
    public function theRulesTakeNothingOutOfTheCorpusButTheKeyTheyWereWrittenFor(): void
    {
        $directories = array_values(array_filter([Paths::feedback(), Paths::feedbackArchive()], is_dir(...)));
        self::assertNotEmpty($directories);

        $hits = [];
        $read = 0;
        foreach (Finder::create()->files()->in($directories)->depth(0)->name('*.md') as $file) {
            $contents = (string) file_get_contents($file->getPathname());
            if (str_contains($contents, self::MARKER)) {
                continue;
            }

            ++$read;
            $removed = Redaction::of($contents)->removed;
            if ($removed !== []) {
                $hits[$file->getFilename()] = $removed;
            }
        }

        self::assertGreaterThan(100, $read, 'the corpus this was measured against was not read');
        self::assertSame([
            '2026-07-31-185900-after-the-audit-i-invoked-typo3-cms-mcp.md' => ['a 96-character hexadecimal value'],
        ], $hits);
    }

    /**
     * A session files one feedback per subject and files them in one breath. So
     * they open on the same sentence, the one that says which session this is.
     * That sentence is longer than a filename has room for. Eight feedback of
     * 2026-08-01 had the name `debrief-of-the-typo3-14-testimonials-session` to
     * the character, apart by their timestamp alone, and they were about eight
     * different things.
     *
     * The first of a series keeps the opening, because nothing yet says it is
     * one. Every feedback after it takes its name from what it alone says.
     */
    #[Decision('D-FBK-006')]
    #[Requirement('R-FBK-008')]
    #[Test]
    public function notesThatOpenAlikeAreNamedAfterWhatTellsThemApart(): void
    {
        $opening = self::MARKER . ' debrief of the session, missed item: ';

        $first = $this->recordFeedback(['observation' => $opening . 'nothing said which pid the records go to']);
        $second = $this->recordFeedback(['observation' => $opening . 'the fixture harness was written by hand']);
        $third = $this->recordFeedback(['observation' => $opening . 'clearing the cache meant deleting files']);

        self::assertStringContainsString('debrief-of-the-session', $first);
        self::assertStringContainsString('the-fixture-harness-was-written', $second);
        self::assertStringContainsString('clearing-the-cache-meant-deleting', $third);
        self::assertStringNotContainsString('debrief-of-the-session', $second, 'the name says what both feedback say');
        self::assertStringNotContainsString('debrief-of-the-session', $third, 'the name says what both feedback say');
    }

    #[Test]
    public function recordedNotesAreListedNewestFirst(): void
    {
        $file = $this->recordFeedback(['observation' => self::MARKER . ' a listed feedback']);

        $found = Channel::all('open', null, 100);
        $files = array_column($found, 'file');

        self::assertContains($file, $files);
        foreach ($found as $feedback) {
            self::assertSame('open', $feedback['status']);
            self::assertNotSame('', $feedback['title']);
        }
    }

    #[Requirement('R-FBK-002')]
    #[Test]
    public function aNoteThatWasWorkedOffKeepsEverythingItSaid(): void
    {
        // To close a feedback used to mean to delete it, which left the agent
        // that wrote it to see the file vanish. It left the closed half of the
        // list as bare filenames. The front matter that says what the feedback
        // was about went with the file.
        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' the archive keeps what the feedback said',
            'category' => 'tool-gap',
            'tool' => 'typo3_component_lookup',
        ]);

        $archived = Channel::archive($file);
        self::assertSame('feedback/archive/' . basename($file), $archived);
        self::assertFileDoesNotExist($this->inStore($file));
        self::assertStringContainsString(
            'status: closed',
            (string) file_get_contents($this->inStore($archived)),
        );

        self::assertNotContains($file, array_column(Channel::all('open', null, 200), 'file'));
        $feedback = self::noteFor($archived);
        self::assertSame('closed', $feedback['status']);
        self::assertSame('tool-gap', $feedback['category']);
        self::assertSame(['typo3_component_lookup'], $feedback['tools']);

        // And the filters reach it, which is the half the deleted feedback lost.
        self::assertContains(
            $archived,
            array_column(Channel::all('closed', 'tool-gap', 200, 'typo3_component_lookup'), 'file'),
        );
    }

    #[Requirement('R-FBK-002')]
    #[Test]
    public function aNoteThatWasWorkedOffIsStillAnswerableFor(): void
    {
        // What came of a feedback is the commit that archived it. That is the
        // one thing the agent that reported the gap cannot see for itself.
        // Without it the same gap comes in again, and a request that shipped in
        // the meantime goes without a word. Built rather than found. A read of
        // whatever this repository happens to have archived asserts the working
        // tree, not the code. With 138 files in there it can never fail. In a
        // fresh clone of a checkout that had archived nothing it would have had
        // nothing to say either.
        $archived = $this->archived('Answer label lookups with the translation domain');

        $closed = Channel::all('closed', null, 200);
        $mine = array_values(array_filter(
            $closed,
            static fn(array $feedback): bool => $feedback['file'] === $archived,
        ));

        self::assertCount(1, $mine, 'the archived feedback did not read back as closed');
        self::assertSame('closed', $mine[0]['status']);
        self::assertStringStartsWith('feedback/archive/', $mine[0]['file']);
        self::assertNotNull($mine[0]['closedBy']);
        self::assertSame('abc1234', $mine[0]['closedBy']['commit']);
        // The subject is the sentence that says what happened to it.
        self::assertSame('Answer label lookups with the translation domain', $mine[0]['closedBy']['subject']);

        // An open feedback is in the same list and says it is open.
        $file = $this->recordFeedback(['observation' => self::MARKER . ' open beside the closed ones']);
        $all = Channel::all('all', null, 200);
        self::assertContains($file, array_column($all, 'file'));
        self::assertContains('closed', array_column($all, 'status'));
    }

    /**
     * One archived feedback, as `bin/cli feedback:archive` leaves it. Below
     * feedback/archive/, with the commit that closed it in its front matter, in
     * this case's own store like every other fixture here.
     */
    private function archived(string $subject): string
    {
        // The store before the write, so this lands beside what the case
        // records rather than in the archive this repository keeps.
        $this->ownFeedbackStore();

        $name = '2026-07-28-120000-' . self::MARKER . '.md';
        $path = Paths::feedbackArchive() . '/' . $name;

        file_put_contents($path, implode("\n", [
            '---',
            'date: 2026-07-28T12:00:00+00:00',
            'category: wrong-answer',
            'status: closed',
            'closed: 2026-07-28',
            'commit: abc1234',
            'subject: "' . $subject . '"',
            'tool: typo3_label_lookup',
            '---',
            '',
            '# ' . self::MARKER . ' one that was worked off',
            '',
            'The observation this was recorded with.',
            '',
        ]));

        return 'feedback/archive/' . $name;
    }

    #[Test]
    public function onlyAnOpenNoteCanBeArchived(): void
    {
        $file = $this->recordFeedback(['observation' => self::MARKER . ' archived once']);
        Channel::archive($file);

        // The same feedback twice is the mistake this catches: the second call
        // would otherwise overwrite the answer the first one recorded.
        $this->expectException(\InvalidArgumentException::class);
        Channel::archive($file);
    }

    #[Test]
    public function theListCanBeRestrictedToACategory(): void
    {
        $this->recordFeedback(['observation' => self::MARKER . ' a bug feedback', 'category' => 'bug']);

        foreach (Channel::all('all', 'bug', 100) as $feedback) {
            self::assertSame('bug', $feedback['category']);
        }
    }

    #[Requirement('R-FBK-001')]
    #[Decision('D-ANS-017')]
    #[Test]
    public function severalToolsStaySeveralTools(): void
    {
        // An observation about the four tools that go quiet together is a
        // normal one. Stripping the separator ran their names into
        // "typo3_label_lookuptypo3_icon_lookup", which no filter can match —
        // `D-ANS-017`.
        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' both lookups went quiet',
            'tool' => 'typo3_label_lookup, typo3_icon_lookup',
        ]);

        self::assertStringContainsString(
            'tool: typo3_label_lookup, typo3_icon_lookup',
            (string) file_get_contents($this->inStore($file))
        );

        $feedback = self::noteFor($file);
        self::assertSame(['typo3_label_lookup', 'typo3_icon_lookup'], $feedback['tools']);
    }

    /**
     * The recorder still takes a list, and the schema no longer offers one.
     *
     * `tool` had the declaration `["string", "array"]` until `D-ANS-017`, and
     * this case is what covered the second branch. It is below the wire. The
     * validator now refuses a client that sends an array before the call
     * reaches `record()`, and
     * `StdioServerTest::aListOfToolNamesIsRefusedWithTheTypeItWanted` holds
     * that. What remains here is the tolerance itself, for a caller inside this
     * package. A list that reached the recorder would otherwise go without a
     * word, which is the one failure the feedback behind that decision
     * reported.
     */
    #[Requirement('R-FBK-001')]
    #[Decision('D-ANS-017')]
    #[Test]
    public function theRecorderStillTakesAListTheSchemaNoLongerDeclares(): void
    {
        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' recorded with a list',
            'tool' => ['typo3_label_lookup', 'typo3_icon_lookup'],
        ]);

        self::assertSame(['typo3_label_lookup', 'typo3_icon_lookup'], self::noteFor($file)['tools']);
    }

    #[Requirement('R-FBK-001')]
    #[Test]
    public function theListCanBeRestrictedToOneTool(): void
    {
        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' about two tools',
            'tool' => 'typo3_label_lookup typo3_icon_lookup',
        ]);

        // The obvious thing to want from a store of these: every feedback about one tool,
        // including the ones that name it alongside others.
        $files = array_column(Channel::all('all', null, 100, 'typo3_icon_lookup'), 'file');
        self::assertContains($file, $files);

        foreach (Channel::all('all', null, 100, 'typo3_icon_lookup') as $feedback) {
            self::assertContains('typo3_icon_lookup', $feedback['tools']);
        }
        self::assertNotContains($file, array_column(Channel::all('all', null, 100, 'typo3_rule_lookup'), 'file'));
    }

    /**
     * A skill has the form `typo3-extension-health` in the listing a session
     * reads it from, in `skills/`, and in the description that invites a name
     * here. Stored as `typo3extensionhealth` it was an identifier the project
     * carries nowhere. The grep that answers "what do the reports say about
     * this skill" found none of the seven feedback about it.
     */
    #[Requirement('R-FBK-013')]
    #[Test]
    public function aRecordedNameKeepsTheSpellingItWasGivenIn(): void
    {
        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' the skill was read and its lookups were not run',
            'tool' => 'typo3-extension-health',
        ]);

        self::assertStringContainsString(
            'tool: typo3-extension-health',
            (string) file_get_contents($this->inStore($file))
        );
        self::assertSame(['typo3-extension-health'], self::noteFor($file)['tools']);
        self::assertContains(
            $file,
            array_column(Channel::all('open', null, 200, 'typo3-extension-health'), 'file'),
        );
    }

    /**
     * The description asks the observation to open with the task. So every
     * feedback from one session opens on the same words, and the title derived
     * from it says nothing that tells them apart. The subject is what only this
     * one says, and it names both surfaces a maintainer triages on.
     */
    #[Test]
    public function theSubjectNamesTheTitleAndTheFileWhereTheOpeningIsShared(): void
    {
        $opening = self::MARKER . ' task was to work off the open todos in this checkout, and then ';

        $first = $this->recordFeedback([
            'observation' => $opening . 'the release branch log misled the measurement.',
            'subject' => self::MARKER . ' a release branch log answers about shared history',
        ]);
        $second = $this->recordFeedback([
            'observation' => $opening . 'the source had three properties nothing wrote down.',
            'subject' => self::MARKER . ' what a re-read of get.typo3.org has to know',
        ]);

        // The slug fits what a file name has room for, so this holds that the
        // two differ where they used to share a first line.
        self::assertStringContainsString('a-release-branch-log', $first);
        self::assertStringContainsString('what-a-re-read', $second);

        $body = (string) file_get_contents($this->inStore($first));
        self::assertStringContainsString(
            '# ' . self::MARKER . ' a release branch log answers about shared history',
            $body,
        );
        // The task line is what traces the report back to what exposed it. So a
        // subject named apart must not take it out of the report.
        self::assertStringContainsString('task was to work off the open todos', $body);
    }

    /** Without one, the opening still names both, which is what every feedback filed so far did. */
    #[Test]
    public function withoutASubjectTheOpeningStillNamesIt(): void
    {
        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' the resources were never enumerated at all.',
        ]);

        self::assertStringContainsString('the-resources-were', $file);
        self::assertStringContainsString(
            '# ' . self::MARKER . ' the resources were never enumerated at all.',
            (string) file_get_contents($this->inStore($file)),
        );
    }

    /**
     * A name this server does not register is the only kind that carries
     * capitals. It is the kind that arrives because a session reached for
     * somebody else's tool instead of one of these — `D-FBK-039`.
     */
    #[Requirement('R-FBK-013')]
    #[Test]
    public function aNameFromOutsideThisServerKeepsItsCapitals(): void
    {
        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' the resources were never enumerated',
            'tool' => 'typo3_server_scope, ListMcpResourcesTool',
        ]);

        self::assertStringContainsString(
            'tool: typo3_server_scope, ListMcpResourcesTool',
            (string) file_get_contents($this->inStore($file))
        );
        self::assertSame(['typo3_server_scope', 'ListMcpResourcesTool'], self::noteFor($file)['tools']);
        self::assertContains(
            $file,
            array_column(Channel::all('open', null, 200, 'listmcpresourcestool'), 'file'),
            'the filter no longer finds a name it stored',
        );
    }

    #[Requirement('R-FBK-013')]
    #[Decision('D-FBK-039')]
    #[Test]
    #[DataProvider('theSpellingsOneNameArrivesIn')]
    public function aNameIsFoundHoweverItsSeparatorsAreSpelled(string $spelling): void
    {
        // The store holds what the session wrote, so one name arrives in more
        // than one form and the filter is where they meet. That is `D-ANS-006`
        // applied to the one thing that filters this store — `D-FBK-039`.
        $file = $this->recordFeedback([
            'observation' => self::MARKER . ' named the skill with hyphens',
            'tool' => 'typo3-extension-health',
        ]);

        self::assertContains(
            $file,
            array_column(Channel::all('open', null, 200, $spelling), 'file'),
        );
    }

    /** @return array<string, array{0: string}> */
    public static function theSpellingsOneNameArrivesIn(): array
    {
        return [
            'underscores, which is how this project writes it' => ['typo3_extension_health'],
            'no separator at all' => ['typo3extensionhealth'],
            'shouted, which is neither' => ['TYPO3_Extension_Health'],
        ];
    }

    /**
     * The corpus, because that is where the mangled names are. The fix rewrote
     * 43 of them to the form the project uses, and a name that arrives mangled
     * again is what this catches — `D-FBK-039`.
     *
     * Only a name that resolves to something this project has gets a judgement.
     * A feedback that names a tool since gone under a new name, or names its
     * client's wrapper, is a session's report. It stays as the session wrote
     * it.
     */
    #[Requirement('R-FBK-013')]
    #[Decision('D-FBK-039')]
    #[Test]
    public function everyNameTheCorpusCarriesIsSpelledTheWayThisProjectSpellsIt(): void
    {
        $spellings = [];
        foreach ([...array_column(Registry::definitions(), 'name'), ...self::skillNames()] as $name) {
            $spellings[(string) preg_replace('/[^a-z0-9]/', '', $name)] = $name;
        }

        $mangled = [];
        $corpus = Channel::all('all', null, PHP_INT_MAX);
        self::assertGreaterThan(100, count($corpus), 'the corpus this reads was not read');

        foreach ($corpus as $feedback) {
            foreach ($feedback['tools'] as $name) {
                $spelling = $spellings[(string) preg_replace('/[^a-z0-9]/', '', $name)] ?? $name;
                if ($spelling !== $name) {
                    $mangled[] = $feedback['file'] . ': ' . $name . ' is spelled ' . $spelling;
                }
            }
        }

        self::assertSame([], $mangled, 'stored under a spelling this project does not use');
    }

    /** @return array<int, string> */
    private static function skillNames(): array
    {
        $names = [];
        foreach (Finder::create()->directories()->in(Paths::root() . '/skills')->depth(0) as $skill) {
            $names[] = $skill->getFilename();
        }

        return $names;
    }

    /**
     * @return array{file: string, date: string, category: string, status: string, model: string, tool: string, tools: array<int, string>, title: string}
     */
    private static function noteFor(string $file): array
    {
        foreach (Channel::all('all', null, 200) as $feedback) {
            if ($feedback['file'] === $file) {
                return $feedback;
            }
        }

        self::fail($file . ' was written but is not listed');
    }
}
