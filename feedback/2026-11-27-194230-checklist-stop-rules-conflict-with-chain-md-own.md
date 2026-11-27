---
date: 2026-11-27T19:42:30+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3-core-patch-checkout
directory: /home/benji/skill13-core
---

# checklist "stop" rules conflict with chain.md "own change may resolve more" for a single own change

## Observation

Task: cherry-pick Gerrit change 87873 onto current main to run tests. The requester owns the change. checklist.md says stop at generated output, at more than a handful of hunks, and where the branch fixed the same thing differently. chain.md says that on the requester's own links you may resolve more than a handful of hunks. That rule sits only in the chain file. It is unclear whether it holds for a single own change. I stopped and asked, because two conflicts were real design choices. The viewpage dropdown and the live-search counter were these choices. The guidance did not say whether the generated JS conflicts may be rebuilt without asking on an own change.

## Query

Skill typo3-core-patch-checkout, references/checklist.md and references/chain.md, change 87873 owned by the requester

## Suggestion

Move the own-change rule into checklist.md so that it holds for one change and for a chain. State per stop rule whether ownership lifts it. Example: generated output on an own change is rebuilt with the build suite. A design conflict still goes back to the owner.
