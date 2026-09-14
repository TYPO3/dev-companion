---
id: R-GUI-011
title: 'A readiness answer names the classification it was not given'
status: held
restsOn: [D-GUI-001]
heldBy:
  - CommitMessageGuideTest::aCheckedMessageSaysTheClassificationWasAssumed
  - CommitMessageTest::aClassificationNobodyGaveIsNamedInTheChecks
  - CommitMessageTest::aClassificationTheCallerGaveIsNotAskedAboutAgain
---

# R-GUI-011 — A readiness answer names the classification it was not given

**Where `typo3_commit_message_guide` checks a core message with no `[!!!]` and
no `isBreaking`, the checks say the classification is an assumption, not a
check.** `isDeprecation` is the same field and owes the same sentence.

The guide cannot derive either one. Both are inputs and the tool never sees the
diff. That limit is not the defect. The defect is that the answer does not state
it. So a caller who has not yet classified the change reads a scoped result as a
clearance. That caller is the one most likely to ask.

A subject that already carries `[!!!]` needs nothing said. The caller has
answered, and the changelog and release-target checks fire on it today. So has a
caller who passed `isBreaking` themselves, whichever value. That is why the
field goes through as `null` where nobody supplied it rather than as `false`. It
is why the input schema no longer declares `false` as its default.

## From

A core patch review of `9f6c6eb9093` (#110359), which passed the whole message
with no `isBreaking` and got `no-issues-found` back. The patch removed a
protected method from a class that is neither `final` nor `@internal`
(`feedback/2026-08-03-144432`, 2026-08-03). The session behind `R-GUI-007`
reported the same clearance beside an unready message the day before
(`feedback/2026-08-02-144315`, 2026-08-02).
