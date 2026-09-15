# EXT-02 — A new extension from nothing

**Environment:** `E-SITE`, in a project where the new extension will live
under `packages/`, beside the one that environment already carries ·
**Contract:** `held` — `R-SCO-001`, `R-SCO-002`
**Held by:** `ScopeTest::workOnAProjectExtensionIsRecognizedAsOutsideTheCore`,
`CatalogTest::aDomainIsDerivedForAFileThatDoesNotExistYet`,
`IconLookupTest::everyAnswerSaysTheIdentifiersAreTheBackendRegistrys`

> I need a new extension `acme_seminars` in this project: a record type for
> seminars with dates and a location, editable in the backend, listed in the
> frontend by a plugin. Set it up from scratch.

**What the agent needs from this server**

- The file layout of an extension and what each required file is for.
- TCA conventions for a new table, and how to declare the schema.
- Services and dependency injection, so the plugin is not built with
  `GeneralUtility::makeInstance` everywhere.
- Where labels go, what name the file has, and what domain it resolves to.
- How to register an icon for the record type, and which identifier to use.
- Fluid conventions for the plugin template, and the namespaces available
  without declaring them.

**What has to come out of it**

- The answer is right for an extension, not for a system extension. Paths under
  the package, no core-only steps, no checkout discovery instructions for a
  checkout that is not there.
- The label domain for a file that does not exist yet comes from a computation,
  not a guess.
- The answer verifies the registered icon identifier against the installation.

**How it fails**

- The task recognised as core work because it is about TCA and Fluid, and
  answered with core paths (`R-SCO-001`).
- The extension's own not-yet-installed state confusing the installation-backed
  lookups into reporting "nothing registered".
