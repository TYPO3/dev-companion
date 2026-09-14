---
id: D-SKL-073
title: A verdict that ends an issue carries the comment that closes it
date: 2026-08-24
status: open
coveredBy:
  - SkillTest::aVerdictThatEndsTheIssueCarriesTheCommentThatClosesIt
---

# D-SKL-073 — A verdict that ends an issue carries the comment that closes it

**A triage whose verdict ends the issue hands over the comment that closes it,
and the tracker boundary stops the act rather than the wording.**

The skill stops one step earlier than that today. It says what a verdict owes as
evidence and what form the answer takes. The sentence closest to the moment
tells the session that the close is somebody else's. That reads as a stop before
the text as well as before the act.

## Evidence

- `feedback/2026-08-24-170208-triage-that-proves-an-issue-is-already-fixed.md`
  reports a triage that established an issue was already fixed and handed over
  no reason the reporter could close it with.
- The skill as it stands says it. The **Gone** verdict in
  `skills/typo3-core-issue-triage/references/checklist.md` owes "what changed,
  named" and nothing that goes to the reporter. The `Say what the triage found`
  section of `SKILL.md` names the form, markdown the reader can copy
  (`D-SKL-042`), and never the content for this case. Nothing in either file has
  changed since `4c1fe8fc` on 2026-08-19, so the feedback describes the file
  that is there now.
- The checklist's last bullet is what a session reads at that moment: "Whether
  anything here is a recommendation to close, reassign or reopen. That is the
  maintainer's act; the triage supplies what it rests on and stops." Two
  readings of it are defensible. The one that costs the deliverable is the one
  the session took.
- A second session reported the same absent deliverable from another task shape.
  `feedback/2026-08-24-173131-which-releases-contain-a-given-fix-took-four.md`
  got a mid-task ask for "the list of already-fixed issues with justification so
  they could close them on Forge". It assembled the release fact by hand four
  times, and got one of the four wrong until it read the `Releases:` trailer.
- `bin/cli hints:probe "a triage session on an open forge.typo3.org issue that turns out to be fixed already"`
  matched nothing. `knowledge/` names Forge in the Gerrit and sources documents
  alone. What a closing comment on the tracker owes is not written anywhere
  here.

## Decided

- The diagnosis is step 4 of the ladder, wording, in the triage skill's own two
  files. The verdict, the evidence it owes and the copyable form all arrived.
  What stopped the session was a sentence about the act, read as a sentence
  about the text.
- The verdicts that end the issue owe the deliverable: **Gone**, **Superseded**
  and **Not a defect**. The two that ask the reporter something instead do not.
  **A security defect** stays as it is. It owes the tracker nothing, and a
  closing comment is the public step that verdict exists to prevent.
- The boundary does not move. This server holds no credential, comments on
  nothing and closes nothing, and the person who files the comment is still the
  one who acts.
- The work goes to the queue rather than into the judgement run, on both of the
  tests `documentation/records/judging.rst` sets. It changes a skill's contract,
  which lands in somebody else's project, and it needs a fact about TYPO3 that
  is not in this repository.
- That fact is the todo's first step and is a step 1a of its own: what a closing
  comment on forge.typo3.org owes. That is which status and resolution close a
  fixed issue, how to name the change that fixed it, and which markup the
  tracker renders. Read from the core's own contribution documentation, not
  recalled.
- The card stays its own rather than folds into the one that serves
  `feedback/2026-08-24-173131-which-releases-contain-a-given-fix-took-four.md`.
  That one is a tool gap about a fact nothing answers in one call, and this one
  is the skill's deliverable. The wording will route to whatever answers the
  fact by the time somebody writes it.
- The priority is `normal`, set by two sessions that reported the same gap from
  two task shapes on one day. The maintainer filed this one for work now.

## Assumed

- The reporter pastes the wording as it stands. Where the tracker renders
  something other than markdown, the form is part of what the read has to settle
  rather than a detail of it.
- A maintainer wants the closing text supplied. The feedback says the reporter
  had to compose it, which is evidence that somebody wanted it once.

## Wrong if

- A triage under the rewritten skill hands over a closing a maintainer rewrites
  before the paste. That would make the deliverable the evidence and not the
  text.
- A maintainer reports a supplied closing as noise, on the ground that the close
  of an issue is the tracker's own voice.
- A session writes a closing for a verdict that should have asked the reporter a
  question. "not reproducible as written" written up as "gone" is the trap the
  checklist already names. A deliverable that only exists on the closing side is
  a reason to reach for it.

## Since then

The step 1a came from the tracker and the contribution guide rather than from
recall. The tracker carries ten statuses and no resolution field, and three of
them close. The guide ties two of them to a merged patch and a report that no
longer reproduces. It does not describe the third at all. The commit convention
is why a fixed bug is still open and owes the comment. The releases trailer
names the branches a change serves rather than the release that carries it.

The shape comes off three issues closed that day. One of them names no commit
and states what did not reproduce instead, the shape a verdict with no named
change hands over. The markup comes off the tracker rather than the guide, which
settles the form half of the first assumption.
