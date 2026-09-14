# CORE-06 — The fix belongs on a release branch

**Environment:** `E-CORE`, checked out on a release branch · **Contract:**
`held` — `R-AUD-004`
**Held by:** `VersionsTest::aStatedVersionWinsOverTheInstallationBeingRead`,
`CatalogTest::aComponentNotVerifiedOnTheTargetIsDeclined`;
that a release-branch patch changes trailer and refspec is not guarded

**Read 2026-09-02, and repaired:** the state above said `open` because `R-AUD-004` was. That requirement is `held`, so the state is `held` and the parenthesis behind it is gone. The corpus `R-KNW-057` holds answers the push. `core/contribution/gerrit-workflow` gives the refspec and the rule that a push goes to a release branch only where the bug is not on `main`. `core/contribution/commit-messages` says `Releases:` names the branches a patch targets. What stays unguarded is that a session changes trailer and refspec when
the patch goes to the branch directly.

> This bug only exists on 13.4, on main the code was rewritten and the problem is
> gone. Prepare the patch for 13.4 and tell me what is different about pushing it
> there.

**What the agent needs from this server**

- That a patch which cannot go to `main` goes to the release branch directly,
  and what that changes about the release trailer and the refspec.
- Whether the conventions and any catalog answer it hands over hold on 13.4 at
  all.

**What has to come out of it**

- The release trailer names the branch the patch targets, and the push goes to
  that branch's refspec.
- Where an answer comes from a pinned revision, the answer says so. Where the
  server cannot know whether something holds on 13.4, it says that rather than
  implies it does.

**How it fails**

- Anything stated for "TYPO3" that is only true on the revision the catalogs
  come from, with no word about the branch (`R-AUD-004`).
- The normal main-first rule applied to a bug that does not exist on main.
