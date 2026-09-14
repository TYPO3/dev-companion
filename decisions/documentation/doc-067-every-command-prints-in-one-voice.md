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

**`bin/cli` prints in five shapes and `Upkeep\Voice` is where they are. A
heading, a row, a verdict with its mark, a problem on the error stream and a
note. The colour sits on the mark and the bar, never in the words.**

Fifty-five commands had grown fifty-five ways to say "fine" and the maintainer
read the sum of them as unfinished, which it was.

## Evidence

- On 2026-09-05 the read-only commands ran in one sweep. A check ended in one of
  three shapes. A sentence that begins "Every", a count line such as
  `234 requirements, 0 problems`, or `%d problem(s) found.` Where a command
  printed a problem it went to stdout in one command and to stderr in the next.
- Headings were bare lines, and an aggregator drew its own with `── subject`.
  Whether a blank line stood before a section was each command's own choice, and
  `components:check` put one after a section and none before the next.
- No command used a colour or an emphasis, and no output told a verdict from a
  row from a remedy except by wording.
- `documentation:preview --watch` printed nothing while it waited and nothing
  for the four seconds a render took, and the maintainer read it as dead.
- `todo:list` printed the question of a todo that waits on one line, which was
  three hundred characters wide.

## Decided

- One class, `Upkeep\Voice`, with one method per shape. A command builds a row
  from `key()` and `dim()`, which escape what they get and hand back a tag.
  Every other method escapes the sentence it prints.
- A blank line sets a heading off from what came before it and the first has
  none, tracked per output. So an aggregator that runs a check into its own
  output gets the blank lines while neither of them manages it.
- A check's problems go to the error stream and its verdict to stdout, which is
  what `Cli::errors()` promised and half the checks did.
- A count stands in the number's form, `1 problem`, `3 problems`, and never as
  `problem(s)`.
- A bar draws on a terminal and on nothing else. On an output without decoration
  it attaches to a `NullOutput`, because a bar that cannot redraw is a line per
  redraw in a log. Symfony's `ProgressBar` serves as it comes, with the format,
  the characters and the width set once.
- `Site::clear()` empties the renderer's output directory before every render.
  It removes nothing itself, and the theme's finish step read three stale pages
  for two minutes at full tilt before somebody found this.
- Rejected: `SymfonyStyle`. Its blocks wrap every sentence at the terminal width
  and put a blank line around each. That turns a check's one-line verdict into
  four and breaks every substring a test asserts on.
- Rejected: a decorator that recognises a verdict by its wording. That is a
  regex over prose, and the rule this repository already has for that.

## Assumed

- That `✓` and `✗` render in every terminal a session runs in, and read as what
  they are in a log where no colour survives.
- That a test reads output through a `BufferedOutput`, which has no decoration,
  so the tags fall away and the words are what the assertion covers.

## Wrong if

- A command grows a sixth shape that fits none of the five, and the fix is a
  `writeln` beside them rather than a method.
- A CI log carries a redrawn bar, because some output in the chain reported
  itself decorated.
- A reader cannot tell a check's verdict from its rows without the colour. That
  would mean the mark is not enough and the words have to carry it.
