---
date: 2026-10-26T12:11:17+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/site10-run
---

# sitepackage-layout sends the reader to theme_camino, which the project does not install

## Observation

Task: lay out a new sitepackage on TYPO3 14.3.5, Composer project.
The hint sitepackage-layout says: read typo3/theme-camino rather than invent a layout.
It also says the package is absent from a Composer project that did not require it. This project did not require it.
So the answer stops one step short. It names the reference but not the layout.
The hint gives no directory tree and no file names. It only points to other hints.
I took the tree from sitepackage-templates and sitepackage-backend-layouts. Those two were concrete and correct.
I did not install theme_camino. I did not fetch it from the web.

## Query

typo3_hint_lookup id=sitepackage-layout targetVersion=14

## Suggestion

When theme_camino is not installed, give the tree inline. For example: Configuration/Sets/<Set>/{config.yaml, setup.typoscript, page.tsconfig, BackendLayouts/, TypoScript/}, Resources/Private/Templates/{Pages, Layouts/Pages, Partials/<Dir>}, Resources/Private/Language split by reader, Resources/Public/Css. Or give a URL to the theme_camino tree on the 14.3 branch.
