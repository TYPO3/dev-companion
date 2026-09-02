---
name: typo3-core-issue-triage
description: 'Find the issues worth working on in an area of the forge.typo3.org backlog — old or untouched ones, and whether anybody is on one already — or say what is still true about one issue: whether it still happens against the core checkout, was fixed, or was never a defect. A task that ends in a patch starts here; the patch is typo3-core-patch-development''s.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Core Issue Triage

Take one open issue and say what is still true about it, or take an area of the
backlog and hand back the candidates worth working on. Keep this skill as
routing and working order; the tracker, the review server and the checkout's own
commands are lookups, and a copy of what they answer goes stale here with
nothing to report it.

Triage is not a smaller version of writing the patch. What it produces is a
statement somebody can act on — this still happens, this is gone, this was never
a defect, this cannot be settled without X — and the outcome that ends the work
early is the valuable one.

## Find the candidates

1. Work through [references/base.md](references/base.md), which fixes the order
   every task here starts in. It establishes the checkout you are standing in,
   which is what the verification below is against.
2. `typo3_forge_lookup` with `backlog` to get it rather than one issue. `oldest`
   and `stale` are two different questions and the second is usually the one
   being asked: filed long ago is about the report, untouched for years is about
   the attention it got, and an issue filed in 2009 with a comment from last
   month is being worked by somebody. Narrow with `category` in the user's own
   words, and with `tracker`, because an old Bug and an old Feature are two
   different findings — one claims something is broken today, the other that
   something was wanted once.

   Read the count that comes back against the number of entries. A page is not
   the set, and a triage that takes thirty of two thousand for the problem has
   measured the limit rather than the backlog.

**Nothing in that list is a finding.** Age is what makes an issue a candidate
and says nothing about whether it is right; a report from 2011 can describe
behaviour the branch still has, and one from last year can be about code that no
longer exists. What separates them is the rest of this order.

**The list is the first deliverable, and choosing from it is not yours.**
Triaging a backlog and triaging an issue are two different jobs, and the step
below is the second one: it takes a number. Hand the backlog over first, one row
per candidate carrying what it has to be chosen on — the number, the area, the
subject, how long it has been untouched, and whether the code its text names is
still installed here — and let whoever asked pick. A session that picks for
itself reports on four issues out of thirty-nine and has silently answered a
question it was never asked; where the request really was "just find me
something", say which rows you would take and why, and let that be the choice.

**Where you do pick, pick on where the symptom is visible and on how much the
checkout already models it.** Age is not it, and neither is the subject matter.
Read in this order and stop at the first that decides:

- **What has already happened to it.** A change on the review server is the
  cheapest description of what a fix looks like, and one that was abandoned is a
  verdict somebody wrote down. A relation to an epic, or to a parent somebody
  accepted, says the report is one strand of a larger piece whose decision is
  not a session's to take.
- **Whether the code it names is still there.** Every row carries the classes,
  methods and core files its own text cites, each with where it stands in the
  packages installed here, so a report whose names are all gone is settled
  without a file being opened. A name the answer could not place decides
  nothing, and neither does one that stands: a class that is still there is a
  candidate to read rather than a defect that still reproduces.
- **The category, against the branch you are standing on.** One naming a
  subsystem the branch no longer ships settles the issue before the report is
  read. The tracker keeps a category long after the code goes, and most of an
  old backlog still names subsystems that are there.
- **Where the symptom appears.** A rendered fragment, a stored row, a resolved
  value — anything a process produces — is reachable in the layers that need
  neither an installation nor a browser. One that appears only after an
  interaction in the backend needs both standing up first, and that is most of
  the session.
- **How far the mechanism reaches.** A report naming one class and the behaviour
  in it is the settleable shape. One naming several and the order between them
  has already said it is an interaction, and a reporter who worked that out is
  usually right.
- **What the suite already models.** Look for a test over the class the report
  is about, at the level the symptom appears at: a case added to a file that
  exists is a reproduction with no fixture to build. The level is the whole of
  it — a component tested on its own cannot see an order between components —
  and the core models more constellations than a category suggests.

Say which of those decided, and say of the rows you passed over that you passed
over them. Skipping is not triaging, and the list is still what was asked for.

## Establish what the issue claims

3. `typo3_forge_lookup` with the number, and read what comes back as a report
   rather than as a specification. Three parts of it are not in the description
   a session otherwise starts from: the **status and target version as they
   stand today**, the **relations**, which are one hop from the change that
   introduced the behaviour, and the **notes**, where a maintainer said why. Who
   it is assigned to is the fourth, and on an old issue it usually names who
   last touched it rather than who is on it — an assignee is not evidence that
   anybody is working on it, and an unassigned issue is not evidence that nobody
   minds.

Separate the three claims the report mixes before verifying any of them: what
the reporter saw, what they believed caused it, and what they wanted instead.
The first is the only one a checkout can settle. A report is regularly right
about the symptom and wrong about the cause, and verifying the cause and
reporting the issue as invalid is the most common way this work goes wrong.

Where the issue quotes a rule — an API may not be used this way, this is not
supported — verify it in the checkout rather than carrying it at the strength
the reporter put on it. Enforced in code, warned about in a docblock and advised
in prose are three different claims, and the reporter's word for all three is
the same.

## Ask what happened since it was filed

4. `typo3_gerrit_lookup` with the issue number, **before opening the checkout**.
   Its cheapest outcome is the one that ends the work: somebody has a patch up,
   and the triage is that it is under review rather than unaddressed. An answer
   of nothing is a result and a narrow one — the review server is read without a
   credential, so nothing public names the issue, which is not that nobody fixed
   it.
5. `typo3_changelog_lookup` with the words the report uses, for whether the area
   was deprecated, removed or reworked since it was filed. A rework is what
   turns a valid report into one about code that is gone, and it is also what
   makes the reproduction below fail for a reason that has nothing to do with
   the defect.

A changelog records change events, so an area nobody has touched has no entry at
all. An empty answer is not evidence that the behaviour is unchanged.

## Verify against the checkout you are standing in

Reproduce against what the branch does today, never against the version in the
report. Half of what an old issue describes is usually gone, and the half that
remains is the finding.

Establish the code path first: find the class the report is about and read
whether the behaviour it describes is still written there. That is what
separates "still happens" from "cannot happen any more, the method is gone", and
the second is a verdict that needs no reproduction at all.

**Before writing a test, look for the one the core already wrote and switched
off.** Where a defect was known and nobody fixed it, the suite regularly carries
it as a commented-out data-provider row with the reason beside it, over a
fixture that already models the constellation. `grep -rn "@todo" <sysext>/Tests`
narrowed to the subsystem the report is about is the whole of the search, and
the reason text is what says whether a hit is this report — "Fails, not expanded
to sub-pages" is one, and "wrong assertion" is a note to whoever wrote the test.
There are few of them and they are worth the one grep: removing a comment is a
reproduction with no fixture to build and no harness to prove.

`markTestSkipped` is a different thing and rarely this one. Most of them are
about the machine — no APCu, no Redis, no ImageMagick, a case-sensitive
filesystem — and a test skipped for the environment says nothing about the
report.

`typo3_test_run_guide` with the paths you have just read says which suites can
fail on them, and whether the behaviour can be pinned by a test at all. Where it
can, a failing test is the strongest thing a triage produces: it survives being
handed to somebody else, and it is the patch's first half already written. Where
no layer can hold it — backend markup, a build step, shipped JavaScript — say so
and reproduce by hand instead, writing down the steps and what you saw.

That test is a throwaway until a patch adopts it, and it has three rules of its
own. It goes where the suite already looks, mirroring the path of the class it
is about, because a file the runner does not collect proves nothing. **It has to
be seen failing before it is believed**: a reproduction that is green on its
first run is testing nothing until you have shown it red, and the first run
failing for a reason that is not the issue — a field the type does not show, a
fixture that was never loaded — is a result about your harness and not about the
report. And it comes out again when the triage ends, unless the work carries
straight on into the patch that keeps it.

**The core's suites are not the ones a manifest here declares, and they are not
run the way an extension's are.** They belong to the core's own runner, which no
`composer` script names, and `typo3_test_run_guide` is what gives the targeted
invocation for the paths in hand rather than a suite name to guess at.
`typo3_script_lookup` is the rest of what that runner offers — the options that
decide which PHP and which database a suite runs against, which is exactly what
an old report turns on when it says the behaviour depends on either.

Where the symptom is rendered output, the throwaway has to produce it before it
can assert anything, and the value is the unknown rather than the expectation.
`typo3_rule_lookup` with `documentId="core/testing/proving-a-rendering"` is that
harness: the snippet into TypoScript, the operator forms that silently do
something else instead, and how the rendered HTML is printed at all.

Two of those decide whether a reproduction means anything, and both were already
answered by the step the base opens with:

- **Where the checkout has a DDEV project, the suites and the console run inside
  it.** The same command in your own shell runs on whatever PHP the machine
  carries and against whatever database it has, which reproduces something else
  and looks identical in the output. `typo3_project_describe` says whether this
  checkout is one of those and what the form is; take it from there rather than
  from what worked in another repository.
- **A green that ran over no files is not a green.** Where a suite reports
  success, confirm it inspected something — the count of tests or files it names
  — before reading it as the behaviour being gone. That is the failure mode a
  triage is most exposed to, because "the suite passes" is the evidence it is
  about to write a verdict on.
- **Once the change is committed, `git stash` measures nothing.** The same
  failure in a second costume: the stash finds nothing to save on a clean tree,
  the run that follows is the patched code, and the result reads as a
  without-patch measurement. Compare against the parent instead — a worktree on
  `HEAD~1`, or `git revert --no-commit` with a restore after — and confirm the
  tree actually changed before believing the run. `git stash list` naming
  nothing new is what should stop you.

An old report frequently names the versions it was seen on. Those are what the
reporter had, not what it still reproduces on, and the version the suites run
against here is a property of this checkout. Say which one the verification
used; a verdict that names no version and no branch cannot be repeated.

**A reproduction that fails to reproduce is a result and not a dead end.** Say
which of the three it is: the behaviour is gone, the steps were insufficient, or
the report never contained enough to try. They lead to opposite outcomes and
they look identical in a session that only writes down "could not reproduce".

## Where the finding is a vulnerability

**Ask it of every finding before the verdict is written, rather than when one
happens to look alarming.** A triage produces what a vulnerability report is
made of — a step-by-step reproduction against a branch people are running — and
it produces it for the tracker. Nothing else in this order asks the question, so
the finding would be disclosed by the step that was meant to report it.

The stopping point is the verified reproduction. It stands, and the public step
is not taken: nothing about the finding goes into the issue, onto the review
server or into a chat — not the reproduction, not the failing test, not the
verdict.

Where it goes instead is `typo3_rule_lookup` with
`documentId="any/security/reporting-a-vulnerability"`, which is the whole
procedure and also stands as
`typo3://guides/any/security/reporting-a-vulnerability`. The address is read
there and never from here: a contact route is the fact that moves, and this file
is a copy no release of this server corrects.

Hand over what that report needs — the branch, the code path, the reproduction
and the version it ran on — and say that the ordinary verdict is being withheld
and why. Filing it is the user's act: this workflow supplies what the report
rests on and takes no step of its own.

## Say what the triage found

[references/checklist.md](references/checklist.md) carries the verdicts, what
evidence each one owes, and the questions that decide between them. Read it
before writing the answer rather than after: the verdicts are not degrees of
confidence in one finding, and picking one first decides what still has to be
established.

Report what you did not establish beside what you did. A triage whose reading
stopped at the code path says so, because the next person's work is exactly the
part that was left.

**The verdict is markdown the reader can copy, and the answer is where it
goes.** It is written for the person who will act on it, and rendered output is
what does not survive being moved. Write it to a file only where the caller asks
for one, at a path outside the checkout the reproduction ran in, whose tree this
workflow leaves as it found it.

**A verdict that ends the issue carries the comment it is closed with.** Three
of them do, and the checklist says which, what that comment holds and which
markup the tracker renders — Forge is not markdown. The comment stands in the
answer beside the verdict it rests on. Filing it is the maintainer's act: this
workflow holds no credential and comments on nothing.

## What a previous attempt cost

Where the issue was fixed once and the fix taken back out, the verdict is not
the answer somebody is waiting for. "Still happens" and "was tried and reverted"
are the same verdict and opposite propositions, and what separates them is why
it was reverted and whether that reason still holds.

The trigger is in the issue answer rather than in the reading. A relation marked
`precedes` or `duplicates` carries its subject, and `reviews` names every change
the journal mentions — so an issue whose history is a merged-then-reverted fix
says so before the checkout is opened.

- Read the related issue the revert was filed under. The reason lives there and
  nowhere else: the reverting commit says what was reverted, and the issue says
  what it cost.
- Read the attempt itself, which is the one thing no lookup here returns. The
  issue answer carries the change numbers and the Gerrit search by issue number
  their state, so what is left is the diff: fetch the patch set into the
  checkout and read it. The ref it is under, and the remote it is on rather than
  the one a core checkout fetches from, are two sections of one page, so where
  the fetch is the task read it whole: `typo3_rule_lookup` with
  `documentId="core/contribution/gerrit-workflow"`, which also stands as
  `typo3://guides/core/contribution/gerrit-workflow`. Abandoned is a verdict
  somebody wrote down, and the diff under it is the cheapest description of what
  a fix looks like against a modern core. The patch set is read and goes onto no
  branch: rebasing an abandoned attempt is writing the patch rather than judging
  the issue.

  `typo3_gerrit_lookup` with a change number earns a call of its own for a
  `reviews` entry the search by issue number did not answer with. That search
  finds a change whose commit message names the issue, so an entry missing from
  it is the one whose branch, patch set and status nothing else has stated.
- Find every production caller of the method the reverted patch touched, in the
  checkout. A fix that scopes to one call site is a different proposition from
  one that changes a path everything else goes through.
- Establish whether the path named in the revert still routes through that
  method. A subsystem rebuilt since is what turns the old objection into
  history.

**A reverted core fix becomes re-attemptable when the shared consumer that made
it expensive has been rebuilt, or when the caller set has shrunk to the one site
the fix needs.** That is the form that transfers; neither half is readable off
the issue, and both are one grep and one file in the checkout.

What comes out of this is what a maintainer would need before the issue can
move, which is what the answer owes. It is not a design and not a patch: naming
the constraint the last attempt broke is the deliverable, and proposing the code
that respects it is the next workflow's.

## Where the triage ends and the patch begins

**When the verdict is that it still happens and you are asked to fix it, invoke
`typo3-core-patch-development` before making the change.** That is a step, not a
note about ownership: load the skill by name and work from it. What crosses over
is the issue number, the verdict, the code path that was established and the
failing test where there is one.

The reason it is written as a step is that it did not fire as anything else. A
session read this paragraph, held exactly that handoff, was asked for the patch,
and wrote it over forty more turns without ever opening the skill — deciding for
itself the changelog obligation, which suites to run and on which databases, the
commit trailers and the release branches. Nothing it decided was reported wrong;
all of it was reconstructed.

This skill owns saying what is still true about an issue: choosing it out of the
backlog, reading the report against the branch, reproducing it or failing to,
and the verdict that comes out. It stops at the tracker — nothing here comments,
assigns, closes or reopens anything, and the verdict, with the comment that
closes the issue, is written for the person who will. Judging a patch somebody
pushed is `typo3-core-patch-review`, which reads the diff rather than the
report.
