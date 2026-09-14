---
id: D-GUI-011
title: Reviewing a report against code is a change type of its own
date: 2026-08-08
status: open
coveredBy:
  - HintsTest::aTriageIsAnsweredWithWhatDecidingAReportNeeds
---

# D-GUI-011 — Reviewing a report against code is a change type of its own

**`typo3_task_guide` has a `changeType` for a decision on an open bug report.**
`audit` means a review of a body of code. A session with no other value for work
that writes no file picked it and got a review of a diff.

## Evidence

- `feedback/2026-08-07-233443`. A core triage passed `changeType: "audit"` and
  says why. It "is documented as writing no file and getting what a review needs
  — which is the closest of the available values". What came back was the audit
  intent's checklist, which is a review of a diff. "enumerate what it removes or
  renames before judging it", a matcher below
  `typo3/sysext/install/Configuration/ExtensionScanner/Php/`, the `[!!!]`
  prefix, `checkRst` and `checkExtensionScannerRst` over a core diff. The
  session used none of them and says so.
- A triage produces no diff. It removes nothing, renames nothing and writes no
  changelog file, so three of `audit`'s six items cannot apply to it at all.
- The session names the distinction itself: "audit appears to mean reviewing a
  body of code, whereas a triage reviews a report against code. Those need
  different briefs, and there is no changeType that says so."
- Measured on 2026-08-08 with `changeType: "unknown"` on the same text: the
  brief was the patch skeleton. "Keep the patch focused on the stated task",
  "Add or update the narrowest useful test coverage", and the commit-message
  step. So the honest value was the patch shape and the closest value was the
  review shape, and neither is a triage.

## Decided

- **A third value on the enum, and the skeleton forks on it**, which is what
  `D-GUI-006` established and `D-GUI-008` did for a boot. `audit` keeps its
  meaning and does not branch. A review of a diff and a review of a report are
  two kinds of work, not one kind under a condition.
- **The word is `triage`**, and it is the id of the intent as well, the way
  `audit` is. It has to be safe in the matcher because the change type goes to
  `TaskIntents::detect()`. Measured against every needle in the corpus, `triage`
  matches the triage intent and nothing else.
- **What withholds the diff items is that the two vocabularies do not overlap.**
  It is not a rule about which items a task with no diff may see. A caller who
  states `triage` reaches no needle of `audit`, so the removal, the matcher and
  the `checkRst` items do not come back at all. No mechanism, one fewer branch.
- **A triage wins the skeleton where both stand confirmed.** "Review this old
  bug report" is the call this entry came from, and the review arm is the answer
  that was wrong.
- **The arm carries only what the intent does not**, which is the same
  arrangement the other two have. The triage intent holds what a triage owes.
  The skeleton holds the premise, the read of nearby tests, and what the triage
  did not reach.
- **Reaching it took a second change, and it is `D-SKL-023`'s.** A core-scoped
  intent drops to weak where nothing says the work is the core's. Neither
  "Triage an old open core bug report" nor "Forge 15984" said so.
  `Scope::isCoreWork` reads the core as a tracker and a checkout now, `forge `
  included.

## Assumed

- A caller who classifies work as `triage` does not also write a patch in the
  same call. That is `D-GUI-006`'s assumption for `audit` and
  `todo/waiting/2026-08-01-115711` is it under question. `D-GUI-009` is what
  keeps a stated patch type from the loss of its skeleton either way.
- The three items dropped from `audit`'s six are the diff ones. Read off the
  corpus entry rather than from a run that returned them beside a triage.
- One session. It reported the shape and named the distinction, and no second
  reader has said `triage` is the word for it.

## Wrong if

- A session states `triage` about work that does end in a patch, "triage this
  and fix it" is the form, and loses the patch steps. The answer is then a
  condition on the value rather than the end of the fork, which is what
  `D-GUI-006`'s **Wrong if** already says.
- A report says a triage still gets review content. That would say the `audit`
  needles reach a report after all and the two vocabularies do overlap.
- A report says `forge ` reached a task that is not about the tracker. That
  would say a marker matched on a word rather than on a subject.
