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
answer says that an explicit reference is required. A resource for which the
scan finds no reference stays in the answer and is warned: a domain assembled at
runtime cannot be proved absent from source files.

The automatic site-set case is a reference of its own. A `labels.xlf` beside a
set's `config.yaml` is selected by TYPO3 without the path being written into
that file, and the answer names that implicit reference rather than warning that
none exists.

## From

The maintainer's request of 2026-09-01, after the package fallback was found to
enumerate every matching trans-unit without saying whether its resource could be
reached.
