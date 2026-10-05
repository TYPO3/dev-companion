---
date: 2026-10-05T09:42:11+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3_gerrit_lookup, typo3_test_run_guide, typo3_rule_lookup, typo3_project_describe, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# what worked in a core patch review: Gerrit read, worktree cglGit warning, TASK changelog rule

## Observation

Task: review Gerrit change 96353 in a worktree. These answers saved real work. Keep them.
typo3_gerrit_lookup change=96353 gave branch, patch set 1, commit e2d9b386, fetch ref, chain (96354 on top), mergeable, issue and Releases in one call. The commit matched git rev-parse after the fetch.
typo3_test_run_guide warned that cglGit reports SUCCESS in a git worktree after it read nothing. I used cgl -n instead.
It also said a worktree may lack vendor. I checked, and Branchery had copied it.
typo3_rule_lookup "changelog entry task" said a TASK owes no entry. I did not demand an RST file.
The gerrit-workflow rule said a TASK goes to main and one line back. That made Releases: 13.4 a finding. The checkout then showed a missing fixture on 13.4.
typo3_project_describe listed core/testing/timing-a-code-path. I would not have found that page otherwise.
The review skill's demand to name suites not run and dropped candidates kept the report honest.
The second gerrit_lookup before the report only repeated the first. It cost one call with nothing new, which is the expected case.

## Query

Whole session: review of https://review.typo3.org/c/Packages/TYPO3.CMS/+/96353 in a Branchery worktree.

## Suggestion

Keep these answers as they are. Consider giving typo3_gerrit_lookup a "changed since" flag so the re-read before the report answers in one line.
