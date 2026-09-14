---
id: D-DIS-014
title: 'The refresh is wired by the project'
date: 2026-08-08
status: open
coveredBy:
  - InstallerRecordTest::updateSaysSoWhereNothingIsInstalledAtAll
  - InstallerTest::codexUpdateRemovesSkillsTrackedByThePreviousCentralState
---

# D-DIS-014 — The refresh is wired by the project

**Of Laravel Boost's three ways to keep a published copy current, this package
documents the Composer hook and already has the stale-name sweep. It does not
take the marker fence.**

`R-DIS-025` says a publication that went stale says so, and stops there:
somebody has to run `update`. Boost writes guidance into the user's project too,
so its three answers to the same problem came first, before anyone invented one
here.

## Evidence

- Boost's `GuidelineWriter` wraps what it writes in
  `<laravel-boost-guidelines>`, finds the block again with
  `/<laravel-boost-guidelines>.*?<\/laravel-boost-guidelines>/s`, and replaces
  it or appends one, under a retried file lock. What it fences is prose written
  into `CLAUDE.md` and `AGENTS.md` — markdown the user writes in as well.
- What this installer shares with a user is `.mcp.json`, `.codex/config.toml`
  and eleven more. JSON and TOML, merged by key and by section, with every field
  it does not own kept: `carriedOver()` and `rewrittenTomlSection()`,
  `D-AUD-005` and `D-AUD-006`. The skills go into a directory per skill,
  replaced whole, beside skills the project wrote itself.
- Boost's `SkillWriter::sync()` takes the names the caller tracked, `array_diff`
  against what it publishes now, and removes the rest.
  `Installer::publishSkills()` is that against `state.json`, and `outdated()`
  says it before the update runs.
- Boost does not write the Composer hook. Neither its own `composer.json`, its
  `InstallCommand` nor its `Support\Composer` touches an application's scripts.
  The Laravel documentation tells the reader to add
  `"post-update-cmd": ["@php artisan boost:update --ansi"]` themselves, and
  `UpdateCommand::runningAsComposerScript()` only keeps it from a prompt there.
- Measured in a fixture project on 2026-08-08. Composer pushes the declared
  `bin-dir` onto `PATH` before it runs a script, so a bare `probe` resolved from
  `.build/bin`. The script ran in the project root. An exit 1 ended the run with
  `Script probe handling the post-update-cmd event returned with error code 1`
  and Composer's own exit code 1.

## Decided

- The hook stands documented and nothing writes it. `R-DIS-011` makes a write
  into a project an explicit `install`, and `composer.json` is the file that
  decides what the project consists of. A dev dependency that edits it during an
  update decides for the project what runs on every future one.
- `update` in a project with nothing installed succeeds and says so. It used to
  exit 1, which is right for the person who typed it and fatal for the hook. The
  record ignores itself (`R-DIS-024`), so it is in nobody's checkout. The first
  colleague to run `composer update` would have it fail over a dev tool they
  never set up.
- The fence is not taken, because there is nothing to fence. Where this package
  shares a file the format has keys, and a merge by them keeps more than a text
  block would. Where it owns the bytes it owns a whole directory.
- The sweep needs nothing. It read as the gap this package most clearly had. It
  is in `publishSkills()` and
  `InstallerTest::codexUpdateRemovesSkillsTrackedByThePreviousCentralState`
  holds it.
- Against a hook that covers the fresh clone. `post-update-cmd` fires on
  `update` and on an `install` with no lock file, so a colleague who installs
  from the lock runs nothing. That case stays the notice's, at the next server
  start.

## Assumed

- That a project's `composer.json` is the project's to edit, and that a line
  somebody added themselves is one they will read when it speaks.
- That the moment the package moves is the moment worth a refresh. It is where
  the copy actually goes stale, and every other moment depends on somebody's
  memory.

## Wrong if

- A project wires the hook and `composer update` starts to fail for something
  this package owns. That is a `.codex/config.toml` section it refuses to
  rewrite, or a skills directory it cannot remove. Then a dev tool blocks
  dependency work. The documented line has to be one that cannot fail the run,
  or it comes out of the documentation.
- Somebody wires it and the skills go stale anyway, because this package moved
  without a `composer update`. That is a path repository, or the standalone
  checkout every knowledge session uses. Then the hook is not the mechanism for
  the case this entry took it for.
- This package starts to write prose into a file the user writes in as well.
  Then the fence is exactly the gap, and this entry is why it was not already
  there.
