---
id: D-ANS-136
title: The Fluid namespace source is chosen by the installed version
date: 2026-09-02
status: open
coveredBy:
  - FluidNamespaceListTest::aVersionBeforeFourteenReadsTheRuntimeConfiguration
  - FluidNamespaceListTest::aVersionBeforeFourteenWithoutTheKeyIsUnsupportedRatherThanEmpty
  - FluidNamespaceListTest::fourteenUsesTheFluidNamespaceCommand
---

# D-ANS-136 — The Fluid namespace source is chosen by the installed version

**`typo3_fluid_namespace_list` asks the console on TYPO3 14 and up, and the
booted container on every covered version below it.**

## Evidence

Read in `.checkouts/` on 2026-09-02:

- `fluid:namespaces` exists in 14.3 and main, as
  `fluid/Classes/Command/NamespacesCommand.php`, and is announced by the 14.2
  changelog. 12.4 and 13.4 have no such command, so asking it there returns the
  console's own "command is not defined".
- `Configuration/Fluid/Namespaces.php` exists six times in 14.3 and main and not
  once in 12.4 or 13.4, so the file fallback has nothing to read below 14.
- `SYS/fluid/namespaces` is populated in the `DefaultConfiguration.php` of 12.4
  and 13.4 and empty in 14.3, where the registration moved into those files.
- 14.3 still merges the `TYPO3_CONF_VARS` value in
  `ViewHelperResolverFactory::create()`, marked
  `@deprecated remove ... in TYPO3 v15.0`. Routing 14 and up to the command
  rather than to that value is what survives the removal.

This is the third case of the shape `D-ANS-077` and `D-ANS-052` decided: a
console command that arrived above the lines this server covers.

## Decided

- Above the boundary the console answers, and the package files are the fallback
  that says what it leaves out.
- Below it the container answers, because no command and no file carries the
  registry there.
- An absent `SYS/fluid/namespaces` is unsupported rather than none. Every
  covered version below 14 declares the key, so a reading without it went wrong,
  and an empty list would tell a template author that `f:` needs declaring per
  template.

## Assumed

- `Instance::typo3Major()` answers wherever an installation was found at all: it
  reads the core package's own `Typo3Version.php`, and a root without that
  package has no console and no container either.

## Wrong if

- A v15 line drops `SYS/fluid/namespaces` while a covered version still needs
  the container path. Then the boundary is no longer one number.
- An installation below 14 registers namespaces somewhere the container does not
  report. Then the container is not the complete source this assumes.
