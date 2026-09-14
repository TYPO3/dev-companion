---
id: D-VER-002
title: The prose is not bound; it says which half it is
date: 2026-07-29
status: revoked
revokedBy: D-VER-005
coveredBy: []
---

# D-VER-002 — The prose is not bound; it says which half it is

**The prose carries no version bound and says so in every answer, with
`typo3_hint_lookup` and `targetVersion` named as where the bound form is.**

The architecture hints now carry `since`/`until` on every statement that changed
inside the covered range. The markdown documents below `knowledge/` are the long
form of the same subjects and carry nothing. The event listener attribute, the
Fluid file extension, the backend tokens and the translation domains all stand
there as the shape, with no range.

## Decided

- No bound mechanism for prose. It would need per-bullet metadata in markdown, a
  parser, a renderer and a test, for a corpus a caller reads whole rather than
  filters. The same statements already carry a bound where a caller acts on
  them.
- Every prose answer says so instead, in one sentence from
  `Tools::renderSections()`, and names `typo3_hint_lookup` with `targetVersion`
  as where the bound form is. One sentence in one place, so a caller who learns
  it in a rule answer finds it unchanged in a script answer.

## Assumed

- A caller told that no filter reads the prose will ask the hints when the
  version matters. The alternative, a filter on prose sections by the ranges of
  the hints that share their subject, would guess at a mapping nobody declared.

## Wrong if

- A prose section misleads on an LTS badly enough that the sentence does not
  save it. That would mean that statement belongs in the hints rather than in
  the document.

## Confirmed on 2026-08-02

The **Wrong if** fired. `typo3-core-scripts.md` handed a 12.4 contributor three
suites that branch does not have, and the answer returns the section whole with
no filter. The decision holds because its own remedy absorbed all of them. The
range for a command lives on the suite, so the sections lost the commands and
gained a pointer. What did not hold is the line every rendered section opens
with, which named the hint lookup alone. It names the test run guide for a
command now. A caller still reads prose that describes a shape rather than a
command, and does not run it.

## Revoked on 2026-08-04

The class this entry left open arrived as a section whose body is a file the
caller writes out verbatim. Both properties this rested on fail there. The
document is where the caller acts rather than a long form of something bound
elsewhere. No hint carries the file for the range to sit on. The remedy that
absorbed the **Wrong if** in August does not apply either. A command could come
out of the prose because its range already lived on a suite, and a file has
nowhere to go.

What holds instead is
[`D-VER-005`](ver-005-a-document-section-declares-the-majors-it-holds-for.md),
whose **Wrong if** is a different list. What can go wrong now is a variant
handed over with nothing that says which to write. It is not a section that
misleads because nothing could filter it.
