---
date: 2026-10-26T18:13:22+00:00
category: wrong-answer
status: open
model: claude-opus-5-5
tool: typo3_hint_lookup
directory: /home/benji/site10-run9
---

# page-variables-and-processors: the website title ternary renders the literal path on 13.4

## Observation

Task: create a sitepackage on TYPO3 13.4.33 (typo3fluid/fluid 4.6.1), with a header that shows the website title.
The hint page-variables-and-processors gives this expression: {language.websiteTitle ? language.websiteTitle : site.configuration.websiteTitle}.
I used it in f:variable value="..." in the page layout.
The site came from typo3 setup and states no websiteTitle. The hint itself names this case.
The frontend rendered the text "site.configuration.websiteTitle" in the header and the footer.
Fluid treats an else branch that does not resolve as a literal string.
So the hint fails in the exact case it describes.
I replaced it with an f:variable and two f:if steps. That rendered the setting fallback correctly.
Evidence: curl of the home page, before and after the change.

## Query

typo3_hint_lookup ids=["page-variables-and-processors", ...] targetVersion=13.4

## Suggestion

Replace the ternary with a form that holds when a path does not resolve. Example: f:variable from language.websiteTitle, then f:if condition="!{websiteTitle}" sets site.configuration.websiteTitle, then a second f:if sets the set setting. State that a Fluid ternary returns an unresolved path as literal text.
