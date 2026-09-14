---
id: D-COD-003
title: A directory is read through symfony/finder
date: 2026-08-02
status: confirmed
coveredBy:
  - StructureTest::everyDirectoryIsReadThroughTheFinder
---

# D-COD-003 — A directory is read through symfony/finder

**This package reads every directory with `symfony/finder`: `glob()`,
`scandir()` and `RecursiveDirectoryIterator` appear nowhere in `src/`, `bin/` or
`tests/`.**

There were two idioms for one question. A flat listing was `glob()` with `?: []`
behind it. A recursive one was a `RecursiveIteratorIterator` with an
`instanceof` check, an extension comparison and a `sort()` after it. The second
shape ran to a dozen lines and appeared eleven times, five of those as a
directory removal copied between test classes.

## Evidence

- Written on 2026-08-02, with all 45 call sites converted at once. That was 20
  flat, 11 recursive, 6 removals, plus the three flat ones that did something
  `glob()` cannot. `composer ci` ran before and after. The session compared the
  outputs of `bin/cli todo:list` and `bin/cli unresolved:list` across the
  change. `symfony/finder` was already in the tree as a dev dependency of
  php-cs-fixer, and it requires nothing but PHP.

## Decided

- `symfony/finder` in `require` rather than `require-dev`, because half the call
  sites are on the server's own answer paths: the installation's extensions, its
  labels, its changelog. A directory that may be absent now says so with
  `is_dir()`, since Finder throws where `glob()` returned nothing. The tolerance
  was implicit and now stands at every site that relied on it. `GLOB_BRACE` is
  gone with it, which is a portability question rather than a style one. The
  constant does not exist wherever the C library has no brace expansion.

## Assumed

- That nothing below `knowledge/`, `scenarios/`, `skills/`, `todo/`, `src/` or
  `tests/` is a dot file. Finder ignores those by default and the old recursive
  walks did not. So a hidden markdown or PHP file would now pass unread by
  `Prose`, `ToolNamingTest` and `StructureTest`. A removal turns the default
  off, because a hidden file left behind is one the closing `rmdir` fails on.

## Wrong if

- Finder stops to walk a directory before what is in it. `Directory::remove()`
  and `Installer::removeDirectory()` take `reverseSorting()` to mean
  deepest-first, which holds because the traversal is `SELF_FIRST` and nothing
  else. The failure is loud, `rmdir` on a directory that still has something in
  it. The fallback is the explicit sort by depth, which does not depend on the
  traversal order at all.

## Confirmed on 2026-08-22

The idiom held. `glob()`, `scandir()` and `RecursiveDirectoryIterator` appear
nowhere in `src/`, `bin/` or `tests/`, and the test has widened to `opendir`,
`readdir` and `FilesystemIterator`. Two things moved. `Directory::remove()`,
which the **Wrong if** names, is no class this repository declares any more. And
the **Assumed** is narrower than it read. Four call sites turn
`ignoreDotFiles(false)` on, because a published copy carries dot files a session
writes.
