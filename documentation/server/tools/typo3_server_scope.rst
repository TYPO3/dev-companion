:navigation-title: server_scope

``typo3_server_scope``
======================

*Orientation for this server*

Orientation for this server: what it covers and at which depth, what it
deliberately does not cover, and which tool to call when. Start here when it is
unclear whether this server can answer a question at all, or which of the
lookups is the right one. It answers whole, which is the largest answer here.
Where you know which part you need, say whether an installation and its console
answer, name it in sections. Answers from: knowledge, installation.

.. facts::

    :Answers from: :ref:`knowledge <answer-sources-knowledge>`, :ref:`installation <answer-sources-installation>`
    :readOnlyHint: ``true``
    :destructiveHint: ``false``
    :idempotentHint: ``true``
    :openWorldHint: ``false``

Takes
-----

.. code-block:: yaml

    # The parts of the answer to return, named by the fields they arrive in. Omit it
    # for all of them, which is the answer for a caller that does not yet know what
    # it can ask this server. Whatever you name, the answer keeps the purpose, the
    # instructions clients receive at initialize time, and the tools your list
    # lacks. The withheld field says what each part left out would have held.
    sections: [string]  # optional

Answers with
------------

.. code-block:: yaml

    # What this server is for.
    purpose: string
    # The boundary statement clients receive at initialize time.
    instructions: string  # optional
    covers:  # optional
      - topic: string
        # How deep the coverage of the topic goes.
        depth: string
        tools: [string]
        # Knowledge file or typo3:// resource behind the topic.
        source: string
        # One of: core, project, extension, any. Which kind of work the answers are
        # for. core: the contribution process and the scripts of that repository.
        # any: a convention that holds wherever somebody writes TYPO3.
        scope: string
    doesNotCover:  # optional
      - topic: string
        why: string
        # What to do instead of a call to this server.
        instead: string
    checkoutDiscovery:  # optional
      - establish: string
        how: string
    routing:  # optional
      - when: string
        call: string
    # The TYPO3 versions the knowledge binds to. The answer leaves out a statement
    # outside a range when it knows a target version.
    versions:  # optional
      - major: integer
        # The branch this server verifies that line against.
        branch: string
        # elts, lts, stable, or development. lts and elts are long-term support,
        # elts the paid extension past its end. stable is a released line before its
        # LTS release.
        status: string
    # Which tools are worth a call in the state this machine is in. Where nothing
    # runs, the tools answer from knowledge and packages alone. Every tool states
    # its own sources at the foot of its description; this groups them the other way
    # round.
    answersFrom:  # optional
      - # installation, packages, knowledge, network or checkout.
        source: string
        # What that source is, and what it cannot answer.
        meaning: string
        # The offered tools it can answer. A tool with two sources stands under
        # both.
        tools: [string]
    excludedTools:
      # The tools that are really gone, and the only reason the list is ever shorter
      # than the documented one. Empty unless the variable has a value.
      names: [string]
      # Names in the variable that took nothing away. No tool answers to the name,
      # or it is one of the three this server offers whatever the variable says.
      # Each of them is in the tool list. Absent means nothing to report, which is
      # the ordinary case.
      ignored: [string]  # optional
      # Environment variable that names them.
      variable: string
    installation:  # optional
      # Whether there is an installation to read at all.
      found: boolean
      # Absolute path of the installation.
      root: string or null  # optional
      # core-checkout or composer-project.
      kind: string or null  # optional
      # How the server found it: discovery (a walk up from the start directory) or
      # environment (TYPO3_DEV_COMPANION_ROOT names it).
      via: string or null  # optional
      # Where the search started, or the configured value.
      startedFrom: string or null  # optional
      # The directories the search walked. A failure here means a layout the server
      # cannot read or a server started in the wrong place; this says which.
      searched: [string]
      # TYPO3 packages found in it.
      packageCount: integer
      # Set when the server could not follow a configured value. Nothing falls back
      # to a discovered installation.
      misconfiguration: string or null  # optional
      console:
        # False means every installation-backed tool answers with unsupported in
        # place of its result.
        reachable: boolean
        # ddev, php, or override.
        via: string or null  # optional
        # The PHP version it runs on, where the server knows it.
        php: string or null  # optional
        # The invocation, as the server runs it.
        command: string or null  # optional
        # Why it cannot run. Null when it can.
        reason: string or null  # optional
        # What limits the console the server found. An interpreter of this machine
        # answers for a project whose containers are stopped. That reaches what
        # TYPO3 assembles from its own files and not the services the project's own
        # runtime brings. Null when nothing limits it.
        caveat: string or null  # optional
      settings:  # optional
        # Environment variable that names the installation root.
        root: string
        # Environment variable that names the console command.
        console: string
    upstream:  # optional
      # One of: current, behind, unknown, unavailable, off, not-a-checkout. current:
      # nothing on the upstream branch is missing here. behind: commits are, and
      # every answer says so. unknown: no read yet, or the checkout stands on a
      # commit the upstream does not have. unavailable: the last read failed, and
      # lastAnswered says when one last worked. off: the variable turned the read
      # off. not-a-checkout: the server runs from an install without a git
      # directory.
      state: string
      # Where the upstream is.
      repository: string
      # The commit this checkout stands on.
      revision: string or null
      # How many commits the upstream branch has that this checkout lacks. Null
      # where nothing counted them.
      behind: integer or null
      # When the start last asked, in UTC. The answer is kept an hour and shared by
      # every session of this checkout.
      checkedAt: string or null
      # When a read last worked.
      lastAnswered: string or null
      # Why the last read failed: GitHub did not answer, or answered without a
      # count. Null unless the state is unavailable.
      reason: string or null
      # Environment variable that turns the read off.
      variable: string
    # The parts this call did not ask for. A field named here is absent from this
    # answer rather than empty, so a narrowed answer does not read as the whole one.
    # Empty where sections was not passed, which is the whole orientation.
    withheld:
      - section: string
        # What that part of the answer would have carried.
        carries: string
    # What the caller has to do with this answer that no field states beside the
    # value it is about: the next call, a command to run first, a boundary on what
    # to reuse. A notice that this server is stale or behind its repository opens
    # the list. Empty where nothing is owed.
    owed: [string]

Answered
--------

Recorded on 2026-11-27 by ``bin/cli tools:record``. Answered against
core-checkout, TYPO3 15.0.0-dev, the main core checkout below .checkouts/. Its
console is out of reach: <installation> has no TYPO3 console — none of
bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed —
vendor/autoload.php is not there either, and composer install writes both.
Nothing checks what is below this heading; everything above it is derived from
the class that answers the call, and ``bin/cli tools:check`` holds it.

scope
~~~~~

Called with:

.. code-block:: json

    {}

.. tabs::

    .. tab:: Text

        .. code-block:: text

            A development companion for coding agents working with TYPO3, for three audiences: the core contributor, the extension author and the site developer. It establishes the project and installation the agent works in, supplies current, version-bound TYPO3 knowledge, and hands task workflows to the skills that own them. A scope answer describes what is present and does not treat it as correct. The server also answers what is registered in the installation it was started in. Where that installation does not boot or does not exist, the bundled knowledge and the installed packages answer, and the answer says which. Every answer says which TYPO3 versions it holds for and which kind of work it belongs to.

            Covered, and how deeply. Each topic says which kind of work its answers are for: core is the contribution process and the scripts that belong to that repository, any is a convention that holds wherever TYPO3 is written. Where the source names the installation, the answer is read from the one this server was started in rather than from any snapshot.
            ## Contribution rules and review readiness
            Curated prose. The rules a patch is judged by, not a full style guide.
            Tools: typo3_rule_lookup, typo3_task_guide
            Source: typo3://guides/core/contribution/rules (core)
            ## Gerrit workflow: setup, pushing, amending, opening a patch set on somebody else's change, backports
            Curated prose, command level. Covers the local git side; it cannot talk to the Gerrit server.
            Tools: typo3_rule_lookup
            Source: typo3://guides/core/contribution/gerrit-workflow (core)
            ## The JavaScript and CSS the core commits beside the sources they are built from
            Curated prose, command level: which source tree builds which committed file, how a minified file is diffed, and a rebuild in a separate worktree. It covers a backport that conflicts in a built file. typo3_test_run_guide names the suite per branch.
            Tools: typo3_rule_lookup
            Source: typo3://guides/core/contribution/committed-build-output (core)
            ## What a patch loses when the code moved under it, which no conflict reports
            Four checks against a checkout: what main changed on the paths since the base, whether a fix in a rewritten file survives, which removed members still have callers, and which suites cover the rewritten paths. Plus the git call that shows stale committed JavaScript.
            Tools: typo3_rule_lookup, typo3_test_run_guide
            Source: typo3://guides/core/contribution/rebasing-a-stale-patch (core)
            ## Forge issues: what one says and what was decided about it, which other issues describe the same thing, and what stands open in the core's backlog
            Forger's index of the tracker, read live, and the tracker's own API where Forger does not answer. By number: the report, the comments that decided it, related issues, files and review changes. By words: text matches, ranked by Forger. As a backlog: the core's issues by age, neglect or recency, with filters and a breakdown. Each says whether the installed packages still ship the cited code.
            Tools: typo3_forge_lookup
            Source: https://forger.typo3.com, then https://forge.typo3.org (network) (core)
            ## Gerrit changes: whether a patch already exists, what state its review is in, which changes touch a path or name a wording, and what stands open in the core's review backlog
            Forger's index of the review server, read live, for an issue's changes, a search and a backlog without a person or vote filter. The anonymous Gerrit REST API for the rest and where Forger does not answer. By change number, Change-Id or commit: the change, its siblings, patch set, paths, votes, comments, chain and trailers, and on request the review log and the CI jobs that failed. By words and path: the matching changes. As a backlog: open changes by age or neglect, with filters. A private change is invisible.
            Tools: typo3_gerrit_lookup
            Source: https://forger.typo3.com, then https://review.typo3.org, and https://git.typo3.org (network) (core)
            ## Commit messages
            Rules plus a working draft and a check, including 72-character body wrapping. workflow="project" serves the same conventions for a repository without Forge issues or release branches. The user-invoked prompt commit_message exposes the same guide.
            Tools: typo3_commit_message_guide, typo3_rule_lookup
            Source: typo3://guides/core/contribution/commit-messages (core)
            ## How the comments and docblocks a patch carries have to read
            Ten rules, each with a rejected sentence and its replacement: say what is, name who acts, one thought per sentence, and seven more. Whether a comment is owed at all is the codebase's own rule.
            Tools: typo3_rule_lookup, typo3_task_guide
            Source: typo3://guides/any/writing/the-prose-a-patch-carries (any)
            ## The changelog entry a core patch owes: which type, which directory a backport goes into, and what checks the file
            Curated prose, with the skeleton in the documentation-changelog hint: which of the four types a change owes, and which release directory the file goes in, keyed on the branches the patch reaches. It names the core's template and the command that writes the file.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/core/contribution/changelog (core)
            ## What a new core issue carries: the fields of a Bug, where the area comes from, who sets the target version, and the markup the description renders as
            Curated prose for writing a report: the trackers, the fields of a Bug, the order a report is written in, and the Textile markup the description renders as. typo3_issue_report_guide drafts and checks the issue and opens the filled-in form. Filing it needs an account and stays the caller's.
            Tools: typo3_issue_report_guide, typo3_rule_lookup, typo3_forge_lookup
            Source: typo3://guides/core/contribution/reporting-an-issue (core)
            ## Core testing suites: runTests.sh options and targeted invocation
            Every suite this knowledge base knows, with the command, the targeted form, and when to use it. For a path outside the core, the answer states the boundary instead of a command.
            Tools: typo3_test_run_guide, typo3_script_lookup
            Source: knowledge/test-suite-hints.json, typo3://guides/core/testing/scripts (core)
            ## Proving what a rendering contains, where a finding turns on it and no test in the checkout produces it
            The throwaway functional test that renders one page and prints the result: the cObj, how output leaves the test, one marker per region, the targeted invocation, and where the RTE setup comes from per version. It does not say what parseFunc does to a snippet.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/core/testing/proving-a-rendering (core)
            ## Exercising the system resource publisher from a functional test
            Which file system publisher a test instance runs and why, why publishing does nothing for a package below the public path, and which state at the target fails for real. From TYPO3 14.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/core/testing/exercising-asset-publishing (core)
            ## Timing a code path between a patch and its parent, where a review asks what a change costs at runtime
            The throwaway functional test that times one path on the patch and on its parent, so the finding is a ratio, and what the number leaves out.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/core/testing/timing-a-code-path (core)
            ## The PHPUnit configuration an extension runs its own tests with
            The two Build/ files, whole, one variant per PHPUnit the paired typo3/testing-framework release allows, with the attributes a copy must correct and the environment a functional run reads. It names no dependency constraint.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/extension/testing/phpunit (extension)
            ## What an extension's Documentation/ directory consists of
            The files that make a directory a manual: a minimal guides.xml, the Index.rst header and toctree, two look-alikes that are no template, and the render command with the flag that fails on a broken directive. It does not write the chapters.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/extension/documentation/manual (extension)
            ## Whether an API is there on a TYPO3 major the package declares and the installation does not have
            How to settle it without a run: which declaration matters, what the changelog settles, and how to read one symbol off the other branch. It states no signature, because a bundled one goes wrong at the next release.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/extension/compatibility/a-declared-major-that-is-not-installed (extension)
            ## What the backend styleguide states about a component, and what it leaves unstated
            Where the styleguide lives per major, what its listing settles, and the two ways a demo is misread: an example does not mark its required parts, and the classes in its template are not the whole component.
            Tools: typo3_rule_lookup, typo3_component_lookup
            Source: typo3://guides/any/backend/using-the-styleguide (any)
            ## Which route a package's own CSS or JavaScript takes into a page, and what proves it still carries
            Every route a package's CSS or JavaScript takes into a page, with its check: the backend import map, PageRenderer calls, TypoScript includeCSS, the AssetCollector, and the later ViewHelpers. It ends with what to do after a rebuild renamed or dropped a file.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/any/assets/how-an-asset-reaches-a-page (any)
            ## Running a package's own suite against a TYPO3 major it declares and the installation does not have
            The procedure: what CI already covers, a second Composer root resolved for the other major, what that root resolves differently, and which checks are worth a run there. It installs nothing itself.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/extension/compatibility/running-on-a-declared-major-that-is-not-installed (extension)
            ## The Playwright suite a project runs its browser tests from
            The configuration, the backend login per TYPO3 major, and one spec, whole. Beside them: where the base URL and the credentials come from, and which locator on the login form is ambiguous. It installs nothing.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/project/testing/playwright (project)
            ## Booting a cloned project repository into a running installation
            The steps from a clone to a site that answers: what the clone lacks, why the environment starts twice, where the data comes from, and what a login costs. It runs nothing.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/project/installation/booting-a-clone (project)
            ## Renaming an extension key, its tables or its CTypes in an installation that holds records
            What a rename touches on the data side, in order: new tables and nothing dropped, the positional INSERT ... SELECT trap, and every column that stores a name as a value. It runs nothing, and the proof is a query.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/project/refactoring/renaming-an-installed-extension (project)
            ## Looking at a change in a real browser, against the installation that has the content
            Which installation can show the case, how a browser in a container reaches a DDEV site, and where a harness and its screenshots go so neither reaches a commit. It runs nothing.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/any/testing/browser-check (any)
            ## Drawing the icon a content element is picked by, and the set it belongs to
            The 16-pixel box of the core's content icons, what the set varies to tell elements apart, and the check that a set works at rendered size. Whether an identifier resolves is typo3_icon_lookup's question.
            Tools: typo3_rule_lookup, typo3_icon_lookup
            Source: typo3://guides/any/icons/drawing-a-content-icon (any)
            ## Proving that a TypoScript condition matched against a running installation
            How a condition's verdict is read off the page: a marker only the guarded branch renders, the negative control, and the cache between two runs. It requests nothing and flushes nothing.
            Tools: typo3_rule_lookup, typo3_hint_lookup
            Source: typo3://guides/any/testing/proving-a-condition (any)
            ## Proving that a change to how a site renders changed only what it meant to
            How the pages to capture are chosen from the content that exists, why the baseline is captured twice, which cache keeps the old compiled Fluid, and how the diffs are read. It requests nothing and flushes nothing.
            Tools: typo3_rule_lookup, typo3_record_lookup
            Source: typo3://guides/any/testing/proving-a-rendering-held (any)
            ## Reporting a security defect in TYPO3: who receives it, what the report carries, and what is not done with it in the meantime
            TYPO3's published policy, from SECURITY.md and the security team's pages, for the core and for extensions alike. It rates no finding.
            Tools: typo3_rule_lookup
            Source: typo3://guides/any/security/reporting-a-vulnerability (any)
            ## Where the upstream contribution documentation is
            The official contribution guide is not bundled. This names its entry points, so a question past the local documents goes to the source.
            Tools: typo3_rule_lookup
            Source: typo3://guides/core/contribution/sources (core)
            ## Core conventions: DI, events and hooks, TCA and FormEngine, FormEngine data providers, DataHandler, routing, Fluid templates and ViewHelpers, frontend page rendering with PAGEVIEW, how a sitepackage is laid out, records in the frontend and the routing for them, registering a content element, Extbase plugins, TypoScript site sets and TSconfig, language files, upgrade wizards, frontend DataProcessors, upgrading an installation, testing strategy, assembling a test suite for a project extension, setting up static analysis and the coding standards fixer for an extension, browser and accessibility tests with Playwright
            Conventions per subsystem, matched by path or topic, from both angles: what a change to the subsystem has to satisfy, and how the mechanism is used. It describes how the core is built, never what your checkout contains.
            Tools: typo3_hint_lookup, typo3_task_guide
            Source: knowledge/hints/ (any)
            ## Task workflows for extension, sitepackage and project work: backend modules, content elements, package health, change review, distribution content, asset builds, sitepackages, documentation, tests, the development installation, and upgrades
            The skills this server publishes, one resource per workflow: the order of the steps, with the lookup that answers each fact. bin/typo3-dev-companion install writes the same files into the client's own skills directory.
            Tools: typo3_task_guide
            Source: typo3://skill/typo3-backend-module-development/SKILL.md, typo3://skill/typo3-content-element-development/SKILL.md, typo3://skill/typo3-development-installation/SKILL.md, typo3://skill/typo3-distribution-content/SKILL.md, typo3://skill/typo3-extension-documentation/SKILL.md, typo3://skill/typo3-extension-health/SKILL.md, typo3://skill/typo3-extension-patch-review/SKILL.md, typo3://skill/typo3-extension-asset-build/SKILL.md, typo3://skill/typo3-extension-testing/SKILL.md, typo3://skill/typo3-extension-upgrade/SKILL.md, typo3://skill/typo3-sitepackage-development/SKILL.md (any)
            ## The core's own task workflows: saying what an open issue still claims, getting a patch under review into a checkout, writing a patch and carrying it to review, and reviewing somebody else's patch set
            The same shape, with steps that stop at the core repository: the Forge issue, the patch set ref, runTests.sh, the changelog entry and the Gerrit push. A triage writes a verdict only, and a checkout stops at conflicts the patch does not decide.
            Tools: typo3_task_guide
            Source: typo3://skill/typo3-core-issue-triage/SKILL.md, typo3://skill/typo3-core-patch-checkout/SKILL.md, typo3://skill/typo3-core-patch-development/SKILL.md, typo3://skill/typo3-core-patch-review/SKILL.md (core)
            ## Official TYPO3 API, reference and tutorial documentation
            Live search in the tables of contents of TYPO3 Explained, TypoScript Explained, the TCA Reference and the ViewHelper Reference, for one covered release. Pass a result URL back to read the page as text. An unreachable service is not an empty search.
            Tools: typo3_documentation_lookup
            Source: https://docs.typo3.org (any)
            ## Whether a documentation permalink resolves, what it lands on, and which identifier replaces an old docs.typo3.org URL
            The Sphinx inventory of each manual. Per identifier: the page and anchor, the registered type, and the other spellings. Per URL: the identifiers that point at it, or candidates where the page is gone. Many at a time. It says which branch answered. An extension manual outside the core is not answered.
            Tools: typo3_permalink_lookup
            Source: https://docs.typo3.org (network), knowledge/manuals.json (any)
            ## Backend CSS architecture, design tokens, class naming
            Curated prose covering the Sass sources, the token contract, and the component structure.
            Tools: typo3_hint_lookup, typo3_rule_lookup
            Source: knowledge/hints/backend-css.json (any)
            ## Backend UI components: markup, variants, custom properties
            A curated subset, not every core CSS class. The active installation supplies presence, classes, custom properties and styleguide markup. A version-bound snapshot is the fallback.
            Tools: typo3_component_lookup
            Source: installed EXT:backend and EXT:styleguide files; knowledge/catalog/component/entries.json as index and fallback — read from the installation being worked in, not from a bundled snapshot. (any)
            ## Which extensions the TYPO3 core ships, and on which versions
            Every system extension of every covered line, by key and package name, with its purpose and version range. It answers for an extension that is not installed. A miss means not a system extension there.
            Tools: typo3_system_extension_lookup
            Source: knowledge/catalog/system-extension/entries.json (any)
            ## Where the core keeps its own worked examples
            An index of the core's worked examples: one line per directory, its package and its versions. Use it where a hint is thin.
            Tools: typo3_reference_list
            Source: knowledge/catalog/reference/entries.json (any)
            ## Translation domains: how the domain of an XLF file is derived
            Computed, not a snapshot: the core's path-to-domain rules, ported, so any XLF path resolves, a new one included. Where the installation predates translation domains, the answer is the full LLL:EXT: reference.
            Tools: typo3_translation_domain_lookup
            Source: src/Knowledge/Catalog/TranslationDomain.php, knowledge/hints/labels.json (any)
            ## How current the catalogs are
            Whether component contracts came from the active installation or the bundled fallback, plus the core revision, branch, and verification date behind that fallback.
            Tools: typo3_snapshot_scope
            Source: knowledge/catalog/meta.json (any)
            ## What the installation you are working in supplies: backend component contracts, labels, icons, backend modules, Fluid namespaces, effective configuration, the columns TYPO3 derives for a table beside what its database has, the services its container assembles, and the data structure one of its flex fields resolves to
            Answered by that installation, across its active packages, project extensions included. Its console answers where a command exists. Where none does, or where the command carries less, TYPO3 boots in a subprocess and its container answers. Package files answer where neither works. Label reuse is limited to the XLF resource of the consuming code. Every answer names its source.
            Tools: typo3_component_lookup, typo3_label_lookup, typo3_icon_lookup, typo3_backend_module_lookup, typo3_fluid_namespace_list, typo3_configuration_lookup, typo3_schema_lookup, typo3_service_lookup, typo3_flexform_lookup
            Source: the discovered TYPO3 installation — read from the installation being worked in, not from a bundled snapshot. (any)
            ## What is in a table of one of this project's own extensions
            The rows and their counts: per page, live, hidden or deleted, with uid, label, timestamps and flags, narrowed by exact values. Only the tables of this project's own extensions are read. Every answer says it used the shell user's database access, not a backend user's permissions.
            Tools: typo3_record_lookup
            Source: the discovered installation, booted, with one grouped count and one row read against its database. What a row carries is fixed here rather than composed by the caller. (project)
            ## The project around the installation: its extensions, its sites and their sets, its own commands, the environment it declares and what that environment runs by itself, and what one of those extensions registers
            Read from the repository's files — composer.json, package.json, config/sites, .github/workflows and .ddev — so it answers on a fresh clone. It states the declared commands, the PHP and Node versions each place declares, and the DDEV hooks and pull recipes. Per extension: its tables, content elements, modules, icons, site sets, services, middlewares and Fluid namespaces. It describes and does not rate.
            Tools: typo3_project_describe, typo3_extension_describe
            Source: the discovered project — read from the installation being worked in, not from a bundled snapshot. (any)
            ## What a TYPO3 version broke, deprecated, added or noted
            One entry per change, searched by words, type and version, from the changelog docs.typo3.org renders after every merge. The core package on disk answers what docs.typo3.org does not list. Each entry names its source.
            Tools: typo3_changelog_lookup
            Source: docs.typo3.org for every covered major, and the discovered installation for what docs.typo3.org does not list or where it does not answer. Not a bundled snapshot either way. (any)
            ## Which published extensions cover a subject, and which versions of one the TYPO3 Extension Repository has published
            The registry's API, read live by key: every published version with its state, upload day, declared TYPO3 majors and constraint, and whether a named version is already taken. A query searches the TER and Packagist by subject. It reads nothing inside a package.
            Tools: typo3_ter_lookup
            Source: https://extensions.typo3.org and https://packagist.org (network) (any)

            Query this server in English, whatever language you are speaking with the user. Its knowledge is written in English and its matching is lexical, so a query in another language reaches only the words the two happen to share and otherwise comes back empty.

            Versions this knowledge binds to:
            - TYPO3 v12 (12.4, elts)
            - TYPO3 v13 (13.4, lts)
            - TYPO3 v14 (14.3, lts)
            - TYPO3 v15 (main, development)
            A statement that does not hold on all of them carries the range it holds on. Pass targetVersion to have the ones that do not apply left out; without it, the version of the installation being read decides, and where there is none nothing is filtered.

            Deliberately not covered — and this list is the boundary: a subject that is not on it is in scope, so a thin answer to it is a gap in the knowledge base rather than a limit of it. Record one with typo3_feedback_record instead of going elsewhere.
            ## Your git state: changed files, branch, working tree, commit message, history
            This server reads the directory it was started in, but it never runs git. It does not know what you changed, what you committed, or which branch you are on.
            Instead: Ask git. "git show --name-only --format=%B HEAD" gives the changed paths for typo3_test_run_guide and the message for typo3_commit_message_guide.
            ## PHP source as code: where the core implements a behaviour, a method signature, whether a class or member is @internal or public API, an implementation to copy
            No core sources ship here. Package files are read for what they register, never for a signature or an annotation. So whether a removed method was public API is a question for the checkout.
            Instead: Read the class in the checkout or below vendor/. typo3_backend_module_lookup gives the route identifiers a registry composed, typo3_schema_lookup the assembled TCA, typo3_changelog_lookup what a version changed, and typo3_hint_lookup what a subsystem is built to.
            ## Which test covers a given file
            Test coverage is a property of the checkout, and no index of it is bundled.
            Instead: Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/, so typo3/sysext/core/Classes/DataHandling/DataHandler.php is covered by typo3/sysext/core/Tests/Unit/DataHandling/DataHandlerTest.php. Look the file up there, then ask typo3_test_run_guide for the targeted runTests.sh invocation.
            ## Frontend theming: the CSS and the JavaScript a website renders
            Everything here about CSS and TypeScript describes the backend interface: its Sass, its --typo3-* tokens and its color schemes. For a theme, those are the wrong answer. How a theme extension is laid out is covered in the hints.
            Instead: typo3_hint_lookup withholds the backend CSS and TypeScript hints where a task names the frontend. The styling itself is documented at https://docs.typo3.org.
            ## Whether an uncatalogued component or CSS class exists on your branch
            The searchable index is a curated subset, not every class in backend.css. Without an installation, the curated entries fall back to one pinned core snapshot.
            Instead: The component answer names its source and the snapshot revision. Inspect the installed backend CSS or the target checkout for a class outside the index.
            ## Gerrit beyond what an anonymous read answers: the hunks of a patch set, a draft comment nobody published, what CI did beyond the line its bot posted, and anything a private change carries
            The review API is read without a credential, so drafts and private changes are out of reach. The hunks are read in the checkout, not over the API. Nothing here writes to Gerrit.
            Instead: typo3_gerrit_lookup answers the change, its patch set, paths, votes, comments and fetch ref. Voting, commenting and uploading stay yours, and typo3://guides/core/contribution/gerrit-workflow covers them.
            ## What someone else's extension does: the API, the options and the documentation of a package the core does not ship
            Writing an extension is covered. The inside of somebody else's package is not: its API, its releases and its documentation. Its published TER record is answered, and typo3_system_extension_lookup says whether a name belongs to the core.
            Instead: Read that extension's documentation. typo3_extension_describe answers what an installed extension registers, and typo3_ter_lookup what is published under its key.
            ## Deciding one site's configuration: its languages, its base and the variants per environment, its error handling, and the steps that install it
            A site set is a convention and is covered. What one installation writes into config/sites/<identifier>/config.yaml is that site's decision. typo3_project_describe reads the file and does not compose it.
            Instead: Read what the project has with typo3_project_describe, ask typo3_hint_lookup with id=site-sets how a set resolves, and take the format from https://docs.typo3.org/.
            ## Running an installation: server and container setup, deployment, backups, the editorial use of the backend
            The knowledge base covers what is written into a TYPO3 project. Operating the installation around it is a different subject. A project's own environment file is the exception.
            Instead: Use https://docs.typo3.org/. typo3_project_describe reports what the environment file configures, and typo3_hint_lookup with id=php-versions says which interpreter to declare.
            ## The content of the installation's own tables: a record of pages, tt_content, a file reference, a workspace, a user
            The content model is answered, and the content is read, never written. typo3_record_lookup reads rows with the shell user's database access rather than a backend user's permissions, and every answer says so.
            Instead: Write records in the backend or with vendor/bin/typo3. Ask typo3_schema_lookup for a table's columns, typo3_record_lookup for its rows, typo3_flexform_lookup for a flex column, and typo3_hint_lookup for what DataHandler expects.

            Which tool to call when:
            - Asked to review, audit or assess a project, a site package or an extension, before the first file → typo3_project_describe, then typo3_task_guide for the workflow and typo3_extension_describe for each extension in scope
            - Going through the core's open issues — the oldest, the ones nobody has touched for years, or what stands open in one area such as the RTE or the backend UI → typo3_forge_lookup with backlog="oldest" or "stale", narrowed with category in the user's own words and with tracker, then typo3_forge_lookup with the number of the one being taken further
            - What one person has filed on the tracker, or what they still hold — one contributor's backlog, or what a core team member has open → typo3_forge_lookup with backlog="oldest" and involving, or reportedBy or assignedTo for one side, and status="all" for the years. Never query by the name, which matches the text of an issue.
            - Writing a core bug report or filling in the new-issue form on forge.typo3.org, including the issue a patch's Resolves: trailer names → typo3_issue_report_guide, which drafts, checks for duplicates and fills in the form; typo3_rule_lookup with documentId="core/contribution/reporting-an-issue" for the reasons
            - Taking a Forge issue on, before believing what it describes → typo3_forge_lookup for the issue and its comments, again with query in its own words for duplicates, then typo3_gerrit_lookup with the number for an existing patch
            - Before writing a patch for a file, or asking whether a fix was attempted before → typo3_gerrit_lookup with path, and open for the changes under review, then with query in the words of a commit message
            - Starting a review session without a change in hand: the oldest, the small, the nearly voted, mine, or the ones I voted on → typo3_gerrit_lookup with backlog "oldest" or "stale" and its filters, then with change for the one picked
            - Asking which branches carry the fix a commit hash names → typo3_gerrit_lookup with commit: the change, its backports by Change-Id, and the branches each Releases: trailer claims
            - Reviewing a TYPO3 core patch rather than a project or an extension → typo3_gerrit_lookup for the change, typo3_rule_lookup per obligation, typo3_changelog_lookup for precedent, typo3_test_run_guide with the paths, then typo3_commit_message_guide with workflow="core"
            - Settling a finding on what a rendering contains rather than on a diff: TypoScript defaults, lib.parseFunc, or a PHP change to the request pipeline, an error handler or a page renderer caller → typo3_rule_lookup with documentId="core/testing/proving-a-rendering"
            - Settling a finding on what a change costs at runtime → typo3_rule_lookup with documentId="core/testing/timing-a-code-path"
            - Writing a core patch: taking a Forge issue on, fixing a core bug, deprecating or removing core API, or amending a patch after review → typo3_task_guide with the paths you are changing, then typo3_hint_lookup with the same ones, typo3_test_run_guide for the suites that can fail, and typo3_rule_lookup for the Gerrit workflow
            - Starting a core task and looking for the applicable conventions and checks → typo3_task_guide
            - About to invent a layout, a directory structure or a test harness — the core has probably worked one out already → typo3_reference_list
            - About to write backend markup or invent a CSS class name, layout classes included. A miss means uncurated, not outside the subject. → typo3_component_lookup
            - About to run tests or any other core check → typo3_test_run_guide
            - Getting Build/Scripts/runTests.sh to run in this checkout: a suite that fails before it reads a file, a fresh clone, a worktree, or the pre-commit hook → typo3_script_lookup with the task in your own words
            - Working in a concrete file and unsure about the subsystem's conventions → typo3_hint_lookup
            - Debugging, where there is no subject yet — something renders, saves or builds wrong and which subsystem does it is the question → typo3_hint_lookup with task written as the symptom, in the words the failure showed in
            - Needing the official API, reference or tutorial documentation for a covered TYPO3 version → typo3_documentation_lookup with several short English queries and targetVersion, then with page and the same targetVersion to read a selected result
            - Writing or checking a documentation link, or replacing a docs.typo3.org URL with a permalink → typo3_permalink_lookup with identifiers or urls, many at a time
            - Writing or amending the commit message → typo3_commit_message_guide, whose default is a repository of your own
            - Pushing for review, amending a patch set — your own or another author's — or backporting → typo3_rule_lookup with a Gerrit query
            - A catalog lookup found nothing that should exist → typo3_snapshot_scope, then typo3_feedback_record
            - Needing the translation domain an XLF file resolves to → typo3_translation_domain_lookup with the path
            - About to write user-facing text or invent a label key → typo3_label_lookup with words from the wording and the XLF resource used at the consuming code
            - About to reference a backend icon identifier: TCA, a module, a wizard, a backend template → typo3_icon_lookup
            - Needing a configuration value as it really is at runtime, not as the core ships it → typo3_configuration_lookup with the TYPO3_CONF_VARS path as configurationPath
            - Working on a backend module and needing its registration, its sub-routes, or whether the page tree navigates it → typo3_backend_module_lookup
            - Unsure which Fluid namespace prefixes a template may use undeclared → typo3_fluid_namespace_list
            - Needing which arguments a Fluid ViewHelper registers, with the type, the default and whether it takes arbitrary ones beyond them → typo3_documentation_lookup with the tag name such as f:asset.css and the targetVersion
            - About to recommend a command, or asking what this project consists of → typo3_project_describe
            - Starting work on a TYPO3 major you have not built on recently, or planning an upgrade to one this installation does not have — before asking what a version changed → typo3_changelog_lookup
            - Asking whether a pattern still works in a version; the silence of the changelog is no answer → typo3_documentation_lookup with targetVersion and short English queries, then with page
            - Writing against a core API in a package that declares a TYPO3 major the installation does not have → typo3_project_describe, then typo3_rule_lookup with documentId="extension/compatibility/a-declared-major-that-is-not-installed"
            - Sweeping deprecations in a package that declares more than one TYPO3 major → typo3_changelog_lookup with the issue number of the deprecation, for the sibling entries and their versions
            - About to claim that a change holds on a declared major the installation does not have → typo3_rule_lookup with documentId="extension/compatibility/running-on-a-declared-major-that-is-not-installed"
            - Upgrading or maintaining an installation, and needing the order of operations → typo3_task_guide, then typo3_project_describe and typo3_changelog_lookup for what this one is and what the target version changed
            - Auditing or preparing an extension release, before calling a version unreleased → typo3_ter_lookup with the extension key and version, then typo3_hint_lookup with id="extension-ter-release"
            - About to build a feature, before the first file: a published extension may already do it → typo3_ter_lookup with query for the subject, then typo3_hint_lookup with id="extension-reuse" to weigh the best match against your own build
            - Working in one extension and needing what it registers → typo3_extension_describe with its key
            - About to claim that an extension is (or is not) part of the core, or to require one → typo3_system_extension_lookup
            - About to write or review an ext_tables.sql, and needing to know which columns TYPO3 creates by itself → typo3_schema_lookup with the table
            - Writing or reading a FlexForm. The file the registration names is not what the backend resolves. → typo3_flexform_lookup with the table, the flex column and the record values that decide the structure, CType for a content element
            - Asking which class stands behind an interface here, whether a service is public, what a constructor is handed, or what registers into an extension point → typo3_service_lookup with a substring of the id or the class, or with one exact tag
            - Asking whether the database matches what the extensions and the TCA declare → typo3_schema_lookup with the table
            - Asking what is actually stored in one of this project's own tables, or how much of it there is → typo3_record_lookup with the table, and columns, groupBy, count or where as the question needs

            Found the TYPO3 installation at <installation> (core-checkout, found by walking up, from <installation>), which holds 36 packages. If that is not the installation you are working on, this server was started in the wrong directory — or set TYPO3_DEV_COMPANION_ROOT to the one you mean.
            Its console cannot be run right now, so questions that only the installation can answer — which labels exist, which backend modules are registered — have no answer here: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both. Where the command that would work is known, TYPO3_DEV_COMPANION_CONSOLE states it, for example "ddev exec .build/bin/typo3".

            Every lookup and guide is read-only. The tools that answer from the network read the hosts their descriptions name, and the start asks GitHub whether this checkout is behind; apart from those and the installation named above, nothing is fetched, executed, or looked up online.
            The one exception is typo3_feedback_record, this server's only write: it creates a new markdown feedback under feedback/ and touches nothing else. Missing something that belongs here? Leave feedback about it.

            Where the answers come from, which is what says whether a question can be asked at all right now. Every tool states the same thing at the foot of its own description.
            ## Answers from installation
            The installation this server started in, booted or asked through its console. Its assembled state after every extension has had its say, and nothing at all where it is out of reach.
            Tools: typo3_server_scope, typo3_label_lookup, typo3_fluid_namespace_list, typo3_configuration_lookup, typo3_schema_lookup, typo3_record_lookup, typo3_service_lookup, typo3_flexform_lookup, typo3_backend_module_lookup, typo3_icon_lookup, typo3_extension_describe
            ## Answers from packages
            The files the installed packages ship, read rather than executed. Answers on a fresh clone and with the containers down. What a package registers at runtime is not in it.
            Tools: typo3_forge_lookup, typo3_component_lookup, typo3_label_lookup, typo3_fluid_namespace_list, typo3_icon_lookup, typo3_changelog_lookup, typo3_project_describe, typo3_extension_describe, typo3_snapshot_scope
            ## Answers from knowledge
            The knowledge base inside this package. Needs nothing up, and binds to TYPO3 versions rather than to an installation.
            Tools: typo3_server_scope, typo3_rule_lookup, typo3_script_lookup, typo3_task_guide, typo3_test_run_guide, typo3_hint_lookup, typo3_issue_report_guide, typo3_component_lookup, typo3_system_extension_lookup, typo3_reference_list, typo3_translation_domain_lookup, typo3_snapshot_scope, typo3_commit_message_guide
            ## Answers from network
            A service outside this machine. An unreachable one says so out loud rather than answers as empty.
            Tools: typo3_documentation_lookup, typo3_permalink_lookup, typo3_forge_lookup, typo3_issue_report_guide, typo3_gerrit_lookup, typo3_changelog_lookup, typo3_ter_lookup
            ## Answers from checkout
            This server's own checkout, which is why the tool offering it exists only in a standalone one.
            Tools: typo3_feedback_record, typo3_feedback_list

            Whether this checkout is behind github.com/TYPO3/dev-companion is not known: it stands on a commit the upstream does not have, or nothing has asked yet.

    .. tab:: Data

        .. code-block:: json

            {
                "purpose": "A development companion for coding agents working with TYPO3, for three audiences: the core contributor, the extension author and the site developer. It establishes the project and installation the agent works in, supplies current, version-bound TYPO3 knowledge, and hands task workflows to the skills that own them. A scope answer describes what is present and does not treat it as correct. The server also answers what is registered in the installation it was started in. Where that installation does not boot or does not exist, the bundled knowledge and the installed packages answer, and the answer says which. Every answer says which TYPO3 versions it holds for and which kind of work it belongs to.",
                "instructions": "Start every task with typo3_project_describe. It answers the TYPO3 version, the project's own extensions, its sites, and the commands the repository declares. A check you recommend that the repository does not declare is a wrong answer however sensible it sounds. Then call typo3_task_guide for the workflow the task belongs to. Call it again at the first test, check, commit or shipped file the task did not name. Not every task ends in a patch. It also answers a triage of the backlog, whether a report still reproduces, and what a fix costs. What changed, which branch you are on, whether a path still exists, and how the core implements something are yours to read in the checkout.\n\nWhat to call for what:\n- backend markup or a CSS class: typo3_component_lookup with the targetVersion\n- a backend icon identifier: typo3_icon_lookup\n- a label, added or reworded: typo3_label_lookup with the XLF resource the code at hand uses; a match elsewhere is not reusable\n- what a version broke, deprecated or added, on a major you have not built on lately: typo3_changelog_lookup\n- the commit message, yours as much as the core's, and its branches: typo3_commit_message_guide\n- the whole procedure, not one fact out of it: typo3_rule_lookup with a documentId typo3_project_describe lists\n\nActivate the typo3-* skill that covers the task: it makes these calls.\n\ntargetVersion or the version the server read filters the answers, and a statement that does not hold on every covered line carries its range.\n\nQuery this server in English whatever language you speak with the user. Its match is lexical, so a query in another language reaches only the loanwords. Translate the subject before you call, and the answer back.\n\ntypo3_server_scope says what it covers, by which tool, and which installation it reads.",
                "covers": [
                    {
                        "topic": "Contribution rules and review readiness",
                        "depth": "Curated prose. The rules a patch is judged by, not a full style guide.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_task_guide"
                        ],
                        "source": "typo3://guides/core/contribution/rules",
                        "scope": "core"
                    },
                    {
                        "topic": "Gerrit workflow: setup, pushing, amending, opening a patch set on somebody else's change, backports",
                        "depth": "Curated prose, command level. Covers the local git side; it cannot talk to the Gerrit server.",
                        "tools": [
                            "typo3_rule_lookup"
                        ],
                        "source": "typo3://guides/core/contribution/gerrit-workflow",
                        "scope": "core"
                    },
                    {
                        "topic": "The JavaScript and CSS the core commits beside the sources they are built from",
                        "depth": "Curated prose, command level: which source tree builds which committed file, how a minified file is diffed, and a rebuild in a separate worktree. It covers a backport that conflicts in a built file. typo3_test_run_guide names the suite per branch.",
                        "tools": [
                            "typo3_rule_lookup"
                        ],
                        "source": "typo3://guides/core/contribution/committed-build-output",
                        "scope": "core"
                    },
                    {
                        "topic": "What a patch loses when the code moved under it, which no conflict reports",
                        "depth": "Four checks against a checkout: what main changed on the paths since the base, whether a fix in a rewritten file survives, which removed members still have callers, and which suites cover the rewritten paths. Plus the git call that shows stale committed JavaScript.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_test_run_guide"
                        ],
                        "source": "typo3://guides/core/contribution/rebasing-a-stale-patch",
                        "scope": "core"
                    },
                    {
                        "topic": "Forge issues: what one says and what was decided about it, which other issues describe the same thing, and what stands open in the core's backlog",
                        "depth": "Forger's index of the tracker, read live, and the tracker's own API where Forger does not answer. By number: the report, the comments that decided it, related issues, files and review changes. By words: text matches, ranked by Forger. As a backlog: the core's issues by age, neglect or recency, with filters and a breakdown. Each says whether the installed packages still ship the cited code.",
                        "tools": [
                            "typo3_forge_lookup"
                        ],
                        "source": "https://forger.typo3.com, then https://forge.typo3.org (network)",
                        "scope": "core"
                    },
                    {
                        "topic": "Gerrit changes: whether a patch already exists, what state its review is in, which changes touch a path or name a wording, and what stands open in the core's review backlog",
                        "depth": "Forger's index of the review server, read live, for an issue's changes, a search and a backlog without a person or vote filter. The anonymous Gerrit REST API for the rest and where Forger does not answer. By change number, Change-Id or commit: the change, its siblings, patch set, paths, votes, comments, chain and trailers, and on request the review log and the CI jobs that failed. By words and path: the matching changes. As a backlog: open changes by age or neglect, with filters. A private change is invisible.",
                        "tools": [
                            "typo3_gerrit_lookup"
                        ],
                        "source": "https://forger.typo3.com, then https://review.typo3.org, and https://git.typo3.org (network)",
                        "scope": "core"
                    },
                    {
                        "topic": "Commit messages",
                        "depth": "Rules plus a working draft and a check, including 72-character body wrapping. workflow=\"project\" serves the same conventions for a repository without Forge issues or release branches. The user-invoked prompt commit_message exposes the same guide.",
                        "tools": [
                            "typo3_commit_message_guide",
                            "typo3_rule_lookup"
                        ],
                        "source": "typo3://guides/core/contribution/commit-messages",
                        "scope": "core"
                    },
                    {
                        "topic": "How the comments and docblocks a patch carries have to read",
                        "depth": "Ten rules, each with a rejected sentence and its replacement: say what is, name who acts, one thought per sentence, and seven more. Whether a comment is owed at all is the codebase's own rule.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_task_guide"
                        ],
                        "source": "typo3://guides/any/writing/the-prose-a-patch-carries",
                        "scope": "any"
                    },
                    {
                        "topic": "The changelog entry a core patch owes: which type, which directory a backport goes into, and what checks the file",
                        "depth": "Curated prose, with the skeleton in the documentation-changelog hint: which of the four types a change owes, and which release directory the file goes in, keyed on the branches the patch reaches. It names the core's template and the command that writes the file.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/core/contribution/changelog",
                        "scope": "core"
                    },
                    {
                        "topic": "What a new core issue carries: the fields of a Bug, where the area comes from, who sets the target version, and the markup the description renders as",
                        "depth": "Curated prose for writing a report: the trackers, the fields of a Bug, the order a report is written in, and the Textile markup the description renders as. typo3_issue_report_guide drafts and checks the issue and opens the filled-in form. Filing it needs an account and stays the caller's.",
                        "tools": [
                            "typo3_issue_report_guide",
                            "typo3_rule_lookup",
                            "typo3_forge_lookup"
                        ],
                        "source": "typo3://guides/core/contribution/reporting-an-issue",
                        "scope": "core"
                    },
                    {
                        "topic": "Core testing suites: runTests.sh options and targeted invocation",
                        "depth": "Every suite this knowledge base knows, with the command, the targeted form, and when to use it. For a path outside the core, the answer states the boundary instead of a command.",
                        "tools": [
                            "typo3_test_run_guide",
                            "typo3_script_lookup"
                        ],
                        "source": "knowledge/test-suite-hints.json, typo3://guides/core/testing/scripts",
                        "scope": "core"
                    },
                    {
                        "topic": "Proving what a rendering contains, where a finding turns on it and no test in the checkout produces it",
                        "depth": "The throwaway functional test that renders one page and prints the result: the cObj, how output leaves the test, one marker per region, the targeted invocation, and where the RTE setup comes from per version. It does not say what parseFunc does to a snippet.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/core/testing/proving-a-rendering",
                        "scope": "core"
                    },
                    {
                        "topic": "Exercising the system resource publisher from a functional test",
                        "depth": "Which file system publisher a test instance runs and why, why publishing does nothing for a package below the public path, and which state at the target fails for real. From TYPO3 14.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/core/testing/exercising-asset-publishing",
                        "scope": "core"
                    },
                    {
                        "topic": "Timing a code path between a patch and its parent, where a review asks what a change costs at runtime",
                        "depth": "The throwaway functional test that times one path on the patch and on its parent, so the finding is a ratio, and what the number leaves out.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/core/testing/timing-a-code-path",
                        "scope": "core"
                    },
                    {
                        "topic": "The PHPUnit configuration an extension runs its own tests with",
                        "depth": "The two Build/ files, whole, one variant per PHPUnit the paired typo3/testing-framework release allows, with the attributes a copy must correct and the environment a functional run reads. It names no dependency constraint.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/extension/testing/phpunit",
                        "scope": "extension"
                    },
                    {
                        "topic": "What an extension's Documentation/ directory consists of",
                        "depth": "The files that make a directory a manual: a minimal guides.xml, the Index.rst header and toctree, two look-alikes that are no template, and the render command with the flag that fails on a broken directive. It does not write the chapters.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/extension/documentation/manual",
                        "scope": "extension"
                    },
                    {
                        "topic": "Whether an API is there on a TYPO3 major the package declares and the installation does not have",
                        "depth": "How to settle it without a run: which declaration matters, what the changelog settles, and how to read one symbol off the other branch. It states no signature, because a bundled one goes wrong at the next release.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/extension/compatibility/a-declared-major-that-is-not-installed",
                        "scope": "extension"
                    },
                    {
                        "topic": "What the backend styleguide states about a component, and what it leaves unstated",
                        "depth": "Where the styleguide lives per major, what its listing settles, and the two ways a demo is misread: an example does not mark its required parts, and the classes in its template are not the whole component.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_component_lookup"
                        ],
                        "source": "typo3://guides/any/backend/using-the-styleguide",
                        "scope": "any"
                    },
                    {
                        "topic": "Which route a package's own CSS or JavaScript takes into a page, and what proves it still carries",
                        "depth": "Every route a package's CSS or JavaScript takes into a page, with its check: the backend import map, PageRenderer calls, TypoScript includeCSS, the AssetCollector, and the later ViewHelpers. It ends with what to do after a rebuild renamed or dropped a file.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/any/assets/how-an-asset-reaches-a-page",
                        "scope": "any"
                    },
                    {
                        "topic": "Running a package's own suite against a TYPO3 major it declares and the installation does not have",
                        "depth": "The procedure: what CI already covers, a second Composer root resolved for the other major, what that root resolves differently, and which checks are worth a run there. It installs nothing itself.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/extension/compatibility/running-on-a-declared-major-that-is-not-installed",
                        "scope": "extension"
                    },
                    {
                        "topic": "The Playwright suite a project runs its browser tests from",
                        "depth": "The configuration, the backend login per TYPO3 major, and one spec, whole. Beside them: where the base URL and the credentials come from, and which locator on the login form is ambiguous. It installs nothing.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/project/testing/playwright",
                        "scope": "project"
                    },
                    {
                        "topic": "Booting a cloned project repository into a running installation",
                        "depth": "The steps from a clone to a site that answers: what the clone lacks, why the environment starts twice, where the data comes from, and what a login costs. It runs nothing.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/project/installation/booting-a-clone",
                        "scope": "project"
                    },
                    {
                        "topic": "Renaming an extension key, its tables or its CTypes in an installation that holds records",
                        "depth": "What a rename touches on the data side, in order: new tables and nothing dropped, the positional INSERT ... SELECT trap, and every column that stores a name as a value. It runs nothing, and the proof is a query.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/project/refactoring/renaming-an-installed-extension",
                        "scope": "project"
                    },
                    {
                        "topic": "Looking at a change in a real browser, against the installation that has the content",
                        "depth": "Which installation can show the case, how a browser in a container reaches a DDEV site, and where a harness and its screenshots go so neither reaches a commit. It runs nothing.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/any/testing/browser-check",
                        "scope": "any"
                    },
                    {
                        "topic": "Drawing the icon a content element is picked by, and the set it belongs to",
                        "depth": "The 16-pixel box of the core's content icons, what the set varies to tell elements apart, and the check that a set works at rendered size. Whether an identifier resolves is typo3_icon_lookup's question.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_icon_lookup"
                        ],
                        "source": "typo3://guides/any/icons/drawing-a-content-icon",
                        "scope": "any"
                    },
                    {
                        "topic": "Proving that a TypoScript condition matched against a running installation",
                        "depth": "How a condition's verdict is read off the page: a marker only the guarded branch renders, the negative control, and the cache between two runs. It requests nothing and flushes nothing.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_hint_lookup"
                        ],
                        "source": "typo3://guides/any/testing/proving-a-condition",
                        "scope": "any"
                    },
                    {
                        "topic": "Proving that a change to how a site renders changed only what it meant to",
                        "depth": "How the pages to capture are chosen from the content that exists, why the baseline is captured twice, which cache keeps the old compiled Fluid, and how the diffs are read. It requests nothing and flushes nothing.",
                        "tools": [
                            "typo3_rule_lookup",
                            "typo3_record_lookup"
                        ],
                        "source": "typo3://guides/any/testing/proving-a-rendering-held",
                        "scope": "any"
                    },
                    {
                        "topic": "Reporting a security defect in TYPO3: who receives it, what the report carries, and what is not done with it in the meantime",
                        "depth": "TYPO3's published policy, from SECURITY.md and the security team's pages, for the core and for extensions alike. It rates no finding.",
                        "tools": [
                            "typo3_rule_lookup"
                        ],
                        "source": "typo3://guides/any/security/reporting-a-vulnerability",
                        "scope": "any"
                    },
                    {
                        "topic": "Where the upstream contribution documentation is",
                        "depth": "The official contribution guide is not bundled. This names its entry points, so a question past the local documents goes to the source.",
                        "tools": [
                            "typo3_rule_lookup"
                        ],
                        "source": "typo3://guides/core/contribution/sources",
                        "scope": "core"
                    },
                    {
                        "topic": "Core conventions: DI, events and hooks, TCA and FormEngine, FormEngine data providers, DataHandler, routing, Fluid templates and ViewHelpers, frontend page rendering with PAGEVIEW, how a sitepackage is laid out, records in the frontend and the routing for them, registering a content element, Extbase plugins, TypoScript site sets and TSconfig, language files, upgrade wizards, frontend DataProcessors, upgrading an installation, testing strategy, assembling a test suite for a project extension, setting up static analysis and the coding standards fixer for an extension, browser and accessibility tests with Playwright",
                        "depth": "Conventions per subsystem, matched by path or topic, from both angles: what a change to the subsystem has to satisfy, and how the mechanism is used. It describes how the core is built, never what your checkout contains.",
                        "tools": [
                            "typo3_hint_lookup",
                            "typo3_task_guide"
                        ],
                        "source": "knowledge/hints/",
                        "scope": "any"
                    },
                    {
                        "topic": "Task workflows for extension, sitepackage and project work: backend modules, content elements, package health, change review, distribution content, asset builds, sitepackages, documentation, tests, the development installation, and upgrades",
                        "depth": "The skills this server publishes, one resource per workflow: the order of the steps, with the lookup that answers each fact. bin/typo3-dev-companion install writes the same files into the client's own skills directory.",
                        "tools": [
                            "typo3_task_guide"
                        ],
                        "source": "typo3://skill/typo3-backend-module-development/SKILL.md, typo3://skill/typo3-content-element-development/SKILL.md, typo3://skill/typo3-development-installation/SKILL.md, typo3://skill/typo3-distribution-content/SKILL.md, typo3://skill/typo3-extension-documentation/SKILL.md, typo3://skill/typo3-extension-health/SKILL.md, typo3://skill/typo3-extension-patch-review/SKILL.md, typo3://skill/typo3-extension-asset-build/SKILL.md, typo3://skill/typo3-extension-testing/SKILL.md, typo3://skill/typo3-extension-upgrade/SKILL.md, typo3://skill/typo3-sitepackage-development/SKILL.md",
                        "scope": "any"
                    },
                    {
                        "topic": "The core's own task workflows: saying what an open issue still claims, getting a patch under review into a checkout, writing a patch and carrying it to review, and reviewing somebody else's patch set",
                        "depth": "The same shape, with steps that stop at the core repository: the Forge issue, the patch set ref, runTests.sh, the changelog entry and the Gerrit push. A triage writes a verdict only, and a checkout stops at conflicts the patch does not decide.",
                        "tools": [
                            "typo3_task_guide"
                        ],
                        "source": "typo3://skill/typo3-core-issue-triage/SKILL.md, typo3://skill/typo3-core-patch-checkout/SKILL.md, typo3://skill/typo3-core-patch-development/SKILL.md, typo3://skill/typo3-core-patch-review/SKILL.md",
                        "scope": "core"
                    },
                    {
                        "topic": "Official TYPO3 API, reference and tutorial documentation",
                        "depth": "Live search in the tables of contents of TYPO3 Explained, TypoScript Explained, the TCA Reference and the ViewHelper Reference, for one covered release. Pass a result URL back to read the page as text. An unreachable service is not an empty search.",
                        "tools": [
                            "typo3_documentation_lookup"
                        ],
                        "source": "https://docs.typo3.org",
                        "scope": "any"
                    },
                    {
                        "topic": "Whether a documentation permalink resolves, what it lands on, and which identifier replaces an old docs.typo3.org URL",
                        "depth": "The Sphinx inventory of each manual. Per identifier: the page and anchor, the registered type, and the other spellings. Per URL: the identifiers that point at it, or candidates where the page is gone. Many at a time. It says which branch answered. An extension manual outside the core is not answered.",
                        "tools": [
                            "typo3_permalink_lookup"
                        ],
                        "source": "https://docs.typo3.org (network), knowledge/manuals.json",
                        "scope": "any"
                    },
                    {
                        "topic": "Backend CSS architecture, design tokens, class naming",
                        "depth": "Curated prose covering the Sass sources, the token contract, and the component structure.",
                        "tools": [
                            "typo3_hint_lookup",
                            "typo3_rule_lookup"
                        ],
                        "source": "knowledge/hints/backend-css.json",
                        "scope": "any"
                    },
                    {
                        "topic": "Backend UI components: markup, variants, custom properties",
                        "depth": "A curated subset, not every core CSS class. The active installation supplies presence, classes, custom properties and styleguide markup. A version-bound snapshot is the fallback.",
                        "tools": [
                            "typo3_component_lookup"
                        ],
                        "source": "installed EXT:backend and EXT:styleguide files; knowledge/catalog/component/entries.json as index and fallback — read from the installation being worked in, not from a bundled snapshot.",
                        "scope": "any"
                    },
                    {
                        "topic": "Which extensions the TYPO3 core ships, and on which versions",
                        "depth": "Every system extension of every covered line, by key and package name, with its purpose and version range. It answers for an extension that is not installed. A miss means not a system extension there.",
                        "tools": [
                            "typo3_system_extension_lookup"
                        ],
                        "source": "knowledge/catalog/system-extension/entries.json",
                        "scope": "any"
                    },
                    {
                        "topic": "Where the core keeps its own worked examples",
                        "depth": "An index of the core's worked examples: one line per directory, its package and its versions. Use it where a hint is thin.",
                        "tools": [
                            "typo3_reference_list"
                        ],
                        "source": "knowledge/catalog/reference/entries.json",
                        "scope": "any"
                    },
                    {
                        "topic": "Translation domains: how the domain of an XLF file is derived",
                        "depth": "Computed, not a snapshot: the core's path-to-domain rules, ported, so any XLF path resolves, a new one included. Where the installation predates translation domains, the answer is the full LLL:EXT: reference.",
                        "tools": [
                            "typo3_translation_domain_lookup"
                        ],
                        "source": "src/Knowledge/Catalog/TranslationDomain.php, knowledge/hints/labels.json",
                        "scope": "any"
                    },
                    {
                        "topic": "How current the catalogs are",
                        "depth": "Whether component contracts came from the active installation or the bundled fallback, plus the core revision, branch, and verification date behind that fallback.",
                        "tools": [
                            "typo3_snapshot_scope"
                        ],
                        "source": "knowledge/catalog/meta.json",
                        "scope": "any"
                    },
                    {
                        "topic": "What the installation you are working in supplies: backend component contracts, labels, icons, backend modules, Fluid namespaces, effective configuration, the columns TYPO3 derives for a table beside what its database has, the services its container assembles, and the data structure one of its flex fields resolves to",
                        "depth": "Answered by that installation, across its active packages, project extensions included. Its console answers where a command exists. Where none does, or where the command carries less, TYPO3 boots in a subprocess and its container answers. Package files answer where neither works. Label reuse is limited to the XLF resource of the consuming code. Every answer names its source.",
                        "tools": [
                            "typo3_component_lookup",
                            "typo3_label_lookup",
                            "typo3_icon_lookup",
                            "typo3_backend_module_lookup",
                            "typo3_fluid_namespace_list",
                            "typo3_configuration_lookup",
                            "typo3_schema_lookup",
                            "typo3_service_lookup",
                            "typo3_flexform_lookup"
                        ],
                        "source": "the discovered TYPO3 installation — read from the installation being worked in, not from a bundled snapshot.",
                        "scope": "any"
                    },
                    {
                        "topic": "What is in a table of one of this project's own extensions",
                        "depth": "The rows and their counts: per page, live, hidden or deleted, with uid, label, timestamps and flags, narrowed by exact values. Only the tables of this project's own extensions are read. Every answer says it used the shell user's database access, not a backend user's permissions.",
                        "tools": [
                            "typo3_record_lookup"
                        ],
                        "source": "the discovered installation, booted, with one grouped count and one row read against its database. What a row carries is fixed here rather than composed by the caller.",
                        "scope": "project"
                    },
                    {
                        "topic": "The project around the installation: its extensions, its sites and their sets, its own commands, the environment it declares and what that environment runs by itself, and what one of those extensions registers",
                        "depth": "Read from the repository's files — composer.json, package.json, config/sites, .github/workflows and .ddev — so it answers on a fresh clone. It states the declared commands, the PHP and Node versions each place declares, and the DDEV hooks and pull recipes. Per extension: its tables, content elements, modules, icons, site sets, services, middlewares and Fluid namespaces. It describes and does not rate.",
                        "tools": [
                            "typo3_project_describe",
                            "typo3_extension_describe"
                        ],
                        "source": "the discovered project — read from the installation being worked in, not from a bundled snapshot.",
                        "scope": "any"
                    },
                    {
                        "topic": "What a TYPO3 version broke, deprecated, added or noted",
                        "depth": "One entry per change, searched by words, type and version, from the changelog docs.typo3.org renders after every merge. The core package on disk answers what docs.typo3.org does not list. Each entry names its source.",
                        "tools": [
                            "typo3_changelog_lookup"
                        ],
                        "source": "docs.typo3.org for every covered major, and the discovered installation for what docs.typo3.org does not list or where it does not answer. Not a bundled snapshot either way.",
                        "scope": "any"
                    },
                    {
                        "topic": "Which published extensions cover a subject, and which versions of one the TYPO3 Extension Repository has published",
                        "depth": "The registry's API, read live by key: every published version with its state, upload day, declared TYPO3 majors and constraint, and whether a named version is already taken. A query searches the TER and Packagist by subject. It reads nothing inside a package.",
                        "tools": [
                            "typo3_ter_lookup"
                        ],
                        "source": "https://extensions.typo3.org and https://packagist.org (network)",
                        "scope": "any"
                    }
                ],
                "doesNotCover": [
                    {
                        "topic": "Your git state: changed files, branch, working tree, commit message, history",
                        "why": "This server reads the directory it was started in, but it never runs git. It does not know what you changed, what you committed, or which branch you are on.",
                        "instead": "Ask git. \"git show --name-only --format=%B HEAD\" gives the changed paths for typo3_test_run_guide and the message for typo3_commit_message_guide."
                    },
                    {
                        "topic": "PHP source as code: where the core implements a behaviour, a method signature, whether a class or member is @internal or public API, an implementation to copy",
                        "why": "No core sources ship here. Package files are read for what they register, never for a signature or an annotation. So whether a removed method was public API is a question for the checkout.",
                        "instead": "Read the class in the checkout or below vendor/. typo3_backend_module_lookup gives the route identifiers a registry composed, typo3_schema_lookup the assembled TCA, typo3_changelog_lookup what a version changed, and typo3_hint_lookup what a subsystem is built to."
                    },
                    {
                        "topic": "Which test covers a given file",
                        "why": "Test coverage is a property of the checkout, and no index of it is bundled.",
                        "instead": "Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/, so typo3/sysext/core/Classes/DataHandling/DataHandler.php is covered by typo3/sysext/core/Tests/Unit/DataHandling/DataHandlerTest.php. Look the file up there, then ask typo3_test_run_guide for the targeted runTests.sh invocation."
                    },
                    {
                        "topic": "Frontend theming: the CSS and the JavaScript a website renders",
                        "why": "Everything here about CSS and TypeScript describes the backend interface: its Sass, its --typo3-* tokens and its color schemes. For a theme, those are the wrong answer. How a theme extension is laid out is covered in the hints.",
                        "instead": "typo3_hint_lookup withholds the backend CSS and TypeScript hints where a task names the frontend. The styling itself is documented at https://docs.typo3.org."
                    },
                    {
                        "topic": "Whether an uncatalogued component or CSS class exists on your branch",
                        "why": "The searchable index is a curated subset, not every class in backend.css. Without an installation, the curated entries fall back to one pinned core snapshot.",
                        "instead": "The component answer names its source and the snapshot revision. Inspect the installed backend CSS or the target checkout for a class outside the index."
                    },
                    {
                        "topic": "Gerrit beyond what an anonymous read answers: the hunks of a patch set, a draft comment nobody published, what CI did beyond the line its bot posted, and anything a private change carries",
                        "why": "The review API is read without a credential, so drafts and private changes are out of reach. The hunks are read in the checkout, not over the API. Nothing here writes to Gerrit.",
                        "instead": "typo3_gerrit_lookup answers the change, its patch set, paths, votes, comments and fetch ref. Voting, commenting and uploading stay yours, and typo3://guides/core/contribution/gerrit-workflow covers them."
                    },
                    {
                        "topic": "What someone else's extension does: the API, the options and the documentation of a package the core does not ship",
                        "why": "Writing an extension is covered. The inside of somebody else's package is not: its API, its releases and its documentation. Its published TER record is answered, and typo3_system_extension_lookup says whether a name belongs to the core.",
                        "instead": "Read that extension's documentation. typo3_extension_describe answers what an installed extension registers, and typo3_ter_lookup what is published under its key."
                    },
                    {
                        "topic": "Deciding one site's configuration: its languages, its base and the variants per environment, its error handling, and the steps that install it",
                        "why": "A site set is a convention and is covered. What one installation writes into config/sites/<identifier>/config.yaml is that site's decision. typo3_project_describe reads the file and does not compose it.",
                        "instead": "Read what the project has with typo3_project_describe, ask typo3_hint_lookup with id=site-sets how a set resolves, and take the format from https://docs.typo3.org/."
                    },
                    {
                        "topic": "Running an installation: server and container setup, deployment, backups, the editorial use of the backend",
                        "why": "The knowledge base covers what is written into a TYPO3 project. Operating the installation around it is a different subject. A project's own environment file is the exception.",
                        "instead": "Use https://docs.typo3.org/. typo3_project_describe reports what the environment file configures, and typo3_hint_lookup with id=php-versions says which interpreter to declare."
                    },
                    {
                        "topic": "The content of the installation's own tables: a record of pages, tt_content, a file reference, a workspace, a user",
                        "why": "The content model is answered, and the content is read, never written. typo3_record_lookup reads rows with the shell user's database access rather than a backend user's permissions, and every answer says so.",
                        "instead": "Write records in the backend or with vendor/bin/typo3. Ask typo3_schema_lookup for a table's columns, typo3_record_lookup for its rows, typo3_flexform_lookup for a flex column, and typo3_hint_lookup for what DataHandler expects."
                    }
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
                "routing": [
                    {
                        "when": "Asked to review, audit or assess a project, a site package or an extension, before the first file",
                        "call": "typo3_project_describe, then typo3_task_guide for the workflow and typo3_extension_describe for each extension in scope"
                    },
                    {
                        "when": "Going through the core's open issues — the oldest, the ones nobody has touched for years, or what stands open in one area such as the RTE or the backend UI",
                        "call": "typo3_forge_lookup with backlog=\"oldest\" or \"stale\", narrowed with category in the user's own words and with tracker, then typo3_forge_lookup with the number of the one being taken further"
                    },
                    {
                        "when": "What one person has filed on the tracker, or what they still hold — one contributor's backlog, or what a core team member has open",
                        "call": "typo3_forge_lookup with backlog=\"oldest\" and involving, or reportedBy or assignedTo for one side, and status=\"all\" for the years. Never query by the name, which matches the text of an issue."
                    },
                    {
                        "when": "Writing a core bug report or filling in the new-issue form on forge.typo3.org, including the issue a patch's Resolves: trailer names",
                        "call": "typo3_issue_report_guide, which drafts, checks for duplicates and fills in the form; typo3_rule_lookup with documentId=\"core/contribution/reporting-an-issue\" for the reasons"
                    },
                    {
                        "when": "Taking a Forge issue on, before believing what it describes",
                        "call": "typo3_forge_lookup for the issue and its comments, again with query in its own words for duplicates, then typo3_gerrit_lookup with the number for an existing patch"
                    },
                    {
                        "when": "Before writing a patch for a file, or asking whether a fix was attempted before",
                        "call": "typo3_gerrit_lookup with path, and open for the changes under review, then with query in the words of a commit message"
                    },
                    {
                        "when": "Starting a review session without a change in hand: the oldest, the small, the nearly voted, mine, or the ones I voted on",
                        "call": "typo3_gerrit_lookup with backlog \"oldest\" or \"stale\" and its filters, then with change for the one picked"
                    },
                    {
                        "when": "Asking which branches carry the fix a commit hash names",
                        "call": "typo3_gerrit_lookup with commit: the change, its backports by Change-Id, and the branches each Releases: trailer claims"
                    },
                    {
                        "when": "Reviewing a TYPO3 core patch rather than a project or an extension",
                        "call": "typo3_gerrit_lookup for the change, typo3_rule_lookup per obligation, typo3_changelog_lookup for precedent, typo3_test_run_guide with the paths, then typo3_commit_message_guide with workflow=\"core\""
                    },
                    {
                        "when": "Settling a finding on what a rendering contains rather than on a diff: TypoScript defaults, lib.parseFunc, or a PHP change to the request pipeline, an error handler or a page renderer caller",
                        "call": "typo3_rule_lookup with documentId=\"core/testing/proving-a-rendering\""
                    },
                    {
                        "when": "Settling a finding on what a change costs at runtime",
                        "call": "typo3_rule_lookup with documentId=\"core/testing/timing-a-code-path\""
                    },
                    {
                        "when": "Writing a core patch: taking a Forge issue on, fixing a core bug, deprecating or removing core API, or amending a patch after review",
                        "call": "typo3_task_guide with the paths you are changing, then typo3_hint_lookup with the same ones, typo3_test_run_guide for the suites that can fail, and typo3_rule_lookup for the Gerrit workflow"
                    },
                    {
                        "when": "Starting a core task and looking for the applicable conventions and checks",
                        "call": "typo3_task_guide"
                    },
                    {
                        "when": "About to invent a layout, a directory structure or a test harness — the core has probably worked one out already",
                        "call": "typo3_reference_list"
                    },
                    {
                        "when": "About to write backend markup or invent a CSS class name, layout classes included. A miss means uncurated, not outside the subject.",
                        "call": "typo3_component_lookup"
                    },
                    {
                        "when": "About to run tests or any other core check",
                        "call": "typo3_test_run_guide"
                    },
                    {
                        "when": "Getting Build/Scripts/runTests.sh to run in this checkout: a suite that fails before it reads a file, a fresh clone, a worktree, or the pre-commit hook",
                        "call": "typo3_script_lookup with the task in your own words"
                    },
                    {
                        "when": "Working in a concrete file and unsure about the subsystem's conventions",
                        "call": "typo3_hint_lookup"
                    },
                    {
                        "when": "Debugging, where there is no subject yet — something renders, saves or builds wrong and which subsystem does it is the question",
                        "call": "typo3_hint_lookup with task written as the symptom, in the words the failure showed in"
                    },
                    {
                        "when": "Needing the official API, reference or tutorial documentation for a covered TYPO3 version",
                        "call": "typo3_documentation_lookup with several short English queries and targetVersion, then with page and the same targetVersion to read a selected result"
                    },
                    {
                        "when": "Writing or checking a documentation link, or replacing a docs.typo3.org URL with a permalink",
                        "call": "typo3_permalink_lookup with identifiers or urls, many at a time"
                    },
                    {
                        "when": "Writing or amending the commit message",
                        "call": "typo3_commit_message_guide, whose default is a repository of your own"
                    },
                    {
                        "when": "Pushing for review, amending a patch set — your own or another author's — or backporting",
                        "call": "typo3_rule_lookup with a Gerrit query"
                    },
                    {
                        "when": "A catalog lookup found nothing that should exist",
                        "call": "typo3_snapshot_scope, then typo3_feedback_record"
                    },
                    {
                        "when": "Needing the translation domain an XLF file resolves to",
                        "call": "typo3_translation_domain_lookup with the path"
                    },
                    {
                        "when": "About to write user-facing text or invent a label key",
                        "call": "typo3_label_lookup with words from the wording and the XLF resource used at the consuming code"
                    },
                    {
                        "when": "About to reference a backend icon identifier: TCA, a module, a wizard, a backend template",
                        "call": "typo3_icon_lookup"
                    },
                    {
                        "when": "Needing a configuration value as it really is at runtime, not as the core ships it",
                        "call": "typo3_configuration_lookup with the TYPO3_CONF_VARS path as configurationPath"
                    },
                    {
                        "when": "Working on a backend module and needing its registration, its sub-routes, or whether the page tree navigates it",
                        "call": "typo3_backend_module_lookup"
                    },
                    {
                        "when": "Unsure which Fluid namespace prefixes a template may use undeclared",
                        "call": "typo3_fluid_namespace_list"
                    },
                    {
                        "when": "Needing which arguments a Fluid ViewHelper registers, with the type, the default and whether it takes arbitrary ones beyond them",
                        "call": "typo3_documentation_lookup with the tag name such as f:asset.css and the targetVersion"
                    },
                    {
                        "when": "About to recommend a command, or asking what this project consists of",
                        "call": "typo3_project_describe"
                    },
                    {
                        "when": "Starting work on a TYPO3 major you have not built on recently, or planning an upgrade to one this installation does not have — before asking what a version changed",
                        "call": "typo3_changelog_lookup"
                    },
                    {
                        "when": "Asking whether a pattern still works in a version; the silence of the changelog is no answer",
                        "call": "typo3_documentation_lookup with targetVersion and short English queries, then with page"
                    },
                    {
                        "when": "Writing against a core API in a package that declares a TYPO3 major the installation does not have",
                        "call": "typo3_project_describe, then typo3_rule_lookup with documentId=\"extension/compatibility/a-declared-major-that-is-not-installed\""
                    },
                    {
                        "when": "Sweeping deprecations in a package that declares more than one TYPO3 major",
                        "call": "typo3_changelog_lookup with the issue number of the deprecation, for the sibling entries and their versions"
                    },
                    {
                        "when": "About to claim that a change holds on a declared major the installation does not have",
                        "call": "typo3_rule_lookup with documentId=\"extension/compatibility/running-on-a-declared-major-that-is-not-installed\""
                    },
                    {
                        "when": "Upgrading or maintaining an installation, and needing the order of operations",
                        "call": "typo3_task_guide, then typo3_project_describe and typo3_changelog_lookup for what this one is and what the target version changed"
                    },
                    {
                        "when": "Auditing or preparing an extension release, before calling a version unreleased",
                        "call": "typo3_ter_lookup with the extension key and version, then typo3_hint_lookup with id=\"extension-ter-release\""
                    },
                    {
                        "when": "About to build a feature, before the first file: a published extension may already do it",
                        "call": "typo3_ter_lookup with query for the subject, then typo3_hint_lookup with id=\"extension-reuse\" to weigh the best match against your own build"
                    },
                    {
                        "when": "Working in one extension and needing what it registers",
                        "call": "typo3_extension_describe with its key"
                    },
                    {
                        "when": "About to claim that an extension is (or is not) part of the core, or to require one",
                        "call": "typo3_system_extension_lookup"
                    },
                    {
                        "when": "About to write or review an ext_tables.sql, and needing to know which columns TYPO3 creates by itself",
                        "call": "typo3_schema_lookup with the table"
                    },
                    {
                        "when": "Writing or reading a FlexForm. The file the registration names is not what the backend resolves.",
                        "call": "typo3_flexform_lookup with the table, the flex column and the record values that decide the structure, CType for a content element"
                    },
                    {
                        "when": "Asking which class stands behind an interface here, whether a service is public, what a constructor is handed, or what registers into an extension point",
                        "call": "typo3_service_lookup with a substring of the id or the class, or with one exact tag"
                    },
                    {
                        "when": "Asking whether the database matches what the extensions and the TCA declare",
                        "call": "typo3_schema_lookup with the table"
                    },
                    {
                        "when": "Asking what is actually stored in one of this project's own tables, or how much of it there is",
                        "call": "typo3_record_lookup with the table, and columns, groupBy, count or where as the question needs"
                    }
                ],
                "versions": [
                    {
                        "major": 12,
                        "branch": "12.4",
                        "status": "elts"
                    },
                    {
                        "major": 13,
                        "branch": "13.4",
                        "status": "lts"
                    },
                    {
                        "major": 14,
                        "branch": "14.3",
                        "status": "lts"
                    },
                    {
                        "major": 15,
                        "branch": "main",
                        "status": "development"
                    }
                ],
                "excludedTools": {
                    "names": [],
                    "ignored": [],
                    "variable": "TYPO3_DEV_COMPANION_EXCLUDE_TOOLS"
                },
                "answersFrom": [
                    {
                        "source": "installation",
                        "meaning": "The installation this server started in, booted or asked through its console. Its assembled state after every extension has had its say, and nothing at all where it is out of reach.",
                        "tools": [
                            "typo3_server_scope",
                            "typo3_label_lookup",
                            "typo3_fluid_namespace_list",
                            "typo3_configuration_lookup",
                            "typo3_schema_lookup",
                            "typo3_record_lookup",
                            "typo3_service_lookup",
                            "typo3_flexform_lookup",
                            "typo3_backend_module_lookup",
                            "typo3_icon_lookup",
                            "typo3_extension_describe"
                        ]
                    },
                    {
                        "source": "packages",
                        "meaning": "The files the installed packages ship, read rather than executed. Answers on a fresh clone and with the containers down. What a package registers at runtime is not in it.",
                        "tools": [
                            "typo3_forge_lookup",
                            "typo3_component_lookup",
                            "typo3_label_lookup",
                            "typo3_fluid_namespace_list",
                            "typo3_icon_lookup",
                            "typo3_changelog_lookup",
                            "typo3_project_describe",
                            "typo3_extension_describe",
                            "typo3_snapshot_scope"
                        ]
                    },
                    {
                        "source": "knowledge",
                        "meaning": "The knowledge base inside this package. Needs nothing up, and binds to TYPO3 versions rather than to an installation.",
                        "tools": [
                            "typo3_server_scope",
                            "typo3_rule_lookup",
                            "typo3_script_lookup",
                            "typo3_task_guide",
                            "typo3_test_run_guide",
                            "typo3_hint_lookup",
                            "typo3_issue_report_guide",
                            "typo3_component_lookup",
                            "typo3_system_extension_lookup",
                            "typo3_reference_list",
                            "typo3_translation_domain_lookup",
                            "typo3_snapshot_scope",
                            "typo3_commit_message_guide"
                        ]
                    },
                    {
                        "source": "network",
                        "meaning": "A service outside this machine. An unreachable one says so out loud rather than answers as empty.",
                        "tools": [
                            "typo3_documentation_lookup",
                            "typo3_permalink_lookup",
                            "typo3_forge_lookup",
                            "typo3_issue_report_guide",
                            "typo3_gerrit_lookup",
                            "typo3_changelog_lookup",
                            "typo3_ter_lookup"
                        ]
                    },
                    {
                        "source": "checkout",
                        "meaning": "This server's own checkout, which is why the tool offering it exists only in a standalone one.",
                        "tools": [
                            "typo3_feedback_record",
                            "typo3_feedback_list"
                        ]
                    }
                ],
                "installation": {
                    "found": true,
                    "root": "<installation>",
                    "kind": "core-checkout",
                    "via": "discovery",
                    "startedFrom": "<installation>",
                    "searched": [
                        "<installation>"
                    ],
                    "packageCount": 36,
                    "misconfiguration": null,
                    "console": {
                        "reachable": false,
                        "via": null,
                        "php": null,
                        "command": null,
                        "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both",
                        "caveat": null
                    },
                    "settings": {
                        "root": "TYPO3_DEV_COMPANION_ROOT",
                        "console": "TYPO3_DEV_COMPANION_CONSOLE"
                    }
                },
                "upstream": {
                    "repository": "https://github.com/TYPO3/dev-companion",
                    "state": "unknown",
                    "revision": null,
                    "behind": null,
                    "checkedAt": null,
                    "lastAnswered": null,
                    "reason": null,
                    "variable": "TYPO3_DEV_COMPANION_UPSTREAM_CHECK"
                },
                "withheld": [],
                "owed": [
                    "The one exception is typo3_feedback_record, this server's only write: it creates a new markdown feedback under feedback/ and touches nothing else. Missing something that belongs here? Leave feedback about it.",
                    "Record one with typo3_feedback_record instead of going elsewhere."
                ]
            }

scope: one section
~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "sections": [
            "installation"
        ]
    }

.. tabs::

    .. tab:: Text

        .. code-block:: text

            A development companion for coding agents working with TYPO3, for three audiences: the core contributor, the extension author and the site developer. It establishes the project and installation the agent works in, supplies current, version-bound TYPO3 knowledge, and hands task workflows to the skills that own them. A scope answer describes what is present and does not treat it as correct. The server also answers what is registered in the installation it was started in. Where that installation does not boot or does not exist, the bundled knowledge and the installed packages answer, and the answer says which. Every answer says which TYPO3 versions it holds for and which kind of work it belongs to.

            Query this server in English, whatever language you are speaking with the user. Its knowledge is written in English and its matching is lexical, so a query in another language reaches only the words the two happen to share and otherwise comes back empty.

            Found the TYPO3 installation at <installation> (core-checkout, found by walking up, from <installation>), which holds 36 packages. If that is not the installation you are working on, this server was started in the wrong directory — or set TYPO3_DEV_COMPANION_ROOT to the one you mean.
            Its console cannot be run right now, so questions that only the installation can answer — which labels exist, which backend modules are registered — have no answer here: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both. Where the command that would work is known, TYPO3_DEV_COMPANION_CONSOLE states it, for example "ddev exec .build/bin/typo3".

            Every lookup and guide is read-only. The tools that answer from the network read the hosts their descriptions name, and the start asks GitHub whether this checkout is behind; apart from those and the installation named above, nothing is fetched, executed, or looked up online.
            The one exception is typo3_feedback_record, this server's only write: it creates a new markdown feedback under feedback/ and touches nothing else. Missing something that belongs here? Leave feedback about it.

            Left out, because this call named sections: covers, doesNotCover, checkoutDiscovery, routing, versions, answersFrom, upstream. Ask again naming those, or call this tool with no arguments for the whole orientation.

    .. tab:: Data

        .. code-block:: json

            {
                "purpose": "A development companion for coding agents working with TYPO3, for three audiences: the core contributor, the extension author and the site developer. It establishes the project and installation the agent works in, supplies current, version-bound TYPO3 knowledge, and hands task workflows to the skills that own them. A scope answer describes what is present and does not treat it as correct. The server also answers what is registered in the installation it was started in. Where that installation does not boot or does not exist, the bundled knowledge and the installed packages answer, and the answer says which. Every answer says which TYPO3 versions it holds for and which kind of work it belongs to.",
                "instructions": "Start every task with typo3_project_describe. It answers the TYPO3 version, the project's own extensions, its sites, and the commands the repository declares. A check you recommend that the repository does not declare is a wrong answer however sensible it sounds. Then call typo3_task_guide for the workflow the task belongs to. Call it again at the first test, check, commit or shipped file the task did not name. Not every task ends in a patch. It also answers a triage of the backlog, whether a report still reproduces, and what a fix costs. What changed, which branch you are on, whether a path still exists, and how the core implements something are yours to read in the checkout.\n\nWhat to call for what:\n- backend markup or a CSS class: typo3_component_lookup with the targetVersion\n- a backend icon identifier: typo3_icon_lookup\n- a label, added or reworded: typo3_label_lookup with the XLF resource the code at hand uses; a match elsewhere is not reusable\n- what a version broke, deprecated or added, on a major you have not built on lately: typo3_changelog_lookup\n- the commit message, yours as much as the core's, and its branches: typo3_commit_message_guide\n- the whole procedure, not one fact out of it: typo3_rule_lookup with a documentId typo3_project_describe lists\n\nActivate the typo3-* skill that covers the task: it makes these calls.\n\ntargetVersion or the version the server read filters the answers, and a statement that does not hold on every covered line carries its range.\n\nQuery this server in English whatever language you speak with the user. Its match is lexical, so a query in another language reaches only the loanwords. Translate the subject before you call, and the answer back.\n\ntypo3_server_scope says what it covers, by which tool, and which installation it reads.",
                "excludedTools": {
                    "names": [],
                    "ignored": [],
                    "variable": "TYPO3_DEV_COMPANION_EXCLUDE_TOOLS"
                },
                "installation": {
                    "found": true,
                    "root": "<installation>",
                    "kind": "core-checkout",
                    "via": "discovery",
                    "startedFrom": "<installation>",
                    "searched": [
                        "<installation>"
                    ],
                    "packageCount": 36,
                    "misconfiguration": null,
                    "console": {
                        "reachable": false,
                        "via": null,
                        "php": null,
                        "command": null,
                        "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both",
                        "caveat": null
                    },
                    "settings": {
                        "root": "TYPO3_DEV_COMPANION_ROOT",
                        "console": "TYPO3_DEV_COMPANION_CONSOLE"
                    }
                },
                "withheld": [
                    {
                        "section": "covers",
                        "carries": "what is covered, at which depth, and by which tool"
                    },
                    {
                        "section": "doesNotCover",
                        "carries": "what this server deliberately does not answer, and what to do instead"
                    },
                    {
                        "section": "checkoutDiscovery",
                        "carries": "what to establish in the checkout before the work, and how"
                    },
                    {
                        "section": "routing",
                        "carries": "which tool to call when"
                    },
                    {
                        "section": "versions",
                        "carries": "the TYPO3 versions this knowledge binds to"
                    },
                    {
                        "section": "answersFrom",
                        "carries": "which source answers which tool, in the state this machine is in"
                    },
                    {
                        "section": "upstream",
                        "carries": "whether this server's checkout is behind the repository it comes from"
                    }
                ],
                "owed": [
                    "The one exception is typo3_feedback_record, this server's only write: it creates a new markdown feedback under feedback/ and touches nothing else. Missing something that belongs here? Leave feedback about it.",
                    "Ask again naming those, or call this tool with no arguments for the whole orientation."
                ]
            }
