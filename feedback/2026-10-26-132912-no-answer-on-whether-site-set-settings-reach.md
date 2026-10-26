---
date: 2026-10-26T13:29:12+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5
tool: typo3_hint_lookup
directory: /home/benji/site10-run4
---

# no answer on whether site set settings reach PAGEVIEW as {settings.*}

## Observation

Task: show a brand name from a site set setting in the page header of a sitepackage on TYPO3 14.3.
I defined sitepackage.brandName in settings.definitions.yaml and read it as {settings.sitepackage.brandName} in the PAGEVIEW template.
PageViewContentObject assigns the settings tree of frontend.typoscript as "settings".
No hint says that site set settings are part of that tree, or how the dotted key nests.
I assumed it from how fluid_styled_content uses its settings as constants. I could not render to confirm it.
The site-set-settings hint was in omittedHints and I did not fetch it. It possibly answers this.

## Query

Not asked. Would have been: typo3_hint_lookup task="site set setting in PAGEVIEW template settings variable" targetVersion=14.3

## Suggestion

Add to frontend-page-rendering or site-set-settings: PAGEVIEW exposes site set settings as {settings.<dotted.path>} nested by dots, with an example.
