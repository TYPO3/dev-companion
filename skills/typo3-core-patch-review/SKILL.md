---
name: typo3-core-patch-review
description: 'Review a TYPO3 core patch — your own before you push it, or somebody else''s patch set — and say what is wrong, missing or not ready, in priority order: the diff, its tests, the changelog entry, the commit message, the issue reference and the target branch.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Core Patch Review

Review one patch against the checkout it sits in, and report in priority order.
Keep this skill as routing and review method. The contribution rules, the suites
and the commit-message rules are lookups. A copy of them here is one nobody can
correct.

## Establish the patch, then the rules you judge it by

1. Work through [references/base.md](references/base.md). It fixes the order
   every task here starts in, and a review is where that order decides the
   result. A rule you fetch after you read the diff confirms a reading instead
   of a test of it.
2. Read [references/checklist.md](references/checklist.md) for the review
   surfaces, what a finding owes, what a dropped candidate owes, and the
   severity rubric.
3. Establish the patch itself: **the changed paths**, **the branch it targets**,
   **the commit message**, and **the issue it names**. Every lookup below takes
   one of them as its argument. A review that has not established the target
   branch reviews against the wrong conventions and cannot tell.
   `typo3_gerrit_lookup` with the `Change-Id` or the change number answers all
   four for a patch set somebody has pushed. So this costs no checkout, and you
   triage a shortlist without a fetch. A patch nobody pushed is the other case,
   and one reading of the diff produces the same four.

The changed paths are the argument, not the subject. Pass them to
`typo3_hint_lookup` for the conventions of the subsystem the patch is in, one
call per subsystem. Do that before you form a view of whether the code is right.

**Where the review needs the patch on disk, invoke `typo3-core-patch-checkout`
for that one change. Say in the report which ref it fetched.** A read of the
hunks needs it, because the answer above carries the paths and not the diff. So
does a suite run against the patch.

The decision which of several changes to read does not. The paths, the size and
the vote state settle that. Afterwards nobody can tell a ref you fetched for a
triage from the branches with the reader's own work. One session pulled in eight
before its user stopped it.

## What the project already says about this patch

The message names two things the diff does not contain: the issue it resolves
and the change it is. Read both before you read the code a second time.

- `typo3_forge_lookup` with the issue number. What the change is *for* belongs
  to the issue. A review that takes it from the commit message has the author's
  account of the problem and of the solution. It is also where a series says it
  is one. An issue that calls itself a part tells you the patch is not meant to
  stand alone. That decides every finding about what it lacks.
- The `typo3_gerrit_lookup` answer step 3 established the patch from also says
  what the review so far is. That is the votes on it and the comments on it. A
  comment somebody left on an earlier patch set and nobody answered is a finding
  of its own. It is the one this review would otherwise make a second time.

  What "unanswered" means is yours to read. The flag on a thread and the reply
  under it are two facts, and both come back. Where the patch reached you as a
  commit rather than a change number, its `Change-Id` is the argument. A commit
  hash out of the checkout reaches the same answer.

**Both arguments come out of the commit message, and that makes them safe.**
`Resolves:` is the Forge issue, `Change-Id:` is the change, and the `Change-Id`
still names it after an amend. A number you carry in from elsewhere does not
fail. Asked under the other's name, both lookups answer. They return a real
change and a real issue that belong to neither this patch nor each other.

So the check is the subject. What comes back carries the subject of the commit
under review, or the number was wrong rather than the patch.

A patch nobody pushed yet has no change, and an answer of nothing is a result.
Say so rather than leave the surface silent. Where the commit in the checkout
and the change on the server differ, name which of the two you read. A review of
an older patch set than the one that exists is the failure this step exists for.
The checkout cannot report it.

The answer carries the commit the current patch set is. Hold it against
`git rev-parse HEAD`, and where the two differ, say which one the findings are
about.

Reading is the whole of it. Votes, comments and uploads stay with the person who
does the review.

## What the patch owes, per finding

Ask the owner of each obligation rather than recall it:

- `typo3_rule_lookup` for the contribution rules the diff makes relevant. That
  is what a breaking change owes and what a deprecation owes. It is what belongs
  in a changelog entry, and what review readiness means. The sections have
  subjects for names. So ask in the words of a subject rather than in a sentence
  about the patch.

  **Two subjects at most in one call, and a third is a call of its own.**
  `breaking change changelog entry` returns both sections whole. The two asked
  apart return the same pair twice.

  The lookup keeps a section only where it carries half of what the query asks
  for. So every subject you add takes coverage off the sections the others
  reach. `changelog entry testing review readiness` returns nothing at all,
  while `changelog entry` and `review readiness` each answer whole. Measured:
  two subjects never empty a query, and a third regularly does, whether or not
  the subjects share a document.
- Enumerate what the diff **removes or renames** before you ask. A public class,
  method, property, constant, TCA field, TypoScript path or Fluid ViewHelper
  argument can disappear. That is the class of finding this review exists for. A
  read of the new code does not surface it. The evidence is in what is gone, not
  in what is there.
- `typo3_changelog_lookup` for the precedent, when the patch does something the
  core has done before. What an earlier entry required of the same kind of
  change is the strongest argument a review can make. It also settles
  disagreement without an appeal to taste.

  **List the kind before you search for words: `type` and `version`, and no
  query at all.** What makes an earlier change a precedent is its shape, and a
  shape has no vocabulary. The entry that settles a finding regularly shares no
  noun with the diff. Two reviews lost theirs to a query and found it by hand
  afterwards.

  `type` is the obligation the finding is about. `version` is the line the
  precedent would sit on. That is a released line the core backports the change
  to, or a major before that. A released line publishes few entries per type. So
  the listing is the whole of what the core did of that kind. You pick a
  precedent out of its titles.

  A major that still collects entries holds more of a type than the default
  answer carries, so raise `limit` there.

  **Ask it in the words of the entry's title, not in the identifier the diff
  removes.** The enumeration above leaves you with a class and a method name. A
  removal has a title after what it removed *about*: the subsystem, the kind of
  API. It carries the identifiers in a list inside the file.

  So a query naming one of them and coming back empty has established nothing.
  Neither has one narrowed to the branch this patch targets. A precedent sits
  under the version it landed in, which is an earlier one by definition. Where
  the listing and the words both miss, the precedent is still there, and the
  checkout holds it. That is `Documentation/Changelog`, which this server does
  not read and you do. Say which of the two answered.

  **What kind of change an entry came out of is two readings rather than one.**
  `typo3_forge_lookup` with its issue number says what the reporter filed the
  issue as. The argument a review makes is about the commit keyword instead. It
  says that an earlier bugfix of this kind owed an entry.

  The two disagree in both directions, so read the keyword where it stands.
  `git log --diff-filter=A` over the entry's own file names the commit that
  added it. The issue behind a security entry is not public, and an unavailable
  answer there is not an outage.
- `typo3_documentation_lookup` where the diff changes behaviour a manual states.
  The books it searches live outside the core repository. So what the patch owes
  there is a follow-up rather than part of this patch. That is the finding
  rather than the reason to skip it.

  A review said the wording lived elsewhere and concluded that the patch owed no
  documentation change. The patch made the documented sentence about
  `stdWrap.override` false. Whether the patch owes a manual anything at all is
  `typo3_rule_lookup` asked for `documentation`. A system extension's own
  `Documentation/` is in the checkout, and it changes in the patch itself.
- **Sweep the checkout for the call sites before proposing an alternative.** A
  recommendation to a core reviewer needs precedent rather than taste. Whether
  an idiom has a foothold in the core is precedent this server does not hold.
  The base's step after the lookups starts at the class that implements a
  behaviour. This question has none, and PHP source as code is outside what this
  server reads.

  The checkout answers it, and the answer is the call sites at their paths and
  lines. Say how many: one is a coincidence and a spread across system
  extensions is a convention. A review that proposes an alternative and names
  none has argued from taste.

Every finding names the changed path it is about. A statement about the
subsystem that ties to no line in this diff belongs in the issue.

Where the patch is one of a set, read a finding against the end of the set
first. What a later patch in the same set removes is not a defect of the set. To
establish that is a reading of that patch rather than of what a message promises
about it. Still report it where each patch has to stand on its own, and name the
later one that settles it.

## Verification is the project's own, and the diff narrows it

`typo3_test_run_guide` with the changed paths returns the suites that can fail
on this change and the targeted invocation for each. That is the verification a
review proposes: the narrowest applicable suite first, the broader ones named
after it.

The core's suites are not among the commands `typo3_project_describe` declares.
That answer is about the repository's own composer scripts. The test runner is a
script rather than one of them. Take the commands from `typo3_test_run_guide`
and `typo3_script_lookup`, never from memory and never from the host's own PHP.
A check run outside the project's runner is evidence about your machine.

A review may run what cannot change the code, and it says what it ran and what
it printed. **It then writes out, by name, the suites on that list it did not
run.** Left out, they make four green suites read as a finished verification.
That is the claim a review is least able to support. "The tests would presumably
still pass" is not a review sentence. And an unnamed suite is the same sentence
with the words taken out.

**A scratch probe is one of the things it may run.** Add a temporary fixture
column, a model property or a test of your own. Run a targeted suite against it,
read what it prints, and put the tree back. You do not edit the patch under
review, which is the boundary that matters. A probe writes files and restores
them, and you verify the restoration rather than assume it.

This is what turns "this would presumably throw" into a pasted error.
Dropped-candidate findings are where it earns most. What disproves a path is
what makes it impossible, and a probe is often the only thing that can.

**Put the tree back to what the probe found, which is not always the committed
state.** `git checkout -- <path>` restores the file from the index, and in a
review the index holds the patch set. On a file that carries nothing else, that
undoes the probe. On a file that also carries a change the user asked you for,
it undoes the change with it. Nothing reports the loss.

Where the file carries work of your own, copy it aside before the probe and back
afterwards. Or `git add <path>` first, so the restore lands on your work. Verify
with `git diff --stat <path>` rather than with `git status`. A clean status is
the confirmation on a file you did not edit, and the loss on one you did.

**A finding that turns on what the frontend rendered is one no reading can
settle.** TypoScript defaults, TypoScript in an `ext_localconf.php` and anything
below `lib.parseFunc` are the obvious half. A PHP change to the request
pipeline, an error handler or a page renderer caller is the same case. What
changed is what comes out, not what the file says.

Where no test covers the constellation, the suites stay green on either side of
it. A throwaway functional test that renders one page and prints what came out
settles it. `typo3_rule_lookup` with
`documentId="core/testing/proving-a-rendering"` says how you build one. It says
how you get the output out of a run that would otherwise print nothing. It says
one marker per region, so the response says which part of it changed. It says
what a service holds while the request still runs.

## Commit shape and target branch

`typo3_commit_message_guide` with `workflow="core"`, the message and the change
type says whether the message is submittable. Without that argument it checks
the message as a repository of its own and asks for no Forge issue. Read its
answer against the diff rather than on its own. That is the subject that
describes the wrong action, or the missing issue reference. It is the marker a
breaking change needs and this one does not carry.

The branch the patch targets decides which conventions apply and which findings
matter. So a patch whose stated target its diff does not fit is a finding of its
own.

## Report

Order by what stops the patch, and say why each one stops it:

1. what blocks the submission of the patch at all;
2. what a reviewer would send it back for;
3. what is worth a change and would not block it;
4. what you checked and found correct, briefly, so a reader can tell a silent
   surface from a verified one;
5. what you raised while you read and dropped, with what dropped it.

Close on the checklist's surfaces with each one marked assessed, unassessed or
not applicable to this diff. A reader cannot tell a review that reports only
findings from one that looked at less.

**The report is markdown the reader can copy, and the answer is where it goes.**
Everything this section asks for makes it long, and length makes the form
matter. Somebody carries a review into the change, the issue or a chat, and
rendered output does not survive the move. Write it to a file only where the
caller asks for one, at a path outside the checkout under review. A modified or
untracked file beside the patch is a surface this review reports on.

## Where the review ends and the rework begins

**When the user asks you to make the change, invoke
`typo3-core-patch-development` and work from it.** That includes the amend and
the push. An instruction to change the patch asks for it: "finish it", "fix it",
"amend it", "write the test". It looks like nothing at all from the inside. It
is a sentence in a conversation in the middle of a session that goes well.

A session that carries on under review rules holds "it does not change the
patch" while it changes the patch. One did. It edited `ColumnMap.php`, added a
fixture column, wrote a functional test, ran seven suites and amended the
commit. All of that was still inside this skill. Nothing broke and the tree
stayed clean, which is why nothing marked the crossing.

**Before the first edit to a file meant to survive, ask whether
`typo3-core-patch-development` should run.** You have to recognise a sentence,
and one has arrived in words this enumeration does not reach. An edit is an act
you already perform. A scratch probe is not that edit: you put it back and it
leaves no diff. The verification section above draws its boundary.

**Three of its calls take an argument this review has just established. That
makes them a restart rather than a repeat.** Those are `typo3_task_guide` with
the change type you are about to write, and `typo3_hint_lookup` with the paths
you will edit. The third is the deprecation sweep. That sweep is
`typo3_changelog_lookup` with `type: deprecation`, which a review is exempt from
and a change is not. You walked the base once at the start of this review,
against files nobody was going to write.

**A remark about a finding's weight is not that instruction.** "That is a reason
to reject it", "I think the tests should show that", "that one blocks it". Each
reaffirms a finding and commissions nothing. So it asks for the finding
re-ranked and the review carried on.

One session read "I think the tests should prove it" as the handover and invoked
the patch skill. The reader had meant that the missing test was reason enough to
reject the patch. Where the sentence could be either, ask which the user meant.
A switch costs a turn under the wrong skill's rules, and a question costs one
sentence.

Until the user asks you for the change, the rule above stands whole. A review
that rewrites what it reviews has destroyed the evidence for its own findings.
Where the answer is that the patch needs work, name it and stop.

This skill owns the review of a core patch and the order it reports its findings
in. A review of an extension, a sitepackage or a site project belongs to
`typo3-extension-health` and its checklist. That reads different surfaces
against different rules.
