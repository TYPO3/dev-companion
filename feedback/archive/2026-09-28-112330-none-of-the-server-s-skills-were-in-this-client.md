---
date: 2026-09-28T11:23:30+00:00
category: idea
status: closed
closed: 2026-10-05
model: claude-opus-5[1m]
tool: none, activated, server, instruction, about, typo3-, skills
directory: /home/lina/projects/typo3/manuals/coreapi
---

# none of the server's skills were in this client's listing, so its skill instruction could not be ...

## Observation

Task: a long documentation session - triaging changelog issues, writing new pages, reviewing three pull requests, converting directives - in the TYPO3 Explained manual.

The server instructions say: "Where a typo3-* skill in your listing covers the task, activate it: it makes these calls." No skill belonging to this server was in my listing. The only typo3-prefixed skill offered to me was typo3-docs-style-check, a separate plugin skill about this repository's prose style, which I did not activate either: the repository ships its own authoritative checks (make test-rst-style, test-editorconfig, test-cgl and a standalone Build/Scripts/rstStyleLint.php) and I used those instead, because their verdict is the one CI enforces.

So the instruction was unfollowable rather than ignored, and from the server's side the two look identical: it sees no skill activation and no way to tell whether a skill was missing, shut, or absent from the client. Reported because the debrief asked, and because "none activated" is a result.

Also worth recording in the same breath: the guides list that typo3_project_describe returned (29 entries, from any/assets/how-an-asset-reaches-a-page to project/testing/playwright) was the only place I learned those documents exist, and I read none of them whole. Nothing in my task matched one closely enough - they are about building, testing and shipping TYPO3 code, while I was writing prose about it - but a session that never calls project_describe would not learn of them at all.

## Query

Session-long task: triaging Changelog-To-Doc issues and writing/reviewing TYPO3 documentation in TYPO3CMS-Reference-CoreApi. The server instruction read: "Where a typo3-* skill in your listing covers the task, activate it: it makes these calls." No skill of this server appeared in the listing, so none was activated.

## Suggestion

Two things. First, if the skills are meant to be reachable, say in the server instructions how a client obtains them - the instruction as written assumes they are already in the listing, and a client where they are not gives the session no way to act. Second, for a documentation-writing session specifically, the guides list would be more useful filtered: 29 titles arrived in the project_describe answer, all about writing or testing code, so a prose task has to scan them all to conclude that none applies. A scope marker such as "for code" or "for docs" on each entry, or a note that the guide set targets code changes, would have let me skip the list with confidence rather than by assumption.
