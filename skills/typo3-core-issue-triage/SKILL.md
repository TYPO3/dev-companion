---
name: typo3-core-issue-triage
description: 'Find the issues worth working on in an area of the forge.typo3.org backlog — old or untouched ones, and whether anybody is on one already — or say what is still true about one issue: whether it still happens against the core checkout, was fixed, or was never a defect. A task that ends in a patch starts here; the patch is typo3-core-patch-development''s.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Core Issue Triage

Take one open issue and say what is still true about it. Or take an area of the
backlog and hand back the candidates worth work. Keep this skill as routing and
working order. The tracker, the review server and the checkout's own commands
are lookups. A copy of what they answer goes stale here, and nothing reports it.

Triage is not a smaller version of the patch. What it produces is a statement
somebody can act on. This still happens, this is gone, this was never a defect,
nobody can settle this without X. The outcome that ends the work early is the
valuable one.

## Find the candidates

1. Work through [references/base.md](references/base.md), which fixes the order
   every task here starts in. It establishes the checkout you stand in, which
   the verification below runs against.
2. `typo3_forge_lookup` with `backlog` to get it rather than one issue. `oldest`
   and `stale` are two different questions, and the second is usually the one
   the user asks. Filed long ago is about the report. Untouched for years is
   about the attention it got. Somebody works on an issue filed in 2009 with a
   comment from last month.

   Narrow with `category` in the user's own words, and with `tracker`. An old
   Bug and an old Feature are two different findings. One claims something is
   broken today, the other that somebody wanted something once.

   Read the count that comes back against the number of entries. A page is not
   the set. A triage that takes thirty of two thousand for the problem has
   measured the limit rather than the backlog.

**Nothing in that list is a finding.** Age makes an issue a candidate and says
nothing about whether it is right. A report from 2011 can describe behaviour the
branch still has. One from last year can be about code that no longer exists.
The rest of this order separates them.

**The list is the first deliverable, and the choice from it is not yours.**
Triaging a backlog and triaging an issue are two different jobs. The step below
is the second one: it takes a number. Hand the backlog over first, one row per
candidate with what the user chooses on. That is the number, the area, the
subject, and how long it has sat untouched. It is whether the code its text
names is still installed here.

Let whoever asked pick. A session that picks for itself reports on four issues
out of thirty-nine. It has silently answered a question nobody asked. Where the
request really was "just find me something", say which rows you would take and
why. Let that be the choice.

**Where you do pick, pick on where the symptom is visible and on how much the
checkout already models it.** Age is not it, and neither is the subject matter.
Read in this order and stop at the first that decides:

- **What has already happened to it.** A change on the review server is the
  cheapest description of what a fix looks like. One somebody abandoned is a
  verdict somebody wrote down. A relation to an epic, or to an accepted parent,
  says the report is one strand of a larger piece. That piece's decision is not
  a session's to take.
- **Whether the code it names is still there.** Every row carries the classes,
  methods and core files its own text cites. Each comes with where it stands in
  the packages installed here. So a report whose names are all gone settles
  without one open file. A name the answer could not place decides nothing, and
  neither does one that stands. A class that is still there is a candidate to
  read rather than a defect that still reproduces.
- **The category, against the branch you stand on.** One that names a subsystem
  the branch no longer ships settles the issue before you read the report. The
  tracker keeps a category long after the code goes. Most of an old backlog
  still names subsystems that are there.
- **Where the symptom appears.** A rendered fragment, a stored row, a resolved
  value: you reach anything a process produces in the cheap layers. Those need
  neither an installation nor a browser. One that appears only after an
  interaction in the backend needs both, and that is most of the session.
- **How far the mechanism reaches.** A report that names one class and the
  behaviour in it is the shape you can settle. One that names several and the
  order between them has already said it is an interaction. A reporter who
  worked that out is usually right.
- **What the suite already models.** Look for a test over the class the report
  is about, at the level the symptom appears at. A case added to a file that
  exists is a reproduction with no fixture to build. The level is the whole of
  it. A component tested on its own cannot see an order between components. The
  core models more constellations than a category suggests.

Say which of those decided. Say of the rows you passed over that you passed over
them. A skip is not a triage, and the list is still what the user asked for.

## Establish what the issue claims

3. `typo3_forge_lookup` with the number. Read what comes back as a report rather
   than as a specification. Three parts of it are not in the description a
   session otherwise starts from. Those are the **status and target version as
   they stand today**, the **relations**, and the **notes**. The relations are
   one hop from the change that introduced the behaviour. The notes are where a
   maintainer said why.

   The assignee is the fourth. On an old issue it usually names who last touched
   it rather than who is on it. An assignee is not evidence that anybody works
   on it, and an unassigned issue is not evidence that nobody minds.

Separate the three claims the report mixes before you verify any of them. Those
are what the reporter saw, what they believed caused it, and what they wanted
instead. The first is the only one a checkout can settle. A report is regularly
right about the symptom and wrong about the cause. To verify the cause and
report the issue as invalid is the most common way this work goes wrong.

Where the issue quotes a rule, verify it in the checkout. A rule is "do not use
an API this way" or "this is not supported". Do not carry it at the strength the
reporter put on it. Enforced in code, warned about in a docblock and advised in
prose are three different claims. The reporter's word for all three is the same.

## Ask what happened since the report

4. `typo3_gerrit_lookup` with the issue number, **before you open the
   checkout**. Its cheapest outcome is the one that ends the work. Somebody has
   a patch up, and the triage is that it is under review rather than
   unaddressed. An answer of nothing is a result, and a narrow one. The server
   reads the review server without a credential. So nothing public names the
   issue, which is not that nobody fixed it.
5. `typo3_changelog_lookup` with the words the report uses. It says whether the
   core deprecated, removed or reworked the area since the report. A rework
   turns a valid report into one about code that is gone. It also makes the
   reproduction below fail for a reason that has nothing to do with the defect.

A changelog records change events. So an area nobody has touched has no entry at
all. An empty answer is not evidence that the behaviour stayed the same.

## Verify against the checkout you stand in

Reproduce against what the branch does today, never against the version in the
report. Half of what an old issue describes is usually gone, and the half that
remains is the finding.

Establish the code path first. Find the class the report is about and read
whether the behaviour it describes is still there. That separates "still
happens" from "cannot happen any more, the method is gone". The second is a
verdict that needs no reproduction at all.

**Before you write a test, look for the one the core already wrote and switched
off.** Where people knew a defect and nobody fixed it, the suite regularly
carries it as a commented-out data-provider row. The reason stands beside it.
The fixture under it already models the constellation.

`grep -rn "@todo" <sysext>/Tests` narrowed to the subsystem the report is about
is the whole of the search. The reason text says whether a hit is this report.
"Fails, not expanded to sub-pages" is one, and "wrong assertion" is a note to
whoever wrote the test. There are few of them and they are worth the one grep.
To remove a comment is a reproduction with no fixture to build and no harness to
prove.

`markTestSkipped` is a different thing and rarely this one. Most of them are
about the machine: no APCu, no Redis, no ImageMagick, a case-sensitive
filesystem. A test skipped for the environment says nothing about the report.

`typo3_test_run_guide` with the paths you have just read says which suites can
fail on them. It says whether a test can pin the behaviour at all. Where it can,
a failing test is the strongest thing a triage produces. It survives a handover
to somebody else, and it is the patch's first half already written. Where no
layer can hold it — backend markup, a build step, shipped JavaScript — say so.
Reproduce by hand instead, and write down the steps and what you saw.

That test is a throwaway until a patch adopts it, and it has three rules of its
own. It goes where the suite already looks. It mirrors the path of the class it
is about, because a file the runner does not collect proves nothing.

**Watch it fail before you believe it.** A reproduction that is green on its
first run tests nothing until you have shown it red. A first run may fail for a
reason that is not the issue. That is a field the type does not show, or a
fixture nobody loaded. That is a result about your harness and not about the
report. And it comes out again when the triage ends, unless the work carries on
into the patch that keeps it.

**The core's suites are not the ones a manifest here declares. You do not run
them the way you run an extension's.** They belong to the core's own runner,
which no `composer` script names. `typo3_test_run_guide` gives the targeted
invocation for the paths in hand rather than a suite name to guess at.

`typo3_script_lookup` is the rest of what that runner offers. That is the
options that decide which PHP and which database a suite runs against. An old
report turns on exactly that when it says the behaviour depends on either.

Where the symptom is in the rendered output, the throwaway has to produce it
before it can assert anything. The value is the unknown rather than the
expectation. `typo3_rule_lookup` with
`documentId="core/testing/proving-a-rendering"` is that harness. It says how the
snippet goes into TypoScript and which operator forms silently do something
else. It says how you print the rendered HTML at all.

Two of those decide whether a reproduction means anything. The step the base
opens with already answered both:

- **Where the checkout has a DDEV project, the suites and the console run inside
  it.** The same command in your own shell runs on whatever PHP the machine
  carries and against whatever database it has. That reproduces something else
  and looks identical in the output. `typo3_project_describe` says whether this
  checkout is one of those and what the form is. Take it from there rather than
  from what worked in another repository.
- **A green that ran over no files is not a green.** Where a suite reports
  success, confirm it inspected something — the count of tests or files it
  names. Do that before you read it as the behaviour being gone. That is the
  failure mode a triage is most exposed to. "The suite passes" is the evidence
  it is about to write a verdict on.
- **Once you commit the change, `git stash` measures nothing.** The same failure
  in a second costume. The stash finds nothing to save on a clean tree. The run
  that follows is the patched code, and the result reads as a without-patch
  measurement.

  Compare against the parent instead: a worktree on `HEAD~1`, or
  `git revert --no-commit` with a restore after. Confirm that the tree changed
  before you believe the run. `git stash list` that names nothing new should
  stop you.

An old report frequently names the versions the reporter saw it on. Those are
what the reporter had, not what it still reproduces on. The version the suites
run against here is a property of this checkout. Say which one the verification
used. A verdict that names no version and no branch is one nobody can repeat.

**A reproduction that fails to reproduce is a result and not a dead end.** Say
which of the three it is. The behaviour is gone, the steps were insufficient, or
the report never contained enough to try. They lead to opposite outcomes. They
look identical in a session that only writes down "could not reproduce".

## Where the finding is a vulnerability

**Ask it of every finding before you write the verdict, not when one happens to
look alarming.** A triage produces what makes up a vulnerability report: a
step-by-step reproduction against a branch people run. It produces it for the
tracker. Nothing else in this order asks the question. So the step that was
meant to report the finding would disclose it.

The stopping point is the verified reproduction. It stands, and you take no
public step. Nothing about the finding goes into the issue, onto the review
server or into a chat. Not the reproduction, not the failing test, not the
verdict.

Where it goes instead is `typo3_rule_lookup` with
`documentId="any/security/reporting-a-vulnerability"`. That is the whole
procedure, and it also stands as
`typo3://guides/any/security/reporting-a-vulnerability`. Read the address there
and never from here. A contact route is the fact that moves, and this file is a
copy no release of this server corrects.

Hand over what that report needs: the branch, the code path, the reproduction
and the version it ran on. Say that you withhold the ordinary verdict and why.
The user files it. This workflow supplies what the report rests on and takes no
step of its own.

## Say what the triage found

[references/checklist.md](references/checklist.md) carries the verdicts, what
evidence each one owes, and the questions that decide between them. Read it
before you write the answer rather than after. The verdicts are not degrees of
confidence in one finding. The one you pick first decides what you still have to
establish.

Report what you did not establish beside what you did. A triage whose reading
stopped at the code path says so. The next person's work is exactly the part you
left.

**The verdict is markdown the reader can copy, and the answer is where it
goes.** You write it for the person who will act on it, and rendered output does
not survive the move. Write it to a file only where the caller asks for one, at
a path outside the checkout. This workflow leaves that tree as it found it.

**A verdict that ends the issue carries the comment that closes it.** Three of
them do. The checklist says which, what that comment holds and which markup the
tracker renders. Forge is not markdown. The comment stands in the answer beside
the verdict it rests on. Filing it is the maintainer's act. This workflow holds
no credential and comments on nothing.

## What a previous attempt cost

Where somebody fixed the issue once and reverted the fix, the verdict is not the
answer people wait for. "Still happens" and "somebody tried and reverted it" are
the same verdict and opposite propositions. What separates them is why they
reverted it and whether that reason still holds.

The trigger is in the issue answer rather than in the reading. A relation marked
`precedes` or `duplicates` carries its subject, and `reviews` names every change
the journal mentions. So an issue whose history is a merged-then-reverted fix
says so before you open the checkout.

- Read the related issue somebody filed the revert under. The reason lives there
  and nowhere else. The commit that reverts says what it reverted, and the issue
  says what it cost.
- Read the attempt itself, which is the one thing no lookup here returns. The
  issue answer carries the change numbers, and the Gerrit search by issue number
  their state. So what remains is the diff: fetch the patch set into the
  checkout and read it. The ref it is under and the remote it is on are two
  sections of one page. That remote is not the one a core checkout fetches from.
  So where the fetch is the task, read it whole: `typo3_rule_lookup` with
  `documentId="core/contribution/gerrit-workflow"`, which also stands as
  `typo3://guides/core/contribution/gerrit-workflow`.

  Abandoned is a verdict somebody wrote down. The diff under it is the cheapest
  description of what a fix looks like against a modern core. Read the patch
  set; put it onto no branch. To rebase an abandoned attempt is to write the
  patch rather than judge the issue.

  `typo3_gerrit_lookup` with a change number earns a call of its own for a
  `reviews` entry the issue search left out. That search finds a change whose
  commit message names the issue. So an entry missing from it is the one whose
  branch, patch set and status nothing else has stated.
- Find every production caller of the method the reverted patch touched, in the
  checkout. A fix scoped to one call site is a different proposition from one
  that changes a shared path.
- Establish whether the path the revert names still routes through that method.
  A subsystem rebuilt since is what turns the old objection into history.

**A reverted core fix becomes re-attemptable when somebody has rebuilt the
shared consumer that made it expensive. It also does when the caller set has
shrunk to the one site the fix needs.** That is the form that transfers. Neither
half is readable off the issue, and both are one grep and one file in the
checkout.

What comes out of this is what a maintainer needs before the issue can move.
That is what the answer owes. It is not a design and not a patch. To name the
constraint the last attempt broke is the deliverable. To propose the code that
respects it is the next workflow's.

## Where the triage ends and the patch begins

**When it still happens and the user asks for the fix, invoke
`typo3-core-patch-development` before making the change.** That is a step, not a
note about ownership. Load the skill by name and work from it. What crosses over
is the issue number, the verdict, the established code path and any failing
test.

It stands as a step because it did not fire as anything else. A session read
this paragraph and held exactly that handoff. The user asked for the patch, and
the session wrote it over forty more turns without the skill. It decided for
itself the changelog obligation, the suites and databases to run, the commit
trailers and the release branches. Nothing it decided was wrong in the report;
it reconstructed all of it.

This skill owns the statement of what is still true about an issue. That is the
choice out of the backlog and the report read against the branch. It is the
reproduction or the failure to reproduce, and the verdict that comes out.

It stops at the tracker. Nothing here comments, assigns, closes or reopens
anything. You write the verdict, with the comment that closes the issue, for the
person who will. The judgement of a patch somebody pushed is
`typo3-core-patch-review`, which reads the diff rather than the report.
