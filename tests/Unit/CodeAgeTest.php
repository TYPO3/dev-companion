<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use Mcp\Schema\Content\TextContent;
use Mcp\Server\ClientGateway;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Sdk\ToolHandler;
use TYPO3\DevCompanion\Server\CodeAge;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Directory;

/**
 * A guide named a tool the running process did not serve, because the checkout
 * had moved after the start, and nothing said so — `D-DIS-030`.
 */
#[Decision('D-DIS-030')]
final class CodeAgeTest extends TestCase
{
    private string $root = '';

    #[After]
    public function forgetTheStart(): void
    {
        CodeAge::forget();
        if ($this->root !== '') {
            Directory::remove($this->root);
        }
    }

    #[Test]
    public function aCheckoutThatMovedAfterTheStartMakesEveryAnswerSaySo(): void
    {
        $this->root = sys_get_temp_dir() . '/typo3-dev-companion-age-' . bin2hex(random_bytes(6));
        self::assertTrue(mkdir($this->root . '/src', 0o777, true));
        file_put_contents($this->root . '/src/Tool.php', "<?php\n");
        CodeAge::useRoot($this->root);
        CodeAge::markStart();

        self::assertFalse(CodeAge::isStale());
        $text = $this->answer();
        self::assertStringStartsNotWith(CodeAge::NOTICE, $text);

        file_put_contents($this->root . '/src/NewTool.php', "<?php\n");

        self::assertTrue(CodeAge::isStale());
        self::assertStringStartsWith(CodeAge::NOTICE, $this->answer());
    }

    #[Test]
    public function aProcessThatMarkedNoStartIsNeverStale(): void
    {
        self::assertFalse(CodeAge::isStale());
    }

    private function answer(): string
    {
        $result = (new ToolHandler('typo3_server_scope'))->execute(
            ['sections' => ['versions']],
            self::createStub(ClientGateway::class),
        );
        $content = $result->content[0];
        self::assertInstanceOf(TextContent::class, $content);

        return $content->text;
    }
}
