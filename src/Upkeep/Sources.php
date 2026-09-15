<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

/**
 * The PHP this repository declares, as the records name it.
 *
 * Three readers want it and none wants another's copy. `RecordsTest` holds a
 * backticked `Class::member` to a class that has it. `Decisions::uncovered()`
 * asks which entries point at this code and name no test that would catch a
 * move. `decisions:cover` writes each entry's `coveredBy` from the
 * `#[Decision]` attributes. A second copy of the list is the duplication those
 * readings exist to find.
 *
 * Read from the files rather than through reflection. The records name a
 * private member as readily as a public one. A load of every class to ask about
 * it is a cost a check pays on every run.
 */
final class Sources
{
    /** @var array{0: array<string, true>, 1: array<string, true>, 2: array<string, string>}|null */
    private static ?array $declared = null;

    /**
     * Every class name below `src/` and `tests/`.
     *
     * @return array<string, true>
     */
    public static function classes(): array
    {
        return self::read()[0];
    }

    /**
     * Every `Class::member` those files declare. A method, a constant, a
     * property or an enum case, which is the whole of what a record can point
     * at with two colons.
     *
     * @return array<string, true>
     */
    public static function members(): array
    {
        return self::read()[1];
    }

    /**
     * Which tests declare they hold an entry, keyed by id.
     *
     * The `#[Decision]` and `#[Requirement]` attributes over a test are where
     * the tie stands, and the entry's `coveredBy` or `heldBy` comes from them.
     * One source, so the test and the entry cannot name each other differently.
     *
     * An attribute over the class names the class. That is a claim about every
     * method in it and what a requirement writes where the whole class is the
     * answer.
     *
     * Read from the text rather than through reflection, for the reason the
     * rest of this class is. A load of every test class to ask about it is a
     * cost a check pays on every run.
     *
     * @return array<string, list<string>>
     */
    public static function held(string $attribute): array
    {
        $held = [];
        foreach (Finder::create()->files()->in(Paths::root() . '/tests')->name('*Test.php')->sortByName() as $file) {
            $class = basename($file->getFilename(), '.php');
            $pending = [];
            foreach (explode("\n", (string) file_get_contents($file->getPathname())) as $line) {
                $line = trim($line);
                if (preg_match('/^#\[' . $attribute . "\\('([^']+)'\\)]$/", $line, $named) === 1) {
                    $pending[] = $named[1];
                    continue;
                }
                if (preg_match('/^public function (\w+)\(/', $line, $test) === 1) {
                    $names = array_fill_keys($pending, $class . '::' . $test[1]);
                } elseif (preg_match('/^(?:final |abstract |readonly )*class \w+/', $line) === 1) {
                    $names = array_fill_keys($pending, $class);
                } elseif (str_starts_with($line, '#[')) {
                    continue;
                } else {
                    $pending = [];
                    continue;
                }
                foreach ($names as $id => $name) {
                    $held[$id][] = $name;
                }
                $pending = [];
            }
        }

        foreach ($held as $id => $tests) {
            sort($tests);
            $held[$id] = array_values(array_unique($tests));
        }
        ksort($held);

        return $held;
    }

    /**
     * The file that declares each class.
     *
     * @return array<string, string>
     */
    public static function files(): array
    {
        return self::read()[2];
    }

    /**
     * The scan, once per process. Both callers run over the whole corpus, so a
     * second read of the tree is the difference between one pass and two.
     *
     * @return array{0: array<string, true>, 1: array<string, true>, 2: array<string, string>}
     */
    private static function read(): array
    {
        if (self::$declared !== null) {
            return self::$declared;
        }

        $classes = [];
        $members = [];
        $files = [];
        foreach (['src', 'tests'] as $tree) {
            foreach (Finder::create()->files()->in(Paths::root() . '/' . $tree)->name('*.php')->sortByName() as $file) {
                $code = (string) file_get_contents($file->getPathname());
                if (preg_match('/^(?:final |abstract |readonly )*(?:class|enum|interface|trait) (\w+)/m', $code, $named) !== 1) {
                    continue;
                }
                $classes[$named[1]] = true;
                $files[$named[1]] = $file->getPathname();
                foreach (['/function (\w+)\s*\(/', '/const (\w+)/', '/(?:public|private|protected)[^;{]*\$(\w+)/', '/case (\w+)/'] as $pattern) {
                    preg_match_all($pattern, $code, $found);
                    foreach ($found[1] as $member) {
                        $members[$named[1] . '::' . $member] = true;
                    }
                }
            }
        }

        return self::$declared = [$classes, $members, $files];
    }
}
