---
id: D-KNW-082
title: 'A content element names its template'
date: 2026-08-17
status: open
coveredBy:
  - HintsTest::theCTypeTemplateDerivationIsAttributedToItsTheme
---

# D-KNW-082 — A content element names its template

**The corpus states that `fluid_styled_content` sets `templateName` per element
and attributes the `uppercamelcase` derivation to `theme_camino`, which
configures it.**

`sitepackage-templates` carried the derivation as a property of
`lib.contentElement`, which is a package a project may not have installed. A
sitepackage that trusted it would have named no template at all.

## Evidence

- `feedback/2026-08-17-205817`. A session that built a v14 demo site read the
  installed `fluid_styled_content` before it wrote its TypoScript for six custom
  elements. It found the derivation absent there, and reports it would otherwise
  have configured nothing.
- The claim holds, read in `.checkouts/14.3` at `627949e9dd`.
  `typo3/sysext/fluid_styled_content/Configuration/TypoScript/Helper/ContentElement.typoscript`
  sets `templateName = Default` as a plain value.
  `Configuration/TypoScript/ContentElement/Text.typoscript` is
  `tt_content.text =< lib.contentElement` followed by `templateName = Text`.
  There is one such file per element, and `setup.typoscript` imports each.
- It holds on the branches below as well. Both files are the same in
  `.checkouts/12.4` and `.checkouts/13.4`, so that half of the statement carries
  no version boundary.
- The derivation is `theme_camino`'s and nothing else's.
  `Configuration/Sets/camino/TypoScript/content.typoscript` unsets
  `templateName >` and sets `templateName.ifEmpty.cObject` to a `TEXT` with
  `field = CType` and `case = uppercamelcase`. The theme's own
  `camino_textmedia_teaser_grid`, registered in
  `Configuration/TCA/Overrides/20_tt_content_textmedia_teaser_grid.php`, renders
  through
  `Resources/Private/Templates/Content/CaminoTextmediaTeaserGrid.fluid.html`.
  The set declares no dependency on `fluid_styled_content`.
- The same file is where `tt_content = CASE` gets a `default` of
  `lib.contentElement`. The core's own default, in
  `typo3/sysext/frontend/ext_localconf.php`, is the yellow "has no rendering
  definition" `TEXT`. So under the theme the derivation answers for every CType,
  and under `fluid_styled_content` alone an unnamed one is a visible error.
- What the feedback expects of the failure is not what the tree says.
  `fluid_styled_content` ships no `Default` template on any covered branch.
  `Resources/Private/Templates/` holds `Text`, `Textmedia`, `Generic` and the
  rest. `Default` is a *layout*, `Resources/Private/Layouts/Default.html` on
  12.4 and 13.4, `Default.fluid.html` on 14.3. So the copied `Default` resolves
  to no file rather than to a frame. The statement says what this run read
  rather than what the feedback predicted.
- Nothing else in the corpus rested on the derivation. `templateName`,
  `lib.contentElement` and `uppercamelcase` across `knowledge/` and `skills/`
  reach `content-element-preview`, which pointed at this statement for how the
  name follows from the CType. They reach four statements that only name
  `lib.contentElement` as the object content elements render on.

## Decided

- Rewritten as two statements: what `fluid_styled_content` does, unbound, and
  what `theme_camino` configures, `since: 14`. The consequence moved with it — a
  `snake_case` CType is a requirement under the derivation and a habit without
  it.
- Closed in this run rather than queued. `documentation/records/judging.rst`
  puts a feedback that needs a TYPO3 lookup on the todo side of the line because
  the judgement run has read nothing but this repository. The read that
  disqualifies it is the one the ladder owes any feedback that claims something
  about TYPO3. That read happened here, in `.checkouts/`, on all three covered
  majors and named by file above.
- The ladder has no rung for a statement the server delivered, the session took,
  and that was wrong. This is step 1a by the gap, since the corpus never said
  what `fluid_styled_content` does, and step 4 by what the repair costs. One
  feedback is not evidence for a new rung, so the ladder stays as it is.
- The feedback's second ask is which of the two configurations a sitepackage
  should choose. The hint answers it with both stated and with the cost of the
  theme's convention named. That cost is a copy of that block. Which one a
  project wants is its own decision, and a recommendation here would be one this
  run has no evidence for.
- The same commit corrects the pointer in `content-element-preview`. It said the
  template name follows from the CType, which is the same error one hint further
  on.

## Assumed

- That a project sitepackage is the reader of both halves. The hint's other
  statements are for one, and the session that reported built one.
- That the derivation is worth a statement at all now that it is somebody
  else's. It is the convention a reader opens the core's own theme for, and the
  sitepackage layout hint already sends a reader there.

## Wrong if

- A session reports a copy of the theme's block where per-element `templateName`
  was what its project wanted, or the other way round. Then naming both
  configurations without saying which fits what is the gap, and the choice is
  what the statement owes.
- `fluid_styled_content` ships a `Default` template on a later branch. Then an
  element copied off `lib.contentElement` renders a frame instead of a failure,
  and the trap named here is the wrong one.
- `theme_camino` moves out of the core, which `sitepackage-layout` says the core
  has announced. The path named in the statement no longer resolves in a
  checkout, and the sentence has to say where the theme lives instead.
- A judging run cites this entry to close a false statement without reading the
  checkout. The exception is the reading, not the closing.
