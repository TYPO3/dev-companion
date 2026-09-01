<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PhpCsFixer\ConfigInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Editorconfig;

/**
 * What holds the shape of the source tree itself, rather than what any one
 * class does.
 */
final class StructureTest extends TestCase
{
    /**
     * A second class in a file is invisible to PSR-4: the file is found through
     * the first class's name, so the second one loads only where something has
     * already loaded the first. It works in the file that wrote it and fails as
     * a missing class from anywhere else, which is the kind of failure that
     * arrives long after the commit — `D-COD-001`.
     */
    #[Decision('D-COD-001')]
    #[Test]
    public function everyFileDeclaresOneClass(): void
    {
        foreach (self::sources() as $file) {
            $tokens = \PhpToken::tokenize((string) file_get_contents($file));

            $declarations = [];
            foreach ($tokens as $index => $token) {
                if (!$token->is([T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM])) {
                    continue;
                }
                // `Foo::class` and an anonymous `new class` are not
                // declarations, and only a name makes one.
                $next = $tokens[$index + 2] ?? null;
                $previous = $tokens[$index - 1] ?? null;
                if ($next?->is(T_STRING) === true && $previous?->is(T_DOUBLE_COLON) !== true) {
                    $declarations[] = $next->text;
                }
            }

            self::assertCount(
                1,
                $declarations,
                sprintf('%s declares %s.', $file, implode(', ', $declarations) ?: 'nothing'),
            );
        }
    }

    /**
     * A skipped test is how a test stops holding anything without stopping the
     * suite, and the summary that reports it is the one nobody reads twice.
     *
     * Every precondition this suite has is a property of this repository — it
     * is a standalone checkout, its feedback archive is committed, its
     * knowledge base is on disk — so each is an assertion instead, and a
     * precondition that stopped being true is a failure with a sentence rather
     * than a test that quietly went away. The two skips this replaced had both
     * been true since the day they were written.
     *
     * A genuinely environment-dependent case would need a way past this. It
     * would also need this paragraph rewritten, which is the point —
     * `D-FBK-013`.
     */
    #[Decision('D-FBK-013')]
    #[Test]
    public function noTestSkipsItselfInsteadOfHolding(): void
    {
        $skipping = [];
        foreach (self::testFiles() as $file) {
            $contents = (string) file_get_contents($file);
            foreach (['markTestSkipped', 'markTestIncomplete'] as $escape) {
                if (str_contains($contents, $escape . '(')) {
                    $skipping[] = basename($file) . ' calls ' . $escape . '()';
                }
            }
        }

        self::assertSame([], $skipping);
    }

    /**
     * One place spells how a document is addressed — `D-KNW-059`.
     *
     * The prefix was written by hand in `Result\Prose`, `Tool\HintLookup` and
     * `Sdk\ResourceHandler` at once, and neither of the first two may reach
     * into the SDK adapter for a name. `Documents::uri()` is what they call, so
     * a namespace that moves again moves in one file.
     *
     * The source alone. A test that drives the wire spells the URI out on
     * purpose: an expectation computed from the code under test asserts that
     * the code equals itself.
     */
    #[Decision('D-KNW-059')]
    #[Test]
    public function onlyTheCorpusSpellsHowADocumentIsAddressed(): void
    {
        $files = Finder::create()->files()->in(dirname(__DIR__, 2) . '/src')
            ->name('*.php')->notName('Documents.php')->sortByName();

        foreach ($files as $file) {
            self::assertDoesNotMatchRegularExpression(
                "/['\"]typo3:\/\/guides\//",
                (string) file_get_contents($file->getPathname()),
                $file->getRelativePathname() . ' builds a document URI of its own; call Documents::uri()',
            );
        }
    }

    /**
     * One idiom for reading a directory, so a flat listing and a deep one are
     * the same call and a tolerance is written where it is relied on —
     * `D-COD-003`.
     */
    #[Decision('D-COD-003')]
    #[Test]
    public function everyDirectoryIsReadThroughTheFinder(): void
    {
        $found = [];
        $files = Finder::create()->files()->in([dirname(__DIR__, 2) . '/src', dirname(__DIR__, 2) . '/bin', dirname(__DIR__)])
            ->notName('StructureTest.php')->sortByName();
        foreach ($files as $file) {
            preg_match_all(
                '/\b(glob|scandir|opendir|readdir)\s*\(|\bRecursive(?:Directory|Iterator)Iterator\b|\bFilesystemIterator\b/',
                (string) file_get_contents($file->getPathname()),
                $matches,
            );
            foreach ($matches[0] as $call) {
                $found[] = $file->getFilename() . ' uses ' . rtrim($call, ' (');
            }
        }

        self::assertSame([], $found);
    }

    /**
     * A conflict a rebase left behind, in any file this repository keeps.
     *
     * Nothing else caught one. A resolution that drops a marker leaves a file
     * that parses, lints and passes every test that does not happen to read it
     * — the run of 2026-08-02 left a `>>>>>>>` in a decision, and `composer ci`
     * went green over it because no test opens that entry. It reached `main` in
     * a commit whose diff looked deliberate.
     *
     * It is here rather than in a `:check` command because this is the suite a
     * branch runs in its own worktree after the rebase, which is the one moment
     * a bad resolution exists and the only one where naming it is cheap. That
     * is the same reason the group listings are deliberately *not* here: a
     * listing is stale in every branch that adds an entry and correct once on
     * `main`, while a marker is wrong wherever it stands.
     *
     * The two arrow markers are enough on their own. `=======` is a heading
     * underline in Markdown and a rule in half the documents here, so matching
     * it would fail on prose that is doing its job, and no resolution takes out
     * both arrows and leaves the middle.
     */
    #[Test]
    public function noFileCarriesAConflictMarker(): void
    {
        // Built rather than written, so that this file is not the first thing
        // the check reports.
        $markers = [str_repeat('<', 7), str_repeat('>', 7), str_repeat('|', 7)];

        $found = [];
        foreach (self::kept() as $file) {
            $lines = explode("\n", (string) file_get_contents($file));
            foreach ($lines as $number => $line) {
                foreach ($markers as $marker) {
                    if (str_starts_with($line, $marker)) {
                        $found[] = sprintf(
                            '%s:%d carries %s',
                            substr($file, strlen(dirname(__DIR__, 2)) + 1),
                            $number + 1,
                            $marker,
                        );
                    }
                }
            }
        }

        self::assertSame([], $found, 'a rebase was resolved and one of its markers stayed in the file');
    }

    /**
     * `.editorconfig` is what an editor obeys while a file is being typed, and
     * php-cs-fixer is what rewrites it afterwards. Where the two disagree, each
     * undoes the other: a line typed at the stated indentation comes back
     * reindented, and nobody looks for the argument in a config file.
     *
     * The fixer states its indentation by not stating one — PER-CS 3.0 is four
     * spaces and `Config` defaults to it — so this asks the config rather than
     * the rule list.
     */
    #[Test]
    public function editorconfigTypesPhpTheWayTheFixerRewritesIt(): void
    {
        $config = require dirname(__DIR__, 2) . '/.php-cs-fixer.dist.php';

        self::assertInstanceOf(ConfigInterface::class, $config);
        self::assertSame(
            strlen($config->getIndent()),
            Editorconfig::indentFor('Paths.php'),
            '.php-cs-fixer.dist.php and .editorconfig disagree about how PHP is indented',
        );
    }

    /** @return array<int, string> */
    private static function testFiles(): array
    {
        $tests = [];
        foreach (Finder::create()->files()->in(dirname(__DIR__))->name('*Test.php')->sortByName() as $file) {
            $tests[] = $file->getPathname();
        }

        return $tests;
    }

    /**
     * Every file this repository writes and keeps, whatever it is written in.
     *
     * A conflict lands wherever the two branches disagreed, and on this
     * repository that is far more often a decision or a hint than a class — so
     * the walk is by directory rather than by extension, and the exclusions are
     * the three trees nobody here authors: `vendor/` and `.worktrees/` belong
     * to other checkouts, and `.checkouts/` is 861 MB of TYPO3 whose own
     * history carries conflict markers in test fixtures.
     *
     * @return array<int, string>
     */
    private static function kept(): array
    {
        $root = dirname(__DIR__, 2);

        $files = [];
        foreach (['AGENTS.md', 'CLAUDE.md', 'readme.md', 'composer.json', 'phpstan.neon', 'phpunit.xml.dist'] as $name) {
            if (is_file($root . '/' . $name)) {
                $files[] = $root . '/' . $name;
            }
        }

        $directories = array_values(array_filter(
            array_map(
                static fn(string $directory): string => $root . '/' . $directory,
                ['src', 'tests', 'bin', 'decisions', 'requirements', 'todo', 'documentation', 'scenarios', 'skills', 'knowledge', 'feedback'],
            ),
            is_dir(...),
        ));
        if ($directories !== []) {
            foreach (Finder::create()->files()->in($directories)->sortByName() as $file) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Every class of this package, which is every PHP file below src/ except
     * the ones that are deliberately not classes: the bootstrap, and the probe
     * that runs inside somebody else's installation.
     *
     * @return array<int, string>
     */
    private static function sources(): array
    {
        $sources = [];
        $files = Finder::create()->files()->in(dirname(__DIR__, 2) . '/src')->name('*.php')
            ->notName('bootstrap.php')->notName('probe.php')->sortByName();
        foreach ($files as $file) {
            $sources[] = $file->getPathname();
        }

        return $sources;
    }
    /**
     * Retrieval is lexical, and nothing here speaks to a database.
     *
     * `D-ANS-003` decided both halves and nothing held either: an embedding
     * library would arrive as a dependency, and a generic SQL or schema tool
     * would arrive as a connection opened in this process. What the entry rests
     * on is that version, scope, binding and source decide what may be
     * returned, which a semantic match cannot see.
     *
     * The dependency list is asserted whole rather than searched for names a
     * library might have. A sixth one is a deliberate act, and this is where it
     * is read against the entry.
     */
    #[Decision('D-ANS-003')]
    #[Test]
    public function retrievalIsLexicalAndNothingHereOpensADatabase(): void
    {
        $manifest = json_decode((string) file_get_contents(Paths::root() . '/composer.json'), true);
        self::assertIsArray($manifest);
        self::assertSame(
            ['php', 'ext-curl', 'ext-dom', 'mcp/sdk', 'symfony/finder', 'symfony/yaml'],
            array_keys($manifest['require']),
            'the runtime dependencies changed, and D-ANS-003 is what a retrieval library would be added against',
        );

        foreach (Finder::create()->files()->in(Paths::root() . '/src')->name('*.php') as $file) {
            self::assertDoesNotMatchRegularExpression(
                '/new PDO\b|mysqli_|Doctrine\\DBAL/',
                (string) file_get_contents($file->getPathname()),
                $file->getFilename() . ' opens a database connection, which is the tool D-ANS-003 declined',
            );
        }
    }

}
