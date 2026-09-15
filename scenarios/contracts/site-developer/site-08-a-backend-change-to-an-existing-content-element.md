# SITE-08 — A backend change to an existing content element

**Environment:** `E-SITE` · **Contract:** `held`
**Held by:**
`HintsTest::aBackendTaskIsNotAnsweredWithTheSitepackageLayout`,
`HintsTest::aBackendTaskIsNotCalledFluidAndTypoScriptWork`,
`HintsTest::aContentElementBuiltInASitepackageKeepsItsLayout`,
`HintsTest::aBackendModuleInASitepackageDoesNotBecomeFrontendWork`

> The accordion content element we already have needs one more field in the
> backend form, and the wrong icon shows for it in the new content element
> wizard. Nothing about the rendering changes.

**What the agent needs from this server**

- The TCA conventions for adding the field, and where a CType's registration
  declares its icon.
- An icon identifier the installation has registered, rather than one that reads
  plausibly.
- Nothing about where templates live: the element exists and its output is not
  what the user asked about.

**What has to come out of it**

- The element's registration and its backend form are what the answer is about.
- The sitepackage layout is not returned. It is the longest hint in the corpus
  and stands in the words of the backend that administers the package. So it
  reads as an answer to any task that names a content element (`D-KNW-001`).
- A task that does name the other half — building the element and its frontend
  output — still gets it, which is `SITE-05`.

**How it fails**

- The brief opens with where `Resources/Private/Templates/Content/` goes for a
  task that changes one field.
- An icon identifier invented instead of read from the registry.
- A Fluid or TypoScript convention stated as one that applies, or a brief that
  opens with both domains. "Content element" is a keyword of each and stays one,
  because a task that names neither half of TYPO3 is the case it serves. What it
  no longer does is add them where the task names only the backend
  (`D-KNW-006`).
