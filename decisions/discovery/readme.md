# Discovery — which installation is read, and how

Everything the server reads from a working installation rests on finding it
first: the packages, the console, the version. A wrong answer here is invisible,
because it looks exactly like an installation that has nothing to say.

See [the decisions readme](../readme.md) for how an entry is written and when
one is added.

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

[D-DIS-022]: dis-022-the-schema-the-database-has-is-answered-and-the-rows-are-not.md
[D-DIS-023]: dis-023-the-container-is-assembled-a-second-time-to-be-read.md
[D-DIS-021]: dis-021-a-stale-publication-is-put-back-where-the-server-starts.md
[D-DIS-019]: dis-019-a-project-root-is-found-from-what-its-manifest-declares.md
[D-DIS-017]: dis-017-the-skills-reach-a-project-through-the-installer.md
[D-DIS-018]: dis-018-what-install-writes-stays-inside-the-project.md
[D-DIS-014]: dis-014-the-refresh-is-wired-by-the-project.md
[D-DIS-016]: dis-016-how-an-entrypoint-may-be-named-is-a-per-client-question.md
[D-DIS-013]: dis-013-the-record-holds-a-digest-of-what-was-published.md
[D-DIS-011]: dis-011-what-was-read-from-the-installation-lives-as-long-as-the-call.md
[D-DIS-012]: dis-012-the-driver-decides-whether-the-derived-columns-need-the-database-server.md
[D-DIS-010]: dis-010-what-this-package-writes-into-a-project-ignores-itself.md
[D-DIS-007]: dis-007-the-ddev-console-is-named-by-the-mount-not-by-the-variable.md
[D-DIS-009]: dis-009-installed-is-one-step-short-of-callable.md
[D-DIS-006]: dis-006-the-installation-stays-worked-out-from-the-directory-the-server-was-started-in.md
[D-DIS-005]: dis-005-a-registry-with-no-console-command-is-read-by-booting-the-installation.md
[D-DIS-001]: dis-001-the-root-package-counts-as-an-installed-package.md
[D-DIS-004]: dis-004-the-version-comes-from-the-core-package-not-from-the-console.md

### Revoked, and kept as the record

- [`D-DIS-015`][D-DIS-015] — The installed entrypoint is named relatively wherever it exists · 2026-08-08 → D-DIS-016
- [`D-DIS-008`][D-DIS-008] — The columns TYPO3 derives are reachable where the database server is · 2026-08-02 → D-DIS-012
- [`D-DIS-002`][D-DIS-002] — Discovery honours the declared vendor-dir and bin-dir · 2026-07-29 → D-DIS-007
- [`D-DIS-003`][D-DIS-003] — A label query is words and the console is asked with a regex · 2026-07-29

[D-DIS-015]: dis-015-the-installed-entrypoint-is-named-relatively-wherever-it-exists.md
[D-DIS-008]: dis-008-the-columns-typo3-derives-are-reachable-where-the-database-server-is.md
[D-DIS-002]: dis-002-discovery-honours-the-declared-vendor-dir-and-bin-dir.md
[D-DIS-003]: dis-003-a-label-query-is-words-and-the-console-is-asked-with-a-regex.md
