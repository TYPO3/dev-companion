---
id: R-ANS-032
title: 'The instructions index the question each tool answers'
status: held
restsOn: [D-AUD-011]
heldBy:
  - ScopeTest::theInstructionsIndexTheQuestionEachToolAnswers
  - ScopeTest::theScopeInstructionsOrientTheClientBeforeItsFirstCall
---

# R-ANS-032 — The instructions index the question each tool answers

**The statement sent at initialize names, per question a session has, the tool
that answers it.**

A tool description is not a channel under a client that defers schemas. The
names arrive and nothing else, so the choice rests on a name or not at all. The
`routing` block says which question goes to which tool and sits behind
`typo3_server_scope`. That is a call, and a session that calls nothing never
makes it. The index is what survives. This holds it here because the budget
[`R-ANS-013`](ans-013-the-instructions-fit-what-a-client-keeps.md) fixes makes
every line somebody else's to displace.

## From

Two sessions of 2026-08-18, in `bootstrap_package` and in `blog`, that called
this server nothing at all under such a client.

## Held by

- `ScopeTest::theScopeInstructionsOrientTheClientBeforeItsFirstCall` for the
  three whose value exists only before the mistake:
  [`R-ANS-009`](ans-009-the-instructions-say-when-to-call-the-lookups-that-come-first.md)
