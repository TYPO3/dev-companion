# SKILL-06 — Add browser coverage without replacing PHP tests

**Environment:** `E-SITE`, in a project with working PHPUnit tests but no
browser runner · **Contract:** `held`
**Held by:** `SkillTest::extensionTestingLoadsOnlyTheSelectedLayerGuide`,
`HintsTest::theTestKindThatNeedsABrowserIsCovered`,
`HintsTest::theBrowserLayerIsReachedByAPromptThatNamesOnlyTheOutcome`

> A frontend regression reached production even though our PHP tests passed.
> Add browser coverage for the important page, its form, and backend login. Keep
> the PHP suite, and make the browser setup work locally and in CI.

**What has to come out of it**

- The answer verifies the PHP harness that exists and keeps it. Browser coverage
  comes in because nothing there can prove JavaScript, form interaction and
  login.
- Playwright belongs to the runnable project, uses real mounted URLs and derives
  its base URL, authentication and browser execution location from the project.
- Stable project scripts exist before CI calls the same commands, and at least
  one real spec plus its report or trace runs locally.
- Credentials, developer hosts and temporary authentication state are not
  committed. Snapshot updates, if used, require an understood visual change.

**How it fails**

- PHP tests that work replaced or described as useless.
- Browser packages or URLs imposed from a generic DDEV recipe without a look at
  how this project runs.
- Configuration and CI added, but no served page exercised.
- A screenshot-only assertion presented as proof that the form submitted or
  login succeeded.
