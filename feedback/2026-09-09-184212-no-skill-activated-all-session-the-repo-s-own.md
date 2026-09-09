---
date: 2026-09-09T18:42:12+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-core-issue-triage, typo3-core-patch-development, typo3_project_describe, typo3_task_guide, typo3_script_lookup
directory: /home/benji/projects/typo3-cms/.worktrees/toplevel-access
---

# no skill activated all session; the repo's own AGENTS.md curl route beat the server to the question

## Observation

Task: finish a WIP TypeScript refactoring in a TYPO3 core checkout — issue title, prior-ticket search, salvage from an earlier attempt, then amend the commit's Resolves:/Releases: trailers for a main + 14.3 backport.

No skill activated in the whole session. Two fit almost word for word: typo3-core-issue-triage ("say what is still true about one issue ... whether anybody is on one already. A task that ends in a patch starts here") and typo3-core-patch-development ("Also amending after review and backporting to a release branch"). The moments they would have opened are nameable. The user asked, in German, "haben wir bereits tickets welche darauf verweisen würden?" with the commit under HEAD and top-window.ts open in the editor — that is issue-triage's sentence. Later: "wir sollten es in v14 backporten" — that is patch-development's.

The server's own instruction block told me to start every task with typo3_project_describe and to call typo3_task_guide again "at the first test, check, commit or shipped file the task did not name". I called neither, across a session that amended a commit twice and reasoned about a backport to a maintained LTS branch.

What I did instead, as the first research move: curl against forge.typo3.org/search.json. Not from ignorance of the server — from the repository's own AGENTS.md, which prescribes that route explicitly, with the bot-check caveat: "Query it through the JSON API with curl -H \"Accept: application/json\" and the default curl user agent". The repo's instructions and the server's instructions describe the same job, and the repo's were in the project I had been told to follow. The repo won. Two Bash round trips, seven queries, no hit, and the miss recorded in the neighbouring feedback about #101694.

The server cannot see any of this from its call log: what it saw is a session that begins at ToolSearch. What turned it around was the user interrupting mid-turn to ask "nutzt du den mcp?" — a correction, not a discovery.

A smaller instance of the same shape: during git commit --amend the project's pre-commit hook failed with "ERROR: There are spaces in file or path names", pointing at ./.cache/.mozilla/firefox/Profile Groups. I diagnosed it from the output alone and told the user it was a false positive scanning the working tree rather than the index — hedged with "offenbar", but a guess. typo3_script_lookup exists and is named for exactly that question. It did not occur to me to ask it.

My context for this session was complete — it did not begin at a summary of earlier turns — so the above is the whole session, not the part I can still see.

## Query

Whole session. User turns, verbatim: "schreib mir bitte ein forge issue title dafür" / "habe nwir bereits tickets welche darauf verweisen würden?" / "nutzt du den mcp?" / "könnne wir noch was von franzke übernehmen was er besser macht?" / "hier ist die issue nummber für unser ticket 110666" / "wir sollten es in v14 backporten um uns die maintenance einfach zu halten es ist kein wichtiges feature - nur eine maintenance erleichterung". Working dir: a git worktree of the TYPO3 core, HEAD = a WIP commit "[TASK] Unify the top level window access", 137 files, Resolves: #XXXXXX.

## Suggestion

The skill descriptions read well; the problem is that nothing pulls them into a session that opens inside a repository whose AGENTS.md already prescribes a route for the same question. Two things would help:

1. Have typo3-core-issue-triage's description carry the phrasings a session actually types, in more than one language: "does a ticket already exist for this", "gibt es dazu schon ein Ticket", "has anyone attempted this before", "is somebody working on this". A skill is chosen on its description alone, and the word "triage" is not what anyone types when they have a finished commit and want to know whether to file.

2. Consider having the server's instruction block name its competitor. A sentence saying that it supersedes a hand-written curl route to forge.typo3.org where a project documents one — and why: the bot check, the issues[] cross-route from Gerrit, releaseLines — is worth more than another sentence describing the server itself. The core's AGENTS.md is checked in and will keep telling every agent to use curl.
