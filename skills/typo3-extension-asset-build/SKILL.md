---
name: typo3-extension-asset-build
description: 'The asset build of a TYPO3 extension, sitepackage or project package: npm and package.json dependency updates, Dependabot pull requests, webpack, vite, Grunt or Sass, the built CSS and JavaScript under Resources/Public, the import map it reaches the backend by, and the core classes and icons it borrows. Stops at a bundler or library migration.'
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Asset Build

This skill covers the TYPO3 half of a package's asset build. That is the
commands the build runs with, and whether the repository commits the built CSS
and JavaScript. It is also the route by which each built file reaches a page. It
is also the core classes and icons the output uses, and on which majors they are
valid. npm, the bundler and the libraries do the rest. This skill stops at the
migration a bundler or a library asks for. It also stops at a change of the
supported TYPO3 majors. An audit of the package and the tests for the build are
other skills.

**A built file reaches a page only by a route.** A route is a declaration that
names the file. An import map is the route for backend JavaScript. TypoScript,
`<f:asset.css>` or `PageRenderer` is the route for a frontend stylesheet. A
build that renames, hashes, splits or drops a file breaks its route. Nothing
fails in PHP, so only a check after the rebuild finds it.

Keep this skill as routing and workflow: it says which tool to ask and when to
stop. Do not write a dependency version, a bundler configuration, a build
command or a core class name into it. Each of those comes from the repository in
front of you and from the majors it declares.

## Steps at a glance

1. Establish the package, its majors and its build commands.
2. Find out whether the repository commits the output, and the route of each
   file.
3. Verify each core class and icon before the change uses it.
4. Make the change the task asks for, and stop where a migration begins.
5. Rebuild, then check the backend routes and the frontend routes.
6. Report, and commit the rebuilt files with their source.

If the rebuild gives a diff your change does not explain, read
[references/unreproducible-build.md](references/unreproducible-build.md). Read
it too if the build does not run at all.

## 1. Establish the package and its build

1. Work through [references/base.md](references/base.md) first. It fixes what
   this package is, which majors it declares and how it builds.

2. The first step of the base discharges `typo3_project_describe`. Its answer
   gives you:

   - the manifests of the repository, at the root and one directory down;

   - the commands each manifest declares, and whether a command reports or
     changes;

   - the Node version that the manifest, the version file, the CI workflow and
     the container each state;

   - where those Node versions disagree.

   Run the commands exactly as the answer reports them. A command you rewrite
   from habit runs in the wrong directory or on the wrong Node. Both show up as
   a diff nobody can explain.

3. **Read a pin against the release current on the day.** The answer says what
   each source states. It does not say whether that is still current. So find
   the current release where its publisher announces it:

   - the release schedule of the runtime;
   - the package registry;
   - the repository that tags an action.

   Report every pin behind it as a finding that carries the raise.

   A bound this repository declares speaks against a raise. Examples are the
   Node the build needs and the majors the package supports. Then the finding
   names the newest release that bound allows. The maintainer decides the raise.
   A change of the bound is a separate task.

## 2. Find what the repository commits, and the route of each file

This server does not read your working tree. So answer three questions from the
repository itself:

- Does git track the built output?
- Does a check assert that a build leaves the tree clean?
- What is the route of each output file?

**Committed output: source and output change together.** The users of the
package install what is in the repository. A commit with new source and old
built files gives all of them the old behaviour.

**Uncommitted output: the deployment runs the build.** Then do not commit the
built files. Make sure instead that the build runs where the deployment runs it.

**A missing clean-tree check is a finding.** A check that asserts a clean tree
after a build enforces the choice above. Its absence does not permit you to
ignore that choice. `typo3-extension-testing` adds that check. Invoke
`typo3-extension-testing` for it. Give it the build command and the output paths
from this step.

**Find the route of each output file before you change anything.** The backend
and the frontend use different routes:

- Backend JavaScript: an import map, with one specifier per file.
- A frontend stylesheet: TypoScript, the AssetCollector behind `<f:asset.css>`,
  or `PageRenderer`.

Step 5 checks that the same route still names the file. It cannot check a route
it never saw.

## 3. Verify each core class and icon before the change uses it

Verify a class or an icon the output takes from the core before you write it. A
check after the change finds it already in the diff. Then a no costs a second
pass over markup that looks finished.

Call these in this order:

1. `typo3_documentation_lookup`, at each major the package declares, for the
   contract of a backend JavaScript module. It also says what an extension can
   expect the backend to load already. Do not assume a library is there because
   the backend once shipped it. This answer decides that.

2. `typo3_component_lookup` with the `targetVersion` of each declared major.
   Name the component in the query. The answer says where each class sits:
   around the component, on its root element, or inside it.

   Example: `table-fit` sits on the element *around* a `.table`. Neither its
   name nor its stylesheet rule says so.

   - **Make one call per declared major.** A class can sit above its component
     on one major and not on another. So the finding is the range of majors the
     class is valid on. A class you did not verify on the lowest declared major
     is a defect on that major.

   - **A class the answer does not place is one the core stylesheet says nothing
     about.** That does not permit you to put it anywhere. The position must
     come from another source. The entry names the core Sass file to read on
     that branch.

   - **A class the package's own stylesheet only adds a rule to is a core class
     too.** In the diff it looks exactly like a class of the package.

3. `typo3_icon_lookup` for an icon identifier from the core. It answers from the
   installation. So it settles the installed major and nothing about the other
   declared majors.

4. `typo3_changelog_lookup`, restricted to each declared major, for a core asset
   the output stops using. Do not delete a rule only because the core no longer
   ships the icon font it names. Do not add a class only because the core uses
   it. The build passes either way, so only this answer decides.

5. `typo3_rule_lookup` with `documentId="any/backend/using-the-styleguide"`
   before you take a styleguide demo as the contract for a component. It says
   what a demo states and what it does not.

## 4. Make the change

Change what the task asks for. Stop where the migration of a bundler or a
library begins.

A bundler's configuration format, a library's API change and a runtime defect
are in that project's manual. Read them there. Say in the answer which manual
answered. A migration you rebuild from the installed sources of a dependency
describes that one version only.

Two requests look like this task and are not:

- **The package moves to other TYPO3 majors**, or a major removed something it
  used. The majors decide every lookup of step 3. So invoke
  `typo3-extension-upgrade`. Give it the build commands and the output paths.

- **The request is an audit of the package**, not a change the user already
  agreed to. Invoke `typo3-extension-health`. Give it what you found about the
  build.

## 5. Rebuild and check the routes

Rebuild with the command step 1 reported. Then check both halves.

### The backend

Check each path in the import map against the file the build wrote. A build that
renames, splits, hashes or drops a file breaks the map. Nothing fails in PHP.

A pipeline written for the frontend gives the wrong shape here: one hashed
bundle where the map names single files. The document of the frontend check says
which file declares the map, and which check belongs to it.

### The frontend

Here there is no file to compare. The TypoScript that resolves for a site
decides whether a stylesheet reaches a page. So check that the route from step 2
still names the file the build now writes.

- Call `typo3_rule_lookup` with
  `documentId="any/assets/how-an-asset-reaches-a-page"`. It gives each route and
  the check for it.

- A renamed, hashed or moved file breaks the route silently. There is no
  exception. The symptom is a page without its styles.

- Call `typo3_hint_lookup` for `Resources/Public/` paths. It returns the
  `public-assets` hint. That hint says how a package publishes its public files
  into the document root. It also says what makes a file resolvable at all. That
  differs between majors, so check a build directory outside the default paths
  on each major.

- Where an output moved out of `Resources/Public/`, the problem is the publish
  step, not the build. Say in the answer which of the two it is.

## 6. Report and commit

1. Report what you rebuilt and what the build printed.

2. Report which mapped paths, core classes and icons you verified, and on which
   majors. Name each lookup that came back withheld or without an answer.

3. Where step 2 found committed output, commit the rebuilt files together with
   the source that produced them.

4. Write the message with `typo3_commit_message_guide` and `workflow="project"`.
   The change lands in the package's own repository.

## Where this skill stops

- **A bundler or library migration** is in that project's manual.
- **A change of the supported TYPO3 majors** is `typo3-extension-upgrade`.
- **An audit of the package** is `typo3-extension-health`.
- **The check that proves the build** is `typo3-extension-testing`.
