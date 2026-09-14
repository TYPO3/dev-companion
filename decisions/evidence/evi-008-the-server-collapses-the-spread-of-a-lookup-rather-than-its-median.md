---
id: D-EVI-008
title: The server collapses the spread of a lookup rather than its median
date: 2026-08-18
status: open
---

# D-EVI-008 — The server collapses the spread of a lookup rather than its median

**Measured against sessions without it, this server left the median cost of a
lookup roughly unchanged and collapsed the spread. So the promise says
predictability, not a saving.**

The readme promises knowledge, version bounds and skills, and says nothing about
the shape of the saving. So somebody who weighs an install guesses at one.

## Evidence

- A comparison outside this repository, on one TYPO3 13.4 installation with one
  fixture extension, one model, five runs per cell, across four task families.
  This checkout cannot run it again and holds none of its data. Everything below
  comes off that report, and a session that wants to check a number has to go to
  where somebody measured it.
- Lookup tasks: the turn count fell by about two fifths and the wall time by
  about half. The median cost fell 18 percent and does not survive a drop of the
  strongest of the four tasks. At that point it is one percent the other way.
  Four tasks won and four lost.
- The one effect that is not a coin toss is the spread. On the widest task the
  runs without the server ranged 37-fold from cheapest to dearest and the runs
  with it 1.4-fold. That is a ceiling, and a ceiling is what a person who pays
  per session buys.
- Code-change tasks went the other way: 2.8 times the cost, four times the input
  tokens, twelve turns against nine. The two distributions do not overlap. All
  three tasks were changes inside an existing extension. That is the shape that
  had no task intent until the commit that added one.
- Nothing about correctness got a measurement at all. Every recorded failure in
  that sweep was a defect of its own harness. Its author says in as many words
  that nobody may quote a success rate or a hallucination rate from it.

## Decided

- The readme says what the shape of the saving is, in one sentence, and says it
  as a ceiling. No number goes there. It came from elsewhere, on one project,
  and a figure in a promise outlives the run it came from.
- The measurement does not go into `knowledge/` and no tool answers from it. It
  is a fact about this server rather than about TYPO3, and the corpus is for the
  second kind.
- Nothing here claims the server makes a session cheaper. Where the median moved
  it moved by less than the choice of task. To say otherwise would be a promise
  the next reader measures and finds false.

## Assumed

- That predictability is worth a statement to somebody who decides whether to
  install it. A budget stands against a ceiling, and the alternative reading is
  that only the average ever counts.
- That the four lookup tasks represent what this server gets asked. Eleven of
  its tools never ran in that sweep, because it had no task for them. So half
  the surface has no measurement rather than a measurement as neutral.

## Wrong if

- A second project reports a lower median instead, with the spread unchanged.
  Then the saving is the average after all and the sentence names the wrong
  thing.
- The spread collapses just as far without the server once its own harness
  defects are gone. That would make the ceiling a property of the measurement
  rather than of the answers.
- Correctness turns out to move in the other direction. Nothing here has a
  measurement for it, and a server that makes a session predictable and wrong is
  not one this sentence should recommend.

## Since then

The sentence is in `documentation/readme.rst` and says all three halves this
decided. "What it buys a session is a ceiling rather than a lower average: a
lookup that would otherwise take a handful of turns and cost whatever the search
happens to cost arrives in one call, at a price that varies little. It is not a
discount, and nothing here has been measured for whether it makes an agent more
correct". No number, and the promise names the third **Wrong if** rather than
leaves it for a reader to find.

None of the three can settle here. Each needs a second comparison on another
project, a lower median, or a spread that collapses without the server.
`scenarios/runs/` still holds three recorded forward runs, none of them that
measurement.
