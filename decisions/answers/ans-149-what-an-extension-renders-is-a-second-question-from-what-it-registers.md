---
id: D-ANS-149
title: What an extension renders is a second question from what it registers
date: 2026-09-04
status: open
coveredBy:
  - ProjectTest::whatAPackageRendersAndDoesNotRegisterIsItsOwnList
---

# D-ANS-149 — What an extension renders is a second question from what it registers

**`typo3_extension_describe` reports the content types an extension renders and
does not register, and the `Generic` template it ships, beside the elements it
registers.**

A package that takes the rendering frame over from `fluid_styled_content` owns
rendering for CTypes it never registered, and the answer named none of it: the
two questions stopped having one answer and only one of them was askable.

## Evidence

- `feedback/2026-09-04-053810`. A sitepackage that had just rebuilt
  `tt_content.shortcut` and a `Generic` template asked the tool as a
  verification pass. Neither file appears anywhere in the answer — not under
  `contentElements`, not under `typoScript`, not under `files`.
- The session only noticed because it had written both twenty minutes earlier.
  A session inheriting the repository reads the answer as six elements, and
  deleting `Shortcut.typoscript` leaves an element editors can still select with
  nothing to render it.
- The evidence was already read. `Extension::typoScriptValues()` walks
  `Configuration/TypoScript/` and `Configuration/Sets/` alike and holds every
  assignment with the file it came from, so both facts are in hand before this
  change and neither reached the answer.
- The `Generic` case is the one nothing points at. `ExtensionUtility::
  configurePlugin()` writes `templateName = Generic` for every plugin registered
  as a content type — verified in `.checkouts/12.4`, `13.4`, `14.3` and `main` —
  so the file carries every Extbase plugin resolving through this extension's
  template root, `EXT:form`'s content element included, while reading as unused.
- The feedback's third observation is not a defect. `typoScript` came back empty
  beside a populated `siteSets`, and the schema says what that field is: files
  below `Configuration/TypoScript/`. A package whose TypoScript is in a set has
  none.

## Decided

- **Two fields rather than one.** `renderedContentTypes` is a list of content
  types, and the `Generic` template is not one — it is the frame every plugin
  shares. A row for it in that list would put a template name in an identifier
  column, so `pluginFrame` carries it as the path it is.
- **The entry shape follows `contentElements`**, which the reporting session
  says is exactly right for a "did I forget one" check: the identifier, the
  template name where the TypoScript sets one, and the file it came from.
- **Who registers it is the installation's to say**, read off the `EXT:`
  reference in the CType's label the way every other attribution in this answer
  is. That answers the feedback's ask to tell a kept core element from somebody
  else's plugin without a list of core CTypes going stale here.
- **`registeredBy: null` is two things and the schema says both**: no
  installation answered, or the installation answered and nothing registers that
  identifier — which is a rendering definition for an element no editor can
  select.
- Nothing about `contentElements` moves. The session asked for it to be left
  alone and the two lists answer different questions.

## Assumed

- That a `tt_content.<identifier>` assignment in an extension's own TypoScript
  means that extension renders it. A file that only overrides one property of a
  definition somebody else owns reads the same way, and the source file beside
  the entry is what a reader checks that with.
- That the `Generic` template is worth finding by name under
  `Resources/Private/Templates/`. A package putting it in a set's own template
  root instead is not found, and reports null where it ships one.

## Wrong if

- A package reports a long `renderedContentTypes` because it overrides one
  property of many core elements, which would make the list a diff rather than
  an inventory.
- A session deletes a file this now names, on the reading that the answer is a
  list of what may go rather than of what is owned.
- `pluginFrame` reports null on a package that does ship the frame somewhere
  else, which would make the file name too little to look for.
