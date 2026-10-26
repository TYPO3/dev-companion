---
name: typo3-sitepackage-development
description: 'Create a TYPO3 sitepackage or change how one is laid out: the extension, its site set, the page templates with header and footer, the page layouts editors choose, and the wiring into the site. Content elements, asset builds and the installation itself belong to their own workflows.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Sitepackage Development

Build the sitepackage a site renders through, or change its layout. Keep this
skill as routing and order. The tree, the TypoScript and the page layout rules
are lookups. A copy of them here is one nobody can correct.

**The work ends on a frontend that renders, one page for each page layout.** A
list of questions is not that result. Where the request leaves a choice open,
take the default this skill names and go on. Name every default in the report,
with the alternative and what it costs. Ask only for what neither the request
nor the project answers.

## Establish the site, then the package

Work through [references/base.md](references/base.md) first. It fixes the order
every task here starts in.

Then, for this workflow:

- Read the sites and their sets out of the base's `typo3_project_describe`. A
  sitepackage replaces the sets a site depends on, so note them now.
- Where a sitepackage exists already, `typo3_extension_describe` with its key
  says what it registers. Change that package rather than start a second one.
- Keep the core's content elements, and with them the dependency on
  fluid_styled_content, unless the user said otherwise. `typo3_hint_lookup` with
  `id="sitepackage-fluid-styled-content"` says what the other choice costs. Put
  that in the report.
- Take the vendor from the project's own packages, and the author from the root
  `composer.json` or the git configuration. Name the first page layout
  `Default`, so a page with no layout selected renders through it too.

## Choose where a new package comes from

There are two starts. Use the one the user named. Where the user named none, use
the builder, and name the other start in the report.

1. **The builder at get.typo3.org.** It generates a base package from one
   request. Fetch `https://get.typo3.org/api/doc.json` first. Take the endpoint,
   the fields and the values each one accepts from that schema, never from an
   example. Choose the base package that matches the fluid_styled_content
   decision. The TYPO3 version is the line the installation runs.
2. **A tree written by hand.** `typo3_hint_lookup` with
   `id="sitepackage-layout"` gives the tree and what each directory is for. Take
   this start where the builder does not offer the installed line, or does not
   answer.

`typo3_hint_lookup` with `id="sitepackage-start"` says what else creates a
sitepackage, and what each tool writes on the installed major.

**A generated package is a starting point.** Remove the empty directories and
files this site does not use, and say which ones you removed. Where the
builder's tree differs from what the hints below describe, keep one convention
for the whole package. Tell the user which one you kept and why.

## Lay it out

Ask `typo3_hint_lookup` for each part before you write it, by id:

- `sitepackage-templates` for the template root, the layouts and the partials.
- `sitepackage-backend-layouts` for the page layouts, where they live, and what
  a page with no layout selected renders.
- `sitepackage-typoscript-reference` for which package defines each object you
  reference.
- `page-content-areas` for the areas a page template may address.
- `page-variables-and-processors` for what a page template is handed: the
  settings of the set, the site, the page and the menu.

A page layout and its page template are one name in three places. Write them in
one step, not one after the other.

## Wire it in and prove it

The package is not done when its files exist. Work these steps in order:

1. Require the package through Composer, from the path it sits in.
2. Set it up with the console's `extension:setup`.
3. Add its set to the dependencies of the site configuration. Remove the sets it
   replaces.
4. Check the site's own `setup.typoscript`. `typo3_hint_lookup` with
   `id="installation-setup"` says what an installer leaves there.
5. Flush the caches, then render one page for each page layout.

How a browser reaches the page is `typo3_rule_lookup` with
`documentId="any/testing/browser-check"`. Ask for it at step 5, not earlier.
Where you could not render a layout, report it as unproven and name it.

## Where this stops

**When the user asks for a content element, invoke
`typo3-content-element-development`.** Carry across the extension key, the set
and the template root. **When the package needs an asset build, invoke
`typo3-extension-asset-build`** with the same three. Where the site has no
running installation to render in, **invoke `typo3-development-installation`**
at the point the package is written and wired.

The site configuration's languages, base and error handling are the user's.
Leave them as they are.

`typo3_commit_message_guide` with `workflow="project"` drafts the message for
the commit in the project repository.

This skill owns the sitepackage extension, its set, its page templates, its page
layouts and their wiring into the site. Stop before you edit another owner's
files.
