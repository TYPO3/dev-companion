---
name: typo3-extension-documentation
description: 'Create, update or review documentation for a TYPO3 project or extension: Documentation/ manuals, README, configuration and integration guides, examples, migration notes, screenshots and user-facing XLF labels.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Documentation

Document the implementation that exists, and bind TYPO3 claims to the active
version. Keep this skill as routing and documentation workflow. Do not copy API
reference material into it.

## Gather authoritative evidence

Work through [references/base.md](references/base.md) first. It fixes the order
every task here starts in and says why that order is not interchangeable. This
workflow leans hardest on its second step. `typo3_extension_describe` reports
whether a manual and a README exist at all. It reports which XLF files there
are, with the source language each declares.

Then, for this workflow:

- `typo3_documentation_lookup` with several short English queries and the target
  version, for every external TYPO3 API or workflow claim. Prefer the links it
  returns over hand-built documentation URLs.
- `typo3_label_lookup` before you add or reword a label, and
  `typo3_translation_domain_lookup` for the extension's own XLF path.
- Read its public configuration, registration files, templates, examples,
  existing manual and tests. Code is the source for what this package
  implements. Documentation that outruns it is the defect this workflow exists
  to avoid.

For backend documentation, verify modules through `typo3_backend_module_lookup`,
icons through `typo3_icon_lookup`, and undeclared Fluid prefixes through
`typo3_fluid_namespace_list` when those facts appear.

## Choose the documentation surface

Read [references/checklist.md](references/checklist.md) for the audience and
surface selection, secret hygiene, evidence conflicts, and the completion gate.

- Extend the existing `Documentation/` structure. Do not create a competing
  manual layout. Where step 2 reported no manual, or one older than
  `guides.xml`, call `typo3_rule_lookup` with
  `documentId="extension/documentation/manual"`. It holds the files that make a
  directory a manual, whole, and the command that renders it before you publish.
- Keep README content concise: the purpose, the essential setup, and the
  canonical documentation link where one exists.
- Separate the administrator and developer setup from the editor workflows.
- Derive configuration keys, defaults, types, and examples from the checkout.
- Mark a version requirement or a migration behavior only with evidence. That is
  Composer constraints, the installed changelog, or the official documentation.
- Use runnable, minimal examples that match the extension's namespaces and file
  layout.

Do not claim that a command, module, option, label, or rendered result exists
without a check with its owner. Do not document an internal implementation
detail as stable public API.

## Write and verify

- Preserve the repository's existing markup format, heading hierarchy, link
  style, terminology, and line-wrap conventions.
- Reuse canonical TYPO3 terminology and existing project labels.
- Make prerequisites, commands, expected outcomes, and failure conditions
  explicit.
- Validate internal links, referenced paths, configuration examples, and code
  identifiers against the checkout.
- Validate every docs.typo3.org link the page carries with
  `typo3_permalink_lookup`. It takes the identifiers and the URLs together and
  answers what each one reaches. A link the host redirects still resolves in a
  browser and points at another branch than the page claims. So a read of the
  page is not the check.
- Run documentation, lint, and test commands only when the project declares them
  or the task establishes them.
- Report the files you updated and the validation you performed. Report any
  behavior you could not verify from the installation or project.
- Draft the message for what you wrote with `typo3_commit_message_guide` and
  `workflow="project"`. A manual ships from the repository it documents, which
  is the workflow that argument names.

## Where the page stops and the code begins

**When a page would have to claim what the code does not do, invoke the skill
that owns that code first.** That is a step, not a note about ownership. Load
the skill by name and work from it. The finding at that moment is that the
behaviour is missing, not the manual.

If you write the page anyway, the manual outruns the package it ships with. What
crosses over is the surface, the behaviour the page was going to claim, and its
source files. You write the page from what comes back verified.

This skill owns documentation and user-facing wording changes. Test
implementation is `typo3-extension-testing`. Conformance assessment is
`typo3-extension-health`. Backend-module code is
`typo3-backend-module-development`, and a content element is
`typo3-content-element-development`. Stop before you edit that owner's files.
Document only the public behaviour it hands back verified.
