---
name: typo3-core-issue-triage
description: 'Find the issues worth working on in an area of the forge.typo3.org backlog or in one person''s — old or untouched ones, and whether anybody is on one — or say what is still true about one issue: whether it still happens in the checkout, was fixed, or was never a defect. A task that ends in a patch starts here; typo3-core-patch-development writes it.'
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Core Issue Triage

This skill covers what is still true about issues on forge.typo3.org. Use it to
find the issues in an area of the backlog that are worth work. Use it also to
say whether one issue still happens, was fixed, or was never a defect. It stops
before the fix, which `typo3-core-patch-development` covers. It also stops at
the tracker: nothing here comments on an issue, assigns, closes or reopens it.

**A triage produces a statement somebody can act on.** "This still happens",
"this is gone", "this was never a defect" or "nobody can settle this without X".
A result that ends the work early is the most valuable one.

Keep this skill as routing and stop rules: it says which tool to ask and when to
stop. The tracker, the review server and the checkout's own commands give the
facts. A copy of their answers here would go out of date, and nothing would
report it.

## Steps at a glance

1. Find the candidates in the backlog, and let the user choose.
2. Establish what the issue claims.
3. Ask what happened since the report.
4. Verify the claim against the checkout you work in.
5. Check whether the finding is a vulnerability.
6. Write the verdict.
7. Hand over to the patch when the user asks for the fix.

Before step 1, work through [references/base.md](references/base.md). It fixes
the order every task starts in. It also establishes the checkout that step 4
verifies against. Where the request names one issue, start at step 2.

## 1. Find the candidates

Call `typo3_forge_lookup` with `backlog` to get a list rather than one issue.
Then narrow it:

- **`oldest` or `stale`.** These answer two different questions, and users
  usually mean the second. `oldest` is about when somebody filed the report.
  `stale` is about when anybody last touched it. An issue filed in 2009 can have
  a comment from last month.

- **`category`**, in the user's own words.

- **`tracker`.** An old Bug and an old Feature are different findings. A Bug
  claims that something is broken today. A Feature says that somebody once
  wanted something.

- **`involving`**, where the request names a person rather than an area. It
  returns what the person filed and what is assigned to them, as one set. Never
  pass a name as `query`. That matches the text of an issue, not the people on
  it.

- **`words`**, where the request names a topic inside an area. They match the
  subject and the description inside the set the area selects.

- **`offset`**, to page through a set larger than 50.

Compare the count in the answer with the number of entries. A page is not the
whole set. A triage that takes thirty of two thousand for the backlog has
measured the page limit.

**Nothing in that list is a finding yet.** Age makes an issue a candidate. It
says nothing about whether the report is right. A report from 2011 can describe
behaviour the branch still has. A report from last year can be about code that
no longer exists. Steps 2 to 4 tell the two apart.

### Hand the list over

**The list is the first deliverable, and the user chooses from it.** Triaging a
backlog and triaging an issue are two different jobs. Step 2 starts the second
job, and it needs one issue number. Give one row per candidate, with what the
user chooses on:

- the number, the area and the subject;
- how long nobody has touched it;
- whether the code its text names is still installed here.

Let the user pick. A session that picks for itself reports on four issues out of
thirty-nine. It answers a question nobody asked. Where the request really was
"just find me something", say which rows you would take and why. Then let the
user choose.

### When you choose

**Choose by where the symptom is visible and by how much the checkout already
models it.** Age does not decide it, and the subject does not either. Read these
in order, and stop at the first that decides:

1. **What already happened to it.** A change on the review server is the
   cheapest description of a fix. An abandoned change is a verdict somebody
   wrote down. A relation to an epic or to an accepted parent makes the report
   one part of larger work. The decision about that larger work is not yours.

2. **Whether the code it names is still there.** Every row lists the classes,
   methods and core files its text cites. Each comes with where it is in the
   packages installed here. A report whose names are all gone is settled without
   one open file. A name the answer could not find decides nothing. A name that
   is still there decides nothing either. Its class is a candidate to read, not
   a defect that still reproduces.

3. **The category, against your branch.** A category that names a subsystem the
   branch no longer ships settles the issue before you read the report. The
   tracker keeps a category long after the code is gone. But most of an old
   backlog names subsystems that are still there.

4. **Where the symptom appears.** You reach a rendered fragment, a stored row or
   a resolved value without an installation or a browser. A symptom that appears
   only after a click in the backend needs both. That takes most of the session.

5. **How far the mechanism reaches.** A report that names one class and its
   behaviour is one you can settle. A report that names several classes and the
   order between them describes an interaction. A reporter who worked that out
   is usually right.

6. **What the suite already models.** Look for a test of the class the report is
   about, at the level where the symptom appears. A case added to an existing
   file is a reproduction with no fixture to build. The level decides it. A test
   of one component alone cannot see the order between components. The core
   models more combinations than a category suggests.

Say which of those decided. Say of the rows you passed over that you passed over
them. A skipped row is not a triaged row, and the list is still what the user
asked for.

**End the list with each step of the base order you did not reach.** Give each
one a line: `Not reached: <step> — <why>`. A list of the backlog changes no
code, so the deprecation sweep is one of them.

## 2. Establish what the issue claims

Call `typo3_forge_lookup` with the issue number. Read the answer as a report,
not as a specification. Four parts of it are not in the description:

- **The status and the target version**, as they are today.

- **The relations.** They often lead directly to the change that introduced the
  behaviour.

- **The notes.** A maintainer wrote the reasons there.

- **The assignee.** On an old issue it usually names who last touched it, not
  who works on it. An assignee is not evidence that anybody works on it. An
  issue without one is not evidence that nobody cares.

Before you verify anything, separate the three claims a report mixes:

- what the reporter saw;
- what they believed caused it;
- what they wanted instead.

A checkout can settle only the first. A report is often right about the symptom
and wrong about the cause. This work most often fails like this: you disprove
the cause and report the whole issue as invalid.

The report can quote a rule, such as "do not use an API this way" or "this is
not supported". Verify that rule in the checkout. Do not repeat it at the
strength the reporter gave it. Code can enforce a rule, a docblock can warn
about it, or prose can advise it. Those are three different claims, and a
reporter uses the same words for all three.

## 3. Ask what happened since the report

Make these two calls before you open the checkout:

1. **`typo3_gerrit_lookup` with the issue number.** Its cheapest result ends the
   work: somebody has a patch up. Then the triage is that the issue is under
   review. An empty answer is a result, but a narrow one. The server reads the
   review server without a login. So it shows only that nothing public names the
   issue, not that nobody fixed it.

2. **`typo3_changelog_lookup` with the words the report uses.** It says whether
   the core deprecated, removed or reworked the area since the report. After a
   rework, a valid report can be about code that is gone. A rework also makes a
   reproduction fail for a reason unrelated to the defect.

A changelog records changes, so an area nobody changed has no entry. An empty
answer is not evidence that the behaviour stayed the same.

**When the history shows a fix that somebody reverted, read
[references/previous-attempt.md](references/previous-attempt.md).** The issue
answer shows that history before you open the checkout. A relation marked
`precedes` or `duplicates` comes with its subject. `reviews` names every change
the journal mentions.

## 4. Verify against the checkout you work in

Reproduce against what the branch does today, never against the version in the
report. Much of what an old issue describes is usually gone. The part that
remains is the finding.

### Find the code path

Find the class the report is about. Read whether the behaviour it describes is
still there. That separates "still happens" from "cannot happen any more, the
method is gone". The second is a verdict that needs no reproduction.

### Look for a test the core switched off

**Before you write a test, look for the one the core already wrote and switched
off.** Where a defect was known and not fixed, the suite often keeps it as a
data-provider row in a comment. The reason is in a comment beside it. The
fixture under it already models the situation.

Run `grep -rn "@todo" <sysext>/Tests` for the subsystem the report is about.
That is the whole search. The reason text says whether a hit is this report.
"Fails, not expanded to sub-pages" is one. "Wrong assertion" is a note to
whoever wrote the test. There are few hits, and they are worth the one grep.
Where you find one, remove the comment. That is a reproduction with no fixture
to build.

`markTestSkipped` is a different thing and rarely this one. Most skips are about
the machine: no APCu, no Redis, no ImageMagick, a case-sensitive file system. A
test skipped for the environment says nothing about the report.

### Write a test that fails

Call `typo3_test_run_guide` with the paths you read. It names the suites that
can fail on them, and whether a test can pin the behaviour at all. Where it can,
a failing test is the strongest result of a triage. It stays valid when somebody
else takes over, and it is the first half of the patch.

Where no test layer can hold the behaviour, say so. Backend markup, a build step
and shipped JavaScript are such cases. Then reproduce by hand, and write down
the steps and what you saw.

That test is a throwaway until a patch adopts it. Three rules apply to it:

- **Put it where the suite already looks.** Mirror the path of the class it
  tests. The runner does not collect a file anywhere else, and a test it never
  runs proves nothing.

- **Watch it fail before you believe it.** A reproduction that passes on its
  first run tests nothing until you have seen it fail. A first run can also fail
  for a reason that is not the issue. A field the type does not show and a
  fixture nobody loaded are examples. That is a result about your test, not
  about the report.

- **Remove it when the triage ends**, unless the work goes on into the patch
  that keeps it.

### Run the core's suites

**The core's suites run through the core's own runner.** No `composer` script
names them, unlike an extension's. `typo3_test_run_guide` gives the targeted
command for the paths in hand. Do not guess a suite name.

`typo3_script_lookup` gives the other options of that runner. They include which
PHP and which database a suite runs against. An old report often depends on
exactly that.

Where the symptom is in the rendered output, the test has to produce that output
before it can assert anything. The output is the unknown, not the expected
value. Call `typo3_rule_lookup` with
`documentId="core/testing/proving-a-rendering"`. It says how the snippet goes
into TypoScript, and which operator forms silently do something else. It also
says how to print the rendered HTML.

Check three things before you believe a run. The base order answered the first
two at its start:

- **Where the checkout has a DDEV project, run the suites and the console inside
  it.** The same command in your own shell uses the PHP and the database of your
  machine. It reproduces something else, and the output looks the same.
  `typo3_project_describe` says whether this checkout has one and what the
  command is. Take it from there, not from what worked in another repository.

- **A green that ran over no files is not a green.** Where a suite reports
  success, confirm it inspected something — the count of tests or files it
  names. Do that before you read it as proof that the behaviour is gone. A
  triage is most exposed to this failure. Its verdict is about to rest on "the
  suite passes".

- **Once you commit the change, `git stash` measures nothing.** On a clean tree
  the stash saves nothing. The next run tests the patched code, but it reads as
  a measurement without the patch.

  Compare against the parent commit instead: a worktree on `HEAD~1`, or
  `git revert --no-commit` and a restore after it. Confirm that the tree changed
  before you believe the run. Stop when `git stash list` names nothing new.

### Name the version

An old report often names the versions the reporter saw it on. Those are what
the reporter had, not where it still reproduces. The version the suites run
against is a property of this checkout. Say which version and which branch the
verification used. Nobody can repeat a verdict that names neither.

**A reproduction that fails is a result, not a dead end.** Say which of three it
is:

- the behaviour is gone;
- your steps were not enough;
- the report never had enough to try.

They lead to opposite outcomes. In a report that only says "could not
reproduce", they look the same.

## 5. Where the finding is a vulnerability

**Ask this of every finding before you write the verdict, not only when one
happens to look alarming.** A triage produces what a vulnerability report
consists of: a step-by-step reproduction against a branch people run. And it
produces it for a public tracker. No other step asks the question. Without this
step, the report of the finding would disclose it.

**The stopping point is the verified reproduction.** Keep it, and take no public
step. Put nothing about the finding into the issue, onto the review server or
into a chat. That includes the reproduction, the failing test and the verdict.

Instead, call `typo3_rule_lookup` with
`documentId="any/security/reporting-a-vulnerability"`. That is the whole
procedure. It is also served as
`typo3://guides/any/security/reporting-a-vulnerability`. Read the contact
address there, never from this file. A contact address can change, and no
release of this server corrects a copy of this skill.

Hand over what the report needs: the branch, the code path, the reproduction and
the version it ran on. Say that you hold back the normal verdict, and why. The
user files the report. This skill supplies the material and takes no step of its
own.

## 6. Write the verdict

Read [references/checklist.md](references/checklist.md) before you write the
answer, not after. It lists the verdicts, the evidence each one needs, and the
questions that decide between them. The verdicts are not degrees of confidence
in one finding. The verdict you pick first decides what you still have to
establish.

Report what you did not establish beside what you did. Where your reading
stopped at the code path, say so. The next person's work is exactly the part you
left.

End the verdict with each step of the base order you did not reach. Use the same
form: `Not reached: <step> — <why>`.

**The verdict is markdown the reader can copy, and the answer is where it
goes.** You write it for the person who acts on it, and rendered output does not
survive the move. Put the whole verdict in one fenced `markdown` block, with a
fence of four backticks. A client that renders the answer copies it as rendered
text, and the fence keeps the source. Write it to a file only where the caller
asks for one, at a path outside the checkout. This skill leaves the checkout as
it found it.

**A verdict that ends the issue comes with the comment that closes it.** Three
verdicts end an issue, and the checklist names them. It also says what the
comment contains and which markup Forge renders. Forge is not markdown. Put the
comment in the answer, beside the verdict. Filing it is the maintainer's act.
This skill has no login and comments on nothing.

## 7. Where the triage ends and the patch begins

**When the issue still happens and the user asks for the fix, invoke
`typo3-core-patch-development` before you make the change.** Load that skill by
name and work from it. Hand over the issue number, the verdict, the code path
you established and any failing test.

This is a step, not a note. A session that read it as a note wrote a whole patch
without that skill. It then worked out the changelog, the suites, the commit
message and the release branches again by itself.

## Where this skill stops

- **The fix** is `typo3-core-patch-development`. Step 7 says when to hand over.

- **Every act on the tracker** stays with a person. Nothing here comments on an
  issue, assigns, closes or reopens it. You write the verdict and the closing
  comment for the person who does.

- **A judgement of a patch somebody pushed** is `typo3-core-patch-review`. That
  skill reads the diff, not the report.
