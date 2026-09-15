---
id: D-KNW-158
title: A timing probe is a page beside the rendering probe
date: 2026-09-15
status: open
coveredBy:
  - KnowledgeTest::timingACodePathIsAnsweredWithTheProbeAndWhatItLeavesOut
---

# D-KNW-158 — A timing probe is a page beside the rendering probe

**`core/testing/timing-a-code-path` states the temporary functional test that
times one call on a patch and on its parent, and the three things its number
leaves out.**

A review session was asked about performance. It wrote a temporary functional
test with `hrtime()` around five hundred calls and ran it on both revisions.
Then it wrote the caveats into its report itself.
`core/testing/proving-a-rendering` gave it the technique and nothing gave it the
rest.

## Evidence

- **The report.**
  [`feedback/2026-09-15-073817`](../../feedback/2026-09-15-073817-a-comparison-of-alternative-changes-and-a.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5`. It names the page it
  wanted, the warm-up, the per-call division and the three caveats.
- **Searched on 2026-09-15.** `typo3_rule_lookup` with "we also have to address
  performance" reached `core/contribution/gerrit-workflow` and nothing about a
  measurement. No hint names `hrtime`. Step 1a.
- **Read in `.checkouts/` on 2026-09-15.** `Build/Scripts/runTests.sh` sets
  `DBMS="sqlite"` on `12.4`, `13.4`, `14.3` and `main`. The `runtime` cache is a
  `TransientMemoryBackend` in `DefaultConfiguration.php`.
  `FunctionalTestCase::setUp()` in `typo3/testing-framework` builds the instance
  on the first test of a class and reuses it after, so one probe method runs in
  one process.
- **Two words moved a ranking.** "throwaway" and "xdebug" in the first draft
  changed what `typo3_rule_lookup` handed over for two queries `D-ANS-101`
  holds. `D-KNW-156` recorded the same for the first word. Both came out; the
  page says "temporary", and the debugger caveat went, because the report did
  not name it.

## Decided

- **Closed on the spot under `D-FBK-052`.** The run held the evidence, and the
  change touches `knowledge/`, `server-scope.json` and a test.
- **A page of its own rather than a section of the rendering probe.** That page
  says what a rendering contains. Its `whenToUse` sends a caller who wants a
  number elsewhere. The two name each other.
- **The example times `ImageService::getImage()`**, because that is the path the
  report measured. A reader substitutes the call and keeps the shape.
- **Against a sentence in the review skill.** A skill is a contract, and the
  routing entry reaches the page from the reviewer's words.
- **The other half of the report, the comparison of two changes, is
  `D-ANS-156`.**

## Assumed

- That `echo` from a test body reaches the runner on every covered major. The
  rendering page states it, and this page rests on that statement.

## Wrong if

- A session reads a difference smaller than the spread between two runs of one
  side as a finding. Then the page's sentence about noise did not take.
- A session times a path whose cost is the runtime cache and reads the cached
  loop as the path. Then the caveat is in the wrong place on the page.
