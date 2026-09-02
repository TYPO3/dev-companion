# What this server has to do

The working directory: one file per requirement, in the group its id names. The
listing below and the one at the foot of each group's own `readme.md` are
written by `bin/cli requirements:index`.

What a requirement is and what its states mean:
[documentation/records/requirements.rst](../documentation/records/requirements.rst).
Where an entry goes and how one is written:
[documentation/records/writing-a-requirement.rst](../documentation/records/writing-a-requirement.rst),
which `bin/cli requirements:check` holds every file to.

## Every requirement, by group

**open** is accepted and not built yet, `not guarded` is built and named by no
test. Both are legitimate, nothing fails on either, and
`bin/cli unresolved:list` is what reads them out.

### audience

- [`R-AUD-001`][R-AUD-001] — Core, extension and site work are each served · held
- [`R-AUD-002`][R-AUD-002] — The audience is a property of the task · held
- [`R-AUD-003`][R-AUD-003] — Commit conventions differ by audience · held
- [`R-AUD-004`][R-AUD-004] — The knowledge is bound to versions · held
- [`R-AUD-005`][R-AUD-005] — An answer says who it obliges · held
- [`R-AUD-006`][R-AUD-006] — The query language is English · held

[R-AUD-001]: audience/aud-001-core-extension-and-site-work-are-each-served.md
[R-AUD-002]: audience/aud-002-the-audience-is-a-property-of-the-task.md
[R-AUD-003]: audience/aud-003-commit-conventions-differ-by-audience.md
[R-AUD-004]: audience/aud-004-the-knowledge-is-bound-to-versions.md
[R-AUD-005]: audience/aud-005-an-answer-says-who-it-obliges.md
[R-AUD-006]: audience/aud-006-the-query-language-is-english.md

### discovery

- [`R-DIS-001`][R-DIS-001] — Discovery belongs to the stdio entrypoint alone · held
- [`R-DIS-002`][R-DIS-002] — The packages are read from the declared vendor directory · held
- [`R-DIS-003`][R-DIS-003] — The console is looked for where the installation declares it · held
- [`R-DIS-004`][R-DIS-004] — The extension being worked on is part of its own installation · held
- [`R-DIS-005`][R-DIS-005] — A repository with no installation around it is not one · held
- [`R-DIS-006`][R-DIS-006] — Nothing is started as a side effect of a lookup · held
- [`R-DIS-007`][R-DIS-007] — The installation and the console can be named outright · held
- [`R-DIS-008`][R-DIS-008] — A failed discovery names where it looked · held
- [`R-DIS-009`][R-DIS-009] — A negative is never remembered · held
- [`R-DIS-010`][R-DIS-010] — Reachable and ready are two questions · held
- [`R-DIS-011`][R-DIS-011] — The entrypoint installs its own client configuration · held
- [`R-DIS-012`][R-DIS-012] — Codex setup installs the server and its skills · held
- [`R-DIS-013`][R-DIS-013] — Every supported agent client can be installed into · held
- [`R-DIS-014`][R-DIS-014] — An installed skill is a workflow, not a prompt fragment · held
- [`R-DIS-015`][R-DIS-015] — The DDEV client entry names an entrypoint that exists · held
- [`R-DIS-016`][R-DIS-016] — A repository that serves two majors is answered for both · held
- [`R-DIS-017`][R-DIS-017] — An extension below Tests/ is the test setup's · held
- [`R-DIS-018`][R-DIS-018] — A console command never inherits the client's stdin · held
- [`R-DIS-019`][R-DIS-019] — A registry with no command is answered by the installation itself · held
- [`R-DIS-020`][R-DIS-020] — The project records which clients are installed in it · held
- [`R-DIS-021`][R-DIS-021] — The client entry is rewritten when the project outgrows it · held
- [`R-DIS-022`][R-DIS-022] — A call can tell where it came from · held
- [`R-DIS-023`][R-DIS-023] — An install says what is left before a tool can be called · held
- [`R-DIS-024`][R-DIS-024] — The published directories ignore themselves · held
- [`R-DIS-025`][R-DIS-025] — A publication that went stale says so before the first call · held

[R-DIS-001]: discovery/dis-001-discovery-belongs-to-the-stdio-entrypoint-alone.md
[R-DIS-002]: discovery/dis-002-the-packages-are-read-from-the-declared-vendor-directory.md
[R-DIS-003]: discovery/dis-003-the-console-is-looked-for-where-the-installation-declares-it.md
[R-DIS-004]: discovery/dis-004-the-extension-being-worked-on-is-part-of-its-own-installation.md
[R-DIS-005]: discovery/dis-005-a-repository-with-no-installation-around-it-is-not-one.md
[R-DIS-006]: discovery/dis-006-nothing-is-started-as-a-side-effect-of-a-lookup.md
[R-DIS-007]: discovery/dis-007-the-installation-and-the-console-can-be-named-outright.md
[R-DIS-008]: discovery/dis-008-a-failed-discovery-names-where-it-looked.md
[R-DIS-009]: discovery/dis-009-a-negative-is-never-remembered.md
[R-DIS-010]: discovery/dis-010-reachable-and-ready-are-two-questions.md
[R-DIS-011]: discovery/dis-011-the-entrypoint-installs-its-own-client-configuration.md
[R-DIS-012]: discovery/dis-012-codex-setup-installs-the-server-and-its-skills.md
[R-DIS-013]: discovery/dis-013-every-supported-agent-client-can-be-installed-into.md
[R-DIS-014]: discovery/dis-014-an-installed-skill-is-a-workflow-not-a-prompt-fragment.md
[R-DIS-015]: discovery/dis-015-the-ddev-client-entry-names-an-entrypoint-that-exists.md
[R-DIS-016]: discovery/dis-016-a-repository-that-serves-two-majors-is-answered-for-both.md
[R-DIS-017]: discovery/dis-017-an-extension-below-tests-is-the-test-setups.md
[R-DIS-018]: discovery/dis-018-a-console-command-never-inherits-the-clients-stdin.md
[R-DIS-019]: discovery/dis-019-a-registry-with-no-command-is-answered-by-the-installation-itself.md
[R-DIS-020]: discovery/dis-020-the-project-records-which-clients-are-installed-in-it.md
[R-DIS-021]: discovery/dis-021-the-client-entry-is-rewritten-when-the-project-outgrows-it.md
[R-DIS-022]: discovery/dis-022-a-call-can-tell-where-it-came-from.md
[R-DIS-023]: discovery/dis-023-an-install-says-what-is-left-before-a-tool-can-be-called.md
[R-DIS-024]: discovery/dis-024-the-published-directories-ignore-themselves.md
[R-DIS-025]: discovery/dis-025-a-publication-that-went-stale-says-so-before-the-first-call.md

### answers

- [`R-ANS-001`][R-ANS-001] — "Could not ask" never looks like "does not exist" · held
- [`R-ANS-002`][R-ANS-002] — The reason is in the data, not only in the text · held
- [`R-ANS-003`][R-ANS-003] — A component answer carries its source and its version · held
- [`R-ANS-004`][R-ANS-004] — A tool implements the query language it documents · held
- [`R-ANS-005`][R-ANS-005] — A console that answered "none" has answered · held
- [`R-ANS-006`][R-ANS-006] — A miss says what there would have been to find · held
- [`R-ANS-007`][R-ANS-007] — The discriminating terms of a query decide the answer · held
- [`R-ANS-008`][R-ANS-008] — The files answer where the console cannot · held
- [`R-ANS-008b`][R-ANS-008b] — A short term is not the prefix of a longer word · held
- [`R-ANS-009`][R-ANS-009] — The instructions say when to call the lookups that come first · held
- [`R-ANS-010`][R-ANS-010] — A component answers the query that names it · held
- [`R-ANS-011`][R-ANS-011] — A content element is answered by what it owns · held
- [`R-ANS-012`][R-ANS-012] — An answer that cannot read something says so · held
- [`R-ANS-013`][R-ANS-013] — The instructions fit what a client keeps · held
- [`R-ANS-014`][R-ANS-014] — A registration is answered wherever it is declared · held
- [`R-ANS-015`][R-ANS-015] — A label rule reaches the task that never names a label · held
- [`R-ANS-016`][R-ANS-016] — A content-element task is offered the Extbase fork · held
- [`R-ANS-017`][R-ANS-017] — A removal is told what the scanner matcher requires · held
- [`R-ANS-018`][R-ANS-018] — An answer names the tool for what it says is absent · held
- [`R-ANS-019`][R-ANS-019] — A rendered-verification question reaches the layer that verifies it · held
- [`R-ANS-020`][R-ANS-020] — The Classes section covers the directory it names · held
- [`R-ANS-021`][R-ANS-021] — The review answer says which patch set it is about · held
- [`R-ANS-022`][R-ANS-022] — A resource is picked out of a list · held
- [`R-ANS-023`][R-ANS-023] — A review answer names only changes that name the issue · held
- [`R-ANS-024`][R-ANS-024] — A field that is answered empty is one nothing could fill · held
- [`R-ANS-025`][R-ANS-025] — An issue answer says what its comments refer to · held
- [`R-ANS-026`][R-ANS-026] — A path names the subsystem · held
- [`R-ANS-027`][R-ANS-027] — An answer that cannot separate two causes says so in the answer · held
- [`R-ANS-028`][R-ANS-028] — An answer that names a document says how to read it whole · held
- [`R-ANS-029`][R-ANS-029] — An answer that names a record says enough of it to judge whether to open it · held
- [`R-ANS-030`][R-ANS-030] — A bound on an answer is asked for and never applied by default · held
- [`R-ANS-031`][R-ANS-031] — A symptom reaches the hint that explains it · held
- [`R-ANS-032`][R-ANS-032] — The instructions index the question each tool answers · held
- [`R-ANS-033`][R-ANS-033] — A path names the repository it is in · held
- [`R-ANS-034`][R-ANS-034] — A suite an answer offers says what running it does to the checkout · held
- [`R-ANS-035`][R-ANS-035] — An answer that names a target branch names the lines that take a patch · held
- [`R-ANS-036`][R-ANS-036] — A suite list names the paths no suite covers · held
- [`R-ANS-037`][R-ANS-037] — An answer says how to read the current version rather than naming one · not guarded
- [`R-ANS-038`][R-ANS-038] — A file-read label names what makes its resource usable · held

[R-ANS-001]: answers/ans-001-could-not-ask-never-looks-like-does-not-exist.md
[R-ANS-002]: answers/ans-002-the-reason-is-in-the-data-not-only-in-the-text.md
[R-ANS-003]: answers/ans-003-a-component-answer-carries-its-source-and-its-version.md
[R-ANS-004]: answers/ans-004-a-tool-implements-the-query-language-it-documents.md
[R-ANS-005]: answers/ans-005-a-console-that-answered-none-has-answered.md
[R-ANS-006]: answers/ans-006-a-miss-says-what-there-would-have-been-to-find.md
[R-ANS-007]: answers/ans-007-the-discriminating-terms-of-a-query-decide-the-answer.md
[R-ANS-008]: answers/ans-008-the-files-answer-where-the-console-cannot.md
[R-ANS-008b]: answers/ans-008b-a-short-term-is-not-the-prefix-of-a-longer-word.md
[R-ANS-009]: answers/ans-009-the-instructions-say-when-to-call-the-lookups-that-come-first.md
[R-ANS-010]: answers/ans-010-a-component-answers-the-query-that-names-it.md
[R-ANS-011]: answers/ans-011-a-content-element-is-answered-by-what-it-owns.md
[R-ANS-012]: answers/ans-012-an-answer-that-cannot-read-something-says-so.md
[R-ANS-013]: answers/ans-013-the-instructions-fit-what-a-client-keeps.md
[R-ANS-014]: answers/ans-014-a-registration-is-answered-wherever-it-is-declared.md
[R-ANS-015]: answers/ans-015-a-label-rule-reaches-the-task-that-never-names-a-label.md
[R-ANS-016]: answers/ans-016-a-content-element-task-is-offered-the-extbase-fork.md
[R-ANS-017]: answers/ans-017-a-removal-is-told-what-the-scanner-matcher-requires.md
[R-ANS-018]: answers/ans-018-an-answer-names-the-tool-for-what-it-says-is-absent.md
[R-ANS-019]: answers/ans-019-a-rendered-verification-question-reaches-the-layer-that-verifies-it.md
[R-ANS-020]: answers/ans-020-the-classes-section-covers-the-directory-it-names.md
[R-ANS-021]: answers/ans-021-the-review-answer-says-which-patch-set-it-is-about.md
[R-ANS-022]: answers/ans-022-a-resource-is-picked-out-of-a-list.md
[R-ANS-023]: answers/ans-023-a-review-answer-names-only-changes-that-name-the-issue.md
[R-ANS-024]: answers/ans-024-a-field-that-is-answered-empty-is-one-nothing-could-fill.md
[R-ANS-025]: answers/ans-025-an-issue-answer-says-what-its-comments-refer-to.md
[R-ANS-026]: answers/ans-026-a-path-names-the-subsystem.md
[R-ANS-027]: answers/ans-027-an-answer-that-cannot-separate-two-causes-says-so-in-the-answer.md
[R-ANS-028]: answers/ans-028-an-answer-that-names-a-document-says-how-to-read-it-whole.md
[R-ANS-029]: answers/ans-029-an-answer-that-names-a-record-says-enough-of-it-to-judge-whether-to-open-it.md
[R-ANS-030]: answers/ans-030-a-bound-on-an-answer-is-asked-for-and-never-applied-by-default.md
[R-ANS-031]: answers/ans-031-a-symptom-reaches-the-hint-that-explains-it.md
[R-ANS-032]: answers/ans-032-the-instructions-index-the-question-each-tool-answers.md
[R-ANS-033]: answers/ans-033-a-path-names-the-repository-it-is-in.md
[R-ANS-034]: answers/ans-034-a-suite-an-answer-offers-says-what-running-it-does-to-the-checkout.md
[R-ANS-035]: answers/ans-035-an-answer-that-names-a-target-branch-names-the-lines-that-take-a-patch.md
[R-ANS-036]: answers/ans-036-a-suite-list-names-the-paths-no-suite-covers.md
[R-ANS-037]: answers/ans-037-an-answer-says-how-to-read-the-current-version-rather-than-naming-one.md
[R-ANS-038]: answers/ans-038-a-file-read-label-names-what-makes-its-resource-usable.md

### documentation

- [`R-DOC-001`][R-DOC-001] — The live manuals answer for the version they were asked for · held
- [`R-DOC-002`][R-DOC-002] — A manual search says what it matched on · held
- [`R-DOC-003`][R-DOC-003] — A ViewHelper question is answered from the manual that documents ViewHelpers · held

[R-DOC-001]: documentation/doc-001-the-live-manuals-answer-for-the-version-they-were-asked-for.md
[R-DOC-002]: documentation/doc-002-a-manual-search-says-what-it-matched-on.md
[R-DOC-003]: documentation/doc-003-a-viewhelper-question-is-answered-from-the-manual-that-documents-viewhelpers.md

### task-skills

- [`R-SKL-001`][R-SKL-001] — A backend-module task activates its own guidance · held
- [`R-SKL-002`][R-SKL-002] — A testing task verifies the harness before relying on it · held
- [`R-SKL-003`][R-SKL-003] — Crossing into another skill's work is an explicit transition · held
- [`R-SKL-004`][R-SKL-004] — An assessment establishes its base before opening the checkout · held
- [`R-SKL-005`][R-SKL-005] — The order a task starts in is written once · held
- [`R-SKL-006`][R-SKL-006] — How a skill is written is written down once · held
- [`R-SKL-007`][R-SKL-007] — An upgrade establishes what breaks before it chooses a range · held
- [`R-SKL-008`][R-SKL-008] — A task skill does not run without the server it came from · held
- [`R-SKL-009`][R-SKL-009] — A release answer is about the archive a registry receives · **open**
- [`R-SKL-010`][R-SKL-010] — A skill's description names every side of what it owns · held
- [`R-SKL-011`][R-SKL-011] — A review reports what it dropped and what dropped it · held
- [`R-SKL-012`][R-SKL-012] — A finding is attributed to the change under review · held
- [`R-SKL-013`][R-SKL-013] — A surface reported as assessed names what was read · held
- [`R-SKL-014`][R-SKL-014] — A review reads what the project already says about the patch · held
- [`R-SKL-015`][R-SKL-015] — A rule quoted at a patch is verified in the checkout · held
- [`R-SKL-016`][R-SKL-016] — The assessment before a core patch reads the issue and the review server · held
- [`R-SKL-017`][R-SKL-017] — The commit step is named where a skill's workflow ends in a change · held
- [`R-SKL-018`][R-SKL-018] — A skill that hands over tells the session to invoke the next one · held
- [`R-SKL-019`][R-SKL-019] — Every published skill is named by an intent the brief can reach · held
- [`R-SKL-020`][R-SKL-020] — A workflow that ends in public stops when the finding is a vulnerability · held
- [`R-SKL-021`][R-SKL-021] — Every description is written to the budget they share · held
- [`R-SKL-022`][R-SKL-022] — A review surface names the lookup that can answer it · held
- [`R-SKL-023`][R-SKL-023] — A skill whose product is a report says the report is copyable markdown · held
- [`R-SKL-024`][R-SKL-024] — A build step a guide answers names the call that fetches it · held
- [`R-SKL-025`][R-SKL-025] — An audit's list says what the repository already carries unmerged · held
- [`R-SKL-026`][R-SKL-026] — A runtime lookup a step names says what it adds after the extension answer · held
- [`R-SKL-027`][R-SKL-027] — A core patch covers every point its issue lists · held
- [`R-SKL-028`][R-SKL-028] — A widened request re-establishes what the patch is and what it owes · held
- [`R-SKL-029`][R-SKL-029] — A skill reading a project checks its pinned versions against the day's release · held

[R-SKL-001]: task-skills/skl-001-a-backend-module-task-activates-its-own-guidance.md
[R-SKL-002]: task-skills/skl-002-a-testing-task-verifies-the-harness-before-relying-on-it.md
[R-SKL-003]: task-skills/skl-003-crossing-into-another-skills-work-is-an-explicit-transition.md
[R-SKL-004]: task-skills/skl-004-an-assessment-establishes-its-base-before-opening-the-checkout.md
[R-SKL-005]: task-skills/skl-005-the-order-a-task-starts-in-is-written-once.md
[R-SKL-006]: task-skills/skl-006-how-a-skill-is-written-is-written-down-once.md
[R-SKL-007]: task-skills/skl-007-an-upgrade-establishes-what-breaks-before-it-chooses-a-range.md
[R-SKL-008]: task-skills/skl-008-a-task-skill-does-not-run-without-the-server-it-came-from.md
[R-SKL-009]: task-skills/skl-009-a-release-answer-is-about-the-archive-a-registry-receives.md
[R-SKL-010]: task-skills/skl-010-a-skills-description-names-every-side-of-what-it-owns.md
[R-SKL-011]: task-skills/skl-011-a-review-reports-what-it-dropped-and-what-dropped-it.md
[R-SKL-012]: task-skills/skl-012-a-finding-is-attributed-to-the-change-under-review.md
[R-SKL-013]: task-skills/skl-013-a-surface-reported-as-assessed-names-what-was-read.md
[R-SKL-014]: task-skills/skl-014-a-review-reads-what-the-project-already-says-about-the-patch.md
[R-SKL-015]: task-skills/skl-015-a-rule-quoted-at-a-patch-is-verified-in-the-checkout.md
[R-SKL-016]: task-skills/skl-016-the-assessment-before-a-core-patch-reads-the-issue-and-the-review-server.md
[R-SKL-017]: task-skills/skl-017-the-commit-step-is-named-where-a-skills-workflow-ends-in-a-change.md
[R-SKL-018]: task-skills/skl-018-a-skill-that-hands-over-tells-the-session-to-invoke-the-next-one.md
[R-SKL-019]: task-skills/skl-019-every-published-skill-is-named-by-an-intent-the-brief-can-reach.md
[R-SKL-020]: task-skills/skl-020-a-workflow-that-ends-in-public-stops-when-the-finding-is-a-vulnerability.md
[R-SKL-021]: task-skills/skl-021-every-description-is-written-to-the-budget-they-share.md
[R-SKL-022]: task-skills/skl-022-a-review-surface-names-the-lookup-that-can-answer-it.md
[R-SKL-023]: task-skills/skl-023-a-skill-whose-product-is-a-report-says-the-report-is-copyable-markdown.md
[R-SKL-024]: task-skills/skl-024-a-build-step-a-guide-answers-names-the-call-that-fetches-it.md
[R-SKL-025]: task-skills/skl-025-an-audits-list-says-what-the-repository-already-carries-unmerged.md
[R-SKL-026]: task-skills/skl-026-a-runtime-lookup-a-step-names-says-what-it-adds-after-the-extension-answer.md
[R-SKL-027]: task-skills/skl-027-a-core-patch-covers-every-point-its-issue-lists.md
[R-SKL-028]: task-skills/skl-028-a-widened-request-re-establishes-what-the-patch-is-and-what-it-owes.md
[R-SKL-029]: task-skills/skl-029-a-skill-reading-a-project-checks-its-pinned-versions-against-the-days-release.md

### project

- [`R-PRJ-001`][R-PRJ-001] — The project is describable from its files alone · held
- [`R-PRJ-002`][R-PRJ-002] — One unreadable site configuration costs that site and no other · held
- [`R-PRJ-003`][R-PRJ-003] — The installed changelog answers what a version changed · held
- [`R-PRJ-004`][R-PRJ-004] — An upgrade is answered as an order of operations · held
- [`R-PRJ-005`][R-PRJ-005] — What an extension registers is answered from the installation and its files · held
- [`R-PRJ-006`][R-PRJ-006] — What an extension does not ship is answered too · held
- [`R-PRJ-007`][R-PRJ-007] — A declared command says whether running it changes anything · held
- [`R-PRJ-008`][R-PRJ-008] — The project answer says what runs it, not only what it declares · held
- [`R-PRJ-009`][R-PRJ-009] — The project answer states the lifecycle its environment declares · held
- [`R-PRJ-010`][R-PRJ-010] — The project answer relates its PHP numbers rather than listing them · held
- [`R-PRJ-011`][R-PRJ-011] — A project root is a repository that declares TYPO3 · held
- [`R-PRJ-012`][R-PRJ-012] — A declared command says whether it can start · held
- [`R-PRJ-013`][R-PRJ-013] — The project answer states the Node its declared commands run on · held

[R-PRJ-001]: project/prj-001-the-project-is-describable-from-its-files-alone.md
[R-PRJ-002]: project/prj-002-one-unreadable-site-configuration-costs-that-site-and-no-other.md
[R-PRJ-003]: project/prj-003-the-installed-changelog-answers-what-a-version-changed.md
[R-PRJ-004]: project/prj-004-an-upgrade-is-answered-as-an-order-of-operations.md
[R-PRJ-005]: project/prj-005-what-an-extension-registers-is-answered-from-the-installation-and-its-files.md
[R-PRJ-006]: project/prj-006-what-an-extension-does-not-ship-is-answered-too.md
[R-PRJ-007]: project/prj-007-a-declared-command-says-whether-running-it-changes-anything.md
[R-PRJ-008]: project/prj-008-the-project-answer-says-what-runs-it-not-only-what-it-declares.md
[R-PRJ-009]: project/prj-009-the-project-answer-states-the-lifecycle-its-environment-declares.md
[R-PRJ-010]: project/prj-010-the-project-answer-relates-its-php-numbers-rather-than-listing-them.md
[R-PRJ-011]: project/prj-011-a-project-root-is-a-repository-that-declares-typo3.md
[R-PRJ-012]: project/prj-012-a-declared-command-says-whether-it-can-start.md
[R-PRJ-013]: project/prj-013-the-project-answer-states-the-node-its-declared-commands-run-on.md

### scope

- [`R-SCO-001`][R-SCO-001] — Outside the core is recognised from structure, not wording · held
- [`R-SCO-002`][R-SCO-002] — A scope outside the core changes the payload, entry by entry · held
- [`R-SCO-003`][R-SCO-003] — A core-only intent needs evidence of core work · held
- [`R-SCO-004`][R-SCO-004] — The backend CSS conventions are named as the backend's · held
- [`R-SCO-005`][R-SCO-005] — Every tool applies the same outside-the-core check · held
- [`R-SCO-006`][R-SCO-006] — Every topic says which kind of work it is for · held
- [`R-SCO-007`][R-SCO-007] — Only the caller shortens the tool list · held
- [`R-SCO-008`][R-SCO-008] — A subject the not-covered list omits is in scope · held
- [`R-SCO-009`][R-SCO-009] — Individual tools can be excluded · held

[R-SCO-001]: scope/sco-001-outside-the-core-is-recognised-from-structure-not-wording.md
[R-SCO-002]: scope/sco-002-a-scope-outside-the-core-changes-the-payload-entry-by-entry.md
[R-SCO-003]: scope/sco-003-a-core-only-intent-needs-evidence-of-core-work.md
[R-SCO-004]: scope/sco-004-the-backend-css-conventions-are-named-as-the-backends.md
[R-SCO-005]: scope/sco-005-every-tool-applies-the-same-outside-the-core-check.md
[R-SCO-006]: scope/sco-006-every-topic-says-which-kind-of-work-it-is-for.md
[R-SCO-007]: scope/sco-007-only-the-caller-shortens-the-tool-list.md
[R-SCO-008]: scope/sco-008-a-subject-the-not-covered-list-omits-is-in-scope.md
[R-SCO-009]: scope/sco-009-individual-tools-can-be-excluded.md

### guides

- [`R-GUI-001`][R-GUI-001] — A guide's checks describe the draft it returned · held
- [`R-GUI-002`][R-GUI-002] — The commit rules are available without the Gerrit trailers · held
- [`R-GUI-003`][R-GUI-003] — A guide points at the tool that performs the step · held
- [`R-GUI-004`][R-GUI-004] — A guide routes to the tools its subjects are answered by · held
- [`R-GUI-005`][R-GUI-005] — The commit-message guide is also a prompt · held
- [`R-GUI-006`][R-GUI-006] — A review is not answered with a checklist for changing something · held
- [`R-GUI-007`][R-GUI-007] — A body the guide reflowed or left too long says so · held
- [`R-GUI-008`][R-GUI-008] — A brief states what the change is for before its steps · held
- [`R-GUI-009`][R-GUI-009] — A hint a brief carries names the lookup that owns it · held
- [`R-GUI-010`][R-GUI-010] — A review brief names what the change removes · held
- [`R-GUI-011`][R-GUI-011] — A readiness answer names the classification it was not given · held
- [`R-GUI-012`][R-GUI-012] — A brief names the hints it left behind · held
- [`R-GUI-013`][R-GUI-013] — A brief names the guide the recognized work is written up in · held
- [`R-GUI-014`][R-GUI-014] — A brief names the acts the workflow question is asked again at · held

[R-GUI-001]: guides/gui-001-a-guides-checks-describe-the-draft-it-returned.md
[R-GUI-002]: guides/gui-002-the-commit-rules-are-available-without-the-gerrit-trailers.md
[R-GUI-003]: guides/gui-003-a-guide-points-at-the-tool-that-performs-the-step.md
[R-GUI-004]: guides/gui-004-a-guide-routes-to-the-tools-its-subjects-are-answered-by.md
[R-GUI-005]: guides/gui-005-the-commit-message-guide-is-also-a-prompt.md
[R-GUI-006]: guides/gui-006-a-review-is-not-answered-with-a-checklist-for-changing-something.md
[R-GUI-007]: guides/gui-007-a-body-the-guide-reflowed-or-left-too-long-says-so.md
[R-GUI-008]: guides/gui-008-a-brief-states-what-the-change-is-for-before-its-steps.md
[R-GUI-009]: guides/gui-009-a-hint-a-brief-carries-names-the-lookup-that-owns-it.md
[R-GUI-010]: guides/gui-010-a-review-brief-names-what-the-change-removes.md
[R-GUI-011]: guides/gui-011-a-readiness-answer-names-the-classification-it-was-not-given.md
[R-GUI-012]: guides/gui-012-a-brief-names-the-hints-it-left-behind.md
[R-GUI-013]: guides/gui-013-a-brief-names-the-guide-the-recognized-work-is-written-up-in.md
[R-GUI-014]: guides/gui-014-a-brief-names-the-acts-the-workflow-question-is-asked-again-at.md

### feedback

- [`R-FBK-001`][R-FBK-001] — A feedback is about as many tools as it is about · held
- [`R-FBK-002`][R-FBK-002] — A feedback that was worked off stays answerable for · held
- [`R-FBK-003`][R-FBK-003] — A forward run is a record, not a remembered status · held
- [`R-FBK-004`][R-FBK-004] — Only an open prompt produces forward evidence · held
- [`R-FBK-005`][R-FBK-005] — A feedback is attributed to the model that left it · held
- [`R-FBK-006`][R-FBK-006] — A recorded feedback is reported where it actually is · held
- [`R-FBK-007`][R-FBK-007] — The work already judged comes before judging more · held
- [`R-FBK-008`][R-FBK-008] — A feedback's name says what only that feedback says · held
- [`R-FBK-009`][R-FBK-009] — A todo is worked from what was read · held
- [`R-FBK-010`][R-FBK-010] — Work somebody has in hand is offered to nobody else · held
- [`R-FBK-011`][R-FBK-011] — A recorded feedback carries no secret out of the installation · held
- [`R-FBK-012`][R-FBK-012] — A debrief reports the window the session could see · not guarded
- [`R-FBK-013`][R-FBK-013] — A recorded name keeps the spelling it was given in · held
- [`R-FBK-014`][R-FBK-014] — A judgement takes the card it replaced with it · held
- [`R-FBK-015`][R-FBK-015] — A feedback field that was cut says so · held
- [`R-FBK-016`][R-FBK-016] — A field that arrived carrying its call is refused · held

[R-FBK-001]: feedback/fbk-001-a-feedback-is-about-as-many-tools-as-it-is-about.md
[R-FBK-002]: feedback/fbk-002-a-feedback-that-was-worked-off-stays-answerable-for.md
[R-FBK-003]: feedback/fbk-003-a-forward-run-is-a-record-not-a-remembered-status.md
[R-FBK-004]: feedback/fbk-004-only-an-open-prompt-produces-forward-evidence.md
[R-FBK-005]: feedback/fbk-005-a-feedback-is-attributed-to-the-model-that-left-it.md
[R-FBK-006]: feedback/fbk-006-a-recorded-feedback-is-reported-where-it-actually-is.md
[R-FBK-007]: feedback/fbk-007-the-work-already-judged-comes-before-judging-more.md
[R-FBK-008]: feedback/fbk-008-a-feedbacks-name-says-what-only-that-feedback-says.md
[R-FBK-009]: feedback/fbk-009-a-todo-is-worked-from-what-was-read.md
[R-FBK-010]: feedback/fbk-010-work-somebody-has-in-hand-is-offered-to-nobody-else.md
[R-FBK-011]: feedback/fbk-011-a-recorded-feedback-carries-no-secret-out-of-the-installation.md
[R-FBK-012]: feedback/fbk-012-a-debrief-reports-the-window-the-session-could-see.md
[R-FBK-013]: feedback/fbk-013-a-recorded-name-keeps-the-spelling-it-was-given-in.md
[R-FBK-014]: feedback/fbk-014-a-judgement-takes-the-card-it-replaced-with-it.md
[R-FBK-015]: feedback/fbk-015-a-feedback-field-that-was-cut-says-so.md
[R-FBK-016]: feedback/fbk-016-a-field-that-arrived-carrying-its-call-is-refused.md

### knowledge

- [`R-KNW-001`][R-KNW-001] — Upgrade wizards and DataProcessors have hints of their own · held
- [`R-KNW-002`][R-KNW-002] — A hint carries the words its subject is asked about in · held
- [`R-KNW-003`][R-KNW-003] — A hint says how a subsystem is used · held
- [`R-KNW-004`][R-KNW-004] — An authoring answer points at the reading side · held
- [`R-KNW-005`][R-KNW-005] — Where a mechanism fails silently, the hint names the failure · held
- [`R-KNW-006`][R-KNW-006] — Where the core ships the example, the answer names it · held
- [`R-KNW-007`][R-KNW-007] — A hint that ends in an instruction carries its steps · held
- [`R-KNW-008`][R-KNW-008] — Putting own records on a page is covered as one subject · held
- [`R-KNW-009`][R-KNW-009] — Registering something so the core finds it is covered · held
- [`R-KNW-010`][R-KNW-010] — An answer says which half of TYPO3 it is for · held
- [`R-KNW-011`][R-KNW-011] — Extbase is covered as its own subject · held
- [`R-KNW-012`][R-KNW-012] — Whether an extension is part of the core is answerable · held
- [`R-KNW-013`][R-KNW-013] — A statement lives in the category it is asked from · held
- [`R-KNW-014`][R-KNW-014] — A file list covers the one on its way out · held
- [`R-KNW-015`][R-KNW-015] — Building the test harness is covered as its own subject · held
- [`R-KNW-016`][R-KNW-016] — The test kind that needs a browser is covered · held
- [`R-KNW-017`][R-KNW-017] — A convention read off the core carries its condition · held
- [`R-KNW-018`][R-KNW-018] — Where an artifact can be verified is part of the answer · held
- [`R-KNW-019`][R-KNW-019] — The core's own worked examples are indexed · held
- [`R-KNW-020`][R-KNW-020] — The repository around the extension is a subject of its own · held
- [`R-KNW-021`][R-KNW-021] — A hint is reachable by what it says · held
- [`R-KNW-022`][R-KNW-022] — A hint is a candidate for the question it is asked from · held
- [`R-KNW-023`][R-KNW-023] — No prose document dates a statement in its sentence · held
- [`R-KNW-024`][R-KNW-024] — A check is offered only where the command exists · held
- [`R-KNW-025`][R-KNW-025] — The two site-local settings sources carry their precedence · held
- [`R-KNW-026`][R-KNW-026] — A routed argument carries its cache-hash boundary · held
- [`R-KNW-027`][R-KNW-027] — EXT:form is covered as a subsystem · held
- [`R-KNW-028`][R-KNW-028] — A surviving hook is named by the subsystem that calls it · held
- [`R-KNW-029`][R-KNW-029] — A core-only convention points at its project counterpart · held
- [`R-KNW-030`][R-KNW-030] — A non-English site reaches its label-language setup · held
- [`R-KNW-031`][R-KNW-031] — A persisted alias is answered in both directions · held
- [`R-KNW-032`][R-KNW-032] — Project configuration states who owns which file · held
- [`R-KNW-033`][R-KNW-033] — A new label names its source language · held
- [`R-KNW-034`][R-KNW-034] — Configuration is placed by the reach of its value · held
- [`R-KNW-035`][R-KNW-035] — Backend-module guidance continues past registration · held
- [`R-KNW-036`][R-KNW-036] — Label reuse stays at the usage context · held
- [`R-KNW-037`][R-KNW-037] — A distributed extension has repository conventions of its own · held
- [`R-KNW-038`][R-KNW-038] — A missing icon identifier has no matches · held
- [`R-KNW-039`][R-KNW-039] — A backend module in a sitepackage stays backend-module work · held
- [`R-KNW-040`][R-KNW-040] — An environment variable answer names what the core reads itself · held
- [`R-KNW-041`][R-KNW-041] — A preview template answer says what the template is handed · held
- [`R-KNW-042`][R-KNW-042] — A preview answer says what the default renderer already draws · held
- [`R-KNW-043`][R-KNW-043] — A datamap answer says what the parent column holds · held
- [`R-KNW-044`][R-KNW-044] — The fixture rule is stated with the empty database under it · held
- [`R-KNW-045`][R-KNW-045] — Reading records is covered as its own subject · held
- [`R-KNW-046`][R-KNW-046] — impexp is the way a page tree is established again · held
- [`R-KNW-047`][R-KNW-047] — An extension copies the phpunit XML and not the bootstrap · held
- [`R-KNW-048`][R-KNW-048] — Which processor claims a file is answered · held
- [`R-KNW-049`][R-KNW-049] — A check that can pass without reading anything says so · held
- [`R-KNW-050`][R-KNW-050] — A preview answer names what the preview draws from · held
- [`R-KNW-051`][R-KNW-051] — A changelog question is told which type the change owes · held
- [`R-KNW-052`][R-KNW-052] — The suite answer names the install a fresh checkout owes · held
- [`R-KNW-053`][R-KNW-053] — The per-class database answer says what survives the run · held
- [`R-KNW-054`][R-KNW-054] — Where FAL stops in the image pipeline is answered · held
- [`R-KNW-055`][R-KNW-055] — A rendered-output change is told where the expectations hide · held
- [`R-KNW-056`][R-KNW-056] — The placement answer names the document root as a place a script may not go · held
- [`R-KNW-057`][R-KNW-057] — The push a session cannot take back is answered in full · held
- [`R-KNW-058`][R-KNW-058] — The placement answer says which page may hold the record · held
- [`R-KNW-059`][R-KNW-059] — A change is told which cache group holds its old output · held
- [`R-KNW-060`][R-KNW-060] — The project configuration answer names what DDEV writes and what it cannot configure · held
- [`R-KNW-061`][R-KNW-061] — A translation file is told what a missing `target-language` costs it · held
- [`R-KNW-062`][R-KNW-062] — The import answer says what it rewrites in a site configuration · held
- [`R-KNW-063`][R-KNW-063] — A template answer states that the file-name fallback runs once per root path · held
- [`R-KNW-064`][R-KNW-064] — The Composer keys that install TYPO3 beneath an extension are answered · held
- [`R-KNW-065`][R-KNW-065] — Booting a declared installation is answered as its own subject · held
- [`R-KNW-066`][R-KNW-066] — A core PHP change is told what the class's public surface commits it to · held
- [`R-KNW-067`][R-KNW-067] — The e2e answer states the price of a Playwright-only change · held
- [`R-KNW-068`][R-KNW-068] — A suite that waits for a keypress says it needs a terminal · held
- [`R-KNW-069`][R-KNW-069] — A new backend label is told what it costs before it resolves · held
- [`R-KNW-070`][R-KNW-070] — A relation value says which placeholder spelling survives it · held
- [`R-KNW-071`][R-KNW-071] — A clone is told when DDEV writes additional.php · held
- [`R-KNW-072`][R-KNW-072] — Which interpreter a covered version needs is answerable before anything is installed · held
- [`R-KNW-073`][R-KNW-073] — A step that reads from a cache says what invalidates it · held
- [`R-KNW-074`][R-KNW-074] — A prescribed command whose success is unconditional carries its discriminator · held
- [`R-KNW-075`][R-KNW-075] — A change to the core commit trailer rule is the maintainer's · **open**
- [`R-KNW-076`][R-KNW-076] — The two routes out of a generated additional.php are given in order · held

[R-KNW-001]: knowledge/knw-001-upgrade-wizards-and-dataprocessors-have-hints-of-their-own.md
[R-KNW-002]: knowledge/knw-002-a-hint-carries-the-words-its-subject-is-asked-about-in.md
[R-KNW-003]: knowledge/knw-003-a-hint-says-how-a-subsystem-is-used.md
[R-KNW-004]: knowledge/knw-004-an-authoring-answer-points-at-the-reading-side.md
[R-KNW-005]: knowledge/knw-005-where-a-mechanism-fails-silently-the-hint-names-the-failure.md
[R-KNW-006]: knowledge/knw-006-where-the-core-ships-the-example-the-answer-names-it.md
[R-KNW-007]: knowledge/knw-007-a-hint-that-ends-in-an-instruction-carries-its-steps.md
[R-KNW-008]: knowledge/knw-008-putting-own-records-on-a-page-is-covered-as-one-subject.md
[R-KNW-009]: knowledge/knw-009-registering-something-so-the-core-finds-it-is-covered.md
[R-KNW-010]: knowledge/knw-010-an-answer-says-which-half-of-typo3-it-is-for.md
[R-KNW-011]: knowledge/knw-011-extbase-is-covered-as-its-own-subject.md
[R-KNW-012]: knowledge/knw-012-whether-an-extension-is-part-of-the-core-is-answerable.md
[R-KNW-013]: knowledge/knw-013-a-statement-lives-in-the-category-it-is-asked-from.md
[R-KNW-014]: knowledge/knw-014-a-file-list-covers-the-one-on-its-way-out.md
[R-KNW-015]: knowledge/knw-015-building-the-test-harness-is-covered-as-its-own-subject.md
[R-KNW-016]: knowledge/knw-016-the-test-kind-that-needs-a-browser-is-covered.md
[R-KNW-017]: knowledge/knw-017-a-convention-read-off-the-core-carries-its-condition.md
[R-KNW-018]: knowledge/knw-018-where-an-artifact-can-be-verified-is-part-of-the-answer.md
[R-KNW-019]: knowledge/knw-019-the-cores-own-worked-examples-are-indexed.md
[R-KNW-020]: knowledge/knw-020-the-repository-around-the-extension-is-a-subject-of-its-own.md
[R-KNW-021]: knowledge/knw-021-a-hint-is-reachable-by-what-it-says.md
[R-KNW-022]: knowledge/knw-022-a-hint-is-a-candidate-for-the-question-it-is-asked-from.md
[R-KNW-023]: knowledge/knw-023-no-prose-document-dates-a-statement-in-its-sentence.md
[R-KNW-024]: knowledge/knw-024-a-check-is-offered-only-where-the-command-exists.md
[R-KNW-025]: knowledge/knw-025-the-two-site-local-settings-sources-carry-their-precedence.md
[R-KNW-026]: knowledge/knw-026-a-routed-argument-carries-its-cache-hash-boundary.md
[R-KNW-027]: knowledge/knw-027-ext-form-is-covered-as-a-subsystem.md
[R-KNW-028]: knowledge/knw-028-a-surviving-hook-is-named-by-the-subsystem-that-calls-it.md
[R-KNW-029]: knowledge/knw-029-a-core-only-convention-points-at-its-project-counterpart.md
[R-KNW-030]: knowledge/knw-030-a-non-english-site-reaches-its-label-language-setup.md
[R-KNW-031]: knowledge/knw-031-a-persisted-alias-is-answered-in-both-directions.md
[R-KNW-032]: knowledge/knw-032-project-configuration-states-who-owns-which-file.md
[R-KNW-033]: knowledge/knw-033-a-new-label-names-its-source-language.md
[R-KNW-034]: knowledge/knw-034-configuration-is-placed-by-the-reach-of-its-value.md
[R-KNW-035]: knowledge/knw-035-backend-module-guidance-continues-past-registration.md
[R-KNW-036]: knowledge/knw-036-label-reuse-stays-at-the-usage-context.md
[R-KNW-037]: knowledge/knw-037-a-distributed-extension-has-repository-conventions-of-its-own.md
[R-KNW-038]: knowledge/knw-038-a-missing-icon-identifier-has-no-matches.md
[R-KNW-039]: knowledge/knw-039-a-backend-module-in-a-sitepackage-stays-backend-module-work.md
[R-KNW-040]: knowledge/knw-040-an-environment-variable-answer-names-what-the-core-reads-itself.md
[R-KNW-041]: knowledge/knw-041-a-preview-template-answer-says-what-the-template-is-handed.md
[R-KNW-042]: knowledge/knw-042-a-preview-answer-says-what-the-default-renderer-already-draws.md
[R-KNW-043]: knowledge/knw-043-a-datamap-answer-says-what-the-parent-column-holds.md
[R-KNW-044]: knowledge/knw-044-the-fixture-rule-is-stated-with-the-empty-database-under-it.md
[R-KNW-045]: knowledge/knw-045-reading-records-is-covered-as-its-own-subject.md
[R-KNW-046]: knowledge/knw-046-impexp-is-the-way-a-page-tree-is-established-again.md
[R-KNW-047]: knowledge/knw-047-an-extension-copies-the-phpunit-xml-and-not-the-bootstrap.md
[R-KNW-048]: knowledge/knw-048-which-processor-claims-a-file-is-answered.md
[R-KNW-049]: knowledge/knw-049-a-check-that-can-pass-without-reading-anything-says-so.md
[R-KNW-050]: knowledge/knw-050-a-preview-answer-names-what-the-preview-draws-from.md
[R-KNW-051]: knowledge/knw-051-a-changelog-question-is-told-which-type-the-change-owes.md
[R-KNW-052]: knowledge/knw-052-the-suite-answer-names-the-install-a-fresh-checkout-owes.md
[R-KNW-053]: knowledge/knw-053-the-per-class-database-answer-says-what-survives-the-run.md
[R-KNW-054]: knowledge/knw-054-where-fal-stops-in-the-image-pipeline-is-answered.md
[R-KNW-055]: knowledge/knw-055-a-rendered-output-change-is-told-where-the-expectations-hide.md
[R-KNW-056]: knowledge/knw-056-the-placement-answer-names-the-document-root-as-a-place-a-script-may-not-go.md
[R-KNW-057]: knowledge/knw-057-the-push-a-session-cannot-take-back-is-answered-in-full.md
[R-KNW-058]: knowledge/knw-058-the-placement-answer-says-which-page-may-hold-the-record.md
[R-KNW-059]: knowledge/knw-059-a-change-is-told-which-cache-group-holds-its-old-output.md
[R-KNW-060]: knowledge/knw-060-the-project-configuration-answer-names-what-ddev-writes-and-what-it-cannot-configure.md
[R-KNW-061]: knowledge/knw-061-a-translation-file-is-told-what-a-missing-target-language-costs-it.md
[R-KNW-062]: knowledge/knw-062-the-import-answer-says-what-it-rewrites-in-a-site-configuration.md
[R-KNW-063]: knowledge/knw-063-a-template-answer-states-that-the-file-name-fallback-runs-once-per-root-path.md
[R-KNW-064]: knowledge/knw-064-the-composer-keys-that-install-typo3-beneath-an-extension-are-answered.md
[R-KNW-065]: knowledge/knw-065-booting-a-declared-installation-is-answered-as-its-own-subject.md
[R-KNW-066]: knowledge/knw-066-a-core-php-change-is-told-what-the-classs-public-surface-commits-it-to.md
[R-KNW-067]: knowledge/knw-067-the-e2e-answer-states-the-price-of-a-playwright-only-change.md
[R-KNW-068]: knowledge/knw-068-a-suite-that-waits-for-a-keypress-says-it-needs-a-terminal.md
[R-KNW-069]: knowledge/knw-069-a-new-backend-label-is-told-what-it-costs-before-it-resolves.md
[R-KNW-070]: knowledge/knw-070-a-relation-value-says-which-placeholder-spelling-survives-it.md
[R-KNW-071]: knowledge/knw-071-a-clone-is-told-when-ddev-writes-additional-php.md
[R-KNW-072]: knowledge/knw-072-which-interpreter-a-covered-version-needs-is-answerable-before-anything-is-installed.md
[R-KNW-073]: knowledge/knw-073-a-step-that-reads-from-a-cache-says-what-invalidates-it.md
[R-KNW-074]: knowledge/knw-074-a-prescribed-command-whose-success-is-unconditional-carries-its-discriminator.md
[R-KNW-075]: knowledge/knw-075-a-change-to-the-core-commit-trailer-rule-is-the-maintainers.md
[R-KNW-076]: knowledge/knw-076-the-two-routes-out-of-a-generated-additional-php-are-given-in-order.md

### code

- [`R-COD-001`][R-COD-001] — Every entrypoint is driven by a test that goes through it · held
- [`R-COD-002`][R-COD-002] — What the server ships is held to the prose rule · held
- [`R-COD-003`][R-COD-003] — A unit test holds a small part and stubs what is outside it · not guarded
- [`R-COD-004`][R-COD-004] — The versions this repository pins are checked against the day's release · not guarded

[R-COD-001]: code/cod-001-every-entrypoint-is-driven-by-a-test-that-goes-through-it.md
[R-COD-002]: code/cod-002-what-the-server-ships-is-held-to-the-prose-rule.md
[R-COD-003]: code/cod-003-a-unit-test-holds-a-small-part-and-stubs-what-is-outside-it.md
[R-COD-004]: code/cod-004-the-versions-this-repository-pins-are-checked-against-the-days-release.md
