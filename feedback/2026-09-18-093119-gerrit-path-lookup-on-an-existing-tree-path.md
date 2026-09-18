---
date: 2026-09-18T09:31:19+00:00
category: bug
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# gerrit path lookup on an existing tree path failed while a stale sibling path answered

## Observation

Task: find and fix the page tree losing its selected node after the filter is reset (core checkout, main, 15.0.0-dev).
I made two path lookups in one parallel block. The first asked for open changes under Build/Sources/TypeScript/backend/tree, which is the directory that exists on main. It came back unavailable with cause source-not-answering. The second asked for Build/Sources/TypeScript/backend/page-tree, a directory that does not exist on main. It answered with three open changes (77248, 80380, 80004) that touch that old path. The answer did not say that the path no longer exists on the branch. Three issue lookups against the same server answered normally in the minute before, and the changelog and forge lookups beside them answered too. So the outage was specific to one call. I did not repeat the failed call. The result is that the one question I needed, whether somebody works on tree.ts now, stayed unanswered, and I moved on without it.

## Query

typo3_gerrit_lookup path="Build/Sources/TypeScript/backend/tree" open=true limit=15 ; typo3_gerrit_lookup path="Build/Sources/TypeScript/backend/page-tree" open=true limit=15 (both in one parallel block)

## Suggestion

Retry a path search once before you report source-not-answering, and say in the answer that a retry happened. When a path query answers changes, say whether the path exists on the branch each change targets, or at least note that a file: regex matches historical paths. A caller who searches a renamed directory then knows the hits are old.
