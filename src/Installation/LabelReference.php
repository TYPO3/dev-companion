<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

use Symfony\Component\Finder\Finder;

/** Static source locations that name a label resource. */
final class LabelReference
{
    /** @var array<int, string> */
    private const SOURCE_FILES = [
        '*.php', '*.yaml', '*.yml', '*.typoscript', '*.tsconfig', '*.html',
        '*.xml', '*.js', '*.mjs', '*.cjs', '*.ts', '*.tsx', '*.json',
    ];

    /**
     * @param array<int, array{absolute: string, resource: string, domain: string, implicitReferences: array<int, string>}> $resources
     * @param array<string, string> $packages
     * @return array<string, array<int, string>> Resource to source paths.
     */
    public static function find(array $resources, string $projectRoot, array $packages): array
    {
        $references = [];
        foreach ($resources as $resource) {
            $references[$resource['resource']] = $resource['implicitReferences'];
        }

        $seen = [];
        $roots = array_map(
            static fn(string $path): array => ['path' => $path, 'project' => false],
            array_values($packages),
        );
        $roots[] = ['path' => $projectRoot, 'project' => true];

        foreach ($roots as $root) {
            if (!is_dir($root['path'])) {
                continue;
            }
            $finder = Finder::create()
                ->files()
                ->in($root['path'])
                ->exclude(['.git', '.Build', 'node_modules', 'var'])
                ->name(self::SOURCE_FILES)
                ->sortByName();
            if ($root['project']) {
                $finder->exclude('vendor');
            }

            foreach ($finder as $source) {
                $path = $source->getPathname();
                if (isset($seen[$path]) || $source->getSize() > 2_000_000) {
                    continue;
                }
                $seen[$path] = true;
                $content = file_get_contents($path);
                if (!is_string($content)) {
                    continue;
                }

                foreach ($resources as $resource) {
                    if ($path === $resource['absolute'] || !self::isNamed($content, $resource)) {
                        continue;
                    }
                    $references[$resource['resource']][] = self::displayPath(
                        $path,
                        $projectRoot,
                        $packages,
                    );
                }
            }
        }

        foreach ($resources as $resource) {
            $configuration = dirname($resource['absolute']) . '/config.yaml';
            if (!is_file($configuration)) {
                continue;
            }
            $content = file_get_contents($configuration);
            if (is_string($content) && str_contains($content, basename($resource['absolute']))) {
                $references[$resource['resource']][] = self::displayPath(
                    $configuration,
                    $projectRoot,
                    $packages,
                );
            }
        }

        foreach ($references as &$paths) {
            $paths = array_values(array_unique($paths));
            sort($paths);
        }

        return $references;
    }

    /** @param array{resource: string, domain: string} $resource */
    private static function isNamed(string $content, array $resource): bool
    {
        if (str_contains($content, $resource['resource'])) {
            return true;
        }

        $withoutExtension = preg_replace('/\.xlf$/i', '', $resource['resource']);
        if (is_string($withoutExtension) && str_contains($content, $withoutExtension)) {
            return true;
        }

        return $resource['domain'] !== '' && str_contains($content, $resource['domain']);
    }

    /** @param array<string, string> $packages */
    private static function displayPath(string $path, string $projectRoot, array $packages): string
    {
        foreach ($packages as $key => $package) {
            $prefix = rtrim($package, '/') . '/';
            if (str_starts_with($path, $prefix)) {
                return 'EXT:' . $key . '/' . substr($path, strlen($prefix));
            }
        }

        $prefix = rtrim($projectRoot, '/') . '/';

        return str_starts_with($path, $prefix) ? substr($path, strlen($prefix)) : $path;
    }
}
