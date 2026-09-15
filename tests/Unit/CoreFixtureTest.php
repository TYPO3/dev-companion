<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Installation\Icons;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Tool\Source;
use TYPO3\DevCompanion\Upkeep\CoreFixture;
use TYPO3\DevCompanion\Upkeep\ToolCalls;

/**
 * The core checkout this repository writes, and the set of answers it may
 * stand in for.
 *
 * Eight tools have their answered half derived from this root rather than
 * recorded from somebody's checkout, which is what puts those pages inside
 * `bin/cli tools:check`. That is only sound while their answers really do not
 * move with what a root contains, and a wrong one costs in a quiet way. The
 * page still says "derived and checked" while it shows one installation's
 * content as everybody's answer.
 *
 * So this measures the property rather than trusts it. Two roots declare the
 * same identity, the core monorepo on one TYPO3 major, and agree on nothing
 * else. No shared packages, no shared changelog, a different patch level. What
 * answers the same from both reads neither.
 */
final class CoreFixtureTest extends TestCase
{
    private ?string $variant = null;

    #[After]
    public function forgetTheInstanceAndTheVariant(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();
        Typo3Runtime::forget();
        Icons::forget();

        if ($this->variant !== null && is_dir($this->variant)) {
            $entries = iterator_to_array(Finder::create()->in($this->variant)->sortByName()->reverseSorting(), false);
            foreach ($entries as $entry) {
                $entry->isDir() ? rmdir($entry->getPathname()) : unlink($entry->getPathname());
            }
            rmdir($this->variant);
        }
        $this->variant = null;
    }

    /**
     * What the whole derivation rests on: this root reads as the core monorepo
     * and says which TYPO3 it is. Without the first every answer that places a
     * path comes back `uncertain`, and without the second every one bound to a
     * version comes back unbound. Nobody would notice either, because a wrong
     * answer here is still a well-formed one — `D-DOC-016`.
     */
    #[Decision('D-DOC-016')]
    #[Test]
    public function theWrittenCheckoutIsReadAsOneAndSaysWhichTypo3ItIs(): void
    {
        Instance::discoverFrom(CoreFixture::write());

        self::assertSame(Instance::KIND_CORE_CHECKOUT, Instance::describe()['kind'] ?? null);
        self::assertSame(CoreFixture::typo3Version(), Instance::typo3Version());
    }

    /**
     * Both directions, because the list fails in both.
     *
     * A tool in it whose answers move is a checked page that asserts one root's
     * content. A tool outside it whose answers do not move is a record nobody
     * needs. Two kinds are outside the question rather than outside the set.
     * One that reaches a host answers from something no root here holds
     * (`D-DOC-008`), and the test does not drive it at all. One that answers
     * from the installation turns on a console neither root has. So the two
     * agree for a reason that says nothing about a caller with a booted TYPO3 —
     * `D-DOC-016`.
     */
    #[Decision('D-DOC-016')]
    #[Test]
    public function everyAnswerThatDoesNotMoveWithARootIsDerivedFromOne(): void
    {
        $derived = ToolCalls::derived();

        foreach ($this->answersFromBothRoots() as $name => $calls) {
            $moved = array_keys(array_filter($calls, static fn(bool $same): bool => !$same));

            if (in_array($name, $derived, true)) {
                self::assertSame(
                    [],
                    $moved,
                    $name . ' is derived and its answer moved with the root on: ' . implode(', ', $moved),
                );

                continue;
            }

            if ($this->answersFrom($name, Source::Installation)) {
                continue;
            }

            self::assertNotSame(
                [],
                $moved,
                $name . ' answers the same from any root and is recorded anyway — add it to ToolCalls::derived()',
            );
        }
    }

    /**
     * Every driven call answered from each root, as whether the two agreed.
     *
     * @return array<string, array<string, bool>>
     */
    private function answersFromBothRoots(): array
    {
        $answers = ['a' => [], 'b' => []];
        foreach (['a' => CoreFixture::write(), 'b' => $this->variantRoot()] as $side => $root) {
            Instance::discoverFrom($root);
            Typo3Cli::forget();
            Typo3Runtime::forget();
            Icons::forget();

            foreach (ToolCalls::all() as $label => [$name, $arguments]) {
                // A tool that reaches a host is not driven at all here. It is
                // outside the question, because what it answers comes from
                // something no root holds. A call would put this test's
                // requests on somebody else's service twice per root.
                if ($this->answersFrom($name, Source::Network)) {
                    continue;
                }
                $result = Registry::call($name, $arguments);
                $answers[$side][$name][$label] = $result->text . "\n" . json_encode($result->data);
            }
        }

        $compared = [];
        foreach ($answers['a'] as $name => $calls) {
            foreach ($calls as $label => $answer) {
                $compared[$name][$label] = $answer === $answers['b'][$name][$label];
            }
        }

        return $compared;
    }

    /** Whether a tool declares any of these as a source it can answer from. */
    private function answersFrom(string $name, Source ...$sources): bool
    {
        foreach (Registry::definitions() as $definition) {
            if ($definition['name'] === $name) {
                foreach ($sources as $source) {
                    if (in_array($source->value, $definition['answersFrom'], true)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * The other root of the same identity: the core monorepo on the same major,
     * with a different patch level. It has a package, a stylesheet and a
     * changelog the written one has none of.
     *
     * Something reads each thing it holds. A tool whose answer is the same from
     * both because neither root has what it looks for would pass this by
     * accident. That is the one way a measurement like this comes out green for
     * no reason.
     *
     * The path names this process. Every other worktree that runs `composer ci`
     * at that moment shares a fixed name below `sys_get_temp_dir()`. Each of
     * them removes the root the others read — `D-COD-006`.
     */
    private function variantRoot(): string
    {
        $root = sys_get_temp_dir() . '/typo3-dev-companion-core-variant-' . getmypid() . '-' . bin2hex(random_bytes(6));
        $core = $root . '/typo3/sysext/core';
        $backend = $root . '/typo3/sysext/backend';
        $major = explode('.', CoreFixture::typo3Version())[0];

        $write = static function (string $path, string $contents): void {
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0o777, true);
            }
            file_put_contents($path, $contents);
        };
        $manifest = static fn(string $name, string $key): string => (string) json_encode([
            'name' => $name,
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => $key]],
        ]);

        $write($root . '/composer.json', (string) json_encode(['name' => 'typo3/cms', 'type' => 'typo3-cms-core']));
        $write($core . '/composer.json', $manifest('typo3/cms-core', 'core'));
        $write(
            $core . '/Classes/Information/Typo3Version.php',
            "<?php\n\nnamespace TYPO3\\CMS\\Core\\Information;\n\nclass Typo3Version\n{\n"
            . "    protected const VERSION = '" . $major . ".99.99';\n}\n",
        );
        $write($backend . '/composer.json', $manifest('typo3/cms-backend', 'backend'));
        $write(
            $backend . '/Configuration/Icons.php',
            "<?php\n\nreturn ['variant-icon' => ['source' => 'EXT:backend/Resources/Public/Icons/variant.svg']];\n",
        );
        $write($backend . '/Resources/Public/Css/backend.css', ".variant-class { --variant-property: 0; }\n");
        $write(
            $core . '/Documentation/Changelog/' . CoreFixture::typo3Version() . '/Breaking-900009-AVariantEntry.rst',
            ".. include:: /Includes.rst.txt\n\n=====================================\n"
            . "Breaking: #900009 - A variant entry\n=====================================\n\n"
            . "Description\n===========\n\nOnly this root has it.\n\n.. index:: PHP-API\n",
        );

        return $this->variant = $root;
    }
}
