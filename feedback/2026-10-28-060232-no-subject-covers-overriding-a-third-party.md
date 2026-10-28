---
date: 2026-10-28T06:02:32+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# no subject covers overriding a third-party plugin's Fluid templates, the ordinary way to restyle one

## Observation

Task: make the search work on a sitepackage site, TYPO3 14.3, with indexed_search.

I had to make the plugin's output match the design. No subject covers that. A query about overriding a plugin's Fluid templates returns a hint about breaking changes at the top. It carries nothing of the question.

The corpus does carry template root knowledge. All of it warns about collisions. sitepackage-templates warns about the shared partial root of fluid_styled_content. page-content-element-rendering warns about the page frame against the content element frame. Both are scoped to lib.contentElement and PAGEVIEW.

I read them as a general hazard. So I styled the extension's markup with CSS rather than replace it. That kept the extension's own labels, which read "Search form" and "Search for:". It made me hide a legend, and hide a whole information block with display none. That block held the result path, which a visitor wants.

The link to the advanced search is the third case. Behind it stand eight select elements and a checkbox. A thirteen page council site needs none of them. CSS hides the link and leaves the form and its route reachable. Only the template decides whether that link is written at all.

A model without this server pointed plugin.tx_indexedsearch.view.templateRootPaths at the sitepackage. Its page carries its own labels and keeps the path. The user compared the two and called that one better. The comparison is correct.

Part of this is mine. I wrote the stylesheet from a list of class names, never from the markup, and I never opened the page.

## Query

typo3_hint_lookup task='override the Fluid templates of a third party extension plugin so its frontend output matches the sitepackage design' targetVersion=14. Earlier in the same session: ids=[sitepackage-templates, page-content-element-rendering, content-rendering-templates].

## Suggestion

Add a subject for the view paths of a third-party plugin. They are plugin.tx_<key>.view.templateRootPaths, partialRootPaths and layoutRootPaths. Say that those roots belong to that plugin alone. Say that they do not collide with lib.contentElement or with PAGEVIEW. Name the trade: a copied template drifts from the extension, and CSS cannot change the text the extension writes.

Say what CSS cannot reach: the text the extension writes, the fields it offers, and the links it renders to its own actions.

Say in sitepackage-templates which roots its collision warning covers. A warning scoped to one object reads as a rule about every root.
