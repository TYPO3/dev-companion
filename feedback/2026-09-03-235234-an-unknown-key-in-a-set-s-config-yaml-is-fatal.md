---
date: 2026-09-03T23:52:34+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/site-tierheim
---

# an unknown key in a set's config.yaml is fatal, and site-sets reads as if it were ignored

## Observation

Task: set up an XML sitemap and a robots.txt for a TYPO3 v14 project site, and answer whether robots.txt can live in a site set instead of the site configuration.

The site-sets hint lists what a set directory may hold: "Next to config.yaml the set may hold setup.typoscript, settings.definitions.yaml, settings.yaml, labels.xlf, page.tsconfig and route-enhancers.yaml. Only config.yaml is required." That is accurate and it was the right answer to the user's question. What it does not say is what happens to a key the list does not cover, and the sentence leaves "unknown keys are ignored" as the natural reading.

The truth is far harsher. YamlSetDefinitionProvider::createDefinition() spreads the parsed config.yaml straight into the SetDefinition constructor (`new SetDefinition(...$setData)`), and SetDefinition is a readonly class with a fixed parameter list. So an unknown key is a PHP \Error, wrapped as InvalidSetException. I proved it by putting a `routes:` key into a project set's config.yaml: the set became invalid, and the site's whole frontend answered HTTP 500 with "Invalid set definition: {...} - Invalid properties: routes".

That matters because the failure is not local to the unsupported feature. One stray key in one set takes down every page of every site depending on it, and the exception names "Invalid properties" rather than "this key does not exist in a set", so the cause is not obvious from the message. A person experimenting with a set - exactly what the user was asking me to do - hits a white screen rather than a warning.

## Query

typo3_hint_lookup task="XML sitemap configuration, records sitemap provider for a custom table, robots.txt route in site configuration, site sets and settings definitions" targetVersion="14" paths=["packages/animalshelter_animals/Configuration/Sets/Animals/setup.typoscript","config/sites/main/config.yaml"] limit=8 - returned the site-sets hint, whose file list is correct but silent on unknown keys. Reproduce the failure with a set config.yaml carrying any key outside the SetDefinition constructor, for example routes:.

## Suggestion

Add one statement to site-sets, since 13: the keys a set's config.yaml may carry are exactly the SetDefinition constructor parameters (name, label, dependencies, optionalDependencies, settings, hidden, plus the ones derived from the sibling files), and any other key raises InvalidSetException with "Invalid properties: <key>" rather than being ignored - which invalidates the set and makes every site depending on it fail to render. That single sentence turns "these files are supported" into "and nothing else is tolerated", and it names the message to search for.

It would also settle the neighbouring question without an experiment: static routes are a site configuration key (routes), read by the StaticRouteResolver middleware from the site's own config.yaml, and there is no set equivalent - so a set cannot ship one. The hint already says which sources a site entity has; naming routes as one that stays with the site would close the gap that sent me to the source.
