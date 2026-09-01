---
date: 2026-09-01T21:04:51+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# Commit messages: prose not bullet lists, no Signed-off-by, and never run git commit

## Observation

Trimmed on 2026-09-02, on the maintainer's answer of the same day. Two of the
three are settled.

The sign-off memory is older than the rule it contradicts. The maintainer
reversed that rule on 2026-08-25 on the TYPO3 Association board's statement on
GPL and AI-generated code, and `core/contribution/commit-messages` has said
since then that every core patch carries `Signed-off-by:` — with `git commit -s`
and the certificate the line stands for. `R-KNW-075` reserves that change for
the maintainer, who confirmed on 2026-09-01 that the rule stands. Nothing is
owed here beyond not judging the next copy of this memory again.

Never executing `git commit` is the client's to enforce and not this server's:
nothing it answers with runs a command in the caller's checkout.

What is left is the body. The maintainer ruled the prose form a core rule, and
`typo3_commit_message_guide` neither states nor checks it — a bulleted body
passes today.
