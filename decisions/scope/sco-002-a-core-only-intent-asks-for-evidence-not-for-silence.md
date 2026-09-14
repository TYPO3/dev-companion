---
id: D-SCO-002
title: A core-only intent asks for evidence, not for silence
date: 2026-07-29
status: confirmed
coveredBy:
  - ScopeTest::aCorePathStillMakesTheSameWordAPatchSubmission
  - ScopeTest::aCoreTaskNamingNoPathKeepsTheSubmissionRules
  - ScopeTest::inASitePackageThePatchSubmissionIntentIsNotOfferedAtAll
  - ScopeTest::maintainingAnExtensionIsNotSubmittingAPatchToTheCore
  - ScopeTest::theBriefNamesWhatWouldTurnTheConditionIntoFact
---

# D-SCO-002 — A core-only intent asks for evidence, not for silence

**A core-only intent needs positive evidence of core work. Where nothing says
either way, the brief demotes it to a conditional match rather than drops or
states it.**

`outsideCore` was to be the gate for the patch submission intent. It is not
enough. The reported task reads "Maintain and extend the third-party TYPO3
extension bk2k/bootstrap-package … review TCA …". It trips no outside-core
marker, because "third-party TYPO3 extension" is not the phrase the list
carries. Gating on the flag alone would have left the feedback's own case
answered exactly as before.

## Decided

- A core-only intent needs positive evidence — a `typo3/sysext/` path, or
  Gerrit, Forge, "TYPO3 core" named outright. Outside the core the brief drops
  it. Where nothing says either way, the brief demotes it to the conditional
  match the catalog already models. So the answer offers it rather than states
  it.

## Assumed

- `coreOnly` is a property of the intent, not of the task, and patch submission
  is currently the only one. The session weighed deprecation, breaking change
  and changelog and left them alone. Their subject is real work outside the core
  too, and only their `checks` are core-only, which R-SCO-002 handles.

## Wrong if

- A core contributor's task text names neither a sysext path nor Gerrit and they
  now get the submission rules as conditional rather than as fact. That is the
  cost of a brief that does not guess, and the condition line keeps it cheap.

## Confirmed on 2026-08-02

The **Wrong if** happened as written and costs what the entry said it would.
`CORE-07` is the case. Its prompt «The page tree loses drag and drop as soon as
a mount point is set. Fix that, then take me through pushing it for review.»
names neither a `typo3/sysext/` path nor Gerrit. The brief answers "Possibly
also: Patch submission, if the patch goes to the TYPO3 core itself". That is one
line, one condition, and the submission steps under it rather than dropped. That
is the demotion this entry decided, met in the one text the entry predicted. It
stays cheap because the condition is a sentence the contributor settles from
their own intent. A sysext path named in the same session turns it into a stated
match. `ScopeTest::aCoreTaskNamingNoPathKeepsTheSubmissionRules` and
`ScopeTest::aCorePathStillMakesTheSameWordAPatchSubmission` hold both halves.
