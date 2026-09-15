<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Knowledge\Versions;

/**
 * Which Node the npm half of a repository's declared commands runs on, read
 * from the four files that state one. `package.json` a range, an `.nvmrc` a
 * pin, an `actions/setup-node` step what CI installs, a DDEV project what its
 * container runs.
 *
 * The first two come from wherever the repository keeps its manifest, and the
 * answer names the file each came from. A core-shaped checkout declares both of
 * them in `Build/` and nothing at its root.
 *
 * Read outright or not at all, `R-PRJ-013`. A version a matrix or another file
 * decides comes back as the workflow writes it. The caller reads that workflow
 * in one call, and a resolved wrong number would carry this answer's authority.
 */
final class Node
{
    /** What sets Node up in a workflow, at whichever version of the action. */
    private const SETUP_NODE = '#^actions/setup-node(@|$)#i';

    /**
     * A version stated outright: `24`, `24.19`, `v24.19.0`, and the wildcard at
     * the end that means any release of what stands before it.
     *
     * Everything else stays unread: `lts/*`, `latest`, a `>=20` that installs
     * whatever is newest, and every `${{ }}` expression.
     */
    private const STATED_VERSION = '/^v?(\d+(?:\.\d+)*?)(?:\.(?:x|\*))?$/i';

    /**
     * What each source says, and where they stand to each other.
     *
     * Null where this repository says nothing about Node anywhere and has no
     * `package.json` to run one. There is no npm surface, and a sentence about
     * the interpreter of commands that do not exist is cost with no reader.
     *
     * @param ?string $environment what the environment states, where it does
     * @return array{
     *     engines: ?string,
     *     enginesIn: ?string,
     *     nvmrc: ?string,
     *     nvmrcIn: ?string,
     *     environment: ?string,
     *     ci: array<int, array{workflow: string, from: string, states: string, version: ?string}>,
     *     relation: array{declared: string, declaredBy: string, nvmrcAgainstEngines: ?string, inEnvironment: ?string, ci: ?string, inCi: ?string}|null
     * }|null
     */
    public static function describe(string $root, ?string $environment): ?array
    {
        $manifests = Project::npmManifests($root);
        $engines = null;
        $enginesIn = null;
        $nvmrc = null;
        $nvmrcIn = null;
        foreach (self::directories($manifests) as $directory) {
            $declared = Data::json($root . '/' . $directory . 'package.json')['engines']['node'] ?? null;
            if ($engines === null && is_string($declared) && trim($declared) !== '') {
                $engines = trim($declared);
                $enginesIn = $directory . 'package.json';
            }
            $stated = self::nvmrc($root . '/' . $directory . '.nvmrc');
            if ($nvmrc === null && $stated !== null) {
                $nvmrc = $stated;
                $nvmrcIn = $directory . '.nvmrc';
            }
        }
        $ci = self::workflows($root);

        if ($manifests === [] && $engines === null && $nvmrc === null && $ci === [] && $environment === null) {
            return null;
        }

        return [
            'engines' => $engines,
            'enginesIn' => $enginesIn,
            'nvmrc' => $nvmrc,
            'nvmrcIn' => $nvmrcIn,
            'environment' => $environment,
            'ci' => $ci,
            'relation' => self::relation($engines, $enginesIn, $nvmrc, $nvmrcIn, $environment, $ci),
        ];
    }

    /**
     * The directories a declaration comes from, relative to the root and with a
     * slash at the end where it is not the root itself.
     *
     * The root always, because an `.nvmrc` is what a version manager here
     * selects whether or not a manifest sits beside it. Then the directory of
     * every npm manifest after it. The first statement of each of the two files
     * is the one that holds. So a root manifest answers for a repository that
     * has one and `Build/` answers for the core's layout.
     *
     * @param array<int, string> $manifests
     * @return array<int, string>
     */
    private static function directories(array $manifests): array
    {
        $directories = [''];
        foreach ($manifests as $manifest) {
            $directory = dirname($manifest);
            if ($directory !== '.') {
                $directories[] = $directory . '/';
            }
        }

        return $directories;
    }

    /**
     * How the numbers stand to each other, against the one this repository
     * declares for itself.
     *
     * The pin is that number where there is one. `.nvmrc` is what a machine's
     * own version manager reads and so what a run executes on. The range's
     * floor answers for it where there is none. Whichever wins carries the name
     * of the file it came from, which is not the same file in every repository.
     * Null where neither reads. Nothing declared is a state to say in words,
     * not one to relate three numbers to.
     *
     * @param array<int, array{workflow: string, from: string, states: string, version: ?string}> $ci
     * @return array{declared: string, declaredBy: string, nvmrcAgainstEngines: ?string, inEnvironment: ?string, ci: ?string, inCi: ?string}|null
     */
    private static function relation(
        ?string $engines,
        ?string $enginesIn,
        ?string $nvmrc,
        ?string $nvmrcIn,
        ?string $environment,
        array $ci,
    ): ?array {
        $pinned = self::version($nvmrc);
        $floor = Versions::floor($engines);
        $declared = $pinned ?? $floor;
        if ($declared === null) {
            return null;
        }

        $inEnvironment = self::version($environment);
        // Several workflows in agreement are one answer. Several at odds are a
        // finding the list above carries, and no single relation states it.
        $stated = array_values(array_unique(array_filter(array_column($ci, 'version'))));

        return [
            'declared' => $declared,
            'declaredBy' => (string) ($pinned === null ? $enginesIn : $nvmrcIn),
            'nvmrcAgainstEngines' => $pinned === null || $floor === null ? null : Project::sits($pinned, $floor),
            'inEnvironment' => $inEnvironment === null ? null : Project::sits($inEnvironment, $declared),
            'ci' => count($stated) === 1 ? $stated[0] : null,
            'inCi' => count($stated) === 1 ? Project::sits($stated[0], $declared) : null,
        ];
    }

    /**
     * What `.nvmrc` says, as it says it.
     *
     * One line and nothing else is the whole format nvm reads, and an alias is
     * as legitimate a content as a number. `lts/iron` stays and stays unread,
     * because what it resolves to is a list nvm downloads rather than anything
     * in this repository.
     */
    private static function nvmrc(string $file): ?string
    {
        if (!is_file($file)) {
            return null;
        }
        $stated = trim(strtok((string) file_get_contents($file), "\n") ?: '');

        return $stated === '' ? null : $stated;
    }

    /**
     * Every `actions/setup-node` step below `.github/workflows/`, with what it
     * states.
     *
     * One entry per distinct statement rather than per step. A matrix of five
     * jobs that set the same version up is one fact about this repository. Five
     * lines that say it are five lines.
     *
     * @return array<int, array{workflow: string, from: string, states: string, version: ?string}>
     */
    private static function workflows(string $root): array
    {
        $directory = $root . '/.github/workflows';
        if (!is_dir($directory)) {
            return [];
        }

        $steps = [];
        foreach (Finder::create()->files()->in($directory)->depth(0)->name('*.yml')->name('*.yaml')->sortByName() as $file) {
            $jobs = Data::yaml($file->getPathname())['jobs'] ?? null;
            foreach (is_array($jobs) ? $jobs : [] as $job) {
                foreach (is_array($job['steps'] ?? null) ? $job['steps'] : [] as $step) {
                    $stated = is_array($step) ? self::setupNode($step) : null;
                    if ($stated !== null) {
                        $steps[] = ['workflow' => '.github/workflows/' . $file->getFilename()] + $stated;
                    }
                }
            }
        }

        return array_values(array_unique($steps, SORT_REGULAR));
    }

    /**
     * What one step sets Node up with, or null where it is not that step.
     *
     * A step that states neither input is not a step that states nothing. It
     * installs whatever the runner image ships, which is a version this
     * repository does not decide and the next runner image changes.
     *
     * @param array<mixed> $step
     * @return array{from: string, states: string, version: ?string}|null
     */
    private static function setupNode(array $step): ?array
    {
        $uses = is_string($step['uses'] ?? null) ? trim($step['uses']) : '';
        if (preg_match(self::SETUP_NODE, $uses) !== 1) {
            return null;
        }

        $with = is_array($step['with'] ?? null) ? $step['with'] : [];
        $version = self::stated($with['node-version'] ?? null);
        if ($version !== null) {
            return ['from' => 'node-version', 'states' => $version, 'version' => self::version($version)];
        }
        $file = self::stated($with['node-version-file'] ?? null);

        return $file === null
            ? ['from' => 'none', 'states' => '', 'version' => null]
            : ['from' => 'node-version-file', 'states' => $file, 'version' => null];
    }

    /** A `with` value as the workflow spells it, whatever YAML made of it. */
    private static function stated(mixed $value): ?string
    {
        $stated = match (true) {
            is_float($value) || is_int($value) => (string) $value,
            is_string($value) => trim($value),
            default => '',
        };

        return $stated === '' ? null : $stated;
    }

    /** The version a statement names outright, or null where it names none. */
    private static function version(?string $stated): ?string
    {
        if ($stated === null || preg_match(self::STATED_VERSION, $stated, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }

}
