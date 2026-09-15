<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Knowledge\Hints;
use TYPO3\DevCompanion\Knowledge\Scope;
use TYPO3\DevCompanion\Knowledge\TaskIntents;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Tool\TranslationDomainLookup;

/**
 * Which TYPO3 an answer is for, and what that leaves out.
 *
 * A convention that is current on the development line may not exist on the LTS
 * a site runs. Handing it over anyway produces code that fails at runtime and
 * fails silently — which is why the range is data rather than a sentence.
 */
#[Requirement('R-AUD-004')]
final class VersionsTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        Instance::discoverFrom(null);
    }

    #[Test]
    public function theCoveredVersionsAreDeclaredInOnePlaceAndSorted(): void
    {
        $covered = Versions::covered();

        self::assertNotSame([], $covered);
        self::assertSame(Versions::majors(), array_values(array_unique(Versions::majors())));
        $sorted = Versions::majors();
        sort($sorted);
        self::assertSame($sorted, Versions::majors());
        foreach ($covered as $version) {
            self::assertNotSame('', $version['branch'], 'a covered version names the branch it is verified against');
        }
    }

    /**
     * Whether this server knows the TYPO3 somebody runs is what decides whether
     * they install it. They read it in the two places somebody arrives at: the
     * readme on GitHub and the front page of the site. So this holds the
     * sentence that names the lines to the declaration. Not to what the
     * coverage was on the day somebody wrote it — `D-DOC-026`, `D-DOC-030`.
     */
    #[Decision('D-DOC-026')]
    #[Decision('D-DOC-030')]
    #[Test]
    public function whatSomebodyArrivesAtNamesEveryCoveredLine(): void
    {
        foreach (['readme.md', 'documentation/readme.rst'] as $page) {
            $landing = (string) file_get_contents(Paths::root() . '/' . $page);

            foreach (Versions::covered() as $version) {
                self::assertStringContainsString(
                    '**' . $version['branch'] . '**',
                    $landing,
                    $version['branch'] . ' is covered and ' . $page . ' does not name it',
                );
            }
        }
    }

    #[Test]
    public function theOneVersionFactTheCodeCarriesIsOneOfTheDeclaredVersions(): void
    {
        // D-DIS-004 puts the version translation domains arrived in into the
        // code rather than the knowledge base. The answer below it stays back
        // rather than gets a qualifier. It is one number in one place so that a
        // backport has one thing to make wrong. That number and
        // knowledge/versions.json stand apart and can drift apart. A covers
        // list that drops a major below SINCE leaves the withhold branch to
        // answer for versions this base no longer covers. Every behaviour test
        // still passes because each names its own version string. So this reads
        // the number against the declared list. The two versions the behaviour
        // pins at derive from that list rather than stand a second time.
        $majors = Versions::majors();

        self::assertContains(
            TranslationDomainLookup::SINCE,
            $majors,
            'the version domains are withheld below is not one knowledge/versions.json declares',
        );

        $below = array_values(array_filter($majors, static fn(int $major): bool => $major < TranslationDomainLookup::SINCE));
        if ($below === []) {
            self::fail('no covered major is below it, so the answer it withholds is one nothing can ask for');
        }

        // And what the number means, at the two covered majors it divides.
        $ask = function (int $major): array {
            Instance::discoverFrom($this->composerProject('vendor', $this->versionOn($major)));

            return Registry::call('typo3_translation_domain_lookup', [
                'path' => 'EXT:my_ext/Resources/Private/Language/locallang_db.xlf',
            ])->data;
        };

        $newest = $ask((int) max($below));
        self::assertNull($newest['domain'], 'the newest covered major below SINCE still has domains withheld');
        self::assertSame('my_ext.db', $newest['domainOnNewerVersions']);

        self::assertSame(
            'my_ext.db',
            $ask(TranslationDomainLookup::SINCE)['domain'],
            'and the major SINCE names is answered with the domain',
        );
    }

    /**
     * The domain answered as the module imports it, on the majors that have
     * both.
     *
     * `D-ANS-132`. The resolver and the `~labels` import map prefix arrived
     * together. So the branch with no domain has nothing to import either, and
     * the field is absent there rather than null.
     */
    #[Decision('D-ANS-132')]
    #[Test]
    public function theDomainIsAnsweredInTheFormAModuleImportsIt(): void
    {
        Instance::discoverFrom($this->composerProject('vendor', $this->versionOn(TranslationDomainLookup::SINCE)));
        $answered = Registry::call('typo3_translation_domain_lookup', [
            'path' => 'EXT:my_ext/Resources/Private/Language/locallang_db.xlf',
        ]);

        self::assertSame('~labels/my_ext.db', $answered->data['moduleImport']);
        self::assertStringContainsString('import labels from "~labels/my_ext.db"', $answered->text);

        $below = array_values(array_filter(
            Versions::majors(),
            static fn(int $major): bool => $major < TranslationDomainLookup::SINCE,
        ));
        if ($below === []) {
            self::fail('no covered major is below the version domains arrived in');
        }

        Instance::discoverFrom($this->composerProject('vendor', $this->versionOn(max($below))));
        $withheld = Registry::call('typo3_translation_domain_lookup', [
            'path' => 'EXT:my_ext/Resources/Private/Language/locallang_db.xlf',
        ]);

        self::assertArrayNotHasKey('moduleImport', $withheld->data);
    }

    /**
     * A version string on a covered major, from the branch a session verifies
     * that major against. So `main` becomes a number rather than a branch name.
     */
    private function versionOn(int $major): string
    {
        $branch = (string) Versions::branch($major);

        return preg_match('/^\d+\.\d+$/', $branch) === 1 ? $branch . '.0' : $major . '.0.0';
    }

    #[Test]
    public function aStatedVersionWinsOverTheInstallationBeingRead(): void
    {
        Instance::discoverFrom($this->composerProject('vendor', '13.4.33'));

        self::assertSame(13, Versions::target(null), 'the installation answers when nobody stated one');
        self::assertSame(12, Versions::target('12.4'), 'a caller working on another line says so');
        self::assertSame(15, Versions::target('v15'));
    }

    #[Test]
    public function withoutAnyVersionNothingIsFiltered(): void
    {
        self::assertNull(Versions::target(null));
        self::assertTrue(Versions::holds(14, null, null));
        self::assertTrue(Versions::holds(null, 13, null));
    }

    #[Test]
    public function aRangeIsWhatItSays(): void
    {
        self::assertTrue(Versions::holds(14, null, 14));
        self::assertFalse(Versions::holds(14, null, 13));
        self::assertTrue(Versions::holds(null, 13, 12));
        self::assertFalse(Versions::holds(null, 13, 14));
        self::assertTrue(Versions::holds(13, 14, 14));
        self::assertFalse(Versions::holds(13, 14, 15));

        self::assertSame('', Versions::label(null, null));
        self::assertSame('TYPO3 v14 and newer', Versions::label(14, null));
        self::assertSame('up to TYPO3 v13', Versions::label(null, 13));
        self::assertSame('TYPO3 v13 to v14', Versions::label(13, 14));
    }

    #[Test]
    public function aStatementIsLeftOutOfAnAnswerItDoesNotHoldFor(): void
    {
        // Translation domains do not exist below the version they arrived in,
        // and the domain string is syntactically fine there — the label just
        // renders empty. The whole hint has the bound. Every statement of it
        // arrived in 14. So on 13 there is nothing left of it, and the answer
        // drops it rather than returns it empty. That is the same rule one
        // statement further.
        self::assertNull(Hints::byId('translation-domain', 13));

        $onFourteen = implode("\n", array_column(
            Hints::byId('translation-domain', 14)['hints'],
            'text',
        ));
        self::assertStringContainsString('translation domain', $onFourteen);
    }

    #[Test]
    public function withoutATargetTheStatementComesBackWithItsRange(): void
    {
        $result = Registry::call('typo3_hint_lookup', ['id' => 'translation-domain']);

        self::assertNull($result->data['targetVersion']);
        $bound = array_values(array_filter(
            $result->data['hints'][0]['hints'],
            static fn(array $statement): bool => $statement['since'] !== null || $statement['until'] !== null,
        ));
        self::assertNotSame([], $bound, 'a bound statement is still returned, with the range beside it');
        self::assertStringContainsString('TYPO3 v', $bound[0]['versions']);
        self::assertStringContainsString('[TYPO3 v', $result->text);
    }

    #[Decision('D-VER-005')]
    #[Test]
    #[DataProvider('theProseLookupsAndAQueryEachAnswers')]
    public function proseSaysWhereARangeItDoesNotCarryLives(string $tool, string $query): void
    {
        // A section carries its own range since `D-VER-005`, so the sentence no
        // longer stands in for a bound. Two ranges are still somewhere else,
        // and a caller sent to the wrong one goes nowhere. A runTests.sh
        // command has its bound in the suite in test-suite-hints.json, a
        // convention in the statement in the hints.
        $text = Registry::call($tool, ['query' => $query, 'task' => $query])->text;

        self::assertStringContainsString('A section carries the range it holds for', $text);
        self::assertStringContainsString('typo3_hint_lookup with targetVersion', $text);
        self::assertStringContainsString('typo3_test_run_guide with targetVersion', $text);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function theProseLookupsAndAQueryEachAnswers(): array
    {
        return [
            'the rule lookup' => ['typo3_rule_lookup', 'event listener'],
            'the script lookup' => ['typo3_script_lookup', 'unit tests'],
        ];
    }

    #[Test]
    public function theRangeIsNeverWrittenIntoTheSentence(): void
    {
        // No filter, render or check reaches a version in the prose, and it is
        // the thing that goes stale without a word.
        foreach (Hints::load() as $hint) {
            foreach ($hint['hints'] as $statement) {
                self::assertDoesNotMatchRegularExpression(
                    '/\bTYPO3 v\d|\bsince v?\d|\bfrom v\d/i',
                    $statement['text'],
                    $hint['id'] . ' dates a statement in its prose instead of binding it',
                );
            }
        }
    }

    #[Requirement('R-AUD-005')]
    #[Decision('D-KNW-007')]
    #[Test]
    public function whoIsObligedIsWrittenAsDataToo(): void
    {
        // Same rule as the version range, for the other question a statement
        // can answer differently per caller. An answer cannot filter or mark
        // what stands inside the sentence. "core" was the only value for as
        // long as the corpus wrote only that one. The mirror is a data entry
        // rather than a vocabulary change. `Scope::ofKnowledge()` has offered
        // `project` and `extension` since `D-KNW-005`. What writes them are the
        // hints whose whole subject is a repository outside the core
        // (`D-KNW-007`).
        foreach (Hints::load() as $hint) {
            foreach (array_merge([$hint], $hint['hints']) as $entry) {
                if (($entry['scope'] ?? null) !== null) {
                    self::assertContains(
                        $entry['scope'],
                        Scope::ofKnowledge(),
                        $hint['id'] . ' binds to something this server has no vocabulary for',
                    );
                }
            }
        }

        // The task intents answer the same question about the same caller and
        // wrote it as coreOnly: true. That is the same axis in a second
        // vocabulary. A boolean cannot carry the value a third audience would
        // need. One name, one enforced value, both corpora.
        foreach (TaskIntents::load() as $intent) {
            if ($intent['scope'] !== null) {
                self::assertSame(
                    Scope::Core,
                    $intent['scope'],
                    $intent['id'] . ' binds to something this server has no vocabulary for',
                );
            }
        }
    }

    #[Requirement('R-DIS-016')]
    #[Test]
    public function anExtensionThatServesTwoMajorsIsAnsweredForBoth(): void
    {
        // One codebase, two majors. What arrived in 14 and what is still true
        // on 13 are both rules its author has to hold. The difference between
        // them is the constraint the code stands around.
        $root = $this->composerProject('vendor', '14.3.5');
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/extension',
            'type' => 'typo3-cms-extension',
            'require' => ['typo3/cms-core' => '^13.4 || ^14.3'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        self::assertSame([13, 14], Versions::targets(), 'the declaration decides where there is one');
        self::assertSame(14, Versions::target(), 'what the repository runs is still a single version');
        self::assertSame([14], Versions::targets('14'), 'a caller who names a version is asking about it');

        $statements = implode("\n", array_column(
            Hints::byId('extension-manifest', Versions::targets())['hints'],
            'text',
        ));
        self::assertStringContainsString(
            'what makes a directory an extension outside Composer',
            $statements,
            'the rule that still holds on 13.4 is dropped when the answer is filtered to the installed major',
        );
        self::assertStringContainsString(
            'deprecated fallback',
            $statements,
            'and the rule that arrived in 14 is in the same answer',
        );
    }

    #[Requirement('R-DIS-016')]
    #[Test]
    public function theAnswerSaysWhichMajorsItWasComposedFor(): void
    {
        $root = $this->composerProject('vendor', '14.3.5');
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/extension',
            'require' => ['typo3/cms-core' => '^13.4 || ^14.3'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_hint_lookup', ['id' => 'extension-manifest']);

        self::assertSame([13, 14], $result->data['targetVersions']);
        self::assertSame(14, $result->data['targetVersion']);
        self::assertStringContainsString('TYPO3 v13 and v14 at once', $result->text);
        self::assertStringContainsString('^13.4 || ^14.3', $result->text);
    }

    #[Requirement('R-DIS-016')]
    #[Test]
    public function aStatedMajorSaysWhichOtherOneItLeftOut(): void
    {
        // How a session switches the wider answer off in practice. It reads
        // 14.3.0 out of typo3_project_describe and states it, because a repeat
        // of what the repository runs looks like the accurate thing to do. The
        // narrow answer is then correct, since the caller asked for it, but
        // invisible. What comes back is the answer this filter changed to stop.
        // So the answer names the major it is for, the ones the repository
        // declares beside it, and that their statements are absent.
        $root = $this->composerProject('vendor', '14.3.5');
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/extension',
            'require' => ['typo3/cms-core' => '^13.4 || ^14.3'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        $narrowed = Registry::call('typo3_hint_lookup', ['id' => 'extension-files', 'targetVersion' => '14.3']);

        self::assertSame([14], $narrowed->data['targetVersions'], 'what the caller stated still wins');
        self::assertStringContainsString('Answered for TYPO3 v14 alone', $narrowed->text);
        self::assertStringContainsString('^13.4 || ^14.3', $narrowed->text);
        self::assertStringContainsString('only on v13 is missing from this answer', $narrowed->text);
        self::assertStringContainsString('Leave targetVersion out', $narrowed->text);

        // The task guide is where the review stated its version first, and it
        // said nothing at all about it.
        $guide = Registry::call('typo3_task_guide', ['task' => 'Review this extension', 'targetVersion' => '14.3']);
        self::assertStringContainsString('Answered for TYPO3 v14 alone', $guide->text);

        // A repository that declares one major has no wider answer to point
        // at, and the sentence stays the one it always was.
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/extension',
            'require' => ['typo3/cms-core' => '^14.3'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        $single = Registry::call('typo3_hint_lookup', ['id' => 'extension-files', 'targetVersion' => '14.3']);
        self::assertStringContainsString('Answered for TYPO3 v14: statements that do not hold', $single->text);
        self::assertStringNotContainsString('Answered for TYPO3 v14 alone', $single->text);
        self::assertStringNotContainsString(
            'Answered for TYPO3',
            Registry::call('typo3_task_guide', ['task' => 'Review this extension', 'targetVersion' => '14.3'])->text,
            'and the ordinary task guide says nothing about versions at all',
        );
    }

    #[Requirement('R-DIS-016')]
    #[Test]
    public function aConstraintIsReadByAskingItAboutEachCoveredMajor(): void
    {
        self::assertSame([13, 14], Versions::declared('^13.4 || ^14.3'));
        self::assertSame([13], Versions::declared('^13.4'));
        self::assertSame([13], Versions::declared('~13.4'));
        self::assertSame([12, 13, 14], Versions::declared('>=12.4 <15'));
        self::assertSame([12, 13, 14], Versions::declared('>=12.4,<15.0'));
        self::assertSame(Versions::majors(), Versions::declared('*'));

        // Unreadable is not "everything": the caller falls back to the single
        // installed version, which is what this has always answered.
        self::assertSame([], Versions::declared('dev-main'));
        self::assertSame([], Versions::declared(null));
        self::assertSame([], Versions::declared('   '));
    }

    #[Test]
    public function aPinToOneMajorIsTellableFromAConstraintThatSpansTwo(): void
    {
        // What `bin/cli versions:check` asks of the Fluid constraint in each
        // core checkout, and the reason D-VER-003 needs no engine axis. The pin
        // is what makes the TYPO3 major carry the engine. So a constraint that
        // no longer pins has to stand apart from one that never did. Asked over
        // engine majors rather than covered TYPO3 ones, which is the point of a
        // question per major instead of a parse of a range.
        $spans = static fn(string $constraint): array => array_values(array_filter(
            range(1, 20),
            static fn(int $major): bool => Versions::admits($constraint, $major),
        ));

        self::assertSame([2], $spans('^2.15.0'));
        self::assertSame([4], $spans('^4.6.1'));
        self::assertSame([5], $spans('~5.3.1'));
        self::assertSame([4, 5], $spans('^4.6.1 || ^5.0'));
        self::assertSame([4, 5], $spans('>=4.6.1 <6'));

        // Unreadable is not a pin either: a constraint nothing here can read is a
        // constraint nobody can say the engine major from.
        self::assertSame([], $spans('dev-main'));
    }

    /**
     * The range spellings three extension checkouts actually declare a TYPO3
     * major with, and the majors each one serves.
     *
     * `D-VER-004` is wrong if a form in the wild answers false for a major it
     * does serve. Nothing but a table like this one would catch it. Read on
     * 2026-08-02 out of the three checkouts that play `E-EXT`, their root
     * manifests and every `typo3/cms-*` requirement in their vendor trees.
     * Every expectation below is composer/semver's own answer for that form.
     *
     * Asked over majors rather than over the covered ones, because which majors
     * a form serves is a property of the form. A version this knowledge base
     * stops covering must not rewrite the table without a word.
     *
     * @return iterable<string, array{string, array<int, int>}>
     */
    public static function rangeSpellingsFromTheWild(): iterable
    {
        yield 'a caret per major, in both current checkouts' => ['^13.4 || ^14.3', [13, 14]];
        yield 'a caret per major, pinned to the patch that fixed something' => ['^12.4.37 || ^13.4.15', [12, 13]];
        yield 'a release that also takes the next major from its branch' => ['^13.4 || ^14.0 || 14.*.*@dev', [13, 14]];
        yield 'four majors, as a compatibility extension declares them' => ['^11.5 || ^12.4 || ^13.4 || ^14.0', [11, 12, 13, 14]];
        yield 'branch wildcards alone, as typo3/testing-framework writes them' => ['13.*.*@dev || 14.*.*@dev', [13, 14]];
        yield 'the same, one major behind' => ['12.*.*@dev || 13.*.*@dev', [12, 13]];
        yield 'an exact version, as the core packages require each other' => ['14.3.0', [14]];
        yield 'an exact version on the older line' => ['13.4.33', [13]];
        // Not read off a typo3/cms-core requirement but off the php one beside
        // it, in georgringer/news: `>= 8.1 < 8.5`. Composer takes the space and
        // this did not. So the same author with a core range in that form would
        // have got an answer for the installed major alone.
        yield 'an operator with a space after it, the way that manifest writes php' => ['>= 12.4.37 < 14', [12, 13]];
    }

    /**
     * A supported range is a property of the package, so every form a real
     * manifest writes answers for the majors it serves — `D-VER-004`.
     */
    /** @param array<int, int> $majors */
    #[Decision('D-VER-004')]
    #[Test]
    #[DataProvider('rangeSpellingsFromTheWild')]
    public function aSpellingFromTheWildAnswersForEveryMajorItServes(string $constraint, array $majors): void
    {
        $answered = array_values(array_filter(
            range(10, 16),
            static fn(int $major): bool => Versions::admits($constraint, $major),
        ));

        self::assertSame($majors, $answered, $constraint);
    }

    /**
     * Every `require.php` form the four core checkouts declare, and the lowest
     * version each one admits.
     *
     * `D-VER-004` names the way `D-ANS-082`'s assumption failed one level up.
     * So it owes the same corpus: every `require.php` in the four checkouts and
     * their vendor trees, read on 2026-08-18. Every expectation is
     * composer/semver's own answer for that form rather than a read of it.
     *
     * The last four are not in that corpus and are the shapes `D-ANS-082` is
     * wrong if this misreads. Three answer, and the hyphen range does not. A
     * wrong floor carries the answer's authority where an absent one costs a
     * sentence, so it stays unread the way `D-VER-004` left it.
     *
     * @return iterable<string, array{string, ?string}>
     */
    public static function phpConstraintSpellingsFromTheCheckouts(): iterable
    {
        yield 'the commonest of all, a bare lower bound' => ['>=8.2', '8.2'];
        yield 'the same one minor down' => ['>=8.1', '8.1'];
        yield 'a lower bound carrying a patch level' => ['>=8.4.1', '8.4'];
        yield 'a lower bound at a major on its own' => ['>=7.0', '7.0'];
        yield 'a caret' => ['^8.1', '8.1'];
        yield 'the newest one in the corpus' => ['^8.5', '8.5'];
        yield 'a caret per major, the shape a library spanning two writes' => ['^7.2 || ^8.0', '7.2'];
        yield 'the same with a patch level on the lower one' => ['^7.2.5 || ^8.0', '7.2'];
        yield 'three majors, the oldest of them a 5' => ['^5.3.2 || ^7.0 || ^8.0', '5.3'];
        yield 'alternatives separated by a single pipe' => ['^7.4|^8.0', '7.4'];
        yield 'a single pipe with spaces around it' => ['^7.1 | ^8.0', '7.1'];
        yield 'alternatives in descending order, so the floor is not the first' => ['^8.0 || ^7.4', '7.4'];
        yield 'a lower and an upper bound in one alternative' => ['>=7.1 <9.0', '7.1'];
        yield 'minor wildcards, one per supported minor' => ['8.1.* || 8.2.* || 8.3.* || 8.4.*', '8.1'];
        yield 'a tilde per minor, as a tool pinning each one writes it' => [
            '~7.4.0 || ~8.0.0 || ~8.1.0 || ~8.2.0 || ~8.3.0 || ~8.4.0 || ~8.5.0 || ~8.6.0',
            '7.4',
        ];

        yield 'an operator with a space after it, which broke D-VER-004' => ['>= 8.1 < 8.5', '8.1'];
        yield 'a bare minor wildcard' => ['8.2.*', '8.2'];
        yield 'a tilde on a minor, with no patch level' => ['~8.3', '8.3'];
        yield 'a constraint admitting everything, which names no floor' => ['*', null];
        yield 'a branch name, which is no version at all' => ['dev-main', null];
        yield 'Composer\'s hyphen range, which this declines to read' => ['8.1 - 8.4', null];
    }

    #[Requirement('R-PRJ-010')]
    #[Test]
    #[DataProvider('phpConstraintSpellingsFromTheCheckouts')]
    public function aPhpSpellingFromTheCheckoutsAnswersItsLowestVersion(string $constraint, ?string $floor): void
    {
        self::assertSame($floor, Versions::floor($constraint), $constraint);
    }

    #[Test]
    public function aConstraintThatDeclaresNoPhpHasNoFloor(): void
    {
        // The ordinary case in a site project, and the reason the whole relation
        // is null rather than a comparison against a floor of 0.
        self::assertNull(Versions::floor(null));
        self::assertNull(Versions::floor(''));
        self::assertNull(Versions::floor('   '));
    }

    #[Requirement('R-PRJ-010')]
    #[Test]
    public function theFloorIsReadOneLevelBelowTheMajorTheRestOfThisAnswers(): void
    {
        // Why this exists at all. `admits()` reasons in majors, so the pair the
        // session had, `^8.3` declared against a core that requires `^8.2`, is
        // a difference it cannot carry. Both are TYPO3-major 8 to it, and both
        // would be the same answer.
        self::assertSame(Versions::admits('^8.3', 8), Versions::admits('^8.2', 8));
        self::assertNotSame(Versions::floor('^8.3'), Versions::floor('^8.2'));
    }

    #[Test]
    public function oneDeclaredMajorAnswersExactlyAsBefore(): void
    {
        $root = $this->composerProject('vendor', '14.3.5');
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/extension',
            'require' => ['typo3/cms-core' => '^14.3'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        self::assertSame([14], Versions::targets());
        self::assertStringContainsString(
            'Answered for TYPO3 v14',
            Registry::call('typo3_hint_lookup', ['id' => 'extension-manifest'])->text,
        );
    }
}
