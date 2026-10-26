---
id: D-SKL-101
title: A sitepackage has a task skill of its own
date: 2026-10-26
status: open
coveredBy:
  - SkillTest::theSitepackageSkillAsksForTheDesignOnceBeforeTheFirstTemplate
  - SkillTest::theSitepackageSkillEndsOnEveryLayoutRendered
---

# D-SKL-101 — A sitepackage has a task skill of its own

**`typo3-sitepackage-development` owns the creation of a sitepackage and the
change of its layout. It offers the get.typo3.org builder as one start, beside a
tree written by hand.**

## Evidence

- The first run of `SITE-10`, 2026-10-26, in a copy of `E-SITE` 14.3 outside
  this checkout. No skill named a sitepackage, so the session loaded none. It
  read seven core files to fill what the hints left open, and it worked the
  wiring out alone. That run is the baseline the new skill is measured against
  (`D-SKL-035`).
- [`feedback/2026-10-26-121101-no-skill-or-guide`](../../feedback/archive/2026-10-26-121101-no-skill-or-guide-carries-create-a-sitepackage.md)
  asked for a guide or a skill.
- [`feedback/2026-10-26-121124-what-worked`](../../feedback/archive/2026-10-26-121124-what-worked-welcome-setup-typoscript-override.md)
  names the hints that prevented an error. The skill routes to each of them by
  id.
- The maintainer decided on 2026-10-26 that the task needs a skill. A session
  had judged a checklist item enough and was overruled.
- The maintainer chose the scope and the start. The skill owns the creation and
  the layout. It offers the builder, whose schema the session fetches first,
  because the official tutorial starts there.

## Decided

- **Construction, so no checklist.** The hints and a rendered page per page
  layout answer what the skill would otherwise judge.
- **The builder's tree and the hints' tree are left to one convention per
  package**, which the session names to the user. Which of the two the skill
  prefers is open.
- **The wiring is the skill's last section**, ending on one rendered page per
  page layout.

## Assumed

- That a session with the skill reads no core source for the steps the baseline
  run had to read it for.

## Wrong if

- A run of `SITE-10` with the skill installed reads core source for the tree,
  the wiring or the page layout fallback, or skips a step of the wiring.
- A session offered both starts cannot finish the builder's one without the
  user's help beyond the base package and the author.

## Since then

The first run with the skill, the same day, loaded it and stopped on four
questions with no package written. The maintainer ruled that a result without a
rendered frontend is not one. So the skill ends on a page per page layout, and
it takes a default where the request leaves a choice open. The defaults are
fluid_styled_content kept, the builder as the start, the vendor and the author
read from the project, and `Default` as the first page layout. The report names
each of them with its alternative.

Three more runs followed, the last with a full shell. It took the builder,
trimmed its output and rendered all three cases. What the runs still worked out
alone now stands in the skill: the deprecation tags a sitepackage reads, a
second layout rendered through a page set for the request, a stop without a
shell, and each generated file read against the hints.

## Since then

The get.typo3.org builder was fixed upstream the same day and deployed. Its 14.3
output no longer ships `ext_tables.php`, removed TypoScript options, a CKEditor
4 preset or a backend layout label it lacks. What it still ships is its own tree
and a few placeholder files. The skill's read of each generated file covers
both, so no hint lists the builder's output.

## Since then

The maintainer asked on 2026-10-26 that the skill take the site's design into
account. So it asks one question before the first template: colours and fonts,
logo and favicon, a design to follow, and a CSS framework. Each point carries a
default, and one reply that accepts them is enough. This is the one question the
skill asks before it works. A session that gets no reply waits, which the
maintainer accepted in exchange for a package built to the design.
