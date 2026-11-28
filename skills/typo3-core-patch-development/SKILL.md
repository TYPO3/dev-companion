---
name: typo3-core-patch-development
description: 'Write a TYPO3 core patch and take it to review: the changelog entry, the project''s checks, the push to Gerrit. Also amending after review and backporting to a release branch.'
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Core Patch Development

This skill covers one change to the TYPO3 core, from the issue to a patch
somebody can review. That is the change, its tests, its changelog entry, its
checks, its commit message and its push. It also covers the amend after a review
and the backport to a release branch. It stops before a judgement of a patch,
which `typo3-core-patch-review` covers. It also stops before a change to an
extension, a sitepackage or a site project.

Keep this skill as routing and the order of the work. The suites, the scripts,
the contribution rules and the Gerrit commands come from the lookups. A copy of
them here goes out of date in somebody else's checkout, and nothing reports it.

## Steps at a glance

1. Establish the issue: what it reports, what it asks for, and whether somebody
   already fixed it.
2. Decide what kind of change this is and how far it reaches.
3. Stop where the finding is a vulnerability.
4. Make the change.
5. Verify it with the project's own commands.
6. Write the changelog entry the change needs.
7. Commit, then push when the user asks.
8. Invoke the review on the final diff.
9. Amend the patch after the review.

## Establish the issue before you believe it

1. Work through [references/base.md](references/base.md) first. It fixes the
   order every task starts in.

2. Call `typo3_rule_lookup` for what this kind of change must include. A bugfix,
   a feature, a deprecation and a removal each have different obligations. The
   kind decides the changelog entry, the commit subject and the target branch.

3. Call `typo3_forge_lookup` with the issue number. Read the answer as a report,
   not as a specification. An issue can be stale or half fixed. It can be right
   about the symptom and wrong about the cause. A maintainer's comment can be a
   product judgement rather than a fact about the API.

   The answer has three parts that the issue description does not have:

   - The **status and target version as they stand today**. A closure or a
     reassignment shows there, without a change to the report.

   - The **relations**. They link directly to the change that introduced the
     behaviour the issue complains about. A search on the issue's words often
     does not find that change.

   - The **notes**, where a maintainer said why.

   From them, establish three things before you write code. What the reporter
   saw, what the branch does today, and what the project intends the API for.
   Then do these three reads, and write what each one produces into the
   assessment:

   - **Read the closure reason and the target version for what the conversation
     decided. Write that down rather than what the report is worth.** A closure
     for lack of feedback after a long silence has two readings. The reporter
     could not use the answer, or the reporter gave up. A target version says on
     which branch a fix was still expected.

     Say what the closure settles and what it leaves open. A closed issue is
     not a finding that the need is absent.

   - **Where a comment names an alternative, write out what the alternative
     drops against what the reported code did.** Name the arguments and the
     behaviour the reported code had and the replacement does not. An
     alternative closes an issue only if it does the same work. What it drops is
     usually the capability the reporter wanted.

   - **Enumerate the points the issue requires, the ones only a comment names
     included.** A subject often names two things while the comments name three.
     Then the comments are the list. One patch covers all of them, or each point
     it leaves gets its own issue here, before any code. Each split part needs
     an issue number. The `Resolves:` trailer and the changelog file name each
     take one.

     A point that is riskier to change is an argument for an issue of its own,
     not for a drop. `typo3_issue_report_guide` drafts that issue. It also
     searches the tracker for duplicates and returns the filled-in form. The
     user submits the form, because that needs an account and this server has
     none.

4. Call `typo3_gerrit_lookup` with the same issue number, **before any code
   exists**. The answer can cancel the work, and it costs one call.

   An empty answer is a result, but a narrow one. The server reads the review
   server without a login. So the answer says that nothing public names the
   issue, not that nobody has fixed it. A change pushed unlisted is invisible to
   it.

5. **Verify in the checkout every rule the issue quotes.** A rule about what an
   API is for is a claim, the way a path or an identifier is. Read the class it
   names, its docblock and the core's own tests for the form in dispute. Then
   say which of the three states the rule.

   Code that enforces a rule, a warning that a form is fragile, and advice in
   prose are three different strengths. Two APIs side by side often differ in
   this. Repeat the rule at the strength its own source gives it. "May change in
   a future version" is not "must not". Do not reject the patch because you read
   the first as the second.

6. **Reproduce against the branch you fix**, not against the version in the
   report. Often half of what a stale issue describes is gone. The patch fixes
   the half that remains.

   Call `typo3_test_run_guide` with the paths you are about to change. It names
   the suites that can fail on them. A change to backend markup, a build step or
   shipped JavaScript may have no suite that can show the bug.

   - **Where a suite can show it, write the test first.** Show that it fails
     before the fix and passes after it, in that order. A test written after the
     fix only asserts what the code now does.

   - **Where no suite can show it, reproduce by hand.** Write down the steps and
     what you saw, because a reviewer repeats them. A claim nobody can reproduce
     sends a patch back, whether or not it is right.

7. **Where the issue defers the work because something is missing, check that
   blocker against what the branch has today.** Examples are "it needs an event
   that does not exist" or "there is no API for this yet". The reason was true
   on the day somebody wrote it. The branch may have changed since. The issue
   text looks the same whether the reason still holds or not.

## Decide what kind of change this is

**The argument for a bugfix is an inconsistency inside one version.** "The
branch handles the same input one way here and another way there" is a defect a
reviewer can act on. "This would be better if it also did that" is a wish.
Agreement with the wish does not make it a bug.

So look for a place where the system already does the right thing. If you find
one, the wish becomes a bugfix. If you find none, that is also an answer. The
change is a feature, and step 2 of the issue section already told you what a
feature must include.

**Establish the blast radius here, before you start the work.** The blast radius
is how much behaviour that the suites already test has to change with the patch.
Count it. That decides the kind of change. It is a quiet bugfix, a change that
has to announce itself, or a breaking one. The kind decides the target branch,
the commit subject and the changelog entry. If you count it only while you work,
you have already described the change. Then you must correct that description.

**Then establish who may already extend what you are about to edit.** No suite
in the checkout answers that. A green run says that no core class overrides the
method. It does not say that no extension does. Your change may touch the
declaration of a public or protected member: a parameter, a type, a visibility
or `final`. Call `typo3_hint_lookup` for the id `public-api-surface` before you
write it. The answer decides the target branch. A fix for a maintained release
line must know it first.

## Where the finding is a vulnerability

**Ask whether the finding is a vulnerability once you have reproduced it.** Ask
it before you write any code. Ask it of every finding, not only when a defect
happens to look alarming. This workflow ends in a push, and a push is
publication. The diff, the test that proves the defect and the commit message
become public. Without this question, the fix discloses the defect it fixes.

The stopping point is the verified reproduction. Do nothing after it here:

- no patch;
- no pushed test;
- no changelog entry;
- no comment on the issue.

Call `typo3_rule_lookup` with
`documentId="any/security/reporting-a-vulnerability"` instead. That is the whole
procedure. The server also serves it as
`typo3://guides/any/security/reporting-a-vulnerability`. Read the contact
address there, never from this file. A contact address can change, and no
release of this server corrects this copy.

Ask the question again wherever the work turns into a vulnerability fix. The
issue rarely says so. A fix whose real effect is that a restriction now applies
is this case under another name. Where a change is already up for review, an
amend takes nothing back, because every patch set stays fetchable. So name what
is already public in the report, and do not try to repair it.

## Make the change

**Ask `typo3_hint_lookup` for the conventions of each subsystem you touch,
before you write.** A convention you fetch afterwards only confirms what you
wrote. Ask by id for the hints that `omittedHints` in the brief names. Ask with
paths only for a path the brief did not see. The same paths asked again return
the same hints.

**Where the change touches a source below `Build/Sources/`, the generated file
beside it is part of the patch.** Call `typo3_rule_lookup` with
`documentId="core/contribution/committed-build-output"`. It says which source
produces which committed file. It says how to rebuild one file without risk to
the rest of the tree. It also says what to do with a backport that has conflict
markers in a built file.

**Keep the patch one change.** Anything else you noticed is another issue and
another patch. A reviewer must accept or reject a diff as one, even when it
fixes two things.

That narrows the work and never the points the issue lists. You settled those in
the assessment. All of them are in this patch, or the others already have their
own issues. A point you drop here disappears without a trace. `Resolves:` closes
the issue on every point it names, and nobody reopens a closed one.

**Where the request widens after the patch is under way, re-establish three
things. That is what kind of change this is now, which branches it reaches, and
what it must include.** Do it before you write the widened part, and say which
of the three moved.

The rule lookup in step 2 of the issue section settled the first. The blast
radius settled the second, and the changelog entry the third. Each was settled
for the narrower request. If you only continue, none of them is decided again. A
change that gains a second subsystem also gains that subsystem's build, its
checks and its backport constraint.

The same applies where the fix moves to another system extension. Run the
deprecation sweep of the base order again, with the `ext:` tag of the extension
the change touches now. A sweep for the first extension says nothing about what
the second one deprecated.

**Find out whether the area moves before you build on it.** Fetch and rebase
onto the branch you target before you finish. Code can change under a patch.
Then the method it called can be gone, and with it the reason the change looked
right.

## Verify with the project's own commands

Call `typo3_test_run_guide` with the changed paths. It returns the suites that
can fail on this change, each with its targeted command. Call
`typo3_script_lookup` for the scripts around them. Run the narrow ones while you
work and the broad ones before you push.

Two rules decide whether a run means anything:

- **Run through the project's own runner.** A suite run through the host's PHP
  or a globally installed binary is a result nobody can reproduce.

- **A check that passed on no files proves nothing.** Where a check reports
  success, confirm that it inspected something. Look for the count of files,
  tests or fixtures it names. A check that found nothing to check reports
  success, and a reviewer does not notice the failure.

## Write the changelog entry

Call `typo3_rule_lookup` with `documentId="core/contribution/changelog"`. The
server also serves it as `typo3://guides/core/contribution/changelog`. It says
which of the four types of entry the change needs. It also says the release
directory, the file name and what checks the file.

Decide the type from what the change does, not from habit. An entry for a change
that needs none is as much a review finding as a missing entry.

Write the file into the `<lts>.x` directory of the oldest branch the `Releases:`
trailer names. Where two maintained lines take the change, write it into both
`.x` directories. The branches the patch reaches decide this. The branch you
write the patch on decides nothing.

A change can also make a sentence in a manual false. The usual case is a change
to what a core ViewHelper renders. The Fluid ViewHelper Reference documents each
one. `typo3_documentation_lookup` finds the page. What the patch must do for
that manual is in `typo3_rule_lookup` with
`documentId="core/contribution/rules"`, section "Documentation".

## Commit and push

**Check the message with `typo3_commit_message_guide`.** Pass `workflow="core"`,
the drafted message and the change type. It reports what is still wrong before
the hook does. Always state the workflow. The default is a repository of your
own, which needs neither the Forge issue nor the `Releases:` trailer.

The rules behind it are one page: `typo3_rule_lookup` with
`documentId="core/contribution/commit-messages"`. The server also serves it as
`typo3://guides/core/contribution/commit-messages`. It gives the subject, the
trailers and the release targets. Read it once, rather than learn each rule from
a separate failed check.

**Read the pre-commit hook's output before you amend because of it.**
`git commit` runs the core's pre-commit hook on the host. The hook reads the
whole working directory, ignored folders included. It does not stop on what it
finds. So worktrees or caches in the checkout make it slow. They also make it
report paths the commit does not contain. `typo3_rule_lookup` with
`documentId="core/testing/scripts"`, section "The Pre-Commit Hook", says how to
read it.

**Read the Gerrit workflow page whole before the first push.** Call
`typo3_rule_lookup` with `documentId="core/contribution/gerrit-workflow"`. The
server also serves it as `typo3://guides/core/contribution/gerrit-workflow`. It
says what the push is. It says how to amend a change into a new patch set rather
than a second commit. It says what must stay the same between patch sets. A
search returns only the section your words matched, and this step needs several
sections.

**Establish where you push to.** A core checkout can push to another remote than
the one it fetches from. The checkout's git configuration says which, not the
repository's name.

### Rebase where the branch moved

A commit that waited while you verified it is behind `origin/main`. Rebase it as
part of the push. Two rules apply:

- **First stop any `runTests.sh` suite that still runs.** The script reads the
  tree while it runs. A rebase under a running suite makes its result invalid
  without a failure. Remove the suite's leftover containers before you start.

- **Confirm that the `Change-Id` survived the rebase.** The `Change-Id` makes
  the push a new patch set on your existing change. Without it, the push opens a
  second change, and another push does not undo that.

Then run the checks again on the new base. Where the commits you rebased over
touch the same files, read them. A suite that passed before the rebase only says
something about the old base.

### A change that is already on the review server

**Where the commit to change is a patch set on the review server, invoke
`typo3-core-patch-checkout` for that one change.** Then work from the copy it
leaves. That applies to somebody else's change you take over. It also applies to
your own change where this checkout no longer has it. That skill gets the patch
set into the checkout and puts the checkout back afterwards. It hands back the
working copy, the patch set it fetched, and whether it had to rebase the patch.

Your own change that this checkout still holds needs no handover. That is the
case when the tree is clean and `git cat-file -e <commit>` finds the commit.
`typo3_gerrit_lookup` gives that commit for the current patch set. Then
`git switch -c review/<change number> <commit>` is the whole step, and the work
continues here.

### The push

**Push only when the user asks for it.** Everything before the push is local and
can be undone. The push is public and cannot.

**Before you ask for the push, end your answer with each step of the base order
you did not reach.** Give each one a line: `Not reached: <step> — <why>`. The
user decides about the push from that list. A walk through the base in an
earlier review does not replace a step.

**Ask every time whether the change goes up visible to everyone or unlisted.**
The two use different refspecs. A visible push publishes the change to everyone
who watches the project and notifies the reviewers. Nothing undoes the
publication or the notification. The Gerrit workflow page gives both forms. The
choice is the user's, never a default taken from what the session did last.

## Where the patch is finished and the review begins

**When the checks pass and you have written the commit, invoke
`typo3-core-patch-review` on the diff.** Do that before you push or hand the
patch over. Load the skill by name and work from it. Hand over the diff, the
target branch, the change type and what the checks reported. Take its findings
back as a work list.

A review of the same change earlier in this session does not take its place. The
final diff is new, and the earlier findings are about the old one. Where the
review skill is already loaded, do not load it again. Work through its checklist
and its report section on the final diff.

## Amend after review

A patch that came back is the same change, not a new one. Fetch the patch set
that exists and amend it. Keep the `Change-Id` that links it to its review. The
Gerrit workflow page says how to do each of these.

**Answer every review comment.** Address it in the patch or answer it in the
review. Never ignore one, because an unanswered comment keeps a change unmerged.
The answer is a reply in the comment's own thread, and the reply decides whether
the thread stays open. The Gerrit workflow page gives the details under "Answer
a Review Comment". Post a reply only when the user asks, because a reply is
public like a push.

Where the change has another author, read
[references/foreign-change.md](references/foreign-change.md) before the first
commit.

## Where this skill stops

- **A judgement of a patch**, somebody else's or your own, is
  `typo3-core-patch-review`. It reads other sources than this skill does.

- **Getting a patch set into a checkout and out again** is
  `typo3-core-patch-checkout`.

- **A change to an extension, a sitepackage or a site project** belongs to the
  extension skills. Their conventions are not the core's.
