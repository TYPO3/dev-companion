# CORE-04 — Deprecating a public API

**Environment:** `E-CORE` · **Contract:** `held` — `R-KNW-001` held
**Held by:** `HintsTest::aDeprecationTaskIsRecognizedAsOne`,
`CommitMessageTest::deprecationRulesAreEnforced`; what an upgrade wizard looks
like is the thin part `R-KNW-001` names and is not guarded

**Read 2026-09-02:** `R-KNW-001` is `held`, and
`bin/cli hints:probe "upgrade wizard for a deprecated api"` still reaches
`upgrade-wizards` through the curated vocabulary, behind `deprecated-apis` and
`installation-upgrade`. So the thin part has a hint to reach. That a session
writes the wizard is unguarded as before.

> `\TYPO3\CMS\Core\Utility\GeneralUtility::getUrl()` should go away in favour of
> the request factory. Deprecate it properly for the next major, including
> everything that goes with it, and make sure existing installations survive the
> upgrade.

**What the agent needs from this server**

- What a deprecation consists of in the core: the annotation, the runtime
  notice, the changelog entry, the extension scanner matcher.
- Whether the change is breaking, and what that does to the commit message.
- The upgrade path for installations — the wizard, where it lives, what it has
  to satisfy.

**What has to come out of it**

- The deprecation checklist is complete, and the parts that are branch-specific
  are given as a procedure to run in the checkout rather than a list from one
  revision.
- The commit message carries the deprecation shape and the right release line.
- Where the answer for `Classes/Updates/` is thin, the agent says so instead of
  inventing the conventions of an upgrade wizard.

**How it fails**

- Generic PHP hints for the wizard part, with nothing about upgrade wizards
  themselves (`R-KNW-001` — record what the task needed beyond that entry).
- A changelog file name or a version number quoted from the pinned revision as
  though it held on every branch.
