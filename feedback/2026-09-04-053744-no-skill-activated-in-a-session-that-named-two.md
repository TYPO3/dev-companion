---
date: 2026-09-04T05:37:44+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-extension-health, typo3-content-element-development, typo3_task_guide
directory: /home/benji/projects/site-tierheim
---

# No skill activated in a session that named two, and the gap they leave has a shape

## Observation

Task: remove fluid_styled_content as a dependency from a TYPO3 14.3.6 project and rebuild everything the sitepackage needed from it.

No skill activated in this session. typo3_task_guide named two and I opened neither, deliberately in one case and by not noticing a fit in the other.

typo3-extension-health I ruled out on its own description — "The audit reports first; nothing is changed before the list is agreed" — against a user instruction that said not to ask questions. That was the right call and the description is what made it possible, so the description is working.

typo3-content-element-development I should probably have opened and did not. Its description covers "CType registration, TCA, inline child records, a custom backend preview, TypoScript, Fluid, assets, labels and tests". Two thirds of what I wrote is content element TypoScript and Fluid: a lib.contentElement definition, a content element layout, a header partial, a Generic template for Extbase plugins, a Shortcut template and its rendering definition. What kept it shut is that the description is framed around *a* content element — building or refactoring one — and my task was the frame every element on the site shares. I read it as "not this" and moved on.

The step no skill carried, and the one I would work out again next session: what a package owes when it drops a system extension it renders through. Concretely, at the moment one would have had to open, I had composer.json, config/sites/main/config.yaml and two Configuration/Sets/*/config.yaml on screen and the request in front of me was "entferne fluid_styled_content als dependency und baue alles was du brauchst nach". The work from there was: enumerate what the extension supplies that this installation actually reaches (one TypoScript object, one layout, four partials, two templates, one CSS set, two settings namespaces); establish which of those the site can and cannot do without by querying the content that exists; find the two silent breakages (Extbase plugins via templateName = Generic, the shortcut CType's frontend rendering); prove the ordering question that the removal raises (whether "=<" survives its target being defined later in the include tree, which it does because it is a reference); and prove the result by diffing rendered HTML before and after.

That is a recognisable shape and it is not one element. It is also not an upgrade, so typo3-extension-upgrade would not have caught it either — nothing was deprecated or removed by TYPO3; the project chose to stop depending on something.

## Query

Session task: "entferne fluid_styled_content als dependency und baue alles was du brauchst nach. prüfe alle elemente, stelle sicher du hast keine vergessen. stelle keine rückfragen recherchiere und finde die beste lösung. nutze den typo3 mcp um dir helfen zu lassen". typo3_task_guide returned skills ["typo3-extension-health","typo3-content-element-development"]. Neither was invoked.

## Suggestion

A skill for "dropping or replacing a dependency a package renders through" would have carried this session. Trigger words a description should hold, because they are what the request actually used: "remove X as a dependency", "drop fluid_styled_content", "rebuild what X provided", "stop depending on", "vendor it into the sitepackage". Its procedure is the one above — inventory what the dependency supplies, intersect it with what the installation reaches, name the silent failures, prove the rendering before and after.

Failing a new skill, widen typo3-content-element-development's description to say it also covers the shared frame those elements render in — lib.contentElement, the content element layout and its partials, the Generic template Extbase plugins use — because a reader deciding from the description alone currently sees only the singular element and rules it out for exactly this work.

Keep typo3-extension-health's description as it is. "The audit reports first; nothing is changed before the list is agreed" is precise enough that I could tell in one read that it was wrong for a task that forbade asking, and that is the whole job of a description.
