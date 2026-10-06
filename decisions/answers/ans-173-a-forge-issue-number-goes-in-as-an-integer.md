---
id: D-ANS-173
title: A Forge issue number goes in as an integer
date: 2026-10-06
status: open
coveredBy:
  - ToolContractTest::aForgeIssueNumberIsAnInteger
---

# D-ANS-173 — A Forge issue number goes in as an integer

**`typo3_forge_lookup` and `typo3_gerrit_lookup` declare `issue` as an integer
from 1. `typo3_issue_report_guide` already declared `parentIssue` that way, so
every argument that takes a Forge issue number has one type.**

Until 2026-10-06 the two lookups declared `issue` as a string, with or without
the `#` in front. A session sent the number as an integer, and the server
refused the call.

## Evidence

- `feedback/archive/2026-10-06-164424-typo3-forge-lookup-rejects-integer-issue-number.md`,
  from Gemini 3.8 Flash in a core checkout. The session reviewed change 93806
  and sent `{"issue": 109692}`.
- Re-run on 2026-10-06 over stdio. The server answered `-32602`,
  `Property '/issue': Invalid type. Expected string, but received integer.`
  The same issue as `"109692"` answered whole.
- The same session sent `change` to `typo3_gerrit_lookup` as `"93806"`, and that
  call answered. `change` stays a string, because a Change-Id is one of its two
  forms.
- Every answer that names a Forge issue already carries it as an integer: the
  rows of `typo3_forge_lookup` and the changes on review.
- `D-ANS-017` rules out the union of the two types, and
  `ToolContractTest::noArgumentDeclaresMoreThanOneType` holds that.

## Decided

- The maintainer chose the integer on 2026-10-06. The two other options were to
  keep the string, with the rejection as the correction, and to wait for a
  second model that sends an integer.
- The `#` form goes with the string. `Forge::issue()` and
  `Gerrit::changesForIssue()` take the integer and strip nothing.
- `typo3_commit_message_guide` keeps `issue` as a string. With
  `workflow="project"` it names an issue in the caller's own tracker, and that
  id is not always a number.

## Assumed

- A client that reads the declared type sends an integer for it. The session
  that reported this did so against a schema that declared the other type.
- No caller depends on the `#` form. Nothing in `knowledge/` or `skills/` calls
  either tool with it.

## Wrong if

- A session reports that the server refused `"110348"` or `"#110348"` for
  `issue`. Then clients follow the commit message form more than the declared
  type, and the string comes back.
- A model that sent an integer against the string still sends a string against
  the integer. Then the type a schema declares does not steer that model, and
  neither form helps it.
