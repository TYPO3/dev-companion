---
id: D-DIS-001
title: The root package counts as an installed package
date: 2026-07-29
status: confirmed
coveredBy:
  - InstanceTest::aMonorepoRootIsCountedBesideThePackagesItHolds
  - InstanceTest::aRootAlsoInstalledIntoVendorIsOnePackage
---

# D-DIS-001 — The root package counts as an installed package

**In an extension development checkout the root package is one of the installed
packages, and a root package on its own is not an installation.**

## Assumed

- In an extension development checkout, the extension under work is the root
  package, and every answer about "this installation" includes it. Its icons and
  labels are as registered as any dependency's.
- A root package alone is not an installation. The root only joins when
  Composer's metadata yielded packages. So an extension repository whose
  dependencies nobody installed still reports no installation rather than one
  that holds a single package and no console.

## Wrong if

- A monorepo whose root declares a TYPO3 package type but is not the thing under
  work. Or a setup that installs the root into the vendor directory as well; the
  two entries then resolve to the same realpath under one key. That is the
  intent, but nobody here has seen it in the wild.

## Confirmed on `2026-08-01`

Both shapes exist as fixtures, which is as far as this goes without an
installation. The second collapses as the entry said. A root required through a
path repository resolves to one realpath, so it stays the project's own. A read
of the vendor path would have made the extension under work a dependency of the
repository it is. The first displaces nothing. What a fixture cannot settle is
what the extra entry costs a monorepo somebody works in. This entry holds no
guess at which roots are containers, because nothing in the metadata carries it.
