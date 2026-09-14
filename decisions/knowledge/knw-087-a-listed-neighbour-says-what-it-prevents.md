---
id: D-KNW-087
title: A listed neighbour says what it prevents
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::anUndeclaredContentAreaIsSaidToThrow
---

# D-KNW-087 — A listed neighbour says what it prevents

**The six closing statements that list their neighbours say what each one
prevents. `page-content-areas` gets its correction first, because it denies the
consequence a pointer at it would name.**

A session read the pointers, took about half of them, and paid for two it
dropped. That is five review findings and an HTTP 500 on every page.

## Evidence

- `feedback/2026-08-17-211306`. A build of a v14 demo site over 22
  `typo3_hint_lookup` calls, from a trace of its own transcript rather than from
  recall. It followed roughly half the neighbour ids it got. Two answers named
  `project-build-and-scripts`, the session fetched it after the work, and it
  held the answer to five of a reviewer's ten findings. An answer named
  `page-content-areas`, the session skipped it, and fetched it three calls later
  to diagnose the HTTP 500 it would have prevented.
- The corpus today: 139 hints, of which 37 close by naming another hint's id,
  and 6 of those open with the bare formula — `sitepackage-initial-content`,
  `extension-repository-layout`, `frontend-page-rendering`, `installation-boot`,
  `site-sets`, `browser-tests`. Three of the six are ones the feedback names as
  skipped.
- Delivery and routing were not the failure. The session got the sentences and
  read them. That is what puts this at step 4 of the ladder rather than at step
  2 or 3.
- The feedback's positive control is not a neighbour reference.
  `sitepackage-templates`' layout-collision warning, which it says made it act
  immediately, is a statement in that hint's own body about its own subject. So
  the contrast it draws is between a rule and a pointer, not between two
  pointers. The evidence it offers does not test the mechanism it proposes, that
  a consequence makes a pointer take. `content-element-preview` closes with a
  pointer that does give a reason, and the session skipped that one too.
- The hint it points at states the consequence the feedback wants the other way
  round. `page-content-areas` says a column without an explicit identifier
  "cannot be addressed by name, and the template then renders empty with no
  error". The same hint prescribes
  `<f:render.contentArea contentArea="{content.main}" />` as the way to render
  one. `ContentAreaViewHelper::render()` throws `InvalidArgumentValueException`
  1770212183 for a `contentArea` argument that is not a `ContentArea`, in
  `.checkouts/14.3` and `.checkouts/main` alike. That is the 500 the session
  reported.

## Decided

- Step 4 of the ladder, queued rather than closed in this run. What a rewritten
  pointer states is a consequence about TYPO3, and
  `documentation/records/judging.rst` puts a lookup on the todo side of that
  line. Nothing in `src/`, in a declared schema or in a skill moves.
- The six formula statements are the scope. The 31 references that already carry
  a reason stay as they are. Nothing established here says a pointer with a
  reason fails, and `content-element-preview` is a case against a rewrite of
  them all.
- `page-content-areas` gets its correction in the same work and before the
  pointer at it. A pointer naming a consequence the pointed-at hint denies is
  worse than the list it replaced, because the reader who follows it arrives at
  the contradiction.
- No requirement yet. What must hold from now on is what the counter-case leaves
  open. Does a neighbour reference owe a consequence at all, or only where the
  neighbour guards a failure. A requirement that asserts it today would be one
  sentence ahead of its evidence.
- The card goes up to `normal`. The cost has a measure and the report names the
  same mechanism twice. But that is from one session rather than from two, which
  is what keeps it off `high`.
- The schedule half of the suggestion stays where its own card already has it.
  That is the half that a neighbour that matters only at a later stage says so.
  `feedback/2026-08-17-211118` reports it against a skill's multi-id step, which
  is a different surface from a hint's closing statement. That card is in hand
  elsewhere.

## Assumed

- That the formula is what discriminates. The six read as a table of contents
  and the other 31 read as prose with a reason. The session that reported
  skipped examples of both.
- That the session's 500 came from the ViewHelper rather than from the
  page-content processor above it. The exception code matches what the hint
  prescribes and this run read nothing else.

## Wrong if

- The six get their rewrite and a session reports that it skipped one anyway
  with the consequence in front of it. Then the wording is not the lever, where
  the offer sits is, and this is step 5 against `D-KNW-032` rather than step 4.
- The correction to `page-content-areas` holds only for an identifier the layout
  never declared, and a declared column that is empty renders quietly after all.
  The pointer would then warn about a trap on the path the reader is not on.
- A session reports the opposite cost: a pointer written as a warning followed
  into a hint the task did not need, crowding the limit. That is the second
  **Wrong if** of `D-KNW-032` from this side.

## Since then

The second **Wrong if** is half true and does not falsify the entry. A declared
column with no records does render quietly. What throws is the other path, where
nobody declared the identifier at all. That is the path the session that
reported was on. So the pointer warns about the trap the reader walks into
rather than the one beside it. Both are statements of their own now, and what an
undeclared column costs carries a version bound the old sentence did not have.
