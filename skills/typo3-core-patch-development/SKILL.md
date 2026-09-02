---
name: typo3-core-patch-development
description: 'Write a TYPO3 core patch and carry it to review: the changelog entry, the project''s checks, the push to Gerrit. Also amending after review and backporting to a release branch.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Core Patch Development

Carry one change from an issue to a patch somebody can review. Keep this skill
as routing and working order; the suites, the scripts, the contribution rules
and the Gerrit commands are lookups, and a copy of them here goes stale in
somebody else's checkout with nothing to report it.

## Establish the issue before you believe it

1. Work through [references/base.md](references/base.md), which fixes the order
   every task here starts in.
2. `typo3_rule_lookup` for what this kind of change owes — a bugfix, a feature,
   a deprecation and a removal are four different sets of obligations, and which
   one you are in decides the changelog entry, the commit subject and the target
   branch.
3. `typo3_forge_lookup` with the issue number, and read what comes back as a
   report rather than as a specification. An issue can be stale, half fixed, or
   right about the symptom and wrong about the cause, and the maintainers'
   comments on it can be product judgement rather than an API fact. Three parts
   of that answer are not in the description a session otherwise starts from:
   the **status and target version as they stand today**, which is where a
   closure or a reassignment shows without the report ever being rewritten; the
   **relations**, which are one hop from the change that introduced the
   behaviour being complained about, and reach it when a query on the wording
   does not; and the **notes**, where a maintainer said why. Establish which of
   those you have before writing code: what the reporter saw, what the branch
   does today, and what the project intends the API to be for. Three of that
   reading are acts, and what they produce goes into the assessment before any
   code:
   - **Read the closure reason and the target version for what the conversation
     decided, and write that down rather than what the report is worth.** Closed
     for lack of feedback after a long silence is as consistent with an answer
     the reporter could not use as with the reporter giving up. A target version
     says which branch a fix was still expected on. Say what the closure settles
     and what it leaves open; a closed issue is not a finding that the need is
     absent.
   - **Where a comment names an alternative, write out what the alternative
     drops against what the reported code did.** Name the arguments and the
     behaviour the reported code had and the replacement does not. An
     alternative closes an issue only if it does the same work, and what it
     drops is usually the capability the reporter was reaching for.
   - **Enumerate the points the issue requires, the ones only a comment names
     included.** A subject naming two things over comments naming three is the
     ordinary case, and the comment is the list. One patch covers all of them,
     or each point it leaves is given an issue of its own here, before any code:
     a split part needs a number, and the `Resolves:` trailer and the changelog
     file name each take one. A point that is riskier to change is an argument
     for giving it its own issue rather than for dropping it. What that issue
     carries — the tracker, the fields, the markup its description renders as —
     is `typo3_rule_lookup` with
     `documentId="core/contribution/reporting-an-issue"`; filing it is the
     user's, because it takes an account and this server holds none.

4. `typo3_gerrit_lookup` with the same issue number, **before any code is
   written**. Its cheapest outcome is the one that cancels the work and it costs
   one call. An answer of nothing is a result, and a narrow one: the review
   server is read without a credential, so it says that nothing public names the
   issue rather than that nobody has fixed it, and a change pushed unlisted is
   invisible to it.

5. **Verify in the checkout every rule the issue quotes.** A rule about what an
   API may or may not be used for is a claim, the way a path or an identifier
   is. Read the class it names, its docblock and the core's own tests for the
   form under dispute, and say which of the three carries the rule. Enforced in
   code, warned about as fragile and advised in prose are three different
   claims, and two neighbouring APIs regularly make different ones. Carry it at
   the strength its own source puts on it. An assessment that hardens "may
   change in a future version" into "must not" argues the patch away on a rule
   nothing holds.

6. **Reproduce against the branch you are fixing**, not against the version in
   the report. Half of what a stale issue describes is usually gone, and the
   half that remains is the patch.

Whether that reproduction can be a test is a property of what you are changing,
and `typo3_test_run_guide` with the paths you are about to touch is what says
so: it names the suites that can fail on them, and a change to backend markup, a
build step or shipped JavaScript may have none that can hold the bug.

Where there is a layer, write the test first and prove it fails before the fix
and passes after it, in that order. A test written afterwards asserts what the
code now does, which is true of any code. Where there is none, reproduce by hand
and write down the steps and what you saw, because that is what a reviewer
repeats — an unreproducible claim is what sends a patch back regardless of
whether it is right.

A reason for not doing something is dated and the API it rested on is not. Where
the issue carries a decision to defer — it needs an event that does not exist,
there is no API for this yet — check that blocker against what the branch has
today before treating it as standing. An expired objection is written in the
same words as one that still holds, and nothing in the notes separates them.

The argument that carries a bugfix is the same inconsistency inside one version.
"The same input is handled one way here and another way there, on the branch as
it stands" is a defect a reviewer can act on; "this would be better if it also
did that" is a wish, and agreeing with it does not make it a bug. Finding the
place where the system already does the right thing is what turns the second
into the first, and failing to find one is an answer as well — it says the
change is a feature, and step 2 has already priced what that owes.

Establish the blast radius here rather than meeting it while working. How much
of the behaviour the suites already pin down has to change with it is what
decides whether this is a quiet bugfix, a change that has to announce itself, or
a breaking one, and that decision sits upstream of the target branch, the commit
subject and the entry. Discovered incrementally it arrives after the change has
been characterised, and then it is the characterisation that has to be taken
back.

Its other half is who may already be extending what you are about to edit, and
no suite in the checkout answers it: a green run says no core class overrides
the method, never that no extension does. Where the shape you have in mind
touches the declaration of a public or protected member — a parameter, a type, a
visibility, a `final` — settle what that commits the patch to before writing it,
with `typo3_hint_lookup` for the id `public-api-surface`. It decides the target
branch, so a fix owed to a maintained release line has to know it first.

## Where the finding is a vulnerability

**Ask it once the reproduction stands and before any code is written, rather
than when a defect happens to look alarming.** This workflow ends in a push, and
a push is publication: it puts the diff, the test that proves the defect and a
message explaining both in front of everyone who can read the project. Nothing
else in this order asks the question, so the fix would disclose the defect it
fixes.

The stopping point is the verified reproduction. It stands, and nothing after it
here is done: no patch, no test pushed, no entry written, no comment on the
issue.

Where it goes instead is `typo3_rule_lookup` with
`documentId="any/security/reporting-a-vulnerability"`, which is the whole
procedure and also stands as
`typo3://guides/any/security/reporting-a-vulnerability`. The address is read
there and never from here: a contact route is the fact that moves, and this file
is a copy no release of this server corrects.

Ask it again wherever the work turns into one, because the issue rarely says so:
a fix whose real effect is that a restriction now holds is this case under
another name. And where something is already up for review, amending it takes
nothing back — every patch set stays fetchable — so what has been published is
named in the report rather than repaired.

## Make the change

Ask `typo3_hint_lookup` with the concrete paths for the conventions of each
subsystem you touch, before writing rather than after. A convention fetched
afterwards confirms what you already wrote.

Where the change touches a source below `Build/Sources/`, the generated file
beside it is part of the patch. `typo3_rule_lookup` with
`documentId="core/contribution/committed-build-output"` is which source produces
which committed file, how one is rebuilt without putting the rest of the working
tree at risk, and what a backport that came back with conflict markers in it
needs.

Keep the patch one change. What else you noticed is another issue and another
patch; a diff that fixes two things is a diff a reviewer has to accept or reject
as one.

That narrows the work and never the points the issue lists. Those were settled
while it was assessed: all of them are in this patch, or the ones that are not
already have issues of their own. A point dropped here is dropped invisibly,
because `Resolves:` closes the issue on every point it names and nobody reopens
a closed one.

Where the request widens after the patch is under way, re-establish three
things: what kind of change this is now, which branches it reaches, and what it
owes. Do it before writing the widened part, and say which of the three moved.
Step 2 settled the first, the blast radius the second and the changelog section
the third, each against the narrower request. Carrying on re-derives none of
them. A change that gains a second subsystem gains that subsystem's build, its
checks and its backport constraint with it.

Find out whether the area is moving before you build on it, and fetch and rebase
onto the branch you target before you finalise. A patch written against code
that changed underneath it is not a patch that needs adjusting — the method it
called can be gone, and with it the reason the change looked right.

## Verify with the project's own commands

`typo3_test_run_guide` with the changed paths returns the suites that can fail
on this change, each with its targeted invocation, and `typo3_script_lookup`
returns the scripts around them. Run the narrow ones while iterating and the
broad ones before pushing.

Two things decide whether that verification means anything:

- The runner is the project's. A suite run through the host's own PHP or an
  installed binary rather than through the project's runner is a green nobody
  can reproduce.
- A green that ran over no files is not a green. Where a check reports success,
  confirm it inspected something — the count of files, tests or fixtures it
  names — before treating it as evidence. A check that silently found nothing to
  check is the failure mode that survives review.

## The changelog entry the change owes

The procedure is one page: `typo3_rule_lookup` with
`documentId="core/contribution/changelog"`, which also stands as
`typo3://guides/core/contribution/changelog`. It says which of the four types
the change owes, which release directory the file goes into, what it is named
and what checks it. Decide the type from what the change does rather than from
habit: writing an entry for a change that owes none is as much a review finding
as omitting one that does. Write the file into the `<lts>.x` directory of the
oldest branch the `Releases:` trailer names, and into both `.x` directories
where two maintained lines take the change. That is the branches the patch
reaches deciding it, and the branch it is written on deciding nothing.

## Commit and push

`typo3_commit_message_guide` with `workflow="core"`, the drafted message and the
change type reports what is still wrong with it before the hook does. State the
workflow: its default is a repository of your own, which demands neither the
Forge issue nor the `Releases:` trailer a patch here owes. What the rules behind
it say — the subject, the trailers, the release targets and the changelog entry
the change type owes — is one page: `typo3_rule_lookup` with
`documentId="core/contribution/commit-messages"`, which also stands as
`typo3://guides/core/contribution/commit-messages`. Reading it once here is
cheaper than learning it from checks one call at a time. Then the Gerrit
workflow: what the push actually is, how a change is amended into a new patch
set rather than a second commit, and what must not be edited between patch sets.
That procedure exists whole as `typo3_rule_lookup` with
`documentId="core/contribution/gerrit-workflow"`, which also stands as
`typo3://guides/core/contribution/gerrit-workflow`. Read it before the first
push rather than a section at a time: a search returns the part your words
matched, and everything below here is a different part of the same page.

Before pushing, establish where you are pushing to. A core checkout's remote is
not necessarily the one it fetches from, and the answer is in the checkout's own
git configuration rather than in what the repository is called.

### Rebase where the branch moved under you

A commit that sat while you verified it is behind `origin/main`, and rebasing it
is part of pushing rather than a thing of its own. Two parts of that are not
obvious and both were worked out from scratch by a session that had no skill
telling it either:

- **Stop a running `runTests.sh` suite first.** The script mounts the working
  tree and reads it as it goes, so rebasing underneath a run invalidates it
  without failing it — and the run then reports about a tree that no longer
  exists. Clear the suite's leftover containers before starting.
- **Confirm the `Change-Id` survived the rebase.** It is what makes the push a
  new patch set on the change you already have. Losing it opens a second change
  instead, which is not undone by pushing again.

Then re-run the checks on the new base. Inspect the commits you rebased over
where any of them touch the same files: the suite passing before the rebase is
evidence about the old base.

**Where the commit to be changed is a patch set on the review server rather than
one written here, invoke `typo3-core-patch-checkout` for that one change and
work from the copy it leaves.** Somebody else's change picked up to be finished
arrives that way, and so does your own where this checkout no longer holds it.
The ref, the remote, which of the three destinations the patch goes to and
putting the checkout back afterwards are that workflow's whole subject. What
crosses back here is the working copy the patch sits in, the patch set that was
fetched, and whether it had to be carried onto current code to apply.

Pushing is a step of its own and is taken when it is asked for. Everything above
is local and reversible; the push is neither.

**Ask whether the change goes up visible to everyone or unlisted, every time.**
The two are different refspecs and the difference is not a preference: one
publishes the change to whoever watches the project and notifies reviewers, and
neither the publication nor the notification is quietly undone.
`typo3_rule_lookup` for the Gerrit workflow has both forms. Which one this
change wants is the user's decision and never a default read off what the
session did last.

## Where the patch is finished and the review begins

**When the checks pass and the commit is written, invoke
`typo3-core-patch-review` on the diff, before the patch is pushed or handed
over.** That is a step, not a note about ownership: load the skill by name and
work from it. What crosses over is the diff, the branch it targets, the change
type and what the checks reported — and what comes back is the work list the
paragraph below already says to take it as.

The reason it is written as a step is that the ownership sentence did not fire
as one. A session finished a push-ready patch here — three files, two functional
tests, a commit message — ran the project's checks and reported it without ever
opening the review, twenty turns after invoking this skill out of a triage whose
crossing is written as an act. The act fired and the boundary did not, in one
session on one task.

## Amending after review

A patch that came back is the same change, not a new one. Fetch the patch set
that exists, amend it, and keep the identifier that links it to its review —
`typo3_rule_lookup` for the Gerrit workflow says how each of those is done.
Reviewer comments are addressed in the patch or answered in the review, never
silently dropped: a comment nobody replied to is the reason a change sits
unmerged.

Where the change is somebody else's, those steps hold and two more come with
them: the author is asked before the patch set goes up, and every decision taken
on their behalf is said in a comment on the change, because the diff between two
patch sets says what moved and never why. The amend leaves them the author and
makes you the committer, which is the shape this is supposed to have — the same
page carries it. The author line is never overwritten, however much of the patch
you wrote: `--reset-author` has no use here.

This skill owns writing a core patch and delivering it — the change itself, its
tests, its changelog entry, the checks it has to pass, its commit message and
its push. It does not own judging somebody else's patch, and it does not own
judging its own: where the request is to say what is wrong with a change rather
than to make one, `typo3-core-patch-review` owns that and reads different
surfaces for it. Take its findings as a work list when it hands them over.
Changing an extension, a sitepackage or a site project belongs to the extension
skills, whose conventions are not the core's.
