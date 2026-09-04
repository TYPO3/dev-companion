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
shows to the probe. A session then decided a whole rewrite from six
`ddev mysql` queries over `tt_content` and `pages` and never called the tool,
which would have refused every one of them.

## Evidence

- `feedback/2026-09-04-053618`. Six queries on a live installation, each one of
  which changed what the session wrote: the CType distribution that made it safe
  not to rebuild nine core elements, a ten-column distribution that decided the
  markup of the replacement layout, the one `header_layout` row it exposed, the
  `header_link` distribution per CType, and two page listings for a rendering
  baseline.
- The single exception is what the distribution was worth. One row of 137
  carried a header layout no other row did; it is the site's only `h1`, and the
  session says a header partial trimmed to a fixed `h2` — which every other
  signal justified — would have demoted it with neither the CSS nor the browser
  suite catching it.
- The shape was already half here. `groupBy` shipped on 2026-09-02
  ([`D-ANS-141`](../answers/ans-141-a-distribution-is-one-call-rather-than-one-per-value.md))
  and answers the distribution; what it could not be pointed at was the table,
  and what it could not name was the row that departs.
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
  record; a table nothing describes is the caches, the queues and the session
  store, and none of those holds one. `pages`, `tt_content`, the user tables and
  what a dependency brings are all in, and the extension a listing reports for
  each is attribution rather than admission.
- **What a row carries beside its fixed shape is the caller's to name.**
  `columns` takes a list, each one checked against what TYPO3 derives for the
  table the way a filter's columns are, so a name goes into the SQL as an
  identifier only after the table was asked whether it has it. The fixed shape —
  uid, pid, the `ctrl` label, the timestamps and the two flags — is unchanged, so
  a caller naming nothing sees what it saw before.
- **A grouped answer carries `groupDefault` and `departing`.** The TCA default
  of the grouped column, and the uid and pid of the rows not carrying it, capped
  at one page of the record list. A distribution says what a table holds and not
  which of it is the exception, and the exception is what a cleanup breaks.
- **A column with no TCA default says that** rather than answering with an empty
  list. Nothing departs from a default that does not exist, and an empty list
  would read as every row being the convention.
- **Everything else `D-AUD-017` settled stands**: the rows come back beside the
  counts, `where` is exact equality and nothing else, `count` is its own
  parameter, `limit` means what it says with zero for all of them, and every
  answer says it was read with the shell user's database access.
- The routing blurb in `knowledge/task-intents.json` is rewritten in the same
  work, because a widened tool that is still described as a counter is the same
  loss again.
- The checklist framing the feedback proposes is not taken. It asks for an order
  of failure — plugins, then shortcut, then CSS — which is a guide's job and not
  a tool's.

## Assumed

- That the shell user's database access is the trust model, stated rather than
  narrowed. The agent holding this server already has it, every answer says so,
  and what moved is what this server answers for rather than what it can reach.
  `be_users.password` is a column like any other under that reading, which the
  maintainer settled on 2026-09-04 against keeping the user tables out.
- That the departing rows are few. The cap is one page of the record list, and a
  column where half the table departs is a distribution the caller reads off the
  groups instead.
- That the TCA default is comparable to the stored value with a bound `<>`. A
  default declared as `0` against a column storing `'0'` is the case, and the
  platform's own loose comparison is what settles it.

## Wrong if

- An answer carries something an installation would not want reported onwards —
  a password hash, a session, a personal detail out of `fe_users` — and the
  sentence about the shell user's access turns out not to be the whole warning.
- A caller names so many columns that the answer is a table dump, which would
  make `columns` a thing to cap rather than to honour.
- The departing list comes back full on every call, which would mean the cap is
  hiding the answer rather than bounding it.
- A grouped column whose default TYPO3 supplies from somewhere other than
  `columns.<name>.config.default` reports every row as a departure, which would
  make the default read here the wrong one.
- A table TCA describes turns out not to be readable as rows — a view, or one
  the installation has no connection for — which would make TCA the wrong list
  to take the boundary from.
