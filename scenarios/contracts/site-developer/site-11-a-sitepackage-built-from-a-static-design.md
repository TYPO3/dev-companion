# SITE-11 — A sitepackage built from a static design

**Environment:** `E-SITE`, freshly installed, with
[`site-11-design/`](site-11-design/index.html) copied to `design/` at the
project root · **Contract:** `held`
**Held by:** `ScenariosTest::aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout`,
which holds that the prompt below confirms the `sitepackage` intent, and
`HintsTest::aNewcomersDesignReachesTheHintsOnConvertingIt` and
`SkillTest::theSitepackageSkillConvertsADesignAndCountsTheDocument`, which hold
what that intent and its skill say about a design. That a session converts the
design rather than pastes it is **not guarded**. Three runs of this case, each
graded blind, measure it (`D-EVI-013`), and they answer whether a conversion
needs a skill of its own (`todo/open/T-261028-5e8b.md`).

The design is an ordinary small site. A session of this repository wrote it on
2026-10-29, the maintainer's choice over the design of the run that failed. It
knows no TYPO3 idiom and carries no hint of what a conversion owes.

> We have a fresh TYPO3 installation, and our agency delivered a static HTML
> design in the design/ folder. Build it into TYPO3 as our sitepackage, so the
> editors can maintain the pages themselves.

**What the agent needs from this server**

- What the design maps onto: a page template per page type, the header,
  navigation and footer as partials, and the variable parts as content.
- That the navigation comes from the page tree, and that repeated blocks such as
  the tiles and the notices are content elements or records.
- Where a sitepackage comes from and how the project requires it, as in
  `SITE-10`.

**What has to come out of it**

- Each rendered page holds one `<html>`, one `<head>` and one `<body>`. The
  partials carry no document frame of their own.
- The main navigation renders from the page tree, with the active page marked.
  No link of `design/` survives as a hardcoded URL.
- The tiles, the notices and the about text are content an editor changes in the
  backend. No template carries their text, and no condition on a page uid
  selects them.
- The home page and the content page with its sidebar are two page layouts the
  editors can choose from.
- The stylesheet is an asset of the sitepackage and is included once. The design
  folder itself is not what the site serves.
- The answer names each part of the design and where it went, and what it left
  for the editors to fill in.

**How it fails**

- The design pasted whole: a partial with its own `<!DOCTYPE html>`, `<head>` or
  `<body>`, or `config.disableAllHeaderCode` set to hide a second document.
- The tiles or the notices hardcoded in a template, or picked with
  `<f:if condition="{data.uid} == 1">`.
- The navigation copied as static links from `design/`.
- The site served from `design/` or with the design's relative paths, so an
  asset answers 404 on a subpage.
- The same failures as `SITE-10`: a tutorial for another major, a shared layout
  that renders the frame inside every content element, a builder request with
  guessed fields.
