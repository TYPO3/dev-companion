---
id: R-SKL-029
title: "A skill reading a project checks its pinned versions against the day's release"
status: held
heldBy:
  - SkillTest::everySkillThatReadsAPinChecksItAgainstTheDaysRelease
---

# R-SKL-029 — A skill reading a project checks its pinned versions against the day's release

**A skill that reads a project checks the versions it pins against the current
release, and reports the ones behind. That is node, the GitHub Actions, DDEV,
the libraries.**

The check runs on the day, never against a number in the skill. A skill goes
into somebody else's project. So the next release of this server does not
correct a version written into it. A project that follows it stays pinned to
whatever was current at publication.

What comes of it is a finding with the raise on offer, not a raise carried out.
The measure of the target is the lower bound the project declares. That is the
PHP range the installed TYPO3 supports, the majors the extension says it runs
on, the node the build needs. Where that bound rules the newest release out, the
finding names the newest version the bound does allow. It does not name the
newest that exists, and it does not drop the pin from the report. A raise that
would move the bound itself is a different change and the skill asks for it. So
is one that leaves the session's task.

`R-COD-004` is the same demand on this repository, and `R-ANS-037` on what an
answer may name.

## From

The maintainer's instruction of 2026-08-29, and the session behind
[the feedback of 2026-08-19](../../feedback/archive/2026-08-19-090200-no-skill-covers-the-npm-webpack-asset-build-of.md).
That session had the task to take an extension's dependencies to their newest
versions. It found no skill that covers the build those versions sit in.

## Held by

- `SkillTest::everySkillThatReadsAPinChecksItAgainstTheDaysRelease`, over the
  three skills whose task reads a pin. That is the asset build for the Node and
  the manifest, and the development installation for what the environment
  configuration pins. The third is the extension health checklist for the
  actions and the declared dependencies. What it reads is the measure, the raise
  on offer rather than taken, and the bound that can refuse it.
