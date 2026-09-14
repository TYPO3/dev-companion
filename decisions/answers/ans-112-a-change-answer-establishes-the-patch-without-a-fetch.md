---
id: D-ANS-112
title: A change answer establishes the patch without a fetch
date: 2026-08-26
status: open
coveredBy:
  - GerritTest::aChangeReadByNameCarriesThePathsItsPatchSetTouches
  - GerritTest::aFileWithNoLinesToCountSaysWhyItHasNone
  - GerritTest::aMovedFileNamesThePathItCameFrom
  - GerritTest::pathsNobodyAskedForAreNotPathsThatCouldNotBeRead
  - GerritTest::theCommitMessageIsReadByBothFormsAndHandedBackByAChangeReadByName
  - GerritTest::theTextHalfCarriesTheCommitMessageWhole
  - GerritTest::theTextHalfNamesEveryPathAndWhatThePatchDoesToIt
---

# D-ANS-112 — A change answer establishes the patch without a fetch

**`typo3_gerrit_lookup` answers the paths a patch set touches and the commit
message it carries. So a session establishes a change with nothing on disk.**

The answer says how to fetch a patch set and nothing about what is in one. So
the ref is the only route this server offers to either fact. A session on a
triage of a shortlist fetched every change on it into the user's own working
checkout.

## Evidence

- `feedback/2026-08-24-195307` fetched eight open changes into the user's
  working checkout for a triage and tagged each one `gr/<number>`, then a ninth
  for the review itself. The user stopped the session over it twice. Its own
  account is that the review never needed the refs, and that
  `/changes/<n>/revisions/current/{commit,files}` would have covered the
  shortlist.
- `feedback/2026-08-25-105203` is a different task shape that reaches the same
  endpoint by hand. Before the skill ran, that session had already called
  `changes/95369/detail?o=CURRENT_REVISION&o=CURRENT_COMMIT` with raw curl.
- Re-run on 2026-08-26 against the review server. `change: "95369"` answers
  number, changeId, subject, status, branch, patchSet, commit, project, updated,
  created, insertions, deletions and mergeable. It answers url, fetch, labels,
  commentCount, unresolvedCommentCount, comments, chain, issues, releases and
  messages. It names no file and carries no commit message, and `+47 -48` is the
  whole of what it says about the diff.
- The tool fetches the message already and drops it on purpose. Both queries
  that read it ask for `o=CURRENT_COMMIT`. `Gerrit::changesForIssue()` and
  `Gerrit::issues()` each `unset()` it before they answer, because what those
  two read it for is the handles.
- `typo3-core-patch-review` asks for the changed paths as one of the four things
  a review establishes. It passes them to `typo3_hint_lookup`,
  `typo3_test_run_guide` and its own enumeration of what the diff removes. It
  asks for the commit message as the argument of `typo3_commit_message_guide`.
  The fetch is the only way this server offers to reach either of them.
- `bin/cli hints:probe "reviewing open gerrit changes without fetching refs into the checkout"`
  matched nothing. This is not 1a either: the fact is on the review server
  rather than absent from the world.

## Decided

- Taken on, step 1b, an absent shape. Not 2, because no answer carries the paths
  and there is nothing to move. Not 4, because no wording could tell a review to
  work from the API while the API answer withholds what the review has to
  establish.
- `files` is the paths the current patch set touches, which is Gerrit's
  `o=CURRENT_FILES` on the query already in flight rather than a second call.
- `message` is the commit message the tool fetches today and drops. What made
  the drop right is that nothing then read it. The review skill reads it, and a
  caller with the subject alone cannot check a trailer.
- The boundary is what the patch touches and what it says about itself, and not
  the diff. A file list decides a triage, and the hunks are what a fetch is for.
  That is the line the session that reported it drew itself.
- Both fields go on a change read by name, where the votes and the comments
  already are —
  [`D-ANS-079`](ans-079-a-change-answer-carries-its-votes-and-its-comments.md).
  A search answers up to 25 changes and asks whether a patch exists at all,
  which no file list changes.
- [`D-ANS-068`](ans-068-a-change-answer-carries-the-ref-that-fetches-the-patch-set.md)
  stands. The ref is still the answer where a session needs a checkout. It was
  the whole answer only because nothing beside it said what the patch was.
- Queued rather than built in this run. It changes `src/` and a declared output
  schema, which
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md)
  leaves on the far side of the line whatever evidence the judging run holds.
- Nothing holds it yet, so `coveredBy` is empty until the change lands with the
  tests that read the two fields.
- The card carries `normal`. Two sessions from different task shapes reached the
  same endpoints by hand, and one of them cost its user the session.

## Assumed

- That `o=CURRENT_FILES` serves the file list over the same anonymous path as
  the options already asked for. It is Gerrit's documented option for it and was
  not measured here.
- That one file list is small enough to sit on every change a lookup by name
  answers. A core patch is one commit, so this is the size of a patch rather
  than of a series, and it was not weighed.

## Wrong if

- A session holds the file list and fetches the change anyway, which would say
  the paths were not what sent it to the checkout.
- The file list is the weight that makes a change lookup too big to read. That
  would put it behind a parameter rather than in the answer.
- A triage acts on a file list where the hunks contradict it, a path the patch
  only moves read as a path it rewrites. That would say the boundary sits in the
  wrong place.

## Since then

Built on 2026-08-26 for a change read by name, and null everywhere else. The key
is present and null rather than absent. A caller that branches on whether a key
is there cannot tell an answer that read nothing from a server that carries
nothing.

Both **Assumed** were measured. The option serves the list over the same
anonymous path, and the weight comes off the population rather than off one
change. The median change touches five files and the ninetieth percentile forty.
So the list rides on every named change rather than behind a parameter, and what
a lookup grows by is regularly under a kilobyte.
