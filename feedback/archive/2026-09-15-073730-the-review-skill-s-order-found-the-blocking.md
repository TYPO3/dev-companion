---
date: 2026-09-15T07:37:30+00:00
category: idea
status: closed
closed: 2026-09-15
model: claude-opus-5
tool: typo3-core-patch-review, typo3-core-patch-checkout, typo3_task_guide, typo3_hint_lookup, typo3_test_run_guide, typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# the review skill's order found the blocking suite before the diff was read

## Observation

Task: review core change 95814 (patch set 1) in a worktree, then compare its alternatives 95817 and 95818.

The skill typo3-core-patch-review activated on the request and fitted the task. Its order paid off once in a way I want to record. The task guide omitted four hints and named them. I fetched fluid-resource-uris by id before I read the diff. That hint said that the core covers EXT: paths in f:image in SvgImageViewHelperTest. I ran that suite first. It failed 48 of 152 cases. The patch does not touch it, and CI later voted -1 on all three changes. Without the hint I would have run the patch's own tests only, and they are green.

Two more answers changed findings. The dependency-injection hint gave F3.1 (makeInstance() beside an injected service). The changelog rule gave F2.6: features go to main, and a Releases line with 14.3 is a claim.

The checkout skill and the gerrit-workflow page gave the ref form and the anonymous https fetch. typo3_test_run_guide named the composerInstall precondition for a worktree and the cglGit trap. I used cgl -n because of it.

Calls I would not make again: typo3_hint_lookup id=system-extension-boundaries returned three generic sentences. typo3_rule_lookup query="review readiness" returned six sections, and only the first one applied. typo3_changelog_lookup query="fallback storage" returned nothing, which was a correct absence. typo3_changelog_lookup type=feature version=14.3 returned 0 entries. I doubted it and read the checkout. The checkout agrees: 14.3 holds 15 deprecations and no feature files. The answer was right.

The second typo3_gerrit_lookup on 95814, one day later, found the alternatives and the CI vote. The first call had none of that. A review that runs over two days needs that second call. The skill does not say so.

What I read whole: core/contribution/gerrit-workflow, by documentId. It carried the fetch end to end. The client showed no resource list. I found the document ids in the guides list of typo3_project_describe.

What I would keep: the omittedHints list with ids, the checkout skill's stopping rules, the test guide's worktree notes. What I would drop: nothing in the skill. The skill text is long, and I read the two reference files whole; that cost one turn each and was worth it.

## Query

Task text: "review mir bitte 95814: [FEATURE] Process system resource images without FAL im worktree". Skills: typo3-core-patch-review (args 95814), typo3-core-patch-checkout (args "95814 into a worktree for review"). Calls in order: typo3_project_describe; typo3_gerrit_lookup change=95814 files=full messages=people; typo3_task_guide changeType=audit with 13 paths; typo3_forge_lookup issue=110702; typo3_hint_lookup id=fal-basics, id=fluid-resource-uris, id=system-extension-boundaries, id=dependency-injection; typo3_rule_lookup documentId=core/contribution/gerrit-workflow; typo3_rule_lookup query="feature changelog entry"; typo3_changelog_lookup type=feature version=14.3 and version=14.3.x; typo3_test_run_guide with 9 paths; typo3_rule_lookup query="review readiness"; typo3_changelog_lookup query="system resource"; typo3_changelog_lookup query="fallback storage"; typo3_commit_message_guide workflow=core; typo3_documentation_lookup queries and page for imgResource; typo3_gerrit_lookup change=95814 again next day; change=95817; change=95818.

## Suggestion

Add one sentence to typo3-core-patch-review: repeat typo3_gerrit_lookup before the report when the review spans more than one sitting, because votes, messages and alternatives arrive without a checkout signal. Keep the omittedHints mechanism as it is. The ids it returns were the most valuable calls of the session.
