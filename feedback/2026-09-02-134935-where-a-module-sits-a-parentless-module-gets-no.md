---
date: 2026-09-02T13:49:35+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_backend_module_lookup
directory: /home/benji/projects/site-tierheim
---

# Where a module sits: a parentless module gets no routes, and after:'*' does not work alone

## Observation

Task: same session — moving one backend module around the v14.3 module tree while the person I worked for changed their mind about where it should sit.

Two behaviours cost me a round trip each, and neither is in the backend-modules hint.

1. A module registered WITHOUT a `parent` is treated as a container: its `routes` are not registered at all. I had removed the parent to make it a single top-level entry. typo3_backend_module_lookup then reported the module with `"routes":[]`, and the backend silently fell back to rendering the Layout module for /module/animalshelter/animals — no error, no log entry. The fix is core's "Media" pattern: keep a parent and give it `appearance.promotesSingleSubmoduleToStandalone => true`. I found that by reading EXT:core Configuration/Backend/Modules.php.

2. `'position' => ['after' => '*']` did not put a single module last under "Content"; it landed third. Reading EXT:backend Classes/Module/ModuleRegistry.php::applySorting() explains it: modules with after:'*' go into $lowPriorityModules, are populated early, and the block that moves them to the end is guarded by `$firstLowPriorityModule !== $lastLowPriorityModule` — so with exactly one such module the move is skipped. The same method carries a core @todo asking whether an empty position should imply after:'*'.

What actually works, and what I ended with: state no position at all. A module without one keeps its registration order, and a project package loads after the core ones, so it is last anyway. Naming the last neighbour (`after: 'web_FormFormbuilder'`) also works but ties the file to an extension the package does not require.

All three facts came from reading core source. The hint carries the registration keys but nothing about what they resolve to.

## Query

typo3_backend_module_lookup(query="animalshelter") repeatedly while changing Configuration/Backend/Modules.php: parent removed (routes came back empty), parent restored with promotesSingleSubmoduleToStandalone, parent changed to 'content' with position after:'*', then after:'web_FormFormbuilder', then no position.

## Suggestion

Three sentences in the backend-modules hint would have saved both round trips:

- A module without `parent` is a container and gets no route; a single standalone entry is a parent whose one submodule is promoted with appearance.promotesSingleSubmoduleToStandalone.
- `position: ['after' => '*']` only reaches the end when more than one module claims it (ModuleRegistry::applySorting); a module with no position keeps its registration order, which for a project package is already last.
- `inheritNavigationComponentFromMainModule => false` is how a module under "content" avoids inheriting the page tree (EXT:form uses it). I needed this too and found it by reading EXT:form.

typo3_backend_module_lookup is what diagnosed all of this after the fact and it did that well — its `routes` and `position` fields are exactly the evidence. The gap is that nothing warns before the file is written.
