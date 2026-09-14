---
id: D-ANS-016
title: 'A miss names the query that would have hit'
date: 2026-08-02
status: open
coveredBy:
  - LabelSearchTest::aQueryNoResourceHoldsWholeIsToldSo
  - LabelSearchTest::aResourceHoldingNothingNamesTheResourcesThatDo
  - LabelSearchTest::aResourceHoldingPartOfTheQueryIsNotReplacedByAListing
  - LabelSearchTest::aResourceThatChangedNothingIsNotBlamedForTheMiss
  - LabelSearchTest::anEmptyResultNamesTheLargestPartOfTheQueryThatDoesReach
  - LabelSearchTest::theSubsetThatNarrowsBestComesFirst
  - PackageSourcesTest::aFilterThatChangedNothingIsNotBlamedForTheMiss
  - PackageSourcesTest::aMissNamesTheLargestPartOfTheQueryThatWouldHaveHit
  - PackageSourcesTest::aMissNarrowedByAVersionOpensWithTheVersionThatEmptiedIt
---

# D-ANS-016 — A miss names the query that would have hit

**The changelog miss owes the caller a query rather than per-word counts. That
is the term to ask again with, and the terms that have to go first.**

`typo3_label_lookup` prints the same reach line off the same matcher and ends it
with "ask again with the one that narrows best". `typo3_changelog_lookup` prints
the numbers and stops there.

## Evidence

- `feedback/2026-07-31-194819` re-run on 2026-08-02 from
  `/home/benji/projects/site-new`, against the 3763 changelog entries TYPO3 14.3
  ships. `form set yaml registration deprecated` and
  `form sets discover yaml configuration` both still return nothing. Every word
  of both reaches entries on its own: 358, 213, 17, 29 and 144 for the first.
- The sentence the sibling tool already carries would have ended it.
  `query: "yaml"` with no type and no version returns 17 entries. The first two
  are `14.2 Deprecation: TypoScript-based form YAML registration (#109412)` and
  `14.2 Feature: Auto-discovery of form YAML configurations (#109412)`. Those
  are the two entries the feedback names, in one more call rather than three.
- The rule the feedback asks for is the wrong way round. It wants the word with
  the smallest reach dropped. That word is `yaml` at 17, and it is the one term
  both target entries carry; a drop of it loses them.
- No single word can go either. Both queries need two terms removed before
  anything matches: `form yaml registration` reaches the deprecation, and
  `form discover yaml` reaches the feature. No four-term subset of either
  reaches a thing. Marginal counts cannot show that, so "the reach line already
  carries the data to do this" is false.
- A named subset cost 28 ms and 33 ms over the whole changelog. That is 15
  subsets for a five-term query, peeled a level at a time until a level hits.
  The `tag` filter `D-ANS-006` added costs 23 ms for one major's deprecations,
  read off the same file names.

## Decided

- **Step 4 of the ladder, wording.** The session called the tool correctly, the
  tool answered honestly and its answer reached the session. It delivered data
  about the query rather than a next call. So the session spent three calls to
  arrive at one the answer could have handed it.
- **Queued rather than closed on the spot.** Both halves are `src/`, which
  [judging.md](../../documentation/records/judging.rst) puts on the far side of
  the autonomous line. The second half is a computation rather than a rewrite.
- **The suggestion fails as written and stands on what it is after.** The
  smallest reach names the term to keep, not the term to drop, and no marginal
  count answers which drop lets the intersection survive. The answer owes the
  subset itself.
- `R-ANS-006` is where this already stands. "A miss says what there would have
  been to find, and what it names can be asked for outright" is general. Only
  the hint corpus holds it. The changelog miss names five numbers, and a caller
  can ask for none of the five.
- Recorded against the answer rather than against the search. `D-ANS-006` left
  the matcher right and this leaves it right; the gap is between the empty
  result and the text.

## Assumed

- That a caller with one query in hand follows it. Nothing measures which
  sentence of a miss a session acts on, which is the assumption `D-SKL-003` and
  `D-ANS-010` both left open about wording.
- That the peel stays cheap as the changelog grows. It reads file names and
  never opens an entry, so it scales with the count above rather than with the
  files.
- ~~That two words is far enough to peel. Both queries here recovered at that
  depth, and no query so far needed three.~~ Moot on 2026-08-02. The build is
  one pass with no depth to bound. One of the twelve queries it ran over
  recovered only at two of five words.

## Wrong if

- A caller follows the offered re-query and gets entries about something else.
  The subset that survives is the query's vague half rather than its subject.
- The same trial-and-error follows a miss with the sentence in it, in a later
  feedback. Then the next call was not what the session lacked, and
  `D-SKL-003`'s bounds are the whole of the answer.
- A measurement against a fuller changelog shows the peel costs more than the
  `tag` filter this entry priced it against. Then it is a second call the caller
  makes rather than something a miss can afford.

## Since then

Built on 2026-08-02, and not as the peel this entry priced. A subset reaches an
entry exactly when that entry carries every word of it. So the largest subsets
that reach anything are the largest sets of words a single entry carries. That
is one pass rather than fifteen, and no depth to bound. Four milliseconds over
the entries the checkout ships, against 28 for a walk over the subsets.

The **Assumed** that two words is far enough to peel is therefore moot rather
than confirmed. One of the twelve queries recovered only at two of five words.
The miss names every largest subset rather than the best of them.

## Since then

The filters owe the same wording. Re-run, a narrowed query returns nothing and
says one of its words reaches one entry. The count covers every number inside
the filter and nothing marks it as such. Drop the version and all four words
reach, and one of them returns the entry the session was after, alone, in one
call.

So a caller gets a reach line that reads as a fact about the changelog and is a
fact about the filter. A miss narrowed by an axis owes the same thing about that
axis.

## Since then

A second corpus asks for it. The rule lookup reached its no-match answer only
where the hints missed too. So a compound query that matched a hint heard that a
boundary emptied it, and the subsets are what that miss now offers instead.

The second caller showed that the two have the pass in common and not the
matcher. One pass, handed each corpus's own containment: the labels match a
substring and an identifier spelling, the prose at a word boundary.

## Since then

The first report of the gap from practice took the decision this entry names and
does not take. A session quotes a miss as its structured fields and states that
nothing came back to re-ask with. The subsets were in the text of that same
answer. So the first **Wrong if** did not fire and the offer went unread. That
is the distinction the entry could not draw on its date.

## Since then

One read carried this entry out and established nothing beyond it, so it is a
line here rather than a section of its own. Judged on 2026-08-22.

- 2026-08-03, the wording the read above found owed. A miss narrowed by
  `version` or `type` counts its terms a second time over the whole changelog.
  Where a word reaches there and nothing inside the narrowed set, the miss opens
  with the filter. The counts that stay say where they come from. The "ask again
  with the one that narrows best" goes there, because the call to action is the
  filter. It costs 48 ms over 3795 entries and a hit never reaches it. Unlike
  the subsets, the miss offers it where the caller asked for a `tag`. A count is
  what a word reaches, and only a subset promises entries the same call would
  not return.

## Since then

A third corpus asks for the filter half, and it is the label search. A session
called with a resource path it had guessed and got no match with every word at
zero. It read that as the resource with no such label and went to grep. There is
no such file, and the label it wanted is in the one beside it.

The resource narrows the labels the way the version narrows the changelog, and
the count comes after that filter. So a path that names nothing at all reports
every word at zero, which is what the count reserves for a word nothing here
carries. The counts read as a fact about the labels and are not.

## Since then

Both halves are in the tool, verified against the reported call. The two words
come back with their counts, and the resources name the six files with a label
that carries both. A second ask with one of them returns the label the session
went to `grep` for, in one call. The whole miss costs 23 ms, and a hit never
reaches either computation.

The resources are the sibling's tags rather than the report's directory list.
The judgment had already chosen that, and the implementation made it cheap. They
are the resources of the labels that match before the resource narrowed them. A
sort puts the near misses together for nothing, because a path sorts by its
directory.
