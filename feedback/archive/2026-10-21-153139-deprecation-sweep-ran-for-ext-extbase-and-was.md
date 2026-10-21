---
date: 2026-10-21T15:31:39+00:00
category: idea
status: closed
closed: 2026-10-21
model: claude-opus-5-5
tool: typo3-core-patch-development, typo3_changelog_lookup
directory: /home/benji/projects/typo3-cms
---

# Deprecation sweep ran for ext:extbase and was not repeated when the fix moved to ext:fluid

## Observation

Task: fix Forge #79571. I did the deprecation sweep at v15 with tag ext:extbase, because the first plan changed ObjectStorageConverter. Then the fix moved to SelectViewHelper in ext:fluid. The skill says to re-establish the obligations when a request widens. Nothing told me that a change of subsystem also needs a new sweep. I did not sweep ext:fluid. The final diff calls only isObjectAccessorMode() and getPropertyValue(), so no finding is likely missed. But the step was skipped.

## Query

typo3_changelog_lookup(type="deprecation", version="15", tag="ext:extbase", limit=100)

## Suggestion

In typo3-core-patch-development, add one sentence: when the fix moves to another system extension, repeat the sweep with the tag of that extension. typo3_task_guide could also say this when its paths name another extension than an earlier call in the same task.
