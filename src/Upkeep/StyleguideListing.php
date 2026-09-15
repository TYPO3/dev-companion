<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;

/**
 * The components the styleguide of one core checkout lists.
 *
 * What the styleguide lists is public API, and nobody may use or suggest what
 * it does not list. The maintainer, 2026-08-24. So this is a boundary rather
 * than a demo, and it comes out of the checkout like everything else. The
 * controller offers one action per component, and that list is the whole of it.
 *
 * Whether a demo shows a single class is not readable here. A demo renders its
 * markup through a ViewHelper or a web component as often as it writes it,
 * `<be:avatar>` spells no avatar class at all. What the template does write is
 * as often the styleguide's own page furniture, such as the `indicators-grid`
 * that lays out the status indicators. Read on 2026-08-24: that question
 * belongs to a rendered styleguide.
 *
 * The styleguide is a system extension from 13.4 and an installable package
 * before it, so a checkout that ships none answers nothing here.
 */
final class StyleguideListing
{
    private const CONTROLLER = '/typo3/sysext/styleguide/Classes/Controller/ComponentsController.php';

    /**
     * Templates and the partials they render, which is where a demo's markup
     * stands.
     */
    private const TEMPLATES = '/typo3/sysext/styleguide/Resources/Private';

    /** The action that lists the rest, which is the page and not a component. */
    private const OVERVIEW = 'componentsOverview';

    /** @var list<string> */
    private array $components;

    /** @param list<string> $templates the demo templates, as they are written */
    public function __construct(string $controller, private readonly array $templates = [])
    {
        $this->components = self::listed($controller);
    }

    /** Null where the checkout ships no styleguide, which 12.4 and older do not. */
    public static function of(string $checkout): ?self
    {
        $root = rtrim($checkout, '/');
        $path = $root . self::CONTROLLER;
        if (!is_file($path)) {
            return null;
        }

        $templates = [];
        $directory = $root . self::TEMPLATES;
        if (is_dir($directory)) {
            foreach (Finder::create()->files()->in($directory)->name('*.html') as $file) {
                $templates[] = $file->getContents();
            }
        }

        return new self((string) file_get_contents($path), $templates);
    }

    /**
     * Whether a demo writes this element.
     *
     * A tag survives a read out of a template where a class name does not. A
     * demo loops over the variants it assigns and builds `badge-{variant}`, and
     * it never builds a tag name the same way. Read on 2026-08-24, this finds
     * 13 of the 137 the core declares — `D-CAT-009`.
     */
    public function demonstrates(string $tag): bool
    {
        // The tag has to end where the name ends, or `content-navigation`
        // reads `content-navigation-toggle` as itself; and what follows it is
        // whitespace as often as `>`, because a demo puts the attributes on the
        // next line.
        $written = '/<' . preg_quote($tag, '/') . '(?=[\s>\/])/';
        foreach ($this->templates as $template) {
            if (preg_match($written, $template) === 1) {
                return true;
            }
        }

        return false;
    }

    /** @return list<string> */
    public function components(): array
    {
        return $this->components;
    }

    public function lists(string $component): bool
    {
        return in_array($component, $this->components, true);
    }

    /** @return list<string> */
    private static function listed(string $controller): array
    {
        $matches = [];
        if (!preg_match('/\$allowedActions\s*=\s*\[(.*?)\];/s', $controller, $matches)) {
            return [];
        }
        $names = [];
        if (!preg_match_all("/'([a-zA-Z]+)'/", $matches[1], $names)) {
            return [];
        }

        return array_values(array_diff($names[1], [self::OVERVIEW]));
    }
}
