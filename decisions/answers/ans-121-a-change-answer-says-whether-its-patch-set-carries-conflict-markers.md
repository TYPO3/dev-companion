---
id: D-ANS-121
title: A change answer says whether its patch set carries conflict markers
date: 2026-08-27
status: open
coveredBy:
  - GerritTest::aChangeNamesTheChangeItWasCherryPickedFrom
  - GerritTest::aChangeSaysWhichFilesItsCurrentPatchSetCarriesConflictMarkersIn
  - GerritTest::aReportOnAPatchSetSomebodyHasReplacedIsNotThisPatchSet
  - GerritTest::aSearchAsksForNoLogAndSaysNothingAboutConflicts
  - GerritTest::theConflictLineSaysTheChangeIsBrokenRatherThanUnreviewed
  - GerritTest::theReviewLogIsAskedForAndTheServiceUsersHalfIsSeparated
---

# D-ANS-121 — A change answer says whether its patch set carries conflict markers

**`typo3_gerrit_lookup` says whether the current patch set of a change carries
git conflict markers, and where it was cherry-picked from, whatever `messages`
asked for.**

A backport created through Gerrit's web UI landed with unresolved conflict
markers in a shipped JavaScript file. Every field of the default answer read as
a healthy new change.

## Evidence

- `feedback/2026-08-25-110659`, a review of change 95412 — the 14.3 backport of
  95392, created with the web **Cherry pick** action. Patch set 1 committed
  conflict markers into
  `typo3/sysext/form/Resources/Public/JavaScript/backend/form-editor/view-model.js`.
  What the default answer carried around that. Status NEW, patch set 1,
  `commentCount` 0, an empty `comments`, both labels with empty vote arrays. A
  clean subject, and a resolved Forge issue whose subject matched. A reviewer
  who reads it concludes "fresh patch set, nobody has looked yet", which is
  wrong in the way that matters. The patch is broken and a merge kills the form
  editor on 14.3.
- The session saw it because it passed `messages: "people"`, carried over from
  the previous call in the same session. Nothing asked it to.
- Re-run on 2026-08-27 through `GerritLookup::answer()`. 95412 is MERGED at
  patch set 3 and its conflict is history. The review log still carries the
  message on patch set 1 while the default answer says nothing about it. The
  report reproduces as a property of the answer rather than as a fact about one
  change.
- Where the fact lives, read against `review.typo3.org` on 2026-08-27: nowhere
  in the change payload. `ChangeInfo` carries `cherry_pick_of_change` and
  `cherry_pick_of_patch_set` unasked and no `contains_git_conflicts`, and
  `o=ALL_REVISIONS` answers each revision with `kind`, `_number`, `created`,
  `uploader`, `ref`, `fetch`, `commit` and `branch`. Only the change message
  says it.
- Of 867 open core changes read with `o=MESSAGES` the same day, 8 carry a
  conflict message. **One** carries it on the patch set that is current. That is
  change 93689, a 13.4 backport at patch set 1 since 2026-04-16, whose conflicts
  are in `Build/phpstan/phpstan-baseline.neon` and
  `PackageActivationService.php`.
- The other 7 are what the feedback's own shape would fire on. It asks for the
  field wherever such a message exists. A read that ignores which patch set the
  message names is wrong on 7 of those 8.
- Of the 400 most recently merged core changes, 133 are cherry-picks and 17
  carried a conflict message — one in eight. All 17 named patch set 1 and all 17
  merged at a later one, so the process catches this. What catches it is
  somebody who reads the log.
- A cherry-pick is not what creates one. The core project carries 39 conflict
  messages across its open, merged and abandoned changes. 26 open "Cherry Picked
  from branch …" and **13** open "Patch Set N was rebased". A field derived from
  the cherry-pick provenance misses a third of them.
- What a request for it costs. `o=MESSAGES` is no second round trip on the call
  `Gerrit::named()` already makes. Over 50 open core changes read both ways the
  same day it added a median of 6.3 KB per change. That is 1.7 KB at the
  smallest, 79 KB at the largest. The 57.9 KB against 14.3 KB `D-ANS-079`
  measured is one change with 21 patch sets. It is the cost of the log in the
  caller's hands, which nothing here changes.
- Two things the same report names as what worked, and both already arrive. The
  "Outdated Votes: * Verified+1 (copy condition: …)" line explained a vote that
  was gone. That is what `D-ANS-079` asks for the log for, and what the answer
  already points at wherever it read a label and `messages` was `none`. The
  `issues` array joined the change to Forge issue 110502 with its subject and
  status, which is `D-ANS-098`.

## Decided

- Built as two fields on a change read by name, beside the comments, the chain
  and the files. The subject, the verb and the way in stay: a caller names a
  change and gets what the server knows about it.
- **`conflicts` is the paths the conflict message on the current patch set
  names.** It is empty where that patch set carries none and null where the tool
  read no log. The patch set the message is about is `_revision_number`, and the
  message held against it is the whole of the field. A conflict on a superseded
  patch set is history, and 7 of the 8 open changes with one are that.
- It comes off "The following files contain Git conflicts:" rather than off the
  cherry-pick line. A rebase writes the same list and is 13 of the 39 messages
  on record.
- **`cherryPickOf` names the change and the patch set this one was cherry-picked
  from**, read off the two fields the payload carries unasked. It is provenance
  and not a caveat: 133 of 400 recent merged changes carry it and 17 of those
  ever conflicted.
- `Gerrit::named()` asks `o=MESSAGES` on every call, and `messages` still
  decides what the caller gets. `D-ANS-079` made the log opt-in because it is 50
  KB in a caller's context. Nothing there weighed a fact about the change's own
  integrity filed in the same place. What moves out of the log is one fact
  rather than the log.
- Only a change read by name. A search answers up to 25 changes and the
  enumeration reads up to 2000. So neither asks for the option and both answer
  `conflicts` null, the silence a search keeps everywhere else.
- The text half says it beside the status line, because that is where a reader
  decides whether to read the change at all. It says which of the two it is: the
  patch set is broken rather than unreviewed.
- **The third suggestion fails.** The "Outdated Votes" line lifted into a
  per-label note is the case `D-ANS-079` already decided and already delivers.
  The log carries why a vote is gone, and the answer names it wherever it read a
  label under the default. That is a pointer a caller can act on rather than a
  fact a default hides.

## Assumed

- That a reviewer wants the conflict fact more than it wants the 6.3 KB of
  latency back. The option rides on this server's fetch rather than in the
  caller's context. A read of 50 changes took 0.36 seconds with it against 1.03
  without on the same day, which is noise rather than a measurement.
- That the change message is the only place that carries the fact. Read against
  `ChangeInfo` and `RevisionInfo` on 2026-08-27. A Gerrit that starts to persist
  `contains_git_conflicts` would make the parse unnecessary rather than wrong.
- That a patch set with markers is worth a statement whatever came of it. All 17
  recently merged ones had a repair before the merge, so the field warns about a
  window rather than about a defect that landed.

## Wrong if

- A change answer reports conflicts on a patch set that carries none, which is
  the message read without the patch set it names.
- Gerrit rewords the sentence and the field goes quietly empty. Nothing here
  reads that wording from the server, so only a reviewer who saw the markers in
  a diff would notice.
- Callers read `cherryPickOf` as the caveat and pass over ordinary backports.
  133 of 400 recent merged changes carry it and it says nothing about them.
- The log requested on every change read by name shows up as a latency somebody
  reports. That would say the fact belongs behind a second call made only where
  the payload already hints at one.
- The core no longer cherry-picks and rebases through the web UI, and the field
  is null across a whole release. Then the answer grew a field for a practice
  that ended.
