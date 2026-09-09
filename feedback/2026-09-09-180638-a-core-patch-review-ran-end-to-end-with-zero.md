---
date: 2026-09-09T18:06:38+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-core-patch-review, typo3-core-patch-checkout, typo3_project_describe, typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# A core patch review ran end to end with zero calls to this server

## Observation

Task: review the final patch set of Gerrit change 95482 against an existing local review worktree, decide whether earlier review concerns were addressed, then substantiate the remaining points with tests. Full transcript visible, no summary boundary.

Counted off the transcript: 96 Bash calls, 0 MCP tool calls, 0 skill activations. Not one typo3_* tool was invoked in a multi-hour TYPO3 core review session that ended in a production-code fix, four test files, and a Gerrit comment.

Two listed skills matched the request almost literally and stayed shut:

- typo3-core-patch-review ("Review a TYPO3 core patch — your own before you push it, or somebody else's patch set — and say what is wrong, missing or not ready, in priority order: the diff, its tests, the changelog entry, the commit message, the issue reference and the target branch"). The opening request was exactly this, with a review.typo3.org URL in it. The five findings I produced by hand map one-to-one onto that description's list: diff, tests, changelog entry, commit message, target branch (Releases: 14.3, main).
- typo3-core-patch-checkout ("Get a patch under review on review.typo3.org into a core checkout ... into a git worktree beside it"). Activation moment: the user wrote "lass uns unseren branch auf diesen stand setzen und darauf weiter arbeiten" while I had the worktree .worktrees/review-95482 open at patch set 1. I did the fetch/reset/backup-branch dance by hand.

Why they stayed shut, honestly:

1. The repository's own AGENTS.md already carried the recipes — the Gerrit JSON API with curl and the default user agent, the runTests.sh dispatcher, the changelog rules, the commit trailer order. With that in context, every server call had to beat a document I had already read.
2. A live checkout made grep cheaper than any lookup for anything answerable from files.
3. Every typo3_* tool arrived as a deferred name with no schema. Calling one required a ToolSearch round trip first. So the cheapest possible server call cost strictly more than the grep it would have replaced, and I never paid it — including for typo3_project_describe, which this server's own instructions tell me to start every task with. That instruction was in my context and I did not follow it.

What this means for the maintainers: the server saved me from nothing this session, because it was never asked. That is not a quality judgement on its answers — I have no evidence about those. It is a finding about reach. A server whose tools are strictly more expensive to try than grep, in a repo with a strong AGENTS.md, gets skipped by a model that is optimising round trips.

## Query

Session task: "wir haben bereits ein review für 95482: [BUGFIX] Always exclude app package from active packages | https://review.typo3.org/c/Packages/TYPO3.CMS/+/95482 der patch ist nun final und hat sich nochmal geändert, kannst du bitte das mit unserem worktree abgleichen ob unsere änderungen / bedenken berücksichtig werden?" — followed by "können wir die offenen punkte mit tests belegen?", "lass uns unseren branch auf diesen stand setzen", "kannst du mir einen kommentar als frage formuliert für gerrit geben", "helmut answered in the pr please verify it".

## Suggestion

The two skill descriptions are good prose; what is missing is the trigger surface. Consider naming the artifacts that identify the task in the description itself, because a skill is chosen on its description alone: a review.typo3.org URL, a bare change number ("95482"), the words "patch set", "Gerrit", "review worktree". My request contained three of those and none of them appeared in a description I was matching against.

Second, and more structural: where a client defers tool schemas, the value of a tool has to be legible from its name and one-line description alone, because trying it costs a round trip. Tools whose description says what they *are* ("look up a Gerrit change") lose to grep; tools whose description says what they *save* ("returns the current patch set's diff, parent SHA and the fetch ref for a core checkout") get tried. That is worth a pass over every description on this server.
