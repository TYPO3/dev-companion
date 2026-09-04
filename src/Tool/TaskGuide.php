<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Feedback\Channel;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Knowledge\Coverage;
use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Knowledge\Domains;
use TYPO3\DevCompanion\Knowledge\Hints;
use TYPO3\DevCompanion\Knowledge\Scope;
use TYPO3\DevCompanion\Knowledge\TaskIntents;
use TYPO3\DevCompanion\Knowledge\TestSuiteHints;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Result\MatchedHints;
use TYPO3\DevCompanion\Result\Prose;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\VersionScope;
use TYPO3\DevCompanion\Server\Installer;

/**
 * A task checklist, enriched with the hints and core checks that
 * match it.
 */
final class TaskGuide extends ReadOnlyTool
{
    /** The premise every checklist below is composed under, stated once (R-GUI-008). */
    public const PRODUCT_PREMISE = 'Content changes, so what is delivered has to be the version that is '
        . 'current after the change — that is what the editor and the visitor are owed. A defect is judged by '
        . 'that outcome: the old version still being served is the defect, and the error it eventually throws '
        . 'is the symptom.';

    /**
     * How many hints a brief carries for one group of paths.
     *
     * Fewer than the hint lookup's own default, because the hints are one
     * section of a brief there and the whole answer here. It is a named
     * constant so that the number the answer states and the number it slices by
     * are the same number.
     */
    public const HINTS_PER_GROUP = 4;

    /**
     * How many suites a brief carries, out of the list `typo3_test_run_guide`
     * returns for the same paths.
     *
     * Named for the same reason as the constant above it: the schema says how
     * many the answer carries, and it says the number the answer is sliced by.
     */
    public const SUITES_PER_BRIEF = 4;

    /**
     * Whose the hints in a brief are, said where they are printed (R-GUI-009).
     *
     * They are `typo3_hint_lookup`'s corpus, matched by the same matcher and
     * quoted statement for statement — and a selection of it, which is the half
     * a caller cannot see from here. The fifth recorded `REVIEW-03` run cited
     * two of them as that lookup without having called it: the attribution was
     * right, nothing in the answer had said so, and the reader was sent to a
     * tool that did not answer.
     */
    public const HINTS_SOURCE = 'The hints below are typo3_hint_lookup\'s, matched for these paths and quoted '
        . 'whole. A finding that cites one of these rules is citing that lookup rather than this guide.';

    /**
     * The second half of `HINTS_SOURCE`, said only where it is true.
     *
     * It was printed unconditionally and claimed the lookup holds more whenever
     * a brief carried hints at all, beside an `omittedHints` that was empty and
     * said the opposite in the same payload — which is the pointer `R-GUI-012`
     * exists to stop being empty.
     */
    public const HINTS_TRUNCATED = 'A brief carries the %d strongest per group of paths, which is not '
        . 'everything the lookup holds on them — call it for the rest, by path, with a larger limit, or by id.';

    /**
     * The same half where the brief did carry everything the lookup matched.
     *
     * A caller that has to find out by calling has paid a round trip to be
     * told nothing, and the step the brief stands in for is the one a skill
     * prescribes next — so the answer says which it was.
     */
    public const HINTS_COMPLETE = 'These are everything typo3_hint_lookup matches for these paths, so calling '
        . 'it again by path adds nothing; a subject it holds under another path or id is still a call away.';

    /**
     * Which hints the brief left, said where the count is (R-GUI-012).
     *
     * The sentence above states how many were carried and not which ones were
     * not, so a subsystem the brief did not reach is invisible until the lookup
     * is called. The review that lost `dependency-injection` on a patch
     * injecting a new service read four hint bodies and established the rule by
     * grepping three call sites out of the checkout instead
     * (`feedback/2026-08-03-144410`).
     */
    public const HINTS_OMITTED = 'What it holds for these paths and this brief left: %s. Each is one call away '
        . 'by id, and a subject named there and not below is one this brief did not reach.';

    /**
     * Where the task belongs, said in the answer that recognized it
     * (`D-SKL-013`).
     *
     * The skill is a file in the caller's own project and this server cannot
     * see it, so the line names it and stops there: what it is worth is the
     * order it carries, and a brief is one call inside that order.
     */
    public const SKILLS_OWNING = 'Owned by: %s. Load it where this project has it installed — the skill carries '
        . 'the working order for this kind of work, and this brief is one call inside it.';

    /**
     * The same line where the task reads something and then changes it
     * (`D-SKL-081`).
     *
     * Two names without an order are two names to pick one of, and the session
     * this was written from picked neither. The order is the work's: what
     * establishes what is there comes before what writes the change.
     */
    public const SKILLS_IN_ORDER = 'Owned by: %s — in that order, because this task establishes what is there '
        . 'before it changes it. Load both where this project has them installed: each carries the working order '
        . 'for its half, and this brief is one call inside the first.';

    /**
     * Said where the skill is named, for the copy this project has of it
     * (`D-SKL-086`).
     *
     * The same thing is said once at initialize, before a task is known, and a
     * session read it there and worked four such skills anyway. This is the
     * last moment this server controls: the load itself is a call it cannot
     * see.
     */
    public const SKILLS_STALE = 'Behind what this server publishes, in this project: %s. Run '
        . 'typo3-dev-companion update before loading %s, or say in any report which copy you read.';

    /**
     * The sweep a change owes, said in the brief that classified it
     * (`D-GUI-013`).
     *
     * It is step 5 of `skills/base.md`, and the paragraph before it exempts the
     * task that produces no change — so a session that walked the order on a 404
     * and then wrote four PHP files has read the step and taken the exemption.
     * What is placed here is the obligation, its axes and the condition it is
     * skipped under (`D-GUI-025`); the step's reasoning stays where it is.
     */
    public const DEPRECATION_SWEEP = 'Sweep the deprecations before writing: typo3_changelog_lookup with '
        . 'type "deprecation" and the query omitted, %s. Only a change touching no TYPO3 API skips it, a CI '
        . 'file being the shape of one, and how small the diff is decides nothing. One call per tag: the ext: '
        . 'tag of each system extension this package calls into, and TCA, Fluid, Backend or Frontend for the '
        . 'kinds of file it ships. Every call also returns the tags that major carries, so the second '
        . 'onwards is read off the first.';

    /**
     * The page this kind of work is written up in, said in the same place
     * (`D-GUI-012`).
     *
     * The pointer arrives with the work instead of before it, where the `guides`
     * key of `typo3_project_describe` names the corpus once a session. Named as
     * the call rather than as the `typo3://guides` address, because a client
     * that lists no resources cannot act on an address (`D-ANS-061`).
     */
    public const GUIDES_OWNING = 'Written up in the pages below, each one typo3_rule_lookup call with that '
        . 'documentId, no resource list needed — the procedure for this kind of work, which this brief does not '
        . 'repeat. Read the one whose sentence names the work you are about to do:';

    /**
     * The change type of a task that changes nothing, and the id of the intent
     * that recognizes one described rather than classified. They are the same
     * word because a caller states one or writes the other.
     */
    private const AUDIT = 'audit';

    /**
     * The other change type that writes no file, and the intent it reaches.
     *
     * The two are spelled differently because the intent stands beside
     * `installation-setup` and `installation-upgrade` as the third thing that is
     * done to an installation, rather than being what a caller states about the
     * work (`D-GUI-008`).
     */
    private const OPERATIONS = 'operations';
    private const OPERATIONS_INTENT = 'installation-operations';

    /**
     * The third change type that writes no file, and the intent it is the id of.
     *
     * It exists because a session triaging a core report picked `audit` — the
     * one documented as writing no file — and was answered with what a review of
     * a diff owes, which it used none of because a triage produces no diff
     * (`D-GUI-011`). So `audit` keeps its meaning, and what withholds the diff
     * items is that neither intent matches the other's words rather than a rule
     * about which items apply.
     */
    private const TRIAGE = 'triage';

    /**
     * The fourth, and the id of the intent it reaches (`D-SKL-065`).
     *
     * A defect reported by its symptom, where the request is the file and the
     * cause and nothing is to be changed yet. It is the shape that was answered
     * worst of the four: measured on 2026-08-19, a content element rendering
     * wrong handed over the workflow for adding one, and a page answering with
     * an error was recognized as nothing at all and came back with the steps a
     * patch owes. The value exists for the same reason `triage` does — a caller
     * classifying rather than describing reaches for the one documented as
     * writing no file, and `audit` is what they picked last time (`D-GUI-011`).
     */
    private const DIAGNOSIS = 'diagnosis';

    /**
     * The intent that recognizes the caller's own act of writing the change,
     * and the one that ends every shape above where a brief carries it.
     *
     * It is the discriminator `D-SKL-039` had no word for: a review names the
     * change it is about, and the words of that change are the words of writing
     * one, while "and fix it" is the caller saying what they are going to do.
     */
    private const PATCH = 'patch';

    /**
     * The intent a stated change type names, where it names one.
     *
     * A caller states the type instead of describing the work, and the four
     * that write no file are only reachable that way. So the route is real and
     * what it may not be is a word appended to the task text: that made every
     * intent carrying the type as a needle a strong match, and `cleanup` names
     * two different pieces of work — a mechanical patch, which is what the
     * enum value means and what the arm below answers, and putting a whole
     * repository right, which is the intent (`D-GUI-027`). `bugfix`, `feature`
     * and `cleanup` name no intent: what those tasks are about is the sentence.
     *
     * @var array<string, string>
     */
    private const CHANGE_TYPE_INTENT = [
        self::AUDIT => self::AUDIT,
        self::TRIAGE => self::TRIAGE,
        self::OPERATIONS => self::OPERATIONS_INTENT,
        self::DIAGNOSIS => self::DIAGNOSIS,
        'deprecation' => 'deprecation',
        'documentation' => 'documentation',
        'test' => 'tests',
    ];

    /** @var array<string, array<int, string>> */
    private const CHANGE_TYPE_CHECKLIST = [
        'bugfix' => [
            'Reproduce the bug first, ideally with a failing test that the fix turns green.',
            // The checkout a caller stands in is regularly the one branch, so
            // this asked for a reading nobody could make and a session
            // answered it from typo3_commit_message_guide instead and said so
            // (`D-GUI-023`). That tool states which lines take the change and
            // what naming an older one claims; which of them carry the defect
            // is still the caller's reading, on whatever branches they have.
            'Settle which release branches the fix goes to with typo3_commit_message_guide, which names the '
                . 'lines a change of this type takes and says what claiming an older one costs. Whether the '
                . 'defect is on them is your reading, and a checkout holding one branch cannot make it.',
            // The obligation the core's own conventions file leaves at "user-facing
            // changes need an entry", which a session working a bugfix answered by
            // guessing and said so (`D-AUD-014`). The directory is here for the
            // same reason and is the second half of `D-KNW-132`; the four types and
            // the boundary against Breaking stay the page's.
            'A bugfix owes a changelog entry only where it changes what an installation renders, is configured '
                . 'by, or has documented, and then it is an Important below typo3/sysext/core/Documentation/'
                . 'Changelog/. Write it into the <lts>.x directory of the oldest branch the Releases: trailer '
                . 'names, and into both .x directories where two maintained lines take the change. The whole '
                . 'rule is one typo3_rule_lookup call with documentId "core/contribution/changelog".',
        ],
        'feature' => [
            'Add a changelog feature file under typo3/sysext/core/Documentation/Changelog/ for public API additions.',
            'Cover the new behaviour with functional tests, not only unit tests.',
        ],
        'cleanup' => ['Keep the cleanup mechanical; avoid mixing behavioural changes into the same patch.'],
        'test' => ['Confirm the test fails without the fix and passes with it; avoid asserting on incidental output.'],
        'documentation' => ['Run ./Build/Scripts/runTests.sh -s checkRst to validate ReST syntax.'],
        // The one type whose rules are stated elsewhere: it names the
        // `deprecation` intent above, which already carries them for the caller
        // who describes the work instead of classifying it. A block here would
        // print every one of those items a second time.
        'deprecation' => [],
        // The type that changes nothing. What a review owes is the audit
        // intent's, for the same reason, and what it does not owe is the
        // checklist this one is not assembled into at all — see answer().
        self::AUDIT => [],
        // The other one. What operating an installation owes is the intent's,
        // which this value names above, and what it does not owe is the
        // skeleton a review is composed into — see answer().
        self::OPERATIONS => [],
        // The third, and the same arrangement: the triage intent carries what a
        // triage owes, and this value is how a caller reaches it by classifying
        // rather than by describing.
        self::TRIAGE => [],
        // The fourth, and the same arrangement again: what looking for a cause
        // owes is the diagnosis intent's, and this value is how a caller
        // reaches it by classifying rather than by describing.
        self::DIAGNOSIS => [],
        'unknown' => [],
    ];

    /** Extra domain signal carried by the change type itself. */
    private const CHANGE_TYPE_TERMS = [
        'documentation' => 'documentation changelog rst',
        'test' => 'unit test functional test',
        'feature' => 'changelog',
        'deprecation' => 'deprecation changelog rst deprecated api',
        'bugfix' => '',
        'cleanup' => '',
        self::AUDIT => '',
        // Empty, and measured rather than assumed: the terms here reach
        // Domains::detect() for this brief alone, while the hints are matched by
        // Hints::find() from the paths and the task text. `frontend build
        // sitepackage` — the vocabulary an asset build is asked for in — left
        // the four hints of `feedback/2026-08-03-154508` identical and moved the
        // brief to buildCss, lintScss and the component lookup, which is what
        // writes backend markup rather than what boots an installation.
        self::OPERATIONS => '',
        self::TRIAGE => '',
        self::DIAGNOSIS => '',
        'unknown' => '',
    ];

    /**
     * The tool that answers a matched subject from the installation instead of
     * from memory. These are invented or misregistered in bulk when nobody
     * points at them, and fail at runtime rather than at build time.
     *
     * @var array<string, string>
     */
    private const HINT_TOOLS = [
        'backend-modules' => 'typo3_backend_module_lookup, to compare the declaration with modules registered '
            . 'by the active installation',
        'language-files' => 'typo3_label_lookup with the XLF resource used at the consuming code, while writing '
            . 'labels: a matching label elsewhere in the installation is not reusable in that context',
        'icon-usage' => 'typo3_icon_lookup, before writing an icon identifier: an unknown one renders an empty box',
    ];

    public static function name(): string
    {
        return 'typo3_task_guide';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Knowledge];
    }

    public static function description(): string
    {
        return 'Answers what one change owes, which a repository\'s own conventions file cannot: that file states its rules once for every task, and this narrows them to the kind of change, the paths and the TYPO3 majors in front of you — down to whether this fix owes a changelog entry. Where such a rule stays general it names the call that settles it, the branches a Releases: trailer takes among them. The answer is a task checklist with the hints and core checks that match. Not only for work that ends in a patch: deciding whether an open bug report still holds is changeType "triage", reviewing a body of code is "audit", bringing an installation up is "operations", and finding out why something is broken before anybody changes it is "diagnosis" — all four get a brief of their own rather than the steps a patch owes. Built from bundled conventions only: it does not read your checkout, so it also names what you have to establish there yourself, routes to the lookups that fit the task, and names the task skill that owns the work where a published one does, beside the guide the work is written up in where this server carries one. The hints for a set of paths without the checklist around them are typo3_hint_lookup, and a procedure read whole is typo3_rule_lookup. Work that reads as a project or third-party extension is answered with what transfers only — the core checks, checklist items and steps that name something only the core repository has are left out rather than handed over.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'task' => ['type' => 'string', 'minLength' => 1, 'description' => 'Short description of the TYPO3 core task, in English.'],
                'paths' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'The files the task is about, as they are in the repository they belong to. Pass them where the work touches more than one place: each is placed on its own, so a core path and an extension path in one call are not answered with one verdict. An extension key counts as a path. A subsystem no path can be named for belongs in task, because every entry here is answered as a file.'],
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version this task is for, for example "13.4" or "14". Conventions that do not hold there are left out, including those the repository needs for another major it declares. Defaults to every major this repository declares typo3/cms-core for, or to the installation this server was started in where there is no declaration.'],
                'changeType' => ['type' => 'string', 'enum' => ['bugfix', 'feature', 'cleanup', 'test', 'documentation', 'deprecation', self::AUDIT, self::TRIAGE, self::OPERATIONS, self::DIAGNOSIS, 'unknown'], 'default' => 'unknown', 'description' => 'What kind of change the task is. Four of them write no file and get a brief of their own instead of the steps a patch owes: audit asks for what reviewing a body of code needs, triage for what deciding an open bug report needs — whether it still happens, what a previous attempt cost, what a maintainer would need before it can move — operations for what running an installation needs, booting the environment a repository declares, importing its data, building its assets, and diagnosis for what finding the cause of a reported defect needs, before anybody has agreed what to change. Reviewing a report against code, reviewing a diff and saying why something is broken are three briefs and not one. A task that describes any of the four gets that shape without stating the type.'],
            ],
            'required' => ['task'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'task' => Schema::string(),
            'paths' => Schema::listOf(Schema::string(), 'The paths this brief was composed for. Empty where the call named none.'),
            'scopes' => Schema::scopes('Which kind of work each path is. Where every path is outside the core, the core checks, the core-only checklist items and the submission route are left out of the whole brief.'),
            'changeType' => Schema::string(),
            'targetVersion' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major this repository runs — stated by the caller, or read from the installation. Null means nothing was filtered by version. Where the repository serves several majors, targetVersions is what the answer holds for.'],
            'targetVersions' => Schema::listOf(['type' => 'integer'], 'Every TYPO3 major the answer holds for. One entry is the ordinary case. Several mean this repository declares typo3/cms-core for more than one of them, so a statement was kept when it holds on any — and where two statements about the same subject differ, the difference is the constraint the code lives under rather than drift. Empty when nothing was filtered by version.'),
            'domains' => Schema::listOf(Schema::string()),
            'scope' => Schema::scope('Which kind of work the call as a whole reads as. Anything but core means the answer holds core conventions that may transfer, not a checklist for the task. Where the paths disagree, scopes is the answer and this is what the task text alone says.'),
            'intents' => Schema::listOf(Schema::object([
                'id' => Schema::string(),
                'title' => Schema::string(),
                'confidence' => ['type' => 'string', 'enum' => ['strong', 'weak'], 'description' => 'weak: a word named the subject without naming the work, or the intent is a core-only one and nothing in the task says this is core work. Either way it applies only under its condition.'],
                'condition' => Schema::string('When a weakly matched intent applies. Empty for a strong match.'),
            ], ['id', 'title', 'confidence', 'condition']), 'The kinds of core work recognized in the task text.'),
            'staleSkills' => Schema::listOf(Schema::string(), 'Which of the skills above this project holds an older copy of, read from what is published there against what this server would write now. Empty where every named copy is current, where this server never installed into this project, and where no skill was named at all — so it is a subset of skills and never a statement that one is missing. What to do about it is one command, typo3-dev-companion update, and what it costs is a change to the caller\'s own checkout.'),
            'skills' => Schema::listOf(Schema::string(), 'The task skills that own the recognized work, named so that a caller who reached this server without one can load it. A skill is a file in your own project rather than something this server can see, so a name here is not a promise that it is installed. A review, a triage, a boot and a diagnosis name only the workflows that change nothing either: the kind of change under review is still recognized in intents, and the workflow for writing one is not the one you are in. Empty means no published skill owns what was recognized, which is not a statement that the work has no workflow.'),
            'guides' => Schema::listOf(Schema::guideReference(), 'The whole procedures this work is written up in or owes, the same corpus typo3_project_describe lists at orientation and this server serves as typo3://guides resources. Owes is the second half and the one a task text never asks for: a change that ends in a backend UI owes the browser check whether or not it says so. Named rather than carried: a brief is one call inside a procedure, and the page is one typo3_rule_lookup call by documentId. Empty means no page here is the write-up of what was recognized, which is not a statement that none of them is worth reading — the whole list is in that orientation call.'),
            'hints' => Schema::listOf(Schema::hintRecord(), 'What typo3_hint_lookup answers for these paths, quoted whole and carried here — the strongest few per group of paths, not everything it holds on them. A hint declaring a different kind of repository from the paths given ranks below the ones that bind them. A rule taken from one of these belongs to that lookup, so a report citing it names typo3_hint_lookup and a caller who needs more of the subject calls it directly. What was left is named in omittedHints.'),
            'omittedHints' => Schema::listOf(Schema::hintReference(), 'What typo3_hint_lookup also holds for these paths and this brief did not carry, named rather than counted. A hint declared for another kind of repository is here for that reason rather than for having matched weakly. Empty means what it carries is everything that matched. A subject listed here and not in hints is one the brief did not reach, so it is the gap the pointer to that lookup stands for.'),
            'rules' => Schema::listOf(Schema::knowledgeMatch(), 'Rule sections that apply to this task.'),
            'checks' => Schema::listOf(Schema::string(), 'Commands to run, ready to execute from the core root. They '
                . 'are the base suites of the domains above, which run whatever the task turns out to be, plus the '
                . 'ones the recognized work names. This is the list to run: testSuites is a second narrowing of the '
                . 'same corpus, and a suite named there and not here is one to decide about.'),
            'conditionalChecks' => Schema::listOf(Schema::object([
                'title' => Schema::string(),
                'condition' => Schema::string(),
                'checks' => Schema::listOf(Schema::string()),
            ], ['title', 'condition', 'checks']), 'Checks that only apply if the task really is the kind of work a weakly matched intent suggests.'),
            'testSuites' => Schema::listOf(Schema::testSuiteRecord(), sprintf(
                'The suites of those same domains that rank strongest against the task text, %d at most. A selection '
                . 'to pick a targeted run from rather than a list to run — what the task owes in any case is checks '
                . 'above, and neither list holds the other. typo3_test_run_guide called with these paths returns the '
                . 'whole list these were ranked out of.',
                self::SUITES_PER_BRIEF,
            )),
            'checklist' => Schema::listOf(Schema::string()),
            'checkoutDiscovery' => Schema::listOf(Schema::object([
                'establish' => Schema::string(),
                'how' => Schema::string(),
            ], ['establish', 'how']), 'What this server cannot see and the agent has to establish itself.'),
            'nextTools' => Schema::listOf(Schema::nextTool()),
        ], ['task', 'changeType', 'domains', 'skills', 'staleSkills', 'guides', 'hints', 'omittedHints', 'checks', 'checklist', 'nextTools']);
    }

    public static function answer(array $args): ToolResult
    {
        $task = (string) ($args['task'] ?? '');
        $changeType = (string) ($args['changeType'] ?? 'unknown');

        $paths = array_values(array_filter(array_map(
            static fn(mixed $path): string => trim((string) $path),
            $args['paths'] ?? [],
        )));
        $domains = Domains::detect($paths, $task . ' ' . (self::CHANGE_TYPE_TERMS[$changeType] ?? ''));

        // Several of the conventions below — the changelog, the Gerrit
        // workflow, the runTests.sh suites — do not exist outside the core, so
        // handing them over as a checklist for a project extension is worse
        // than saying the question is outside what this server knows. The
        // decision is per path, because a call is not one piece of work, and
        // the brief states it once for what every path shares: the checklist,
        // the checks and the discovery steps are filtered where nothing in the
        // call is core work, and the notice names the paths they are not for
        // where something is (D-SCO-009).
        $scopes = Scope::ofEach($paths, $task);
        $groups = Scope::groups($paths, $scopes, $task);
        $outside = Scope::pathsOf($scopes, Scope::Project, Scope::Extension);
        // One group is the ordinary call, and its scope is the call's. Where
        // the paths disagree there is no one answer, so the whole-call verdict
        // falls back to what the task text says on its own. A group that placed
        // nothing is not that disagreement and does not veto the placement
        // either: it takes the one the rest of the call has (`D-SCO-016`).
        $placed = Scope::placed($groups);
        $scope = count($placed) === 1 ? $placed[0]['scope'] : Scope::of('', $task);
        $outsideCore = Scope::everyPlacedPathIsOutsideTheCore($groups);

        $coreWork = Scope::isCoreWork($paths, $task);
        $statedIntent = self::CHANGE_TYPE_INTENT[$changeType] ?? '';
        $intents = TaskIntents::scoped(
            TaskIntents::detect($task, $statedIntent === '' ? [] : [$statedIntent]),
            $scope,
            $coreWork
        );
        // A stated change type is the caller's own classification and it keeps
        // the skeleton: "review the patch that deprecates X" is authoring work
        // described from the reviewer's side, and a brief that answered it as a
        // review would leave out the steps that patch owes. The same holds for
        // the boot half of "fix the post-start hook so the import runs". What
        // the words add is kept, because the other caller is real too — one who
        // states the type of the patch under review rather than of their own
        // work — and appending is what costs neither of them a step
        // (`D-GUI-009`).
        $stated = !in_array($changeType, [self::AUDIT, self::TRIAGE, self::OPERATIONS, self::DIAGNOSIS, 'unknown'], true);
        $confirmed = TaskIntents::confirmed($intents);
        $confirmedIds = array_column($confirmed, 'id');
        // A task may read and then write, and "find an old Forge issue and fix
        // it" is the shape two sessions asked for. Where the words name the
        // caller's own change, the skeleton is the patch's and the reading half
        // arrives as its intent's own items beside it — one brief, carrying
        // what the change owes (`D-SKL-081`). A stated change type says the
        // same thing by classification and already does this.
        $writes = in_array(self::PATCH, $confirmedIds, true);
        $reading = !$stated && !$writes;
        $triages = $reading && in_array(self::TRIAGE, $confirmedIds, true);
        // A triage is checked before a review, because a task that reads as
        // both is the one this was written from: "review this old bug report"
        // reviews a report and not a diff, and the review arm is the answer
        // that was wrong.
        $reviews = $reading && !$triages && in_array(self::AUDIT, $confirmedIds, true);
        $operates = $reading && in_array(self::OPERATIONS_INTENT, $confirmedIds, true);
        // Last of the four, so a task that reads as a boot and as a diagnosis is
        // a boot: booting is what makes the cause readable, and the diagnosis
        // intent's own items arrive in that brief anyway.
        $diagnoses = $reading && !$operates && in_array(self::DIAGNOSIS, $confirmedIds, true);
        $changesNothing = $reviews || $triages || $operates || $diagnoses;
        // Both halves recognized, which is what makes the two names an order
        // rather than a choice between them.
        $spansBoth = $writes && array_intersect(
            [self::TRIAGE, self::AUDIT, self::OPERATIONS_INTENT, self::DIAGNOSIS],
            $confirmedIds,
        ) !== [];
        $conditional = array_values(array_filter(
            $intents,
            static fn(array $intent): bool => !in_array($intent, $confirmed, true)
        ));
        // The core's own skills own the work only where nothing in the call is
        // outside it. A path in an extension settles the side, and the word
        // "core" in a task text about a sitepackage does not. A brief that
        // changes nothing routes only the workflows that change nothing either:
        // the words of the change under review name what it is about, not what
        // the caller is doing (`D-SKL-039`).
        $skills = TaskIntents::skills($confirmed, $coreWork && !$outsideCore, $changesNothing);
        $guides = self::guideRecords(TaskIntents::guides($confirmed, $coreWork && !$outsideCore, $changesNothing));

        $stated = isset($args['targetVersion']) ? (string) $args['targetVersion'] : null;
        $target = Versions::target($stated);
        $targets = Versions::targets($stated);
        // Matched per group, because a hint matched for a core path and one
        // matched for an extension path are answers to different questions.
        //
        // Matched at the lookup's own ceiling and cut here rather than there:
        // the tier below decides which hints a brief carries, and applied to a
        // slice already taken it would only reorder what it was meant to
        // choose. The same list is what the brief names as left rather than
        // counts (R-GUI-012), so the pointer names what that tool holds for
        // these paths.
        $found = [];
        $held = [];
        foreach ($groups as $group) {
            $matched = Hints::find($group['paths'], $task, HintLookup::MAX_HINTS, null, $targets);
            $ranked = self::bindingFirst($matched['matchedHints'], $group['scope']);
            foreach ($ranked as $hint) {
                $held[(string) $hint['id']] ??= [
                    'id' => (string) $hint['id'],
                    'title' => (string) $hint['title'],
                    'category' => (string) $hint['category'],
                ];
            }
            $matched['matchedHints'] = array_slice($ranked, 0, self::HINTS_PER_GROUP);
            $found[] = ['scope' => $group['scope'], 'paths' => $group['paths'], 'result' => $matched];
        }
        $hints = MatchedHints::merged($found);
        // Per call rather than per group: a hint the brief carries for one group
        // of paths is in the answer, whichever group left it.
        $omitted = array_values(array_diff_key(
            $held,
            array_flip(array_column($hints['matchedHints'], 'id')),
        ));
        $testHints = array_slice(TestSuiteHints::find($task, $domains, $target), 0, self::SUITES_PER_BRIEF);

        $lines = [];
        if ($outsideCore) {
            $lines[] = Scope::OUTSIDE_CORE_NOTICE . ' Take what follows as conventions that may transfer, not as '
                . 'a checklist for this task. '
                . 'typo3_server_scope states the boundary.';
            $lines[] = '';
        } elseif ($outside !== []) {
            $lines[] = Scope::outsideCoreAmong($outside)
                . ' The checks below, the changelog and the submission route belong to the core repository, so '
                . 'they are steps for the paths that are in it and for none of the others.';
            $lines[] = '';
        }
        // The checklist below is the one payload of this server that states a
        // process as the process, so where nothing placed the work it says so
        // before stating it — the changelog and the Gerrit route are steps a
        // caller in their own repository cannot take at all.
        if ($scope === Scope::Uncertain) {
            $lines[] = Scope::UNCERTAIN_NOTICE . ' Name a path the work touches, and this brief is composed '
                . 'for the repository it is in.';
            $lines[] = '';
        }
        // Said once, because the steps a caller expects and does not find are
        // the ones they go looking for a second time.
        if ($changesNothing) {
            $lines[] = 'This is a brief for work that changes nothing, so what a patch owes — the deprecation '
                . 'sweep, the focused diff, the test coverage, the commit message — is left out below. Pass '
                . 'changeType where the task does change something.';
            $lines[] = '';
        }

        $lines = array_merge($lines, [
            'Task: ' . $task,
            'Change type: ' . $changeType,
            'Domains: ' . implode(', ', $domains),
        ]);
        // Named with what each was placed as, because the point of passing them
        // is that the caller can tell which half of the brief is about which of
        // its files. Also where there is one: the verdict on it is what every
        // filtered list below was filtered by.
        if ($paths !== []) {
            $lines[] = "Paths:\n" . implode("\n", array_map(
                static fn(array $entry): string => '- ' . $entry['path']
                    . ($entry['scope'] === Scope::Core ? '' : ' (' . $entry['scope']->value . ')'),
                $scopes,
            ));
        }
        // Silent on the ordinary task, where one version is the whole question
        // and saying so is noise. It speaks for the repository that serves
        // several majors — whether the answer holds for all of them, or was
        // narrowed to one because the caller stated it.
        if (count($targets) > 1 || VersionScope::severalDeclared() !== []) {
            $lines[] = VersionScope::line($targets);
        }
        if ($confirmed !== []) {
            $lines[] = 'Recognized as: ' . implode(', ', array_map(
                static fn(array $intent): string => (string) $intent['title'],
                $confirmed
            ));
        }
        foreach ($conditional as $intent) {
            $lines[] = 'Possibly also: ' . $intent['title'] . ', ' . $intent['condition']
                . '. Its checklist items are marked as conditional below and its checks are listed separately.';
        }
        // Above the payload rather than under it: a caller that is in the wrong
        // workflow is in it for the whole answer, and the line is worth nothing
        // once the reading has started.
        $stale = Installer::behind(Instance::startedFrom() ?? '', $skills);
        if ($skills !== []) {
            $lines[] = sprintf(
                $spansBoth && count($skills) > 1 ? self::SKILLS_IN_ORDER : self::SKILLS_OWNING,
                implode(', ', $skills),
            );
        }
        if ($stale !== []) {
            $lines[] = sprintf(
                self::SKILLS_STALE,
                implode(', ', $stale),
                count($stale) === 1 ? 'it' : 'them',
            );
        }
        // Under the skill and above the payload for the same reason, and two
        // lines rather than one: the skill is a file in the caller's own
        // project and the guide is a page here, so a session that has neither
        // installed nor listed still gets the one it can reach.
        if ($guides !== []) {
            $lines[] = self::GUIDES_OWNING;
            foreach ($guides as $guide) {
                $lines[] = sprintf('- %s — %s. %s', $guide['id'], $guide['title'], $guide['when']);
            }
        }

        $lines[] = '';
        $lines[] = 'Hints:';
        if ($hints['matchedHints'] !== []) {
            // Said above the blocks rather than under them, because what it
            // corrects is a citation and the citation is written while the
            // block is being read.
            $lines[] = self::HINTS_SOURCE;
            // Which of the two follows is what the brief actually did, not a
            // standing disclaimer: a caller told there is more when there is
            // not spends the call the pointer promised on nothing.
            if ($omitted !== []) {
                $lines[] = sprintf(self::HINTS_TRUNCATED, self::HINTS_PER_GROUP);
                $lines[] = sprintf(self::HINTS_OMITTED, implode(', ', array_column($omitted, 'id')));
            } else {
                $lines[] = self::HINTS_COMPLETE;
            }
            $lines[] = '';
            // One block per group, and the heading only where there is more
            // than one: the caller named two repositories, and which half of
            // the brief is about which path is half of the answer.
            $sectionTexts = [];
            foreach ($found as $group) {
                if ($group['result']['matchedHints'] === []) {
                    continue;
                }
                if (count($found) > 1) {
                    $sectionTexts[] = sprintf(
                        '# For %s%s',
                        implode(' and ', $group['paths']),
                        $group['scope'] === Scope::Core ? '' : ' — ' . $group['scope']->value,
                    );
                }
                $sectionTexts[] = MatchedHints::sections(
                    $group['result']['matchedHints'],
                    $group['scope'],
                    $target,
                );
            }
            $lines[] = implode("\n\n", $sectionTexts);
        } else {
            $lines[] = '- No hint matched this task text. That means no convention was recognized, '
                . 'not that none applies: call typo3_hint_lookup again with the concrete file paths once they are known.';
        }

        // Only the confirmed intents may state a rule as applying: a
        // conditionally matched one would fill the whole section with rules for
        // work the task may not contain at all.
        $rules = TaskIntents::rules($confirmed, 2, $targets);
        if ($rules !== []) {
            $lines[] = '';
            $lines[] = 'Rules that apply to this task:';
            $lines[] = '';
            $lines[] = Prose::sections($rules, $outsideCore);
        }

        // The checks of a matched hint belong in the list as much
        // as the ones an intent carries. Leaving them out dropped the functional
        // suite from a FormEngine brief while the FormEngine hint that names it
        // was right there in the same answer.
        $checks = self::mergedChecks($confirmed, $domains, $target);
        $conditionalChecks = self::conditionalChecks($conditional, $checks, $target);

        // Every check this server knows is a runTests.sh invocation against a
        // script in the core repository. Reporting a scope outside the core and then listing
        // four of them was the whole complaint: the flag said the answer knew,
        // and the payload said it had not acted on it.
        if ($outsideCore) {
            $checks = [];
            $conditionalChecks = [];
            $testHints = [];
        }

        $lines[] = '';
        if ($outsideCore) {
            $lines[] = 'Checks: none of the core\'s own apply here, so none is listed. Verify with what this '
                . 'repository provides — the scripts in its composer.json, its package.json, and its CI '
                . 'configuration are where its own suites are declared.';
        } else {
            $lines[] = 'Relevant TYPO3 core checks — the list to run, whatever this task turns out to be:';
            foreach ($checks as $check) {
                $lines[] = '- `' . $check . '`';
            }
            if ($testHints !== []) {
                // The two lists sit next to each other and are two narrowings of
                // one corpus, which is what the reporting session had no way to
                // read: it took the shorter one for the authoritative one and
                // the longer one for suites the first had dropped
                // (`D-ANS-108`).
                $lines[] = '';
                $lines[] = 'Suites that match this task, strongest first. Each is one to decide about rather than '
                    . 'one the list above left out, and typo3_test_run_guide holds the rest for these paths.';
                foreach ($testHints as $hint) {
                    $lines[] = '## ' . $hint['suite'];
                    $lines[] = '`' . $hint['command'] . '`';
                    if ($hint['targeted'] !== null) {
                        $lines[] = 'Targeted: `' . $hint['targeted'] . '`';
                    }
                    $lines[] = $hint['whenToUse'];
                }
            } elseif ($checks === []) {
                $lines[] = '- No topic-specific check matched. Run the narrowest relevant suite, then broaden before review.';
            }

            foreach ($conditionalChecks as $entry) {
                $lines[] = '';
                $lines[] = 'Checks for ' . $entry['title'] . ', ' . $entry['condition'] . ':';
                foreach ($entry['checks'] as $check) {
                    $lines[] = '- `' . $check . '`';
                }
            }
        }

        // Three of the five in the last arm are steps a review does not take,
        // and handing them to one was the whole of R-GUI-006. What replaces
        // them is the reading a finding rests on rather than a second copy of an
        // audit workflow: what the review is about is the audit intent's
        // checklist. The arms before it are each other's counterparts — a review
        // owes the gaps it left, a boot owes what it produced, a diagnosis owes
        // the reading each half of its answer came from — and none owes the
        // others', which is why the shapes that write no file are four arms
        // rather than one (D-GUI-008, D-SKL-065).
        //
        // Each arm carries only what its intent does not: this is the skeleton
        // the intent's own items are appended to, and the arm is chosen by that
        // intent having matched.
        //
        // The premise opens all three. R-GUI-008 is about every brief, and the
        // review is the case it was written from: a session that never asks
        // what the change does to the editor and the visitor reads a report as
        // an API question and answers the wrong one.
        if ($triages) {
            $checklist = [
                self::PRODUCT_PREMISE,
                'Inspect nearby code, tests, and established subsystem conventions. What the report describes is '
                    . 'usually reachable from the test the subsystem already has, and a defect the suite carries '
                    . 'switched off is the cheapest reproduction there is.',
                'Report what the triage did not reach — a version it was not tried on, a configuration the report '
                    . 'names and nothing here could build. Silence there reads as a verdict.',
            ];
        } elseif ($reviews) {
            $checklist = [
                self::PRODUCT_PREMISE,
                'Establish what this repository is before reading a file: the TYPO3 and PHP versions it supports, '
                    . 'and the commands it declares. Whether a finding holds is a property of them.',
                'Inspect nearby code, tests, and established subsystem conventions.',
                'Report what the review did not reach — a surface the request left out, a subsystem this '
                    . 'repository does not ship, a claim no read code settles. Silence there reads as coverage.',
            ];
        } elseif ($operates) {
            $checklist = [
                self::PRODUCT_PREMISE,
                'Establish what this repository declares before running anything: the TYPO3 and PHP it runs, the '
                    . 'environment it ships, and the commands it declares to start, import and build. What boots '
                    . 'this installation is those files rather than a procedure that holds for every project.',
                'Read the state you are starting from: which steps have run already, and what a failed attempt '
                    . 'left behind. A command that is safe against an empty installation is not safe against a '
                    . 'half-built one.',
                'Report what nobody else can see afterwards: the URL the installation answers on, the backend '
                    . 'user that now exists with the values you chose for it, and every step you had to correct '
                    . 'by hand. What is not written down is derived again by the next session.',
            ];
        } elseif ($diagnoses) {
            $checklist = [
                self::PRODUCT_PREMISE,
                'Establish what this installation is before opening a file: the TYPO3 and PHP it runs, which '
                    . 'extensions are active in it, and which of them are this project\'s own. Whether a cause '
                    . 'is even reachable is a property of those three.',
                'Name the file and the reason together, each with the reading it was established from. A cause '
                    . 'that can name neither is a hypothesis, and reporting it as one is the answer rather than '
                    . 'a weaker version of it.',
                'Stop at the finding. The fix is the next task and owes what a patch owes, so say what it would '
                    . 'change and what would prove it — and change nothing here.',
                'Report what the diagnosis did not reach: a reading that was not available, a half the evidence '
                    . 'does not settle. Silence there reads as the cause being established.',
            ];
        } else {
            $checklist = [
                self::PRODUCT_PREMISE,
                $outsideCore
                    ? 'Confirm the target branch and the issue context of this repository.'
                    : 'Confirm the target TYPO3 core branch and issue context.',
                'Inspect nearby code, tests, and established subsystem conventions.',
                sprintf(
                    self::DEPRECATION_SWEEP,
                    $targets === []
                        ? 'at each TYPO3 major this repository declares'
                        : 'at TYPO3 ' . VersionScope::majorList($targets),
                ),
                'Keep the patch focused on the stated task.',
                'Add or update the narrowest useful test coverage.',
                'Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.',
            ];
        }
        foreach (self::CHANGE_TYPE_CHECKLIST[$changeType] ?? [] as $entry) {
            $checklist[] = $entry;
        }
        foreach ($confirmed as $intent) {
            foreach ($intent['checklist'] as $entry) {
                $checklist[] = (string) $entry;
            }
        }
        foreach ($conditional as $intent) {
            foreach ($intent['checklist'] as $entry) {
                $checklist[] = ucfirst((string) $intent['condition']) . ': ' . lcfirst((string) $entry);
            }
        }
        if (!$changesNothing) {
            $checklist[] = $outsideCore
                ? 'Write the commit message with typo3_commit_message_guide: '
                    . 'summarize the changed behavior, the affected area and the commands you ran, and it '
                    . 'hands back a draft that is wrapped and checked.'
                : 'Write the commit message with typo3_commit_message_guide and workflow="core": summarize the changed behavior, '
                    . 'the affected area and the commands you ran, and it hands back a draft that carries '
                    . 'the keyword, the trailers and the wrapping.';
        }

        // Per line, not per section: a checklist mixes "reproduce the bug with
        // a failing test" — true anywhere — with a changelog file below
        // typo3/sysext/, which is a path the caller's repository does not have.
        if ($outsideCore) {
            $checklist = array_values(array_filter(
                $checklist,
                static fn(string $entry): bool => !Scope::isCoreOnly($entry)
            ));
        }

        $lines[] = '';
        $lines[] = 'Suggested checklist:';
        foreach ($checklist as $entry) {
            $lines[] = '- ' . $entry;
        }

        // The brief is assembled from bundled knowledge alone, so everything
        // that depends on the working tree is the agent's job. Saying which
        // parts those are — and how to get them — is more useful than letting
        // the checklist read as if the brief had already looked.
        $checkoutDiscovery = Coverage::read()['checkoutDiscovery'];
        if ($outsideCore) {
            $checkoutDiscovery = array_values(array_filter(
                $checkoutDiscovery,
                static fn(array $entry): bool => !Scope::isCoreOnly($entry['establish'] . ' ' . $entry['how'])
            ));
        }
        $lines[] = '';
        $lines[] = 'Establish in your checkout — this server cannot see it:';
        foreach ($checkoutDiscovery as $entry) {
            $lines[] = '- ' . $entry['establish'] . "\n  " . $entry['how'];
        }

        $nextTools = self::nextTools(
            $intents,
            $domains,
            array_column($hints['matchedHints'], 'id'),
            $target,
            $outsideCore,
            $changesNothing
        );
        $lines[] = '';
        $lines[] = 'Next lookups for this task:';
        foreach ($nextTools as $suggestion) {
            $lines[] = '- ' . $suggestion['tool']
                . ($suggestion['when'] === '' ? '' : ' — ' . $suggestion['when']);
        }

        return ToolResult::create(implode("\n", $lines), [
            'task' => $task,
            'paths' => $paths,
            'scopes' => array_map(static fn(array $entry): array => [
                'path' => $entry['path'],
                'scope' => $entry['scope']->value,
            ], $scopes),
            'changeType' => $changeType,
            'targetVersion' => $target,
            'targetVersions' => $targets,
            'domains' => $domains,
            'scope' => $scope->value,
            'intents' => array_map(static fn(array $intent): array => [
                'id' => (string) $intent['id'],
                'title' => (string) $intent['title'],
                'confidence' => (string) $intent['confidence'],
                'condition' => (string) $intent['condition'],
            ], $intents),
            'skills' => $skills,
            'staleSkills' => $stale,
            'guides' => $guides,
            'hints' => MatchedHints::records($hints['matchedHints']),
            'omittedHints' => $omitted,
            'rules' => Prose::records($rules),
            'checks' => $checks,
            'conditionalChecks' => $conditionalChecks,
            'testSuites' => TestSuiteHints::records($testHints),
            'checklist' => $checklist,
            'checkoutDiscovery' => $checkoutDiscovery,
            'nextTools' => $nextTools,
        ]);
    }

    /**
     * The documents an intent named, with the titles a caller picks one by.
     *
     * A document whose file is gone is dropped rather than named: an id that
     * answers nothing is worse than no pointer, and `KnowledgeTest` fails on
     * the same mapping so nobody finds out here.
     *
     * @param array<int, string> $ids
     * @return array<int, array{id: string, title: string, when: string}>
     */
    private static function guideRecords(array $ids): array
    {
        $documents = array_column(Documents::documents(), null, 'id');

        $records = [];
        foreach ($ids as $id) {
            if (isset($documents[$id])) {
                $records[] = Documents::reference($documents[$id]);
            }
        }

        return $records;
    }

    /**
     * The matched hints with the ones declared for another repository last.
     *
     * A brief carries `HINTS_PER_GROUP` per group, and there a hint about
     * somebody else's repository takes the place of one the caller is obliged
     * by: a build is described in the same words wherever it runs, so
     * `extension-asset-build` outranked `backend-typescript` on a core patch
     * (`D-ANS-097`, `R-ANS-033`). Nothing is dropped — `D-KNW-007` stands, and
     * what moves past the slice the brief names in `omittedHints`.
     *
     * What is demoted is what `MatchedHints::scopeNotice()` has something to say
     * about, so the order and the notice above a block answer one question and
     * not two. Within each tier the matcher's own ranking stands.
     *
     * @param array<int, array<string, mixed>> $hints
     * @return array<int, array<string, mixed>>
     */
    private static function bindingFirst(array $hints, Scope $of): array
    {
        $binding = [];
        $elsewhere = [];
        foreach ($hints as $hint) {
            if (MatchedHints::scopeNotice($hint, $of) === null) {
                $binding[] = $hint;
                continue;
            }
            $elsewhere[] = $hint;
        }

        return array_merge($binding, $elsewhere);
    }

    /**
     * The checks a brief states as applying: the base suites of the domains the
     * task is in, then those of the confirmed intents, deduplicated.
     *
     * The base suites come first because they hold whatever the task turns out
     * to be, and because nothing else states them: an intent is recognised from
     * the words of the task, and a bugfix in FormEngine names no suite in any
     * of its words.
     *
     * @param array<int, array<string, mixed>> $intents
     * @param array<int, string> $domains
     * @return array<int, string>
     */
    private static function mergedChecks(array $intents, array $domains, ?int $target): array
    {
        $checks = [];
        foreach (TestSuiteHints::baseFor($domains, $target) as $command) {
            $checks[$command] = true;
        }
        foreach ($intents as $intent) {
            foreach ($intent['checks'] as $check) {
                $checks[(string) $check] = true;
            }
        }

        // An intent names suites that a given branch's runTests.sh may not have.
        return TestSuiteHints::checksFor(array_keys($checks), $target);
    }

    /**
     * The checks of the conditionally matched intents, minus the ones already
     * stated as applying.
     *
     * @param array<int, array<string, mixed>> $intents
     * @param array<int, string> $stated
     * @return array<int, array{title: string, condition: string, checks: array<int, string>}>
     */
    private static function conditionalChecks(array $intents, array $stated, ?int $target): array
    {
        $entries = [];
        foreach ($intents as $intent) {
            $checks = array_values(array_diff(
                TestSuiteHints::checksFor(array_map('strval', $intent['checks']), $target),
                $stated,
            ));
            if ($checks === []) {
                continue;
            }
            $entries[] = [
                'title' => (string) $intent['title'],
                'condition' => (string) $intent['condition'],
                'checks' => $checks,
            ];
        }

        return $entries;
    }

    /**
     * Routes to the specialised tools, so an agent that starts here learns that
     * they exist instead of writing markup or label keys from memory.
     *
     * @param array<int, array<string, mixed>> $intents
     * @param array<int, string> $domains
     * @param array<int, string> $hintIds ids of the hints this brief matched
     * @return array<int, array{tool: string, when: string}>
     */
    private static function nextTools(
        array $intents,
        array $domains,
        array $hintIds,
        ?int $target,
        bool $outsideCore,
        bool $changesNothing,
    ): array {
        $candidates = [];
        foreach ($intents as $intent) {
            foreach ($intent['tools'] as $tool) {
                $candidates[] = (string) $tool;
            }
        }

        if (array_intersect([Domains::CSS, Domains::FLUID], $domains) !== []) {
            $candidates[] = 'typo3_component_lookup, before writing backend markup or CSS classes';
        }
        // A subject whose hint matched is a subject the caller is about to write
        // in, and both of these answer from the installation rather than from
        // memory. The pointer is in the hint text as well, which is exactly the
        // place nobody rereads while writing the fortieth label key.
        foreach (self::HINT_TOOLS as $hintId => $suggestion) {
            if (in_array($hintId, $hintIds, true)) {
                $candidates[] = $suggestion;
            }
        }
        $candidates[] = $target === null
            ? 'typo3_changelog_lookup, for what the version you are building on changed about this area'
            : sprintf(
                'typo3_changelog_lookup, for what %d changed about this area — the first stop when you have not '
                    . 'built on it recently, not only a lookup after the fact',
                $target
            );
        $candidates[] = 'typo3_hint_lookup with the concrete file paths, once they are known';
        // What the round trip buys, and nothing more: the `testSuites` above are
        // the strongest few of the list it returns, so what it adds is the rest
        // of them and the invocation notes (`D-KNW-067`).
        $candidates[] = 'typo3_test_run_guide, for the targeted runTests.sh invocation — it lists every suite these domains hold, of which the testSuites above are the strongest few';
        // The one step this brief describes and never pointed at. A caller who
        // read the routing table at the start of a session is committing hours
        // later, from this list — and outside the core it is the follow-up call
        // that has to carry the workflow, because the guide defaults to the
        // core's and cannot read a repository off a commit message. The
        // checklist above says it; a list of calls that leaves it out is one
        // answer disagreeing with itself about the same step — which is why a
        // review, whose checklist ends without it, does not point at it either.
        if (!$changesNothing) {
            $candidates[] = $outsideCore
                ? 'typo3_commit_message_guide, before committing — its default is this repository\'s case and '
                    . 'demands neither an issue number nor a release trailer'
                : 'typo3_commit_message_guide with workflow="core", before committing — the default is a '
                    . 'repository of your own and demands no Forge issue or release trailer';
        }
        // This call again. It is made once, against the request, which is the
        // moment least is known about the work — so what the brief adds is the
        // acts to ask at, which are the caller's own and can be named without
        // seeing the checkout (`D-SKL-062`). The instructions carry the same
        // acts in a shorter form, for the session that never asked for a brief.
        $candidates[] = 'typo3_task_guide again, where the work enters a subject this task did not name — the '
            . 'first file under a test directory, the first run of a check the repository declares, the first '
            . 'branch or commit, the first edit to code or documentation the package ships';
        if (Channel::isAvailable()) {
            $candidates[] = 'typo3_feedback_record, when one of these answers was wrong or incomplete';
        }

        // What only the core has goes before that, not after it: an intent's
        // own wording is what the check reads, and the generic candidate for
        // the same tool — which does name the artefact — is what would be left
        // to carry the caller outside the core (`D-SCO-015`).
        if ($outsideCore) {
            $candidates = array_filter(
                $candidates,
                static fn(string $candidate): bool => !Scope::isCoreOnly($candidate)
            );
        }

        // One entry per tool: an intent that already suggested a tool keeps its
        // own wording, the generic fallback for that tool is dropped.
        $suggestions = [];
        foreach ($candidates as $candidate) {
            $tool = strtok($candidate, ' ,');
            if ($tool === false || isset($suggestions[$tool])) {
                continue;
            }
            $suggestions[$tool] = [
                'tool' => $tool,
                // The candidates are written as one sentence, "tool, when", so
                // the separator has to come off with the tool name — otherwise
                // both halves carry it and the answer reads "tool , when".
                'when' => ltrim(substr($candidate, strlen($tool)), ' ,'),
            ];
        }

        return array_values($suggestions);
    }
}
