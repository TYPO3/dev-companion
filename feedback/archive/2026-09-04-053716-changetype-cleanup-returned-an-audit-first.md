---
date: 2026-09-04T05:37:16+00:00
category: idea
status: closed
closed: 2026-09-04
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/site-tierheim
---

# changeType "cleanup" returned an audit-first checklist that contradicted the user's explicit inst...

## Observation

Task as the user gave it, in German: "remove fluid_styled_content as a dependency and rebuild everything you need. Check all elements, make sure you have not forgotten any. Do not ask any questions, research and find the best solution."

I called typo3_task_guide with changeType "cleanup". The intents came back with "cleanup — Putting a repository right" at strong confidence, skills ["typo3-extension-health","typo3-content-element-development"], and a 15-item checklist. Roughly a third of that checklist is the audit protocol:

- "Run the audit before writing the list, and let it own the findings"
- "Show the list whole and let the maintainer cut items, reorder them or stop, before a single file is changed. That agreement is the one step nothing downstream recovers"
- "Re-run the audit on the worked list rather than grading it off the diff"
- "Report the items still open, the ones dropped with what dropped them, and the ones sent back unassigned"

Every one of those is good advice for the task it is written for. None of it is the task I had. The user had explicitly said not to ask, and the change was a single coherent one with a stated scope, not a body of code to be surveyed. Following the checklist literally would have meant stopping and presenting a worklist for agreement — i.e. violating the instruction I was given. I followed the user and ignored those four items.

The other checklist items were on target and I did follow them: confirm the branch, inspect nearby conventions, sweep the deprecations, keep the patch focused, narrowest useful test coverage, keep the cleanup mechanical, write the commit message with typo3_commit_message_guide.

The mismatch is not that "cleanup" is the wrong enum value — I still think it was the closest one. It is that "cleanup" appears to route through the same intent as "audit", so a task that says "change this" inherits a protocol written for "tell me what to change". The enum description for cleanup says nothing about this; the intent condition, which I only saw after the call, reads "only if the task asks for the repository as a whole to be changed rather than reviewed, or for the findings of a review to be worked off". My task was neither: it named one dependency and one outcome.

## Query

typo3_task_guide task="Remove the fluid_styled_content system extension dependency from a TYPO3 project sitepackage and reimplement everything it provided (TypoScript lib.contentElement, content element rendering definitions, TCA defaults, page TSconfig, ViewHelpers) inside the sitepackage", changeType="cleanup", paths=["composer.json","packages/animalshelter_sitepackage","packages/animalshelter_animals"], targetVersion="14.3"

## Suggestion

Separate a scoped cleanup from a repository-wide one. Two possibilities:

1. Tighten the cleanup intent's match so a task naming a specific target ("remove dependency X", "drop the calls to Y", "delete the dead Z") does not pull in the audit-first items. The condition text already tries to draw this line — "the repository as a whole" — but it fired anyway on a task that named three paths and one package.

2. Where the audit items do come back, mark them as conditional the way the backend-UI items are marked ("Only if the change adds or alters backend component markup..."). A checklist item that says "stop and get agreement before changing a file" is not one a caller can weigh against an explicit instruction unless it says what it is conditional on. The two backend-markup items in the same answer were marked and were trivially easy to skip correctly; the audit items were not marked and I had to decide against them on my own judgement.

Worth saying because it is the failure mode that matters: an agent less willing to override a checklist would have stopped and asked, on a task whose first sentence was "do not ask".
