---
id: D-KNW-121
title: What registering an argument costs a tag-based ViewHelper is a subject this server owns
date: 2026-08-25
status: open
readings:
  - 2026-09-01
coveredBy: []
---

# D-KNW-121 — What registering an argument costs a tag-based ViewHelper is a subject this server owns

**Registering an argument on a tag-based ViewHelper takes the value off the tag
it was arriving on, and the corpus states that where the ViewHelper rules
already are.**

A session patching `f:asset.css`, `f:asset.script` and `f:image` for Forge
#106584 read five files of `typo3fluid/fluid` to establish it, and reports that
the mechanism is what made the patch a behaviour change rather than a
declaration.

## Evidence

- The probe misses. `bin/cli hints:probe` with the feedback's own query reaches
  `public-assets`, `page-content-areas`, `fluid-layouts-sections` and
  `css-source-build-boundaries`, none of which is about arguments. Written as
  the mechanism it reaches `fluid-viewhelpers` at `appliesTo(10) + text(59)`,
  whose statements stop at what `registerArgument()` checks a value against.
- Nothing below `knowledge/` or `skills/` carried `additionalArguments`,
  `TagBuilder` or `AbstractTagBasedViewHelper` before this change.
- The statements alone did not fix the miss. With them written and `appliesTo`
  untouched, the feedback's own query still reached the same four entries, and
  `fluid-viewhelpers` ranked below two Fluid entries matching on text. Widening
  `appliesTo` by `registered arguments` and `tag attribute` puts it first among
  them at `text only(215)`. The two Fluid queries asked afterwards keep the
  entries they had: `fluid layout section rendering` answers
  `fluid-layouts-sections` first and `fluid condition f:if empty array literal`
  answers `fluid-conditions-and-arrays` alone.
- The other half of the question is answered. `typo3_documentation_lookup` over
  stdio at `14.3`, with the queries `f:asset.css` and `asset css ViewHelper`,
  returns `Global/Asset/Css.html` first — the argument table, and the sentence
  "This ViewHelper allows you to pass arbitrary arguments not defined below
  directly to the HTML tag created". That is the book
  [`D-ANS-026`](../answers/ans-026-the-viewhelper-reference-is-indexed.md)
  indexed and
  [`R-DOC-003`](../../requirements/documentation/doc-003-a-viewhelper-question-is-answered-from-the-manual-that-documents-viewhelpers.md)
  keeps.
- The claim about TYPO3 holds, read on the three engines the covered branches
  pin — 2.15.0, 4.6.1 and 5.3.1 — by the throwaway-directory procedure of
  [`D-VER-003`](../versions/ver-003-the-fluid-engine-gets-no-version-axis-of-its-own.md),
  because `.checkouts/` has no `vendor/`.
- `ViewHelperInvoker::invoke()` hands `handleAdditionalArguments()` only what no
  `ArgumentDefinition` claims: `$undeclaredArguments` on 2.15.0, and
  `unset($arguments[$argumentName])` inside the definition loop on 4.6.1 and
  5.3.1.
- `AbstractTagBasedViewHelper::initialize()` pushes every one of them onto the
  TagBuilder on all three, and `initializeArgumentsAndRender()` calls
  `initialize()` before `render()`.
- `TagBuilder::render()` walks `$this->attributes` with a plain `foreach` on all
  three, so the emitted order is the order the attributes were added in.
- The core side is in the checkouts. `Asset/CssViewHelper.php` reads
  `$this->tag->getAttributes()` on `.checkouts/13.4` and `.checkouts/14.3`, and
  `ImageViewHelper.php` reads `$this->additionalArguments['alt']` on
  `.checkouts/main`. On `.checkouts/12.4` that same `CssViewHelper` declares
  `href`, `media` and the rest through `registerTagAttribute()`.
- `registerTagAttribute()` and `registerUniversalTagAttributes()` are deprecated
  on 2.15.0 and 4.6.1 and absent from 5.3.1, so the route that registers an
  argument and still emits it exists on 12.4 and 13.4 and not above.

## Decided

- **Step 1a, closed on the spot.** The reading was made in this run against all
  three engines, and queueing it would send the next session to the same files —
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md).
  Nothing in `src/`, no declared schema and no skill contract moves.
- Three statements on `fluid-viewhelpers`, beside the argument rules they
  qualify: what reaches `$this->additionalArguments` and what registering takes
  off the tag, the insertion order the attribute moves in, and
  `registerTagAttribute()` at `until: 13` as the older route.
- The pass-through statement is unbound and the route out is bound. The
  mechanism is the same on all three engines; what changed with Fluid 5 is only
  whether a registered attribute can still be emitted without the render adding
  it.
- Two phrases on the entry's `appliesTo`, because the statements alone left the
  feedback's own query where it was. What the query says is `registered` and
  `arguments`, and the entry said `viewhelper`.
- **A routing line, step 3.** The session chose from the tool list by name, and
  the `routing` block named Fluid for namespaces alone. The line sends the
  argument list of a named tag to `typo3_documentation_lookup`.
- The feedback's own suggestion is **declined**. Rejected:
  typo3_fluid_viewhelper_lookup, keyed by tag name and target version — the
  reference answers that in one call, and what it would add beyond it is the
  statement now in the corpus.
- Not a rename of `typo3_fluid_namespace_list`. It lists namespaces and says so,
  the name is known outside this checkout, and what was missing was a route to
  the manual rather than a second reading of that name.
- `coveredBy: []` because nothing runs over a hint's wording. `HintsTest` holds
  the corpus's shape and the version binding, not that this subject is in it.

## Assumed

- That the two phrases added to `appliesTo` earn their place across the corpus.
  They were measured on the query that missed and on no other.
- That the manual's argument table is enough for the half it answers. It was
  read as a search excerpt here, not as a page.

## Wrong if

- A session registers an argument on a tag-based ViewHelper with the statement
  in reach and still ships the silent loss. Then it is placed where the task
  does not pass, and the lever is delivery rather than the corpus.
- The widened `appliesTo` pulls `fluid-viewhelpers` into answers about something
  else. `registered arguments` is close to what any ViewHelper question says, so
  a query about layouts or conditions ranking it first is the cost.
- A Fluid minor inside one of the pinned majors changes what reaches
  `$this->additionalArguments`. Then the unbound statement needs a boundary, and
  that is `D-VER-003`'s second **Wrong if** seen from this subject.
- A ViewHelper's arguments turn out to be listed incompletely in the reference —
  registered by a trait or by a parent and absent from the page — so the routing
  line sends the question somewhere thinner than the class.
