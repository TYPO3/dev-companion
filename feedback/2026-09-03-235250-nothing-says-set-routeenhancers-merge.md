---
date: 2026-09-03T23:52:50+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_task_guide
directory: /home/benji/projects/site-tierheim
---

# nothing says set routeEnhancers merge recursively, so a partial enhancer is possible and fragile

## Observation

Task: add an XML sitemap for a custom record table to a TYPO3 v14 project, which meant adding one entry to the StaticValueMapper map of the Sitemap route enhancer that the core set typo3/seo-sitemap ships.

route-enhancers.yaml in a site set is named by the site-sets hint as a file a set may hold, and that is all. Nothing says how two sets that both contribute route enhancers combine. That is the decisive fact for this task: SiteConfiguration::getRouteEnhancersFromSets() folds them with ArrayUtility::replaceAndAppendScalarValuesRecursive() per set and then merges the site's own on top the same way. Because the merge is recursive, my set could declare only

  Sitemap:
    aspects:
      sitemap:
        map:
          animals: animals

and that single entry lands inside the enhancer typo3/seo-sitemap declared, without wiping its `pages` entry, its type or its routePath. Had the merge replaced per identifier instead, I would have had to restate the whole enhancer and would have silently overwritten the core set's definition. I only established which of the two it was by reading SiteConfiguration.

The same fact has a sharp edge nobody warned me about. A partial enhancer is only valid while the depended-on set is loaded. When I removed typo3/seo-sitemap from the dependencies for an isolation test, the fragment was all that was left and the site answered HTTP 500 with "Enhancer type cannot be empty" from EnhancerFactory - a message that says nothing about sets or dependencies. So the set dependency is load-bearing for the enhancer fragment, and that coupling is invisible in the route-enhancers.yaml itself.

The exception codes around this code (1764749081-1764749083 in YamlSetDefinitionProvider) look recent, which fits: route-enhancers.yaml in sets appears to be new enough that the conventions have not caught up with it.

## Query

typo3_hint_lookup id=site-sets and typo3_task_guide changeType="feature" targetVersion="14" task="Add XML sitemap to a TYPO3 site: include the typo3/seo-sitemap site set, register a records sitemap provider for a custom table, and add a robots.txt route to the site configuration" - neither answered how route enhancers from two sets combine, nor that a partial enhancer depends on the set supplying the rest.

## Suggestion

Extend site-sets, since 13, with two statements about route-enhancers.yaml:

1. Route enhancers from sets are merged recursively (ArrayUtility::replaceAndAppendScalarValuesRecursive) in dependency order, and the site's own routeEnhancers are merged over the result. So a set can contribute a fragment into an enhancer another set declares - one entry into an aspect's map, for instance - rather than having to restate the whole identifier.

2. Because of that, a fragment is valid only while the set supplying the enhancer's type is a dependency. Declare that dependency; without it the fragment reaches the router alone and every request fails with "Enhancer type cannot be empty" (EnhancerFactory), which names neither the set nor the missing dependency.

Both belong in the hint rather than only in the documentation, because the second one is a failure mode read backwards from an unrelated-sounding message.
