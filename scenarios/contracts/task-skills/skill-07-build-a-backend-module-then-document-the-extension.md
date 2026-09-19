# SKILL-07 — Build a backend module, then document the extension

**Environment:** `E-SITE`, in the project's site package ·
**Contract:** `open` — `R-SKL-003`
**Held by:**
`SkillTest::backendModuleDocumentationIsAnExplicitSkillTransition`,
`SkillTest::theBaseFixesTheOrderEveryTaskStartsIn`,
`SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder` and
`ScenariosTest::aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout`, which holds
the prompt below to the intent it is about rather than to a brief that names it;
that a session actually hands over at that point is not guarded, and this case
read by hand is what stands in for it (`D-EVI-002`)

**Read 2026-09-02, again 2026-09-19:** the backend-module workflow still stops itself where the documentation begins. It names `typo3-extension-documentation` and invokes it before the first edit to a documentation file. It carries the extension key, the target version and the verified public behaviour across. Repaired on 2026-08-19 in two halves. The gerund became three needles, `reviewing the`, `reviewing this`, `reviewing my`, so `audit` no longer fires on the subject the module serves. `documentation` became an intent of its own (`D-SKL-066`), because "document the public workflow" had reached only `changelog`, whose checklist is the core's release artifact.

> Add a backend module for reviewing imported records, including the module
> shell, status list and refresh action. Once it works, document the public
> workflow for the extension's maintainers and editors in the right place.

**What has to come out of it**

- The backend-module workflow establishes project and extension scope and owns
  the implementation through its verification.
- Once the implementation stands verified, that workflow stops and the
  documentation workflow activates before the first edit to a documentation
  file.
- The verified extension key, target TYPO3 version and public behavior cross the
  boundary; implementation detail that is not public does not.
- The documentation update goes into the site package's own README/manual
  structure, not into the project-level README around it.

**How it fails**

- The backend-module skill remains the only active skill and edits documentation
  itself.
- Both workflows run as one undifferentiated checklist with no observable
  ownership boundary.
- Extension functionality documented at project level, or internal controller
  details presented as stable public behavior.
