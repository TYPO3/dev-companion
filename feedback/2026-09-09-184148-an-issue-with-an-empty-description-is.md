---
date: 2026-09-09T18:41:48+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms/.worktrees/toplevel-access
---

# an issue with an empty description is unreachable by forge query; its Gerrit change found it

## Observation

Task: finish a WIP TypeScript refactoring in a TYPO3 core checkout (unify top-level window access, 137 files), write its Forge issue title, and find whether a ticket for it already existed.

The decisive prior art was Forge #101694 "Streamline top frame detection" (Task, Rejected) with Gerrit change 80556 (abandoned 2025-03-05) — the same idea, a top-frame utility module plus ESLint rules, 50 TypeScript files. Advising the user without it would have been actively wrong: they would have filed straight into an objection already raised and sustained ("feels wrong to build an abstraction for a broken concept").

typo3_forge_lookup never found it. I tried query='top window access JavaScript backend' (6 results, none of them it) and query='unify JavaScript API backend frame' (empty). Before that I had tried the same thing over Redmine's search.json by hand with 'topWindow', 'top.document', 'top level window', 'window.top', 'list_frame', 'contentIframe', 'top frame backend JavaScript' — seven wordings, no hit.

The reason is visible in the issue once read: #101694 has description:'' and noteCount 10, of which 8 are bot notes. Its only human text is one journal note, and its subject says "top frame detection" where every word available from the code was "top window" or "top level window". A full-text AND search cannot reach it from the vocabulary of the codebase.

What did reach it: typo3_gerrit_lookup query='top window', which matched change 80556's commit message, and then change='80556', whose issues[] carried {101694, resolves, "Streamline top frame detection", Task, Rejected} — tracker and status included, so the Forge state came back without a Forge call. The route was code -> review -> issue, never issue-first.

I did act correctly on the miss diagnostics in one respect: the empty answer's per-term counts showed 'unify'(140) and 'frame'(2156) each matching plenty, so the AND was the problem and not the corpus. But the description's own advice — "ask again in the reporter's words" — was unfollowable here, because the reporter's word was "frame" and nothing in the code under change uses it.

## Query

typo3_forge_lookup query='top window access JavaScript backend' limit=25; typo3_forge_lookup query='unify JavaScript API backend frame' limit=25; then typo3_gerrit_lookup query='top window' limit=25; typo3_gerrit_lookup change='80556' messages='people'; typo3_forge_lookup issue='101694'. Goal: find prior art for a refactoring that centralises top-level window access across Build/Sources/TypeScript.

## Suggestion

Say in typo3_forge_lookup's description that a query reaches only text somebody actually wrote, so an issue with an empty description is reachable by its subject alone — and that the route for "has this been attempted before" is typo3_gerrit_lookup by path or by commit-message words, following issues[] back to the ticket. A path search over the files a patch touches is the wording-independent way in, and it is the one that worked here; it deserves naming in forge_lookup's description, not only in gerrit_lookup's, because the question is asked at the tracker.

Alternatively: when a forge query's AND over all fields is empty, fall back to a subject-only match and say in the answer that it did. #101694 would have come back for "top frame".
