---
date: 2026-09-09T18:42:54+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup, typo3_forge_lookup
directory: /home/benji/projects/typo3-cms/.worktrees/toplevel-access
---

# six answer details that carried this session, recorded so a later trim does not cut them

## Observation

Task: finish a WIP TypeScript refactoring in a TYPO3 core checkout — issue title, prior-ticket search, salvage from an abandoned attempt, trailers for a main + 14.3 backport. Filed as the positive counterpart to the three complaints from the same session.

1. releaseLines on every typo3_gerrit_lookup answer. I had to set Releases: for a backport to "v14". From memory I would have guessed 14.4, on the conventional LTS numbering. The answer said main (development), 14.3 (maintained to 2029-06-30), 13.4 (maintained to 2027-12-31), with source and readAt=2026-08-05. I checked it against git ls-remote --heads origin '14.*', which returned 14.0, 14.1, 14.3 — consistent, and 14.3 is the live one. It arrives unasked on answers that have nothing to do with releases, including an empty one, which is exactly why it was in front of me when the question came up two turns later.

2. messages='people' on a change read. Change 80556 has 17 messages, 10 of them bot. The one that decided the session was Franzke's abandon note, last of the human ones. Dropping the CI noise made the log readable in one pass, and the count of what was dropped was still reported so I knew nothing had been hidden.

3. typo3_gerrit_lookup path='Build/Sources/TypeScript/core/utility'. Cheapest and highest-value call of the session: it returned change 93620, the user's own WIP change carrying the same Change-Id as the commit under HEAD, patch set 2 pushed hours earlier. Without it I would have told them to push a new change and they would have got a duplicate. I would not have guessed from the tool's name that a repository path was a way in; I found it only by reading the schema after ToolSearch.

4. issues[] on a change read, carrying subject, tracker and status — {101694, resolves, "Streamline top frame detection", Task, Rejected}. That is the cross-route that found the prior art at all. I then called typo3_forge_lookup issue='101694' anyway and it added almost nothing: empty description, notes restating the commit message. The trailer had already answered it. That Forge call is the one call of the session I would not make again.

5. The empty answer from typo3_forge_lookup query='unify JavaScript API backend frame' reported per-term match counts rather than a bare zero. That let me conclude the AND was at fault, not the corpus, and stop rewording after two tries instead of four more.

6. typo3_forge_lookup issue='110666' returned the issue's own typo3Version:'14' alongside placedAgainst:'15.0.0-dev'. The mismatch was visible in one answer, and I flagged it to the user, whose commit at that moment said Releases: main.

## Query

typo3_gerrit_lookup path='Build/Sources/TypeScript/core/utility'; typo3_gerrit_lookup query='top window'; typo3_gerrit_lookup change='80556' messages='people'; typo3_forge_lookup query='unify JavaScript API backend frame'; typo3_forge_lookup issue='101694'; typo3_forge_lookup issue='110666'.

## Suggestion

Keep all six. If answer size ever has to be cut, releaseLines and issues[] are the two that earned their place precisely by being present when nobody had asked for them — cutting them to save bytes would cost more than it saves, because the session that needs them is the one that does not know to ask. The per-term counts on an empty answer are what makes a miss actionable rather than a prompt to guess again.

The one change worth making on the strength of this: mention the path way-in earlier in typo3_gerrit_lookup's description, or in the server instruction block's "what to call for what" list — "is somebody already working on this file: typo3_gerrit_lookup with path". It is buried in a long parameter description today, and it is the call that saved this session from a duplicate change.
