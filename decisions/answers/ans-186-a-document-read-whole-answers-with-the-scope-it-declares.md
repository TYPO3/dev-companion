---
id: D-ANS-186
title: A document read whole answers with the scope it declares
date: 2026-10-26
status: open
coveredBy:
  - ToolContractTest::everyDocumentReadByItsIdKeepsTheOutputSchema
---

# D-ANS-186 — A document read whole answers with the scope it declares

**`typo3_rule_lookup` with a `documentId` answers with the scope the document
declares, `any` included, and its output schema allows that value.**

## Evidence

- [`feedback/2026-10-26-130934-rule-lookup`](../../feedback/archive/2026-10-26-130934-rule-lookup-by-documentid-fails-output.md).
  A run of `SITE-10` with `typo3-sitepackage-development` read
  `any/testing/browser-check` at the step the skill names. The client rejected
  the answer: `scope` was `any`, and the schema allowed only the four scopes a
  path can fall in.
- The answer for an unknown id carried `any` as well. So every `any/` page and
  every miss broke the schema for a client that validates it. The contract test
  drove the tool with a query alone and never read a page whole.

## Decided

- **Widen the schema rather than map the value.** `any` is what the document
  declares, and an answer that renamed it to a path scope would claim a
  placement nothing made.
- **A contract test reads every document by its id** and validates each answer.

## Wrong if

- A client reads `any` as a fifth kind of work and routes on it as if a call had
  placed it.
