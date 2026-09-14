---
id: D-KNW-015
title: The corpus states what a Fluid preview template replaces
date: 2026-08-02
status: revoked
revokedBy: D-KNW-021
---

# D-KNW-015 — The corpus states what a Fluid preview template replaces

**A Fluid preview template replaces the content half of a page module preview
and not the header above it. Nothing in this server says so.**

The manual this server serves does not say it either, which makes the statement
this server's own rather than a restatement. The feedback goes to the queue.

The corpus registers a preview template and stops at the TSconfig key. A session
that follows it writes a template against `{record}`. It has no way to know what
is already on the page above the template's own output. The duplicate header is
the first thing an editor sees.

## Evidence

- The miss reproduces against the manual as this server serves it today. Called
  over stdio with the feedback's own `page` argument and
  `targetVersion: "14.3"`, `typo3_documentation_lookup` returns *Configure
  custom backend preview for content element* with `status: answered`. The
  example under *Preview rendering with a Fluid template and page TSconfig* is
  still `<h2>{record.header}</h2>` followed by `<p>{record.bodytext}</p>`.
  Nothing in that section says anybody else draws the header.
  `renderPageModulePreviewHeader()` does appear on the page, in the
  `PreviewRendererInterface` listing under *Writing a preview renderer*. That is
  the PHP route a session on the TSconfig route has no reason to read.
- The feedback's claim about TYPO3 holds. On `.checkouts/14.3`,
  `GridColumnItem::getPreview()` calls `renderPageModulePreviewHeader()` first,
  dispatches `PageContentPreviewRenderingEvent` after it, and uses the
  listener's value only as the *content*. `wrapPageModulePreview()` then puts
  the header into `<div class="element-preview-header">` and the content into
  `<div class="element-preview-content">`. `FluidBasedContentPreviewRenderer` is
  exactly such a listener — it renders the template with `record` assigned and
  calls `$event->setPreviewContent()`, and touches nothing else.
- What the header already carries is four things rather than one, so the copy
  the feedback reports is one case of a shape. `element-preview-header-status`
  (the hidden-header notice when `header_layout` is `100`), `-date`, `-header`
  (the schema's label field, linked to the edit form) and `-subheader`. A
  template that repeats `{record.subheader}` or `{record.date}` makes the same
  mistake as one that repeats `{record.header}`.
  `renderPageModulePreviewFooter()` is the same story below the content.
- The split is not version-bound, which is what decides whether the statement
  needs a `since`. On `.checkouts/13.4` `GridColumnItem::getPreview()` has
  `renderPageModulePreviewHeader()` at line 78, the event after it and
  `wrapPageModulePreview()` at line 96. The 13.4 renderer builds the same four
  header parts. So one statement holds on both majors — unlike the one
  [`D-KNW-014`](knw-014-the-record-a-v14-preview-template-is-handed-is-a-subject-this-server-owns.md)
  queues, which is `since 14`.
- Nothing below `knowledge/` or `skills/` says it. `bin/cli hints:probe` reaches
  nothing on "backend preview element header already rendered default renderer"
  or on "backend preview template header duplicate
  StandardContentPreviewRenderer". The one preview statement on the
  `content-elements` hint gives the TSconfig key
  `mod.web_layout.tt_content.preview.<CType>` and the TypoScript beside it.
  `skills/typo3-content-element-development/SKILL.md` has one line, "Add a
  useful backend preview for a custom CType".
- `feedback/2026-08-01-003935-guidance-item-previews-for-content-elements.md`
  reports the same gap from the other side. It asks for a preview that
  summarises the assigned data rather than "a re-render of fields the default
  renderer already shows". That feedback awaits its judgement and its own card
  is in the queue, so it is corroboration here rather than something this run
  decided.

## Decided

- Step 1a of the ladder, and queued. Content elements are inside this server's
  scope, the answer is in neither `knowledge/` nor `skills/`, and there is
  nothing here to reword or move.
- Not closed on the spot. What lands is a statement in the corpus about TYPO3
  behaviour. The run that writes it has to redo the judgement run's research
  against both majors.
- The feedback's own suggestion speaks to the wrong repository. "Annotate the
  preview-template example" is a change to docs.typo3.org. The lever this server
  has is a statement of its own, and it is a stronger one than an annotation
  would be. A silent manual is what makes the statement worth its place.
  Recorded so the todo does not open with a search for a documentation fix.
- The statement lands on the same `content-elements` preview statement that
  `D-KNW-014`'s todo rewrites, so the two have an order rather than run
  parallel. Whichever lands second rewrites in place.

## Assumed

- The statement belongs on `content-elements` beside the preview statement
  already there, rather than on `fluid-templates`. That is the assumption
  `D-KNW-014` makes, for the same reason: a session that writes a preview
  template arrives from the registration.
- The four header parts named are worth more than one sentence that says the
  header is already there. A session that reads only the shorter form still
  writes
  `{record.subheader}`.

## Wrong if

- A listener may replace the whole preview rather than its content half. The
  core moves the header behind the event, or `wrapPageModulePreview()` no longer
  gets a separately rendered header. Then a template written to the statement
  shows no header at all, which is worse than the duplicate.
- The header block turns out not to copy `{record.header}` for the tables a
  Fluid preview registers for. It renders the schema's *label* capability, not a
  field named `header`. So the statement as it stands is too narrow if previews
  register for tables whose label is another field.
- The todo finds the answer already reachable through
  `typo3_documentation_lookup` on another page — the TSconfig reference or the
  page module chapter. It is then step 2 or 3 and the corpus wants a pointer
  rather than a statement of its own.

## Confirmed on 2026-08-02

The research ran again on both checkouts and the split holds. The header renders
before the event dispatch, the listener's value is the content only, and the
wrap is the same between the majors. The four header parts are the same four,
built from different sources, and neither difference is visible to a template.
So the statement carries no `since`.

The narrow case the second **Wrong if** names did not show up for the table in
play. The statement stands so another table's preview has cover rather than a
contradiction. The footer stands with the header, because the same template
cannot reach it. The probe still reached nothing once the statement was in,
because the hint carried no preview vocabulary at all.

## Revoked on 2026-08-02

By the work this entry queued: its statement ends "and nothing in this server
says so", and what it asked for landed the same day. A reader of a listing has
the title and the status. So `confirmed` over that sentence reads as a claim
about a gap that has closed. `D-KNW-021` holds from here. One of the three
**Wrong if** survives into it and is a different failure there. A statement in
the corpus that has gone false, rather than one nobody had written.
