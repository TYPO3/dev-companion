---
id: D-ANS-174
title: A branch of an input anyOf defines what it requires
date: 2026-10-06
status: open
coveredBy:
  - ToolContractTest::everyBranchOfAnInputAnyOfDefinesWhatItRequires
---

# D-ANS-174 — A branch of an input anyOf defines what it requires

**Every branch of a root `anyOf` in an input schema is `type: object` and
defines each property its `required` names. `Schema::branch()` writes it with
the type the root declares, and the description stays at the root.**

`typo3_commit_message_guide` and `typo3_issue_report_guide` declared branches
with a `title` and `required` alone. A Gemini session reported that the commit
message guide published no parameter names.

## Evidence

- `feedback/archive/2026-10-06-164432-typo3-commit-message-guide-lacks-clear.md`,
  from Gemini 3.8 Flash in a core checkout, the day `D-DIS-032` installed the
  server into Antigravity. The session guessed `changeType` and `commitMessage`
  and found the real names by trial and error.
- The same session called `typo3_forge_lookup` with `issue` and
  `typo3_gerrit_lookup` with `change`. Both tools declare a root `oneOf`, so the
  names of those arguments did reach it.
- The two guides are the only tools with a root `anyOf`, and the commit message
  guide is the one the session reported.
- Gemini validates a function declaration against its own schema. That
  validation refuses an `anyOf` branch that carries `required` without
  `type: object`. It also refuses a branch whose `required` names a property the
  branch does not define. The
  [home-generative-agent v3.26.1 release note](https://newreleases.io/project/github/goruck/home-generative-agent/release/v3.26.1)
  reports both, for a schema of the same shape.

## Decided

- The maintainer chose the typed branches on 2026-10-06. The two other options
  were to drop the root `anyOf` and leave the rule to each tool's own refusal,
  and to wait for a second report from Gemini or Antigravity.
- A branch property carries its type alone. The full schema, its description
  included, stands once in the root `properties`, so the listing does not carry
  each description twice.
- The `oneOf` of the four lookups stays as `D-ANS-012` left it. Their names
  reached the session.

## Assumed

- Antigravity hands Gemini an input `anyOf` as it is, rather than drops the
  tool's parameters for a reason of its own. Nobody here has run Antigravity
  against either shape.
- Antigravity drops the `oneOf`, which Gemini does not know, and keeps the
  properties beside it. The session's correct calls are the only evidence for
  that.

## Wrong if

- A Gemini or Antigravity session still reports that either guide publishes no
  parameter names. Then the root `anyOf` itself is the obstacle, and it goes.
- A client refuses a tool over a typed branch. Then the type that Gemini needs
  costs another client, and the root `anyOf` goes for both.
