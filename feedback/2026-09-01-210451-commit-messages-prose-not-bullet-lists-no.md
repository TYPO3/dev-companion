---
date: 2026-09-01T21:04:51+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# Commit messages: prose not bullet lists, no Signed-off-by, and never run git commit

## Observation

Migrating a local memory into the server: three standing corrections from this user about commit messages, all of which contradict what a session would otherwise do by default or by reading AGENTS.md.

1. Write the body as prose sentences, not bullet-point or dash lists. The user corrected this explicitly; lists read as noisy. No "-", "*" or numbered lists in a commit body.

2. Do NOT add a Signed-off-by: trailer, even though AGENTS.md in the TYPO3 core checkout prescribes it and tells you to use git commit -s or git config format.signOff true. The user removed a Signed-off-by line I had added and stated they do not use it. The footer is Resolves: / Releases: / optional Related:, plus the hook-generated Change-Id:, and nothing else. Never suggest -s or format.signOff either.

3. Never execute git commit or git commit --amend. Output the commit message as text for the user to copy and paste. This holds without exception, including when the user has asked for a change that obviously ends in a commit.

Point 2 is the interesting one for this server, because it is a case where the repository's own conventions file is not what the person wants, and a session that follows AGENTS.md faithfully gets corrected for it.

## Query

Migrated from local project memory files (feedback_commit_style.md and the "Commit Messages — Never Execute" section of MEMORY.md). Corrections this user gave repeatedly across sessions.

## Suggestion

typo3_commit_message_guide should be able to carry per-user or per-repository overrides of this kind, or at least note that the Signed-off-by requirement stated in a core checkout's AGENTS.md is not universally applied and should be confirmed rather than assumed. The prose-not-lists preference is a reasonable default for the guide to state outright — the core's own history is written in prose. The never-execute rule is a client-side working preference rather than TYPO3 knowledge, so it may not belong in the server at all; recorded here because the user asked for all local memory to be migrated.
