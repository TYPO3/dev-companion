---
date: 2026-09-03T23:53:15+00:00
category: missing-knowledge
status: closed
closed: 2026-09-04
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/site-tierheim
---

# no hint covers the XML sitemap, and RecordsXmlSitemapDataProvider has three traps that cost me a bug

## Observation

Task: give a TYPO3 v14 animal shelter site an XML sitemap that includes 3101 records of a custom table whose detail views live on filtered list pages.

typo3_hint_lookup with an explicit sitemap query returned bestCoverage 0.26 and handed me site-sets, site-label-language, frontend-records and routing-request-handling. Nothing in the bundled conventions covers XML sitemaps at all, and the availableHints index has no candidate either. The live documentation lookup carried the subject well, so I was not stuck - but the three things that actually bit me are not in that documentation, and they are exactly the shape of a hint.

1. RecordsXmlSitemapDataProvider adds only a WorkspaceRestriction to its query. No hidden, no starttime, no endtime. So every visibility rule the frontend applies has to be restated in additionalWhere or the sitemap advertises records no visitor can see. Worse, XmlSitemapRenderer reads the sitemap configuration as a plain array (TypoScriptService::convertTypoScriptArrayToPlainArray) with no stdWrap, so additionalWhere is a static string and a starttime/endtime comparison cannot be expressed portably at all. That is a dead end worth naming before somebody tries.

2. url.pageId is read as `$this->config['url']['pageId'] ?? $pageId`. A site setting whose default is 0 - the idiomatic default for "no page chosen" - therefore does not fall back to the request's page; it is taken as page id 0 and every generated URL is broken. The `??` makes an integer 0 indistinguishable from a deliberate choice.

3. The conceptual one, which caused a real defect in my work. The records provider addresses a whole table against one configured page. That is wrong for any record whose detail view is rendered by a list plugin that filters: the list answers for the record only where its own selection holds it. On this site nine list elements select by status, species and age, so pointing the whole table at one page put 3033 of 3101 URLs onto a page that answered with the list instead of the record - each one a near duplicate carrying a self-referencing canonical. I shipped that, verified it against the installation, and had to replace it with a provider of my own that resolves the page per record off the configured elements.

Trap 3 is the one that matters most, because it produces a sitemap that looks entirely healthy - HTTP 200 on every URL, no duplicate addresses - while being actively harmful.

## Query

typo3_hint_lookup task="XML sitemap configuration, records sitemap provider for a custom table, robots.txt route in site configuration, site sets and settings definitions" targetVersion="14" limit=8 -> bestCoverage 0.26, no sitemap hint, none offered in availableHints. Same for typo3_task_guide with the sitemap task text.

## Suggestion

Add a hint, say xml-sitemap, covering EXT:seo's sitemap for a project's own table. The three statements above, plus two more I needed:

- The sitemap arrives only through the site set typo3/seo-sitemap; requiring typo3/cms-seo installs the extension but leaves /sitemap.xml a 404 until a set depends on that one. The set brings the PageType decorator (sitemap.xml -> 1533906435) and the Sitemap enhancer with it.
- A provider is constructed by XmlSitemapRenderer through GeneralUtility::makeInstance($class, $request, $key, $config) with positional arguments, so it cannot take services through the container. A service it fetches itself has to be public: true, or makeInstance constructs it with no arguments and the request fails with ArgumentCountError. (di-service-not-found exists and I did not call it - but the sitemap hint is where somebody writing a provider will be looking.)

And for the design trap: say that the records provider fits a table whose records all live on one detail page, and that a record reachable on several filtered list pages needs a provider of its own extending AbstractXmlSitemapDataProvider, resolving the page per record. The documentation names the extension point; what it does not say is when the shipped provider is the wrong tool.
