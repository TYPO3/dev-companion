.. _typo3_rule_lookup:

``typo3_rule_lookup``
=====================

Search the TYPO3 rules and procedures this server carries, by topic. The core
contribution process is most of it: the commit message conventions, which
branches take a patch today, the changelog entry each change type owes, the
Gerrit push and amend workflow with both refspecs, and the notes beside
runTests.sh. It answers outside a core checkout too — setting up an extension
manual, PHPUnit in an extension, Playwright in a project — and there the
core-only documents are withheld and named rather than dropped in silence. What
comes back is the sections that matched, each naming the document it was cut
from — or, where more than one of them is in one document, that document whole,
because the rest of the page regularly answers the next thing. What the code
itself has to look like — the convention at a path being changed, the idiom a
subsystem is written in — is typo3_hint_lookup instead. Pass a documentId back
instead of a query to read any page whole; it needs no resource list. Answers
from: knowledge.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`knowledge <answer-sources-knowledge>`.

Takes
-----

.. code-block:: yaml

    # Topic to look up, in English, for example testing, review, deprecation, or
    # code style. A call carries query or documentId, never both.
    query: string  # optional
    # One document to read whole instead of searching, named by the documentId a
    # match carries — for example "core/contribution/commit-messages". Use it when
    # a matched section came out of a document whose other sections may answer what
    # the query did not: the whole page comes back, no search, no version filter. A
    # call carries query or documentId, never both.
    documentId: string  # optional
    # The TYPO3 version the answer has to hold on, for example "13.4" or "14". A
    # section bound to another major is left out. Defaults to every major this
    # repository declares typo3/cms-core for, or to the installation this server was
    # started in; where there is neither, every section comes back with the range it
    # holds for. Ignored for documentId, which returns the document as written.
    targetVersion: string  # optional

The call carries exactly one of these sets of arguments: ``query`` — or
``documentId``.

Answers with
------------

.. code-block:: yaml

    query: string
    # The exact XLF resource the lookup restricted the result to. Null means the
    # caller gave no usage context.
    resource: string or null  # optional
    matchCount: integer
    matches:
      - documentId: string
        # Title of the knowledge document.
        title: string
        # The typo3://guides resource that holds the full document.
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
        # Whether the lookup cut the body. Read the resource for the rest.
        truncated: boolean
    # Documents in the knowledge base with the topics they cover. The lookup returns
    # them when nothing matched.
    documents:  # optional
      - id: string
        title: string
        topics: [string]
    # Documents outside the searched ones that do match the query.
    elsewhere: [string]  # optional
    # Hints that match the same query. They are a second corpus, which
    # typo3_hint_lookup searches, and it takes one of these ids.
    alsoInHints:  # optional
      - id: string
        title: string
    # One of: core, uncertain, project, extension. Which kind of work this answer is
    # for. core: a patch to the TYPO3 core itself. project: the site repository
    # around an installation. extension: a package in it, a sitepackage or a
    # third-party one. uncertain: nothing in the call placed the work, and the
    # answer is the core's own.
    scope: string
    # The headings the query matched, where more than one match was in one document
    # and the answer is that document whole rather than the excerpts. Empty on every
    # other answer, whose matches carry their own heading each. The text above a
    # page's first heading is no heading and is not one of them; where it is one of
    # the matches, the answer names it in words.
    matchedHeadings: [string]
    # Documents that matched and were left out because they answer for the core
    # repository alone. Empty inside the core. Each is still readable in full as its
    # typo3://guides resource, which is the way to get one deliberately rather than
    # by accident.
    withheldDocuments:  # optional
      - id: string
        title: string

Answered
--------

Derived by ``bin/cli tools:index``, and ``bin/cli tools:check`` holds it —
the same as everything above this heading. This tool reads nothing an
installation contains: what reaches its answer is the bundled knowledge and
which TYPO3 major the caller is on, so what comes back is written down rather
than recorded from one machine's checkout. Answered against the core checkout
this repository writes below .fixtures/, declaring TYPO3 14.3.0.

rules: hit
~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "deprecation"
    }

Text:

.. code-block:: text

    A section carries the range it holds for where it has one. What is bound elsewhere: call typo3_hint_lookup with targetVersion for a convention, and typo3_test_run_guide with targetVersion for a runTests.sh command.

    ## Deprecations
    Source: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages) — matches 100% of the query terms

    - A deprecation must not use `[!!!]`.
    - A deprecation may only use `[TASK]` or `[FEATURE]`.
    - A deprecation must have a changelog RST file.
    - A deprecation needs migration guidance and may need extension scanner
      considerations.
    - All of the above is the author's side. The reader's side works the other way
      round. It asks what a given version deprecated, and what that means for code
      that uses it. The Extension Scanner in the Install Tool checks an installation
      against two directories. Those are the changelog files below
      `Documentation/Changelog/` of the core package, and the matchers below the
      install package's `Configuration/ExtensionScanner/Php/`. Both ship with a
      Composer installation.

    ## Breaking Changes
    Source: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages) — matches 100% of the query terms

    - A breaking change must use `[!!!]` before the keyword.
    - A breaking change must have a changelog RST file.
    - A breaking change should usually target `main`.
    - A removed or narrowed PHP API gets an extension scanner matcher entry in the
      same patch, below `typo3/sysext/install/Configuration/ExtensionScanner/Php/`.
      How code writes the removed member where it uses it decides the file:
      - `MethodCallMatcher.php` — an instance method.
      - `MethodCallStaticMatcher.php` — a static method.
      - `PropertyPublicMatcher.php` — a removed public property.
      - `PropertyProtectedMatcher.php` — a public property that became protected.
      - `ClassNameMatcher.php` — a whole class or interface.
    - Visibility routes a property and never a method. The method matchers are a
      weak match on the method name where code uses it. They do not resolve the
      class, so they cannot see visibility, and a protected method goes with a
      public one. `RendererRegistry->getRendererInstances` went from public to
      protected in `Breaking-110277`, and it stands in `MethodCallMatcher.php`. The
      list above has no row for a protected method because it needs none. That
      absence says nothing about whether the change owes an entry.
    - The fully qualified name with `->` or `::` keys an entry. The entry carries
      `restFiles`, which names the changelog file that removed the member. The
      method matchers add `numberOfMandatoryArguments` and
      `maximumNumberOfArguments`. A member deprecated before its removal lists both
      changelog files.
    - Every Breaking and Deprecation entry carries exactly one of `NotScanned`,
      `PartiallyScanned` and `FullyScanned` in its `.. index::` line. That tag is
      the claim those entries have to back. `FullyScanned` says the scanner finds
      every item the changelog entry names. The scanner reads PHP. So what an entry
      changes in TypoScript, TCA, YAML or JavaScript leaves it partially scanned.
    - `./Build/Scripts/runTests.sh -s checkExtensionScannerRst` checks that the
      changelog files the matchers name exist. Nothing checks the other direction. A
      missing entry surfaces when somebody audits the matcher files against the
      changelog.

    ## Changed Signatures
    Source: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages) — matches 100% of the query terms

    A signature change is the third breaking move beside a removal and a narrowing.
    An added parameter is one, an optional parameter included. A public or protected
    method on a class that is not final is an override point. Every subclass that
    declares the old signature fatals as it loads.

    - The obligation follows from the member being overridable, not from an override
      anybody found. `Breaking-101133` files a changed parameter of
      `IconFactory->getIcon()` against "custom extensions extending the method".
      `Breaking-110218` declares `LogRecord` final and calls the affected
      installations very unlikely.
    - A member marked `@internal` takes an `Important` instead. `Important-107342`
      extended `FormPersistenceManagerInterface::listForms()` by two optional
      arguments and reached `13.4.x` on that ground. The change still owes an entry.
      Only its type changes, and that is what lets such a change reach a release
      line.
    - Neither owes a matcher, and both are `NotScanned`. A matcher keys on where
      code calls a member. An override is not a call, and an added optional
      parameter leaves every existing call site valid.
    - So it decides the target branch before anything else. A maintained release
      line takes no breaking change. So a fix you owe to one cannot carry the
      signature change at all. The shape that reaches it is the additive one. That
      is a method of its own, or the state on something the callee already receives.
      To declare the class or the method final first is no cheaper, because that is
      itself a breaking change.
    - Nothing in a core checkout reports any of this. No core class has to override
      the method. So the unit, functional, coding-guidelines and static-analysis
      runs are all green on the change.
    - A member promoted from protected to public is not a signature change and owes
      none of it. The core promotes one in a plain `[TASK]` or `[BUGFIX]` commit
      that carries no changelog file. Such a patch reaches a maintained release
      line, which a breaking change cannot. The changelog holds the move in the
      other direction only: `Deprecation-86047` narrows public members of
      `TypoScriptFrontendController` to protected. A subclass that re-declares the
      member as protected fatals with "Access level … must be public". The core
      files nothing for that either.

    ## Reading Changelog Entries Instead of Writing One
    Source: The Changelog Entry a Core Patch Owes (typo3://guides/core/contribution/changelog) — matches 100% of the query terms

    - All of the above is the author's side. An installation reads the same files.
      They ship with the core package, and `typo3 upgrade:list` and
      `typo3 upgrade:run` act on the migrations behind them.
    - What a version broke, deprecated, added or noted is `typo3_changelog_lookup`.
      It answers from the installation and from the published changelog rather than
      from a checkout.

    ## Release Targets
    Source: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages) — matches 100% of the query terms

    - `Releases:` names branches: `main` and the maintained release lines, comma
      separated.
    - Which lines those are changes with every LTS release and every support window
      that closes. So it is a lookup and not a rule to remember.
      `typo3_commit_message_guide` names them where you leave the trailer out. It
      reports a branch that is out of regular support as an error.
    - A line out of regular support still has releases, and the ELTS partners make
      them. A patch pushed to Gerrit is not one of them.
    - The branch list in a checkout does not answer this. `git branch -r` reaches
      back to `TYPO3_3-6`. A count of `Releases:` trailers on recent commits samples
      what other changes needed rather than what this one does.
    - Which of the maintained lines a change reaches is your judgement of where the
      defect is. The trailer is the claim you verified it there, when you read the
      changed file on each branch you name.
    - A feature, a deprecation and a breaking change go to `main`. A backport of one
      happens, and it is the release managers' call. `origin/main..origin/13.4`
      carries three `[FEATURE]` commits against 969 `[BUGFIX]` ones, and
      `origin/main..origin/14.3` carries none at all.
    - A bug fix and a task go to `main` and to the one release line back from it. A
      defect present on an older maintained line does not put that line in the
      trailer. The older lines take priority bug fixes and grave or
      security-relevant defects. When you name one for an ordinary fix, you ask a
      merger to cherry-pick onto the wrong line.
    - So the trailer is two judgements rather than one. Where the defect is, on each
      line, is the first. Whether its severity earns an older line is the second.
      You state that judgement rather than derive it from the first.
    - What a release branch carries since its cut is `origin/main..origin/14.3`. A
      plain log on that branch, or a `--since` window over it, answers about the
      history it shares with `main`. It reports every change made before the branch
      existed as if the branch had taken it. The same count is 0 one way and 188 the
      other. The two differ by one operator and give opposite answers about whether
      features reach a release line.

    ## Review Readiness
    Source: TYPO3 Core Contribution Rules (typo3://guides/core/contribution/rules) — matches 100% of the query terms

    - A reviewer can reproduce the change from the issue or the task description.
    - The patch explains the problem and the chosen fix in a few sentences.
    - A breaking change, a migration and a deprecation need clear notes.
    - Security-sensitive behavior needs extra care and focused tests.

    Each excerpt above is one section of a longer document, and each page below carries the `##` headings that are not above. Where the task is the whole procedure rather than the fact you searched for, read the page — typo3_rule_lookup with documentId, which needs no resource list:
    - core/contribution/commit-messages — TYPO3 Core Commit Message Rules: 9 of its 13 headings are not above — Who Reads It, Summary Line, Work in Progress, Body, The Longest Line The Hook Accepts, Relationships, The Trailers A Core Commit Carries, What The Commit Hook Writes, The Changelog Entry a Message Announces.
    - core/contribution/changelog — The Changelog Entry a Core Patch Owes: 5 of its 6 headings are not above — Which Change Owes a Changelog File, Where a Changelog File Goes, What a Changelog File Is Called, What a Changelog File Carries, What Checks a Changelog File.
    - core/contribution/rules — TYPO3 Core Contribution Rules: 4 of its 5 headings are not above — Contribution Flow, Code Style, Testing, Documentation.

    The hints also cover this — call typo3_hint_lookup with the id:
    - documentation-changelog — Documentation and Changelog
    - documentation-links — Linking Into the Official Documentation From a Template or From PHP

Data:

.. code-block:: json

    {
        "query": "deprecation",
        "matchCount": 6,
        "matches": [
            {
                "documentId": "core/contribution/commit-messages",
                "title": "TYPO3 Core Commit Message Rules",
                "uri": "typo3://guides/core/contribution/commit-messages",
                "heading": "Deprecations",
                "body": "- A deprecation must not use `[!!!]`.\n- A deprecation may only use `[TASK]` or `[FEATURE]`.\n- A deprecation must have a changelog RST file.\n- A deprecation needs migration guidance and may need extension scanner\n  considerations.\n- All of the above is the author's side. The reader's side works the other way\n  round. It asks what a given version deprecated, and what that means for code\n  that uses it. The Extension Scanner in the Install Tool checks an installation\n  against two directories. Those are the changelog files below\n  `Documentation/Changelog/` of the core package, and the matchers below the\n  install package's `Configuration/ExtensionScanner/Php/`. Both ship with a\n  Composer installation.",
                "versions": "",
                "coverage": 1,
                "score": 117,
                "truncated": false
            },
            {
                "documentId": "core/contribution/commit-messages",
                "title": "TYPO3 Core Commit Message Rules",
                "uri": "typo3://guides/core/contribution/commit-messages",
                "heading": "Breaking Changes",
                "body": "- A breaking change must use `[!!!]` before the keyword.\n- A breaking change must have a changelog RST file.\n- A breaking change should usually target `main`.\n- A removed or narrowed PHP API gets an extension scanner matcher entry in the\n  same patch, below `typo3/sysext/install/Configuration/ExtensionScanner/Php/`.\n  How code writes the removed member where it uses it decides the file:\n  - `MethodCallMatcher.php` — an instance method.\n  - `MethodCallStaticMatcher.php` — a static method.\n  - `PropertyPublicMatcher.php` — a removed public property.\n  - `PropertyProtectedMatcher.php` — a public property that became protected.\n  - `ClassNameMatcher.php` — a whole class or interface.\n- Visibility routes a property and never a method. The method matchers are a\n  weak match on the method name where code uses it. They do not resolve the\n  class, so they cannot see visibility, and a protected method goes with a\n  public one. `RendererRegistry->getRendererInstances` went from public to\n  protected in `Breaking-110277`, and it stands in `MethodCallMatcher.php`. The\n  list above has no row for a protected method because it needs none. That\n  absence says nothing about whether the change owes an entry.\n- The fully qualified name with `->` or `::` keys an entry. The entry carries\n  `restFiles`, which names the changelog file that removed the member. The\n  method matchers add `numberOfMandatoryArguments` and\n  `maximumNumberOfArguments`. A member deprecated before its removal lists both\n  changelog files.\n- Every Breaking and Deprecation entry carries exactly one of `NotScanned`,\n  `PartiallyScanned` and `FullyScanned` in its `.. index::` line. That tag is\n  the claim those entries have to back. `FullyScanned` says the scanner finds\n  every item the changelog entry names. The scanner reads PHP. So what an entry\n  changes in TypoScript, TCA, YAML or JavaScript leaves it partially scanned.\n- `./Build/Scripts/runTests.sh -s checkExtensionScannerRst` checks that the\n  changelog files the matchers name exist. Nothing checks the other direction. A\n  missing entry surfaces when somebody audits the matcher files against the\n  changelog.",
                "versions": "",
                "coverage": 1,
                "score": 29,
                "truncated": false
            },
            {
                "documentId": "core/contribution/commit-messages",
                "title": "TYPO3 Core Commit Message Rules",
                "uri": "typo3://guides/core/contribution/commit-messages",
                "heading": "Changed Signatures",
                "body": "A signature change is the third breaking move beside a removal and a narrowing.\nAn added parameter is one, an optional parameter included. A public or protected\nmethod on a class that is not final is an override point. Every subclass that\ndeclares the old signature fatals as it loads.\n\n- The obligation follows from the member being overridable, not from an override\n  anybody found. `Breaking-101133` files a changed parameter of\n  `IconFactory->getIcon()` against \"custom extensions extending the method\".\n  `Breaking-110218` declares `LogRecord` final and calls the affected\n  installations very unlikely.\n- A member marked `@internal` takes an `Important` instead. `Important-107342`\n  extended `FormPersistenceManagerInterface::listForms()` by two optional\n  arguments and reached `13.4.x` on that ground. The change still owes an entry.\n  Only its type changes, and that is what lets such a change reach a release\n  line.\n- Neither owes a matcher, and both are `NotScanned`. A matcher keys on where\n  code calls a member. An override is not a call, and an added optional\n  parameter leaves every existing call site valid.\n- So it decides the target branch before anything else. A maintained release\n  line takes no breaking change. So a fix you owe to one cannot carry the\n  signature change at all. The shape that reaches it is the additive one. That\n  is a method of its own, or the state on something the callee already receives.\n  To declare the class or the method final first is no cheaper, because that is\n  itself a breaking change.\n- Nothing in a core checkout reports any of this. No core class has to override\n  the method. So the unit, functional, coding-guidelines and static-analysis\n  runs are all green on the change.\n- A member promoted from protected to public is not a signature change and owes\n  none of it. The core promotes one in a plain `[TASK]` or `[BUGFIX]` commit\n  that carries no changelog file. Such a patch reaches a maintained release\n  line, which a breaking change cannot. The changelog holds the move in the\n  other direction only: `Deprecation-86047` narrows public members of\n  `TypoScriptFrontendController` to protected. A subclass that re-declares the\n  member as protected fatals with \"Access level … must be public\". The core\n  files nothing for that either.",
                "versions": "",
                "coverage": 1,
                "score": 29,
                "truncated": false
            },
            {
                "documentId": "core/contribution/changelog",
                "title": "The Changelog Entry a Core Patch Owes",
                "uri": "typo3://guides/core/contribution/changelog",
                "heading": "Reading Changelog Entries Instead of Writing One",
                "body": "- All of the above is the author's side. An installation reads the same files.\n  They ship with the core package, and `typo3 upgrade:list` and\n  `typo3 upgrade:run` act on the migrations behind them.\n- What a version broke, deprecated, added or noted is `typo3_changelog_lookup`.\n  It answers from the installation and from the published changelog rather than\n  from a checkout.",
                "versions": "",
                "coverage": 1,
                "score": 29,
                "truncated": false
            },
            {
                "documentId": "core/contribution/commit-messages",
                "title": "TYPO3 Core Commit Message Rules",
                "uri": "typo3://guides/core/contribution/commit-messages",
                "heading": "Release Targets",
                "body": "- `Releases:` names branches: `main` and the maintained release lines, comma\n  separated.\n- Which lines those are changes with every LTS release and every support window\n  that closes. So it is a lookup and not a rule to remember.\n  `typo3_commit_message_guide` names them where you leave the trailer out. It\n  reports a branch that is out of regular support as an error.\n- A line out of regular support still has releases, and the ELTS partners make\n  them. A patch pushed to Gerrit is not one of them.\n- The branch list in a checkout does not answer this. `git branch -r` reaches\n  back to `TYPO3_3-6`. A count of `Releases:` trailers on recent commits samples\n  what other changes needed rather than what this one does.\n- Which of the maintained lines a change reaches is your judgement of where the\n  defect is. The trailer is the claim you verified it there, when you read the\n  changed file on each branch you name.\n- A feature, a deprecation and a breaking change go to `main`. A backport of one\n  happens, and it is the release managers' call. `origin/main..origin/13.4`\n  carries three `[FEATURE]` commits against 969 `[BUGFIX]` ones, and\n  `origin/main..origin/14.3` carries none at all.\n- A bug fix and a task go to `main` and to the one release line back from it. A\n  defect present on an older maintained line does not put that line in the\n  trailer. The older lines take priority bug fixes and grave or\n  security-relevant defects. When you name one for an ordinary fix, you ask a\n  merger to cherry-pick onto the wrong line.\n- So the trailer is two judgements rather than one. Where the defect is, on each\n  line, is the first. Whether its severity earns an older line is the second.\n  You state that judgement rather than derive it from the first.\n- What a release branch carries since its cut is `origin/main..origin/14.3`. A\n  plain log on that branch, or a `--since` window over it, answers about the\n  history it shares with `main`. It reports every change made before the branch\n  existed as if the branch had taken it. The same count is 0 one way and 188 the\n  other. The two differ by one operator and give opposite answers about whether\n  features reach a release line.",
                "versions": "",
                "coverage": 1,
                "score": 29,
                "truncated": false
            },
            {
                "documentId": "core/contribution/rules",
                "title": "TYPO3 Core Contribution Rules",
                "uri": "typo3://guides/core/contribution/rules",
                "heading": "Review Readiness",
                "body": "- A reviewer can reproduce the change from the issue or the task description.\n- The patch explains the problem and the chosen fix in a few sentences.\n- A breaking change, a migration and a deprecation need clear notes.\n- Security-sensitive behavior needs extra care and focused tests.",
                "versions": "",
                "coverage": 1,
                "score": 29,
                "truncated": false
            }
        ],
        "matchedHeadings": [],
        "scope": "core",
        "withheldDocuments": [],
        "alsoInHints": [
            {
                "id": "documentation-changelog",
                "title": "Documentation and Changelog"
            },
            {
                "id": "documentation-links",
                "title": "Linking Into the Official Documentation From a Template or From PHP"
            }
        ]
    }

rules: miss
~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "quantum entanglement pineapple"
    }

Text:

.. code-block:: text

    No knowledge section matched "quantum entanglement pineapple".

    This knowledge base covers:
    - How a Package's Asset Reaches a Page: The Backend Import Map, for JavaScript, The Module Template, for a Backend Module, TypoScript, for the Frontend, The Asset Collector, from a Template, The Asset Collector's Later Arrivals, Styling One Element From a Template, From PHP, Anywhere, What to Do After a Rebuild
    - Using the Backend Styleguide: Where the Styleguide Lives, Installing the Styleguide Where the Core Does Not Ship It, Reading It Without the Module, What an Example States, and What It Does Not, What a Template Writes Is Not What the Demo Shows, What Places a Class
    - Drawing a Content Icon, and a Set of Them: What the Box Fixes, What It Leaves Free, and What the Core Varies, The Set Is the Unit, What Belongs Where
    - Reporting a TYPO3 Vulnerability: Who Receives a Report, What the Report Carries, What You Leave Undone With It, A Finding That Is Already Public
    - Looking at a Change in a Real Browser: Which Installation Shows It, Reaching a DDEV Site From a Container, Where the Harness and Its Output Go
    - Proving a TypoScript Condition Verdict: What Does Not Answer It, The Marker Only the Branch Produces, A Marker You Put There on Purpose, Which URL You Request, The Negative Control, What Stands Between Two Runs
    - Proving a Rendering Held Across a Change: What to Capture, and Why Not the Templates, Capture Twice Before You Edit Anything, What Has to Run Between the Two Captures, Reading the Diff, What It Does Not Prove
    - The Prose a Patch Carries: Say What Is, Name Who Acts, One Thought per Sentence, End on the Object, Name the Place Instead of Pointing at It, Use the Words the Codebase Uses, A Longer Correct Sentence Beats a Short Broken One, One Fact, One Place, Say Why, Length Is a Ceiling, The Check Before You Hand It Over
    - The Changelog Entry a Core Patch Owes: Which Change Owes a Changelog File, Where a Changelog File Goes, What a Changelog File Is Called, What a Changelog File Carries, What Checks a Changelog File, Reading Changelog Entries Instead of Writing One
    - TYPO3 Core Commit Message Rules: Who Reads It, Summary Line, Work in Progress, Body, The Longest Line The Hook Accepts, Relationships, Release Targets, The Trailers A Core Commit Carries, What The Commit Hook Writes, Breaking Changes, Changed Signatures, Deprecations, The Changelog Entry a Message Announces
    - The Build Output the Core Commits: Where the Committed Build Output Comes From, Reading a Minified Diff Without a Build, Rebuilding It Where Nothing of Yours Is at Risk, Output No Source Produces Any More, A Backport That Conflicts in a Generated File, The Same Module Built on Two Branches
    - TYPO3 Gerrit Workflow: One-Time Setup, Where This Checkout Pushes, Fetch a Change Into This Checkout, Carry a Change Onto Current Code, Push a Patch for Review, Push a Private or Work in Progress Change, Pushing From a Git Worktree, Update an Existing Patch, Open a Patch Set on Somebody Else's Change, The Forge Issue a Change Hangs Off, Release Branches and Backports
    - Rebasing a Stale Patch: 1. What Main Landed on These Paths, 2. Whether a Fix Survived the Rewrite, 3. Removed Members That Still Have Callers, 4. The Suites That Cover What It Rewrote, The Committed JavaScript
    - Filing a TYPO3 Core Bug Report: Whether Somebody Has Already Reported It, What a Report Carries, The Area, The Target Version, The Markup, What the Description Says
    - TYPO3 Core Contribution Rules: Contribution Flow, Code Style, Testing, Documentation, Review Readiness
    - TYPO3 Contribution Sources: Core Contribution Guide, Local Policy
    - Exercising Asset Publishing in a Functional Test: The Symlink Publisher Is Active, and Not Because of the Context, Publishing Does Nothing for a Package Inside the Public Path, A Regular File at the Target Is What Fails, What the Test Instance Pins
    - Proving What a Rendering Change Renders: The Probe, Putting the Snippet Into TypoScript, Reading What It Rendered, Saying Which Part of the Response Changed, Printing What a Service Holds Mid-Request, Why the userFunc Carries an Attribute, Where lib.parseFunc_RTE Comes From, Running It, Removing the Probe
    - TYPO3 Core Script Help: Invoking runTests.sh, Common Commands, When a Suite Fails for the Install Rather Than the Code, The Pre-Commit Hook, Script Notes
    - Settling an API Question on a Declared Major That Is Not Installed: Which Majors the Question Is About, What the Changelog Settles and What It Does Not, Reading the Branch, What Reading Proves, and What It Does Not
    - Running a Package on a Declared Major That Is Not Installed: Ask What CI Already Covers, Before You Install Anything, A Composer Root of Its Own, What It Writes, and What the Installation Keeps, What the Second Root Resolves Differently, Whether the Database Survives, Which Checks Are Worth a Second Run There, What the Second Root Does Not Give, What It Leaves Behind
    - Setting Up an Extension Manual: Documentation/guides.xml, Documentation/Index.rst, The two conventional files, Rendering it before you publish it
    - Setting Up PHPUnit in a TYPO3 Extension: Build/UnitTests.xml, Build/FunctionalTests.xml, What you change in the copied files, Where the Configuration Sits in a Project, Running the suites, Database credentials for the functional suite, What a run leaves behind
    - Booting a Clone Into a Running Installation: What the Clone Does Not Carry, The Order the Steps Go In, Why You Start the Environment Twice, Where the Data Comes From, Making the Installation Agree With the Code, The Login the Dump Did Not Bring, The Host You Serve the Site Under, What Says the Boot Worked
    - Renaming an Extension That Already Holds Content: What Moves Itself and What Does Not, Where TYPO3 Stores an Identifier as a Value, The Trap That Is Not a Substitution, What Proves It
    - Setting Up Playwright in a TYPO3 Project: Build/playwright.config.ts, Build/tests/browser/helper/login.setup.ts, Build/tests/browser/frontend/pages.spec.ts, Build/tests/browser/e2e/backend.spec.ts, Reaching into a module, An Assertion Is Evidence Once It Has Been Seen to Fail, The environment the suite reads, What the login setup asserts, and why it differs by version, When the extension itself is the Composer root, What you do not commit

    For backend UI components use typo3_component_lookup, and call typo3_server_scope for what this server covers at all. If the topic should be covered here, leave a feedback with typo3_feedback_record.

Data:

.. code-block:: json

    {
        "query": "quantum entanglement pineapple",
        "matchCount": 0,
        "matches": [],
        "matchedHeadings": [],
        "scope": "core",
        "withheldDocuments": [],
        "alsoInHints": [],
        "documents": [
            {
                "id": "any/assets/how-an-asset-reaches-a-page",
                "title": "How a Package's Asset Reaches a Page",
                "topics": [
                    "The Backend Import Map, for JavaScript",
                    "The Module Template, for a Backend Module",
                    "TypoScript, for the Frontend",
                    "The Asset Collector, from a Template",
                    "The Asset Collector's Later Arrivals",
                    "Styling One Element From a Template",
                    "From PHP, Anywhere",
                    "What to Do After a Rebuild"
                ]
            },
            {
                "id": "any/backend/using-the-styleguide",
                "title": "Using the Backend Styleguide",
                "topics": [
                    "Where the Styleguide Lives",
                    "Installing the Styleguide Where the Core Does Not Ship It",
                    "Reading It Without the Module",
                    "What an Example States, and What It Does Not",
                    "What a Template Writes Is Not What the Demo Shows",
                    "What Places a Class"
                ]
            },
            {
                "id": "any/icons/drawing-a-content-icon",
                "title": "Drawing a Content Icon, and a Set of Them",
                "topics": [
                    "What the Box Fixes",
                    "What It Leaves Free, and What the Core Varies",
                    "The Set Is the Unit",
                    "What Belongs Where"
                ]
            },
            {
                "id": "any/security/reporting-a-vulnerability",
                "title": "Reporting a TYPO3 Vulnerability",
                "topics": [
                    "Who Receives a Report",
                    "What the Report Carries",
                    "What You Leave Undone With It",
                    "A Finding That Is Already Public"
                ]
            },
            {
                "id": "any/testing/browser-check",
                "title": "Looking at a Change in a Real Browser",
                "topics": [
                    "Which Installation Shows It",
                    "Reaching a DDEV Site From a Container",
                    "Where the Harness and Its Output Go"
                ]
            },
            {
                "id": "any/testing/proving-a-condition",
                "title": "Proving a TypoScript Condition Verdict",
                "topics": [
                    "What Does Not Answer It",
                    "The Marker Only the Branch Produces",
                    "A Marker You Put There on Purpose",
                    "Which URL You Request",
                    "The Negative Control",
                    "What Stands Between Two Runs"
                ]
            },
            {
                "id": "any/testing/proving-a-rendering-held",
                "title": "Proving a Rendering Held Across a Change",
                "topics": [
                    "What to Capture, and Why Not the Templates",
                    "Capture Twice Before You Edit Anything",
                    "What Has to Run Between the Two Captures",
                    "Reading the Diff",
                    "What It Does Not Prove"
                ]
            },
            {
                "id": "any/writing/the-prose-a-patch-carries",
                "title": "The Prose a Patch Carries",
                "topics": [
                    "Say What Is",
                    "Name Who Acts",
                    "One Thought per Sentence",
                    "End on the Object",
                    "Name the Place Instead of Pointing at It",
                    "Use the Words the Codebase Uses",
                    "A Longer Correct Sentence Beats a Short Broken One",
                    "One Fact, One Place",
                    "Say Why",
                    "Length Is a Ceiling",
                    "The Check Before You Hand It Over"
                ]
            },
            {
                "id": "core/contribution/changelog",
                "title": "The Changelog Entry a Core Patch Owes",
                "topics": [
                    "Which Change Owes a Changelog File",
                    "Where a Changelog File Goes",
                    "What a Changelog File Is Called",
                    "What a Changelog File Carries",
                    "What Checks a Changelog File",
                    "Reading Changelog Entries Instead of Writing One"
                ]
            },
            {
                "id": "core/contribution/commit-messages",
                "title": "TYPO3 Core Commit Message Rules",
                "topics": [
                    "Who Reads It",
                    "Summary Line",
                    "Work in Progress",
                    "Body",
                    "The Longest Line The Hook Accepts",
                    "Relationships",
                    "Release Targets",
                    "The Trailers A Core Commit Carries",
                    "What The Commit Hook Writes",
                    "Breaking Changes",
                    "Changed Signatures",
                    "Deprecations",
                    "The Changelog Entry a Message Announces"
                ]
            },
            {
                "id": "core/contribution/committed-build-output",
                "title": "The Build Output the Core Commits",
                "topics": [
                    "Where the Committed Build Output Comes From",
                    "Reading a Minified Diff Without a Build",
                    "Rebuilding It Where Nothing of Yours Is at Risk",
                    "Output No Source Produces Any More",
                    "A Backport That Conflicts in a Generated File",
                    "The Same Module Built on Two Branches"
                ]
            },
            {
                "id": "core/contribution/gerrit-workflow",
                "title": "TYPO3 Gerrit Workflow",
                "topics": [
                    "One-Time Setup",
                    "Where This Checkout Pushes",
                    "Fetch a Change Into This Checkout",
                    "Carry a Change Onto Current Code",
                    "Push a Patch for Review",
                    "Push a Private or Work in Progress Change",
                    "Pushing From a Git Worktree",
                    "Update an Existing Patch",
                    "Open a Patch Set on Somebody Else's Change",
                    "The Forge Issue a Change Hangs Off",
                    "Release Branches and Backports"
                ]
            },
            {
                "id": "core/contribution/rebasing-a-stale-patch",
                "title": "Rebasing a Stale Patch",
                "topics": [
                    "1. What Main Landed on These Paths",
                    "2. Whether a Fix Survived the Rewrite",
                    "3. Removed Members That Still Have Callers",
                    "4. The Suites That Cover What It Rewrote",
                    "The Committed JavaScript"
                ]
            },
            {
                "id": "core/contribution/reporting-an-issue",
                "title": "Filing a TYPO3 Core Bug Report",
                "topics": [
                    "Whether Somebody Has Already Reported It",
                    "What a Report Carries",
                    "The Area",
                    "The Target Version",
                    "The Markup",
                    "What the Description Says"
                ]
            },
            {
                "id": "core/contribution/rules",
                "title": "TYPO3 Core Contribution Rules",
                "topics": [
                    "Contribution Flow",
                    "Code Style",
                    "Testing",
                    "Documentation",
                    "Review Readiness"
                ]
            },
            {
                "id": "core/contribution/sources",
                "title": "TYPO3 Contribution Sources",
                "topics": [
                    "Core Contribution Guide",
                    "Local Policy"
                ]
            },
            {
                "id": "core/testing/exercising-asset-publishing",
                "title": "Exercising Asset Publishing in a Functional Test",
                "topics": [
                    "The Symlink Publisher Is Active, and Not Because of the Context",
                    "Publishing Does Nothing for a Package Inside the Public Path",
                    "A Regular File at the Target Is What Fails",
                    "What the Test Instance Pins"
                ]
            },
            {
                "id": "core/testing/proving-a-rendering",
                "title": "Proving What a Rendering Change Renders",
                "topics": [
                    "The Probe",
                    "Putting the Snippet Into TypoScript",
                    "Reading What It Rendered",
                    "Saying Which Part of the Response Changed",
                    "Printing What a Service Holds Mid-Request",
                    "Why the userFunc Carries an Attribute",
                    "Where lib.parseFunc_RTE Comes From",
                    "Running It",
                    "Removing the Probe"
                ]
            },
            {
                "id": "core/testing/scripts",
                "title": "TYPO3 Core Script Help",
                "topics": [
                    "Invoking runTests.sh",
                    "Common Commands",
                    "When a Suite Fails for the Install Rather Than the Code",
                    "The Pre-Commit Hook",
                    "Script Notes"
                ]
            },
            {
                "id": "extension/compatibility/a-declared-major-that-is-not-installed",
                "title": "Settling an API Question on a Declared Major That Is Not Installed",
                "topics": [
                    "Which Majors the Question Is About",
                    "What the Changelog Settles and What It Does Not",
                    "Reading the Branch",
                    "What Reading Proves, and What It Does Not"
                ]
            },
            {
                "id": "extension/compatibility/running-on-a-declared-major-that-is-not-installed",
                "title": "Running a Package on a Declared Major That Is Not Installed",
                "topics": [
                    "Ask What CI Already Covers, Before You Install Anything",
                    "A Composer Root of Its Own",
                    "What It Writes, and What the Installation Keeps",
                    "What the Second Root Resolves Differently",
                    "Whether the Database Survives",
                    "Which Checks Are Worth a Second Run There",
                    "What the Second Root Does Not Give",
                    "What It Leaves Behind"
                ]
            },
            {
                "id": "extension/documentation/manual",
                "title": "Setting Up an Extension Manual",
                "topics": [
                    "Documentation/guides.xml",
                    "Documentation/Index.rst",
                    "The two conventional files",
                    "Rendering it before you publish it"
                ]
            },
            {
                "id": "extension/testing/phpunit",
                "title": "Setting Up PHPUnit in a TYPO3 Extension",
                "topics": [
                    "Build/UnitTests.xml",
                    "Build/FunctionalTests.xml",
                    "What you change in the copied files",
                    "Where the Configuration Sits in a Project",
                    "Running the suites",
                    "Database credentials for the functional suite",
                    "What a run leaves behind"
                ]
            },
            {
                "id": "project/installation/booting-a-clone",
                "title": "Booting a Clone Into a Running Installation",
                "topics": [
                    "What the Clone Does Not Carry",
                    "The Order the Steps Go In",
                    "Why You Start the Environment Twice",
                    "Where the Data Comes From",
                    "Making the Installation Agree With the Code",
                    "The Login the Dump Did Not Bring",
                    "The Host You Serve the Site Under",
                    "What Says the Boot Worked"
                ]
            },
            {
                "id": "project/refactoring/renaming-an-installed-extension",
                "title": "Renaming an Extension That Already Holds Content",
                "topics": [
                    "What Moves Itself and What Does Not",
                    "Where TYPO3 Stores an Identifier as a Value",
                    "The Trap That Is Not a Substitution",
                    "What Proves It"
                ]
            },
            {
                "id": "project/testing/playwright",
                "title": "Setting Up Playwright in a TYPO3 Project",
                "topics": [
                    "Build/playwright.config.ts",
                    "Build/tests/browser/helper/login.setup.ts",
                    "Build/tests/browser/frontend/pages.spec.ts",
                    "Build/tests/browser/e2e/backend.spec.ts",
                    "Reaching into a module",
                    "An Assertion Is Evidence Once It Has Been Seen to Fail",
                    "The environment the suite reads",
                    "What the login setup asserts, and why it differs by version",
                    "When the extension itself is the Composer root",
                    "What you do not commit"
                ]
            }
        ]
    }
