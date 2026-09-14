---
id: D-ANS-106
title: A commit in a checkout is a handle the review lookup takes
date: 2026-08-25
status: open
coveredBy:
  - GerritTest::aChangeNamesTheBranchesItsReleasesTrailerClaims
  - GerritTest::aCommitFromACheckoutNamesTheChangeItIsAPatchSetOf
  - GerritTest::aMessageThatWasNotReadClaimsNothingRatherThanNoBranches
  - GerritTest::anEmptyAnswerForACommitSaysWhatItCannotSeparate
  - GerritTest::theTextHalfTellsTheTrailerApartFromWhatWasPushed
---

# D-ANS-106 — A commit in a checkout is a handle the review lookup takes

**`typo3_gerrit_lookup` takes a commit hash as a fourth handle, and every change
it answers names the branches its `Releases:` trailer claims.**

A session on a triage of old issues had commit hashes in hand and had no way to
ask about them. It spent four git calls per commit, six where a backport came
into it. It told the user a fix had reached two branches where the trailer said
three.

## Evidence

- The feedback's question was re-asked against `review.typo3.org` on 2026-08-25,
  because the session's own query was git rather than this server.
- `commit:cf227b18e20` answers change 89740, MERGED on `main`. The commit
  message that comes back with it carries `Releases: main, 13.4, 12.4`, the line
  the session reached last and had already contradicted.
- The Change-Id query `Gerrit::change()` already makes for every named change
  answers all three siblings. Those are 89740 on `main`, 90012 on `13.4` at
  commit `aaec618cf33`, and 90014 on `12.4`. That commit is the backport hash
  the session went to `git log origin/13.4 -S` for.
- So one call carries what the feedback counted as six, which is the measure
  [`D-FBK-027`](../feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)
  sets.
- The handle is absent rather than undocumented. `change` is a Change-Id or a
  change number, and `change:cc880c67777` answers HTTP 400
  `Invalid change format`, which `Fetch` reads as no answer at all. So the tool
  reports that the review server did not answer, about the one handle a checkout
  hands it. `commit:cc880c67777` answers change 92881.
- The trailer is in the payload already. Both directions ask for
  `o=CURRENT_COMMIT`, and `Gerrit::trailers()` reads `Resolves:` and `Related:`
  out of that same message and drops the rest of it.
- `bin/cli hints:probe "which TYPO3 releases contain a given fix commit backport"`
  reaches `public-api-surface`, `breaking-without-a-moved-member` and
  `extension-ter-release`. None of them is the subject, and 1a is not the answer
  either. The fact is on the review server rather than absent from the world.
- One session reports it. What weighs beside that count is the correction. The
  session stated the release set wrongly to the user and took it back a turn
  later, which is a cost no round trip carries.

## Decided

- Taken on, step 1b, an absent shape. Not 1a, because nothing about TYPO3 is
  unknown here. Not 2, because no answer carries the fact and there is nothing
  to move. Not 4, because no wording hands a caller a handle the schema refuses.
- `commit` is a fourth way in beside `issue`, `change` and the search, and it
  queries `commit:`. What it answers is what `change` answers: it names one
  change, and everything after that is the read the tool already makes.
- `releases` is a field per change — the branches the commit message's
  `Releases:` trailer names, read from the message the answer already fetches.
  Empty where the message carries no trailer, which is every change outside the
  core project.
- Release versions stay outside the boundary. Gerrit answers branches, and a tag
  needs the release list held against a merge date, which is a second source and
  a second read.
- The trailer and the siblings are two claims and the answer keeps them apart. A
  trailer says where the author meant the patch to go; a sibling on a branch is
  a patch that is there. That is
  [`D-ANS-073`](ans-073-what-can-take-a-patch-and-where-this-one-goes-are-two-readings.md)
  one level down.
- The tracker half the feedback asks for is not built.
  [`D-ANS-064`](ans-064-an-issue-answer-holds-what-a-triage-needs.md) decided
  that `reviews[]` carries handles because the state is one
  `typo3_gerrit_lookup` call away. A trailer there costs one review-server call
  per change on every issue read.
- Queued rather than built in this run. It changes `src/` and a declared output
  schema, which
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md)
  leaves on the far side of the line whatever evidence the judging run holds.
- The card carries `normal`, set by the measured calls and the correction
  against the one session that reported both.

## Assumed

- That a session with a commit hash in hand thinks to ask this server at all.
  This one went to git and weighed nothing else, so the handle needs the routing
  that says it is there.
- That `commit:` takes the abbreviated hash a caller pastes out of `git log`.
  Eleven characters answered on 2026-08-25, and nothing says where the floor is.

## Wrong if

- A session gets the change, its siblings and its trailer and runs
  `git tag --contains` all the same. The branches would then not be what a
  triage needed, and the version half is the answer rather than something beyond
  it.
- A trailer names a branch no sibling targets and a caller reports the patch as
  released there. The two stand side by side for that reason. A reader who takes
  the trailer for the outcome would say the pair is not enough.
- The handle exists and nothing passes it, which would make this step 3 and the
  routing the lever rather than the schema.

## Since then

Built the same day the card came up. The commit hash is the fourth way in, and
the trailer is a field on every change whose message came back. The routing that
says the handle is there is in three places. A second read of the query and the
trailer against the review server came with the tests. The fixtures are that
read: one change, its two backports, and the trailer all three carry.

The issue direction carries the field too, which the decision neither asked for
nor ruled out. That search already fetches the message, so one rule covers every
path: the tool reads the trailer wherever the message came back. What stays
outside is the search that asks for no message at all.
