---
id: R-ANS-020
title: 'The Classes section covers the directory it names'
status: held
restsOn: [D-ANS-045]
heldBy:
  - ProjectTest::aFluidRootIsRenderedAsADirectory
  - ProjectTest::everyDirectoryBelowClassesIsInTheAnswer
---

# R-ANS-020 — The Classes section covers the directory it names

**`typo3_extension_describe` names every directory below `Classes/` and counts
every PHP file under it. Nothing it reads off the file tree poses as a
registration.**

The answer promises the shape of an extension's `Classes/` directory. It
recognised thirteen directory names and dropped anything else. That went under
no other kind, into no total, absent. A caller who checked the section against
`find` got a different answer and concluded the section is wrong. That is
[`D-ANS-008`](../../decisions/answers/ans-008-a-number-a-reader-cannot-reproduce-is-read-as-wrong.md)
one level up: there it was a number nobody could reproduce, here it is the list.

The same holds for what this answer states with no declaration behind it.
`fluidRoots` is three `is_dir()` calls. So the line that renders it stands apart
from the service tags and the middlewares. It says the extension ships the
directory rather than that it declared a root.

## From

`feedback/2026-08-03-164651`, a conformance audit of EXT:guidedtour against a
TYPO3 14.3.5 installation. Re-run on 2026-08-03.
`Classes/Utility/MascotResolver.php` is in no line of the answer. It is the only
class in that extension that touches the public-resource URL API the audit asked
about. Measured against `.checkouts/14.3` the same day, `core` reports 106 of
the 1508 PHP files below its `Classes/`.

## Held by

- `ProjectTest::everyDirectoryBelowClassesIsInTheAnswer`, the first half. The
  answer names every directory below `Classes/`, counts a PHP file directly in
  it where it lies, and its total is the number `find` gives.
- `ProjectTest::aFluidRootIsRenderedAsADirectory`, the second half: the line
  says the extension ships the directory rather than declares it.
