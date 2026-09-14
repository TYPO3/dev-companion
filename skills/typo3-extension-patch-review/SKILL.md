---
name: typo3-extension-patch-review
description: 'Judge one incoming change against a TYPO3 extension, sitepackage or project package — a GitHub pull request, a patch, a branch somebody proposes — and say what stops it being merged, from its versions to its commit message and its checks. It stops at the verdict; the whole repository is typo3-extension-health''s.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Patch Review

Judge one change proposed against a package that is not the core. Report what
stops the merge. Keep this skill as routing and review method. The conventions,
the version boundaries and the commit rules are lookups. A copy of them here is
one nobody can correct.

## Establish the change, then the range you judge it in

1. Work through [references/base.md](references/base.md). It fixes the order
   every task here starts in, and a review is where that order decides the
   result. A rule you fetch after you read the diff confirms a reading instead
   of a test of it.
2. Read [references/checklist.md](references/checklist.md) for the review
   surfaces, what a finding owes, what a dropped candidate owes, and the
   severity rubric.
3. Establish the change itself. This server reads neither your git state nor the
   forge the change arrived on. What it needs from you is exactly what the
   change is. That is **the changed paths**, **the diff**, **the branch it
   targets** and **the commit messages**. It is also **whether it merges and
   what the repository's own pipeline said**.

   One reading produces all of them. `git fetch <remote> <ref>` and
   `git diff <base>...<head>` give the first four. The forge's own client or its
   web page gives the last. Every lookup below takes one of them as its
   argument.

The constraint the package declares for the core is the axis every answer below
turns on. The installation the review runs against supplies one point in it. A
finding you establish there holds for that point. The base's first step
discharges `typo3_project_describe`, because its answer already carries that
constraint and the commands this repository declares.

## Whether the change is right where it lands

The changed paths are the argument, not the subject. Pass them to
`typo3_hint_lookup`, one call per subsystem the diff touches. Do that before you
form a view of whether the code is right. A query that describes the review
reaches nothing.

The hints match by path and by subsystem. A sentence about how you judge a
change comes back as the index of ids it did not return.

Enumerate what the diff **removes or renames** before you ask. A package has its
own public surface, and it is not only PHP. It is a TCA field, a TypoScript
path, a Fluid partial or section, a ViewHelper argument, a label key. It is a
signature somebody else's template or override calls.

That is the class of finding this review exists for. A read of the new code does
not surface it. The evidence is in what is gone.

Whether a member that stays is public API, and what its signature is, is the
checkout's answer, not a lookup's. That reading belongs in the base's step after
the lookups. A finding built on it names the version you read it at.

## Whether it holds on every version the package declares

A change you confirm against the installation holds on one of the declared
majors. You settle the others by reading. The procedure exists whole rather than
as sections a search would cut it into:

- `typo3_rule_lookup` with
  `documentId="extension/compatibility/a-declared-major-that-is-not-installed"`.
  It says which majors the question is about, what the changelog settles and
  where it stops. It gives the invocations that read one symbol off the branch
  that carries the other major. The question is per symbol rather than per
  package.
- `typo3_rule_lookup` with
  `documentId="extension/compatibility/running-on-a-declared-major-that-is-not-installed"`.
  That is for a claim you have to run rather than read. It says what the
  repository's own pipeline already covers. It says how a Composer root of its
  own stands the other major up beside the installation.

`typo3_changelog_lookup` narrows that question and does not close it. It answers
change events. So a member added later to a class that was already there leaves
no entry. Neither does a widened signature.

It also reads the changelog off the installed core. So a bare clone of the
package under review has none to read. The answer then says it could not answer
there, which is not the same as an empty one. Say which of the two you got.

`typo3_documentation_lookup` with `targetVersion` per declared major answers
"does this still work there" for a documented surface. The silence of a
changelog does not.

## Whether a core API already does what the change hand-rolls

The conventions lookup above answers most of this by itself. Where the core has
an API for the construct, the hints for that subsystem carry it and its majors.
A change that reaches past it is the finding.

Two things settle the rest, and neither is a recollection:

- **Sweep the package and the installed core for the call sites before you
  propose an alternative.** Whether an idiom has a foothold is a count, not a
  taste, and no lookup here holds it. The answer is the call sites at their
  paths and lines, and one of them is a coincidence.
- `typo3_system_extension_lookup` before you recommend an API that lives in
  another extension. It says whether that extension is part of the core on the
  majors the package declares. Where it is not, the recommendation adds a
  dependency, and the finding has to say that.

## The commit message, against the convention this repository writes

Which convention that is comes out of the repository's own log, which this
server does not read. Establish it before you check anything.

Where the repository writes TYPO3 keywords, call `typo3_commit_message_guide`
with `workflow="project"` and the message under review. It says whether the
subject, its length and the body wrap hold. Where the repository writes another
convention, that guide answers about TYPO3's instead. It reports a subject
without a keyword as an error. To pass that on is a finding about a repository
nobody reviews. Say which convention you judged the message against.

## What is green, and what that is worth

Whether the branch merges and whether the pipeline passed are the forge's
answers. Read them there and report them as read. Then run what this repository
declares as checks. The base's first step marked each declared command a check,
a change or unknown.

A check hands the code back as it was, so a review told to change nothing runs
it. A green pipeline covers the entries its own commands cover and says nothing
about the ones it has none for. **Write out, by name, the checks you did not
run.**

## Report

Order by what stops the merge, and say why each one stops it:

1. what blocks the merge;
2. what the maintainer would send it back for;
3. what is worth a change and would not block it;
4. what you checked and found correct, briefly, so a reader can tell a silent
   surface from a verified one;
5. what you raised while you read and dropped, with what dropped it.

Every finding names the changed path it is about. A statement about the package
that ties to no line in this diff belongs in an issue. Close on the checklist's
surfaces with each one marked assessed, unassessed or not applicable to this
diff. A reader cannot tell a review that reports only findings from one that
looked at less.

**The report is markdown the reader can copy, and the answer is where it goes.**
Everything this section asks for makes it long, and length makes the form
matter. Somebody carries a review into a thread, an issue or a chat, and
rendered output does not survive the move. Write it to a file only where the
caller asks for one, at a path outside the checkout under review. A modified or
untracked file beside the change is a surface this review reports on.

## Where the review ends

**When the user asks you to make the change, invoke `typo3-extension-health` and
work from it.** That includes the commit. An instruction to change the package
asks for it: "fix it", "do the first three", "push that".

It arrives in the middle of a session that goes well. That makes it easy to
carry on under review rules while you rewrite what the review was about. The
same skill takes the request when it widens past this change. That is "audit the
package", "what else is wrong in here".

It owns the surface list you read a whole repository against. To run that list
on one diff is what this workflow exists not to do. What crosses over either way
is the paths and what you already established about them.

**A remark about a finding's weight is not that instruction.** "That one blocks
it", "are you sure", "I would reject it for that": each reaffirms a finding and
commissions nothing. Where the sentence could be either, ask which the user
meant.

This skill owns the judgement of one change proposed against a package that is
not the core. It stops at the verdict. The repository around that change belongs
to `typo3-extension-health`. That is what else is wrong with it, and the
committed changes that answer a finding.
