<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Result;

use TYPO3\DevCompanion\Installation\Project;
use TYPO3\DevCompanion\Knowledge\Versions;

/**
 * Which TYPO3 versions an answer selected for, and why.
 *
 * The task guide and the hint lookup both filter by version and both have to
 * say what that filter cost, so the sentence stands once.
 */
final class VersionScope
{
    /**
     * The case worth a note is the one this said nothing about for a long time.
     * A repository that declares `^13.4 || ^14.3` gets both majors. A caller
     * that does not know this reads a statement labelled for one of them as the
     * current shape and the other as drift. The code builds around the
     * difference between the two, the file kept for the older major, the
     * interface not replaced yet. So the sentence names it as a constraint
     * rather than leaves it for discovery.
     *
     * @param array<int, int> $targets
     */
    public static function line(array $targets): string
    {
        if ($targets === []) {
            return 'No target TYPO3 version was stated and none was found to read, so every statement comes back '
                . 'with the versions it holds for. Pass targetVersion to have the ones that do not apply left out.';
        }

        $constraint = Project::coreConstraint();
        $declared = self::severalDeclared();

        // The filter is invisible from inside the answer, and that is how a
        // careful caller switches a wider default off. A session reads the
        // installed version out of typo3_project_describe and states it because
        // it looks like the accurate thing to do. It gets back exactly the
        // answer this filter changed to stop. So the one case where the two
        // disagree says so.
        if (count($targets) === 1) {
            if ($declared === []) {
                return sprintf('Answered for TYPO3 v%d: statements that do not hold there are left out.', $targets[0]);
            }

            return sprintf(
                'Answered for TYPO3 v%d alone, because targetVersion stated it. This repository declares '
                . 'typo3/cms-core as %s, so one codebase serves %s here, and every statement that holds only on '
                . '%s is missing from this answer — on a repository like this one those are not somebody else\'s '
                . 'rules, they are the constraint this code lives under. Leave targetVersion out to be answered '
                . 'for all of them at once.',
                $targets[0],
                $constraint === null ? 'a range' : '"' . $constraint . '"',
                self::majorList($declared),
                self::majorList(array_values(array_diff($declared, $targets))),
            );
        }

        return sprintf(
            'Answered for TYPO3 %s at once, because this repository declares typo3/cms-core as %s and one codebase '
            . 'serves all of them. A statement is kept when it holds on any of them, and the range beside it says '
            . 'which — where two statements about the same subject differ, that difference is the constraint this '
            . 'code lives under rather than something to clean up. Pass targetVersion to answer for one of them.',
            self::majorList($targets),
            $constraint === null ? 'a range' : '"' . $constraint . '"',
        );
    }

    /**
     * The majors this repository declares, where it declares more than one.
     *
     * Empty is the ordinary case and covers both shapes that behave alike. A
     * repository built for a single major, and one whose constraint nothing
     * here can read. Neither has an answer that could have been wider.
     *
     * @return array<int, int>
     */
    public static function severalDeclared(): array
    {
        $declared = Versions::declared(Project::coreConstraint());

        return count($declared) > 1 ? $declared : [];
    }

    /** @param array<int, int> $majors */
    public static function majorList(array $majors): string
    {
        $labels = array_map(static fn(int $major): string => 'v' . $major, $majors);
        $last = array_pop($labels);

        return $labels === [] ? $last : implode(', ', $labels) . ' and ' . $last;
    }
}
