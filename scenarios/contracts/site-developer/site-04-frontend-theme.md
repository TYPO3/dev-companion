# SITE-04 — The frontend theme

**Environment:** `E-SITE` with a site package using Bootstrap ·
**Contract:** `held` — `R-SCO-004`
**Held by:**
`HintsTest::aFrontendThemeIsNotAnsweredWithTheBackendsOwnCssConventions`,
`HintsTest::stylingABackendModuleStillReachesTheBackendCssHints`,
`HintsTest::aPhpClassNameThatCarriesTheWordScssIsStillPhp`

> Our site package theme is Bootstrap 5 with our own SCSS on top. The card
> component looks wrong on mobile and the spacing is inconsistent across
> breakpoints. Clean up the SCSS and make the components consistent.

**What the agent needs from this server**

- Nothing, honestly, beyond recognising that this is not the TYPO3 backend. The
  server's CSS knowledge is the backend's, and the backend left Bootstrap
  behind.
- The Fluid side of the templates, where it comes up.

**What has to come out of it**

- The backend CSS architecture, its design tokens and its class naming do
  **not** come back as advice for a Bootstrap frontend theme.
- If the answer offers CSS knowledge at all, it marks it as the core backend's
  and as one that does not apply here.

**How it fails**

- Backend token names, backend component class names, and "the backend has moved
  off Bootstrap" handed over as instructions for a Bootstrap theme. That is four
  confidently inverted hints, which is the note this scenario exists for
  (`R-SCO-004`).
- A `.scss` path being enough to trigger core CSS conventions.
