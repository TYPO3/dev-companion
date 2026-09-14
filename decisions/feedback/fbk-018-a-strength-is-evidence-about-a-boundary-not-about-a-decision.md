---
id: D-FBK-018
title: A strength is evidence about a boundary, not about a decision
date: 2026-08-02
status: confirmed
readings:
  - 2026-08-02
  - 2026-08-03
  - 2026-08-09
  - 2026-08-18
  - 2026-08-21
  - 2026-08-22
  - 2026-08-25
  - 2026-08-27
  - 2026-08-28
coveredBy:
  - ForgeTest::theCommentFilterInvitesTheReadingItNarrows
---

# D-FBK-018 — A strength is evidence about a boundary, not about a decision

**A feedback that reports no gap is not a confirmation. What it carries is where
a boundary runs, read against the costs other feedback report at the same one.**

The ladder has no rung for a report of what worked. Every step names something
absent, misplaced or misworded, so the one question has to come from the other
side: what is the strength evidence of?

## Evidence

- Three feedback from one debrief, five seconds apart, same directory
  (`/home/benji/projects/bootstrap_package`) and same model. `2026-07-31-174529`
  reports `typo3_changelog_lookup` as what made the review's first finding
  provable. `2026-07-31-174524` reports the same tool as unable to say whether
  the v14 Page module renders a backend layout without column identifiers.
  `2026-07-31-174526` reports that no lookup says whether a
  `contentRenderingTemplates` registration is still consumed.
- The strength reproduces. Re-run on 2026-08-02 through
  `bin/typo3-dev-companion` from that directory. `ext_tables.php` reaches *14.3
  Deprecation: ext_tables.php in extensions* (#109438), and `UpgradeWizard`
  reaches the 14.0 deprecation of the moved interfaces (#106947).
  `addPiFlexFormValue` reaches its 14.0 deprecation (#107047), and all three
  `.rst` files are in `.checkouts/main`.
- Two of its claims are looser than the answer. `typo3_project_describe`
  classifies six of ten declared commands as `check` or `change` and three as
  `unknown`, rather than "every repo command". The three are the phpunit suites,
  which the answer's own prose says it will not classify. The platform reality
  it credits to that tool comes from `typo3_extension_describe`. Its footer
  reports that no question went to the installation because the host runs PHP
  8.3.23 against a `>= 8.4.0` requirement.
- Nothing here can say which `base.md` that session read. Both installed copies
  of the skill in that repository changed after the report, `.claude/` at 18:01
  and `.agents/` at 20:04 against a 17:45 feedback. The step 5 deprecation sweep
  had landed 81 minutes before it.

## Decided

- This commit closes the feedback. There is nothing to queue. A keep is not
  work, and the two costs it points at are on the board with cards of their own.
  [`D-FBK-017`](fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)
  makes "nothing to do" the close answer rather than a special case.
- A strength does not confirm a decision. It is a session's account of its own
  run, which is what [judging.md](../../documentation/records/judging.rst)
  already refuses to assess in the other direction. The session was there and
  the reader was not. Recorded runs with timings in them confirm `D-SKL-001`,
  and a self-report cannot stand against its **Wrong if** the same way. Nothing
  joined it here.
- What the strength is evidence for is where the corpus stops. It and the two
  costs are one boundary from both sides. The changelog answers what **changed**
  at a version, and both costs asked what still **holds** at one. A change
  carries an issue number and a state does not. That is why the same tool is
  precise in one direction and silent in the other.
- The entry names the gap and not the fix. What fills the state half belongs to
  the two cost cards, which are in hand elsewhere. Their answer named from here
  would be the copy-down that judging.md says produces a guess with a
  judgement's authority.

## Assumed

- That the three came from one session. They share a directory, a model and five
  seconds, and nothing in a feedback records a session.
- That a strength is worth a judgement run at all. This one cost a second run
  and bought the boundary above, which none of the three files states on its
  own. But the cheaper alternative, the archive with no read, would have looked
  identical from outside.

## Wrong if

- ~~A positive feedback turns out to carry a lever nothing else does — praise
  that names what the session did instead. The ladder would then apply after
  all. A strength read as boundary-evidence only would have skipped it.~~ Fired
  in **Since then** below and in four visits after it.
- The two cost cards get their judgement and land somewhere other than the
  change/state boundary. The pair above would then be a view of three files
  rather than a property of the corpus.
- ~~Strengths accumulate with no read, because a close of one leaves nothing
  anybody can point at afterwards. This entry and its commit are the whole
  record. If nobody cites either again, the run was a cost with no return.~~ The
  visits below answer it. Each leaves a guard or a judgement on another entry
  rather than a commit message.
- ~~The line a strength's praise implies turns out not to be worth its place.
  `typo3_extension_describe` with an absent translation named on every extension
  that ships none costs more than it buys. A read of what the praise implies
  would then be right about the asymmetry and wrong about its weight.
  `R-PRJ-006` is what would need the sentence instead.~~ Answered on 2026-08-02:
  the line costs a word, and `Ships:` says it.
- A strength turns up whose praise implies a property the answer does not have,
  and a check of its quotations reaches that property anyway. The step added
  below — read what the praise implies, not only what it quotes — would then be
  one this entry did not need.
- A keep-request meets a refusal on a second run and the sentence turns out to
  have been right after all. So a strength read against today's file lost
  something the session knew and this repository had forgotten.
- The text is not where an absence has to stand, because the clients that matter
  render the data. `R-ANS-002` assumes the opposite client, and no recorded run
  here shows which of the two a session is.
- A keep-request answered with a test that quotes refuses the rewrite that
  improves a sentence as readily as the one that drops it. The helper those
  tests read a skill through takes the line breaks out of the comparison and
  nothing takes the wording out. So from then on the test is what an author has
  to argue with rather than the file. That is the cost of the only form a keep
  has here, and it is paid per sentence.

## Confirmed on 2026-08-28

Twenty visits, compact here since the day the form got its bound (`D-DOC-066`).
Each held the rule and each is a date above. What none of them found is a
strength that confirmed a decision rather than described a boundary. Three
findings outlived their accounts. A strength regularly misplaces the credit.
Five corpora on record, and not one was wrong about what it received, only about
which file gave it. A save can be a statement plus the round trip that statement
costs, which reads as a pair and is step 4. And what a test reaches by name
against what it reaches only by a quote is the line the 2026-08-09 four split
on. The archived feedback carries each account, and the entries those readings
bear on carry what they changed.
