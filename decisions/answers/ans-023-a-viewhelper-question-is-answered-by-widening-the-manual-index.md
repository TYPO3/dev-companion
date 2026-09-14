---
id: D-ANS-023
title: A ViewHelper question is answered by widening the manual index
date: 2026-08-02
status: revoked
revokedBy: D-ANS-026
coveredBy: []
---

# D-ANS-023 — A ViewHelper question is answered by widening the manual index

**`typo3_documentation_lookup` searches three books and none of them documents a
ViewHelper, so a question about `f:if` comes back with whatever prose carries
the word.**

One session lost a task to three separate Fluid mistakes and read them as one
systemic gap. It is one gap, and this is where it sits. The reference that
answers what a ViewHelper does is a manual of its own, published the way the TCA
reference is. The lookup does not carry it.

## Evidence

- The miss reproduces. `bin/cli hints:probe` with the feedback's own query
  reaches one hint, `frontend-records`, which says nothing about Fluid. Narrowed
  to `fluid template conditional` it reaches `fluid-templates` at
  `appliesTo(14) + text(99)`. `IfViewHelper condition branch`,
  `iterate relation field f:for` and `typolink inside conditional` reach nothing
  at all.
- The corpus names `f:then` once, in the `until: 12` statement on
  `fluid-templates` about an array-typed argument. That statement is about what
  an `f:if` evaluates to as a value, not about which branch renders.
- The manual answers no better. Called over stdio at `targetVersion: "14"` with
  `f:if f:then f:else condition ViewHelper`, and again with `IfViewHelper` and
  `f:then`. The lookup returned the same four pages both times. Those are
  Developing a custom ViewHelper, the Translate ViewHelper, JavaScript form
  helpers, and TSconfig Conditions.
- The book it should have returned exists and has versions like the others.
  `https://docs.typo3.org/other/typo3/view-helper-reference/<version>/en-us/`
  answers 200 on 12.4, 13.4, 14.3 and main, with 341 to 401 anchors at each
  root. One page per ViewHelper stands below it,
  `typo3fluid/fluid/latest/If.html` for this one.
- That page carries the `f:then` / `f:else` structure as its second example. It
  does not carry the trap. Its Basic usage says everything inside the tag
  appears when the condition is true. Nothing there says what an `f:else` beside
  it does to that.
- The claim the feedback makes about TYPO3 holds. Read in `typo3fluid/fluid`
  5.3.1 — the engine 14.3 pins,
  [`D-VER-003`](../versions/ver-003-the-fluid-engine-gets-no-version-axis-of-its-own.md)
  — in a local installation's `vendor/`, because no core checkout has one.
  Uncached, `AbstractConditionViewHelper::renderThenChild()` walks the child
  nodes and returns `null` where it finds an `ElseViewHelper` and no
  `ThenViewHelper`. Compiled, `convert()` wraps the body as `__then` only when
  it saw no `f:then`, `f:else` or `f:else if` child. So the same template
  compiles with no then closure at all. Nothing raises and nothing lands in a
  log, which is the empty link the session reported.
- Adding the book is not one entry in an array. Documentation::search() builds
  every base as `/m/<document>/<version>/en-us/`, and this manual lives under
  `/other/`.

## Decided

- Step 1b of the ladder, and queued. The answer is available and not in the
  shape the task needed. The lever is the index of `typo3_documentation_lookup`
  rather than a fourth hint. It touches `src/`, so it is not closed on the spot.
- The aggregate feedback gets the aggregate lever. What `f:if` does to the
  branch beside it is the whole subject of
  `feedback/2026-08-01-003448-specific-fluid-f-if-f-then-f-else-failure-a.md`,
  which is still unjudged and has its own card in `todo/open/`. This entry
  records the engine read above so that judgement does not have to repeat it,
  and so the statement it queues stands once.
- Not queued a second time. What a v14 preview template gets, and whether a
  relation field is iterable, are already
  [`D-KNW-014`](../knowledge/knw-014-the-record-a-v14-preview-template-is-handed-is-a-subject-this-server-owns.md)
  and the todo in hand for it. This feedback reports both, and neither gets a
  second pass here.
- Not step 3, and not step 4. The query reaches the hint it should reach, and
  the skill the session had active names `typo3_documentation_lookup` for Fluid
  APIs. There is nothing here to move or reword.

## Assumed

- The reference's root is a table of contents that Documentation::links() can
  read, on the strength of the anchor counts alone. The parser was not run
  against it.
- One book more is worth one fetch more per call. The lookup already fetches
  every indexed root before it scores anything.

## Wrong if

- The anchors at that root turn out to be navigation rather than a page list. So
  an index of the book adds a request and no candidates.
- Indexing it makes the other answers worse. It is a large book of short titles,
  and `FIELD_WEIGHTS` weighs `manual` at 2. A question that merely says Fluid
  would then outrank the Fluid chapter of TYPO3 Explained.
- The pages come back too thin to excerpt. A ViewHelper page is largely argument
  tables and code, and the reader takes its excerpt from paragraphs.

## Confirmed on 2026-08-02

The index has the book: each manual carries the collection it lives in, and one
method builds every base from it.

None of the three **Wrong if** held. The root is a table of contents and the
index reads 189 pages out of it. The excerpts are prose, and the shortest of
them still says what its page is for. No Fluid question loses its rank, because
every page of the book carries the word, and a term everything carries separates
nothing. The regressions asked for at the same time held.

## Revoked on 2026-08-02

By the change this entry asked for. The statement is what the lookup did until
that afternoon. An entry a reader may build on has to be one whose statement is
true when they read it. The evidence and the confirmation stay. The gap was
real, the measurement covered the three **Wrong if** and none held. That read is
why the change looks the way it does. `D-ANS-026` holds from here.
