<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Directory;
use TYPO3\DevCompanion\Upkeep\PinnedPackage;

/**
 * Which release of a package the core pins a statement about it was read in.
 *
 * Each of them releases on its own cycle, so the statements are verified
 * against a tag rather than against a core branch, and which tag that is is
 * derived from the core's own pin — `D-KNW-106`. This holds the one step of
 * that derivation which needs no repository to try; everything else is a git
 * call, and `bin/cli versions:check` is where it is exercised.
 */
final class PinnedPackageTest extends TestCase
{
    #[Decision('D-KNW-106')]
    #[Test]
    public function aPinThatNamesOneReleaseLineIsThatLine(): void
    {
        // The harness pins the four covered branches carry today, and the
        // engine pins beside them: 12.4 reads Fluid 2, 13.4 reads 4, and the
        // two newest read 5.
        self::assertSame('8', PinnedPackage::line('^8.3.1'));
        self::assertSame('9', PinnedPackage::line('^9.2.1'));
        self::assertSame('9', PinnedPackage::line('^9.5.0'));
        self::assertSame('main', PinnedPackage::line('dev-main'));
        self::assertSame('2', PinnedPackage::line('^2.15.0'));
        self::assertSame('4', PinnedPackage::line('^4.6.1'));
        self::assertSame('5', PinnedPackage::line('^5.3.2'));
    }

    #[Decision('D-KNW-106')]
    #[Test]
    public function aPinThatNamesTwoLinesNamesNone(): void
    {
        // The case the check exists for: a core major that admits two harnesses
        // no longer says which one a statement bound to it was read in, and a
        // line picked out of the two would be a guess wearing a version number.
        self::assertNull(PinnedPackage::line('^8.3.1 || ^9.0'));
        self::assertNull(PinnedPackage::line('>=8'));
        self::assertNull(PinnedPackage::line(''));
    }

    #[Decision('D-KNW-146')]
    #[Test]
    public function eachPackageIsReadFromTheSectionTheCorePinsItIn(): void
    {
        // The harness is a development dependency of the core and the engine is
        // a dependency of it, so a pairing that read one section for both would
        // find no pin at all and create no worktree — quietly, because a branch
        // that pins nothing is a state this reports rather than fails on.
        $checkouts = sys_get_temp_dir() . '/typo3-dev-companion-pins-' . bin2hex(random_bytes(6));
        foreach (Versions::covered() as $version) {
            mkdir($checkouts . '/' . $version['branch'], 0o777, true);
            file_put_contents($checkouts . '/' . $version['branch'] . '/composer.json', json_encode([
                'require' => ['typo3fluid/fluid' => 'dev-main'],
                'require-dev' => ['typo3/testing-framework' => 'dev-main'],
            ], JSON_THROW_ON_ERROR));
        }

        foreach (PinnedPackage::all() as $package) {
            foreach ($package->pairing($checkouts) as $pair) {
                self::assertSame('dev-main', $pair['constraint'], $package->package . ' on ' . $pair['branch']);
                self::assertSame('main', $pair['ref']);
                self::assertStringEndsWith('/' . $package->directory . '/main', $pair['path']);
            }
        }

        Directory::remove($checkouts);
    }
}
