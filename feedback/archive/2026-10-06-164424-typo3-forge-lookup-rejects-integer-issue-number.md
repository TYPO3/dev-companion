---
date: 2026-10-06T16:44:24+00:00
category: bug
status: closed
closed: 2026-10-06
model: Gemini 3.8 Flash
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# typo3_forge_lookup rejects integer issue number

## Observation

We reviewed Gerrit change 93806. The tool failed when we supplied the issue number as an integer. The schema allows only strings.

## Query

{"issue": 109692}

## Suggestion

Accept both integer and string types for the issue parameter.
