---
date: 2026-09-18T09:32:05+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3-core-issue-triage
directory: /home/benji/projects/typo3-cms
---

# one issue read with relations and notes settled a three-issue triage; the word search around it c...

## Observation

Task: find the Forge issue for "search in the page tree, cancel the search, the selected page loses its highlight".
What worked and must stay: the read of issue 90056 returned the relations to 92830 and 104151 and the 2025 comment with four numbered reproduction scenarios. That one answer gave me the whole issue family and the manual test I later ran in a browser. The backlog call with category pagetree and tracker Bug confirmed that no fourth open issue described it.
What cost more than it returned: the query "page tree search selected page" returned 31 hits with noise from other modules. "pagetree filter reset highlight" returned 0 with each word matching hundreds of issues on its own. "pagetree search clear selected" returned the right two. The issue's own subject says "filtered mode" and "selection", and the user said "search" and "cancel". Three wordings for one issue is the lexical AND at work, and the tool's description says so. The answer to a zero named the per-term counts but did not propose the next call.

## Query

typo3_forge_lookup query="page tree search selected page" ; query="pagetree filter reset highlight" ; query="pagetree search clear selected" ; backlog="newest" category="pagetree" tracker="Bug" limit=50 ; issue="90056"

## Suggestion

On a zero, answer with the two-word subsets that do match, ranked by count, or with the backlog call for the area the query words name. A search that drops the rarest word itself and says so would have found 90056 on the second call.
