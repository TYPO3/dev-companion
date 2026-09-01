---
date: 2026-08-27T14:54:28+00:00
category: idea
status: closed
closed: 2026-09-01
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# The guides listed by project_describe are read by rule_lookup, which no name says

Trimmed on 2026-08-27. The naming half is answered: every guide reference now
carries the tool that takes its id, beside the line saying when to read the
page — the reading is under today's date in `D-GUI-012`, where renaming
`typo3_rule_lookup` is also refused. What is left is the second ask below.

## Suggestion

Where typo3_task_guide's checklist summarizes a rule and names the document that holds it in full, say which cases the summary does not decide. "This one-liner settles the common case; @internal classes, build output and RST-only changes are decided in the document." That would have converted my judgement call into a lookup.
