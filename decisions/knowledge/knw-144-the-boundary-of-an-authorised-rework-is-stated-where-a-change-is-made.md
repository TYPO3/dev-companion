---
id: D-KNW-144
title: The boundary of an authorised rework is stated where a change is made
date: 2026-09-02
status: open
coveredBy: []
---

# D-KNW-144 — The boundary of an authorised rework is stated where a change is made

**That an unrequested change to the author's decision is reported rather than
made is stated in the workflows that change something, and not in the `audit`
brief.**

The card asked whether the audit checklist should carry the sentence in general.
That brief already opens by saying it changes nothing, so it is the one workflow
in which the failure cannot arise.

## Evidence

- What `typo3_task_guide` returns for `changeType="audit"`, measured on
  2026-09-02: the brief opens with "This is a brief for work that changes
  nothing, so what a patch owes — the deprecation sweep, the focused diff, the
  test coverage, the commit message — is left out below". The intent carries
  `changesNothing`, and its last checklist item says a review changes nothing
  itself and the report is what it produces.
- The event the boundary came from was not a review. The session had been
  authorised to rework and changed the author's own decisions — a subnamespace,
  a class left non-final, accessor visibility — which
  `feedback/archive/2026-09-01-210110` reports from the review side and
  `D-SKL-090` answered: the change stacked above refuted all four findings.
- Three surfaces state it already, each where a change is actually written. The
  review skill's crossing: a review that rewrites what it reviews has destroyed
  the evidence for its own findings, and the first edit to a file meant to
  survive asks whether `typo3-core-patch-development` should be running. That
  skill's own rule: keep the patch one change, and what else you noticed is
  another issue and another patch. And `core/contribution/rules` under
  `## Code Style`, which `D-KNW-141` filled on 2026-09-02 with editing rather
  than rewriting and leaving the form of what was not touched.

## Decided

- Nothing is added to the `audit` checklist. A fourth statement of it in the one
  workflow where nothing is written buys nothing and is paid for out of the
  budget `D-FBK-020` names.
- The feedback is archived whole. Its other half — that an agreed step proceeds
  without asking — is the client's working preference, and this server states
  nothing about how a session is driven.
- Against a sentence in `skills/base.md`. Every task would carry it for a case
  that arises only where a change is authorised on work somebody else decided.
- Nothing holds this entry, because what it decided is that nothing was written.
  The three surfaces that do carry the boundary are held where they stand —
  `KnowledgeTest::theRulesSayWhatShapeAPatchIsLeftIn` for the contribution
  rules, and the review skill's own tests for the crossing.

## Assumed

- That a session reworking somebody's patch reads "what else you noticed is
  another issue and another patch" as covering a decision as much as a defect. A
  class left non-final is noticed the same way a missing guard is.
- That the report of one session, which was reading its own transcript, is what
  this rests on. No second session has reported the same crossing.

## Wrong if

- A session reports making an unrequested change under an authorised rework and
  names the brief or the skill it was holding, which would put the statement in
  the wrong place rather than say it is absent.
- The same crossing is reported in a project or an extension repository, where
  `core/contribution/rules` is withheld and both skills that carry it are the
  core's.
