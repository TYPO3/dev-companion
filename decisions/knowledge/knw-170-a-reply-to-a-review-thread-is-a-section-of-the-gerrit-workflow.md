---
id: D-KNW-170
title: A reply to a review thread is a section of the Gerrit workflow
date: 2026-10-22
status: open
---

# D-KNW-170 — A reply to a review thread is a section of the Gerrit workflow

**"Answer a Review Comment" in `core/contribution/gerrit-workflow` gives the
reply's input, the patch set it goes on and the flag that decides the thread.
The development skill's amend step names that section.**

## Evidence

- [`feedback/2026-10-22-105711-no-procedure-to-reply`](../../feedback/archive/2026-10-22-105711-no-procedure-to-reply-to-a-review-thread-on.md).
  A session answered three reviewer comments on change 96975 and kept them open.
  No page and no skill said how. It wrote a `ReviewInput` by hand. It guessed
  the patch set, and the guess worked.
- Step 1a of the ladder. The skill said "answer it in the review" and nothing
  more. `gerrit-workflow` had no section on comments.
- Read on 2026-10-22 from Gerrit's own documentation. `cmd-review` says `--json`
  reads a `ReviewInput` from stdin and excludes six other options.
  `rest-api-changes` says a `CommentInput` without `unresolved` takes the value
  of its `in_reply_to` parent. It also says that a `CommentInput` without `line`
  or `range` is a file comment. A revision id may be a patch set number.
- Read on 2026-10-22 from review.typo3.org. `/config/server/version` answers
  `3.11.11`. `/changes/96975/comments` holds the three replies the session
  posted, each on patch set 1 with its parent's line, `in_reply_to` and
  `unresolved` true.
- `typo3_gerrit_lookup` already answers `id`, `patchSet`, `file`, `line` and
  `threadUnresolved` per comment. The session names those fields as what made
  the reply possible.

## Decided

- The procedure is a section of `gerrit-workflow`, beside the amend. It is the
  page the development skill already sends a session to after review.
- The feedback's suggestion is wrong on one point, and the section says what
  holds. `unresolved` does not have to be set to keep a thread open, because a
  reply inherits it. The section still sets it, so the file says what it does.
- The section says to post on the patch set the answered comment carries. The
  measurement shows that this works. Nothing here measured a reply on a later
  patch set, and the section claims nothing about one.
- A reply is publication, so the skill and the section both say to post it when
  the user asks. That is the rule the push already has.

## Wrong if

- A reply posted as the section says lands outside its thread, or changes the
  thread state other than the flag says.
- A reply on a later patch set turns out to be what reviewers expect. Then the
  rule to post on the parent's patch set costs the reader the context of the
  current code.
