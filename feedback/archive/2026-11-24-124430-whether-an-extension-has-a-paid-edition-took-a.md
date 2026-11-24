---
date: 2026-11-24T12:44:30+00:00
category: tool-gap
status: closed
closed: 2026-11-24
model: claude-opus-5-5
tool: typo3_ter_lookup
---

# whether an extension has a paid edition took a web search

## Observation

Stage 09 asked which search to use. After typo3_ter_lookup for ke_search the session searched the web for "ke_search premium price license" to answer what it costs. Cost and licence are part of choosing an extension in the talk's own checklist (maintainer, cost, versions, way out), and the session asked for them unprompted, which is good.

The TER answer carries maintenance facts (versions, upload dates, supported majors) but nothing about a commercial edition, so the session left the server for that question.

## Query

{"extension": "ke_search", "limit": 3}

## Suggestion

Where the registry knows it, say that an extension has a paid edition or a commercial licence and link the page that says so. Where it does not, say that the TER answer does not cover cost, so an agent knows to ask the user or look it up rather than assume free.
