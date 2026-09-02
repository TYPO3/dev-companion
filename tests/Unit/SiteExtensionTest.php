<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\SiteExtension;
use TYPO3\DevCompanion\Upkeep\ToolCalls;

/**
 * The extension a made `E-SITE` carries, held to what it is written for.
 *
 * Nothing here starts an installation: what the environment does with these
 * files is minutes of DDEV and is the recording's business. What is held is
 * that the files say what the answer rests on — `D-EVI-010`.
 */
final class SiteExtensionTest extends TestCase
{
    private string $installation = '';

    #[After]
    public function takeTheWrittenFilesBack(): void
    {
        if ($this->installation === '' || !is_dir($this->installation)) {
            return;
        }

        $finder = (new Finder())->in($this->installation)->files();
        foreach ($finder as $file) {
            unlink($file->getPathname());
        }
        foreach (array_reverse(iterator_to_array((new Finder())->in($this->installation)->directories())) as $directory) {
            rmdir($directory->getPathname());
        }
        rmdir($this->installation);
        $this->installation = '';
    }

    /**
     * The one thing the whole answer rests on: a table is this project's
     * because the `EXT:` reference in its ctrl title says so, which is what
     * `Typo3Runtime::extensionIn` reads and the only attribution either side
     * has — `D-EVI-010`.
     */
    #[Decision('D-EVI-010')]
    #[Test]
    public function theTableIsAttributedToTheExtensionByItsCtrlTitle(): void
    {
        $tca = include $this->written() . '/packages/' . SiteExtension::KEY
            . '/Configuration/TCA/' . SiteExtension::TABLE . '.php';

        self::assertIsArray($tca);
        self::assertSame(
            SiteExtension::KEY,
            Typo3Runtime::extensionIn((string) $tca['ctrl']['title']),
            'the table would be read as the installation\'s own',
        );
        self::assertSame('title', $tca['ctrl']['label'], 'a row would come back without a label');
    }

    /**
     * One recorded call is answered from both roots, so the table the fixture
     * registers and the table this environment fills are one table — the
     * environment for real, the fixture as the boundary it holds instead.
     */
    #[Decision('D-EVI-010')]
    #[Test]
    public function theRecordedCallAsksForTheTableThisExtensionRegisters(): void
    {
        $asked = [];
        foreach (ToolCalls::all() as [$name, $arguments]) {
            if ($name === 'typo3_record_lookup' && isset($arguments['table'])) {
                $asked[] = (string) $arguments['table'];
            }
        }

        self::assertContains(
            SiteExtension::TABLE,
            $asked,
            'the recording asks for a table this environment does not have',
        );
    }

    /**
     * The counts are what the tool answers with, so every state they separate
     * has rows in it. A recorded answer whose hidden and deleted are both zero
     * shows two fields nobody can tell from a table that has neither.
     */
    #[Decision('D-EVI-010')]
    #[Test]
    public function everyStateTheCountSeparatesHasRowsInIt(): void
    {
        $seed = SiteExtension::files()['Build/seed.php'];

        foreach (['live' => SiteExtension::LIVE, 'hidden' => SiteExtension::HIDDEN, 'deleted' => SiteExtension::DELETED] as $state => $rows) {
            self::assertGreaterThan(0, $rows, $state . ' is a state the recorded counts cannot show');
            self::assertStringContainsString("['" . $state . "', " . $rows . ']', $seed, $state . ' is never written');
        }
    }

    /** Writes the extension into a directory of its own and hands that back. */
    private function written(): string
    {
        $this->installation = sys_get_temp_dir() . '/typo3-dev-companion-site-extension-' . getmypid();
        SiteExtension::write($this->installation);

        return $this->installation;
    }
}
