---
id: D-SKL-028
title: 'A triage reaching for a previous attempt is routed to it'
date: 2026-08-09
status: open
coveredBy:
  - SkillTest::aTriageSaysWhatThePreviousAttemptCostBeforeItHandsOver
---

# D-SKL-028 — A triage reaching for a previous attempt is routed to it

**A triage that reaches for a previous attempt routes to the patch itself, not
to a second lookup of a change the issue answer carries.**

`typo3-core-issue-triage` numbers its steps in one run. Step 7 asks for
`typo3_gerrit_lookup` with the change numbers `reviews` carries, after step 4
has asked it with the issue number. What step 7 says it is for is the attempt's
code, the cheapest description of what a fix looks like. That is the one thing
neither call returns.

## Evidence

- Re-run on 2026-08-09, the queries `feedback/2026-08-08-224352` reports.
  `typo3_gerrit_lookup(issue "82228")` and `typo3_gerrit_lookup(change "53819")`
  answer with the same change field for field: 53819, ABANDONED, master, patch
  set 3, `0271db52b1d088b4b5ac33f1f7a5e15833e08cd0`, `Packages/TYPO3.CMS`, last
  moved 2017-11-23.
- `typo3_forge_lookup(issue "82228")` had already answered `reviews` as
  `[{change 53819, patchSet 3, on 2017-08-29, url}]`, which is where the number
  came from. Three calls produced what the first two did.
- The change form still answers what the issue form cannot.
  `Gerrit::changesForIssue()` hands back only a change whose commit message
  names the issue, and drops the one whose own number is the issue number
  (`D-ANS-055`). A `reviews` entry the issue answer did not carry is exactly the
  entry worth naming.
- What settled the triage was the diff. The session that reported fetched
  `refs/changes/19/53819/3` and read it. The 2017 proposal reinterpreted `width`
  plus `height` as a fit-into-box, which is what the `m` modifier already does.
  So the report asked for behaviour that exists under another form.
- The session built the refspec by hand from the shard rule.
  `feedback/2026-08-08-224354` reports the same reconstruction from a different
  task, a review of change 95179 in a checkout. So two sessions built one string
  every input to which was in the answer.
- The string stands here and no triage passes it.
  `knowledge/documents/core/contribution/gerrit-workflow.md` carries the ref
  form, the sharded path and the remote asymmetry, and
  `typo3-core-patch-checkout` step 3 is the only skill that routes to it
  (`D-SKL-021`).

## Decided

- Step 7 no longer asks for a change the issue search already answered for. It
  names the case where the change form buys something: a `reviews` entry the
  step 4 answer does not carry.
- The step routes to `typo3://guides/core/contribution/gerrit-workflow` for the
  fetch, which is where the ref form lives. The skill carries no ref form of its
  own. `D-SKL-021` settled that, because a ref in a file installed in somebody
  else's project is a fact no release here corrects.
- It is a routing line and not a handoff to `typo3-core-patch-checkout`. That
  workflow puts a change under review into the checkout, rebases it and restores
  the branch. A session reads an abandoned patch from 2017 and never applies it.
  A triage sent down that workflow would have the session do the act `D-SKL-021`
  keeps the two skills apart over.
- Nothing changes in `typo3_gerrit_lookup`'s `change` description, which the
  feedback names as what invited the call. A caller reads a parameter
  description while it composes a call, so it cannot say the call is not worth
  it. The step that named the call can.
- The refspec in the Gerrit answer is not decided here.
  `feedback/2026-08-08-224354` reports it whole, and what this judgement adds to
  it is the second report.

## Assumed

- The review bot posts on a Forge issue because the commit message names it, so
  a `reviews` entry is ordinarily one `message:<issue>` finds. The entry the
  change form stays for is the exception rather than the rule.
- A session at step 7 can fetch. The triage stands in a core checkout by then,
  and the fetch is against the review server rather than the remote that
  checkout fetches from.

## Wrong if

- A session follows the changed step, fetches the attempt and reports the diff
  as not worth the fetch. That would say the lookup was the right instrument
  after all.
- A triage misses an attempt because the step no longer enumerates `reviews`.
  That shows as a verdict that names no previous patch on an issue whose journal
  carries one.
- Sessions that reach that step start to rebase or push the patch set they
  fetched. That would say the routing merged two workflows rather than named a
  page.
