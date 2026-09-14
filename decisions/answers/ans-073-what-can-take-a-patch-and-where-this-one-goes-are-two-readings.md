---
id: D-ANS-073
title: What can take a patch and where this one goes are two readings
date: 2026-08-10
status: open
coveredBy:
  - CommitMessageTest::aBranchTheListDoesNotCarryIsAWarningSayingWhenItWasRead
  - CommitMessageTest::aMaintainedLineFurtherBackSaysWhatItClaims
  - GerritTest::thePlacementNamesTheToolThatReadsATrailerAgainstThoseLines
  - GerritTest::theTextHalfTellsTheTrailerApartFromWhatWasPushed
---

# D-ANS-073 — What can take a patch and where this one goes are two readings

**A bug fix and a task reach the development line and the one release line back
from it, and no further without severity.**

The server said a bug fix goes to every maintained line that carries the defect,
and `ReleaseLines::releasable()` answered three lines. A caller who reads the
two together writes all three into the trailer and believes the guide told it
to.

## Evidence

- `feedback/2026-08-10-114833` is the maintainer of this repository as they
  state the rule directly, which is the only source it has. No session
  established it, and this entry records the correction rather than a file from
  which a reader could reconstruct it.
- The core's published documentation carries the shape and not the sentence. The
  contribution guide says "we **always** fix things on **main** first and then
  backport a change if it goes along with our support rules for older versions".
  Its own review checklist asks whether "the `Releases:` scope of a patch [is]
  spanning the proper TYPO3 versions (depending on the state of current LTS and
  priority-bugfix-only releases)". So a line's state, not the defect's presence,
  is what the published rule turns on too. TYPO3 Explained says support is
  "provided for the current as well as the preceding LTS release", which is the
  same reach from the other end.
- The over-broad half is what costs somebody else work. A trailer that names the
  third line asks a merger to cherry-pick onto a line the change was never meant
  for. A reviewer who refused it had this server's own sentence against them.
- Two of the three places the feedback names carry the statement:
  `core/contribution/commit-messages.md` and
  `core/contribution/gerrit-workflow.md`. The review skill carries neither the
  word `Releases` nor `trailer` — it never repeated the rule, so there was
  nothing to correct there.

## Decided

- The trailer is two readings and the corpus says so: where the defect is, on
  each line, and whether the severity earns an older one. The second is a
  judgement the author states rather than a consequence of the first.
- `ReleaseLines::ordinary()` is the reach, `releasable()`'s first two, since the
  list is newest first. It is a second method rather than a narrower first. The
  two answer different questions, and the absent-trailer check now names both:
  what can take a patch at all, and what this change goes to.
- A trailer that names a maintained line beyond that reach is a warning rather
  than an error, on a `[BUGFIX]` and a `[TASK]` alone. It is legitimate exactly
  where the severity earns it, so the check says what the trailer claims and
  leaves the claim to the author. A `[FEATURE]` is the release managers' call
  and never gets it.

## Assumed

- That the reach is always two lines. It is what "one line back from main" means
  while one release line is current. Nothing here derives it from the release
  calendar, and a period with two current lines would need the rule restated
  rather than the arithmetic changed.

## Wrong if

- A merger reports a trailer that should have named an older line and did not.
  The warning is what would have discouraged it, and then the wording would be
  wrong rather than the rule.
- The published contribution documentation changes to state the reach in
  branches. Then the statement has a source the next session can read again, and
  this entry is no longer the only evidence for it.
