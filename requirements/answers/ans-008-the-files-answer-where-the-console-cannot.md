---
id: R-ANS-008
title: 'The files answer where the console cannot'
status: held
heldBy:
  - LabelSearchTest::aConsoleThatCannotBootIsAnsweredFromTheFilesItWouldHaveRead
  - LabelSearchTest::aConsoleThatExitsWellAndSaysNothingIsUnanswered
  - LabelSearchTest::aConsoleThatExitsWellAndSaysNothingUsableEstablishesNothing
  - LabelSearchTest::aDatabaseWithoutASchemaIsNamed
  - PackageSourcesTest::withoutAConsoleTheDeclarationsAreTheAnswerAndSaySoAsOne
  - Typo3CliTest::aFailureIsDiagnosedOnlyWhereTheMessageDoesNotSayEnough
---

# R-ANS-008 — The files answer where the console cannot

**A console that settles nothing does not cost an installation-backed answer
where the files hold it anyway.**

`typo3_label_lookup` falls back to the XLF files of the same packages, and
`typo3_fluid_namespace_list` to their `Configuration/Fluid/Namespaces.php` on
the versions that have one. Both report `answeredBy: "packages"` and name what
the weaker source leaves out. Below TYPO3 14 there is no such file and the
container answers instead, so that tool has no file fallback there
(`D-ANS-136`). A console that exits with success and prints neither a payload
nor its words for an empty result settled nothing. It takes the same route. An
exit code of 0 is not an answer. Where nothing can answer, the tool diagnoses
the failure rather than passes it through. A query against an absent table means
the database has no schema, not that the installation is broken.

## From

An installed TYPO3 13.4.33 before the dump import. The labels sat in the files,
and both console-backed lookups returned a raw SQL stack trace (2026-07-29).
