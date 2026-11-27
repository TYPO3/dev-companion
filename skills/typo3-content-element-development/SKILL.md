---
name: typo3-content-element-development
description: Build or refactor TYPO3 content elements and the shared frame they all render in, replacing that frame included where a package drops the extension supplying it. Both sides, what an editor fills in and sees in the page module and what a visitor gets. Use for CType registration, TCA, child records, backend previews, TypoScript, Fluid, labels and tests.
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Content Element Development

This skill covers the content elements of an extension or a sitepackage, on both
sides. One side is what an editor fills in and sees in the page module. The
other is what a visitor gets. It also covers the shared frame every element
renders in. Where a package drops the extension that supplies the frame, it
covers the replacement too. It stops at a backend module of its own, at test
infrastructure, at manuals and at a package audit. "Where this skill stops"
names the skill for each.

**Design the editor workflow before you choose fields or rendering.** Build the
smallest model that keeps its content together and fits the project's file
organization.

Keep this skill as routing and design method: it says which tool to ask and in
which order to design. The versioned TYPO3 facts come from the lookups. A copy
of them here would go out of date in your project.

## Steps at a glance

1. Establish the evidence.
2. Choose the content model.
3. Settle the frame the element renders in.
4. Put each part of the element in its own file.
5. Implement the full lifecycle.
6. Look at the element in the frontend and the backend.
7. Verify it at the right layers.
8. Write it up, and commit it.

When something breaks at any step, follow "When something breaks". When the
request is about the whole package, follow "Where this skill stops".

## 1. Establish the evidence

1. Work through [references/base.md](references/base.md) first. It fixes the
   order every task starts in.

2. Then make the calls this workflow adds:

   - **`typo3_documentation_lookup`** for the version-specific TCA, DataHandler,
     Fluid or AssetCollector API.

   - **`typo3_label_lookup` and `typo3_icon_lookup`** before you add a label or
     choose an icon identifier. A label reference you copied from another
     extension is one your code uses, just like one you wrote. So a new
     extension with nothing of its own to reuse still has labels to check.

   - **`typo3_component_lookup`** with the target TYPO3 version before you write
     the markup of the backend preview. It gives the CSS classes and the custom
     properties the preview reads. A guessed custom property behind a CSS
     fallback renders, and it is wrong.

   - **`typo3_rule_lookup`** with
     `documentId="any/icons/drawing-a-content-icon"` where the element needs an
     icon of its own. That is where the icon lookup refused a borrowed
     identifier, or where the element is one of a set. The page gives the box
     every content icon shares and what the core's own set varies. It also gives
     the check that shows a set works.

3. Read the nearby content elements, TCA files, TypoScript imports, templates,
   assets, schema and tests. A new element has to fit the project's file
   organization, and only the checkout shows it.

## 2. Choose the content model

Describe how an editor creates, orders, translates, hides and deletes the
content before you implement it. Then choose one of four models:

- **Fields on `tt_content`** for one bounded element.

- **A child table of its own with `type=inline`** for a repeatable ordered
  collection: slides, tabs, accordions or cards.

- **References to existing records** only when reuse is an explicit requirement.
  First understand the lifecycle, visibility, localization and duplicate
  rendering.

- **A container of any `tt_content`** only when nested content of any type is a
  deliberate requirement. Do not use the generic `records` field in place of a
  child table for a repeatable collection.

Read [references/checklist.md](references/checklist.md) before you create or
change a content element.

### The record count decides where editors maintain the records

Read the count of a table, and do not estimate it. `typo3_record_lookup` with
the table gives the number of rows and the page they are on. You can read that
before any import runs.

The record list on a storage folder searches, sorts by column and pages. It does
not filter on the value of a field, and it groups nothing. So a table editors
work in at four figures is a long scroll on a slow page. That table needs a
module of its own.

**Where the count says so, invoke `typo3-backend-module-development`.** Decide
it in the TCA, before records exist. The fields the module filters and sorts by
are part of the same decision.

### The record count also decides what renders it

- A bounded element renders from TypoScript with a core data processor.
- A list that filters, pages, resolves a detail view or has its own routing is a
  plugin.

`typo3_hint_lookup` with `id=extbase` states that decision. A data processor
that somebody chose by default for a list shows it. You then find a second query
beside the list, request arguments read by hand, and page numbers computed by
hand. You find a storage pid checked again for one record.

## 3. Settle the frame the element renders in

Every element on a site renders inside one shared object. That object is the
content element FLUIDTEMPLATE, with the layout, partials and settings it
resolves. Either the site provides that object, or a system extension does. The
answer decides where the template of an element may look for a partial. It also
decides what a change to the layout costs every other element on the site.

1. **Find out who provides the frame before you write into it.** The answer of
   `typo3_extension_describe` in the base lists two things. One is the content
   types a package renders but does not register. The other is the shared plugin
   template the package ships. A package that renders an element it never
   registered has taken over the frame. A package that ships neither still uses
   the system extension.

2. **Where the task ends the dependency on that extension, make an inventory
   before the first edit.** Such a task says "remove it as a dependency",
   "rebuild what it provided" or "vendor it into the sitepackage". The inventory
   lists:

   - what the extension supplies;
   - which of that this installation uses;
   - which of that the site can do without.

   `typo3_record_lookup` answers the last two from the content that exists, not
   from what the templates allow. Its `groupBy` names the rows that differ from
   the default of a column. Each such row is a record that a branch written for
   the default alone breaks.

3. **Read the two hints about a removal.** A removal causes two failures that
   show no error, and neither is in the element you work on. `typo3_hint_lookup`
   with `id=sitepackage-fluid-styled-content` states what a removal must
   replace. `id=page-content-element-rendering` states the shared template roots
   and the collision they cause.

4. **Prove the frame by the markup, not by the files.** Render the pages that
   show each element before and after the change, and diff the HTML. A frame
   that resolves can still render differently. The element that changed is
   rarely the one you edited.

## 4. Put each part of the element in its own file

- Put shared CType groups and changes for every element in the generic
  `tt_content` override.
- Put the fields and the registration of one element in a named override beside
  it.
- Put a custom record table in its own TCA file.
- Put the rendering of one element in its own TypoScript file, in the project's
  directory for content elements.
- Put the Fluid template in the project's template root for content elements.
  Name it by the project's convention from CType to template.
- Load the CSS and JavaScript of one element from its template through the Fluid
  AssetCollector. Include an asset on every page only when the whole site needs
  it.

## 5. Implement the full lifecycle

- Configure sorting, workspaces, localization, enable fields and cascade
  behaviour for child records.
- Put backend field labels in domain label files, and text for visitors in
  frontend message files. Do not hard-code state labels in JavaScript.
- Add a useful backend preview for a custom CType.
- Use core data processors where they express the query. Add a custom processor
  only for behaviour the core processors cannot express.
- Output raw HTML in Fluid only for markup that a trusted TYPO3 rendering API
  already rendered.

## 6. Look at the element

**When the element renders at all, open it and look, in the frontend and in the
backend.** `typo3_rule_lookup` with `documentId="any/testing/browser-check"`
gives this step, and it is not the test suite of step 7. It says which
installation shows the case, how a browser reaches it, and where the harness
goes.

Look before you write a test. A test asserts what somebody already knows. If you
write it first, it records what you believe, not what is there. Two assertions
that then pass for the wrong reason are the usual result.

Look at the backend too. A frontend screenshot does not show the wizard entry,
the preview, the icon or the badges on the record.

## 7. Verify at the right layers

- Validate PHP, YAML, Fluid, XLIFF and TypoScript with the commands the project
  declares.

- Add unit tests only for isolated logic.

- Add functional tests for TCA, schema, inline persistence, localization and
  rendered output.

- A functional frontend subrequest proves the server-side HTML and the
  AssetCollector registration, and nothing more. It does not execute JavaScript,
  apply CSS, measure layout or prove interaction. State that limit in the
  report.

- Add browser tests when JavaScript interaction, the editor workflow or
  accessibility is part of the feature. Two guides answer this step:

  - `typo3_rule_lookup` with `documentId="any/testing/browser-check"` says how
    you look at the element in an installation that already holds the content.
    You do that before anything asserts a backend preview.
  - `documentId="project/testing/playwright"` is for a repository that has no
    browser suite yet. It gives the configuration, the backend login and a spec
    per project, whole. `typo3-extension-testing` establishes that suite.

- Run `typo3_extension_describe` again after the change. Report what its parser
  cannot see apart from defects in your implementation.

**Where the layer this element needs has no harness, invoke
`typo3-extension-testing` before you write the test.** Load the skill by name
and work from it. Hand over the extension key, the target version, the layer the
behaviour needs, and what you verified without it. This is a step of its own,
because sessions skipped the name in a last paragraph — `D-SKL-022`.

## 8. Write it up, and commit it

**When you have verified the element and something has to describe it, invoke
`typo3-extension-documentation` first.** Do that before you edit a manual or a
README. Hand over the extension key, the target version and the public behaviour
you verified. That skill chooses where the description goes.

Commit with `typo3_commit_message_guide` and `workflow="project"`. It drafts the
message and checks it. The element goes into an extension or a sitepackage, and
that is the workflow the argument names.

End the answer with each step of the base order you did not reach. Give each one
a line: `Not reached: <step> — <why>`.

## When something breaks

A symptom is a reason for a lookup, just like a task. `typo3_hint_lookup` takes
the observation as its `task`, for example "content elements in reverse order"
or "child rows that saved without their parent". It matches the observation
against what each hint states, so you need not name the subject first.

Make that call before reading the installed source. The base puts that reading
after the lookups, not in their place. An empty result is an answer, and the
reading comes after it.

## Where this skill stops

**When the user asks whether the package is sound, invoke
`typo3-extension-health` and work from its report.** A request about the package
asks for it, not one about this element. Examples are "review it", "what else is
wrong with it" and "is it ready". The request usually comes when the work here
looks finished. Hand over the extension key, the target version and what this
workflow verified.

A question about this element is work for this skill, not an audit. Examples are
"does the preview render", "is the inline relation right" and "why is the order
wrong". Where the request could be either, ask the user which one they mean.

The other skills cover these:

- a backend module of its own: `typo3-backend-module-development`;
- test infrastructure: `typo3-extension-testing`;
- manuals and READMEs: `typo3-extension-documentation`;
- an audit of the whole package: `typo3-extension-health`.

Do not edit the files of another skill's work. Hand over the extension key, the
target version and the behaviour you already verified.
