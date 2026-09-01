<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Contribution\Gerrit;
use TYPO3\DevCompanion\Http\Recent;
use TYPO3\DevCompanion\Knowledge\ReleaseLines;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tool\GerritLookup;

/**
 * The review server is somebody else's host, so what is held here is everything
 * this side does with what comes back: the prefix that has to come off, the
 * question the query actually asks, and the three ways an answer can fail to be
 * one. The live call is exercised by the recording, which is evidence rather
 * than a check.
 */
final class GerritTest extends TestCase
{
    #[After]
    public function forgetWhatWasHeld(): void
    {
        Recent::forget();
    }

    /**
     * A response as the API sends it: the XSSI prefix, then the array. The two
     * revision fields are what `o=CURRENT_REVISION` adds, in the shape
     * review.typo3.org answered change 95040 with on 2026-08-03.
     */
    private const RESPONSE = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[TASK] Deprecate AssetCollector media handling",'
        . '"status":"MERGED","updated":"2026-08-02 20:40:50.000000000","_number":95040,"current_revision_number":3,'
        . '"current_revision":"e82b930e6e0587842427496c5ce01f625b27fb66"}]';

    /**
     * One change of a backport pair, as `change:95169` answered it on
     * 2026-08-14: the id both changes carry is in the number-form response, and
     * nothing in it names the other change.
     */
    private const NUMBERED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Add link parsing in RTE figcaption",'
        . '"status":"NEW","updated":"2026-08-07 17:50:27.000000000","_number":95169,'
        . '"change_id":"I4b0290760f14296feec6ab30ad49595899ca08f4","current_revision_number":2,'
        . '"current_revision":"65aa3ece11944bf20f6baeb52c13b39a2009150f"}]';

    /**
     * The same pair as `change:I4b02…` answered it the same day — both changes,
     * the backport first, because Gerrit orders by last activity and the 13.4
     * change moved an hour after the one on `main`.
     */
    private const SHARED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"13.4","subject":"[BUGFIX] Add link parsing in RTE figcaption",'
        . '"status":"NEW","updated":"2026-08-07 18:54:00.000000000","_number":93202,'
        . '"change_id":"I4b0290760f14296feec6ab30ad49595899ca08f4","current_revision_number":2,'
        . '"current_revision":"a50286ef4ccb170282ed88eee5768d63f81597c5"},'
        . '{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Add link parsing in RTE figcaption",'
        . '"status":"NEW","updated":"2026-08-07 17:50:27.000000000","_number":95169,'
        . '"change_id":"I4b0290760f14296feec6ab30ad49595899ca08f4","current_revision_number":2,'
        . '"current_revision":"65aa3ece11944bf20f6baeb52c13b39a2009150f"}]';

    /**
     * What `message:<number>` really answers, measured against
     * review.typo3.org on 2026-08-05: the change the issue is resolved by, and
     * the change whose own number happens to be that of the issue.
     *
     * The second one is the false positive `feedback/2026-08-05-033826`
     * reported five of. Its message names issue 106318 and carries the queried
     * number in the `Reviewed-on:` trailer alone, which is the trailer a merged
     * change gains and which ends in the change's own number.
     */
    private const BOTH = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Do not split paragraphs at inner linebreaks",'
        . '"status":"NEW","_number":95108,"current_revision_number":1,'
        . '"current_revision":"6929747e86b1d45993fb4ca950fc8e47ba5c1ca4","revisions":{'
        . '"6929747e86b1d45993fb4ca950fc8e47ba5c1ca4":{"commit":{"message":"[BUGFIX] Do not split paragraphs at inner linebreaks\n\nRteHtmlParser divided the content at every line break.\n\nResolves: #88556\nReleases: main, 14.3, 13.4\nChange-Id: I17ba56a7a78a2282495fb422513d4859e2818d05\n"}}}},'
        . '{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Parallel execution of non-parallel scheduler task",'
        . '"status":"MERGED","_number":88556,"current_revision_number":9,'
        . '"current_revision":"a1b2c3d4e5f60718293a4b5c6d7e8f9012345678","revisions":{'
        . '"a1b2c3d4e5f60718293a4b5c6d7e8f9012345678":{"commit":{"message":"[BUGFIX] Parallel execution of non-parallel scheduler task\n\nClose a time window in Scheduler::executeTask().\n\nResolves: #106318\nReleases: main, 13.4\nChange-Id: I1264b5c248dd9aa5402383a498d82650932f29e4\nReviewed-on: https://review.typo3.org/c/Packages/TYPO3.CMS/+/88556\n"}}}}]';

    /**
     * Change 95375 as `change:95375` answered it on 2026-08-24 with
     * `o=CURRENT_COMMIT`, its message trimmed to the first paragraph and the
     * trailers: the patch `feedback/2026-08-24-100458` set out to review, whose
     * commit message names all three issues that session took four calls to
     * reach.
     */
    private const NAMING = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Build a blank form from the blank start template",'
        . '"status":"NEW","_number":95375,"change_id":"I84e6ed9d47c9e8c734c00941c0a06aa5f7ea8414",'
        . '"current_revision_number":2,"current_revision":"4938c8ce9b57d41283adda8e54ea5f6c427b45de","revisions":{'
        . '"4938c8ce9b57d41283adda8e54ea5f6c427b45de":{"commit":{"message":"[BUGFIX] Build a blank form from the blank start template\n\nThe \"create new form\" wizard lets editors pick \"Blank\" or \"Predefined\"\nin step 1.\n\nResolves: #110493\nRelated: #110331\nRelated: #107080\nReleases: main, 14.3\nChange-Id: I84e6ed9d47c9e8c734c00941c0a06aa5f7ea8414\n"}}}}]';

    /**
     * Change 95385 as `change:95385` answered it on 2026-08-26 with
     * `o=CURRENT_COMMIT` and `o=CURRENT_FILES`: the message trimmed to its
     * first paragraph and its trailers, and four of its seven files, which is
     * one of each shape a line count comes back in. Gerrit omits a count that
     * is zero and both of them on a binary, and it sends the map in an order of
     * its own — the fixture keeps that order.
     */
    private const TOUCHING = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[TASK] Add image information to system resources",'
        . '"status":"NEW","_number":95385,"change_id":"I4a60d8265480854a8efcae7caaf0a56337feb1e7",'
        . '"current_revision_number":3,"current_revision":"127c0f8fb38a9acebeed9c91641470015e9520d9","revisions":{'
        . '"127c0f8fb38a9acebeed9c91641470015e9520d9":{"commit":{"message":"[TASK] Add image information to system resources\n\nAllow system resources to report whether they reference an image and\nprovide the detected image dimensions.\n\nResolves: #\nRelated: #110440\nReleases: main, 14.3\n\nChange-Id: I4a60d8265480854a8efcae7caaf0a56337feb1e7\n"},"files":{'
        . '"typo3/sysext/core/Tests/Unit/SystemResource/Type/Fixtures/test.png":'
        . '{"status":"A","new_mode":33188,"binary":true,"size_delta":3009,"size":3009},'
        . '"typo3/sysext/core/Classes/SystemResource/Type/PackageResource.php":'
        . '{"old_mode":33188,"new_mode":33188,"lines_inserted":61,"lines_deleted":2,"size_delta":2251,"size":6018},'
        . '"typo3/sysext/core/Classes/SystemResource/Type/SystemResourceInterface.php":'
        . '{"old_mode":33188,"new_mode":33188,"lines_inserted":12,"size_delta":436,"size":1659},'
        . '"typo3/sysext/core/Classes/SystemResource/Exception/CanNotDetectImageDimensionOfSystemResourceException.php":'
        . '{"status":"A","new_mode":33188,"lines_inserted":24,"size_delta":711,"size":711}'
        . '}}}}]';

    /** The change, a tracker with nothing to say about it, and no stack. */
    private static function touching(string $url): string
    {
        if (str_contains($url, 'forge.typo3.org')) {
            return '{"issues":[],"total_count":0}';
        }

        return str_contains($url, '/related') ? ")]}'\n" . '{"changes":[]}' : self::TOUCHING;
    }

    /**
     * Two files of change 95211 as it answered on 2026-08-26: a move that
     * carries edits and a move that carries none, both with the path they came
     * from. That change moves 38 files out of one system extension into
     * another, which is the shape a rename arrives in at all.
     */
    private const MOVED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[!!!] Removal of EXT:fluid_styled_content",'
        . '"status":"NEW","_number":95211,"change_id":"I8fad38737cbcef2727d4803837f10493166db72b",'
        . '"current_revision_number":2,"current_revision":"d7ef2d93cbc11a03ae704a2bf2f3fa835e1792df","revisions":{'
        . '"d7ef2d93cbc11a03ae704a2bf2f3fa835e1792df":{"files":{'
        . '"typo3/sysext/fluid_styled_content/Classes/DataProcessing/GalleryProcessor.php":'
        . '{"status":"R","old_mode":33188,"new_mode":33188,'
        . '"old_path":"typo3/sysext/frontend/Classes/DataProcessing/GalleryProcessor.php",'
        . '"lines_inserted":2,"lines_deleted":2,"size_delta":-33,"size":17856},'
        . '"typo3/sysext/fluid_styled_content/Configuration/TCA/Overrides/250-tt_content-content_type-menu_abstract.php":'
        . '{"status":"R","old_mode":33188,"new_mode":33188,'
        . '"old_path":"typo3/sysext/frontend/Configuration/TCA/Overrides/250-tt_content-content_type-menu_abstract.php",'
        . '"size_delta":0,"size":753}'
        . '}}}}]';

    /**
     * What `/issues.json?issue_id=110493,110331,107080&status_id=*` answered on
     * 2026-08-24, trimmed to the four fields that are read. One call for the
     * whole set, which is the read a relation is already filled by.
     */
    private const TRACKED = '{"issues":['
        . '{"id":110493,"tracker":{"id":1,"name":"Bug"},"status":{"id":8,"name":"Under Review"},'
        . '"subject":"Bug: Form wizard always selects first template regardless of Blank/Predefined mode"},'
        . '{"id":110331,"tracker":{"id":2,"name":"Feature"},"status":{"id":8,"name":"Under Review"},'
        . '"subject":"Improve form template selection in \"Create new form\""},'
        . '{"id":107080,"tracker":{"id":1,"name":"Bug"},"status":{"id":8,"name":"Under Review"},'
        . '"subject":"Form prototype not selectable with blank form"}],"total_count":3}';

    /** The change, the tracker, and a chain nothing is stacked in. */
    private static function naming(string $url): string
    {
        if (str_contains($url, 'forge.typo3.org')) {
            return self::TRACKED;
        }

        return str_contains($url, '/related') ? ")]}'\n" . '{"changes":[]}' : self::NAMING;
    }

    /**
     * The one the query was for, and not the one that shares its number.
     *
     * Both skills that call this treat a hit as grounds to stop working, so a
     * change that does not name the issue is the answer this tool must not
     * give: a session reading one MERGED core change with a plausible subject
     * has no signal at all that it is spurious — `D-ANS-055`.
     */
    #[Requirement('R-ANS-023')]
    #[Decision('D-ANS-055')]
    #[Test]
    public function aChangeMatchedByItsNumberIsNotAnswered(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::BOTH);

        $answer = $gerrit->changesForIssue('88556');

        self::assertSame([95108], array_column($answer['changes'], 'number'));
        self::assertSame(1, $answer['dropped']);
        self::assertSame('answered', $answer['status']);
    }

    /**
     * The trailer that carries the number without meaning it is the one a
     * merged change gains, and it ends in the change's own number — so reading
     * the message as text would clear exactly the change the filter is for —
     * `D-ANS-055`.
     */
    #[Requirement('R-ANS-023')]
    #[Decision('D-ANS-055')]
    #[Test]
    public function theNumberInAReviewUrlIsNotTheIssueBeingNamed(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::BOTH);

        // 106318 is what the merged change actually resolves, and it is named
        // in a trailer rather than in a URL.
        $answer = $gerrit->changesForIssue('106318');

        self::assertSame([88556], array_column($answer['changes'], 'number'));
        self::assertSame(1, $answer['dropped']);
    }

    /**
     * Everything the server matched being a false positive is the truthful
     * empty answer, which is what the caller acts on: nothing public names this
     * issue — `D-ANS-055`.
     */
    #[Requirement('R-ANS-023')]
    #[Decision('D-ANS-055')]
    #[Test]
    public function anAnswerOfNothingButFalsePositivesIsEmpty(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::BOTH);

        $answer = $gerrit->changesForIssue('95108');

        self::assertSame('empty', $answer['status']);
        self::assertSame([], $answer['changes']);
        self::assertSame(2, $answer['dropped']);
        self::assertNull($answer['cause']);
    }

    /**
     * A page of issues is one question, and each change is sorted onto the
     * issue its commit message names.
     *
     * The alternative is a call per row, which is what keeps a backlog answer
     * from carrying the one signal a triage stops on (`D-ANS-069`). The filter
     * carries over unchanged: a change is the answer to the issue its message
     * names and to no other number in the batch.
     */
    #[Decision('D-ANS-069')]
    #[Test]
    public function aPageOfIssuesIsOneQueryAndEachHitLandsOnTheIssueItNames(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::BOTH;
        });

        $found = $gerrit->changesForIssues([88556, 106318]);

        self::assertCount(1, $asked);
        self::assertStringContainsString('q=message%3A88556%20OR%20message%3A106318', $asked[0]);
        self::assertStringContainsString('o=CURRENT_COMMIT', $asked[0]);
        self::assertSame([95108], array_column($found[88556], 'number'));
        // And the merged change is the answer to the issue it resolves rather
        // than to the one whose number it carries as its own.
        self::assertSame([88556], array_column($found[106318], 'number'));
        self::assertNull($found[88556][0]['message']);
    }

    /**
     * What bounds a query is the URL rather than a documented limit, so a page
     * wider than one batch is more than one call — `D-ANS-069`.
     */
    #[Test]
    public function aPageWiderThanOneQueryIsAskedInBatches(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return ")]}'\n[]";
        });

        self::assertSame([], $gerrit->changesForIssues(range(80001, 80015)));
        self::assertCount(2, $asked);
    }

    /**
     * The commit message is asked for by both forms and read by each for its
     * own thing: the search holds what the server matched against what the
     * message says (`D-ANS-055`), and a change lookup lifts the issues its
     * trailers name out of it (`D-ANS-098`).
     *
     * Only the change hands it back. An issue search answers up to 25 changes
     * and asks whether a patch exists at all, which the prose does not decide;
     * a caller that named one change is establishing that patch, and the
     * trailers it would check are unreachable from the subject (`D-ANS-112`).
     * Null is the shape of both silences, so a client reads one model rather
     * than branching on whether the key is there.
     */
    #[Decision('D-ANS-055')]
    #[Decision('D-ANS-098')]
    #[Decision('D-ANS-112')]
    #[Test]
    public function theCommitMessageIsReadByBothFormsAndHandedBackByAChangeReadByName(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return str_contains($url, 'forge.typo3.org') ? '{"issues":[],"total_count":0}' : self::BOTH;
        });

        $searched = $gerrit->changesForIssue('88556');
        $named = $gerrit->change('95108');

        $queries = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '?q=')));
        self::assertStringContainsString('o=CURRENT_COMMIT', $queries[0]);
        self::assertStringContainsString('o=CURRENT_COMMIT', $queries[1]);
        self::assertNull($searched['changes'][0]['message']);
        self::assertStringContainsString(
            'RteHtmlParser divided the content at every line break.',
            $named['changes'][0]['message'],
        );
    }

    /**
     * The paths the patch set touches, which is the first of the four things a
     * review is told to establish and was reachable only by fetching the change
     * (`D-ANS-112`). A session triaging a shortlist fetched eight open changes
     * into the user's own working checkout for what one query answers, and the
     * user stopped it over that.
     *
     * Sorted here rather than as it arrived: the map comes back in an order of
     * Gerrit's own, and the two `Classes/` files sit apart in it.
     */
    #[Decision('D-ANS-112')]
    #[Test]
    public function aChangeReadByNameCarriesThePathsItsPatchSetTouches(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::touching($url);
        });

        $change = $gerrit->change('95385')['changes'][0];

        self::assertStringContainsString('o=CURRENT_FILES', $asked[0]);
        self::assertSame([
            'typo3/sysext/core/Classes/SystemResource/Exception/CanNotDetectImageDimensionOfSystemResourceException.php',
            'typo3/sysext/core/Classes/SystemResource/Type/PackageResource.php',
            'typo3/sysext/core/Classes/SystemResource/Type/SystemResourceInterface.php',
            'typo3/sysext/core/Tests/Unit/SystemResource/Type/Fixtures/test.png',
        ], array_column($change['files'], 'path'));
        self::assertSame(['added', 'modified', 'modified', 'added'], array_column($change['files'], 'action'));
        self::assertSame([
            'path' => 'typo3/sysext/core/Classes/SystemResource/Type/PackageResource.php',
            'action' => 'modified',
            'insertions' => 61,
            'deletions' => 2,
            'binary' => false,
            'movedFrom' => null,
        ], $change['files'][1]);
    }

    /**
     * A count Gerrit omits is no lines, and a file it counts nothing for is a
     * binary rather than a file the patch set left alone — which is the one
     * misreading a list of paths and numbers invites (`D-ANS-112`).
     */
    #[Decision('D-ANS-112')]
    #[Test]
    public function aFileWithNoLinesToCountSaysWhyItHasNone(): void
    {
        $change = (new Gerrit(static fn(string $url): string => self::touching($url)))->change('95385')['changes'][0];

        self::assertSame([
            'path' => 'typo3/sysext/core/Tests/Unit/SystemResource/Type/Fixtures/test.png',
            'action' => 'added',
            'insertions' => 0,
            'deletions' => 0,
            'binary' => true,
            'movedFrom' => null,
        ], $change['files'][3]);
        // The other omission, on a file that is not binary: nothing was
        // removed, and the review server says that by leaving the count out.
        self::assertSame(12, $change['files'][2]['insertions']);
        self::assertSame(0, $change['files'][2]['deletions']);
    }

    /**
     * A moved file names where it came from, because that is the whole of what
     * separates a rename from a delete and an add — and a review reading the
     * second reports a subsystem as gone (`D-ANS-112`).
     */
    #[Decision('D-ANS-112')]
    #[Test]
    public function aMovedFileNamesThePathItCameFrom(): void
    {
        $change = (new Gerrit(static fn(string $url): string => str_contains($url, '/related')
            ? ")]}'\n" . '{"changes":[]}'
            : self::MOVED))->change('95211')['changes'][0];

        self::assertSame(['renamed', 'renamed'], array_column($change['files'], 'action'));
        self::assertSame(
            'typo3/sysext/frontend/Classes/DataProcessing/GalleryProcessor.php',
            $change['files'][0]['movedFrom'],
        );
        // A move that changed nothing in the file still carries both counts at
        // zero, and it is a move rather than an untouched file all the same.
        self::assertSame(0, $change['files'][1]['insertions']);
        self::assertSame(0, $change['files'][1]['deletions']);
    }

    /**
     * One line per path, saying what the patch does to it and what that costs.
     *
     * The list is printed whole rather than paged: 200 open core changes read
     * on 2026-08-26 touch 5 files at the median, and a cap would fall on the
     * one thing the answer is here to carry (`D-ANS-112`).
     */
    #[Decision('D-ANS-112')]
    #[Test]
    public function theTextHalfNamesEveryPathAndWhatThePatchDoesToIt(): void
    {
        $change = (new Gerrit(static fn(string $url): string => self::touching($url)))->change('95385')['changes'][0];

        $lines = GerritLookup::touches($change, true);

        self::assertSame('### Files (4)', $lines[1]);
        self::assertContains(
            '- modified typo3/sysext/core/Classes/SystemResource/Type/PackageResource.php · +61 -2',
            $lines,
        );
        self::assertContains(
            '- added typo3/sysext/core/Tests/Unit/SystemResource/Type/Fixtures/test.png · binary',
            $lines,
        );
        self::assertContains(
            '- renamed typo3/sysext/fluid_styled_content/Classes/DataProcessing/GalleryProcessor.php · '
                . 'from typo3/sysext/frontend/Classes/DataProcessing/GalleryProcessor.php · +2 -2',
            GerritLookup::touches(
                (new Gerrit(static fn(string $url): string => str_contains($url, '/related')
                    ? ")]}'\n" . '{"changes":[]}'
                    : self::MOVED))->change('95211')['changes'][0],
                true,
            ),
        );
    }

    /**
     * A search asks for no paths, so silence there is not a claim that they
     * could not be read — which the same silence on a change read by name is.
     */
    #[Decision('D-ANS-112')]
    #[Test]
    public function pathsNobodyAskedForAreNotPathsThatCouldNotBeRead(): void
    {
        self::assertSame([], GerritLookup::touches(['files' => null], false));
        self::assertStringContainsString(
            'could not be read',
            implode("\n", GerritLookup::touches(['files' => null], true)),
        );
    }

    /**
     * The commit message whole, which is what a trailer is checked against and
     * what `typo3_commit_message_guide` takes — the subject alone is not either
     * (`D-ANS-112`). Nothing is printed where it did not come back, because the
     * issues section says that once already.
     */
    #[Decision('D-ANS-112')]
    #[Test]
    public function theTextHalfCarriesTheCommitMessageWhole(): void
    {
        $change = (new Gerrit(static fn(string $url): string => self::touching($url)))->change('95385')['changes'][0];

        $lines = GerritLookup::commitMessage($change);

        self::assertSame('### Commit message', $lines[1]);
        self::assertStringContainsString('  Releases: main, 14.3', $lines[3]);
        self::assertSame([], GerritLookup::commitMessage(['message' => null]));
    }

    /**
     * A server that answered without the option is not a reason to drop
     * everything or to hand back the false positive: the one rule that needs no
     * message is that a change is not the answer to the issue whose number it
     * carries as its own — `D-ANS-055`.
     */
    #[Requirement('R-ANS-023')]
    #[Decision('D-ANS-055')]
    #[Test]
    public function aChangeWhoseMessageDidNotComeBackIsJudgedByItsNumberAlone(): void
    {
        $gerrit = new Gerrit(static fn(): string => ")]}'\n"
            . '[{"_number":88556,"branch":"main","status":"MERGED","subject":"[BUGFIX] Parallel execution"},'
            . '{"_number":95108,"branch":"main","status":"NEW","subject":"[BUGFIX] Do not split paragraphs"}]');

        $answer = $gerrit->changesForIssue('88556');

        self::assertSame([95108], array_column($answer['changes'], 'number'));
        self::assertSame(1, $answer['dropped']);
    }

    #[Test]
    public function theQuestionIsWhichChangeNamesTheIssueInItsCommitMessage(): void
    {
        $asked = '';
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked = $url;

            return self::RESPONSE;
        });

        $answer = $gerrit->changesForIssue('#110348', 3);

        // `message:` and not a bare term: the issue number is in the commit
        // message, where `Resolves:` put it, and a free-text search would also
        // match a change that merely mentions it.
        self::assertSame('message:110348', $answer['query']);
        self::assertStringContainsString('q=message%3A110348', $asked);
        self::assertStringContainsString('n=3', $asked);
        self::assertSame('answered', $answer['status']);
    }

    #[Test]
    public function theResponseIsReadPastThePrefixThatKeepsABrowserFromRunningIt(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::RESPONSE);

        $change = $gerrit->changesForIssue('110348')['changes'][0];

        self::assertSame(95040, $change['number']);
        self::assertSame('MERGED', $change['status']);
        self::assertSame('main', $change['branch']);
        self::assertSame('[TASK] Deprecate AssetCollector media handling', $change['subject']);
        self::assertSame('https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040', $change['url']);
    }

    /**
     * A change is a series of patch sets, and a review is of one of them. The
     * commit is what a reviewer holds a local `HEAD` against; the number alone
     * cannot say whether the two are the same thing, and neither is served
     * unless the query asks for the current revision.
     */
    #[Requirement('R-ANS-021')]
    #[Test]
    public function theAnswerCarriesThePatchSetACheckoutIsHeldAgainst(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::RESPONSE;
        });

        $change = $gerrit->change('95040')['changes'][0];

        self::assertStringContainsString('o=CURRENT_REVISION', $asked[0]);
        self::assertSame(3, $change['patchSet']);
        self::assertSame('e82b930e6e0587842427496c5ce01f625b27fb66', $change['commit']);
    }

    /**
     * The other half of that, and `D-ANS-068`. The remote is in the ref because
     * a core clone fetches from the GitHub mirror, where it does not exist.
     *
     * The second case is the padding. Gerrit shards by the change number modulo
     * 100 written as two digits, so 95108 is filed under `08` and a ref built
     * by hand from the last digit alone resolves to nothing.
     */
    #[Decision('D-ANS-068')]
    #[Test]
    public function theAnswerCarriesTheRefThatFetchesThePatchSetItNames(): void
    {
        $change = (new Gerrit(static fn(): string => self::RESPONSE))->change('95040')['changes'][0];

        self::assertSame([
            'ref' => 'refs/changes/40/95040/3',
            'remote' => 'https://review.typo3.org/Packages/TYPO3.CMS',
        ], $change['fetch']);

        $sharded = (new Gerrit(static fn(): string => self::BOTH))->changesForIssue('88556')['changes'][0];

        self::assertSame('refs/changes/08/95108/1', $sharded['fetch']['ref']);
    }

    /**
     * A backport is a change of its own on the release branch, linked to the
     * original by the Change-Id and by nothing else — `D-ANS-080`. Which handle
     * the caller holds decided until now whether it is in the answer at all:
     * `feedback/2026-08-12-092654` reviewed 95169 and learned of its 13.4
     * sibling from the tracker.
     */
    #[Decision('D-ANS-080')]
    #[Test]
    public function aChangeNamedByItsNumberAnswersWithItsSiblings(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return count($asked) === 1 ? self::NUMBERED : self::SHARED;
        });

        $answer = $gerrit->change('95169', 10);

        $queries = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '?q=')));
        self::assertStringContainsString('q=change%3A95169', $queries[0]);
        // The id the first response carried, asked for as a query of its own.
        self::assertStringContainsString('q=change%3AI4b0290760f14296feec6ab30ad49595899ca08f4', $queries[1]);
        self::assertCount(2, $queries);
        self::assertSame([95169, 93202], array_column($answer['changes'], 'number'));
        self::assertSame(['main', '13.4'], array_column($answer['changes'], 'branch'));
        self::assertSame(
            'I4b0290760f14296feec6ab30ad49595899ca08f4',
            $answer['changes'][0]['changeId'],
        );
        // The query that answers this set by hand is the second one.
        self::assertSame('change:I4b0290760f14296feec6ab30ad49595899ca08f4', $answer['query']);
    }

    /**
     * What `commit:cf227b18e20` answered on 2026-08-25, trimmed to the fields
     * that are read and its message to one paragraph and the trailers.
     *
     * Change 89740, merged on `main`. Its `Releases:` line is the one the
     * reporting session reached after four git calls per commit, and after
     * telling the user a release set that line contradicts — `D-ANS-106`.
     */
    private const FIXED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Allow replacing files with different mime-type and extension",'
        . '"status":"MERGED","updated":"2025-07-08 08:39:40.000000000","_number":89740,'
        . '"change_id":"I7e09432feea63d481f356a074ac7e1eb4a422064","current_revision_number":6,'
        . '"current_revision":"cf227b18e205a3720599f07ac98a8747c7008398","revisions":{'
        . '"cf227b18e205a3720599f07ac98a8747c7008398":{"commit":{"message":"[BUGFIX] Allow replacing files with different mime-type and extension\n\nReplacing an existing file.pdf with another file.png was denied.\n\nResolves: #106890\nReleases: main, 13.4, 12.4\nChange-Id: I7e09432feea63d481f356a074ac7e1eb4a422064\nReviewed-on: https://review.typo3.org/c/Packages/TYPO3.CMS/+/89740\n"}}}}]';

    /**
     * The three changes `change:I7e09432feea63d481f356a074ac7e1eb4a422064`
     * answered the same day, most recently moved first: the patch on `main` and
     * the two backports, each carrying the same trailer.
     *
     * 90012 at `aaec618cf33` is the backport hash the session went to
     * `git log origin/13.4 -S` for, and 90014 is the 12.4 change it never found
     * at all.
     */
    private const BACKPORTED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"12.4","subject":"[BUGFIX] Allow replacing files with different mime-type and extension",'
        . '"status":"MERGED","updated":"2025-07-08 08:39:51.000000000","_number":90014,'
        . '"change_id":"I7e09432feea63d481f356a074ac7e1eb4a422064","current_revision_number":5,'
        . '"current_revision":"4a046b51e5130b29655c78e4c0e6fe50e172371c","revisions":{'
        . '"4a046b51e5130b29655c78e4c0e6fe50e172371c":{"commit":{"message":"[BUGFIX] Allow replacing files with different mime-type and extension\n\nResolves: #106890\nReleases: main, 13.4, 12.4\nChange-Id: I7e09432feea63d481f356a074ac7e1eb4a422064\n"}}}},'
        . '{"project":"Packages/TYPO3.CMS","branch":"13.4","subject":"[BUGFIX] Allow replacing files with different mime-type and extension",'
        . '"status":"MERGED","updated":"2025-07-08 08:39:45.000000000","_number":90012,'
        . '"change_id":"I7e09432feea63d481f356a074ac7e1eb4a422064","current_revision_number":3,'
        . '"current_revision":"aaec618cf335f094b361856e9e357c46a5c08508","revisions":{'
        . '"aaec618cf335f094b361856e9e357c46a5c08508":{"commit":{"message":"[BUGFIX] Allow replacing files with different mime-type and extension\n\nResolves: #106890\nReleases: main, 13.4, 12.4\nChange-Id: I7e09432feea63d481f356a074ac7e1eb4a422064\n"}}}},'
        . '{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Allow replacing files with different mime-type and extension",'
        . '"status":"MERGED","updated":"2025-07-08 08:39:40.000000000","_number":89740,'
        . '"change_id":"I7e09432feea63d481f356a074ac7e1eb4a422064","current_revision_number":6,'
        . '"current_revision":"cf227b18e205a3720599f07ac98a8747c7008398","revisions":{'
        . '"cf227b18e205a3720599f07ac98a8747c7008398":{"commit":{"message":"[BUGFIX] Allow replacing files with different mime-type and extension\n\nResolves: #106890\nReleases: main, 13.4, 12.4\nChange-Id: I7e09432feea63d481f356a074ac7e1eb4a422064\n"}}}}]';

    /** What the issue all three resolve answered on 2026-08-25. */
    private const RESOLVED = '{"issues":[{"id":106890,"tracker":{"id":1,"name":"Bug"},'
        . '"status":{"id":5,"name":"Closed"},'
        . '"subject":"Replacing a file in filelist with different file extension"}],"total_count":1}';

    /** The commit, the siblings its Change-Id finds, the tracker, and no stack. */
    private static function fixed(string $url): string
    {
        if (str_contains($url, 'forge.typo3.org')) {
            return self::RESOLVED;
        }
        if (str_contains($url, '/related')) {
            return ")]}'\n" . '{"changes":[]}';
        }

        return str_contains($url, 'q=commit') ? self::FIXED : self::BACKPORTED;
    }

    /**
     * A commit is the handle a checkout hands over, and it reads on exactly as
     * a change number does — `D-ANS-106`.
     *
     * `feedback/2026-08-24-173131` held three hashes and had no way to ask
     * about them: `change:` refuses one, so the session spent four git calls
     * per commit and six where a backport was involved. One call answers the
     * change, the two backports and the branch each of them targets.
     */
    #[Decision('D-ANS-106')]
    #[Test]
    public function aCommitFromACheckoutNamesTheChangeItIsAPatchSetOf(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::fixed($url);
        });

        $answer = $gerrit->commit('cf227b18e20', 10);

        $queries = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '?q=')));
        // The abbreviated hash as it was pasted, under the operator that takes
        // one: `change:cc880c67777` answers HTTP 400 `Invalid change format`.
        self::assertStringContainsString('q=commit%3Acf227b18e20', $queries[0]);
        self::assertStringContainsString('q=change%3AI7e09432feea63d481f356a074ac7e1eb4a422064', $queries[1]);
        self::assertSame([89740, 90014, 90012], array_column($answer['changes'], 'number'));
        self::assertSame(['main', '12.4', '13.4'], array_column($answer['changes'], 'branch'));
        // The backport hash the session went to `git log origin/13.4 -S` for.
        self::assertSame('aaec618cf335f094b361856e9e357c46a5c08508', $answer['changes'][2]['commit']);
        // And the read a change number gets, on the change the commit named.
        self::assertSame([106890], array_column($answer['changes'][0]['issues'], 'issue'));
    }

    /**
     * The trailer is in the message the answer already fetches, and it is the
     * authority the session reached last — `D-ANS-106`.
     *
     * It cost that session a correction: it read `git branch -r --contains` on
     * the `main` commit, told the user the fix was in 14.0 and 13.4, and took
     * it back a turn later when the trailer said 12.4 as well.
     */
    #[Decision('D-ANS-106')]
    #[Test]
    public function aChangeNamesTheBranchesItsReleasesTrailerClaims(): void
    {
        $gerrit = new Gerrit(static fn(string $url): string => self::fixed($url));

        $answer = $gerrit->commit('cf227b18e20', 10);

        self::assertSame(['main', '13.4', '12.4'], $answer['changes'][0]['releases']);
        // On every change the answer carries, because a backport states its own.
        self::assertSame(['main', '13.4', '12.4'], $answer['changes'][2]['releases']);
        // No call of its own: the message it is read from is the one the issues
        // beside it are read from, and the one the answer now carries whole.
        self::assertStringContainsString('Releases: main, 13.4, 12.4', $answer['changes'][0]['message']);

        // The issue direction reads the same message, so it carries it too —
        // which is the direction a triage holding an issue number is in.
        $searched = (new Gerrit(static fn(): string => self::BOTH))->changesForIssue('88556')['changes'][0];

        self::assertSame(['main', '14.3', '13.4'], $searched['releases']);
    }

    /**
     * A message with no such trailer claims nothing, and a message that was not
     * read says nothing — `D-ANS-106`.
     *
     * The first is every change outside the core project, and change 95350 is
     * one on it: it was abandoned with a message naming no branch. The second
     * is the search by words and path, which asks for no message at all, and an
     * empty list there would be this side inventing what it never read.
     */
    #[Decision('D-ANS-106')]
    #[Test]
    public function aMessageThatWasNotReadClaimsNothingRatherThanNoBranches(): void
    {
        $none = ")]}'\n"
            . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[TASK] Skip database setup for database-free functional tests",'
            . '"status":"ABANDONED","_number":95350,"current_revision_number":1,"current_revision":"56bb0a89",'
            . '"revisions":{"56bb0a89":{"commit":{"message":"[TASK] Skip database setup for database-free functional tests\n\nChange-Id: I7d53b24b881f0970ed35f21976b945311a2c45d9\n"}}}}]';

        $silent = new Gerrit(static fn(string $url): string => str_contains($url, '/related')
            ? ")]}'\n" . '{"changes":[]}'
            : $none);

        self::assertSame([], $silent->change('95350')['changes'][0]['releases']);
        self::assertNull(
            (new Gerrit(static fn(): string => self::MATCHED))->changesMatching('impexp', '')['changes'][0]['releases'],
        );
    }

    /**
     * The two claims stand side by side, and the text half says which is which
     * — `D-ANS-106`, `D-ANS-073`.
     *
     * A trailer says where the author meant the patch to go; a change on a
     * branch is a patch that is there. A reader taking the first for the second
     * reports a fix as released on a branch nobody pushed it to.
     */
    #[Decision('D-ANS-106')]
    #[Decision('D-ANS-073')]
    #[Test]
    public function theTextHalfTellsTheTrailerApartFromWhatWasPushed(): void
    {
        $changes = (new Gerrit(static fn(string $url): string => self::fixed($url)))
            ->commit('cf227b18e20', 10)['changes'];

        self::assertSame(['Releases: main, 13.4, 12.4'], GerritLookup::releases($changes[0]));

        $said = implode("\n", GerritLookup::releaseClaim($changes));

        self::assertStringContainsString('the author\'s claim', $said);
        self::assertStringContainsString('sharing a Change-Id', $said);
        // The version half, which is a second source and stays outside this
        // answer rather than being inferred from a branch.
        self::assertStringContainsString('Which release carries it is neither', $said);

        // Nothing to print for a message that claims none, and nothing to say
        // about trailers where no change carried one.
        self::assertSame([], GerritLookup::releases(['releases' => []]));
        self::assertSame([], GerritLookup::releases(['releases' => null]));
        self::assertSame([], GerritLookup::releaseClaim([['releases' => null], ['releases' => []]]));
    }

    /**
     * A hash reaches the review server only where somebody pushed it, so an
     * empty answer there is not "this commit is in no change" — `D-ANS-106`.
     */
    #[Requirement('R-ANS-027')]
    #[Decision('D-ANS-106')]
    #[Test]
    public function anEmptyAnswerForACommitSaysWhatItCannotSeparate(): void
    {
        $said = GerritLookup::indistinguishable('empty', 'commit');

        self::assertNotNull($said);
        self::assertStringContainsString('without credentials', $said);
        self::assertStringContainsString('never pushed for review', $said);
        // The way back in, which is the one that reaches a change whatever
        // commit its patch set stands at.
        self::assertStringContainsString('`query`', $said);

        self::assertNull(GerritLookup::indistinguishable('answered', 'commit'));
    }

    /**
     * The other handle asks the whole question in one query, so nothing is
     * asked twice — including where that query answers one change, which is a
     * patch nobody has backported yet — `D-ANS-080`.
     */
    #[Decision('D-ANS-080')]
    #[Test]
    public function aChangeIdIsNotAskedAgainWhereItIsWhatTheCallerPassed(): void
    {
        $asked = 0;
        $shared = new Gerrit(function (string $url) use (&$asked): string {
            $asked += str_contains($url, '?q=') ? 1 : 0;

            return self::SHARED;
        });
        $alone = new Gerrit(function (string $url) use (&$asked): string {
            $asked += str_contains($url, '?q=') ? 1 : 0;

            return self::NUMBERED;
        });

        $pair = $shared->change('I4b0290760f14296feec6ab30ad49595899ca08f4', 10);
        // Lower case, because a commit message is copied by hand and Gerrit
        // matches the id either way.
        $one = $alone->change('i4b0290760f14296feec6ab30ad49595899ca08f4', 10);

        self::assertSame(2, $asked);
        self::assertSame([93202, 95169], array_column($pair['changes'], 'number'));
        self::assertSame([95169], array_column($one['changes'], 'number'));
    }

    /**
     * `n` is applied after the change that was named, not to the query that
     * finds its siblings. Gerrit orders by last activity, so
     * `change:I4b02…&n=1` answers the backport — a caller asking for one change
     * by number would otherwise be handed the other one — `D-ANS-080`.
     */
    #[Decision('D-ANS-080')]
    #[Test]
    public function theChangeThatWasNamedIsInItsOwnAnswerWhateverTheLimit(): void
    {
        $gerrit = new Gerrit(static fn(string $url): string => str_contains($url, 'q=change%3A95169')
            ? self::NUMBERED
            : self::SHARED);

        $answer = $gerrit->change('95169', 1);

        self::assertSame([95169], array_column($answer['changes'], 'number'));
    }

    /**
     * A second query that did not answer is not an absence of siblings, so the
     * change the caller named stands rather than being replaced by nothing —
     * `D-ANS-080`.
     */
    #[Decision('D-ANS-080')]
    #[Test]
    public function aSiblingQueryThatDidNotAnswerLeavesTheNamedChangeStanding(): void
    {
        $answers = [self::NUMBERED, null];
        $gerrit = new Gerrit(static function () use (&$answers): ?string {
            return array_shift($answers);
        });

        $answer = $gerrit->change('95169', 10);

        self::assertSame('answered', $answer['status']);
        self::assertSame([95169], array_column($answer['changes'], 'number'));
    }

    /**
     * The option is the server's to honour. A response without the revision
     * fields is still an answer about the change, so the patch set is absent
     * rather than guessed — and zero is what the schema calls named none.
     *
     * There is no ref either, because a ref names a patch set: null rather than
     * a string carrying a zero, which would fetch nothing and read like a
     * command to run — `D-ANS-068`.
     */
    #[Requirement('R-ANS-021')]
    #[Decision('D-ANS-068')]
    #[Test]
    public function aChangeWithoutARevisionSaysSo(): void
    {
        $gerrit = new Gerrit(static fn(): string => ")]}'\n" . '[{"_number":95040,"branch":"main","status":"NEW"}]');

        $change = $gerrit->change('95040')['changes'][0];

        self::assertSame(0, $change['patchSet']);
        self::assertSame('', $change['commit']);
        self::assertNull($change['fetch']);
    }

    /**
     * Change 91563, the head of the stack `feedback/2026-08-21-074010` read as
     * one change.
     */
    private const STACKED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[WIP][FEATURE] Introduce Action API",'
        . '"status":"NEW","updated":"2026-08-19 09:12:41.000000000","_number":91563,'
        . '"change_id":"I8ca1a6d0d3d5e0c02c1d3a2b7e6f5a4c3b2a1908","current_revision_number":46,'
        . '"current_revision":"ad7dc9be0e8a2c4f6b8d0a2c4e6f8a0b2c4d6e80"}]';

    /**
     * What `/changes/91563/revisions/current/related` answered on 2026-08-21,
     * four of its fifteen entries: the top of the stack, the one merged part,
     * the change that was asked about, and what it is built on.
     *
     * 92323 is the entry the two revision numbers are two facts on — the chain
     * holds patch set 8 while the change stands at 10.
     */
    private const RELATED = ")]}'\n"
        . '{"changes":['
        . '{"project":"Packages/TYPO3.CMS","change_id":"I455266664387f5bb28c7518fb962a13d90b75ff0",'
        . '"commit":{"commit":"e02be2c6672f09e8ca570924e13aff37efc89ff2",'
        . '"parents":[{"commit":"9ada9785fe1c8ff22b3f9ccf4d431c70e735bdf4"}],'
        . '"subject":"[WIP][FEATURE] Provide Record Actions"},'
        . '"_change_number":92197,"_revision_number":9,"_current_revision_number":9,"status":"NEW"},'
        . '{"project":"Packages/TYPO3.CMS","change_id":"I3b8c1e6a5d4f2c0b9a8e7d6c5b4a39281706f5e4",'
        . '"commit":{"commit":"b4c6d8e0f2a4c6e80a2c4e6f8a0b2c4d6e8f0a2c",'
        . '"parents":[{"commit":"ad7dc9be0e8a2c4f6b8d0a2c4e6f8a0b2c4d6e80"}],'
        . '"subject":"[TASK] Avoid `json_encode()` workarounds in Settings API"},'
        . '"_change_number":92323,"_revision_number":8,"_current_revision_number":10,"status":"MERGED"},'
        . '{"project":"Packages/TYPO3.CMS","change_id":"I8ca1a6d0d3d5e0c02c1d3a2b7e6f5a4c3b2a1908",'
        . '"commit":{"commit":"ad7dc9be0e8a2c4f6b8d0a2c4e6f8a0b2c4d6e80",'
        . '"parents":[{"commit":"c4e6f8a0b2c4d6e8f0a2c4e6f8a0b2c4d6e8f0a2"}],'
        . '"subject":"[WIP][FEATURE] Introduce Action API"},'
        . '"_change_number":91563,"_revision_number":46,"_current_revision_number":46,"status":"NEW"},'
        . '{"project":"Packages/TYPO3.CMS","change_id":"Ie6f8a0b2c4d6e8f0a2c4e6f8a0b2c4d6e8f0a2c4",'
        . '"commit":{"commit":"c4e6f8a0b2c4d6e8f0a2c4e6f8a0b2c4d6e8f0a2","parents":[],'
        . '"subject":"[TASK] Introduce JSON SchemaBuilder"},'
        . '"_change_number":93064,"_revision_number":16,"_current_revision_number":16,"status":"NEW"}]}';

    /** The stacked change, with its chain where that is what was asked for. */
    private static function stacked(string $url): string
    {
        return str_contains($url, '/related') ? self::RELATED : self::STACKED;
    }

    /**
     * A change read alone says a feature exists; the stack under it says what
     * the feature consists of and how far it has got — `D-ANS-094`.
     *
     * `feedback/2026-08-21-074010` read 91563 and was handed the head of a
     * fifteen-change stack with nothing saying there was more to read. The
     * order is git parentage, child first, so the place of the change in the
     * list is what says how much is stacked on it and how much it is built on.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function aChangeCarriesTheStackOfChangesItIsOnePartOf(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::stacked($url);
        });

        $change = $gerrit->change('91563')['changes'][0];

        self::assertContains(
            'https://review.typo3.org/changes/91563/revisions/current/related',
            $asked,
        );
        self::assertSame([92197, 92323, 91563, 93064], array_column($change['chain'], 'number'));
        // The entry's own state, which is what says that one part of the
        // feature landed and one is still open.
        self::assertSame(['NEW', 'MERGED', 'NEW', 'NEW'], array_column($change['chain'], 'status'));
        self::assertSame([false, false, true, false], array_column($change['chain'], 'thisChange'));
        self::assertSame('[TASK] Introduce JSON SchemaBuilder', $change['chain'][3]['subject']);
    }

    /**
     * The two revision numbers per entry are two facts, and the merged entry is
     * where they come apart: the stack holds patch set 8 of change 92323 while
     * that change stands at 10.
     *
     * Acting on the patch set the chain names is the mistake carrying only that
     * number would make possible, which is `D-ANS-094`'s second **Wrong if**.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function aChainEntryNamesThePatchSetInTheStackAndTheOneItStandsAt(): void
    {
        $gerrit = new Gerrit(static fn(string $url): string => self::stacked($url));

        $chain = $gerrit->change('91563')['changes'][0]['chain'];

        self::assertSame(['chainedAt' => 8, 'patchSet' => 10], [
            'chainedAt' => $chain[1]['chainedAt'],
            'patchSet' => $chain[1]['patchSet'],
        ]);
        // The ordinary entry, where the chain holds what the change stands at.
        self::assertSame([46, 46], [$chain[2]['chainedAt'], $chain[2]['patchSet']]);
    }

    /**
     * By the number and not by the Change-Id. `/changes/<Change-Id>/…/related`
     * answered 404 `Multiple changes found` on 2026-08-21 for the backport pair
     * `D-ANS-080` puts in this answer, so the handle the caller passed is not
     * the handle this call can be made with — `D-ANS-094`.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function theChainIsAskedByTheChangeNumberOfEveryChangeInTheAnswer(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return str_contains($url, 'q=change%3A95169') ? self::NUMBERED : self::SHARED;
        });

        $gerrit->change('95169', 10);

        self::assertSame([
            'https://review.typo3.org/changes/95169/revisions/current/related',
            'https://review.typo3.org/changes/93202/revisions/current/related',
        ], array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '/related'))));
    }

    /**
     * A change nothing is stacked on and that is stacked on nothing. The review
     * server answers `{"changes":[]}` for it — 20 bytes measured on 2026-08-21
     * — and that is the ordinary case rather than a failure, so it is an empty
     * chain and not a null one — `D-ANS-094`.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function aChangeStandingAloneHasAnEmptyChain(): void
    {
        $gerrit = new Gerrit(static fn(string $url): string => str_contains($url, '/related')
            ? ")]}'\n" . '{"changes":[]}'
            : self::RESPONSE);

        $change = $gerrit->change('95040')['changes'][0];

        self::assertSame([], $change['chain']);
    }

    /**
     * A chain that could not be read is not a change standing alone. Nothing in
     * the change payload says whether there is one, so an empty list here would
     * be this side inventing the answer the call failed to bring back —
     * `D-ANS-094`.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function aChainCallThatDidNotAnswerIsNotAChangeStandingAlone(): void
    {
        $gerrit = new Gerrit(static fn(string $url): ?string => str_contains($url, '/related')
            ? null
            : self::RESPONSE);

        $answer = $gerrit->change('95040');

        self::assertSame('answered', $answer['status']);
        self::assertNull($answer['changes'][0]['chain']);
    }

    /**
     * The stack is what the caller reads, so the text half says where the
     * change sits in it and which entries have moved on — `D-ANS-094`.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function theTextHalfSaysWhereInTheStackTheChangeSits(): void
    {
        $chain = (new Gerrit(static fn(string $url): string => self::stacked($url)))
            ->change('91563')['changes'][0];

        $said = implode("\n", GerritLookup::chain($chain, true));

        self::assertStringContainsString('4 changes, 2 stacked on this one and 1 under it', $said);
        self::assertStringContainsString('91563 · NEW · [WIP][FEATURE] Introduce Action API · this change', $said);
        self::assertStringContainsString('chained at patch set 8, now at 10', $said);
        // Nothing to print for a change standing alone: that is the ordinary
        // case rather than a finding.
        self::assertSame([], GerritLookup::chain(['chain' => []], true));
        // And an issue search asked for none of it, so silence there is not a
        // claim that it could not be read.
        self::assertSame([], GerritLookup::chain(['chain' => null], false));
        self::assertStringContainsString('could not be read', implode("\n", GerritLookup::chain(['chain' => null], true)));
    }

    /**
     * A chain entry is the one record in this answer that arrived as a bare
     * number, and the endpoint answers the project beside it — so the URL is
     * built here from the same two fields a change URL is built from
     * (`D-ANS-103`).
     *
     * An entry naming no project falls back to the number alone, which the
     * review server resolves. The canonical path asserts a project and renders
     * a page whether or not the change is there, so composing one out of
     * nothing answers a caller with a page about nothing.
     */
    #[Decision('D-ANS-103')]
    #[Test]
    public function aChainEntryCarriesTheUrlBuiltFromItsProjectAndNumber(): void
    {
        $chain = (new Gerrit(static fn(string $url): string => self::stacked($url)))
            ->change('91563')['changes'][0]['chain'];

        self::assertSame(
            'https://review.typo3.org/c/Packages/TYPO3.CMS/+/92323',
            $chain[1]['url'],
        );

        // The same chain URL, so what was held for the first reading is what the
        // second would be answered with.
        Recent::forget();
        $unnamed = new Gerrit(static fn(string $url): string => str_contains($url, '/related')
            ? ")]}'\n" . '{"changes":[{"commit":{"subject":"[TASK] Something"},"_change_number":92323,'
                . '"_revision_number":1,"_current_revision_number":1,"status":"NEW"}]}'
            : self::STACKED);

        self::assertSame(
            'https://review.typo3.org/c/92323',
            $unnamed->change('91563')['changes'][0]['chain'][0]['url'],
        );
    }

    /**
     * An agent repeating an id to a person renders it as a link, and a bare
     * number gives it nothing to render but a guess — `D-ANS-103`.
     *
     * Both lines print from a record that already holds the URL for the issues
     * and now holds it for the chain, so what the two halves say is one thing.
     */
    #[Decision('D-ANS-103')]
    #[Test]
    public function everyIdTheTextHalfNamesCarriesTheUrlThatReachesIt(): void
    {
        $stacked = (new Gerrit(static fn(string $url): string => self::stacked($url)))
            ->change('91563')['changes'][0];
        $naming = (new Gerrit(static fn(string $url): string => self::naming($url)))
            ->change('95375')['changes'][0];

        self::assertStringContainsString(
            '- 92323 · MERGED · [TASK] Avoid `json_encode()` workarounds in Settings API · chained at patch set 8, '
                . 'now at 10 · https://review.typo3.org/c/Packages/TYPO3.CMS/+/92323',
            implode("\n", GerritLookup::chain($stacked, true)),
        );
        self::assertStringContainsString(
            '- related #107080 — Bug · Under Review · Form prototype not selectable with blank form · '
                . 'https://forge.typo3.org/issues/107080',
            implode("\n", GerritLookup::issues($naming, true)),
        );
    }

    /**
     * The two relations are told apart where both are in one answer. A stack is
     * different changes built on one another; a shared Change-Id is one patch
     * on several branches, and reading the first as the second would say the
     * label was the whole of the work — `D-ANS-094`.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function theTwoRelationsAChangeStandsInAreToldApart(): void
    {
        $said = implode("\n", GerritLookup::relations(false));

        self::assertStringContainsString('built on one another', $said);
        self::assertStringContainsString('Change-Id', $said);
        // The entry's status is the entry's, which is the first way a chain is
        // misread.
        self::assertStringContainsString('MERGED entry says that change landed', $said);
        // Said only where an entry in this answer is behind, because it is a
        // warning about those entries rather than a property of chains.
        self::assertStringNotContainsString('moved on since', $said);
        self::assertStringContainsString('moved on since', implode("\n", GerritLookup::relations(true)));
    }

    /**
     * What a chain entry is evidence for, said where the caller reads it.
     *
     * A review had the chain in its first answer, read it as "there is a
     * follow-up", and reported four shapes the change above it explains —
     * `D-SKL-090`. The paragraph said what a chain is and stopped there.
     */
    #[Decision('D-SKL-090')]
    #[Test]
    public function theChainSaysWhatAShapeAboveItExplains(): void
    {
        $said = implode("\n", GerritLookup::relations(false));

        self::assertStringContainsString('evidence about the shape of the change itself', $said);
        self::assertStringContainsString('class left non-final', $said);
        self::assertStringContainsString('reads exactly like an oversight in this one', $said);
    }

    /**
     * The commit message is what joins a patch to the tracker, and the answer
     * carried neither — `D-ANS-098`.
     *
     * `feedback/2026-08-24-100458` walked change 95375 to change 95015 to issue
     * #107080 to an abandoned patch that answered the question it had been
     * asked twice. Its first call already held all three issues, inside a
     * message this tool did not hand over, and the session read the second
     * `Resolves:` line by eye and nearly missed it.
     *
     * The two trailers are told apart because they are different claims: what
     * the patch closes, and what it touches.
     */
    #[Decision('D-ANS-098')]
    #[Requirement('R-ANS-029')]
    #[Test]
    public function aChangeCarriesTheIssuesItsCommitMessageNames(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::naming($url);
        });

        $change = $gerrit->change('95375')['changes'][0];

        self::assertStringContainsString('o=CURRENT_COMMIT', $asked[0]);
        self::assertSame([110493, 110331, 107080], array_column($change['issues'], 'issue'));
        self::assertSame(['resolves', 'related', 'related'], array_column($change['issues'], 'trailer'));
        // Filled with what says whether to read one, which a bare number is the
        // failure `R-ANS-029` exists against.
        self::assertSame([
            'issue' => 107080,
            'trailer' => 'related',
            'subject' => 'Form prototype not selectable with blank form',
            'tracker' => 'Bug',
            'status' => 'Under Review',
            'url' => 'https://forge.typo3.org/issues/107080',
        ], $change['issues'][2]);
        // One read of the tracker for the whole set.
        self::assertSame(
            ['https://forge.typo3.org/issues.json?issue_id=110493%2C110331%2C107080&status_id=%2A&limit=3'],
            array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, 'forge'))),
        );
    }

    /**
     * A trailer naming no number names no issue, and a message carrying no
     * trailer names none either — `D-ANS-098`.
     *
     * Both are on the review server as it stands. Patch set 46 of change 91563
     * carries the line `Resolves: #` with nothing after it, and change 95350
     * was abandoned with a message that names no issue at all — an issue
     * nobody can look up is worse than none, and neither costs a call to the
     * tracker to find that out.
     */
    #[Decision('D-ANS-098')]
    #[Test]
    public function aTrailerWithNoNumberBehindItNamesNoIssue(): void
    {
        $empty = ")]}'\n"
            . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[WIP][FEATURE] Introduce Action API",'
            . '"status":"NEW","_number":91563,"current_revision_number":46,"current_revision":"ad7dc9be0e8",'
            . '"revisions":{"ad7dc9be0e8":{"commit":{"message":"[WIP][FEATURE] Introduce Action API\n\nReleases: main\nResolves: #\nChange-Id: I242eedc16bb7ca1e5c83adeaa0526a9e68f275e2\n"}}}}]';
        $none = ")]}'\n"
            . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[TASK] Skip database setup for database-free functional tests",'
            . '"status":"ABANDONED","_number":95350,"current_revision_number":1,"current_revision":"56bb0a89",'
            . '"revisions":{"56bb0a89":{"commit":{"message":"[TASK] Skip database setup for database-free functional tests\n\nMark 25 functional test classes as not requiring database setup.\n\nChange-Id: I7d53b24b881f0970ed35f21976b945311a2c45d9\n"}}}}]';

        $asked = [];
        $answering = static function (string $change) use (&$asked): \Closure {
            return function (string $url) use (&$asked, $change): string {
                $asked[] = $url;

                return str_contains($url, '/related') ? ")]}'\n" . '{"changes":[]}' : $change;
            };
        };

        $said = (new Gerrit($answering($empty)))->change('91563')['changes'][0];
        $silent = (new Gerrit($answering($none)))->change('95350')['changes'][0];

        self::assertSame([], $said['issues']);
        self::assertSame([], $silent['issues']);
        self::assertSame([], array_filter($asked, static fn(string $url): bool => str_contains($url, 'forge')));
    }

    /**
     * The issues are what the caller walks on, so the text half names them with
     * the claim each trailer makes — `D-ANS-098`.
     */
    #[Decision('D-ANS-098')]
    #[Test]
    public function theTextHalfNamesTheIssuesAndWhatEachTrailerClaims(): void
    {
        $change = (new Gerrit(static fn(string $url): string => self::naming($url)))
            ->change('95375')['changes'][0];

        $said = implode("\n", GerritLookup::issues($change, true));

        self::assertStringContainsString('Issues named in the commit message (3)', $said);
        self::assertStringContainsString('- resolves #110493 — Bug · Under Review', $said);
        self::assertStringContainsString('- related #107080 — Bug · Under Review · Form prototype not selectable', $said);
        // Nothing to print for a message that names none, which is what a patch
        // outside the core's own process ordinarily is.
        self::assertSame([], GerritLookup::issues(['issues' => []], true));
        // And an issue search asked for none of it, so silence there is not a
        // claim that the message was read and named nothing.
        self::assertSame([], GerritLookup::issues(['issues' => null], false));
        self::assertStringContainsString(
            'did not come back',
            implode("\n", GerritLookup::issues(['issues' => null], true)),
        );
    }

    /**
     * The review half of change 93319, as review.typo3.org answered it on
     * 2026-08-14 with the labels, the accounts and the messages asked for.
     *
     * Trimmed to what is read: the avatars, the reviewers and the revisions are
     * three quarters of the 57.9 KB and nothing here looks at them. Verified
     * runs to +2 on this server, which is why a +1 leaves it unsatisfied.
     */
    private const REVIEWED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[TASK] Add E2E tests for page creation",'
        . '"status":"NEW","_number":93319,"change_id":"I4924af7ec012e7b12771d64c75687f194952e703",'
        . '"current_revision_number":21,"current_revision":"39f5c1b0a2d4e6f8a0b2c4d6e8f0a2b4c6d8e0f2",'
        . '"total_comment_count":0,"unresolved_comment_count":0,'
        . '"submit_records":[{"rule_name":"gerrit~DefaultSubmitRule","status":"NOT_READY","labels":['
        . '{"label":"Verified","status":"NEED"},{"label":"Code-Review","status":"NEED"}]}],'
        . '"labels":{"Verified":{"recommended":{"_account_id":7064,"name":"Oliver Klee"},"all":['
        . '{"value":1,"date":"2026-08-13 18:16:10.000000000","_account_id":7064,"name":"Oliver Klee"},'
        . '{"value":1,"date":"2026-08-13 17:39:41.000000000","_account_id":52386,"name":"core-ci","tags":["SERVICE_USER"]}],'
        . '"values":{"-1":"Fails"," 0":"No score","+1":"Verified","+2":"Verified by team member"},"value":1,"default_value":0},'
        . '"Code-Review":{"all":[{"value":0,"_account_id":7059,"name":"Benni Mack"}],'
        . '"values":{"-2":"This shall not be merged"," 0":"No score","+2":"Looks good to me, approved"},"default_value":0}}';

    /** What `o=MESSAGES` adds to it, three of the 46 that change carries. */
    private const LOG = ',"messages":['
        . '{"id":"a1","author":{"_account_id":52386,"name":"core-ci","tags":["SERVICE_USER"]},'
        . '"date":"2026-07-15 12:33:09.000000000","message":"Patch Set 20: Verified+1\n\nCore CI is happy: https://git.typo3.org/typo3/CI/cms/-/pipelines/104789","_revision_number":20},'
        . '{"id":"a2","author":{"_account_id":7059,"name":"Benni Mack"},"date":"2026-07-17 07:17:46.000000000",'
        . '"message":"Patch Set 20: Code-Review+1","_revision_number":20},'
        . '{"id":"a3","author":{"_account_id":7059,"name":"Benni Mack"},"date":"2026-08-13 17:27:20.000000000",'
        . '"tag":"autogenerated:gerrit:newPatchSet",'
        . '"message":"Patch Set 21: Patch Set 20 was rebased\n\nOutdated Votes:\n* Code-Review+1 (copy condition: \"changekind:NO_CHANGE OR is:MIN\")\n","_revision_number":21}]';

    /** The change as that query answers it, with the log where it asked for one. */
    private static function reviewed(string $url): string
    {
        return self::REVIEWED . (str_contains($url, 'o=MESSAGES') ? self::LOG : '') . '}]';
    }

    /**
     * The votes are what the surface turns on, so they come with a change
     * nobody asked anything extra for — `D-ANS-079`.
     *
     * A reviewer picking this change up needs two things the fields before this
     * one cannot say: that the Verified+1 is not enough to submit on this
     * server, and that Code-Review stands at nothing although somebody is on
     * it. Both are the label state rather than a count of patch sets.
     */
    #[Decision('D-ANS-079')]
    #[Test]
    public function aChangeCarriesTheVoteEachLabelStandsAt(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::reviewed($url);
        });

        $change = $gerrit->change('93319')['changes'][0];

        self::assertStringContainsString('o=DETAILED_LABELS', $asked[0]);
        self::assertStringContainsString('o=DETAILED_ACCOUNTS', $asked[0]);
        self::assertSame('I4924af7ec012e7b12771d64c75687f194952e703', $change['changeId']);
        self::assertSame([
            [
                'label' => 'Verified',
                'state' => 'NEED',
                'satisfied' => false,
                'votes' => [
                    ['voter' => 'Oliver Klee', 'value' => 1, 'on' => '2026-08-13 18:16:10.000000000'],
                    ['voter' => 'core-ci', 'value' => 1, 'on' => '2026-08-13 17:39:41.000000000'],
                ],
            ],
            [
                'label' => 'Code-Review',
                'state' => 'NEED',
                'satisfied' => false,
                // A reviewer who was added and has not voted, which is not the
                // same answer as nobody being on it.
                'votes' => [['voter' => 'Benni Mack', 'value' => 0, 'on' => '']],
            ],
        ], $change['labels']);
    }

    /**
     * The comments are a second endpoint, and a change that says it carries
     * none is answered without asking it.
     *
     * 93319 is that change: `total_comment_count` is 0 and its whole review
     * history is in the messages. The count is in the payload of the call that
     * was made anyway, so the second one is spent only where there is something
     * to fetch.
     */
    #[Decision('D-ANS-079')]
    #[Test]
    public function aChangeWithNoCommentCostsNoCallToFindThatOut(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::reviewed($url);
        });

        $change = $gerrit->change('93319')['changes'][0];

        self::assertSame([], array_filter($asked, static fn(string $url): bool => str_contains($url, '/comments')));
        self::assertSame(0, $change['commentCount']);
        self::assertSame([], $change['comments']);
    }

    /**
     * What `/changes/95179/comments` answered on 2026-08-14, trimmed to the
     * fields that are read. One of the three is unresolved and has a reply
     * under it, which is the case the surface is about.
     */
    private const COMMENTS = ")]}'\n"
        . '{"/PATCHSET_LEVEL":['
        . '{"author":{"_account_id":37281,"name":"Mathias Brodala"},"unresolved":true,"patch_set":1,'
        . '"id":"9dc25800_aed74e93","updated":"2026-08-10 06:45:34.000000000",'
        . '"message":"Can we add an Important RST for this?"},'
        . '{"author":{"_account_id":7342,"name":"Georg Ringer"},"unresolved":false,"patch_set":1,'
        . '"id":"85e9c99f_d1a448dd","updated":"2026-08-10 08:02:08.000000000",'
        . '"message":"this should be have an important.rst + no 13.4 backport pls"},'
        . '{"author":{"_account_id":21249,"name":"Benjamin Kott"},"unresolved":true,"patch_set":1,'
        . '"id":"399ce3e3_1d8a86be","in_reply_to":"9dc25800_aed74e93","updated":"2026-08-10 07:02:56.000000000",'
        . '"message":"sure! will check it later"}]}';

    /**
     * The same change, with comments on it and no messages asked for.
     *
     * The unresolved count is the one those three comments come to under the
     * rule the review server states — one thread of the two, Mathias Brodala's,
     * whose last comment is Benjamin Kott's "sure! will check it later". A
     * tally of the flag says two, which is what `D-ANS-111` took out.
     */
    private const COMMENTED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Do not split paragraphs",'
        . '"status":"NEW","_number":95179,"change_id":"I17ba56a7a78a2282495fb422513d4859e2818d05",'
        . '"current_revision_number":1,"current_revision":"6929747e86b1d45993fb4ca950fc8e47ba5c1ca4",'
        . '"total_comment_count":3,"unresolved_comment_count":1,"labels":{},"submit_records":[]}]';

    /**
     * A comment somebody replied to without resolving is two facts, and this
     * server hands over both rather than deciding between them.
     *
     * `unresolved` alone would report a thread that was answered; the reply
     * alone would drop one that was answered and left open on purpose. Change
     * 95179 carries exactly that comment, which is why neither field is read
     * here into an answer of "nobody answered" — `D-ANS-079`.
     */
    #[Decision('D-ANS-079')]
    #[Test]
    public function aCommentCarriesItsThread(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return str_contains($url, '/comments') ? self::COMMENTS : self::COMMENTED;
        });

        $change = $gerrit->change('95179')['changes'][0];

        self::assertContains('https://review.typo3.org/changes/95179/comments', $asked);
        self::assertSame(3, $change['commentCount']);
        // Oldest first, across the files rather than within one: a thread is
        // read in the order it was written.
        self::assertSame(
            ['Mathias Brodala', 'Benjamin Kott', 'Georg Ringer'],
            array_column($change['comments'], 'author'),
        );

        $answered = $change['comments'][1];
        self::assertTrue($answered['unresolved']);
        self::assertSame('9dc25800_aed74e93', $answered['inReplyTo']);
        self::assertSame('/PATCHSET_LEVEL', $answered['file']);
        self::assertNull($answered['line']);
        self::assertSame(1, $answered['patchSet']);
    }

    /**
     * What `/changes/91127/comments` answered on 2026-08-26, trimmed to the
     * fields that are read and with the longest message cut.
     *
     * The change `feedback/2026-08-24-183447` reviewed: seven comments in five
     * threads, one of which carries `unresolved: true` — Oliver Klee's question
     * of 2025-12-09, which Torben Hansen answered and Klee closed with "Ack.".
     * The rows come back per file and unordered within one, which is what the
     * chronological sort is for.
     */
    private const THREADED = ")]}'\n"
        . '{"/COMMIT_MSG":['
        . '{"author":{"_account_id":21079,"name":"Benjamin Franzke"},"unresolved":false,"patch_set":10,"line":27,'
        . '"id":"a3d7d001_dca4140c","updated":"2026-08-25 07:31:29.000000000",'
        . '"message":"ack, voting this as a hack for the state we have"}],'
        . '"/PATCHSET_LEVEL":['
        . '{"author":{"_account_id":6382,"name":"Christian Weiske"},"unresolved":false,"patch_set":4,'
        . '"id":"0d3d8f33_97d51c8c","updated":"2026-01-12 09:44:37.000000000",'
        . '"message":"I can confirm that this patch works on 12.4.40 as long as `bodyContent` is reset."},'
        . '{"author":{"_account_id":7064,"name":"Oliver Klee"},"unresolved":true,"patch_set":4,'
        . '"id":"aff6212c_6070833d","updated":"2025-12-09 08:59:28.000000000",'
        . '"message":"Is there any way this can be covered with a functional test?"},'
        . '{"author":{"_account_id":7064,"name":"Oliver Klee"},"unresolved":false,"patch_set":4,'
        . '"id":"15369d34_7841ae25","in_reply_to":"3d14ec42_498fd7ea","updated":"2025-12-09 18:45:28.000000000",'
        . '"message":"Ack."},'
        . '{"author":{"_account_id":26517,"name":"Torben Hansen"},"unresolved":false,"patch_set":4,'
        . '"id":"3d14ec42_498fd7ea","in_reply_to":"aff6212c_6070833d","updated":"2025-12-09 18:38:03.000000000",'
        . '"message":"For functional test I would say no. Acceptance test would be an option."},'
        . '{"author":{"_account_id":7064,"name":"Oliver Klee"},"unresolved":false,"patch_set":6,'
        . '"id":"008a0294_ad3c783f","updated":"2026-05-03 08:30:45.000000000",'
        . '"message":"(Needs a rebase and conflict resolution, though.)"},'
        . '{"author":{"_account_id":21249,"name":"Benjamin Kott"},"unresolved":false,"patch_set":9,'
        . '"id":"e59fe452_b738cdf1","updated":"2026-08-24 18:21:05.000000000",'
        . '"message":"i added the missing tests and also reenabled the site request tests"}]}';

    /**
     * That change, as review.typo3.org answered it on 2026-08-26.
     *
     * It states no unresolved comment at all, and one of the seven carries the
     * flag: the pair `D-ANS-111` is about.
     */
    private const THREADS = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main",'
        . '"subject":"[BUGFIX] Reset page renderer when performing subrequest",'
        . '"status":"MERGED","_number":91127,"change_id":"Ibb426e12fe37d89471c4b7fa8cb11fade77ba5f3",'
        . '"current_revision_number":11,"current_revision":"119866d287afb6a06134d0fbe181762e78192fc3",'
        . '"total_comment_count":7,"unresolved_comment_count":0,"labels":{},"submit_records":[]}]';

    /** The change with its comments, and nothing else answering. */
    private static function threaded(string $url): string
    {
        return str_contains($url, '/comments') ? self::THREADED : self::THREADS;
    }

    /**
     * A comment says which thread it is in and what that thread stands at,
     * rather than leaving both to be worked out from the reply ids.
     *
     * Gerrit stores a thread's state in its last comment and counts the open
     * threads as `unresolved_comment_count`, so the flag on one comment is one
     * writer's and nothing more. On this change the two come apart: Oliver
     * Klee's question carries `true` and the thread it opened is settled, which
     * is why a tally of the flags said one where the review server says none —
     * `D-ANS-111`.
     */
    #[Decision('D-ANS-111')]
    #[Test]
    public function everyCommentSaysWhichThreadItIsInAndWhatThatThreadStandsAt(): void
    {
        $gerrit = new Gerrit(static fn(string $url): string => self::threaded($url));

        $change = $gerrit->change('91127')['changes'][0];
        $comments = $change['comments'];

        // The three that are one exchange, oldest first: the question, the
        // answer under it, and the acknowledgement under that.
        self::assertSame(
            ['Oliver Klee', 'Torben Hansen', 'Oliver Klee'],
            array_column(array_slice($comments, 0, 3), 'author'),
        );
        self::assertSame(
            ['aff6212c_6070833d', 'aff6212c_6070833d', 'aff6212c_6070833d'],
            array_column(array_slice($comments, 0, 3), 'thread'),
        );
        self::assertCount(5, array_unique(array_column($comments, 'thread')));
        // The head is flagged and its thread is settled, which is the reading
        // the answer no longer leaves to the caller.
        self::assertTrue($comments[0]['unresolved']);
        self::assertFalse($comments[0]['threadUnresolved']);
        self::assertSame(
            $change['unresolvedCommentCount'],
            count(array_unique(array_column(
                array_filter($comments, static fn(array $comment): bool => $comment['threadUnresolved']),
                'thread',
            ))),
        );
    }

    /**
     * A reply whose parent is not in the answer opens a thread of its own.
     *
     * That is what a comment answering a draft looks like from here — the draft
     * is the writer's alone and this server reads Gerrit without credentials
     * (`R-ANS-027`), so the reply arrives naming an id nothing here carries.
     * Putting it in no thread would drop it from the listing.
     */
    #[Decision('D-ANS-111')]
    #[Test]
    public function aReplyToACommentNobodyCanSeeStandsAsAThreadOfItsOwn(): void
    {
        $orphaned = str_replace('"in_reply_to":"3d14ec42_498fd7ea"', '"in_reply_to":"9e14ec42_notvisible"', self::THREADED);
        $gerrit = new Gerrit(static fn(string $url): string => str_contains($url, '/comments')
            ? $orphaned
            : self::THREADS);

        $comments = $gerrit->change('91127')['changes'][0]['comments'];

        $ack = $comments[2];
        self::assertSame('Ack.', $ack['message']);
        self::assertSame('15369d34_7841ae25', $ack['thread']);
        self::assertCount(6, array_unique(array_column($comments, 'thread')));
    }

    /**
     * The text half lists one thread at a time and says what each stands at,
     * which is the ranking the reporting session made for itself out of the
     * flags and the reply ids — `D-ANS-111`.
     *
     * What each comment says is under the line naming who wrote it, and that is
     * the half the listing exists for: `feedback/2026-08-25-105203` rewrote its
     * review note and its `Releases:` recommendation off a backporting thread it
     * would not have read otherwise (`D-ANS-079`). A heading and a state alone
     * would say a thread is there and leave the reader to fetch it.
     */
    #[Decision('D-ANS-079')]
    #[Decision('D-ANS-111')]
    #[Test]
    public function theTextHalfListsOneThreadAtATimeAndSaysWhatEachStandsAt(): void
    {
        $gerrit = new Gerrit(static fn(string $url): string => self::threaded($url));

        $change = $gerrit->change('91127')['changes'][0];
        $said = implode("\n", GerritLookup::comments($change, true));

        self::assertStringContainsString('### Comments (7 comments in 5 threads, none unresolved)', $said);
        self::assertStringContainsString("#### Resolved · Oliver Klee\n\n- Oliver Klee · patch set 4", $said);
        self::assertStringContainsString('#### Resolved · Benjamin Franzke · /COMMIT_MSG:27', $said);
        // What was written, under the line that says who wrote it and on which
        // patch set. The question and the answer both, since a thread read
        // without the reply is the question standing open.
        self::assertStringContainsString(
            "- Oliver Klee · patch set 4\n  Is there any way this can be covered with a functional test?",
            $said,
        );
        self::assertStringContainsString(
            "- Torben Hansen · patch set 4\n  For functional test I would say no. Acceptance test would be an option.",
            $said,
        );
        // The state is the thread's and stands over it once. A comment line
        // carrying one is what said seven states where the change has five.
        self::assertStringNotContainsString('· resolved', $said);
        // The order inside a thread is who answered whom, so the reply is not
        // announced as answering the line above it either.
        self::assertStringNotContainsString('answering', $said);

        $open = new Gerrit(static fn(string $url): string => str_contains($url, '/comments')
            ? self::COMMENTS
            : self::COMMENTED);
        $standing = implode("\n", GerritLookup::comments($open->change('95179')['changes'][0], true));

        self::assertStringContainsString('### Comments (3 comments in 2 threads, 1 unresolved)', $standing);
        self::assertStringContainsString('#### Unresolved · Mathias Brodala', $standing);
        self::assertStringContainsString('#### Resolved · Georg Ringer', $standing);
    }

    /**
     * None of it reaches the issue search. That question is whether a patch
     * exists, it is answered with up to 25 changes, and the comments are a call
     * each — so a hit says the review was not read rather than that there is
     * none of it.
     *
     * The issues its message names are left out for the other reason: the
     * caller of a search is holding the issue number already (`D-ANS-098`).
     */
    #[Decision('D-ANS-098')]
    #[Test]
    public function anIssueSearchAsksForNoneOfTheReview(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::BOTH;
        });

        $change = $gerrit->changesForIssue('88556')['changes'][0];

        self::assertCount(1, $asked);
        self::assertStringNotContainsString('o=DETAILED_LABELS', $asked[0]);
        self::assertNull($change['labels']);
        self::assertNull($change['comments']);
        self::assertNull($change['messages']);
        self::assertNull($change['issues']);
    }

    /**
     * The log is 57.9 KB against 14.3 KB on one change, so it is asked for —
     * and the half a service user wrote is what "people" drops.
     *
     * The rule is the account rather than the message tag. Gerrit tags the
     * messages it generates on an upload, but those are the uploader's own and
     * carry the copy condition a rebase dropped a vote by, which is the one
     * thing the log is worth fetching for.
     */
    #[Decision('D-ANS-079')]
    #[Decision('D-ANS-121')]
    #[Test]
    public function theReviewLogIsAskedForAndTheServiceUsersHalfIsSeparated(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::reviewed($url);
        });

        // The option is on every query a change read by name makes, since one
        // fact in the log is about the change rather than about its review
        // (`D-ANS-121`). What `messages` decides is what the caller is handed,
        // and the default hands over none of the 57.9 KB.
        self::assertNull($gerrit->change('93319')['changes'][0]['messages']);
        self::assertStringContainsString('o=MESSAGES', $asked[0]);

        $whole = $gerrit->change('93319', 1, 'all')['changes'][0];

        self::assertCount(3, $whole['messages']);
        self::assertSame(1, $whole['botMessageCount']);
        self::assertStringContainsString('copy condition', $whole['messages'][2]['message']);
        self::assertSame(21, $whole['messages'][2]['patchSet']);

        $written = $gerrit->change('93319', 1, 'people')['changes'][0];

        self::assertSame(['Benni Mack', 'Benni Mack'], array_column($written['messages'], 'author'));
        // Answered whichever way it was asked, so a zero here is the tag gone
        // rather than a change no bot has been near.
        self::assertSame(1, $written['botMessageCount']);
    }

    /**
     * Change 93689 as review.typo3.org answered it on 2026-08-27, trimmed to
     * what is read.
     *
     * Of the 867 open core changes read that day, the one carrying a conflict
     * report on the patch set that is current: a 13.4 backport cherry-picked
     * from 93657, standing at patch set 1 since April and never repaired
     * (`D-ANS-121`).
     */
    private const CONFLICTED = '{"project":"Packages/TYPO3.CMS","branch":"13.4",'
        . '"subject":"[BUGFIX] Introduce PackageSetup to unify extension setup","status":"NEW","_number":93689,'
        . '"change_id":"Ib7e39136736a77487ef20a6889dd6ee85909d7e0","current_revision_number":1,'
        . '"current_revision":"83442f7d568800f0cff71a24d28edb8e17755c38","updated":"2026-04-16 11:51:23.000000000",'
        . '"cherry_pick_of_change":93657,"cherry_pick_of_patch_set":9,'
        . '"total_comment_count":1,"unresolved_comment_count":0,"labels":{},"submit_records":[]';

    /** What `o=MESSAGES` adds to it, and the whole of where the fact is written. */
    private const REPORT = ',"messages":[{"author":{"_account_id":7298,"name":"Helmut Hummel"},'
        . '"tag":"autogenerated:gerrit:newWipPatchSet","date":"2026-04-16 11:33:28.000000000","_revision_number":1,'
        . '"message":"Patch Set 1: Cherry Picked from branch main.\n\nThe following files contain Git conflicts:\n'
        . '* Build/phpstan/phpstan-baseline.neon\n'
        . '* typo3/sysext/core/Classes/Package/PackageActivationService.php"}]';

    /** The paths that report names, which is what the field comes to. */
    private const CONFLICTS = [
        'Build/phpstan/phpstan-baseline.neon',
        'typo3/sysext/core/Classes/Package/PackageActivationService.php',
    ];

    /** That change, its one comment, and the log where the query asked for one. */
    private static function conflicted(string $url): string
    {
        if (str_contains($url, '/comments')) {
            return ")]}'\n" . '{"/PATCHSET_LEVEL":[{"author":{"_account_id":7298,"name":"Helmut Hummel"},'
                . '"unresolved":false,"patch_set":1,"id":"eaf49b9c_700670e4",'
                . '"updated":"2026-04-16 11:34:14.000000000","message":"Superseded by another change"}]}';
        }
        if (str_contains($url, '/related')) {
            return ")]}'\n" . '{"changes":[]}';
        }

        return ")]}'\n[" . self::CONFLICTED . (str_contains($url, 'o=MESSAGES') ? self::REPORT : '') . '}]';
    }

    /**
     * Whether the current patch set carries git conflict markers is answered
     * under the default, which is the answer that hid it — `D-ANS-121`.
     *
     * The report is the only place Gerrit writes the fact: the change payload
     * carries no field for it and no revision does either. So the log is asked
     * for on every change read by name, and the sentence is what is matched —
     * a rebase through the web UI writes the same list under a first line of
     * its own, and that is 13 of the 39 reports the core project carries.
     */
    #[Decision('D-ANS-121')]
    #[Test]
    public function aChangeSaysWhichFilesItsCurrentPatchSetCarriesConflictMarkersIn(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::conflicted($url);
        });

        $change = $gerrit->change('93689')['changes'][0];

        self::assertStringContainsString('o=MESSAGES', $asked[0]);
        self::assertSame(self::CONFLICTS, $change['conflicts']);
        // The log itself is still what the caller asks for, and this call asked
        // for none of it.
        self::assertNull($change['messages']);

        $rebased = new Gerrit(static fn(string $url): string => str_replace(
            'Patch Set 1: Cherry Picked from branch main.',
            'Patch Set 1: Patch Set 1 was rebased',
            self::conflicted($url),
        ));

        self::assertSame(self::CONFLICTS, $rebased->change('93689')['changes'][0]['conflicts']);
    }

    /**
     * Change 95412 as review.typo3.org answered it on 2026-08-27: the backport
     * `feedback/2026-08-25-110659` reviewed, merged at patch set 3 with the
     * conflict its patch set 1 landed with repaired.
     */
    private const REPAIRED = '{"project":"Packages/TYPO3.CMS","branch":"14.3",'
        . '"subject":"[BUGFIX] Fix null access when marking invalid stage items","status":"MERGED","_number":95412,'
        . '"change_id":"I0b2e645902dd392c3cc1d05ec83821ac154affa4","current_revision_number":3,'
        . '"current_revision":"06dc629259959ee6fe73d602a769c562250092a5","updated":"2026-08-25 11:12:34.000000000",'
        . '"cherry_pick_of_change":95392,"cherry_pick_of_patch_set":3,'
        . '"total_comment_count":0,"unresolved_comment_count":0,"labels":{},"submit_records":[]';

    /** The report on the patch set two uploads have since replaced. */
    private const REPAIR = ',"messages":[{"author":{"_account_id":21249,"name":"Benjamin Kott"},'
        . '"tag":"autogenerated:gerrit:newWipPatchSet","date":"2026-08-25 10:56:44.000000000","_revision_number":1,'
        . '"message":"Patch Set 1: Cherry Picked from branch main.\n\nThe following files contain Git conflicts:\n'
        . '* typo3/sysext/form/Resources/Public/JavaScript/backend/form-editor/view-model.js"}]';

    /** That change, with the log where the query asked for one. */
    private static function repaired(string $url): string
    {
        if (str_contains($url, '/related')) {
            return ")]}'\n" . '{"changes":[]}';
        }

        return ")]}'\n[" . self::REPAIRED . (str_contains($url, 'o=MESSAGES') ? self::REPAIR : '') . '}]';
    }

    /**
     * A report is held against the patch set it was written about, so a conflict
     * somebody has already replaced is history — `D-ANS-121`.
     *
     * That is 7 of the 8 open core changes carrying such a message, and it is
     * what the reported shape would have fired on: the field is about the change
     * as it stands rather than about anything that ever happened to it.
     */
    #[Decision('D-ANS-121')]
    #[Test]
    public function aReportOnAPatchSetSomebodyHasReplacedIsNotThisPatchSet(): void
    {
        $gerrit = new Gerrit(static fn(string $url): string => self::repaired($url));

        $change = $gerrit->change('95412')['changes'][0];

        self::assertSame(3, $change['patchSet']);
        self::assertSame([], $change['conflicts']);

        // The message is in the log all the same, which is where a reader asks
        // what the change has been through.
        $whole = $gerrit->change('95412', 1, 'all')['changes'][0];

        self::assertSame(1, $whole['messages'][0]['patchSet']);
        self::assertStringContainsString('contain Git conflicts', $whole['messages'][0]['message']);
    }

    /**
     * A search asks for no log and says nothing about conflicts, which is the
     * silence it keeps everywhere else — `D-ANS-121`.
     *
     * It answers up to 25 changes and the enumeration reads up to 2000, and the
     * option cost a median of 6.3 KB a change when it was measured. What a
     * caller does with a hit is read it by name, which is where the fact is.
     */
    #[Decision('D-ANS-121')]
    #[Test]
    public function aSearchAsksForNoLogAndSaysNothingAboutConflicts(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::conflicted($url);
        });

        $change = $gerrit->changesMatching('package setup', '', true)['changes'][0];

        self::assertStringNotContainsString('o=MESSAGES', implode(' ', $asked));
        self::assertNull($change['conflicts']);
    }

    /**
     * Where a change was cherry-picked from is read off the two fields the
     * payload carries unasked — `D-ANS-121`.
     *
     * It is provenance and not a warning: 133 of 400 recent merged core changes
     * are cherry-picks and 17 of those ever conflicted, so a reader acting on
     * this field passes over ordinary backports.
     */
    #[Decision('D-ANS-103')]
    #[Decision('D-ANS-121')]
    #[Test]
    public function aChangeNamesTheChangeItWasCherryPickedFrom(): void
    {
        $picked = (new Gerrit(static fn(string $url): string => self::conflicted($url)))
            ->change('93689')['changes'][0];

        self::assertSame([
            'change' => 93657,
            'patchSet' => 9,
            'url' => 'https://review.typo3.org/c/Packages/TYPO3.CMS/+/93657',
        ], $picked['cherryPickOf']);

        // Null on a change somebody pushed, which the payload says by carrying
        // neither field. Its conflicts are null for the other reason: this
        // query asked for no log.
        $pushed = (new Gerrit(static fn(string $url): string => self::RESPONSE))->change('95040')['changes'][0];

        self::assertNull($pushed['cherryPickOf']);
        self::assertNull($pushed['conflicts']);
    }

    /**
     * The text half says which of the two a reader is looking at: a patch set
     * that is broken rather than one nobody has reviewed — `D-ANS-121`.
     *
     * The reporting session read status NEW, patch set 1, no comments and empty
     * vote arrays, and concluded that nobody had looked at the change yet. Every
     * one of those readings was right and the change was broken, so the paths
     * alone would leave the same conclusion standing.
     */
    #[Decision('D-ANS-121')]
    #[Test]
    public function theConflictLineSaysTheChangeIsBrokenRatherThanUnreviewed(): void
    {
        $change = (new Gerrit(static fn(string $url): string => self::conflicted($url)))
            ->change('93689')['changes'][0];

        $said = implode("\n", GerritLookup::conflicts($change, true));

        self::assertStringContainsString(
            'Git conflicts in this patch set: Build/phpstan/phpstan-baseline.neon, '
                . 'typo3/sysext/core/Classes/Package/PackageActivationService.php.',
            $said,
        );
        self::assertStringContainsString('broken rather than merely unreviewed', $said);
        self::assertSame(
            ['Cherry-picked from change 93657 patch set 9 · https://review.typo3.org/c/Packages/TYPO3.CMS/+/93657'],
            GerritLookup::cherryPick($change),
        );
        // Nothing for the patch set almost every change has, and nothing for a
        // search that asked for no log.
        self::assertSame([], GerritLookup::conflicts(['conflicts' => []], true));
        self::assertSame([], GerritLookup::conflicts(['conflicts' => null], false));
        self::assertSame([], GerritLookup::cherryPick(['cherryPickOf' => null]));
        self::assertStringContainsString(
            'could not be read',
            implode("\n", GerritLookup::conflicts(['conflicts' => null], true)),
        );
    }

    /**
     * Change 76606 as review.typo3.org answered it on 2026-08-25: abandoned two
     * years after the review that stopped it, and sent with an empty `labels`
     * object although `o=DETAILED_LABELS` asked for one.
     */
    private const STOPPED = '{"project":"Packages/TYPO3.CMS","branch":"main",'
        . '"subject":"[WIP][BUGFIX] addQueryString.exclude overrides config.linkVars",'
        . '"status":"ABANDONED","updated":"2024-12-12 09:02:43.000000000","_number":76606,'
        . '"change_id":"Icf817319827f1b0fcf6a2d8932c5cb910fe55eb6","current_revision_number":2,'
        . '"current_revision":"fb4d853efa009969a1f2665a7ad9ec2e0f4ab92d","total_comment_count":1,'
        . '"revisions":{"fb4d853efa009969a1f2665a7ad9ec2e0f4ab92d":{"commit":{"message":'
        . '"[WIP][BUGFIX] addQueryString.exclude overrides config.linkVars\n\nResolves: #35069\n'
        . 'Change-Id: Icf817319827f1b0fcf6a2d8932c5cb910fe55eb6\n"}}}';

    /** What the review options add to it, the empty label state included. */
    private const STOPPING = ',"labels":{},"submit_records":[],"messages":['
        . '{"author":{"name":"Benni Mack"},"date":"2022-11-15 12:23:42.000000000","_revision_number":1,'
        . '"message":"Uploaded patch set 1."},'
        . '{"author":{"name":"core-ci","tags":["SERVICE_USER"]},"date":"2022-11-15 12:31:16.000000000",'
        . '"_revision_number":1,"message":"Patch Set 1: Verified+1"},'
        . '{"author":{"name":"Benni Mack"},"date":"2022-11-15 13:02:13.000000000","_revision_number":1,'
        . '"message":"Patch Set 1: Code-Review-1\n\n(1 comment)"},'
        . '{"author":{"name":"Benni Mack"},"date":"2024-12-12 09:02:43.000000000","_revision_number":2,'
        . '"message":"Abandoned"}]';

    /** The one comment on it, filed against the change rather than a line. */
    private const REJECTION = ")]}'\n" . '{"/PATCHSET_LEVEL":[{"id":"feb17490_1ccc116f",'
        . '"author":{"_account_id":22037,"name":"Benni Mack"},"patch_set":1,"unresolved":false,'
        . '"updated":"2022-11-15 13:02:13.000000000","message":"wrong approach :("}]}';

    /** The change by either way in, its comment, and a chain it is not in. */
    private static function stopped(string $url): string
    {
        if (str_contains($url, '/comments')) {
            return self::REJECTION;
        }
        if (str_contains($url, '/related')) {
            return ")]}'\n" . '{"changes":[]}';
        }

        return ")]}'\n[" . self::STOPPED . (str_contains($url, 'o=MESSAGES') ? self::STOPPING : '') . '}]';
    }

    /**
     * An abandoned change is answered whole, by the number and by the issue its
     * commit message names.
     *
     * This is the one state a narrowing would quietly take away, and it is the
     * state the answer is worth most in: what a rejected change carries is the
     * argument against the approach, which exists nowhere else.
     * `feedback/2026-08-24-173151` and `feedback/2026-08-24-183447` each read
     * one and each dropped a patch it would otherwise have written — the first
     * on this very change, the second on 85224. `changesMatching()` takes a
     * `status:open` narrowing, so the shape that would drop them is already in
     * this class.
     *
     * The vote is the other half. `o=DETAILED_LABELS` was asked and the review
     * server sent no label state at all, so the Code-Review-1 survives in the
     * message log alone — which `messages` is opt-in for.
     */
    #[Decision('D-ANS-079')]
    #[Test]
    public function anAbandonedChangeIsAnsweredWholeByBothWaysIn(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::stopped($url);
        });

        $change = $gerrit->change('76606', 1, 'people')['changes'][0];

        self::assertSame('ABANDONED', $change['status']);
        self::assertSame('wrong approach :(', $change['comments'][0]['message']);
        self::assertSame('/PATCHSET_LEVEL', $change['comments'][0]['file']);
        // The review that stopped it is a message and not a vote: the label
        // state came back empty on a change nobody will vote on again.
        self::assertSame([], $change['labels']);
        self::assertStringContainsString(
            'Patch Set 1: Code-Review-1',
            implode("\n", array_column($change['messages'], 'message')),
        );
        self::assertStringNotContainsString('status:', implode(' ', $asked));

        // The batched query a backlog row is filled from reaches it too, which
        // is what makes the jump from an issue to the argument possible at all.
        $found = $gerrit->changesForIssues([35069]);

        self::assertSame([76606], array_column($found[35069], 'number'));
        self::assertSame('ABANDONED', $found[35069][0]['status']);
    }

    /**
     * Nothing public is not nothing. A change pushed as private answers this
     * search with an empty list, so the caller is told what was searched rather
     * than that no patch exists — the distinction the tool's own text carries.
     */
    #[Test]
    public function aSearchThatMatchedNothingIsEmpty(): void
    {
        $gerrit = new Gerrit(static fn(): string => ")]}'\n[]");

        $answer = $gerrit->changesForIssue('105403');

        self::assertSame('empty', $answer['status']);
        self::assertSame([], $answer['changes']);
        self::assertNull($answer['cause']);
    }

    /**
     * `R-ANS-027`. An empty answer for a named change is two things at once.
     *
     * `feedback/2026-08-07-132416` looked up the Change-Id its own commit
     * carried, got `{"status":"empty","changes":[]}`, and made "this was never
     * pushed" the first finding of a review — recommending its author
     * coordinate with a contributor who was themselves. The change was private,
     * and this server reads Gerrit without credentials, so nothing in that
     * answer could have said otherwise.
     */
    #[Decision('D-ANS-062')]
    #[Requirement('R-ANS-027')]
    #[Test]
    public function anEmptyAnswerForANamedChangeSaysWhatItCannotSeparate(): void
    {
        $said = GerritLookup::indistinguishable('empty', 'change');

        self::assertNotNull($said);
        self::assertStringContainsString('without credentials', $said);
        self::assertStringContainsString('private', $said);

        // A search over commit messages is a claim about a query, not about a
        // record the caller is holding, and it has its own sentence.
        self::assertNull(GerritLookup::indistinguishable('empty', 'issue'));
        // Nothing to hedge where something came back.
        self::assertNull(GerritLookup::indistinguishable('answered', 'change'));
        self::assertNull(GerritLookup::indistinguishable('unavailable', 'change'));
    }

    /**
     * Where the tracker settled it, the answer stops hedging.
     *
     * Gerrit Code Review posts a note on the issue for every patch set it
     * receives, so a review URL there and nothing from the review server is not
     * two possibilities — the change exists and this reader may not see it.
     * That is the report's own last idea, and only the issue side of it is
     * built: searching the tracker for a change number costs 2.5 seconds and
     * answers two issues, one unrelated, and searching for a Change-Id answers
     * nothing at all.
     */
    #[Requirement('R-ANS-027')]
    #[Test]
    public function aReviewNoteOnTheIssueTurnsTheHedgeIntoAnAnswer(): void
    {
        $said = GerritLookup::indistinguishable('empty', 'issue', [
            'author' => 'Gerrit Code Review',
            'url' => 'https://review.typo3.org/c/Packages/TYPO3.CMS/+/95162',
        ]);

        self::assertNotNull($said);
        self::assertStringContainsString('does exist', $said);
        self::assertStringContainsString('95162', $said);
        // And it is no longer the sentence that says nothing here separates the
        // two, because something did.
        self::assertStringNotContainsString('nothing here separates them', $said);

        // Still nothing to say where the review server answered.
        self::assertNull(GerritLookup::indistinguishable('answered', 'issue', [
            'author' => 'Gerrit Code Review',
            'url' => 'https://review.typo3.org/c/Packages/TYPO3.CMS/+/95162',
        ]));
    }

    /**
     * `D-SKL-038`. A caller holding one change is about to review it or to
     * fetch it, and both of those are a workflow this server publishes.
     *
     * `feedback/2026-08-12-092545` is the session that shows what the answer
     * was short of: no skill opened, `typo3_project_describe`'s schema was
     * loaded and the tool never called, and this answer — the one call it did
     * make — handed it the ref it then fetched the patch set with by hand.
     *
     * `feedback/2026-08-24-122413` is the one that shows the names alone were
     * short too: it read this tail on change 95179, opened neither skill, and
     * both reviewed and rebased by hand. So the order is asserted beside them.
     */
    #[Decision('D-SKL-038')]
    #[Test]
    public function aNamedChangeIsHandedTheWorkflowsThatOwnIt(): void
    {
        $said = GerritLookup::workflow('answered', '95169');

        self::assertNotNull($said);
        self::assertStringContainsString('typo3-core-patch-review', $said);
        self::assertStringContainsString('typo3-core-patch-checkout', $said);
        // The call the order opens on, which is the act the session skipped.
        self::assertStringContainsString('typo3_project_describe', $said);
        // And not the tool two sessions finished a task without calling:
        // naming one nobody invokes is what `D-ANS-061` ruled out.
        self::assertStringNotContainsString('typo3_server_scope', $said);

        // The order itself, which is what a session that opens neither skill
        // has to act on. Each of the three is a step the reporting session
        // took by hand and took differently: it judged the diff without
        // establishing the branch, it carried the patch onto a branch of its
        // own naming, and it rebased onto current code as a matter of course.
        self::assertStringContainsString('the branch it targets', $said);
        self::assertStringContainsString('review/<change number>', $said);
        self::assertStringContainsString('no longer applies is the finding', $said);
        // Where the review ends, which is the half of that request the two
        // named workflows do not own.
        self::assertStringContainsString('typo3-core-patch-development', $said);

        // The issue form takes none of it. "Has somebody already fixed this"
        // precedes triage, patch development and review alike, so there is no
        // one workflow to name.
        self::assertNull(GerritLookup::workflow('answered', ''));
        // Nothing to hand over where nothing came back.
        self::assertNull(GerritLookup::workflow('empty', '95169'));
        self::assertNull(GerritLookup::workflow('unavailable', '95169'));
    }

    /**
     * Two of the 22 changes `status:open file:^typo3/sysext/impexp/…` answered
     * on 2026-08-24, in the fields `o=CURRENT_REVISION` alone brings back. The
     * first is the reporting session's own patch, which is the surface this
     * search is for.
     */
    private const MATCHED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Import pages in their export order",'
        . '"status":"NEW","updated":"2026-08-24 09:41:02.000000000","_number":95393,'
        . '"change_id":"I55fced4b84048a812adc7dca6d7f66261ef147b5","current_revision_number":1,'
        . '"current_revision":"7c1d9a0b2e4f6a8c0e2d4b6f8a0c2e4d6b8f0a2c"},'
        . '{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[TASK] Clean up ext:impexp worker classes",'
        . '"status":"MERGED","updated":"2026-08-19 14:02:55.000000000","_number":89000,'
        . '"change_id":"I96efca966eeb72cb3f41c24917cb7c08c204d37a","current_revision_number":4,'
        . '"current_revision":"e02be2c6672f09e8ca570924e13aff37efc89ff2"}]';

    /**
     * The words, the path and the narrowing are composed into one query here,
     * and the answer carries it so the caller can ask it again — `D-ANS-100`.
     *
     * Every part of the form was measured against review.typo3.org on
     * 2026-08-24. A word is quoted because Gerrit's parser reads punctuation
     * before the index does; the path is matched whole, so the path itself and
     * everything under it are two alternatives, and the second carries no `^`
     * because that character is the marker for a regex rather than part of one.
     */
    #[Decision('D-ANS-100')]
    #[Test]
    public function theWordsAndThePathBecomeOneQueryTheCallerCanRerun(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::MATCHED;
        });

        $answer = $gerrit->changesMatching('impexp translation', 'typo3/sysext/impexp', true, 25);

        self::assertSame(
            'status:open "impexp" "translation" file:"^typo3/sysext/impexp/.*|typo3/sysext/impexp"',
            $answer['query'],
        );
        self::assertSame([95393, 89000], array_column($answer['changes'], 'number'));
        self::assertStringContainsString('n=25', $asked[0]);
        // Every state without it, which is what "has anybody ever tried this"
        // needs: an abandoned or merged attempt is the answer to that.
        self::assertStringNotContainsString(
            'status:open',
            $gerrit->changesMatching('impexp translation', '', false, 25)['query'],
        );
    }

    /**
     * A path is matched as itself rather than as a pattern — `D-ANS-100`.
     *
     * An unescaped `.` matches any character, so a path naming `Import.php`
     * would answer changes touching `ImportXphp` as well.
     */
    #[Decision('D-ANS-100')]
    #[Test]
    public function aPathIsMatchedAsItselfRatherThanAsAPattern(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::MATCHED);

        self::assertSame(
            'file:"^typo3/sysext/impexp/Classes/Import\\\\.php/.*|typo3/sysext/impexp/Classes/Import\\\\.php"',
            $gerrit->changesMatching('', 'typo3/sysext/impexp/Classes/Import.php')['query'],
        );
        // A path the caller wrote with slashes around it names the same place.
        self::assertSame(
            'file:"^typo3/sysext/impexp/.*|typo3/sysext/impexp"',
            $gerrit->changesMatching('', '/typo3/sysext/impexp/')['query'],
        );
    }

    /**
     * A word is a value rather than syntax — `D-ANS-100`.
     *
     * `why does impexp fail?` answers `400 no viable alternative at character
     * '?'` bare, and quoted it answers nothing: a search that matched, rather
     * than a call that failed.
     */
    #[Decision('D-ANS-100')]
    #[Test]
    public function aWordIsAValueRatherThanSyntax(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::MATCHED);

        self::assertSame(
            '"why" "does" "impexp" "fail?"',
            $gerrit->changesMatching('why  does impexp fail?', '')['query'],
        );
    }

    /**
     * The boundary is the issue search's, and the commit message is outside it:
     * the 22 open changes on `typo3/sysext/impexp` came back at 34.4 KB with
     * the current revision alone and at 54.2 KB with `o=CURRENT_COMMIT` beside
     * it, measured on 2026-08-24 — `D-ANS-100`.
     *
     * So a hit says the review was not read rather than that there is none of
     * it, and reading one by name is what answers that.
     */
    #[Decision('D-ANS-100')]
    #[Test]
    public function aSearchAsksForNothingBeyondThePatchSetEachHitStandsAt(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::MATCHED;
        });

        $change = $gerrit->changesMatching('impexp', '')['changes'][0];

        self::assertCount(1, $asked);
        self::assertStringContainsString('o=CURRENT_REVISION', $asked[0]);
        self::assertStringNotContainsString('o=CURRENT_COMMIT', $asked[0]);
        self::assertStringNotContainsString('o=DETAILED_LABELS', $asked[0]);
        self::assertSame(1, $change['patchSet']);
        self::assertSame('7c1d9a0b2e4f6a8c0e2d4b6f8a0c2e4d6b8f0a2c', $change['commit']);
        self::assertSame('refs/changes/93/95393/1', $change['fetch']['ref']);
        self::assertNull($change['labels']);
        self::assertNull($change['comments']);
        self::assertNull($change['chain']);
        self::assertNull($change['issues']);
        self::assertNull($change['message']);
        self::assertNull($change['files']);
    }

    /**
     * A search naming nothing to match is not a query for the whole server —
     * `D-ANS-100`.
     */
    #[Decision('D-ANS-100')]
    #[Test]
    public function aSearchWithNoWordsAndNoPathAsksTheServerNothing(): void
    {
        $asked = 0;
        $gerrit = new Gerrit(function () use (&$asked): string {
            ++$asked;

            return self::MATCHED;
        });

        $answer = $gerrit->changesMatching('  ', ' / ', true);

        self::assertSame(0, $asked);
        self::assertSame('empty', $answer['status']);
        self::assertSame('', $answer['query']);
    }

    /**
     * An empty search is not an absence, and what it fails to separate is
     * different for the two ways of asking it — `D-ANS-100`.
     *
     * The words have a trap of their own beside the anonymous read.
     * `feedback/2026-08-24-110833` searched for `flatInversePageTree`, got
     * nothing, and reported that nobody had ever attempted the fix — but the
     * match is against the commit message rather than the diff: change 89000
     * added `writePagesOrder`, and searching that name answers nothing while
     * the words of its own subject answer it. Measured on 2026-08-24.
     */
    #[Decision('D-ANS-100')]
    #[Test]
    public function anEmptySearchSaysWhatItCannotSeparate(): void
    {
        $words = GerritLookup::indistinguishable('empty', 'query');
        $path = GerritLookup::indistinguishable('empty', 'path');

        self::assertNotNull($words);
        self::assertStringContainsString('without credentials', $words);
        self::assertStringContainsString('rather than against the diff', $words);
        self::assertStringContainsString('89000', $words);

        self::assertNotNull($path);
        self::assertStringContainsString('without credentials', $path);
        self::assertStringContainsString('nothing here separates them', $path);
        // The diff caveat is about words the caller did not pass.
        self::assertStringNotContainsString('commit message', $path);

        // Nothing to hedge where something came back.
        self::assertNull(GerritLookup::indistinguishable('answered', 'query'));
        self::assertNull(GerritLookup::indistinguishable('unavailable', 'path'));
    }

    #[Test]
    public function aHostThatDoesNotAnswerIsSaid(): void
    {
        $gerrit = new Gerrit(static fn(): ?string => null);

        $answer = $gerrit->changesForIssue('110348');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('source-not-answering', $answer['cause']);
    }

    /**
     * A captive portal answers 200 with HTML. Skipping to the first `[` would
     * parse whatever followed it as a review, so anything that is not the API
     * is a failure with a name.
     */
    #[Test]
    public function somethingThatIsNotTheApiIsNotParsedAsOne(): void
    {
        $gerrit = new Gerrit(static fn(): string => '<!doctype html><title>Sign in</title>');

        $answer = $gerrit->changesForIssue('110348');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('source-not-parseable', $answer['cause']);
    }

    /**
     * `R-ANS-035`. The change answer names a branch and named nothing else, and
     * the session rewriting a `Releases:` trailer rebuilt the rest from
     * `git branch -r` against a remote 59 commits behind
     * (`feedback/2026-08-24-122348`).
     *
     * Held against `ReleaseLines` rather than against the branches it carries
     * today: the file is a calendar, and a test naming 14.3 would fail on the
     * day 13.4 leaves regular support rather than on the day this placement
     * breaks.
     */
    #[Requirement('R-ANS-035')]
    #[Decision('D-ANS-104')]
    #[Test]
    public function theBranchesThatTakeAPatchStandBesideTheOneAChangeTargets(): void
    {
        $answer = GerritLookup::releaseLines();
        $said = implode("\n", $answer['lines']);

        self::assertSame(
            ReleaseLines::releasable(),
            array_column($answer['record']['branches'], 'branch'),
        );
        foreach ($answer['record']['branches'] as $line) {
            self::assertStringContainsString($line['branch'], $said);
            self::assertSame(ReleaseLines::state($line['branch']), $line['state']);
            self::assertSame(ReleaseLines::maintainedUntil($line['branch']), $line['maintainedUntil']);
        }

        // The development line is the head of the list and has no such date,
        // which is the one entry where a null is the answer rather than a gap.
        self::assertSame(ReleaseLines::DEVELOPMENT, $answer['record']['branches'][0]['state']);
        self::assertNull($answer['record']['branches'][0]['maintainedUntil']);

        // Every window is in the text as well, so the two halves say the same:
        // a maintained line reads as an oversight or as a typo depending on when
        // its support ends, and only the date carries that.
        foreach ($answer['record']['branches'] as $line) {
            if ($line['maintainedUntil'] !== null) {
                self::assertStringContainsString($line['maintainedUntil'], $said);
            }
        }

        // Where the calendar came from, so a caller can read it again rather
        // than trust this one.
        self::assertStringContainsString(ReleaseLines::source(), $said);
        self::assertStringContainsString(ReleaseLines::readAt(), $said);
    }

    /**
     * `D-ANS-073`. The lines and their windows, never which of them this change
     * belongs on: that reading is the author's claim about severity, and the
     * answer hands over the tool that reads a trailer against them instead of
     * making it.
     */
    #[Requirement('R-ANS-035')]
    #[Decision('D-ANS-073')]
    #[Test]
    public function thePlacementNamesTheToolThatReadsATrailerAgainstThoseLines(): void
    {
        $said = implode("\n", GerritLookup::releaseLines()['lines']);

        self::assertStringContainsString('typo3_commit_message_guide', $said);
        self::assertStringContainsString('workflow="core"', $said);
        self::assertStringContainsString('Releases:', $said);
        // The claim is the author's, said in as many words.
        self::assertStringContainsString('the author\'s claim', $said);
    }

    /**
     * The list comes from a file this server ships rather than from the review
     * server, so it is answered on every path through the tool — an empty search
     * and an unreachable host included. Its schema says so, which is what a
     * client validating `structuredContent` holds it to.
     */
    #[Requirement('R-ANS-035')]
    #[Test]
    public function theListIsAnsweredWhateverTheReviewServerDid(): void
    {
        self::assertContains('releaseLines', GerritLookup::outputSchema()['required']);
        self::assertNotSame([], GerritLookup::releaseLines()['record']['branches']);
    }

    /**
     * Two rows of the open backlog as the review server sends them bare, in the
     * shape `project:Packages/TYPO3.CMS status:open` answered on 2026-08-25:
     * the six fields `D-ANS-107` is about, and no `labels` object, because
     * nothing asked for one.
     *
     * The younger change stands first, which is the order the review server
     * answers in — by last activity — and the opposite of both orders the
     * enumeration is asked for.
     */
    private const OPEN = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Make CGL suites work in git worktrees",'
        . '"status":"NEW","created":"2026-08-25 11:41:26.000000000","updated":"2026-08-25 11:48:01.000000000",'
        . '"mergeable":true,"insertions":39,"deletions":6,"total_comment_count":0,"unresolved_comment_count":0,'
        . '"_number":95413,"current_revision_number":1,"submit_records":[{"rule_name":"gerrit~DefaultSubmitRule",'
        . '"status":"NOT_READY","labels":[{"label":"Verified","status":"NEED"},{"label":"Code-Review","status":"OK"}]}]},'
        . '{"project":"Packages/TYPO3.CMS","branch":"13.4","subject":"[BUGFIX] Ensure invalid pages do not stop DataHandler",'
        . '"status":"NEW","created":"2025-08-13 09:12:00.000000000","updated":"2026-06-18 23:04:54.000000000",'
        . '"mergeable":false,"insertions":7,"deletions":1,"total_comment_count":5,"unresolved_comment_count":2,'
        . '"_number":90384,"current_revision_number":2,"submit_records":[{"rule_name":"gerrit~DefaultSubmitRule",'
        . '"status":"NOT_READY","labels":[{"label":"Verified","status":"NEED"},{"label":"Code-Review","status":"REJECT"}]}]}]';

    /**
     * The filters are arguments composed into one query here, never a Gerrit
     * query passed through — `D-ANS-107`, which is what `D-ANS-100` decided for
     * the words and the path.
     *
     * Every operator in it was measured against review.typo3.org on 2026-08-25,
     * anonymously and in one call each: `delta:<=60` cut the 855 open core
     * changes to 329, `before:2025-01-01` to 54, and the whole almost-ready
     * query below to 74.
     */
    #[Decision('D-ANS-107')]
    #[Test]
    public function everyBacklogFilterIsAnOperatorComposedHere(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::OPEN);

        $query = $gerrit->backlog(
            maxSize: 60,
            minCodeReview: 1,
            negativeVotes: false,
            mergeable: true,
            branch: '13.4',
            updatedBefore: '2025-01-01',
            owner: 'Benjamin Kott',
        )['query'];

        self::assertSame(
            'project:"Packages/TYPO3.CMS" status:open -is:wip delta:<=60 label:Code-Review>=1 -label:Code-Review<=-1 '
                . '-label:Verified<=-1 is:mergeable branch:"13.4" before:"2025-01-01" owner:"Benjamin Kott"',
            $query,
        );
    }

    /**
     * A draft is not offered for review, and it is half the backlog: 411 of the
     * 855 open core changes carried the flag on 2026-08-25, so an enumeration
     * that keeps them answers mostly somebody else's unfinished work.
     *
     * It is in the query rather than behind an argument, and the query is in the
     * answer, so nothing is narrowed that the caller cannot read — `D-ANS-107`.
     */
    #[Decision('D-ANS-107')]
    #[Test]
    public function theChangesTheirAuthorsMarkedUnfinishedAreOutOfEveryEnumeration(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::OPEN);

        self::assertStringContainsString('-is:wip', $gerrit->backlog()['query']);
        self::assertStringContainsString('-is:wip', $gerrit->backlog(order: 'stale', owner: 'nobody')['query']);
    }

    /**
     * A person is one query rather than two reads, which is where this differs
     * from the tracker: Gerrit's parser takes the alternation and Redmine ANDs
     * its filters, so `D-ANS-089` had to union two answers by hand.
     */
    #[Decision('D-ANS-107')]
    #[Test]
    public function aPersonOnEitherSideIsOneQuery(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::OPEN;
        });

        $answer = $gerrit->backlog(involving: 'Benjamin Kott');

        self::assertCount(1, $asked);
        self::assertStringContainsString(
            '(owner:"Benjamin Kott" OR reviewedby:"Benjamin Kott")',
            $answer['query'],
        );
    }

    /**
     * The ordering is this server's, over the set the filters matched.
     *
     * The review server sorts by last activity, indexes no created date and
     * states no total for a query, so oldest-first cannot be asked of it at all
     * — measured on 2026-08-25, where the whole open backlog was 855 changes,
     * two calls and 1.1 MB. `D-ANS-107`.
     */
    #[Decision('D-ANS-107')]
    #[Test]
    public function theOldestFirstOrderIsTheMatchedSetSortedHere(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::OPEN);

        $oldest = $gerrit->backlog(order: 'oldest');
        $stale = $gerrit->backlog(order: 'stale');

        // Pushed first, which is the reverse of what came back.
        self::assertSame([90384, 95413], array_column($oldest['changes'], 'number'));
        // Untouched longest, which on these two is the same order and is not on
        // every set: the second was pushed a year later and moved a month ago.
        self::assertSame([90384, 95413], array_column($stale['changes'], 'number'));
        self::assertSame(2, $oldest['read']);
        self::assertTrue($oldest['complete']);
    }

    /**
     * The whole matched set is read before it is ordered, and the answer says
     * how many rows that was and whether it is all of them.
     *
     * A page is 500 rows and the review server sets `_more_changes` on the last
     * of one it cut, which is the only thing that says there is more: it states
     * no total. A caller shown 25 of 855 that reads them as the backlog has
     * measured the limit — `D-ANS-107`.
     */
    #[Decision('D-ANS-107')]
    #[Test]
    public function theReadSaysHowManyItCoveredAndWhetherThatIsAllOfThem(): void
    {
        $pages = 0;
        $gerrit = new Gerrit(function (string $url) use (&$pages): string {
            ++$pages;
            self::assertStringContainsString('n=500', $url);
            if ($pages > 1) {
                self::assertStringContainsString('&S=' . ($pages - 1) * 500, $url);
            }

            // Every page says there is another, on its last row, which is
            // where the review server puts the flag and the only thing that
            // says the set goes on: it states no total.
            return str_replace('"_number":90384', '"_number":9038' . $pages . ',"_more_changes":true', self::OPEN);
        });

        $answer = $gerrit->backlog(limit: 1);

        self::assertSame(4, $pages);
        self::assertSame(8, $answer['read']);
        self::assertFalse($answer['complete']);
        self::assertCount(1, $answer['changes']);
    }

    /**
     * Six fields the review server sends on every row and this server dropped
     * until `D-ANS-107`: the size, whether it still merges, when it was pushed,
     * how many threads are unresolved, and what the submit rule makes of each
     * label.
     *
     * The row widens for every direction rather than for the enumeration alone,
     * because the reading is the same wherever the change came from.
     */
    #[Decision('D-ANS-107')]
    #[Test]
    public function everyRowCarriesTheSizeTheMergeAndTheAgeOfItsChange(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::OPEN);

        $change = $gerrit->backlog(order: 'stale')['changes'][0];

        self::assertSame(7, $change['insertions']);
        self::assertSame(1, $change['deletions']);
        self::assertFalse($change['mergeable']);
        self::assertSame('2025-08-13 09:12:00.000000000', $change['created']);
        self::assertSame(5, $change['commentCount']);
        self::assertSame(2, $change['unresolvedCommentCount']);
        // The same six on the way in that is not an enumeration.
        $matching = $gerrit->changesMatching('worktrees', '')['changes'][0];
        self::assertSame(39, $matching['insertions']);
        self::assertTrue($matching['mergeable']);
        self::assertSame('2026-08-25 11:41:26.000000000', $matching['created']);
    }

    /**
     * `submit_records` is on every row and the voters are not, so a search
     * answers what each label stands at with `votes` null — a list of zeros
     * there would read as nobody having voted, which is a different answer.
     *
     * The per-voter tallies stay out of a search: `o=DETAILED_LABELS` is 0.9 KB
     * a row, and a page of 500 is 666 KB bare against 1.14 MB with it, measured
     * on 2026-08-25 — `D-ANS-107`.
     */
    #[Decision('D-ANS-107')]
    #[Test]
    public function aSearchAnswersWhatALabelStandsAtWithoutTheVotersBehindIt(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::OPEN;
        });

        $labels = $gerrit->backlog(order: 'stale')['changes'][0]['labels'];

        self::assertStringNotContainsString('o=DETAILED_LABELS', $asked[0]);
        self::assertSame(['Verified', 'Code-Review'], array_column($labels, 'label'));
        self::assertSame(['NEED', 'REJECT'], array_column($labels, 'state'));
        self::assertSame([false, false], array_column($labels, 'satisfied'));
        self::assertSame([null, null], array_column($labels, 'votes'));
    }

    /**
     * A change nobody may submit and a change nobody has voted on are the pair a
     * triage acts on, and "not satisfied" was what this said for both.
     *
     * `REJECT` is a vote blocking the change and `NEED` is a rule the votes have
     * not met, so the worse of them is what the label stands at — `D-ANS-107`.
     */
    #[Decision('D-ANS-107')]
    #[Test]
    public function aBlockingVoteIsWhatALabelStandsAtWhereTheRulesDisagree(): void
    {
        $gerrit = new Gerrit(static fn(): string => str_replace(
            '{"label":"Code-Review","status":"REJECT"}',
            '{"label":"Code-Review","status":"NEED"},{"label":"Code-Review","status":"REJECT"},'
                . '{"label":"Code-Review","status":"MAY"}',
            self::OPEN,
        ));

        $labels = $gerrit->backlog(order: 'stale')['changes'][0]['labels'];
        $said = array_map(
            static fn(array $label): string => GerritLookup::vote($label),
            $labels,
        );

        self::assertSame(['NEED', 'REJECT'], array_column($labels, 'state'));
        self::assertSame(['Verified: needs a vote', 'Code-Review: a vote is blocking it'], $said);
    }

    /**
     * What a page of the backlog is a page of, said before the rows.
     *
     * The size of the set leads, because a page read as the backlog is a triage
     * that believes it has seen it. And age alone is the wrong shortlist: of the
     * five oldest open core changes measured on 2026-08-25, three were over 250
     * lines and three no longer merged — `D-ANS-107`.
     */
    #[Decision('D-ANS-107')]
    #[Test]
    public function aPageOfTheBacklogSaysHowMuchOfItThatIs(): void
    {
        $page = implode("\n", GerritLookup::page(['order' => 'oldest', 'read' => 855, 'complete' => true], 25));
        $whole = implode("\n", GerritLookup::page(['order' => 'stale', 'read' => 12, 'complete' => true], 12));
        $cut = implode("\n", GerritLookup::page(['order' => 'oldest', 'read' => 2000, 'complete' => false], 25));

        self::assertStringContainsString('25 of 855 open core changes, oldest pushed first.', $page);
        self::assertStringContainsString('a page and not the set', $page);
        self::assertStringContainsString('never a finding', $page);
        self::assertStringContainsString('typo3-core-patch-review', $page);

        self::assertStringContainsString('12 of 12 open core changes, longest untouched first.', $whole);
        self::assertStringContainsString('the whole set on these filters', $whole);

        self::assertStringContainsString('The read stopped at 2000 matches', $cut);
        self::assertStringContainsString('one end of the set', $cut);
    }

    /**
     * The three readings a review candidate is picked by, on the line a page is
     * scanned by. Nothing is printed for a field the review server stated
     * nothing for, because a size of zero and an unstated size are different
     * claims — `D-ANS-107`.
     */
    #[Decision('D-ANS-107')]
    #[Test]
    public function theLineAPageIsScannedBySaysSizeMergeAndAge(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::OPEN);
        $changes = $gerrit->backlog(order: 'oldest')['changes'];

        self::assertSame(
            '+7 -1 · no longer merges · 2 unresolved threads of 5 comments · pushed 2025-08-13',
            GerritLookup::standing($changes[0]),
        );
        self::assertSame('+39 -6 · merges · pushed 2026-08-25', GerritLookup::standing($changes[1]));

        // A merged change carries no mergeability and an unstated size is not a
        // zero, so neither is claimed.
        self::assertSame('', GerritLookup::standing([
            'insertions' => null,
            'deletions' => null,
            'mergeable' => null,
            'commentCount' => 0,
            'unresolvedCommentCount' => 0,
            'created' => '',
        ]));
    }

    /**
     * The enumeration's own trap is the person filter. The review server answers
     * a name it cannot place with an empty list and no error — measured on
     * 2026-08-25, where `owner:zzzznotauser` came back HTTP 200 with `[]` — so
     * "nobody by that name" arrives as "this person has nothing open".
     */
    #[Requirement('R-ANS-027')]
    #[Decision('D-ANS-107')]
    #[Test]
    public function anEmptyBacklogSaysWhatItCannotSeparate(): void
    {
        $said = GerritLookup::indistinguishable('empty', 'backlog');

        self::assertNotNull($said);
        self::assertStringContainsString('without credentials', $said);
        self::assertStringContainsString('cannot place', $said);
        self::assertStringContainsString('owner, reviewedBy or involving', $said);

        self::assertNull(GerritLookup::indistinguishable('answered', 'backlog'));
    }

    /**
     * The enumeration is its own argument and the filters narrow it.
     *
     * `open` is a boolean narrowing `query` and `path`, and a boolean that grew
     * the sibling tool's `"oldest" | "stale"` spelling would break every caller
     * passing `true` — so the ways in are six and `backlog` is the sixth
     * (`D-ANS-107`).
     */
    #[Decision('D-ANS-107')]
    #[Test]
    public function theEnumerationIsAWayInOfItsOwnAndTheFiltersNarrowIt(): void
    {
        $schema = GerritLookup::inputSchema();

        self::assertSame(
            [['issue'], ['change'], ['commit'], ['query'], ['path'], ['backlog']],
            array_column($schema['oneOf'], 'required'),
        );
        self::assertSame('boolean', $schema['properties']['open']['type']);
        foreach (['maxSize', 'minCodeReview', 'negativeVotes', 'mergeable', 'branch', 'updatedBefore', 'owner', 'reviewedBy', 'involving'] as $filter) {
            self::assertArrayHasKey($filter, $schema['properties']);
        }
    }

    /**
     * The complement of `involving`, under the name that says what the answer
     * is: the open changes one person neither pushed nor voted on.
     *
     * Both operators go in together, because half of it is a set no report has
     * asked for, and it composes with the filters that select rather than
     * replacing them — `D-ANS-109`.
     */
    #[Decision('D-ANS-109')]
    #[Test]
    public function theChangesAPersonHasNotTouchedAreOneQuery(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::OPEN;
        });

        $query = $gerrit->backlog(owner: 'Frank Nägler', reviewableBy: 'Benjamin Kott')['query'];

        self::assertCount(1, $asked);
        self::assertStringEndsWith(
            'owner:"Frank Nägler" -owner:"Benjamin Kott" -reviewedby:"Benjamin Kott"',
            $query,
        );
        self::assertArrayHasKey('reviewableBy', GerritLookup::inputSchema()['properties']);
    }

    /**
     * This filter's own trap is the inverse of the one the empty answer carries.
     *
     * A name the review server cannot place takes nothing out: `-owner:` and
     * `-reviewedby:` on `zzzznotauser` answered all 444 open core changes on
     * 2026-08-25, where `owner:` on the same word answers none. So the wide
     * answer is where it has to be said — `D-ANS-109`.
     */
    #[Decision('D-ANS-109')]
    #[Test]
    public function aPageThatLeftSomebodyOutSaysWhatAMisspeltNameLooksLike(): void
    {
        $backlog = ['order' => 'oldest', 'read' => 414, 'complete' => true];

        $left = implode("\n", GerritLookup::page($backlog, 25, 'Benjamin Kott'));
        $everybody = implode("\n", GerritLookup::page($backlog, 25));

        self::assertStringContainsString('What "Benjamin Kott" pushed and voted on is out of this.', $left);
        self::assertStringContainsString('the spelling reached nobody', $left);
        self::assertStringNotContainsString('reached nobody', $everybody);
    }
}
