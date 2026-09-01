---
id: D-ANS-131
title: An icon answer says whose picture the identifier already is
date: 2026-09-01
status: open
coveredBy: []
---

# D-ANS-131 — An icon answer says whose picture the identifier already is

**A validated identifier carries the content elements it is already the icon of,
because "registered" and "free to describe something else" are different
questions and only the first was answered.**

A caller asked whether four identifiers exist, was told they do, and put the
core HTML element's icon on a content element of its own.

## Evidence

- The call of 2026-08-31 passed `content-card`, `status-user-group-backend`,
  `content-text-teaser` and `content-special-html` and got four
  `registered: true`. All four answers were correct. The reading error was the
  caller's, and it is the one a yes invites.
- The binding is already read. The probe collects the `CType` items with the
  icon each declares for `typo3_extension_describe`, so this is a second reader
  of one topic rather than a new question put to the installation.
- What it is not is a judgement. Whether an icon fits an element is not
  answerable here, and `any/icons/drawing-a-content-icon` is where the question
  goes instead.

## Decided

- `usedBy` on each validated entry, as `tt_content.CType=<value>`, and a line in
  the text saying that registered means it resolves rather than that it is free.
- The item icon of a CType and nothing else. `typeicon_classes` per table and
  per record type is the same question one level wider, and nothing has asked
  for it.
- Empty where the installation did not answer, which is the same silence as an
  identifier nothing binds. `answeredBy` is what tells the two apart, as it does
  for the rest of this answer.

## Assumed

- That a caller reading "already the icon of tt_content.CType=html" does not
  borrow it. The sentence is what a lookup can do; the decision stays theirs.

## Wrong if

- Somebody reads an empty `usedBy` as permission. It says nothing binds it in
  this installation, and the core set is full of icons that belong to a meaning
  without being bound to a type.
- The bindings that matter turn out to be elsewhere — a record type's own icon,
  a backend module's — and the field answers for one table while looking like it
  answers for all of them.
