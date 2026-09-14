---
id: D-SKL-078
title: The triage intent matches taking an issue off the tracker
date: 2026-08-25
status: open
coveredBy:
  - SkillTest::takingAnIssueOffTheTrackerReachesTheTriageSkill
---

# D-SKL-078 — The triage intent matches taking an issue off the tracker

**`triage` takes "from forge" and "off forge" as strong needles rather than the
bare name. A brief that names one issue somebody already chose is patch work.**

`forge` was a weak needle, and a weak match names no skill. So the brief the
feedback asked for a re-run of, "fetch another old issue from Forge, create a
branch, work it off", got `skills: []`.

## Evidence

- The other gate failed first. `Scope::isCoreWork()` matched each of its markers
  with `str_contains`, which made every marker carry its own boundary. The
  tracker stood as `'forge '`, and the space at the end that keeps "forget" out
  is where the comma of "from Forge," sits. Read with `Text::containsWord()`,
  which the intent matcher beside it already uses, the brief reads as `core`. "I
  forgot to run the tests before pushing" still does not.
- Measured on 2026-08-25 over twenty-one briefs, with both options applied to
  the same list. Bare `forge` in `match` routed all six briefs that take work
  off the tracker, and five more with it. "report a bug on Forge", "file an
  issue in Forge" and "write a new forge issue" got `typo3-core-issue-triage`.
  Those are `reporting`'s job, which no published skill owns. "fix Forge 15984
  in the FormEngine" and "implement what Forge 98765 asks for" came out as
  briefs that change nothing. They lacked the deprecation sweep, the test
  coverage and the commit message step.
- "from forge" and "off forge" moved the six and nothing else on that list. The
  three briefs that file kept `reporting` alone. The two patch briefs kept the
  checklist a patch owes while `forge` still matched them weakly. That is the
  "Possibly also" line and the conditional items.

## Decided

- The needles name the act rather than the tracker. A contributor writes "from
  Forge" and "off Forge" to take one, and "on Forge" and "in Forge" to file one.
  So the preposition is what separates the two jobs in the words people use.
- `forge` stays in `matchWeak`. A brief that names the tracker and nothing else
  may be a triage. A conditional statement of that is what a weak match is for.
- `Scope::isCoreWork()` reads every marker as the word it is, not the tracker's
  alone. A boundary in one needle is one the next needle will lack.

## Assumed

- The preposition holds outside this list. It comes off twenty-one briefs
  written for the measurement and off the one the feedback carried. It does not
  come off a corpus of real requests, which this repository does not have.
- A brief that files, routed to the triage skill, is a cost rather than a bonus.
  The triage workflow is about the backlog, and nobody has reported that they
  reached it from a report they wrote.

## Wrong if

- A session asks for a triage in words that name the tracker and take nothing
  off it, and gets no skill. "look at Forge 15984 and tell me whether it still
  stands" is such a brief. That would say the direction is the wrong
  discriminator and the vocabulary needs the number form as well.
- A brief with "from forge" turns out to be ordinary patch work often enough for
  a report. That is a read of an attachment, a quote of the reproduction steps.
- `forge.typo3.org` keeps routing briefs that file to the triage skill and
  somebody reports that. It is the same collision this entry declined to widen,
  and this entry leaves it. "open an issue on forge.typo3.org for this crash"
  answers `typo3-core-issue-triage` before this change and after it.
