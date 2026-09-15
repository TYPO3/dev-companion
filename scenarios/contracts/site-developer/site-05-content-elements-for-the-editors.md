# SITE-05 — Content elements for the editors

**Environment:** `E-SITE` · **Contract:** `held` — `R-KNW-001`
**Held by:** `HintsTest::aRepeatableContentElementIsRoutedThroughWhatItOwns`,
`ProjectTest::theContentElementsAnExtensionAddsAreNamed`,
`IconLookupTest::aMissingIdentifierHasNoMatchesEvenWhenRelatedIconsExist`

> Editors need a "team members" content element: a list of people picked from a
> folder, rendered as cards. Build it in our site package — the element, its
> backend form, and its frontend output.

**What the agent needs from this server**

- The TCA conventions for the element and its fields.
- How the data gets to the template: the data processor, its registered name,
  what it may do.
- The Fluid conventions for the template, and the namespaces available
  undeclared.
- The label domain of the site package's own language file.
- The backend preview side, and the icon for the element.

**What has to come out of it**

- Conventions come over without core checks or core paths attached.
- Where the answer for data processors is thin, the answer says so rather than
  covers it with generic Fluid advice (`R-KNW-001`).

**How it fails**

- Fluid and TCA hints returned as though they were the whole answer, with the
  data processing step silently skipped.
- An icon identifier invented for the element instead of taken from the
  registry.
