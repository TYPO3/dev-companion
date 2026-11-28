---
name: typo3-extension-patch-review
description: 'Review one incoming change against a TYPO3 extension, sitepackage or project package — a GitHub pull request, a patch, a branch somebody proposes — and say what stops the merge, from its versions to its commit message and its checks. It stops at the verdict. A review of the whole repository, and every change to the package, is typo3-extension-health.'
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Patch Review

This skill covers the review of one change proposed against a TYPO3 extension, a
sitepackage or a project package. The change is a pull request, a patch or a
branch somebody proposes. The review reports what stops the merge, and it stops
at that verdict. A change to the package, and a review of the whole repository,
are `typo3-extension-health`.

Keep this skill as routing and review method. It says which tool to ask, in
which order, and how to judge the answer. The conventions, the version limits
and the commit rules come from the lookups. A copy of them here would go out of
date in somebody else's project, where nobody can correct it.

## Steps at a glance

1. Establish the change and the versions you judge it against.
2. Check whether the change is right where it lands.
3. Check whether it holds on every TYPO3 major the package declares.
4. Check whether a core API already does what the change builds by hand.
5. Check the commit message against the convention of the repository.
6. Read the merge state and the pipeline, and run the declared checks.
7. Write the report.

When the user asks you to change the package, follow "Where the review ends".

## 1. Establish the change

1. Work through [references/base.md](references/base.md) first. It fixes the
   order every task starts in. In a review that order decides the result. A rule
   you fetch after you read the diff confirms your reading instead of testing
   it.

2. Read [references/checklist.md](references/checklist.md). It lists the
   surfaces you review, what a finding needs, what a dropped candidate needs,
   and the severity levels.

3. Establish the change itself. This server reads neither your git state nor the
   forge the change came from. So you supply these five:

   - the changed paths;
   - the diff;
   - the branch it targets;
   - the commit messages;
   - whether it merges, and what the repository's own pipeline reported.

   `git fetch <remote> <ref>` and `git diff <base>...<head>` give the first
   four. The forge's own client or its web page gives the last. Every lookup
   below takes one of them as its argument.

The package declares a constraint on the TYPO3 version, and every answer in this
review depends on it. The base's first step discharges `typo3_project_describe`.
That step already makes the call, so do not make it again. Its answer already
gives that constraint and the commands the repository declares. The installation
you run against is one version inside that constraint. A finding you establish
there holds for that version.

## 2. Whether the change is right where it lands

Call `typo3_hint_lookup` with the changed paths, once per subsystem the diff
touches. Do that before you decide whether the code is right. The hints match by
path and by subsystem. A query that describes the review matches no hint. Its
answer is only the list of ids it did not return.

Before the call, list what the diff **removes or renames**. A package has its
own public surface, and not only in PHP:

- a TCA field;
- a TypoScript path;
- a Fluid partial or section;
- a ViewHelper argument;
- a label key;
- a signature that another package's template or override calls.

This kind of finding is the reason for the review. A read of the new code does
not show it, because the evidence is in what is gone.

Whether a member that stays is public API, and what its signature is, comes from
the checkout. No lookup answers it. Read it in the base's step after the
lookups. A finding built on that read names the version you read it at.

## 3. Whether it holds on every declared major

The installation confirms a change on one of the majors the package declares.
You settle the others by reading. Two procedures say how. Read each one whole,
with its `documentId`, because a search returns only parts of it:

- `typo3_rule_lookup` with
  `documentId="extension/compatibility/a-declared-major-that-is-not-installed"`
  for a claim you can read. It says which majors the question is about, what the
  changelog settles and where it stops. It gives the commands that read one
  symbol from the branch of the other major. Ask the question per symbol, not
  per package.

- `typo3_rule_lookup` with
  `documentId="extension/compatibility/running-on-a-declared-major-that-is-not-installed"`
  for a claim you have to run. It says what the repository's own pipeline
  already covers. It says how a separate Composer root sets up the other major
  beside the installation.

`typo3_changelog_lookup` narrows the question but does not close it:

- It reports changes. A member added later to a class that was already there has
  no entry. A wider signature has none either.

- It reads the changelog of the installed core. A bare clone of the package has
  no core, so there is nothing to read. The answer then says that it could not
  answer, which is not an empty answer. Say which of the two you got.

`typo3_documentation_lookup` with `targetVersion`, once per declared major, says
whether a documented surface still works there. A changelog with no entry does
not say that.

## 4. Whether a core API already does what the change builds by hand

The hint lookup of step 2 answers most of this. Where the core has an API for
the construct, the hints for that subsystem name it and its majors. A change
that goes around that API is the finding.

Two things settle the rest. Neither is something you remember:

- **Search the package and the installed core for call sites before you propose
  an alternative.** Whether an idiom is established is a count, not a taste, and
  no lookup holds that count. Give the call sites with their paths and lines.
  One call site alone does not establish an idiom.

- **Call `typo3_system_extension_lookup` before you recommend an API from
  another extension.** It says whether that extension is part of the core on the
  majors the package declares. Where it is not, the recommendation adds a
  dependency, and the finding must say so.

## 5. The commit message

First find which convention the repository writes. Its own log shows that, and
this server does not read the log. Then:

- **TYPO3 keywords**, such as `[TASK]`: call `typo3_commit_message_guide` with
  `workflow="project"` and the message under review. It says whether the
  subject, its length and the body wrap are correct.

- **Another convention**: do not pass on what that guide says. It answers for
  the TYPO3 convention and reports a subject without a keyword as an error. That
  error is about a convention the repository does not use.

Say which convention you judged the message against.

## 6. The merge state and the checks

Whether the branch merges and whether the pipeline passed come from the forge.
Read them there and report them as you read them.

Then run the checks this repository declares. The base's first step marked each
declared command as check, change or unknown. A check leaves the code as it was,
so run it even in a review told to change nothing.

A green pipeline covers what its own commands check, and nothing else. **List by
name the checks you did not run.**

## 7. Report

**Write the report in the skeleton of
[references/report.md](references/report.md), every heading in its order and
every entry in its line form.** The three review skills use that skeleton, so
two reports compare section by section.

- Order the findings by what stops the merge, and say why each one stops it.

- List what you checked and found correct.

- List the points you raised while you read and then dropped, each with what
  dropped it.

- Name the changed path each finding is about. A statement about the package
  that ties to no line of this diff belongs in an issue.

- Close on the surfaces of the checklist. Mark each one assessed, unassessed or
  not applicable to this diff. Without that, a reader cannot tell a review with
  few findings from a review that looked at less.

**The report is markdown the reader can copy, and the answer is where it goes.**
Somebody copies a review into a thread, an issue or a chat, and rendered output
does not survive the move. So put the whole report in one fenced `markdown`
block, with a fence of four backticks. A client that renders the answer copies
it as rendered text, and the fence keeps the source. Write it to a file only
where the caller asks for one, at a path outside the checkout under review. A
new or changed file beside the change is a surface this review reports on.

**A document the caller asks for is this report, in this order and with every
part above.** A summary in a shape of its own drops the ranks, the paths, the
dropped candidates and the surfaces. A reader checks a review by those.

## Where the review ends

**When the user asks you to make the change, invoke `typo3-extension-health` and
work from it.** That includes the commit. Every instruction to change the
package is that request, such as "fix it", "do the first three" or "push that".
It comes in the middle of a session that goes well. So it is easy to keep the
review rules while you rewrite what you reviewed.

Invoke the same skill when the request goes past this change, such as "audit the
package" or "what else is wrong in here". That skill has the list of surfaces
for a whole repository. This skill does not run that list on one diff. Hand over
the paths and what you already established about them.

**A remark about how serious a finding is asks for no change.** "That one blocks
it", "are you sure" and "I would reject it for that" each confirm a finding.
Where a sentence could be either, ask the user which one they meant.
