# SITE-07 — Start a backend module with the right references

**Environment:** `E-SITE`, with the site package under `packages/` ·
**Contract:** `held` — `R-SKL-001`
**Held by:**
`SkillTest::theBaseFixesTheOrderEveryTaskStartsIn`,
`SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder`,
`HintsTest::aBackendModuleNamesItsShortcutApiAndPostRedirect`,
`DocumentationTest::itSearchesTheRequestedVersionAndKeepsProvenanceOnEveryResult`

> Add a backend module to our site package for reviewing imported records. Start
> with the module shell, a refresh action, status markers and the texts; I want
> it to use the TYPO3 backend rather than recreate it.

**What the agent needs from this server**

- Task-level orientation that makes the relevant sources available before the
  first line of code, instead of an agent that discovers each lookup by chance.
- Project and extension scope, the modules already registered, valid icons and
  existing labels in this installation.
- Version-correct component markup from the catalog and the official module API
  documentation for the installation's TYPO3 version.

**What has to come out of it**

- The agent establishes project and extension scope first, then reads modules,
  icons, labels, components and live documentation before implementing them.
- Facts stay in the tools that own them; the task guidance contains routing and
  order rather than a duplicate copy of their answers.
- The answer imposes no core-only path, check or obligation on the site package.

**How it fails**

- The agent starts coding after one generic hint and invents an icon, wording or
  component class.
- A permanently loaded instruction repeats the catalog markup and becomes a
  second version of it.
- The extension task gets a core patch answer because it concerns the backend.
