---
date: 2026-10-22T10:57:11+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3-core-patch-review, typo3-core-patch-development, typo3-core-patch-checkout
directory: /home/benji/projects/typo3-cms
---

# Three chained skills for an own change that already sits on the local branch tip

## Observation

Task: "review 96975, read the comments, assess them and finish the patch". The change was my user's own. Patch set 1 had the local main tip as its parent. The tree was clean.
I activated typo3-core-patch-review. The request also said "finish it", so I switched to typo3-core-patch-development at once, as the review skill says. The development skill then made me invoke typo3-core-patch-checkout. That skill did one thing here: "git switch -c review/96975 <patch set commit>". The three skill texts cost many thousand tokens.
Keep: the review skill's handover rule, the branch name review/<n>, the undo list, the order "test first, then fix".
I did not do two steps. I did not call typo3_hint_lookup by id for the omitted "core-tests" hint. I did not invoke typo3-core-patch-review again on the final diff, as the development skill tells me. I reviewed the diff without the skill.

## Query

Task text: "bitte review mir 96975 ... lies die kommentare, bewerte sie und mach den patch fertig"

## Suggestion

Let the review skill say which of its surfaces the development skill still owes when the request asks for both review and rework.
