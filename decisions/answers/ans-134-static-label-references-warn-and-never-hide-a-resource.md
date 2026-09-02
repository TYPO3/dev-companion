---
id: D-ANS-134
title: Static label references warn and never hide a resource
date: 2026-09-01
status: open
coveredBy:
  - LabelSearchTest::aStaticReferenceIsNamed
  - LabelSearchTest::anUnreferencedResourceStaysVisible
---

# D-ANS-134 — Static label references warn and never hide a resource

**A label resource with no static reference stays in `typo3_label_lookup` and is
marked with a warning.**

## Evidence

- TYPO3 main's `LabelFileResolver::getAllLabelFilesOfPackage()` recursively
  enumerates XLF files below `Resources/Private/Language/` and
  `Configuration/Sets/`; it does not first establish a reference.
- `YamlSetDefinitionProvider::get()` selects `labels.xlf` beside a set's
  `config.yaml` when the manifest declares no `labels` value. That reference is
  implicit and no text search can find it.
- A translation domain is a string an application can assemble at runtime. A
  static scan can establish a reference it found and cannot establish that no
  runtime reference exists.

Both core classes were read on TYPO3 main on 2026-09-01. The official Site
folder reference lists the supported files below `config/sites/<identifier>/`
and names no XLF resource there, so a project-site XLF is reported as requiring
an explicit reference.

## Decided

- Search PHP, YAML, TypoScript, TSconfig, Fluid, JavaScript, TypeScript, JSON
  and XML files in the project and its installed TYPO3 packages for the exact
  file reference or its translation domain.
- Count the conventional site-set `labels.xlf` beside `config.yaml` as an
  implicit reference.
- Report the paths that carry a reference and warn where the list is empty.
- Keep an unreferenced resource in the results. Excluding it would turn an
  incomplete static reading into a runtime claim.

## Assumed

- A reference in executable or configuration source is useful evidence even
  where the branch that carries it is not active in this installation.
- Documentation prose is not a reference and is outside the scanned file types.

## Wrong if

- TYPO3 exposes a complete runtime usage graph for translation resources. Then
  that graph should replace the static warning.
- A reference can be established only by parsing each supported source language.
  Then plain containment produces enough false positives to mislead callers and
  the scan needs language-specific readers.
