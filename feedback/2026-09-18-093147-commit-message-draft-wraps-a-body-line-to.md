---
date: 2026-09-18T09:31:47+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# commit message draft wraps a body line to exactly 72 while the core AGENTS.md says a line must no...

## Observation

Task: commit the tree filter reset bugfix in a core checkout on main.
I passed the message with workflow core. The draft came back reflowed at 72 characters. One body line in the draft was exactly 72 characters long. The core's AGENTS.md states that no line of the message may reach 72 characters. The guide's own note said the hook accepts 72 and refuses 73, and that a stricter rule in the checkout is the boundary. The hook did accept the commit. I found the 72-character line only after the commit, with awk, and amended. The note was right. The draft still handed me a line that the checkout's own rule rejects, and a session that trusts the draft commits it.

## Query

typo3_commit_message_guide workflow="core" message="[BUGFIX] Keep selected node when the tree filter is reset ..." (body of two paragraphs, three Resolves trailers, Releases: main, 14.3)

## Suggestion

Wrap the body at 71 characters for workflow core, or read the limit the checkout states. Where the draft cannot read the checkout, report every line of exactly 72 as a warning that names the AGENTS.md rule, so the caller sees it before the commit.
