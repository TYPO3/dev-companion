---
id: R-KNW-028
title: 'A surviving hook is named by the subsystem that calls it'
status: held
heldBy:
  - HintsTest::survivingHooksAreNamedByTheirSubsystemAndIntent
---

# R-KNW-028 — A surviving hook is named by the subsystem that calls it

**The subsystem that still calls a hook names it, beside the narrower event for
a concrete intent.**

Intent words belong in that hint's `appliesTo`. There is no parallel
extension-point lookup whose registry would copy the subsystem knowledge and
drift from it.

## From

A prefill of an EXT:form field that needed a grep to discover both a hook that
still lives and the request-aware event to use (2026-07-29).
