<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Server\PhpExtensions;
use TYPO3\DevCompanion\Tests\Support\Decision;

#[Decision('D-ANS-176')]
final class PhpExtensionsTest extends TestCase
{
    #[After]
    public function forgetTheSeam(): void
    {
        PhpExtensions::useLoaded(null);
    }

    #[Test]
    public function everyExtensionComposerJsonRequiresIsChecked(): void
    {
        $manifest = json_decode((string) file_get_contents(Paths::root() . '/composer.json'), true);
        self::assertIsArray($manifest);
        $required = array_values(array_filter(
            array_keys($manifest['require']),
            static fn(int|string $key): bool => str_starts_with((string) $key, 'ext-'),
        ));
        PhpExtensions::useLoaded(static fn(): bool => false);

        self::assertSame($required, array_map(static fn(string $name): string => 'ext-' . $name, PhpExtensions::missing()));
    }

    #[Test]
    public function aMissingExtensionIsRefusedWithItsNameAndThePhp(): void
    {
        PhpExtensions::useLoaded(static fn(string $name): bool => $name !== 'curl');

        self::assertSame(['curl'], PhpExtensions::missing());
        $refusal = (string) PhpExtensions::refusal();
        self::assertStringContainsString('lacks ext-curl, which composer.json requires', $refusal);
        self::assertStringContainsString(PHP_BINARY . ' (' . PHP_VERSION . ')', $refusal);
    }

    #[Test]
    public function aPhpWithEveryExtensionIsNotRefused(): void
    {
        PhpExtensions::useLoaded(static fn(): bool => true);

        self::assertNull(PhpExtensions::refusal());
    }
}
