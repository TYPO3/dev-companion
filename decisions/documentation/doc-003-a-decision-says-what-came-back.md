---
id: D-DOC-003
title: 'A decision says what came back'
date: 2026-08-02
status: confirmed
coveredBy:
  - DecisionsTest::aStatusNamesTheLastDatedLineInTheFile
  - DecisionsTest::everyTestADecisionNamesExists
  - UnresolvedTest::theOpenDecisionsAreReadOldestFirst
---

# D-DOC-003 — A decision says what came back

**The decision states are `open`, `confirmed` and `revoked`. An entry consists
of sections rather than labelled bullets, and a requirement names the decisions
it rests on.**

`corrected` was one word for four outcomes. The status is the only thing a
reader has before they open a file that may be months old. Naming what a
requirement stands on is what makes a revoked decision under it audible rather
than silent.

## Evidence

- The twelve entries with `corrected`, read on 2026-08-02. On three it meant
  reversed and replaced — D-AUD-002, D-KNW-003, D-SCO-008. On three the **Wrong
  if** had fired with nothing in its place: D-FBK-003, D-KNW-001, D-CAT-002. On
  three one named part was wrong and the rest held. D-FBK-005 says so outright:
  "the order this entry is mostly about … is untouched … what was wrong is the
  number beside it". On D-DIS-003 the **Wrong if** had explicitly *not* fired.
- A decision confirmed and later revoked had no way to say so. A run on the
  morning of 2026-08-02 confirmed D-KNW-003, and the evidence that arrived the
  same day revoked it. The check rejected an entry with both lines, so the run
  had to fold into the revocation prose and the two-stage history was lost.
- Twelve entries name a test in prose and nothing read it. One of the names in
  `D-KNW-003` was already stale, a `KnowledgeTest` method renamed that morning.
  The entry still read as though something held it. The check written here
  caught it on its first run, and caught a second stale name in this entry's own
  first draft.
- `R-FBK-007` rests on `D-FBK-005`, which stands revoked. The requirement is
  `held`, its test passes, and nothing anywhere says the argument under it fell.

## Decided

- Three states, not four. This entry weighed `amended` for the three
  partly-wrong entries and dropped it. A reader who must not build on part of an
  entry does better with `revoked` plus a dated line that names what fell. A
  state that says "some of this is safe" and not which serves them worse.
- `open` rather than `standing`, and it is not a workflow step. The commit that
  implements an entry writes it, so every open decision is already in the code.
  `open` says nobody has been back to the **Wrong if**. `confirmed` rather than
  `tested`, because several of these readings are readings rather than runs.
- The status names the last dated line. An entry may carry several, and what a
  reader relies on is the latest.
- `Covered by` is optional. Most entries here are about process and nothing runs
  over them. A required field would have said "nothing" on two thirds of the
  directory.
- Every test named *anywhere* in an entry has to exist, not only the ones under
  `Covered by`. The prose makes the same claim and goes stale the same way.
- `bin/cli unresolved:list` reads out a requirement that rests on a revoked
  decision, and it fails nothing. Whether it still stands is a judgement, and
  AGENTS.md holds that no check may fail on a state that is legitimately
  unfinished. What does fail is a `restsOn` that names an id no decision has.
- The generated listing is two lists, what still holds and then what fell, and a
  revoked entry shows its successor from `revokedBy`. One list of 55 rows made a
  revoked entry look exactly like something to build on.
- The body is `## Evidence`, `## Decided`, `## Assumed`, `## Wrong if`,
  `## Covered by` and a dated section per later visit, each with bullets. It was
  a flat list of bullets with a bold label each, and the label repeated.
  Measured over the 56 entries, `Decided` carries more than one item on 25 of
  the 53 that have it. `Assumed` does on 22 of 49, up to seven in one entry. A
  section says the word once. The dated sections carry prose instead of bullets,
  because each is an account of one visit rather than a list.
- `Covered by` is a list, one test per line, rather than a comma-separated
  sentence. A check reads it, and a list is what it is.

## Assumed

- The eleven `restsOn` links, taken from what each requirement already named in
  prose, are the real dependencies. Nothing checked whether a requirement rests
  on a decision it never mentions, and 112 of the 123 name none at all.
- `revoked` is not so heavy that a session avoids it and leaves an entry `open`
  instead. The word is stronger than `corrected` was, and that is the point, but
  it is also the failure mode.
- The 56 entries survived a rewrite by script: bullets to sections, re-wrapped
  to 79 columns and sentence-cased where the label had carried the capital. The
  re-wrap broke 30 code spans across lines before a second pass made a span
  unbreakable. `D-DOC-001` is the entry against that, and nothing would have
  failed on it. `bin/cli decisions:check` holds the shape and `ProseTest` the
  sentences; neither reads for a sentence that lost its sense.

## Wrong if

- An entry appears whose outcome is none of the three. Most likely one where the
  decision still holds but the world it came from is gone, which is
  `D-SCO-001`'s shape. That one sat under `corrected` because there was nowhere
  else.
- `restsOn` stays at eleven entries. A crossing nobody maintains reports
  nothing, and a report that is always empty reads as "nothing is wrong" rather
  than "nothing was recorded".

## Confirmed on 2026-08-22

The second **Wrong if** has its answer and sessions maintain the crossing.
`restsOn` stood on eleven entries and stands on 108 of 222 requirements, so the
report reads what stands recorded rather than stays empty. The three states
carry the corpus, and 33 of the 39 revoked entries name a successor. The first
**Wrong if** is a judgement per entry rather than a sweep. Nobody has reached
for a fourth word, and the six revocations that name no successor are the
nearest thing to the shape.
