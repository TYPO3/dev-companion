---
id: D-ANS-122
title: 'A backlog row says whether the code its report cites is still there'
date: 2026-08-27
status: open
coveredBy:
  - CitedCodeTest
  - ForgeTest::aRowSaysWhereTheCodeItsReportNamesStandsInTheInstalledPackages
  - ForgeTest::anIssueIsReadForTheCodeItsCommentsNameAsWell
---

# D-ANS-122 — A backlog row says whether the code its report cites is still there

**`typo3_forge_lookup` names the classes, methods and files an issue's own text
cites and says which of them the installed packages still ship.**

A stale issue's status stays as it was by definition, so the tracker cannot say
that a 2015 report is about code that is gone. Three of the five candidates one
session read in full were dead in exactly that way. It found out from a read of
the checkout.

## Evidence

- `feedback/2026-08-26-223257` counted the cost. Roughly 13 round trips of code
  reads rejected three candidates, against roughly 5 that confirmed the one it
  patched. Of the session's ~85 calls 12 were this server and ~72 were Bash
  reads of the checkout.
- The three it rejected are three different verdicts, and none of them is on the
  tracker. #71566 names `is_callable()` in an `ObjectAccess` that has since
  moved onto Symfony PropertyAccess. #78546 asks for something
  `ClassSchema::reflectMethods()` cannot carry. #78607 is already gone because
  `Result::forProperty()` has the type `?string` today.
- Re-run on 2026-08-27 through `ForgeLookup::answer()`. Issues 78607 and 71566
  both answer `answered`, and their payload carries `id`, `subject`, `status`,
  `tracker`, `priority`, `assignedTo`, `targetVersion`, `typo3Version` and
  `phpVersion`. It carries `createdOn`, `updatedOn`, `url`, `description`,
  `relations`, `attachments`, `reviews`, `noteCount`, `botNoteCount` and
  `notes`. Not one of those is about the code the report is about, so the
  feedback is not answered by the tool as it stands.
- `typo3Version` is the nearest field and says the opposite of what a triage
  needs. #78607 answers "6.2" and #71566 "7", which the answer itself calls what
  the reporter had rather than what it still reproduces on.
- The advice was already there and the read was still by hand.
  `ForgeLookup::workflow()` has printed five reads under a page of `stale` and
  `oldest` since `e12ec2c9` on 2026-08-25 13:37, a day before this session. Two
  of them, where the symptom appears and how far the mechanism reaches, are the
  read those 13 round trips were. So the gap is the performance of it and not
  the prescription.
- `bin/cli hints:probe "does the class this issue names still exist in the installed core"`
  matches nothing out of 109 candidate hints. This is not step 1a: no statement
  in the corpus would answer it and none should, because the answer is about one
  caller's own tree.
- The same session named the shape, as a strength rather than as a request.
  `feedback/2026-08-26-223414` credits the inline `reviews` field with the
  decision on four candidates without a Gerrit call. It asks for this verdict in
  that shape, a small field on the row a sweep reads without a second call.
- The source is precedented. `Source::Packages` is the files the installed
  packages ship, read rather than executed. `typo3_changelog_lookup` already
  declares `[Packages, Network]`, so a tracker answer that also reads the tree
  is not a new kind of tool.
- A read covered the page whole on 2026-08-27. `open="stale"`, `tracker="Bug"`,
  `updatedBefore="2020-01-01"`, `limit=25` answered 25 of 71, filed between 2010
  and 2018. The read covered every description and every comment of those 25.
  Thirteen notes is the most any of them carries, so the fifteen `Forge` returns
  never cut one.
- Fourteen of the 25 name a core class, a method or a core file somewhere in
  their text. The other eleven name a TCA key, a TypoScript path or a table
  column instead, and two of those carry no description at all.
- A `\TYPO3\CMS\…` namespace is in 8 of them: 5 inside a `<pre>` block, 4 in
  prose. It appears with the first backslash, without it, and doubled inside a
  PHP string. `Class::method` is in 3 and only #81102 carries it in the subject.
  `Class->method()` is in one, `Class::class` in one, `Class::CONSTANT` in one.
- A bare class name is in 10, and in 5 of those it is the only handle the report
  gives. Those are `ObjectStorage` (#79571), `RequestBuilder` (#82033),
  `ReferenceIndex` (#24299), `DatabaseConnection` (#72962), and
  `BackendUtility::setUpdateSignal` (#81102), which is a subject line and no
  namespace.
- A core file path is in one. #84998 pastes a stack trace that names
  `…/Classes/DataHandling/Localization/DataMapProcessor.php` at line 631. Five
  further issues name a `.php` file and not one of the five is a core file.
- A method is as often an English word as a call. "the readMM-method" (#75145),
  "Triggering setUpdateSignal" (#81102) and `_loadDefaultValues_` (#82033) carry
  neither parentheses nor a class. Redmine's own markup sticks to the name in
  `@PropertyMappingConfiguration@`, `_TcaColumnsOverrides_` and
  `_\TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRecordTypeValue_`.
- Placed against `.checkouts/main` and `.checkouts/12.4` with the extension key
  derived from the namespace's second segment, all 16 core namespaces resolved
  to a file. The two placeholder ones, `\Vendor\Ext\…` in #78546 and
  `MyComp\SomeModule\…` in #63810, matched no package at all.
- The verdicts are real and they move with the version.
  `TypoScriptFrontendController` (#63810) is gone on `main` and there on `12.4`,
  and `DatabaseConnection` (#63810, #72962) is gone on both.
  `ExtensionManagementUtility::makeCategorizable` (#61923) is gone on both while
  the class it sat on stands.
- A bare name matched by file basename lands on the wrong package. `Example`,
  which #78714 names as `Domain\Model\Example.php` of an example extension,
  matches two core test fixtures on `main`. `PropertyMappingConfiguration`
  matches `form` and `extbase`, and `ActionController` matches `extbase` and
  `extensionmanager`.
- Redmine's index answer carries `description` and no journal. A field on an
  enumerated row therefore costs no second read, and it sees what 9 of the 25
  cite in their subject or description. One more, #63810, cites only in a
  comment.
- A capitalised word is a class as often as it is a label. The first build of
  the field answered #88556 with eleven names, `PasteFromWord`,
  `HorizontalRule`, `ShowBlocks` and eight more. Those are the buttons a
  comment's CKEditor configuration removes, and the answer reported each as
  shipped by nothing. Neither the shape of the word nor a stop list of product
  names tells those from `DatabaseConnection`.
- What does tell them apart is in the reports. #72962 writes "the
  DatabaseConnection class", #82033 "Extbase RequestBuilder class", and the
  tracker's own `@…@` is the other mark. The button list carries none of the
  three. Restricted to a bare name the text marks or an installed package ships,
  the same page answers nothing for #88556. It keeps every name the read
  counted.
- Measured again on 2026-08-27 through the built field, against
  `.checkouts/main` at 15.0.0-dev and over the same page. 14 of the 25 issues
  read whole cite something, which is the count the read above made by hand. 12
  of the 25 rows do, three above the 9 a row's subject and description carry.
  The verdicts hold too: #61923's `makeCategorizable` and #72962's
  `DatabaseConnection` answer not shipped, #63810's
  `TypoScriptFrontendController::setSysLastChanged` answers not shipped on
  `main`, and #78546's `\Vendor\Ext\…` answers unplaced.

## Decided

- It is built. The measure is `D-FBK-027`'s. The question costs its caller a
  round trip per cited symbol, and a caller asks it once per candidate on a page
  of candidates. Both counts are in the report.
- It is a field on `typo3_forge_lookup` and not a tool of its own. Nobody asks
  the question apart from an issue, and a second tool would be a second call on
  every row. The six verbs have nothing this would be the seventh of.
- The field sits on a row of an enumeration as well as on an issue read whole.
  The sweep is where it pays, which is what `reviews` already demonstrates.
- It reads `Source::Packages` and nothing else — no boot, no console, no
  `Typo3Cli`. `.checkouts/` is this repository's own evidence and never a
  caller's tree, so it is not read here either.
- What it says is where a symbol stands, not whether the defect reproduces.
  Ranking candidates is what the report asked for, and it is what an extraction
  out of ten-year-old prose can carry.
- It reports a name it cannot place as unplaced and never as gone. A wrong
  "gone" discards a valid candidate unread, which costs more than the hand read
  it replaces. It is the one failure this may not have.
- The field extracts and places a bare class name too. The namespace alone
  answers 8 of the 25 and would report "no code cited" for the 5 whose only
  handle is a bare name. That is a wrong answer in the shape of an empty one.
- The field looks a bare name up by file basename below each package's
  `Classes/`, and one that matches two packages names both. Three of the ten
  read match two, and a pick of one of them is where a right-looking verdict
  lands on the wrong class.
- The field takes a bare name where the report marks it as code or an installed
  package ships one under it, and nowhere else. Its shape alone admits every
  capitalised word in the prose. A page of those answered "shipped by nothing"
  is a wall of verdicts about English where the evidence should be.
- The field reports a bare name no installed package ships as not shipped rather
  than as gone. That is true of `DatabaseConnection` (#72962), which core
  removed, and of a name that belongs to an extension the caller never
  installed. Nothing in the text tells the two apart.

## Assumed

- One session, which reported the cost twice — once as the gap and once as the
  shape to build it in. The corpus holds no second triage and a session files
  nothing unprompted, so that is one report rather than a rate.
- That a name a report gives bare means the class an installed package ships
  under it. The page read on 2026-08-27 holds the counter-case already:
  `Example` belongs to an example extension and matches two core test fixtures.
- That a caller on a triage of the core backlog stands in a tree that holds the
  core. That is a checkout for a contributor, and `vendor/` for everybody else.
  Both are `Source::Packages`.

## Wrong if

- A session reports that it discarded a candidate on a "gone" that turns out
  wrong. That would say the extraction claims more than it can place.
- A page of stale Bugs answers "nothing cited" for more than the eleven rows of
  25 the read counted. That would say the extraction is narrower than the forms
  of those reports.
- A row places a bare name on a package the report was never about, which the
  read found once before the field existed.
- A session reads the field and goes to the checkout for the same candidates
  anyway. That would say what decides a candidate is not where its symbols are.
