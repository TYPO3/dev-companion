---
id: D-SKL-010
title: 'The assessment before a core patch reads the issue'
date: 2026-08-03
status: open
coveredBy:
  - SkillTest::theAssessmentBeforeAPatchReadsTheIssueAndTheReviewServer
---

# D-SKL-010 — The assessment before a core patch reads the issue

**A session assesses a core patch before it writes it. That assessment asks
`typo3_forge_lookup` for the issue and `typo3_gerrit_lookup` for whether
somebody has already fixed it.**

`D-SKL-008` put both calls into the review skill and recorded, as evidence, that
`typo3-core-patch-development` routes to neither. The session that can still
save the work is the one about to write the patch.

## Evidence

Everything below comes from a measure on 2026-08-03 through this branch's
`bin/typo3-dev-companion`, started in `/home/benji/projects/typo3-cms`, which is
the checkout the feedback came from.

- The judged feedback is `feedback/2026-08-02-145128`: nine assessment steps
  from the Forge #105403 session, offered as a procedure for `typo3_task_guide`
  at `changeType=bugfix`. Two of the nine have landed since. Step 1, reproduce
  against the branch you fix, is the fourth sentence of the skill's "Establish
  the issue before you believe it". Step 8, the goal behind the symptom, is
  `R-GUI-008` and opens the checklist of every brief.
- The knowledge the session established by hand is here as well. The re-run of
  its own call returns the hint `fluid-resource-uris`. That call is task "Fix
  f:image ViewHelper failing when src contains a cache busting query string",
  `changeType=bugfix`, `area=fluid`, `targetVersion=15.0`. The hint states where
  the core applies the cache bust and that `f:image` and `f:uri.image` are not
  on the System Resource API. That is the inconsistency the session hunted for,
  answered rather than hunted.
- The same answer's `nextTools` names six tools for a bugfix and neither lookup.
  The skill's step 3 says to read the issue and names nothing to read it with.
- `typo3_forge_lookup` with `issue: 105403` answers what the report cannot.
  Status `Under Review` and target `next-patchlevel` today, against the closure
  the session read in the notes — "closing as lack of feedback and alternatives
  possible", Georg Ringer, 2026-03-10. Six notes, of which two are the Gerrit
  bot's announcement of patch sets 1 and 2 of the session's own change.
- **The relations reach the change that introduced the behaviour, in that same
  call.** They are #99203 and #105953. `Feature-99203` under `13.2/` in
  `.checkouts/13.4` — "Streamline FE/versionNumberInFilename to 'EXT:'
  resources" — is the entry that gave `f:uri.resource` its `useCacheBusting`
  argument. The feedback attributes that to #100696 and reached it with a Forge
  search on the feature wording. One lookup on the reported issue reaches it.
- The changelog route to the same fact misses. `typo3_changelog_lookup` with
  `cache busting resource ViewHelper` at version 13 matches nothing and says
  which words reach what. The entry's title names `versionNumberInFilename` and
  its body carries the session's words
  ([`D-ANS-030`](../answers/ans-030-the-changelog-matcher-runs-over-the-title-it-prints.md)).
  The issue's relations are the route that works here, which is why the step is
  the Forge call rather than a changelog query.
- `typo3_gerrit_lookup` with `issue: 105403` answers `empty`, and with
  `change: 95067` — the change number the issue's own notes announce — `empty`
  as well. `D-ANS-033`'s caveat therefore reproduces rather than goes stale. The
  change is not visible to an anonymous caller, and an empty answer is a
  statement about the review server.

## Decided

- The two calls go into the assessment step of `typo3-core-patch-development`,
  where the review skill already has them. A session reads them for what the
  description does not carry: the status and target version as they stand today,
  the relations, and the notes.
- The session makes the Gerrit call before it writes code. Its cheapest outcome
  is the one that cancels the work, and it costs one request.
- Three assessment rungs the order does not carry come with them. A session
  checks a deferred decision against today's API before it treats its blocker as
  still there. The argument that carries a bugfix is the same inconsistency
  inside one version, which is what separates a defect from a wish. The session
  establishes the blast radius while it assesses, because it decides the change
  type and what the entry owes.
- **The venue is the skill and not `typo3_task_guide`, which is what the
  feedback asked for.** The guide answers one call and returns a checklist. A
  session has to read an order that decides what it commits to in sequence, and
  the skill is what this server ships for that. The guide's checklist keeps the
  one line it already has.
- Not decided: whether the rungs are sentences in the section that exists or a
  reference file beside it. That is writing, and
  [writing-a-skill.md](../../documentation/contributing/writing-a-skill.rst) is
  what it waits for.

## Assumed

- That the relations of a reported issue usually reach what introduced the
  behaviour. One case says so here, and it is the case the feedback is about.
- That the three rungs are the domain's method rather than one session's. The
  corpus supports the two calls from four sessions. The rungs come from this
  feedback alone, and their claim is that each one changed what that session
  concluded.

## Wrong if

- A session with the skill active reads a stale issue as current anyway. Then
  the step is placed where the reading has already formed, which is what step 3
  of the order exists to prevent.
- `typo3_gerrit_lookup` answers `empty` on every issue a session takes on,
  because a core issue worth a fix is usually one nobody has pushed for. Then
  the call buys nothing and `D-FBK-027` refuses it.
- The rungs read as a checklist and a session skips them as one. A skill that
  grows a sentence per feedback is no longer an order. The three added here are
  the first that came from a single report.
- The blast radius turns out to be knowable only after the change. Then it is
  not an assessment step, and what it belongs to is the changelog decision.

## Since then

Written into the patch development skill the same day, and the open question is
settled by the writing: the two calls are steps of the section that establishes
the issue, and the three rungs are paragraphs closing it rather than a reference
beside it — a reference is read once and this is read in sequence.

Two of the seven steps in the feedback are not in the change and neither is a
slip. One was already carried in the skill's own words. The other is no fourth
rung on purpose. This **Wrong if** says a skill that grows a sentence per
feedback is no longer an order. What the tracker answer names is the author of
each note rather than of the issue. A second session reporting an assessment
formed on the reporter's standing is what would make it one.
