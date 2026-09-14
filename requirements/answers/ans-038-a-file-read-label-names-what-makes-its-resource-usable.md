---
id: R-ANS-038
title: A file-read label names what makes its resource usable
status: held
heldBy:
  - LabelSearchTest::aNonStandardLabelFileIsWarned
  - LabelSearchTest::aProjectSiteLabelFileIsReadBesideAnEmptyConsoleAnswer
  - LabelSearchTest::aSiteSetLabelsFileCarriesItsImplicitReference
  - LabelSearchTest::aStaticReferenceIsNamed
  - LabelSearchTest::anUnreferencedResourceStaysVisible
---

# R-ANS-038 — A file-read label names what makes its resource usable

**A label read directly from a file says whether its name follows the convention
of its directory and where a static reference reaches it.**

`typo3_label_lookup` also reads XLF files below the project's `config/sites/`
directory. TYPO3 does not discover those as package language resources, so the
answer says that they need an explicit reference. A resource for which the scan
finds no reference stays in the answer with a warning. Nothing can prove a
domain assembled at runtime absent from source files.

The automatic site-set case is a reference of its own. TYPO3 selects a
`labels.xlf` beside a set's `config.yaml` without the path written into that
file. The answer names that implicit reference rather than warns that none
exists.

## From

The maintainer's request of 2026-09-01. A session had found that the package
fallback enumerates every trans-unit that matches without a word about whether
its resource is reachable.
