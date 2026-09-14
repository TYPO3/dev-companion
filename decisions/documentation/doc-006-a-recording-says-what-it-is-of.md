---
id: D-DOC-006
title: 'A recording says what it is of'
date: 2026-08-02
status: open
coveredBy:
  - ToolAnswersTest::everyAnswerOnAPageOfTwoRecordingsSaysWhichItCameFrom
  - ToolAnswersTest::everyRecordedAnswerIsJson
  - ToolAnswersTest::everyRecordedPageSaysWhichDayItWasAnsweredOn
  - ToolAnswersTest::everyToolTheTableDrivesHasARecordedAnswer
  - ToolAnswersTest::theRecordingCarriesNobodysDirectoryLayout
---

# D-DOC-006 — A recording says what it is of

**The recording under `documentation/clients/tool-answers/` is evidence rather
than a derivation. It names the day, the checkout and the console behind it, and
no check reads it.**

The other half of the tool surface — what a filled answer looks like — needs an
installation, and no test run discovers one. So a command records it and a
commit keeps it. That makes it the first page here that can be true on its day
and stale a week later.

## Evidence

- Recorded against `.checkouts/14.3`, the newest released covered line, which is
  the one installation this repository can recreate. `bin/cli checkouts:update`
  makes it, so a reader can record the same thing again. Its console is out of
  reach, so five installation-backed tools answer from the packages with
  `answeredBy: "packages"`. Two answer `unsupported` with
  `installation-not-answering`. That is three shapes a client meets, in one run.
- 42 calls come to 2,800 lines with the answers cut. Uncut, one
  `typo3_icon_lookup` answer alone is longer than the page it would be in.

## Decided

- Nothing checks it. A command only a machine with `.checkouts/` can run must
  not be able to turn CI red. That is the same line `D-FBK-011` drew between the
  suite and the check commands. `tools:check` reads `tools.md` and not this.
- The head says what it is of. That is the date, the kind and version of the
  installation, and whether the console answered. Also which checkout it was
  when it is one of ours. Without those the page is an assertion about nothing.
- Cut to fit, and the cut is data rather than an ellipsis in prose. A list keeps
  two entries and gains one that says how many followed; a string keeps 320
  characters and says how long it was. What remains parses as JSON, which is
  what `ToolAnswersTest` holds.
- Absolute paths stand as `<repository>`, `<installation>` and `<home>`, before
  the cut rather than after it. A path chopped in the middle is no longer a
  string a substitution recognises. The page ships inside this package and would
  otherwise carry one machine's directory layout into every checkout.
- The call table moves to `Upkeep\ToolCalls`, which both the recording and
  `ToolContractTest` read. Two tables would drift, and the recording would then
  illustrate calls nothing validates.

## Assumed

- A reader who wants to know whether the recording still holds re-runs the
  command rather than trusts the page. That is what the head asks for and
  nothing enforces it.

## Wrong if

- Somebody reads a recorded answer as the current one and files a defect against
  a shape the code no longer has. Then the page needs a check after all. It
  would have to be one that passes where there are no checkouts, which is why it
  has none today.
- Nobody re-runs the recording, so it ages into a picture of a server that no
  longer exists. A test that fails regenerates `tools.md`; this one has nothing
  that asks.

## Since then

Two work directories rather than one: the newest released core checkout and the
fixture installation, because neither fills the whole surface on its own. The
second answer goes on the pages of the tools that declare `answeredBy`, which is
where a reader who compares the two stands.
