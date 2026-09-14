---
id: D-ANS-002
title: Rarity, field length and corpus length decide a lookup's rank
date: 2026-07-30
status: confirmed
---

# D-ANS-002 — Rarity, field length and corpus length decide a lookup's rank

**An unknown term, a diluted long field and a per-corpus reference length decide
what a lookup answers.**

The change scores the hint text as well as its keywords. The part that took the
work stops it from an answer to everything. Three constants came out of it. A
sweep over sixteen queries with a known right answer picked each, rather than an
argument. That is also why this entry writes them down. A number that fell out
of one afternoon's corpus is a number the corpus can grow out of.

## Decided

- A term nothing in the corpus carries weighs `log(total) / 2`, as if the square
  root of the corpus held it. At zero, the previous behaviour, «how do I write a
  good sonnet» decayed into a query about *writing* and about *good*. Something
  always answers that. At full weight, one unfamiliar word sank queries the
  corpus does answer. Nobody wrote «upload», and the storage hint no longer
  answered «file upload storage configuration». The sweep was flat between a
  quarter and three quarters, so the entry takes the middle.
- A term found in a field longer than the corpus's ordinary field counts less,
  on a log scale. Without it the longest hint — over a thousand words — answered
  at full confidence whenever a query's words all appeared in it somewhere. This
  is the one that carried the precision. With the unknown-term weight already in
  place, dilution switched off put the returned hints back up from 33 to 49 for
  the same recall.
- The reference length is per corpus, not global — 200 words for the hints, 400
  for the prose. Hint bodies have no cap and differ by more than twenty times,
  so the mean is the meaningful reference. `MAX_SECTION_LENGTH` cuts a prose
  section, so a section at that length is ordinary rather than an outlier. The
  sweep tried a single global value first, and it broke `typo3_script_lookup` on
  «php-cs-fixer and phpstan». There two sections each answer half of a two-word
  query and sat exactly on the floor.

## Assumed

- The hint corpus keeps roughly its current shape. 200 is its mean body length
  today. A corpus that doubles its typical hint makes the ordinary hint look
  like an outlier to this code.

## Wrong if

- Hints get longer on average; the queued dissolution of the architecture prose
  into this corpus moves in that direction. Or a query that should hit misses
  while its hint is plainly about it. Both show up in `bin/cli hints:coverage`
  once that exists; until then the eight queries in `HintsTest` are the only
  tripwire, and they are a sample.

## Since then

The stopword list is English, so German filler words behave like unknown
technical terms and sink the whole query. «wie lege ich ein neues
Content-Element an» reached `content-elements` before this change and does not
now. This entry left it open rather than papered it over with a lower floor. The
measurement that followed settled it the other way round. A caller queries the
server in English, and the server says so to the agent (`R-AUD-006`). A lexical
matcher over an English corpus has nothing else to match on. So the alternative
was never a stopword list; it was a translation of the knowledge base. Twelve
German queries also surfaced two genuine bugs on the way, both independent of
the language and both repaired under `R-ANS-008b`. A three-letter term matched
as a prefix, and `appliesTo` matched by plain substring.

## Confirmed on `2026-08-01`

The first symptom happened and the decision holds anyway. The mean hint body has
grown a quarter past the reference that gave the number its name. The second did
not — every recoverable query of the sweep still answers as recorded.

The second measurement settles that the number should not follow the mean that
gave it its name. Swept again, recall is whole from a reference of 120 words up
to 320 while the hints returned climb the whole way. So the low end of the range
is what the number is for.

## Since then

The second half of the **Wrong if** happened on 2026-08-02, and neither wall saw
it. Individual hints, rather than the mean, walked past a length at which the
dilution weight decides candidacy. Below it the weight only damps a coverage
share. Past `UNDILUTED_WORDS * e` a one-term query cannot clear the floor at
all. So the matcher dropped 12 hints from every question nobody curated them
for. The mean this entry watches sat inside its headroom. The sweep that holds
it stayed green, because all of its queries are several words long. The three
constants stand and none of them moved. The wrong part read a share of the query
off a number that says how long the hint is.
[`D-ANS-025`](ans-025-a-query-a-hint-carries-whole-is-not-diluted-out-of-it.md)
is the measurement and the fix, and it leaves both walls where this entry put
them.
