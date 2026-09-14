---
id: D-DOC-047
title: An entry is filed under the title it has
date: 2026-08-23
status: open
restsOn: [D-DOC-046]
coveredBy:
  - RenumberTest::anEntryIsRefiledUnderItsTitleAndEveryPathThatNamedItMoves
---

# D-DOC-047 — An entry is filed under the title it has

**A file's name is the id and the title of the entry in it, and
`bin/cli decisions:rename` is what moves one after a title correction.**

The corpus had never held the two together. 104 of 669 files carried an older
wording, and 60 of those predate any change of today.

## Evidence

- Read on 2026-08-23. Of 669 entries, 104 had a file name that is not the slug
  of their title. 44 went stale that day through the shorter titles under
  `D-DOC-046`; the other 60 were already so.
  `three-registration-kinds-read-from-what-core-reads-them-for.md` stood under a
  title of 23 words.
- The older slugs are often the better text. The file names had done for months
  what `D-DOC-046` did to the titles, which is the same conclusion reached twice
  and written down neither time.
- The cost to hold them together had a price and went up: 104 moves and 333
  references. The objection against it was that a check would make every future
  title correction expensive. The answer to that was that a wrong title is
  wrong. The move is a consequence of the correction rather than a reason not to
  make it.

## Decided

- The file name is `<group>-<number>-<slug of the title>.md`, and
  `bin/cli decisions:check` fails where it is not. `Entry::fileName()` is the
  one implementation, because a check that holds a name and the command that
  writes one have to agree on every character. An apostrophe and a backtick drop
  rather than turn into a separator. Every other run of what is neither a letter
  nor a figure is one dash.
- `bin/cli decisions:rename` and `bin/cli requirements:rename` do the move and
  rewrite every path that names the file. That is what answers the objection.
  The expensive half is mechanical, so it belongs in a command rather than in a
  reason to leave a title wrong.
- Over the whole corpus rather than one entry, because a title changes where it
  stands and nobody knows afterwards which one moved.
- `Renumber` does both moves now. A renumber has to tell two entries that share
  an id apart and reports what it cannot settle. A rename does not, because a
  file name is unambiguous where an id is not, so it rewrites every reference
  and counts them.

## Assumed

- That a file name may be long. The longest this produces is 118 characters,
  which every filesystem this runs on takes. What it buys is a name that says
  what the entry says. The pressure it puts on a long title is the pressure
  `D-DOC-046` wants.
- That nothing outside this repository links to a file by path. The published
  documentation comes from `documentation/`, and no answer this server gives
  names a decision file.

## Wrong if

- Nobody corrects a title any more because the rename is in the way. The command
  is what this rests on, and a session that corrects a title by hand and leaves
  the file is what would show it. The check fails on exactly that, so it would
  show as a red check rather than as a quiet drift.
- The slug rule and a name somebody writes disagree on a character the corpus
  has not seen yet. A colon, a slash, a quotation mark inside the title.
  `Entry::fileName()` is where that settles, and a title with one in it is what
  would find it.
