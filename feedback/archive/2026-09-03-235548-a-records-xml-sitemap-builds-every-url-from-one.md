---
date: 2026-09-03T23:55:48+00:00
category: missing-knowledge
status: closed
closed: 2026-09-04
model: claude-opus-5[1m]
directory: /home/benji/projects/site-tierheim
---

# A records XML sitemap builds every URL from one pageId, and no hint says so

## Observation

Task: audit a project site page by page and say what is broken. The worst defect I found was in a feature the repository had added two commits earlier - an XML sitemap for a record table via TYPO3\CMS\Seo\XmlSitemap\RecordsXmlSitemapDataProvider, configured under plugin.tx_seo.config.xmlSitemap.sitemaps with table, pid, additionalWhere and url.pageId from a site setting.

The setting pointed at a page that carried no plugin for those records. All 3.101 entries therefore answered HTTP 200 with that page's section menu and no record on it, and the sitemap advertised every one of them. Nothing raises anything: the provider builds addresses out of a pageId it never checks, and the page renders happily without the argument it was handed.

I found this by crawling the sitemap and reading the responses, not from any lookup. The hints index carries record-routing and record-page-title for a record's detail view and neither mentions the sitemap; there is no hint about the records provider at all. The two facts that make the defect are written down nowhere I could reach:

1. url.pageId is one page for the whole table, so a records sitemap can only carry records that this one page renders. Where the detail view lives on several filtered list pages, the sitemap and the detail view have to be narrowed to the same selection or they disagree.
2. additionalWhere is the only lever for that narrowing and defaults to nothing, so a provider given hidden=0 alone will list records no page shows.

The repair was to put a matching list on the configured page and narrow additionalWhere to the same status, reasoned out from the TypoScript and the flexforms of nine content elements.

What would have helped: extending the record-detail family with the sitemap, or a hint id such as record-xml-sitemap - what the provider needs, that its addresses are never verified against what the page renders, that additionalWhere is where the record selection is repeated and therefore where it silently diverges from the plugin's own filter, that the frontend restrictions do not apply to its query, and that the check which proves it is one request per sample address, read for whether the record is in the response rather than for a 200.

## Query

Session task: "walk the pages of the site and report which pages carry errors and which are incomplete", against a TYPO3 14.3.6 project installation. No lookup was offered for RecordsXmlSitemapDataProvider; typo3_hint_lookup around record detail views returns record-routing and record-page-title, which stop before the sitemap.
