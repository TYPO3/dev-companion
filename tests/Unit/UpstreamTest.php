<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use Mcp\Schema\Content\TextContent;
use Mcp\Server\ClientGateway;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Http\Fetch;
use TYPO3\DevCompanion\Sdk\ToolHandler;
use TYPO3\DevCompanion\Server\Upstream;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Directory;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * The server is a rolling release, and a checkout learned of nothing upstream
 * by itself. The start asks once, and every answer says when it is behind —
 * `D-DIS-031`.
 */
#[Decision('D-DIS-031')]
final class UpstreamTest extends TestCase
{
    private const REVISION = '1111111111111111111111111111111111111111';

    private string $root = '';

    #[Before]
    public function layOutACheckout(): void
    {
        $this->root = sys_get_temp_dir() . '/typo3-dev-companion-upstream-' . bin2hex(random_bytes(6));
        self::assertTrue(mkdir($this->root . '/.git/refs/heads', 0o777, true));
        file_put_contents($this->root . '/.git/HEAD', "ref: refs/heads/main\n");
        file_put_contents($this->root . '/.git/refs/heads/main', self::REVISION . "\n");
        Upstream::useRoot($this->root);
        Upstream::useDirectory($this->root);
        putenv(Upstream::VARIABLE);
    }

    #[After]
    public function forgetTheUpstream(): void
    {
        Upstream::forget();
        putenv(Upstream::VARIABLE);
        Directory::remove($this->root);
    }

    #[Test]
    public function aCheckoutBehindItsUpstreamSaysSoInEveryAnswer(): void
    {
        $asked = [];
        Upstream::useFetch($this->github(['status' => 'behind', 'ahead_by' => 3, 'behind_by' => 0], $asked));

        Upstream::check();

        self::assertSame(
            ['https://api.github.com/repos/TYPO3/dev-companion/compare/' . self::REVISION . '...main'],
            $asked,
        );
        self::assertSame('behind', Upstream::report()['state']);
        self::assertStringContainsString('3 commits behind github.com/TYPO3/dev-companion', Upstream::notice());
        self::assertStringContainsString('git -C ' . $this->root . ' pull', Upstream::notice());
        self::assertStringStartsWith('This server\'s checkout is 3 commits behind', $this->answer());

        $scope = Registry::call('typo3_server_scope', ['sections' => ['upstream']])->data['upstream'];
        self::assertSame(['behind', self::REVISION, 3], [$scope['state'], $scope['revision'], $scope['behind']]);
    }

    #[Test]
    public function theKeptAnswerSparesTheNextStartItsRead(): void
    {
        $asked = [];
        Upstream::useFetch($this->github(['status' => 'identical', 'ahead_by' => 0, 'behind_by' => 0], $asked));

        Upstream::check();
        Upstream::check();

        self::assertCount(1, $asked, 'a second start within the hour asked again');
        self::assertSame('current', Upstream::report()['state']);
        self::assertSame('', Upstream::notice());
    }

    /** No answer is not "up to date", and it does not fill every answer with a warning either. */
    #[Test]
    public function aFailedReadSaysSoAndClaimsNothing(): void
    {
        $asked = [];
        Upstream::useFetch(new Fetch(static function (string $url) use (&$asked): ?string {
            $asked[] = $url;

            return null;
        }));

        Upstream::check();

        self::assertSame('unavailable', Upstream::report()['state']);
        self::assertNull(Upstream::report()['lastAnswered']);
        self::assertSame('', Upstream::notice());
    }

    #[Test]
    public function theVariableTurnsTheReadOff(): void
    {
        putenv(Upstream::VARIABLE . '=off');
        $asked = [];
        Upstream::useFetch($this->github(['ahead_by' => 3], $asked));

        Upstream::check();

        self::assertSame([], $asked);
        self::assertSame('off', Upstream::report()['state']);
    }

    #[Test]
    public function anInstallWithoutAGitDirectoryIsNoCheckout(): void
    {
        Directory::remove($this->root . '/.git');
        $asked = [];
        Upstream::useFetch($this->github(['ahead_by' => 3], $asked));

        Upstream::check();

        self::assertSame([], $asked);
        self::assertSame('not-a-checkout', Upstream::report()['state']);
    }

    /**
     * @param array<string, mixed> $compare
     * @param list<string> $asked
     */
    private function github(array $compare, array &$asked): Fetch
    {
        return new Fetch(static function (string $url) use ($compare, &$asked): string {
            $asked[] = $url;

            return (string) json_encode($compare);
        });
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
