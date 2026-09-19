---
date: 2026-09-18T09:32:24+00:00
category: idea
status: closed
closed: 2026-09-19
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_hint_lookup, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# task_guide accepted a path that does not exist and the later hint lookup restated its hints

## Observation

Task: triage and then patch the tree filter reset defect.
I called typo3_task_guide with a guessed path Build/Sources/TypeScript/backend/page-tree/page-tree-element.ts. The file lives under backend/tree/. The guide placed the path as scope core and said nothing. The description says the guide does not read the checkout, so this is by design, but a caller does not learn that the path is wrong until ls fails.
Later the patch-development skill told me to call typo3_hint_lookup with the concrete paths before editing. That call returned the same four hints the task guide had carried (backend-ui, backend-typescript, javascript-unit-tests, backend-lit-task) plus CSS hints and a project-build hint that did not apply to a TypeScript-only change. The task guide's omittedHints had listed three ids; the base.md order says to fetch those by id instead of re-querying. I re-queried, which was my error, but the skill text that sent me there says "ask typo3_hint_lookup with the concrete paths" without the condition.

## Query

typo3_task_guide task="Page tree: cancelling the search filter loses the selected page highlight/focus" changeType="triage" paths=["Build/Sources/TypeScript/backend/page-tree/page-tree-element.ts","Build/Sources/TypeScript/backend/tree/tree.ts","typo3/sysext/backend/Resources/Public/JavaScript/page-tree/page-tree-element.js"] targetVersion="15" ; typo3_hint_lookup paths=[tree.ts, tree-test.ts, tree.js] task="fix filter reset in tree web component and add JS unit test"

## Suggestion

In the patch-development skill, phrase the hint step as: fetch by id the hints the task guide listed as omitted; re-query only for a path the guide did not see. In task_guide, where a path's directory name matches no known core subsystem directory, say that the path could not be placed rather than place it silently.
