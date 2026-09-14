---
id: D-AUD-018
title: Records are read and the boundary is the TCA the installation has
date: 2026-09-04
status: open
coveredBy:
  - RecordLookupTest::aColumnDeclaringNoDefaultSaysSoRatherThanNamingNoRow
  - RecordLookupTest::aFilterIsEchoedSoTheNumberSaysWhatItCounted
  - RecordLookupTest::aFilterOnAColumnTheTableHasNotIsAnsweredRatherThanRun
  - RecordLookupTest::aNamedColumnTheTableHasNotIsAnsweredRatherThanRead
  - RecordLookupTest::aTableFullEnoughToLeaveTheRecordListIsSaidToBe
  - RecordLookupTest::aTableOneScreenLongAsksForNothing
  - RecordLookupTest::aTableTheCoreRegistersIsReadLikeAProjectsOwn
  - RecordLookupTest::aTableTheInstallationHasNoTcaForIsRefusedRatherThanRead
  - RecordLookupTest::askingForTheCountLeavesTheRowsUnread
  - RecordLookupTest::everyCountSaysItWasReadWithoutBackendPermissions
  - RecordLookupTest::theColumnsARowCarriesBeyondItsFixedShapeAreTheCallers
  - RecordLookupTest::theRowsComeBackBesideTheCountThatSaysHowManyThereAre
  - RecordLookupTest::theRowsDepartingFromTheColumnsDefaultAreNamedBesideIt
  - RecordLookupTest::theTablesItWillReadAreListedWithoutOneBeingNamed
  - ScopeTest::theBriefRoutesToTheCountWhereTheCountDecidesSomething
  - ScopeTest::theToolsACallerCannotChooseBetweenNameEachOther
---

# D-AUD-018 — Records are read and the boundary is the TCA the installation has

**`typo3_record_lookup` reads any table this installation has TCA for, and the
columns a row carries beside its fixed shape are the caller's to name.**

A grouped answer says which rows depart from the column's default, which is the
half of a distribution that decides something.

[`D-AUD-017`](aud-017-records-are-read-and-the-boundary-is-the-table-they-are-in.md)
drew the boundary at a project-owned extension's tables and left what a row
shows to the probe. A session then decided a whole rewrite from six `ddev mysql`
queries over `tt_content` and `pages`. It never called the tool, which would
have refused every one of them.

## Evidence

- `feedback/2026-09-04-053618`. Six queries on a live installation, and each one
  changed what the session wrote. The CType distribution made it safe to leave
  nine core elements as they are. A ten-column distribution decided the markup
  of the replacement layout. The others are the one `header_layout` row it
  exposed, the `header_link` distribution per CType, and two page listings for a
  render baseline.
- The single exception is what the distribution was worth. One row of 137
  carried a header layout no other row did; it is the site's only `h1`. The
  session says a header partial trimmed to a fixed `h2`, which every other
  signal justified, would have demoted it. Neither the CSS nor the browser suite
  would have caught that.
- The shape was already half here. `groupBy` shipped on 2026-09-02
  ([`D-ANS-141`](../answers/ans-141-a-distribution-is-one-call-rather-than-one-per-value.md))
  and answers the distribution. What a caller could not point it at was the
  table, and what it could not name was the row that departs.
- The session never read the tool's own description, which does say the
  boundary. What it read was `knowledge/task-intents.json`, whose `nextTools`
  line described the tool as a counter and named neither the distribution nor
  the boundary. That line is the same failure as
  [`D-ANS-072`](../answers/ans-072-a-tool-description-says-which-questions-it-takes.md),
  one level up: a caller routed by a blurb calls nothing.
- The narrower boundary cost more than it protected. Ten of the six queries'
  columns are `tt_content`'s own markup fields, and none of them is anything an
  editor could not read off the page.

## Decided

- **The boundary is TCA and the whole of it.** A row a `ctrl` describes is a
  record. A table nothing describes is the caches, the queues and the session
  store, and none of those holds one. `pages`, `tt_content`, the user tables and
  what a dependency brings are all in. The extension a listing reports for each
  is attribution rather than admission.
- **What a row carries beside its fixed shape is the caller's to name.**
  `columns` takes a list, and the tool checks each name against what TYPO3
  derives for the table, the way it checks a filter's columns. So a name goes
  into the SQL as an identifier only after the table has said it has it. The
  fixed shape stays: uid, pid, the `ctrl` label, the timestamps and the two
  flags. So a caller that names nothing sees what it saw before.
- **A grouped answer carries `groupDefault` and `departing`.** The TCA default
  of the grouped column, and the uid and pid of the rows that do not carry it.
  The list stops at one page of the record list. A distribution says what a
  table holds and not which of it is the exception, and the exception is what a
  cleanup breaks.
- **A column with no TCA default says that** rather than answers with an empty
  list. Nothing departs from a default that does not exist, and an empty list
  would read as if every row were the convention.
- **Everything else `D-AUD-017` settled stands**. The rows come back beside the
  counts, `where` is exact equality and nothing else, and `count` is its own
  parameter. `limit` means what it says with zero for all of them, and every
  answer says it ran with the shell user's database access.
- The same work rewrites the routing blurb in `knowledge/task-intents.json`,
  because a wider tool whose blurb still calls it a counter is the same loss
  again.
- Rejected: the checklist frame the feedback proposes. It asks for an order of
  failure — plugins, then shortcut, then CSS — which is a guide's job and not a
  tool's.

## Assumed

- That the shell user's database access is the trust model, stated rather than
  narrowed. The agent that holds this server already has it, and every answer
  says so. What moved is what this server answers for rather than what it can
  reach. `be_users.password` is a column like any other under that account,
  which the maintainer settled on 2026-09-04 against a boundary around the user
  tables.
- That the rows which depart are few. The cap is one page of the record list. A
  column where half the table departs is a distribution the caller reads off the
  groups instead.
- That the TCA default is comparable to the stored value with a bound `<>`. A
  default declared as `0` against a column that stores `'0'` is the case, and
  the platform's own loose comparison is what settles it.

## Wrong if

- An answer carries something an installation would not want reported onwards, a
  password hash, a session, a personal detail out of `fe_users`. The sentence
  about the shell user's access turns out not to be the whole caveat.
- A caller names so many columns that the answer is a table dump, which would
  make `columns` a thing to cap rather than to honour.
- The list of rows that depart comes back full on every call, which would mean
  the cap hides the answer rather than bounds it.
- A grouped column whose default TYPO3 supplies from somewhere other than
  `columns.<name>.config.default` reports every row as a departure. That would
  make the default read here the wrong one.
- A table TCA describes turns out not to be readable as rows, a view, or one the
  installation has no connection for. That would make TCA the wrong list to take
  the boundary from.
