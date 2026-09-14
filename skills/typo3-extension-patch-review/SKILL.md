---
name: typo3-extension-patch-review
description: 'Judge one incoming change against a TYPO3 extension, sitepackage or project package — a GitHub pull request, a patch, a branch somebody proposes — and say what stops it being merged, from its versions to its commit message and its checks. It stops at the verdict; the whole repository is typo3-extension-health''s.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Patch Review

Judge one change proposed against a package that is not the core, and report
what stops it being merged. Keep this skill as routing and review method; the
conventions, the version boundaries and the commit rules are lookups, and a copy
of them here is one that cannot be corrected.

## Establish the change, then the range it is judged in

1. Work through [references/base.md](references/base.md). It fixes the order
   every task here starts in, and a review is where that order decides the
   result: a rule fetched after the diff has been read confirms a reading
   instead of testing it.
2. Read [references/checklist.md](references/checklist.md) for the review
   surfaces, what a finding owes, what a dropped candidate owes, and the
   severity rubric.
3. Establish the change itself. This server reads neither your git state nor the
   forge the change arrived on, and what it needs from you is exactly what the
   change is: **the changed paths**, **the diff**, **the branch it targets**,
   **the commit messages**, and **whether it merges and what the repository's
   own pipeline said**. One reading produces all of them —
   `git fetch <remote> <ref>` and `git diff <base>...<head>` for the first four,
   the forge's own client or its web page for the last — and every lookup below
   takes one of them as its argument.

The constraint the package declares for the core is the axis every answer below
turns on: the installation the review runs against supplies one point in it, and
a finding established there is established for that point. The base's first step
discharges `typo3_project_describe`, because its answer already carries that
constraint and the commands this repository declares.

## Whether the change is right where it lands

The changed paths are the argument, not the subject. Pass them to
`typo3_hint_lookup`, one call per subsystem the diff touches, before forming a
view of whether the code is right. A query describing the review reaches nothing
— the hints are matched by path and by subsystem, and a sentence about judging a
change comes back as the index of ids it did not return.

Enumerate what the diff **removes or renames** before asking. A package has its
own public surface, and it is not only PHP: a TCA field, a TypoScript path, a
Fluid partial or section, a ViewHelper argument, a label key, a signature
somebody else's template or override calls. That is the finding class this
review exists for, and it is the one a reading of the new code does not surface
— the evidence is in what is gone.

Whether a member that stays is public API, and what its signature really is, is
the checkout's answer rather than a lookup's. The base's step after the lookups
is where that reading belongs, and a finding built on it names the version it
was read at.

## Whether it holds on every version the package declares

A change confirmed against the installation is confirmed on one of the declared
majors. The others are settled by reading, and the procedure exists whole rather
than as sections a search would cut it into:

- `typo3_rule_lookup` with
  `documentId="extension/compatibility/a-declared-major-that-is-not-installed"`
  — which majors the question is about, what the changelog settles and where it
  stops, and the invocations that read one symbol off the branch carrying the
  other major. The question is per symbol rather than per package.
- `typo3_rule_lookup` with
  `documentId="extension/compatibility/running-on-a-declared-major-that-is-not-installed"`
  — for a claim that has to be run rather than read: what the repository's own
  pipeline already covers, and the Composer root of its own that stands the
  other major up beside the installation.

`typo3_changelog_lookup` narrows that question and does not close it. It answers
change events, so a member added later to a class that was already there leaves
no entry, and neither does a signature that was widened. It also reads the
changelog off the installed core, so a bare clone of the package under review
has none to read: the answer says it could not be answered there, which is not
the same as an empty one. Say which of the two you got.

`typo3_documentation_lookup` with `targetVersion` per declared major is what
answers "does this still work there" for a documented surface — the silence of a
changelog does not.

## Whether a core API already does what the change hand-rolls

The conventions lookup above answers most of this by itself: where the core has
an API for the construct, the hints for that subsystem carry it with the majors
each form holds on, and a change that reaches past it is the finding.

Two things settle the rest, and neither is a recollection:

- **Sweep the package and the installed core for the call sites before proposing
  an alternative.** Whether an idiom is established is a count, not a taste, and
  no lookup here holds it: the answer is the call sites at their paths and
  lines, and one of them is a coincidence.
- `typo3_system_extension_lookup` before recommending an API that lives in
  another extension: it says whether that extension is part of the core on the
  majors the package declares. Where it is not, the recommendation adds a
  dependency, and that is what the finding has to say.

## The commit message, against the convention this repository writes

Which convention that is comes out of the repository's own log, which this
server does not read. Establish it before checking anything.

Where the repository writes TYPO3 keywords, `typo3_commit_message_guide` with
`workflow="project"` and the message under review says whether the subject, its
length and the body wrapping hold. Where the repository writes another
convention, that guide answers about TYPO3's instead — it reports a subject
carrying no keyword as an error — and passing that on is a finding about a
repository nobody is reviewing. Say which convention the message was judged
against.

## What is green, and what that is worth

Whether the branch merges and whether the pipeline passed are the forge's
answers, read there and reported as read. Then run what this repository declares
as checks — the base's first step marked each declared command a check, a change
or unknown, and a check hands the code back as it was, so a review told to
change nothing runs it. A green pipeline covers the entries its own commands
cover and says nothing about the ones it has none for. **Write out, by name, the
checks you did not run.**

## Report

Order by what stops the change being merged, and say why each one stops it:

1. what blocks the merge;
2. what the maintainer would send it back for;
3. what is worth changing and would not block it;
4. what was checked and is correct — briefly, so a silent surface and a verified
   one are not read alike;
5. what was raised while reading and dropped, with what dropped it.

Every finding names the changed path it is about. A statement about the package
that does not tie to a line in this diff belongs in an issue, not in a review of
one change. Close on the checklist's surfaces with each one marked assessed,
unassessed or not applicable to this diff: a review that reports only findings
cannot be told apart from one that looked at less.

**The report is markdown the reader can copy, and the answer is where it goes.**
Everything this section asks for is what makes it long, and length is what makes
the form matter: a review is carried into a pull request thread, into an issue
or into a chat, and rendered output is what does not survive being moved. Write
it to a file only where the caller asks for one, at a path outside the checkout
under review — what is modified or untracked beside the change is a surface this
review reports on.

## Where the review ends

**When you are asked to make the change, invoke `typo3-extension-health` and
work from it.** That includes the commit. What asks for it is an instruction to
change the package — "fix it", "do the first three", "push that" — and it
arrives in the middle of a session that is going well, which is what makes it
easy to carry on under review rules while rewriting what the review was about.
The same skill is where the request goes when it widens past this change —
"audit the package", "what else is wrong in here" — because it owns the surface
list a whole repository is read against, and running that list on one diff is
what this workflow exists not to do. What crosses over either way is the paths
and what was already established about them.

**A remark about a finding's weight is not that instruction.** "That one blocks
it", "are you sure", "I would reject it for that" — each reaffirms a finding and
commissions nothing. Where the sentence could be either, ask which was meant.

This skill owns judging one change proposed against a package that is not the
core, and it stops at the verdict. The repository around that change — what else
is wrong with it, and the committed changes that answer a finding — belongs to
`typo3-extension-health`.
