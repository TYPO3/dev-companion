# CORE-02 — A new state on a backend list row

**Environment:** `E-CORE` · **Contract:** `held` — `R-ANS-003` held
**Held by:**
`CatalogTest::aStatedVersionSaysWhatItDidToTheAnswer`,
`IconLookupTest::aMissingIdentifierHasNoMatchesEvenWhenRelatedIconsExist`,
`LabelSearchTest::aResourceRestrictsReuseToTheUsageContext`

> Records that are scheduled for publication should be marked in the page module
> list — some badge next to the title, with an icon and a tooltip. Build it the
> way the backend does it elsewhere, I do not want a one-off style.

**What the agent needs from this server**

- The canonical markup of the component, its variants and its custom-property
  contract, instead of a class name invented from what the DOM looked like.
- An icon identifier that is actually registered — the shape, not the intent.
- Whether a label for that wording already exists before the agent invents a new
  key, and the domain the XLF file resolves to.
- The backend CSS rules: where the source lives, what may go there, and what the
  build does.
- The suites that can fail on a Sass-and-Fluid change, which is not the PHP
  ones.

**What has to come out of it**

- Markup and class names come from the component catalog, and where the catalog
  has no such component the answer says so instead of inventing one.
- The icon identifier is one the installation has registered.
- The agent reuses a label where one exists. A new key follows the naming of the
  file it goes into, and the domain reference is the computed one.
- The answer states the revision the catalog answers for where the caller works
  on anything but that revision.

**How it fails**

- A plausible but unregistered icon identifier.
- A new label key for a wording that already exists three times.
- Markup from a newer core presented without a word about which revision it is
  from (`R-ANS-003`).
