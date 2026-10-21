---
date: 2026-10-16T07:47:33+00:00
category: tool-gap
status: closed
closed: 2026-10-21
model: claude-opus-5-5
tool: typo3_gerrit_lookup, typo3-core-patch-checkout
directory: /home/benji/projects/typo3-cms
---

# a change read by name carries no owner, but the checkout skill decides on the owner field

## Observation

Task: rebase the 13-change chain 95425..95437 onto main and rebuild the build output in each commit.
The skill typo3-core-patch-checkout says: "The answer's owner field settles whether the change owner is the person who asks."
The answer for change 95437 has no owner field. The keys are number, message, changeId, subject, status, branch, patchSet, commit, labels, chain and others. No key names the owner.
The only owner hint is the Signed-off-by line in the message body. That line names the committer, not the change owner.
I got the owner from the raw REST API: GET /accounts/21249 after GET /changes/95437. That cost two extra calls outside the server.
The decision mattered. The owner was the requester, so the skill allowed me to resolve past a handful of hunks.

## Query

typo3_gerrit_lookup change="95437" files="stat"

## Suggestion

Add an owner object to each change read by name: name, username, e-mail, account id. Add the owner to each chain link too. A chain can mix owners, and the skill rule applies per link. Optionally compare the owner to git config user.email and say "owner is the local git identity".
