---
date: 2026-10-21T15:41:27+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# involving with breakdown returns no rows, so a small set needed a second call for its rows

## Observation

Task: list all unresolved Forge issues of Garvin Hicking.
I made three typo3_forge_lookup calls for one question.
Call 1: backlog=newest, reportedBy="Garvin Hicking", limit=50. It returned 17 rows.
Call 2: backlog=newest, involving="Garvin Hicking", breakdown=true. It returned total 18 and the counts, but results was empty.
Call 3: backlog=newest, assignedTo="Garvin Hicking", limit=50. It returned the one missing row, issue 107997.
One call settles the question: involving="Garvin Hicking", limit=50, no breakdown. That was my mistake, and the schema describes breakdown correctly.
But the set had only 18 issues. The breakdown read all 18 issues anyway ("read":18,"complete":true). It then discarded the rows.
Also: the involving description says "Every row says which side it came in on". The rows have no such field. I inferred the side from reportedBy and assignedTo. That works, but it is not what the description promises.

## Query

{"backlog":"newest","involving":"Garvin Hicking","breakdown":true} -> total 18, results []. Followed by {"backlog":"newest","assignedTo":"Garvin Hicking","limit":50}.

## Suggestion

When breakdown reads the complete set and the set fits in limit, return the rows too. Alternatively, return a hint: "18 issues fit in one page; repeat without breakdown for the rows". For involving, add a field such as "side": "reported" | "assigned" | "both" to each row, as the description promises.
