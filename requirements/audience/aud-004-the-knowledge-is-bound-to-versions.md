---
id: R-AUD-004
title: 'The knowledge is bound to versions'
status: held
heldBy:
  - CatalogTest::aComponentNotVerifiedOnTheTargetIsDeclined
  - CatalogTest::aComponentVerifiedOnTheTargetCarriesItsRange
  - CatalogTest::anInstallationWithoutDomainsIsGivenTheFileReference
  - CatalogTest::everyRecordedBindingNamesACoveredVersion
  - CatalogTest::theCatalogSaysHowItRelatesToTheInstallationBeingRead
  - CatalogTest::theCatalogSaysHowMuchOfItWasVerifiedOnAStatedVersion
  - CatalogTest::withoutATargetEachEntryCarriesItsRange
  - InstanceTest::theTypo3VersionIsReadFromTheCorePackage
  - VersionsTest
---

# R-AUD-004 — The knowledge is bound to versions

**The knowledge binds to versions.**

`knowledge/versions.json` declares which TYPO3 lines the knowledge covers. A
statement that does not hold on all of them carries `since`/`until` as data
rather than says so in its prose. An answer is for the version the caller
stated, else for the one the installation under read runs, else for none. Then
every statement comes back with the range it holds for. A catalog entry carries
the same bound for the whole entry, because markup and a class list go together
in a paste. A target version withholds it rather than qualifies it: a class that
is not there fails in a browser without an error. The answer names what it
withholds, with what to verify it against, so silence never reads as "does not
exist". The markdown documents carry no range and no filter reads them; a prose
answer says so and names where the bound form is.

## From

V15 markup handed to a 13.4 caller, and a translation domain handed to an
installation that resolves none (2026-07-29).

## Held by

- `VersionsTest` in full — the range model, the precedence, the filter, that
- No statement dates itself in prose, and that a prose answer says it is not the
  bound half
