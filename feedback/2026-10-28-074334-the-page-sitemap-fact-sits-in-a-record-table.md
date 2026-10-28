---
date: 2026-10-28T07:43:34+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup, typo3-sitepackage-development
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# the page sitemap fact sits in a record table subject, and nothing prompts an SEO pass

## Observation

Task: build a sitepackage site on TYPO3 14.3, then judge it for search engines.

I required typo3/cms-seo and /sitemap.xml answered 404. The corpus states that case exactly. record-xml-sitemap says that requiring typo3/cms-seo leaves /sitemap.xml at 404 until a set of the site depends on typo3/seo-sitemap.

That hint is titled "An XML Sitemap of a Record Table". It sits in the records subject, and its other statements are about RecordsXmlSitemapDataProvider. A session building a sitemap of pages does not look there. I did not reach it.

Nothing else covers the subject. hreflang appears nowhere in the knowledge base. Meta description appears nowhere. No skill names SEO, a sitemap, a canonical or a heading level.

So no step of the sitepackage workflow asks whether the pages can be indexed. The user asked after the build was finished. The home page carried no h1 in either language. Every page had an empty description column beside a filled abstract. Each subpage rendered an h2 before its h1.

A query about this answers at 30 percent.

## Query

typo3_hint_lookup task='make the pages of a sitepackage site search engine friendly: an XML sitemap, meta descriptions and one h1 per page' targetVersion=14 limit=3. Then ids=[record-xml-sitemap] targetVersion=14.

## Suggestion

Add an SEO pass to the sitepackage skill, beside the step that proves a page renders. It reads the rendered head and the heading order: one h1 per page, a description that is not the abstract column, the canonical, the hreflang set, and whether /sitemap.xml and /robots.txt answer. Each of those is read off a response rather than off a file.

Say that a sitepackage on a multilingual site owes hreflang and a description per language. Nothing in the corpus names either today.
