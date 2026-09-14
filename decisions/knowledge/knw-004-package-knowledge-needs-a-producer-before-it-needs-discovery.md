---
id: D-KNW-004
title: Package knowledge needs a producer before it needs discovery
date: 2026-07-30
status: confirmed
---

# D-KNW-004 — Package knowledge needs a producer before it needs discovery

**A package contribution comes with the package and version Composer states and
an authority of its own.**

No discovery path arrives until a real producer has established a shape.

An installed extension can eventually contribute task guidance for its own API
and workflow. But a load of an arbitrary markdown path from every package would
erase a distinction this server already makes. That is the one between a core
rule, a transferable convention and somebody else's package advice.

## Decided

- A package contribution, when one exists, comes with `package` and
  `packageVersion` from Composer rather than from its file. It comes with
  `authority: "package"`, and `appliesTo` fixed to that Composer package by
  default. A package may augment its own namespace. An explicit override target
  is valid only below the same package authority. A match on the path or name of
  a bundled core convention never overrides it.
- No discovery path or general loader exists yet. There is one canonical package
  skill in this repository and no real third-party producer. No layout, update
  cycle or override need has established a common shape. A fixture written now
  would prove only that code can read the format it just invented.

## Assumed

- The first useful contribution will be procedural task guidance, not
  replacement facts about TYPO3 itself. Facts about a package remain its
  documentation; facts about TYPO3 remain versioned live documentation or the
  curated corpus.

## Wrong if

- One real extension is ready to ship agent material. Add its scenario first,
  record the package and version in every answer, then implement the narrowest
  discovery path its package can actually publish. A second producer is what
  justifies a shared format and override rules.

## Since then

The maintainer settled on 2026-08-02 that no third-party package contributes
data here for now. From here the absent discovery path is a scope choice rather
than a wait, so **Wrong if** narrows. A ready producer is no longer enough on
its own. The todo that carried the question is gone with it, and this entry is
where the question lives.

## Confirmed on 2026-08-22

Read on 2026-08-22: no discovery path, no loader and no `authority` field exist
in `src/`. So the second half of **Decided** is what the code still does.
**Wrong if** cannot fire in its narrow form, since the maintainer's answer of
2026-08-02 made a ready producer insufficient on its own. No producer has
appeared either.
