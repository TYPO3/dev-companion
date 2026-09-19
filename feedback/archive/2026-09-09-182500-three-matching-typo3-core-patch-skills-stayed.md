---
date: 2026-09-09T18:25:00+00:00
category: idea
status: closed
closed: 2026-09-19
model: claude-opus-5[1m]
tool: typo3-core-patch-review, typo3-core-patch-checkout, typo3-core-patch-development, typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# Three matching typo3-core-patch-* skills stayed shut for a review-and-finish task

## Observation

Task: review and finish TYPO3 core Gerrit change 93620 ("[WIP] Toplevel access"), taking the prework in change 95425 into account. Core checkout, TYPO3 15.0.0-dev, main.

No skill activated in the entire session. Not one. I made ~88 tool calls, of which exactly 3 went to this server (typo3_project_describe, typo3_gerrit_lookup twice); everything else was Bash, git and grep.

Three skills in my listing matched the task almost word for word:

- typo3-core-patch-review: "Review a TYPO3 core patch - your own before you push it, or somebody else's patch set - and say what is wrong, missing or not ready, in priority order: the diff, its tests, the changelog entry, the commit message, the issue reference and the target branch." The user's words were "please review and finish this patch 93620". That is the description.
- typo3-core-patch-checkout: "Get a patch under review on review.typo3.org into a core checkout ... into a git worktree beside it". The user then said "use a worktree please to finish it". Again, the description.
- typo3-core-patch-development: "Write a TYPO3 core patch and carry it to review: the changelog entry, the project's checks, the push to Gerrit. Also amending after review."

I also never called typo3_task_guide, although the server's own initialize instructions say to: "Then call typo3_task_guide for the workflow the task belongs to, and again at the first test, check, commit or shipped file the task did not name." I hit all four of those triggers - the first test I wrote, the first runTests.sh suite, the commit, and files I shipped that the task never named - and called it zero times.

Why it happened, as best I can reconstruct from the transcript: the user's first message contained two review.typo3.org URLs. My first move was ToolSearch for "gerrit lookup", then straight into typo3_gerrit_lookup. The URLs pulled me to a specific lookup tool, and once I had the change metadata I was in a diff-reading loop and never came back up to ask what workflow this was. The skill listing is presented once, in the system prompt, and nothing at the moment of "review this patch" pointed back at it.

This is a routing failure on my side, not a gap in the server. But it is a repeatable one: a request naming a Gerrit URL will keep pulling models straight to typo3_gerrit_lookup, past the skills that would have framed the work. The cost was not zero - see the separate feedback about five commit-message amends, which typo3_commit_message_guide would have prevented.

## Query

User request, verbatim: "please review and finish this patch 93620: [WIP] Toplevel access | https://review.typo3.org/c/Packages/TYPO3.CMS/+/93620, please also consider the similar prework we did in https://review.typo3.org/c/Packages/TYPO3.CMS/+/95425". Follow-up mid-turn: "use a worktree please to finish it". Skills available and not activated: typo3-core-patch-review, typo3-core-patch-checkout, typo3-core-patch-development. Tools called this session: typo3_project_describe (1), typo3_gerrit_lookup (2). typo3_task_guide: 0 calls.

## Suggestion

Consider having typo3_gerrit_lookup's answer carry a routing line when the change is open and the caller is plainly working on it - something like "finishing or reviewing this change is typo3-core-patch-review / typo3-core-patch-development; the checkout steps are typo3-core-patch-checkout". The lookups are where a Gerrit URL lands a model, so that is where the pointer back to the skills has to be. The initialize instruction to call typo3_task_guide is easy to read past once a specific-looking tool has already answered.
