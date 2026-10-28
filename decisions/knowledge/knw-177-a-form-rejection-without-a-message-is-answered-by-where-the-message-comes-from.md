---
id: D-KNW-177
title: A form rejection without a message is answered by where the message comes from
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::aFormRejectionWithoutAMessageIsAnsweredByWhereTheMessageComesFrom
---

# D-KNW-177 — A form rejection without a message is answered by where the message comes from

**The `form-framework` hint states which elements EXT:form validates, which
partials ignore an error or a property, and where a message comes from.**

## Evidence

- [`feedback/2026-10-28-085931`](../../feedback/archive/2026-10-28-085931-what-ext-form-accepts-and-then-ignores.md)
  came from a 14.3 sitepackage that added a spam guard to a contact form. Step
  1a: `typo3_hint_lookup ids=[form-framework]` said nothing about validation or
  the Honeypot, and nothing else below `knowledge/` did.
- `FormRuntime::mapAndValidatePage()` maps and validates every element that
  `isRenderableEnabled()` admits, in `.checkouts/12.4`, `13.4`, `14.3` and
  `main`. Nothing in `Classes/` reads the element type or a `readonly`
  attribute. So the feedback's "a validator on Hidden or readonly never runs"
  does not hold.
- `Hidden.html` and `Hidden.fluid.html` render no `Field/Field` partial and no
  `f:form.validationResults` in all four checkouts. A rejection there shows no
  message, which is the likely source of the observation.
- `Honeypot.html` and `Honeypot.fluid.html` pass `additionalAttributes` as a
  literal in all four checkouts. From 13.4 the Honeypot's form editor writes
  `properties.fluidAdditionalAttributes.autocomplete`, which the partial never
  reads.
- `TranslateElementErrorViewHelper` hands the error code and the error's own
  message to `TranslationService::translateFormElementError()`. That method
  tries `validationErrorMessages`, then the form's `translationFiles` under
  `validation.error.<code>`, then the message, in all four checkouts.
  `AbstractValidator::translateErrorMessage()` calls `LocalizationUtility` and
  returns `''` for a missing key, on 12.4 and on 14.3.

## Decided

- Closed on the spot under `D-FBK-052` for the four statements. The run read the
  checkouts and holds the evidence, and no contract moves. All four hold on
  every covered line, so none carries a bound.
- The two further notes of the feedback stay open, trimmed in the feedback. One
  is the `aria-hidden` value rendered as its own name. Fluid 4 and 5 render a
  boolean `true` as the attribute name, and Fluid 2 does not. Whether a YAML
  value reaches the `TagBuilder` as a boolean through `translateElementProperty`
  is not read. The other is the request after a cache flush that answered from
  the old definition. Both need an installation.

## Assumed

- That a session which builds a form guard reaches `form-framework`. The probe
  "add a validator to a hidden form field" ranks it first. The probe "form
  validator error message translationFiles locallang" does not reach it.

## Wrong if

- A Hidden element's or a readonly field's validator does not run in an
  installation where the element is enabled. Then something outside
  `FormRuntime` intervenes, and the first statement is too strong.
- A session gets an empty validation message after this hint was in its answer.
  Then the gap is delivery rather than knowledge.

## Since then

`D-KNW-181` closed the two notes this entry left open, read in the `E-SITE`
environments. A `.form.yaml` file turns the string `'true'` into a boolean
before it reaches the `TagBuilder`, so quotes change nothing. The first request
after a flush rendered the changed definition on every covered line.
