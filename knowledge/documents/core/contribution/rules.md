---
description: >-
  The rules a reviewer judges a core patch by: what one change contains, which tests it owes, and what a reviewer looks at.
whenToUse: >-
  Before you write or review a patch to the TYPO3 core, to know what makes it ready to merge.
hints: []
---

# TYPO3 Core Contribution Rules

This file is the first local knowledge base for the TYPO3 Dev Companion server.
Keep it practical, versioned, and conservative. Prefer links to the official
TYPO3 documentation when a rule depends on a specific branch or a current
policy.

## Contribution Flow

- Work against the TYPO3 core repository and target the intended active branch.
- Keep a change focused on one bug, feature, cleanup, or test improvement.
- Add or update tests for a behavior change.
- Run the narrowest useful test first, then broaden when the change touches
  shared behavior.
- Name the affected subsystems and the commands you ran in the final task
  summary.

## Code Style

- Follow the coding style the touched TYPO3 subsystem already uses.
- Prefer existing TYPO3 APIs and services over new local abstractions.
- Keep a public API change explicit and documented.
- Avoid unrelated refactoring in a bug fix patch.
- The diff is what a reviewer reads, and you shape the patch for it. Edit the
  file that is there rather than rewrite it. Leave the formatting you found: the
  indentation, the line breaks and the order of what you did not touch. A
  reformatted line the change does not need is noise that hides the change. A
  review pushes back on a large diff.
- Do not collapse a multi-line array onto one line, or expand a single-line one,
  unless that is the change. `Build/php-cs-fixer/config.php` normalises the
  trailing comma for each form and takes no position on which form an array has.
  So nothing in the suite undoes the noise or reports it.
- Write a new array expanded, one key per line. A later change then marks the
  key it touched instead of the whole line. That is the same reason the rest of
  this section asks you to leave lines alone.
- Extend a test the same way. Merge an assertion into the test that already
  covers the case. Where you remove redundant tests, keep the fixtures and
  helpers they used.

## Testing

- A reviewer expects a unit test for isolated behavior.
- A reviewer expects a functional test for persistence, configuration, routing,
  backend behavior, or integration with TYPO3 services.
- An end-to-end test in the `e2e` suite is useful for a change to an editor or
  administrator workflow. Such a change breaks only in the assembled backend.
  That suite replaced the former acceptance suites.
- Document the tests you could not execute and why.

## Documentation

- A patch changes documentation in two repositories, and only one of them is the
  core. The core carries the changelog and the manuals of the system extensions,
  below `typo3/sysext/<extension>/Documentation/`. A change to what one of those
  extensions does carries its manual pages in the same patch.
- The manuals that document the framework itself are not in the core repository.
  Those are TYPO3 Explained, TypoScript Explained, the TCA Reference and the
  Fluid ViewHelper Reference. The TYPO3-Documentation GitHub organisation
  maintains them, and a pull request there changes them, never the patch.
- What the patch owes those manuals is the changelog entry.
  `Documentation/Changelog/Howto.rst` names the documentation team as one of the
  readers the changelog exists for. It points at the workflow that carries the
  entries over. A change nobody wrote an entry for reaches them through nothing.
- So a page outside the core repository is not a reason that the patch owes no
  documentation change. It is the reason the change is a follow-up somewhere
  else. A review that finds the page the patch makes false names it, so somebody
  can make that change.
- A bug fix that makes a documented sentence false is not the casual one that
  owes no entry. The casual bug fix is the one nothing outside the code has to
  learn about. `Important` is the type that carries the rest on a release line
  the other three may not reach.

## Review Readiness

- A reviewer can reproduce the change from the issue or the task description.
- The patch explains the problem and the chosen fix in a few sentences.
- A breaking change, a migration and a deprecation need clear notes.
- Security-sensitive behavior needs extra care and focused tests.
