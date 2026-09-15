<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

/**
 * The pages published, written out as the source a site generator publishes.
 *
 * The site is `documentation/` and nothing besides. So the front page is that
 * directory's own page and the repository's `readme.md` stays out of the site,
 * `D-DOC-026`. Every link that leaves the published pages changes here to the
 * file on GitHub, a literal block included. The sources keep the paths a reader
 * of the checkout follows, which is what `links:check` goes on to read. The
 * copy carries the renames a generator needs. `readme.rst` is what this
 * repository calls a directory's own page, `index.rst` is what a generator
 * publishes as the directory itself.
 */
final class Site
{
    /** The directory published, relative to the root. */
    public const SOURCE = 'documentation';

    /**
     * The renderer's own configuration, which sits with the corpus it
     * configures and is the one file in there that is not a page —
     * `D-DOC-027`.
     */
    private const CONFIG = 'guides.xml';

    /**
     * Where the site build lands, and what `guides.xml` names as its `input`
     * and its `output`. Gitignored, because a build product in the repository
     * is one somebody edits.
     */
    public const ROOT = '.site';
    public const TARGET = self::ROOT . '/source';
    public const HTML = self::ROOT . '/html';

    /** The name of a directory's own page here, and its name on the site. */
    private const OWN_PAGE = 'readme.rst';
    private const PUBLISHED_PAGE = 'index.rst';

    /** The branch a link that leaves the published tree points into. */
    private const BRANCH = 'main';

    /** Where the drawings sit, in the checkout and in the published site alike. */
    public const DRAWINGS = 'images';

    private static ?string $repository = null;

    /**
     * Writes the copy, and takes back out of it whatever this no longer writes.
     *
     * The strangers go rather than the directory. A build that starts with the
     * deletion of a path it received is one bad argument away from the deletion
     * of something else.
     *
     * @return array{written: list<string>, removed: list<string>}
     */
    public static function build(string $target): array
    {
        $target = str_starts_with($target, '/') ? $target : Paths::root() . '/' . $target;

        $written = [];
        foreach (self::sources() as $source) {
            $published = self::published($source);
            $contents = (string) file_get_contents(Paths::root() . '/' . $source);
            self::write(
                $target . '/' . $published,
                str_ends_with($source, '.rst') ? self::page($source, $contents) : $contents,
            );
            $written[$published] = true;
        }
        foreach (SkillSurface::files() as $file => $contents) {
            $source = substr($file, strlen(Paths::root()) + 1);
            $published = self::published($source);
            self::write(
                $target . '/' . $published,
                str_ends_with($source, '.rst') ? self::page($source, $contents) : $contents,
            );
            $written[$published] = true;
        }

        return ['written' => array_keys($written), 'removed' => self::sweep($target, $written)];
    }

    /**
     * Every file the site consists of, named as this repository names it.
     *
     * @return list<string>
     */
    private static function sources(): array
    {
        $sources = [];
        $files = Finder::create()->files()->in(Paths::root() . '/' . self::SOURCE)->notName(self::CONFIG)->sortByName();
        foreach ($files as $file) {
            $sources[] = self::SOURCE . '/' . str_replace('\\', '/', $file->getRelativePathname());
        }

        return $sources;
    }

    /**
     * Takes everything below a build directory away, and leaves the directory.
     *
     * The renderer writes over what is there and removes nothing, so a page
     * renamed or deleted since the last render stays on the site. The theme's
     * finish step read those stale pages for two minutes at full tilt before
     * this existed.
     */
    public static function clear(string $target): void
    {
        $target = str_starts_with($target, '/') ? $target : Paths::root() . '/' . $target;
        if (is_dir($target)) {
            self::sweep($target, []);
        }
    }

    /**
     * Every file the copy comes from, and when each last changed.
     *
     * Two of these compared is what a watch goes on. A file has the path this
     * repository knows it by, and its value moves on a save and on a save to a
     * different length. So two saves within one second still read as two. The
     * skills are in it because the copy carries them.
     *
     * @return array<string, string>
     */
    public static function stamps(): array
    {
        $stamps = [];
        $files = Finder::create()->files()->in([Paths::root() . '/' . self::SOURCE, Paths::root() . '/skills'])->sortByName();
        foreach ($files as $file) {
            $path = substr($file->getPathname(), strlen(Paths::root()) + 1);
            $stamps[$path] = $file->getMTime() . '.' . $file->getSize();
        }

        return $stamps;
    }

    /**
     * One page as the site shows it. Every link that leaves the tree turned
     * into the file on GitHub, and everything else exactly as it stands.
     *
     * There is only the one case now. A link inside the corpus is a `:doc:` or
     * a `:ref:`, which the renderer resolves against the document tree itself.
     * So nothing here has to know a page's name on the site or how many
     * directories up it sits. What remains is the links that name something
     * this site does not serve, a decision, a todo, a class. Every one of those
     * is somewhere on GitHub.
     *
     * That nobody can confuse the two is the convention `SiteTest` holds. An
     * embedded link that names a published page would turn into GitHub here and
     * send the reader out of the site.
     */
    public static function page(string $file, string $rst): string
    {
        $directory = dirname($file);

        return Links::rewritten($rst, static function (string $target) use ($directory): string {
            $path = strtok($target, '#');
            if ($path === false) {
                return $target;
            }
            $fragment = substr($target, strlen($path));
            $resolved = self::resolve($directory, $path);

            // A directory and a file are two paths on GitHub, and half the
            // entries this documentation points at are directories.
            return sprintf(
                '%s/%s/%s/%s%s',
                self::repository(),
                is_dir(Paths::root() . '/' . $resolved) ? 'tree' : 'blob',
                self::BRANCH,
                $resolved,
                $fragment,
            );
        });
    }

    /**
     * The name of a path in this repository on the site.
     *
     * `documentation/` sits at the root of the site, so a link against the
     * checkout points at a page a segment higher than it reads. Every target
     * resolves against the repository and takes its name again from there,
     * rather than a swap of the last segment in place.
     */
    public static function published(string $path): string
    {
        if ($path === self::SOURCE) {
            return self::PUBLISHED_PAGE;
        }

        return (string) preg_replace(
            '#(^|/)' . preg_quote(self::OWN_PAGE, '#') . '$#',
            '$1' . self::PUBLISHED_PAGE,
            substr($path, strlen(self::SOURCE) + 1),
        );
    }

    /**
     * Where the sources live, said once, in the manifest that already declares
     * the package to everybody else. A repository that moves moves in one line
     * and every link this writes follows it.
     */
    public static function repository(): string
    {
        if (self::$repository !== null) {
            return self::$repository;
        }

        $manifest = json_decode(
            (string) file_get_contents(Paths::root() . '/composer.json'),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        $source = is_array($manifest) && is_array($manifest['support'] ?? null) ? $manifest['support']['source'] ?? null : null;
        if (!is_string($source) || $source === '') {
            throw new \RuntimeException('composer.json declares no support.source, so nothing says where the published links point.');
        }

        return self::$repository = rtrim($source, '/');
    }

    /**
     * What stands in the target that this build did not write, taken back out.
     * A renamed or deleted page otherwise stays on the site for as long as the
     * directory stays.
     *
     * @param array<string, true> $written
     *
     * @return list<string>
     */
    private static function sweep(string $target, array $written): array
    {
        $removed = [];
        foreach (Finder::create()->files()->in($target)->ignoreDotFiles(false)->sortByName() as $file) {
            $found = str_replace('\\', '/', $file->getRelativePathname());
            if (!isset($written[$found])) {
                unlink($file->getPathname());
                $removed[] = $found;
            }
        }

        // Deepest first, so a directory emptied by the pass above is gone
        // before the one that holds it comes up.
        $directories = Finder::create()->directories()->in($target)->ignoreDotFiles(false)->reverseSorting();
        foreach ($directories as $directory) {
            if (!Finder::create()->in($directory->getPathname())->ignoreDotFiles(false)->hasResults()) {
                rmdir($directory->getPathname());
            }
        }

        return $removed;
    }

    /** Where a link written in one directory of this repository points, from the root. */
    private static function resolve(string $directory, string $path): string
    {
        $segments = [];
        foreach (explode('/', $directory . '/' . $path) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }
            if ($segment === '..') {
                array_pop($segments);
                continue;
            }
            $segments[] = $segment;
        }

        return implode('/', $segments);
    }

    private static function write(string $path, string $contents): void
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($path, $contents);
    }
}
