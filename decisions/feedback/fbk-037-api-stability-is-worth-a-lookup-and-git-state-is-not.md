---
id: D-FBK-037
title: API stability is worth a lookup and git state is not
date: 2026-08-03
status: revoked
revokedBy: D-FBK-038
coveredBy: []
---

# D-FBK-037 — API stability is worth a lookup and git state is not

**The API-stability question gets taken on and the git-state question does not.
One costs the caller a read it cannot finish and the other costs it one
command.**

Two tool-gap reports arrived from the same patch-review session and read alike.
Judged against `D-FBK-027` they come apart.

## Evidence

- The API-stability report had to read `GifBuilder.php` **and its history** to
  establish that the class is public API while the removed
  `getTemporaryImageWithText()` is `@internal`. That is the fact that decides
  whether a removal is breaking, and the read does not end in one call. The
  annotation may sit on the class, on the member, or on neither. The report asks
  for the answer on each covered major, which a caller's own checkout cannot
  give because it has one.
- The git-state report had to retrieve the changed paths and the commit message
  with repeated path-filtered `git show` calls. Both facts come out of one:
  `git show --name-only --format=%B HEAD` prints the message and the paths
  together. The cost in the report was the shape of the calls, not an access
  path a caller has to discover.
- The Forge case `D-FBK-027` rests on is the contrast. There the caller met 403,
  then 200 around a challenge page, then JSON whose answer sits in a field
  nobody would guess. Git has no such trap: it is local, documented, and answers
  the first time.
- The mechanism for the first is already in this package. `PhpArray`,
  `Extension` and `FluidNamespaces` read shipped PHP with `token_get_all` and
  execute none of it, which is the same token stream a docblock sits in.

## Decided

- The API-stability lookup sits in the queue as a card of its own. What it lacks
  is a design. Where the per-version answer comes from is the open question, and
  the card carries it rather than assumes the installation.
- No git-reading tool. It would replace one command the caller already runs with
  one call to this server. That is the definition `D-FBK-027` gives for what
  does not qualify, a fact the caller reads once from its own checkout.
- What the second report was actually right about has its fix instead. The scope
  entry told the caller to determine the changed paths themselves and never said
  how, while two tools here demand exactly that as input. It now names the
  single command that produces both.
- The feedback behind the second has its answer and sits in the archive. The
  first stays open behind its card.
- Running git was not rejected as out of bounds. `Typo3Cli` already shells out
  to the installation's console, and a read-only `git show` is no different in
  kind. The count rejects it, which is the argument `D-FBK-027` asks for.

## Assumed

- That a class's `@internal` moves rarely between majors, so the per-version
  half of the report is a smaller promise than it reads as. Nobody has counted
  it, and the card asks for that count before the source is chosen.
- That the caller has git. Every checkout this server points at is one, and the
  reports both came from sessions that already ran git commands.

## Wrong if

- A session asks for the changed paths in a way this server could have answered
  and pays more than the one command for them. A shallow clone, a detached
  worktree, a review of somebody else's patch with no checkout at all. The last
  one is the plausible case and would reopen it.
- The API-stability lookup, once built, answers `unavailable` more often than it
  answers. That would mean the source question the card carries settled the
  wrong way.

## Revoked on 2026-08-03

Half of this came without a read of the core's own changelogs, and that half is
wrong. One removal of `@internal` methods stands as **Breaking** because
non-Composer scripts called them, and one rename of `@internal` classes stands
as **Important**. The marker is an input and not the decision, so a lookup that
reports it would have answered beside the question. It would have told the
reviewer of that first change the opposite of what the core concluded. The gap
was the rule rather than the fact, which `D-FBK-038` carries, and the card this
queued is gone rather than reshaped.
