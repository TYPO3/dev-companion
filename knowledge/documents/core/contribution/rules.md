---
description: >-
  The rules a core patch is judged by: what one change contains, which tests it owes, and what a reviewer looks at.
whenToUse: >-
  Before writing or reviewing a patch to the TYPO3 core, to know what makes it merge-ready.
hints: []
---

# TYPO3 Core Contribution Rules

This file is the first local knowledge base for the TYPO3 Dev Companion server.
Keep it practical, versioned, and conservative. Prefer links to official TYPO3
documentation when rules depend on a specific branch or current policy.

## Contribution Flow

- Work against the TYPO3 core repository and target the intended active branch.
- Keep changes focused on one bug, feature, cleanup, or test improvement.
- Add or update tests for behavior changes.
- Run the narrowest useful test first, then broaden when shared behavior is
  touched.
- Mention affected subsystems and executed commands in the final task summary.

## Code Style

- Follow the coding style already used by the touched TYPO3 subsystem.
- Prefer existing TYPO3 APIs and services over new local abstractions.
- Keep public API changes explicit and documented.
- Avoid unrelated refactoring in bug fix patches.
- The diff is what a reviewer reads, and it is what the patch is shaped for.
  Edit the file that is there rather than rewriting it, and leave the formatting
  you found — the indentation, the line breaks and the order of what you did not
  touch. Reformatting a line the change does not need is noise that hides the
  change, and a large diff is what a review pushes back on.
- Do not collapse a multi-line array onto one line, or expand a single-line one,
  unless that is the change. `Build/php-cs-fixer/config.php` normalises the
  trailing comma for each form and takes no position on which form an array has,
  so nothing in the suite undoes the noise or reports it.
- Write a new array expanded, one key per line. A later change then marks the
  key it touched instead of the whole line, which is the same reason the rest of
  this section is about leaving lines alone.
- Tests are extended the same way: merge an assertion into the test that already
  covers the case, and where redundant ones go, the fixtures and helpers they
  used stay.

## Testing

- Unit tests are expected for isolated behavior.
- Functional tests are expected for persistence, configuration, routing, backend
  behavior, or integration with TYPO3 services.
- End-to-end tests, the `e2e` suite, are useful when the change affects editor
  or administrator workflows and only breaks in the assembled backend. They
  replaced the former acceptance suites.
- Document tests that could not be executed and why.

## Documentation

- A patch changes documentation in two repositories, and only one of them is the
  core. The core carries the changelog and the manuals of the system extensions,
  below `typo3/sysext/<extension>/Documentation/`, and a change to what one of
  those extensions does carries its manual pages in the same patch.
- The manuals that document the framework itself — TYPO3 Explained, TypoScript
  Explained, the TCA Reference and the Fluid ViewHelper Reference — are not in
  the core repository. They are maintained in the TYPO3-Documentation GitHub
  organisation and changed by a pull request there, never by the patch.
- What the patch owes those manuals is the changelog entry.
  `Documentation/Changelog/Howto.rst` names informing the documentation team as
  one of the reasons the changelog exists, and points at the workflow that
  carries the entries over. A change nobody wrote an entry for reaches them
  through nothing.
- A page living outside the core repository is therefore not a reason no
  documentation change is owed. It is the reason the change is a follow-up
  somewhere else, and a review that finds the page the patch makes false names
  it so somebody can make that change.
- A bug fix that makes a documented sentence false is not the casual one that
  owes no entry. The casual bug fix is the one nothing outside the code has to
  learn about, and `Important` is the type that carries the rest on a release
  line the other three may not reach.

## Review Readiness

- The change should be reproducible from the issue or task description.
- The patch should include a concise explanation of the problem and the chosen
  fix.
- Breaking changes, migrations, and deprecations need clear notes.
- Security-sensitive behavior needs extra care and focused tests.
