---
id: D-GUI-015
title: "A case's own prompt reaches less than the brief"
date: 2026-08-19
status: open
coveredBy:
  - ScenariosTest::aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout
---

# D-GUI-015 — A case's own prompt reaches less than the brief

**Tests that feed a brief which names the answer hold four contract cases. The
prompt each case actually carries reaches less than that brief does.** So what
those tests confirm is the vocabulary, not that the work arrives.

## Evidence

Measured on 2026-08-19 through `TaskIntents::detect()`,
`TaskIntents::confirmed()` and `typo3_task_guide`, with the prompt each case
carries and paths a session would plausibly stand in.

- `EXT-08` reaches nothing. Its prompt, a line added to the mail the core sends
  with no override of the class, detects no intent. The guide answers the events
  hint only where the caller already passes `Classes/EventListener/`. With no
  path, with `Classes/` and with `ext_localconf.php` it does not. The brief its
  proxy feeds is "Register an event listener for the PSR-14 event a package
  dispatches", which names the answer the case is about.
- `SKILL-11` reaches nothing. A security review in the words a maintainer uses,
  with `Classes/` and a template path, names no skill. The case is about
  `typo3-extension-conformance` as it narrows correctly.
- `SKILL-07` reaches the wrong one. It detects `backend-module`, `backend-ui`
  and `audit`, confirms `backend-module` and `audit`, and the guide names
  `typo3-extension-conformance`. The word that does that is `reviewing`, inside
  the module's own subject, "a backend module for reviewing imported records".
  The documentation half the case exists for reaches no documentation intent at
  all.
- `SITE-09` detects `site-setting` and confirms nothing, so the guide names no
  skill. The proxy asserts on `confirmed()`, which the case's own prompt does
  not reach.

## Decided

- **The four cases keep their `not guarded` line and gain the measurement.** The
  check is what the recurrent todo asks for. A case that says what its measure
  ran against is what the next check holds to.
- **The repair goes to the queue rather than happens here.** Each of the four is
  a needle curation for a different intent. `D-SKL-013` is the permanent caveat
  that a needle which reaches two intents is a false route. Four at once is four
  chances to make one.
- **`SKILL-07` is the one to take first.** It is not silence but a wrong answer.
  A session that asks for a backend module gets a conformance audit.
  `D-AUD-003`'s argument about a description that swallows a neighbour task
  applies to a needle the same way.
- **No test joins for this.** A test that feeds the case's prompt would hold the
  arrival, and a hold before the needle curation fixes today's miss into the
  suite.

## Assumed

- That the prompts are how a caller writes. They arrived as such, and no filed
  session carries these four task shapes in its own words.
- That paths are what a session passes. `EXT-08` has its measure on three, and
  the other three on one plausible each.

## Wrong if

- The needle curation lands and a session still arrives by another route. Then
  the brief was never the channel, and `D-SKL-062`'s mid-task question is what
  carries these shapes.
- A filed session reports that one of these four task shapes reached its
  workflow today. Then the wording measured here is not the wording that
  arrives.
- `SKILL-07`'s conformance answer turns out to be the wanted one. Then `audit`
  is right to fire on `reviewing`, and the gap is the documentation half alone.

## Since then

All four got their repair on 2026-08-19, each measured on its own prompt and
against the neighbours it could steal from. The bare gerund is gone from `audit`
in favour of the forms that carry what is under review. The other three gained
the goal beside the mechanism. What the visit corrected here is that one of them
names no skill either way, because its intent routes to none.

The fourth **Decided** is spent. The needle curation landed, so a test that
feeds each case's own prompt no longer fixes a miss into the suite. The half one
case did not get closed the same day with an intent of its own (`D-SKL-066`). A
wider changelog intent would have handed a manual the core's release artifact.
Found beside it: a brief that names the right workflow is not evidence that what
it states is the right work.
