---
name: typo3-core-patch-review
description: 'Review a TYPO3 core patch — your own before you push it, or somebody else''s patch set — and say what is wrong, missing or not ready, in priority order: the diff, its tests, the changelog entry, the commit message, the issue reference and the target branch.'
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Core Patch Review

This skill covers the review of one TYPO3 core patch. That is your own patch
before you push it, or a patch set somebody else pushed. The review reports what
is wrong, missing or not ready, in priority order. It stops before any change to
the patch, which `typo3-core-patch-development` covers. A review of an
extension, a sitepackage or a site project is `typo3-extension-health`. That
skill reads other surfaces against other rules.

**Fetch the rules before you read the diff.** A rule you fetch after the diff
only confirms your reading. It no longer tests it.

**A review does not change the patch it reviews.** A review that rewrites the
patch destroys the evidence for its own findings.

Keep this skill as routing and review method: it says which tool to ask and
when. The contribution rules, the suites and the commit-message rules come from
the lookups. A copy of them here would go out of date where nobody can correct
it.

## Steps at a glance

1. Establish the patch and the rules you judge it by.
2. Read what the issue and the review server already say about it.
3. Check what the patch must deliver, with one lookup per obligation.
4. Run the verification that the diff narrows.
5. Check the commit message and the target branch.
6. Report the findings.

When the user asks you to change the patch, follow "Where the review ends and
the rework begins".

## 1. Establish the patch and the rules

1. Work through [references/base.md](references/base.md) first. It fixes the
   order every task starts in. In a review, that order decides the result.

2. Read [references/checklist.md](references/checklist.md). It lists the review
   surfaces and the severity rubric. It also says what a finding and a dropped
   candidate must name.

3. Establish four facts about the patch. Every later lookup takes one of them as
   its argument.

   - **The changed paths.**
   - **The target branch.** A review against the wrong branch applies the wrong
     conventions and cannot tell.
   - **The commit message.**
   - **The issue it names.**

   For a pushed patch set, call `typo3_gerrit_lookup` with the `Change-Id` or
   the change number. It returns all four without a fetch. So you can compare a
   shortlist of changes before you check out any of them. For a patch nobody
   pushed, read the four from the diff.

4. Pass the changed paths to `typo3_hint_lookup`, one call per subsystem. The
   answer gives the conventions of the subsystem the patch is in. Do this before
   you decide whether the code is right.

5. Read `omittedHints` in the answer of `typo3_task_guide`. It lists the hints
   the brief left out, and the line above the brief names them too. **An omitted
   id for a subsystem the diff changes is a call you make before any finding
   about that subsystem.** Fetch it with `typo3_hint_lookup` by id, the `lookup`
   its entry names. The hints in the brief do not replace it.
   `datahandler-testing` on a patch that adds DataHandler tests is such a case.

**Where the review needs the patch on disk, invoke `typo3-core-patch-checkout`
for that one change.** Say in the report which ref it fetched. You need the
patch on disk to read the hunks, because the lookup returns the paths and not
the diff. You also need it to run a suite against the patch.

You do not need it to choose which of several changes to read. The paths, the
size and the votes decide that. A ref fetched for that choice later looks like
the reader's own work. One session fetched eight before its user stopped it.

## 2. What the project already says about this patch

The commit message names two things the diff does not contain: the issue and the
change. Read both before you read the code a second time.

- **The issue.** Call `typo3_forge_lookup` with the issue number. The issue says
  what the change is for. The commit message gives only the author's view of the
  problem and the solution. An issue that calls itself a part tells you the
  patch is not meant to stand alone. That decides every finding about what the
  patch lacks.

- **The review so far.** The `typo3_gerrit_lookup` answer from step 1 also gives
  the votes and the comments. A comment on an earlier patch set that nobody
  answered is a finding of its own. Without it, this review repeats a point
  somebody already made.

  You decide whether a thread is answered. The answer returns the flag on a
  thread and the reply under it, which are two facts. Where the patch reached
  you as a commit, its `Change-Id` is the argument. A commit hash from the
  checkout gets the same answer.

**Both arguments come out of the commit message, and that makes them safe.**

- `Resolves:` is the Forge issue.
- `Change-Id:` is the change, and it still names it after an amend.

A number from anywhere else can be wrong without an error. Each lookup answers a
number that belongs to the other one. It returns a real change or issue that has
nothing to do with this patch. So check the subject. The answer has the subject
of the commit under review, or the number was wrong rather than the patch.

A patch nobody pushed yet has no change, and an answer of nothing is a result.
Say so in the report rather than leave the surface empty.

The answer names the commit of the current patch set. Compare it with
`git rev-parse HEAD`. Where the two differ, say which one the findings are
about. A review of an older patch set is the failure this check prevents. The
checkout cannot show it.

This step only reads. Votes, comments and uploads stay with the person who does
the review.

## 3. What the patch must deliver, per finding

Ask the lookup for each obligation. Do not recall it.

1. **The contribution rules.** Call `typo3_rule_lookup` for the rules the diff
   makes relevant. Examples are what a breaking change or a deprecation
   requires, what a changelog entry contains, and what review readiness means.
   The sections have subjects as names, so ask in the words of a subject.

   **Two subjects at most in one call, and a third is a call of its own.**
   `breaking change changelog entry` returns both sections whole. Asked apart,
   the two return the same pair twice.

   The lookup keeps a section only where it matches half of the query. So each
   added subject takes matches away from the others.
   `changelog entry testing review readiness` returns nothing at all.
   `changelog entry` and `review readiness` each answer whole.

2. **What the diff removes or renames.** List it before you ask. A public class,
   method, property or constant can disappear. So can a TCA field, a TypoScript
   path or a Fluid ViewHelper argument. This review exists for that kind of
   finding. A read of the new code does not show it, because the evidence is
   what is gone.

3. **The precedent.** Call `typo3_changelog_lookup` when the patch does
   something the core did before. What an earlier entry required of the same
   kind of change is the strongest argument a review can make. It also settles a
   disagreement without an appeal to taste.

   **List the kind before you search for words: `type` and `version`, and no
   query at all.** Before you argue from a precedent, read
   [references/precedent.md](references/precedent.md). It says which version to
   list and what to do when the listing misses.

4. **The manual.** Call `typo3_documentation_lookup` where the diff changes
   behaviour a manual describes. The manuals it searches are outside the core
   repository. So the manual change is a follow-up, not part of this patch. The
   follow-up is the finding, not a reason to skip it.

   A review said the wording lived elsewhere and concluded that the patch owed
   no documentation change. The patch had made the documented sentence about
   `stdWrap.override` false. Whether the patch needs a manual change at all is
   `typo3_rule_lookup` asked for `documentation`. A system extension's own
   `Documentation/` is in the checkout, and the patch itself changes it.

5. **An alternative you propose.** **Sweep the checkout for the call sites
   before you propose an alternative.** A recommendation to a core reviewer
   needs precedent rather than taste. This server does not know how common an
   idiom is in the core.

   The base's step after the lookups starts at the class that implements a
   behaviour. This question has none, and PHP source as code is outside what
   this server reads. The checkout answers it with the call sites at their paths
   and lines. Say how many: one is a coincidence and a spread across system
   extensions is a convention.

6. **The prose.** Call `typo3_rule_lookup` with
   `documentId="any/writing/the-prose-a-patch-carries"` for the **Prose**
   surface. A brief includes that page only for a task that writes files. A
   review writes none, so it asks here.

Every finding names the changed path it is about. A statement about the
subsystem that ties to no line in this diff belongs in the issue.

**Where the patch is one of a set, read a finding against the end of the set
first.** What a later patch of the set removes is not a defect of the set.
Establish that from a reading of that patch rather than of what a message
promises about it. Still report the finding where each patch must stand on its
own. Name the later patch that settles it.

## 4. Run the verification the diff narrows

Call `typo3_test_run_guide` with the changed paths. It returns the suites that
can fail on this change, with a targeted command for each. Run the narrowest
suite first and the broader ones after it.

Take the commands from `typo3_test_run_guide` and `typo3_script_lookup`. Never
take them from memory or from the host's own PHP. The core's test runner is a
script, so `typo3_project_describe` does not list it. That answer lists the
repository's composer scripts only. A check run outside the project's runner is
evidence about your machine.

### Name every suite you did not run

A review may run anything that cannot change the code. It says what it ran and
what that printed. **It then writes out, by name, the suites on that list it did
not run.** Without them, four green suites read as a finished verification. A
review can support that claim least of all. "The tests would presumably still
pass" is not a review sentence. And an unnamed suite is the same sentence with
the words taken out.

The list is the guide's `verifying`: every entry it marks `verifies`, by name.
Every name on it appears on a `Ran:` line or a `Not run:` line. So the
`Not run:` line is that list minus what ran. An install, a dispatcher or a
server for a browser checks nothing and is not on the list.

**A suite on that list stays unrun for one of four reasons, and its `Not run:`
line names the reason.**

1. The suite writes into the checkout and has no report-only form.
2. You asked the user before the skip, and the user said no.
3. No worktree holds the patch, and nobody was there to ask.
4. The review skipped it to finish faster. A narrower suite that passed and a
   scope the session chose are this reason too, and the line says so.

**Where a worktree holds the patch, the third reason is false.** A suite left
unrun there takes the fourth.

**A skip for speed never takes the test a High or blocking finding rests on.**
Run that test alone, in the targeted form `typo3_test_run_guide` returns for its
suite. Use the database the failure was seen on, in place of the `-d` of that
form. Its `Ran:` line names the test. The `Not run:` line of the suite names the
rest.

### Run the suites in a worktree

**Run the suites in a `git worktree` on the patch set rather than in the main
checkout.** The worktree leaves the reader's branch and uncommitted work alone.
So a review that must not touch the checkout has a reason for a worktree. It has
no reason to skip the run.

Where no worktree holds the patch yet, ask the user whether to create one. Ask
before you skip the run, never instead of the run. A skip after that question
names the question and its answer on its `Not run:` line.

**Where nobody can answer the question, run the suites in a worktree that
already exists.** A headless session has no user to ask. It may skip a suite to
finish faster, and says so. It creates no worktree without the question, because
that writes into the user's repository.

### Probe what a reading cannot settle

**A scratch probe is one of the things a review may run.** Add a temporary
fixture column, a model property or a test of your own. Run a targeted suite
against it, read what it prints, and put the tree back. Do not edit the patch
under review: that is the boundary. A probe writes files and restores them, and
you verify the restoration rather than assume it.

A probe turns "this would presumably throw" into a pasted error. It helps most
with a dropped candidate. Only what makes a path impossible disproves it, and
often only a probe can show that.

**Put the tree back to what the probe found, which is not always the committed
state.** `git checkout -- <path>` restores the file from the index, and in a
review the index holds the patch set. On a file with no other change, that
undoes the probe. On a file that also has a change the user asked you for, it
undoes that change too. Nothing reports the loss.

Where the file has work of your own, do one of these before the probe:

- Copy the file aside, and copy it back afterwards.
- Run `git add <path>`, so the restore lands on your work.

Verify with `git diff --stat <path>` rather than with `git status`. A clean
status confirms the restore on a file you did not edit. On a file you did edit,
it means the loss.

**A finding that turns on what the frontend rendered is one no reading can
settle.** TypoScript defaults, TypoScript in an `ext_localconf.php` and anything
below `lib.parseFunc` are such cases. So is a PHP change to the request
pipeline, an error handler or a caller of the page renderer. What changed is the
output, not what the file says.

Where no test covers the case, the suites stay green on either side of it. A
throwaway functional test that renders one page and prints the output settles
it. `typo3_rule_lookup` with `documentId="core/testing/proving-a-rendering"`
says how to build one.

**Check a speed claim in the message at its cause.** A before and an after name
an expensive call that the patch makes less often. Count that call on both sides
before you time anything. Measure where the author measured. Turn each part of
the patch off once, so each cause gets its share. `typo3_rule_lookup` with
`documentId="core/testing/timing-a-code-path"` says how.

## 5. Check the commit message and the target branch

Call `typo3_commit_message_guide` with `workflow="core"`, the message and the
change type. It says whether the core accepts the message. Without
`workflow="core"`, it checks the message as for a repository of its own. Then it
asks for no Forge issue.

Read its answer against the diff, not on its own. Look for a subject that
describes the wrong action, or a missing issue reference. Look for a breaking
change without its marker.

The target branch decides which conventions apply and which findings matter. So
a diff that does not fit its stated target is a finding of its own.

Every branch in `Releases:` is a claim the review checks. One `git merge-tree`
per branch says whether the patch applies there. `typo3_rule_lookup` with
`documentId="core/contribution/gerrit-workflow"` gives the command.

## 6. Report

Call `typo3_gerrit_lookup` again before you write, where time has passed since
you read the change. On a busy change, a few hours is enough. Votes, messages
and alternatives pushed as separate changes reach the server with no sign in the
checkout. One review found the CI vote and two alternatives only on its second
day.

**Write the report in the skeleton of
[references/report.md](references/report.md), every heading in its order and
every entry in its line form.** The three review skills share that skeleton, so
two reports compare section by section. The report contains:

- The findings, ordered by what stops the patch, each with why it stops it.
- What you checked and found correct.
- The points you raised while you read and then dropped, each with what dropped
  it.
- Each surface of the checklist, marked assessed, unassessed or not applicable
  to this diff. Without it, a reader cannot tell a short review from one that
  looked at less.
- One line `Not reached: <step> — <why>` for each step of the base order you did
  not reach.

**The report is markdown the reader can copy, and the answer is where it goes.**
A full report is long, so its form matters. Somebody pastes a review into the
change, the issue or a chat, and rendered output does not survive the move.

Put the whole report in one fenced `markdown` block, with a fence of four
backticks. A client that renders the answer copies it as rendered text, and the
fence keeps the source. Write it to a file only where the caller asks for one,
at a path outside the checkout under review. A changed or untracked file beside
the patch is a surface this review reports on.

**A document the caller asks for is this report, in this order and with every
part above.** A summary in a shape of its own drops the ranks, the paths, the
dropped candidates and the surfaces. A reader checks a review by those.

## Where the review ends and the rework begins

**When the user asks you to make the change, invoke
`typo3-core-patch-development` and work from it.** That includes the amend and
the push. These instructions ask for it: "finish it", "fix it", "amend it",
"write the test". Such an instruction is easy to miss. It is one sentence in the
middle of a session that goes well.

A session that goes on under review rules changes the patch and still reports
that it does not. Nothing breaks and the tree stays clean, so nothing marks the
change of task.

**Before the first edit to a file meant to survive, ask whether
`typo3-core-patch-development` should run.** An instruction can arrive in words
this list does not include. An edit is an act you notice yourself. A scratch
probe is not that edit: you put it back and it leaves no diff. Step 4 draws that
boundary.

**Three calls take an argument this review has just established. So the order
starts again rather than repeats.**

- `typo3_task_guide` with the change type you are about to write.
- `typo3_hint_lookup` with the paths you will edit.
- `typo3_changelog_lookup` with `type: deprecation`, the deprecation sweep. A
  review skips that sweep, and a change does not.

You walked the base once at the start of this review, against files nobody would
write.

**A remark about the weight of a finding is not that instruction.** "That is a
reason to reject it", "I think the tests should show that", "that one blocks
it". Each confirms a finding and asks for no change. So re-rank the finding and
continue the review.

Where the sentence could be either, ask which the user meant. A switch costs a
turn under the wrong skill's rules, and a question costs one sentence.

Until the user asks for the change, the review changes nothing. Where the patch
needs work, name the work and stop.
