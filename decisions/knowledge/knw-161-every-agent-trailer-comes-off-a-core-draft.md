---
id: D-KNW-161
title: Every agent trailer comes off a core draft
date: 2026-10-05
status: open
coveredBy:
  - CommitMessageTest::aCoreDraftRefusesTheTrailersTheProjectDoesNotSet
---

# D-KNW-161 — Every agent trailer comes off a core draft

**`typo3_commit_message_guide` with `workflow="core"` refuses `Assisted-by:` and
`Generated-by:` beside `Co-Authored-By:` and the session trailer. Each is an
`error` and comes off the draft. The rule binds the agent strictly, while the
merged history does not keep it.**

## Evidence

- `feedback/archive/2026-10-05-094210-core-message-check-keeps-an-assisted-by-agent.md`.
  A review of change 96353 passed a message with
  `Assisted-by: Claude Code (Claude Opus 5.5) <noreply@anthropic.com>`. The
  draft kept the line and no check named it. That is the first **Wrong if** of
  `D-KNW-110`: a trailer under a name the list did not carry.
- Measured in `.checkouts/main` at `098b878f8f` on 2026-10-05, since 2025-01-01.
  No merged commit carries `Assisted-by:` or `Generated-by:`. Six carry
  `Co-authored-by:`, three of them since 2026-07-28. The merged history also
  carries `Reverts:`, `Security-References:` and `Security-Bulletin:`, and
  Gerrit adds `Reviewed-by:`, `Tested-by:` and `Reviewed-on:` at the merge.
- The maintainer decided on 2026-10-05 that an `Assisted-by:` line comes off.
  Other people do not follow the rule, and the agent follows it strictly.

## Decided

- The refusal stays a list of names. A list of the five a draft may keep would
  refuse `Reverts:` and the security trailers, which merged patches carry.
- The check message says the rule and that merged commits break it now and then.
  A session that reads the log does not take that as a precedent.
- The rule document says the same and tells a reviewer to name such a trailer in
  somebody else's patch as a finding.

## Assumed

- That an agent attribution arrives under one of the four names. A fifth name
  passes the check, and only the document says it does not belong.

## Wrong if

- A core patch carries an agent trailer under a fifth name, and the check keeps
  it. Then the rule belongs on what the value names rather than on the name.
- A reviewer strikes a merged-practice trailer that this list refuses, such as a
  human `Co-authored-by:`, and the maintainer keeps it. Then the strict rule
  reaches further than the maintainer wants.
