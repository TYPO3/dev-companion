---
id: D-KNW-044
title: One search over the whole Tests/ tree finds what asserts a rendered output
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aRenderedOutputChangeIsToldWhereTheExpectationsHide
  - HintsTest::theIterateNarrowlyNoteCarriesTheOneChangeItIsWrongFor
---

# D-KNW-044 — One search over the whole Tests/ tree finds what asserts a rendered output

**What asserts a rendered output is one search, aimed at the text around the
changed value rather than at the value. It runs over the whole `Tests/` tree,
not the files named `*Test.php`.**

`D-KNW-040` queued this as a gap and said what landed might be either a sentence
or a recipe with each shape named. Its first **Wrong if** was the sentence, and
the research found it. The eight shapes the feedback counted are eight ways to
write one expectation. They differ in how they encode the value, not in where
they live. Aim the search at what none of them encodes and one search reaches
them.

## Evidence

- Measured on `.checkouts/main` at `c71b2bdb2f`, against the 26 files
  `feedback/2026-08-02-145003` had to touch. Grepping `typo3/sysext/*/Tests/`
  for `fileadmin/|typo3temp/assets|Resources/Public/` reaches 24 of them, 188
  files to read. Restricted to `--include='*Test.php'` the same pattern reaches
  21, and 91 files — it loses all three
  `backend/Tests/Functional/Template/Fixtures/*CopyToClipboard.php`, which are
  where those expectations actually are.
- The two it never reaches hold no expectation. `ShortcutButtonTest` has a
  single `require sprintf(self::FIXTURES_PATH_PATTERN, ...)` and no literal of
  its own. The search reaches the fixture it loads, and that is what the edit
  changes. `FluidEmailTest` asserts no URI at that commit at all. Every
  assertion in its 482 lines is a content string, a media subtype or a render
  count. `\.svg|\.png|/Icons/|typo3/` matches one line, the extension path in
  `$testExtensionsToLoad`. The feedback's "quoted-printable-encoded text in an
  email body" is in the corpus, but in
  `core/Tests/Functional/Serializer/Fixtures/BoE4HIpmXv.message`. There the same
  cache-busted URI appears twice, once plain at line 442 and once soft-wrapped
  across lines 1201 and 1202 as `login-l=` / `ogo.svg?1730096904`.
- A second search adds nothing. `filemtime\(|->getSha1\(\)` reaches 12 of the 26
  on its own, and its union with the path search is still 24. Every file it
  finds, the path search already had.
- Searching for the value is what fails. `\?[0-9]{9,10}`, the rendered cache
  buster as it appears in the output, reaches 1 of the 26 and 2 files in the
  tree.
- That half the expectations are outside `*Test.php` is a property of the
  corpus, not of the asset area. `<a href=|<img src=|<script src=` sits at 44 of
  71, `data-[a-z-]+="` at 30 of 48, `&amp;|&lt;` at 49 of 79. That is a third to
  a half outside in each case. The non-`*Test.php` half is `.php` fixtures that
  return a heredoc (52), `.csv` (23), `.xml` (14), `.typoscript` (4). And one
  each of `.zip`, `.message` and two `.htaccess_*`.
- The shapes carry no version range. On 13.4 at `1d104f3b95` the path search
  reaches 179 files of which 85 are `*Test.php`. On 14.3 at `faf60eea22` 192 of
  which 94, against main's 188 of which 91. `contentMatchRegExp` is in 2 files
  on all three, the quoted-printable fixture in 1, the `{$...}` placeholder
  fixtures in 23, 23 and 25. Only `filemtime()`/`getSha1()` differs, 5 files on
  13.4 against 23 and 24. That is the cache-busting change itself, not where an
  expectation lives.

## Decided

- The statement lands on `core-tests` and says both halves, because either one
  alone leaves the search wrong. The tree without the aim finds the value
  nowhere, the aim without the tree finds half the files.
- The shapes are still named, as the reason for the aim rather than as a recipe
  to run. A caller told to search around the value and not why will search for
  the value.
- The exception goes beside the iterate-narrowly note in
  `knowledge/test-suite-hints.json`, which every `typo3_test_run_guide` answer
  emits. It points at `core-tests` rather than restates it. Told only to run the
  whole suite, the caller is back where the feedback started.
- `D-KNW-040` stands revoked rather than confirmed. Its **Wrong if** that names
  the single search is what happened. Its statement, that this is absent from
  the server, no longer described the server from this commit.
- No tool gets built, which `D-KNW-040` decided and this visit does not disturb.
  The search runs in the caller's checkout and this server does not read it.

## Assumed

- That the aim generalises past a resource URI. The measure ran on one, and the
  markup markers had a count but no run against a known set. No other
  rendered-output change in this repository's feedback has one.
- That the stable neighbourhood is findable by the caller. Around a cache-busted
  URI it is the resource path, which is obvious. Around a changed attribute name
  or a changed tag it may be the thing that changed.

## Wrong if

- A rendered-output change arrives whose value has no stable neighbourhood — the
  whole rendered token changes — and the search reaches nothing. The statement
  then owes a second move, and "search around the value" is what sent the caller
  on the search for one.
- The soft wrap lands inside the marker rather than beside it. `Resources/`
  survived intact in `BoE4HIpmXv.message` by where the 76th column fell. A
  quoted-printable fixture that breaks mid-marker is a file this search does not
  find at all.
- A session follows the statement, searches once, and still finds the rest
  through a run of the suite. The 24 of 26 came from one change. A second change
  that reaches materially fewer means the shapes did belong to the asset area
  after all.
