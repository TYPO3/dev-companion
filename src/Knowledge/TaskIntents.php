<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge;

use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Search\Text;

/**
 * Recognises what kind of core work a task description asks for.
 *
 * A task brief is the first tool an agent calls. So it has to know that
 * "deprecate a method" is one of the most rule-heavy change types in the core
 * and not just a cleanup. Each intent carries the checklist items, checks, and
 * follow-up tools that apply. It carries the query that pulls the matched rule
 * sections out of the knowledge documents.
 */
final class TaskIntents
{
    /** Knowledge documents an intent may pull rule sections from. */
    /**
     * The intent whose workflow owns the write of a core change, named where
     * the task sentence names none, `D-SKL-082`.
     */
    private const PATCH = 'patch';

    /**
     * The page every task that writes files owes, whatever it writes.
     *
     * Not an intent's `owes`, because no intent owns it. What it is about is a
     * property of any write at all. A line in each write intent would be the
     * same line in a dozen places.
     */
    private const PROSE_A_PATCH_CARRIES = 'any/writing/the-prose-a-patch-carries';

    private const RULE_DOCUMENTS = [
        'core/contribution/rules',
        'core/contribution/commit-messages',
        'core/contribution/gerrit-workflow',
    ];

    /**
     * @return array<int, array{id: string, title: string, skill: string, skillCore: string, guide: string, guideCore: string, owes: array<int, string>, changesNothing: bool, scope: ?Scope, match: array<int, string>, matchWeak: array<int, string>, condition: string, rulesQuery: string, checklist: array<int, string>, checks: array<int, string>, tools: array<int, string>}>
     */
    public static function load(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::knowledgeFile('task-intents.json')), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid task-intents.json');
        }

        return array_map(static fn(array $entry): array => [
            'id' => (string) $entry['id'],
            'title' => (string) $entry['title'],
            // The task skill that owns this kind of work, on each side of the
            // core boundary, and empty where no published skill does. The two
            // are separate entries rather than one because the same words name
            // two workflows. An audit is the conformance skill's outside the
            // core and the patch review's inside it. Each of those two
            // descriptions hands the other side away in as many words.
            'skill' => (string) ($entry['skill'] ?? ''),
            'skillCore' => (string) ($entry['skillCore'] ?? ''),
            // The knowledge document that writes this kind of work up, split
            // the same way and for the same reason. A package's test harness
            // and the core's are two procedures. A brief that names the wrong
            // one sends a session to a page it cannot follow. Empty where no
            // page here writes that side up, which is every core one so far.
            // What a core intent would name is the three contribution documents
            // the rule sections in the same answer already do (`D-GUI-012`).
            'guide' => (string) ($entry['guide'] ?? ''),
            'guideCore' => (string) ($entry['guideCore'] ?? ''),
            'owes' => array_map('strval', (array) ($entry['owes'] ?? [])),
            // Whether the work this intent describes produces no change of its
            // own. A review of one, a triage of a report, a fetch of somebody
            // else's patch, a run of an installation. It is what skills() reads
            // to decide which intents may route in a brief that changes
            // nothing.
            'changesNothing' => (bool) ($entry['changesNothing'] ?? false),
            // Whether the intent is the core's own contribution process rather
            // than a kind of work. Patch submission is one. Outside the core
            // there is no Gerrit to submit to, so the intent is not a weaker
            // match there, it is not one at all.
            'scope' => isset($entry['scope']) ? Scope::from((string) $entry['scope']) : null,
            'match' => array_map('strval', $entry['match'] ?? []),
            'matchWeak' => array_map('strval', $entry['matchWeak'] ?? []),
            'condition' => (string) ($entry['condition'] ?? ''),
            'rulesQuery' => (string) ($entry['rulesQuery'] ?? ''),
            'checklist' => array_map('strval', $entry['checklist'] ?? []),
            'checks' => array_map('strval', $entry['checks'] ?? []),
            'tools' => array_map('strval', $entry['tools'] ?? []),
        ], $decoded);
    }

    /**
     * Intents the task text mentions, in catalog order, each with how sure the
     * match is.
     *
     * A word can name a subject without a name for the work. "Field label" in a
     * FormEngine task is not an XLF change, but the word alone looks exactly
     * like one. Such a needle lives in matchWeak, and the intent it triggers
     * comes back as conditional rather than as recognized.
     *
     * An id in $stated counts as recognized whatever the text says, which is
     * how a caller who classifies the work reaches the intent that owns it. It
     * is a parameter rather than a word appended to the text, because an
     * appendix makes every intent with that word as a needle a strong match,
     * `D-GUI-027`.
     *
     * @param array<int, string> $stated Intent ids the call names outright.
     * @return array<int, array<string, mixed>>
     */
    public static function detect(string $text, array $stated = []): array
    {
        $haystack = mb_strtolower($text);
        $detected = [];

        foreach (self::load() as $intent) {
            $confidence = in_array($intent['id'], $stated, true) ? 'strong' : null;
            if ($confidence === null) {
                foreach ($intent['match'] as $needle) {
                    if (Text::containsWord($haystack, $needle)) {
                        $confidence = 'strong';
                        break;
                    }
                }
            }
            if ($confidence === null) {
                foreach ($intent['matchWeak'] as $needle) {
                    if (Text::containsWord($haystack, $needle)) {
                        $confidence = 'weak';
                        break;
                    }
                }
            }
            if ($confidence !== null) {
                $intent['confidence'] = $confidence;
                $detected[] = $intent;
            }
        }

        return $detected;
    }

    /**
     * The intents a brief may state as fact — the strongly matched ones.
     *
     * A weak match never moves up, not even when it is the only one. The brief
     * does not know whether the task really is that kind of work. A line that
     * says so is more useful than a guess. The checklist and checks of a weak
     * intent still come back, with the condition they hold under.
     *
     * @param array<int, array<string, mixed>> $intents
     * @return array<int, array<string, mixed>>
     */
    public static function confirmed(array $intents): array
    {
        return array_values(array_filter(
            $intents,
            static fn(array $intent): bool => $intent['confidence'] === 'strong'
        ));
    }

    /**
     * The detected intents, with the core-only ones held to the evidence there
     * is for them.
     *
     * The words that select a core-only intent are ordinary ones, "push",
     * "submit", and they occur in every description of maintenance work. A read
     * of one of them as a Gerrit patch submission put the whole core
     * contribution workflow into an answer about a third-party extension. That
     * is not a partly wrong answer but a wholly wrong one.
     *
     * Outside the core the intent drops out. There is no Gerrit to submit to,
     * so it is not a weaker match there but none at all. Where nothing says
     * either way it moves down to a conditional match. Most tasks name neither
     * side, and a brief that guesses is what this fixes.
     *
     * @param array<int, array<string, mixed>> $intents
     * @return array<int, array<string, mixed>>
     */
    public static function scoped(array $intents, Scope $scope, bool $coreWork): array
    {
        $scoped = [];
        foreach ($intents as $intent) {
            if ($intent['scope'] !== Scope::Core || $coreWork) {
                $scoped[] = $intent;
                continue;
            }
            if ($scope->isOutsideTheCore()) {
                continue;
            }
            $intent['confidence'] = 'weak';
            $scoped[] = $intent;
        }

        return $scoped;
    }

    /**
     * The task skills that own the work these intents recognized, in catalog
     * order and deduplicated.
     *
     * This is the one route from an answer to the workflow the caller should be
     * in, and nothing here named one until `D-SKL-013`. Only confirmed intents
     * route. A weak match is a word that named the subject without a name for
     * the work. A whole workflow on one is the wrong answer rather than a
     * partly wrong one. A brief that changes nothing routes only the intents
     * that change nothing either (`D-SKL-039`). What the intent knows still
     * reaches the caller in its checklist items, and only the route stays back.
     *
     * @param array<int, array<string, mixed>> $intents
     * @return array<int, string>
     */
    public static function skills(array $intents, bool $coreWork, bool $changesNothing): array
    {
        $named = self::owned($intents, $coreWork ? 'skillCore' : 'skill', $changesNothing);
        if ($named !== [] || !$coreWork || $changesNothing) {
            return $named;
        }

        // Nothing in the sentence named a workflow and the call already says
        // what this is: files below `typo3/sysext/` and a change type that
        // changes one. A task in the words of the defect rather than of the
        // work confirms no intent, *add the missing language parameter to
        // getMovedRecordsFromPages* confirmed none. A brief that knows it is a
        // core change owes the caller the workflow that owns it anyway
        // (`D-SKL-082`). `audit` and `triage` do not reach here. Both confirm
        // their own intent, so `changesNothing` is true above.
        $patch = self::declared(self::PATCH, 'skillCore');

        return $patch === '' ? [] : [$patch];
    }

    /**
     * One field of one intent, read from the file that declares it.
     *
     * The skill name lives in `knowledge/task-intents.json` and this reads it
     * from there rather than repeats it. So a rename of a published skill stays
     * one edit.
     */
    private static function declared(string $id, string $field): string
    {
        foreach (self::load() as $intent) {
            if ($intent['id'] === $id) {
                return is_string($intent[$field] ?? null) ? $intent[$field] : '';
            }
        }

        return '';
    }

    /**
     * The knowledge documents that write up the work these intents recognized,
     * in catalog order and deduplicated.
     *
     * The other half of the same route (`D-GUI-012`). The skill is the workflow
     * in the caller's own project and this one is the page on this server. So a
     * session whose client lists no resources learns the guide exists at the
     * moment the work does. Which intents may name one follows the rule above.
     * A guide loaded on a weak match or on the words of the change under review
     * is the wrong page. Not a partly right one.
     *
     * @param array<int, array<string, mixed>> $intents
     * @return array<int, string>
     */
    public static function guides(array $intents, bool $coreWork, bool $changesNothing): array
    {
        $named = self::owned($intents, $coreWork ? 'guideCore' : 'guide', $changesNothing);

        // A procedure a kind of work owes is not the write-up of it, so it
        // stands beside the intent rather than confirms a second one. The
        // browser check is the case. A backend module owes it and no task text
        // says "browser". Wider words on the intent to reach it put a second
        // checklist and a second skill into the brief. That is what `D-SKL-051`
        // measured and refused (`D-ANS-140`).
        foreach (self::confirmed($intents) as $intent) {
            if ($changesNothing && $intent['changesNothing'] !== true) {
                continue;
            }
            foreach ($intent['owes'] as $owed) {
                if (!in_array($owed, $named, true)) {
                    $named[] = $owed;
                }
            }
        }

        // A task that writes files hands its prose to a reviewer, and no intent
        // owns that. The register a session reads is the one it writes its
        // comments in. Three reports traced a patch's rejected comments back to
        // this server's own surfaces (`D-DOC-068`). So it hangs off the one
        // property every write intent shares rather than off each of them.
        if (!$changesNothing && !in_array(self::PROSE_A_PATCH_CARRIES, $named, true)) {
            $named[] = self::PROSE_A_PATCH_CARRIES;
        }

        return $named;
    }

    /**
     * What the confirmed intents name under one key, once each, the read before
     * the write.
     *
     * A brief that finds an issue on the tracker and then fixes it names two
     * workflows. The order is what tells the caller which one to be in first.
     * The half that establishes what is there runs before the half that changes
     * it (`D-SKL-081`). Within each half the catalog order stands.
     *
     * @param array<int, array<string, mixed>> $intents
     * @return array<int, string>
     */
    private static function owned(array $intents, string $key, bool $changesNothing): array
    {
        $reading = [];
        $writing = [];
        foreach (self::confirmed($intents) as $intent) {
            if ($changesNothing && $intent['changesNothing'] !== true) {
                continue;
            }
            $named = (string) $intent[$key];
            if ($named === '') {
                continue;
            }
            if ($intent['changesNothing'] === true) {
                $reading[$named] = true;
                continue;
            }
            $writing[$named] = true;
        }

        return array_keys($reading + $writing);
    }

    /**
     * The rule sections behind the detected intents, deduplicated.
     *
     * @param array<int, array<string, mixed>> $intents
     * @param int|array<int, int>|null $target The majors the answer has to hold on.
     * @return array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}>
     */
    public static function rules(array $intents, int $limitPerIntent = 2, int|array|null $target = null): array
    {
        $sections = [];
        $seen = [];
        foreach ($intents as $intent) {
            if ($intent['rulesQuery'] === '') {
                continue;
            }
            foreach (Documents::search($intent['rulesQuery'], self::RULE_DOCUMENTS, $limitPerIntent, $target) as $section) {
                // The range is part of the key. One subject bound to two of
                // them is two sections under one heading, and a package that
                // serves both majors needs both.
                $key = $section['id'] . '#' . $section['heading'] . '#' . $section['since'] . '-' . $section['until'];
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $sections[] = $section;
            }
        }

        return $sections;
    }
}
