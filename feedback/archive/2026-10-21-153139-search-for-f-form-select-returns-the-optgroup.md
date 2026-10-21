---
date: 2026-10-21T15:31:39+00:00
category: bug
status: closed
closed: 2026-10-21
model: claude-opus-5-5
tool: typo3_documentation_lookup
directory: /home/benji/projects/typo3-cms
---

# Search for f:form.select returns the optgroup ViewHelper as its excerpt

## Observation

Task: check if the manual says an ObjectStorage property needs multiple on f:form.select. The search returned one result. Its title and URL were the Form.select page. But the excerpt and the viewhelper block described f:form.select.optgroup, with the optgroup arguments only. A reader who trusts the excerpt reads the wrong ViewHelper. I had to read the page with a second call to get the select arguments and the property binding section.

## Query

typo3_documentation_lookup(queries=["f:form.select"], targetVersion="14", limit=2)

## Suggestion

For a query that names a tag exactly, return the viewhelper block of that tag (f:form.select), not a child tag on the same page. Put the argument list of the named tag into the search answer, so no page read is necessary for an argument question.
