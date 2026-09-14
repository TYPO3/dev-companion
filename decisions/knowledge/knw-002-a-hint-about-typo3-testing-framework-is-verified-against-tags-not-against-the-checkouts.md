---
id: D-KNW-002
title: A hint about typo3/testing-framework is verified against tags, not against the checkouts
date: 2026-07-29
status: revoked
revokedBy: D-KNW-106
---

# D-KNW-002 — A hint about typo3/testing-framework is verified against tags, not against the checkouts

**A hint whose subject is `typo3/testing-framework` stands verified against the
tags that pair with the covered majors. Only what the core itself decides
carries a range.**

`project-extension-tests` is the first hint whose subject is not the core. The
phpunit boilerplate, the environment variables, the extension-path resolution
and `setUpFrontendRootPage()` all live in `typo3/testing-framework`. That is a
package with its own release cycle that `.checkouts/` does not contain.

## Decided

- Verify it against the tags that pair with the covered majors, from a clone of
  the package repository. Those are 7.1.1 (v12), 8.3.3 (v13), 9.6.1 (v14) and
  `main` (v15). Leave the statements unbound where all four agree. Only what the
  core itself decides carries a range: `SiteWriter` against `SiteConfiguration`,
  and the site set that `setUpFrontendRootPage()` drops.

## Assumed

- Those behaviours are stable within a major of the package. They have survived
  four majors as they are. The two that would hurt most, the hardcoded `clear`
  flag and the document-root-relative extension paths, are identical in all
  four.

## Wrong if

- A testing-framework release changes one of them inside a major. Nothing here
  would notice: `bin/cli catalog:check` re-reads the core checkouts, and this
  package is not one of them. The cheap fix is to teach that script the
  tag-to-major pairs. The honest alternative is to bind the statements to the
  package version instead of the TYPO3 one, which the hint format has no field
  for.

## Since then

The command the **Wrong if** names closes the gap. The package sits beside the
core checkouts, one worktree per pinned line. `catalog:check` derives the pairs
from the pins again and reads the half of the statements that carries weight
there. Nothing records the pairs, so a release inside a line arrives with the
next update rather than with an entry somebody maintains. One that changes
nothing relevant passes without a word, which is what separates a guard from a
reminder to go and look. What no needle covers stays unguarded.

## Revoked on 2026-08-01

The pairs are off by one release line, and the core says so itself. Each covered
branch pins the package in its own `require-dev`: `^8.3.1` on 12.4, `^9.2.1` on
13.4, `^9.5.0` on 14.3, `dev-main` on main. So v12 pairs with 8.x rather than
with 7.1.1, and v13 with 9.x rather than with 8.3.3. Composer resolves a project
the same way, because a line admits the major it serves and the one before it.
8.x requires `typo3/cms-core: 12.*.*@dev || 13.*.*@dev` and 9.x `13 || 14`, so
an extension on v12 installs 8.3.3. The statements stay as they are. 7.1.1
answered for a major nobody here covers. The three refs the corrected pairs
name, 8.3.3, 9.6.1 and `main`, all had their read at the time.
