---
date: 2026-11-27T19:42:20+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3_gerrit_lookup, typo3_rule_lookup
directory: /home/benji/skill13-core
---

# gerrit_lookup mergeable and fetch.ref prevented a wrong fetch and predicted the conflict

## Observation

Task: cherry-pick Gerrit change 87873 onto current main to run tests. One gerrit_lookup call gave the target branch, patch set 13, the commit, fetch.ref and fetch.remote. The origin remote of this clone is the GitHub mirror. It has no refs/changes. The fetch.remote field sent the fetch to review.typo3.org on the first try. The mergeable=false field predicted the conflict before the fetch. The chain field showed that change 91093 sits above this change at patch set 4. The owner email showed that the change belongs to the user. The gerrit-workflow page gave the review/<number> branch pattern and the delete command. Keep these fields and this page as they are.

## Query

typo3_gerrit_lookup change="87873"; typo3_rule_lookup documentId="core/contribution/gerrit-workflow"

## Suggestion

Keep fetch.remote, mergeable, chain with chainedAt, and owner email in the change answer. Do not move them behind an option.
