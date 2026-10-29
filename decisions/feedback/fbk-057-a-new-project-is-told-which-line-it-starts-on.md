---
id: D-FBK-057
title: A new project is told which line it starts on
date: 2026-10-29
status: open
coveredBy:
  - ProjectTest::whereNoCoreIsInstalledTheAnswerNamesTheNewestLtsLine
---

# D-FBK-057 — A new project is told which line it starts on

**This server gets a statement about the line a new project starts on and how it
is installed. The first call names the newest LTS line where nothing is
installed yet.**

## Evidence

- `feedback/2026-10-28-162800-a-new-project-gets-no-version-and-no-install.md`
  compares four greenfield runs of 2026-10-28. No run chose 14 on its own. One
  opencode run named this server as the warrant for 13.4, which nothing here
  says.
- The query ran again on 2026-10-29, over stdio from an empty directory.
  `typo3_project_describe` answers `unsupported` with cause `no-installation`
  and names no line. The `start` instructions say "the version the user named,
  or ask, or say in the answer which one you assumed".
- `typo3_task_guide` with "install TYPO3 and build a sitepackage for a new site"
  reaches the `installation-setup` and `sitepackage` intents, both strong. Its
  `targetVersion` is null, and no checklist line names a line or the Composer
  installation.
- `bin/cli hints:probe "which TYPO3 version should a new project start on"`
  reaches `project-extension-tests` and `installation-upgrade`, neither on the
  subject. A probe for create-project against a classic installation reaches
  `sitepackage-initial-content` alone.
- `typo3_server_scope` carries the lines and their status from
  `knowledge/versions.json`. The Claude run with this server knew 14.3 is
  stable, so the fact arrived where a session asked the scope. It still advised
  13, for the extensions a project needed none of.
- `knowledge/versions.json` was verified on 2026-07-29 and called 14.3 `stable`
  beside 13.4 `lts`. On 2026-10-29 get.typo3.org lists v14 as the LTS "for new
  projects", v13 "for existing projects" and v12 as ELTS.
- The Getting Started tutorial, page "TYPO3 version and which to start",
  recommends the latest LTS. Its installation chapter installs with Composer
  under DDEV and assumes that installation from then on. TYPO3 Explained, "TYPO3
  installation overview", supports Composer and the classic installation both.
  It gives Composer to teams and the classic one to managed hosting.

## Decided

- Step 1a for the statement and step 2 for the line. Taken on, not closed on the
  spot. The statement says what TYPO3 recommends, which needs a read of its own.
  The describe half changes `src/` and a declared schema.
- The statement is the hint `new-project-line`. It says which line a new project
  starts on, and that a project with a repository is a Composer installation. It
  names the line through `knowledge/versions.json`, never as a literal in the
  sentence. It says what decides for an older line: an extension the project
  requires that has no release for the newer one.
- `typo3_project_describe` names the newest LTS line where no core is installed,
  read from `Versions::covered()`, in `newProjectLine`. The todo said the newest
  released line. The LTS is what TYPO3 recommends, and the two part while a new
  major is released and not yet LTS. It names the fact and not the choice. The
  choice is the statement's, and the user's.
- The statuses follow get.typo3.org: 14.3 `lts`, 12.4 `elts`. `stable` stays the
  word for a released line before its LTS release.
- The statement goes where the task passes: the `installation-setup` intent and
  the create branch of `typo3-development-installation`. The skill routes to it
  and keeps no number, as its own rules say.
- Priority `high`. Four runs of four missed the first decision a new project
  makes, and every later step builds on that decision.
- The feedback stays open until the commit that ships both halves archives it.

## Assumed

- That the opencode runs called `typo3_project_describe` and not the scope. The
  feedback does not say which calls each run made.

## Wrong if

- A run with both halves in place still takes the older line with no extension
  that requires it. Then it is step 4, wording, and the statement did not take.
- A run that called `typo3_project_describe` still names no line. Then the field
  is not where the session reads, and the instructions are the place.
