---
id: D-KNW-186
title: A password question reaches the install tool and a project commits settings.php
date: 2026-11-06
status: open
coveredBy:
  - HintsTest::aPasswordQuestionReachesTheInstallToolPassword
  - HintsTest::theQuestionWhatToCommitReachesSettingsPhp
---

# D-KNW-186 — A password question reaches the install tool and a project commits settings.php

**`install-tool-password` answers a question about either password, and
`project-configuration-files` says that a project commits `settings.php` and
where its secrets go. `installation-setup` stays as it is until the run
`T-261029-bb2b` waits on.**

## Evidence

- `feedback/archive/2026-11-06-065413-no-statement-says-how-a-v14-install-tool.md`
  and
  `feedback/archive/2026-11-06-070244-nothing-says-a-project-commits-settings-php-and.md`
  reported the two questions. `bin/cli hints:probe` reached no statement for
  either on 2026-11-06.
- `.checkouts/13.4` has no `install:password:set` and no `/install` route, and
  its modules stand below `/module/tools/`. `.checkouts/14.3` and `main` have
  both, and their modules stand below `/module/system/`.
- `Middleware/Maintenance.php` prints `Calculated hash:` on `13.4` and `14.3`,
  and TYPO3 Explained 14.3 documents that step. The feedback called it probably
  obsolete.
- `EntryPointRedirectController::redirectAction` sends `/install` and
  `/install.php` to `?__typo3_install`, and TYPO3 Explained 14.3 documents that
  form. The feedback called the entry point gone.
- `BackendUserAuthentication::isSystemMaintainer()` passes every administrator
  in the Development context. The feedback named the list alone.
- TYPO3 Explained 14.3, "Version control of TYPO3 projects with Git", lists
  `config/system/settings.php` under the files to always commit, and loads
  secrets from an untracked `credentials.php`.

## Decided

- **A hint rather than a document.** The answer is one command and a few facts,
  and a hint is what a password question reaches.
- **A new hint rather than a statement in `installation-setup`.** The maintainer
  chose on 2026-10-30 that the run measures that entry as two runs read it.
- The warning at the moment the value is created stays with `T-261029-e58d`,
  because it changes the same checklist.

## Assumed

- That a session asked to build a site does not look this hint up, so
  `T-261029-bb2b` still measures what it set out to measure.

## Wrong if

- A session asks about a password, reads the hint, and still names one password
  alone. Then the statement arrives and does not take.
- A later TYPO3 Explained moves `settings.php` out of the files to always
  commit.