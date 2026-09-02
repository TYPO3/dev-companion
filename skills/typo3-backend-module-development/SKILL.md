---
name: typo3-backend-module-development
description: 'Build or change a TYPO3 backend module: registration, controllers, routes, backend templates, buttons, status markers, labels and icons. A content element''s backend preview in the page module is not a module and belongs to content-element work.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Backend Module Development

Establish the task's scope and query the sources that own each fact before
writing code. Keep this skill as routing only; never retain markup, identifiers,
labels, API signatures, or version facts here.

## Gather the evidence

Work through [references/base.md](references/base.md) first — it fixes the order
every task here starts in and why that order is not interchangeable.

Then, for this workflow:

- `typo3_server_scope` is discharged by the base's `typo3_project_describe`:
  whether the installation this module registers into can be reached is what
  that answer already states. The orientation tool is for a caller who does not
  know whether this server can answer at all, which is not this workflow's
  question.
- `typo3_backend_module_lookup` before choosing the module identifier, parent,
  position, route, or registration shape.
- `typo3_icon_lookup` for every proposed module or action icon. Do not invent an
  identifier.
- `typo3_label_lookup` with words from recurring backend wording before adding a
  label, and `typo3_translation_domain_lookup` for the extension's own XLF path.
- `typo3_component_lookup` with the target TYPO3 version before writing buttons,
  status markers, cards, tables, or other backend markup.
- `typo3_documentation_lookup` with several short English queries and the target
  TYPO3 version for module registration, controller, routing, security, and
  other official API details.

If an installation-backed lookup is unavailable, report that gap and its
diagnosis. Do not turn it into an empty registry or replace it with memory. If
live documentation is unavailable, keep the failure distinct from no match.

## Settle the module's shape before the first file

Where the module hangs, what it is called, who may open it and what it keeps per
user are one decision rather than four, because each of them constrains the next
— and `typo3_task_guide` returns what each costs to get wrong. Take them from
what this installation already registers rather than from the extension being
copied.

What neither answers is the user's: which parent an editor should find this
under, and whom it is for. Ask before registering rather than after, because a
module is placed for as long as it exists and a rename is carried by the
registration afterwards rather than undone.

## Implement and verify

- Read the existing extension and nearby working modules before editing. Tool
  answers describe conventions and registrations; they do not inspect the
  caller's changed files.
- Reuse the module API, backend components, labels, and registered icons. Avoid
  custom CSS that recreates the TYPO3 backend.
- Run the checks `typo3_project_describe` listed, and no other: a module lives
  in an extension, a sitepackage or a site project, and the checks that hold it
  are the ones that repository declares. The core's own suites and rules are
  another workflow's and reach nothing here.
- Re-run the lookups when the target version, extension, or implementation
  choice changes; do not treat an earlier result as universal.

## Prove it by opening it

The work is done when the module has been opened, not when its files are
written. Five things fail there and nowhere earlier: the entry appears where it
was placed, the route answers, the icon renders, the labels resolve instead of
showing their keys, and the access declared lets in whom it claims to. A green
suite reports none of the five, and a registration file has no schema behind it
to fail against.

Opening it needs a backend that runs. Where this repository has none, that is a
prerequisite of the task rather than a part of it. **Invoke
`typo3-development-installation` at the verified point — the module is written
and registered, and nothing has been opened — and come back to these five.** It
brings an installation into existence or boots the one the repository declares.
Where that is not wanted, report the module as written and unproven and say
which of the five is unestablished. Correcting a mistake from a test afterwards
is what the lookups above are there to make unnecessary.

## Where the module is written up

**When implementation is verified and only documentation remains, stop this
workflow and invoke `typo3-extension-documentation` before editing
documentation.** Carry forward the extension key, target version, and verified
public behavior; let that skill select the documentation surface. Documentation
for functionality encapsulated in an extension belongs to that extension, not to
the project around it.

## Commit it

`typo3_commit_message_guide` with `workflow="project"` drafts the message and
checks it: a module in an extension, a sitepackage or a site project is
committed in that repository, which is the workflow the argument names.

This skill owns backend module registration, controllers, routes, and backend UI
implementation, up to the module being opened. Test infrastructure is
`typo3-extension-testing`'s, a broader audit is `typo3-extension-health`'s, and
the element in the page module is `typo3-content-element-development`'s — stop
before implementing a content element or its backend preview, stop before
editing any of those owners' files, and carry across the extension key, the
target version and the behaviour already verified.
