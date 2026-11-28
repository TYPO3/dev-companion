---
name: typo3-sitepackage-development
description: 'Create a TYPO3 sitepackage or change how one is laid out: the extension, its site set, the page templates with header and footer, the page layouts editors choose, and the wiring into the site. Content elements, asset builds and the installation itself belong to their own workflows.'
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Sitepackage Development

This skill covers the sitepackage a site renders through. That is the extension,
its site set, its page templates and its page layouts. It also covers how the
site loads the package. It stops before content elements, which
`typo3-content-element-development` covers. It also stops before an asset build
and before the installation itself.

**The work ends on a frontend that renders, one page for each page layout.** Do
not end the work with a list of questions. Where the request leaves a choice
open, take the default this skill names and go on. In the report, name each
default, the alternative and what the alternative costs. Ask only for what
neither the request nor the project answers. The design is the one exception, in
step 2.

Keep this skill as routing and order: it says which tool to ask and when. The
tree, the TypoScript and the rules for page layouts come from the lookups. A
copy of them here would go out of date, and nobody could correct it.

## Steps at a glance

1. Establish the site and the package.
2. Ask for the design once, or convert the design the user gave you.
3. Choose where a new package comes from.
4. Lay out the package.
5. Install the package in the site.
6. Render one page for each page layout.
7. Read what the finished site serves.
8. Hand over what is outside this skill, and commit.

## 1. Establish the site and the package

1. Work through [references/base.md](references/base.md) first. It sets the
   order every task starts in.

2. Read the sites and their sets from the base's `typo3_project_describe`. Note
   the sets each site depends on. A sitepackage replaces them.

3. Where a sitepackage exists already, call `typo3_extension_describe` with its
   key. The answer says what the package registers. Change that package, and do
   not start a second one.

4. Keep the core's content elements and the dependency on fluid_styled_content.
   Change this only when the user asks. `typo3_hint_lookup` with
   `id="sitepackage-fluid-styled-content"` says what the other choice costs. Put
   that cost in the report.

5. Run the deprecation sweep of the base with `tags` set to `TypoScript`,
   `TSConfig`, `Fluid`, `YAML` and `Frontend`. Send `excludeTags` set to
   `PHP-API`. These tags are the kinds of file a sitepackage ships. Also keep
   the entries about a file the package ships, such as `ext_localconf.php`.

6. Take the vendor from the project's own packages. Take the author from the
   root `composer.json` or from the git configuration.

7. Name the first page layout `Default`. A page with no layout selected then
   renders through it too.

## 2. Settle the design

### No design in the request: ask once

**Where the request names no design, ask one question before the first
template.** Put these four points into it, each with the default you take:

- Colours and fonts. The default is a neutral palette and the system font stack.
- A logo and a favicon. The default is the site title as text and no favicon.
- A website, a mockup or a styleguide to follow. The default is none.
- A CSS framework. The default is plain CSS without a build step.

Say that one reply such as "use the defaults" is enough. Ask once. Where the
reply leaves a point out, take its default and do not ask again.

Put colours and fonts into CSS custom properties. Where an editor changes a
value per site, make it a setting of the set. A framework with a build step goes
to `typo3-extension-asset-build`, at the moment you write the templates.

### A design in the request: convert it

**Where the user gives you a design, convert it rather than paste it.** Before
the first template, call `typo3_hint_lookup` with `ids` set to
`sitepackage-from-a-design` and `content-model-vs-markup`. Put each part of a
static page where those hints say:

- The doctype and the html, head and body tags go to the page object. A partial
  is a part of the body.

- The stylesheets and scripts go to `includeCSS` and `includeJS`. Every other
  asset goes through `f:uri.resource`.

- The navigation is a menu processor, not a list of page uids.

- Each kind of page in the design is a page layout of its own.

- Each block that repeats is a content element with fields, not markup in the
  page template. Name each one in the report as a content element that is still
  to build.

## 3. Choose where a new package comes from

Skip this step where the package exists already. There are two ways to start.
Use the one the user named. Where the user named none, use the builder, and name
the other way in the report.

1. **The builder at get.typo3.org.** It generates a base package from one
   request.

   - Fetch `https://get.typo3.org/api/doc.json` first. Take the endpoint, the
     fields and their accepted values from that schema, never from an example.
   - Choose the base package that matches the fluid_styled_content decision of
     step 1.
   - Send the line the installation runs as the TYPO3 version.
   - The request sends an author email to a service outside the project. Send a
     placeholder unless the user agreed. Write the real author into the local
     files.
   - The builder takes the vendor from the company and the key from the title.
     So send the vendor and the key the package must have.

2. **A tree written by hand.** `typo3_hint_lookup` with
   `id="sitepackage-layout"` gives the tree and the purpose of each directory.
   Use this way where the builder does not offer the installed line, or does not
   answer.

`typo3_hint_lookup` with `id="sitepackage-start"` names the other tools that
create a sitepackage. It also says what each one writes on the installed major.

**A generated package is only a start.** Read each generated file against the
hints of step 4 and against the sweep, before you keep it:

- Remove the empty directories and the files this site does not use. Say which
  ones you removed.
- Move each part outside the tree of `sitepackage-layout` into that tree. Where
  `sitepackage-start` names a place for a part of the builder, use that place.
  Report the moves.

## 4. Lay out the package

Call `typo3_hint_lookup` for these parts in one call, with their ids in `ids`.
Do this before you write the files. Leave out the ids the brief of
`typo3_task_guide` already lists in its hints.

- `sitepackage-templates`: the template root, the layouts and the partials.
- `sitepackage-backend-layouts`: the page layouts, where they are, and what a
  page with no layout selected renders.
- `sitepackage-typoscript-reference`: which package defines each object you
  reference.
- `page-content-areas`: the areas a page template can address.
- `page-variables-and-processors`: the data a page template gets. That is the
  settings of the set, the site, the page and the menu.
- `translation-domain`: how a template and a page layout name a label of the
  package.

A page layout and its page template use one name in three places. Write all
three in one step.

## 5. Install the package in the site

**Where no shell runs Composer and the console, stop after the files.** Do not
change the dependencies of the site. A set that nothing installed breaks every
page. Give the user steps 5 and 6 as commands, and say that the frontend is
untested.

With a shell, do these steps in this order:

1. Require the package through Composer, from the path it is in.

2. Set it up with the console's `extension:setup`.

3. Add its set to the dependencies of the site configuration. Remove the sets it
   replaces.

4. Find the welcome page of the installer. `typo3_hint_lookup` with
   `id="installation-setup"` says where the installed major puts it. That is the
   site's own `setup.typoscript`, or a root `sys_template` row that clears.
   Replace the file's content with a comment, or hide the row. Delete neither,
   and report what it held.

## 6. Render one page for each page layout

The files alone do not prove the package. A request to each page does.

1. Flush the caches. Then request one page for each page layout.

2. Where no page uses a layout yet, set one page's layout in its page
   properties. Without a backend, write its `backend_layout` field as
   `pagets__<identifier>` through DataHandler, never by SQL:

   - A write by SQL skips the caches, the history and the reference index.
   - `typo3_hint_lookup` with `ids` set to `datahandler-seeding` and
     `datahandler-writing` gives the script and its datamap.
   - Read the old value first with `typo3_record_lookup`. Put it back the same
     way after the request. Report both writes.

3. A layout whose template uses an area other than `main` also proves that the
   page TSconfig of the set loaded. Put a content element into that area the
   same way. Delete it the same way afterwards.

4. Count the tags in each response. Each page holds one `<html>`, one `<head>`
   and one `<body>`. A second set of these tags passes every other check. Remove
   it from the templates, and keep the page object. Never meet the count with
   `config.disableAllHeaderCode`. That setting drops every asset the page object
   includes.

The base of the site can name a host the environment does not serve. Then
`typo3_project_describe` says so, and names the Host header to send instead.

For a look in a browser, call `typo3_rule_lookup` with
`documentId="any/testing/browser-check"` at this step, not earlier. Then open
the page module once, and check that each layout shows its title and columns.

Where you could not render a layout, report it as unproven and name it.

## 7. Read what the finished site serves

**A page that renders is not yet a page a search engine can index or a keyboard
user can work.** Run this pass after step 6, on its responses. Read the home
page and one subpage in each language.

Call `typo3_hint_lookup` once, with `ids` set to `page-head-seo`,
`page-heading-levels`, `xml-sitemap` and `sitepackage-focus-contrast`. Then read
each point off a response, not off a file:

- One h1 per page, and no heading before it.
- The meta description and the canonical in the head. Where the page has a
  translation, also the hreflang set.
- `/sitemap.xml` and `/robots.txt`, each answered by the site.
- The focus colour against every surface a link is on. Compute that ratio from
  the declared colours.

Fix what this skill covers: the page templates, the partials and the CSS. Report
the rest with the page and the language. An empty description is text the
editors write. For a sitemap or a `robots.txt` that is missing, report the fix
the hints name.

## 8. Hand over, and commit

- **When the user asks for a content element, invoke
  `typo3-content-element-development`.** Hand over the extension key, the set
  and the template root.

- **When the package needs an asset build, invoke
  `typo3-extension-asset-build`.** Hand over the same three.

- **Where the site has no installation to render in, invoke
  `typo3-development-installation`.** Do this after you wrote the package and
  added it to the site.

`typo3_commit_message_guide` with `workflow="project"` writes the message for
the commit in the project repository.

## Where this skill stops

- **The site configuration's languages, base and error handling** belong to the
  user. Leave them as they are.

- **The files of another workflow** stay as they are. Stop before you edit them.
