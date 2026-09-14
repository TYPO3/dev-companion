---
id: R-KNW-066
title: "A core PHP change is told what the class's public surface commits it to"
status: held
restsOn: [D-KNW-065]
heldBy:
  - HintsTest::wideningAPublicSignatureIsAnsweredAsTheBreakingMoveItIs
---

# R-KNW-066 — A core PHP change is told what the class's public surface commits it to

**A core PHP change learns before the diff ends that a public or protected
method on a non-final class is an override point.**

A new parameter on one is a signature change for every subclass that overrides
it, an optional parameter included. PHP fails those subclasses on load. Nothing
in a core checkout reports it. No core class has to override the method, so the
unit, functional, coding-guidelines and static-analysis runs are all green on
the draft that breaks.

The answer owes the consequence in the direction the session meets it. What the
wider signature gains is one call site. What it costs is `[!!!]`, a Breaking
changelog entry and the release managers' decision about the target branch. A
fix that two maintained release lines owe cannot carry the signature change at
all. The additive shape and a final class or method first are the two moves that
avoid it, and the second is itself a breaking change.

The answer sits in two places, because the path cannot carry it. A session that
asks reaches `public-api-surface`. The development skill asks a session that
does not, where the blast radius already stands before the change.

## From

The feedback of 2026-08-08 22:43, from a core patch for Forge #58705 in
`/home/benji/projects/typo3-cms`. The first draft added an optional third
parameter to the public `GifBuilder::start()` on a class that is not final.
`typo3_hint_lookup` with both changed paths returned `fal-processing`,
`fal-basics` and `system-extension-boundaries`. 3613 functional tests, 235 unit
tests, cgl over 6300 files and phpstan over 6265 were green on it. What raised
it was the `breaking-not-assessed` info line of `typo3_commit_message_guide`,
after the diff had ended and every suite had run.
