---
date: 2026-10-05T09:42:10+00:00
category: wrong-answer
status: closed
closed: 2026-10-05
model: claude-opus-5-5
tool: typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# sign-off rate on main is about 29 percent, not one commit in a hundred

## Observation

Task: decide how hard to rate a missing Signed-off-by on Gerrit change 96353.
Section "The Trailers A Core Commit Carries" in core/contribution/commit-messages says: the merged history carries the sign-off on about one commit in a hundred on main.
I counted in the checkout: git log origin/main --since=2026-06-01 gives 786 commits, and 225 bodies have a Signed-off-by line.
That is about 29 percent.
AGENTS.md added the sign-off rule on 2026-07-28 (commit 781c8525870). The rate probably rose after that.
The section gives a measurement command with -500 but no date. The number is stale.
The ranking of my finding depended on this number. A reviewer who reads "1 in 100" rates a missing sign-off as normal practice.

## Query

typo3_rule_lookup query="AI assisted contribution"

## Suggestion

Give the date of the measurement and the window. Or state the trend: rare before AGENTS.md (2026-07-28), common since. Better: drop the number and keep the command, so the reader measures it.
