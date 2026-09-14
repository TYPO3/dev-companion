---
id: D-KNW-144
title: The boundary of an authorised rework is stated where a change is made
date: 2026-09-02
status: open
coveredBy: []
---

# D-KNW-144 — The boundary of an authorised rework is stated where a change is made

**The workflows that change something state that a session reports an
unrequested change to the author's decision rather than makes it. The `audit`
brief does not.**

The card asked whether the audit checklist should carry the sentence in general.
That brief already opens with the statement that it changes nothing, so it is
the one workflow in which the failure cannot arise.

## Evidence

- What `typo3_task_guide` returns for `changeType="audit"`, measured on
  2026-09-02. The brief opens with "This is a brief for work that changes
  nothing, so what a patch owes — the deprecation sweep, the focused diff, the
  test coverage, the commit message — is left out below". The intent carries
  `changesNothing`, and its last checklist item says a review changes nothing
  itself and the report is what it produces.
- The event the boundary came from was not a review. The session had
  authorisation to rework and changed the author's own decisions: a
  subnamespace, a class left non-final, accessor visibility.
  `feedback/archive/2026-09-01-210110` reports that from the review side and
  `D-SKL-090` answered it. The change stacked above refuted all four findings.
- Three surfaces state it already, each where a change is actually written. The
  review skill's boundary line. A review that rewrites what it reviews has
  destroyed the evidence for its own findings. The first edit to a file meant to
  survive asks whether `typo3-core-patch-development` should run. That skill's
  own rule: keep the patch one change, and what else you noticed is another
  issue and another patch. And `core/contribution/rules` under `## Code Style`,
  which `D-KNW-141` filled on 2026-09-02. Edit rather than rewrite, and leave
  the form of what the change did not touch.

## Decided

- The `audit` checklist gains nothing. A fourth statement of it in the one
  workflow where nobody writes buys nothing and costs out of the budget
  `D-FBK-020` names.
- The feedback goes to the archive whole. Its other half, that an agreed step
  proceeds with no question, is the client's work preference. This server states
  nothing about how a client drives a session.
- Against a sentence in `skills/base.md`. Every task would carry it for a case
  that arises only where a change has authorisation on work somebody else
  decided.
- Nothing holds this entry, because what it decided is that nothing changes.
  Tests hold the three surfaces that do carry the boundary where they stand.
  `KnowledgeTest::theRulesSayWhatShapeAPatchIsLeftIn` for the contribution
  rules, and the review skill's own tests for the boundary line.

## Assumed

- That a session that reworks somebody's patch reads "what else you noticed is
  another issue and another patch" as a decision. It reads it as much as a
  defect. A session notices a class left non-final the same way it notices an
  absent guard.
- That the report of one session, which was reading its own transcript, is what
  this rests on. No second session has reported the same boundary line.

## Wrong if

- A session reports an unrequested change under an authorised rework and names
  the brief or the skill it held. That would put the statement in the wrong
  place rather than say it is absent.
- A session reports the same boundary line in a project or an extension
  repository. There the server withholds `core/contribution/rules` and both
  skills that carry it are the core's.
