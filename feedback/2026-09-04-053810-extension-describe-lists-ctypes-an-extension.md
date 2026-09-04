---
date: 2026-09-04T05:38:10+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_extension_describe
directory: /home/benji/projects/site-tierheim
---

# extension_describe lists CTypes an extension registers in TCA, not the ones it renders

## Observation

Task: remove fluid_styled_content and rebuild what the sitepackage needed, with the user asking explicitly to "check all elements, make sure you have not forgotten any". I called typo3_extension_describe at the end as a verification pass.

It answered well on most of what I wanted: requires no longer listed typo3/cms-fluid-styled-content, fluidRoots had picked up the two new directories (Resources/Private/Partials/, Resources/Private/Layouts/) beside the existing template root, and the six own content elements came back each with its templateName and the source file its rendering definition sits in. That last shape — identifier, kind, templateName, source — is exactly right for a "did I forget one" check and I would not want it changed.

What it did not report is the two rendering definitions the package had just gained for CTypes it does not itself register:

- tt_content.shortcut, a core CType kept in this site's TCEFORM keepItems, whose frontend rendering the package now owns in Configuration/Sets/AnimalShelter/TypoScript/ContentElement/Shortcut.typoscript with Templates/Content/Shortcut.fluid.html.
- Templates/Content/Generic.fluid.html, which is what ExtensionUtility::configurePlugin() renders every Extbase plugin through, EXT:form's content element included.

Neither appears anywhere in the answer — not under contentElements, not under typoScript (which came back as an empty array), not under files. The description says the tool reports "the content elements it adds to tt_content, with the template each renders through", so the omission is consistent with what it promises; the promise is what is narrow. For a package that has just taken over the rendering frame from a system extension, "which CTypes does this package render" and "which CTypes does this package register" have stopped being the same question, and only the second is answerable.

I noticed because I had rebuilt the shortcut myself twenty minutes earlier and could see it was missing. A session that inherited this repository and asked the same question would read the answer as "this package renders six elements" and would not learn that dropping Shortcut.typoscript silently kills an element editors can still select.

## Query

typo3_extension_describe extension="animalshelter_sitepackage", on TYPO3 14.3.6, after the package had gained Configuration/Sets/AnimalShelter/TypoScript/ContentElement/Shortcut.typoscript (tt_content.shortcut =< lib.contentElement, templateName = Shortcut) and Resources/Private/Templates/Content/Shortcut.fluid.html and Generic.fluid.html.

## Suggestion

Report rendering definitions the package carries for CTypes it does not register, as a separate list beside contentElements — same shape (identifier, templateName, source), a different heading, something like renderedContentTypes or foreignContentElements. The evidence is already in the files the tool reads: a "tt_content.<identifier> =< lib.contentElement" assignment in a set's TypoScript whose identifier is not among the record types this extension adds.

Two things that would make it decisive for a removal or a handover:

- Mark whether the identifier is a core CType (shortcut, text, textmedia …) or an Extbase plugin signature, so a reader can tell "we kept a core element and now own its rendering" from "we render somebody else's plugin".
- Report Generic.fluid.html specifically when the package ships one, since its presence is the thing that keeps every Extbase plugin on the site alive and nothing else in the package points at it. It is the file most likely to be deleted as unused.

Also worth noting: typoScript came back as an empty array for a package whose site set carries a setup.typoscript with several imports and a lib.contentElement definition. If that field means "TypoScript registered outside a site set" it reads as a gap rather than a distinction; siteSets did list the set and its files, so the information is not lost, but the empty array beside it is misleading.
