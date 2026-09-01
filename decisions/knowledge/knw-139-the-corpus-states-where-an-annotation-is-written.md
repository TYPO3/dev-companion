---
id: D-KNW-139
title: 'The corpus states where an annotation is written'
date: 2026-09-02
status: open
coveredBy:
  - HintsTest::whereAnAnnotationIsWrittenIsStatedBesideTheRulesThatAreOff
---

# D-KNW-139 — The corpus states where an annotation is written

**An annotation goes where inference cannot reach, and a second one on the same
value is what the code under it is checked against.**

`backend-typescript` said which lint rules are off and stopped there, so a
session imitating a neighbouring file had the rule that nothing pushes it and no
rule about what to write.

## Evidence

- The maintainer ruled on 2026-09-01 that this is a rule and holds for code in
  general rather than for the core alone — `D-FBK-053`, where the migrated
  memory it arrived in is read.
- The narrowing convention is the core's own. A type argument on
  `querySelectorAll` types the result, and the callbacks walking one take the
  parameter bare through the backend sources, read in `.checkouts/main`.
- The `@lit/task` half is two forms in one checkout, and the difference is the
  rule rather than taste. `backend/context-menu.ts` and `dashboard/dashboard.ts`
  pin the tuple with `args: () => [...] as const` and destructure it in `task`
  without an annotation; `backend/localization/steps/` and
  `backend/wizard/steps/finisher-step.ts` leave `args` unpinned and repeat the
  types on every task function. Both forms stand on `13.4`, `14.3` and `main`,
  so the statement is unbound.
- Two of the report's three defects could not be established here and are not
  written. A number assigned to `innerText` is in no covered checkout — every
  assignment reads a string — and the property spelled two ways is two types
  rather than a defect: `form-editor-tree.ts` declares its own
  `FormEditorTreeNode` with `expanded`, while the `node` it writes `__expanded`
  on is the backend's `TreeNodeInterface`, which declares it.

## Decided

- The statement goes beside the switched-off rules in `backend-typescript`
  rather than into a hint of its own. That sentence is where a session is told
  the linter will not push it in this direction, and this is what to do instead.
- The procedure is stated as the actionable half: removing a redundant
  annotation is the change, and the compiler error that follows is evidence
  about the annotation before it is a reason to restore it. Dropping a name is
  named as the other thing, because an alias that means something survives the
  annotation that repeated it.
- `any` on a callback parameter is the instance given, and the only one. It
  switches the check off where the query had just narrowed it, which needs no
  reading of the type system to see.
- The `@lit/task` statement is `backend-lit-task`'s. That hint already owns the
  class and says what its branches render; where its parameters get their types
  is the same subject.

## Assumed

- That a session meets this while editing a `.ts` file, so the vocabulary added
  is the words of somebody holding a compiler error rather than the words of the
  rule. `type annotation`, `annotate`, `inferred type` and
  `redundant annotation` are what `appliesTo` gained.

## Wrong if

- A session reads it and still repeats a narrowed type, which would make the
  lever the review rather than the corpus.
- The unpinned `args` form spreads rather than the pinned one, which would make
  this the maintainer's preference against the core's practice rather than a
  reading of it.
- A statement of the general rule turns out to belong outside a TYPO3 corpus at
  all, which is what `D-FBK-053`'s first **Wrong if** watches for across the
  cards beside this one.
