---
id: R-ANS-031
title: 'A symptom reaches the hint that explains it'
status: held
restsOn: [D-ANS-084]
heldBy:
  - HintsTest::aQuotedErrorMessageCrossesTheGateTheWayAPlainPhraseDoes
  - HintsTest::aSymptomReachesTheHintThatExplainsItFromAnotherDomain
  - HintsTest::aTypeScriptTestPathIsNotAnsweredWithPhpunit
  - HintsTest::theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay
---

# R-ANS-031 — A symptom reaches the hint that explains it

**A hint outside the domains a query selects answers it where the query spells
out a phrase in that hint's index. No selected hint may claim that phrase.**

The domain gate asks the query where the work belongs. That is the same question
a task description answers and the opposite of what a symptom says. The layer a
failure shows in is rarely the layer that caused it. A curated phrase is what
somebody wrote down for the query that should reach the hint, so it crosses. It
crosses only where the layers the query did name carry no claim to it, because
then those layers cannot answer it themselves.

## From

`feedback/2026-08-17-212010`, a session that spent nine debugging cycles and
roughly 45 round trips without a question to this server. A symptom is not a
subject and the index goes by subject. `D-ANS-081` carries the measurement. Over
the sweep, every hint title and every scenario prompt, the rule crosses the gate
twice and both crossings answer. The two wider rules it stood against cross 9
and 39 times and displace answers.

## Held by

- `HintsTest::aTypeScriptTestPathIsNotAnsweredWithPhpunit`, the other half: a
  phrase the selected layers claim themselves does not cross
- `HintsTest::theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay`, that the
  wider gate did not widen into an answer to everything
