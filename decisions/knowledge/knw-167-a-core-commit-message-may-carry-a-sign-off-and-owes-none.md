---
id: D-KNW-167
title: A core commit message may carry a sign-off and owes none
date: 2026-10-07
status: open
revokes: [D-KNW-125]
coveredBy:
  - CommitMessageTest::aCoreDraftKeepsASignOffAndAsksForNone
  - KnowledgeTest::theTrailerAnswerStatesTheRuleAndWhatLeavesItUnenforced
---

# D-KNW-167 — A core commit message may carry a sign-off and owes none

**A core commit message carries `Resolves:`, `Related:`, `Releases:` and the
`Change-Id:` the hook writes, and may carry `Signed-off-by:`.
`typo3_commit_message_guide` keeps a sign-off where the message has one, and
neither writes nor asks for an absent one.**

`D-KNW-125` made the sign-off required. This entry revokes it whole, as that
entry revoked `D-KNW-110`, because which trailers a core commit carries is one
rule.

## Evidence

- The maintainer ruled it on 2026-10-07, in the session that recorded this. A
  contributor may set the sign-off, and it is no requirement. That is
  `R-KNW-075` exercised a second time.
- The core's own `AGENTS.md` still says to sign off every commit. The maintainer
  reported on 2026-10-07 that an active proposal removes that demand from the
  core. Until it lands, a session in a core checkout reads a demand this page
  does not make.
- The board's statement of 2026-07-20 that `D-KNW-125` rested on calls the
  certificate "a recommendation to consider, not a precondition". So the board
  supports this rule as well as the one before it.
- `Build/git-hooks/commit-msg` leaves the line out of the copy it hashes the
  `Change-Id` from, read on 2026-08-25 for `D-KNW-125`. So a sign-off that stays
  on an amend keeps the patch set valid, and so does one that goes.
- The merged history carries the sign-off on about half the commits since
  2026-08-23, measured on 2026-10-05. A rule that strikes it would strike half
  the history, and a rule that demands it would fail the other half.

## Decided

- The draft keeps a `Signed-off-by:` line as an unknown trailer. It stays after
  `Releases:` and before `Change-Id:`, in the order the message had it among the
  other unknown trailers.
- No placeholder and no `missing-sign-off` check. The placeholder existed to ask
  for the line, and the read-back that dropped it went with it.
- The page keeps what a sign-off claims and how `git commit -s` writes it. A
  contributor who sets the line still makes the attestation.
- The agent trailers stay refused. `D-KNW-161` holds them and this entry does
  not touch them.

## Assumed

- That nobody commits a draft with `Signed-off-by: YOUR_NAME <YOUR_EMAIL>` from
  an answer this server gave before the change. The draft now keeps such a line
  as it keeps any other.

## Wrong if

- A reviewer strikes a core patch for a missing sign-off. Then the practice on
  Gerrit requires what this rule leaves open.
- The core adds a check that rejects a message without the trailer. Then the
  draft passes this tool and fails the hook.
- A session in a core checkout follows that checkout's `AGENTS.md` demand over
  this page and reports an absent sign-off as a defect. Then the page states the
  rule where that session does not read it.
- The proposal to remove the demand from the core's `AGENTS.md` fails. Then the
  core keeps asking for what this rule leaves open, and the page has to say
  which of the two a core patch follows.
