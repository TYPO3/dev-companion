---
id: R-KNW-023
title: 'No prose document dates a statement in its sentence'
status: held
heldBy:
  - KnowledgeTest::noProseDocumentDatesAStatementInItsSentence
---

# R-KNW-023 — No prose document dates a statement in its sentence

**The same rule holds the prose documents as the hints.**

No filter can read a statement dated in its sentence, and in markdown there is
no field to move the date into.

So the subject moves to the hint corpus rather than the sentence to other words.
A version-bound statement is evidence that it sits in the wrong corpus. This is
the version bound applied to the half of `knowledge/` the bound cannot reach. It
is what decides what may stay as prose at all.

## From

«Since TYPO3 v14.1 a label marked that way raises an `E_USER_DEPRECATED`» in
`core/contribution/rules.md`, handed unqualified to a caller on 13.4 by
`typo3_rule_lookup`. That tool has no `targetVersion` and searches every
document (2026-07-30).
