---
id: D-KNW-171
title: A tool outside the core is read at a release and dated here
date: 2026-10-25
status: open
coveredBy: []
---

# D-KNW-171 — A tool outside the core is read at a release and dated here

**A statement about a tool outside the core carries its evidence here, with the
release somebody read and the day.**

`.checkouts/` holds the core alone, so a reader cannot reproduce a statement
about a community package from this repository. A release number in a hint is
invisible to the version filter and nothing here reads it again.

## Evidence

- `friendsoftypo3/kickstarter` released 1.0.0 on 2026-10-05 and 0.4.0 on
  2025-12-14. Both require `typo3/cms-core ^14`. In both,
  `Classes/Command/SitePackageCommand.php` warns that a site package for v14 is
  not supported and returns `Command::FAILURE`, above the former body, which
  stands commented out.
- That commented body fetched the result from get.typo3.org, under the heading
  "Questions to load a new TYPO3 SitePackage". So the command was the console
  form of the Site Package Builder rather than a generator of its own.
- 0.3.5 requires `typo3/cms-core ^13.4.3` and `typo3/cms-install ^13.4.3`, and
  its `execute()` runs that body.
- 1.0.0 carries `make:site-set` and `make:site-settings-definition`.
  `SiteSetCommand` writes the set and prints the `dependencies:` line a site
  configuration needs.
- `make:extension` warns where the export directory already holds that
  extension, asks `confirm(..., false)`, and exits where the answer is no. The
  export directory is the extension setting, `typo3temp/ext-kickstarter/` by
  default.
- TYPO3 Explained 14.3, "Extension kickstarters", states that TYPO3 has no
  official tool for creating extensions. Of the builder it says "The Site
  Package Builder creates an extension containing a site package (theme) for a
  site". Of Make it says "It cannot be used to kickstart site packages or
  Extbase extensions".
- Every release above was read from the GitHub API at its tag on 2026-10-25, and
  every quotation from docs.typo3.org the same day.

## Decided

- The hint names no release of a community package. Each release of the
  kickstarter pins one TYPO3 major, so the major a project runs decides which
  release it installs. `since` and `until` carry that, and the version filter
  reads them.
- The refusal is bound rather than described as temporary. A caller on the major
  where the command works never reads it.
- `coveredBy: []` because no test can hold what another project's repository
  does. A run of its command is outside this suite.

## Assumed

- That a project installs the newest release its major allows, which is what
  Composer resolves to.
- That the commented body is what 0.3.5 runs, rather than something rewritten
  between the two releases.

## Wrong if

- The kickstarter ships a working `make:site-package` for the current major. The
  bound statement then sends a caller to the web form where the console would
  answer.
- A release for the older major gains the set commands. The set half would then
  hold on both majors and the bound statement splits the subject wrongly.
- A session has to read this entry to write a hint about any other community
  tool. The evidence would belong beside each statement rather than in one
  place.
