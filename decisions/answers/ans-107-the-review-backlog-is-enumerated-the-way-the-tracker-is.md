---
id: D-ANS-107
title: The review backlog is enumerated the way the tracker is
date: 2026-08-25
status: confirmed
coveredBy:
  - GerritTest::aBlockingVoteIsWhatALabelStandsAtWhereTheRulesDisagree
  - GerritTest::aPageOfTheBacklogSaysHowMuchOfItThatIs
  - GerritTest::aPersonOnEitherSideIsOneQuery
  - GerritTest::aSearchAnswersWhatALabelStandsAtWithoutTheVotersBehindIt
  - GerritTest::anEmptyBacklogSaysWhatItCannotSeparate
  - GerritTest::everyBacklogFilterIsAnOperatorComposedHere
  - GerritTest::everyRowCarriesTheSizeTheMergeAndTheAgeOfItsChange
  - GerritTest::theChangesTheirAuthorsMarkedUnfinishedAreOutOfEveryEnumeration
  - GerritTest::theEnumerationIsAWayInOfItsOwnAndTheFiltersNarrowIt
  - GerritTest::theLineAPageIsScannedBySaysSizeMergeAndAge
  - GerritTest::theOldestFirstOrderIsTheMatchedSetSortedHere
  - GerritTest::theReadSaysHowManyItCoveredAndWhetherThatIsAllOfThem
---

# D-ANS-107 — The review backlog is enumerated the way the tracker is

**`typo3_gerrit_lookup` enumerates the open review backlog under the filters a
triage names. Every row it answers carries a change's size, vote state and
whether it still merges.**

Two sessions asked it for the backlog on one day, from different tasks, and both
left the server for hand-built curl against the review API. `typo3_forge_lookup`
answers the same question about the tracker, so this is a hole in one tool
rather than a domain nobody has built.

## Evidence

- `feedback/2026-08-24-183253` had the task to work off old reviews that are
  small and nearly voted through. It ran four paged calls against the changes
  endpoint and scored 859 changes locally on created date, size, Code-Review
  tallies, mergeable and unresolved comments. It says it would write the same
  script again.
- `feedback/2026-08-24-205050` was told to finish almost-ready open reviews. It
  loaded this tool's schema first, never called it, and wrote `owner:`,
  `reviewedby:` and four `label:` predicates by hand.
- `GerritLookup::inputSchema()` takes `issue`, `change`, `commit`, `query`,
  `path`, `open` as a boolean and `limit`. Nothing orders, nothing filters by
  size, vote or person, and `Gerrit::MOST` caps a page at 25 with no offset.
- Every predicate the two sessions wrote by hand answers anonymously, measured
  on 2026-08-25. `delta:<=60` narrows the 855 open core changes to 329 and
  `age:1y` to 159.
  `-is:wip is:mergeable label:Code-Review>=1 -label:Code-Review<=-1 -label:Verified<=-1`,
  the query the second session's whole task rested on, narrows to 74, in 0.1
  seconds.
- `owner:` resolves a name as well as an address: `benjamin.kott@outlook.com`
  and `Benjamin Kott` answer the same 45 changes.
- The three fields both reports ask for cost nothing. A bare row already carries
  `insertions`, `deletions`, `mergeable`, `created`, `unresolved_comment_count`
  and `submit_records`, and `Gerrit::change_()` drops all six.
- The per-voter tallies are the one thing that is not free. A page of 500 rows
  is 666 KB as it comes and 1.14 MB with `o=DETAILED_LABELS`.
- The review server takes no order by age. It answers by last activity, and
  offers no created-date predicate and no total count. So oldest-first is the
  matched set ordered here, and the whole open backlog is two calls, 1.1 MB and
  half a second.
- `feedback/2026-08-25-105203` wants the mergeable read from the other
  direction, after it established by hand whether the patch set it fetched still
  applies.

## Decided

- Built, and as a further way into `typo3_gerrit_lookup` rather than as a tool
  of its own. That is what `D-ANS-054` decided for the tracker: one subject, one
  verb, one record shape.
- The enumeration is its own argument and `open` keeps its meaning. It is a
  boolean that narrows `query` and `path` today. A boolean that becomes the
  sibling tool's `"oldest" | "stale"` enum breaks every caller that passes
  `true`; `AGENTS.md` adds fields rather than renames them.
- The filters are arguments this server composes into a query, never a Gerrit
  query passed through. The operators, the quotes and the escapes stay here, and
  `query` in the answer says what they produced. That is what `D-ANS-100`
  decided for the words and the path.
- The row widens for every direction rather than for the enumeration alone. That
  is size, whether it still merges, its creation date, how many comments are
  unresolved, and the label states `submit_records` carries. That moves the
  per-hit boundary `D-ANS-100` set, and it moves it onto fields the server sends
  unasked.
- The per-voter tallies stay out of a search. They are what a read of one change
  answers, and they cost 0.9 KB a row.
- A person is a filter here as it is on the tracker (`D-ANS-089`), by `owner:`,
  by `reviewedby:` and by the union of the two.
- The order is this server's, over the set the filters matched, and the answer
  says how many rows it read. A caller shown 25 of 855 that reads them as the
  backlog has measured the limit.
- No breakdown in the first build. It is what answers a set too large to page,
  and the filters measured above cut 855 to 74.

## Assumed

- That the anonymous search index still answers these predicates. It is what the
  project's own web UI searches with, and a change to it arrives as a smaller
  answer rather than as an error.
- That size, vote state and mergeable are what a reviewer picks a change by.
  Both reports scored on those three and nobody asked either what else it would
  have used.
- That sessions ask for a triage of the review backlog often enough to earn the
  maintenance that moves here, which is the trade `D-FBK-027` names.

## Wrong if

- Callers reach the enumeration and go to the API by hand anyway, which would
  say the filters do not carry the question. That is `D-ANS-054`'s first **Wrong
  if** asked on this server.
- The ways into this tool are no longer one question. That is `D-ANS-100`'s
  first **Wrong if**, and an enumeration that grows its own answer shape is what
  would revoke that entry rather than extend it.
- A caller reads the widened row as the review. `mergeable` is the server's own
  last computation, and a vote a patch set dropped is absent rather than zero.
  So a row that looks like a review and carries none is worse than one that
  carries nothing.
- The order of the matched set here turns out to mean a read of the whole
  backlog for most questions. That is 1.1 MB and two calls each.
- The oldest open changes turn out not to be the ones worth a review. Of the
  five oldest measured on 2026-08-25, three are over 250 lines and three no
  longer merge. That is the opposite of what the first report's task asked for.

## Since then

Built on 2026-08-25, and four things this entry left open settled in the build,
each measured against the review server the same day.

The argument takes the sibling tool's name, so a caller who knows one
enumeration knows the other. The work-in-progress filter is in every enumeration
rather than an argument a caller sets. It is nearly half the open changes, so an
enumeration that keeps them answers mostly unfinished work nobody offered for
review. The query the answer states is where a caller reads that the filter
applied. The date filter is absolute rather than relative, and both read the
only date the server indexes.

## Confirmed on 2026-08-27

**The report this entry's last evidence bullet names proposed a different test
for the same question, and the data refutes it.** It asks for a field that names
the commit a patch set sits on. And whether that commit is still an ancestor of
the target branch. It established that by hand.

The purpose is the field decided here and shipped under two hours after the
report's date. The proposed test is not it. One change measured that day sits on
a commit git calls an ancestor of the target. The review server answers that it
no longer merges. Ancestry would have called it applicable.
