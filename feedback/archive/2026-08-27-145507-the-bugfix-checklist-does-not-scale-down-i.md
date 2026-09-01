---
date: 2026-08-27T14:55:07+00:00
category: idea
status: closed
closed: 2026-09-01
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# The bugfix checklist does not scale down; I skipped the deprecation sweep it demanded

Trimmed on 2026-08-27. The release-branch half is answered: that item now names
`typo3_commit_message_guide`, which states the lines a change of this type takes
and what claiming an older one costs, and keeps the reading a one-branch
checkout cannot make — `D-GUI-023`. What is left is below.

## Suggestion

Condition the checklist on the shape of the diff, or say what the condition is so I can apply it.

For the deprecation sweep: "Sweep the deprecations before writing where the change introduces a call, a TCA key, a ViewHelper or a configuration option you have not used on v15 — a change confined to APIs already called in the file you are editing does not need it." That gives me a test I can run against my own diff in one thought, instead of an unconditional instruction I quietly drop.

More generally — typo3_task_guide takes `changeType` but nothing about magnitude. A one-method fix and a subsystem rework are both "bugfix" and get the same checklist. If the answer carried the checklist in two tiers, or if the items each said what makes them apply, I would follow more of it rather than picking.
