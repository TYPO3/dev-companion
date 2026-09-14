---
id: D-COD-001
title: One file declares one class
date: 2026-08-01
status: confirmed
coveredBy:
  - StructureTest::everyFileDeclaresOneClass
---

# D-COD-001 — One file declares one class

**A PHP file below `src/` declares exactly one class, interface, trait or enum,
and carries its name.**

PSR-4 finds a file through the name of the class under load. A second class in
the same file has no path of its own. So it resolves only where something has
already loaded the first one. The file that declared it works, and the first
caller from anywhere else gets a class-not-found for a class that is plainly
there.

## Evidence

- Written on 2026-08-01 during the addition of a listing renderer. Two small
  classes went into one file because they were two shapes of the same idea. The
  arrangement would have worked until a second place used the second one.

## Decided

- One file, one class, which `StructureTest::everyFileDeclaresOneClass` holds
  rather than a review. The test names two exceptions, and they are not classes
  at all: `bootstrap.php`, which locates the autoloader, and `probe.php`, which
  runs inside somebody else's installation.

## Assumed

- That a class too small to deserve its own file is a sign about the class
  rather than about the rule. The renderer that prompted this shrank to one
  method and stayed worth a name.

## Wrong if

- Something needs two declarations in one file, say a backed enum that exactly
  one class uses, and a split makes both harder to read. Then the rule needs an
  allowed list rather than a flat ban, and the test is where it goes.

## Confirmed on 2026-08-22

The rule holds and the **Wrong if** has not fired. Three files read the smallest
of the four enums below `src/`, so none is the one-caller enum that would have
asked for an allowed list. One path in **Decided** gets a correction. The second
exception is `src/Installation/probe.php`, which the test excludes by base name,
for the reason already given.
