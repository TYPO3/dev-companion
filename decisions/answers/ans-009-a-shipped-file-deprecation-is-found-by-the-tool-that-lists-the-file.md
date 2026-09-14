---
id: D-ANS-009
title: A shipped-file deprecation is found by the tool that lists the file
date: 2026-08-02
status: confirmed
coveredBy:
  - ExtensionTest::aFrameworkPackageIsExemptFromBoth
  - ExtensionTest::anIconBelowResourcesIsWhatSilencesTheRootOne
  - ExtensionTest::declaringOneOfTheTwoFieldsStillReadsTheFile
  - ExtensionTest::theRenamedFileBesideItIsWhatSilencesTheOldOne
  - HintsTest::noHintStatesSomethingThatOnlyHoldsOnOneBranch
  - ProjectTest::theDeprecatedFilesBlockNamesEveryPredicateItLookedAt
  - ProjectTest::theOrientationAnswerCarriesTheVerdictForTheRepositorysOwnExtensions
---

# D-ANS-009 — A shipped-file deprecation is found by the tool that lists the file

**The tool that lists a file the extension ships finds the deprecation whose
predicate is that file. A changelog sweep over what the code calls does not.**

`typo3_extension_describe` prints both file names this feedback is about, in one
line beside four files nothing is wrong with, and says nothing about either.

## Evidence

- The query of `feedback/2026-07-31-172757-…` re-run over
  `/home/benji/projects/bootstrap_package` today answers
  `Registration files: ext_localconf.php, ext_tables.php, ext_tables.sql, ext_emconf.php, Configuration/Services.yaml, Configuration/JavaScriptModules.php`.
  The two predicates are in the answer as a file listing, and
  `ext_localconf.php` beside them carries nothing.
- Both predicates hold, read at their trigger sites in `.checkouts/14.3`.
  `Configuration\Extension\ExtTablesFactory` raises `E_USER_DEPRECATED` in
  `createCacheEntry()` and in `loadSingleExtTablesFiles()` for every active
  package that has an `ext_tables.php` and is not `isFrameworkType()`.
  `Package\PackageManager::getComposerManifest()` raises it where
  `isComposerOnlyCapable()` is false, which is `providesPackages` unset, or
  neither a top-level `version` nor `extra.typo3/cms.version`.
- The feedback dates both to v14.3. `#109438` is 14.3; `#108345` is 14.2, and
  its changelog states no impact on a Composer-based installation. A signal that
  reads "composer.json missing version/providesPackages: yes" would therefore be
  true, and the Composer majority would misread it. `bootstrap_package` declares
  neither field, so both fire for the extension under review.
- The `extension-files` hint of `knowledge/architecture-hints/php.json` already
  carries `#108345` whole, bound `since 14 until 14`. It names
  `failOnDeprecation` as what surfaces it, which is how the session that
  reported it found it. `#109438` is in `knowledge/` nowhere: `ext_tables.php`
  occurs once, as an `appliesTo` string, and `bin/cli hints:probe` reaches that
  hint whose text is silent on the file.
- `typo3_changelog_lookup` answers `ext_tables.php` with `#109438` today, which
  it did not on the report's date — `D-ANS-006`. That closes the retrieval half
  and not this one. A reviewer with no rule that says the file matters has no
  reason to type its name into a changelog.
- The extension-key sweep `feedback/2026-07-31-172753-…` asked for would not
  have reached either entry. Both carry the tags `ext:core` and `NotScanned`,
  because both describe what core does with any extension's files rather than an
  API that extension calls.

## Decided

- Queued rather than closed on the spot. The lever is
  `typo3_extension_describe`, so the change touches `src/` and a declared
  `outputSchema`, and the predicates needed the checkouts. Those are the two
  things `documentation/records/judging.rst` puts on the far side of that line.
- Step 2 for the `ext_emconf.php` half: the rule is here, complete and bound,
  and the answer that names the file does not carry it. Step 1a then step 2 for
  the `ext_tables.php` half, since the statement it would deliver does not
  exist.
- Not step 1b. No verb and no skill is absent. `typo3_extension_describe`
  already reads every file both predicates turn on, and
  `typo3-extension-conformance` is the skill the review ran under.
- No requirement yet, and the todo names no field. Whether this is a hint, a
  section of the extension answer, or both is not established by a run that read
  only this repository.

## Assumed

- The reviewer would have acted on a signal in the answer it already read. That
  is what the feedback reports about itself and nothing here can check it.

## Wrong if

- ~~The statement turns out unwritable where it has to go. A hint binds by major
  integer, because `since` and `until` carry `12` to `15` across all of
  `knowledge/`. `#109438` holds from 14.3, so `since: 14` is false for 14.0 to
  14.2. `HintsTest::noHintStatesSomethingThatOnlyHoldsOnOneBranch` forbids the
  minor in the text.~~ Answered on 2026-08-02, in the read below. A statement
  that starts to hold inside a major already binds to the whole of it here. So
  `#109438` loses the granularity `#108345` had already lost.
- A later feedback reports the opposite cost. A caller reads an extension answer
  that volunteers deprecations as a compatibility verdict. It treats the absence
  of a signal as a clean bill for the next major.

## Since then

Step 1a landed and the first **Wrong if** did not bite. It assumed a mid-major
arrival would be a new imprecision, and it is the one the corpus already
carries. The statement next to it binds to the whole major and has been green
all along. So the entry consists of two statements. The deprecation binds to one
major, reads at both trigger sites and says that a cached request raises
nothing. The removal binds to the next, where the class is gone and a
registration left there is lost without a report.

## Confirmed on 2026-08-03

The statement holds, reported by a session that had never read this entry. It
calls the finding one it could not have derived from a file listing, for the
reason this entry rests on. The trigger is the presence of the file rather than
anything the extension calls. It reproduces.

The second **Wrong if** did not fire and what arrived is its opposite. Nothing
read the block as a compatibility verdict, and what the session could not read
is which files the tool looked at. The confirmation was in the answer it already
had, and its own quotation dates the copy it ran against.

## Since then

The set is four. A sweep of the changelogs for a file name rather than for an
API found the two the answer lacked: 213 distinct names, two hits. One
deprecates the extension icon at the root. On the oldest covered major that
raises a deprecation, and the newer ones simply do not look for it. The
extension appears without an icon and nothing lands in a log. The other stopped
the include of two TypoScript files before the covered range starts. The session
still read both sides, and the file is inert on every version this server
covers, with no message and no log entry.

## Since then

One read carried this entry out and established nothing beyond it. Two shapes
were open and it is the sentence: the block closes with the coverage stated once
rather than a line per file.

The orientation answer volunteers the verdict for the extensions inside the
repository since 2026-08-27. It stays the named tool's for the one a caller
names. A session held that tool's description complete and in context under a
client that defers, and made no call. So a louder name for it is the alternative
that had already failed. Read for an extension of origin `project` alone: a
dependency's files are its maintainer's.
