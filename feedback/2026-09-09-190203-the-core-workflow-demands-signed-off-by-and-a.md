---
date: 2026-09-09T19:02:03+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# the core workflow demands Signed-off-by and a real issue where the repository in front of me uses...

## Observation

Task: write a short new commit message for Gerrit 93628, whose old subject "[WIP][TASK] ALLOW GD ONLY" had repeatedly caused irritation in review.

The draft itself was good and I used it nearly verbatim: the body came back wrapped at 72, the wrapping was sensible, and the line-length-boundary note (the hook accepts 72 and refuses 73, and measures trailers too) is the kind of precision that saves a rejected push. Keep that.

Two of the three findings it returned were false against the repository actually in front of me.

1. missing-sign-off, raised as an error, with a strong argument that the DCO is mine to state. But this chain carries no Signed-off-by at all. I checked before acting: the sibling commit 93760 has Resolves / Releases / Change-Id and no sign-off, and so does the MERGED 93666 (9468741c8f8) that the chain sits on. Adding one would have made this commit the odd one out in its own relation chain. I dropped it and said so to the user, who did not object. The repository's AGENTS.md does ask for sign-off, so the rule is not wrong in general - but an error level on a message whose own siblings in the same chain omit it is a finding the guide could have made instead of a demand, if it could read the neighbouring commits.

2. missing-issue, raised as an error. Change 93628 has never carried a Resolves: line across 51 patch sets, and the user's instruction when I raised it was explicit: keep "#xxxxxx" as a deliberate placeholder while the change is WIP, and do not reference other tickets. So the working convention here is that a WIP change carries a placeholder issue until it is ready to be filed. The guide has no notion of that state, though [WIP] in the subject is exactly the signal for it.

3. breaking-not-assessed was fair and I did not act on it - the change is WIP, its classification is not settled, and the note correctly says the tool never sees the diff.

Worth recording separately: typo3_forge_lookup made the issue question answerable. My first call, query="image processing GD ImageMagick processor", returned total 0 - but the terms breakdown (image 4464, processing 1211, GD 656, ImageMagick 272, processor 690) told me instantly that the AND was the problem rather than the subject, which is good design and cost me only one repeat. The second call, backlog="newest" with reportedBy="Benjamin Kott", status="all", createdSince="2026-03-01", answered the real question: it listed the whole neighbourhood (109578 for the merged 93666, 109576, 109660 for 93760) and showed that no issue exists for 93628. That negative is what let me tell the user honestly that there was nothing to reference.

## Query

typo3_commit_message_guide with workflow="core", keyword="TASK", summary="Make GD the baseline image processor", a two-paragraph body, releases=["main"]. Rewriting the message of Gerrit 93628, a WIP change on 51 patch sets.
