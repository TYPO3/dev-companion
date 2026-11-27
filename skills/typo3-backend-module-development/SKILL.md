---
name: typo3-backend-module-development
description: 'Build or change a TYPO3 backend module: registration, controllers, routes, backend templates, buttons, status markers, labels and icons. A content element''s backend preview in the page module is not a module and belongs to content-element work.'
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Backend Module Development

This skill covers a backend module in an extension, a sitepackage or a site
project. That is its registration, controllers, routes, templates and backend
UI. It ends when you have opened the module in a browser. It stops at three
other kinds of work:

- **A content element and its backend preview** in the page module are
  `typo3-content-element-development`.
- **Test infrastructure** is `typo3-extension-testing`.
- **A broader audit** of the extension is `typo3-extension-health`.

Stop before you implement a content element or its backend preview. Stop before
you edit files of those three skills. Hand over the extension key, the target
version and the behaviour you already verified.

Keep this skill as routing only: it says which tool to ask and when. Markup,
identifiers, labels, API signatures and version facts come from the lookups. A
copy of them here would go out of date in somebody else's project.

## Steps at a glance

1. Gather the evidence from the lookups.
2. Decide where the module goes and whom it is for.
3. Implement it and run the project's checks.
4. Open the module and check five things.
5. Hand the documentation to `typo3-extension-documentation`.
6. Commit it.

## 1. Gather the evidence

Work through [references/base.md](references/base.md) first. It fixes the order
every task starts in.

The base's `typo3_project_describe` discharges `typo3_server_scope`. That answer
already says whether this server can read the installation the module registers
into. So you do not need the second call.

Then call these lookups before you write the part each one answers:

- `typo3_backend_module_lookup` before you choose the module identifier, the
  parent module, the position, the route or the registration shape.
- `typo3_icon_lookup` for every icon of a module or an action. Do not invent an
  identifier.
- `typo3_label_lookup` before you add a label. Search with words from backend
  wording that recurs. Then call `typo3_translation_domain_lookup` for the XLF
  path of the extension.
- `typo3_component_lookup` with the target TYPO3 version before you write
  buttons, status markers, cards, tables or other backend markup.
- `typo3_documentation_lookup` with several short English queries and the target
  TYPO3 version. Ask it for module registration, controllers, routing, security
  and other official API details.

If a lookup that reads the installation is unavailable, report the gap and its
diagnosis. Do not report it as an empty registry, and do not fill it from
memory. If the live documentation is unavailable, report that as a failure, not
as "no match".

## 2. Decide where the module goes and whom it is for

Settle four things together before the first file:

- the parent module it appears under;
- its name;
- who may open it;
- what data it keeps.

Each one limits the next, so it is one decision and not four. `typo3_task_guide`
says what each one costs to get wrong. Take them from what this installation
already registers, not from the extension you copy.

Ask the user which parent an editor should find the module under, and whom it is
for. No lookup answers that. Ask before you register the module, not after. A
module keeps its place as long as it exists. A later rename needs a change to
the registration and does not undo the first one.

## 3. Implement and check

- Read the extension and the modules near it that work before you edit. The
  lookups describe conventions and registrations. They do not read the files you
  changed.
- Use the module API, the backend components, the existing labels and the
  registered icons. Do not write custom CSS that copies the TYPO3 backend.
- Run the checks `typo3_project_describe` listed, and no other. A module is in
  an extension, a sitepackage or a site project. The checks that apply are the
  ones that repository declares.
- Call the lookups again when the target version, the extension or the
  implementation choice changes. An earlier answer holds only for what you
  asked.

## 4. Open the module

The work ends when you have opened the module, not when you have written its
files. Five things fail only there:

1. The entry appears where you placed it.
2. The route answers.
3. The icon renders.
4. The labels show their text, not their keys.
5. The access you declared lets in the users it should.

A test suite that passes shows none of the five. No schema checks a registration
file.

Get the procedure for the browser from `typo3_rule_lookup` with
`documentId="any/testing/browser-check"`. It says how a browser reaches the
backend and where the throwaway spec goes. Do not write your own procedure. Ask
for it at this step, not at the start (`D-SKL-045`).

To open the module, you need a backend that runs. Where the repository has none,
that is a prerequisite of the task. **Invoke `typo3-development-installation` at
the verified point, and come back to these five.** The verified point is the
module written and registered, with nothing open yet. That skill creates an
installation, or starts the one the repository declares.

If the user does not want that, report the module as written and not proven. Say
which of the five you did not check.

## 5. Hand over the documentation

**When you have verified the implementation and only documentation remains, stop
this workflow. Invoke `typo3-extension-documentation` before you edit
documentation.** Hand over the extension key, the target version and the
verified public behaviour. That skill selects where the documentation goes. The
documentation of a function the extension contains belongs to that extension,
not to the project around it.

## 6. Commit it

Call `typo3_commit_message_guide` with `workflow="project"`. It drafts the
message and checks it. You commit the module in the repository of its extension,
sitepackage or site project. That is what `workflow="project"` means.
