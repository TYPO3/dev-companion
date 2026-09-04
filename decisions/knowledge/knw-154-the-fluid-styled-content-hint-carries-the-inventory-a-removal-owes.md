---
id: D-KNW-154
title: 'The fluid_styled_content hint carries the inventory a removal owes'
date: 2026-09-04
status: open
---

# D-KNW-154 — The fluid_styled_content hint carries the inventory a removal owes

**What a site owes when it drops `fluid_styled_content` is stated in the hint
that already names the object it takes the dependency for.**

The hint reached the session unprompted and decided four of its steps. What it
left the session to read out of the extension itself is everything the
dependency carries beside `lib.contentElement`.

## Evidence

- `feedback/2026-09-04-053551`. The reporting session names the hint as the most
  useful answer of its run and lists four statements it acted on directly. It
  then names what it had to establish by reading
  `fluid_styled_content`'s own `setup.typoscript` and `Layouts/Default`.
- Routing and delivery are not where it failed.
  `bin/cli hints:probe "Remove the fluid_styled_content system extension
  dependency from a TYPO3 project sitepackage and reimplement everything it
  provided"` ranks the hint first, and `typo3_task_guide` returned it without
  being asked for it. That is ladder step 1a: the answer is not there.
- The plugin path holds on every covered branch.
  `extbase/Classes/Utility/ExtensionUtility.php` writes
  `tt_content.<signature> =< lib.contentElement` with `templateName = Generic`
  in `.checkouts/12.4`, `13.4`, `14.3` and `main`, and `Generic` is
  `fluid_styled_content`'s own template in all four. `EXT:form`, `EXT:felogin`
  and `EXT:indexed_search` each register a plugin through that call.
- The two failure modes are different and neither is the one the feedback
  assumed. `tt_content` is a `CASE` on the CType with a `default` printing "has
  no rendering definition", registered in `frontend/ext_localconf.php`; the
  plugin's own key is written whether or not the extension is installed, so the
  default never fires and the element renders as nothing. Where
  `lib.contentElement` is redefined without a `Generic` template beside it,
  `FluidTemplateContentObject` rethrows `InvalidTemplateResourceException`
  naming the template and every root it checked.
- The split across three extensions is real. `shortcut`'s TCA is
  `EXT:frontend`'s, its page module preview is
  `backend/Classes/Preview/StandardContentPreviewRenderer.php`, and only
  `ContentElement/Shortcut.typoscript` and the template beside it are this
  extension's.
- `Layouts/Default` renders `Before`, `Header`, `Footer` and `After` as optional
  sections whose fallback child is a partial, byte-identical in `.checkouts/12.4`
  and `main`.
- `styles.templates.*` and `styles.content.*` are declared by
  `fluid_styled_content` and by no other system extension, and
  `ConstantAwareTokenStream::__toString()` falls back to the token where nothing
  declares one — so a surviving reference becomes literal text rather than an
  empty value.

## Decided

- **Closed on the spot**, not queued. The change is six statements in
  `knowledge/`, no schema and no skill contract moves, and the reading against
  `.checkouts/` was made by this run —
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md).
- The statements go into `sitepackage-fluid-styled-content` rather than a
  sibling hint. A caller asking what the dependency is worth and one asking what
  removing it costs arrive at the same object, and splitting them would give the
  second question a hint the first one's `appliesTo` already wins.
- Each statement names the mechanism and the file it lives in, which is what
  carried the four the session acted on —
  [`D-KNW-062`](knw-062-what-a-hint-pays-with-is-the-mechanism-and-the-file.md).
- The `list` subtype statement is bound rather than written as the rule, because
  `ContentElement/List.typoscript` is gone on the newer branches.
- The feedback's checklist framing is not taken. A hint states what holds and
  the order a failure is noticed in is the guide's, so the plugin statement
  leads and the CSS one keeps the place it already had.

## Assumed

- That a site dropping the dependency defines `lib.contentElement` itself, which
  is what the hint's closing statement already recommends. A site that removes
  the object outright meets the silent case instead, and both are stated.
- That naming `Generic` by template name rather than by file name is enough for
  a caller to find it. The suffix differs across the covered branches, so a file
  name would be a version number in prose.

## Wrong if

- A session reports rebuilding `lib.contentElement` and still missing one of the
  four, which would mean the inventory is a document rather than a run of
  statements.
- A session reports the `Generic` statement and looks for a file the branch does
  not spell that way, which would make the template name too little to go on.
- The core moves a core element's frontend rendering out of this extension, or
  registers `lib.contentElement` somewhere else, which would make the split
  across three extensions a snapshot rather than a rule.
