# Which checkout plays which environment

The standing answer to a question every run asks first. A scenario names a kind
of directory. Which one on this machine plays it belongs here, where it can go
stale without a case in tow. A forward run is a fresh MCP client session with
the installed skills. A session in this repository may neither activate those
skills nor grade its own implementation as behavioral evidence.

Two of them are no longer a machine's business at all.
`bin/cli environment:create` makes `E-SITE` and `E-NONE` below `.environments/`,
so a case that needs an installation to answer from needs nothing off this page.
`bin/cli environment:status` says what this checkout has. A case that names
another covered version is `bin/cli environment:create E-SITE <version>`. That
is one installation per covered line rather than one page entry per machine
(`D-EVI-006`). What stays here is what a scaffold cannot produce. The site
package `REVIEW-01` reviews, the three extension checkouts whose real
infrastructure is what they play (`D-EVI-004`), and the core checkout somebody
works in. The last is where a patch to review comes from.

- **`E-CORE`** — `/home/benji/projects/typo3-cms`, the core repository itself:
  `main` at TYPO3 15.0.0-dev, PHP `^8.5` declared and 8.5 in DDEV, no extensions
  and no sites. The server is not a dependency there, so the install comes from
  this checkout the way `E-EXT`'s does. Done on 2026-08-03, when the published
  copies were a day behind the skills here. Again on 2026-08-06 after the rename
  of `D-AUD-008`, which is what put the new binary in its `.mcp.json` and
  replaced the state directory. And again on 2026-08-08. That last one took the
  dead `typo3-cms-mcp` entry out of `.mcp.json` and the orphaned
  `typo3-extension-release` out of `.claude/skills/`. Neither the rename nor an
  `update` had removed them, because the rename replaced the state directory the
  removal reads from. **Somebody works in it**, which is what it plays.
  `origin/main..main` carried an unpushed core patch and `.claude/worktrees/` a
  branch with more when `REVIEW-03` came unblocked on 2026-08-03. So that review
  finds a diff nobody made for it. `git status` there is never empty. The
  generated `.gitignore` block stands uncommitted, `.claude/` and
  `opencode.json` untracked. A run notes that before it starts rather than reads
  it afterwards as a session that wrote. The DDEV project has stood paused since
  2026-08-02, so a runtime lookup answers unsupported until somebody starts it.
  A GPT-5 mini session reviewed the GD/SVG placeholder patch here on 2026-08-01
  (`feedback/2026-08-01-114526`). It got a subsystem task rather than the
  scenario prompt, so it is precedent for the environment and not a run.
- **`E-SITE`** — **gone from this machine as of 2026-08-06.**
  `/home/benji/projects/site-new` does not exist, so the site package
  `REVIEW-01` reviews has no checkout and nobody can run that review until one
  exists. What it was: site package below `extensions/printworks_sitepackage`,
  TYPO3 14.3.5 under DDEV, with the server as a Composer dependency.
  `ddev exec php vendor/bin/typo3-dev-companion update --agent=claude` refreshed
  it. A replacement is the one entry here that also has to re-require the
  package under its new name. `typo3/dev-companion` resolves from nowhere yet,
  so it is a path repository onto this checkout until the vendor question has an
  answer.
- **`E-EXT`** — two checkouts play it, and which one a run needs is a property
  of the run. In both the server is **not** a Composer dependency, so the client
  reaches it from this checkout.
  `php /home/benji/projects/typo3-cms-mcp/bin/typo3-dev-companion install --agent=claude`
  from the project root publishes the skills and writes the host-php
  `.mcp.json`. Repeat it after any skill change — the published skills are a
  copy and nothing reports it when they are older than the server. The generated
  ignore block in each `.gitignore` and the untracked `.mcp.json` are from that
  install and stay. **Both carry a current install as of 2026-08-08**, twelve
  published skills each. What `.mcp.json` names is the checkout the install ran
  from. So one run from a worktree points the client at that worktree until a
  repeat of the install from here. That is what the run of 2026-08-04 did, and
  undid afterwards. Repeating it in `/home/benji/projects/syntax` that day took
  the checkout from five published skills to nine. The ignore block it had still
  names the five, and the ones that arrived carry a `.gitignore` of their own.
  So a block short of them leaves nothing untracked.

    What 2026-08-08 had to repair in both is the same thing, and it is what an
    `update` after a rename does not reach: the state directory is
    `.typo3-dev-companion` now, so `update` read no state, reported nothing
    installed and removed nothing, while the pre-rename artefacts stayed —
    `syntax` still had `.typo3-cms-mcp/state.json`, both had a dead
    `typo3-cms-mcp` entry naming the removed binary, and `syntax` still
    published `typo3-extension-release`, a skill this server no longer has.
    `install --agent=<client>` per client is what put each right;
    `bootstrap_package` takes three of them — the generic one into
    `.agents/skills`, `claude`, and `opencode`.
  - `/home/benji/projects/syntax` — `bk2k/syntax` 5.0.0, TYPO3 14.3.0 below
    `.build/vendor`, DDEV project `syntax` on PHP 8.2, declared
    `^13.4 || ^14.3`. **Static quality infrastructure is incomplete**:
    php-cs-fixer and phplint in CI, no PHPStan, no `Tests/` at all. `REVIEW-02`
    ran here twice on 2026-07-31, `covered` at 12:21 and `covered` again at
    13:32 — the second against the server that runs the checks, and the first
    run of any kind to reach the console half from an extension checkout. **It
    is committed in**, which is what the run of 2026-08-04 for `D-GUI-002` left
    behind: `bc0946c` on `master`, one line of TypoScript, unpushed and
    untagged, so the tree is clean again and `master` is one ahead of the 5.0.0
    release commit. That run also started the DDEV project and left it up, and
    rewrote `tt_content` uid 1 in its database into a PHP snippet to have
    something to render — the record was `syntax_language=none` with placeholder
    text before. Both are outside git, and a run that needs the fixture back is
    the one that restores it.
  - `/home/benji/projects/bootstrap_package` — TYPO3 14.3.0 below
    `.build/vendor`, DDEV project `bootstrap-package` on PHP 8.5. **Complete**
    infrastructure, which is what it plays. `REVIEW-02` ran here twice on
    2026-07-31, `partial` at 02:55 and `covered` at 08:15 after the corrections
    that run earned.
  - `/home/benji/projects/news` — **gone from this machine as of 2026-08-06**,
    so `EXT-01` and `SKILL-09` have no checkout to run in and a clone has to
    reproduce the properties below rather than take the latest of anything.
    `georgringer/news` 13.0.2 at `3fe278a2`, TYPO3 **13.4.33** below
    `.Build/vendor` (capital B), host PHP 8.3, no DDEV. **A major behind the
    world**, which is what it plays: it declares `^12.4.37
    || ^13.4.15` on PHP `>= 8.1 < 8.5` while 14 is out, so the declared range
    does real work in a run instead of being quoted. It owns
    `Build/Scripts/runTests.sh`, two per-major workflows, 30 test classes and a
    `Documentation/` tree, and at 132 classes it is the only checkout here large
    enough that a review has to choose what to open. Cloned `--single-branch`
    **on purpose**: `origin/main` carries the finished v14 migration, and the
    checkout that plays this environment for `EXT-01` must not hand that answer
    over with one `git log` — fetching another branch into it ends its
    usefulness for that scenario. It is also the one checkout here that carries
    a **correct escaping opt-out**: six `<f:format.htmlentitiesDecode>` around
    `{newsItem.title}` and `{newsItem.alternativeTitle}` in `Detail.html` and
    its two `Styles/` copies, all inside `<n:titleTag>`, whose
    `TitleTagViewHelper` returns nothing and hands the rendered children to
    `NewsTitleProvider`; the installed 13.4.33 core puts the resolved title
    through `htmlspecialchars()` into `<title>|</title>` in `PageRenderer`. That
    shape is what `SKILL-09` needs, so it is the checkout that case is read in.
    `REVIEW-02` ran here on 2026-07-31, `partial` at 14:23; a first attempt at
    14:02 on the `12-13` branch was discarded rather than judged, because that
    branch is 185 commits behind `13.x` and 0 ahead, and the run spent its top
    finding saying so.
