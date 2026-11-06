---
date: 2026-10-31T07:37:18+00:00
category: bug
status: open
model: claude-opus-5
directory: /home/lina/projects/typo3/tools/dev-companion
---

# a server launched through ddev exec cannot report its own stopped project

## Observation

`D-DIS-037` answered the stopped project. One form remains. A client hit it on
2026-10-31 with the project running. Two calls returned nothing for 1829 seconds,
and the client aborted both. The identical command answered by hand in two
seconds against that same container, its first line of stdout a valid JSON-RPC
frame, with DDEV's warnings on stderr.

The session did not establish what this form is. A stdio server answers one
request at a time, so the second call may have queued behind the first. That is a
guess and stays one.

## Suggestion

Judge this when a second report or a transcript shows the same silence.
