---
id: D-KNW-184
title: The corpus weighs no package outside the core against the core
date: 2026-10-30
status: open
---

# D-KNW-184 — The corpus weighs no package outside the core against the core

**No hint weighs the core way of building a content element against
`friendsoftypo3/content-blocks`. The corpus describes the core way, and
`typo3_ter_lookup` finds what is published beside it.**

## Evidence

- `feedback/2026-10-29-105200-where-a-requirement-is-open-the-model-settles.md`
  reports that both runs built their elements on TCA record types and were
  offered no choice. It asks for a statement that weighs the two.
- `D-KNW-004`: the maintainer settled on 2026-08-02 that no third-party package
  contributes data here. A statement about such a package has no producer that
  keeps it true across its releases.

## Decided

- The maintainer declined the weighing on 2026-10-30, when asked before
  `T-261029-3c7e` was built.
- A session that asks how to build an element gets the core way.
  `extension-reuse` and `typo3_ter_lookup` are the route to what is published
  beside it.

## Assumed

- That a session that needs a package outside the core finds it through the
  registry search rather than through this corpus.

## Wrong if

- A recorded run builds an element the core way where the user had asked for a
  package outside it, and nothing in the brief routed it to the registry.
