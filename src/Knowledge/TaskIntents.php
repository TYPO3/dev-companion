<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge;

use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Search\Text;

/**
 * Recognises what kind of core work a task description asks for.
 *
 * A task brief is the first tool an agent calls, so it has to know that
 * "deprecate a method" is one of the most rule-heavy change types in the core
 * and not just a cleanup. Each intent carries the checklist items, checks, and
 * follow-up tools that apply, plus the query that pulls the matching rule
 * sections out of the knowledge documents.
 */
final class TaskIntents
{
    /** Knowledge documents an intent may pull rule sections from. */
    /**
     * The intent whose workflow owns writing a core change, named where the
     * task sentence names none — `D-SKL-082`.
     */
    private const PATCH = 'patch';

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
            // two workflows: an audit is the conformance skill's outside the
            // core and the patch review's inside it, and each of those two
            // descriptions hands the other side away in as many words.
            'skill' => (string) ($entry['skill'] ?? ''),
            'skillCore' => (string) ($entry['skillCore'] ?? ''),
            // The knowledge document this kind of work is written up in, split
            // the same way and for the same reason: a package's test harness
            // and the core's are two procedures, and a brief that names the
            // wrong one sends a session to a page it cannot follow. Empty where
            // no page here writes that side up, which is every core one so far:
            // what a core intent would name is the three contribution documents
            // the rule sections in the same answer already do (`D-GUI-012`).
            'guide' => (string) ($entry['guide'] ?? ''),
            'guideCore' => (string) ($entry['guideCore'] ?? ''),
            'owes' => array_map('strval', (array) ($entry['owes'] ?? [])),
            // Whether the work this intent describes produces no change of its
            // own: reviewing one, triaging a report, fetching somebody else's
            // patch, running an installation. It is what skills() reads to
            // decide which intents may route in a brief that changes nothing.
            'changesNothing' => (bool) ($entry['changesNothing'] ?? false),
            // Whether the intent is the core's own contribution process rather
            // than a kind of work. Patch submission is one: outside the core
            // there is no Gerrit to submit to, so the intent is not a weaker
            // match there — it is not one at all.
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
     * Intents mentioned in the task text, in catalog order, each carrying how
     * sure the match is.
     *
     * A word can name a subject without naming the work: "field label" in a
     * FormEngine task is not an XLF change, but the word alone looks exactly
     * like one. Such a needle lives in matchWeak, and the intent it triggers is
     * returned as conditional rather than as recognized — its checklist and
     * checks apply only if the change really does what the word suggests.
     *
     * An id in $stated is recognized whatever the text says, which is how a
     * caller who classifies the work reaches the intent that owns it. It is a
     * parameter rather than a word appended to the text, because appending one
     * makes every intent carrying that word as a needle a strong match: stating
     * `cleanup` for a one-file edit confirmed *putting a repository right* and
     * put its six audit items into the brief, over the condition that intent
     * states for itself (`D-GUI-027`).
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
     * A weak match is never promoted, not even when it is the only one. The
     * brief does not know whether the task really is that kind of work, and
     * saying so is more useful than guessing: the checklist and checks of a
     * weak intent are still returned, marked with the condition they hold
     * under.
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
     * The words that select a core-only intent are ordinary ones — "push",
     * "submit" — and they occur in every description of maintenance work.
     * Reading one of them as a Gerrit patch submission put the whole core
     * contribution workflow into an answer about a third-party extension, which
     * is not a partly wrong answer but a wholly wrong one.
     *
     * Outside the core the intent is dropped: there is no Gerrit to submit to,
     * so it is not a weaker match there but none at all. Where nothing says
     * either way it is demoted to a conditional match, because most tasks name
     * neither side and a brief that guesses is what this is fixing.
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
     * route: a weak match is a word that named the subject without naming the
     * work, and loading a whole workflow on one is the wrong answer rather than
     * a partly wrong one. A brief that changes nothing routes only the intents
     * that change nothing either (`D-SKL-039`) — what the intent knows still
     * reaches the caller in its checklist items, and only the route is withheld.
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
        // changes one. A task worded as the defect rather than as the work
        // confirms no intent — *add the missing language parameter to
        // getMovedRecordsFromPages* confirmed none — and a brief that knows it
        // is a core change owes the caller the workflow that owns it anyway
        // (`D-SKL-082`). `audit` and `triage` do not reach here: both confirm
        // their own intent, so `changesNothing` is true above.
        $patch = self::declared(self::PATCH, 'skillCore');

        return $patch === '' ? [] : [$patch];
    }

    /**
     * One field of one intent, read from the file that declares it.
     *
     * The skill name lives in `knowledge/task-intents.json` and this reads it
     * from there rather than repeating it, so renaming a published skill stays
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
     * The other half of the same route (`D-GUI-012`): the skill is the workflow
     * in the caller's own project and this one is the page on this server, so a
     * session whose client lists no resources learns the guide exists at the
     * moment the work does. Which intents may name one is decided exactly as
     * above, because a guide loaded on a weak match or on the words of the
     * change under review is the wrong page rather than a partly right one.
     *
     * @param array<int, array<string, mixed>> $intents
     * @return array<int, string>
     */
    public static function guides(array $intents, bool $coreWork, bool $changesNothing): array
    {
        $named = self::owned($intents, $coreWork ? 'guideCore' : 'guide', $changesNothing);

        // A procedure a kind of work owes is not the write-up of it, so it is
        // named beside the intent rather than by confirming a second one. The
        // browser check is the case: a backend module owes it and no task text
        // says "browser", and widening the intent's own words to reach it put
        // a second checklist and a second skill into the brief — which is what
        // `D-SKL-051` measured and refused (`D-ANS-140`).
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

        return $named;
    }

    /**
     * What the confirmed intents name under one key, once each, reading before
     * writing.
     *
     * A brief that finds an issue on the tracker and then fixes it names two
     * workflows, and the order is what tells the caller which one to be in
     * first: the half that establishes what is there runs before the half that
     * changes it (`D-SKL-081`). Within each half the catalog order stands.
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
                // The range is part of the key: one subject bound to two of them
                // is two sections under one heading, and a package serving both
                // majors needs both.
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
