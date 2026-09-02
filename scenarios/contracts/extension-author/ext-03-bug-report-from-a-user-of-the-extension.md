# EXT-03 — A bug report from a user of the extension

**Environment:** `E-EXT` · **Contract:** `held` — `R-AUD-003`, `R-GUI-002`
**Held by:**
`CommitMessageTest::outsideTheCoreNoTrailerIsAddedAndNoneIsDemanded`,
`CommitMessageTest::outsideTheCoreTheSubjectAndBodyRulesStillHold`,
`ScopeTest::maintainingAnExtensionIsNotSubmittingAPatchToTheCore`,
`ScopeTest::theBriefPointsAtTheGuideForTheStepItEndsWith`; the first three hold
that the guide answers without the trailers **when it is asked to** and the last
that the brief asks for it by name, but that a session passes
`workflow="project"` at all is **not guarded**. Left out, the draft carries the
`Releases:` line this case is written against, and the guide reports a hard
`missing-issue` error besides — measured on 2026-08-02 and recorded on
[`D-GUI-002`](../../../decisions/guides/gui-002-the-commit-workflow-is-asked-for-not-inferred.md).

**Read 2026-09-02:** the 2026-08-02 measurement still does not reproduce. With
the workflow left out the guide answers `workflow: "project"`: the draft carries
no `Releases:` trailer, the only check is `no-issues-found`, and the answer says
the Forge issue and that trailer do not apply. The sign-off a core commit owes
arrived since the last reading and does not reach this workflow either. That a
session names the workflow at all is unguarded still, and costs it nothing.

> Somebody reported that our list plugin crashes when the storage page is empty.
> Reproduce it, fix it, and commit it — we tag a patch release afterwards.

**What the agent needs from this server**

- The conventions for the code being touched, without the core apparatus.
- A commit message that is right for this repository: the subject and body rules
  transfer, the Gerrit and Forge trailers do not.
- What the test for the fix should look like, and how this repository runs it.

**What has to come out of it**

- The commit message follows the TYPO3 subject and body conventions and carries
  no `Releases:`, no Forge issue trailer, and no `Change-Id`.
- The fix is accompanied by a test, and how to run it comes from the
  repository's own setup rather than from the core script.

**How it fails**

- A message with `Releases: main` in a repository that has no core releases
  (`R-AUD-003`).
- The commit rules only being available in their Gerrit-shaped form, so the
  agent either takes the trailers along or abandons the conventions entirely
  (`R-GUI-002`).
