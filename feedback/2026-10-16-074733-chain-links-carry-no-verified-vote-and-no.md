---
date: 2026-10-16T07:47:33+00:00
category: tool-gap
status: open
model: claude-opus-5-5
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# chain links carry no Verified vote, and no answer names the CI job that failed

## Observation

Task: rebase the 13-change chain 95425..95437 onto main and rebuild the build output in each commit.
The tip answer showed Verified-1 by core-ci. Each chain link carried only number, status, subject, patchSet and chainedAt.
I needed to know which links were red, and why. A rebase with a rebuild has to know whether the build is the failure.
I used 13 REST calls for the labels of each link. Then I read the review messages to get the GitLab pipeline URLs.
Then I called the GitLab API for the failed jobs. All four pipelines failed only "e2e playwright ... pre-merge 2/4".
The job traces answered HTTP 401 anonymously. So I could not see the failing test.
Link 95425 was red, and 95426 and 95427 on top of it were green. That pattern suggests a flaky chunk. Nothing confirmed it.
Also: the tip answered mergeable false. git merge-tree onto origin/main exited 0, and the rebase had no conflict. The tool description warns about this, so it is a note, not a defect.

## Query

typo3_gerrit_lookup change="95437" files="stat" (chain of 13 open links, tip Verified-1)

## Suggestion

Give each chain link its Verified and Code-Review state for its current patch set. For a Verified-1 by core-ci, give the pipeline URL from the review message and the names of the failed jobs from the public GitLab jobs API (projects/typo3%2FCI%2Fcms/pipelines/ID/jobs?scope[]=failed). Say that traces need a login. One call would then have answered "which links are red, and on which job".
