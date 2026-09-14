---
name: typo3-core-patch-review
description: 'Review a TYPO3 core patch — your own before you push it, or somebody else''s patch set — and say what is wrong, missing or not ready, in priority order: the diff, its tests, the changelog entry, the commit message, the issue reference and the target branch.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Core Patch Review

Review one patch against the checkout it sits in, and report in priority order.
Keep this skill as routing and review method; the contribution rules, the suites
and the commit-message rules are lookups, and a copy of them here is one that
cannot be corrected.

## Establish the patch, then the rules it is judged by

1. Work through [references/base.md](references/base.md). It fixes the order
   every task here starts in, and a review is where that order decides the
   result: a rule fetched after the diff has been read confirms a reading
   instead of testing it.
2. Read [references/checklist.md](references/checklist.md) for the review
   surfaces, what a finding owes, what a dropped candidate owes, and the
   severity rubric.
3. Establish the patch itself: **the changed paths**, **the branch it targets**,
   **the commit message**, and **the issue it names**. Every lookup below takes
   one of them as its argument, and a review that has not established the target
   branch is reviewing against the wrong conventions and cannot tell.
   `typo3_gerrit_lookup` with the `Change-Id` or the change number answers all
   four for a patch set somebody has pushed, so this costs no checkout and a
   shortlist is triaged without fetching anything. A patch that is not pushed is
   the other case, and one reading of the diff produces the same four.

The changed paths are the argument, not the subject. Pass them to
`typo3_hint_lookup` for the conventions of the subsystem the patch is in, one
call per subsystem, before forming a view of whether the code is right.

**Where the review needs the patch on disk, invoke `typo3-core-patch-checkout`
for that one change and say in the report which ref it fetched.** Reading the
hunks needs it, because the answer above carries the paths and not the diff, and
so does running a suite against the patch. Deciding which of several changes to
read does not: that is what the paths, the size and the vote state settle. A ref
fetched to triage a shortlist cannot be told apart afterwards from the branches
the reader's own work sits on, and one session pulled in eight before its user
stopped it.

## What the project already says about this patch

The message names two things the diff does not contain — the issue it resolves
and the change it is — and both are read before the code is read a second time.

- `typo3_forge_lookup` with the issue number. What the change is *for* belongs
  to the issue, and a review that takes it from the commit message has the
  author's account of the problem and of the solution. It is also where a series
  says it is one: an issue calling itself a part tells you the patch is not
  meant to stand alone, which decides every finding about what is missing from
  it.
- The `typo3_gerrit_lookup` answer step 3 established the patch from also says
  what the review so far is: the votes on it and the comments left on it. A
  comment somebody left on an earlier patch set and nobody answered is a finding
  of its own, and it is the one this review would otherwise make a second time.
  What "unanswered" means is yours to read: the flag on a thread and the reply
  under it are two facts and both come back. Where the patch reached you as a
  commit rather than as a change number, the `Change-Id` its message carries is
  the argument, and a commit hash out of the checkout reaches the same answer.

**Both arguments come out of the commit message, and that is what makes them
safe.** `Resolves:` is the Forge issue, `Change-Id:` is the change, and the
`Change-Id` still names it after an amend. A number carried in from elsewhere
does not fail: asked for under the other's name both lookups answer, with a real
change and a real issue belonging to neither this patch nor each other. So the
check is the subject — what comes back carries the subject of the commit under
review, or the number was wrong rather than the patch.

A patch that is not pushed yet has no change, and an answer of nothing is a
result: say so rather than leaving the surface silent. Where the commit in the
checkout and the change on the server differ, name which of the two was read —
reviewing an older patch set than the one that exists is the failure this step
is here for, and the checkout cannot report it. The answer carries the commit
the current patch set is: hold it against `git rev-parse HEAD`, and where the
two differ say which one the findings are about.

Reading is the whole of it. Voting, commenting and uploading stay with the
person doing the review.

## What the patch owes, per finding

Ask the owner of each obligation rather than recalling it:

- `typo3_rule_lookup` for the contribution rules the diff makes relevant — what
  a breaking change owes, what a deprecation owes, what belongs in a changelog
  entry, and what review readiness means. The sections are named by subject, so
  ask in the words of a subject rather than in a sentence about the patch. **Two
  subjects at most in one call, and a third is a call of its own.**
  `breaking change changelog entry` returns both sections whole, and asking the
  two separately returns the same pair twice. A section is kept only where it
  carries half of what the query asks for, so every subject added takes coverage
  off the sections the others reach: `changelog entry testing review readiness`
  returns nothing at all, while `changelog entry` and `review readiness` each
  answer whole. Two subjects were measured never to empty a query and a third
  regularly does, whether or not the subjects share a document.
- Enumerate what the diff **removes or renames** before asking. A public class,
  method, property, constant, TCA field, TypoScript path or Fluid ViewHelper
  argument that disappears is the finding class this review exists for, and it
  is the one a reading of the new code does not surface — the evidence is in
  what is gone, not in what is there.
- `typo3_changelog_lookup` for the precedent, when the patch does something the
  core has done before. What an earlier entry required of the same kind of
  change is the strongest argument a review can make, and it is also the one
  that settles disagreement without an appeal to taste.

  **List the kind before you search for words: `type` and `version`, and no
  query at all.** What makes an earlier change a precedent is its shape, and a
  shape has no vocabulary — the entry that settles a finding regularly shares no
  noun with the diff, and two reviews have lost theirs to a query and found it
  by hand afterwards. `type` is the obligation the finding is about; `version`
  is the line the precedent would sit on, which is a released one the change is
  backported to or a major before that. A released line publishes few entries
  per type, so the listing is the whole of what the core did of that kind and
  its titles are what a precedent is picked out of. A major still collecting
  entries holds more of a type than the default answer carries, so raise `limit`
  there.

  **Ask it in the words the entry is titled in, not in the identifier the diff
  removes.** What the enumeration above leaves you holding is a class and a
  method name, while a removal is titled after what was removed *about* — the
  subsystem, the kind of API — and carries the identifiers in a list inside the
  file. So a query naming one of them and coming back empty has established
  nothing, and neither has one narrowed to the branch this patch targets: a
  precedent is filed under the version it landed in, which is an earlier one by
  definition. Where the listing and the words both miss, the precedent is still
  there and the checkout is what holds it — `Documentation/Changelog`, which
  this server does not read and you do. Say which of the two answered.

  **What kind of change an entry came out of is two readings rather than one.**
  `typo3_forge_lookup` with its issue number says what the issue was filed as,
  and the argument a review makes is about the commit keyword instead — that an
  earlier bugfix of this kind owed an entry. The two disagree in both
  directions, so the keyword is read where it is written:
  `git log --diff-filter=A` over the entry's own file names the commit that
  added it. The issue behind a security entry is not public, and an unavailable
  answer there is not an outage.
- `typo3_documentation_lookup` where the diff changes behaviour a manual states.
  The books it searches are published outside the core repository, so what is
  owed there is a follow-up rather than part of this patch — which is the
  finding rather than the reason to skip it. A review said the wording lived
  elsewhere and concluded that no documentation change was owed, for a patch
  that makes the documented sentence about `stdWrap.override` false. Whether the
  patch owes a manual anything at all is `typo3_rule_lookup` asked for
  `documentation`, and a system extension's own `Documentation/` is in the
  checkout, where it changes in the patch itself.
- **Sweep the checkout for the call sites before proposing an alternative.** A
  recommendation to a core reviewer needs precedent rather than taste, and
  whether an idiom is established in the core is precedent this server does not
  hold. The base's step after the lookups starts at the class that implements a
  behaviour; this question has none, and PHP source as code is outside what this
  server reads. The checkout answers it, and the answer is the call sites at
  their paths and lines. Say how many there are — one is a coincidence and a
  spread across system extensions is a convention. A review that proposes an
  alternative and names none has argued from taste.

Every finding names the changed path it is about. A statement about the
subsystem that does not tie to a line in this diff belongs in the issue, not in
a review of a patch.

Where the patch is one of a set, a finding is read against the state at the end
of the set before it is reported. What a later patch in the same set removes is
not a defect of the set, and establishing that is a reading of that patch rather
than of what a message promises about it. It is still reported where each patch
has to stand on its own, and it names the later one that settles it.

## Verification is the project's own, and it is narrowed by the diff

`typo3_test_run_guide` with the changed paths returns the suites that can fail
on this change and the targeted invocation for each. That is the verification a
review proposes: the narrowest applicable suite first, the broader ones named
after it.

The core's suites are not among the commands `typo3_project_describe` declares —
that answer is about the repository's own composer scripts, and the test runner
is a script rather than one of them. Take the commands from
`typo3_test_run_guide` and `typo3_script_lookup`, never from memory and never
from the host's own PHP: a check run outside the project's runner is evidence
about your machine.

A review may run what cannot change the code, and says what it ran and what it
printed. **It then writes out, by name, the suites on that list it did not
run.** Leaving them out is what makes four green suites read as a finished
verification, and that is the claim a review is least able to support. "The
tests would presumably still pass" is not a review sentence, and an unnamed
suite is the same sentence with the words taken out.

**A scratch probe is one of the things it may run.** Add a temporary fixture
column, a model property or a test of your own, run a targeted suite against it,
read what it prints, and put the tree back. The patch under review is not
edited, which is the boundary that matters; a probe writes files and restores
them, and the restoration is verified rather than assumed. This is what turns
"this would presumably throw" into a pasted error, and dropped-candidate
findings are where it earns most: what disproves a path is what makes it
impossible, and a probe is often the only thing that can.

**Put the tree back to what the probe found, which is not always what is
committed.** `git checkout -- <path>` restores the file from the index, and in a
review the index holds the patch set. On a file that carries nothing else, that
is the probe undone. On a file that also carries a change you were asked for, it
is the change undone with it, and nothing reports the loss. Where the file
carries work of your own, copy it aside before the probe and back afterwards, or
`git add <path>` first so the restore lands on your work. Verify with
`git diff --stat <path>` rather than with `git status`. A clean status is the
confirmation on a file you were not editing, and the loss on one you were.

**A finding that turns on what the frontend rendered is one reading cannot
settle.** TypoScript defaults, TypoScript declared in an `ext_localconf.php` and
anything below `lib.parseFunc` are the obvious half, and a PHP change to the
request pipeline, an error handler or a caller of the page renderer is the same
case: what changed is what comes out, not what the file says. Where no test
covers the constellation the suites stay green on either side of it. What
settles it is a throwaway functional test that renders one page and prints what
came out, and building one is `typo3_rule_lookup` with
`documentId="core/testing/proving-a-rendering"` — how the output is got out of a
run that would otherwise print nothing, one marker per region so the response
says which part of it changed, and what a service holds while the request is
still running.

## Commit shape and target branch

`typo3_commit_message_guide` with `workflow="core"`, the message and the change
type says whether the message is submittable. Without that argument it checks
the message as a repository of its own and asks for no Forge issue. Read its
answer against the diff rather than on its own: the subject that describes the
wrong action, the missing issue reference, the marker a breaking change needs
and this one does not carry.

The branch the patch targets decides which conventions apply and which findings
matter, so a patch whose target is stated and whose diff does not fit it is a
finding of its own.

## Report

Order by what stops the patch, and say why each one stops it:

1. what blocks the patch from being submitted at all;
2. what a reviewer would send it back for;
3. what is worth changing and would not block it;
4. what was checked and is correct — briefly, so a silent surface and a verified
   one are not read alike;
5. what was raised while reading and dropped, with what dropped it.

Close on the checklist's surfaces with each one marked assessed, unassessed or
not applicable to this diff. A review that reports only findings cannot be told
apart from one that looked at less.

**The report is markdown the reader can copy, and the answer is where it goes.**
Everything this section asks for is what makes it long, and length is what makes
the form matter: a review is carried into the change, into the issue or into a
chat, and rendered output does not survive the move. Write it to a file only
where the caller asks for one, at a path outside the checkout under review —
what is modified or untracked beside the patch is a surface this review reports
on.

## Where the review ends and the rework begins

**When you are asked to make the change, invoke `typo3-core-patch-development`
and work from it.** That includes the amend and the push. What asks for it is an
instruction to change the patch — "finish it", "fix it", "amend it", "write the
test" — and it looks like nothing at all from the inside, a sentence in a
conversation in the middle of a session that is going well. A session that
carries on under review rules is holding "it does not change the patch" while
changing the patch, and one did: it edited `ColumnMap.php`, added a fixture
column, wrote a functional test, ran seven suites and amended the commit, all
still inside this skill. Nothing broke and the tree stayed clean, which is why
nothing marked the crossing.

**Before the first edit to a file meant to survive, ask whether
`typo3-core-patch-development` should be running.** A sentence has to be
recognised, and one has arrived in words this enumeration does not reach. An
edit is an act you are already performing. A scratch probe is not that edit: it
is put back and leaves no diff, and the verification section above is where its
boundary is drawn.

**Three of its calls take an argument this review has just established, and that
is what makes them a restart rather than a repeat.** `typo3_task_guide` with the
change type about to be written, `typo3_hint_lookup` with the paths about to be
edited, and the deprecation sweep — `typo3_changelog_lookup` with
`type: deprecation`, which a review is exempt from and a change is not. The base
was walked once at the start of this review, against files nobody was going to
write.

**A remark about a finding's weight is not that instruction.** "That is a reason
to reject it", "I think the tests should show that", "that one blocks it" — each
reaffirms a finding and commissions nothing, so what it asks for is the finding
re-ranked and the review carried on. One session read "I think the tests should
prove it" as the handover and invoked the patch skill, where the reader had
meant that the missing test was reason enough to reject the patch. Where the
sentence could be either, ask which was meant: switching costs a turn under the
wrong skill's rules, and asking costs one sentence.

Until you are asked for the change, the rule above stands whole: a review that
rewrites what it reviews has destroyed the evidence for its own findings. Where
the answer is that the patch needs work, name it and stop.

This skill owns the review of a core patch and the order its findings are
reported in. Reviewing an extension, a sitepackage or a site project belongs to
`typo3-extension-health` and its checklist, which reads different surfaces
against different rules.
