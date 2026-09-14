---
id: D-KNW-021
title: 'A Fluid preview template replaces the content half'
date: 2026-08-02
status: open
coveredBy:
  - HintsTest::aPreviewAnswerSaysWhatTheDefaultRendererAlreadyDraws
---

# D-KNW-021 — A Fluid preview template replaces the content half

**A Fluid preview template replaces the content half of the page module preview,
and the corpus names the header and footer parts drawn around it.**

[`D-KNW-015`](knw-015-the-corpus-states-what-a-fluid-preview-template-replaces.md)
is the result: the split is real, the manual does not carry it, and nothing here
said it either. This entry is what took its place. The statement is on
`content-elements`, and the exposure is now a statement that could go false
rather than one that is absent.

## Evidence

- The `content-elements` hint says the template "replaces the content half of
  the preview and nothing around it". It names the parts by field rather than as
  "the header". The notice where `header_layout` hides the header, the date
  field with its label. The record type's label field linked to the edit form,
  `header` on `tt_content`, and subheader. The footer below the content carries
  `starttime`, `endtime`, `fe_group`, `space_before_class`, `space_after_class`
  and the internal description.
- The split reads the same on both checkouts today.
  `GridColumnItem::getPreview()` calls `renderPageModulePreviewHeader()` at line
  78 on `.checkouts/13.4` and line 81 on `.checkouts/14.3`. It dispatches
  `PageContentPreviewRenderingEvent` after it in both, and hands header and
  content to `wrapPageModulePreview()`. `getFooterInfo()` calls
  `renderPageModulePreviewFooter()`, outside that wrap, on both.
- The statement carries no version of its own, which is what the research above
  decides: nothing between the two majors is visible to a template.
- It is reachable from the symptom rather than from the mechanism.
  `bin/cli hints:probe` on "backend preview element header already rendered by
  the default renderer" reaches `content-elements` at
  `appliesTo(15) + text(263)`. `D-KNW-015` recorded the same probe with no reach
  at all; the change that wrote the statement put `backend preview` into the
  hint's `appliesTo`.

## Decided

- `D-KNW-015` stands revoked in place. Its statement was that nothing here says
  so, and something here says so. An entry a reader may still build on has to be
  one whose headline is true when they read it.
- `R-KNW-042` now rests on this entry rather than on the revoked one, for the
  reason
  [`D-KNW-020`](knw-020-what-a-preview-template-is-handed-is-stated-on-both-majors.md)
  repoints `R-KNW-041`.
- Two successors and not one, because the two fail differently. What `{record}`
  resolves to goes wrong when a field type moves, and what the template replaces
  goes wrong when the core moves the header. One entry would have a **Wrong if**
  nobody could act on either half of.
- Not decided again: that the statement is this server's own rather than a
  restatement of the manual. That is `D-KNW-015`'s account, and the manual had
  no second read here.

## Assumed

- The four header parts and the six footer fields are what a template meets.
  They came off the two renderers, not from a page module.
- "The record type's label field" covers a preview registered for a table whose
  label is not `header`. `tt_content` declares `header` on both majors, so the
  wording is what carries the other tables and nothing has exercised one.

## Wrong if

- The core moves the header behind the event, or lets a listener replace the
  whole preview rather than its content half. A template written to the
  statement then draws no header at all, which is worse than the copy it stood
  against.
- The header or footer parts change on a major. The statement carries no
  `since`, so a caller on that major hears about a part that is not there. The
  bind that would have caught it stayed off on purpose.
- `backend preview` in the hint's `appliesTo` starts to pull `content-elements`
  into questions about a frontend template. What says it does not is two symptom
  probes and one frontend query rather than a sweep. "Fluid template layout
  partial section frontend rendering" still reaches `fluid-templates` and
  `frontend-page-rendering` alone.

## Since then

A third gap on the same statement got its judgement on 2026-08-02 and sits in
the queue,
[`D-KNW-025`](knw-025-what-a-backend-preview-owes-the-editor-is-a-subject-this-server-owns.md).
This entry's statement ends "what it owes the editor is what those parts do not
already say", and that is where the two meet. The clause rules out a second
header and rules in a static label that says nothing. What a preview should draw
instead is the queued statement, and it goes beside this one rather than into
it. The two go wrong on different events. This one when the core moves the
header, the other when the core changes what a preview is for.
