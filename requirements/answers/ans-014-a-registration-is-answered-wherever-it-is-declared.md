---
id: R-ANS-014
title: 'A registration is answered wherever it is declared'
status: held
restsOn: [D-ANS-014, D-ANS-019]
heldBy:
  - ProjectTest::aFlexFormBoundThroughACallThisDoesNotReadIsStillReported
  - ProjectTest::aFormSetIsAnsweredWithTheDefinitionsItStores
  - ProjectTest::aSiteSetIsAnsweredByTheFilesCoreReadsItFor
  - ProjectTest::theFlexFormAContentElementBindsIsOnItsEntry
---

# R-ANS-014 — A registration is answered wherever it is declared

**`typo3_extension_describe` reports every registration the extension declares
in a file that stands still. It does not stop at the files the answer first
stood on.**

The answer lists fifteen kinds of registration. Three that a site package
ordinarily ships were in none of them. Each has a static declaration, and each
is reachable from a file the answer already opens or already names:

- The FlexForm a content element binds, from the calls below
  `Configuration/TCA/Overrides/`. The answer reports it against the identifier
  it belongs to, because a content element with a FlexForm and one without are
  different things to review. It reports a bound structure whose identifier no
  entry carries apart rather than drops it.
- What a site set carries beside its name and path: the files the core reads
  there by exact name, `route-enhancers.yaml` among them.
- The form storage an extension registers, both ways in, and the form
  definitions in it.

A session established which of those hold across the covered majors against
`.checkouts/` at 12.4, 13.4 and 14.3 before it wrote anything. Each of the three
moved inside that range.
[`D-ANS-019`](../../decisions/answers/ans-019-three-registration-kinds-are-read-the-way-core-reads-them.md)
carries the four call shapes, the eight file names and the two registration
ways, with what would show each wrong.

The boundary the entry does not cross is the file listing. Test files by path
and a walk of `Configuration/` are a tree. This answer states what a tree
cannot: what a declaration means, and which of the four artifacts is absent.

## From

`feedback/2026-07-31-194510`, a conformance audit of a TYPO3 14 site package
that fell back to `glob` and `read` for the whole file tree. Re-run on
2026-08-02 against `printworks_sitepackage`. Its two FlexForms, its
`route-enhancers.yaml` and its form set are in no answer this server gives. The
two content elements the answer describes least are exactly the two whose
FlexForm it did not read.
