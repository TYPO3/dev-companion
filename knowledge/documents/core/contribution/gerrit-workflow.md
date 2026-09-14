---
description: >-
  How a core patch reaches review: the one-time git setup, how you fetch a patch set into the checkout, carry one onto current code, push, amend a patch set, open one on somebody else's change, and backport.
whenToUse: >-
  When a change is ready to leave the checkout, when you have to read or try out a patch under review locally, or when you have to change a patch already under review — your own or another author's.
hints: []
---

# TYPO3 Gerrit Workflow

Source: https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/

A TYPO3 core patch is not a pull request. You push it to Gerrit at
https://review.typo3.org, one commit per patch, and you improve it when you
amend that commit.

## One-Time Setup

The push URL points at Gerrit while the fetch still uses GitHub:

```bash
git remote set-url --push origin ssh://<username>@review.typo3.org:29418/Packages/TYPO3.CMS.git
```

Gerrit identifies a patch by the `Change-Id:` line in the commit message, which
the `commit-msg` hook adds. Install the hooks once per clone:

```bash
composer gerrit:setup
```

This enables the `commit-msg` hook and a `pre-commit` hook that runs local
checks. The `commit-msg` hook adds the `Change-Id`. It rejects a commit message
with a line longer than 72 characters, and one without a `Resolves:` line. The
hook file itself lives in `Build/git-hooks/commit-msg`, and you can also copy it
by hand:

```bash
cp Build/git-hooks/commit-msg .git/hooks/commit-msg && chmod +x .git/hooks/commit-msg
```

Without the hook, Gerrit rejects the first push because the commit has no
`Change-Id`.

## Where This Checkout Pushes

The command above is the write side. Where a checkout already points is four
questions of its own. Neither the directory name nor the URL it fetches from
answers them. GitLab hosts the sources and mirrors them to GitHub, which is what
a clone usually fetches. Gerrit couples to GitLab.

```bash
git remote -v
git config --get remote.origin.pushurl
git config --get remote.origin.push
cat .gitreview
```

- `git remote -v` prints a `(fetch)` and a `(push)` line per remote. The two
  differ only where somebody set a push URL. So the same URL twice means a push
  goes back where the clone came from.
- `remote.origin.pushurl` is that push URL, and `git remote set-url --push`
  writes it. Unset, the read prints nothing and exits non-zero, and a push uses
  the fetch URL. A clone in that state cannot open a change on Gerrit at all.
  That is the normal state of a checkout you keep to look things up.
- `remote.origin.push` is the refspec a bare `git push` uses. Set to
  `+refs/heads/main:refs/for/main`, `git push` on its own goes to Gerrit. Unset,
  it addresses the branch, which Gerrit rejects.
- `.gitreview` in the checkout root carries `host`, `port`, `project` and
  `defaultbranch` under a `[gerrit]` heading. It is the `git-review` tool's
  configuration, and git itself never looks at it. So it says where the project
  lives on Gerrit even in a clone whose remote points nowhere near it. With an
  account name those values are the push URL:
  `ssh://<username>@<host>:<port>/<project>.git`.

## Fetch a Change Into This Checkout

A patch under review is a ref named after the change's own number, and every
patch set keeps its own:

```bash
git fetch <the Gerrit URL above> refs/changes/<last two digits>/<change>/<patch set>
git switch --detach FETCH_HEAD
```

`refs/changes/02/95102/2` is change 95102, patch set 2, filed under the last two
digits of the number. Patch set 1 stays fetchable after somebody pushes 2. So
you can read a review comment about an earlier revision against the revision its
author wrote it on.

There is no ref for "the current one". You read the patch set number off the
change; nothing defaults it. The only other ref a change carries is `meta`. That
is its review history and not a commit anybody builds on.

**The ref is on Gerrit and not on GitHub.** A core clone fetches from the mirror
and pushes to the review server. So `git fetch origin refs/changes/…` reports
that the ref does not exist, in a checkout whose push would reach it. That is
the same asymmetry as above, from the read side. Fetch from
`remote.origin.pushurl`, or from a remote of its own that points at the review
server. Measured on 2026-08-05: `refs/changes/02/95102/2` resolved over the
Gerrit URL and returned nothing at all over the GitHub one.

A fetched patch set is somebody else's commit and is on no branch. When you
detach onto `FETCH_HEAD`, the checkout says that. That keeps a local branch from
a quiet acquisition of a commit that belongs to a review. It holds while the
checkout is on the fetched commit and no longer. A replay onto other code
produces a commit made here, which is the next section.

## Carry a Change Onto Current Code

You read a patch against the code its author wrote it on. When you carry it onto
current code, you answer a different question: whether it still applies there
and still passes. Gerrit's Download menu copies the command for it:

```bash
git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/19/93319/21 && git cherry-pick FETCH_HEAD
```

That URL is the review server over https, and it serves the change refs to
anyone. Measured on 2026-08-14: `git ls-remote` over it listed all 21 patch sets
of change 93319 with no account configured. A push needs the ssh URL above.

The command does not say where the commit lands. The
[contribution guide's cherry-pick page](https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/HandlingAPatch/CherryPick.html)
runs it after `git reset --hard origin/main`. So the cherry-pick writes onto
`main` itself. A local branch that tracks the core then carries a commit
upstream does not have. Nothing in the checkout says which one it is. The next
thing that reads that branch reads the patch as if somebody had merged it.

So the carry goes on a branch of its own. Name it for the change and start it at
the tip of the branch the change targets:

```bash
git switch -c review/93319 origin/main
git fetch <the Gerrit URL above> refs/changes/19/93319/21
git cherry-pick FETCH_HEAD
```

Every patch is exactly one commit. So a rebase of the fetched commit onto
current code and a cherry-pick are one move with two names. Both replay one
commit onto code its author did not write it on. The cherry-pick is the form the
guide documents and the review server hands out.

The carried commit is on no review server and in no other branch. So the branch
is the only thing that holds it, and git refuses to drop it as merged:

```bash
git switch main
git branch -D review/93319
```

## Push a Patch for Review

```bash
git push origin HEAD:refs/for/main
```

`refs/for/<branch>` is Gerrit's magic ref. It opens a review instead of a write
to the branch. A plain push like this one publishes the change to everyone who
can read the project. It puts the change in front of reviewers. The next section
has the two forms that do not. Which of the three a change wants is the author's
to say.

## Push a Private or Work in Progress Change

```bash
git push origin HEAD:refs/for/main%private
git push origin HEAD:refs/for/main%wip
```

Both are Gerrit push options appended to the magic ref. Git carries the string
and does not read it. So the form is the same from any client and any checkout.

- `%private` decides **who can see the change**. That is its owner, the
  reviewers somebody added to it, and accounts with the View Private Changes
  capability. Nobody else. It is not a quieter way to ask for review. Somebody
  nobody added by name cannot reach the change at all.
- `%wip` decides **who Gerrit asks to act**. Anyone can see a work in progress
  change. Gerrit notifies no reviewer, keeps it out of reviewers' dashboards,
  and sends nothing on most later operations. It says to the review server what
  `[WIP]` in the subject says to a reader.

The way back out is not symmetrical:

- `%private` sticks. A further patch set pushed without the option does not
  publish the change. `%remove-private` removes the flag.
- `%wip` comes off with `%ready` on a push, or with the change's own "Start
  Review" button. Only the change owner, project owners and the review server's
  administrators may use `%wip` and `%ready` on a push.

What `%private` does not do:

- It does not survive the merge. A merged private change becomes visible to
  everyone who can read the target branch and loses the flag. So it is no way to
  land a security fix quietly.
- It does not hide from a follow-up. A non-private change pushed on top of it
  makes the private parent visible through the parent relationship.
- It does not hide the commit from whoever knows its id. Anyone who could
  otherwise see the change can fetch the commit by its hash.

A Gerrit account can also open every new change as work in progress. So read
what state a push produced off the change rather than off the command that made
it.

## Pushing From a Git Worktree

Nothing about the push changes in a `git worktree`: same remote, same magic ref,
same options.

```bash
git push origin HEAD:refs/for/main%private
```

`HEAD` resolves to that worktree's own commit. The remotes, the configuration
and the hooks live in the one git directory every worktree of a clone shares. So
`git config --get remote.origin.pushurl` answers the same in all of them. The
`commit-msg` hook installed once for the clone runs for a commit made in a
worktree. It writes its `Change-Id` there. `git rev-parse --git-path hooks`
names the directory git takes it from.

The branch point decides the push, not the worktree. What goes up is `HEAD` and
every ancestor the target branch does not already have. A worktree branched off
an up-to-date `origin/main` sends one commit.

One branched off a local `main` that is behind sends what lies between as well.
`git log --oneline origin/main..HEAD` says which of the two this is. Where a
worktree does change the answer is the checks. A suite that takes its file list
from git can report success in one after it inspected nothing.

## Update an Existing Patch

Every patch is exactly one commit. An improvement never adds a second commit.
You amend the existing one and push it again, which creates a new patch set:

```bash
git commit --amend -a
git push origin HEAD:refs/for/main
```

- Keep the `Change-Id:` line as it is. It links the new patch set to the
  existing review. A changed or removed `Change-Id` opens an unrelated second
  change.
- Gerrit never overwrites an earlier patch set. Reviewers can diff between them,
  so an amend is safe.
- Before you amend, fetch the current patch set when it is not already the local
  commit. The fetch section says which ref that is and where you fetch it from.

## Open a Patch Set on Somebody Else's Change

Anybody may improve a change under review. The improvement goes up as a patch
set on that change rather than as a change of its own. The contribution guide
asks for one thing first. It says: "You can even commit and contribute on other
people's patches - always make sure to ask first, before you do that."

The review server asks for nothing. Gerrit grants `Add Patch Set` to registered
users on `refs/for/*` by default. So Gerrit cannot tell the owner's push from
anybody else's. The ask is what stands between them.

Nor is it a last resort. The guide asks a reviewer to fix a coding guidelines
violation and push a new set rather than vote -1. It says that "voting -1
because of simple CGL violations can easily scare away people and kill
motivation".

You amend the commit exactly as above, and the amend keeps the change its
author's:

- `git commit --amend` leaves the **author** as it was, with name, address and
  author date. It makes you the **committer**. That is the intended shape and
  not something to correct. The author of a change is whoever pushed its first
  patch set. The committer is whoever last touched the commit.
- `git commit --amend --reset-author` overwrites the author, and nothing here
  takes it. The author of a change stays its author however much of the patch
  somebody else wrote. What you contributed is the committer line and the
  comment you leave on the change.
- The `Change-Id` stays as it is. The `commit-msg` hook leaves an existing one
  alone rather than add a second. The id belongs to the change and not to
  whoever pushes.
- Still one commit. `git log --oneline origin/main..HEAD` says so before the
  push, whoever wrote the commit.

Measured on 2026-08-27 in a scratch clone with `Build/git-hooks/commit-msg`
installed. An amend of a commit made under another name left the author line
unchanged. It moved the committer to the local identity. It kept the one
`Change-Id` line and left one commit above the base.

`git cherry-pick` answers the same way: it keeps the author and carries the
`Change-Id` with it. So a patch carried onto current code is a rebase of that
change. A push of it opens a patch set on it rather than a review of your own.

What the upload owes the author is a comment on the change. The diff between two
patch sets says what moved and never why. So say there anything you decided on
the author's behalf: another anchor, a reworded body, a different `Releases:`
line. The guide's own model is a reviewer who writes "Just pushed a new version
fixing minor CGL stuff. Would be cool if you read through my changes compared to
your version".

A comment is the whole answer where what would change is the author's to decide.
The guide votes -1 on a change that "is broken, does not fix the issue, is
bogus, architecturally wrong or collides with other goals". None of those is a
correction somebody else writes into the patch. What comes out is a change its
author did not propose, under their name. The same holds while the author has
not answered the ask.

## The Forge Issue a Change Hangs Off

Every core change names its issue in the commit message with
`Resolves: #<issue number>`. The `commit-msg` hook checks that such a line is
there: a number, not the state of the issue. Gerrit does not ask Forge either.
So nothing refuses a push against a closed issue, and it opens a change that
looks like any other.

Forge is the side that notices. An issue gets a comment from Gerrit Code Review
that links the change when its first patch set arrives. That moves a report to
"Under Review". Whether that also happens on a closed report is a property of
the tracker's own workflow. Nothing here establishes it.

Forge closes a report for five reasons. It went stale, or nobody can reproduce
it. It lies outside the project's goals, it got no feedback, or somebody
abandoned its patch. A change that hangs off one asks whoever looks at it to
reverse a decision the issue already records. It says so nowhere in the change
itself.

So settle the closure before the push rather than after it. Reopen the issue
where the reason no longer holds, which is the tracker's step and may need
whoever closed it. Or answer on the change why it stands anyway. No rule
requires the reopen; anyone may contribute a patch to any issue. What a skipped
reopen costs is the first question anybody who reads the change asks.

## Release Branches and Backports

```bash
git push origin HEAD:refs/for/13.4
```

Push to a release branch only when the bug exists there and not on `main`. In
the normal case the patch targets `main`, and the core team member who merges
takes care of the backport. The `Releases:` line in the commit message names the
branches the change is meant for.

How far back that goes is not "everywhere the defect is". A bug fix and a task
go to `main` and to the one line back from it. An older maintained line takes
priority bug fixes and grave or security-relevant defects. So when you name one,
you claim something about the severity and not only about where the defect is.

A backport is a cherry-pick of the merged commit onto the release branch. It
usually starts from Gerrit's "Cherry pick" action. The `Change-Id` of the
original change stays unchanged; that lets Gerrit link the backport to it. You
remove everything below the `Change-Id` line: `Reviewed-by:`, `Tested-by:`, and
the blank lines. You adjust the code to the older branch where it no longer
applies cleanly.

A generated file is where "adjust" is not what happens to it. The JavaScript and
CSS committed beside a `Build/Sources/` change conflict whenever the two
branches' sources diverged in the same module. Neither side of that conflict is
a resolution. `typo3_rule_lookup` with
`documentId="core/contribution/committed-build-output"` has what is. Gerrit's
"Cherry pick" action commits the conflict markers rather than refuse. So search
a backport made that way for them before anything else.
