---
id: D-DIS-010
title: What this package writes into a project ignores itself
date: 2026-08-03
status: open
coveredBy:
  - InstallerRecordTest::neitherCommandWritesIntoTheProjectsGitignore
  - InstallerTest::gitReportsTheProjectsOwnFiles
---

# D-DIS-010 — What this package writes into a project ignores itself

**Each directory the install writes carries a `.gitignore` of its own that says
`*`, and the project's `.gitignore` stays the project's.**

The project's own `.gitignore` kept generated skills out of a project's history.
So a use of this package meant a change to a tracked file that belongs to
somebody else, on every run.

## Evidence

- What the block cost, measured on 2026-08-03 against the version before this
  change, in a project whose `.gitignore` held one line of its own. 14 lines
  after `install --agent=claude`, and 32 after a second and third client that
  read their skills from different paths. Nine skills times each distinct skills
  directory, plus the record, plus two markers.
- It was rewritten on every `install` and every `update`, by
  `ignoreGenerated()`, which read the file, replaced its own block whole and
  wrote it back. A project that had committed its `.gitignore`, which is the
  ordinary case, therefore had a diff in a tracked file on each skills refresh.
- The same check, done in a real repository on 2026-08-03: with a `.gitignore`
  that says `*` inside the published directory, `git status -uall` reports
  `?? .claude/skills/my-own-skill.md` and nothing else. The file ignores itself
  along with everything beside it, and the skill the project wrote stays as it
  is. `git check-ignore -v` names that file as the rule for both.
- `documentation/usage/installing.rst` already drew the line this entry extends:
  merged agent or MCP configuration "is not ignored, because the project may
  share it". A project shares a `.gitignore` more than any of those files, and
  it was the one this package wrote into.
- The record was the one artefact with no other option.
  `typo3-dev-companion.json` sat at the project root, and a file cannot ignore
  itself.
- The package is not on Packagist and a project requires it from a local
  checkout, which `documentation/usage/installing.rst` says outright. Somebody
  set up every project that has the old layout by hand from a checkout they own.

## Decided

- Each published skill directory and `.typo3-dev-companion/` get `.gitignore`
  with `*`. The effect stands where the effect belongs, and it scales with
  nothing: one file per directory this package already replaces whole.
- The record moves to `.typo3-dev-companion/state.json` and nothing reads it
  from anywhere else.
- Nothing migrates a project that a development build set up. The old record and
  the old block stay where they are. The code that would find them is gone
  rather than kept for a case that is somebody's own checkout. This package is
  unreleased. A reader for a layout no released version ever had is a second
  shape to keep in order for as long as anybody remembers why.
- Against `.git/info/exclude`. It is not a file the project shares, which
  answers the whole objection. But it is still a file this package does not own,
  and it holds for one clone rather than for the project. To find it means to
  resolve a git directory through worktrees and submodules.
- Against no ignore at all, with the choice left to the project. That is the
  honest account of "generated content is the project's business". It costs
  every install nine untracked directories per skills path in `git status`,
  which is the noise this kept out in the first place.

## Assumed

- That a project wants the published skills out of its history. That assumption
  stood before this change as well, and nothing blocks whoever disagrees.
  `git add -f` reaches a file below an ignored directory the same as any other.
- That no client refuses, or reads, a `.gitignore` that sits in a skill
  directory. What each client looks for there is `SKILL.md` and the references
  beside it; nothing establishes what any of them does with an unexpected
  dotfile.
- That nobody runs a development build of this package in a project they do not
  own. A project requires it from a local path repository, so this holds by the
  way the package arrives rather than by anything asked of anybody.

## Wrong if

- A client no longer finds a published skill after this, or reports the extra
  file as part of the skill. Then the ignore belongs one level up, at a
  directory this package owns outright, with the skills below it.
- Somebody's checkout loses skills they expected to have committed. A team that
  deliberately shares the published directories, where the `*` now hides them
  from the commit that would carry them. Then this is a choice the project makes
  rather than a property of the package.
- The block a development build left behind outlives the reason for it in a
  project somebody works in. `install` writes the new layout beside it, the old
  lines ignore directories that ignore themselves, and nothing says so. Then the
  absent migration cost somebody a confused read of their own `.gitignore`, and
  the release notes are what has to carry it.
