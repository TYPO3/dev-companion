# SKILL-13 — Stop where the patch no longer decides it

**Environment:** `E-CORE`, in a core checkout whose branch has moved past the
change · **Contract:** `open` — `R-SKL-003`
**Held by:** `SkillTest::everySkillStatesWhatItOwns` and
`SkillTest::judgmentHeavySkillsKeepTheirChecklistBesideThem`, which read back
the crossing and that the stopping rules sit beside the workflow. That a session
stops at a conflict it cannot resolve, rather than resolves its way to something
that compiles, is **not guarded**. That is what this case measures.

> Pull down that patch from review and get it onto current main so I can run the
> tests against it.

**What has to come out of it**

- The session establishes the change before the checkout moves: which branch it
  targets, which patch set is current, and whether it is still open.
- The patch set the session fetches is the current one, and the answer says
  which one it was.
- The session establishes a clean work tree first. Uncommitted work ends the
  work with what it found rather than goes into a stash as a convenience.
- The session writes the commit the checkout started on down, and a stop returns
  it there rather than leaves a rebase half-applied.
- The session decides conflicts one at a time, and each resolution names why the
  change itself decided it.
- A conflict the change does not decide ends the work. The report names the
  files, the hunks, what the two sides wanted, and what the session had already
  resolved before the stop.
- Any result from a rebased checkout says that it is about the rebase and not
  about the patch set the reviewers see.
- The session puts the checkout back whichever way it went. Nothing in progress,
  on the branch it started on, nothing of the patch left untracked. The branch
  fast-forwarded from the remote it fetches from, and the installed dependencies
  back in step with it. The answer names the branch, the commit and that the
  tree is clean.

**How it fails**

- Conflicts resolved to whatever compiles, and an answer that reports a clean
  apply of the patch.
- A test expectation or a changelog entry resolved as if it were an ordinary
  conflict.
- The call site of an API that changed shape is rewritten, which is writing the
  patch rather than applying it.
- The checkout left mid-rebase, on a detached head nobody named, or with the
  start commit unrecorded.
- The branch restored but left behind its remote, or updated with a merge rather
  than a fast-forward, or updated from the review server's URL.
- Files the patch added left untracked in a checkout reported as clean. Or the
  dependencies left at the other revision and the next failure diagnosed as a
  test failure.
- The rebased commit pushed, or offered for a push, in the author's name.
- An older patch set fetched and reported on as the change.
- The suites run through something the checkout does not declare, or a green
  reported for a run that inspected no files.
