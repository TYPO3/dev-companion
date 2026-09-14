---
id: D-ANS-131
title: An icon answer says whose picture the identifier already is
date: 2026-09-01
status: open
readings:
  - 2026-09-02
coveredBy: []
---

# D-ANS-131 — An icon answer says whose picture the identifier already is

**A validated identifier carries the content elements it is already the icon of.
"Registered" and "free to describe something else" are different questions, and
only the first had an answer.**

A caller asked whether four identifiers exist, was told they do, and put the
core HTML element's icon on a content element of its own.

## Evidence

- The call of 2026-08-31 passed `content-card`, `status-user-group-backend`,
  `content-text-teaser` and `content-special-html` and got four
  `registered: true`. All four answers were correct. The wrong conclusion was
  the caller's, and it is the one a yes invites.
- The tool already reads the bound icon. The probe collects the `CType` items
  with the icon each declares for `typo3_extension_describe`. So this is a
  second reader of one topic rather than a new question to the installation.
- What it is not is a judgement. Nothing here can answer whether an icon fits an
  element, and `any/icons/drawing-a-content-icon` is where the question goes
  instead.

## Decided

- `usedBy` on each validated entry, as `tt_content.CType=<value>`, and a line in
  the text that says registered means it resolves rather than that it is free.
- The item icon of a CType and nothing else. `typeicon_classes` per table and
  per record type is the same question one level wider, and nothing has asked
  for it.
- Empty where the installation did not answer, which is the same silence as an
  identifier nothing binds. `answeredBy` is what tells the two apart, as it does
  for the rest of this answer.

## Assumed

- That a caller who reads "already the icon of tt_content.CType=html" does not
  borrow it. The sentence is what a lookup can do; the decision stays theirs.

## Wrong if

- Somebody reads an empty `usedBy` as permission. It says nothing binds it in
  this installation. The core set is full of icons that belong to a meaning and
  bind to no type.
- The bound uses that matter turn out to be elsewhere, a record type's own icon,
  a backend module's. The field answers for one table and looks like it answers
  for all of them.
