---
date: 2026-09-28T11:23:08+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_backend_module_lookup, typo3_changelog_lookup, typo3_component_lookup
directory: /home/lina/projects/typo3/manuals/coreapi
---

# no route to a version-exact Core source fact, so four claims were checked by reading vendor

## Observation

Task: review a documentation PR that explains how an Extbase backend module differs from a frontend plugin, and verify its factual claims for main and 14.3.

Four claims needed checking. I answered three of them by reading the vendored Core in .Build/vendor/typo3/cms-* and never asked this server, because its tool list reads as a surface over an installation's registries and configuration - component, icon, label, schema, service, changelog, configuration lookups - and none of the names suggested "where in Core is this implemented, on this version". What I read: cms-backend/Classes/Module/ExtbaseModule.php (keys _default and <alias>_<action>), cms-backend/Classes/Module/ModuleRegistry.php line 126 (addNamePrefix with the module identifier and a dot), cms-extbase/Classes/Mvc/Web/RequestBuilder.php (sets useArgumentsWithoutNamespace for an ExtbaseModule and then reads getQueryParams and getParsedBody unprefixed), cms-extbase/Classes/Configuration/BackendConfigurationManager.php (module.tx_<extension>. overlaid with strtolower(extension _ plugin)), cms-extbase/Classes/Core/Bootstrap.php (pluginName set to the module identifier), cms-extbase/Classes/Utility/ExtensionUtility.php (alias strips the Controller suffix). About six read-only calls, version-exact, no ambiguity.

That is a finding in two directions. The server was not asked, so it cannot see the need: a session reviewing documentation asks "how does Core do X on this version", and the only tool that fitted was typo3_backend_module_lookup, which I reached from its name after the peer session suggested the server at all. Once the installation ran, that tool answered claim 1 better than the source did, because a live registry is evidence and source is inference.

I assumed source-level questions were out of scope. I do not know whether that assumption held - and per the debrief request, a wish dropped as out of scope is the one maintainers never hear.

## Query

Task: review docs PR 7131 in TYPO3CMS-Reference-CoreApi, "How Extbase Backend Modules work - part 1". Claims to verify: (1) sub-routes named "<module identifier>.<Controller>_<action>" with _default for the first action, (2) module arguments are read without the plugin namespace, (3) configuration comes from module.tx_myextension_<module identifier>, (4) the module identifier stands in where a plugin request has the plugin name. No server call was made for 2, 3 or 4.

## Suggestion

Either a lookup that answers "which class, and which line, implements this behaviour on the version this checkout has" - the answer I assembled by hand for route naming, argument namespacing and the module TypoScript scope - or one sentence in the server instructions saying that source-level questions are not its subject, so a session stops wondering and goes to the checkout at once.

Second, smaller point: typo3_backend_module_lookup's description sells it as a list of registered modules, and its strongest property only became clear after using it - it reports the route and every sub-route as the registry resolved them, which is exactly what a documentation claim about route naming needs, and it also shows the contrast with a non-Extbase module. Saying that in the description ("the route identifiers a module registers, as resolved, which registration files cannot give you") would have pulled me to it before I read any source.
