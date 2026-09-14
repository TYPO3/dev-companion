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

**The record gains a digest of the publication, and the check compares that
rather than the files. The sentence it needs displaces one from the
instructions.**

The record held names alone, and a name is the one thing that does not move when
a skill is rewritten. So a project kept the workflow its install came with,
under the current name, and every listing on both sides agreed.

## Evidence

- The checkout this came up in, read on 2026-08-06:
  `.typo3-dev-companion/state.json` records the client `claude` and twelve
  skills, and no `.claude/` directory exists at all. The published directories
  ignore themselves (`R-DIS-024`), so `git clean -xdf` removes them and the
  record survives and says they are there.
- The instructions handed to a client at initialize were 1925 characters of the
  2048 `R-ANS-013` holds them to. The case where a caller excludes every tool
  stood at 2048 exactly, with the short form of the exclusion prefix already.
  There was no room for a sentence. The test's own comment had recorded that on
  2026-08-05 after a session asked for one and found nowhere to put it.
- A digest over the skill directories and `skills/base.md` costs one `hash_init`
  and about forty file reads at server start. That only where a record exists at
  all.

## Decided

- The digest covers each published skill's own files and `skills/base.md`, in
  sorted order, with the set folded in. The drafts change the publication, so
  they change the digest. The digest of a project that asked for them cannot
  read as current in one that did not.
- A record with no digest counts as stale rather than as current. "Not
  established" and "matches" are what this exists to keep apart, and one
  `update` turns the first into the second.
- Beside the digest, one `is_dir` per recorded skill. That is a stat rather than
  a comparison, and it answers the case the digest cannot see: the publication
  is gone. What its files say stays the digest's half.
- The sentence that leaves the instructions is this one. "What is a property of
  an installation rather than of TYPO3 is asked of that installation instead of
  answered from the catalogue." It is the abstract of what the two paragraphs
  above it already say concretely. Start with `typo3_project_describe`, call
  `typo3_icon_lookup` against the installation. The version bound that followed
  it in the same paragraph stays. `R-ANS-013` requires a displacement rather
  than an addition, and this is it.
- Against a comparison of the published files themselves. It cannot lie, catches
  a hand-edited copy, and reads every published tree of every recorded client at
  every server start. Every session pays that cost for a case that is one edit
  in this package.
- Against recording `Factory::SERVER_VERSION`. It is the cheapest and it lies.
  The constant sits at `0.3.0` and moves rarely, so after every change between
  two bumps the digest still says the project is current.

## Assumed

- That the server starts in the directory the record is in. That is where
  `install` writes and what the client entry a session starts from names. So the
  two are the same directory or neither exists. Nothing walks up for a record:
  it would find a parent project's.
- That a notice which reaches the agent is worth a sentence out of the routing.
  The displaced sentence is recoverable from `typo3_server_scope` and from the
  two paragraphs that demonstrate it. A skill loaded from an old publication is
  recoverable from nothing, because it does not announce which version it is.
- That a client shows the instructions to the model. Where it does not, stderr
  is what remains, and that is why the long form goes there.

## Wrong if

- The digest changes on something nobody published, a file mode, a newline a
  checkout on another platform writes differently. Then a project hears "update"
  on every start with nothing to update. Then the hash covers too much and
  belongs narrowed to the skill bodies.
- Somebody edits a published copy deliberately and this stays silent about it.
  Then the case for a read of the files rather than the record stands. The cost
  of that at every start is the question to answer.
- The instructions gain a sentence again and there is nowhere to put it. The
  budget goes either way. What this change establishes is that the notice fires
  rarely and the routing is what pays for it. The next session may read that
  trade differently.

## Since then

The set came back out on 2026-09-01. There is one publication per release again,
so the digest covers the skills and `skills/base.md` and nothing folded in —
`D-SKL-087`.
