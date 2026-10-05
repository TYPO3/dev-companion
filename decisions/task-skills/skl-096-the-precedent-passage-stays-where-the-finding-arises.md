---
id: D-SKL-096
title: The precedent passage stays where the finding arises
date: 2026-10-05
status: open
---

# D-SKL-096 — The precedent passage stays where the finding arises

**`typo3-core-patch-review` keeps its guidance on finding a changelog precedent
in the skill, beside the obligation it serves, rather than in a reference.**

## Evidence

- `feedback/archive/2026-10-05-105018-review-skill-requires-the-checkout-skill-but-a.md`.
  A review of a `[TASK]` patch found the passage cost reading time and gave
  nothing, and proposed a reference file with one line in the skill.
- The passage came from two reviews that lost their precedent to a query and
  found it by hand afterwards, which the skill itself says.
- The maintainer chose on 2026-10-05 to keep it in the skill.

## Decided

- The passage stays. It is conditional on a finding that an earlier change
  settles, and a reference read at that moment is one more step a session skips.
- The worktree half of the same feedback is `D-SKL-095`.

## Wrong if

- A second review reports the passage as a cost on a patch that owed no entry.
- A session that owed a precedent reads the passage and still misses it.
