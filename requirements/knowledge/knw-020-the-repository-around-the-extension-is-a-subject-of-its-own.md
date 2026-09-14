---
id: R-KNW-020
title: 'The repository around the extension is a subject of its own'
status: held
heldBy:
  - HintsTest::whereSomethingGoesInTheRepositoryIsAnsweredToo
---

# R-KNW-020 — The repository around the extension is a subject of its own

**The repository around the extension is a subject of its own.**

The catalog goes by subsystem, which is the model of someone who already knows
where their file goes. A project developer asks "where does this go". The answer
for what is not part of any package is nowhere in the core, because the core is
not a project. That is the build tools, the suites that need a live site, the
scripts, what the ignore list holds. The answer names places with the reason
each one exists, and it is not a skeleton to copy. Projects differ in whether
they have Node, DDEV or one site or twenty, and only the reasons transfer.

## From

A session that had to invent the location of the phpunit configurations and the
browser suite with its config. It invented the scripts a project exposes, and
the ignore list. It had a working answer for the extension
(`sitepackage-layout`) and none for what sits around it (2026-07-29).
