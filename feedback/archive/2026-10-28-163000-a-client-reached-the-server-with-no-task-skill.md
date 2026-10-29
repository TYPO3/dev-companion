---
date: 2026-10-28T16:30:00+00:00
category: tool-gap
status: closed
closed: 2026-10-29
model: claude-opus-5
tool: typo3-dev-companion install
directory: /home/lina/projects/typo3/projects/jambo-open
---

# a client reached the server with no task skill installed

## Observation

Two opencode runs used this server. Neither project holds .agents/skills, which is where Installer publishes for opencode. Neither holds any other skills directory.
So both runs had the tools and none of the procedure.
The run in jambo-pickle stopped at the written files and reported the page ready. The site served an exception.
Entrypoint hands Installer::ABSENT into the initialize instructions, so the client is told. Whether opencode shows those instructions to the model could not be established from the projects.
Both Claude runs carried .claude/skills, and both ended on a site that serves.

## Query

typo3-dev-companion install, read from the projects it did not reach.

## Suggestion

Establish whether the ABSENT notice reaches the model in a client other than Claude Code.
Where it does not, that sentence has to travel on something the model does read, for example the answer of the first tool it calls.
A server whose procedure never arrives answers worse than its own documentation.
