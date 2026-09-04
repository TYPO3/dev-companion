---
description: >-
  The changelog entry a core patch owes: which of the four types it is, which directory it goes into when the change is backported, and what reports a file that is wrong.
whenToUse: >-
  When a core change adds, removes, deprecates or announces something an installation notices, and when a review asks for the entry.
hints:
  - documentation-changelog
---

# The Changelog Entry a Core Patch Owes

Source: `typo3/sysext/core/Documentation/Changelog/Howto.rst` in a core
checkout, which is the authority on all of this, and
`Build/Scripts/validateRstFiles.php`, which reports what a file is missing.

## Which Change Owes a Changelog File

This is the changelog obligation per change type: which change types owe a
changelog entry, which owe none, and what the entry a review asks for has to be.
A `BUGFIX` owes none, a `TASK` owes none, and the four types below are the whole
list.

- The type is the first of four that describes the change: `Breaking` where it
  moves or removes core functionality that may break or affect third-party code,
  `Deprecation` where it marks core functionality for a planned removal,
  `Feature` where it adds functionality, and `Important` for anything else that
  may require manual action.
- `Important` is the last resort, and the only one of the four an LTS release
  may carry.
- A casual bug fix owes no entry, because its commit message carries the
  information. Casual is the fix that changes nothing an installation renders,
  is configured by, or has documented. Demanding one of a `BUGFIX` that changes
  none of the three is a review defect of its own.
- A `BUGFIX` that does change one of the three owes an `Important`, and that is
  the entry a review asks such a fix for. The keyword is what the obligation is
  read off in the common case and not what settles it.
- `@internal` on the changed member does not exempt it either. The test is what
  the fix changes for an installation, and the core has filed entries for
  `@internal` members on the ground that extensions used them —
  `Important-107848-DataHandlerPropertiesUseridAndAdminRemoved.rst` in the 14.0
  directory is one.
- `Breaking` reaches past a moved PHP member. `affect` in that definition covers
  a change in what an installation renders or is configured by, and which of
  those the core files as breaking, and where the boundary against `Important`
  runs, is `typo3_hint_lookup` with the id `breaking-without-a-moved-member`.

## Where a Changelog File Goes

- Write the entry below `typo3/sysext/core/Documentation/Changelog/`, into the
  `<lts>.x` directory of the oldest branch the `Releases:` trailer names. A
  change that names `main` alone goes into the directory of the minor version
  `main` is developing.
- That trailer decides the directory and the branch the patch is written on
  decides nothing, so a backport's file stands in the same directory on every
  branch that carries the change.
- Add the entry to both `.x` directories where two maintained lines take the
  change, on each branch that has both, and each release branch carries the
  directories it has. It happens for the fixes an old LTS still takes, so it is
  `Important` in practice.
- Entries are never deleted from a younger branch, which is why `main` carries a
  directory per maintained line.
- The third example in `Howto.rst` names a directory and then says the branch
  does not have it. The rule its other two examples state is the one that holds.

## What a Changelog File Is Called

- The name is `<Type>-<forgeIssueNumber>-<UpperCamelCaseDescription>.rst`, with
  the type spelled as one of the four.
- The prefix is the registration. Each directory's `Index.rst` pulls the types
  in by a glob toctree, so the file is added and nothing else is edited.
- A directory that has replaced a type's toctree with a sentence carries no file
  of that type, which is what the LTS directories do with `Breaking`.
- `Task` is a commit message keyword and not a changelog type. Those four are
  the whole list, and `checkRst` fails a title opening with anything else.

## What a Changelog File Carries

- The core ships the skeleton: `Build/rstTemplates/rstTemplate<Type>.rst`, with
  `{ISSUE}`, `{TITLE}` and `{TIMESTAMP}` to substitute. Copying it is cheaper
  than assembling the same file from a neighbouring entry, and
  `typo3_hint_lookup` with the id `documentation-changelog` says what each part
  of it is for.
- `./Build/Scripts/runTests.sh -s watchRst core interactive` writes that file
  from the template, asks for the type, the issue and the title, and renders it
  live while it is edited. It writes into the directory of the release the
  branch is developing, so a backport's file is moved afterwards.
- Every type has a Description section. A `Feature`, a `Deprecation` and a
  `Breaking` have an Impact section, and a `Deprecation` and a `Breaking`
  additionally have a Migration section.
- What a type owes is not what its entries carry. An `Important` owes no Impact
  section and plenty of them carry one; the Affected installations section is in
  the `Deprecation` and `Breaking` templates and in a minority of their entries.
- The `Important` template is the one that disagrees with the tree: it ships
  Affected installations and Migration, which few `Important` entries carry, and
  no Impact, which many do. Read a neighbouring entry in the directory the file
  is going into before copying it.
- A `Deprecation` or `Breaking` entry also commits the patch to an extension
  scanner matcher, and which matcher file takes which kind of removal is
  `typo3_rule_lookup` with `documentId` `core/contribution/commit-messages`.

## What Checks a Changelog File

- `./Build/Scripts/runTests.sh -s checkRst` runs
  `Build/Scripts/validateRstFiles.php` over the whole changelog tree and names
  the piece each file is missing.
- `./Build/Scripts/runTests.sh -s checkExtensionScannerRst` holds a matcher to
  the changelog file it names, in that direction only.
- Which of the four types the change owes is what neither reports. A wrong type
  passes every suite and is caught in review or not at all.
- `./Build/Scripts/runTests.sh -s checkRstRenderingSingle core` renders the
  whole changelog into `Documentation-GENERATED-temp/` for reading.

## Reading Changelog Entries Instead of Writing One

- All of the above is the authoring side. An installation reads the same files:
  they ship with the core package, and `typo3 upgrade:list` and
  `typo3 upgrade:run` are what acts on the migrations behind them.
- What a version broke, deprecated, added or noted is `typo3_changelog_lookup`,
  which answers from the installation and from the published changelog rather
  than from a checkout.
