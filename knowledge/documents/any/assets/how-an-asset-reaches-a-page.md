---
description: >-
  Every route a package's own CSS and JavaScript takes into a rendered page, backend and frontend, and how you check each one after a rebuild.
whenToUse: >-
  After a build wrote different files than it did before — renamed, split, hashed or moved — and before you change where a build writes. It names the route each output file takes and what proves the route still carries. A broken route raises nothing in PHP and shows as a page without its styles.
hints:
  - public-assets
  - extension-asset-build
  - extension-declarative-files
---

# How a Package's Asset Reaches a Page

A file below `Resources/Public/` does not load because it is there. Every route
is a declaration somewhere else. A build that renames or moves an output breaks
the declaration, and nothing fails in PHP. This page lists the routes and what
proves each one still carries.

Two things run underneath all of them, and this page does not repeat them. The
package has to publish the file into the document root, which `public-assets`
covers. The file has to be addressable, which the same hint covers for a build
directory outside the package's default paths.

## The Backend Import Map, for JavaScript

`Configuration/JavaScriptModules.php` maps a bare specifier onto a path in the
package. It lists the extensions whose modules the package depends on. It is the
only way built backend JavaScript reaches the backend, and
`extension-declarative-files` describes the file itself.

**Nothing bundles backend JavaScript.** The backend delivers it as ES modules,
one specifier per file. So a pipeline that emits one hashed bundle produces
something the map cannot name.

**You check it statically.** The map is a file and the build wrote files. So you
compare every mapped path against what is on disk. That comparison is the whole
check, and it needs nothing to run.

## The Module Template, for a Backend Module

A backend module does not declare its assets in a configuration file. It calls
`PageRenderer`: `loadJavaScriptModule()` for a specifier the import map
resolves, `addCssFile()` for a stylesheet. The module template does the same for
what the backend itself needs.

**You check it at the call site.** The path or specifier stands in PHP, so you
find it when you read the controller. Whether the call runs is a question about
the request rather than about the build.

## TypoScript, for the Frontend

`page.includeCSS.<key>` and the `includeJS` family name a file per key. The key
is what another setup can override or unset. This is the commonest route a
package's own stylesheet takes.

**You cannot check it statically.** The TypoScript that resolves for one site
decides whether the file reaches the page. So the resolved setup or a rendered
page proves it, never the presence of the file.

## The Asset Collector, from a Template

`<f:asset.css>` and `<f:asset.script>` register a file with the
`AssetCollector`. They render nothing where they stand. The collector
deduplicates by the identifier you give it, which lets a partial rendered many
times contribute one tag.

**Where the call sits decides whether it runs at all.** A call outside the
section Fluid renders registers nothing. It leaves the file published and
unasked for, and it produces no request and so no 404 to find.
`fluid-layouts-sections` carries that trap.

## The Asset Collector's Later Arrivals

**Since:** 13

`<f:asset.module>` registers an ES module by its bare specifier. So a template
reaches the same import map the backend uses.

## Styling One Element From a Template

**Since:** 14

`<f:asset.styleAttr>` collects declarations for a single element rather than a
file. That is a different thing from the routes above: it loads nothing and
publishes nothing.

## From PHP, Anywhere

`PageRenderer::addCssFile()`, `addJsFooterFile()` and `addCssInlineBlock()` add
a file or a block directly. `AssetCollector::addStyleSheet()` and
`addJavaScript()` do the same through the collector. Both are available in the
frontend and the backend.

**You check it at the call site**, as with a backend module.

## What to Do After a Rebuild

The order is the same whichever route a file takes:

1. List what the build wrote, by name, and compare it with what it wrote before.
2. For every renamed, moved, split or dropped file, find the declaration that
   names it. The import map is a file. To find a TypoScript key, a ViewHelper
   argument or a PHP call, search the package for the old name.
3. Where a file moved out of the package's default public paths, the finding is
   the publish step, not the build. `public-assets` says what makes it
   addressable again.
4. What no declaration names is either dead output or a route nobody has found
   yet. Read to say which of the two it is; do not guess.
