---
date: 2026-09-09T18:26:35+00:00
category: idea
status: closed
closed: 2026-09-09
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# What worked: chain, mergeable and fetch.ref on a second change stopped a wrong rebase

## Observation

Task: review and finish TYPO3 core Gerrit change 93620, taking the prework in change 95425 into account. Filing this because what worked has to survive the changes the other feedbacks ask for.

typo3_gerrit_lookup on 95425 returned three fields that changed what I did, inside a single call:

1. chain - 14 entries. The user described 95425 as "similar prework". The chain showed it was not a standalone patch but the base of a 14-change strictNullChecks series (95424 below it, 95426 through 95437 above). Without that I would have read it as a peer of 93620 and might well have proposed rebasing 93620 onto it. That would have been wrong: 93620 sits on current main and merges, 95425 does not.

2. mergeable: false on 95425, mergeable: true on 93620, plus commit and parent shas. I checked the parents with git merge-base and confirmed 93620's base was the current main HEAD, which is why I could reset --hard onto the fetched ref instead of cherry-picking and resolving. That was one command instead of a conflict resolution I had budgeted for.

3. The full commit message of 95425. It described its accessor as "@typo3/core/utility/top-window" with a TopWindow type. 93620 had added "@typo3/core/utility/top-level-window" with a different signature and opposite semantics - one throws where the other falls back. Two competing accessors in two open chains by the same author is the finding the whole review turned on, and I had it from the message text before I read a single line of either diff.

fetch.ref (refs/changes/20/93620/2 and refs/changes/25/95425/1) was correct in both answers and I fetched with it directly - no guessing at the refs/changes/NN/NNNNN/P sharding.

The messages field with messages="people" was also worth its cost: it showed "Uploaded patch set 2. Outdated Votes: * Verified+1 (copy condition: changekind:NO_CHANGE)", which explained why a change with a CI +1 in labels was showing as needing Verified. I would have misread the label state otherwise.

If the file list is trimmed as I suggest in the separate feedback, these fields are the ones that must stay.

## Query

mcp__typo3-dev-companion__typo3_gerrit_lookup, change="93620", messages="people"; then change="95425", messages="people". Fields that changed the plan: chain (14 entries on 95425), mergeable (true/false), fetch.ref, commit/parent shas, the full commit message body of 95425, and the Outdated Votes note in messages.

## Suggestion

Keep chain, mergeable, fetch.ref, the commit message body and messages="people" exactly as they are when trimming the answer size. The chain field in particular did work nothing in the checkout could have done: a clone shows what landed and says nothing about a 14-change series sitting open on top of a change somebody hands you as "prework".
