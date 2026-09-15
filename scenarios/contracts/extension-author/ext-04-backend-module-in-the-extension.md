# EXT-04 — A backend module in the extension

**Environment:** `E-SITE` with the extension under `packages/`, owning records
of its own · **Contract:** `open`
**Held by:**
`SkillTest::theBaseFixesTheOrderEveryTaskStartsIn`,
`SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder`,
`SkillTest::backendModuleDocumentationIsAnExplicitSkillTransition`,
`CatalogTest::anInstalledContractDoesNotAnswerForAnotherTargetMajor`

> Our editors need a backend module that lists the records this extension
> manages with their state, and one action they can trigger on the list. It
> should look like a normal TYPO3 backend module, not like something bolted on.

**What the agent needs from this server**

- How to register a backend module and what the registration file contains.
- Which modules are already registered in this installation, so the new one gets
  a place in the tree rather than a collision.
- The backend UI components for the list, the buttons and the state markers,
  with their real markup.
- Registered icons for the module and its actions.
- The labels that exist for the wordings that recur, save, delete, refresh,
  before the agent invents new ones, and which language a new one takes.
- The official module API documentation for the installation's version, far
  enough to write the controller from.

**What has to come out of it**

- The module registration is the current shape and the paths are the extension's
  own.
- Component markup comes from the catalog, with its revision stated, and the
  agent verifies against the installation's TYPO3 version where they differ.
- The backend look comes from core classes, not from custom CSS reimplementing
  them.
- New labels follow the package's source-language convention, and the answer
  reports a package that already breaks it rather than continues it.
- A value the module needs sits at the reach it has. Per site where it differs
  per site, on the task where a task acts on it. It does not sit instance-wide
  by default.
- The official manual for the installation's version answers what it describes,
  and the agent reads the installed core sources where it does not.

**How it fails**

- Backend CSS source paths in the core handed over as the place to write the
  module's styles.
- Component markup from a newer core silently used on an older installation.
- Documentation about the extension written into the project around it instead
  of into the package that holds the functionality.
- The API re-derived from the installed core sources although the manual
  describes it. Or a runtime-only mistake made and then corrected from a test
  rather than avoided from the lookups.
