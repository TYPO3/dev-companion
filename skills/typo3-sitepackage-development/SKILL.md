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
nor the project answers. The design is the one exception, in *Ask for the design
once*.

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
- Run the base's deprecation sweep with `tags` set to `TypoScript`, `TSConfig`,
  `Fluid`, `YAML` and `Frontend`, the kinds of file a sitepackage ships, and
  `excludeTags` set to `PHP-API`. Add the entries about a file the package
  ships, such as `ext_localconf.php`.
- Take the vendor from the project's own packages, and the author from the root
  `composer.json` or the git configuration. Name the first page layout
  `Default`, so a page with no layout selected renders through it too.

## Ask for the design once

**Where the request names no design, ask one question before the first
template.** Bundle these four points into it, each with the default you take:

- Colours and fonts. The default is a neutral palette and the system font stack.
- A logo and a favicon. The default is the site title as text and no favicon.
- A website, a mockup or a styleguide to follow. The default is none.
- A CSS framework. The default is plain CSS without a build step.

Say that one reply such as "use the defaults" is enough. Ask once, and do not
ask again for a point the user left out. Take its default.

Put colours and fonts into CSS custom properties. Make a value a setting of the
set where an editor changes it per site. A framework with a build step is
`typo3-extension-asset-build`'s, at the point the templates are written.

## Choose where a new package comes from

There are two starts. Use the one the user named. Where the user named none, use
the builder, and name the other start in the report.

1. **The builder at get.typo3.org.** It generates a base package from one
   request. Fetch `https://get.typo3.org/api/doc.json` first. Take the endpoint,
   the fields and the values each one accepts from that schema, never from an
   example. Choose the base package that matches the fluid_styled_content
   decision. The TYPO3 version is the line the installation runs. The request
   carries an author email to a service outside the project. Send a placeholder
   unless the user agreed, and write the real author into the local files. The
   builder takes the vendor from the company and the key from the title, so send
   the ones the package is meant to carry.
2. **A tree written by hand.** `typo3_hint_lookup` with
   `id="sitepackage-layout"` gives the tree and what each directory is for. Take
   this start where the builder does not offer the installed line, or does not
   answer.

`typo3_hint_lookup` with `id="sitepackage-start"` says what else creates a
sitepackage, and what each tool writes on the installed major.

**A generated package is a starting point.** Read each generated file against
the hints in *Lay it out* and the sweep before you keep it. Remove the empty
directories and the files this site does not use, and say which ones you
removed. Where the builder's tree differs from what the hints below describe,
keep one convention for the whole package. Tell the user which one you kept and
why.

## Lay it out

Ask `typo3_hint_lookup` for these parts in one call, with their ids in `ids`,
before you write them. Leave out the ones the brief of `typo3_task_guide`
already carries in its hints:

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
4. Find the installer's welcome page. `typo3_hint_lookup` with
   `id="installation-setup"` says where the installed major puts it: the site's
   own `setup.typoscript`, or a root `sys_template` row that clears. Replace the
   file's content with a comment, or hide the row. Delete neither, and report
   what it held.
5. Flush the caches, then render one page for each page layout. Where the
   installation has no page with a layout, set one page's layout in its page
   properties. Without a backend at hand, set its `backend_layout` field to
   `pagets__<identifier>` for the request, and put the old value back.
   `typo3_project_describe` names the database driver, and for SQLite the file
   to write in. Report both writes. A layout whose template addresses an area
   other than `main` also proves that the set's page TSconfig loaded.

A request to the page proves the render. Where the site's base names a host the
environment does not serve, `typo3_project_describe` says so and names the Host
header to send instead. For a look in a browser, ask `typo3_rule_lookup` with
`documentId="any/testing/browser-check"` at step 5, not earlier. Then open the
page module once, and check that each layout shows its title and columns. Where
you could not render a layout, report it as unproven and name it.

**Where no shell runs Composer and the console, stop after the files.** Leave
the site's dependencies as they are, because a set nothing installed breaks
every page. Hand the user steps 1 to 5 as commands, and say the frontend is
untested.

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
