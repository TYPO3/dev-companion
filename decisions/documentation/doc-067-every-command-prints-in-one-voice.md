---
id: D-DOC-067
title: 'Every command prints in one voice'
date: 2026-09-05
status: open
coveredBy:
  - VoiceTest::aHeadingAfterAnotherIsSetOffByABlankLine
  - VoiceTest::aKeyIsPaddedToItsColumnBeforeItIsColoured
  - VoiceTest::aPlaceholderInAngleBracketsIsPrintedAsWritten
  - VoiceTest::aProblemIsWrittenToTheErrorStream
  - VoiceTest::aProgressBarIsDrawnOnATerminalAndNotInAPipe
  - VoiceTest::aVerdictIsTheExitCodeWithItsMarkBeforeTheSentence
  - VoiceTest::theColourIsOnTheMarkAndNotInTheWords
---

# D-DOC-067 — Every command prints in one voice

**`bin/cli` prints in five shapes and `Upkeep\Voice` is where they are: a
heading, a row, a verdict with its mark, a problem on the error stream and a
note. The colour sits on the mark and the bar, never in the words.**

Fifty-five commands had grown fifty-five ways of saying "fine" and the
maintainer read the sum of them as unfinished, which it was.

## Evidence

- On 2026-09-05 the reading commands were run in one sweep. A check ended in one
  of three shapes — a sentence beginning "Every", a count line such as
  `234 requirements, 0 problems`, or `%d problem(s) found.` — and where a
  problem was printed it went to stdout in one command and to stderr in the
  next.
- Headings were bare lines, and an aggregator drew its own with `── subject`.
  Whether a blank line stood before a section was each command's own choice, and
  `components:check` put one after a section and none before the next.
- No command used a colour or an emphasis, and no output distinguished a verdict
  from a row from a remedy except by wording.
- `documentation:preview --watch` printed nothing while it waited and nothing
  for the four seconds a render took, and the maintainer read it as dead.
- `todo:list` printed a waiting todo's question on one line, which was three
  hundred characters wide.

## Decided

- One class, `Upkeep\Voice`, with one method per shape. A command builds a row
  from `key()` and `dim()`, which escape what they are given and hand back a
  tag, and every other method escapes the sentence it prints.
- A heading is set off by a blank line from what came before it and the first is
  not, tracked per output, so an aggregator running a check into its own output
  gets the spacing without either of them managing it.
- A check's problems go to the error stream and its verdict to stdout, which is
  what `Cli::errors()` promised and half the checks did.
- A count is written in the number's form — `1 problem`, `3 problems` — and
  never as `problem(s)`.
- A bar is drawn on a terminal and on nothing else: on an output that is not
  decorated it is attached to a `NullOutput`, because a bar that cannot redraw
  is a line per redraw in a log. Symfony's `ProgressBar` is used as it comes,
  with the format, the characters and the width set once.
- The renderer's output directory is cleared before every render. It removes
  nothing itself, and the theme's finish step read three stale pages for two
  minutes at full tilt before this was found — `Site::clear()`.
- Rejected: `SymfonyStyle`. Its blocks wrap every sentence at the terminal width
  and put a blank line around each, which turns a check's one-line verdict into
  four and breaks every substring a test asserts on.
- Rejected: a decorator that recognises a verdict by its wording. That is a
  regex over prose, and the rule this repository already has for that.

## Assumed

- That `✓` and `✗` render in every terminal a session runs in, and read as what
  they are in a log where no colour survives.
- That a test reads output through a `BufferedOutput`, which is not decorated,
  so the tags are stripped and the words are what is asserted on.

## Wrong if

- A command grows a sixth shape that fits none of the five, and the fix is a
  `writeln` beside them rather than a method.
- A CI log carries a redrawn bar, because some output in the chain reported
  itself decorated.
- A reader cannot tell a check's verdict from its rows without the colour, which
  would mean the mark is not enough and the words have to carry it.
