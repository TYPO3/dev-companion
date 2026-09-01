---
date: 2026-09-01T21:04:11+00:00
category: missing-knowledge
status: closed
closed: 2026-09-01
model: claude-opus-5
tool: typo3_hint_lookup, typo3_backend_module_lookup, typo3_configuration_lookup
directory: /home/benji/projects/typo3-cms
---

# AjaxRoutes are registered without the ajax_ prefix but addressed with it, and bare again in JS

## Observation

Migrating a local memory into the server: this is knowledge the server does not answer today, and it is a three-way naming asymmetry that is guessed wrong in every direction.

One backend AJAX endpoint carries its route name in three spellings:

1. In Configuration/Backend/AjaxRoutes.php the route is registered WITHOUT the ajax_ prefix, for example 'clearcache_flush'.
2. UriBuilder::buildUriFromRoute() must be called WITH the prefix, for example 'ajax_clearcache_flush'. TYPO3 maps an ajax_* name passed to buildUriFromRoute back onto the corresponding entry in AjaxRoutes.php.
3. TYPO3.settings.ajaxUrls in JavaScript and TypeScript uses the BARE route name again, no prefix, for example ajaxUrls.clearcache_page.

Getting any of the three wrong fails only at runtime: the PHP side throws a route-not-found, and the JS side quietly reads undefined out of ajaxUrls and requests the current URL instead, which looks like an unrelated bug.

## Query

Migrated from a local project memory file (the "AJAX Routes Convention" section of MEMORY.md). Learned while wiring backend AJAX endpoints and their TypeScript callers.

## Suggestion

Add a hint matched by paths ending in Configuration/Backend/AjaxRoutes.php and by queries like "ajax route", "ajaxUrls", "buildUriFromRoute". It should state all three spellings side by side as a table rather than describing the mapping in prose, because the value of this is being able to check one call site against the other two. Worth naming the failure mode for each side, since neither is a clear error.
