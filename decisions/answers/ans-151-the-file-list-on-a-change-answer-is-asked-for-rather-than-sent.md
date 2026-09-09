---
id: D-ANS-151
title: The file list on a change answer is asked for rather than sent
date: 2026-09-09
status: open
coveredBy: []
---

# D-ANS-151 — The file list on a change answer is asked for rather than sent

**A change read by name carries its file list only where the caller asks for it,
because on a large patch it is the biggest thing this server returns and the
least used.**

`D-ANS-112` weighed the list against a median change of five files and put it on
every named change. Its second **Wrong if** has fired on the changes a
refactoring is made of.

## Evidence

- **The report.**
  [`feedback/2026-09-09-182544`](../../feedback/2026-09-09-182544-typo3-gerrit-lookup-inlines-the-whole-file-list.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5[1m]`. Two reads by name in
  one session: roughly 140 file entries for change 93620 and roughly 200 for
  95425. Its own words are that this was by far the largest thing the server
  returned all session and that it used almost none of it.
- **The population the list was weighed against does not cover this shape.**
  `D-ANS-112` measured a median of five files and a ninetieth percentile of
  forty. Both changes here are past that percentile by three and five times, and
  the session read two of them, because "finish this patch, taking that one into
  account" is one request rather than two.
- **What the caller used instead is named, twice.** The same report lists
  subject, status, branch, patchSet, owner, mergeable, fetch.ref, commit and
  parent, the commit message body, chain and messages — and the file list in
  neither list.
  [`feedback/2026-09-09-182635`](../../feedback/archive/2026-09-09-182635-what-worked-chain-mergeable-and-fetch-ref-on-a.md)
  is the same session's positive counterpart and names the same fields, filed so
  a later trim would not cut them.
- **A third session names two more and says why they earned it.**
  [`feedback/2026-09-09-184254`](../../feedback/2026-09-09-184254-six-answer-details-that-carried-this-session.md):
  `releaseLines` and `issues[]` arrive unasked on answers that have nothing to
  do with either, and each decided a turn two steps after it was read. A caller
  that needs them is the one that does not know to ask.
- **A fourth names the field a trim would reach for first and should not.**
  [`feedback/2026-09-09-190227`](../../feedback/2026-09-09-190227-what-carried-this-session-the-chain-and.md):
  `chain` turned a request naming one number into the three links it had to act
  on, and `chainedAt` against `patchSet` on the merged link is why that chain's
  base was not an ancestor of `main`. Nothing in a checkout says either.
- **The boundary at the diff held.**
  [`feedback/2026-09-09-184232`](../../feedback/2026-09-09-184232-reading-a-change-gives-its-file-list-and-line.md)
  wanted content, fetched with the answer's own `fetch.ref` and read it in the
  checkout, and calls that a good route that is not written down.

## Decided

- **The list goes behind a parameter, which is what `D-ANS-112` named as the
  consequence of this **Wrong if**.** The default is the cheap end, since three
  sessions used none of it and the one that wanted paths wanted them filtered.
- **The fields the four reports name stay where they are, unasked.** `chain`
  with `chainedAt`, `mergeable`, `fetch.ref`, `commit` and its parent, the
  commit message body, `messages` with the service users dropped, `releaseLines`
  and `issues[]`. Each did work no checkout could do, and two of them were read
  by a caller who had not asked.
- **Against a content parameter.** `D-ANS-112` drew the line at what the patch
  touches and what it says about itself, and the session that wanted content
  fetched and read it in the checkout in five cheap calls. What is missing is
  the sentence saying that is the route, not a second way to carry a diff
  through this server.
- **The description says what the answer carries.** Three reports assumed from
  the name alone that this tool returns metadata they already had, and one of
  them never called it. `chain`, `chainedAt`, `mergeable`, `fetch.ref` and the
  `path` way in are what they named as decisive after reading the schema.
- Queued rather than built here. It changes `src/` and a declared input schema,
  which `D-FBK-052` leaves on the far side of the line.
- The card carries `normal`. Four sessions, one tool, and none of them lost a
  task over it.

## Assumed

- That a caller wanting paths wants them narrowed. Read off one report, which
  says a count and the top-level directories would have answered what it
  actually wanted.
- That the two-step to content stays cheap. It was five `git show` calls against
  a ref the answer handed over, in a checkout that was already there.

## Wrong if

- A session reports asking for the file list on every read it makes. Then the
  parameter costs a round trip where the field cost bytes, and the default is on
  the wrong side.
- A session reports acting on a trimmed answer where the full list would have
  stopped it. Then what was cut was load-bearing after all.
- A session reports the two-step to content as the thing that cost it the task,
  with the sentence naming it in place. Then the boundary `D-ANS-112` drew is in
  the wrong place and a content parameter is what is left.
