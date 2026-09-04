# What was decided, and on what evidence

The working directory: one file per decision, in the group its id names. The
listing below and the one at the foot of each group's own `readme.md` are
written by `bin/cli decisions:index`.

What a decision is and what its states mean:
[documentation/records/decisions.rst](../documentation/records/decisions.rst).
Where an entry goes, how one is written and what a later session adds to it:
[documentation/records/writing-a-decision.rst](../documentation/records/writing-a-decision.rst),
which `bin/cli decisions:check` holds every file to.

## Every decision, by group

What is not listed as revoked still holds. `confirmed` marks the ones somebody
went back to and found standing; the rest are open, which is the ordinary case
and not a defect. What was decided lately is `bin/cli decisions:list`.

### audience

- [`D-AUD-017`][D-AUD-017] — Records are read and the boundary is the table they are in · 2026-09-01
- [`D-AUD-014`][D-AUD-014] — A description opens with what the caller's own route cannot do · 2026-08-27
- [`D-AUD-015`][D-AUD-015] — What decides whether to call a tool stands in its description · 2026-08-27
- [`D-AUD-012`][D-AUD-012] — The second call of the entry point is an imperative · 2026-08-19
- [`D-AUD-011`][D-AUD-011] — The instructions index the question each tool answers · 2026-08-18
- [`D-AUD-010`][D-AUD-010] — The content model is answered and the records stay with the installation · 2026-08-12
- [`D-AUD-009`][D-AUD-009] — The entry point claims patch work · 2026-08-08
- [`D-AUD-008`][D-AUD-008] — The server is called dev-companion · 2026-08-06 · confirmed
- [`D-AUD-005`][D-AUD-005] — An exclusion naming no tool is reported and the server starts · 2026-08-04
- [`D-AUD-006`][D-AUD-006] — The server reports the exclusion that happened · 2026-08-04
- [`D-AUD-007`][D-AUD-007] — The prose documents are named where a session already looks · 2026-08-04
- [`D-AUD-004`][D-AUD-004] — Every client is offered every tool · 2026-08-02
- [`D-AUD-003`][D-AUD-003] — The instructions carry the entry point, because the tool descriptions never arrive · 2026-07-31 · confirmed
- [`D-AUD-001`][D-AUD-001] — The outward description stays core-first until there is non-core knowledge · 2026-07-29 · confirmed

[D-AUD-017]: audience/aud-017-records-are-read-and-the-boundary-is-the-table-they-are-in.md
[D-AUD-014]: audience/aud-014-a-description-opens-with-what-the-callers-own-route-cannot-do.md
[D-AUD-015]: audience/aud-015-what-decides-whether-to-call-a-tool-stands-in-its-description.md
[D-AUD-012]: audience/aud-012-the-second-call-of-the-entry-point-is-an-imperative.md
[D-AUD-011]: audience/aud-011-the-instructions-index-the-question-each-tool-answers.md
[D-AUD-010]: audience/aud-010-the-content-model-is-answered-and-the-records-stay-with-the-installation.md
[D-AUD-009]: audience/aud-009-the-entry-point-claims-patch-work.md
[D-AUD-008]: audience/aud-008-the-server-is-called-dev-companion.md
[D-AUD-005]: audience/aud-005-an-exclusion-naming-no-tool-is-reported-and-the-server-starts.md
[D-AUD-006]: audience/aud-006-the-server-reports-the-exclusion-that-happened.md
[D-AUD-007]: audience/aud-007-the-prose-documents-are-named-where-a-session-already-looks.md
[D-AUD-004]: audience/aud-004-every-client-is-offered-every-tool.md
[D-AUD-003]: audience/aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md
[D-AUD-001]: audience/aud-001-the-outward-description-stays-core-first-until-there-is-non-core-knowledge.md

### discovery

- [`D-DIS-022`][D-DIS-022] — The schema the database has is answered and the rows are not · 2026-09-01
- [`D-DIS-023`][D-DIS-023] — The container is assembled a second time to be read · 2026-09-01
- [`D-DIS-021`][D-DIS-021] — A stale publication is put back where the server starts · 2026-08-29
- [`D-DIS-019`][D-DIS-019] — A project root is found from what its manifest declares · 2026-08-18
- [`D-DIS-017`][D-DIS-017] — The skills reach a project through the installer · 2026-08-12
- [`D-DIS-018`][D-DIS-018] — What `install` writes stays inside the project · 2026-08-12
- [`D-DIS-014`][D-DIS-014] — The refresh is wired by the project · 2026-08-08
- [`D-DIS-016`][D-DIS-016] — How an entrypoint may be named is a per-client question · 2026-08-08 · confirmed
- [`D-DIS-013`][D-DIS-013] — The record holds a digest of what was published · 2026-08-06
- [`D-DIS-011`][D-DIS-011] — What was read from the installation lives as long as the call · 2026-08-04
- [`D-DIS-012`][D-DIS-012] — The driver decides whether the derived columns need the database server · 2026-08-04
- [`D-DIS-010`][D-DIS-010] — What this package writes into a project ignores itself · 2026-08-03
- [`D-DIS-007`][D-DIS-007] — The DDEV console is named by the mount, not by the variable · 2026-08-02 · confirmed
- [`D-DIS-009`][D-DIS-009] — Installed is one step short of callable · 2026-08-02 · confirmed
- [`D-DIS-006`][D-DIS-006] — The installation stays worked out from the directory the server was started in · 2026-08-01
- [`D-DIS-005`][D-DIS-005] — A registry with no console command is read by booting the installation · 2026-07-31 · confirmed
- [`D-DIS-001`][D-DIS-001] — The root package counts as an installed package · 2026-07-29 · confirmed
- [`D-DIS-004`][D-DIS-004] — The version comes from the core package, not from the console · 2026-07-29 · confirmed

[D-DIS-022]: discovery/dis-022-the-schema-the-database-has-is-answered-and-the-rows-are-not.md
[D-DIS-023]: discovery/dis-023-the-container-is-assembled-a-second-time-to-be-read.md
[D-DIS-021]: discovery/dis-021-a-stale-publication-is-put-back-where-the-server-starts.md
[D-DIS-019]: discovery/dis-019-a-project-root-is-found-from-what-its-manifest-declares.md
[D-DIS-017]: discovery/dis-017-the-skills-reach-a-project-through-the-installer.md
[D-DIS-018]: discovery/dis-018-what-install-writes-stays-inside-the-project.md
[D-DIS-014]: discovery/dis-014-the-refresh-is-wired-by-the-project.md
[D-DIS-016]: discovery/dis-016-how-an-entrypoint-may-be-named-is-a-per-client-question.md
[D-DIS-013]: discovery/dis-013-the-record-holds-a-digest-of-what-was-published.md
[D-DIS-011]: discovery/dis-011-what-was-read-from-the-installation-lives-as-long-as-the-call.md
[D-DIS-012]: discovery/dis-012-the-driver-decides-whether-the-derived-columns-need-the-database-server.md
[D-DIS-010]: discovery/dis-010-what-this-package-writes-into-a-project-ignores-itself.md
[D-DIS-007]: discovery/dis-007-the-ddev-console-is-named-by-the-mount-not-by-the-variable.md
[D-DIS-009]: discovery/dis-009-installed-is-one-step-short-of-callable.md
[D-DIS-006]: discovery/dis-006-the-installation-stays-worked-out-from-the-directory-the-server-was-started-in.md
[D-DIS-005]: discovery/dis-005-a-registry-with-no-console-command-is-read-by-booting-the-installation.md
[D-DIS-001]: discovery/dis-001-the-root-package-counts-as-an-installed-package.md
[D-DIS-004]: discovery/dis-004-the-version-comes-from-the-core-package-not-from-the-console.md

### answers

- [`D-ANS-142`][D-ANS-142] — The area a word names is resolved from the extension it is the key of · 2026-09-04
- [`D-ANS-143`][D-ANS-143] — What a tool refuses is said to the caller that sent it · 2026-09-04
- [`D-ANS-144`][D-ANS-144] — A declared property is reached by its own name · 2026-09-04
- [`D-ANS-145`][D-ANS-145] — The answer that hands over a command carries what a run can take · 2026-09-04
- [`D-ANS-132`][D-ANS-132] — The domain answer carries the form a module imports · 2026-09-02
- [`D-ANS-133`][D-ANS-133] — A describe field is a file's own declaration · 2026-09-02
- [`D-ANS-135`][D-ANS-135] — The reference scan is asked for the resources an answer names · 2026-09-02
- [`D-ANS-136`][D-ANS-136] — The Fluid namespace source is chosen by the installed version · 2026-09-02
- [`D-ANS-137`][D-ANS-137] — Three argument names meant two things each · 2026-09-02
- [`D-ANS-138`][D-ANS-138] — The hint corpus dilutes its outliers as it grows · 2026-09-02
- [`D-ANS-139`][D-ANS-139] — Three tools hand over less than they read · 2026-09-02
- [`D-ANS-140`][D-ANS-140] — A kind of work names the procedure it owes · 2026-09-02
- [`D-ANS-141`][D-ANS-141] — A distribution is one call rather than one per value · 2026-09-02
- [`D-ANS-127`][D-ANS-127] — Knowledge that yields a judgement is delivered where the work finishes · 2026-09-01
- [`D-ANS-128`][D-ANS-128] — An answer's index buys recovery and its foot is read last · 2026-09-01
- [`D-ANS-129`][D-ANS-129] — A list in an answer names the call it raises · 2026-09-01
- [`D-ANS-130`][D-ANS-130] — A hint answer says how much of the question it carries · 2026-09-01
- [`D-ANS-131`][D-ANS-131] — An icon answer says whose picture the identifier already is · 2026-09-01
- [`D-ANS-134`][D-ANS-134] — Static label references warn and never hide a resource · 2026-09-01
- [`D-ANS-126`][D-ANS-126] — The runnable form of a declared command is a field · 2026-08-28
- [`D-ANS-114`][D-ANS-114] — A page read whole names the hints it declares · 2026-08-27
- [`D-ANS-115`][D-ANS-115] — A phrasing a requirement rests on is carried by the hint's own vocabulary · 2026-08-27
- [`D-ANS-116`][D-ANS-116] — A duplicate check reads the recent end of the backlog · 2026-08-27
- [`D-ANS-117`][D-ANS-117] — The commit draft names the workflow that owns the commit · 2026-08-27
- [`D-ANS-118`][D-ANS-118] — A permalink identifier is resolved from the inventories the manual lookup already reads · 2026-08-27
- [`D-ANS-119`][D-ANS-119] — A permalink identifier is every inventory name that is not a page · 2026-08-27
- [`D-ANS-120`][D-ANS-120] — A manual is reached by a listed shortcode or by a core package name · 2026-08-27
- [`D-ANS-121`][D-ANS-121] — A change answer says whether its patch set carries conflict markers · 2026-08-27
- [`D-ANS-122`][D-ANS-122] — A backlog row says whether the code its report cites is still there · 2026-08-27
- [`D-ANS-123`][D-ANS-123] — An issue answer carries the issues its prose cites · 2026-08-27
- [`D-ANS-124`][D-ANS-124] — A first-hit assertion rests on the hint's own vocabulary · 2026-08-27
- [`D-ANS-125`][D-ANS-125] — One reviews field means the changes the review server holds · 2026-08-27
- [`D-ANS-110`][D-ANS-110] — A changelog miss with no re-query names the manual and the rules · 2026-08-26
- [`D-ANS-111`][D-ANS-111] — A change answer says which comment thread is open · 2026-08-26
- [`D-ANS-112`][D-ANS-112] — A change answer establishes the patch without a fetch · 2026-08-26
- [`D-ANS-113`][D-ANS-113] — A suite whose mark warns the caller off names what answers its question instead · 2026-08-26
- [`D-ANS-105`][D-ANS-105] — The unsupported answer says what would make it answerable · 2026-08-25
- [`D-ANS-106`][D-ANS-106] — A commit in a checkout is a handle the review lookup takes · 2026-08-25
- [`D-ANS-107`][D-ANS-107] — The review backlog is enumerated the way the tracker is · 2026-08-25 · confirmed
- [`D-ANS-108`][D-ANS-108] — A suite the pre-merge pipeline gates is a base check of its domain · 2026-08-25
- [`D-ANS-109`][D-ANS-109] — The backlog names what one person could still review · 2026-08-25
- [`D-ANS-097`][D-ANS-097] — A bounded answer spends its slots on the hints that bind its caller · 2026-08-24
- [`D-ANS-098`][D-ANS-098] — A change answer names the issues its commit message resolves · 2026-08-24
- [`D-ANS-099`][D-ANS-099] — A suite that stages the working tree is offered marked rather than withheld · 2026-08-24
- [`D-ANS-100`][D-ANS-100] — The review server is searched by words and by path · 2026-08-24
- [`D-ANS-101`][D-ANS-101] — A concentrated search is more than one match · 2026-08-24
- [`D-ANS-102`][D-ANS-102] — The project answer says whether the installed tree matches the lock · 2026-08-24
- [`D-ANS-103`][D-ANS-103] — An id an answer names carries the URL that reaches it · 2026-08-24
- [`D-ANS-104`][D-ANS-104] — The maintained release lines are placed where a task names a branch · 2026-08-24
- [`D-ANS-096`][D-ANS-096] — An outside source is read in the form it publishes · 2026-08-23 · confirmed
- [`D-ANS-091`][D-ANS-091] — The project answer leaves the second call to the instructions · 2026-08-21
- [`D-ANS-092`][D-ANS-092] — The project answer says how its declared suites are run · 2026-08-21
- [`D-ANS-093`][D-ANS-093] — A major's deprecations come back in one call · 2026-08-21
- [`D-ANS-094`][D-ANS-094] — A change answer names the relation chain the change sits in · 2026-08-21
- [`D-ANS-095`][D-ANS-095] — A flex field is answered with the data structure the installation resolves · 2026-08-21
- [`D-ANS-086`][D-ANS-086] — The project answer carries the bound that stops a command · 2026-08-19
- [`D-ANS-087`][D-ANS-087] — The project answer stays whole because a call is what costs · 2026-08-19
- [`D-ANS-088`][D-ANS-088] — The orientation answer is asked for by section · 2026-08-19
- [`D-ANS-089`][D-ANS-089] — A person is a filter on the backlog · 2026-08-19 · confirmed
- [`D-ANS-090`][D-ANS-090] — A matched set larger than a page is answered by its shape · 2026-08-19
- [`D-ANS-082`][D-ANS-082] — The project answer states how its three PHP numbers relate · 2026-08-18
- [`D-ANS-084`][D-ANS-084] — A curated phrase crosses the domain gate · 2026-08-18
- [`D-ANS-085`][D-ANS-085] — The project answer is read from the repository's own files · 2026-08-18
- [`D-ANS-083`][D-ANS-083] — The unsupported answer is the whole diagnostic · 2026-08-17
- [`D-ANS-079`][D-ANS-079] — A change answer carries its votes and its comments · 2026-08-14 · confirmed
- [`D-ANS-080`][D-ANS-080] — A change answer names the siblings that share its Change-Id · 2026-08-14
- [`D-ANS-077`][D-ANS-077] — The module answer carries the resolved navigation component and each module's routes · 2026-08-12
- [`D-ANS-078`][D-ANS-078] — The icon lookup validates a list of identifiers in one call · 2026-08-12
- [`D-ANS-074`][D-ANS-074] — A path-narrowed suite list names what it withheld · 2026-08-11
- [`D-ANS-075`][D-ANS-075] — The hint index is ordered by the rank the matcher already computed · 2026-08-11
- [`D-ANS-076`][D-ANS-076] — A search matching one page answers with the page · 2026-08-11
- [`D-ANS-071`][D-ANS-071] — The environment answer names the project and what its files serve · 2026-08-10
- [`D-ANS-072`][D-ANS-072] — A tool description says which questions it takes · 2026-08-10
- [`D-ANS-073`][D-ANS-073] — What can take a patch and where this one goes are two readings · 2026-08-10
- [`D-ANS-068`][D-ANS-068] — A change answer carries the ref that fetches the patch set · 2026-08-09
- [`D-ANS-070`][D-ANS-070] — A document is handed over by the call that reads it · 2026-08-09
- [`D-ANS-064`][D-ANS-064] — An issue answer holds what a triage needs · 2026-08-08
- [`D-ANS-065`][D-ANS-065] — The manual index is the inventory each manual publishes · 2026-08-08
- [`D-ANS-066`][D-ANS-066] — One handle serves every read of one Fetch · 2026-08-08
- [`D-ANS-067`][D-ANS-067] — The changelog above the installed major comes from the manual · 2026-08-08
- [`D-ANS-069`][D-ANS-069] — A backlog row carries the review server and not the journal · 2026-08-08
- [`D-ANS-060`][D-ANS-060] — A bare word in `appliesTo` reaches a path segment · 2026-08-07
- [`D-ANS-061`][D-ANS-061] — An answer that names a document hands it over · 2026-08-07
- [`D-ANS-062`][D-ANS-062] — An anonymous read cannot tell a restricted change from an absent one · 2026-08-07
- [`D-ANS-063`][D-ANS-063] — An option list is what the caller did not know to ask for · 2026-08-07
- [`D-ANS-054`][D-ANS-054] — The backlog is a third way into the tracker · 2026-08-05 · confirmed
- [`D-ANS-055`][D-ANS-055] — A change answers for an issue its commit message names · 2026-08-05
- [`D-ANS-056`][D-ANS-056] — A search hit is filled from the issue it is · 2026-08-05
- [`D-ANS-057`][D-ANS-057] — What hangs off an issue is named · 2026-08-05
- [`D-ANS-058`][D-ANS-058] — The release lines a trailer claims are a lookup · 2026-08-05 · confirmed
- [`D-ANS-059`][D-ANS-059] — What a session named as load-bearing is kept · 2026-08-05
- [`D-ANS-048`][D-ANS-048] — A tool declares what can answer it, and both readers render that · 2026-08-04
- [`D-ANS-049`][D-ANS-049] — An answer from outside is held where the caller cannot change it · 2026-08-04
- [`D-ANS-050`][D-ANS-050] — A curated needle matches the word it is · 2026-08-04
- [`D-ANS-051`][D-ANS-051] — A manual result carries how much of the question it covers · 2026-08-04
- [`D-ANS-052`][D-ANS-052] — The configuration lookup answers for the installation as it stands · 2026-08-04
- [`D-ANS-053`][D-ANS-053] — A rejected call names the argument that was not understood · 2026-08-04
- [`D-ANS-033`][D-ANS-033] — The review server is read anonymously · 2026-08-03 · confirmed
- [`D-ANS-035`][D-ANS-035] — The matcher entry is owed to what the changelog tag claims · 2026-08-03 · confirmed
- [`D-ANS-036`][D-ANS-036] — A query in Fluid tags is searched in the book for them · 2026-08-03
- [`D-ANS-037`][D-ANS-037] — A compound rule query is owed the section its score prefers · 2026-08-03
- [`D-ANS-038`][D-ANS-038] — The tracker is searched by words as well as read by number · 2026-08-03
- [`D-ANS-039`][D-ANS-039] — The Extbase fork is delivered by the content-element intent · 2026-08-03
- [`D-ANS-040`][D-ANS-040] — A boundary guard is asked with a query that clears the floor · 2026-08-03
- [`D-ANS-041`][D-ANS-041] — The changelog title is read where the file names carry nothing · 2026-08-03
- [`D-ANS-042`][D-ANS-042] — An identifier reaches the changelog entries whose body names it · 2026-08-03
- [`D-ANS-043`][D-ANS-043] — A miss is answered in data · 2026-08-03
- [`D-ANS-044`][D-ANS-044] — The environment answer carries the lifecycle it declares · 2026-08-03
- [`D-ANS-045`][D-ANS-045] — The Classes section reports every directory below it · 2026-08-03
- [`D-ANS-046`][D-ANS-046] — A manual result covers the question it is returned for · 2026-08-03
- [`D-ANS-047`][D-ANS-047] — A word behind a namespace prefix is searched as itself · 2026-08-03
- [`D-ANS-005`][D-ANS-005] — An unsupported question is answered in a shape of its own · 2026-08-02
- [`D-ANS-006`][D-ANS-006] — An identifier is found however it is spelled · 2026-08-02
- [`D-ANS-007`][D-ANS-007] — `unsupported` and `unavailable` are two answers, and `cause` says why · 2026-08-02 · confirmed
- [`D-ANS-008`][D-ANS-008] — A number a reader cannot reproduce is read as wrong · 2026-08-02
- [`D-ANS-009`][D-ANS-009] — A shipped-file deprecation is found by the tool that lists the file · 2026-08-02 · confirmed
- [`D-ANS-010`][D-ANS-010] — "Does it still work" is a question for the manual, not the changelog · 2026-08-02
- [`D-ANS-011`][D-ANS-011] — A scope answer states what a manifest declares · 2026-08-02
- [`D-ANS-012`][D-ANS-012] — An `oneOf` alternative is stated where the caller composes the call · 2026-08-02
- [`D-ANS-013`][D-ANS-013] — What runs a project is a placement, not a missing answer · 2026-08-02
- [`D-ANS-014`][D-ANS-014] — The extension answer enumerates registrations, not files · 2026-08-02
- [`D-ANS-015`][D-ANS-015] — A registration the extension answer misreads is inside its boundary · 2026-08-02
- [`D-ANS-016`][D-ANS-016] — A miss names the query that would have hit · 2026-08-02
- [`D-ANS-017`][D-ANS-017] — A union-typed argument gets the wording a client can compose against · 2026-08-02
- [`D-ANS-018`][D-ANS-018] — A plugin is a kind of content element · 2026-08-02 · confirmed
- [`D-ANS-019`][D-ANS-019] — Three registration kinds are read the way core reads them · 2026-08-02 · confirmed
- [`D-ANS-020`][D-ANS-020] — A deprecation is answered by the version that removes it · 2026-08-02
- [`D-ANS-021`][D-ANS-021] — The manual lookup says why a short query ranks better · 2026-08-02
- [`D-ANS-022`][D-ANS-022] — The matcher takes a hyphenated compound apart, measured over the corpus first · 2026-08-02
- [`D-ANS-024`][D-ANS-024] — A rule reaches only the task that already names its subject · 2026-08-02
- [`D-ANS-025`][D-ANS-025] — A query a hint carries whole is not diluted out of it · 2026-08-02 · confirmed
- [`D-ANS-026`][D-ANS-026] — The ViewHelper reference is indexed · 2026-08-02
- [`D-ANS-028`][D-ANS-028] — A two-letter query word is searched for · 2026-08-02
- [`D-ANS-029`][D-ANS-029] — The scanner matcher is stated on the route a removal takes · 2026-08-02 · confirmed
- [`D-ANS-030`][D-ANS-030] — The changelog matcher runs over the title it prints · 2026-08-02
- [`D-ANS-031`][D-ANS-031] — The core answer names the tool that runs the suites · 2026-08-02
- [`D-ANS-032`][D-ANS-032] — The manual ranking is diluted by an ordinary title's length · 2026-08-02
- [`D-ANS-004`][D-ANS-004] — The instruction budget is 2048 characters, on one client's evidence · 2026-07-31
- [`D-ANS-002`][D-ANS-002] — Rarity, field length and corpus length decide a lookup's rank · 2026-07-30 · confirmed
- [`D-ANS-003`][D-ANS-003] — Retrieval stays lexical and runtime inspection stays narrow · 2026-07-30 · confirmed

[D-ANS-142]: answers/ans-142-the-area-a-word-names-is-resolved-from-the-extension-it-is-the-key-of.md
[D-ANS-143]: answers/ans-143-what-a-tool-refuses-is-said-to-the-caller-that-sent-it.md
[D-ANS-144]: answers/ans-144-a-declared-property-is-reached-by-its-own-name.md
[D-ANS-145]: answers/ans-145-the-answer-that-hands-over-a-command-carries-what-a-run-can-take.md
[D-ANS-132]: answers/ans-132-the-domain-answer-carries-the-form-a-module-imports.md
[D-ANS-133]: answers/ans-133-a-describe-field-is-a-files-own-declaration.md
[D-ANS-135]: answers/ans-135-the-reference-scan-is-asked-for-the-resources-an-answer-names.md
[D-ANS-136]: answers/ans-136-the-fluid-namespace-source-is-chosen-by-the-installed-version.md
[D-ANS-137]: answers/ans-137-three-argument-names-meant-two-things-each.md
[D-ANS-138]: answers/ans-138-the-hint-corpus-dilutes-its-outliers-as-it-grows.md
[D-ANS-139]: answers/ans-139-three-tools-hand-over-less-than-they-read.md
[D-ANS-140]: answers/ans-140-a-kind-of-work-names-the-procedure-it-owes.md
[D-ANS-141]: answers/ans-141-a-distribution-is-one-call-rather-than-one-per-value.md
[D-ANS-127]: answers/ans-127-knowledge-that-yields-a-judgement-is-delivered-where-the-work-finishes.md
[D-ANS-128]: answers/ans-128-an-answers-index-buys-recovery-and-its-foot-is-read-last.md
[D-ANS-129]: answers/ans-129-a-list-in-an-answer-names-the-call-it-raises.md
[D-ANS-130]: answers/ans-130-a-hint-answer-says-how-much-of-the-question-it-carries.md
[D-ANS-131]: answers/ans-131-an-icon-answer-says-whose-picture-the-identifier-already-is.md
[D-ANS-134]: answers/ans-134-static-label-references-warn-and-never-hide-a-resource.md
[D-ANS-126]: answers/ans-126-the-runnable-form-of-a-declared-command-is-a-field.md
[D-ANS-114]: answers/ans-114-a-page-read-whole-names-the-hints-it-declares.md
[D-ANS-115]: answers/ans-115-a-phrasing-a-requirement-rests-on-is-carried-by-the-hints-own-vocabulary.md
[D-ANS-116]: answers/ans-116-a-duplicate-check-reads-the-recent-end-of-the-backlog.md
[D-ANS-117]: answers/ans-117-the-commit-draft-names-the-workflow-that-owns-the-commit.md
[D-ANS-118]: answers/ans-118-a-permalink-identifier-is-resolved-from-the-inventories-the-manual-lookup-already-reads.md
[D-ANS-119]: answers/ans-119-a-permalink-identifier-is-every-inventory-name-that-is-not-a-page.md
[D-ANS-120]: answers/ans-120-a-manual-is-reached-by-a-listed-shortcode-or-by-a-core-package-name.md
[D-ANS-121]: answers/ans-121-a-change-answer-says-whether-its-patch-set-carries-conflict-markers.md
[D-ANS-122]: answers/ans-122-a-backlog-row-says-whether-the-code-its-report-cites-is-still-there.md
[D-ANS-123]: answers/ans-123-an-issue-answer-carries-the-issues-its-prose-cites.md
[D-ANS-124]: answers/ans-124-a-first-hit-assertion-rests-on-the-hints-own-vocabulary.md
[D-ANS-125]: answers/ans-125-one-reviews-field-means-the-changes-the-review-server-holds.md
[D-ANS-110]: answers/ans-110-a-changelog-miss-with-no-re-query-names-the-manual-and-the-rules.md
[D-ANS-111]: answers/ans-111-a-change-answer-says-which-comment-thread-is-open.md
[D-ANS-112]: answers/ans-112-a-change-answer-establishes-the-patch-without-a-fetch.md
[D-ANS-113]: answers/ans-113-a-suite-whose-mark-warns-the-caller-off-names-what-answers-its-question-instead.md
[D-ANS-105]: answers/ans-105-the-unsupported-answer-says-what-would-make-it-answerable.md
[D-ANS-106]: answers/ans-106-a-commit-in-a-checkout-is-a-handle-the-review-lookup-takes.md
[D-ANS-107]: answers/ans-107-the-review-backlog-is-enumerated-the-way-the-tracker-is.md
[D-ANS-108]: answers/ans-108-a-suite-the-pre-merge-pipeline-gates-is-a-base-check-of-its-domain.md
[D-ANS-109]: answers/ans-109-the-backlog-names-what-one-person-could-still-review.md
[D-ANS-097]: answers/ans-097-a-bounded-answer-spends-its-slots-on-the-hints-that-bind-its-caller.md
[D-ANS-098]: answers/ans-098-a-change-answer-names-the-issues-its-commit-message-resolves.md
[D-ANS-099]: answers/ans-099-a-suite-that-stages-the-working-tree-is-offered-marked-rather-than-withheld.md
[D-ANS-100]: answers/ans-100-the-review-server-is-searched-by-words-and-by-path.md
[D-ANS-101]: answers/ans-101-a-concentrated-search-is-more-than-one-match.md
[D-ANS-102]: answers/ans-102-the-project-answer-says-whether-the-installed-tree-matches-the-lock.md
[D-ANS-103]: answers/ans-103-an-id-an-answer-names-carries-the-url-that-reaches-it.md
[D-ANS-104]: answers/ans-104-the-maintained-release-lines-are-placed-where-a-task-names-a-branch.md
[D-ANS-096]: answers/ans-096-an-outside-source-is-read-in-the-form-it-publishes.md
[D-ANS-091]: answers/ans-091-the-project-answer-leaves-the-second-call-to-the-instructions.md
[D-ANS-092]: answers/ans-092-the-project-answer-says-how-its-declared-suites-are-run.md
[D-ANS-093]: answers/ans-093-a-majors-deprecations-come-back-in-one-call.md
[D-ANS-094]: answers/ans-094-a-change-answer-names-the-relation-chain-the-change-sits-in.md
[D-ANS-095]: answers/ans-095-a-flex-field-is-answered-with-the-data-structure-the-installation-resolves.md
[D-ANS-086]: answers/ans-086-the-project-answer-carries-the-bound-that-stops-a-command.md
[D-ANS-087]: answers/ans-087-the-project-answer-stays-whole-because-a-call-is-what-costs.md
[D-ANS-088]: answers/ans-088-the-orientation-answer-is-asked-for-by-section.md
[D-ANS-089]: answers/ans-089-a-person-is-a-filter-on-the-backlog.md
[D-ANS-090]: answers/ans-090-a-matched-set-larger-than-a-page-is-answered-by-its-shape.md
[D-ANS-082]: answers/ans-082-the-project-answer-states-how-its-three-php-numbers-relate.md
[D-ANS-084]: answers/ans-084-a-curated-phrase-crosses-the-domain-gate.md
[D-ANS-085]: answers/ans-085-the-project-answer-is-read-from-the-repositorys-own-files.md
[D-ANS-083]: answers/ans-083-the-unsupported-answer-is-the-whole-diagnostic.md
[D-ANS-079]: answers/ans-079-a-change-answer-carries-its-votes-and-its-comments.md
[D-ANS-080]: answers/ans-080-a-change-answer-names-the-siblings-that-share-its-change-id.md
[D-ANS-077]: answers/ans-077-the-module-answer-carries-the-resolved-navigation-component-and-each-modules-routes.md
[D-ANS-078]: answers/ans-078-the-icon-lookup-validates-a-list-of-identifiers-in-one-call.md
[D-ANS-074]: answers/ans-074-a-path-narrowed-suite-list-names-what-it-withheld.md
[D-ANS-075]: answers/ans-075-the-hint-index-is-ordered-by-the-rank-the-matcher-already-computed.md
[D-ANS-076]: answers/ans-076-a-search-matching-one-page-answers-with-the-page.md
[D-ANS-071]: answers/ans-071-the-environment-answer-names-the-project-and-what-its-files-serve.md
[D-ANS-072]: answers/ans-072-a-tool-description-says-which-questions-it-takes.md
[D-ANS-073]: answers/ans-073-what-can-take-a-patch-and-where-this-one-goes-are-two-readings.md
[D-ANS-068]: answers/ans-068-a-change-answer-carries-the-ref-that-fetches-the-patch-set.md
[D-ANS-070]: answers/ans-070-a-document-is-handed-over-by-the-call-that-reads-it.md
[D-ANS-064]: answers/ans-064-an-issue-answer-holds-what-a-triage-needs.md
[D-ANS-065]: answers/ans-065-the-manual-index-is-the-inventory-each-manual-publishes.md
[D-ANS-066]: answers/ans-066-one-handle-serves-every-read-of-one-fetch.md
[D-ANS-067]: answers/ans-067-the-changelog-above-the-installed-major-comes-from-the-manual.md
[D-ANS-069]: answers/ans-069-a-backlog-row-carries-the-review-server-and-not-the-journal.md
[D-ANS-060]: answers/ans-060-a-bare-word-in-appliesto-reaches-a-path-segment.md
[D-ANS-061]: answers/ans-061-an-answer-that-names-a-document-hands-it-over.md
[D-ANS-062]: answers/ans-062-an-anonymous-read-cannot-tell-a-restricted-change-from-an-absent-one.md
[D-ANS-063]: answers/ans-063-an-option-list-is-what-the-caller-did-not-know-to-ask-for.md
[D-ANS-054]: answers/ans-054-the-backlog-is-a-third-way-into-the-tracker.md
[D-ANS-055]: answers/ans-055-a-change-answers-for-an-issue-its-commit-message-names.md
[D-ANS-056]: answers/ans-056-a-search-hit-is-filled-from-the-issue-it-is.md
[D-ANS-057]: answers/ans-057-what-hangs-off-an-issue-is-named.md
[D-ANS-058]: answers/ans-058-the-release-lines-a-trailer-claims-are-a-lookup.md
[D-ANS-059]: answers/ans-059-what-a-session-named-as-load-bearing-is-kept.md
[D-ANS-048]: answers/ans-048-a-tool-declares-what-can-answer-it-and-both-readers-render-that.md
[D-ANS-049]: answers/ans-049-an-answer-from-outside-is-held-where-the-caller-cannot-change-it.md
[D-ANS-050]: answers/ans-050-a-curated-needle-matches-the-word-it-is.md
[D-ANS-051]: answers/ans-051-a-manual-result-carries-how-much-of-the-question-it-covers.md
[D-ANS-052]: answers/ans-052-the-configuration-lookup-answers-for-the-installation-as-it-stands.md
[D-ANS-053]: answers/ans-053-a-rejected-call-names-the-argument-that-was-not-understood.md
[D-ANS-033]: answers/ans-033-the-review-server-is-read-anonymously.md
[D-ANS-035]: answers/ans-035-the-matcher-entry-is-owed-to-what-the-changelog-tag-claims.md
[D-ANS-036]: answers/ans-036-a-query-in-fluid-tags-is-searched-in-the-book-for-them.md
[D-ANS-037]: answers/ans-037-a-compound-rule-query-is-owed-the-section-its-score-prefers.md
[D-ANS-038]: answers/ans-038-the-tracker-is-searched-by-words-as-well-as-read-by-number.md
[D-ANS-039]: answers/ans-039-the-extbase-fork-is-delivered-by-the-content-element-intent.md
[D-ANS-040]: answers/ans-040-a-boundary-guard-is-asked-with-a-query-that-clears-the-floor.md
[D-ANS-041]: answers/ans-041-the-changelog-title-is-read-where-the-file-names-carry-nothing.md
[D-ANS-042]: answers/ans-042-an-identifier-reaches-the-changelog-entries-whose-body-names-it.md
[D-ANS-043]: answers/ans-043-a-miss-is-answered-in-data.md
[D-ANS-044]: answers/ans-044-the-environment-answer-carries-the-lifecycle-it-declares.md
[D-ANS-045]: answers/ans-045-the-classes-section-reports-every-directory-below-it.md
[D-ANS-046]: answers/ans-046-a-manual-result-covers-the-question-it-is-returned-for.md
[D-ANS-047]: answers/ans-047-a-word-behind-a-namespace-prefix-is-searched-as-itself.md
[D-ANS-005]: answers/ans-005-an-unsupported-question-is-answered-in-a-shape-of-its-own.md
[D-ANS-006]: answers/ans-006-an-identifier-is-found-however-it-is-spelled.md
[D-ANS-007]: answers/ans-007-unsupported-and-unavailable-are-two-answers-and-cause-says-why.md
[D-ANS-008]: answers/ans-008-a-number-a-reader-cannot-reproduce-is-read-as-wrong.md
[D-ANS-009]: answers/ans-009-a-shipped-file-deprecation-is-found-by-the-tool-that-lists-the-file.md
[D-ANS-010]: answers/ans-010-does-it-still-work-is-a-question-for-the-manual-not-the-changelog.md
[D-ANS-011]: answers/ans-011-a-scope-answer-states-what-a-manifest-declares.md
[D-ANS-012]: answers/ans-012-an-oneof-alternative-is-stated-where-the-caller-composes-the-call.md
[D-ANS-013]: answers/ans-013-what-runs-a-project-is-a-placement-not-a-missing-answer.md
[D-ANS-014]: answers/ans-014-the-extension-answer-enumerates-registrations-not-files.md
[D-ANS-015]: answers/ans-015-a-registration-the-extension-answer-misreads-is-inside-its-boundary.md
[D-ANS-016]: answers/ans-016-a-miss-names-the-query-that-would-have-hit.md
[D-ANS-017]: answers/ans-017-a-union-typed-argument-gets-the-wording-a-client-can-compose-against.md
[D-ANS-018]: answers/ans-018-a-plugin-is-a-kind-of-content-element.md
[D-ANS-019]: answers/ans-019-three-registration-kinds-are-read-the-way-core-reads-them.md
[D-ANS-020]: answers/ans-020-a-deprecation-is-answered-by-the-version-that-removes-it.md
[D-ANS-021]: answers/ans-021-the-manual-lookup-says-why-a-short-query-ranks-better.md
[D-ANS-022]: answers/ans-022-the-matcher-takes-a-hyphenated-compound-apart-measured-over-the-corpus-first.md
[D-ANS-024]: answers/ans-024-a-rule-reaches-only-the-task-that-already-names-its-subject.md
[D-ANS-025]: answers/ans-025-a-query-a-hint-carries-whole-is-not-diluted-out-of-it.md
[D-ANS-026]: answers/ans-026-the-viewhelper-reference-is-indexed.md
[D-ANS-028]: answers/ans-028-a-two-letter-query-word-is-searched-for.md
[D-ANS-029]: answers/ans-029-the-scanner-matcher-is-stated-on-the-route-a-removal-takes.md
[D-ANS-030]: answers/ans-030-the-changelog-matcher-runs-over-the-title-it-prints.md
[D-ANS-031]: answers/ans-031-the-core-answer-names-the-tool-that-runs-the-suites.md
[D-ANS-032]: answers/ans-032-the-manual-ranking-is-diluted-by-an-ordinary-titles-length.md
[D-ANS-004]: answers/ans-004-the-instruction-budget-is-2048-characters-on-one-clients-evidence.md
[D-ANS-002]: answers/ans-002-rarity-field-length-and-corpus-length-decide-a-lookups-rank.md
[D-ANS-003]: answers/ans-003-retrieval-stays-lexical-and-runtime-inspection-stays-narrow.md

### knowledge

- [`D-KNW-147`][D-KNW-147] — A list of what is supported says what an unsupported key does · 2026-09-04
- [`D-KNW-148`][D-KNW-148] — What the schema of one record type holds is a subject this server owns · 2026-09-04
- [`D-KNW-139`][D-KNW-139] — The corpus states where an annotation is written · 2026-09-02
- [`D-KNW-140`][D-KNW-140] — The corpus states a check the core does not run · 2026-09-02
- [`D-KNW-141`][D-KNW-141] — The shape of a patch is stated where a patch is judged · 2026-09-02
- [`D-KNW-142`][D-KNW-142] — A test is named for what holds rather than for the issue · 2026-09-02
- [`D-KNW-143`][D-KNW-143] — Output a pipeline determines whole is asserted whole · 2026-09-02
- [`D-KNW-144`][D-KNW-144] — The boundary of an authorised rework is stated where a change is made · 2026-09-02
- [`D-KNW-145`][D-KNW-145] — A curated phrase crosses the gate whatever punctuation it carries · 2026-09-02
- [`D-KNW-146`][D-KNW-146] — The Fluid engine is kept beside the core checkouts · 2026-09-02
- [`D-KNW-137`][D-KNW-137] — A relaunch is a kind of work rather than a hint beside one · 2026-09-01
- [`D-KNW-138`][D-KNW-138] — A hint names its next call in a statement · 2026-09-01
- [`D-KNW-133`][D-KNW-133] — A guide's whenToUse names the answer it hands over · 2026-08-28
- [`D-KNW-134`][D-KNW-134] — What the functional harness does to the working directory is stated · 2026-08-28
- [`D-KNW-135`][D-KNW-135] — A condition attribute carries its quoting and its zero · 2026-08-28
- [`D-KNW-136`][D-KNW-136] — A fixture's sys_template row discards what the site's sets built · 2026-08-28
- [`D-KNW-126`][D-KNW-126] — The syntax floor a core patch is bound by is a subject this server owns · 2026-08-27
- [`D-KNW-127`][D-KNW-127] — How a backend web component surfaces a failed load is a subject this server owns · 2026-08-27
- [`D-KNW-128`][D-KNW-128] — Building a link into the official documentation is a subject this server owns · 2026-08-27
- [`D-KNW-129`][D-KNW-129] — Opening a patch set on somebody else's change is a subject this server owns · 2026-08-27
- [`D-KNW-130`][D-KNW-130] — What the JavaScript unit layer can reach is stated in the corpus · 2026-08-27
- [`D-KNW-131`][D-KNW-131] — The author of a change survives every amend somebody else makes · 2026-08-27 · confirmed
- [`D-KNW-132`][D-KNW-132] — The changelog directory is stated where the file is written · 2026-08-27
- [`D-KNW-118`][D-KNW-118] — How a development installation renders a package that ships no page TypoScript is a subject this server owns · 2026-08-25
- [`D-KNW-119`][D-KNW-119] — The corpus tells apart the failures one usage synopsis presents alike · 2026-08-25 · confirmed
- [`D-KNW-120`][D-KNW-120] — A hint that states a merge names the lookup that reads the result · 2026-08-25
- [`D-KNW-121`][D-KNW-121] — What registering an argument costs a tag-based ViewHelper is a subject this server owns · 2026-08-25
- [`D-KNW-122`][D-KNW-122] — A procedure document is routed by the evidence a task needs · 2026-08-25
- [`D-KNW-123`][D-KNW-123] — The corpus tells a widened signature apart from a widened visibility · 2026-08-25
- [`D-KNW-124`][D-KNW-124] — Frontend render pipeline state is a gap this server owns · 2026-08-25
- [`D-KNW-125`][D-KNW-125] — A core commit message carries four trailers and the hook's Change-Id · 2026-08-25
- [`D-KNW-107`][D-KNW-107] — Which side of a backend module resolves a resource path is a subject this server owns · 2026-08-24 · confirmed
- [`D-KNW-108`][D-KNW-108] — Where an impexp import puts the records it writes is a subject this server owns · 2026-08-24
- [`D-KNW-111`][D-KNW-111] — The changelog procedure is a guide of its own · 2026-08-24
- [`D-KNW-112`][D-KNW-112] — The invocation notes say where runTests.sh stops reading its own options · 2026-08-24
- [`D-KNW-113`][D-KNW-113] — Reporting a core issue is a subject this server owns · 2026-08-24
- [`D-KNW-114`][D-KNW-114] — What a core patch owes PHPStan is a subject this server owns · 2026-08-24
- [`D-KNW-115`][D-KNW-115] — The key a site names its sets under is stated with the sets · 2026-08-24
- [`D-KNW-116`][D-KNW-116] — The page object typo3 setup leaves behind is a subject this server owns · 2026-08-24
- [`D-KNW-117`][D-KNW-117] — The invocation notes say what one missing path costs a run · 2026-08-24
- [`D-KNW-106`][D-KNW-106] — A hint about typo3/testing-framework is read at the line the core pins · 2026-08-23
- [`D-KNW-105`][D-KNW-105] — The corpus states what a not-found means once a site answered · 2026-08-19
- [`D-KNW-083`][D-KNW-083] — The shared-root collision is stated for the partial root as well · 2026-08-18
- [`D-KNW-084`][D-KNW-084] — The corpus states which placeholder spelling a relation value survives · 2026-08-18
- [`D-KNW-085`][D-KNW-085] — When DDEV writes additional.php is a subject this server owns · 2026-08-18 · confirmed
- [`D-KNW-086`][D-KNW-086] — Which PHP a covered version runs on is a subject this server owns · 2026-08-18
- [`D-KNW-087`][D-KNW-087] — A listed neighbour says what it prevents · 2026-08-18
- [`D-KNW-088`][D-KNW-088] — What a Composer installation generates below the root is a subject this server owns · 2026-08-18
- [`D-KNW-089`][D-KNW-089] — What a warm TCA cache hides from `extension:setup` is a subject this server owns · 2026-08-18
- [`D-KNW-090`][D-KNW-090] — The corpus names the PHP type a record arrives as · 2026-08-18 · confirmed
- [`D-KNW-091`][D-KNW-091] — A PHP version is the payload a hint may state · 2026-08-18
- [`D-KNW-092`][D-KNW-092] — What an unanswering installation is diagnosed from is a subject this server owns · 2026-08-18
- [`D-KNW-093`][D-KNW-093] — A command that always succeeds is told what a result looks like · 2026-08-18 · confirmed
- [`D-KNW-094`][D-KNW-094] — How a variable reaches a console command is a subject this server owns · 2026-08-18 · confirmed
- [`D-KNW-095`][D-KNW-095] — The installation procedure is a document and the hints keep the facts · 2026-08-18
- [`D-KNW-096`][D-KNW-096] — How a package fills a fresh instance is a subject this server owns · 2026-08-18
- [`D-KNW-097`][D-KNW-097] — Which site a request matches when two bases collide is a subject this server owns · 2026-08-18
- [`D-KNW-098`][D-KNW-098] — Where a site nobody wrote came from is a subject this server owns · 2026-08-18 · confirmed
- [`D-KNW-099`][D-KNW-099] — What a row handed to lib.contentElement owes is a subject this server owns · 2026-08-18 · confirmed
- [`D-KNW-100`][D-KNW-100] — How an extension extends a TypoScript condition is a subject this server owns · 2026-08-18 · confirmed
- [`D-KNW-101`][D-KNW-101] — What a TypoScript condition can reach is a subject this server owns · 2026-08-18 · confirmed
- [`D-KNW-102`][D-KNW-102] — Proving a condition verdict against an installation is a procedure this server carries · 2026-08-18
- [`D-KNW-103`][D-KNW-103] — How an extension adds a field to a core palette is a subject this server owns · 2026-08-18 · confirmed
- [`D-KNW-104`][D-KNW-104] — The corpus states how a field reaches a core palette · 2026-08-18
- [`D-KNW-080`][D-KNW-080] — The impexp export hint is corrected against a run · 2026-08-17 · confirmed
- [`D-KNW-081`][D-KNW-081] — What a NEW placeholder may contain is a subject this server owns · 2026-08-17 · confirmed
- [`D-KNW-082`][D-KNW-082] — A content element names its template · 2026-08-17
- [`D-KNW-071`][D-KNW-071] — Proving what a rendering change renders is a procedure this server carries · 2026-08-14
- [`D-KNW-072`][D-KNW-072] — What makes a change breaking without a member moving is a subject this server owns · 2026-08-14 · confirmed
- [`D-KNW-073`][D-KNW-073] — The corpus states what makes a change breaking with no member moved · 2026-08-14
- [`D-KNW-074`][D-KNW-074] — The shape a Record-sourced row has is a subject this server owns · 2026-08-14 · confirmed
- [`D-KNW-075`][D-KNW-075] — How Fluid resolves an object path is a subject this server owns · 2026-08-14 · confirmed
- [`D-KNW-076`][D-KNW-076] — What a new backend label costs is a subject this server owns · 2026-08-14 · confirmed
- [`D-KNW-077`][D-KNW-077] — The TypeScript style hint carries what cannot be guessed · 2026-08-14
- [`D-KNW-078`][D-KNW-078] — The corpus states the shape a Record-sourced row has · 2026-08-14
- [`D-KNW-079`][D-KNW-079] — The corpus states what a new backend label costs · 2026-08-14
- [`D-KNW-070`][D-KNW-070] — Backend routing internals are a gap this server owns · 2026-08-12
- [`D-KNW-066`][D-KNW-066] — The browser baseline is a release day · 2026-08-10
- [`D-KNW-067`][D-KNW-067] — The JavaScript test layer is a hint of its own · 2026-08-10
- [`D-KNW-068`][D-KNW-068] — Looking at a backend change is a suite the core already carries · 2026-08-10
- [`D-KNW-069`][D-KNW-069] — A browser in a container reaches a site on the router · 2026-08-10
- [`D-KNW-065`][D-KNW-065] — What a public method commits its author to is a subject this server owns · 2026-08-09 · confirmed
- [`D-KNW-064`][D-KNW-064] — The disabled assertions a core checkout carries are a grep · 2026-08-08
- [`D-KNW-063`][D-KNW-063] — What a TCA type stores is a subject this server owns · 2026-08-07
- [`D-KNW-055`][D-KNW-055] — The first check a standalone extension gets is a subject this server owns · 2026-08-04
- [`D-KNW-056`][D-KNW-056] — A file skeleton is shipped as a version-bound document section · 2026-08-04
- [`D-KNW-057`][D-KNW-057] — A document declares what it is and when to reach for it · 2026-08-04
- [`D-KNW-058`][D-KNW-058] — The document namespace is scope first and derived from the file · 2026-08-04
- [`D-KNW-059`][D-KNW-059] — One place spells how a document is addressed · 2026-08-04
- [`D-KNW-060`][D-KNW-060] — What a backend spec locates by is written where the spec is · 2026-08-04
- [`D-KNW-061`][D-KNW-061] — The manual scaffold is a document and the hint keeps the policy · 2026-08-04
- [`D-KNW-062`][D-KNW-062] — What a hint pays with is the mechanism and the file · 2026-08-04
- [`D-KNW-029`][D-KNW-029] — A hint names the domains it is asked from · 2026-08-03
- [`D-KNW-030`][D-KNW-030] — A hint is one question · 2026-08-03
- [`D-KNW-031`][D-KNW-031] — A suite is a property of the domain, not of the hint · 2026-08-03
- [`D-KNW-032`][D-KNW-032] — The corpus is filed by question, and two splits were taken back · 2026-08-03
- [`D-KNW-033`][D-KNW-033] — No hint carries `any` · 2026-08-03
- [`D-KNW-034`][D-KNW-034] — The corpus is one file per subject, named after it · 2026-08-03
- [`D-KNW-035`][D-KNW-035] — The corpus and the tool that answers from it are called hints · 2026-08-03
- [`D-KNW-036`][D-KNW-036] — The standards check handed over is the one that cannot pass empty · 2026-08-03 · confirmed
- [`D-KNW-037`][D-KNW-037] — A content-element preview draws the element's own payload · 2026-08-03
- [`D-KNW-038`][D-KNW-038] — A hint is reached by the role of a file · 2026-08-03
- [`D-KNW-039`][D-KNW-039] — The type a changelog entry owes is stated in prose · 2026-08-03
- [`D-KNW-041`][D-KNW-041] — The checkout a suite is started in supplies its own dependencies · 2026-08-03
- [`D-KNW-042`][D-KNW-042] — What the image pipeline does below the task layer is a subject this server owns · 2026-08-03
- [`D-KNW-043`][D-KNW-043] — A rule carries the strength of its claim and its source · 2026-08-03
- [`D-KNW-044`][D-KNW-044] — One search over the whole Tests/ tree finds what asserts a rendered output · 2026-08-03
- [`D-KNW-045`][D-KNW-045] — The document root is named by what configures and serves it · 2026-08-03
- [`D-KNW-046`][D-KNW-046] — The non-interactive install path is a subject this server owns · 2026-08-03 · confirmed
- [`D-KNW-047`][D-KNW-047] — What installs TYPO3 below an extension is a subject this server owns · 2026-08-03
- [`D-KNW-048`][D-KNW-048] — What an impexp import rewrites in a site configuration is a subject this server owns · 2026-08-03
- [`D-KNW-049`][D-KNW-049] — What DDEV writes into the settings is named in full · 2026-08-03 · confirmed
- [`D-KNW-050`][D-KNW-050] — What a missing `target-language` costs is a subject this server owns · 2026-08-03
- [`D-KNW-051`][D-KNW-051] — The public-asset answer names the internal static beside the supported route · 2026-08-03
- [`D-KNW-052`][D-KNW-052] — The order a Fluid template name resolves in is a subject this server owns · 2026-08-03 · confirmed
- [`D-KNW-053`][D-KNW-053] — The root-package layout is stated from an installation · 2026-08-03
- [`D-KNW-054`][D-KNW-054] — What booting a declared installation takes is one hint · 2026-08-03
- [`D-KNW-005`][D-KNW-005] — `Scope` is the one word for which work a statement is for · 2026-08-02 · confirmed
- [`D-KNW-006`][D-KNW-006] — A backend word adds no domain to a backend-only task · 2026-08-02 · confirmed
- [`D-KNW-007`][D-KNW-007] — A hint says whose it is in both directions · 2026-08-02
- [`D-KNW-008`][D-KNW-008] — Tooling is a row the answer crosses, not a dimension the corpus stores · 2026-08-02
- [`D-KNW-009`][D-KNW-009] — A domain keyword is a phrasing, not a word · 2026-08-02
- [`D-KNW-010`][D-KNW-010] — What the core reads from the environment is a subject this server owns · 2026-08-02 · confirmed
- [`D-KNW-011`][D-KNW-011] — A rule that names a defect names its correction · 2026-08-02
- [`D-KNW-012`][D-KNW-012] — `extension.neon` is PHPStan's filename · 2026-08-02
- [`D-KNW-013`][D-KNW-013] — This repository's own sentence is reworded rather than indexed · 2026-08-02
- [`D-KNW-016`][D-KNW-016] — What an `f:else` does to the branch beside it is a subject this server owns · 2026-08-02
- [`D-KNW-017`][D-KNW-017] — A verification question is routed to the layer that verifies it · 2026-08-02
- [`D-KNW-018`][D-KNW-018] — What a datamap does to a relation field is a subject this server owns · 2026-08-02 · confirmed
- [`D-KNW-019`][D-KNW-019] — The corpus states that a functional test sees only what it primed · 2026-08-02
- [`D-KNW-020`][D-KNW-020] — What a preview template is handed is stated on both majors · 2026-08-02
- [`D-KNW-021`][D-KNW-021] — A Fluid preview template replaces the content half · 2026-08-02
- [`D-KNW-022`][D-KNW-022] — The corpus states how long a per-class test database lives · 2026-08-02
- [`D-KNW-023`][D-KNW-023] — Which page may hold a record is a subject this server owns · 2026-08-02 · confirmed
- [`D-KNW-024`][D-KNW-024] — The Fluid namespace prefix is what a template question is written in · 2026-08-02
- [`D-KNW-026`][D-KNW-026] — The one-off script rule is owed the place it does not name · 2026-08-02
- [`D-KNW-027`][D-KNW-027] — Which caches a change invalidates is a subject this server owns · 2026-08-02 · confirmed
- [`D-KNW-028`][D-KNW-028] — How a file becomes a processed one is a subject this server owns · 2026-08-02
- [`D-KNW-004`][D-KNW-004] — Package knowledge needs a producer before it needs discovery · 2026-07-30 · confirmed

[D-KNW-147]: knowledge/knw-147-a-list-of-what-is-supported-says-what-an-unsupported-key-does.md
[D-KNW-148]: knowledge/knw-148-what-the-schema-of-one-record-type-holds-is-a-subject-this-server-owns.md
[D-KNW-139]: knowledge/knw-139-the-corpus-states-where-an-annotation-is-written.md
[D-KNW-140]: knowledge/knw-140-the-corpus-states-a-check-the-core-does-not-run.md
[D-KNW-141]: knowledge/knw-141-the-shape-of-a-patch-is-stated-where-a-patch-is-judged.md
[D-KNW-142]: knowledge/knw-142-a-test-is-named-for-what-holds-rather-than-for-the-issue.md
[D-KNW-143]: knowledge/knw-143-output-a-pipeline-determines-whole-is-asserted-whole.md
[D-KNW-144]: knowledge/knw-144-the-boundary-of-an-authorised-rework-is-stated-where-a-change-is-made.md
[D-KNW-145]: knowledge/knw-145-a-curated-phrase-crosses-the-gate-whatever-punctuation-it-carries.md
[D-KNW-146]: knowledge/knw-146-the-fluid-engine-is-kept-beside-the-core-checkouts.md
[D-KNW-137]: knowledge/knw-137-a-relaunch-is-a-kind-of-work-rather-than-a-hint-beside-one.md
[D-KNW-138]: knowledge/knw-138-a-hint-names-its-next-call-in-a-statement.md
[D-KNW-133]: knowledge/knw-133-a-guides-whentouse-names-the-answer-it-hands-over.md
[D-KNW-134]: knowledge/knw-134-what-the-functional-harness-does-to-the-working-directory-is-stated.md
[D-KNW-135]: knowledge/knw-135-a-condition-attribute-carries-its-quoting-and-its-zero.md
[D-KNW-136]: knowledge/knw-136-a-fixtures-sys-template-row-discards-what-the-sites-sets-built.md
[D-KNW-126]: knowledge/knw-126-the-syntax-floor-a-core-patch-is-bound-by-is-a-subject-this-server-owns.md
[D-KNW-127]: knowledge/knw-127-how-a-backend-web-component-surfaces-a-failed-load-is-a-subject-this-server-owns.md
[D-KNW-128]: knowledge/knw-128-building-a-link-into-the-official-documentation-is-a-subject-this-server-owns.md
[D-KNW-129]: knowledge/knw-129-opening-a-patch-set-on-somebody-elses-change-is-a-subject-this-server-owns.md
[D-KNW-130]: knowledge/knw-130-what-the-javascript-unit-layer-can-reach-is-stated-in-the-corpus.md
[D-KNW-131]: knowledge/knw-131-the-author-of-a-change-survives-every-amend-somebody-else-makes.md
[D-KNW-132]: knowledge/knw-132-the-changelog-directory-is-stated-where-the-file-is-written.md
[D-KNW-118]: knowledge/knw-118-how-a-development-installation-renders-a-package-that-ships-no-page-typoscript-is-a-subject-this-server-owns.md
[D-KNW-119]: knowledge/knw-119-the-corpus-tells-apart-the-failures-one-usage-synopsis-presents-alike.md
[D-KNW-120]: knowledge/knw-120-a-hint-that-states-a-merge-names-the-lookup-that-reads-the-result.md
[D-KNW-121]: knowledge/knw-121-what-registering-an-argument-costs-a-tag-based-viewhelper-is-a-subject-this-server-owns.md
[D-KNW-122]: knowledge/knw-122-a-procedure-document-is-routed-by-the-evidence-a-task-needs.md
[D-KNW-123]: knowledge/knw-123-the-corpus-tells-a-widened-signature-apart-from-a-widened-visibility.md
[D-KNW-124]: knowledge/knw-124-frontend-render-pipeline-state-is-a-gap-this-server-owns.md
[D-KNW-125]: knowledge/knw-125-a-core-commit-message-carries-four-trailers-and-the-hooks-change-id.md
[D-KNW-107]: knowledge/knw-107-which-side-of-a-backend-module-resolves-a-resource-path-is-a-subject-this-server-owns.md
[D-KNW-108]: knowledge/knw-108-where-an-impexp-import-puts-the-records-it-writes-is-a-subject-this-server-owns.md
[D-KNW-111]: knowledge/knw-111-the-changelog-procedure-is-a-guide-of-its-own.md
[D-KNW-112]: knowledge/knw-112-the-invocation-notes-say-where-runtests-sh-stops-reading-its-own-options.md
[D-KNW-113]: knowledge/knw-113-reporting-a-core-issue-is-a-subject-this-server-owns.md
[D-KNW-114]: knowledge/knw-114-what-a-core-patch-owes-phpstan-is-a-subject-this-server-owns.md
[D-KNW-115]: knowledge/knw-115-the-key-a-site-names-its-sets-under-is-stated-with-the-sets.md
[D-KNW-116]: knowledge/knw-116-the-page-object-typo3-setup-leaves-behind-is-a-subject-this-server-owns.md
[D-KNW-117]: knowledge/knw-117-the-invocation-notes-say-what-one-missing-path-costs-a-run.md
[D-KNW-106]: knowledge/knw-106-a-hint-about-typo3-testing-framework-is-read-at-the-line-the-core-pins.md
[D-KNW-105]: knowledge/knw-105-the-corpus-states-what-a-not-found-means-once-a-site-answered.md
[D-KNW-083]: knowledge/knw-083-the-shared-root-collision-is-stated-for-the-partial-root-as-well.md
[D-KNW-084]: knowledge/knw-084-the-corpus-states-which-placeholder-spelling-a-relation-value-survives.md
[D-KNW-085]: knowledge/knw-085-when-ddev-writes-additional-php-is-a-subject-this-server-owns.md
[D-KNW-086]: knowledge/knw-086-which-php-a-covered-version-runs-on-is-a-subject-this-server-owns.md
[D-KNW-087]: knowledge/knw-087-a-listed-neighbour-says-what-it-prevents.md
[D-KNW-088]: knowledge/knw-088-what-a-composer-installation-generates-below-the-root-is-a-subject-this-server-owns.md
[D-KNW-089]: knowledge/knw-089-what-a-warm-tca-cache-hides-from-extension-setup-is-a-subject-this-server-owns.md
[D-KNW-090]: knowledge/knw-090-the-corpus-names-the-php-type-a-record-arrives-as.md
[D-KNW-091]: knowledge/knw-091-a-php-version-is-the-payload-a-hint-may-state.md
[D-KNW-092]: knowledge/knw-092-what-an-unanswering-installation-is-diagnosed-from-is-a-subject-this-server-owns.md
[D-KNW-093]: knowledge/knw-093-a-command-that-always-succeeds-is-told-what-a-result-looks-like.md
[D-KNW-094]: knowledge/knw-094-how-a-variable-reaches-a-console-command-is-a-subject-this-server-owns.md
[D-KNW-095]: knowledge/knw-095-the-installation-procedure-is-a-document-and-the-hints-keep-the-facts.md
[D-KNW-096]: knowledge/knw-096-how-a-package-fills-a-fresh-instance-is-a-subject-this-server-owns.md
[D-KNW-097]: knowledge/knw-097-which-site-a-request-matches-when-two-bases-collide-is-a-subject-this-server-owns.md
[D-KNW-098]: knowledge/knw-098-where-a-site-nobody-wrote-came-from-is-a-subject-this-server-owns.md
[D-KNW-099]: knowledge/knw-099-what-a-row-handed-to-lib-contentelement-owes-is-a-subject-this-server-owns.md
[D-KNW-100]: knowledge/knw-100-how-an-extension-extends-a-typoscript-condition-is-a-subject-this-server-owns.md
[D-KNW-101]: knowledge/knw-101-what-a-typoscript-condition-can-reach-is-a-subject-this-server-owns.md
[D-KNW-102]: knowledge/knw-102-proving-a-condition-verdict-against-an-installation-is-a-procedure-this-server-carries.md
[D-KNW-103]: knowledge/knw-103-how-an-extension-adds-a-field-to-a-core-palette-is-a-subject-this-server-owns.md
[D-KNW-104]: knowledge/knw-104-the-corpus-states-how-a-field-reaches-a-core-palette.md
[D-KNW-080]: knowledge/knw-080-the-impexp-export-hint-is-corrected-against-a-run.md
[D-KNW-081]: knowledge/knw-081-what-a-new-placeholder-may-contain-is-a-subject-this-server-owns.md
[D-KNW-082]: knowledge/knw-082-a-content-element-names-its-template.md
[D-KNW-071]: knowledge/knw-071-proving-what-a-rendering-change-renders-is-a-procedure-this-server-carries.md
[D-KNW-072]: knowledge/knw-072-what-makes-a-change-breaking-without-a-member-moving-is-a-subject-this-server-owns.md
[D-KNW-073]: knowledge/knw-073-the-corpus-states-what-makes-a-change-breaking-with-no-member-moved.md
[D-KNW-074]: knowledge/knw-074-the-shape-a-record-sourced-row-has-is-a-subject-this-server-owns.md
[D-KNW-075]: knowledge/knw-075-how-fluid-resolves-an-object-path-is-a-subject-this-server-owns.md
[D-KNW-076]: knowledge/knw-076-what-a-new-backend-label-costs-is-a-subject-this-server-owns.md
[D-KNW-077]: knowledge/knw-077-the-typescript-style-hint-carries-what-cannot-be-guessed.md
[D-KNW-078]: knowledge/knw-078-the-corpus-states-the-shape-a-record-sourced-row-has.md
[D-KNW-079]: knowledge/knw-079-the-corpus-states-what-a-new-backend-label-costs.md
[D-KNW-070]: knowledge/knw-070-backend-routing-internals-are-a-gap-this-server-owns.md
[D-KNW-066]: knowledge/knw-066-the-browser-baseline-is-a-release-day.md
[D-KNW-067]: knowledge/knw-067-the-javascript-test-layer-is-a-hint-of-its-own.md
[D-KNW-068]: knowledge/knw-068-looking-at-a-backend-change-is-a-suite-the-core-already-carries.md
[D-KNW-069]: knowledge/knw-069-a-browser-in-a-container-reaches-a-site-on-the-router.md
[D-KNW-065]: knowledge/knw-065-what-a-public-method-commits-its-author-to-is-a-subject-this-server-owns.md
[D-KNW-064]: knowledge/knw-064-the-disabled-assertions-a-core-checkout-carries-are-a-grep.md
[D-KNW-063]: knowledge/knw-063-what-a-tca-type-stores-is-a-subject-this-server-owns.md
[D-KNW-055]: knowledge/knw-055-the-first-check-a-standalone-extension-gets-is-a-subject-this-server-owns.md
[D-KNW-056]: knowledge/knw-056-a-file-skeleton-is-shipped-as-a-version-bound-document-section.md
[D-KNW-057]: knowledge/knw-057-a-document-declares-what-it-is-and-when-to-reach-for-it.md
[D-KNW-058]: knowledge/knw-058-the-document-namespace-is-scope-first-and-derived-from-the-file.md
[D-KNW-059]: knowledge/knw-059-one-place-spells-how-a-document-is-addressed.md
[D-KNW-060]: knowledge/knw-060-what-a-backend-spec-locates-by-is-written-where-the-spec-is.md
[D-KNW-061]: knowledge/knw-061-the-manual-scaffold-is-a-document-and-the-hint-keeps-the-policy.md
[D-KNW-062]: knowledge/knw-062-what-a-hint-pays-with-is-the-mechanism-and-the-file.md
[D-KNW-029]: knowledge/knw-029-a-hint-names-the-domains-it-is-asked-from.md
[D-KNW-030]: knowledge/knw-030-a-hint-is-one-question.md
[D-KNW-031]: knowledge/knw-031-a-suite-is-a-property-of-the-domain-not-of-the-hint.md
[D-KNW-032]: knowledge/knw-032-the-corpus-is-filed-by-question-and-two-splits-were-taken-back.md
[D-KNW-033]: knowledge/knw-033-no-hint-carries-any.md
[D-KNW-034]: knowledge/knw-034-the-corpus-is-one-file-per-subject-named-after-it.md
[D-KNW-035]: knowledge/knw-035-the-corpus-and-the-tool-that-answers-from-it-are-called-hints.md
[D-KNW-036]: knowledge/knw-036-the-standards-check-handed-over-is-the-one-that-cannot-pass-empty.md
[D-KNW-037]: knowledge/knw-037-a-content-element-preview-draws-the-elements-own-payload.md
[D-KNW-038]: knowledge/knw-038-a-hint-is-reached-by-the-role-of-a-file.md
[D-KNW-039]: knowledge/knw-039-the-type-a-changelog-entry-owes-is-stated-in-prose.md
[D-KNW-041]: knowledge/knw-041-the-checkout-a-suite-is-started-in-supplies-its-own-dependencies.md
[D-KNW-042]: knowledge/knw-042-what-the-image-pipeline-does-below-the-task-layer-is-a-subject-this-server-owns.md
[D-KNW-043]: knowledge/knw-043-a-rule-carries-the-strength-of-its-claim-and-its-source.md
[D-KNW-044]: knowledge/knw-044-one-search-over-the-whole-tests-tree-finds-what-asserts-a-rendered-output.md
[D-KNW-045]: knowledge/knw-045-the-document-root-is-named-by-what-configures-and-serves-it.md
[D-KNW-046]: knowledge/knw-046-the-non-interactive-install-path-is-a-subject-this-server-owns.md
[D-KNW-047]: knowledge/knw-047-what-installs-typo3-below-an-extension-is-a-subject-this-server-owns.md
[D-KNW-048]: knowledge/knw-048-what-an-impexp-import-rewrites-in-a-site-configuration-is-a-subject-this-server-owns.md
[D-KNW-049]: knowledge/knw-049-what-ddev-writes-into-the-settings-is-named-in-full.md
[D-KNW-050]: knowledge/knw-050-what-a-missing-target-language-costs-is-a-subject-this-server-owns.md
[D-KNW-051]: knowledge/knw-051-the-public-asset-answer-names-the-internal-static-beside-the-supported-route.md
[D-KNW-052]: knowledge/knw-052-the-order-a-fluid-template-name-resolves-in-is-a-subject-this-server-owns.md
[D-KNW-053]: knowledge/knw-053-the-root-package-layout-is-stated-from-an-installation.md
[D-KNW-054]: knowledge/knw-054-what-booting-a-declared-installation-takes-is-one-hint.md
[D-KNW-005]: knowledge/knw-005-scope-is-the-one-word-for-which-work-a-statement-is-for.md
[D-KNW-006]: knowledge/knw-006-a-backend-word-adds-no-domain-to-a-backend-only-task.md
[D-KNW-007]: knowledge/knw-007-a-hint-says-whose-it-is-in-both-directions.md
[D-KNW-008]: knowledge/knw-008-tooling-is-a-row-the-answer-crosses-not-a-dimension-the-corpus-stores.md
[D-KNW-009]: knowledge/knw-009-a-domain-keyword-is-a-phrasing-not-a-word.md
[D-KNW-010]: knowledge/knw-010-what-the-core-reads-from-the-environment-is-a-subject-this-server-owns.md
[D-KNW-011]: knowledge/knw-011-a-rule-that-names-a-defect-names-its-correction.md
[D-KNW-012]: knowledge/knw-012-extension-neon-is-phpstans-filename.md
[D-KNW-013]: knowledge/knw-013-this-repositorys-own-sentence-is-reworded-rather-than-indexed.md
[D-KNW-016]: knowledge/knw-016-what-an-f-else-does-to-the-branch-beside-it-is-a-subject-this-server-owns.md
[D-KNW-017]: knowledge/knw-017-a-verification-question-is-routed-to-the-layer-that-verifies-it.md
[D-KNW-018]: knowledge/knw-018-what-a-datamap-does-to-a-relation-field-is-a-subject-this-server-owns.md
[D-KNW-019]: knowledge/knw-019-the-corpus-states-that-a-functional-test-sees-only-what-it-primed.md
[D-KNW-020]: knowledge/knw-020-what-a-preview-template-is-handed-is-stated-on-both-majors.md
[D-KNW-021]: knowledge/knw-021-a-fluid-preview-template-replaces-the-content-half.md
[D-KNW-022]: knowledge/knw-022-the-corpus-states-how-long-a-per-class-test-database-lives.md
[D-KNW-023]: knowledge/knw-023-which-page-may-hold-a-record-is-a-subject-this-server-owns.md
[D-KNW-024]: knowledge/knw-024-the-fluid-namespace-prefix-is-what-a-template-question-is-written-in.md
[D-KNW-026]: knowledge/knw-026-the-one-off-script-rule-is-owed-the-place-it-does-not-name.md
[D-KNW-027]: knowledge/knw-027-which-caches-a-change-invalidates-is-a-subject-this-server-owns.md
[D-KNW-028]: knowledge/knw-028-how-a-file-becomes-a-processed-one-is-a-subject-this-server-owns.md
[D-KNW-004]: knowledge/knw-004-package-knowledge-needs-a-producer-before-it-needs-discovery.md

### versions

- [`D-VER-009`][D-VER-009] — A deprecation's migration target is asked for by its own issue number · 2026-08-21
- [`D-VER-006`][D-VER-006] — A narrowed statement is split before it is bound · 2026-08-18 · confirmed
- [`D-VER-007`][D-VER-007] — A declared major that is not installed is answered by a reading · 2026-08-18
- [`D-VER-008`][D-VER-008] — A declared major is proved on a second installation · 2026-08-18 · confirmed
- [`D-VER-005`][D-VER-005] — A document section declares the majors it holds for · 2026-08-04
- [`D-VER-004`][D-VER-004] — A supported range is a property of the package, not of the checkout · 2026-07-31 · confirmed
- [`D-VER-003`][D-VER-003] — The Fluid engine gets no version axis of its own · 2026-07-30 · confirmed
- [`D-VER-001`][D-VER-001] — A version range is data on the statement, not a sentence in it · 2026-07-29 · confirmed

[D-VER-009]: versions/ver-009-a-deprecations-migration-target-is-asked-for-by-its-own-issue-number.md
[D-VER-006]: versions/ver-006-a-narrowed-statement-is-split-before-it-is-bound.md
[D-VER-007]: versions/ver-007-a-declared-major-that-is-not-installed-is-answered-by-a-reading.md
[D-VER-008]: versions/ver-008-a-declared-major-is-proved-on-a-second-installation.md
[D-VER-005]: versions/ver-005-a-document-section-declares-the-majors-it-holds-for.md
[D-VER-004]: versions/ver-004-a-supported-range-is-a-property-of-the-package-not-of-the-checkout.md
[D-VER-003]: versions/ver-003-the-fluid-engine-gets-no-version-axis-of-its-own.md
[D-VER-001]: versions/ver-001-a-version-range-is-data-on-the-statement-not-a-sentence-in-it.md

### catalog

- [`D-CAT-010`][D-CAT-010] — The catalog scope is kept and the miss carries its own re-check · 2026-09-02
- [`D-CAT-008`][D-CAT-008] — A component entry's classes carry a derived position and range · 2026-08-24
- [`D-CAT-009`][D-CAT-009] — The catalog lists what the styleguide lists · 2026-08-24
- [`D-CAT-007`][D-CAT-007] — A reference entry's range is derived from what it promises · 2026-08-23
- [`D-CAT-006`][D-CAT-006] — A class-shaped query is answered by a second range · 2026-08-21
- [`D-CAT-005`][D-CAT-005] — A reference entry names a form to imitate · 2026-08-18
- [`D-CAT-004`][D-CAT-004] — The component index holds what the core files as a component · 2026-08-11
- [`D-CAT-003`][D-CAT-003] — The component index is curated; its contract comes from the installation · 2026-07-30
- [`D-CAT-001`][D-CAT-001] — A catalog entry is bound whole, and the binding is derived · 2026-07-29 · confirmed

[D-CAT-010]: catalog/cat-010-the-catalog-scope-is-kept-and-the-miss-carries-its-own-re-check.md
[D-CAT-008]: catalog/cat-008-a-component-entrys-classes-carry-a-derived-position-and-range.md
[D-CAT-009]: catalog/cat-009-the-catalog-lists-what-the-styleguide-lists.md
[D-CAT-007]: catalog/cat-007-a-reference-entrys-range-is-derived-from-what-it-promises.md
[D-CAT-006]: catalog/cat-006-a-class-shaped-query-is-answered-by-a-second-range.md
[D-CAT-005]: catalog/cat-005-a-reference-entry-names-a-form-to-imitate.md
[D-CAT-004]: catalog/cat-004-the-component-index-holds-what-the-core-files-as-a-component.md
[D-CAT-003]: catalog/cat-003-the-component-index-is-curated-its-contract-comes-from-the-installation.md
[D-CAT-001]: catalog/cat-001-a-catalog-entry-is-bound-whole-and-the-binding-is-derived.md

### scope

- [`D-SCO-015`][D-SCO-015] — An intent's routing line names the core artifact it needs · 2026-08-28
- [`D-SCO-016`][D-SCO-016] — A path placed outside the core decides a call nothing places in it · 2026-08-28
- [`D-SCO-013`][D-SCO-013] — A declared command carries the interpreter it runs on · 2026-08-19
- [`D-SCO-014`][D-SCO-014] — The npm manifest is read where the repository keeps it, `Build/` included · 2026-08-19
- [`D-SCO-012`][D-SCO-012] — The root manifest places the work before the dependencies are installed · 2026-08-18
- [`D-SCO-010`][D-SCO-010] — All three `typo3` namespaces are kept · 2026-08-04
- [`D-SCO-011`][D-SCO-011] — A tool that describes one thing carries `describe` · 2026-08-04
- [`D-SCO-009`][D-SCO-009] — The brief is one brief · 2026-08-02
- [`D-SCO-002`][D-SCO-002] — A core-only intent asks for evidence, not for silence · 2026-07-29 · confirmed
- [`D-SCO-003`][D-SCO-003] — What is core-only is decided per line, by what it names · 2026-07-29 · confirmed
- [`D-SCO-005`][D-SCO-005] — The installation is evidence about the task, and the weakest kind · 2026-07-29 · confirmed
- [`D-SCO-006`][D-SCO-006] — Every surface says who this server answers for · 2026-07-29 · confirmed

[D-SCO-015]: scope/sco-015-an-intents-routing-line-names-the-core-artifact-it-needs.md
[D-SCO-016]: scope/sco-016-a-path-placed-outside-the-core-decides-a-call-nothing-places-in-it.md
[D-SCO-013]: scope/sco-013-a-declared-command-carries-the-interpreter-it-runs-on.md
[D-SCO-014]: scope/sco-014-the-npm-manifest-is-read-where-the-repository-keeps-it-build-included.md
[D-SCO-012]: scope/sco-012-the-root-manifest-places-the-work-before-the-dependencies-are-installed.md
[D-SCO-010]: scope/sco-010-all-three-typo3-namespaces-are-kept.md
[D-SCO-011]: scope/sco-011-a-tool-that-describes-one-thing-carries-describe.md
[D-SCO-009]: scope/sco-009-the-brief-is-one-brief.md
[D-SCO-002]: scope/sco-002-a-core-only-intent-asks-for-evidence-not-for-silence.md
[D-SCO-003]: scope/sco-003-what-is-core-only-is-decided-per-line-by-what-it-names.md
[D-SCO-005]: scope/sco-005-the-installation-is-evidence-about-the-task-and-the-weakest-kind.md
[D-SCO-006]: scope/sco-006-every-surface-says-who-this-server-answers-for.md

### guides

- [`D-GUI-027`][D-GUI-027] — An intent's condition decides whether its checklist arrives · 2026-09-04
- [`D-GUI-026`][D-GUI-026] — The commit body is prose, and the check says so · 2026-09-02
- [`D-GUI-025`][D-GUI-025] — A checklist item says that it does not decide everything · 2026-09-01
- [`D-GUI-024`][D-GUI-024] — The intent that states an obligation names the page discharging it · 2026-08-28
- [`D-GUI-021`][D-GUI-021] — The subject a draft carries is the summary the caller wrote · 2026-08-27
- [`D-GUI-022`][D-GUI-022] — The paths a brief is composed from name a subsystem rather than a diff · 2026-08-27
- [`D-GUI-023`][D-GUI-023] — A checklist item names the call that can answer it · 2026-08-27
- [`D-GUI-019`][D-GUI-019] — The audit brief names the lookup that reads a configuration default · 2026-08-26
- [`D-GUI-020`][D-GUI-020] — The commit guide states the longest line the hook accepts · 2026-08-26
- [`D-GUI-017`][D-GUI-017] — An issue the caller passed is written in either workflow · 2026-08-21
- [`D-GUI-018`][D-GUI-018] — Keeping a package on the majors it declares is an intent · 2026-08-21
- [`D-GUI-015`][D-GUI-015] — A case's own prompt reaches less than the brief · 2026-08-19
- [`D-GUI-016`][D-GUI-016] — The brief carries its hints whether or not it names a skill · 2026-08-19
- [`D-GUI-012`][D-GUI-012] — The brief names the guide the recognized work belongs to · 2026-08-18
- [`D-GUI-013`][D-GUI-013] — The brief names the sweep a change owes · 2026-08-18
- [`D-GUI-014`][D-GUI-014] — Looking at a change is an intent of its own · 2026-08-18
- [`D-GUI-011`][D-GUI-011] — Reviewing a report against code is a change type of its own · 2026-08-08
- [`D-GUI-009`][D-GUI-009] — A stated change type keeps the skeleton · 2026-08-04
- [`D-GUI-010`][D-GUI-010] — The commit workflow defaults to the repository most callers are in · 2026-08-04
- [`D-GUI-003`][D-GUI-003] — The wrapping conflict is resolved in the answer rather than in silence · 2026-08-03
- [`D-GUI-004`][D-GUI-004] — A review brief states the removal surface rather than matching it · 2026-08-03
- [`D-GUI-005`][D-GUI-005] — The product premise is one statement on every brief · 2026-08-03
- [`D-GUI-006`][D-GUI-006] — A task that changes nothing is a change type of its own · 2026-08-03
- [`D-GUI-007`][D-GUI-007] — The brief carries a selection of the hints and says whose they are · 2026-08-03 · confirmed
- [`D-GUI-008`][D-GUI-008] — Operating an installation is a change type of its own · 2026-08-03
- [`D-GUI-001`][D-GUI-001] — A missing release target becomes a placeholder, not `main` · 2026-07-29

[D-GUI-027]: guides/gui-027-an-intents-condition-decides-whether-its-checklist-arrives.md
[D-GUI-026]: guides/gui-026-the-commit-body-is-prose-and-the-check-says-so.md
[D-GUI-025]: guides/gui-025-a-checklist-item-says-that-it-does-not-decide-everything.md
[D-GUI-024]: guides/gui-024-the-intent-that-states-an-obligation-names-the-page-discharging-it.md
[D-GUI-021]: guides/gui-021-the-subject-a-draft-carries-is-the-summary-the-caller-wrote.md
[D-GUI-022]: guides/gui-022-the-paths-a-brief-is-composed-from-name-a-subsystem-rather-than-a-diff.md
[D-GUI-023]: guides/gui-023-a-checklist-item-names-the-call-that-can-answer-it.md
[D-GUI-019]: guides/gui-019-the-audit-brief-names-the-lookup-that-reads-a-configuration-default.md
[D-GUI-020]: guides/gui-020-the-commit-guide-states-the-longest-line-the-hook-accepts.md
[D-GUI-017]: guides/gui-017-an-issue-the-caller-passed-is-written-in-either-workflow.md
[D-GUI-018]: guides/gui-018-keeping-a-package-on-the-majors-it-declares-is-an-intent.md
[D-GUI-015]: guides/gui-015-a-cases-own-prompt-reaches-less-than-the-brief.md
[D-GUI-016]: guides/gui-016-the-brief-carries-its-hints-whether-or-not-it-names-a-skill.md
[D-GUI-012]: guides/gui-012-the-brief-names-the-guide-the-recognized-work-belongs-to.md
[D-GUI-013]: guides/gui-013-the-brief-names-the-sweep-a-change-owes.md
[D-GUI-014]: guides/gui-014-looking-at-a-change-is-an-intent-of-its-own.md
[D-GUI-011]: guides/gui-011-reviewing-a-report-against-code-is-a-change-type-of-its-own.md
[D-GUI-009]: guides/gui-009-a-stated-change-type-keeps-the-skeleton.md
[D-GUI-010]: guides/gui-010-the-commit-workflow-defaults-to-the-repository-most-callers-are-in.md
[D-GUI-003]: guides/gui-003-the-wrapping-conflict-is-resolved-in-the-answer-rather-than-in-silence.md
[D-GUI-004]: guides/gui-004-a-review-brief-states-the-removal-surface-rather-than-matching-it.md
[D-GUI-005]: guides/gui-005-the-product-premise-is-one-statement-on-every-brief.md
[D-GUI-006]: guides/gui-006-a-task-that-changes-nothing-is-a-change-type-of-its-own.md
[D-GUI-007]: guides/gui-007-the-brief-carries-a-selection-of-the-hints-and-says-whose-they-are.md
[D-GUI-008]: guides/gui-008-operating-an-installation-is-a-change-type-of-its-own.md
[D-GUI-001]: guides/gui-001-a-missing-release-target-becomes-a-placeholder-not-main.md

### evidence

- [`D-EVI-010`][D-EVI-010] — A made installation carries an extension of the project's own · 2026-09-02
- [`D-EVI-009`][D-EVI-009] — A run is read against its own trace · 2026-09-01
- [`D-EVI-007`][D-EVI-007] — A case no test holds says so with its exit code · 2026-08-18
- [`D-EVI-008`][D-EVI-008] — The server collapses the spread of a lookup rather than its median · 2026-08-18
- [`D-EVI-006`][D-EVI-006] — One installation per covered version, kept and started · 2026-08-03
- [`D-EVI-004`][D-EVI-004] — The environment is made here, and the repository under review is not · 2026-08-02
- [`D-EVI-005`][D-EVI-005] — A registration nothing can reach is cleared with its database · 2026-08-02
- [`D-EVI-001`][D-EVI-001] — Forward evidence comes from a review · 2026-07-31 · confirmed
- [`D-EVI-002`][D-EVI-002] — A skill crossing is read rather than run · 2026-07-31 · confirmed
- [`D-EVI-003`][D-EVI-003] — A review runs the checks that cannot change the code · 2026-07-31 · confirmed

[D-EVI-010]: evidence/evi-010-a-made-installation-carries-an-extension-of-the-projects-own.md
[D-EVI-009]: evidence/evi-009-a-run-is-read-against-its-own-trace.md
[D-EVI-007]: evidence/evi-007-a-case-no-test-holds-says-so-with-its-exit-code.md
[D-EVI-008]: evidence/evi-008-the-server-collapses-the-spread-of-a-lookup-rather-than-its-median.md
[D-EVI-006]: evidence/evi-006-one-installation-per-covered-version-kept-and-started.md
[D-EVI-004]: evidence/evi-004-the-environment-is-made-here-and-the-repository-under-review-is-not.md
[D-EVI-005]: evidence/evi-005-a-registration-nothing-can-reach-is-cleared-with-its-database.md
[D-EVI-001]: evidence/evi-001-forward-evidence-comes-from-a-review.md
[D-EVI-002]: evidence/evi-002-a-skill-crossing-is-read-rather-than-run.md
[D-EVI-003]: evidence/evi-003-a-review-runs-the-checks-that-cannot-change-the-code.md

### task-skills

- [`D-SKL-090`][D-SKL-090] — A review rates the patch and reads the chain for what a shape is for · 2026-09-02
- [`D-SKL-087`][D-SKL-087] — Every skill in the directory is published · 2026-09-01
- [`D-SKL-088`][D-SKL-088] — A paragraph three skills share stops being copied · 2026-09-01
- [`D-SKL-089`][D-SKL-089] — The base says what it established and what it will touch · 2026-09-01
- [`D-SKL-083`][D-SKL-083] — A test file is on the sweep's side of the exemption · 2026-08-28
- [`D-SKL-084`][D-SKL-084] — The presence check looks for the qualified tool name too · 2026-08-28
- [`D-SKL-085`][D-SKL-085] — The crossing into a fix says the sweep is owed again · 2026-08-28
- [`D-SKL-086`][D-SKL-086] — The stale notice reaches the answer that names the skill · 2026-08-28
- [`D-SKL-079`][D-SKL-079] — A widened request re-establishes what the patch is and what it owes · 2026-08-27
- [`D-SKL-080`][D-SKL-080] — A path only the core has routes to the core's own workflow · 2026-08-27
- [`D-SKL-081`][D-SKL-081] — A brief spanning triage and the patch it leads to carries both · 2026-08-27
- [`D-SKL-082`][D-SKL-082] — A declared change type on core paths names the patch workflow · 2026-08-27
- [`D-SKL-075`][D-SKL-075] — A patch narrows the work, not the list of points it closes on · 2026-08-25
- [`D-SKL-076`][D-SKL-076] — A description names both jobs a skill's body owns · 2026-08-25
- [`D-SKL-077`][D-SKL-077] — The crossing out of a review is recognised on the first edit meant to survive · 2026-08-25
- [`D-SKL-078`][D-SKL-078] — The triage intent matches taking an issue off the tracker · 2026-08-25
- [`D-SKL-070`][D-SKL-070] — A description is held to a length of its own · 2026-08-24
- [`D-SKL-071`][D-SKL-071] — A probe is put back to the state it found · 2026-08-24
- [`D-SKL-072`][D-SKL-072] — A workflow handover names the calls the next order restarts with · 2026-08-24
- [`D-SKL-073`][D-SKL-073] — A verdict that ends an issue carries the comment that closes it · 2026-08-24
- [`D-SKL-074`][D-SKL-074] — A skipped step is named where the report is written · 2026-08-24
- [`D-SKL-067`][D-SKL-067] — Maintaining a package's asset build earns a task skill · 2026-08-21
- [`D-SKL-068`][D-SKL-068] — An audit's list is established against the work already in flight · 2026-08-21
- [`D-SKL-069`][D-SKL-069] — Each runtime lookup says what it adds after the extension answer · 2026-08-21
- [`D-SKL-064`][D-SKL-064] — The audit and the work that answers it are one skill · 2026-08-19
- [`D-SKL-065`][D-SKL-065] — A defect reported by its symptom is routed to the cause · 2026-08-19
- [`D-SKL-066`][D-SKL-066] — Documenting a package for its readers is an intent of its own · 2026-08-19
- [`D-SKL-044`][D-SKL-044] — A step that names two hint ids says what each answers · 2026-08-18
- [`D-SKL-045`][D-SKL-045] — A build workflow names the guide at the step that needs it · 2026-08-18
- [`D-SKL-046`][D-SKL-046] — A precondition is restated where the file it guards is written · 2026-08-18
- [`D-SKL-047`][D-SKL-047] — The Composer root step fetches the installer keys · 2026-08-18
- [`D-SKL-048`][D-SKL-048] — A build workflow says a symptom is a lookup trigger · 2026-08-18
- [`D-SKL-049`][D-SKL-049] — The gate at the end of a workflow waits for its corrections · 2026-08-18
- [`D-SKL-050`][D-SKL-050] — Producing a distribution's content earns a task skill · 2026-08-18 · confirmed
- [`D-SKL-051`][D-SKL-051] — A site built from scratch reaches the installation intent · 2026-08-18
- [`D-SKL-052`][D-SKL-052] — The injected size of a skill is what the retention rule leaves · 2026-08-18
- [`D-SKL-053`][D-SKL-053] — An absence in the extension answer names the skill that owns it · 2026-08-18
- [`D-SKL-054`][D-SKL-054] — The listing budget is what a client reads · 2026-08-18
- [`D-SKL-055`][D-SKL-055] — A call named in order not to make it is a discharge · 2026-08-18
- [`D-SKL-056`][D-SKL-056] — The installation workflow branches on the declared procedure · 2026-08-18
- [`D-SKL-057`][D-SKL-057] — A command's options are read from the installed console · 2026-08-18
- [`D-SKL-058`][D-SKL-058] — A hint is routed by what the repository is · 2026-08-18
- [`D-SKL-059`][D-SKL-059] — An installation that answers is owned by its workflow · 2026-08-18
- [`D-SKL-060`][D-SKL-060] — A skill names a tool at the step that needs it · 2026-08-18
- [`D-SKL-061`][D-SKL-061] — The upgrade description is reachable from a defect · 2026-08-18
- [`D-SKL-062`][D-SKL-062] — The workflow question is asked again on a new subject · 2026-08-18
- [`D-SKL-063`][D-SKL-063] — Reviewing a change against a package earns a task skill · 2026-08-18
- [`D-SKL-037`][D-SKL-037] — The sweep's exemption names what a task produces · 2026-08-14
- [`D-SKL-038`][D-SKL-038] — The change answer names the skill that owns the patch it describes · 2026-08-14
- [`D-SKL-039`][D-SKL-039] — A brief that changes nothing routes only the workflows that change nothing · 2026-08-14
- [`D-SKL-041`][D-SKL-041] — A patch carried onto current code is carried on a named branch · 2026-08-14
- [`D-SKL-042`][D-SKL-042] — A report is copyable markdown, and the answer is where it goes · 2026-08-14
- [`D-SKL-043`][D-SKL-043] — A rule query carries two subjects · 2026-08-14
- [`D-SKL-035`][D-SKL-035] — A new skill is measured against a run without it · 2026-08-12
- [`D-SKL-036`][D-SKL-036] — A skill runs where the installer put it · 2026-08-12
- [`D-SKL-033`][D-SKL-033] — Whether a skill is activated is the client's and the model's · 2026-08-11
- [`D-SKL-034`][D-SKL-034] — A step is skippable on what the session holds · 2026-08-11
- [`D-SKL-032`][D-SKL-032] — A probe is worth what the session can run · 2026-08-10
- [`D-SKL-028`][D-SKL-028] — A triage reaching for a previous attempt is routed to it · 2026-08-09
- [`D-SKL-029`][D-SKL-029] — Precedent is listed by the changelog's own axes · 2026-08-09
- [`D-SKL-030`][D-SKL-030] — A review surface names the lookup that can answer it · 2026-08-09
- [`D-SKL-031`][D-SKL-031] — A triage picks a candidate on where the symptom shows · 2026-08-09
- [`D-SKL-023`][D-SKL-023] — A skill no intent names is one the brief cannot route to · 2026-08-08
- [`D-SKL-024`][D-SKL-024] — A description names the task and leaves the steps to the body · 2026-08-08 · confirmed
- [`D-SKL-025`][D-SKL-025] — A routed tool is called and held to what it reads · 2026-08-08
- [`D-SKL-026`][D-SKL-026] — The descriptions are written to the listing budget they share · 2026-08-08
- [`D-SKL-022`][D-SKL-022] — A handoff between skills is an instruction rather than a closing sentence · 2026-08-07 · confirmed
- [`D-SKL-021`][D-SKL-021] — Triage and fetching a patch are two workflows · 2026-08-05
- [`D-SKL-014`][D-SKL-014] — The commit step is named where a workflow ends in a change · 2026-08-04
- [`D-SKL-017`][D-SKL-017] — A named check is established against the package it lands on · 2026-08-04
- [`D-SKL-018`][D-SKL-018] — The guide of the chosen layer arrives with the brief · 2026-08-04
- [`D-SKL-019`][D-SKL-019] — An absent surface is asked for by the id of its convention · 2026-08-04
- [`D-SKL-020`][D-SKL-020] — A re-check runs what the finding was about · 2026-08-04
- [`D-SKL-005`][D-SKL-005] — Core contribution earns two task skills · 2026-08-03
- [`D-SKL-006`][D-SKL-006] — The site-new cluster earns the route into the skill that owns the task · 2026-08-03
- [`D-SKL-007`][D-SKL-007] — Every disposition a review makes carries its evidence · 2026-08-03
- [`D-SKL-008`][D-SKL-008] — A review reads the review the patch is already in · 2026-08-03
- [`D-SKL-009`][D-SKL-009] — A rule that keeps not landing is written as an act · 2026-08-03 · confirmed
- [`D-SKL-010`][D-SKL-010] — The assessment before a core patch reads the issue · 2026-08-03
- [`D-SKL-012`][D-SKL-012] — Bringing a package's development installation into existence earns a task skill · 2026-08-03
- [`D-SKL-013`][D-SKL-013] — The guide names the skill that owns the task · 2026-08-03
- [`D-SKL-002`][D-SKL-002] — A focused audit narrows what is assessed, not the list it closes on · 2026-08-02
- [`D-SKL-003`][D-SKL-003] — A sweep is bounded by the changelog's own axes · 2026-08-02
- [`D-SKL-004`][D-SKL-004] — A question no lookup settles is read from the installed source · 2026-08-02
- [`D-SKL-001`][D-SKL-001] — The order a task starts in is one file · 2026-08-01 · confirmed

[D-SKL-090]: task-skills/skl-090-a-review-rates-the-patch-and-reads-the-chain-for-what-a-shape-is-for.md
[D-SKL-087]: task-skills/skl-087-every-skill-in-the-directory-is-published.md
[D-SKL-088]: task-skills/skl-088-a-paragraph-three-skills-share-stops-being-copied.md
[D-SKL-089]: task-skills/skl-089-the-base-says-what-it-established-and-what-it-will-touch.md
[D-SKL-083]: task-skills/skl-083-a-test-file-is-on-the-sweeps-side-of-the-exemption.md
[D-SKL-084]: task-skills/skl-084-the-presence-check-looks-for-the-qualified-tool-name-too.md
[D-SKL-085]: task-skills/skl-085-the-crossing-into-a-fix-says-the-sweep-is-owed-again.md
[D-SKL-086]: task-skills/skl-086-the-stale-notice-reaches-the-answer-that-names-the-skill.md
[D-SKL-079]: task-skills/skl-079-a-widened-request-re-establishes-what-the-patch-is-and-what-it-owes.md
[D-SKL-080]: task-skills/skl-080-a-path-only-the-core-has-routes-to-the-cores-own-workflow.md
[D-SKL-081]: task-skills/skl-081-a-brief-spanning-triage-and-the-patch-it-leads-to-carries-both.md
[D-SKL-082]: task-skills/skl-082-a-declared-change-type-on-core-paths-names-the-patch-workflow.md
[D-SKL-075]: task-skills/skl-075-a-patch-narrows-the-work-not-the-list-of-points-it-closes-on.md
[D-SKL-076]: task-skills/skl-076-a-description-names-both-jobs-a-skills-body-owns.md
[D-SKL-077]: task-skills/skl-077-the-crossing-out-of-a-review-is-recognised-on-the-first-edit-meant-to-survive.md
[D-SKL-078]: task-skills/skl-078-the-triage-intent-matches-taking-an-issue-off-the-tracker.md
[D-SKL-070]: task-skills/skl-070-a-description-is-held-to-a-length-of-its-own.md
[D-SKL-071]: task-skills/skl-071-a-probe-is-put-back-to-the-state-it-found.md
[D-SKL-072]: task-skills/skl-072-a-workflow-handover-names-the-calls-the-next-order-restarts-with.md
[D-SKL-073]: task-skills/skl-073-a-verdict-that-ends-an-issue-carries-the-comment-that-closes-it.md
[D-SKL-074]: task-skills/skl-074-a-skipped-step-is-named-where-the-report-is-written.md
[D-SKL-067]: task-skills/skl-067-maintaining-a-packages-asset-build-earns-a-task-skill.md
[D-SKL-068]: task-skills/skl-068-an-audits-list-is-established-against-the-work-already-in-flight.md
[D-SKL-069]: task-skills/skl-069-each-runtime-lookup-says-what-it-adds-after-the-extension-answer.md
[D-SKL-064]: task-skills/skl-064-the-audit-and-the-work-that-answers-it-are-one-skill.md
[D-SKL-065]: task-skills/skl-065-a-defect-reported-by-its-symptom-is-routed-to-the-cause.md
[D-SKL-066]: task-skills/skl-066-documenting-a-package-for-its-readers-is-an-intent-of-its-own.md
[D-SKL-044]: task-skills/skl-044-a-step-that-names-two-hint-ids-says-what-each-answers.md
[D-SKL-045]: task-skills/skl-045-a-build-workflow-names-the-guide-at-the-step-that-needs-it.md
[D-SKL-046]: task-skills/skl-046-a-precondition-is-restated-where-the-file-it-guards-is-written.md
[D-SKL-047]: task-skills/skl-047-the-composer-root-step-fetches-the-installer-keys.md
[D-SKL-048]: task-skills/skl-048-a-build-workflow-says-a-symptom-is-a-lookup-trigger.md
[D-SKL-049]: task-skills/skl-049-the-gate-at-the-end-of-a-workflow-waits-for-its-corrections.md
[D-SKL-050]: task-skills/skl-050-producing-a-distributions-content-earns-a-task-skill.md
[D-SKL-051]: task-skills/skl-051-a-site-built-from-scratch-reaches-the-installation-intent.md
[D-SKL-052]: task-skills/skl-052-the-injected-size-of-a-skill-is-what-the-retention-rule-leaves.md
[D-SKL-053]: task-skills/skl-053-an-absence-in-the-extension-answer-names-the-skill-that-owns-it.md
[D-SKL-054]: task-skills/skl-054-the-listing-budget-is-what-a-client-reads.md
[D-SKL-055]: task-skills/skl-055-a-call-named-in-order-not-to-make-it-is-a-discharge.md
[D-SKL-056]: task-skills/skl-056-the-installation-workflow-branches-on-the-declared-procedure.md
[D-SKL-057]: task-skills/skl-057-a-commands-options-are-read-from-the-installed-console.md
[D-SKL-058]: task-skills/skl-058-a-hint-is-routed-by-what-the-repository-is.md
[D-SKL-059]: task-skills/skl-059-an-installation-that-answers-is-owned-by-its-workflow.md
[D-SKL-060]: task-skills/skl-060-a-skill-names-a-tool-at-the-step-that-needs-it.md
[D-SKL-061]: task-skills/skl-061-the-upgrade-description-is-reachable-from-a-defect.md
[D-SKL-062]: task-skills/skl-062-the-workflow-question-is-asked-again-on-a-new-subject.md
[D-SKL-063]: task-skills/skl-063-reviewing-a-change-against-a-package-earns-a-task-skill.md
[D-SKL-037]: task-skills/skl-037-the-sweeps-exemption-names-what-a-task-produces.md
[D-SKL-038]: task-skills/skl-038-the-change-answer-names-the-skill-that-owns-the-patch-it-describes.md
[D-SKL-039]: task-skills/skl-039-a-brief-that-changes-nothing-routes-only-the-workflows-that-change-nothing.md
[D-SKL-041]: task-skills/skl-041-a-patch-carried-onto-current-code-is-carried-on-a-named-branch.md
[D-SKL-042]: task-skills/skl-042-a-report-is-copyable-markdown-and-the-answer-is-where-it-goes.md
[D-SKL-043]: task-skills/skl-043-a-rule-query-carries-two-subjects.md
[D-SKL-035]: task-skills/skl-035-a-new-skill-is-measured-against-a-run-without-it.md
[D-SKL-036]: task-skills/skl-036-a-skill-runs-where-the-installer-put-it.md
[D-SKL-033]: task-skills/skl-033-whether-a-skill-is-activated-is-the-clients-and-the-models.md
[D-SKL-034]: task-skills/skl-034-a-step-is-skippable-on-what-the-session-holds.md
[D-SKL-032]: task-skills/skl-032-a-probe-is-worth-what-the-session-can-run.md
[D-SKL-028]: task-skills/skl-028-a-triage-reaching-for-a-previous-attempt-is-routed-to-it.md
[D-SKL-029]: task-skills/skl-029-precedent-is-listed-by-the-changelogs-own-axes.md
[D-SKL-030]: task-skills/skl-030-a-review-surface-names-the-lookup-that-can-answer-it.md
[D-SKL-031]: task-skills/skl-031-a-triage-picks-a-candidate-on-where-the-symptom-shows.md
[D-SKL-023]: task-skills/skl-023-a-skill-no-intent-names-is-one-the-brief-cannot-route-to.md
[D-SKL-024]: task-skills/skl-024-a-description-names-the-task-and-leaves-the-steps-to-the-body.md
[D-SKL-025]: task-skills/skl-025-a-routed-tool-is-called-and-held-to-what-it-reads.md
[D-SKL-026]: task-skills/skl-026-the-descriptions-are-written-to-the-listing-budget-they-share.md
[D-SKL-022]: task-skills/skl-022-a-handoff-between-skills-is-an-instruction-rather-than-a-closing-sentence.md
[D-SKL-021]: task-skills/skl-021-triage-and-fetching-a-patch-are-two-workflows.md
[D-SKL-014]: task-skills/skl-014-the-commit-step-is-named-where-a-workflow-ends-in-a-change.md
[D-SKL-017]: task-skills/skl-017-a-named-check-is-established-against-the-package-it-lands-on.md
[D-SKL-018]: task-skills/skl-018-the-guide-of-the-chosen-layer-arrives-with-the-brief.md
[D-SKL-019]: task-skills/skl-019-an-absent-surface-is-asked-for-by-the-id-of-its-convention.md
[D-SKL-020]: task-skills/skl-020-a-re-check-runs-what-the-finding-was-about.md
[D-SKL-005]: task-skills/skl-005-core-contribution-earns-two-task-skills.md
[D-SKL-006]: task-skills/skl-006-the-site-new-cluster-earns-the-route-into-the-skill-that-owns-the-task.md
[D-SKL-007]: task-skills/skl-007-every-disposition-a-review-makes-carries-its-evidence.md
[D-SKL-008]: task-skills/skl-008-a-review-reads-the-review-the-patch-is-already-in.md
[D-SKL-009]: task-skills/skl-009-a-rule-that-keeps-not-landing-is-written-as-an-act.md
[D-SKL-010]: task-skills/skl-010-the-assessment-before-a-core-patch-reads-the-issue.md
[D-SKL-012]: task-skills/skl-012-bringing-a-packages-development-installation-into-existence-earns-a-task-skill.md
[D-SKL-013]: task-skills/skl-013-the-guide-names-the-skill-that-owns-the-task.md
[D-SKL-002]: task-skills/skl-002-a-focused-audit-narrows-what-is-assessed-not-the-list-it-closes-on.md
[D-SKL-003]: task-skills/skl-003-a-sweep-is-bounded-by-the-changelogs-own-axes.md
[D-SKL-004]: task-skills/skl-004-a-question-no-lookup-settles-is-read-from-the-installed-source.md
[D-SKL-001]: task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md

### feedback

- [`D-FBK-054`][D-FBK-054] — The server answers what is registered and a person answers what it looks like · 2026-09-02
- [`D-FBK-055`][D-FBK-055] — A registration file is checked after the cache flush or not at all · 2026-09-02
- [`D-FBK-053`][D-FBK-053] — A migrated memory is judged against the source its rule has · 2026-09-01
- [`D-FBK-052`][D-FBK-052] — A judgement that holds the evidence makes the change · 2026-08-24
- [`D-FBK-051`][D-FBK-051] — What the TER already holds is a lookup of its own · 2026-08-21
- [`D-FBK-050`][D-FBK-050] — A package's release policy is asked rather than derived · 2026-08-19
- [`D-FBK-047`][D-FBK-047] — The debrief asks what an answer left out · 2026-08-18
- [`D-FBK-048`][D-FBK-048] — The debrief is offered as a prompt where the channel is · 2026-08-18
- [`D-FBK-049`][D-FBK-049] — A stored field states the cap it is cut at · 2026-08-18
- [`D-FBK-045`][D-FBK-045] — A feedback is queued by the call that records it · 2026-08-14
- [`D-FBK-046`][D-FBK-046] — The duplicate-id check names the files and the command · 2026-08-14
- [`D-FBK-041`][D-FBK-041] — What nothing answers for is called unresolved · 2026-08-04
- [`D-FBK-042`][D-FBK-042] — The read-only boundary is the installation · 2026-08-04
- [`D-FBK-043`][D-FBK-043] — A structure is answered with a document rather than with a rule · 2026-08-04
- [`D-FBK-044`][D-FBK-044] — A mangled call is refused rather than taken apart · 2026-08-04
- [`D-FBK-025`][D-FBK-025] — A judgement reads the corpus, decides the shape, and sets the priority · 2026-08-03
- [`D-FBK-026`][D-FBK-026] — The ladder needs an outcome that builds something · 2026-08-03
- [`D-FBK-027`][D-FBK-027] — The server builds what costs its caller round trips · 2026-08-03
- [`D-FBK-038`][D-FBK-038] — What decides a breaking removal is the caller, not the marker · 2026-08-03
- [`D-FBK-039`][D-FBK-039] — A mangled name is rewritten once, and the comparison carries the rest · 2026-08-03
- [`D-FBK-040`][D-FBK-040] — A card a judgement folds into another goes with it · 2026-08-03
- [`D-FBK-011`][D-FBK-011] — The suite holds what one branch can be right about · 2026-08-02
- [`D-FBK-012`][D-FBK-012] — The queue comes first, and the sighting hands over one · 2026-08-02
- [`D-FBK-013`][D-FBK-013] — An empty queue is a state, not a failure · 2026-08-02 · confirmed
- [`D-FBK-014`][D-FBK-014] — Every stage is a directory, and closing is none · 2026-08-02 · confirmed
- [`D-FBK-015`][D-FBK-015] — A priority is a class, and the stamp is the rest · 2026-08-02
- [`D-FBK-016`][D-FBK-016] — A feedback waits on the board rather than behind it · 2026-08-02
- [`D-FBK-017`][D-FBK-017] — A judgement turns a feedback into work, and the work closes it · 2026-08-02
- [`D-FBK-018`][D-FBK-018] — A strength is evidence about a boundary, not about a decision · 2026-08-02 · confirmed
- [`D-FBK-019`][D-FBK-019] — A secret pasted into a feedback is taken out on the way in · 2026-08-02
- [`D-FBK-020`][D-FBK-020] — A session is charged per call, so the calls are what is budgeted · 2026-08-02 · confirmed
- [`D-FBK-021`][D-FBK-021] — A summary feedback is judged against its series, not on its own · 2026-08-02
- [`D-FBK-023`][D-FBK-023] — A correction is judged by what its withdrawal moves · 2026-08-02 · confirmed
- [`D-FBK-024`][D-FBK-024] — A feedback about the caller's conduct toward its user names no surface · 2026-08-02 · confirmed
- [`D-FBK-006`][D-FBK-006] — A name is cut where the feedback starts to differ · 2026-08-01 · confirmed
- [`D-FBK-007`][D-FBK-007] — How a todo is worked travels with the todo · 2026-08-01
- [`D-FBK-008`][D-FBK-008] — One todo is one file, and the queue is in the names · 2026-08-01
- [`D-FBK-009`][D-FBK-009] — A todo nobody can start waits where it says why · 2026-08-01
- [`D-FBK-001`][D-FBK-001] — The backlog is read out rather than enforced · 2026-07-31 · confirmed
- [`D-FBK-002`][D-FBK-002] — The order of the work is declared, not inferred · 2026-07-31 · confirmed
- [`D-FBK-004`][D-FBK-004] — A feedback asks the caller which model is recording it · 2026-07-31 · confirmed

[D-FBK-054]: feedback/fbk-054-the-server-answers-what-is-registered-and-a-person-answers-what-it-looks-like.md
[D-FBK-055]: feedback/fbk-055-a-registration-file-is-checked-after-the-cache-flush-or-not-at-all.md
[D-FBK-053]: feedback/fbk-053-a-migrated-memory-is-judged-against-the-source-its-rule-has.md
[D-FBK-052]: feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md
[D-FBK-051]: feedback/fbk-051-what-the-ter-already-holds-is-a-lookup-of-its-own.md
[D-FBK-050]: feedback/fbk-050-a-packages-release-policy-is-asked-rather-than-derived.md
[D-FBK-047]: feedback/fbk-047-the-debrief-asks-what-an-answer-left-out.md
[D-FBK-048]: feedback/fbk-048-the-debrief-is-offered-as-a-prompt-where-the-channel-is.md
[D-FBK-049]: feedback/fbk-049-a-stored-field-states-the-cap-it-is-cut-at.md
[D-FBK-045]: feedback/fbk-045-a-feedback-is-queued-by-the-call-that-records-it.md
[D-FBK-046]: feedback/fbk-046-the-duplicate-id-check-names-the-files-and-the-command.md
[D-FBK-041]: feedback/fbk-041-what-nothing-answers-for-is-called-unresolved.md
[D-FBK-042]: feedback/fbk-042-the-read-only-boundary-is-the-installation.md
[D-FBK-043]: feedback/fbk-043-a-structure-is-answered-with-a-document-rather-than-with-a-rule.md
[D-FBK-044]: feedback/fbk-044-a-mangled-call-is-refused-rather-than-taken-apart.md
[D-FBK-025]: feedback/fbk-025-a-judgement-reads-the-corpus-decides-the-shape-and-sets-the-priority.md
[D-FBK-026]: feedback/fbk-026-the-ladder-needs-an-outcome-that-builds-something.md
[D-FBK-027]: feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md
[D-FBK-038]: feedback/fbk-038-what-decides-a-breaking-removal-is-the-caller-not-the-marker.md
[D-FBK-039]: feedback/fbk-039-a-mangled-name-is-rewritten-once-and-the-comparison-carries-the-rest.md
[D-FBK-040]: feedback/fbk-040-a-card-a-judgement-folds-into-another-goes-with-it.md
[D-FBK-011]: feedback/fbk-011-the-suite-holds-what-one-branch-can-be-right-about.md
[D-FBK-012]: feedback/fbk-012-the-queue-comes-first-and-the-sighting-hands-over-one.md
[D-FBK-013]: feedback/fbk-013-an-empty-queue-is-a-state-not-a-failure.md
[D-FBK-014]: feedback/fbk-014-every-stage-is-a-directory-and-closing-is-none.md
[D-FBK-015]: feedback/fbk-015-a-priority-is-a-class-and-the-stamp-is-the-rest.md
[D-FBK-016]: feedback/fbk-016-a-feedback-waits-on-the-board-rather-than-behind-it.md
[D-FBK-017]: feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md
[D-FBK-018]: feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md
[D-FBK-019]: feedback/fbk-019-a-secret-pasted-into-a-feedback-is-taken-out-on-the-way-in.md
[D-FBK-020]: feedback/fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md
[D-FBK-021]: feedback/fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md
[D-FBK-023]: feedback/fbk-023-a-correction-is-judged-by-what-its-withdrawal-moves.md
[D-FBK-024]: feedback/fbk-024-a-feedback-about-the-callers-conduct-toward-its-user-names-no-surface.md
[D-FBK-006]: feedback/fbk-006-a-name-is-cut-where-the-feedback-starts-to-differ.md
[D-FBK-007]: feedback/fbk-007-how-a-todo-is-worked-travels-with-the-todo.md
[D-FBK-008]: feedback/fbk-008-one-todo-is-one-file-and-the-queue-is-in-the-names.md
[D-FBK-009]: feedback/fbk-009-a-todo-nobody-can-start-waits-where-it-says-why.md
[D-FBK-001]: feedback/fbk-001-the-backlog-is-read-out-rather-than-enforced.md
[D-FBK-002]: feedback/fbk-002-the-order-of-the-work-is-declared-not-inferred.md
[D-FBK-004]: feedback/fbk-004-a-feedback-asks-the-caller-which-model-is-recording-it.md

### documentation

- [`D-DOC-066`][D-DOC-066] — A dated section says what the reading changed · 2026-08-28
- [`D-DOC-059`][D-DOC-059] — The recording report reads both its days in UTC · 2026-08-27
- [`D-DOC-060`][D-DOC-060] — The worktree says a todo is in hand · 2026-08-27
- [`D-DOC-061`][D-DOC-061] — A todo's id is derived rather than counted · 2026-08-27
- [`D-DOC-062`][D-DOC-062] — A todo's head is front matter · 2026-08-27
- [`D-DOC-063`][D-DOC-063] — A prose sweep reaches what nobody is holding · 2026-08-27
- [`D-DOC-064`][D-DOC-064] — A link to an archived feedback is repaired where the branch rebases · 2026-08-27
- [`D-DOC-058`][D-DOC-058] — A recording is reported against the day its sources moved · 2026-08-26
- [`D-DOC-044`][D-DOC-044] — A failing test names the decisions it was holding · 2026-08-23
- [`D-DOC-045`][D-DOC-045] — What a listing reads is front matter · 2026-08-23
- [`D-DOC-046`][D-DOC-046] — A title is the name an entry is read by · 2026-08-23
- [`D-DOC-047`][D-DOC-047] — An entry is filed under the title it has · 2026-08-23
- [`D-DOC-048`][D-DOC-048] — A test declares the decision it holds · 2026-08-23
- [`D-DOC-049`][D-DOC-049] — A requirement's tests are declared where the test is · 2026-08-23
- [`D-DOC-050`][D-DOC-050] — What is written about a class is a lookup · 2026-08-23
- [`D-DOC-051`][D-DOC-051] — A name carries one claim · 2026-08-23
- [`D-DOC-052`][D-DOC-052] — A revoked entry names no test · 2026-08-23
- [`D-DOC-053`][D-DOC-053] — What no test holds is unheld for a reason in the entry · 2026-08-23
- [`D-DOC-054`][D-DOC-054] — A held decision is read when its behaviour moves · 2026-08-23
- [`D-DOC-055`][D-DOC-055] — A revoked entry is one whose Wrong if fired · 2026-08-23
- [`D-DOC-056`][D-DOC-056] — A subject this server owns is named affirmatively · 2026-08-23
- [`D-DOC-057`][D-DOC-057] — The decision corpus carries no duplicate pair · 2026-08-23
- [`D-DOC-038`][D-DOC-038] — A requirement carries the day it was judged · 2026-08-22
- [`D-DOC-039`][D-DOC-039] — An open decision somebody has been back to is counted apart · 2026-08-22
- [`D-DOC-040`][D-DOC-040] — A renamed tool is corrected where the name is a claim · 2026-08-22
- [`D-DOC-042`][D-DOC-042] — A backticked name is a claim that the thing exists now · 2026-08-22
- [`D-DOC-043`][D-DOC-043] — A test is what holds an entry to the code · 2026-08-22
- [`D-DOC-034`][D-DOC-034] — A recording is answered from the checkout the command makes · 2026-08-18
- [`D-DOC-035`][D-DOC-035] — What the prose costs is counted beside how long a sentence is · 2026-08-18
- [`D-DOC-036`][D-DOC-036] — A todo serves a decision by its id · 2026-08-18
- [`D-DOC-037`][D-DOC-037] — A decision nobody has revisited is held to the console · 2026-08-18
- [`D-DOC-033`][D-DOC-033] — The derived half of a tool page stays committed · 2026-08-14
- [`D-DOC-032`][D-DOC-032] — A section heading is the label a contents list shows · 2026-08-13
- [`D-DOC-024`][D-DOC-024] — The site's theme is a package this repository keeps none of · 2026-08-12
- [`D-DOC-025`][D-DOC-025] — The documentation is four sections, and the bar carries those four · 2026-08-12
- [`D-DOC-026`][D-DOC-026] — The site is the documentation, and the readme stays out of it · 2026-08-12
- [`D-DOC-027`][D-DOC-027] — The renderer's configuration sits with the pages it renders · 2026-08-12
- [`D-DOC-028`][D-DOC-028] — The renderer is a build tool this repository carries none of · 2026-08-12
- [`D-DOC-029`][D-DOC-029] — The documentation is reStructuredText, and the rest of the corpus is not · 2026-08-12
- [`D-DOC-030`][D-DOC-030] — The front page is a landing page, in the theme's marketing layout · 2026-08-12
- [`D-DOC-031`][D-DOC-031] — A page is railed under a label and headed by a sentence · 2026-08-12
- [`D-DOC-022`][D-DOC-022] — The reader picks the colours and the page remembers it · 2026-08-09
- [`D-DOC-017`][D-DOC-017] — The documentation is published from a copy this repository writes · 2026-08-06
- [`D-DOC-015`][D-DOC-015] — A renumber moves what a link path settles and names the rest · 2026-08-04
- [`D-DOC-016`][D-DOC-016] — An answer that reads no installation is derived and checked · 2026-08-04
- [`D-DOC-009`][D-DOC-009] — Prose names what counts rather than the count · 2026-08-03 · confirmed
- [`D-DOC-010`][D-DOC-010] — `targetVersion` opens with one sentence and diverges after it · 2026-08-03 · confirmed
- [`D-DOC-011`][D-DOC-011] — A schema is written as the shape it validates · 2026-08-03
- [`D-DOC-012`][D-DOC-012] — The second root is an installation this repository writes · 2026-08-03
- [`D-DOC-013`][D-DOC-013] — A commit here is three keywords and a condensed subject · 2026-08-03 · confirmed
- [`D-DOC-014`][D-DOC-014] — A record directory keeps its listing and the site carries its description · 2026-08-03
- [`D-DOC-003`][D-DOC-003] — A decision says what came back · 2026-08-02 · confirmed
- [`D-DOC-004`][D-DOC-004] — A requirement is written in the same sections as a decision · 2026-08-02
- [`D-DOC-005`][D-DOC-005] — A number is three digits so a group lists in order · 2026-08-02 · confirmed
- [`D-DOC-006`][D-DOC-006] — A recording says what it is of · 2026-08-02
- [`D-DOC-007`][D-DOC-007] — One page per tool, and the answer on it whole · 2026-08-02
- [`D-DOC-008`][D-DOC-008] — The calls that reach outside stay in the shared table · 2026-08-02 · confirmed
- [`D-DOC-001`][D-DOC-001] — A table is written so it reads unrendered · 2026-08-01 · confirmed
- [`D-DOC-002`][D-DOC-002] — The prose rule is measured, and only the lead fails on it · 2026-08-01

[D-DOC-066]: documentation/doc-066-a-dated-section-says-what-the-reading-changed.md
[D-DOC-059]: documentation/doc-059-the-recording-report-reads-both-its-days-in-utc.md
[D-DOC-060]: documentation/doc-060-the-worktree-says-a-todo-is-in-hand.md
[D-DOC-061]: documentation/doc-061-a-todos-id-is-derived-rather-than-counted.md
[D-DOC-062]: documentation/doc-062-a-todos-head-is-front-matter.md
[D-DOC-063]: documentation/doc-063-a-prose-sweep-reaches-what-nobody-is-holding.md
[D-DOC-064]: documentation/doc-064-a-link-to-an-archived-feedback-is-repaired-where-the-branch-rebases.md
[D-DOC-058]: documentation/doc-058-a-recording-is-reported-against-the-day-its-sources-moved.md
[D-DOC-044]: documentation/doc-044-a-failing-test-names-the-decisions-it-was-holding.md
[D-DOC-045]: documentation/doc-045-what-a-listing-reads-is-front-matter.md
[D-DOC-046]: documentation/doc-046-a-title-is-the-name-an-entry-is-read-by.md
[D-DOC-047]: documentation/doc-047-an-entry-is-filed-under-the-title-it-has.md
[D-DOC-048]: documentation/doc-048-a-test-declares-the-decision-it-holds.md
[D-DOC-049]: documentation/doc-049-a-requirements-tests-are-declared-where-the-test-is.md
[D-DOC-050]: documentation/doc-050-what-is-written-about-a-class-is-a-lookup.md
[D-DOC-051]: documentation/doc-051-a-name-carries-one-claim.md
[D-DOC-052]: documentation/doc-052-a-revoked-entry-names-no-test.md
[D-DOC-053]: documentation/doc-053-what-no-test-holds-is-unheld-for-a-reason-in-the-entry.md
[D-DOC-054]: documentation/doc-054-a-held-decision-is-read-when-its-behaviour-moves.md
[D-DOC-055]: documentation/doc-055-a-revoked-entry-is-one-whose-wrong-if-fired.md
[D-DOC-056]: documentation/doc-056-a-subject-this-server-owns-is-named-affirmatively.md
[D-DOC-057]: documentation/doc-057-the-decision-corpus-carries-no-duplicate-pair.md
[D-DOC-038]: documentation/doc-038-a-requirement-carries-the-day-it-was-judged.md
[D-DOC-039]: documentation/doc-039-an-open-decision-somebody-has-been-back-to-is-counted-apart.md
[D-DOC-040]: documentation/doc-040-a-renamed-tool-is-corrected-where-the-name-is-a-claim.md
[D-DOC-042]: documentation/doc-042-a-backticked-name-is-a-claim-that-the-thing-exists-now.md
[D-DOC-043]: documentation/doc-043-a-test-is-what-holds-an-entry-to-the-code.md
[D-DOC-034]: documentation/doc-034-a-recording-is-answered-from-the-checkout-the-command-makes.md
[D-DOC-035]: documentation/doc-035-what-the-prose-costs-is-counted-beside-how-long-a-sentence-is.md
[D-DOC-036]: documentation/doc-036-a-todo-serves-a-decision-by-its-id.md
[D-DOC-037]: documentation/doc-037-a-decision-nobody-has-revisited-is-held-to-the-console.md
[D-DOC-033]: documentation/doc-033-the-derived-half-of-a-tool-page-stays-committed.md
[D-DOC-032]: documentation/doc-032-a-section-heading-is-the-label-a-contents-list-shows.md
[D-DOC-024]: documentation/doc-024-the-sites-theme-is-a-package-this-repository-keeps-none-of.md
[D-DOC-025]: documentation/doc-025-the-documentation-is-four-sections-and-the-bar-carries-those-four.md
[D-DOC-026]: documentation/doc-026-the-site-is-the-documentation-and-the-readme-stays-out-of-it.md
[D-DOC-027]: documentation/doc-027-the-renderers-configuration-sits-with-the-pages-it-renders.md
[D-DOC-028]: documentation/doc-028-the-renderer-is-a-build-tool-this-repository-carries-none-of.md
[D-DOC-029]: documentation/doc-029-the-documentation-is-restructuredtext-and-the-rest-of-the-corpus-is-not.md
[D-DOC-030]: documentation/doc-030-the-front-page-is-a-landing-page-in-the-themes-marketing-layout.md
[D-DOC-031]: documentation/doc-031-a-page-is-railed-under-a-label-and-headed-by-a-sentence.md
[D-DOC-022]: documentation/doc-022-the-reader-picks-the-colours-and-the-page-remembers-it.md
[D-DOC-017]: documentation/doc-017-the-documentation-is-published-from-a-copy-this-repository-writes.md
[D-DOC-015]: documentation/doc-015-a-renumber-moves-what-a-link-path-settles-and-names-the-rest.md
[D-DOC-016]: documentation/doc-016-an-answer-that-reads-no-installation-is-derived-and-checked.md
[D-DOC-009]: documentation/doc-009-prose-names-what-counts-rather-than-the-count.md
[D-DOC-010]: documentation/doc-010-targetversion-opens-with-one-sentence-and-diverges-after-it.md
[D-DOC-011]: documentation/doc-011-a-schema-is-written-as-the-shape-it-validates.md
[D-DOC-012]: documentation/doc-012-the-second-root-is-an-installation-this-repository-writes.md
[D-DOC-013]: documentation/doc-013-a-commit-here-is-three-keywords-and-a-condensed-subject.md
[D-DOC-014]: documentation/doc-014-a-record-directory-keeps-its-listing-and-the-site-carries-its-description.md
[D-DOC-003]: documentation/doc-003-a-decision-says-what-came-back.md
[D-DOC-004]: documentation/doc-004-a-requirement-is-written-in-the-same-sections-as-a-decision.md
[D-DOC-005]: documentation/doc-005-a-number-is-three-digits-so-a-group-lists-in-order.md
[D-DOC-006]: documentation/doc-006-a-recording-says-what-it-is-of.md
[D-DOC-007]: documentation/doc-007-one-page-per-tool-and-the-answer-on-it-whole.md
[D-DOC-008]: documentation/doc-008-the-calls-that-reach-outside-stay-in-the-shared-table.md
[D-DOC-001]: documentation/doc-001-a-table-is-written-so-it-reads-unrendered.md
[D-DOC-002]: documentation/doc-002-the-prose-rule-is-measured-and-only-the-lead-fails-on-it.md

### code

- [`D-COD-007`][D-COD-007] — A pin goes to the newest version the declared PHP floor allows · 2026-08-29
- [`D-COD-008`][D-COD-008] — Each PHP the matrix runs resolves the dependencies it can take · 2026-08-29
- [`D-COD-006`][D-COD-006] — A test writes below a temporary path that names its own process · 2026-08-24
- [`D-COD-005`][D-COD-005] — The static analysis runs at level 7 · 2026-08-23
- [`D-COD-004`][D-COD-004] — What leaves this process goes through one seam · 2026-08-03
- [`D-COD-003`][D-COD-003] — A directory is read through symfony/finder · 2026-08-02 · confirmed
- [`D-COD-001`][D-COD-001] — One file declares one class · 2026-08-01 · confirmed
- [`D-COD-002`][D-COD-002] — The upkeep CLI is a Symfony Console application · 2026-08-01

[D-COD-007]: code/cod-007-a-pin-goes-to-the-newest-version-the-declared-php-floor-allows.md
[D-COD-008]: code/cod-008-each-php-the-matrix-runs-resolves-the-dependencies-it-can-take.md
[D-COD-006]: code/cod-006-a-test-writes-below-a-temporary-path-that-names-its-own-process.md
[D-COD-005]: code/cod-005-the-static-analysis-runs-at-level-7.md
[D-COD-004]: code/cod-004-what-leaves-this-process-goes-through-one-seam.md
[D-COD-003]: code/cod-003-a-directory-is-read-through-symfony-finder.md
[D-COD-001]: code/cod-001-one-file-declares-one-class.md
[D-COD-002]: code/cod-002-the-upkeep-cli-is-a-symfony-console-application.md

### Revoked, and kept as the record

- [`D-AUD-016`][D-AUD-016] — A count is answered and the row behind it stays with the installation · 2026-09-01 → D-AUD-017
- [`D-AUD-013`][D-AUD-013] — A competing route is corrected where it is written · 2026-08-25 → D-AUD-014
- [`D-KNW-109`][D-KNW-109] — Whether a core commit owes a sign-off is a subject this server owns · 2026-08-24 → D-KNW-110
- [`D-KNW-110`][D-KNW-110] — A core commit message carries three trailers and the hook's Change-Id · 2026-08-24 → D-KNW-125
- [`D-DOC-041`][D-DOC-041] — An entry outgrown by its own history is read out · 2026-08-22 → D-DOC-066
- [`D-ANS-081`][D-ANS-081] — A symptom is answered across the domain it was observed in · 2026-08-18 → D-ANS-084
- [`D-SKL-040`][D-SKL-040] — A skill whose product is a report says it is a file · 2026-08-14 → D-SKL-042
- [`D-DOC-018`][D-DOC-018] — The site opens on the readme · 2026-08-09 → D-DOC-026
- [`D-DOC-019`][D-DOC-019] — The site's stylesheet and script are built files · 2026-08-09 → D-DOC-024
- [`D-DOC-020`][D-DOC-020] — The site is rendered by one command that installs what it needs · 2026-08-09 → D-DOC-028
- [`D-DOC-021`][D-DOC-021] — The site is searched in a dialog opened with Ctrl-K · 2026-08-09 → D-DOC-024
- [`D-DOC-023`][D-DOC-023] — The site is built to the TYPO3 Support App design system · 2026-08-09 → D-DOC-024
- [`D-DIS-015`][D-DIS-015] — The installed entrypoint is named relatively wherever it exists · 2026-08-08 → D-DIS-016
- [`D-SKL-027`][D-SKL-027] — A draft declares itself under this server's own metadata key · 2026-08-08 → D-SKL-087
- [`D-SKL-015`][D-SKL-015] — A step is skipped only where it has already run · 2026-08-04 → D-SKL-034
- [`D-SKL-016`][D-SKL-016] — Acting on a conformance report earns a task skill of its own · 2026-08-04 → D-SKL-064
- [`D-ANS-034`][D-ANS-034] — A source outside this package answers JSON, or it did not answer · 2026-08-03 → D-ANS-096
- [`D-FBK-037`][D-FBK-037] — API stability is worth a lookup and git state is not · 2026-08-03 → D-FBK-038
- [`D-KNW-040`][D-KNW-040] — What asserts a rendered output is a subject this server owns · 2026-08-03 → D-KNW-044
- [`D-SKL-011`][D-SKL-011] — The call plan a skill writes down is measured · 2026-08-03 → D-SKL-043
- [`D-ANS-023`][D-ANS-023] — A ViewHelper question is answered by widening the manual index · 2026-08-02 → D-ANS-026
- [`D-ANS-027`][D-ANS-027] — The Extbase fork is placed where a caller who has not chosen passes · 2026-08-02 → D-ANS-039
- [`D-DIS-008`][D-DIS-008] — The columns TYPO3 derives are reachable where the database server is · 2026-08-02 → D-DIS-012
- [`D-FBK-022`][D-FBK-022] — A feedback brings its card in the commit that brings it in · 2026-08-02 → D-FBK-045
- [`D-KNW-014`][D-KNW-014] — The record a v14 preview template is handed is a subject this server owns · 2026-08-02 → D-KNW-020
- [`D-KNW-015`][D-KNW-015] — The corpus states what a Fluid preview template replaces · 2026-08-02 → D-KNW-021
- [`D-KNW-025`][D-KNW-025] — What a backend preview owes the editor is a subject this server owns · 2026-08-02 → D-KNW-037
- [`D-FBK-005`][D-FBK-005] — The queue is worked before the pile is sighted · 2026-08-01 → D-FBK-012
- [`D-FBK-010`][D-FBK-010] — `main` carries the state and the branch carries the work · 2026-08-01
- [`D-SCO-007`][D-SCO-007] — The signals are combined per call · 2026-08-01 → D-SCO-008
- [`D-SCO-008`][D-SCO-008] — The path decides, and the answer may say it cannot · 2026-08-01 → D-KNW-005
- [`D-FBK-003`][D-FBK-003] — A session is handed one todo, not the file · 2026-07-31 → D-FBK-002
- [`D-KNW-003`][D-KNW-003] — `provenance` is not the third spelling of `binding`, and stays · 2026-07-30 → D-KNW-005
- [`D-ANS-001`][D-ANS-001] — The unanswered result keeps its shape and gains a reason · 2026-07-29 → D-ANS-005
- [`D-AUD-002`][D-AUD-002] — A client is offered the `all` or the `project` profile · 2026-07-29 → D-AUD-004
- [`D-CAT-002`][D-CAT-002] — The index of worked examples is curated · 2026-07-29 → D-CAT-007
- [`D-DIS-002`][D-DIS-002] — Discovery honours the declared vendor-dir and bin-dir · 2026-07-29 → D-DIS-007
- [`D-DIS-003`][D-DIS-003] — A label query is words and the console is asked with a regex · 2026-07-29
- [`D-GUI-002`][D-GUI-002] — The commit workflow is asked for, not inferred · 2026-07-29 → D-GUI-010
- [`D-KNW-001`][D-KNW-001] — Sitepackage work is answered from the General category · 2026-07-29 → D-KNW-006
- [`D-KNW-002`][D-KNW-002] — A hint about typo3/testing-framework is verified against tags, not against the checkouts · 2026-07-29 → D-KNW-106
- [`D-SCO-001`][D-SCO-001] — Outside the core the core test guide declines rather than adapts · 2026-07-29
- [`D-SCO-004`][D-SCO-004] — The frontend is recognised by name · 2026-07-29
- [`D-VER-002`][D-VER-002] — The prose is not bound; it says which half it is · 2026-07-29 → D-VER-005

[D-AUD-016]: audience/aud-016-a-count-is-answered-and-the-row-behind-it-stays-with-the-installation.md
[D-AUD-013]: audience/aud-013-a-competing-route-is-corrected-where-it-is-written.md
[D-KNW-109]: knowledge/knw-109-whether-a-core-commit-owes-a-sign-off-is-a-subject-this-server-owns.md
[D-KNW-110]: knowledge/knw-110-a-core-commit-message-carries-three-trailers-and-the-hooks-change-id.md
[D-DOC-041]: documentation/doc-041-an-entry-outgrown-by-its-own-history-is-read-out.md
[D-ANS-081]: answers/ans-081-a-symptom-is-answered-across-the-domain-it-was-observed-in.md
[D-SKL-040]: task-skills/skl-040-a-skill-whose-product-is-a-report-says-it-is-a-file.md
[D-DOC-018]: documentation/doc-018-the-site-opens-on-the-readme.md
[D-DOC-019]: documentation/doc-019-the-sites-stylesheet-and-script-are-built-files.md
[D-DOC-020]: documentation/doc-020-the-site-is-rendered-by-one-command-that-installs-what-it-needs.md
[D-DOC-021]: documentation/doc-021-the-site-is-searched-in-a-dialog-opened-with-ctrl-k.md
[D-DOC-023]: documentation/doc-023-the-site-is-built-to-the-typo3-support-app-design-system.md
[D-DIS-015]: discovery/dis-015-the-installed-entrypoint-is-named-relatively-wherever-it-exists.md
[D-SKL-027]: task-skills/skl-027-a-draft-declares-itself-under-this-servers-own-metadata-key.md
[D-SKL-015]: task-skills/skl-015-a-step-is-skipped-only-where-it-has-already-run.md
[D-SKL-016]: task-skills/skl-016-acting-on-a-conformance-report-earns-a-task-skill-of-its-own.md
[D-ANS-034]: answers/ans-034-a-source-outside-this-package-answers-json-or-it-did-not-answer.md
[D-FBK-037]: feedback/fbk-037-api-stability-is-worth-a-lookup-and-git-state-is-not.md
[D-KNW-040]: knowledge/knw-040-what-asserts-a-rendered-output-is-a-subject-this-server-owns.md
[D-SKL-011]: task-skills/skl-011-the-call-plan-a-skill-writes-down-is-measured.md
[D-ANS-023]: answers/ans-023-a-viewhelper-question-is-answered-by-widening-the-manual-index.md
[D-ANS-027]: answers/ans-027-the-extbase-fork-is-placed-where-a-caller-who-has-not-chosen-passes.md
[D-DIS-008]: discovery/dis-008-the-columns-typo3-derives-are-reachable-where-the-database-server-is.md
[D-FBK-022]: feedback/fbk-022-a-feedback-brings-its-card-in-the-commit-that-brings-it-in.md
[D-KNW-014]: knowledge/knw-014-the-record-a-v14-preview-template-is-handed-is-a-subject-this-server-owns.md
[D-KNW-015]: knowledge/knw-015-the-corpus-states-what-a-fluid-preview-template-replaces.md
[D-KNW-025]: knowledge/knw-025-what-a-backend-preview-owes-the-editor-is-a-subject-this-server-owns.md
[D-FBK-005]: feedback/fbk-005-the-queue-is-worked-before-the-pile-is-sighted.md
[D-FBK-010]: feedback/fbk-010-main-carries-the-state-and-the-branch-carries-the-work.md
[D-SCO-007]: scope/sco-007-the-signals-are-combined-per-call.md
[D-SCO-008]: scope/sco-008-the-path-decides-and-the-answer-may-say-it-cannot.md
[D-FBK-003]: feedback/fbk-003-a-session-is-handed-one-todo-not-the-file.md
[D-KNW-003]: knowledge/knw-003-provenance-is-not-the-third-spelling-of-binding-and-stays.md
[D-ANS-001]: answers/ans-001-the-unanswered-result-keeps-its-shape-and-gains-a-reason.md
[D-AUD-002]: audience/aud-002-a-client-is-offered-the-all-or-the-project-profile.md
[D-CAT-002]: catalog/cat-002-the-index-of-worked-examples-is-curated.md
[D-DIS-002]: discovery/dis-002-discovery-honours-the-declared-vendor-dir-and-bin-dir.md
[D-DIS-003]: discovery/dis-003-a-label-query-is-words-and-the-console-is-asked-with-a-regex.md
[D-GUI-002]: guides/gui-002-the-commit-workflow-is-asked-for-not-inferred.md
[D-KNW-001]: knowledge/knw-001-sitepackage-work-is-answered-from-the-general-category.md
[D-KNW-002]: knowledge/knw-002-a-hint-about-typo3-testing-framework-is-verified-against-tags-not-against-the-checkouts.md
[D-SCO-001]: scope/sco-001-outside-the-core-the-core-test-guide-declines-rather-than-adapts.md
[D-SCO-004]: scope/sco-004-the-frontend-is-recognised-by-name.md
[D-VER-002]: versions/ver-002-the-prose-is-not-bound-it-says-which-half-it-is.md
