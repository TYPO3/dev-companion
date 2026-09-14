---
id: D-DOC-015
title: A renumber moves what a link path settles and names the rest
date: 2026-08-04
status: open
coveredBy:
  - RenumberTest::aLetterSuffixIsAnotherEntryAndStaysWhereItIs
  - RenumberTest::aReferenceNoLineSettlesIsNamed
  - RenumberTest::everyMentionIsEitherMovedOrNamed
  - RenumberTest::noPathIsLeftPointingAtTheOldFile
---

# D-DOC-015 — A renumber moves what a link path settles and names the rest

**`bin/cli decisions:renumber` rewrites every reference whose own line names the
entry's file, and prints every reference that does not.**

Ten sessions read one `main` and hand out one id twice. The rebase catches the
collision and it costs minutes; the renumber that follows it is what has gone
wrong, twice, silently.

## Evidence

- Four runs of ten todos produced seven duplicate ids. Twice the files that
  named the old number did not all mean one entry. `R-PRJ-008` rested on the
  `D-ANS-013` that kept its number while five other files meant the one that
  became `D-ANS-015`. `ans-006` named the `D-ANS-016` that stayed while a
  requirement and a todo named the one that became `D-ANS-019`.
- Both mis-pointings were **bare** references — an id in a `restsOn:` and an id
  in a sentence. No citation with a link path has yet gone wrong.
- What the checks cover is narrower than it looks. `requirements:check` fires on
  a `restsOn:` that names no decision, and `decisions:check` on an `R-` citation
  that names no requirement. No check covers a bare `D-` id in prose, in PHP or
  in a link path in either direction. A renumber that leaves one behind is
  silent because the entry it now points at exists.
- A sweep of everything git tracks on 2026-08-04, at 221 decisions, found 3026
  mentions of a decision id. 1356 sit in the listings `decisions:index`
  regenerates and 438 are an entry's own front matter and heading. Of the 1232
  written by hand, 260 carry a link path that says which entry they mean. 86 are
  a requirement's `restsOn:`, and 886 are bare. That is a mean of 3.97 per
  entry, a median of 3, 22 on the worst, and none at all on 35 of them.
- The linked share rises. The same sweep on 2026-08-02, at 101 decisions, found
  66 of 601 hand-written mentions with a link: 11%, against 21% now.
- Renumbering `D-GUI-002` to `D-GUI-010` in a copy of this checkout moved 7
  mentions across 4 files and named 8 across 7. `decisions:check` and
  `links:check` were green immediately afterwards. `requirements:check` failed
  on `R-SKL-017 rests on D-GUI-002, which no decision has`, which is one of the
  8 and the only one anything would have caught.

## Decided

- A command that renumbers, rather than a block of numbers reserved at
  `todo:claim` or a number taken out altogether. A reservation removes the
  collision and buys it with gaps, which cost nothing. But a claim cannot know
  which group it will write into, so it reserves in every one. Ten claims of ten
  apiece spend 100 numbers per group per run. Three digits then lasts about ten
  runs, and four happened in three days: it is the option that breaks
  `D-DOC-005`'s assumption rather than a check. Taking the number out is 601
  hand-written mentions that are not mechanical, because a citation handle has
  to become a name somebody chose.
- The command's value is the names, not the moves. The command rewrites a
  reference only where its own line says which entry it means. That is a link
  path, and the reference definition a generated listing ends with. It prints
  every other one with its file, its line and its text, for a person to read
  against `git diff main -- <file>`.
- A `restsOn:` gets a name rather than a move, although the id in it is the one
  thing a check reads. It is the reference that went wrong the first time, and
  it says nothing about which entry it means. What the existence check buys is
  that one left behind fails loudly once the old number is free. In the
  collision case it is not free, which is why the command prints it as well.
- Every mention has its account. A line that names the old id gets a rewrite or
  a report and never neither. That is the property that makes the printed list
  worth a read: a person who has it has all of it.
- The generated listings go back in order, but only where they already carried
  the entry. The id is what a group sorts on, so an in-place rewrite can leave a
  listing `decisions:check` disagrees with. Where the listing never carried the
  entry, nothing writes one either. That is a branch that added it and left the
  block alone, which is what a worktree is told to do.
- Decisions only. Requirement ids collide by the same mechanism, and no recorded
  collision has been one; the second corpus is speculation until it happens.

## Assumed

- That a link path names the entry that was meant. The file is what decides, and
  a link written to the wrong file was wrong before any renumber touched it.
- That a link's label and its path stay on one line. `bin/cli prose:format`
  never breaks inside a link, so the definition in the same file settles the
  label a reference-style link uses.
- That a renumber never changes the group. The prefix names the directory, so a
  move of it is a move between files and what the entry is about moves with it.

## Wrong if

- A renumber leaves a reference that was neither rewritten nor printed. That is
  the failure this exists to prevent, and it would be silent.
- Somebody rewrites the printed list and never opens `git diff main -- <file>`.
  The list makes the wrong move as cheap as the right one, and nothing here can
  tell which one happened.
- The linked share rises until the names half is what costs. A required link
  form for a cross-entry citation would then be the cheaper fix. It is the only
  option measured that would have caught both wrong pointers rather than
  reported them, and it composes with this one.
- A requirement id collides and there is no `requirements:renumber` to move it.
- A branch's listing does carry the entry and the regeneration fires in a
  worktree. The merge conflicts on the one block a branch must not touch.

## Since then

Somebody asked again for reserved numbers at `todo:claim`, and it stays
rejected, priced rather than estimated. A night of 21 decisions across five
groups shows a claim cannot know which group it will write into. So even one
number per group per claim spends 24 in every group and makes the number count
claims rather than entries. It would also bind only the sessions that read it.
What the collisions cost is the other side, ten renumbers and thirty home
invocations for 24 branches. The refusal is what made the repair cheap, since
`todo:home` merges nothing red and leaves the worktree in place. `D-FBK-046`
carries the fallback.
