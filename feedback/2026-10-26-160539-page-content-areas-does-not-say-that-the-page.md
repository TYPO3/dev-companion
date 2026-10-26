---
date: 2026-10-26T16:05:39+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/site10-run6
---

# page-content-areas does not say that the page-content processor must be configured

## Observation

Task: write PAGEVIEW page templates that address {content.main} and {content.aside} on TYPO3 14.3.
The hint page-content-areas says that the content arrives as one variable, named by the contentAs option.
It does not say which object owns contentAs. It does not say that the page needs dataProcessing.<n> = page-content.
page-variables-and-processors lists the menu processor but not page-content.
I kept the builder's line "page-content" because it was in the generated file. Without the builder I would have had to guess. The answer was right and stopped one step short.

## Query

typo3_hint_lookup ids=[page-content-areas, page-variables-and-processors] targetVersion=14.3

## Suggestion

Add one statement to page-content-areas: PAGEVIEW gets the content areas from dataProcessing.<n> = page-content, and contentAs is an option of that processor. Say what the template gets when the processor is missing.
