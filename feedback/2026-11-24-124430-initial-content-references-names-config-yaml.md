---
date: 2026-11-24T12:44:30+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5
tool: typo3_hint_lookup
---

# initial-content-references names config.yaml but not page uids in a site set settings.yaml

## Observation

Stage 04 of the same recordings read initial-content-references through typo3_hint_lookup (ids sitepackage-initial-content, datahandler-seeding, impexp-artifact, initial-content-references, initial-content-import-once) and then shipped the page tree as initial content.

Stage 09 tested a fresh install and found the site settings pointing at the wrong pages: the search form went to page 20 instead of 16, the two menu roots (22, 23) and the 404 page (33) as well. Every news item also got the category Education. Stage 10 fixed it by reading SiteWriter and SiteConfiguration in vendor/typo3 (10 reads), with no further call to this server beyond project_describe and the rule booting-a-clone.

The hint says that every reference in the shipped config.yaml other than the root page keeps its exported uid. The uids here were in the site set's settings (settings.yaml / site settings), which the hint does not name, so the session may have read it as not concerning them. That reading is a guess.

## Suggestion

Name settings.yaml and site set settings of type page beside config.yaml in initial-content-references, and say what a package does instead: resolve the page after the import (for example by a page's slug or a marker field in an initialization listener) and write the setting with SiteWriter, or avoid uids in settings. The category default of news belongs in the same list if it was a plain uid.
