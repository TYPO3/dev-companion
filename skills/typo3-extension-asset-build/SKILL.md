---
name: typo3-extension-asset-build
description: 'The asset build of a TYPO3 extension, sitepackage or project package: npm and package.json dependency updates, Dependabot pull requests, webpack, vite, Grunt or Sass, the built CSS and JavaScript under Resources/Public, the import map it reaches the backend by, and the core classes and icons it borrows. Stops at a bundler or library migration.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Asset Build

A package's own build produces the CSS and JavaScript its backend and frontend
load, and most of that task is npm's, the bundler's and the library's. What this
workflow orders is the TYPO3 half: what the build is run as, what its output
promises the backend, and which of that output is committed. Keep this skill as
routing and workflow; never retain a dependency version, a bundler
configuration, a build command or a core class name — every one of those is a
property of the repository in front of you and of the majors it declares.

## The order

1. Work through [references/base.md](references/base.md). It fixes what this
   package is, which majors it declares, and what it runs its build with.
2. Establish which of the output is committed, and which route each file reaches
   a page by, below.
3. Verify every core surface the change will borrow, before writing it, below.
4. Change what the task asks for, and stop where the library's own migration
   begins.
5. Rebuild, and check what the output promises: the backend below, then the
   frontend below.
6. Commit the rebuilt artefacts together with the source that produced them.

`typo3_project_describe` is discharged by the first step: it reports the
manifests this repository keeps — at the root, and one directory down where the
build sits there — the commands each of them declares with the manifest they
came from, whether a command reports or changes, and the Node that the manifest,
the pinned version, the CI workflow and the container each state, with the
disagreements between them named. Run the commands as it reported them. An
invocation rewritten from habit runs the build in the wrong directory or on a
Node that CI does not use, and both of those surface as a diff nobody can
explain.

**A pin is read against the release current on the day.** That step reports what
each source states and none of them says whether it is still current, so
establish the current release where it is published — the runtime's own release
schedule, the registry a package is published to, the repository an action is
tagged in — and report every pin behind it as a finding carrying the raise. What
speaks against one is a bound this repository declares: the Node the build
needs, the majors the package supports. The finding then names the newest
release that bound allows, raising is the maintainer's, and moving the bound is
a task of its own.

## Which of the output is committed, and how it reaches a page

This server does not read your working tree, so these are the repository's
answers and not a lookup: is the built output tracked in git, does any check
assert that a build leaves the tree clean, and over which route does each output
file reach a page.

- **Committed output means source and output change together.** The package's
  consumers install what is in the repository, so a commit carrying new source
  and last month's artefacts ships the old behaviour to every one of them.
- **Uncommitted output means the deployment runs the build.** Then the artefacts
  are not yours to commit, and what has to hold instead is that the build runs
  where the deployment runs it.
- A check that asserts a clean tree after a build is the executable form of that
  decision, and where the repository has none, its absence is a finding rather
  than a licence. Establishing that check is `typo3-extension-testing`: invoke
  `typo3-extension-testing` for it, carrying the build command and the output
  paths already established here.
- **Each output file reaches a page by being declared somewhere, and the route
  differs between the two halves.** Backend JavaScript is declared in an import
  map; a frontend stylesheet is included by TypoScript, by the AssetCollector
  behind `<f:asset.css>`, or by `PageRenderer`. Find which route this package
  uses for each file before changing anything, because step 5 checks that the
  same route still carries afterwards and cannot find a route it never saw.

## When the build is not reproducible

A build whose output differs on every run makes every later diff unreadable, and
after a change the two causes are indistinguishable: output that differs because
of your change, and output that differs whatever the source says.

- **The repository's own check is what answers this.** A job asserting a clean
  working tree after a build is the executable form of that question, and step 2
  has already established whether there is one. Run it.
- Where the rebuild in step 5 produces a diff your change does not account for,
  build the unchanged checkout and compare. That separates the two causes and
  costs nothing in the ordinary case, where the diff is explained.
- Where the tree comes back dirty on an unchanged checkout, that is the finding,
  and it is about the toolchain rather than about the package. Say which file
  differs and how.
- Where the build will not run at all, the Node the first step reported against
  what this machine has is the first thing to read, and reinstalling the
  dependency tree is the second.

## Where this workflow stops

The bundler's configuration format, a JavaScript library's own API change and a
defect in the runtime are that project's manual, not this server's. Read them
there, and say in the answer which manual answered — a migration reconstructed
from the installed sources of a dependency is a reading of one version of it,
and it is worth what it says about that version alone.

Two changes look like this task and are not:

- The package is being carried to another set of TYPO3 majors, or is broken by
  what one of them removed. That decides the whole reading below, so invoke
  `typo3-extension-upgrade` and carry across the build commands and the output
  paths already established.
- The request is an audit of the package rather than a change already agreed.
  Invoke `typo3-extension-health` and carry across what was established about
  the build.

## Verifying a core surface before you borrow it

A class or an icon the output takes from the core is verified before it is
written, not after. Verified afterwards it is already in the diff, and the cost
of the answer being no is a second pass over markup that reads as finished.

- `typo3_documentation_lookup`, at each major the package declares, for the
  backend JavaScript module contract and what an extension may assume is already
  loaded. Assuming a library is present because the backend once shipped it is a
  decision, and it is one this answer settles.
- **The query names the component, and the answer places the class.**
  `typo3_component_lookup` with the `targetVersion` of a declared major returns
  each class with where it sits: around the component, on its root element, or
  inside it. `table-fit` is the element *wrapping* a `.table`, which its name
  does not say and its own stylesheet rule does not either.
- **A class the answer does not place is one the core's stylesheet says nothing
  about.** That is not a licence to attach it anywhere: it means the position
  has to come from somewhere else, and the entry names the core Sass file to
  read on that branch.
- **One call per declared major, because the position is itself version-bound.**
  A class can be written above its component on one major and not on another, so
  a surface verified on the installed major alone is unverified on the rest. The
  finding is the range it holds on, and a borrowed surface not verified on the
  lowest declared major is a defect in that version.
- A class the package's own stylesheet only adds a rule to is one of these, and
  reads in the diff exactly like one the package owns.
- `typo3_icon_lookup` for a borrowed icon identifier. It answers from the
  installation, so it settles the major installed and says nothing about the
  others the package declares.
- `typo3_changelog_lookup`, restricted to each declared major, for a core asset
  the output stops relying on. Deleting a rule because the core no longer ships
  the icon font it names is the same unverified decision as attaching a class
  because the core does, and the build goes green either way.
- `typo3_rule_lookup` with `documentId="any/backend/using-the-styleguide"` for
  what a styleguide demo does and does not state, before one is read as the
  contract for a component.

## What the rebuilt output promises the backend

Built backend JavaScript does not reach the backend by being present: it is
declared in an import map, one specifier per file. A build that renames, splits,
hashes or drops an output breaks that mapping without anything failing in PHP,
so after every rebuild each mapped path is checked against the file the build
actually wrote. A pipeline written for the frontend produces the wrong shape
here — one hashed bundle where the map names files — and nothing fails in PHP
there either. Which file declares the map, and the check that belongs to it, is
the document the frontend section below hands over.

## What the rebuilt output promises the frontend

The frontend half is not checked the same way, because nothing about it is a
file to compare. Whether a stylesheet reaches a page is decided by the
TypoScript that resolves for a site, so the check is that the route step 2 found
still names the file the build now writes.

- A rebuild that renames, hashes or moves an output breaks that route as
  silently as it breaks the import map, and there is no exception either. The
  symptom is a page rendered without the styles.
- `typo3_rule_lookup` with `documentId="any/assets/how-an-asset-reaches-a-page"`
  for the routes and the check that belongs to each. Which route a file takes is
  what step 2 established; whether that route still names the file the build now
  writes is what this step is.
- `typo3_hint_lookup` for `Resources/Public/` paths, which reaches
  `public-assets`: how a package's public files are published into the document
  root, and what makes one resolvable at all. That is version-bound, and a build
  directory outside the default paths is not the same question on every major.
- Where an output moved out of `Resources/Public/`, the publish step is the
  finding rather than the build, and the answer says which of the two it is.

## Closing the change

1. Report what was rebuilt, what the build printed, and which of the mapped
   paths and borrowed surfaces were verified on which majors — including the
   ones that came back withheld or unanswerable, by name.
2. Draft the message with `typo3_commit_message_guide` and `workflow="project"`.
   The change lands in the package's own repository.

This skill owns the TYPO3 half of a package's asset build: what the build is run
as, whether its output is committed and rebuilt with its source, what the import
map promises about the files that output consists of, and which majors a
borrowed core class or icon holds on. It does not own the migration a bundler or
a JavaScript library asks for on its own account, the range of TYPO3 majors the
package declares, the audit that decides what else is wrong with it, or the
harness that would prove the build — each of those is named above with the
workflow or the manual it belongs to.
