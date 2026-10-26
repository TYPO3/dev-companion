# SITE-10 — A sitepackage built from nothing

**Environment:** `E-SITE`, freshly installed · **Contract:** `held`
**Held by:** `ScenariosTest::aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout`,
which holds that the prompt below confirms the `sitepackage` intent, and
`HintsTest::aNewSitepackageIsRoutedToEverySitepackageHint` and
`HintsTest::aSitepackageIsGeneratedThroughTheBuildersApi`, which hold what that
intent and its first hint route to. That a session then builds the package the
way the hints say is **not guarded**. A run of this case is what measures it,
and that run decides whether a sitepackage skill is owed (`T-261025-2da7`).

> We have a fresh TYPO3 installation and nothing of our own in it yet. Create a
> sitepackage for our site: the extension, its site set, a page template with
> header and footer, and two page layouts the editors can choose from.

**What the agent needs from this server**

- Where a sitepackage comes from, since nothing in the core creates one. That is
  the builder and the schema its request takes, or the console tools of the
  installed major.
- How the package is laid out: one template root, the page layouts and the
  content element layouts apart, and the page layouts declared beside the set.
- Which major the installation runs and which sets its sites already depend on,
  read from the installation rather than assumed.
- What keeping or dropping `fluid_styled_content` costs, before the first
  template depends on the choice.

**What has to come out of it**

- An extension the project requires through Composer, with a set the site
  configuration depends on. The answer names the file that carries that
  dependency.
- A generated package is treated as a starting point. The answer removes or
  names the empty directories it does not need, rather than keeps all of them.
  Where the agent asks the builder for it, the request takes its fields and
  values from the builder's schema.
- The page templates and the content element templates stand under one template
  root. A layout is addressed with its subdirectory, so the page frame never
  renders inside a content element.
- Each page layout is one file whose identifier is the name of its page
  template. The answer says whether the files belong to the set or to the
  extension, and why.
- `lib.fluidPage` and `lib.contentElement` are reached with the reference
  operator, not copied.
- The answer says whether the site keeps `fluid_styled_content`, and what goes
  with the other choice.

**How it fails**

- A package written from a tutorial for another major: TypoScript constants
  where the installed line has set settings, or a page rendered without the
  mechanism that line renders pages with.
- One shared `Layouts/Default`, so the header and footer appear inside every
  content element on a page that renders without an error.
- The core's own theme required or extended as the base of a project
  sitepackage.
- A builder request with fields guessed from an example and repaired through the
  errors it returns.
- The site configuration's languages, base or error handling decided on the way
  past. The prompt does not ask for them, and the server does not cover them.
