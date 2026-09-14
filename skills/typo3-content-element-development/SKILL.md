---
name: typo3-content-element-development
description: Build or refactor TYPO3 content elements and the shared frame they all render in, replacing that frame included where a package drops the extension supplying it. Both sides, what an editor fills in and sees in the page module and what a visitor gets. Use for CType registration, TCA, child records, backend previews, TypoScript, Fluid, labels and tests.
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Content Element Development

Design the editor workflow before you choose fields or rendering. Build the
smallest domain model that owns its content and follows the active project's
file organization. Keep this skill as routing and design method. Get versioned
TYPO3 facts from the MCP tools.

## Establish evidence

Work through [references/base.md](references/base.md) first. It fixes the order
every task here starts in and says why that order is not interchangeable.

Then, for this workflow:

- `typo3_documentation_lookup` for version-specific TCA, DataHandler, Fluid or
  AssetCollector APIs.
- `typo3_label_lookup` and `typo3_icon_lookup` before you add a label or choose
  an icon identifier. Your code consumes a label reference copied out of another
  extension as much as one you wrote. So a new extension with nothing of its own
  to reuse still has those to check.
- `typo3_component_lookup` with the target TYPO3 version before you write the
  backend preview's markup. That covers its CSS classes and the custom
  properties it reads. A guessed custom property behind a CSS fallback renders,
  and it is wrong.
- `typo3_rule_lookup` with `documentId="any/icons/drawing-a-content-icon"` where
  the element needs an icon of its own. That is where the icon lookup above
  refused a borrowed identifier, or where the element is one of a set. The page
  holds the box every content icon shares and what the core's own set varies
  between icons. It holds the check that says a set works.
- Read the nearby content elements, TCA files, TypoScript imports, templates,
  assets, schema and tests. The project's file organization is what a new
  element has to fit, and only the checkout has it.

## Choose the content model first

Describe how an editor creates, orders, translates, hides and deletes the
content before you implement it.

- Use fields on `tt_content` for one bounded element.
- Use a dedicated child table with `type=inline` when an element owns a
  repeatable ordered collection: slides, tabs, accordions or cards.
- Use references to existing records only when reuse is an explicit requirement.
  Understand the lifecycle, visibility, localization and duplicate rendering
  behavior first.
- Use a container of arbitrary `tt_content` only when arbitrary nested content
  is a deliberate requirement. Do not substitute the generic `records` field for
  an owned repeatable model.

**How many records the table will hold decides where editors maintain them. You
can know the count before any import runs.** Afterwards you read it rather than
estimate it. `typo3_record_lookup` with the table says how many rows there are
and which page they sit on. The record list on a storage folder searches, sorts
by column and pages. It filters on no field's value and groups nothing.

So a table an editor works in at four figures is a scroll on a page that opens
slowly. That is a module of its own. Invoke `typo3-backend-module-development`
where the count says so. Decide it with the TCA rather than retrofit it around
records that already exist. The fields the module filters and sorts by are the
same decision.

**The same count decides what renders it.** A bounded element renders from
TypoScript with a core data processor. A list that filters, pages, resolves a
detail view or carries its own routing is a plugin. `typo3_hint_lookup` with
`id=extbase` states that decision.

You can read off the processor what it looks like once somebody took it by
default. That is a second query beside the list, request arguments read by hand,
page numbers computed by hand. It is a storage pid re-checked for one record.

Read [references/checklist.md](references/checklist.md) before you create or
change a content element.

## Keep each element cohesive

- Put shared CType groups or truly cross-element changes in the generic
  `tt_content` override.
- Put one element's fields and registration in a named sibling override.
- Put a custom record table in its own TCA file.
- Put one element's rendering in a dedicated TypoScript file below the project's
  established content-element directory.
- Keep the Fluid template under the project's content-element template root.
  Follow the CType-to-template naming convention.
- Load element-only CSS and JavaScript from the template through the Fluid
  AssetCollector. Use global page inclusion only for assets the whole site
  requires.

## Settle the frame before the element that renders in it

Every element on a site renders inside one shared object. That is the content
element FLUIDTEMPLATE and the layout, partials and settings it resolves. A site
either owns that object or takes it from a system extension. Which of the two
decides where an element's template may look for a partial. It decides what a
change to the layout costs every other element on the site.

- Read who owns the frame before you write into it. Step 2's answer reports the
  content types a package renders and does not register. It reports the shared
  plugin template the package ships. A package that owns rendering for an
  element it never registered has taken the frame over. One that ships neither
  still stands on the system extension.
- Where the task is to end the dependency on that extension, the work is an
  inventory before an edit. Such a task says "remove it as a dependency",
  "rebuild what it provided", or "vendor it into the sitepackage". The inventory
  is what the extension supplies and which of that this installation reaches. It
  is which of those the site can do without. `typo3_record_lookup` answers the
  last two from the content that exists rather than from what the templates
  allow.

  Its `groupBy` names the rows that depart from a column's default. That is the
  single record a branch written for the default alone would break.
- Two of the failures a removal causes are silent, and neither is in the element
  you work on. `typo3_hint_lookup` with `id=sitepackage-fluid-styled-content`
  states what a removal owes. The shared roots and the collision they cause are
  `id=page-content-element-rendering`.
- Prove the frame by the markup rather than by the files. Render the pages that
  carry each element before and after, and diff the HTML. A frame that resolves
  is not a frame that renders the same. The element that changed is rarely the
  one you edited.

## Implement the full lifecycle

- Configure sorting, workspaces, localization, enable fields and cascade
  behavior for owned child records.
- Use domain label files for backend fields and frontend message files for
  visitor-facing text. Do not hard-code JavaScript state labels.
- Add a useful backend preview for a custom CType.
- Use Core data processors where they express the query. Add a custom processor
  only for behavior the Core processors cannot represent.
- Keep raw Fluid output limited to markup a trusted TYPO3 rendering API already
  rendered.

## Look up what broke

A symptom triggers a lookup, and not only a task does. `typo3_hint_lookup` takes
the observation as its `task` — content elements in reverse order, child rows
that saved without their parent. It matches the observation against what each
hint states, so you need not name the subject first. Make that call before
reading the installed source. The base fixes that reading as the step after the
lookups rather than one instead of them. An empty result is an answer, and the
reading follows it.

## Look at it before you assert anything about it

**When the element renders at all, open it and look, in the frontend and the
backend both.** `typo3_rule_lookup` with
`documentId="any/testing/browser-check"` is that step, and it is not the suite
below. It says which installation shows the case, how a browser reaches it and
where the harness goes.

A spec asserts what somebody already knows. So a session that writes one first
has written down what it believes rather than what is there. Two assertions that
then pass for the wrong reason are the ordinary result. People skip the editor's
half. The wizard entry, the preview, the icon and the badges on the record are
visible from no frontend screenshot.

## Verify at the right layers

- Validate PHP, YAML, Fluid, XLIFF and TypoScript through commands the project
  declares.
- Add unit tests only for isolated logic.
- Add functional coverage for TCA, schema, inline persistence, localization and
  rendered output.
- Treat a functional frontend subrequest as proof of server-side HTML rendering
  and AssetCollector registration only. It does not execute JavaScript, apply
  CSS, measure layout or prove interaction. Report that boundary explicitly.
- Add browser coverage when JavaScript interaction, editor workflow or
  accessibility is part of the feature. Two guides answer that step, and this
  page reconstructs neither. `typo3_rule_lookup` with
  `documentId="any/testing/browser-check"` says how you look at the element in
  an installation that already holds the content. That is the step before
  anything asserts a backend preview. `documentId="project/testing/playwright"`
  is for a repository that has no browser suite yet. It holds the configuration,
  the backend login and a spec per project, whole. `typo3-extension-testing`
  establishes that suite.
- Run `typo3_extension_describe` again after the change. Report parser blind
  spots apart from implementation defects.

**Where the layer this element needs has no harness, invoke
`typo3-extension-testing` before you write the test.** That is a step, not a
note about ownership. Load the skill by name and work from it. What crosses over
is the extension key, the target version and the layer the behaviour needs. It
is also what you verified without it.

It stands here as a step because the skill's name at the foot of this file did
not fire. A session followed this workflow to the commit on six elements and
wrote no test at any layer. It had read the empty list step 2 returned.

## Write the element up

**When you have verified the element and something has to describe it, invoke
`typo3-extension-documentation` first.** Do that before you edit a manual or a
README. What crosses over is the extension key, the target version and the
public behaviour you verified here. That skill selects the surface. The same
session wrote three README files by hand. Step 2 had reported no manual and no
README both times it ran.

## Commit the element

`typo3_commit_message_guide` with `workflow="project"` drafts the message and
checks it. The element lands in an extension or a sitepackage, which is the
workflow that argument names.

## Where this element ends and the package begins

**When the user asks whether the package is sound, invoke
`typo3-extension-health` and work from its report.** A request about the package
rather than about this element asks for it. That is "review it", "what else is
wrong with it", "is it ready". It arrives once the work here reads as finished,
which is the moment with the least appetite for another workflow.

What crosses over is the extension key, the target version and what this
workflow verified. Whoever asked for an unaudited package audits it. That
happened once, by hand and after delivery. Seven of the ten defects it turned up
sit inside that skill's stated surfaces.

**A question about this element is not that request.** "Does the preview
render", "is the inline relation right", "why is the order wrong": each is this
skill's own work. An answer is not an audit. Where the sentence could be either,
ask which the user meant.

This skill owns content-element architecture and implementation. A backend
module of its own is `typo3-backend-module-development`'s. Test infrastructure
is `typo3-extension-testing`'s. Manuals are `typo3-extension-documentation`'s,
and a broader extension audit is `typo3-extension-health`'s. Stop before you
edit that owner's files. Carry across the extension key, the target version and
the behaviour you already verified.
