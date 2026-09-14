---
id: R-KNW-069
title: 'A new backend label is told what it costs before it resolves'
status: held
restsOn: [D-KNW-076]
heldBy:
  - HintsTest::aNewBackendLabelIsToldWhatItCostsBeforeItResolves
  - HintsTest::theLabelModuleIsWithheldFromTheMajorsThatHaveNone
---

# R-KNW-069 — A new backend label is told what it costs before it resolves

**A new label for a backend JavaScript module owes a cache flush and a browser
hard reload, and no check before runtime says so.**

Both halves are necessary, and either alone leaves the caller where the report
found them. The `l10n` cache keys the parsed file on the domain and the locale
and on nothing about the file. So the bundle rebuilds only after a flush of
group `system`. The response carries a year of `max-age` at a URL whose
`cacheBustInfix` is the package list rather than the label files. So the page
keeps the import it already has. A flush without the reload changes nothing
visible and reads as a flush that did not work.

The third demand is the symptom, in the words somebody arrives with. The runtime
throw is `Label is not defined: <key>`, and it is the only signal there is. The
type stub regenerates from the same XLF, so the key type-checks, the editor
completes it and the build passes. A caller who trusts the green build looks for
the defect in the module.

The reach is the fourth. The question arrives from a TypeScript task and from an
XLF one, and the answer holds from 14, where the module exists at all.

## From

`feedback/2026-08-13-215652`, an agent's own project note kept because it "most
often saves a session from debugging the wrong file". Its query reached
`language-files`, `backend-typescript` and `backend-ui`, none of which names the
JavaScript label bundle (2026-08-13).
