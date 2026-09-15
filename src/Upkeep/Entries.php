<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

/**
 * Both corpora read together: what the records say about a piece of this code.
 *
 * The attributes reach a session from the red end: a red test names the entries
 * that rested on it. This is the other end, and the one a session is at before
 * it changes anything. Which entries name the class it is about to edit, and
 * which tests would go red for one.
 *
 * A decision and a requirement are one question here, so they come as one list.
 * Where they differ is the corpus each sits in, which the answer carries.
 */
final class Entries
{
    /**
     * Every entry of both corpora, keyed by id.
     *
     * @return array<string, array{id: string, title: string, status: string, file: string, tests: list<string>}>
     */
    public static function all(): array
    {
        $entries = [];
        foreach (Decisions::all() as $id => $decision) {
            $entries[$id] = [
                'id' => $id,
                'title' => $decision['title'],
                'status' => $decision['status'],
                'file' => 'decisions/' . $decision['group'] . '/' . $decision['file'],
                'tests' => $decision['tests'],
            ];
        }
        foreach (Requirements::all() as $id => $requirement) {
            $entries[$id] = [
                'id' => $id,
                'title' => $requirement['title'],
                'status' => Requirements::state($requirement)->value,
                'file' => 'requirements/' . $requirement['group'] . '/' . $requirement['file'],
                'tests' => $requirement['tests'],
            ];
        }

        return $entries;
    }

    /**
     * The classes one path declares — a file, or every file below a directory.
     *
     * @return array<int, string>
     */
    public static function declaredBelow(string $path): array
    {
        $full = str_starts_with($path, '/') ? $path : Paths::root() . '/' . $path;
        $declared = [];
        foreach (Sources::files() as $class => $file) {
            if ($file === $full || str_starts_with($file, rtrim($full, '/') . '/')) {
                $declared[] = $class;
            }
        }

        return $declared;
    }

    /**
     * The entries that name one of these classes in backticks, and which of
     * them each names.
     *
     * A backticked name is how this corpus points at the code. `RecordsTest`
     * holds every one of them to a class that has it, so a name here resolves
     * or the suite is already red.
     *
     * @param array<int, string> $classes
     * @return array<string, list<string>>
     */
    public static function naming(array $classes): array
    {
        if ($classes === []) {
            return [];
        }

        $naming = [];
        $pattern = '/`(' . implode('|', array_map(preg_quote(...), $classes)) . ')(?:::\w+)?`/';
        foreach (self::all() as $id => $entry) {
            preg_match_all($pattern, (string) file_get_contents(Paths::root() . '/' . $entry['file']), $named);
            if ($named[1] === []) {
                continue;
            }
            $naming[$id] = array_values(array_unique($named[1]));
        }

        return $naming;
    }

    /**
     * The test classes whose code names one of these classes, with the entries
     * each of them holds.
     *
     * What would go red, as far as a text can say it. A test that names the
     * class runs over it, and the entries it declares are what that failure
     * would print.
     *
     * @param array<int, string> $classes
     * @return array<string, list<string>>
     */
    public static function testsNaming(array $classes): array
    {
        if ($classes === []) {
            return [];
        }

        $declared = [];
        foreach ([...Sources::held('Decision'), ...Sources::held('Requirement')] as $id => $tests) {
            foreach ($tests as $test) {
                $declared[explode('::', $test)[0]][] = $id;
            }
        }

        $pattern = '/\b(' . implode('|', array_map(preg_quote(...), $classes)) . ')\b/';
        $naming = [];
        foreach (Finder::create()->files()->in(Paths::root() . '/tests')->name('*Test.php')->sortByName() as $file) {
            $class = basename($file->getFilename(), '.php');
            $ids = $declared[$class] ?? [];
            if ($ids === [] || preg_match($pattern, (string) file_get_contents($file->getPathname())) !== 1) {
                continue;
            }
            $naming[$class] = array_values(array_unique($ids));
        }

        return $naming;
    }
}
