---
id: D-KNW-037
title: "A content-element preview draws the element's own payload"
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aPreviewAnswerNamesTheFieldsThePreviewDrawsFrom
---

# D-KNW-037 — A content-element preview draws the element's own payload

**What a backend preview shows is the element's own payload. The corpus states
it as the field kinds a preview draws from rather than as a summary.**

[`D-KNW-025`](knw-025-what-a-backend-preview-owes-the-editor-is-a-subject-this-server-owns.md)
is the result, and this entry is what took its place. The exposure is now a
statement that could go false rather than one that is absent.

## Evidence

- `theme_camino/Resources/Private/Templates/ContentPreviews/` on
  `.checkouts/14.3` holds ten templates, not the nine `D-KNW-025` counted:
  `Author`, `Hero`, `Linklist`, `Testimonial`, `Text`, `Textmedia`,
  `TextmediaTeaser`, `TextmediaTeaserGrid`, `Textpic`, `Textteaser`. All ten
  were read.
- The ten agree on what stays out. None renders a label with the element's name,
  and none renders `record.header`, `record.subheader` or `record.date`. The one
  header anywhere is `item.header` in `TextmediaTeaserGrid`. That is a child of
  the inline relation and a field the default renderer draws for the parent
  alone.
- They agree on what they draw, in five kinds. Text columns, `bodytext`, a
  child's `text`, through `f:format.stripTags` with `<br><sup><sub>` kept and
  `f:sanitize.html`. Cropped only where they repeat, at 150 characters per tile
  in `TextmediaTeaserGrid`. File relations, `image`, `assets`, a child's
  `images`, as `f:image` thumbnails at `height="64"`, `Hero` once per configured
  crop variant. A reference with `properties.hidden` on gets a modifier class
  rather than stays out. Link fields as their label beside what `f:uri.typolink`
  resolves for them, with a translated caveat in place of the URI where it
  resolves to none. A select — `link_config`, `link_icon` — as the `f:translate`
  of its item label rather than as the stored value. Each child of an inline
  relation as a line of its own, by the columns that identify it. `Linklist` and
  `Author` by label and link, `TextmediaTeaserGrid` by header, text, link and
  images.
- Two of those go past the payload to state the record cannot. A broken link
  target and a hidden file reference are exactly what an editor would otherwise
  open the form to find.
- The statement is in reach where the subject has its name.
  `bin/cli hints:probe "what a backend preview of a content element should show the editor"`
  ranks `content-element-preview` first, on `appliesTo(15) + text(348)`.

## Decided

- One statement on `content-element-preview`, directly after the one that says
  what the default renderer already draws. That statement ends "what it owes the
  editor is what those parts do not already say", and this is the answer to it.
- No `since`. The evidence is one major: `theme_camino` ships on 14 and main,
  and 13.4 has no `ContentPreviews/` anywhere in the core. But the rule is about
  what the output owes the editor rather than about how to reach a field. The
  access mechanics on the same hint carry their bounds already. Binding it would
  withhold a design rule from the LTS because the core shipped its worked
  example late.
- Not the feedback's own suggestion. "Group titles, counts, references" is the
  answer for the element that session was building; what landed is the five
  kinds read off the core.
- The `appliesTo` of the hint stays. Two phrases had a trial against the
  feedback's original query and neither made it match. So they went back out
  rather than stayed as untested vocabulary.

## Assumed

- `theme_camino` is the core's position on what a preview shows. It is the theme
  of one release line and announced to move out of the core, so it is evidence
  rather than a contract.
- Naming the kinds is enough to change a template. The alternative is a
  statement that points at the ten examples instead, which costs the caller a
  checkout it may not have.

## Wrong if

- The core ships a preview that renders a static label or a field the header
  already draws. The ten agree today; one that does not is a corpus statement
  gone false, and the honest form is then a pointer to the examples.
- `theme_camino` leaves the core and nothing replaces it. The rule survives, its
  evidence does not, and the `since: 14` statement beside it points at a package
  that is no longer there.
- The unbound rule misleads a 13.4 caller: a field kind named here that a 13.4
  template cannot draw. Nothing in the statement is about the way to reach a
  field, which is what would have to be wrong for that.
