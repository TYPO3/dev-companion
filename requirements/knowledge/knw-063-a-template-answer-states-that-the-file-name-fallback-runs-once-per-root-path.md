---
id: R-KNW-063
title: 'A template answer states that the file-name fallback runs once per root path'
status: held
restsOn: [D-KNW-052]
heldBy:
  - HintsTest::theFileNameFallbackIsStatedAsOncePerRootPath
  - HintsTest::theFluidFileExtensionIsWithheldWhereItDoesNotResolve
---

# R-KNW-063 — A template answer states that the file-name fallback runs once per root path

**A Fluid answer states that the file-name fallback chain runs once per root
path on v14, so a root registered later overloads an earlier one.**

The chain is `.fluid.html`, then `.html`, then the bare name, then those three
again with the name in uppercase. Which of the two extensions each file carries
decides nothing between root paths, and inside one directory `.fluid.html` wins.

That a bare `.html` still resolves is already in the corpus. It is enough to
stop a reviewer from a report of the old name as a defect. It is not enough to
say which of two files that both exist is the one that renders. An extension
that forks a core template ships that pair by construction: its own `Login.html`
beside the core's `Login.fluid.html`. Whether the fork renders at all, and which
file it shadows, are the two questions an audit of such an extension opens with.

The statement carries the version boundary of the rename with it, and three
consequences that follow from the same mechanism. An extension that still
supports TYPO3 below 14 may not use the new file extension. A template file no
longer has to begin with a capital, because the chain tries the requested form
first. A name written out with its own extension in `f:render` leaves the chain
altogether.

## From

`feedback/2026-08-03-164749`, a conformance audit of `EXT:guidedtour` against a
TYPO3 14.3.5 installation (2026-08-03). The extension forks the core backend
login layout and registers it with a layout root path appended while an event
runs. Whether that file still resolves, and whether its name is still right,
cost three shell round trips. It cost a read of `typo3fluid/fluid`'s
`TemplatePaths::resolveFileInPaths()` in the installed vendor tree.

Which root path is the later one is the half no changelog entry states, so the
statement carries that too. The list sorts by its integer key before the walk,
so the highest key wins rather than the last call. The sort skips the whole list
as soon as one key in it is a string.
