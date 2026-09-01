---
id: R-SKL-029
title: "A skill reading a project checks its pinned versions against the day's release"
status: held
heldBy:
  - SkillTest::everySkillThatReadsAPinChecksItAgainstTheDaysRelease
---

# R-SKL-029 — A skill reading a project checks its pinned versions against the day's release

**A skill that reads a project checks the versions it pins — node, the GitHub
Actions, DDEV, the libraries — against the current release, and reports the ones
behind.**

The check is made on the day, never against a number in the skill. A skill is
installed into somebody else's project, so a version written into it is not
corrected by the next release of this server, and a project that follows it is
pinned to whatever was current when the file was published.

What comes of it is a finding with the raise offered, not a raise carried out.
The target is measured against the lower bound the project declares — the PHP
range the installed TYPO3 supports, the majors the extension says it runs on,
the node the build needs. Where that bound rules the newest release out, the
finding names the newest version the bound does allow, rather than the newest
that exists and rather than dropping the pin from the report. A raise that would
move the bound itself is a different change and is asked for, as is one that
leaves what the session was asked to do.

`R-COD-004` is the same demand on this repository, and `R-ANS-037` on what an
answer may name.

## From

The maintainer's instruction of 2026-08-29, and the session behind
[the feedback of 2026-08-19](../../feedback/archive/2026-08-19-090200-no-skill-covers-the-npm-webpack-asset-build-of.md),
which was asked to take an extension's dependencies to their newest versions and
found no skill covering the build those versions sit in.

## Held by

- `SkillTest::everySkillThatReadsAPinChecksItAgainstTheDaysRelease`, over the
  three skills whose task reads a pin: the asset build for the Node and the
  manifest, the development installation for what the environment configuration
  pins, and the extension health checklist for the actions and the declared
  dependencies. What it reads is the measure, the raise offered rather than
  taken, and the bound that can refuse it.
