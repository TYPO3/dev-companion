---
date: 2026-09-01T21:06:02+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_hint_lookup, typo3-extension-testing, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# Test comments describe the case under test, never "regression test for #N"

## Observation

Migrating a local memory into the server: a standing correction from this user about test comments.

Do not frame a test comment as "Regression test for ..." — describe the case being tested instead.

Why: the git history already records that a test was added alongside a fix. Calling a test a regression test in its docblock is metadata that rots — years later the framing is noise, while the description of what the case actually exercises stays useful. Describing the case also reads the same whether the test was born out of a bugfix or out of ordinary coverage.

How to apply: in test comments — PHP docblocks, inline // above a test body, TypeScript test descriptions — lead with the scenario or code path being exercised and why that path matters. Not "Regression test for #12345" and not "Ensures bug X does not recur." This holds for all test types, not only the impexp tests where it first came up.

Note this shapes the test METHOD NAME too, not just the comment: a name saying what holds (subSchemaLabelIsUsedWhenTypeValueIsSet) survives, one naming an issue number does not.

## Query

Migrated from a local project memory file (feedback_no_regression_framing.md). A standing correction from this user on how tests are commented.

## Suggestion

Add to the testing hints beside the naming conventions, since the comment and the method name are one decision. A core patch's tests are read by reviewers who do not have the issue open, which is the argument that makes this more than style. Worth stating that the issue number belongs in the commit message's Resolves: trailer and in the changelog file name, which is precisely why it does not need to be in the test.
