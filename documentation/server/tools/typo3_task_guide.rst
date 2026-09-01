.. _typo3_task_guide:

``typo3_task_guide``
====================

Answers what one change owes, which a repository's own conventions file cannot:
that file states its rules once for every task, and this narrows them to the
kind of change, the paths and the TYPO3 majors in front of you — down to whether
this fix owes a changelog entry. Where such a rule stays general it names the
call that settles it, the branches a Releases: trailer takes among them. The
answer is a task checklist with the hints and core checks that match. Not only
for work that ends in a patch: deciding whether an open bug report still holds
is changeType "triage", reviewing a body of code is "audit", bringing an
installation up is "operations", and finding out why something is broken before
anybody changes it is "diagnosis" — all four get a brief of their own rather
than the steps a patch owes. Built from bundled conventions only: it does not
read your checkout, so it also names what you have to establish there yourself,
routes to the lookups that fit the task, and names the task skill that owns the
work where a published one does, beside the guide the work is written up in
where this server carries one. Work that reads as a project or third-party
extension is answered with what transfers only — the core checks, checklist
items and steps that name something only the core repository has are left out
rather than handed over. Answers from: knowledge.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`knowledge <answer-sources-knowledge>`.

Takes
-----

.. code-block:: yaml

    # Short description of the TYPO3 core task, in English.
    task: string
    # The files the task is about, as they are in the repository they belong to.
    # Pass them where the work touches more than one place: each is placed on its
    # own, so a core path and an extension path in one call are not answered with
    # one verdict. An extension key counts as a path. A subsystem no path can be
    # named for belongs in task, because every entry here is answered as a file.
    paths: [string]  # optional
    # The TYPO3 version this task is for, for example "13.4" or "14". Conventions
    # that do not hold there are left out, including those the repository needs for
    # another major it declares. Defaults to every major this repository declares
    # typo3/cms-core for, or to the installation this server was started in where
    # there is no declaration.
    targetVersion: string  # optional
    # One of: bugfix, feature, cleanup, test, documentation, deprecation, audit,
    # triage, operations, diagnosis, unknown. What kind of change the task is. Four
    # of them write no file and get a brief of their own instead of the steps a
    # patch owes: audit asks for what reviewing a body of code needs, triage for
    # what deciding an open bug report needs — whether it still happens, what a
    # previous attempt cost, what a maintainer would need before it can move —
    # operations for what running an installation needs, booting the environment a
    # repository declares, importing its data, building its assets, and diagnosis
    # for what finding the cause of a reported defect needs, before anybody has
    # agreed what to change. Reviewing a report against code, reviewing a diff and
    # saying why something is broken are three briefs and not one. A task that
    # describes any of the four gets that shape without stating the type.
    changeType: string  # optional

Answers with
------------

.. code-block:: yaml

    task: string
    # The paths this brief was composed for. Empty where the call named none.
    paths: [string]  # optional
    # Which kind of work each path is. Where every path is outside the core, the
    # core checks, the core-only checklist items and the submission route are left
    # out of the whole brief.
    scopes:  # optional
      - path: string
        # One of: core, uncertain, project, extension. Which kind of work this
        # answer is for: core, a patch to the TYPO3 core itself; project, the site
        # repository around an installation; extension, a package in it, whether a
        # sitepackage or a third-party one; or uncertain, which means nothing in the
        # call placed the work and what came back is the core's own.
        scope: string
    changeType: string
    # The TYPO3 major this repository runs — stated by the caller, or read from
    # the installation. Null means nothing was filtered by version. Where the
    # repository serves several majors, targetVersions is what the answer holds for.
    targetVersion: integer or null  # optional
    # Every TYPO3 major the answer holds for. One entry is the ordinary case.
    # Several mean this repository declares typo3/cms-core for more than one of
    # them, so a statement was kept when it holds on any — and where two
    # statements about the same subject differ, the difference is the constraint the
    # code lives under rather than drift. Empty when nothing was filtered by
    # version.
    targetVersions: [integer]  # optional
    domains: [string]
    # One of: core, uncertain, project, extension. Which kind of work the call as a
    # whole reads as. Anything but core means the answer holds core conventions that
    # may transfer, not a checklist for the task. Where the paths disagree, scopes
    # is the answer and this is what the task text alone says.
    scope: string  # optional
    # The kinds of core work recognized in the task text.
    intents:  # optional
      - id: string
        title: string
        # One of: strong, weak. weak: a word named the subject without naming the
        # work, or the intent is a core-only one and nothing in the task says this
        # is core work. Either way it applies only under its condition.
        confidence: string
        # When a weakly matched intent applies. Empty for a strong match.
        condition: string
    # Which of the skills above this project holds an older copy of, read from what
    # is published there against what this server would write now. Empty where every
    # named copy is current, where this server never installed into this project,
    # and where no skill was named at all — so it is a subset of skills and never
    # a statement that one is missing. What to do about it is one command,
    # typo3-dev-companion update, and what it costs is a change to the caller's own
    # checkout.
    staleSkills: [string]
    # The task skills that own the recognized work, named so that a caller who
    # reached this server without one can load it. A skill is a file in your own
    # project rather than something this server can see, so a name here is not a
    # promise that it is installed. A review, a triage, a boot and a diagnosis name
    # only the workflows that change nothing either: the kind of change under review
    # is still recognized in intents, and the workflow for writing one is not the
    # one you are in. Empty means no published skill owns what was recognized, which
    # is not a statement that the work has no workflow.
    skills: [string]
    # The whole procedures the recognized work is written up in, the same corpus
    # typo3_project_describe lists at orientation and this server serves as
    # typo3://guides resources. Named rather than carried: a brief is one call
    # inside a procedure, and the page is one typo3_rule_lookup call by documentId.
    # Empty means no page here is the write-up of what was recognized, which is not
    # a statement that none of them is worth reading — the whole list is in that
    # orientation call.
    guides:
      - # What typo3_rule_lookup takes as documentId to return the whole document.
        id: string
        title: string
        # What the caller has to be doing for this page to be the one to read.
        when: string
        # The tool that takes the id above and returns the page whole.
        tool: string
    # What typo3_hint_lookup answers for these paths, quoted whole and carried here
    # — the strongest few per group of paths, not everything it holds on them. A
    # hint declaring a different kind of repository from the paths given ranks below
    # the ones that bind them. A rule taken from one of these belongs to that
    # lookup, so a report citing it names typo3_hint_lookup and a caller who needs
    # more of the subject calls it directly. What was left is named in omittedHints.
    hints:
      - id: string
        title: string
        # PHP, TypeScript, JavaScript, CSS, or General.
        category: string
        # One of: core, project, extension, null. Which kind of work the whole hint
        # obliges. "core" means it is a condition of a patch to the TYPO3 core and a
        # convention anywhere else — the backend's own design system, the
        # changelog artifact, the paths of the mono repository. "project" and
        # "extension" are the mirror: what the repository around an installation, or
        # a package distributed on its own, has to do, and what is context rather
        # than a condition inside the core. Null, the ordinary case, means it holds
        # wherever TYPO3 is written: an API that throws throws in a sitepackage too.
        scope: string or null
        hints:
          - # The statement itself. It reads the same on every version it holds for;
            # the range is beside it, never inside it.
            text: string
            # First TYPO3 major this holds on. Null means as far back as this
            # knowledge base reaches.
            since: integer or null
            # Last TYPO3 major this holds on. Null means it still holds.
            until: integer or null
            # The same range as a sentence, empty when the statement is bound to
            # nothing.
            versions: string
            # One of: core, project, extension, null. Which kind of work this
            # statement obliges. "core" means it is a condition of a patch to the
            # TYPO3 core and a convention anywhere else — the backend's own design
            # system, the changelog artifact, the paths of the mono repository.
            # "project" and "extension" are the mirror: what the repository around
            # an installation, or a package distributed on its own, has to do, and
            # what is context rather than a condition inside the core. Null, the
            # ordinary case, means it holds wherever TYPO3 is written: an API that
            # throws throws in a sitepackage too.
            scope: string or null
    # What typo3_hint_lookup also holds for these paths and this brief did not
    # carry, named rather than counted. A hint declared for another kind of
    # repository is here for that reason rather than for having matched weakly.
    # Empty means what it carries is everything that matched. A subject listed here
    # and not in hints is one the brief did not reach, so it is the gap the pointer
    # to that lookup stands for.
    omittedHints:
      - # Ask for this hint outright by passing it as id.
        id: string
        title: string
        # PHP, TypeScript, JavaScript, CSS, or General.
        category: string
    # Rule sections that apply to this task.
    rules:  # optional
      - documentId: string
        # Title of the knowledge document.
        title: string
        # typo3://guides resource holding the full document.
        uri: string
        # Heading of the matched section.
        heading: string
        # The section as written, formatting included.
        body: string
        # The TYPO3 majors this section holds for, in words. Empty means every
        # covered major, which is what a section that declares nothing says.
        versions: string  # optional
        # Share of the query terms the section covers, 0 to 1. Zero where no search
        # ranked this record, which is a page the caller named by documentId.
        coverage: number
        # Weighted match score; headings weigh more than body text. Zero where no
        # search ranked this record.
        score: integer
        # Whether the body was cut; read the resource for the rest.
        truncated: boolean
    # Commands to run, ready to execute from the core root. They are the base suites
    # of the domains above, which run whatever the task turns out to be, plus the
    # ones the recognized work names. This is the list to run: testSuites is a
    # second narrowing of the same corpus, and a suite named there and not here is
    # one to decide about.
    checks: [string]
    # Checks that only apply if the task really is the kind of work a weakly matched
    # intent suggests.
    conditionalChecks:  # optional
      - title: string
        condition: string
        checks: [string]
    # The suites of those same domains that rank strongest against the task text, 4
    # at most. A selection to pick a targeted run from rather than a list to run —
    # what the task owes in any case is checks above, and neither list holds the
    # other. typo3_test_run_guide called with these paths returns the whole list
    # these were ranked out of.
    testSuites:  # optional
      - suite: string
        # Full command, run from the core root.
        command: string
        # One of: check, change, git, unknown. What running the command does to the
        # checkout, read off the suite's body in Build/Scripts/runTests.sh rather
        # than by running it. The values typo3_project_describe gives a declared
        # command, plus one for the suites that run git. check: it reports and hands
        # the files back as they were, so a task told not to change files can run it
        # — installing its own node_modules or writing a cache is not a change.
        # change: it rewrites files, generated or installed. git: it runs git over
        # the working tree, so `git add *` stages what it finds, untracked files
        # included, and a suite of this kind may discard uncommitted edits first.
        # unknown: the body does not say, which is what a test suite is, because it
        # runs the core's own code.
        runs: string
        # Narrowed form for iterating on a single file or test. It can run
        # differently from command — `-s cgl -n` reports where `-s cgl` rewrites
        # — and runs above answers for command.
        targeted: string or null
        description: string  # optional
        whenToUse: string  # optional
        domains: [string]  # optional
        # The TYPO3 majors whose runTests.sh has this suite, where that is not all
        # of them. Null means every covered version.
        versions: string or null
    checklist: [string]
    # What this server cannot see and the agent has to establish itself.
    checkoutDiscovery:  # optional
      - establish: string
        how: string
    nextTools:
      - tool: string
        # What to pass and why this call is the next one.
        when: string

Answered
--------

Derived by ``bin/cli tools:index``, and ``bin/cli tools:check`` holds it —
the same as everything above this heading. This tool reads nothing an
installation contains: what reaches its answer is the bundled knowledge and
which TYPO3 major the caller is on, so what comes back is written down rather
than recorded from one machine's checkout. Answered against the core checkout
this repository writes below .fixtures/, declaring TYPO3 14.3.0.

brief: with a path
~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "task": "Deprecate a public method",
        "paths": [
            "typo3/sysext/core/Classes/Utility/GeneralUtility.php"
        ],
        "changeType": "cleanup"
    }

Text:

.. code-block:: text

    Task: Deprecate a public method
    Change type: cleanup
    Domains: php
    Paths:
    - typo3/sysext/core/Classes/Utility/GeneralUtility.php
    Recognized as: Deprecation, Putting a repository right
    Owned by: typo3-core-patch-development. Load it where this project has it installed — the skill carries the working order for this kind of work, and this brief is one call inside it.

    Hints:
    The hints below are typo3_hint_lookup's, matched for these paths and quoted whole. A finding that cites one of these rules is citing that lookup rather than this guide.
    These are everything typo3_hint_lookup matches for these paths, so calling it again by path adds nothing; a subject it holds under another path or id is still a call away.

    ### PHP

    ## System Extension Boundaries
    Hints:
    - Keep changes inside the owning system extension unless a cross-extension contract really changes.
    - Reuse public APIs from other system extensions instead of depending on internal implementation details.
    - Check nearby extension-local tests before adding shared behavior.

    ## Deprecated APIs
    Hints:
    - Whether an API is deprecated is a property of the branch you work on, not of TYPO3 as a whole. Where you work in an installation the server reads that branch: typo3_project_describe names the TYPO3 version installed there, and typo3_changelog_lookup answers from the changelog its core package ships.
    - Read the declaration itself: an @deprecated annotation together with a trigger_error(..., E_USER_DEPRECATED) call is what marks one. The core marks nothing with PHP's #[\Deprecated] attribute, so finding none says nothing about what is deprecated.
    - The trigger does not have to sit in the declaring body, so a file without one settles nothing. A method, a property or a class can raise from its caller, from the __get() and __call() of PublicPropertyDeprecationTrait and PublicMethodDeprecationTrait where the member was made protected and listed there, or from whatever resolves the class.
    - A class constant and an enum case are where the docblock stands alone: nothing runs when one is read, so no trigger_error can be attached to it anywhere. Such a deprecation raises nothing — no deprecation log entry, nothing for a test suite running with failOnDeprecation — and the call site turns into a fatal error in the major that removes it. What finds it is the extension scanner, through the ClassConstantMatcher entry the deprecating patch owes it, rather than anything at runtime.
    - What a branch deprecated is recorded in typo3/sysext/core/Documentation/Changelog/<version>/Deprecation-<issue>-<Title>.rst and in the matchers below typo3/sysext/install/Configuration/ExtensionScanner/Php/. Take the migration path from there instead of assuming a replacement.
    - An entry's Impact section is prose and can promise a deprecation warning the code does not raise. What is raised is a property of the declaration, so read that for the severity rather than the entry.
    - A deprecated API keeps working until the next major release, so an existing call site is not automatically a defect. New code uses the replacement the changelog names.
    - Authoring a deprecation and finding out what a version deprecated are two directions through the same files. From the reading side: the changelog directory and the extension scanner matchers ship with the core and install packages of any installation, the Extension Scanner in the Install Tool runs the matchers over an extension, and `typo3 upgrade:list` and `typo3 upgrade:run` are the console side of the migrations.
    - @internal on a class or on a member says it is not public API, and both are read: a public class can carry an internal method. It is an input to whether a removal is breaking and never the answer on its own.
    - What settles it is whether anything outside the core calls it. The core has removed @internal members both as a breaking change and as an Important note, and what separates the two is the call sites rather than the marker: a Breaking entry names them in its Affected installations section, and the extension scanner matchers are where they are looked for. Writing that section is the test of whether there is one. Where there is none, the removal is an ordinary [TASK] with no marker, no changelog entry and no matcher.
    - An absent annotation is not a statement that something is public API. Read the changelog for the subsystem and the extension scanner matchers before concluding either way.

    ## Changing a Public Method Signature
    Hints:
    - A public or protected method on a class that is not final is an override point: something outside the package may already override it, and PHP compares the two declarations when that subclass is loaded.
    - Adding a parameter to such a method is a signature change, an optional one included. The subclass that declares the old signature fatals as it is autoloaded — "Declaration of Sub::start() must be compatible with Base::start()" — and nothing in the class being edited names it.
    - Nothing in a core checkout reports it. No core class has to override the method, so the unit, functional, coding-guidelines and static-analysis runs are all green on the change.
    - The core files such a change as breaking on the possibility of an override rather than on a demonstrated one. Its changelog entries name the affected installations as the extensions extending the method, and one that calls that set very unlikely files it as breaking anyway.
    - The exception is a member the core has taken out of its public API with @internal, which takes an Important entry instead of a Breaking one. An entry is still owed; only its type changes, and that is what lets such a change reach a release line.
    - So the target branch is decided here rather than at commit-message time. A maintained release line carries no breaking change, no deprecation and no feature, which leaves Important the only one of the four it takes — a fix owed to one cannot carry the signature change at all.
    - Add rather than widen where the change has to reach a release line: a method of its own, or the state handed over on something the callee already receives — the core puts the calling ContentObjectRenderer on the request as the currentContentObject attribute instead of into a signature. Declaring the class or the method final first is no cheaper, because that is itself a breaking change with an entry of its own.
    - Which entries decide each of those, and what the extension scanner can and cannot find, is typo3_rule_lookup(query "breaking change").
    - A change that moves no member at all is settled the other way round, on what it renders: typo3_hint_lookup with the id breaking-without-a-moved-member.

    ## Events and Extension Points
    Hints:
    - A listener is registered with the #[AsEventListener] attribute from TYPO3\CMS\Core\Attribute, on the class or on a single method. Its arguments are identifier, event, method, before and after; the attribute is repeatable, so one class can listen to several events. Autoconfiguration picks it up — do not add an event.listener tag to Services.yaml, no core listener is registered that way. [TYPO3 v13 and newer]
    - Event classes live in Classes/Event/ of the extension that dispatches them, are final, and are readonly where the payload is immutable. A listener that may change the outcome gets setters on the event instead of a return value.
    - Keep event payloads minimal and stable, and prefer a new event over a hook: a hook is only the right answer where the subsystem still has hook-based extension points.
    - The surviving hooks are a subsystem fact, not a second extension-point registry. Ask the subsystem hint with the intent — for example prefilling a form field — so it can name both the remaining hook and the narrower event; the form-framework hint records EXT:form's two remaining SC_OPTIONS calls.
    - A PSR-14 event is public API. A new one needs a changelog entry, careful naming, and regression coverage.

    Rules that apply to this task:

    A section carries the range it holds for where it has one. What is bound elsewhere: call typo3_hint_lookup with targetVersion for a convention, and typo3_test_run_guide with targetVersion for a runTests.sh command.

    ## Deprecations
    Source: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages) — matches 100% of the query terms

    - Deprecations must not use `[!!!]`.
    - Deprecations may only use `[TASK]` or `[FEATURE]`.
    - Deprecations must be documented with a changelog RST file.
    - Deprecations need migration guidance and may need extension scanner
      considerations.
    - All of the above is the authoring side. Reading it — what a given version
      deprecated, and what that means for code that uses it — works the other way
      round: the changelog files below `Documentation/Changelog/` of the core
      package and the matchers below the install package's
      `Configuration/ExtensionScanner/Php/` are what an installation is checked
      against, by the Extension Scanner in the Install Tool. Both directories ship
      with a Composer installation.

    ## Breaking Changes
    Source: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages) — matches 100% of the query terms

    - Breaking changes must use `[!!!]` before the keyword.
    - Breaking changes must be documented with a changelog RST file.
    - Breaking changes should usually target `main`.
    - A removed or narrowed PHP API gets an extension scanner matcher entry in the
      same patch, below `typo3/sysext/install/Configuration/ExtensionScanner/Php/`.
      How the removed member is written where it is used decides the file:
      - `MethodCallMatcher.php` — an instance method.
      - `MethodCallStaticMatcher.php` — a static method.
      - `PropertyPublicMatcher.php` — a removed public property.
      - `PropertyProtectedMatcher.php` — a public property that became protected.
      - `ClassNameMatcher.php` — a whole class or interface.
    - Visibility routes a property and never a method. The method matchers are a
      weak match on the method name where it is used, and they do not resolve the
      class, so they cannot see one. A method that is protected, or that has become
      protected, is entered where a public one is.
      `RendererRegistry->getRendererInstances` went from public to protected in
      `Breaking-110277`, and it stands in `MethodCallMatcher.php`. The list above
      has no row for a protected method because none is needed, and that absence
      says nothing about whether an entry is owed.
    - An entry is keyed by the fully qualified name with `->` or `::` and carries
      `restFiles`, naming the changelog file that removed it. The method matchers
      add `numberOfMandatoryArguments` and `maximumNumberOfArguments`. A member
      deprecated before it was removed lists both changelog files.
    - Every Breaking and Deprecation entry carries exactly one of `NotScanned`,
      `PartiallyScanned` and `FullyScanned` in its `.. index::` line, and that tag
      is the claim those entries have to back: `FullyScanned` says every item the
      changelog entry names can be found. The scanner reads PHP, so what an entry
      changes in TypoScript, TCA, YAML or JavaScript is what leaves it partially
      scanned.
    - `./Build/Scripts/runTests.sh -s checkExtensionScannerRst` checks that the
      changelog files the matchers name exist, and nothing checks the other
      direction. A missing entry surfaces when somebody audits the matcher files
      against the changelog.

    Each excerpt above is one section of a longer document, and each page below carries the `##` headings that are not above. Where the task is the whole procedure rather than the fact you searched for, read the page — typo3_rule_lookup with documentId, which needs no resource list:
    - core/contribution/commit-messages — TYPO3 Core Commit Message Rules: 11 of its 13 headings are not above — Who Reads It, Summary Line, Work in Progress, Body, The Longest Line The Hook Accepts, Relationships, Release Targets, The Trailers A Core Commit Carries, What The Commit Hook Writes, Changed Signatures, The Changelog Entry a Message Announces.

    Relevant TYPO3 core checks — the list to run, whatever this task turns out to be:
    - `CI=true ./Build/Scripts/runTests.sh -s unit`
    - `CI=true ./Build/Scripts/runTests.sh -s functional`
    - `CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp`
    - `CI=true ./Build/Scripts/runTests.sh -s checkRst`
    - `CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst`

    Suites that match this task, strongest first. Each is one to decide about rather than one the list above left out, and typo3_test_run_guide holds the rest for these paths.
    ## checkIntegrityPhp
    `CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp`
    Run it on any patch that writes PHP, test fixtures included — the core's own pre-merge pipeline runs it, so what it reports fails review before a person reads the patch. The one it reports most is the exception code: every throw needs a unique ten-digit integer, and undefined, duplicate and malformed ones each come back with the file and the line.

    Suggested checklist:
    - Content changes, so what is delivered has to be the version that is current after the change — that is what the editor and the visitor are owed. A defect is judged by that outcome: the old version still being served is the defect, and the error it eventually throws is the symptom.
    - Confirm the target TYPO3 core branch and issue context.
    - Inspect nearby code, tests, and established subsystem conventions.
    - Sweep the deprecations before writing: typo3_changelog_lookup with type "deprecation" and the query omitted, at TYPO3 v14. Only a change touching no TYPO3 API skips it, a CI file being the shape of one, and how small the diff is decides nothing. One call per tag: the ext: tag of each system extension this package calls into, and TCA, Fluid, Backend or Frontend for the kinds of file it ships. Every call also returns the tags that major carries, so the second onwards is read off the first.
    - Keep the patch focused on the stated task.
    - Add or update the narrowest useful test coverage.
    - Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.
    - Keep the cleanup mechanical; avoid mixing behavioural changes into the same patch.
    - Annotate the member @deprecated since TYPO3 v<the version this lands in>, will be removed in TYPO3 v<the next major>. and close that line with the migration sentence. Read both versions off the branch you are on rather than off an example.
    - Keep the member working and open its body with trigger_error('<Class>-><member>() is deprecated since TYPO3 v<the version this lands in> and will be removed in TYPO3 v<the next major>. <migration>', E_USER_DEPRECATED). The docblock and the message say the same three things, and the removal is a patch of its own in that major.
    - Migrate every caller of the deprecated API in the same patch, and confirm none is left behind.
    - Add a changelog file below typo3/sysext/core/Documentation/Changelog/<the minor this is released in>/ named Deprecation-<issue>-<UpperCamelCaseDescription>.rst. Nothing has to include it: that directory's Index.rst pulls Deprecation-* in by glob, so the filename is the inclusion. Which directory a backport's file goes into is settled by typo3_rule_lookup with documentId=core/contribution/changelog rather than by this item.
    - Open the changelog file with .. include:: /Includes.rst.txt and a unique .. _deprecation-<issue>-<unix timestamp>: anchor directly above the 'Deprecation: #<issue> - <title>' headline, with See :issue:`<issue>` under it. Then Description, Impact, Affected installations and Migration — the last two are what this type owes over a feature entry.
    - End the changelog file with .. index:: carrying at least one subject tag and exactly one of FullyScanned, PartiallyScanned or NotScanned. Build/Scripts/validateRstFiles.php rejects a Deprecation file without the scanner tag, so it is owed rather than considered.
    - Back a FullyScanned or PartiallyScanned tag with an extension scanner matcher: an entry below typo3/sysext/install/Configuration/ExtensionScanner/Php/, keyed by the deprecated symbol and naming the changelog file in its restFiles. NotScanned is for what no matcher can find, not for what nobody wrote.
    - Use [TASK] or [FEATURE] as the commit keyword. A deprecation must never use the [!!!] breaking prefix.
    - Run the audit before writing the list, and let it own the findings: what a surface is, what evidence a finding rests on, what it is worth and who fixes it are the audit's answers. A list built from a reading of the checkout instead is an impression, and the items in it are not the ones the report would have given.
    - Show the list whole and let the maintainer cut items, reorder them or stop, before a single file is changed. That agreement is the one step nothing downstream recovers, and a list arriving with the changes it produced is one nobody had the chance to disagree with.
    - Keep the list in the reply rather than committing it into the repository. A worklist committed into somebody's history is a file nobody asked for that has to be taken out again; what the history keeps is the commits the items produced, each saying which item it closed.
    - Work an item in the workflow that owns it, and stop before editing files another owner has. An item no workflow owns is worked here only where the project's own suite, linter or static analysis proves the change — anything else goes back unassigned, because a finding nobody owns and no check covers is a hole in the workflow map and quietly filling it hides the hole.
    - Re-run the audit on the worked list rather than grading it off the diff. Work that declares its own findings gone has no evidence for it, and a file that reads correctly can still be rewritten by the environment that owns it.
    - Report the items still open, the ones dropped with what dropped them, and the ones sent back unassigned. A finished list and an abandoned one read alike in a summary.
    - Write the commit message with typo3_commit_message_guide and workflow="core": summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping.

    Establish in your checkout — this server cannot see it:
    - Which files the task actually touches
      git status --short and git diff --name-only in the core checkout, then call typo3_hint_lookup with those paths for the conventions that apply to them.
    - Which tests already cover them
      Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation.
    - The branch you are on and the branches the change is meant for
      git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main.
    - Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch
      Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_snapshot_scope names the fallback revision.
    - Whether an icon identifier is registered, and which one spells the shape you want
      Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family.
    - Whether a label for this wording already exists
      Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask.

    Next lookups for this task:
    - typo3_commit_message_guide — with workflow="core" and isDeprecation=true, for the keyword and prefix rules a message pushed to Gerrit is held to
    - typo3_project_describe — for what the repository is before anything in it is changed
    - typo3_extension_describe — for what each extension in scope registers
    - typo3_changelog_lookup — for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact
    - typo3_hint_lookup — with the concrete file paths, once they are known
    - typo3_test_run_guide — for the targeted runTests.sh invocation — it lists every suite these domains hold, of which the testSuites above are the strongest few
    - typo3_task_guide — again, where the work enters a subject this task did not name — the first file under a test directory, the first run of a check the repository declares, the first branch or commit, the first edit to code or documentation the package ships
    - typo3_feedback_record — when one of these answers was wrong or incomplete

Data:

.. code-block:: json

    {
        "task": "Deprecate a public method",
        "paths": [
            "typo3/sysext/core/Classes/Utility/GeneralUtility.php"
        ],
        "scopes": [
            {
                "path": "typo3/sysext/core/Classes/Utility/GeneralUtility.php",
                "scope": "core"
            }
        ],
        "changeType": "cleanup",
        "targetVersion": 14,
        "targetVersions": [
            14
        ],
        "domains": [
            "php"
        ],
        "scope": "core",
        "intents": [
            {
                "id": "deprecation",
                "title": "Deprecation",
                "confidence": "strong",
                "condition": ""
            },
            {
                "id": "cleanup",
                "title": "Putting a repository right",
                "confidence": "strong",
                "condition": "only if the task asks for the repository as a whole to be changed rather than reviewed, or for the findings of a review to be worked off"
            }
        ],
        "skills": [
            "typo3-core-patch-development"
        ],
        "staleSkills": [],
        "guides": [],
        "hints": [
            {
                "id": "system-extension-boundaries",
                "title": "System Extension Boundaries",
                "category": "PHP",
                "scope": null,
                "hints": [
                    {
                        "text": "Keep changes inside the owning system extension unless a cross-extension contract really changes.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "Reuse public APIs from other system extensions instead of depending on internal implementation details.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "Check nearby extension-local tests before adding shared behavior.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    }
                ]
            },
            {
                "id": "deprecated-apis",
                "title": "Deprecated APIs",
                "category": "PHP",
                "scope": null,
                "hints": [
                    {
                        "text": "Whether an API is deprecated is a property of the branch you work on, not of TYPO3 as a whole. Where you work in an installation the server reads that branch: typo3_project_describe names the TYPO3 version installed there, and typo3_changelog_lookup answers from the changelog its core package ships.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "Read the declaration itself: an @deprecated annotation together with a trigger_error(..., E_USER_DEPRECATED) call is what marks one. The core marks nothing with PHP's #[\\Deprecated] attribute, so finding none says nothing about what is deprecated.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "The trigger does not have to sit in the declaring body, so a file without one settles nothing. A method, a property or a class can raise from its caller, from the __get() and __call() of PublicPropertyDeprecationTrait and PublicMethodDeprecationTrait where the member was made protected and listed there, or from whatever resolves the class.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "A class constant and an enum case are where the docblock stands alone: nothing runs when one is read, so no trigger_error can be attached to it anywhere. Such a deprecation raises nothing — no deprecation log entry, nothing for a test suite running with failOnDeprecation — and the call site turns into a fatal error in the major that removes it. What finds it is the extension scanner, through the ClassConstantMatcher entry the deprecating patch owes it, rather than anything at runtime.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "What a branch deprecated is recorded in typo3/sysext/core/Documentation/Changelog/<version>/Deprecation-<issue>-<Title>.rst and in the matchers below typo3/sysext/install/Configuration/ExtensionScanner/Php/. Take the migration path from there instead of assuming a replacement.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "An entry's Impact section is prose and can promise a deprecation warning the code does not raise. What is raised is a property of the declaration, so read that for the severity rather than the entry.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "A deprecated API keeps working until the next major release, so an existing call site is not automatically a defect. New code uses the replacement the changelog names.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "Authoring a deprecation and finding out what a version deprecated are two directions through the same files. From the reading side: the changelog directory and the extension scanner matchers ship with the core and install packages of any installation, the Extension Scanner in the Install Tool runs the matchers over an extension, and `typo3 upgrade:list` and `typo3 upgrade:run` are the console side of the migrations.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "@internal on a class or on a member says it is not public API, and both are read: a public class can carry an internal method. It is an input to whether a removal is breaking and never the answer on its own.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "What settles it is whether anything outside the core calls it. The core has removed @internal members both as a breaking change and as an Important note, and what separates the two is the call sites rather than the marker: a Breaking entry names them in its Affected installations section, and the extension scanner matchers are where they are looked for. Writing that section is the test of whether there is one. Where there is none, the removal is an ordinary [TASK] with no marker, no changelog entry and no matcher.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "An absent annotation is not a statement that something is public API. Read the changelog for the subsystem and the extension scanner matchers before concluding either way.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    }
                ]
            },
            {
                "id": "public-api-surface",
                "title": "Changing a Public Method Signature",
                "category": "PHP",
                "scope": null,
                "hints": [
                    {
                        "text": "A public or protected method on a class that is not final is an override point: something outside the package may already override it, and PHP compares the two declarations when that subclass is loaded.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "Adding a parameter to such a method is a signature change, an optional one included. The subclass that declares the old signature fatals as it is autoloaded — \"Declaration of Sub::start() must be compatible with Base::start()\" — and nothing in the class being edited names it.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "Nothing in a core checkout reports it. No core class has to override the method, so the unit, functional, coding-guidelines and static-analysis runs are all green on the change.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": "core"
                    },
                    {
                        "text": "The core files such a change as breaking on the possibility of an override rather than on a demonstrated one. Its changelog entries name the affected installations as the extensions extending the method, and one that calls that set very unlikely files it as breaking anyway.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": "core"
                    },
                    {
                        "text": "The exception is a member the core has taken out of its public API with @internal, which takes an Important entry instead of a Breaking one. An entry is still owed; only its type changes, and that is what lets such a change reach a release line.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": "core"
                    },
                    {
                        "text": "So the target branch is decided here rather than at commit-message time. A maintained release line carries no breaking change, no deprecation and no feature, which leaves Important the only one of the four it takes — a fix owed to one cannot carry the signature change at all.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": "core"
                    },
                    {
                        "text": "Add rather than widen where the change has to reach a release line: a method of its own, or the state handed over on something the callee already receives — the core puts the calling ContentObjectRenderer on the request as the currentContentObject attribute instead of into a signature. Declaring the class or the method final first is no cheaper, because that is itself a breaking change with an entry of its own.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "Which entries decide each of those, and what the extension scanner can and cannot find, is typo3_rule_lookup(query \"breaking change\").",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": "core"
                    },
                    {
                        "text": "A change that moves no member at all is settled the other way round, on what it renders: typo3_hint_lookup with the id breaking-without-a-moved-member.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": "core"
                    }
                ]
            },
            {
                "id": "events-extension-points",
                "title": "Events and Extension Points",
                "category": "PHP",
                "scope": null,
                "hints": [
                    {
                        "text": "A listener is registered with the #[AsEventListener] attribute from TYPO3\\CMS\\Core\\Attribute, on the class or on a single method. Its arguments are identifier, event, method, before and after; the attribute is repeatable, so one class can listen to several events. Autoconfiguration picks it up — do not add an event.listener tag to Services.yaml, no core listener is registered that way.",
                        "since": 13,
                        "until": null,
                        "versions": "TYPO3 v13 and newer",
                        "scope": null
                    },
                    {
                        "text": "Event classes live in Classes/Event/ of the extension that dispatches them, are final, and are readonly where the payload is immutable. A listener that may change the outcome gets setters on the event instead of a return value.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "Keep event payloads minimal and stable, and prefer a new event over a hook: a hook is only the right answer where the subsystem still has hook-based extension points.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "The surviving hooks are a subsystem fact, not a second extension-point registry. Ask the subsystem hint with the intent — for example prefilling a form field — so it can name both the remaining hook and the narrower event; the form-framework hint records EXT:form's two remaining SC_OPTIONS calls.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "A PSR-14 event is public API. A new one needs a changelog entry, careful naming, and regression coverage.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    }
                ]
            }
        ],
        "omittedHints": [],
        "rules": [
            {
                "documentId": "core/contribution/commit-messages",
                "title": "TYPO3 Core Commit Message Rules",
                "uri": "typo3://guides/core/contribution/commit-messages",
                "heading": "Deprecations",
                "body": "- Deprecations must not use `[!!!]`.\n- Deprecations may only use `[TASK]` or `[FEATURE]`.\n- Deprecations must be documented with a changelog RST file.\n- Deprecations need migration guidance and may need extension scanner\n  considerations.\n- All of the above is the authoring side. Reading it — what a given version\n  deprecated, and what that means for code that uses it — works the other way\n  round: the changelog files below `Documentation/Changelog/` of the core\n  package and the matchers below the install package's\n  `Configuration/ExtensionScanner/Php/` are what an installation is checked\n  against, by the Extension Scanner in the Install Tool. Both directories ship\n  with a Composer installation.",
                "versions": "",
                "coverage": 1,
                "score": 68,
                "truncated": false
            },
            {
                "documentId": "core/contribution/commit-messages",
                "title": "TYPO3 Core Commit Message Rules",
                "uri": "typo3://guides/core/contribution/commit-messages",
                "heading": "Breaking Changes",
                "body": "- Breaking changes must use `[!!!]` before the keyword.\n- Breaking changes must be documented with a changelog RST file.\n- Breaking changes should usually target `main`.\n- A removed or narrowed PHP API gets an extension scanner matcher entry in the\n  same patch, below `typo3/sysext/install/Configuration/ExtensionScanner/Php/`.\n  How the removed member is written where it is used decides the file:\n  - `MethodCallMatcher.php` — an instance method.\n  - `MethodCallStaticMatcher.php` — a static method.\n  - `PropertyPublicMatcher.php` — a removed public property.\n  - `PropertyProtectedMatcher.php` — a public property that became protected.\n  - `ClassNameMatcher.php` — a whole class or interface.\n- Visibility routes a property and never a method. The method matchers are a\n  weak match on the method name where it is used, and they do not resolve the\n  class, so they cannot see one. A method that is protected, or that has become\n  protected, is entered where a public one is.\n  `RendererRegistry->getRendererInstances` went from public to protected in\n  `Breaking-110277`, and it stands in `MethodCallMatcher.php`. The list above\n  has no row for a protected method because none is needed, and that absence\n  says nothing about whether an entry is owed.\n- An entry is keyed by the fully qualified name with `->` or `::` and carries\n  `restFiles`, naming the changelog file that removed it. The method matchers\n  add `numberOfMandatoryArguments` and `maximumNumberOfArguments`. A member\n  deprecated before it was removed lists both changelog files.\n- Every Breaking and Deprecation entry carries exactly one of `NotScanned`,\n  `PartiallyScanned` and `FullyScanned` in its `.. index::` line, and that tag\n  is the claim those entries have to back: `FullyScanned` says every item the\n  changelog entry names can be found. The scanner reads PHP, so what an entry\n  changes in TypoScript, TCA, YAML or JavaScript is what leaves it partially\n  scanned.\n- `./Build/Scripts/runTests.sh -s checkExtensionScannerRst` checks that the\n  changelog files the matchers name exist, and nothing checks the other\n  direction. A missing entry surfaces when somebody audits the matcher files\n  against the changelog.",
                "versions": "",
                "coverage": 1,
                "score": 21,
                "truncated": false
            }
        ],
        "checks": [
            "CI=true ./Build/Scripts/runTests.sh -s unit",
            "CI=true ./Build/Scripts/runTests.sh -s functional",
            "CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp",
            "CI=true ./Build/Scripts/runTests.sh -s checkRst",
            "CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst"
        ],
        "conditionalChecks": [],
        "testSuites": [
            {
                "suite": "checkIntegrityPhp",
                "command": "CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp",
                "runs": "check",
                "targeted": null,
                "description": "Reads typo3/sysext/*/Classes, Tests/Unit and Tests/Functional for the conventions neither lintPhp nor cgl covers: the exception code on every throw, the annotations, the namespace against the path, the test method prefix and the final test class.",
                "whenToUse": "Run it on any patch that writes PHP, test fixtures included — the core's own pre-merge pipeline runs it, so what it reports fails review before a person reads the patch. The one it reports most is the exception code: every throw needs a unique ten-digit integer, and undefined, duplicate and malformed ones each come back with the file and the line.",
                "domains": [
                    "php"
                ],
                "versions": "TYPO3 v13 and newer"
            }
        ],
        "checklist": [
            "Content changes, so what is delivered has to be the version that is current after the change — that is what the editor and the visitor are owed. A defect is judged by that outcome: the old version still being served is the defect, and the error it eventually throws is the symptom.",
            "Confirm the target TYPO3 core branch and issue context.",
            "Inspect nearby code, tests, and established subsystem conventions.",
            "Sweep the deprecations before writing: typo3_changelog_lookup with type \"deprecation\" and the query omitted, at TYPO3 v14. Only a change touching no TYPO3 API skips it, a CI file being the shape of one, and how small the diff is decides nothing. One call per tag: the ext: tag of each system extension this package calls into, and TCA, Fluid, Backend or Frontend for the kinds of file it ships. Every call also returns the tags that major carries, so the second onwards is read off the first.",
            "Keep the patch focused on the stated task.",
            "Add or update the narrowest useful test coverage.",
            "Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.",
            "Keep the cleanup mechanical; avoid mixing behavioural changes into the same patch.",
            "Annotate the member @deprecated since TYPO3 v<the version this lands in>, will be removed in TYPO3 v<the next major>. and close that line with the migration sentence. Read both versions off the branch you are on rather than off an example.",
            "Keep the member working and open its body with trigger_error('<Class>-><member>() is deprecated since TYPO3 v<the version this lands in> and will be removed in TYPO3 v<the next major>. <migration>', E_USER_DEPRECATED). The docblock and the message say the same three things, and the removal is a patch of its own in that major.",
            "Migrate every caller of the deprecated API in the same patch, and confirm none is left behind.",
            "Add a changelog file below typo3/sysext/core/Documentation/Changelog/<the minor this is released in>/ named Deprecation-<issue>-<UpperCamelCaseDescription>.rst. Nothing has to include it: that directory's Index.rst pulls Deprecation-* in by glob, so the filename is the inclusion. Which directory a backport's file goes into is settled by typo3_rule_lookup with documentId=core/contribution/changelog rather than by this item.",
            "Open the changelog file with .. include:: /Includes.rst.txt and a unique .. _deprecation-<issue>-<unix timestamp>: anchor directly above the 'Deprecation: #<issue> - <title>' headline, with See :issue:`<issue>` under it. Then Description, Impact, Affected installations and Migration — the last two are what this type owes over a feature entry.",
            "End the changelog file with .. index:: carrying at least one subject tag and exactly one of FullyScanned, PartiallyScanned or NotScanned. Build/Scripts/validateRstFiles.php rejects a Deprecation file without the scanner tag, so it is owed rather than considered.",
            "Back a FullyScanned or PartiallyScanned tag with an extension scanner matcher: an entry below typo3/sysext/install/Configuration/ExtensionScanner/Php/, keyed by the deprecated symbol and naming the changelog file in its restFiles. NotScanned is for what no matcher can find, not for what nobody wrote.",
            "Use [TASK] or [FEATURE] as the commit keyword. A deprecation must never use the [!!!] breaking prefix.",
            "Run the audit before writing the list, and let it own the findings: what a surface is, what evidence a finding rests on, what it is worth and who fixes it are the audit's answers. A list built from a reading of the checkout instead is an impression, and the items in it are not the ones the report would have given.",
            "Show the list whole and let the maintainer cut items, reorder them or stop, before a single file is changed. That agreement is the one step nothing downstream recovers, and a list arriving with the changes it produced is one nobody had the chance to disagree with.",
            "Keep the list in the reply rather than committing it into the repository. A worklist committed into somebody's history is a file nobody asked for that has to be taken out again; what the history keeps is the commits the items produced, each saying which item it closed.",
            "Work an item in the workflow that owns it, and stop before editing files another owner has. An item no workflow owns is worked here only where the project's own suite, linter or static analysis proves the change — anything else goes back unassigned, because a finding nobody owns and no check covers is a hole in the workflow map and quietly filling it hides the hole.",
            "Re-run the audit on the worked list rather than grading it off the diff. Work that declares its own findings gone has no evidence for it, and a file that reads correctly can still be rewritten by the environment that owns it.",
            "Report the items still open, the ones dropped with what dropped them, and the ones sent back unassigned. A finished list and an abandoned one read alike in a summary.",
            "Write the commit message with typo3_commit_message_guide and workflow=\"core\": summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping."
        ],
        "checkoutDiscovery": [
            {
                "establish": "Which files the task actually touches",
                "how": "git status --short and git diff --name-only in the core checkout, then call typo3_hint_lookup with those paths for the conventions that apply to them."
            },
            {
                "establish": "Which tests already cover them",
                "how": "Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation."
            },
            {
                "establish": "The branch you are on and the branches the change is meant for",
                "how": "git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main."
            },
            {
                "establish": "Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch",
                "how": "Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_snapshot_scope names the fallback revision."
            },
            {
                "establish": "Whether an icon identifier is registered, and which one spells the shape you want",
                "how": "Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family."
            },
            {
                "establish": "Whether a label for this wording already exists",
                "how": "Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask."
            }
        ],
        "nextTools": [
            {
                "tool": "typo3_commit_message_guide",
                "when": "with workflow=\"core\" and isDeprecation=true, for the keyword and prefix rules a message pushed to Gerrit is held to"
            },
            {
                "tool": "typo3_project_describe",
                "when": "for what the repository is before anything in it is changed"
            },
            {
                "tool": "typo3_extension_describe",
                "when": "for what each extension in scope registers"
            },
            {
                "tool": "typo3_changelog_lookup",
                "when": "for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact"
            },
            {
                "tool": "typo3_hint_lookup",
                "when": "with the concrete file paths, once they are known"
            },
            {
                "tool": "typo3_test_run_guide",
                "when": "for the targeted runTests.sh invocation — it lists every suite these domains hold, of which the testSuites above are the strongest few"
            },
            {
                "tool": "typo3_task_guide",
                "when": "again, where the work enters a subject this task did not name — the first file under a test directory, the first run of a check the repository declares, the first branch or commit, the first edit to code or documentation the package ships"
            },
            {
                "tool": "typo3_feedback_record",
                "when": "when one of these answers was wrong or incomplete"
            }
        ]
    }

brief: task only
~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "task": "Add a badge to the list module"
    }

Text:

.. code-block:: text

    Task: Add a badge to the list module
    Change type: unknown
    Domains: php
    Recognized as: Backend UI markup

    Hints:
    - No hint matched this task text. That means no convention was recognized, not that none applies: call typo3_hint_lookup again with the concrete file paths once they are known.

    Rules that apply to this task:

    A section carries the range it holds for where it has one. What is bound elsewhere: call typo3_hint_lookup with targetVersion for a convention, and typo3_test_run_guide with targetVersion for a runTests.sh command.

    ## Testing
    Source: TYPO3 Core Contribution Rules (typo3://guides/core/contribution/rules) — matches 50% of the query terms

    - Unit tests are expected for isolated behavior.
    - Functional tests are expected for persistence, configuration, routing, backend
      behavior, or integration with TYPO3 services.
    - End-to-end tests, the `e2e` suite, are useful when the change affects editor
      or administrator workflows and only breaks in the assembled backend. They
      replaced the former acceptance suites.
    - Document tests that could not be executed and why.

    Each excerpt above is one section of a longer document, and each page below carries the `##` headings that are not above. Where the task is the whole procedure rather than the fact you searched for, read the page — typo3_rule_lookup with documentId, which needs no resource list:
    - core/contribution/rules — TYPO3 Core Contribution Rules: 4 of its 5 headings are not above — Contribution Flow, Code Style, Documentation, Review Readiness.

    Relevant TYPO3 core checks — the list to run, whatever this task turns out to be:
    - `CI=true ./Build/Scripts/runTests.sh -s unit`
    - `CI=true ./Build/Scripts/runTests.sh -s functional`
    - `CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp`
    - `CI=true ./Build/Scripts/runTests.sh -s lintScss`
    - `CI=true ./Build/Scripts/runTests.sh -s build`

    Suites that match this task, strongest first. Each is one to decide about rather than one the list above left out, and typo3_test_run_guide holds the rest for these paths.
    ## listExceptionCodes
    `CI=true ./Build/Scripts/runTests.sh -s listExceptionCodes`
    Use it to see which codes are taken. It confirms nothing: a missing or duplicated code leaves this suite green, and checkIntegrityPhp is what reports one.
    ## cglGit
    `CI=true ./Build/Scripts/runTests.sh -s cglGit`
    Targeted: `CI=true ./Build/Scripts/runTests.sh -s cgl -n`
    Use for a focused pre-review check after creating a commit, from a normal checkout only. It is `Build/Scripts/cglFixMyCommit.sh` in the container, so running that script directly buys nothing and puts it on the host's PHP rather than on the one the branch pins. Its file list comes from git inside the container, and a git worktree keeps its gitdir outside the mounted directory: git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cgl -n` where the checkout may be a worktree — it asks git nothing.
    ## cglHeaderGit
    `CI=true ./Build/Scripts/runTests.sh -s cglHeaderGit`
    Targeted: `CI=true ./Build/Scripts/runTests.sh -s cglHeader -n`
    Use for a focused header check after creating a commit, from a normal checkout only. It is `Build/Scripts/cglFixMyCommitFileHeader.sh` in the container, and its file list comes from git inside the container: a git worktree keeps its gitdir outside the mounted directory, git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cglHeader -n` where the checkout may be a worktree — it asks git nothing.

    Suggested checklist:
    - Content changes, so what is delivered has to be the version that is current after the change — that is what the editor and the visitor are owed. A defect is judged by that outcome: the old version still being served is the defect, and the error it eventually throws is the symptom.
    - Confirm the target TYPO3 core branch and issue context.
    - Inspect nearby code, tests, and established subsystem conventions.
    - Sweep the deprecations before writing: typo3_changelog_lookup with type "deprecation" and the query omitted, at TYPO3 v14. Only a change touching no TYPO3 API skips it, a CI file being the shape of one, and how small the diff is decides nothing. One call per tag: the ext: tag of each system extension this package calls into, and TCA, Fluid, Backend or Frontend for the kinds of file it ships. Every call also returns the tags that major carries, so the second onwards is read off the first.
    - Keep the patch focused on the stated task.
    - Add or update the narrowest useful test coverage.
    - Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.
    - Use the existing backend component classes and their documented markup instead of new ad-hoc classes.
    - Check the styleguide demo of the component for the canonical structure.
    - Write the commit message with typo3_commit_message_guide and workflow="core": summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping.

    Establish in your checkout — this server cannot see it:
    - Which files the task actually touches
      git status --short and git diff --name-only in the core checkout, then call typo3_hint_lookup with those paths for the conventions that apply to them.
    - Which tests already cover them
      Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation.
    - The branch you are on and the branches the change is meant for
      git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main.
    - Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch
      Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_snapshot_scope names the fallback revision.
    - Whether an icon identifier is registered, and which one spells the shape you want
      Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family.
    - Whether a label for this wording already exists
      Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask.

    Next lookups for this task:
    - typo3_component_lookup — before writing backend markup or CSS classes
    - typo3_changelog_lookup — for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact
    - typo3_hint_lookup — with the concrete file paths, once they are known
    - typo3_test_run_guide — for the targeted runTests.sh invocation — it lists every suite these domains hold, of which the testSuites above are the strongest few
    - typo3_commit_message_guide — with workflow="core", before committing — the default is a repository of your own and demands no Forge issue or release trailer
    - typo3_task_guide — again, where the work enters a subject this task did not name — the first file under a test directory, the first run of a check the repository declares, the first branch or commit, the first edit to code or documentation the package ships
    - typo3_feedback_record — when one of these answers was wrong or incomplete

Data:

.. code-block:: json

    {
        "task": "Add a badge to the list module",
        "paths": [],
        "scopes": [],
        "changeType": "unknown",
        "targetVersion": 14,
        "targetVersions": [
            14
        ],
        "domains": [
            "php"
        ],
        "scope": "core",
        "intents": [
            {
                "id": "backend-ui",
                "title": "Backend UI markup",
                "confidence": "strong",
                "condition": "only if the change adds or alters backend component markup or CSS classes"
            }
        ],
        "skills": [],
        "staleSkills": [],
        "guides": [],
        "hints": [],
        "omittedHints": [],
        "rules": [
            {
                "documentId": "core/contribution/rules",
                "title": "TYPO3 Core Contribution Rules",
                "uri": "typo3://guides/core/contribution/rules",
                "heading": "Testing",
                "body": "- Unit tests are expected for isolated behavior.\n- Functional tests are expected for persistence, configuration, routing, backend\n  behavior, or integration with TYPO3 services.\n- End-to-end tests, the `e2e` suite, are useful when the change affects editor\n  or administrator workflows and only breaks in the assembled backend. They\n  replaced the former acceptance suites.\n- Document tests that could not be executed and why.",
                "versions": "",
                "coverage": 0.5,
                "score": 35,
                "truncated": false
            }
        ],
        "checks": [
            "CI=true ./Build/Scripts/runTests.sh -s unit",
            "CI=true ./Build/Scripts/runTests.sh -s functional",
            "CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp",
            "CI=true ./Build/Scripts/runTests.sh -s lintScss",
            "CI=true ./Build/Scripts/runTests.sh -s build"
        ],
        "conditionalChecks": [],
        "testSuites": [
            {
                "suite": "listExceptionCodes",
                "command": "CI=true ./Build/Scripts/runTests.sh -s listExceptionCodes",
                "runs": "check",
                "targeted": null,
                "description": "Prints every exception code found below typo3/ as JSON. It runs the duplicate check with the flag that only prints, which skips both failure paths, so it exits successfully whatever it finds.",
                "whenToUse": "Use it to see which codes are taken. It confirms nothing: a missing or duplicated code leaves this suite green, and checkIntegrityPhp is what reports one.",
                "domains": [
                    "php"
                ],
                "versions": ""
            },
            {
                "suite": "cglGit",
                "command": "CI=true ./Build/Scripts/runTests.sh -s cglGit",
                "runs": "change",
                "targeted": "CI=true ./Build/Scripts/runTests.sh -s cgl -n",
                "description": "Checks and fixes coding guideline issues in the latest committed patch.",
                "whenToUse": "Use for a focused pre-review check after creating a commit, from a normal checkout only. It is `Build/Scripts/cglFixMyCommit.sh` in the container, so running that script directly buys nothing and puts it on the host's PHP rather than on the one the branch pins. Its file list comes from git inside the container, and a git worktree keeps its gitdir outside the mounted directory: git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cgl -n` where the checkout may be a worktree — it asks git nothing.",
                "domains": [
                    "php"
                ],
                "versions": ""
            },
            {
                "suite": "cglHeaderGit",
                "command": "CI=true ./Build/Scripts/runTests.sh -s cglHeaderGit",
                "runs": "change",
                "targeted": "CI=true ./Build/Scripts/runTests.sh -s cglHeader -n",
                "description": "Checks and fixes the licence header in the latest committed patch.",
                "whenToUse": "Use for a focused header check after creating a commit, from a normal checkout only. It is `Build/Scripts/cglFixMyCommitFileHeader.sh` in the container, and its file list comes from git inside the container: a git worktree keeps its gitdir outside the mounted directory, git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cglHeader -n` where the checkout may be a worktree — it asks git nothing.",
                "domains": [
                    "php"
                ],
                "versions": ""
            }
        ],
        "checklist": [
            "Content changes, so what is delivered has to be the version that is current after the change — that is what the editor and the visitor are owed. A defect is judged by that outcome: the old version still being served is the defect, and the error it eventually throws is the symptom.",
            "Confirm the target TYPO3 core branch and issue context.",
            "Inspect nearby code, tests, and established subsystem conventions.",
            "Sweep the deprecations before writing: typo3_changelog_lookup with type \"deprecation\" and the query omitted, at TYPO3 v14. Only a change touching no TYPO3 API skips it, a CI file being the shape of one, and how small the diff is decides nothing. One call per tag: the ext: tag of each system extension this package calls into, and TCA, Fluid, Backend or Frontend for the kinds of file it ships. Every call also returns the tags that major carries, so the second onwards is read off the first.",
            "Keep the patch focused on the stated task.",
            "Add or update the narrowest useful test coverage.",
            "Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.",
            "Use the existing backend component classes and their documented markup instead of new ad-hoc classes.",
            "Check the styleguide demo of the component for the canonical structure.",
            "Write the commit message with typo3_commit_message_guide and workflow=\"core\": summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping."
        ],
        "checkoutDiscovery": [
            {
                "establish": "Which files the task actually touches",
                "how": "git status --short and git diff --name-only in the core checkout, then call typo3_hint_lookup with those paths for the conventions that apply to them."
            },
            {
                "establish": "Which tests already cover them",
                "how": "Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation."
            },
            {
                "establish": "The branch you are on and the branches the change is meant for",
                "how": "git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main."
            },
            {
                "establish": "Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch",
                "how": "Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_snapshot_scope names the fallback revision."
            },
            {
                "establish": "Whether an icon identifier is registered, and which one spells the shape you want",
                "how": "Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family."
            },
            {
                "establish": "Whether a label for this wording already exists",
                "how": "Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask."
            }
        ],
        "nextTools": [
            {
                "tool": "typo3_component_lookup",
                "when": "before writing backend markup or CSS classes"
            },
            {
                "tool": "typo3_changelog_lookup",
                "when": "for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact"
            },
            {
                "tool": "typo3_hint_lookup",
                "when": "with the concrete file paths, once they are known"
            },
            {
                "tool": "typo3_test_run_guide",
                "when": "for the targeted runTests.sh invocation — it lists every suite these domains hold, of which the testSuites above are the strongest few"
            },
            {
                "tool": "typo3_commit_message_guide",
                "when": "with workflow=\"core\", before committing — the default is a repository of your own and demands no Forge issue or release trailer"
            },
            {
                "tool": "typo3_task_guide",
                "when": "again, where the work enters a subject this task did not name — the first file under a test directory, the first run of a check the repository declares, the first branch or commit, the first edit to code or documentation the package ships"
            },
            {
                "tool": "typo3_feedback_record",
                "when": "when one of these answers was wrong or incomplete"
            }
        ]
    }

brief: paths of two kinds
~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "task": "Fix the query that reads the events",
        "paths": [
            "packages/acme_events/Classes/Domain/Repository/EventRepository.php",
            "typo3/sysext/core/Classes/Database/Query/QueryBuilder.php"
        ],
        "changeType": "bugfix"
    }

Text:

.. code-block:: text

    Of the paths given, packages/acme_events/Classes/Domain/Repository/EventRepository.php is outside the TYPO3 core — a project or third-party extension. What follows is split accordingly: what only the core repository has is left out of the half that is about it. The checks below, the changelog and the submission route belong to the core repository, so they are steps for the paths that are in it and for none of the others.

    Task: Fix the query that reads the events
    Change type: bugfix
    Domains: php
    Paths:
    - packages/acme_events/Classes/Domain/Repository/EventRepository.php (extension)
    - typo3/sysext/core/Classes/Database/Query/QueryBuilder.php
    Recognized as: Writing the change
    Possibly also: Registering an event listener, only if the task listens to an event something else dispatches; dispatching a new event from your own code is the other half of the subject and is not this. Its checklist items are marked as conditional below and its checks are listed separately.
    Owned by: typo3-core-patch-development. Load it where this project has it installed — the skill carries the working order for this kind of work, and this brief is one call inside it.

    Hints:
    The hints below are typo3_hint_lookup's, matched for these paths and quoted whole. A finding that cites one of these rules is citing that lookup rather than this guide.
    These are everything typo3_hint_lookup matches for these paths, so calling it again by path adds nothing; a subject it holds under another path or id is still a call away.

    # For typo3/sysext/core/Classes/Database/Query/QueryBuilder.php

    ### PHP

    ## Reading Records, and What Is Hidden From the Query
    Hints:
    - A QueryBuilder from ConnectionPool::getQueryBuilderForTable() already carries a DefaultRestrictionContainer: DeletedRestriction, HiddenRestriction, StartTimeRestriction and EndTimeRestriction. A plain select therefore hides disabled and time-restricted rows without saying so, which is what a record that is in the database and not in the result usually is.
    - Taking them off is deliberate and partial: getRestrictions()->removeAll() drops all four, and the ordinary form adds DeletedRestriction back, because a deleted row is not a row. BackendUtility::getRecord() is the worked example — removeAll(), then DeletedRestriction unless the caller asked for it too.
    - The frontend uses FrontendRestrictionContainer instead, which PageRepository sets with the current Context: the same four plus WorkspaceRestriction and FrontendGroupRestriction. Access groups and workspaces are conditions there and nowhere else.
    - Outside the frontend a query returns the live record, and the workspace version is put on top of it afterwards: PageRepository::versionOL($table, $row) overlays it in place. It is a step after the query, not a condition in it.
    - The translation works the same way: PageRepository::getLanguageOverlay($table, $row, ?LanguageAspect) replaces the row's fields with the translated ones and honours the fallback chain the LanguageAspect describes. Selecting rows by sys_language_uid is not the same thing and misses the fallback. PageRepository::getPage() shows the order both are applied in: versionOL() first, then the language overlay.
    - PageRepository::getDefaultConstraints($table, $enableFieldsToIgnore) returns the enable-field conditions as QueryBuilder expressions, for a query that builds its own restrictions. [TYPO3 v13 and newer]
    - All of this is about which rows come back. What the object built from one of them is shaped like — and why $row['hidden'] is absent on it rather than false — is record-system-properties. [TYPO3 v13 and newer]

    ## System Extension Boundaries
    Hints:
    - Keep changes inside the owning system extension unless a cross-extension contract really changes.
    - Reuse public APIs from other system extensions instead of depending on internal implementation details.
    - Check nearby extension-local tests before adding shared behavior.

    # For packages/acme_events/Classes/Domain/Repository/EventRepository.php — extension

    ### PHP

    ## Models, Repositories and the Table Behind Them
    Hints:
    - A model maps onto the table its class name implies. Configuration/Extbase/Persistence/Classes.php is where a table named differently is mapped, together with the per-property column names and the record type of a single-table inheritance.
    - Orderings are property names, not column names. Ordering by the order records have in the backend therefore needs a property for that field on the model, although it is not a domain concept.

    Relevant TYPO3 core checks — the list to run, whatever this task turns out to be:
    - `CI=true ./Build/Scripts/runTests.sh -s unit`
    - `CI=true ./Build/Scripts/runTests.sh -s functional`
    - `CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp`

    Suites that match this task, strongest first. Each is one to decide about rather than one the list above left out, and typo3_test_run_guide holds the rest for these paths.
    ## checkIntegrityPhp
    `CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp`
    Run it on any patch that writes PHP, test fixtures included — the core's own pre-merge pipeline runs it, so what it reports fails review before a person reads the patch. The one it reports most is the exception code: every throw needs a unique ten-digit integer, and undefined, duplicate and malformed ones each come back with the file and the line.
    ## e2e-browser
    `CI=true ./Build/Scripts/runTests.sh -s e2e-browser`
    Use to watch a spec run and step through it. It prints the UI URL and the instance URL beside it, then waits on a keypress it reads from /dev/tty. Like e2e-prepare it needs a controlling terminal: a run that has none falls through to the cleanup that removes the containers, and still reports SUCCESS. A run killed before that cleanup leaves them up instead, which the invocation notes say how to read and end.
    ## cglGit
    `CI=true ./Build/Scripts/runTests.sh -s cglGit`
    Targeted: `CI=true ./Build/Scripts/runTests.sh -s cgl -n`
    Use for a focused pre-review check after creating a commit, from a normal checkout only. It is `Build/Scripts/cglFixMyCommit.sh` in the container, so running that script directly buys nothing and puts it on the host's PHP rather than on the one the branch pins. Its file list comes from git inside the container, and a git worktree keeps its gitdir outside the mounted directory: git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cgl -n` where the checkout may be a worktree — it asks git nothing.
    ## checkExtensionScannerRst
    `CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst`
    Use when a deprecation or breaking change adds extension scanner matchers.

    Suggested checklist:
    - Content changes, so what is delivered has to be the version that is current after the change — that is what the editor and the visitor are owed. A defect is judged by that outcome: the old version still being served is the defect, and the error it eventually throws is the symptom.
    - Confirm the target TYPO3 core branch and issue context.
    - Inspect nearby code, tests, and established subsystem conventions.
    - Sweep the deprecations before writing: typo3_changelog_lookup with type "deprecation" and the query omitted, at TYPO3 v14. Only a change touching no TYPO3 API skips it, a CI file being the shape of one, and how small the diff is decides nothing. One call per tag: the ext: tag of each system extension this package calls into, and TCA, Fluid, Backend or Frontend for the kinds of file it ships. Every call also returns the tags that major carries, so the second onwards is read off the first.
    - Keep the patch focused on the stated task.
    - Add or update the narrowest useful test coverage.
    - Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.
    - Reproduce the bug first, ideally with a failing test that the fix turns green.
    - Settle which release branches the fix goes to with typo3_commit_message_guide, which names the lines a change of this type takes and says what claiming an older one costs. Whether the defect is on them is your reading, and a checkout holding one branch cannot make it.
    - A bugfix owes a changelog entry only where it changes what an installation renders, is configured by, or has documented, and then it is an Important below typo3/sysext/core/Documentation/Changelog/. Write it into the <lts>.x directory of the oldest branch the Releases: trailer names, and into both .x directories where two maintained lines take the change. The whole rule is one typo3_rule_lookup call with documentId "core/contribution/changelog".
    - Only if the task listens to an event something else dispatches; dispatching a new event from your own code is the other half of the subject and is not this: find the event that is really dispatched before writing a listener for it. An event class that reads plausibly and is dispatched nowhere is a listener that never runs and throws nothing, which is the failure this task shape has instead of an error.
    - Only if the task listens to an event something else dispatches; dispatching a new event from your own code is the other half of the subject and is not this: how a listener is registered is bound to the TYPO3 line and is not stated here: typo3_hint_lookup with id=events-extension-points carries both mechanisms with the versions each holds on, and a package serving two majors gets both from it.
    - Only if the task listens to an event something else dispatches; dispatching a new event from your own code is the other half of the subject and is not this: say what happens when another listener has already run. Ordering is declared or it is not there, and a listener that quietly assumes it goes first is correct until somebody installs a second extension.
    - Only if the task listens to an event something else dispatches; dispatching a new event from your own code is the other half of the subject and is not this: a hook is not the fallback. Where a subsystem still has hook-based extension points that is a fact about that subsystem — ask its own hint with the intent, and take the narrower event where there is one.
    - Write the commit message with typo3_commit_message_guide and workflow="core": summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping.

    Establish in your checkout — this server cannot see it:
    - Which files the task actually touches
      git status --short and git diff --name-only in the core checkout, then call typo3_hint_lookup with those paths for the conventions that apply to them.
    - Which tests already cover them
      Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation.
    - The branch you are on and the branches the change is meant for
      git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main.
    - Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch
      Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_snapshot_scope names the fallback revision.
    - Whether an icon identifier is registered, and which one spells the shape you want
      Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family.
    - Whether a label for this wording already exists
      Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask.

    Next lookups for this task:
    - typo3_hint_lookup — with id=events-extension-points, for the registration each covered line has and what an event class owes its listeners
    - typo3_extension_describe — for what the extension dispatching the event already registers
    - typo3_changelog_lookup — for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact
    - typo3_test_run_guide — for the targeted runTests.sh invocation — it lists every suite these domains hold, of which the testSuites above are the strongest few
    - typo3_commit_message_guide — with workflow="core", before committing — the default is a repository of your own and demands no Forge issue or release trailer
    - typo3_task_guide — again, where the work enters a subject this task did not name — the first file under a test directory, the first run of a check the repository declares, the first branch or commit, the first edit to code or documentation the package ships
    - typo3_feedback_record — when one of these answers was wrong or incomplete

Data:

.. code-block:: json

    {
        "task": "Fix the query that reads the events",
        "paths": [
            "packages/acme_events/Classes/Domain/Repository/EventRepository.php",
            "typo3/sysext/core/Classes/Database/Query/QueryBuilder.php"
        ],
        "scopes": [
            {
                "path": "packages/acme_events/Classes/Domain/Repository/EventRepository.php",
                "scope": "extension"
            },
            {
                "path": "typo3/sysext/core/Classes/Database/Query/QueryBuilder.php",
                "scope": "core"
            }
        ],
        "changeType": "bugfix",
        "targetVersion": 14,
        "targetVersions": [
            14
        ],
        "domains": [
            "php"
        ],
        "scope": "core",
        "intents": [
            {
                "id": "patch",
                "title": "Writing the change",
                "confidence": "strong",
                "condition": ""
            },
            {
                "id": "event-listener",
                "title": "Registering an event listener",
                "confidence": "weak",
                "condition": "only if the task listens to an event something else dispatches; dispatching a new event from your own code is the other half of the subject and is not this"
            }
        ],
        "skills": [
            "typo3-core-patch-development"
        ],
        "staleSkills": [],
        "guides": [],
        "hints": [
            {
                "id": "persistence-reading",
                "title": "Reading Records, and What Is Hidden From the Query",
                "category": "PHP",
                "scope": null,
                "hints": [
                    {
                        "text": "A QueryBuilder from ConnectionPool::getQueryBuilderForTable() already carries a DefaultRestrictionContainer: DeletedRestriction, HiddenRestriction, StartTimeRestriction and EndTimeRestriction. A plain select therefore hides disabled and time-restricted rows without saying so, which is what a record that is in the database and not in the result usually is.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "Taking them off is deliberate and partial: getRestrictions()->removeAll() drops all four, and the ordinary form adds DeletedRestriction back, because a deleted row is not a row. BackendUtility::getRecord() is the worked example — removeAll(), then DeletedRestriction unless the caller asked for it too.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "The frontend uses FrontendRestrictionContainer instead, which PageRepository sets with the current Context: the same four plus WorkspaceRestriction and FrontendGroupRestriction. Access groups and workspaces are conditions there and nowhere else.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "Outside the frontend a query returns the live record, and the workspace version is put on top of it afterwards: PageRepository::versionOL($table, $row) overlays it in place. It is a step after the query, not a condition in it.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "The translation works the same way: PageRepository::getLanguageOverlay($table, $row, ?LanguageAspect) replaces the row's fields with the translated ones and honours the fallback chain the LanguageAspect describes. Selecting rows by sys_language_uid is not the same thing and misses the fallback. PageRepository::getPage() shows the order both are applied in: versionOL() first, then the language overlay.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "PageRepository::getDefaultConstraints($table, $enableFieldsToIgnore) returns the enable-field conditions as QueryBuilder expressions, for a query that builds its own restrictions.",
                        "since": 13,
                        "until": null,
                        "versions": "TYPO3 v13 and newer",
                        "scope": null
                    },
                    {
                        "text": "All of this is about which rows come back. What the object built from one of them is shaped like — and why $row['hidden'] is absent on it rather than false — is record-system-properties.",
                        "since": 13,
                        "until": null,
                        "versions": "TYPO3 v13 and newer",
                        "scope": null
                    }
                ]
            },
            {
                "id": "system-extension-boundaries",
                "title": "System Extension Boundaries",
                "category": "PHP",
                "scope": null,
                "hints": [
                    {
                        "text": "Keep changes inside the owning system extension unless a cross-extension contract really changes.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "Reuse public APIs from other system extensions instead of depending on internal implementation details.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "Check nearby extension-local tests before adding shared behavior.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    }
                ]
            },
            {
                "id": "extbase-domain-mapping",
                "title": "Models, Repositories and the Table Behind Them",
                "category": "PHP",
                "scope": null,
                "hints": [
                    {
                        "text": "A model maps onto the table its class name implies. Configuration/Extbase/Persistence/Classes.php is where a table named differently is mapped, together with the per-property column names and the record type of a single-table inheritance.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    },
                    {
                        "text": "Orderings are property names, not column names. Ordering by the order records have in the backend therefore needs a property for that field on the model, although it is not a domain concept.",
                        "since": null,
                        "until": null,
                        "versions": "",
                        "scope": null
                    }
                ]
            }
        ],
        "omittedHints": [],
        "rules": [],
        "checks": [
            "CI=true ./Build/Scripts/runTests.sh -s unit",
            "CI=true ./Build/Scripts/runTests.sh -s functional",
            "CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp"
        ],
        "conditionalChecks": [],
        "testSuites": [
            {
                "suite": "checkIntegrityPhp",
                "command": "CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp",
                "runs": "check",
                "targeted": null,
                "description": "Reads typo3/sysext/*/Classes, Tests/Unit and Tests/Functional for the conventions neither lintPhp nor cgl covers: the exception code on every throw, the annotations, the namespace against the path, the test method prefix and the final test class.",
                "whenToUse": "Run it on any patch that writes PHP, test fixtures included — the core's own pre-merge pipeline runs it, so what it reports fails review before a person reads the patch. The one it reports most is the exception code: every throw needs a unique ten-digit integer, and undefined, duplicate and malformed ones each come back with the file and the line.",
                "domains": [
                    "php"
                ],
                "versions": "TYPO3 v13 and newer"
            },
            {
                "suite": "e2e-browser",
                "command": "CI=true ./Build/Scripts/runTests.sh -s e2e-browser",
                "runs": "unknown",
                "targeted": null,
                "description": "The e2e suite in Playwright's own UI, served from the container.",
                "whenToUse": "Use to watch a spec run and step through it. It prints the UI URL and the instance URL beside it, then waits on a keypress it reads from /dev/tty. Like e2e-prepare it needs a controlling terminal: a run that has none falls through to the cleanup that removes the containers, and still reports SUCCESS. A run killed before that cleanup leaves them up instead, which the invocation notes say how to read and end.",
                "domains": [
                    "php",
                    "typescript",
                    "fluid",
                    "css"
                ],
                "versions": "TYPO3 v14 and newer"
            },
            {
                "suite": "cglGit",
                "command": "CI=true ./Build/Scripts/runTests.sh -s cglGit",
                "runs": "change",
                "targeted": "CI=true ./Build/Scripts/runTests.sh -s cgl -n",
                "description": "Checks and fixes coding guideline issues in the latest committed patch.",
                "whenToUse": "Use for a focused pre-review check after creating a commit, from a normal checkout only. It is `Build/Scripts/cglFixMyCommit.sh` in the container, so running that script directly buys nothing and puts it on the host's PHP rather than on the one the branch pins. Its file list comes from git inside the container, and a git worktree keeps its gitdir outside the mounted directory: git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cgl -n` where the checkout may be a worktree — it asks git nothing.",
                "domains": [
                    "php"
                ],
                "versions": ""
            },
            {
                "suite": "checkExtensionScannerRst",
                "command": "CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst",
                "runs": "check",
                "targeted": null,
                "description": "Verifies that all .rst files referenced by the extension scanner exist.",
                "whenToUse": "Use when a deprecation or breaking change adds extension scanner matchers.",
                "domains": [
                    "docs",
                    "php"
                ],
                "versions": ""
            }
        ],
        "checklist": [
            "Content changes, so what is delivered has to be the version that is current after the change — that is what the editor and the visitor are owed. A defect is judged by that outcome: the old version still being served is the defect, and the error it eventually throws is the symptom.",
            "Confirm the target TYPO3 core branch and issue context.",
            "Inspect nearby code, tests, and established subsystem conventions.",
            "Sweep the deprecations before writing: typo3_changelog_lookup with type \"deprecation\" and the query omitted, at TYPO3 v14. Only a change touching no TYPO3 API skips it, a CI file being the shape of one, and how small the diff is decides nothing. One call per tag: the ext: tag of each system extension this package calls into, and TCA, Fluid, Backend or Frontend for the kinds of file it ships. Every call also returns the tags that major carries, so the second onwards is read off the first.",
            "Keep the patch focused on the stated task.",
            "Add or update the narrowest useful test coverage.",
            "Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.",
            "Reproduce the bug first, ideally with a failing test that the fix turns green.",
            "Settle which release branches the fix goes to with typo3_commit_message_guide, which names the lines a change of this type takes and says what claiming an older one costs. Whether the defect is on them is your reading, and a checkout holding one branch cannot make it.",
            "A bugfix owes a changelog entry only where it changes what an installation renders, is configured by, or has documented, and then it is an Important below typo3/sysext/core/Documentation/Changelog/. Write it into the <lts>.x directory of the oldest branch the Releases: trailer names, and into both .x directories where two maintained lines take the change. The whole rule is one typo3_rule_lookup call with documentId \"core/contribution/changelog\".",
            "Only if the task listens to an event something else dispatches; dispatching a new event from your own code is the other half of the subject and is not this: find the event that is really dispatched before writing a listener for it. An event class that reads plausibly and is dispatched nowhere is a listener that never runs and throws nothing, which is the failure this task shape has instead of an error.",
            "Only if the task listens to an event something else dispatches; dispatching a new event from your own code is the other half of the subject and is not this: how a listener is registered is bound to the TYPO3 line and is not stated here: typo3_hint_lookup with id=events-extension-points carries both mechanisms with the versions each holds on, and a package serving two majors gets both from it.",
            "Only if the task listens to an event something else dispatches; dispatching a new event from your own code is the other half of the subject and is not this: say what happens when another listener has already run. Ordering is declared or it is not there, and a listener that quietly assumes it goes first is correct until somebody installs a second extension.",
            "Only if the task listens to an event something else dispatches; dispatching a new event from your own code is the other half of the subject and is not this: a hook is not the fallback. Where a subsystem still has hook-based extension points that is a fact about that subsystem — ask its own hint with the intent, and take the narrower event where there is one.",
            "Write the commit message with typo3_commit_message_guide and workflow=\"core\": summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping."
        ],
        "checkoutDiscovery": [
            {
                "establish": "Which files the task actually touches",
                "how": "git status --short and git diff --name-only in the core checkout, then call typo3_hint_lookup with those paths for the conventions that apply to them."
            },
            {
                "establish": "Which tests already cover them",
                "how": "Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation."
            },
            {
                "establish": "The branch you are on and the branches the change is meant for",
                "how": "git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main."
            },
            {
                "establish": "Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch",
                "how": "Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_snapshot_scope names the fallback revision."
            },
            {
                "establish": "Whether an icon identifier is registered, and which one spells the shape you want",
                "how": "Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family."
            },
            {
                "establish": "Whether a label for this wording already exists",
                "how": "Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask."
            }
        ],
        "nextTools": [
            {
                "tool": "typo3_hint_lookup",
                "when": "with id=events-extension-points, for the registration each covered line has and what an event class owes its listeners"
            },
            {
                "tool": "typo3_extension_describe",
                "when": "for what the extension dispatching the event already registers"
            },
            {
                "tool": "typo3_changelog_lookup",
                "when": "for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact"
            },
            {
                "tool": "typo3_test_run_guide",
                "when": "for the targeted runTests.sh invocation — it lists every suite these domains hold, of which the testSuites above are the strongest few"
            },
            {
                "tool": "typo3_commit_message_guide",
                "when": "with workflow=\"core\", before committing — the default is a repository of your own and demands no Forge issue or release trailer"
            },
            {
                "tool": "typo3_task_guide",
                "when": "again, where the work enters a subject this task did not name — the first file under a test directory, the first run of a check the repository declares, the first branch or commit, the first edit to code or documentation the package ships"
            },
            {
                "tool": "typo3_feedback_record",
                "when": "when one of these answers was wrong or incomplete"
            }
        ]
    }
