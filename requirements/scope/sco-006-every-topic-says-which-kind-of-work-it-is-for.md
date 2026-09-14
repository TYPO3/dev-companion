---
id: R-SCO-006
title: 'Every topic says which kind of work it is for'
status: held
restsOn: [D-KNW-005]
heldBy:
  - KnowledgeTest::everyScopeInTheCorpusIsOneTheEnumDeclares
  - ScopeTest::everyCoveredTopicSaysWhatItIsWorthOutsideTheCore
---

# R-SCO-006 — Every topic says which kind of work it is for

**Every covered topic states which kind of work its answers are for, as a
`Scope`: `core` or `any`.**

The boundary runs through the middle of this server rather than around it. A
caller that has to work that out per tool ends up with trust in all of it or
none of it.

Where an answer comes from is a different question and `source` already answers
it. So `installation` stopped as a value of this field on 2026-08-02
([`D-KNW-005`](../../decisions/knowledge/knw-005-scope-is-the-one-word-for-which-work-a-statement-is-for.md)).

## From

A site developer for whom five installation-backed tools answered correctly
while the curated half handed over runTests.sh commands. Nothing in the scope
separated the two (2026-07-29).
