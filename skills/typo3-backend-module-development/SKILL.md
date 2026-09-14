---
name: typo3-backend-module-development
description: 'Build or change a TYPO3 backend module: registration, controllers, routes, backend templates, buttons, status markers, labels and icons. A content element''s backend preview in the page module is not a module and belongs to content-element work.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Backend Module Development

Establish the task's scope and query the sources that own each fact before you
write code. Keep this skill as routing only. Never keep markup, identifiers,
labels, API signatures, or version facts here.

## Gather the evidence

Work through [references/base.md](references/base.md) first. It fixes the order
every task here starts in and says why that order is not interchangeable.

Then, for this workflow:

- The base's `typo3_project_describe` discharges `typo3_server_scope`. That
  answer already states whether you can reach the installation this module
  registers into. The orientation tool is for a caller who does not know whether
  this server can answer at all. That is not this workflow's question.
- `typo3_backend_module_lookup` before you choose the module identifier, parent,
  position, route, or registration shape.
- `typo3_icon_lookup` for every proposed module or action icon. Do not invent an
  identifier.
- `typo3_label_lookup` with words from recurring backend wording before you add
  a label, and `typo3_translation_domain_lookup` for the extension's own XLF
  path.
- `typo3_component_lookup` with the target TYPO3 version before you write
  buttons, status markers, cards, tables, or other backend markup.
- `typo3_documentation_lookup` with several short English queries and the target
  TYPO3 version. Ask it for module registration, controller, routing, security,
  and other official API details.

If an installation-backed lookup is unavailable, report that gap and its
diagnosis. Do not turn it into an empty registry, and do not replace it with
memory. If live documentation is unavailable, keep the failure distinct from no
match.

## Settle the module's shape before the first file

Where the module hangs, its name, who may open it and what it keeps are one
decision, not four. Each of them constrains the next, and `typo3_task_guide`
returns what each costs to get wrong. Take them from what this installation
already registers rather than from the extension you copy.

Neither answers what the user decides: which parent an editor should find this
under, and whom it is for. Ask before you register rather than after. A module
stays placed for as long as it exists. The registration carries a rename
afterwards rather than undoes it.

## Implement and verify

- Read the existing extension and nearby modules that work before you edit. Tool
  answers describe conventions and registrations. They do not inspect the
  caller's changed files.
- Reuse the module API, backend components, labels, and registered icons. Avoid
  custom CSS that recreates the TYPO3 backend.
- Run the checks `typo3_project_describe` listed, and no other. A module lives
  in an extension, a sitepackage or a site project. The checks that hold it are
  the ones that repository declares. The core's own suites and rules are another
  workflow's and reach nothing here.
- Run the lookups again when the target version, extension, or implementation
  choice changes. Do not treat an earlier result as universal.

## Prove it by opening it

The work ends when you have opened the module, not when you have written its
files. Five things fail there and nowhere earlier. The entry appears where you
placed it, and the route answers. The icon renders. The labels resolve instead
of their keys. The declared access lets in whom it claims to.

A green suite reports none of the five. A registration file has no schema behind
it to fail against.

How a browser reaches it and where the throwaway spec goes is a procedure, not a
step to invent. It is `typo3_rule_lookup` with
`documentId="any/testing/browser-check"`. Ask for it here rather than at the
start. A session that wrote its own spec fifteen times had that id in the guides
list its first call returned. That was hundreds of turns earlier. It never
matched the id against a task it did not have yet.

To open it, you need a backend that runs. Where this repository has none, that
is a prerequisite of the task rather than a part of it. **Invoke
`typo3-development-installation` at the verified point, and come back to these
five.** That point is the module written and registered, and nothing open yet.
It brings an installation into existence or boots the one the repository
declares.

Where the user does not want that, report the module as written and unproven.
Say which of the five you did not establish. The lookups above exist so that you
do not correct a mistake from a test afterwards.

## Where the module is written up

**When you have verified the implementation and only documentation remains, stop
this workflow. Invoke `typo3-extension-documentation` before you edit
documentation.** Carry forward the extension key, the target version, and the
verified public behavior. Let that skill select the documentation surface.
Documentation for functionality an extension encapsulates belongs to that
extension, not to the project around it.

## Commit it

`typo3_commit_message_guide` with `workflow="project"` drafts the message and
checks it. A module in an extension, a sitepackage or a site project commits in
that repository. That is the workflow the argument names.

This skill owns backend module registration, controllers, routes, and backend UI
implementation, up to the moment you open the module. Test infrastructure is
`typo3-extension-testing`'s. A broader audit is `typo3-extension-health`'s. The
element in the page module is `typo3-content-element-development`'s. Stop before
implementing a content element or its backend preview. Stop before you edit any
of those owners' files. Carry across the extension key, the target version and
the behaviour you already verified.
