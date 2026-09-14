---
name: typo3-extension-asset-build
description: 'The asset build of a TYPO3 extension, sitepackage or project package: npm and package.json dependency updates, Dependabot pull requests, webpack, vite, Grunt or Sass, the built CSS and JavaScript under Resources/Public, the import map it reaches the backend by, and the core classes and icons it borrows. Stops at a bundler or library migration.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Asset Build

A package's own build produces the CSS and JavaScript its backend and frontend
load. Most of that task is npm's, the bundler's and the library's. What this
workflow orders is the TYPO3 half. That is what you run the build as, what its
output promises the backend, and which output the repository commits. Keep this
skill as routing and workflow.

Never keep a dependency version, a bundler configuration, a build command or a
core class name. Every one of those is a property of the repository in front of
you and of the majors it declares.

## The order

1. Work through [references/base.md](references/base.md). It fixes what this
   package is, which majors it declares, and what it runs its build with.
2. Establish which of the output the repository commits, and which route each
   file reaches a page by, below.
3. Verify every core surface the change will borrow, before you write it, below.
4. Change what the task asks for. Stop where the library's own migration begins.
5. Rebuild, and check what the output promises: the backend below, then the
   frontend below.
6. Commit the rebuilt artefacts together with the source that produced them.

The first step discharges `typo3_project_describe`. It reports the manifests
this repository keeps: at the root, and one directory down where the build sits
there.

   It reports the commands each of them declares with the manifest they came
   from. It says whether a command reports or changes. It reports the Node that
   the manifest, the pinned version, the CI workflow and the container each
   state. It names the disagreements between them.

   Run the commands as it reported them. An invocation you rewrite from habit
   runs the build in the wrong directory or on the wrong Node. Both of those
   surface as a diff nobody can explain.

**Read a pin against the release current on the day.** That step reports what
each source states, and none of them says whether it is still current. So
establish the current release where its publisher announces it. That is the
runtime's own release schedule, the package registry, the repository that tags
an action. Report every pin behind it as a finding that carries the raise.

What speaks against one is a bound this repository declares: the Node the build
needs, the majors the package supports. The finding then names the newest
release that bound allows. The raise is the maintainer's, and to move the bound
is a task of its own.

## Which of the output is committed, and how it reaches a page

This server does not read your working tree. So these are the repository's
answers and not a lookup. Does git track the built output? Does any check assert
that a build leaves the tree clean? Over which route does each output file reach
a page?

- **Committed output means source and output change together.** The package's
  consumers install what is in the repository. So a commit with new source and
  last month's artefacts ships the old behaviour to every one of them.
- **Uncommitted output means the deployment runs the build.** Then the artefacts
  are not yours to commit. What has to hold instead is that the build runs where
  the deployment runs it.
- A check that asserts a clean tree after a build is the executable form of that
  decision. Where the repository has none, its absence is a finding rather than
  a licence. `typo3-extension-testing` establishes that check. Invoke
  `typo3-extension-testing` for it, and carry the build command and the output
  paths you established here.
- **Each output file reaches a page through a declaration somewhere, and the
  route differs between the two halves.** An import map declares backend
  JavaScript. TypoScript, the AssetCollector behind `<f:asset.css>`, or
  `PageRenderer` includes a frontend stylesheet.

  Find which route this package uses for each file before you change anything.
  Step 5 checks that the same route still carries afterwards, and it cannot find
  a route it never saw.

## When the build is not reproducible

A build whose output differs on every run makes every later diff unreadable.
After a change the two causes look the same. Output differs because of your
change, or it differs whatever the source says.

- **The repository's own check answers this.** A job that asserts a clean tree
  after a build is the executable form of that question. Step 2 has already
  established whether there is one. Run it.
- Where the rebuild in step 5 produces a diff your change does not explain,
  build the unchanged checkout and compare. That separates the two causes. It
  costs nothing in the ordinary case, where the diff has an explanation.
- Where the tree comes back dirty on an unchanged checkout, that is the finding.
  It is about the toolchain rather than about the package. Say which file
  differs and how.
- Where the build does not run at all, first compare the reported Node with what
  this machine has. Reinstall the dependency tree second.

## Where this workflow stops

The bundler's configuration format, a library's own API change and a defect in
the runtime belong to that project's manual. Read them there, and say in the
answer which manual answered. A migration you reconstruct from the installed
sources of a dependency is a reading of one version of it. It is worth what it
says about that version alone.

Two changes look like this task and are not:

- The package moves to another set of TYPO3 majors, or what one of them removed
  broke it. That decides the whole reading below. So invoke
  `typo3-extension-upgrade` and carry across the build commands and the output
  paths you established.
- The request is an audit of the package rather than a change the user already
  agreed to. Invoke `typo3-extension-health` and carry across what you
  established about the build.

## Verifying a core surface before you borrow it

Verify a class or an icon the output takes from the core before you write it,
not after. Verified afterwards, it is already in the diff. The cost of a no is
then a second pass over markup that reads as finished.

- `typo3_documentation_lookup`, at each major the package declares, for the
  backend JavaScript module contract. It also says what an extension may assume
  is already loaded. To assume a library is present because the backend once
  shipped it is a decision, and this answer settles it.
- **The query names the component, and the answer places the class.**
  `typo3_component_lookup` with the `targetVersion` of a declared major returns
  each class with where it sits. That is around the component, on its root
  element, or inside it.

  `table-fit` is the element *around* a `.table`. Its name does not say so, and
  its own stylesheet rule does not either.
- **A class the answer does not place is one the core's stylesheet says nothing
  about.** That is not a licence to attach it anywhere. It means the position
  has to come from somewhere else. The entry names the core Sass file to read on
  that branch.
- **One call per declared major, because the position is itself version-bound.**
  A class can stand above its component on one major and not on another. So a
  surface verified on the installed major alone has no proof on the rest. The
  finding is the range it holds on. A borrowed surface not verified on the
  lowest declared major is a defect in that version.
- A class the package's own stylesheet only adds a rule to is one of these. It
  reads in the diff exactly like one the package owns.
- `typo3_icon_lookup` for a borrowed icon identifier. It answers from the
  installation. So it settles the installed major and says nothing about the
  others the package declares.
- `typo3_changelog_lookup`, restricted to each declared major, for a core asset
  the output stops relying on. To delete a rule because the core no longer ships
  the icon font it names is an unverified decision. So is to attach a class
  because the core does. The build goes green either way.
- `typo3_rule_lookup` with `documentId="any/backend/using-the-styleguide"` for
  what a styleguide demo states and what it does not. Read it before you take a
  demo as the contract for a component.

## What the rebuilt output promises the backend

Built backend JavaScript does not reach the backend because it is present. An
import map declares it, one specifier per file. A build that renames, splits,
hashes or drops an output breaks that map, and nothing fails in PHP. So after
every rebuild, check each mapped path against the file the build wrote.

A pipeline written for the frontend produces the wrong shape here: one hashed
bundle where the map names files. Nothing fails in PHP there either. The
document the frontend section below hands over says which file declares the map
and which check belongs to it.

## What the rebuilt output promises the frontend

You check the frontend half a different way, because nothing about it is a file
to compare. The TypoScript that resolves for a site decides whether a stylesheet
reaches a page. So the check is that the route step 2 found still names the file
the build now writes.

- A rebuild that renames, hashes or moves an output breaks that route as
  silently as it breaks the import map. There is no exception either. The
  symptom is a page rendered without the styles.
- `typo3_rule_lookup` with `documentId="any/assets/how-an-asset-reaches-a-page"`
  for the routes and the check that belongs to each. Step 2 established which
  route a file takes. This step checks whether that route still names the file
  the build now writes.
- `typo3_hint_lookup` for `Resources/Public/` paths, which reaches
  `public-assets`. It says how a package publishes its public files into the
  document root, and what makes one resolvable at all. That is version-bound. A
  build directory outside the default paths is not the same question on every
  major.
- Where an output moved out of `Resources/Public/`, the finding is the publish
  step rather than the build. The answer says which of the two it is.

## Closing the change

1. Report what you rebuilt and what the build printed. Report which of the
   mapped paths and borrowed surfaces you verified on which majors. Name the
   ones that came back withheld or unanswerable.
2. Draft the message with `typo3_commit_message_guide` and `workflow="project"`.
   The change lands in the package's own repository.

This skill owns the TYPO3 half of a package's asset build. That is what you run
the build as, and whether the repository commits its output with its source. It
is what the import map promises about the output files. It is which majors a
borrowed core class or icon holds on.

It does not own the migration a bundler or a JavaScript library asks for on its
own account. It does not own the range of TYPO3 majors the package declares. It
does not own the audit that decides what else is wrong with it. It does not own
the harness that would prove the build. The sections above name each of those
with the workflow or the manual it belongs to.
