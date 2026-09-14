---
id: R-KNW-003
title: 'A hint says how a subsystem is used'
status: held
heldBy:
  - HintsTest::theFrontendRenderingPathIsAnswered
---

# R-KNW-003 — A hint says how a subsystem is used

**A hint says how to use a subsystem, not only what a patch to it has to
satisfy.**

Both audiences read the same entry. "DataHandler changes are high-impact and
usually need functional tests" is true and answers nobody's question about how
to write a datamap. Where a mechanism has a shape that is easy to get wrong, the
hint states it. That is an order, a rule for names, a step that happens at
install time.

## From

A session that built a site with this server as its only reference. It found the
catalog organised around "what must a patch satisfy to be merged". The questions
were "how do I do X with this API" (2026-07-29).

## Held by

- `HintsTest::theFrontendRenderingPathIsAnswered`, and the shape of
  `datahandler-persistence`, `sitepackage-initial-content`, `public-assets`,
  `frontend-page-rendering`. Not guarded beyond that.
