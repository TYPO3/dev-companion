---
id: R-PRJ-005
title: 'What an extension registers is answered from the installation and its files'
status: held
heldBy:
  - ProjectTest::aContentElementRegisteredWithAddRecordTypeIsFoundAsWell
  - ProjectTest::aPatchedDependencyIsPartOfWhatThisProjectIs
  - ProjectTest::aRegistrationBuiltInALoopIsNotDeterminable
  - ProjectTest::anExtensionTheInstallationLacksIsAMissWithTheKeysItHas
  - ProjectTest::theContentElementsAnExtensionAddsAreNamed
  - ProjectTest::whatAnExtensionRegistersIsReadFromItsOwnFiles
  - ProjectTest::whatTheInstallationHasBeatsWhatTheFilesCouldBeReadFor
---

# R-PRJ-005 — What an extension registers is answered from the installation and its files

**The answer says what an extension registers, per extension. The booted
installation answers for the registries that have no file behind them, and the
extension's own files answer for the rest.**

The answer covers the tables its TCA defines and the ones it extends, and the
content elements it adds. It covers its backend modules and routes, and its
icons. It covers its site sets, its service tags, its middlewares, and its Fluid
roots and namespaces.

The three the installation owns are its tables, its content elements and its
icons. The runtime assembles TCA and the icon registry, and they belong to no
package. So the `EXT:<key>/` reference an entry carries attributes it back to
this extension. That is a `LLL:EXT:` label or ctrl title, or an icon the
registry resolves to a file below that extension. Where nothing attributes an
entry, it is the installation's rather than a package's. Where the installation
could not boot, the parsed list stands and the answer says what it leaves out.

The answer reads the table an override file extends from what the file does,
never from its name. Extensions number those files to fix their load order, and
write one file per element. The content elements are the identifiers of the
items it adds to `tt_content.CType`, in both the positional and the keyed item
shape. They come through either call that adds one. `addTcaSelectItem()` names
the table first, and `addRecordType()` takes its table as its fifth argument
with `tt_content` as the default. The pointer at `tt_content` that says where
the elements register is not the element. Each carries the template it renders
through, read from its own TypoScript and left unknown where that says nothing.
A template name derived from the identifier sends the caller to a file that is
not there. The parser reads a file that returns an array for *that* array and
for no other literal in it. Where only a run of the file knows its list, the
parser returns nothing rather than the keys of the literal beside it. That is a
list built in a `foreach` or assembled into a variable. It names the file that
hit that floor rather than leaves it as an omitted section. That is the parser's
floor, and the booted installation is what raises it. The answer carries what
the extension declares. It names what the extension does at runtime as not
covered rather than guesses it. The project's Composer patches are part of what
the project is.

## From

An evaluation for a site with a sitepackage and its own extension, where the
scope named the extension and nothing inside it (2026-07-29). A session whose
CType, registered with `addRecordType()` in a file of its own, came back as no
content element at all (2026-07-30). A `REVIEW-02` run against
`georgringer/news` (2026-07-31) that the answer told the extension registers the
icons `provider` and `source`. Those are the keys of the literal its `foreach`
builds each icon from.
