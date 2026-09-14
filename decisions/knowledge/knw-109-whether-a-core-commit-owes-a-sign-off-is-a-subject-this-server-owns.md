---
id: D-KNW-109
title: Whether a core commit owes a sign-off is a subject this server owns
date: 2026-08-24
status: revoked
revokedBy: D-KNW-110
---

# D-KNW-109 — Whether a core commit owes a sign-off is a subject this server owns

**`core/contribution/commit-messages` states which of the core's own sources
asks for the `Signed-off-by:` trailer. Its agent instructions demand it and one
merged commit in a hundred carries it.**

The page lists the trailers through `Change-Id:` and stops. A reviewer with a
patch without the trailer has to decide whether that blocks submission, and the
three sources that answer disagree with each other.

## Evidence

- `bin/cli hints:probe "signed-off-by"` reaches nothing: 101 hints were
  candidates and none matched. A search of `knowledge/` and `skills/` for the
  trailer, the DCO and `git commit -s` returns hits in `feedback/` and `todo/`
  and nowhere else.
- Read in `.checkouts/main` at `9dd4e1bfd7`, dated 2026-08-23 and fetched for
  this judgement because the worktree stood at 2026-08-18 and `AGENTS.md` had
  landed in between. The core's own `AGENTS.md` says "Sign off every commit —
  `git commit -s` appends the `Signed-off-by:` trailer, or set
  `git config format.signOff true`". It certifies it against the Developer
  Certificate of Origin, and its footer example carries the trailer between
  `Releases:` and `Change-Id:`.
- `Build/git-hooks/commit-msg` is the other half of that sentence. Line 44 is
  `/^Signed-off-by:/d`, over the copy the hook hashes the `Change-Id` from. So
  the trailer neither disturbs a `Change-Id` that exists nor meets any check the
  hook validates.
- `CONTRIBUTING.md` at the same revision says nothing about a sign-off.
- 5 of the last 500 commits on `origin/main` carry the trailer — one in a
  hundred.
- Three sessions from `/home/benji/projects/typo3-cms` on 2026-08-24 arrived at
  it. `feedback/2026-08-24-110851` settled it from the checkout by hand to rank
  an absent trailer as worth a change rather than a block.
  `feedback/2026-08-24-133602` had two drafts with the trailer that the user
  struck. `feedback/2026-08-24-133515` names those same two strikes among the
  costs of a patch session that called this server for nothing.

## Decided

- Written, and into `core/contribution/commit-messages` beside the trailers it
  already lists rather than as a hint. The question arrives while a session
  writes a footer, and that page is what answers a footer.
- What it states is which source asks for it rather than one rule. The core's
  `AGENTS.md` demands it, the hook checks it against nothing, and the merged
  history barely carries it. All three hold at once, and a caller with only the
  first emits a trailer a maintainer strikes.
- `git commit -s` and `format.signOff` are named with the hook's own treatment
  of the `Change-Id`, because that is the fear that keeps a contributor from
  adding the trailer to an amend.
- The reading above is the section's evidence, so the work verifies rather than
  establishes. What is still owed is the official Contribution Guide, which was
  not read here.
- Not what `typo3_commit_message_guide` returns. `feedback/2026-08-24-133602`
  asks for that, and its lever is the guide's footer answer rather than this
  page. It has no judgement yet and its own card is open.

## Assumed

- That the core's `AGENTS.md` stays. It was days old at this reading, and a
  statement about it is a statement about a file one commit removes.
- That the share stays low. One in a hundred over 500 commits is what makes "not
  enforced" the honest reading, and a project that starts enforcing it turns the
  section round.

## Wrong if

- The section rests on `AGENTS.md` alone and a caller emits a trailer a reviewer
  strikes. That is `feedback/2026-08-24-133602` again with this server's answer
  behind it.
- A caller reads the section as leave to omit the trailer and a core maintainer
  asks for it. Then the practice is not what the merged history counts.
- Gerrit or the hook starts to require it. Then it is a rule, and the section is
  a paragraph about a conflict between sources that no longer exists.
- Something outside the two files read here turns out to ask for the trailer,
  the Contribution Guide or a review convention nobody wrote down. Then the
  section names a smaller set of sources than the question has.

## Since then

The section stands, and a session read the Contribution Guide first because that
is what **Decided** left owed. It asks for nothing. The commit message appendix
lists five trailers and stops, and the setup page names every config a
contributor sets and not this one. The guide read whole carries none of the
phrases at all. So the first half of the last **Wrong if** has its answer and
the second does not. A review convention nobody wrote down is still what could
turn this round.

The documentation lookup is not what read it, though the todo asked for it. That
tool indexes four manuals and the guide is none of them. A second verification
of the **Evidence** in the bare repository found every line holds.

## Revoked on 2026-08-24

The maintainer settled the question the same day: the sign-off is not set on a
core patch, and neither is an agent's own attribution trailer. So the statement
above describes a section that no longer exists. What the page states is the
rule, and the sources this entry weighed stand under it as why nothing enforces
it. `D-KNW-110` is what a reader builds on, and the evidence here is what it
rests on.

The first **Wrong if** fired in the direction it did not name. It watched for a
section written from `AGENTS.md` alone; what happened is that naming every
source left the caller to choose, and the choice is what the two struck drafts
were.
