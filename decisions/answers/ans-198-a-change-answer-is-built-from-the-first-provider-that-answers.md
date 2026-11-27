---
id: D-ANS-198
title: A change answer is built from the first provider that answers
date: 2026-11-27
status: open
coveredBy:
  - ChangeTrackerTest
  - ForgerTest
---

# D-ANS-198 — A change answer is built from the first provider that answers

**`typo3_gerrit_lookup` reads an issue's changes, a search and a backlog through
`ChangeTracker`, Forger first and the review server second. A change read by its
number or by a commit comes from the review server alone.**

`D-ANS-197` named the change provider as the next step. This entry records which
of the six ways in Forger serves, measured against its API on 2026-11-27.

## Evidence

- `/api/v1/changes` takes `q`, `path`, `status`, `branch`, `owner`, `maxLines`,
  the date filters, `sort` and one review `list`. It answered 843 open changes
  with their votes, sizes and owners in one read.
- A change read by number carries its files, issues, `Releases:` branches,
  review log, comment threads, chain and conflicts. It carries no commit hash
  and no address of the owner.
- `q` with the commit hash of change 95437, whole or abbreviated, answered 0.
- Forger names no reviewer and no voter. Its open changes include those in
  progress, 76 of the first 100, and no filter leaves them out.
- `q` finds any of the words with stems and ranks by relevance. The review
  server needs every word.

## Decided

- `ChangeProvider` carries the three reads both hosts serve: the changes of an
  issue, a search, and the backlog. A change read by name and by commit stays a
  method of `ChangeTracker` that asks `Gerrit`. The checkout skill holds the
  commit against `HEAD` and the owner's address against `git config`.
- Forger answers a backlog only without a filter on a person or on a vote. It
  reads the open set whole and drops the changes in progress.
- The changes of an issue come from the read of the issue, so a change with the
  issue's number is never one of them. An empty answer asks the review server
  too, because Forger can be minutes behind a push.
- A change from Forger takes the review server's shape. `commit` and the owner's
  `email` stay empty, and the times take the review server's form.
- A change read stays 30 seconds in the cache on either host.
- `source` names the host that answered, and the text says which search
  semantics answered a query.
- The CI jobs that failed a patch set stay with `CoreCi`, which reads
  git.typo3.org whichever provider answered.

## Wrong if

- A caller acts on an empty `commit` from a Forger answer as if the checkout
  holds the wrong revision. Then a list answer needs the commit from the review
  server.
- Forger serves the commit, the owner's address or a reviewer filter. Then the
  ways in it does not serve today move to it.
- The backlog read whole costs more than a session notices. Then Forger's `list`
  filter has to take the place of the client-side drop.
