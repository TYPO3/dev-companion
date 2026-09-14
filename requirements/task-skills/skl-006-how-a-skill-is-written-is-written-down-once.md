---
id: R-SKL-006
title: 'How a skill is written is written down once'
status: held
heldBy:
  - SkillTest::theAuthoringContractIsWrittenDownAndNamesWhatHoldsIt
---

# R-SKL-006 — How a skill is written is written down once

**One place states the rules an author writes a skill under, and every rule
there names the test that holds it.**

They are the name that files and routes it, and that it starts from the base and
states only what it adds. They say that it keeps no second copy of what a tool
owns, and that its references are one hop away and load on demand. They say that
it says what it owns and where it stops.

The rules that run over the skills directory hold a skill written later, whether
or not its author ever saw them. So they are also the ones that have to be
readable before the author writes it. The written form and that set of
assertions hold each other in both directions. A rule stated with nothing behind
it, and a directory-wide assertion nobody wrote down, are both failures.

Three of them no test can hold. The page states them with the rest because they
decide whether any of the others apply and what the file ends up as. A domain
earns a skill only where a scenario or a recorded run shows that the tools and
skills on hand fail the task. The author researches the practice before the
first line rather than recalls it during the write. The sources are the server's
own answers, the official documentation and the tools the task runs through. The
author shows the draft whole to the person who asked for it, with a request for
feedback by name, before publication. A skill written from recall has exactly
the shape of one written from the documentation. That is why the page has to
carry the step instead of a check.

## From

The feedback of 2026-07-30, trimmed on 2026-08-01 to its authoring half once
`bin/cli scenarios` and `scenarios/runs/` had answered the runner half. The
stable rules were visible by then. They spread across seven assertions in
`SkillTest` and five skills that restated them in their own words. That
arrangement made them unreadable to the next author. The next author was the
`typo3-extension-upgrade` skill, queued directly behind this.

## Held by

- `SkillTest::theAuthoringContractIsWrittenDownAndNamesWhatHoldsIt`, which holds
  [documentation/contributing/writing-a-skill.rst](../../documentation/contributing/writing-a-skill.rst)
  and the seven directory-wide assertions to each other. That a skill's author
  read the page before the write is not guarded and cannot be.
