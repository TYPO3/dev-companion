---
id: D-SKL-019
title: An absent surface is asked for by the id of its convention
date: 2026-08-04
status: open
---

# D-SKL-019 — An absent surface is asked for by the id of its convention

**A session asks a conformance surface the checkout has no files for with the
hint id that owns it. The checklist writes that id beside the surface.**

An audit published "no manual needed" for a project sitepackage and never asked
the convention that says an extension has one.

## Evidence

- `feedback/2026-08-04-175935`. `typo3_extension_describe` answered
  `manual: null, readme: null`, and the skill's own "absence of an optional
  subsystem is not a defect" did the rest. The maintainer found the gap
  afterwards. The convention is not silent: `extension-documentation` says a
  manual lives in `Documentation/` with `Index.rst` and `guides.xml`.
- The skill prescribes the query form nobody could compose here: "with the
  subsystem's concrete paths and a short English description". A surface with no
  files has no path, which is exactly the surface whose absence is the finding.
  The same page says so three paragraphs earlier, about the list derived from
  the checklist rather than from `find`.
- The surfaces whose convention did get a question are the ones whose id stands
  where the session worked. `static-quality.md` names
  `extension-static-analysis` and `extension-coding-standards` inline and the
  session called both. The Quality row named documentation as a bare noun.

## Decided

- The judgement is **step 3**, routing, and it is **closed on the spot**. That
  is a routing line onto a hint that exists, with no lookup about TYPO3.
- Both halves are written. The Quality row carries `id=extension-documentation`.
  The method bullet says that a session asks a surface with no files by its id
  rather than by its paths.
- The other bare nouns in that row stay as they are. Which hint owns "the test
  suite" or "upgrade readiness" for an extension audit is a read of the corpus
  rather than a rename. One wrong id in a published skill is worse than a noun.

## Assumed

- That the id in the row is what gets it asked. It is the difference the
  reporting session names between the surfaces it asked and the one it did not,
  and it is one session.

## Wrong if

- Another bare noun in that row produces the same miss. Then the row needs every
  id rather than the one that failed, and the read above waited too long.
- A session asks the hint by id, gets the convention, and still reports absence
  as clean. Then the lever is the skill's "absence is not a defect" sentence
  rather than the lookup.

## Since then

The row still carries the id and it survived the merge into
`typo3-extension-health`, where it is the only id the checklist names. So the
first **Wrong if** is open rather than fired. Four nouns in that row are still
bare, and the one with an answer elsewhere is the check layer. Nothing reports a
second miss, which is what the bullet waits for, and nothing reports the second
**Wrong if** either.
