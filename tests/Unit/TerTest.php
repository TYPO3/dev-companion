<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Publication\Packagist;
use TYPO3\DevCompanion\Publication\Ter;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * The registry is somebody else's host, so this holds everything this side does
 * with what comes back. The wrapper the list arrives in, the order it takes,
 * and the answers a caller must not read as one another. This key has nothing
 * published, this is not a key, and the registry did not answer.
 *
 * Nothing here reaches that host. The seam is `Ter::useReader()`, and a
 * transport is a body without a status. So the one thing it cannot stand in for
 * is the `404` an unknown key gets. That mapping stands in `Ter::versions()`
 * and the `ToolCalls` entry for it drives it live.
 */
final class TerTest extends TestCase
{
    #[After]
    public function putTheHostBack(): void
    {
        Ter::useReader(null);
        Packagist::useReader(null);
    }

    /**
     * Four versions as the API sends them, in the shape and the order
     * `extensions.typo3.org/api/v1/extension/blog/versions` answered on
     * 2026-08-21. The whole list wrapped in an array of one, ordered by version
     * number, and 11.0.0 uploaded years before the 10.0.4 above it.
     */
    private const RESPONSE = '[[{"title":"TYPO3 Blog Extension","number":"10.0.4","state":"stable","category":"fe",'
        . '"typo3_versions":[10,11],"dependencies":{"typo3":">=10.4.0 <=11.5.99"},"upload_date":1780704000},'
        . '{"number":"11.0.0","state":"stable","typo3_versions":[11],'
        . '"dependencies":{"typo3":">=11.5.0 <=11.5.99"},"upload_date":1678320000},'
        . '{"number":"14.0.0","state":"stable","typo3_versions":[13],'
        . '"dependencies":{"typo3":">=13.4.15 <=13.4.99"},"upload_date":1764028800},'
        . '{"number":"14.0.1","state":"stable","typo3_versions":[13,14],'
        . '"dependencies":{"typo3":">=13.4.15 <=14.3.99"},"upload_date":1787237811}]]';

    /**
     * A search as the TER answered it on 2026-10-29, cut to two entries. The
     * second stands under its key alone and has nothing uploaded, so its
     * current version is an empty list.
     */
    private const TER_SEARCH = '{"results":522,"page":1,"per_page":10,"filter":{"search":"news"},"extensions":['
        . '{"key":"news","downloads":4354780,"meta":{"composer_name":"georgringer/news"},'
        . '"current_version":{"description":"Versatile news system.","number":"14.1.2","state":"stable",'
        . '"typo3_versions":[13,14],"dependencies":{"typo3":">=13.4.29 <=14.99.99"},"upload_date":1791793777}},'
        . '{"key":"legacy_news","downloads":12,"meta":{"composer_name":""},"current_version":[]}]}';

    /** The Packagist search for the same subject, cut to two results. */
    private const PACKAGIST_SEARCH = '{"results":['
        . '{"name":"georgringer/news","description":"News system","downloads":5781129},'
        . '{"name":"georgringer/eventnews","description":"Extend EXT:news with events","downloads":678167}'
        . '],"total":283}';

    /**
     * The list is a level down from where the endpoint's name puts it, and a
     * flat read answers four versions as none.
     */
    #[Test]
    public function theListIsReadOutOfTheArrayItArrivesWrappedIn(): void
    {
        $answer = (new Ter(static fn(): string => self::RESPONSE))->versions('blog');

        self::assertSame('answered', $answer['status']);
        self::assertCount(4, $answer['versions']);
        self::assertSame('https://extensions.typo3.org/api/v1/extension/blog/versions', $answer['url']);
    }

    /**
     * Highest number first, whatever order the registry sent. The days say that
     * this is not the upload order, which is the read a release audit gets
     * wrong. 11.0.0 sits below 14.0.1 and went up years earlier.
     */
    #[Test]
    public function theVersionsAreOrderedByNumberAndSayWhenEachWasUploaded(): void
    {
        $answer = (new Ter(static fn(): string => self::RESPONSE))->versions('blog');

        self::assertSame(['14.0.1', '14.0.0', '11.0.0', '10.0.4'], array_column($answer['versions'], 'number'));
        self::assertSame('2026-08-20', $answer['versions'][0]['uploaded']);
        self::assertSame([13, 14], $answer['versions'][0]['majors']);
        self::assertSame('>=13.4.15 <=14.3.99', $answer['versions'][0]['constraint']);
        self::assertSame('2023-03-09', $answer['versions'][2]['uploaded']);
    }

    /**
     * The number a release audit is about, answered as what the registry holds.
     *
     * `held` is the whole of the tool's own read, so the case asserts both
     * sides of it. The version in the list, and one above it that nobody has
     * uploaded.
     */
    #[Test]
    public function aVersionTheRegistryHoldsIsAnsweredAsPublished(): void
    {
        Ter::useReader(static fn(): string => self::RESPONSE);

        $held = Registry::call('typo3_ter_lookup', ['extension' => 'blog', 'extensionVersion' => '14.0.1']);
        self::assertTrue($held->data['held']);
        self::assertStringContainsString('The registry holds 14.0.1 · stable', $held->text);

        $free = Registry::call('typo3_ter_lookup', ['extension' => 'blog', 'extensionVersion' => '14.0.2']);
        self::assertFalse($free->data['held']);
        self::assertStringContainsString('holds no version 14.0.2', $free->text);
        // What the answer may not turn into: the registry says what is out, and
        // whether the number is free is the caller's comparison.
        self::assertStringContainsString('not a judgement that the number is free', $free->text);
    }

    /** The count is of everything published, so a cut list says it is one. */
    #[Test]
    public function aLimitedAnswerStillSaysHowMuchIsPublished(): void
    {
        Ter::useReader(static fn(): string => self::RESPONSE);

        $result = Registry::call('typo3_ter_lookup', ['extension' => 'blog', 'limit' => 2]);

        self::assertSame(4, $result->data['total']);
        self::assertSame(['14.0.1', '14.0.0'], array_column($result->data['versions'], 'number'));
        self::assertStringContainsString('The newest 2 of 4', $result->text);
    }

    /**
     * A name of the wrong kind gets its answer without a read.
     *
     * The registry answers `400` to one, and a report of that as "nothing is
     * published" would tell a maintainer their release is absent. A Composer
     * package name is the way it arrives — the extension key is a field inside
     * that package rather than its name.
     *
     * @return iterable<string, array{0: string}>
     */
    public static function namesTheRegistryDoesNotTake(): iterable
    {
        yield 'a composer package name' => ['georgringer/news'];
        yield 'a package name with a dash' => ['bootstrap-package'];
        yield 'shorter than the route allows' => ['ab'];
        yield 'nothing at all' => [''];
    }

    #[Test]
    #[DataProvider('namesTheRegistryDoesNotTake')]
    public function aNameThatIsNotAKeyIsAnsweredAboutTheName(string $name): void
    {
        $asked = 0;
        Ter::useReader(function () use (&$asked): string {
            ++$asked;

            return self::RESPONSE;
        });

        $result = Registry::call('typo3_ter_lookup', ['extension' => $name, 'extensionVersion' => '1.0.0']);

        self::assertSame(0, $asked, 'the registry was read for a name it does not take');
        self::assertSame('empty', $result->data['status']);
        self::assertSame('', $result->data['url']);
        self::assertNull($result->data['held'], 'a name that was never read answered about a version');
        self::assertStringContainsString('is not an extension key', $result->text);
    }

    /** A key is lowercase by definition, so the case it arrives in decides nothing. */
    #[Test]
    public function aKeyInTheWrongCaseIsTheSameKey(): void
    {
        $asked = [];
        Ter::useReader(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::RESPONSE;
        });

        $result = Registry::call('typo3_ter_lookup', ['extension' => 'Blog']);

        self::assertSame('blog', $result->data['extension']);
        self::assertSame(['https://extensions.typo3.org/api/v1/extension/blog/versions'], $asked);
    }

    /**
     * A key with nothing published under it is an answer, and the answer says
     * the two ways that reads wrong out loud. A package on Composer alone never
     * has a key here, and a key can exist before anything goes up to it.
     */
    #[Test]
    public function aKeyNothingIsPublishedUnderIsAnAnswer(): void
    {
        Ter::useReader(static fn(): string => '[[]]');

        $result = Registry::call('typo3_ter_lookup', ['extension' => 'quantumflux_transponder']);

        self::assertSame('empty', $result->data['status']);
        self::assertNull($result->data['unavailable'], 'a key nobody published under was reported as a failure');
        self::assertSame(0, $result->data['total']);
        self::assertStringContainsString('nothing is published under the key', $result->text);
    }

    /**
     * A host that did not answer is not a registry that holds nothing.
     *
     * The transport reports no body, which is what a timeout, a DNS failure and
     * a 500 all reach this side as.
     */
    #[Test]
    public function aRegistryThatDidNotAnswerIsNotAnEmptyOne(): void
    {
        $answer = (new Ter(static fn(): ?string => null))->versions('blog');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('source-not-answering', $answer['cause']);
        self::assertSame([], $answer['versions']);
    }

    /**
     * And a caller who asks about a version hears nothing about it rather than
     * that it is not published. Those are the two readings a release audit
     * turns on.
     */
    #[Test]
    public function aVersionIsUnansweredWhereNothingWasRead(): void
    {
        Ter::useReader(static fn(): ?string => null);

        $result = Registry::call('typo3_ter_lookup', ['extension' => 'blog', 'extensionVersion' => '14.0.1']);

        self::assertSame('unavailable', $result->data['status']);
        self::assertNull($result->data['held']);
    }

    /**
     * One match per package, from both searches. The TER entry joins the
     * Packagist one by its Composer name and adds the key and the current
     * version. What only the TER found follows what Packagist found.
     */
    #[Decision('D-ANS-193')]
    #[Test]
    public function aSearchJoinsBothRegistriesByThePackageName(): void
    {
        Packagist::useReader(static fn(): string => self::PACKAGIST_SEARCH);
        Ter::useReader(static fn(): string => self::TER_SEARCH);

        $result = Registry::call('typo3_ter_lookup', ['query' => 'news']);

        self::assertSame('answered', $result->data['status']);
        self::assertSame(
            ['georgringer/news', 'georgringer/eventnews', ''],
            array_column($result->data['matches'], 'package'),
        );
        $news = $result->data['matches'][0];
        self::assertSame('news', $news['key']);
        self::assertSame('14.1.2', $news['version']);
        self::assertSame([13, 14], $news['majors']);
        self::assertSame([Packagist::HOST, Ter::HOST], $news['foundIn']);
        self::assertSame('', $result->data['matches'][1]['key']);
        self::assertSame('legacy_news', $result->data['matches'][2]['key']);
        self::assertSame([522, 283], array_column($result->data['searches'], 'total'));
        self::assertContains('Read every version of one with typo3_ter_lookup and extension set to its key.', $result->data['owed']);
    }

    /**
     * A registry that did not answer leaves a gap, and the answer says where.
     * The other one still answers.
     */
    #[Test]
    public function aSearchSaysWhichRegistryWentUnsearched(): void
    {
        Packagist::useReader(static fn(): string => self::PACKAGIST_SEARCH);
        Ter::useReader(static fn(): ?string => null);

        $result = Registry::call('typo3_ter_lookup', ['query' => 'news']);

        self::assertSame('answered', $result->data['status']);
        self::assertSame(['unavailable', 'answered'], array_column($result->data['searches'], 'status'));
        self::assertStringContainsString('Not searched: The registry did not answer. It is reachable at ' . Ter::HOST, $result->text);
    }

    /** No match on either side is an answer, not a failure. */
    #[Test]
    public function aSearchNothingMatchesIsAnAnswer(): void
    {
        Packagist::useReader(static fn(): string => '{"results":[],"total":0}');
        Ter::useReader(static fn(): string => '{"results":0,"page":1,"per_page":10,"filter":{"search":"x"},"extensions":[]}');

        $result = Registry::call('typo3_ter_lookup', ['query' => 'quantumflux transponder']);

        self::assertSame('empty', $result->data['status']);
        self::assertNull($result->data['unavailable']);
        self::assertSame([], $result->data['matches']);
    }

    /** The parameter the list route reads, where `q` answers every extension it holds. */
    #[Test]
    public function theTerIsSearchedThroughItsFilterParameter(): void
    {
        $answer = (new Ter(static fn(): string => self::TER_SEARCH))->search('cookie consent', 5);

        self::assertSame(Ter::HOST . '/api/v1/extension?filter%5Bsearch%5D=cookie%20consent&per_page=5', $answer['url']);
    }

    /** A page with a 200 in front of it is not an answer either. */
    #[Test]
    public function aPageInPlaceOfTheApiIsNotAnAnswer(): void
    {
        $answer = (new Ter(static fn(): string => '<!doctype html><title>Sign in</title>'))->versions('blog');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('source-not-parseable', $answer['cause']);
    }
}
