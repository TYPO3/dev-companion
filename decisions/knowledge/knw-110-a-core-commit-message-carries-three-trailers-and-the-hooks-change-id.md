---
id: D-KNW-110
title: A core commit message carries three trailers and the hook's Change-Id
date: 2026-08-24
status: revoked
revokedBy: D-KNW-125
---

# D-KNW-110 — A core commit message carries three trailers and the hook's Change-Id

**`Resolves:`, `Related:`, `Releases:` and the `Change-Id:` the hook writes are
what a core commit message carries. `typo3_commit_message_guide` takes any other
trailer off a `workflow="core"` draft.**

`D-KNW-109` named which source asks for the `Signed-off-by:` trailer and left
the caller to weigh them. That weight is what produced the two struck drafts
that `feedback/2026-08-24-133602` reports. So the answer this server owes is the
rule rather than the conflict behind it.

## Evidence

- The maintainer ruled it on 2026-08-24, in the session that recorded this. The
  sign-off is against the commit rules this project works by and nobody sets it.
  `Co-Authored-By:` and the session trailer an agent writes about itself do not
  belong in a core commit message either. Nothing in a checkout states this,
  which is why this entry writes it down.
- Every reading in `D-KNW-109` holds and now reads one way.
  `Build/git-hooks/commit-msg` deletes `^Signed-off-by:` and checks for nothing,
  and the official Contribution Guide asks for none of the three.
  `CONTRIBUTING.md` is silent, and about one merged commit in a hundred on
  `main` carries the sign-off.
- The core's own `AGENTS.md` is the one source that demands it, on the Developer
  Certificate of Origin. The rule stands against that file, which is the whole
  of what this entry adds to the research.
- `feedback/2026-08-24-133602` had two drafts struck for the trailer by the same
  person, before this server said anything about it.
- Measured after the change on 2026-08-24. A message with all three trailers
  answers three `refused-trailer` errors under `workflow="core"` and comes back
  as a draft with none. `workflow="project"` keeps all three and reports
  nothing.

## Decided

- One rule in `core/contribution/commit-messages`, in place of the section that
  named the sources. The sources stay under it as why nothing enforces the rule.
  That is what a caller needs when a reviewer strikes a line no check rejected.
- The refusal is an `error` and the line comes off the draft. A caller commits
  the draft this tool returns as it stands. So a refused trailer left in it
  would be an answer that contradicts its own check.
- Core only. `workflow="project"` keeps every trailer it gets. This repository's
  own commits carry `Co-Authored-By:` and a session link, and nothing about them
  belongs to the core's rules.
- A change to the rule is the maintainer's to make. A session that believes it
  owes a trailer asks rather than derives an answer from whichever file it
  holds, `R-KNW-075`.

## Assumed

- That the three names are the list. An agent attribution trailer under a fourth
  name passes the check, and only the sentence in the document says it does not
  belong.
- That the rule outlives the core's `AGENTS.md` demand. Both are about the same
  trailer and they disagree, so a caller reading only one of them acts on it.

## Wrong if

- A core patch is struck for a trailer this list does not name. Then the list is
  the wrong shape and the rule belongs on what a trailer is rather than on which
  three.
- The maintainer sets a sign-off on a core patch. Then the rule was narrower
  than what stands here.
- Somebody drafts a core commit under `workflow="project"` and the refusal never
  runs. Then the check sits behind an argument the caller chooses, which is the
  half no test can hold.
- The core's `AGENTS.md` drops the demand. Then the conflict this entry stands
  against is gone, and what stays is a list nobody disputes.

## Since then

A session measured the core's own instruction file rather than took it from the
neighbour entry. Two sessions had read it differently on the same day. It is a
month old and there on purpose rather than the days-old arrival that entry
assumed. So the conflict this entry rules on is the durable kind.

Two sessions then met the refusal and no **Wrong if** fired. Both quote the
error, both grant the practice, and both went to the log to establish it for
themselves. Neither read the page: the check message states the rule and names
none of what stands behind it.

The case the rule does not reach is somebody else's patch set. A message handed
in with the trailer comes back without it. So the tool tells a session that
amends another contributor's work to strike that person's attestation. That is a
different act from a refusal to write one's own.

## Revoked on 2026-08-25

The maintainer required the sign-off on every core patch, on an association
statement nobody here had read at the time of this entry. So the trailer this
entry strikes is the one a core commit now carries, and `D-KNW-125` is what a
reader builds on.

The second **Wrong if** fired word for word, and what it did not anticipate is
the direction. It watched for a rule that was too narrow, and what arrived was a
source that changed the rule's grounds. Everything else goes into the successor
rather than stays behind, because which trailers a core commit carries is one
rule.
