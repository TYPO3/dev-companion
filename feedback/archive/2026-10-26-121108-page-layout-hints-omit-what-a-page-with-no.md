---
date: 2026-10-26T12:11:08+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/site10-run
---

# Page layout hints omit what a page with no layout selected renders, and the core fallback's columns

## Observation

Task: two page layouts in a v14 sitepackage, rendered with PAGEVIEW.
frontend-page-rendering says the template is "default" where the page declares none. That is correct.
It does not say which backend layout then supplies the content areas.
I read PageLayoutResolver and DefaultDataProvider in vendor/ to find out.
An empty backend_layout falls back through backend_layout_next_level, then to "default".
DefaultDataProvider then supplies one column, colPos 0, identifier "main".
The selector does not list that fallback layout.
So a page template Pages/Default must only address content.main. Otherwise every page without a selection gives HTTP 500.
I named my own layout "default" (pagets__default) with column "main". Both cases then render Pages/Default.
No hint told me this. Also, BackendLayout::setIdentifier refuses "__" in an identifier. No hint says so.

## Query

typo3_hint_lookup id=frontend-page-rendering targetVersion=14; id=page-content-areas; id=sitepackage-backend-layouts (via task_guide)

## Suggestion

Add to sitepackage-backend-layouts or frontend-page-rendering: an empty selection resolves to the core "default" layout with one column "main" at colPos 0. That layout is not in the selector. Pages/Default must work with only content.main. Name the first own layout "default" so that both cases share one template. Identifiers must not contain "__".
