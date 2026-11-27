---
name: typo3-extension-upgrade
description: 'Keep a TYPO3 extension, sitepackage or project package working on the TYPO3 and PHP versions it declares, or move it to another set: code broken by what a supported major deprecated or removed, adding a new major, dropping one, and proving every version it claims.'
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Upgrade

This skill covers what a package must change to work on the TYPO3 majors it
declares or will declare. Use it in two cases. Code fails on a major the package
already declares, because that major deprecated or removed an API. Or the
package must support a major it does not declare yet. The skill stops at
anything outside the upgrade: other defects, missing tests and the manual. Step
3 names the skill for each.

**Find what breaks before you choose the range.** What breaks decides whether
the package can reach a range at all. So each step decides what the next one is
worth.

**An upgrade changes only what the work list justifies.** It is not a
modernization, a cleanup or a rewrite.

Keep this skill as routing and order. It never holds version-specific APIs,
constraints, replacements or changelog contents. Those come from the
installation you read and from the target version.

## Steps at a glance

1. Make the work list: every call site that breaks, from three sources.
2. Settle the range of TYPO3 and PHP versions the package must serve.
3. Change what the work list justifies, and nothing else.
4. Prove the result on every version the package declares, and report.

## 1. Make the work list

Work through [references/base.md](references/base.md) first. It establishes what
this package is and what it ships. Its last step before the checkout is the
sweep of the installed core's deprecations over those files. This workflow
starts from the result of that sweep and does not restate it.

Then add two more sources. Each one finds call sites the others cannot find.

- **The changelog**, as the base sweeps it. Call `typo3_changelog_lookup` again
  with `type: breaking`, for the same majors, still without a query or a tag. A
  review asks what will break. An upgrade also asks what already broke.

- **The Extension Scanner**, in the Upgrade module of the installation. It needs
  a backend you can reach and an administrator account. It reads the installed
  files of the extension, and finds call sites of what its matchers cover. The
  `FullyScanned` / `PartiallyScanned` tag on each changelog entry says whether
  an empty scan means anything. A clean scan for a partially scanned entry is
  not a result. You find those call sites yourself.

- **The deprecation annotations on what this package actually calls**, in the
  installed core and in the packages it depends on. The core writes one
  changelog entry per change and release. An annotation sits on the class,
  method or property itself. So only this source finds a symbol that a major
  outside the sweep deprecated. Only this source finds one in a package without
  a changelog. A class deprecated as a whole makes every call to it an entry.

The **target** is the major this work must reach. That is the major you add. If
you add none, it is the declared major the code fails on.

**The changelog and the scanner answer from the installed core.** They say what
the package must change for the majors the installation runs. They do not know
what the target major changed until the installation is on it. Until then, get
the changes of the target from the official documentation for that version,
never from memory. A list of changes written from memory looks exactly like one
you looked up.

When the installation is on the target, run the sweep again there. That second
sweep shows whether the work is complete.

Write the work list before you change a file. Give it one entry per call site:

- the identifier, with the path and line in this package;
- the declared major that deprecates or removes it;
- the source that found it.

Also list each source that found nothing, with the majors it covered. The work
list is the work, and the report closes on it.

## 2. Settle the range

If the work adds no major and drops none, the range is the declared one. Then
only the first two items below apply. They read the declared range, which the
fix must work on. The other three items decide a change of the range.

- **Read the declared range** from the Composer manifest and from
  `ext_emconf.php`. If the two differ, the difference is a finding. In an
  installation without Composer, only `ext_emconf.php` applies.

- **The PHP range** is the intersection of the PHP versions that every declared
  TYPO3 major supports. It is never the PHP version of the current machine.

- **Check each required system extension.** Ask `typo3_system_extension_lookup`
  whether the target still ships it. It answers by extension key and package
  name, also for an extension that is not installed. A system extension the core
  no longer ships is a requirement that cannot resolve. Its replacement is a
  decision, not a rename.

- **Let the dependency solver answer, and quote what it printed.** "This
  constraint should work" and "the solver accepts this constraint" are different
  claims. A third-party dependency without a release for the target shows up as
  a conflict. That dependency then decides when the upgrade can happen.

- **Only the user drops a major.** Never drop one to make the code simpler. When
  you add a major, keep every major the package declares today, unless the
  request says otherwise.

## 3. Change what the work list justifies

**The lowest declared major decides every shape in the package.** A later major
can introduce a new registration form, attribute or API. The new form cannot
replace the old one while the lowest major still needs the old one. So a runtime
check of the major, or a registration in the older form, is what the declared
range requires. It is not debt to clean up. Say so where the code already does
it. Otherwise the upgrade breaks the version it had to keep.

Before you write a replacement in a subsystem outside the base's scope, ask for
its conventions:

- `typo3_hint_lookup` with the concrete paths;
- `typo3_documentation_lookup` with the target version, where the official API
  decides the shape.

If nothing in the declared range replaces a removed API, say so. One version of
the package cannot then serve both majors. If you choose silently, a supported
version breaks and nobody notices.

**If a change in front of you is not on the work list, invoke the skill that
covers it.** Do not make the change here. Load that skill by name and work from
it, at the moment you find the change:

- `typo3-extension-health` for other defects in the package;
- `typo3-extension-testing` for tests the upgrade needs and the package does not
  have;
- `typo3-extension-documentation` for a manual that now describes another range.

Give that skill the range and the call sites you already found. Tell it the
point the upgrade reached, and do not edit its files from here.

## 4. Prove it on every version it claims

1. **Build the matrix from the declaration.** Take every TYPO3 major the package
   declares. Pair each one with every PHP version that major supports.

2. **Resolve each cell before you run it.** Treat a cell that does not resolve
   as a result — it is the finding. If you skip it, the package declares a
   version nobody ever installed it on.

3. **Run the repository's own commands in every cell, the checks first.** That
   includes the commands its CI declares. A command that runs in one cell only
   leaves the other cells unproven.

   The installation gives you one cell. For every other cell, call
   `typo3_rule_lookup` with
   `documentId="extension/compatibility/running-on-a-declared-major-that-is-not-installed"`.
   It says how to set up that cell beside the installation, and what that costs
   the installation. It also says how to tell a cell that could fail from a cell
   that could not.

4. **Report the result.** The report contains:

   - the work list, with every entry closed or left open on purpose;
   - each resolution, with what the solver printed;
   - what changed, and what did not change on purpose;
   - the matrix, cell by cell.

   Name a cell nobody ran as unrun, and do not leave it out. The matrix is what
   the package claims about itself. An unrun cell is a claim without proof.

   End the answer with each step of the base order you did not reach. Give each
   one a line: `Not reached: <step> — <why>`.

5. **Draft the commit message** with `typo3_commit_message_guide` and
   `workflow="project"`. The change goes into the package's own repository. The
   message is about the range the package now declares.
