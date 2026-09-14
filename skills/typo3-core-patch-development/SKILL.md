---
name: typo3-core-patch-development
description: 'Write a TYPO3 core patch and carry it to review: the changelog entry, the project''s checks, the push to Gerrit. Also amending after review and backporting to a release branch.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Core Patch Development

Carry one change from an issue to a patch somebody can review. Keep this skill
as routing and working order. The suites, the scripts, the contribution rules
and the Gerrit commands are lookups. A copy of them here goes stale in somebody
else's checkout, and nothing reports it.

## Establish the issue before you believe it

1. Work through [references/base.md](references/base.md), which fixes the order
   every task here starts in.
2. `typo3_rule_lookup` for what this kind of change owes. A bugfix, a feature, a
   deprecation and a removal are four different sets of obligations. Which one
   you are in decides the changelog entry, the commit subject and the target
   branch.
3. `typo3_forge_lookup` with the issue number. Read what comes back as a report
   rather than as a specification. An issue can be stale, half fixed, or right
   about the symptom and wrong about the cause. The maintainers' comments on it
   can be product judgement rather than an API fact.

   Three parts of that answer are not in the description a session otherwise
   starts from:

   - The **status and target version as they stand today**. That is where a
     closure or a reassignment shows without a rewrite of the report.
   - The **relations**. They are one hop from the change that introduced the
     behaviour under complaint. They reach it where a query on the wording does
     not.
   - The **notes**, where a maintainer said why.

   Establish which of those you have before you write code. That is what the
   reporter saw, what the branch does today, and what the project intends the
   API for. Three parts of that reading are acts. What they produce goes into
   the assessment before any code:
   - **Read the closure reason and the target version for what the conversation
     decided. Write that down rather than what the report is worth.** A closure
     for lack of feedback after a long silence has two readings. The reporter
     could not use the answer, or the reporter gave up. A target version says
     which branch a fix was still expected on.

     Say what the closure settles and what it leaves open. A closed issue is not a finding that the need is absent.
   - **Where a comment names an alternative, write out what the alternative
     drops against what the reported code did.** Name the arguments and the
     behaviour the reported code had and the replacement does not. An
     alternative closes an issue only if it does the same work. What it drops is
     usually the capability the reporter was reaching for.
   - **Enumerate the points the issue requires, the ones only a comment names
     included.** A subject that names two things over comments that name three
     is the ordinary case. The comment is the list. One patch covers all of
     them, or each point it leaves gets its own issue here, before any code. A
     split part needs a number. The `Resolves:` trailer and the changelog file
     name each take one.

     A point that is riskier to change is an argument for an issue of its own, not for a drop. What that issue carries — the tracker, the fields, the markup its description renders as — is `typo3_rule_lookup` with `documentId="core/contribution/reporting-an-issue"`. The user files it, because that takes an account and this server holds none.

4. `typo3_gerrit_lookup` with the same issue number, **before any code exists**.
   Its cheapest outcome is the one that cancels the work, and it costs one call.
   An answer of nothing is a result, and a narrow one. The server reads the
   review server without a credential. So the answer says that nothing public
   names the issue, not that nobody has fixed it. A change pushed unlisted is
   invisible to it.

5. **Verify in the checkout every rule the issue quotes.** A rule about what an
   API is for is a claim, the way a path or an identifier is. Read the class it
   names, its docblock and the core's own tests for the form under dispute. Read
   them and say which of the three carries the rule.

   Enforced in code, warned about as fragile and advised in prose are three
   different claims. Two neighbouring APIs regularly make different ones. Carry
   it at the strength its own source puts on it. An assessment that hardens "may
   change in a future version" into "must not" argues the patch away on nothing.

6. **Reproduce against the branch you fix**, not against the version in the
   report. Half of what a stale issue describes is usually gone, and the half
   that remains is the patch.

Whether that reproduction can be a test is a property of what you change.
`typo3_test_run_guide` with the paths you are about to touch says so. It names
the suites that can fail on them. A change to backend markup, a build step or
shipped JavaScript may have none that can hold the bug.

Where there is a layer, write the test first. Prove that it fails before the fix
and passes after it, in that order. A test written afterwards asserts what the
code now does, which is true of any code. Where there is none, reproduce by
hand. Write down the steps and what you saw, because that is what a reviewer
repeats. An unreproducible claim sends a patch back whether or not it is right.

A reason not to do something has a date, and the API it rested on does not. The
issue may carry a decision to defer. It needs an event that does not exist, or
there is no API for this yet. Check that blocker against what the branch has
today before you treat it as standing. An expired objection uses the same words
as one that still holds, and nothing in the notes separates them.

The argument that carries a bugfix is the same inconsistency inside one version.
"The branch handles the same input one way here and another there" is a defect a
reviewer can act on. "This would be better if it also did that" is a wish.
Agreement with it does not make it a bug.

To find the place where the system already does the right thing turns the second
into the first. To find none is an answer as well. It says the change is a
feature, and step 2 has already priced what that owes.

Establish the blast radius here rather than meet it while you work. How much of
the behaviour the suites already pin down has to change with it decides the
kind. It is a quiet bugfix, a change that has to announce itself, or a breaking
one. That decision sits upstream of the target branch, the commit subject and
the entry. Discovered step by step, it arrives after you have characterised the
change. Then you have to take the characterisation back.

Its other half is who may already extend what you are about to edit. No suite in
the checkout answers it. A green run says no core class overrides the method,
never that no extension does. The shape you have in mind may touch a public or
protected member's declaration. That is a parameter, a type, a visibility, a
`final`. Settle what that commits the patch to before you write it.

Use `typo3_hint_lookup` for the id `public-api-surface`. It decides the target
branch, so a fix you owe to a maintained release line has to know it first.

## Where the finding is a vulnerability

**Ask it once the reproduction stands and before you write any code, not when a
defect happens to look alarming.** This workflow ends in a push, and a push is
publication. It puts the diff, the test that proves the defect and a message
about both in front of every reader. Nothing else in this order asks the
question, so the fix would disclose the defect it fixes.

The stopping point is the verified reproduction. It stands, and you do nothing
after it here. No patch, no test pushed, no entry written, no comment on the
issue.

Where it goes instead is `typo3_rule_lookup` with
`documentId="any/security/reporting-a-vulnerability"`. That is the whole
procedure, and it also stands as
`typo3://guides/any/security/reporting-a-vulnerability`. Read the address there
and never from here. A contact route is the fact that moves, and this file is a
copy no release of this server corrects.

Ask it again wherever the work turns into one, because the issue rarely says so.
A fix whose real effect is that a restriction now holds is this case under
another name. Where something is already up for review, an amend takes nothing
back. Every patch set stays fetchable. So name what is public in the report
rather than repair it.

## Make the change

Ask `typo3_hint_lookup` with the concrete paths for the conventions of each
subsystem you touch, before you write rather than after. A convention you fetch
afterwards confirms what you already wrote.

Where the change touches a source below `Build/Sources/`, the generated file
beside it is part of the patch. `typo3_rule_lookup` with
`documentId="core/contribution/committed-build-output"` says which source
produces which committed file. It says how you rebuild one without risk to the
rest of the tree. It says what a backport that came back with conflict markers
in it needs.

Keep the patch one change. What else you noticed is another issue and another
patch. A diff that fixes two things is a diff a reviewer has to accept or reject
as one.

That narrows the work and never the points the issue lists. You settled those
while you assessed it. All of them are in this patch, or the ones that are not
already have issues of their own. A point you drop here drops invisibly.
`Resolves:` closes the issue on every point it names, and nobody reopens a
closed one.

Where the request widens after the patch is under way, re-establish three
things. That is what kind of change this is now, which branches it reaches, and
what it owes. Do it before you write the widened part, and say which of the
three moved.

Step 2 settled the first, the blast radius the second and the changelog section
the third. Each did so against the narrower request. To carry on derives none of
them again. A change that gains a second subsystem gains that subsystem's build,
its checks and its backport constraint with it.

Find out whether the area moves before you build on it. Fetch and rebase onto
the branch you target before you finalise. A patch written against code that
changed underneath it is not a patch that needs adjustment. The method it called
can be gone, and with it the reason the change looked right.

## Verify with the project's own commands

`typo3_test_run_guide` with the changed paths returns the suites that can fail
on this change, each with its targeted invocation. `typo3_script_lookup` returns
the scripts around them. Run the narrow ones while you iterate and the broad
ones before you push.

Two things decide whether that verification means anything:

- The runner is the project's. A suite run through the host's own PHP or an
  installed binary is a green nobody can reproduce.
- A green that ran over no files is not a green. Where a check reports success,
  confirm that it inspected something: the count of files, tests or fixtures it
  names. Do that before you treat it as evidence. A check that found nothing to
  check without a word is the failure mode that survives review.

## The changelog entry the change owes

The procedure is one page: `typo3_rule_lookup` with
`documentId="core/contribution/changelog"`, which also stands as
`typo3://guides/core/contribution/changelog`. It says which of the four types
the change owes and which release directory the file goes into. It says what its
name is and what checks it.

Decide the type from what the change does rather than from habit. An entry for a
change that owes none is as much a review finding as a missing one. Write the
file into the `<lts>.x` directory of the oldest branch the `Releases:` trailer
names. Write it into both `.x` directories where two maintained lines take the
change. The branches the patch reaches decide that. The branch you write it on
decides nothing.

## Commit and push

`typo3_commit_message_guide` with `workflow="core"`, the drafted message and the
change type reports what is still wrong. It does so before the hook does. State
the workflow. Its default is a repository of your own. That demands neither the
Forge issue nor the `Releases:` trailer a patch here owes.

The rules behind it are one page: `typo3_rule_lookup` with
`documentId="core/contribution/commit-messages"`, which also stands as
`typo3://guides/core/contribution/commit-messages`. It says the subject, the
trailers, the release targets and the changelog entry the change type owes. One
read of it here is cheaper than the checks that teach it one call at a time.

Then the Gerrit workflow. It says what the push is. It says how you amend a
change into a new patch set rather than a second commit. It says what you must
not edit between patch sets. That procedure exists whole as `typo3_rule_lookup`
with `documentId="core/contribution/gerrit-workflow"`, which also stands as
`typo3://guides/core/contribution/gerrit-workflow`. Read it before the first
push rather than a section at a time. A search returns the part your words
matched, and everything below here is a different part of the same page.

Before you push, establish where you push to. A core checkout's remote is not
necessarily the one it fetches from. The answer is in the checkout's own git
configuration rather than in the repository's name.

### Rebase where the branch moved under you

A commit that sat while you verified it is behind `origin/main`. Its rebase is
part of the push rather than a thing of its own. Two parts of that are not
obvious. A session with no skill that told it either worked both out from
scratch:

- **Stop a running `runTests.sh` suite first.** The script mounts the tree and
  reads it as it goes. So a rebase underneath a run invalidates it without a
  failure. The run then reports about a tree that no longer exists. Clear the
  suite's leftover containers before you start.
- **Confirm that the `Change-Id` survived the rebase.** It makes the push a new
  patch set on the change you already have. Without it you open a second change
  instead, and another push does not undo that.

Then run the checks again on the new base. Inspect the commits you rebased over
where any of them touch the same files. A suite that passed before the rebase is
evidence about the old base.

**Where the commit to change is a patch set on the review server, invoke
`typo3-core-patch-checkout` for that one change.** Work from the copy it leaves.
Somebody else's change you pick up to finish arrives that way. So does your own
where this checkout no longer holds it. That workflow's whole subject is the
ref, the remote, and which of the three destinations the patch goes to. It is
also how you put the checkout back afterwards.

What crosses back here is the working copy the patch sits in and the patch set
you fetched. It is also whether you had to carry the patch onto current code to
apply.

The push is a step of its own, and you take it when the user asks for it.
Everything above is local and reversible. The push is neither.

**Ask whether the change goes up visible to everyone or unlisted, every time.**
The two are different refspecs, and the difference is not a preference. One
publishes the change to whoever watches the project and notifies reviewers.
Nothing quietly undoes the publication or the notification. `typo3_rule_lookup`
for the Gerrit workflow has both forms. Which one this change wants is the
user's decision and never a default read off what the session did last.

## Where the patch is finished and the review begins

**When the checks pass and you have written the commit, invoke
`typo3-core-patch-review` on the diff.** Do that before you push or hand the
patch over. That is a step, not a note about ownership. Load the skill by name
and work from it. What crosses over is the diff, the branch it targets, the
change type and what the checks reported. What comes back is the work list the
paragraph below already says to take it as.

It stands as a step because the ownership sentence did not fire as one. A
session finished a push-ready patch here and ran the project's checks. The patch
was three files, two functional tests, a commit message. It reported the patch
and never opened the review. That was twenty turns after it invoked this skill
out of a triage whose crossing stands as an act. The act fired and the boundary
did not, in one session on one task.

## Amending after review

A patch that came back is the same change, not a new one. Fetch the patch set
that exists, amend it, and keep the identifier that links it to its review.
`typo3_rule_lookup` for the Gerrit workflow says how you do each of those.
Address a reviewer's comment in the patch or answer it in the review. Never drop
one in silence. A comment nobody replied to is the reason a change sits
unmerged.

Where the change is somebody else's, those steps hold and two more come with
them. Ask the author before the patch set goes up. Say every decision you took
on their behalf in a comment on the change. The diff between two patch sets says
what moved and never why.

The amend leaves them the author and makes you the committer, which is the
intended shape. The same page carries it. Never overwrite the author line,
however much of the patch you wrote. `--reset-author` has no use here.

This skill owns the write and the delivery of a core patch. That is the change
itself, its tests, its changelog entry, and the checks it has to pass. It is its
commit message and its push.

It does not own the judgement of somebody else's patch, and it does not own the
judgement of its own. Where the request is to say what is wrong with a change
rather than to make one, `typo3-core-patch-review` owns that. It reads different
surfaces for it. Take its findings as a work list when it hands them over. A
change to an extension, a sitepackage or a site project belongs to the extension
skills. Their conventions are not the core's.
