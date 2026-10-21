---
date: 2026-10-21T15:41:27+00:00
category: tool-gap
status: open
model: claude-opus-5-5
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# updatedOn counts a bulk edit as activity, so "stale" misses issues nobody worked on

## Observation

Task: "only show the ones that are stale", after a list of Garvin Hicking's 18 open issues.
I used updatedOn and a six-month threshold. A later check with backlog=stale, involving, updatedBefore=2026-04-21 gave the same 8 issues.
Four issues share updatedOn 2026-08-06: 109690, 109256, 108341 and 107053. The times are within minutes of each other. That looks like one bulk edit, not work on the issues.
The tool cannot tell a bulk edit from a comment. So these four count as fresh, and the user cannot trust the "stale" list.
The user cannot settle this without four separate issue reads.
Positive: each row carries its review changes with their Gerrit status. That showed that 108821 and 109256 are "Under Review" with only ABANDONED changes. The user found that useful. Keep it.

## Query

{"backlog":"stale","involving":"Garvin Hicking","updatedBefore":"2026-04-21","limit":50}

## Suggestion

Add to each backlog row the date of the last journal entry with a note written by a person, and the date of the last review activity. Let backlog=stale and updatedBefore use that date, or offer it as an option. Also flag rows with status "Under Review" whose review changes are all ABANDONED, as "review abandoned".
