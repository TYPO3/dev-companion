---
date: 2026-09-01T21:04:51+00:00
category: idea
status: closed
closed: 2026-09-01
model: claude-opus-5
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# Commit messages: prose not bullet lists, no Signed-off-by, and never run git commit

## Observation

Answered on 2026-09-02, all three parts.

The body is prose. `typo3_commit_message_guide` reports
`body-written-as-a-list` at warning level where the body carries its argument as
bullets, and passes a list that enumerates what the change touched — the
threshold is the item long enough to be a sentence, calibrated so that none of
the last thousand merged core bodies fires. `core/contribution/commit-messages`
states it beside the blank line and the width. `D-GUI-026` carries both.

The sign-off memory is older than the rule it contradicts. The maintainer
reversed that rule on 2026-08-25 on the board's statement and confirmed on
2026-09-01 that it stands, so every core patch carries the trailer —
`R-KNW-075`, which reserves that change for them.

Never executing `git commit` is the client's to enforce. Nothing this server
answers with runs a command in the caller's checkout.
