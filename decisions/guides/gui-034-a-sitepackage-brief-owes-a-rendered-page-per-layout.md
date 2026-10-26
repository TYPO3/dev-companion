---
id: D-GUI-034
title: A sitepackage brief owes a rendered page per layout
date: 2026-10-26
status: open
coveredBy:
  - HintsTest::aSitepackageBriefOwesARenderedPagePerLayout
---

# D-GUI-034 — A sitepackage brief owes a rendered page per layout

**The `sitepackage` intent adds one item that proves the package by a rendered
page per page layout, and it owes the browser-check guide. The generic items
about tests and the branch stay.**

## Evidence

- [`2026-10-26-132916-task-guide-checklist`](../../feedback/archive/2026-10-26-132916-task-guide-checklist-asks-for-tests-and-a.md).
  A run in a project with no declared command and no git repository got a brief
  that asked for the target branch and for functional tests. It said nothing
  about what does prove a sitepackage.

## Decided

- **The proof the work owes is added, the generic items stay.** A project with
  no test layer is a finding of its own, and a brief that drops the items where
  `typo3_project_describe` finds no command would hide it.
- **`owes` names the browser-check guide**, the same route the `content-element`
  and `backend-module` intents take.

## Wrong if

- A run in a project with tests skips the rendered page because the suite is
  green.
