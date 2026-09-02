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

**An installation-backed answer is not lost to a console that settles nothing
where the files hold it anyway.**

`typo3_label_lookup` falls back to the XLF files of the same packages, and
`typo3_fluid_namespace_list` to their `Configuration/Fluid/Namespaces.php` on
the versions that have one; both report `answeredBy: "packages"` and name what
the weaker source leaves out. Below TYPO3 14 there is no such file and the
container answers instead, so that tool has no file fallback there —
`D-ANS-136`. A console that exits successfully and prints neither a payload nor
the words it prints for an empty result settled nothing and takes the same
route: an exit code of 0 is not an answer. Where nothing can answer, the failure
is diagnosed rather than passed through: a query against a missing table means
the database has no schema, not that the installation is broken.

## From

An installed TYPO3 13.4.33 before the dump was imported, where the labels sat in
the files and both console-backed lookups returned a raw SQL stack trace
(2026-07-29).
