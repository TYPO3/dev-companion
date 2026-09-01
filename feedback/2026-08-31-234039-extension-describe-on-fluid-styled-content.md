---
date: 2026-08-31T23:40:39+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_extension_describe, typo3_hint_lookup
directory: /home/benji/projects/site-tierheim
---

# extension_describe on fluid_styled_content never mentions lib.contentElement, the only reason to ...

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14 with an own sitepackage; the user had explicitly chosen "Eigenes Sitepackage" over a framework at the start. His closing observation: "du hast das bootstrap_package vermieden, aber dann fluid_styled_content genommen."

He is right and the inconsistency is exactly as he states it. What I built: config/sites/main/config.yaml depends on typo3/fluid-styled-content AND typo3/fluid-styled-content-css; every one of my seven content elements is `tt_content.animalshelter_x =< lib.contentElement`; and page.tsconfig then removes every fluid_styled_content element from the CType selector with keepItems. So the site takes the framework's rendering scaffolding while refusing everything the framework renders, and ships fluid-styled-content-css into a site that has 1100 lines of CSS of its own and no fsc elements left to style.

At 21:12:32 I called typo3_extension_describe(extension="fluid_styled_content"). The answer is accurate and complete for what it reports: tcaTables [], contentElements [], siteSets typo3/fluid-styled-content and typo3/fluid-styled-content-css with their files, fluidRoots, typoScript ["constants.typoscript","setup.typoscript"], one ViewHelper class.

It never names lib.contentElement. That object is the single reason a sitepackage depends on this extension at all, and the answer that describes the extension does not contain it — because it is a TypoScript object inside setup.typoscript, and the describe answer reports the file rather than what is in it. I read "contentElements: []" and drew no conclusion; had the answer said "provides lib.contentElement, which project content elements copy from", the dependency would have become a visible decision rather than a habit.

So: correct answer, one step short of the thing the caller is asking about when they ask about this extension.

The related question I never put to the server at all — whether a sitepackage can define its own lib.contentElement and drop the dependency, and what it loses if it does (the stdWrap chain, the data processing defaults, the layout/frame partials) — I did not ask because I did not notice I had taken the dependency. It is still open in the repository.

## Query

typo3_extension_describe(extension="fluid_styled_content") at 21:12:32 -> siteSets, fluidRoots, typoScript file names, contentElements []. Not asked, and would have been the real question: "what does lib.contentElement provide, and what does a sitepackage lose by defining its own?" Files: packages/animalshelter_sitepackage/Configuration/Sets/AnimalShelter/TypoScript/content.typoscript, config/sites/main/config.yaml.

## Suggestion

Trimmed on 2026-09-01. The decision this session made without noticing is now a
hint, sitepackage-fluid-styled-content, established against .checkouts: what
lib.contentElement is and what copying it brings, that fluid-styled-content-css
adds the .ce-* rules and nothing else, and — correcting this report —
that lib.parseFunc and lib.parseFunc_RTE belong to EXT:frontend rather than to
this extension. What is left open is the tool half.

In typo3_extension_describe, report the TypoScript objects an extension defines
at the top level of its setup, not only the file names. For fluid_styled_content
that is lib.contentElement — the thing any project sitepackage actually consumes
from it. A caller looking at "contentElements: []" concludes the extension is
inert for them; the truth is the opposite.
