---
date: 2026-10-26T10:24:28+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5
tool: typo3-core-patch-review, typo3_rule_lookup, typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# a review write-up for a document has no template, so the report shape gets lost

## Observation

Task: write a summary document of the review and amendment of Gerrit change 97164.

The review skill defines the report shape in its "Report" section and in checklist.md.
The shape is: findings ranked by severity, five parts per finding, verified versus reasoned, dropped candidates, a status per checklist surface, and "Not reached" lines.
The agent wrote a free-form summary page instead. It had overview, findings, open points and verification.
The page had no severity ranks, no path and line per finding, no dropped candidates and no surface status.

The skill says to write the report to a file only when the caller asks for one.
The user asked for a document. The skill does not say how the report shape carries into a document.
typo3_rule_lookup with the query "review report" returned no template. It returned security and rebase sections.
typo3_task_guide says it names the guide that writes the work up. For a review it named none.

## Query

typo3_rule_lookup query "review report"; task text "schreib mir bitte ein dokument als zusammenfassung" after a review of change 97164.

## Suggestion

Add a guide for the review write-up, for example core/review/the-review-report.
Give it the section order of the skill: blocking, sent back, worth a change, checked and correct, dropped candidates, surfaces, not reached.
Give it the five parts of a finding as fields.
Let typo3_task_guide name this guide when the change type is audit or the task is a review.
Say in the review skill that a document the user asks for uses the same shape as the chat report.
