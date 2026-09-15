<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Directory;
use TYPO3\DevCompanion\Upkeep\Command\DocumentationPrepare;

/**
 * `bin/cli documentation:prepare` writes the copy and reaches nothing else.
 *
 * That is the whole of what this repository does to a render — `D-DOC-028`. So
 * the case that matters is that one call needs no renderer, no theme and no
 * network to leave a source tree behind.
 *
 * The directory is an argument for the same reason as before: the suite drives
 * the command and writes nothing into the `.site` of this checkout.
 */
final class DocumentationPrepareTest extends TestCase
{
    private string $into = '';

    protected function setUp(): void
    {
        $this->into = sys_get_temp_dir() . '/dev-companion-prepare-' . getmypid();
    }

    protected function tearDown(): void
    {
        Directory::remove($this->into);
    }

    /**
     * One call writes the whole copy a renderer reads, and says where it went,
     * so nothing else here touches a render — `D-DOC-028`.
     */
    #[Decision('D-DOC-028')]
    #[Test]
    public function oneCallWritesTheCopyAndSaysWhereItWent(): void
    {
        $output = new BufferedOutput();

        self::assertSame(0, (new DocumentationPrepare())($output, $this->into));
        self::assertFileExists($this->into . '/source/index.rst');
        self::assertFileExists($this->into . '/source/server/tools/index.rst');
        self::assertFileExists($this->into . '/source/usage/task-skills/typo3-extension-testing/index.rst');
        self::assertFileEquals(
            dirname(__DIR__, 2) . '/skills/typo3-extension-testing/SKILL.md',
            $this->into . '/source/usage/task-skills/typo3-extension-testing/SKILL.md',
        );
        self::assertStringContainsString($this->into . '/source — ', $output->fetch());
    }

    /**
     * A page the documentation no longer has leaves the copy — `D-DOC-028`.
 */
    #[Decision('D-DOC-028')]
    #[Test]
    public function whatTheDocumentationNoLongerHasIsReported(): void
    {
        (new DocumentationPrepare())(new BufferedOutput(), $this->into);
        file_put_contents($this->into . '/source/gone.rst', "Gone\n====\n");

        $output = new BufferedOutput();
        (new DocumentationPrepare())($output, $this->into);

        self::assertStringContainsString('removed gone.rst', $output->fetch());
        self::assertFileDoesNotExist($this->into . '/source/gone.rst');
    }
}
