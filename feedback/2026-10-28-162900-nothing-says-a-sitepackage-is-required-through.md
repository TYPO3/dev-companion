---
date: 2026-10-28T16:29:00+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/lina/projects/typo3/projects/jambo-pickle
---

# nothing says a sitepackage is required through composer before it renders

## Observation

An opencode run reported the page ready. The site answered #1607585445, "No page configured for type=0".
The sitepackage itself is complete. packages/jambo_site carries a manifest, a set that imports its TypoScript, and a page object.
config/sites/main/config.yaml depends on jambo/jambo-site.
The project's own composer.json was never changed, and vendor/jambo does not exist. So the extension is not installed, the set does not exist, and no TypoScript loads.
No hint below knowledge/ names that step. A search for "composer require" over knowledge/ finds nothing.
The contract scenarios/contracts/site-developer/site-10 asks for an extension the project requires through Composer. The contract says it and the knowledge does not.

## Query

typo3_hint_lookup with id=sitepackage-start.

## Suggestion

Say it in sitepackage-start. A package below packages/ renders nothing until the project requires it, and a path repository alone installs nothing. Name the command. Say that the site configuration's dependency resolves only where the extension is active.
