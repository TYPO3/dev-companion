---
id: D-ANS-130
title: A hint answer says how much of the question it carries
date: 2026-09-01
status: open
coveredBy:
  - KnowledgeTest::aHintAnswerSaysHowMuchOfTheQuestionItCarries
---

# D-ANS-130 — A hint answer says how much of the question it carries

**The answer states the coverage of its closest hint, and says above the hints
where that is under the floor a hint answers on its own from.**

Six well-formed hints about neighbouring subjects read exactly like six that
answer the question, and nothing in the answer told them apart.

## Evidence

- The reported call of 2026-08-31 asked for the CType selector and got
  `content-elements`, `tsconfig`, `tca-formengine` and three more, none of which
  states anything about the subject. Re-run on 2026-09-01 before the hint that
  answers it was written: `tsconfig` covered 0.139 of the query and
  `content-elements` 0.402, both admitted by their `appliesTo` patterns rather
  than by the coverage floor of 0.5. The caller spent three calls establishing
  what those two numbers say.
- The matcher already records it. `matchedOn.coverage` per hit has been there
  since `D-ANS-115` and is what `bin/cli hints:probe` prints; nothing in an
  answer read it.
- The unmatched words of the query were the other candidate and are worse. A
  term is weighed by how few hints carry it, so on that same query the rarest
  word no hint stated was "via", and the second was "manipulating".

## Decided

- `bestCoverage` on the answer, between 0 and 1, and null on a call that named
  an id — an id is not a guess at a phrasing.
- The sentence stands above the hints rather than below them, because it says
  how to read what follows. It names the percentage, says that each hit got in
  on a path or an anticipated phrase, and names `typo3_documentation_lookup`.
- The threshold is `Hints::MIN_COVERAGE`, which the corpus was measured against
  already. No second number is introduced for this.
- Against a per-hit coverage in the answer. What a caller does with six numbers
  is what this exists to save them, and the highest of them is the one that says
  whether anything answered.

## Assumed

- That a caller reads a sentence that arrives before what they asked for. The
  same assumption as `D-ANS-129`, and nothing measures either.
- That coverage of the query is the right measure of "about your question" for
  an answer as well as for a probe. It was picked for admission, and this uses
  it to describe rather than to decide.

## Wrong if

- The line fires on ordinary answers often enough to be read past. It is bounded
  by the same floor that admits, so what would show it is a corpus where
  patterns carry most answers.
- A caller reads a low coverage as "the corpus has nothing" and stops, where the
  right hint was there under a phrasing they did not use. The index in the same
  answer is what stands against that, and this sentence names the manual rather
  than the end of the road.
