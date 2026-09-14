---
id: R-KNW-011
title: 'Extbase is covered as its own subject'
status: held
heldBy:
  - HintsTest::anExtbasePluginHasAHintOfItsOwn
---

# R-KNW-011 — Extbase is covered as its own subject

**Extbase is a subject of its own, what breaks during the write of a plugin
included.**

Those are the cache hash of a GET form and the property mapping of an object
argument. They are an unpersisted argument dropped from a link, and a paginator
that clamps an out-of-range page. The routes a paginated plugin needs are the
fifth.

Each of them answers with a wrong page or an error page rather than with a stack
trace anyone could search for.

## From

A catalog with fifty hint ids and not one about Extbase. The five failure modes
met afterwards during the build of the plugin it had nothing to say about
(2026-07-29).
