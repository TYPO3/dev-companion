---
id: D-DOC-009
title: Prose names what counts rather than the count
date: 2026-08-03
status: confirmed
---

# D-DOC-009 — Prose names what counts rather than the count

**A count of something that grows does not go into prose; the thing and the
command that counts it do.**

`readme.md` was about to say "34 files holding 120 hints". Both numbers were
true at the keyboard and wrong at the next commit, and nothing in this
repository fails when they turn.

## Evidence

- The corpus went from 66 hints to 120 in one day, across four commits. Any
  count written into the readme during that day would have gone stale before its
  first reader.
- Nothing checks a number in prose. `prose:check` measures sentence length,
  `links:check` resolves paths, and neither can tell that a figure has drifted.
  So a reader believes it and a maintainer never sees it.

## Decided

- Name the thing and where the live number is: "one file per subject, which
  `bin/cli hints:coverage` counts". Where even that is more than the sentence
  needs, "and many more" is the whole of it.
- A number belongs where it has its measure and its date. A decision records
  what a sweep found on the day it ran. That is why the counts in `D-KNW-032`
  and `D-KNW-033` stay: they are evidence rather than a description.
- A report prints what is true when it runs. That is what `hints:coverage`,
  `todo:list` and `unresolved:list` are for, and why nobody transcribes them.

## Assumed

- A reader who wants the number will run the command. The alternative is a
  generated listing in the readme, which is what `decisions:index` does for the
  entries below it. That is worth a build where the listing is the point, and
  not worth it for one clause.

## Wrong if

- A count turns up in `readme.md`, `AGENTS.md` or a tool description again and
  survives a review. That would mean the rule is not where an author reads it.
- Somebody strips the numbers out of a decision to satisfy this, which is the
  opposite: an undated, unsourced claim in place of dated evidence.

## Confirmed on 2026-08-23

Neither **Wrong if** fired, swept over the three places the first one names. The
only numbers in `readme.md` are the covered majors, and the ones in `AGENTS.md`
are thresholds and entry ids. Those are the 30 words `prose:check` counts, the
80 columns `prose:format` wraps at, the ten lines a comment may spend on an
entry. "34 files holding 120 hints" stands where it always did, as the example
of the defect. Three tool descriptions carry a number and none of them counts
anything that grows. `typo3_forge_lookup` says its limit stops at 50,
`typo3_commit_message_guide` wraps a body at 72, and `typo3_flexform_lookup`
names two TYPO3 majors.

The second one held too. `D-KNW-032` still carries the sweep it came from, 66
hints to 120, a mean body from 297 words to 174. It stands dated and sourced
where this entry says such a number belongs.
