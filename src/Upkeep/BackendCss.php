<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * The compiled backend stylesheet of one core checkout, read as relationships.
 *
 * The core commits this file on every branch. So where a class sits comes from
 * a read rather than an install, and one pass reads all four covered majors.
 * What a selector proves is that the core styles that combination. That is
 * strong evidence of intent and not a promise, which is why `D-CAT-008` keeps
 * the wording of the answer apart from the derivation.
 */
final class BackendCss
{
    private const PATH = '/typo3/sysext/backend/Resources/Public/Css/backend.css';

    /** @var list<string> */
    private array $selectors;

    /** @var array<string, list<list<string>>> the compounds of every selector naming one class */
    private array $compounds = [];

    public function __construct(string $css)
    {
        $this->selectors = self::split($css);
    }

    /** Null where the checkout ships no compiled stylesheet, which 12.4 and older do. */
    public static function of(string $checkout): ?self
    {
        $path = rtrim($checkout, '/') . self::PATH;

        return is_file($path) ? new self((string) file_get_contents($path)) : null;
    }

    /** @return list<string> */
    public function selectors(): array
    {
        return $this->selectors;
    }

    public function carries(string $class): bool
    {
        return $this->mentioning($class) !== [];
    }

    /**
     * Where `$class` sits relative to `$root`. `around` where a selector makes
     * it an ancestor of the root. `below` where it makes it a descendant. `on`
     * where the two stand on one element. Null where no selector places them
     * together, which is the honest answer for a modifier that carries no
     * position to get wrong.
     */
    public function position(string $class, string $root): ?string
    {
        $found = null;
        foreach ($this->mentioning($class) as $units) {
            $count = count($units);
            foreach ($units as $index => $unit) {
                if (!self::compoundHas($unit, '.' . $class)) {
                    continue;
                }
                if (self::compoundHas($unit, '.' . $root)) {
                    return 'on';
                }
                for ($i = $index + 1; $i < $count; $i++) {
                    if (self::compoundHas($units[$i], '.' . $root)) {
                        $found = 'around';
                    }
                }
                for ($i = 0; $i < $index; $i++) {
                    if (self::compoundHas($units[$i], '.' . $root)) {
                        $found ??= 'below';
                    }
                }
            }
        }

        return $found;
    }

    /**
     * What the core styles beneath `$class` out of the names handed in: the
     * inventory a wrapper may hold rather than the structure it is. Limited to
     * names the caller already knows, because every other descendant selector
     * in the file is an implementation detail nobody asked about.
     *
     * @param list<string> $known what to look for: a class with its dot, an element tag without
     * @return list<string>
     */
    public function stylesWithin(string $class, array $known): array
    {
        $within = [];
        foreach ($this->mentioning($class) as $units) {
            $count = count($units);
            foreach ($units as $index => $unit) {
                if (!self::compoundHas($unit, '.' . $class)) {
                    continue;
                }
                for ($i = $index + 1; $i < $count; $i++) {
                    foreach ($known as $name) {
                        if ($name !== '.' . $class && self::compoundHas($units[$i], $name)) {
                            $within[$name] = true;
                        }
                    }
                }
            }
        }
        ksort($within);

        return array_keys($within);
    }

    /** @return list<list<string>> */
    private function mentioning(string $class): array
    {
        if (isset($this->compounds[$class])) {
            return $this->compounds[$class];
        }

        $pattern = '/\.' . preg_quote($class, '/') . '(?![\w-])/';
        $found = [];
        foreach ($this->selectors as $selector) {
            if (preg_match($pattern, $selector)) {
                $found[] = self::units($selector);
            }
        }

        return $this->compounds[$class] = $found;
    }

    /** @return list<string> */
    private static function split(string $css): array
    {
        $css = (string) preg_replace('#/\*.*?\*/#s', '', $css);
        $out = [];
        $head = '';
        $length = strlen($css);
        for ($i = 0; $i < $length; $i++) {
            $character = $css[$i];
            if ($character === '}') {
                $head = '';
                continue;
            }
            if ($character !== '{') {
                $head .= $character;
                continue;
            }
            $selector = trim($head);
            $head = '';
            if ($selector === '' || $selector[0] === '@') {
                // an at-rule: its own block holds the selectors, so read on
                continue;
            }
            foreach (explode(',', $selector) as $one) {
                $one = trim((string) preg_replace('/\s+/', ' ', $one));
                if ($one !== '') {
                    $out[$one] = true;
                }
            }
            // Skip the declarations, which hold no selector. The depth is
            // tested first so that the close of the block leaves $i on the
            // brace: an increment past it here and again in the loop below
            // swallows the first character of the next selector, which a
            // stylesheet written on one line is entirely made of.
            $depth = 1;
            while ($depth > 0 && ++$i < $length) {
                $depth += match ($css[$i]) {
                    '{' => 1,
                    '}' => -1,
                    default => 0,
                };
            }
        }

        return array_keys($out);
    }

    /**
     * The compounds of a selector, in order. The combinator between them goes.
     * A child and a descendant place a class the same way, and the difference
     * is not one an answer acts on.
     *
     * @return list<string>
     */
    private static function units(string $selector): array
    {
        $spaced = (string) preg_replace('/\s*([>+~])\s*/', ' ', $selector);

        return array_values(array_filter(preg_split('/\s+/', trim($spaced)) ?: []));
    }

    /**
     * Whether one compound carries `$name`, which is a class where it stands
     * with its dot and an element tag where it does not. The caller says which,
     * because `table-fit` and `typo3-backend-icon` are told apart by nothing in
     * the name itself.
     */
    private static function compoundHas(string $compound, string $name): bool
    {
        $pattern = str_starts_with($name, '.')
            ? '/\.' . preg_quote(substr($name, 1), '/') . '(?![\w-])/'
            : '/(^|[^\w.#-])' . preg_quote($name, '/') . '(?![\w-])/';

        return (bool) preg_match($pattern, $compound);
    }
}
