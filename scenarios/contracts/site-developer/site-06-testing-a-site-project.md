# SITE-06 — Testing a site project

**Environment:** `E-SITE` · **Contract:** `held` — `R-KNW-016`,
`R-SKL-002`
**Held by:**
`SkillTest::extensionTestingVerifiesItsHarnessBeforeAddingCoverage`,
`HintsTest::theTestKindThatNeedsABrowserIsCovered`,
`HintsTest::theBrowserLayerIsReachedByAPromptThatNamesOnlyTheOutcome`,
`ScopeTest::noRunTestsCommandIsHandedToARepositoryThatHasNoRunTests`

> Before every deployment I want a smoke test: the important pages render, the
> forms submit, the backend login works. Set that up for this project.

**What the agent needs from this server**

- A workflow that inspects and proves existing PHP or browser harnesses before
  extending them, and establishes the browser layer if the requested journeys
  have none.
- The boundary between a functional frontend response and a real browser. A
  render alone does not submit forms, execute JavaScript, log into the backend
  or inspect accessibility.
- Project-owned Playwright guidance for real mounted URLs, shared
  authentication, stable scripts, artifacts and CI, without assuming one
  host-versus-container topology.

**What has to come out of it**

- Existing test commands and configuration survive. Absent browser
  infrastructure goes into the runnable project rather than into one extension
  package or a core test directory.
- At least one important page, form journey and backend login path rests on a
  real mounted URL. It runs locally before CI calls the same project script.
- Reports, traces or screenshots have declared artifact paths; credentials and
  developer-specific hosts stay out of tracked configuration.
- No core `runTests.sh` command or core acceptance-test setup passes as this
  project's.

**How it fails**

- A functional subrequest presented as proof that forms and backend login work
  in a browser.
- Playwright configuration written without running one real spec.
- A URL, login or browser location copied from another project and treated as a
  TYPO3 convention.
- Snapshots created or updated without reviewing what they establish.
