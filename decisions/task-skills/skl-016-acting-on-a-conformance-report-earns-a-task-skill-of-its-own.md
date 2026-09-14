---
id: D-SKL-016
title: Acting on a conformance report earns a task skill of its own
date: 2026-08-04
status: revoked
revokedBy: D-SKL-064
---

# D-SKL-016 — Acting on a conformance report earns a task skill of its own

**To put a repository right is a task skill of its own. It starts from the
conformance report, commits a worklist derived from it, then works that list
off.**

The change half of that request had one route, and that route was the wrong one.
Its removal left the work with no owner at all.

## Evidence

- **Measured in this checkout on 2026-08-04.** `TaskIntents::detect()` matches
  no intent against four requests. "look over my repository and put it right"
  and "improve the code quality of my sitepackage". "clean up my extension and
  fix what is wrong" and "make my TYPO3 project better". So `skills()` returns
  an empty list and `typo3_task_guide` names no skill. The same call with
  "review the TYPO3 project and site package" returns
  `typo3-extension-conformance`: the intent works, and the wording is what it
  does not reach.
- **Nobody can widen the `audit` intent into it.** Its needles are `audit`,
  `conformance`, `code review`, `review the`, `review this`, `review of` and
  `reviewing`. Every one of them is a word for a look, none of them a word for a
  fix.
- **The route that existed was wrong rather than absent.**
  `typo3-extension-conformance` opened its description "Review, audit, or
  improve a TYPO3 project…". A client selects a skill on that line, so clients
  loaded it for change requests whatever its body said (`D-SKL-014`, **Since
  then**). The removal of the word sent a change request into the workflow that
  exists to make none, which is what
  [`R-GUI-006`](../../requirements/guides/gui-006-a-review-is-not-answered-with-a-checklist-for-changing-something.md)
  holds. The half it had carried went nowhere.
- **Conformance already names who takes each finding onward, and that is not
  what is missing.** Read on 2026-08-04. The skill owns "assessment and
  prioritization, and saying who takes each finding onward". It names the
  workflow per finding whether or not the user asked for fixes, hands over for
  the changes and keeps itself responsible for the re-check. So the routing
  exists finding by finding. What no skill owns is the entry point for a request
  worded as a change, and the follow-through. One list across many findings, in
  an order, that survives across sessions.
- **What a session without a route does is on record twice, on other wordings.**
  `D-AUD-003`: a review prompt whose every criterion the conformance skill's
  body would have met did not activate it. All thirty-five calls of that session
  went through Bash. The `E-EXT` run of 2026-07-31 behind
  [`R-SKL-009`](../../requirements/task-skills/skl-009-a-release-answer-is-about-the-archive-a-registry-receives.md):
  forty-one `Bash` calls, no skill activated and no tool called.

## Decided

- **A skill, and this entry rejects the two alternatives.** The card put three
  candidates to the maintainer and ruled a skill out in advance. The person who
  queued it set that restriction aside on 2026-08-04. An intent has nothing to
  route to, because no published workflow owns "the whole repository". A wider
  `description` on another skill until the words fall into it is how the removed
  word got there in the first place.
- **Conformance is the precondition and stays analysis.** The skill does not
  re-derive the findings and does not give the audit a change step. It starts
  from the report `typo3-extension-conformance` produces, which `R-GUI-006`
  keeps free of a patch checklist.
- ~~**The session writes the worklist down and commits it before it works any of
  it.** What the audit found becomes an ordered list of its own. The commit that
  carries it is what a session interrupted halfway comes back to.~~ Reversed on
  2026-08-04, see **Since then**. The session writes it down and agrees it
  before it works any of it, and does not commit it.
- **What it adds is the entry point, the order and the follow-through.** Which
  workflow owns a finding is already conformance's answer and is not restated
  here; what this skill contributes is being reachable from a change-worded
  request, turning a report into a list somebody can work off, and staying with
  that list until it is empty.
- **Each item crosses into the skill that owns it** rather than stays here,
  which is
  [`R-SKL-003`](../../requirements/task-skills/skl-003-crossing-into-another-skills-work-is-an-explicit-transition.md).
- **The route is the second half of publication rather than part of the write.**
  `knowledge/task-intents.json` may name no skill before it is in
  `Installer::skills()`, which
  `SkillTest::everySkillNamedInKnowledgeIsPublished` holds — the shape
  `typo3-development-installation` has been in since
  `D-SKL-013`.

## Assumed

- **That the wording arrives at all.** No filed session has brought it, which is
  why the card is `normal`, and
  [writing-a-skill.md](../../documentation/contributing/writing-a-skill.rst)
  settles a domain with a scenario case or a recorded run rather than with a
  shape. Standing in for one is a hole this repository's own decision made: the
  change half of a route it removed.
- **That the conformance report carries enough to derive a list from.** Nothing
  measures whether what the audit returns has an order or is specific enough to
  become items somebody works off.
- ~~**That a commit of the list before the work is what the maintainer wants of
  it**, rather than one commit at the end. It is what the card asked for and no
  run has been through it.~~ Answered on 2026-08-04: it is not.

## Wrong if

- The draft turns out to re-derive what conformance already found. Then it is
  the audit with an edit step on the end, and the two are one skill rather than
  two.
- A session loads it and works the list and never activates the skills that own
  the items. Then it is a second copy of every workflow, which is what
  `R-SKL-003` exists against.
- The conformance report turns out not to carry findings a session can derive a
  list from. Then the precondition is a boundary rather than a hand-over, and
  what has to change is what the audit returns.
- The skill is out and no session loads it, because nobody words the request
  this way. Then the shape was hypothesised, and the bar `writing-a-skill.md`
  sets was the thing to wait for.

## Revoked on 2026-08-23

The first **Wrong if** happened and a session acted on it. The audit and the
work that answers it are one skill, published as `typo3-extension-health`. It
writes its own surface list and then works the agreed list off. That is what the
bullet said would settle it, and the word "conformance" appears nowhere in the
published workflow. `D-SKL-064` holds instead, and what this entry established
stands inside it — each item still crosses into the skill that owns it. The
strikethrough in **Decided** points at a **Since then** this entry never gained.
What it names is a worklist that stays in the session.
