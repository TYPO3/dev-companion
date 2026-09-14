---
name: typo3-extension-upgrade
description: 'Keep a TYPO3 extension, sitepackage or project package working on the TYPO3 and PHP versions it declares, or carry it to another set: code broken by what a supported major deprecated or removed, adding a new major, dropping one, and proving every version it claims.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Upgrade

A package is broken by what a major it already supports removed. Or it has to
support a range it does not declare yet. Both run in the same order, where each
step decides what the next one is worth. Keep this skill as routing and
workflow. Never keep version-specific APIs, constraints, replacements, or the
contents of a changelog. Every one of those is a property of the installation
you read and of the target you aim at.

## The order

1. Work through [references/base.md](references/base.md). It fixes what this
   package is and what it ships. Its last step before the checkout is the sweep
   of the installed core's deprecations over that surface. This workflow starts
   from the result of that sweep rather than restating it.
2. Widen the sweep, below, into the work list.
3. Settle the range the package has to serve, below. Not before: what breaks
   decides whether you can reach a range at all. Where nothing is being crossed,
   that range is the declared one. This step reads it rather than resolves it.
   It still decides what the two steps after it may do.
4. Change what the list justifies, and nothing else.
5. Prove it against every combination the package declares.

## Widen the sweep into a work list

The base sweeps one source. This workflow needs three, because each reaches call
sites the others cannot:

- **The changelog**, as the base sweeps it, and `typo3_changelog_lookup` again
  with `type: breaking`. Same majors, still no query and no tag. A review asks
  what will stop working. An upgrade also asks what already has.
- **The Extension Scanner**, in the installation's own Upgrade module. It needs
  a reachable backend and an administrator, and it reads the extension's
  installed files. It finds the call sites of what its matchers cover. The
  `FullyScanned` / `PartiallyScanned` tag the base carries out of the changelog
  says whether its silence means anything. A clean scan for a partially scanned
  entry is not a result. You find those call sites.
- **The deprecation annotations on what this package actually calls**, in the
  installed core and in the packages it depends on. A changelog entry is per
  release, and the core writes one. An annotation sits on the class, method or
  property itself. So only this source reaches a symbol deprecated outside the
  majors the sweep covered. Only it reaches one in a package that publishes no
  changelog at all. A class deprecated as a whole takes every call site of it
  with it.

The **target** is the major this work has to reach. That is the one you add, or,
where you add nothing, the declared one the code fails on.

Both the changelog and the scanner answer from the **installed core**. This
whole order rests on that boundary. They say what this package owes the majors
it already runs on. They do not know what the target major changed until the
installation is on it. Until then the target's changes come from official
documentation for that version, never from memory. A list of "what the new major
changed" written from recall reads exactly like one you looked up.

Once the installation is on the target, run the sweep again there. That second
pass says whether the work is complete.

Write the result down before you change a file. Write one entry per call site,
with the identifier, the path and line in this package, which declared major
deprecates or removes it, and which of the three established it. That list is
the work, and the result closes on it. Include the entries that came back empty,
with the majors they covered.

## Settle the range, rather than assert it

Where the work crosses no range, the first two entries are the whole of this
step. The declared range is what the fix has to hold on. The three below them
decide a constraint that does not move.

- The declared range is in the Composer manifest and in `ext_emconf.php`. The
  two either say the same thing, or the difference is itself a finding. For a
  non-Composer installation `ext_emconf.php` is the only constraint that
  governs.
- The PHP range is the intersection of what every declared TYPO3 major supports.
  It is never the PHP the current machine happens to run.
- Where the package requires a system extension, establish that the target still
  ships it. `typo3_system_extension_lookup` answers by key and package name and
  does not need it installed. One that stopped being part of the core is a
  requirement that cannot resolve. The replacement is a decision, not a rename.
- Let the dependency solver answer, and quote what it printed. A constraint that
  should work and one the solver accepts are different claims. The solver
  reports a third-party dependency without a release for the target as a
  conflict rather than as advice. That dependency then decides the schedule.
- To drop a major is the user's decision, never one you take to make the code
  simpler. When you widen to a new one, keep every version the package declares
  today unless the request says otherwise.

## The boundary of what may change

The **lowest declared major decides every shape in the package**. A registration
form, attribute or API introduced later cannot replace one that still has to
work there. So a runtime branch on the major and a registration written the
older way are what the declared range requires. They are not debt to clean up.
Say so where the code already does it. The alternative is an upgrade that breaks
the version it was told to keep.

Where a replacement belongs to a subsystem outside the base's scope, ask its
conventions before you write it. That is `typo3_hint_lookup` with the concrete
paths, and `typo3_documentation_lookup` with the target version where the
official API decides the shape. Where nothing in the declared range replaces a
removal, that is the answer. One package version cannot serve both. A silent
choice is how a supported version stops working without anyone's notice.

Change what the work list justifies. An upgrade is not a modernization, a
cleanup or a rewrite.

**Where the change in front of you is not on the list, invoke the workflow that
owns it.** Do not make it here. That is `typo3-extension-health` for what else
is wrong with the package, `typo3-extension-testing` for coverage the upgrade
wants but does not have, and `typo3-extension-documentation` for the manual that
now describes a different range. That is a step at the moment the reading turns
it up, not a note about ownership. Load the skill by name and work from it.

## Prove it on every version it claims

1. Build the matrix from the declaration, not from convenience. Take every TYPO3
   major the package declares against the PHP versions that major supports.
2. Resolve each cell before you run it. Treat a cell that will not resolve as a
   result — it is the finding. A skip there is what lets a package declare a
   version nobody has ever installed it on.
3. Run the repository's own commands per cell, the checks first. A step that
   runs in only one cell leaves the others unproven. That includes the ones the
   repository's CI declares. The installation supplies one cell. For every other
   one, `typo3_rule_lookup` with
   `documentId="extension/compatibility/running-on-a-declared-major-that-is-not-installed"`
   says how you make that cell exist beside it, what it costs the installation,
   and how you tell a cell that could have failed from one that could not.
4. Report the work list with every entry closed or explicitly left open. Report
   the resolutions with what the solver printed. Report what changed and what
   did not on purpose, and the matrix cell by cell. Name a cell nobody ran as
   unrun rather than leave it out. The matrix is the claim the package makes
   about itself. An unrun cell is the part of that claim nothing stands behind.
5. Draft the message with `typo3_commit_message_guide` and `workflow="project"`.
   The crossing lands in the package's own repository, and the range it now
   declares is what the message is about.

This skill owns what a package owes the TYPO3 majors it declares and the ones it
is meant to declare. That is the sweep that says what breaks and the constraints
that say what it may declare. It is the changes those two justify, and the proof
per declared combination.

It does not own the decision whether the package is otherwise sound. It owns
neither a missing harness nor a rewrite of the documentation. The sections above
name each of those with the workflow it belongs to, and the crossing to it is
explicit. State the verified point the upgrade reached. Stop before you edit
that owner's files. Carry across the range and the call sites you already
established.
