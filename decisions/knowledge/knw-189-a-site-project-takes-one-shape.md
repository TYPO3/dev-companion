---
id: D-KNW-189
title: A site project takes one shape
date: 2026-11-06
status: open
coveredBy:
  - HintsTest::eachProjectShapeSaysWhereItHolds
---

# D-KNW-189 — A site project takes one shape

**`sitepackage-start` states the shape of a site project, and
`extension-repository-installation` says that its own shape holds for an
extension repository.**

## Evidence

- `feedback/archive/2026-11-04-121354-two-statements-describe-two-project-shapes-and.md`:
  a run read both statements and made the sitepackage the Composer root. The run
  without this server wrote the project package.

## Decided

- **The maintainer chose on 2026-11-06 to repair this now**, over waiting for
  the run `T-261029-bb2b` holds back. That run therefore reads these two
  statements as changed, and the two runs of 2026-10-29 did not.

## Assumed

- That the changed baseline costs less than a repeat of the worse shape.

## Wrong if

- A `SITE-11` run reads `sitepackage-start` and still makes the package the
  root.

## Since then

On 2026-11-06 both `SITE-11` runs took the project package below `packages/`.
