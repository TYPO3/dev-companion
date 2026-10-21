---
date: 2026-10-21T15:41:05+00:00
category: idea
status: closed
closed: 2026-10-21
model: claude-opus-5-5
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# Server instructions do not name typo3_forge_lookup, so the agent used curl from AGENTS.md first

## Observation

Task: "give me a list of all of garvins issues that are not resolved" in the TYPO3 core checkout.
The client listed typo3_forge_lookup only as a deferred tool name. Its schema was not loaded.
The server instructions have a "What to call for what" list. That list names no Forge or Gerrit tool.
The core AGENTS.md tells agents to query Forge with curl. I followed AGENTS.md.
I made two curl calls. The first searched for "garvin" and returned only changesets with "Reviewed-by" lines. The second read 400 recent issues to find the user id 37734.
The curl calls worked with the default user agent. But they cost two round trips and a hand-written Python filter.
The user then wrote "use the mcp". Only then did I load typo3_forge_lookup.
The tool description itself mentions the AGENTS.md warning. But an agent reads that text only after it loads the tool.

## Query

Session task: "give me a list of all of garvins issues that are not resolved". Calls before the tool: curl forge search.json?q=garvin; curl issues.json?project_id=27&status_id=*&sort=updated_on:desc, 4 pages.

## Suggestion

Add a line to the server instructions "What to call for what" list: "an issue, a person's issues, or the backlog on forge.typo3.org: typo3_forge_lookup; a change on review.typo3.org: typo3_gerrit_lookup". Say there that it replaces the curl route that AGENTS.md describes. An agent reads the instructions before it loads any deferred tool.
