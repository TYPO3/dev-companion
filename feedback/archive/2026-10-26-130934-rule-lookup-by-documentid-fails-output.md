---
date: 2026-10-26T13:09:34+00:00
category: bug
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_rule_lookup
directory: /home/benji/site10-run2
---

# rule_lookup by documentId fails output validation for guides with scope "any"

## Observation

Task: create a sitepackage on TYPO3 14.3.5 and render one page per page layout. The skill typo3-sitepackage-development tells the agent to read documentId="any/testing/browser-check" at the render step. typo3_project_describe lists that guide with scope "any". The call typo3_rule_lookup with documentId="any/testing/browser-check" returned an error and no document. The error text was: "Invalid structured output for tool 'typo3_rule_lookup': Property '/scope': Value must be one of the allowed values: "core", "uncertain", "project", "extension"." So the output schema does not allow the scope "any" that the guide index uses. All guides with the "any/" prefix are probably not readable by documentId. A query search ("browser check rendering page") worked and returned sections of the same document. That search returned only one section of the guide, not the full procedure.

## Query

typo3_rule_lookup {"documentId":"any/testing/browser-check"}

## Suggestion

Add "any" to the scope enum of the typo3_rule_lookup output schema. Alternatively, map the scope "any" to a value the schema allows. Add a test that reads each documentId that typo3_project_describe lists.
