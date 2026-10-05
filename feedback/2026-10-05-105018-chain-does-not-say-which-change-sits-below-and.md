---
date: 2026-10-05T10:50:18+00:00
category: tool-gap
status: open
model: claude-opus-5-5
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# chain does not say which change sits below, and Releases branches get no apply check

## Observation

Task: review core patch 96354.
The answer for 96354 had a chain of two entries: 96354, then 96353. The answer for 96353 had the same order. No field says which change is the parent.
I used git log on the fetched ref to find it. 96354 sits on top of 96353. The two changes share no file.
The patch names "Releases: main, 14.3, 13.4". "mergeable" is only for the target branch main.
I checked the release branches myself with a temporary git index and git apply --check -3. 14.3: one file missing, one conflict. 13.4: one file missing, three conflicts. On 13.4 the e2e script has no initCommands block, so the patch has no effect there.
That was the main finding of the review. It cost three Bash calls outside the server.

## Query

typo3_gerrit_lookup(change="96354", messages="people"); typo3_gerrit_lookup(change="96353", messages="people")

## Suggestion

Mark each chain entry as "parent", "child" or "this change", or order the chain from the base up and say so.
For each branch in the Releases: trailer, report whether every changed path exists there and whether the patch applies. A note that a backport needs manual work is enough.
