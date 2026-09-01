---
id: D-ANS-132
title: 'The domain answer carries the form a module imports'
date: 2026-09-02
status: open
coveredBy:
  - VersionsTest::theDomainIsAnsweredInTheFormAModuleImportsIt
---

# D-ANS-132 — The domain answer carries the form a module imports

**`typo3_translation_domain_lookup` answers the domain and the specifier a
backend JavaScript module imports it under, which is the same value in the form
that module needs.**

A caller holding `core.core` had to know by itself that the value goes into
`import labels from '~labels/core.core'`, and the corpus states that one call
away.

## Evidence

- `feedback/2026-09-01-210422` asked for it: the tool computes the domain and
  the caller has to know that it belongs in an import specifier.
- The corpus already states the import. `javascript-labels` carries the form,
  `labels.get()`, and what a missing key throws — a call the caller has no
  reason to make while holding a domain.
- The two arrivals are one. The domain resolver and the `~labels` import map
  prefix are both the major `TranslationDomainLookup::SINCE` names, so the
  branch that withholds a domain is the branch that has no import either, and
  neither needs a second boundary.

## Decided

- A field rather than a longer sentence: `moduleImport`, beside `domain`, so the
  value is read rather than parsed out of prose. `D-KNW-005` is why an answer's
  data is where a value goes.
- Returned where a domain was handed over and absent otherwise, which is how
  `domainOnNewerVersions` is already written. On a version below the domains
  there is nothing to import, and the withheld answer already says what to write
  instead.
- One line in the text as well, because the text is the answer a model reads
  first and the field is what a client composes with. Both are built from the
  same value, so they cannot disagree.
- The description says the answer carries it and does not spell the form. A
  description that carries a value is a second place the value lives.

## Assumed

- That a project extension's backend module imports the same way. The prefix is
  resolved by the backend for whatever domain is asked of it, which is what
  `javascript-labels` states, and no project module was read for this.

## Wrong if

- The import map prefix moves, or a module reaches a label another way, which
  would make the field a second form to keep true beside the hint.
- A caller wants the specifier for a frontend module, where the backend's import
  map does not reach and the field would read as an answer.
