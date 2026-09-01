---
name: typo3-content-element-development
description: Build or refactor TYPO3 content elements on both sides — what an editor fills in and sees in the page module, and what a visitor gets rendered. Use for CType registration, TCA, inline child records, a custom backend preview, TypoScript, Fluid, assets, labels and tests.
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Content Element Development

Design the editor workflow before choosing fields or rendering. Build the
smallest domain model that owns its content and follows the active project's
file organization. Keep this skill as routing and design method; obtain
versioned TYPO3 facts from the MCP tools.

## Establish evidence

Work through [references/base.md](references/base.md) first — it fixes the order
every task here starts in and why that order is not interchangeable.

Then, for this workflow:

- `typo3_documentation_lookup` for version-specific TCA, DataHandler, Fluid or
  AssetCollector APIs.
- `typo3_label_lookup` and `typo3_icon_lookup` before adding a label or choosing
  an icon identifier. A label reference copied out of another extension is
  consumed by your code as much as one you wrote, so a new extension with
  nothing of its own to reuse still has those to check.
- `typo3_component_lookup` with the target TYPO3 version before writing the
  backend preview's markup, its CSS classes or the custom properties it reads. A
  guessed custom property behind a CSS fallback renders and is wrong.
- Read the nearby content elements, TCA files, TypoScript imports, templates,
  assets, schema and tests — the project's file organization is the thing a new
  element has to fit, and only the checkout has it.

## Choose the content model first

Describe how an editor creates, orders, translates, hides and deletes the
content before implementing it.

- Use fields on `tt_content` for one bounded element.
- Use a dedicated child table with `type=inline` when an element owns a
  repeatable ordered collection such as slides, tabs, accordions or cards.
- Use references to existing records only when reuse is an explicit requirement
  and the lifecycle, visibility, localization and duplicate rendering behavior
  are understood.
- Use a container of arbitrary `tt_content` only when arbitrary nested content
  is a deliberate requirement. Do not substitute the generic `records` field for
  an owned repeatable model.

**How many records the table will hold decides where they are maintained, and
the count is knowable before any import runs.** The record list on a storage
folder searches, sorts by column and pages; it filters on no field's value and
groups nothing, so a table an editor works in at four figures is a scroll on a
page that has become slow to open. That is a module of its own — invoke
`typo3-backend-module-development` where the count says so, and decide it with
the TCA rather than retrofitting it around records that already exist, because
the fields the module filters and sorts by are the same decision.

**The same count decides what renders it.** A bounded element renders from
TypoScript with a core data processor; a list that filters, pages, resolves a
detail view or carries its own routing is a plugin, and `typo3_hint_lookup` with
`id=extbase` is where that decision is stated. What it looks like once it has
been taken by default is readable off the processor: a second query beside the
list, request arguments read by hand, page numbers computed, a storage pid
re-checked for one record.

Read [references/checklist.md](references/checklist.md) before creating or
changing a content element.

## Keep each element cohesive

- Put shared CType groups or truly cross-element changes in the generic
  `tt_content` override.
- Put one element's fields and registration in a named sibling override.
- Put a custom record table in its own TCA file.
- Put one element's rendering in a dedicated TypoScript file below the project's
  established content-element directory.
- Keep the Fluid template under the project's content-element template root and
  follow the CType-to-template naming convention.
- Load element-only CSS and JavaScript from the template through the Fluid
  AssetCollector. Use global page inclusion only for assets required site-wide.

## Implement the full lifecycle

- Configure sorting, workspaces, localization, enable fields and cascading
  behavior for owned child records.
- Use domain label files for backend fields and frontend message files for
  visitor-facing text. Do not hard-code JavaScript state labels.
- Add a useful backend preview for a custom CType.
- Use Core data processors where they express the query; add a custom processor
  only for behavior the Core processors cannot represent.
- Keep raw Fluid output limited to markup already rendered by a trusted TYPO3
  rendering API.

## Look up what broke

A symptom is a lookup trigger, and not only a task is. `typo3_hint_lookup` takes
the observation as its `task` — content elements in reverse order, child rows
that saved without their parent — and matches it against what each hint states,
so the subject does not have to be named first. Make that call before reading
the installed source, which the base fixes as the step after the lookups rather
than one instead of them. Finding nothing is an answer, and the reading is what
follows it.

## Verify at the right layers

- Validate PHP, YAML, Fluid, XLIFF and TypoScript through commands the project
  actually declares.
- Add unit tests only for isolated logic.
- Add functional coverage for TCA, schema, inline persistence, localization and
  rendered output.
- Treat a functional frontend subrequest as proof of server-side HTML rendering
  and AssetCollector registration only. It does not execute JavaScript, apply
  CSS, measure layout or prove interaction. Report that boundary explicitly.
- Add browser coverage when JavaScript interaction, editor workflow or
  accessibility is part of the feature. Two guides answer that step and neither
  is reconstructed here: `typo3_rule_lookup` with
  `documentId="any/testing/browser-check"` for looking at the element in an
  installation that already holds the content, which is the step before anything
  asserts a backend preview, and with `documentId="project/testing/playwright"`
  for a repository that has no browser suite yet, which is the configuration,
  the backend login and a spec per project, whole. Establishing that suite is
  `typo3-extension-testing`'s.
- Re-run `typo3_extension_describe` after the change and report parser blind
  spots separately from implementation defects.

**Where the layer this element needs has no harness, invoke
`typo3-extension-testing` before writing the test.** That is a step, not a note
about ownership: load the skill by name and work from it. What crosses over is
the extension key, the target version, the layer the behaviour needs and what is
verified without it. It is written as a step because naming that skill at the
end of this file did not fire as one: a session followed this workflow to the
commit on six elements and wrote no test at any layer, having read the empty
list step 2 returned.

## Write the element up

**When the element is verified and something has to describe it, invoke
`typo3-extension-documentation` before editing a manual or a README.** What
crosses over is the extension key, the target version and the public behaviour
verified here; that skill selects the surface. The same session wrote three
README files by hand, with step 2 reporting no manual and no README both times
it was called.

## Commit the element

`typo3_commit_message_guide` with `workflow="project"` drafts the message and
checks it. The element lands in an extension or a sitepackage, which is the
workflow that argument names; the default is the core's.

## Where this element ends and the package begins

**When you are asked whether the package is sound, invoke
`typo3-extension-health` and work from its report.** What asks for it is a
request about the package rather than about this element — "review it", "what
else is wrong with it", "is it ready" — and it arrives once the work here reads
as finished, which is the moment with the least appetite for opening another
workflow. What crosses over is the extension key, the target version and what
this workflow verified. A package handed over unaudited is audited by whoever
asked for it: that happened once, by hand and after delivery, and seven of the
ten defects it turned up sit inside that skill's stated surfaces.

**A question about this element is not that request.** "Does the preview
render", "is the inline relation right", "why is the order wrong" — each is this
skill's own work, and answering it is not an audit. Where the sentence could be
either, ask which was meant.

This skill owns content-element architecture and implementation. A backend
module of its own is `typo3-backend-module-development`'s, test infrastructure
is `typo3-extension-testing`'s, manuals are `typo3-extension-documentation`'s
and a broader extension audit is `typo3-extension-health`'s — stop before
editing that owner's files, and carry across the extension key, the target
version and the behaviour already verified.
