---
date: 2026-09-15T07:38:17+00:00
category: tool-gap
status: open
model: claude-opus-5
tool: typo3_gerrit_lookup, typo3_test_run_guide, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# a comparison of alternative changes and a timing probe had no tool or page

## Observation

Task: compare change 95814 with the alternatives 95817 and 95818 that its author pushed, and measure the hit path.

The three typo3_gerrit_lookup calls answered the messages, the votes, the file lists and the sizes. They cost one round trip each. The comparison itself needed the diffs. The tool says that a diff needs a checkout, so I fetched both refs into the object store of the review worktree and ran git diff. That was the right cost, and I would do it again. What I lacked was a way to say which files differ between two changes without a fetch. The file lists overlap, and the answer I needed was "the diff of 95814 against 95818 is empty for ContentObjectRenderer.php, ImageService.php and every functional test". That sentence carried the whole verdict: every finding of the first review holds for all three. One call, change=95814 and against=95818, with the list of paths that differ and their sizes, would have settled it before any fetch.

The author's messages linked the alternatives by number in free text. The relation chain in the answer was empty, because the changes are not stacked. A reader who does not read the messages misses them.

Also never put to the server: the Soul design system, the diagram grammar and the ASD-STE100 prose check. They live in another repository of the user, and I assumed the server does not know it. That assumption held; nothing in the task guide or the skill named it.

## Query

User: "ok bitte lies die kommentare auf dem patch und vergleiche die alternativen ansätze"; Calls made: typo3_gerrit_lookup change=95814 messages=people files=none; change=95817 messages=people files=full; change=95818 messages=people files=full. Then git fetch of refs/changes/17/95817/2 and refs/changes/18/95818/2 into the worktree's object store as tags, and git diff between the three commits.

## Suggestion

Give typo3_gerrit_lookup a second change argument, or a "differs" answer: for two changes on one parent, the paths that differ between their current patch sets, with insertions and deletions per path. Where the review server cannot diff, say so and return the two fetch refs side by side. Read the messages for change numbers and list them as "named in messages" beside the chain, so an alternative pushed as a separate change is visible in one read.
