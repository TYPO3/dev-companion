---
id: D-DIS-017
title: The skills reach a project through the installer
date: 2026-08-12
status: open
coveredBy:
  - InstallerTest::aDdevProjectUsesTheContainerPhpAndPublishesTheSkill
---

# D-DIS-017 — The skills reach a project through the installer

**`bin/typo3-dev-companion install` is how the task skills get into a project,
and this package ships no plugin beside it.**

Claude Code plugins would be a second route into the same twelve directories.
All three shapes work; what separated them is what this package agrees to own,
and the answer is nothing further.

## Evidence

- The blocker the question opened with is gone. `${CLAUDE_PROJECT_DIR}` resolves
  to the project root and substitutes into an MCP stdio server's `command`,
  `args` and `env`. So a plugin could point at the project's own
  `vendor/bin/typo3-dev-companion` and vendor nothing. `archive` is a source
  type of its own, a zip over HTTPS with an optional `sha256` pin. Read from the
  plugins reference on 2026-08-08.
- **Skills alone** needs no transformation at all: `Installer::publishSkill()`
  already writes `<skill>/SKILL.md` with `references/base.md` beside it, which
  is the shape a plugin loads.
- **Skills and the project's own server** works exactly where `install` already
  works, where the project required this package. Where it did not, it installs
  cleanly with the tools silently absent.
- **Skills and a vendored server** is 6.0 MB across 983 files, measured on
  2026-08-08 from this checkout's `composer.lock` with
  `composer install --no-dev`. It turns the feedback channel on in every project
  that runs it, because `Channel::isAvailable()` reads the root package name out
  of a `vendor/` built here. It ships `bin/cli`, `feedback/` and `scenarios/`
  unless somebody builds the archive selectively.
- A manifest is static JSON and which entry a project needs is a property of the
  project. A plugin always writes the absolute path and always calls the host's
  `php`. So a host whose PHP lives only inside DDEV gets a server that does not
  start.
- Auto-update is off for third-party marketplaces by default, so the update
  stays a user act either way — which is what `R-DIS-025` already prompts for.

## Decided

- No plugin, no marketplace entry, and `Installer` and the thirteen clients it
  writes for stay as they are.
- Rejected first is the vendored shape. A guard on the feedback channel on a
  path nothing else exercises is new code. `R-SCO-009` says those two tools are
  never part of the use of this server.
- Rejected with it is the collision the other two carry. A project that holds
  both the installer's `.claude/skills/typo3-*` and a plugin's copy carries
  twelve descriptions twice. Only the installed half is in the record
  `R-DIS-025` digests, so the plugin's copy goes stale and nothing watches it.

## Assumed

- That `install` reaches the people who would have reached for a plugin. It is
  one command in a project that already has Composer, and this package ships as
  a Composer package.

## Wrong if

- Somebody copies `skills/` by hand because `install` did not reach their
  client. That is the failure the plugin would have prevented and `D-SKL-036`
  declines to support.
- A marketplace becomes how Claude Code users find TYPO3 tools at all, and this
  package is absent from where they look.
- The published skills no longer have the plugin's shape. Then the cheap shape
  above is no longer cheap and the price this entry rests on is out of date.
