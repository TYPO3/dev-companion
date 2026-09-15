# SKILL-03 — Make the documentation match the package

**Environment:** `E-SITE`, in the project's site package · **Contract:** `held`
**Held by:**
`SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder`,
`SkillTest::everySkillStatesWhatItOwns`,
`SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns`,
`ScenariosTest::aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout`

**Read 2026-08-19:** the prompt confirmed `changelog`, so the right skill
arrived behind a checklist about the core's `Documentation/Changelog/`
directory. The `documentation` needle moved to the intent of that name —
`D-SKL-066` — and the prompt confirms `documentation` alone.

> Review and improve our site package's documentation for integrators and
> editors. Make it match what the package really implements, remove unsafe
> example data, and stop once the requested workflows are verifiable.

**What has to come out of it**

- Integrator and editor material stay apart and go into the repository's own
  README/manual structure.
- Keys, defaults, registrations, paths, and examples come from the checkout;
  external TYPO3 claims use versioned official evidence.
- Credentials, personal data, internal hosts, and machine-specific paths go, or
  named placeholders replace them.
- The answer describes a dynamic-registration parser miss as a limit of the
  tools, not as proof that the feature does not exist.
- The work stops at the completion gate and reports validations and remaining
  unknowns rather than continuing an unbounded survey.

**How it fails**

- Copying local passwords or personal addresses into polished examples.
- Replacing checkout truth with incomplete parsed installation output.
- Creating a competing documentation structure or promising internal details as
  stable API.
