<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge;

use TYPO3\DevCompanion\Search\Text;

/**
 * Derives the technical domains a task touches from its paths and description.
 *
 * Used to keep answers inside the domain that was actually asked about: a PHP
 * bugfix should never get a Sass build recommended, and a task that only names
 * PHP paths should not be answered with backend TypeScript conventions.
 *
 * The asset domains are deliberately separate rather than one "frontend": a
 * TypeScript module, a Sass partial, and a Fluid template share a directory
 * tree but not a single convention, test suite, or reviewer. Folding them
 * together made every .ts path pull CSS conventions and every .scss path pull
 * TypeScript ones.
 */
final class Domains
{
    public const PHP = 'php';
    /**
     * TypeScript and the JavaScript it becomes, as one domain.
     *
     * A `.js` file in the backend is the committed output of a `.ts` one, so a
     * path to either detects as this and a hint about either is tagged with it.
     * `javascript` was a category of its own until `D-KNW-034` and never a
     * domain: nothing detected as it and no hint was ever filed under it.
     */
    public const TYPESCRIPT = 'typescript';
    public const TYPOSCRIPT = 'typoscript';
    public const CSS = 'css';
    public const FLUID = 'fluid';
    public const DOCS = 'docs';
    public const XLIFF = 'xliff';

    /**
     * The one a hint may be tagged with and no path ever detects as.
     *
     * `any` is a hint that holds wherever TYPO3 is written and is therefore
     * selected by every query — the bucket every cross-domain hint used to fall
     * into for want of a second tag. Nothing carries it since `D-KNW-033`, and
     * `HintsTest` fails on the first one that does.
     */
    public const ANY = 'any';

    /** @var array<string, array<int, string>> Domain to file extensions. */
    private const EXTENSIONS = [
        self::PHP => ['php', 'yaml', 'yml'],
        self::TYPESCRIPT => ['ts', 'js'],
        self::TYPOSCRIPT => ['typoscript', 'tsconfig'],
        self::CSS => ['scss', 'sass', 'css'],
        self::FLUID => ['html'],
        self::DOCS => ['rst'],
        self::XLIFF => ['xlf', 'xliff'],
    ];

    /** @var array<string, array<int, string>> Domain to words in a task description. */
    private const KEYWORDS = [
        self::PHP => [
            'php', 'class', 'service', 'datahandler', 'tca', 'formengine',
            'middleware', 'repository', 'controller', 'event listener', 'hook',
            'dependency injection', 'phpstan',
            // Testing is PHP work here, and the bare word is not how it is
            // said: `test` alone moved two of the 105 scenario prompts and hint
            // titles into PHP and made one of them worse, because the term
            // weights are taken over the candidates and a wider set reweighs
            // everything in it. These are the phrasings of somebody who has no
            // suite yet, which is the case the domain was missing (D-KNW-009).
            'unit test', 'functional test', 'test coverage', 'test suite',
            'automated test', 'set up tests', 'write tests',
            'backend module', 'module registration', 'backend route',
            // Three subjects that are PHP work and were reachable only while
            // their hints sat in the always-selected bucket: the icon registry,
            // and what an upgrade or a deprecation costs (`D-KNW-033`).
            'icon', 'icons', 'upgrade', 'deprecated api',
        ],
        self::TYPESCRIPT => [
            'typescript', 'javascript', 'web component', 'custom element', 'lit',
            'backend ui',
        ],
        // The first seven are the vocabulary of somebody who already knows the
        // answer is CSS. The rest is what a caller can actually see, and it is
        // there because eight of the nineteen Backend CSS hints could not be
        // reached by their own title: "Color and Surface Tokens" carried no CSS
        // signal, fell back to PHP, and its own category was never a candidate.
        self::CSS => [
            'sass', 'scss', 'css', 'stylesheet', 'styling', 'frontend build',
            'backend ui',
            'color', 'colour', 'dark mode', 'light mode', 'color scheme',
            'design token', 'custom property', 'custom properties',
            'shadow', 'elevation', 'z-index', 'overlay', 'layering',
            'transition', 'animation', 'motion',
            'container query', 'container queries', 'responsive',
            'spacing', 'padding', 'margin', 'border radius',
            'selector', 'specificity', 'contrast', 'focus ring',
            'rtl', 'logical property', 'logical properties',
            'web component', 'custom element',
            'layout shift', 'layout stability', 'text-overflow', 'ellipsis',
        ],
        self::FLUID => [
            // The namespace prefix every Fluid tag carries. A caller reporting
            // what a template did says `f:if`, `f:else`, `f:render` and never
            // the word Fluid — and the domain then fell back to PHP, so no hint
            // in this category was ever a candidate (`D-KNW-024`).
            'f:',
            'fluid', 'viewhelper', 'view helper', 'partial', 'pageview',
            // The root paths a template is resolved out of, asked for in the
            // two spellings they are written in. `partial` already carries
            // partialRootPaths by prefix; the other two carry no Fluid signal
            // at all, and a question about which of them wins fell to PHP.
            // The one phrasing a caller carries who is not asking about Fluid
            // at all. The argument check names the ViewHelper it rejected and
            // nothing else, and it is quoted from a PHP class being edited,
            // where the paths say `php` correctly and the answer to the
            // failure is filed here. `D-KNW-075` measured the alternative:
            // tagging the hint `php` took five of twelve ordinary PHP tasks
            // with it.
            'was registered with type',
            'templaterootpaths', 'layoutrootpaths', 'template root path',
            'page template', 'frontend template', 'content area', 'page layout',
            'backend layout',
            'sitepackage', 'site package', 'content element',
            'menu', 'navigation',
        ],
        self::TYPOSCRIPT => [
            'typoscript', 'tsconfig', 'site set', 'sitesets', 'settings definition',
            'backend layout',
            'constants.typoscript', 'setup.typoscript',
            'sitepackage', 'site package', 'content element',
            'menu', 'navigation',
        ],
        self::DOCS => [
            'changelog', 'rst', 'documentation', 'deprecation', 'breaking change',
        ],
        self::XLIFF => [
            'xlf', 'xliff', 'label', 'translation', 'locallang', 'wording',
            // What somebody calls the file before they know what is in it, and
            // the verb they use for the work: `translation` did not match
            // "translate", which is how SKILL-04 asks for it.
            'language file', 'language files', 'translate', 'translated',
        ],
    ];

    /**
     * Words for a thing that renders the website and is administered from the
     * backend.
     *
     * One of these says which thing the task is about, not which half of TYPO3
     * it is in: an editor's content element is a Fluid template and a TCA
     * definition at once. So a task that names only the backend does not become
     * Fluid and TypoScript work by carrying one, while a task that names the
     * website half keeps both — `D-KNW-006`.
     *
     * @var array<int, string>
     */
    private const ADMINISTERED_FROM_THE_BACKEND = ['sitepackage', 'site package', 'content element'];

    /**
     * The PHP testing phrasings a path in another domain takes back.
     *
     * They are in the PHP list deliberately: that is how somebody with no suite
     * yet asks, and the domain was missing exactly those callers (`D-KNW-009`).
     * What they cannot survive is a path that says which layer is meant, which
     * is `D-KNW-067`. Only these seven, and only against paths: every other PHP
     * keyword names a PHP thing rather than a kind of work, so a `.ts` path
     * beside the word `datahandler` really does touch both.
     *
     * @var array<int, string>
     */
    private const TESTING_PHRASINGS = [
        'unit test', 'functional test', 'test coverage', 'test suite',
        'automated test', 'set up tests', 'write tests',
    ];

    /**
     * Words that place a task in the website output rather than in the backend.
     *
     * @var array<int, string>
     */
    private const FRONTEND_MARKERS = [
        'frontend', 'front-end', 'front end', 'website theme', 'page template',
        'bootstrap 5', 'bootstrap5', 'theme extension', 'sitepackage',
        'site package',
    ];

    /**
     * Words that place it in the backend after all. They win, because the
     * frontend markers are the weaker signal: a backend module is named
     * outright, while "frontend" also appears in a sentence about the boundary
     * between the two.
     *
     * @var array<int, string>
     */
    private const BACKEND_MARKERS = [
        'backend', 'typo3/sysext/backend', 'install tool', 'styleguide',
    ];

    /** @var array<string, array<int, string>> Domain to directory conventions the extension alone does not reveal. */
    private const DIRECTORIES = [
        self::TYPESCRIPT => ['build/sources/typescript', 'resources/public/javascript'],
        self::CSS => ['build/sources/sass', 'resources/public/css'],
        self::FLUID => [
            'resources/private/templates', 'resources/private/partials',
            'resources/private/layouts', 'classes/viewhelpers',
        ],
        self::TYPOSCRIPT => ['configuration/sets/', 'classes/typoscript/'],
        self::DOCS => ['documentation/changelog'],
        self::XLIFF => ['resources/private/language'],
    ];

    /**
     * @param array<int, string> $paths
     * @return array<int, string> The matched domains, or PHP when the input
     *                            carries no signal at all.
     */
    public static function detect(array $paths, string $text = ''): array
    {
        $detected = [];
        $backendOnly = self::namesBackendModule($paths, $text) || self::namesOnlyTheBackend($paths, $text);

        foreach ($paths as $path) {
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            foreach (self::EXTENSIONS as $domain => $extensions) {
                if ($extension !== '' && in_array($extension, $extensions, true)) {
                    $detected[$domain] = true;
                }
            }
        }

        // Keywords are read from the description alone. A path carries its
        // domain in its extension and its directory, and matching words inside
        // it makes a file name mean something it does not: Classes/ViewHelpers/
        // Format/ScssViewHelper.php is PHP, and every word in it is a PHP
        // identifier, not a topic.
        $description = mb_strtolower($text);
        // The paths alone, because a negated mention in the description reads
        // exactly like a positive one — fromPaths().
        $pathDomains = self::fromPaths($paths);
        $testedElsewhere = $pathDomains !== [] && !in_array(self::PHP, $pathDomains, true);
        foreach (self::KEYWORDS as $domain => $keywords) {
            foreach ($keywords as $keyword) {
                // Asking for a test while every path is TypeScript or Sass is
                // asking for that layer's tests, and the PHPUnit hints are then
                // the larger half of an answer none of it applies to.
                if ($testedElsewhere && $domain === self::PHP && in_array($keyword, self::TESTING_PHRASINGS, true)) {
                    continue;
                }

                // A task that named only the backend named one of these for the
                // thing it is about, so the website half it belongs to is not
                // what was asked for. Explicit frontend terms below still add
                // their respective domains.
                if (
                    $backendOnly
                    && in_array($domain, [self::FLUID, self::TYPOSCRIPT], true)
                    && in_array($keyword, self::ADMINISTERED_FROM_THE_BACKEND, true)
                ) {
                    continue;
                }
                if (Text::containsWord($description, $keyword)) {
                    $detected[$domain] = true;
                    break;
                }
            }
        }

        $haystack = mb_strtolower(implode(' ', $paths) . ' ' . $text);
        foreach (self::DIRECTORIES as $domain => $directories) {
            foreach ($directories as $directory) {
                if (str_contains($haystack, $directory)) {
                    $detected[$domain] = true;
                    break;
                }
            }
        }

        // Without any signal, assume PHP: that is what most of the core is, and
        // an unrelated Sass or TypeScript recommendation is worse than a
        // slightly narrow one.
        if ($detected === []) {
            return [self::PHP];
        }

        return array_keys($detected);
    }

    /**
     * Whether the subject explicitly names a backend module or its declaration.
     *
     * This is narrower than merely saying "backend": a backend layout is
     * frontend page configuration, while a backend module is PHP registration
     * plus its own interface.
     *
     * @param array<int, string> $paths
     */
    public static function namesBackendModule(array $paths, string $text = ''): bool
    {
        $haystack = mb_strtolower(implode(' ', $paths) . ' ' . $text);

        return Text::containsWord($haystack, 'backend module')
            || str_contains($haystack, 'configuration/backend/modules.php');
    }

    /**
     * The domains carried by paths alone: no free-text keywords, no PHP
     * fallback, and empty when the paths say nothing.
     *
     * Free text is the wrong signal for narrowing a recommendation, because a
     * negated mention reads exactly like a positive one — "without unrelated
     * PHP or TypeScript suites" names both domains it rules out. A path cannot
     * be negated that way.
     *
     * @param array<int, string> $paths
     * @return array<int, string>
     */
    public static function fromPaths(array $paths): array
    {
        $detected = [];
        foreach ($paths as $path) {
            $lowered = mb_strtolower($path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            foreach (self::EXTENSIONS as $domain => $extensions) {
                if ($extension !== '' && in_array($extension, $extensions, true)) {
                    $detected[$domain] = true;
                }
            }
            foreach (self::DIRECTORIES as $domain => $directories) {
                foreach ($directories as $directory) {
                    if (str_contains($lowered, $directory)) {
                        $detected[$domain] = true;
                        break;
                    }
                }
            }
        }

        return array_keys($detected);
    }

    /**
     * File paths named inside a free-text description, so a query that spells
     * out the file it is about narrows the answer the same way an explicit
     * paths argument would.
     *
     * @return array<int, string>
     */
    public static function pathsIn(string $text): array
    {
        $extensions = array_merge(...array_values(self::EXTENSIONS));
        $pattern = '#[\w./_-]+\.(' . implode('|', $extensions) . ')\b#i';
        preg_match_all($pattern, $text, $matches);

        return array_values(array_unique($matches[0]));
    }

    /**
     * Whether the task is about what the website renders rather than about the
     * TYPO3 backend.
     *
     * The CSS and TypeScript conventions this server holds are the backend's:
     * its Sass sources, its `--typo3-*` custom properties, its light and dark
     * color schemes, its Bootstrap removal. For a theme extension they are not
     * merely irrelevant, they are the opposite of correct, so the frontend has
     * to be recognisable before an answer is composed.
     *
     * @param array<int, string> $paths
     */
    public static function namesTheFrontend(array $paths, string $text = ''): bool
    {
        $haystack = mb_strtolower(implode(' ', $paths) . ' ' . $text);
        foreach (self::BACKEND_MARKERS as $marker) {
            if (Text::containsWord($haystack, $marker)) {
                return false;
            }
        }

        foreach (self::FRONTEND_MARKERS as $marker) {
            if (Text::containsWord($haystack, $marker)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the task names the backend and nothing on the website beside it.
     *
     * The counterpart of namesTheFrontend(), and deliberately not its negation:
     * there the backend markers win, because "frontend" also appears in a
     * sentence about the boundary. A task that names both halves is asking for
     * both, so here a frontend marker is what rules the case out — "build it in
     * our site package, the element, its backend form and its frontend output"
     * is not backend-only, and the sitepackage layout is half of its answer.
     *
     * @param array<int, string> $paths
     */
    public static function namesOnlyTheBackend(array $paths, string $text = ''): bool
    {
        $haystack = mb_strtolower(implode(' ', $paths) . ' ' . $text);
        foreach (self::FRONTEND_MARKERS as $marker) {
            if (Text::containsWord($haystack, $marker)) {
                return false;
            }
        }

        foreach (self::BACKEND_MARKERS as $marker) {
            if (Text::containsWord($haystack, $marker)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The hint domains a task in the given domains is answered from. A hint
     * tagged `any` always applies.
     *
     * @param array<int, string> $domains
     * @return array<int, string>
     */
    public static function hintDomains(array $domains): array
    {
        $selected = [self::ANY];
        if (in_array(self::PHP, $domains, true)) {
            $selected[] = self::PHP;
        }
        if (in_array(self::TYPOSCRIPT, $domains, true)) {
            $selected[] = self::TYPOSCRIPT;
        }
        if (in_array(self::FLUID, $domains, true)) {
            $selected[] = self::FLUID;
        }
        if (in_array(self::TYPESCRIPT, $domains, true)) {
            $selected[] = self::TYPESCRIPT;
        }
        if (in_array(self::CSS, $domains, true)) {
            $selected[] = self::CSS;
        }
        // The two that were detected and had nowhere to go. A label question was
        // answered from `any` because no hint could say it is about XLIFF, and
        // the same for a changelog — which is what kept `general.json` holding
        // the subjects it did (`D-KNW-033`).
        if (in_array(self::XLIFF, $domains, true)) {
            $selected[] = self::XLIFF;
        }
        if (in_array(self::DOCS, $domains, true)) {
            $selected[] = self::DOCS;
        }

        return $selected;
    }

    /**
     * The same, as the labels an answer prints. Only a report reads this — what
     * selects a hint is the domain, and a label is what it is called afterwards.
     *
     * @param array<int, string> $domains
     * @return array<int, string>
     */
    public static function hintCategories(array $domains): array
    {
        return array_map(Hints::label(...), self::hintDomains($domains));
    }
}
