---
id: D-CAT-001
title: A catalog entry is bound whole, and the binding is derived
date: 2026-07-29
status: confirmed
coveredBy:
  - CatalogTest::aDemoIsFoundUnderEitherSpelling
  - CatalogTest::everyDemoIsDigestedOnEveryMajorThatCarriesIt
---

# D-CAT-001 — A catalog entry is bound whole, and the binding is derived

**`since`/`until` sits on the whole catalog entry rather than on its fields, and
`bin/cli catalog:check` derives it rather than a judgement.**

The answer withholds an entry that does not hold on the stated version rather
than qualifies it.

The architecture hints bind one statement at a time. A component entry has no
statements. It is markup, a class list and a custom-property contract that came
off one revision together, and a caller pastes them together.

## Evidence

- Run against 12.4, 13.4, 14.3 and main. 22 of 25 entries bind: 13 from v13,
  where the `--typo3-*` custom-property contract arrives, and 9 from v14.
  Avatar, the infobox ViewHelper and the record search box hold everywhere.

## Decided

- `since`/`until` sits on the entry rather than on its fields. The finer split,
  this variant since v14, that property since v13, is what the Sass sources
  already say. It would put four ranges in an answer whose reader wants one
  question answered: can I paste this.
- The answer withholds an entry that does not hold on the stated version, and
  does not qualify it. A qualified class name still ends up in a stylesheet, and
  a custom property that does not exist fails in a browser without an error.
- `bin/cli catalog:check` derives the bound; no judgement does. An entry holds
  on a version when every Sass file it names exists there. Every class and
  custom property it names that the newest covered version writes in its Sass
  has to be there too. For a custom element, so does its tag name in the
  TypeScript that defines it.

## Assumed

- What the core writes in its own Sass is the evidence. Classes Bootstrap
  generates from a state map, `btn-secondary`, `badge-secondary`,
  `callout-warning`, appear in no stylesheet on any covered version. So they are
  not evidence for or against. The TypeScript corpus in that check made an
  incidental v14 use of `btn-secondary` bind Buttons to v14.

## Wrong if

- An entry's markup changed while every name in it stayed. The derivation reads
  names, not structure, and would call that unchanged. Also a range with a hole
  in it, which `derivedSince()` reports as no bound at all. Such an entry needs
  a split rather than a number.

## Since then

`markupDigests` closes the first half of the **Wrong if**. Each entry records
what its demo said on every covered checkout and `bin/cli catalog:check` reads
them again. So a demo rewritten around identical class names passes the bound
and fails the digest. The check writes nothing, because a new digest is only
true once somebody has reread the entry. Ten entries name no demo or render the
component through a ViewHelper. For those the digest holds the demo rather than
the markup, and the command counts them. The second half stays as it was.

## Since then

`feedback/2026-08-19-090231` is a reader this entry did not have in view. A
session pasted `.table-fit` out of a stylesheet it does not own and wanted only
to know whether the class was still there. Run both ways on 2026-08-21, the
lookup answers unbound and withholds the entry at `12.4`. The class itself is in
the core's Sass on every covered branch. What binds the entry to v14 is one
custom property, which is the design at work. The answer holds an eleven-name
contract back whole so that a paste cannot carry a property that does not exist.
The price is what this read makes visible, and `D-CAT-006` answered it on
2026-08-21 with a second range over the class list.

## Confirmed on 2026-08-22

A session read both halves of **Wrong if** against the checkouts. The section
above closed the first. `bin/cli catalog:check` derives every bound and every
digest again. On 2026-08-22 it reports 26 components and 22 demos against 12.4,
13.4, 14.3 and main with nothing moved under them. The second names a behaviour
`derivedSince()` no longer has. It returns the newest unbroken run, and only an
entry that holds on every covered version returns nothing. The docblock still
described the old behaviour, and the same commit corrects it. No entry has a
hole either way.

The statement survives `D-CAT-006`. That entry adds a second derived range and
gives it a field of its own, `coveredClasses`. It leaves the entry's own bound
as it is, and one `derivedSince()` derives both. So the bound still sits on the
whole entry and a derivation still produces it rather than a judgement.
