---
id: D-KNW-145
title: A curated phrase crosses the gate whatever punctuation it carries
date: 2026-09-02
status: open
coveredBy:
  - HintsTest::aQuotedErrorMessageCrossesTheGateTheWayAPlainPhraseDoes
---

# D-KNW-145 — A curated phrase crosses the gate whatever punctuation it carries

**The matcher counts a curated `appliesTo` phrase in terms rather than in
letters and spaces. So a quoted error message crosses the domain gate the way a
plain phrase already did.**

`D-ANS-084` opened the gate to a multi-word phrase in 2026-08-18. Its shape test
asked for letters, digits and single spaces, which the symptom the gate exists
for does not have.

## Evidence

- `fluid-object-access` carries `is of type "boolean"` and
  `was registered with type "array"`. Both carry quotes, so `spelledOutPhrases`
  read them as no phrase at all. The hint stayed behind the gate for a caller in
  the class that raised the message. That is what `D-KNW-075` measured from the
  other end on 2026-09-02 and answered for that one message with a keyword in
  the Fluid domain.
- Counted on 2026-09-02: 1832 curated patterns, of which 809 cross today. 33
  more carry a space and fail on punctuation alone. Two Fluid messages,
  `the requested page does not exist!`, `typo3conf/ext is empty`,
  `ddev import-db`, `composer test:php:functional` and the like. The rest of
  what the letters-and-spaces test rejected is paths and single words.
- The sweep the todo asked for, over 206 queries, every forward and contract
  prompt and every hint title, returns exactly what it returned before. Entry
  for entry.
- What changes is the case it exists for. With `Classes/Domain/Model/` in the
  paths and `is of type "boolean"` in the task, the answer changed. It went from
  `extbase-domain-mapping` alone to that hint and `fluid-object-access` beside
  it. Two other symptom queries measured the same way stay as they were. The
  hints that answer them are in the domain the paths already selected.

## Decided

- The shape test is two rules rather than a regex. A pattern with a path
  separator is not a phrase, and a pattern of fewer than two terms is a word a
  hint sits under. Both were already the rules `D-ANS-084` decided; only the
  second was expressible in the pattern it used.
- Nothing else about the gate moves. It is still the task text alone, still a
  phrase no selected hint claims, and still no field on the answer.
- The code states the path half and no test holds it. Every query that writes a
  path out places its own domain with it. So no query reaches a path pattern
  where the gate has not already selected that hint's domain. A test that
  asserts it would pass with the rule deleted.

## Assumed

- That a hint with an error message in its `appliesTo` means the matcher to
  match on it. That is what a curated message is for, and the two Fluid ones
  came from a session that arrived with exactly that string.
- That 33 more phrases behave like the 809. The sweep says they change nothing
  on the queries this repository has; a corpus that grows its message vocabulary
  is the case nobody has measured.

## Wrong if

- A session reports a hint from a layer its query never meant, and the hint
  crossed on a phrase with punctuation. That is `D-ANS-084`'s first **Wrong
  if**, now with a wider set of phrases behind it.
- The pass through the gate becomes ordinary rather than an exception as writers
  curate messages onto more hints. `bin/cli hints:coverage` and the sweep are
  what would show that.
