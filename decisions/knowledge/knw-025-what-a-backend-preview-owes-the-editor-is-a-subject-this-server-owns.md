---
id: D-KNW-025
title: 'What a backend preview owes the editor is a subject this server owns'
date: 2026-08-02
status: revoked
revokedBy: D-KNW-037
---

# D-KNW-025 — What a backend preview owes the editor is a subject this server owns

**What a content-element preview should show is inside this server's boundary
and absent from it. So the feedback goes to the queue.**

The corpus says where a preview template registers, what it gets, and which
parts the default renderer already draws. A session that follows all of it can
still ship a preview that reads "Testimonials element" and satisfies every
statement here. Nothing says the preview is what an editor tells two instances
of one element apart by.

## Evidence

- The miss reproduces. Called over stdio with the feedback's own query as `task`
  and `targetVersion: "14"`, `typo3_hint_lookup` returns no hint at all and
  lists the 40 ids instead. `bin/cli hints:probe` on the same query reaches
  nothing from the same candidates.
- In the corpus's own words, the subject is in reach and still silent.
  `task: "backend preview template for a content element"` returns
  `content-elements` with six preview statements. The page TSconfig
  registration, what the template replaces, the one `record` variable. The
  system properties, what a relational field resolves to, and the pointer to
  `theme_camino`. None of them says what belongs in the output.
- The nearest statement forbids where the rule has to require. The hint ends
  "what it owes the editor is what those parts do not already say", which a
  static label satisfies. That clause stood against a header twice,
  [`D-KNW-021`](knw-021-a-fluid-preview-template-replaces-the-content-half.md).
- The mechanism half of this feedback is already answered. That a relation comes
  back as records an `f:for` iterates landed on the same hint on 2026-08-02. The
  feedback that asked for it is in
  `feedback/archive/2026-08-01-002745-task-show-assigned-related-groups-in-a-typo3-14.md`.
  What stays open is the design rule, not how to render it.
- Nothing below `knowledge/` or `skills/` carries the rule's own vocabulary.
  `at a glance`, `without opening`, `summarise`, `abstract view` and
  `tell apart` reach no line. The skill that was active states the whole of its
  guidance as "Add a useful backend preview for a custom CType".
- The rule is establishable from a checkout, which is what makes this queued
  rather than blocked. `.checkouts/14.3` ships nine preview templates in
  `theme_camino/Resources/Private/Templates/ContentPreviews/`, and the four read
  here all draw the record's own payload. `Testimonial` the bodytext and the
  images, `Linklist` each assigned list element with its label and its resolved
  link. `Author` both, `TextmediaTeaserGrid` each assigned tile with its text
  cropped to 150 characters. None of them renders a label with the element's
  name. What the other five show is the todo's research, not this one's.

## Decided

- Step 1a of the ladder, and queued. Content elements are a subject this server
  answers for, and the statement has to come off the core. There is nothing here
  to reword or move.
- Not step 4. The clause that comes closest is `D-KNW-021`'s and it is right
  about what it says: a template may not repeat the header. What a preview
  should contain instead is a second statement, because the two fail
  differently. One when the core moves the header, the other when the core
  changes what a preview is for.
- Not the feedback's own suggestion written down. It asks for "group titles,
  counts, references", which is the answer for the element that session was
  building. What lands comes off the core's own previews.
- Not closed on the spot. Nine templates in the checkout are the evidence and
  this run has read four of them.

## Assumed

- The statement belongs on `content-elements`, beside the other preview
  statements, rather than in the content-element skill. The skill keeps the
  design method and takes versioned facts from the tools. A session that writes
  a preview arrives at that hint from the registration.
- `theme_camino` is the core's position on what a preview shows. The
  sitepackage-layout hint already says it is the theme of one release line with
  a move out of the core announced. So it is evidence rather than a contract.

## Wrong if

- The nine templates do not agree: one of them renders a label with the
  element's name, or a field the header already draws. There is then no single
  rule to state, and what the hint can carry is a pointer to the examples.
- The statement lands as advice. "Show a summary" changes nothing about the
  template somebody writes. What it has to name is the fields and relation kinds
  a preview draws from, and `R-KNW-041` already says what those resolve to.
- 13.4 needs a different answer. `theme_camino` is a 14 package and no worked
  preview ships on 13.4. So a statement off it may arrive with a `since` that a
  design rule does not deserve.

## Confirmed on 2026-08-03

All three **Wrong if** had a visit and none held. The directory holds ten
templates rather than nine and the ten agree. Each draws the record's own
payload, none renders a label with the element's name, none repeats a field the
header draws. What lands is the field kinds rather than advice, and it carries
no range.

What did not get a fix is the route. The feedback's own wording still matches
nothing at all, because the query names no subject in the corpus's words. Two
phrases in a trial against it changed nothing and went back out.

## Revoked on 2026-08-03

By the work this entry queued. Its statement says the rule is "missing from it",
and it is on `content-elements` now. So `confirmed` over that sentence would
read as a claim about a gap that has closed. What holds from here is
`D-KNW-037`, whose **Wrong if** is a different list. What can go wrong now is a
statement in the corpus that goes false as the core's own previews move. Not a
rule nobody had written.
