---
date: 2026-09-09T18:25:44+00:00
category: idea
status: closed
closed: 2026-09-09
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# typo3_gerrit_lookup inlines the whole file list; two calls cost more than the diff itself

## Observation

Task: review and finish TYPO3 core Gerrit change 93620, taking change 95425 into account.

Two calls to typo3_gerrit_lookup, both reading a change by number. Both answers inlined the complete file list with per-file insertions/deletions: roughly 140 file entries for 93620 (137 files changed) and roughly 200 for 95425. That is by far the largest thing this server returned to me all session, and I used almost none of it.

What I actually needed from those answers, and used:
- subject, status, branch, patchSet, owner
- mergeable (93620 true, 95425 false) - decisive
- fetch.ref (refs/changes/20/93620/2) - decisive, I fetched with it immediately
- commit sha and parent, to check the base was current main
- the commit message text of 95425, which described its top-window.ts accessor
- the chain array for 95425 - 14 changes, decisive
- messages, which told me votes were dropped by a re-upload

What I did not need: every path with its line counts. The moment I had fetch.ref I ran git fetch and read the real diff with git diff, which is the only way to see content anyway. The inlined file list is a worse version of git diff --stat, arriving before I could act on it.

For 95425 in particular the file list was pure cost: I only ever wanted to know what its accessor looked like and whether it was independent. Two facts, ~200 file entries.

Nothing here was wrong. The answers were accurate and the useful fields were exactly right. It is a volume problem, and it compounds because reading two related changes is the normal shape of "finish this patch, consider that one".

## Query

mcp__typo3-dev-companion__typo3_gerrit_lookup with change="93620", messages="people" - returned ~140 inlined file entries. Then change="95425", messages="people" - returned ~200 inlined file entries plus a 14-entry chain. In both cases the fields used downstream were: subject, status, branch, patchSet, commit, mergeable, fetch.ref, message, chain, messages. The files array was not used at all; the diff came from git fetch + git diff against the fetched ref.

## Suggestion

A files parameter on the by-name reads - "none" | "stat" | "full", defaulting to something less than full. "none" would have served both my calls completely. Where a caller genuinely wants paths they usually want them filtered anyway (which sysext does this touch), and a count plus the top-level directories would answer that in a fraction of the space.

Alternatively: keep the list but drop it automatically past some size, with a line saying how many files there were and that git diff --stat against fetch.ref will show them. A caller who has just been handed fetch.ref is one command away from the real thing.
