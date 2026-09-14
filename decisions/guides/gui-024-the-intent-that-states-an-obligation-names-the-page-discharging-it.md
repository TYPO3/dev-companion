---
id: D-GUI-024
title: The intent that states an obligation names the page discharging it
date: 2026-08-28
status: open
coveredBy:
  - HintsTest::aBriefNamesTheGuideTheWorkIsWrittenUpIn
---

# D-GUI-024 — The intent that states an obligation names the page discharging it

**`patch-review` carries the compatibility guide. So the brief that says a
result stands on every declared major names the page that says how.**

`D-GUI-012` gave a brief its `guides`, one per matched intent. The obligation
and the procedure were in two different intents, so a brief could state the duty
and name nothing.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-073954`](../../feedback/archive/2026-08-28-073954-the-guides-list-is-read-past-and-its-two.md).
  The package declares `^13.4 || ^14.3` and 14.3 is the installed one. It
  established the other major by hand. A scratch Composer root,
  `typo3fluid/fluid:^4.3` required into it, the render script run again, plus a
  `gh api` call for the constraint 13.4 carries. Four round trips.
- **Measured here on 2026-08-28**, with the report's own call. The brief answers
  `scope: "extension"`. It carries the checklist line "Judge it on every major
  the package's Composer constraint declares and not only on the installed one".
  `guides` came back empty.
- **The line and the page belong to different intents.** The line is
  `patch-review`'s, which had no `guide`; the page is `compatibility`'s, which
  the task text did not match.
- **The page stood in a list it got minutes earlier**, in
  `typo3_project_describe`'s 21-entry `guides` array. The same session filed
  that array as the part of the answer it scrolled past. A page named in a list
  is not a page named at the obligation.

## Decided

- `patch-review` takes
  `extension/compatibility/a-declared-major-that-is-not-installed`, the read
  page rather than the run one. A review has to judge, which a read of the
  branch settles. That page hands off to
  `running-on-a-declared-major-that-is-not-installed` where a run is what the
  question needs. One hop, and the report reached the run page first because it
  needed render behaviour rather than a signature.
- **Made in this run.** It is one key in `knowledge/task-intents.json`, the
  measure is above, and no lookup about TYPO3 ran.
- Against the report's second suggestion, that `typo3_project_describe` mark
  which of its guides the repository state activates. That is a second relevance
  model beside the intents', from different inputs, and the brief is where the
  task stands known.
- Against a `documentId` field on each checklist entry, which is a schema and a
  second home for what `guides` already carries as data.

## Assumed

- That one page per intent is enough. `patch-review` states six checklist lines
  and this is the one whose procedure comes with it here.

## Wrong if

- A session reports the compatibility page named on a review that had nothing
  multi-major about it. It reads the brief as an order for a read it does not
  owe. The `when` line is what carries that condition, and it is one sentence.
- A second checklist line of the same intent turns out to have a bundled
  procedure. Then one `guide` per intent is the wrong shape and the entry is
  what has to move.
