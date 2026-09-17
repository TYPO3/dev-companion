---
id: D-ANS-160
title: A nullable field is two anyOf branches
date: 2026-09-17
status: open
coveredBy:
  - ToolContractTest::noSchemaDeclaresTypeAsAList
---

# D-ANS-160 — A nullable field is two anyOf branches

**A schema spells a field that may be null as `anyOf` of its type and `null`,
and never as a `type` list.**

Both are legal JSON Schema. Several MCP clients read `type` as one string, and
on a list they refuse the tool or drop the constraint. So a tool that validated
here failed there, and nothing here said so.

## Evidence

- **A client's schema check over the server on 2026-09-17.** It warned on the
  four nullable fields of `typo3_icon_lookup`: `icons[].aliasOf`,
  `validated[].aliasOf`, `unsupported.repositoryState` and
  `unsupported.misconfiguration`. Each was `["string", "null"]`.
- **The same shape stood in 76 places.** 71 of them were a type and `null`,
  across 18 tool classes and `Result\Schema`. The last was
  `typo3_record_lookup`'s `where`, whose values are a string, a number or a
  boolean. `Schema::nullableString()` wrote the list too.
- **The SDK reads `anyOf`.** `mcp/sdk` validates with Opis, and
  `typo3_commit_message_guide` has declared an `anyOf` on its input since
  `D-ANS-017`.

## Decided

- `Schema::nullable()` is the one way to write a nullable field. It takes the
  schema and returns `anyOf` of that schema and `{"type": "null"}`. The
  description moves beside the `anyOf`, where it describes both branches.
- An enum inside the branch drops its `null`. The `null` branch carries it, and
  a string branch whose enum names `null` says one thing twice.
- `ToolSurface` reads a field through its branch. The reference still prints
  `string or null`, and a nullable list now unfolds its entries where the type
  list had hidden them.
- The `where` values of `typo3_record_lookup` are three `anyOf` branches. That
  is the same rule where no branch is `null`.
- `ToolContractTest::noSchemaDeclaresTypeAsAList` walks both schemas of every
  tool. A list anywhere in them fails the suite by its path.

## Assumed

- That a client which reads `type` as one string reads `anyOf`. The warning
  proposed that form, and the specification's own examples use it.

## Wrong if

- A client refuses a tool over an `anyOf` in an output schema, or reads the
  field as required-and-typed where it is null. Then that client reads neither
  form, and the answer is to make the field optional, which is the other
  contract.
- The same check warns again on an `anyOf`, and the wrapper only moved the
  warning.
