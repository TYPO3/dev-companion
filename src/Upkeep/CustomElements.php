<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;

/**
 * The custom elements one core checkout declares, read from its TypeScript.
 *
 * A `@customElement` decorator is the one machine-readable component surface
 * the core has. An element is the half of the answer that cannot attach to the
 * wrong node. That is the whole of what went wrong with a borrowed class name
 * (`D-CAT-008`).
 */
final class CustomElements
{
    private const SOURCES = '/Build/Sources/TypeScript';

    /** @return array<string, string> tag name to the file that declares it, relative to the checkout */
    public static function of(string $checkout): array
    {
        $root = rtrim($checkout, '/');
        $directory = $root . self::SOURCES;
        if (!is_dir($directory)) {
            return [];
        }

        $tags = [];
        foreach (Finder::create()->files()->in($directory)->name('*.ts') as $file) {
            $matches = [];
            if (!preg_match_all("/@customElement\(\s*'([^']+)'/", $file->getContents(), $matches)) {
                continue;
            }
            foreach ($matches[1] as $tag) {
                $tags[$tag] = substr($file->getPathname(), strlen($root) + 1);
            }
        }
        ksort($tags);

        return $tags;
    }
}
