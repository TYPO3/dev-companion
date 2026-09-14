---
id: D-KNW-052
title: 'The order a Fluid template name resolves in is a subject this server owns'
date: 2026-08-03
status: confirmed
coveredBy:
  - HintsTest::theFileNameFallbackIsStatedAsOncePerRootPath
  - HintsTest::theFluidFileExtensionIsWithheldWhereItDoesNotResolve
---

# D-KNW-052 — The order a Fluid template name resolves in is a subject this server owns

**The corpus states that the resolver walks the Fluid file-name fallback chain
once per root path, so a root registered later overloads an earlier one.**

Which extension each of the two files carries decides nothing there, and inside
one directory `.fluid.html` wins over `.html`. `fluid-templates` already says
that a bare `.html` still resolves on v14. What no answer here says is which of
two files that both exist is the one Fluid renders. An extension that forks a
core template ships exactly that pair: its own `Login.html` beside the core's
`Login.fluid.html`. Whether Fluid picks the fork up at all is the first question
an audit of it asks. The session that reported settled it in the resolver of a
vendor tree.

## Evidence

- The corpus answers the name half of the ask, and this entry strikes it from
  the feedback. `knowledge/hints/fluid.json` carries "Template files carry the
  `.fluid.html` extension. The bare `.html` form still resolves, so a directory
  of `.html` templates is the predecessor rather than a mistake to fix on sight"
  with `since: 14`. `1d49c912` wrote it on 2026-08-02, the day before this
  feedback.
- It is in reach on the path the audit had in hand. `bin/cli hints:probe`
  returns `fluid-templates` first for `Resources/Private/Layouts/Login.html`,
  `appliesTo(26) + text(128)`. It returns it again for
  `Resources/Private/Layouts/Login.html fork of the core backend login layout`.
  `Resources/Private/Layouts/` is one of its `appliesTo` needles and
  `.fluid.html` is another. The conformance skill asks for one
  `typo3_hint_lookup` per surface with that surface's concrete paths, so
  delivery and routing both work. The feedback's "that rename appears in no hint
  returned for Fluid or template paths" does not reproduce.
- The claim about TYPO3 holds and its source is in the checkout,
  `Documentation/Changelog/14.0/Feature-108166-FluidFileExtensionAndTemplateResolving.rst`
  in `.checkouts/14.3` at `faf60eea22`. It states the chain for one root path in
  six numbered candidates: `myTemplate.fluid.html`, `myTemplate.html`,
  `myTemplate`, then the same three with the name in upper case. It states that
  `*.fluid.*` is "preferred over files without the new extension if both files
  exist in the same folder".
- The per-path half is in that same entry under *Consequences for template
  overloading*. The chain "will be executed *per template path*", and the entry
  spells out both directions. A sitepackage that uses `*.fluid.html` can
  overload an extension that ships `*.html`. A sitepackage that uses `*.html`
  can still overload an extension that ships `*.fluid.html`. That is the
  sentence the audited pattern turns on and no answer here has.
- The rename is real in the tree.
  `.checkouts/14.3/typo3/sysext/backend/Resources/Private/Layouts/` holds
  `Login.fluid.html`, `Module.fluid.html`, `LinkBrowser.fluid.html`,
  `ElementBrowser.fluid.html` and `ElementBrowserWithNavigation.fluid.html`;
  `.checkouts/13.4` holds the same five under the bare names. So a fork taken
  from v13 keeps a file name the core no longer uses, and the file still
  resolves. That is why the verdict needed the order rather than the rename.
- The same entry carries three further facts an audit needs and no hint has.
  One: `*.fluid.*` is **not supported** in an extension that still supports
  TYPO3 below 14. Two: the resolver tries the exact name the caller asked for
  before the upper-case variant. So a template file no longer has to begin with
  a capital. Three: the chain does not run at all where the requested name
  carries its own extension. So `<f:render partial="MyTemplate.html" />` inside
  a `.fluid.json` template has to stand in full.
- Step 1a, and not 2, 3 or 4. Nothing below `knowledge/` states the order.
  `bin/cli hints:probe "which template root path wins override order"` reaches
  nothing out of 78 candidates. `templateRootPaths order override core template`
  returns `content-elements`, `page-cache-flushing`,
  `content-rendering-templates` and `frontend-records` on text alone.
  `sitepackage-templates` speaks of a shadow only where a shared `Layouts/` root
  has no subdirectories and `fluid_styled_content` ships a `Default` of its own.
  `fluid-backend-view` names `templateRootPaths` and says a template refers to a
  partial by name rather than by path. Neither says which path answers.

## Decided

- Step 1a of the ladder on the second half of the ask, queued rather than closed
  on the spot. The gap is a statement about TYPO3 with a version boundary on it.
  The research it needs is a resolver this repository does not have checked out.
- The priority is `normal` and the judgement is what set it. One session
  reported it, which is not the several that would earn `high`. What lifts it
  off the floor is that a forked core template is the pattern the feedback calls
  common and fragile. Whether the fork renders at all is undecidable from the
  corpus today. The session answered it out of installed vendor source rather
  than from anything here.
- This entry strikes the name half rather than queues it. The corpus states it,
  it is current, and it is the top hit on the audited path.
- Not step 1b, and nobody builds the lookup the feedback asks for. A tool that
  takes an extension-relative template path and returns the core file it shadows
  is the fifth sighting of the `D-ANS-003` runtime **Wrong if**. That entry
  records it. `typo3_extension_describe` already reports the Fluid roots. With
  the chain stated, which core file a fork shadows is those roots plus that
  chain. After that the diff is one command in a tree the auditor already has
  open. What the session actually paid three round trips for was the file name,
  which the corpus had.
- The sibling `feedback/2026-08-03-164805` asks for the identifier half of the
  same "read it out of the installed packages" family. Does this method exist
  here, is it deprecated, docblock or attribute. Its card carries it and this
  entry does not fold it in. It is a different question with a different answer,
  and neither one would have told this session which of two files renders.
- Whether `typo3_extension_describe` should say that it read a Fluid root off
  the directory rather than off a declaration is already in the queue. That is
  `todo/say-that-the-fluid-roots-were-read-off-the-directory`, from the same
  audit. It is the provenance of the roots and this is the order the resolver
  walks them in. Neither answers the other.

## Assumed

- That the changelog describes what the installed resolver does.
  `typo3fluid/fluid` is not in `.checkouts/`. The core clone carries only
  `TYPO3\CMS\Fluid\View\TemplatePaths`, which overrides the three setters,
  `getTemplatePathAndFilename()` and `ensureAbsolutePath()` and leaves
  resolution to its parent. `.checkouts/14.3/composer.lock` pins
  `typo3fluid/fluid` 5.3.1. So this entry read the chain from the entry that
  announced it and not from the code that runs it. That is the research the todo
  owes.
- That the order an author sees is the order the resolver walks. The core
  setters sort on integer keys through
  `ArrayUtility::sortArrayWithIntegerKeys()`, while the feedback reports that
  `resolveFileInPaths()` walks `array_reverse($paths)`. So "the last one wins"
  is the highest key rather than the last call. An event listener that appends a
  root path with no key is exactly the case where the two could come apart.
- That a statement about resolution order reaches a session that audits a fork.
  The hint it belongs in is in reach on the template paths today, which is
  evidence about the placement and not about this sentence.

## Wrong if

- The statement lands and an audit of a forked template still cannot say which
  file renders. Then the gap was which root paths the installation registers at
  runtime and in what order. That is a runtime answer and step 1b after all,
  back at the boundary `D-ANS-003` draws.
- The resolver turns out to walk the chain per name rather than per path, every
  root for `X.fluid.html` before any for `X.html`. The changelog says the
  opposite in prose and by example, and this entry did not read the resolver.
- `.fluid.html` turns out to be a demand rather than an option on a v14-only
  extension. The entry calls it "entirely optional", so a conformance finding
  written against a bare `.html` name would rest on nothing.

## Confirmed on 2026-08-03

A session read the resolver where it runs, in an environment on the release the
lock file pins. It appends the candidates per path, so the second **Wrong if**
does not hold. The extension is one candidate of six rather than a requirement,
so neither does the third.

That settles both assumptions, and the second was the research the todo owed.
The order an author sees is the one the resolver walks, for neither reason the
question suggested. The setter sorts each root path list on its integer keys
before the resolver reverses it, so the highest key wins and an appended path
sorts last. What comes apart is the case nobody named. The setter skips that
sort as soon as one key is a string, and then the array's own order decides.

The two older majors try two candidates rather than six, so the order between
root paths carries no boundary and the chain inside one does.
