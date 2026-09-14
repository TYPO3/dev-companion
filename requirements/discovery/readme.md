# Discovery — which installation is read, and how

What the server may conclude about the checkout it started in, how it finds it, and how a client gets set up to reach it. A layout the server cannot read is a one-line fix for the user; five tools that go quiet are not.

See [the requirements readme](../readme.md) for how a session writes an entry and when it adds one.

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

[R-DIS-001]: dis-001-discovery-belongs-to-the-stdio-entrypoint-alone.md
[R-DIS-002]: dis-002-the-packages-are-read-from-the-declared-vendor-directory.md
[R-DIS-003]: dis-003-the-console-is-looked-for-where-the-installation-declares-it.md
[R-DIS-004]: dis-004-the-extension-being-worked-on-is-part-of-its-own-installation.md
[R-DIS-005]: dis-005-a-repository-with-no-installation-around-it-is-not-one.md
[R-DIS-006]: dis-006-nothing-is-started-as-a-side-effect-of-a-lookup.md
[R-DIS-007]: dis-007-the-installation-and-the-console-can-be-named-outright.md
[R-DIS-008]: dis-008-a-failed-discovery-names-where-it-looked.md
[R-DIS-009]: dis-009-a-negative-is-never-remembered.md
[R-DIS-010]: dis-010-reachable-and-ready-are-two-questions.md
[R-DIS-011]: dis-011-the-entrypoint-installs-its-own-client-configuration.md
[R-DIS-012]: dis-012-codex-setup-installs-the-server-and-its-skills.md
[R-DIS-013]: dis-013-every-supported-agent-client-can-be-installed-into.md
[R-DIS-014]: dis-014-an-installed-skill-is-a-workflow-not-a-prompt-fragment.md
[R-DIS-015]: dis-015-the-ddev-client-entry-names-an-entrypoint-that-exists.md
[R-DIS-016]: dis-016-a-repository-that-serves-two-majors-is-answered-for-both.md
[R-DIS-017]: dis-017-an-extension-below-tests-is-the-test-setups.md
[R-DIS-018]: dis-018-a-console-command-never-inherits-the-clients-stdin.md
[R-DIS-019]: dis-019-a-registry-with-no-command-is-answered-by-the-installation-itself.md
[R-DIS-020]: dis-020-the-project-records-which-clients-are-installed-in-it.md
[R-DIS-021]: dis-021-the-client-entry-is-rewritten-when-the-project-outgrows-it.md
[R-DIS-022]: dis-022-a-call-can-tell-where-it-came-from.md
[R-DIS-023]: dis-023-an-install-says-what-is-left-before-a-tool-can-be-called.md
[R-DIS-024]: dis-024-the-published-directories-ignore-themselves.md
[R-DIS-025]: dis-025-a-publication-that-went-stale-says-so-before-the-first-call.md
