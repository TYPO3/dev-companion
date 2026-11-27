---
name: typo3-extension-documentation
description: 'Create, update or review documentation for a TYPO3 project or extension: Documentation/ manuals, README, configuration and integration guides, examples, migration notes, screenshots and user-facing XLF labels.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Documentation

This skill covers the documentation of a TYPO3 project or extension and its
user-facing wording. That is the manual, the README, examples, migration notes
and XLF labels. It stops at the code the page describes. Where the page would
claim behaviour the code does not have, another skill changes the code first.
"Where this skill stops" names those skills.

**Document the implementation that exists.** The code in the checkout is the
source for what the package does. A page that claims more than the code does is
the defect this skill exists to prevent.

**Bind every TYPO3 claim to the active version.** An API, a module or an option
can differ between TYPO3 versions. A claim without a version can be wrong for
the reader's installation.

Keep this skill as routing and workflow: it says which tool to ask and in which
order. The API reference comes from the lookups. A copy of it here would go out
of date.

## Steps at a glance

1. Collect the evidence: the project, the code and the TYPO3 facts.
2. Choose where the documentation goes and who reads it.
3. Write the page.
4. Verify the page, and report.

When a page would claim behaviour the code does not have, follow "Where this
skill stops" before you write it.

## 1. Collect the evidence

1. Work through [references/base.md](references/base.md) first. It fixes the
   order every task starts in. This skill needs its second step most.
   `typo3_extension_describe` reports whether a manual and a README exist. It
   also reports the XLF files and the source language each one declares.

2. Call `typo3_documentation_lookup` for every claim about a TYPO3 API or
   workflow. Use several short English queries and the target version. Use the
   links it returns, not URLs you build by hand.

3. Call `typo3_label_lookup` before you add or reword a label. Call
   `typo3_translation_domain_lookup` for the XLF path of the extension itself.

4. Read the code: the public configuration, the registration files, the
   templates, the examples, the current manual and the tests. The code is the
   source for what this package implements.

5. Verify the backend facts the page names. Use `typo3_backend_module_lookup`
   for a module and `typo3_icon_lookup` for an icon. Use
   `typo3_fluid_namespace_list` for a Fluid prefix the template does not
   declare.

## 2. Choose where the documentation goes

Read [references/checklist.md](references/checklist.md) now. It says who the
reader is and which file serves that reader. It also has the rules for secrets
and for evidence that disagrees, and the check before you finish.

- **Extend the existing `Documentation/` structure.** Do not create a second
  manual layout.

- **Where step 1 found no manual, or one without `guides.xml`, set one up.**
  Call `typo3_rule_lookup` with `documentId="extension/documentation/manual"`.
  It gives the files that make a directory a manual, and the command that
  renders it before you publish.

- **Keep the README short:** the purpose, the essential setup, and the link to
  the manual where there is one.

- **Separate the readers.** Setup for administrators and developers goes apart
  from what editors do.

## 3. Write the page

- Take configuration keys, defaults, types and examples from the checkout.

- Mark a version requirement or a migration behaviour only with evidence. The
  evidence is the Composer constraints, the installed changelog or the official
  documentation.

- Write small examples that run, with the namespaces and the file layout of the
  extension.

- Keep the markup, the heading levels, the link style, the terms and the line
  wrap the repository already uses.

- Use the TYPO3 terms and the labels of the project.

- State the prerequisites, the commands, the expected result and what can go
  wrong.

Do not claim that a command, module, option, label or rendered result exists
before you check it. The check is the lookup for it or the code. Do not document
an internal detail as stable public API.

## 4. Verify and report

1. Check every internal link, referenced path, configuration example and code
   identifier against the checkout.

2. Check every docs.typo3.org link on the page with `typo3_permalink_lookup`.
   Give it the identifiers and the URLs together. It says what each one reaches.
   A link the host redirects still opens in a browser. But it can point at
   another branch than the page says. So a look at the page is not the check.

3. Run documentation, lint and test commands only where the project declares
   them or the task names them.

4. Report the files you changed and the checks you ran. Report every behaviour
   you could not verify in the installation or the project.

5. Write the commit message with `typo3_commit_message_guide` and
   `workflow="project"`. A manual ships from the repository it documents, and
   that is the workflow this argument names.

## Where this skill stops

**When a page would claim behaviour the code does not have, invoke the skill
that covers that code first.** This is a step, not a note. Load the skill by
name and work from it. The finding at that moment is that the behaviour is
missing, not the manual. If you write the page anyway, the manual promises more
than the package does.

Hand over the surface, the behaviour the page would claim, and its source files.
Then write the page from what comes back verified.

- **Tests** are `typo3-extension-testing`.
- **A conformance assessment** is `typo3-extension-health`.
- **Backend-module code** is `typo3-backend-module-development`.
- **A content element** is `typo3-content-element-development`.

Stop before you edit the files of those skills. Document only the public
behaviour that comes back verified.
