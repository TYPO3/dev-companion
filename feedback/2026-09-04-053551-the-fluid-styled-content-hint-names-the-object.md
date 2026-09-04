---
date: 2026-09-04T05:35:51+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_task_guide
directory: /home/benji/projects/site-tierheim
---

# The fluid_styled_content hint names the object to rebuild but not the inventory that goes with it

## Observation

Task: "remove fluid_styled_content as a dependency and rebuild everything you need; check all elements, make sure you have not forgotten any", on a TYPO3 14.3.6 Composer project whose sitepackage offers only its own CTypes.

typo3_task_guide returned hint id=sitepackage-fluid-styled-content without my asking for it, and it was the single most useful answer of the session. Four of its five statements I acted on directly:

- "lib.parseFunc and lib.parseFunc_RTE are not its. EXT:frontend declares both" — the sitepackage had a lib.parseFunc_RTE override; this told me not to rebuild it.
- the record-transformation dataProcessing entry since v14 — nothing in this site's templates reads {record} today, so without this line I would have dropped it as unused and left a latent break.
- the typo3/fluid-styled-content-css set being only plugin.tx_frontend._CSS_DEFAULT_STYLE, and "a site whose page TSconfig removes every one of those elements from the selector is shipping stylesheet rules for markup it never emits" — this decided the CSS set and sent me looking for the dead .frame-type-table rule in the site's own stylesheet.
- "a site that offers only its own elements is paying for a scaffold it overrode" — the verdict for this exact site.

Where it stopped one step short is the inventory. It says lib.contentElement is "a FLUIDTEMPLATE of a few lines" and that what goes with the dependency is "the rendering of the core's own elements". Both true, and both incomplete for a removal:

1. ExtensionUtility::configurePlugin() emits "tt_content.<signature> =< lib.contentElement" with "templateName = Generic", and Generic.fluid.html lives in fluid_styled_content's template root. Removing the extension therefore breaks every Extbase plugin on the site. This installation has EXT:form with four contact forms; they would have rendered empty with no error. I found it in cms-extbase/Classes/Utility/ExtensionUtility.php:65, not from the hint.
2. The core CType "shortcut" is in this site's TCEFORM keepItems. Core registers the TCA and draws the backend preview (StandardContentPreviewRenderer), but the frontend rendering is fluid_styled_content's ContentElement/Shortcut.typoscript plus Shortcut.fluid.html. A site keeping that element keeps two files it has to reproduce.
3. The layout renders "<f:render section='Header' optional='true'><f:render partial='Header/All' .../></f:render>". The fallback child means the partial has to exist even for an element that declares no Header section, so a replacement layout that keeps the pattern also owes a header partial.

I built that list by reading fluid_styled_content's own setup.typoscript and Layouts/Default.fluid.html. The user's instruction was literally "check all elements, forget none", and the hint is the answer that should have carried it.

## Query

typo3_task_guide task="Remove the fluid_styled_content system extension dependency from a TYPO3 project sitepackage and reimplement everything it provided", changeType="cleanup", paths=["composer.json","packages/animalshelter_sitepackage","packages/animalshelter_animals"], targetVersion="14.3" — which returned hint id=sitepackage-fluid-styled-content unprompted.

## Suggestion

Add to sitepackage-fluid-styled-content (or a sibling hint reachable by "remove fluid_styled_content", "drop fluid_styled_content", "sitepackage without fluid_styled_content") the inventory a removal owes, not only the object it centres on:

- Every Extbase plugin renders through templateName = Generic out of this extension's template root, because ExtensionUtility::configurePlugin() writes it. Dropping the extension without shipping a Generic template silently empties every plugin, EXT:form's content element included. Name the file: Resources/Private/Templates/Generic.fluid.html, and that its else branch is <f:cObject typoscriptObjectPath="tt_content.{data.CType}.20" data="{data}" table="tt_content" />.
- The core CTypes whose *rendering* is here while their TCA and backend preview are core's — shortcut above all, since it is the one a site with its own elements plausibly keeps.
- That the layout's Header/Footer/DropIn sections use an optional section with a partial as fallback content, so the partials are part of the contract and not decoration.
- That the settings the object reads ({$styles.templates.*}, {$styles.content.*}) come from this extension's settings.definitions.yaml and evaluate to literal text once its set is gone, so any remaining reference has to be replaced rather than left.

A checklist framing would fit the task better than prose: "what a site owes when it drops this extension", in the order a failure would be noticed — plugins first (silent), shortcut second (silent), CSS third (visible).
