<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Publication\Ter;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * The registry is somebody else's host, so what is held here is everything this
 * side does with what comes back: the wrapper the list arrives in, the order it
 * is put into, and the answers a caller must not read as one another — nothing
 * is published under this key, this is not a key, and the registry did not
 * answer.
 *
 * Nothing here reaches that host. The seam is `Ter::useReader()`, and a
 * transport is a body without a status, so the one thing it cannot stand in for
 * is the `404` an unknown key is answered with. That mapping is stated in
 * `Ter::versions()` and driven live by the `ToolCalls` entry for it.
 */
final class TerTest extends TestCase
{
    #[After]
    public function putTheHostBack(): void
    {
        Ter::useReader(null);
    }

    /**
     * Four versions as the API sends them, in the shape and the order
     * `extensions.typo3.org/api/v1/extension/blog/versions` answered on
     * 2026-08-21: the whole list wrapped in an array of one, ordered by version
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
     * The list is a level down from where the endpoint's name puts it, and
     * reading it flat answers four versions as none.
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
     * Highest number first, whatever order the registry sent — and the days say
     * that this is not the upload order, which is the reading a release audit
     * gets wrong: 11.0.0 sits below 14.0.1 and was uploaded years earlier.
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
     * `held` is the whole of the tool's own reading, so both sides of it are
     * asserted: the version in the list, and one above it that nobody has
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
     * A name of the wrong kind is answered without a read.
     *
     * The registry answers `400` to one, and reporting that as "nothing is
     * published" would tell a maintainer their release is missing. A Composer
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
     * A key with nothing published under it is an answer, and the two ways that
     * reads wrongly are said out loud: a package on Composer alone is never
     * registered here, and a key can be registered before anything is uploaded
     * to it.
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
     * A host that did not answer is not a registry holding nothing.
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
     * And a caller asking about a version is told nothing about it rather than
     * that it is not published — the two readings a release audit turns on.
     */
    #[Test]
    public function aVersionIsUnansweredWhereNothingWasRead(): void
    {
        Ter::useReader(static fn(): ?string => null);

        $result = Registry::call('typo3_ter_lookup', ['extension' => 'blog', 'extensionVersion' => '14.0.1']);

        self::assertSame('unavailable', $result->data['status']);
        self::assertNull($result->data['held']);
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
