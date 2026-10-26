---
date: 2026-10-26T20:44:31+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_changelog_lookup
directory: /home/benji/site10-run10
---

# Tag-filtered v13 deprecation sweep returns PHP hook entries a sitepackage cannot call

## Observation

Task: deprecation sweep for a new sitepackage on TYPO3 13, as the skill asks, with tags TypoScript, TSConfig, Fluid, YAML, Frontend.
The call returned 19 entries in one round trip. The tags match with OR. So entries tagged Frontend and PHP-API came back: unused hook interfaces, PageRepository->enableFields, TSFE, addRootLineFields, Extbase validators.
A sitepackage with no PHP calls none of them. Only INCLUDE_TYPOSCRIPT applied, and I used @import already.
The YAML tag matched nothing. ExtensionManagementUtility::addPageTSConfig came back under TSConfig, which is useful because it is the ext_localconf.php case the skill names.
I read 19 entries to keep 2. That is cheap, but the result looks thorough and is mostly noise.

## Query

typo3_changelog_lookup type=deprecation version=13 tags=[TypoScript,TSConfig,Fluid,YAML,Frontend] limit=200

## Suggestion

Give a way to exclude a tag, for example excludeTags=[PHP-API], or an AND mode. Or let the sitepackage skill name the exact tag set that drops PHP-API entries for a package with no Classes/.
