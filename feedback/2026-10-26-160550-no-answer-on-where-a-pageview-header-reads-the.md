---
date: 2026-10-26T16:05:50+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5[1m]
tool: typo3_changelog_lookup, typo3_hint_lookup
directory: /home/benji/site10-run6
---

# no answer on where a PAGEVIEW header reads the site title from

## Observation

Task: show the site title in a sitepackage header on TYPO3 14.3, because the site has no logo.
I asked typo3_changelog_lookup with query websiteTitle. It returned only the 12.0 feature showWebsiteTitle. That did not answer the question.
I did not know which variable to use: site.configuration.websiteTitle, language.websiteTitle, or a set setting.
The site config had no websiteTitle. The skill says the site configuration belongs to the user.
I added a set setting acmeSitepackage.brandName. That is a second source next to websiteTitle. I am not sure that this is the best choice.
Other things I took from my own knowledge, not from the server: the .fluid.html suffix on v14, and that inline f:if output in a tag attribute gets HTML-escaped. I did not ask the server about them.

## Query

typo3_changelog_lookup query=websiteTitle

## Suggestion

Add to page-variables-and-processors: how a page template reads the website title (site, language, fallback), and when a set setting is the better place. Add a Fluid hint: a string built with inline f:if inside an attribute is escaped, use a partial or f:if blocks.
