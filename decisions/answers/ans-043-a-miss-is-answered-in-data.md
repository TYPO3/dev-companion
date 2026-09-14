---
id: D-ANS-043
title: 'A miss is answered in data'
date: 2026-08-03
status: open
coveredBy:
  - PackageSourcesTest::aMissThatOffersARequeryNamesTheCorpusToAskNext
  - PackageSourcesTest::theNarrowingAMissComputesIsAField
---

# D-ANS-043 — A miss is answered in data

**The narrower query a miss computes is a field of the answer as well as a line
of its text. Where the re-query it offers comes back empty too, the miss names
the corpus that answers what still holds.**

A session read `matchCount: 0` and nothing beside it, and reported that the
changelog says nothing about the backend entry point. The re-query the same miss
had already computed returns the one entry its review turned on.

## Evidence

- `feedback/2026-08-03-144349`, re-run on 2026-08-03 from
  `/home/benji/projects/typo3-cms`, which ships 15.0. `typo3_changelog_lookup`
  with `query: "typo3 directory backend entry point"` still returns nothing. The
  text carries the whole narrower query: five per-word counts, then
  `"typo3 backend entry point" reaches 1 entry`.
- The data half carries none of it. `query`, `matchCount: 0`, `tags: []`,
  `entries: []`, 55 `versions` and `answeredBy: "packages"`. Those are the six
  fields the feedback quotes, and no field carries what the miss worked out.
- The narrower query was there when the session ran. `perTermCounts` reached
  this answer on 2026-08-01 in `40557f5`, the subsets on 2026-08-02 in
  `cf6bcdb`, the filter sentence at 09:47 on 2026-08-03 in `c410db4`. The
  feedback carries the stamp 16:43 that day.
- The offered subset answers the review. `typo3 backend entry point` returns
  `13.0 Deprecation: TYPO3 backend entry point script deprecated (#87889)`,
  alone, in one call, matched by name.
- What #87889 says is what the review was for. Read in the same checkout: the
  deprecated thing is the script `/typo3/index.php`, "it is still in place", and
  the route path becomes configurable through
  `$GLOBALS['TYPO3_CONF_VARS']['BE']['entryPoint']`. Its affected installations
  are "all installations using the TYPO3 backend `/typo3`".
- The URL prefix is still there on this branch. `DefaultConfiguration.php:1637`
  reads `'entryPoint' => '/typo3'` and `UriBuilder.php:199` still constructs
  `new RequestContext('/typo3/')` — the line the session found by grep, after
  the changelog.
- The miss names no tool. Its last line offers the core repository and
  docs.typo3.org, and only for a version this installation does not ship.
- `bin/cli feedback:list` on 2026-08-03: 16 open, 8 of them from this core
  checkout, five written by one review session. Its sibling
  `feedback/2026-08-03-144457` reports the same end, the question settled by
  grep, for the same question.

## Decided

- **Step 2 of the ladder, delivery.** The narrower query exists, is correct,
  reaches the entry, and did not reach the session. Nothing about the
  computation is at fault, so nothing about it changes.
- **This entry takes the decision `D-ANS-016` declined.** Its last **Since
  then** named this gap and left it. It reads "a miss whose whole guidance is
  text is that gap already — `matchCount: 0` is the entire structured answer.
  That is one decision about the shape of a miss rather than three, and this
  entry does not take it."
- **Queued rather than closed on the spot.** A declared `outputSchema` gains a
  field and the answer is `src/`, which
  [judging.md](../../documentation/records/judging.rst) puts on the far side of
  the autonomous line.
- **`normal`, and what set it is the corpus rather than this report.** The same
  end, an empty lookup and then grep, is in `feedback/2026-08-01-115112`, in the
  three `2026-07-31-1745` reports behind `D-ANS-010`, and in this one's own
  sibling. Four sessions from two checkouts, none of them `low`.
- **This entry takes the feedback's second suggestion in the other order.** It
  asks the zero to say that a changelog records change events and to send the
  caller to `typo3_documentation_lookup`. Here the changelog did carry the
  entry, one subset away. So a sentence that names the manual first would have
  routed this session away from it. The query that reaches comes first, and the
  corpus is what to ask once that comes back empty too.
- **No new requirement.** `R-ANS-002` already says the reason is in the data and
  `R-ANS-018` that an answer names the tool for what it says is absent. Both are
  `held`, by cases over `typo3_server_scope` and `typo3_project_describe`. The
  gap is the path, not the rule.

## Assumed

- That a caller who composes on `structuredContent` reads a field it did not ask
  for. Nothing measures that. The other account of this feedback, a session that
  had the text and quoted only the data, leaves the same lever. So the
  assumption decides the size of the win and not whether there is one.
- That the manual answers what the changelog's silence leaves. `D-ANS-010`
  assumed the same and verified one shape of it, and
  `feedback/2026-08-03-144457` doubts it for this very question. That card is in
  hand elsewhere.
- That a sentence in the miss reaches a session the same sentence in
  `skills/base.md` did not. This feedback is the evidence for it: the session
  quotes the base.md routing back and did not follow it.

## Wrong if

- A later feedback quotes the new field and goes to grep anyway. Then the
  delivery was not the gap, and what a miss says is worth less than `D-ANS-016`
  and this entry both assume.
- A session follows the corpus sentence and the manual does not say whether a
  mechanism still holds. That is `D-ANS-010`'s first **Wrong if**, and it fires
  here on the same words.
- A client reports the miss as too long to act on. It already carries up to five
  sentences, and one more is the cost of an offer anybody reads at all.
- A subset the data half now promises returns entries a caller cannot use,
  because the field has a value where the text withholds it. That is under a
  `tag`, where `D-ANS-016` established that a subset promises what the same call
  does not return.

## Since then

Built on 2026-08-03. The miss branch held every value already and declared none
of them, so this cost three fields and three lines beside the count. Each is
present where the miss computed it and absent where the text withholds it.
Re-run, the answer returns the per-word counts and the subset as data, and that
subset still returns one entry alone. Which of the two count fields carries a
number says where it comes from.

## Since then

The branch that paragraph reserved is where a session lost its question: it read
a miss, and swept 1405 commits by hand. Re-run, the answer carries the miss, the
per-word counts and what the installation ships, and names no tool anywhere.

The branch is larger than the paragraph that reserves it reads. The subsets skip
a carried set of one and one of the full size. So a query of exactly two words
can produce no subset at all whatever the corpus holds. The shortest queries are
the ones that end with no route out. They are not an edge of this branch but the
whole of it.

## Since then

The branch landed on 2026-08-26 and names both tools rather than one, which is
[`D-ANS-110`](ans-110-a-changelog-miss-with-no-re-query-names-the-manual-and-the-rules.md).
What decided it is that neither corpus answers the other's shape and the miss
cannot tell them apart. The manual returns the Backend entry point page at 44%
of the reported query's weight, and the rules return which change types owe an
entry.

The single route this entry chose stays where it was. Its objection holds
wherever a re-query is in hand, because there the corpus sentence is about the
call after next. So no miss with an offer grew by a word, and the sentence with
both stands only where the answer offers nothing.
