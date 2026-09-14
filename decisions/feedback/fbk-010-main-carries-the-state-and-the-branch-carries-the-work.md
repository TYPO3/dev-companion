---
id: D-FBK-010
title: '`main` carries the state and the branch carries the work'
date: 2026-08-01
status: revoked
---

# D-FBK-010 — `main` carries the state and the branch carries the work

**Several sessions work several todos: each claims one on `main` and works it on
its own branch in its own worktree.**

`todo/` was already the shape this needs. One todo is one file, decided in
`D-FBK-008` so that two sessions could add, move or drop work and never write
the same file. The case it served was two sessions that do that one after the
other. The same property carries them when they do it at once. Each session
touches its own file in `todo/` and no other, so the claims never conflict.

## Evidence

- The queue on this entry's day: 29 items, nearly all in the shape "read `D-XXX`
  against what the code does now". Each names a different entry and a different
  part of the checkout. That is work with no overlap and no order between the
  items, which is the case parallel sessions are worth anything for. What made
  it impossible was one command: `todo:next` handed all of them the same first
  item.

## Decided

- A fifth place, `todo/progress/`, beside the queue, what recurs, what waits and
  what stays for reference. `bin/cli todo:claim <n>` moves the front of the
  queue into it and `bin/cli todo:release` moves one back. The second is not an
  extra. A session ends where it ends, and a state with no way out fills up with
  claims nobody works and nobody else gets.
- That `bin/cli todo:next` reads the branch the checkout is on and hands over
  that branch's claim. `AGENTS.md` says where a session starts before it says
  anything else, and that sentence has to stay true in a worktree. Otherwise the
  one thing every session does is the one thing a parallel session must not.
- That this repository owns the file moves and the branch names, and names the
  rest. The worktree, the merge and what a question that arrives mid-work leaves
  behind are on one page,
  [documentation/records/working-todos-in-parallel.rst](../../documentation/records/working-todos-in-parallel.rst),
  handed over with every claim. That is the same line `todo:next` draws when it
  names a `Run:` command it does not own instead of runs it.
- That a question a session cannot settle goes on record rather than to a
  person. One session that works alone asks and waits; one of several would
  block a worktree on a person who answers three others. It writes the question
  into `**Waiting on:**` on its claim, commits what it has, and ends. Only the
  claim comes back to `main`, so `main` says what is open and where the work is
  and carries no half-finished change.

## Assumed

- That a worktree costs less than the collisions it prevents. It is a
  `composer install` per claim, and no two claims can share `vendor/`.
  `Paths::root()` is the directory above `src/`, and Composer resolves `src/`
  from where the autoloader physically sits. A symlinked `vendor/` therefore
  points every path in this repository back at the main checkout. A run found
  that, not a read. `bin/cli todo:next` in the worktree answered with the main
  checkout's queue and nothing about the answer looked wrong.

## Wrong if

- `progress/` grows a claim nobody has released in a fortnight. That would mean
  the state is cheap to enter and expensive to leave, the same failure a watch
  covers `waiting/` for, and no recurrent todo asks after this one. Or a merge
  conflicts inside `todo/`, which would mean the one file per todo does not hold
  what this asks it to hold. Or claims still go out two at a time on work that
  turns out to share a file. Then the overlap caveat reads the wrong signal: it
  counts entries a todo serves, and most of this queue serves a directory.

## Since then

The boundary the fourth **Decided** drew has moved, and only that one.
`bin/cli todo:claim` now commits the claims onto `main`, cuts a worktree per
claim, installs in each and starts a session in each. It carries the arrangement
out instead of names it. What held for as long as somebody with a page did the
rest no longer held when a session did it. Three steps that must happen in one
order are one step. The run that got the order wrong paid for it hours later, in
a worktree, as a refusal nobody there could place. The page is still where the
merge and the mid-work question live, because neither is a step this command
could take.

## Since then

The launch has moved across too, on 2026-08-02 and for the same argument one
step further out. The command printed the message and left the start to whoever
read the output. A step left over for somebody with a page is the one that
breaks. Every session of that day started in the directory that was already
open. Three worktrees stood untouched while the sessions read a queue that
belonged to somebody else. The client's name is still not this repository's
business, so `.session-command` is the machine's and git ignores it. The three
things a person gets wrong are the command's: the work directory, the message,
and a session id apiece.

## Since then

The assumption had its measure on 2026-08-13, over three rounds of eight claims:
24 branches in one night, each brought home as its session reported. Five
collided on a decision id and one pair collided in a file. That is the pair
`todo:claim` had named in its overlap caveat before the sessions started, which
is what the second and third checks exist for. All of them got their repair in
the worktree they happened in, and none cost a revert off `main`. What a home
per branch bought is in the same measure. A round's fastest session finished in
5 minutes and its slowest in 25, and the slow one carried the file conflict. So
it rebased onto a `main` that had already absorbed the other seven.

## Revoked on 2026-08-27

`main` does not carry the state any more, which is the half of the title this
entry was about. `D-DOC-060` reads what is in hand off the worktree on the
todo's branch. So the fifth place the first **Decided** made, the commit that
carried it onto `main` and the release that moved one back are all gone.

What it got right is what survives it. A branch and a worktree apiece, and a
session that gets its own todo from the branch it stands on. And a question on
record rather than to a person. The third and fourth **Decided** hold unchanged.

The first **Wrong if** is what came true, in the shape it did not predict. It
watched for a claim nobody released in a fortnight. What happened instead was
that the claim was a third copy of what the branch and the worktree already
said. The copy was the one that could go stale. `D-DOC-060` has the account.
