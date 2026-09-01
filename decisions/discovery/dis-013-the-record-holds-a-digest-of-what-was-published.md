---
id: D-DIS-013
title: The record holds a digest of what was published
date: 2026-08-06
status: open
coveredBy:
  - InstallerRecordTest::aPublicationThatIsNoLongerTheCurrentOneSaysWhichWayItDrifted
  - ScopeTest::theInstructionsFitWhatAClientKeeps
  - StdioServerTest::aProjectWhoseSkillsNobodyHasUpdatedHasThemPutBack
---

# D-DIS-013 — The record holds a digest of what was published

**The record gains a digest of what was published, the check compares that
rather than the files, and the sentence it needs displaces one from the
instructions.**

The record held names alone, and a name is the one thing that does not move when
a skill is rewritten. So a project kept the workflow it was installed with,
under the current name, and every listing on both sides agreed.

## Evidence

- The checkout this was raised in, read on 2026-08-06:
  `.typo3-dev-companion/state.json` recording the client `claude` and twelve
  skills, and no `.claude/` directory at all. The published directories ignore
  themselves (`R-DIS-024`), so `git clean -xdf` removes them and the record
  survives saying they are there.
- The instructions handed to a client at initialize were 1925 characters of the
  2048 `R-ANS-013` holds them to, and the case where a caller excludes every
  tool was at 2048 exactly — the short form of the exclusion prefix already.
  There was no room for a sentence, which the test's own comment had recorded on
  2026-08-05 after a session asked for one and found nowhere to put it.
- A digest over the skill directories and `skills/base.md` costs one `hash_init`
  and about forty file reads at server start, and only where a record exists at
  all.

## Decided

- The digest covers each published skill's own files and `skills/base.md`, in
  sorted order, with the set folded in: the drafts change what is published, so
  they change the digest, and the digest of a project that asked for them cannot
  read as current in one that did not.
- A record with no digest counts as stale rather than as current. "Not
  established" and "matches" are what this exists to keep apart, and one
  `update` turns the first into the second.
- Beside the digest, one `is_dir` per recorded skill. That is a stat rather than
  a comparison, and it answers the case the digest cannot see: the publication
  is gone. What its files say stays the digest's half.
- The sentence displaced from the instructions is "What is a property of an
  installation rather than of TYPO3 is asked of that installation instead of
  answered from the catalogue." It is the abstract of what the two paragraphs
  above it already say concretely — start with `typo3_project_describe`, call
  `typo3_icon_lookup` against the installation — and the version binding that
  followed it in the same paragraph is untouched. `R-ANS-013` requires a
  displacement rather than an addition, and this is it.
- Against comparing the published files themselves. It cannot lie, catches a
  hand-edited copy, and reads every published tree of every recorded client at
  every server start — the cost is paid per session for a case that is one edit
  in this package.
- Against recording `Factory::SERVER_VERSION`. It is the cheapest and it lies:
  the constant sits at `0.3.0` and moves rarely, so every change between two
  bumps leaves the digest saying the project is current.

## Assumed

- That the server is started in the directory the record is in. That is where
  `install` writes and what the client entry a session is started from names, so
  the two are the same directory or neither exists. Nothing walks up for a
  record: it would find a parent project's.
- That the notice reaching the agent is worth a sentence out of the routing.
  What was displaced is recoverable from `typo3_server_scope` and from the two
  paragraphs that demonstrate it; a skill loaded from an old publication is
  recoverable from nothing, because it does not announce which version it is.
- That a client shows the instructions to the model. Where it does not, stderr
  is what is left, and that is why the long form goes there.

## Wrong if

- The digest changes on something nobody published — a file mode, a line ending
  written differently by a checkout on another platform — and a project is told
  to update on every start with nothing to update. Then what is hashed is too
  wide and belongs narrowed to the skill bodies.
- Somebody edits a published copy deliberately and this stays silent about it.
  Then the case for reading the files rather than the record is made, and the
  cost of doing so at every start is what has to be answered.
- The instructions gain a sentence again and there is nowhere to put it. The
  budget is spent either way; what this change establishes is that the notice
  fires rarely and the routing is what pays for it, and the next session may
  read that trade differently.

## Since then

The set came back out on 2026-09-01. There is one publication per release again,
so the digest covers the skills and `skills/base.md` and nothing folded in —
`D-SKL-087`.
