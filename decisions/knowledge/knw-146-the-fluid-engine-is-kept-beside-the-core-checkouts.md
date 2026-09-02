---
id: D-KNW-146
title: The Fluid engine is kept beside the core checkouts
date: 2026-09-02
status: open
coveredBy:
  - PinnedPackageTest::eachPackageIsReadFromTheSectionTheCorePinsItIn
---

# D-KNW-146 — The Fluid engine is kept beside the core checkouts

**A statement about Fluid is read in `.checkouts/fluid/<line>`, at the line the
core's own `require` pins.**

## Evidence

- `feedback/2026-09-02-135220` reports a defect the session's tests passed over:
  `condition="{demand.orderField} === '{field}'"` never matched, where the core
  writes the comparison unquoted. Whether a quoted operand is a literal is a
  property of the parser, and nothing in this repository could be asked it.
- What the core does is not what Fluid does. `beuser`'s Online template and
  `backend`'s SiteConfiguration overview both write the unquoted form, and no
  core template was found writing the other — a convention, and the question is
  the engine's.
- `typo3_documentation_lookup`, asked on 2026-09-02: the `f:if` reference says
  the condition is evaluated as a PHP expression and sends the reader to TYPO3
  Explained for complex ones.
- `BooleanNode` is in `typo3fluid/fluid`, which no core checkout contains. Each
  covered branch pins it in `require` — 12.4 `^2.15.0`, 13.4 `^4.6.1`, 14.3 and
  `main` `^5.3.2` — and every `composer.lock` beside those manifests names
  `https://github.com/TYPO3/Fluid.git` as its source. Read in `.checkouts/` on
  2026-09-02.
- The alternative was the vendor tree of another project on the machine, which
  the next session cannot reproduce and no commit can name.

## Decided

- `bin/cli checkouts:update` keeps it the way it keeps
  `typo3/testing-framework`: one treeless mirror, one worktree per pinned
  release line, each at that line's newest tag.
- `Upkeep\PinnedPackage` holds both. The two differ in the package name, the
  repository, the manifest section the core pins them in and the directory below
  `.checkouts/`, and in nothing else, so the harness class became one of two
  instances rather than a class that was copied.
- The reading it was created for was done in it: `BooleanNode` and
  `BooleanParser` say a quoted operand is a literal, in `.checkouts/fluid/2` at
  2.15.0, `4` at 4.6.1 and `5` at 5.3.2, which is every covered major. The
  statement stands unbound on `fluid-conditions-and-arrays`.

## Assumed

- That a parser behaviour is stable inside an engine major, so the line's newest
  tag answers for the release a project installed. It is `D-KNW-106`'s
  assumption for the harness, inherited rather than measured again.

## Wrong if

- A covered branch pins a Fluid constraint spanning two majors. The line then
  names no single release, `bin/cli versions:check` reports it already, and a
  statement bound to that TYPO3 major stops naming an engine — `D-VER-003`.
- Nothing further is ever read there. One claim was settled in it; a second
  package in `.checkouts/` that answers one question is a clone per machine for
  a reading that could have been a link.
