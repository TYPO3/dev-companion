---
description: >-
  The files an extension's Documentation/ directory consists of, whole, and the command that renders it before you publish it.
whenToUse: >-
  When an extension has no manual yet, or has one older than guides.xml. The hint below says what a manual is for and where it lives; this page says what goes in the directory.
hints:
  - extension-documentation
---

# Setting Up an Extension Manual

Two files make a directory a manual: `Documentation/Index.rst` as the entry
point and `Documentation/guides.xml` as the renderer configuration. Everything
else is convention. docs.typo3.org renders the tree from those two.

## Documentation/guides.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<guides xmlns="https://www.phpdoc.org/guides"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="https://www.phpdoc.org/guides ../vendor/phpdocumentor/guides-cli/resources/schema/guides.xsd">
    <extension class="\T3Docs\Typo3DocsTheme\DependencyInjection\Typo3DocsThemeExtension"/>
    <project title="My Extension"
             version="local"
             copyright="since {year} by the extension's authors &amp; contributors"
    />
</guides>
```

The renderer needs the namespace, the `class` on `extension` and the `title` on
`project`. The rest is optional. The deployment that publishes the manual sets
`version`, so a repository carries `local`. The `extension` element takes the
attributes that produce the edit link and the project header: `edit-on-github`,
`edit-on-github-directory`, `project-home`, `project-issues`,
`interlink-shortcode`. An extension that wants none of them leaves them out.

Two files that look like this one are not the template. A core system
extension's `guides.xml` points `edit-on-github` at `typo3/typo3` and carries
`typo3-core-preferred`. That is the core repository's arrangement, not an
extension's. An extension whose manual predates the current renderer still ships
`Settings.cfg`. That file configures the renderer the project replaced, and a
copy of it produces a manual nothing renders.

## Documentation/Index.rst

```rst
.. include:: /Includes.rst.txt

..  _start:

============
My Extension
============

:Extension key:
   my_extension

:Package name:
   vendor/my-extension

:Version:
   |release|

:Language:
   en

:License:
   This document is published under the
   `Open Publication License <https://www.opencontent.org/openpub/>`__.

:Rendered:
   |today|

----

One sentence on what the extension does.

----

**Table of Contents:**

.. toctree::
   :maxdepth: 2
   :titlesonly:

   Introduction/Index
   Installation/Index
   Configuration/Index
```

The `toctree` pulls the rest of the tree in. Each chapter is one CamelCase
directory with its own `Index.rst`, named without the file extension. The
renderer skips a chapter directory that no `toctree` names.

## The two conventional files

Every page includes `Includes.rst.txt`, and it holds what has to appear on all
of them. It is optional for an extension. The official manuals all carry one,
which is why the include line stands at the top of `Index.rst` above.

`Sitemap.rst` is a nearly empty file the renderer fills while it runs. A hidden
`toctree` at the foot of `Index.rst` lists it.

## Rendering it before you publish it

```bash
docker run --rm --pull always -v $(pwd):/project -it \
  ghcr.io/typo3-documentation/render-guides:latest --config=Documentation
```

`--config` names the directory that holds `guides.xml`. The rendered HTML lands
below `Documentation-GENERATED-temp/`, which belongs in `.gitignore`.

A run whose last line reads "Successfully placed" has not said that everything
rendered. When `Index.rst` includes a file that is not there, the run logs the
failed directive and exits 0. `--fail-on-log` makes the same run exit 1, and a
check calls it with that flag. `--fail-on-error` is the narrower flag and lets a
warning pass. In CI the command also takes `--no-progress`. `--minimal-test`
renders a single page where the check only has to establish that the tree
builds.

Expect that include to fail first. `Index.rst` opens with
`.. include:: /Includes.rst.txt` in every manual worth a copy, and the extension
has to carry the file it names itself.
