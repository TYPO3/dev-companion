---
id: D-ANS-141
title: A distribution is one call rather than one per value
date: 2026-09-02
status: open
coveredBy:
  - RecordLookupTest::oneColumnIsCountedPerValueInOneCall
---

# D-ANS-141 — A distribution is one call rather than one per value

**The read was already grouped by three columns, so the one the caller names is
a fourth on the same query rather than a call per value.**

## Evidence

- `feedback/archive/2026-09-02-135112` established what 3,101 animal records
  were with thirteen counted calls: one per status, then one per column it
  wanted the empties of. It says six of the thirteen would have been a count
  grouped by one column, and that the counts-only mode is what made thirteen
  affordable at all.
- The probe already groups: by `pid`, and by the delete and disable columns
  where the table has them. The page distribution the answer carries is that
  grouping read one way.
- `where` takes exact equality and no operator, which `D-AUD-017` keeps: what
  the session could not ask was not a range but a distribution, and those are
  different asks.

## Decided

- `typo3_record_lookup` takes `groupBy`: one column, counted per distinct value,
  on the same grouped read. It combines with `where`, which narrows what is
  counted.
- The column is held to the table the way a filter's columns are. A column the
  table does not have is answered as such rather than as an empty result.
- A value nothing carries is not in the answer. The distribution is what the
  table holds, and a status no row carries is read off its absence — which is
  how that session found the two statuses no content element can select.
- One column and not several. Two would be a cross-tabulation, which is a
  different answer with a different shape, and no session has asked for it.

## Assumed

- That the grouped read costs what the ungrouped one did. It is one more column
  in the `GROUP BY` of a query that already had three, and nothing measured it
  on a table larger than the one reported.
- That the caller wants the states beside the total. Every group carries live,
  hidden and deleted because the page distribution does, and a distribution that
  counted deleted rows silently into the total would be the wrong number.

## Wrong if

- The values come back in the thousands. A column with one value per row is a
  listing rather than a distribution, and the answer would be a page of it with
  no way to say so.
- A session asks for two columns at once. The cross-tabulation is then the shape
  and this is the half of it, which is a wider answer than the one report here
  supports.
