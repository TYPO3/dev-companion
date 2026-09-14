---
id: D-ANS-025
title: A query a hint carries whole is not diluted out of it
date: 2026-08-02
status: confirmed
coveredBy:
  - HintsTest::aHintThatCarriesPartOfAQueryStillDoesNotAnswerIt
  - HintsTest::aTermOnlyOneHintStatesReachesItHoweverLongThatHintIs
  - HintsTest::theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay
---

# D-ANS-025 — A query a hint carries whole is not diluted out of it

**A hint whose own words carry every term of the query meets the coverage floor,
however long that hint is.**

Dilution still decides the rank, and below a whole cover it still decides the
floor. `MIN_COVERAGE` asks what share of the query a hint accounts for. The
dilution weight damped that share by the length of the field the terms turned up
in. Past a body of `UNDILUTED_WORDS * e` words that made a one-term query
uncoverable in principle. The whole query matches and the share is still under a
half. So the hint is no longer a candidate for a question it used to answer.

## Evidence

- The arithmetic, and it is exact. Coverage of a single matched term is
  `1 / (log(words / 200) + 1)`. At a floor of 0.5 that solves to `200 * e`. So
  **544 words** is the length past which no one-term text query admits a hint at
  all. `bin/cli hints:coverage` reads a mean of 291 and a longest of 1147, and
  12 of the 66 hints are over that line.
- The sweep ran over the corpus that can see the gate: every hint's own body
  vocabulary. That is its body terms minus everything its title and its
  `appliesTo` already index. The sweep asked them one term at a time against the
  hint they belong to. Read over the categories a query with no domain signal
  selects at all. The gate of
  [`D-KNW-009`](../knowledge/knw-009-a-domain-keyword-is-a-phrasing-not-a-word.md)
  sits upstream of this one and holds Backend CSS, Fluid and TypoScript out at
  3% whatever their length. **2427 of their 2438** body terms reach the 30
  always-selected hints under 544 words. The 10 over it, by **30 of 2273**. It
  is not a degradation with length, it is a cliff at one length.
- Four identifiers this corpus states in exactly one hint each, in none of its
  titles and in none of its patterns. `showitem`, `allowProperties`,
  `sys_registry` and `PidInList` reached nothing at all: 40 hints returned as
  the index. Tests written before this assert that three of the four are in
  those hints. So the repository already held that the answer is there and that
  nobody could ask for it by name.
- The shape
  [`D-ANS-022`](ans-022-the-matcher-takes-a-hyphenated-compound-apart-measured-over-the-corpus-first.md)
  used, over the queries a caller writes. That is the 208 multi-word bare
  `appliesTo` patterns and the 41 scenario prompts, taken apart into 1360
  one-term queries against the hint they belong to. 419 reached before, 572
  after, none lost. All 153 gains fall on the 8 hints over the line, and **not
  one on a curated pattern's own words**. An `appliesTo` field is a dozen words
  and dilution never touches it. That is why `list_type` and `mod.web_layout` as
  curated patterns repaired the reported case, and why the curated half of a
  sweep cannot see this gate.
- What it moves, over the same 302 whole queries (41 prompts, 66 titles, 208
  patterns). **0 changed their first hit and 0 entries left a top-6 answer**. 71
  gained entries below what was already there. The 12 recoverable queries of the
  [`D-ANS-002`](ans-002-rarity-field-length-and-corpus-length-decide-a-lookups-rank.md)
  sweep answer as the record says, both negative controls included. 943 tests
  pass, and `bin/cli hints:coverage` prints the same page.
- The two alternatives that change the number instead of the question, measured
  the same way. `UNDILUTED_WORDS` at 422, the value that clears the longest
  hint, buys the same 153 one-term queries and answers «how do I write a good
  sonnet». It moves 136 of the 302 whole queries, changes 4 first hits and drops
  14 entries out of top-6 answers. Taking dilution out of coverage outright buys
  the same 153 and answers the sonnet too.

## Decided

- In the gate rather than in the shared matcher. `TermSearch::score()` returns
  the numbers and each corpus says what it admits on. That is why the third
  return value it already carried was enough:
  `ArchitectureHints::coversEveryTerm()` reads it. The other two corpora do not
  have this to fix. `MAX_SECTION_LENGTH` cuts the prose sections, and the
  longest is 400 words against a ceiling of 1087. The manual reads the score and
  no coverage at all. Hint bodies are the one field with no cap, which is
  `D-ANS-002`'s third bullet, arrived where it pointed.
- Dilution stays in the score untouched, and stays in the floor for every
  partial cover. That is where it earns its keep. A text long enough to contain
  *writing* and *good* covers «how do I write a good sonnet» in part. Nothing in
  the corpus carries "sonnet", so the cover is never whole there and the floor
  still drops it.
- Not `UNDILUTED_WORDS`. `bin/cli hints:coverage` says the fix is to run the
  sweep again and pick the constant again. The sweep says the constant is not
  what is wrong. Every value that clears the longest hint is a value at which a
  partial cover answers, because those are the same wall.
- Not the identifiers curated into `appliesTo`, which is what repaired the
  reported case. It is 153 patterns over 8 hints today and it grows with every
  new hint. Most of the words are ordinary prose rather than vocabulary anybody
  would index. And a pattern outranks a text hit, so a pattern curated for reach
  also decides what comes first. The two patterns already there stay for that
  reason: their removal now would move a rank this did not measure.
- Not a split of the hints that outgrew the line. It keeps the text route alive
  with a change of which id a caller reaches. It has to happen again each time
  one grows, and with the gate repaired nothing asks for it.
- `MAX_MEAN_BODY_WORDS` stays where it is. The failure it watches is the partial
  cover: a mean past which a query the corpus cannot answer gets an answer
  anyway. This does not touch it, and the report still says 9 words of headroom.

## Assumed

- A one-term query is best answered by the hint that says the term, long hint
  included. The measured gains are identifiers a caller who names one is after,
  but they are not all of them. The prompt half of the sweep gained ordinary
  words, `somebody`, `yet`, `now`, and 25 one-term queries that returned nothing
  now return a long hint. That displaced nothing, so the bet is that something
  is better than the index here.

## Wrong if

- A one-term query comes back with a long hint that merely mentions the word
  where a shorter one is about it. The 25 queries that went from nothing to
  something are where that would show first, and `bin/cli hints:probe` prints
  which way in earned each hit.
- The corpus grows until one common word is in every long hint and the same
  handful answers every short query. That is the crowd this floor exists to
  stop, arrived through the exception instead of through the mean.
- A hint over the line turns out to have been *usefully* unreachable. A caller
  asks one word and wants the index rather than the hint that says it.

## Confirmed on 2026-08-22

The crowd the second **Wrong if** watches has not arrived. The corpus has more
than doubled since the gate opened, at a shorter mean body and a shorter longest
one.

Swept the way this entry swept, 596 one-term queries taken from hint bodies
minus every word the title or the patterns already index. 101 distinct hints
come back first, the five commonest answer a quarter of the hits, and the rest
spread over a hundred. Seventeen reached nothing at all, which is the floor
still at work on a word no hint is about.
