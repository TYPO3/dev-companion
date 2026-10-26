---
date: 2026-10-26T20:44:42+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup, typo3-sitepackage-development
directory: /home/benji/site10-run10
---

# Nothing says how the builder derives vendor, namespace and extension key

## Observation

Task: new sitepackage from the get.typo3.org builder.
The skill takes the vendor from the project's own packages. The project had none, so I asked the user and proposed benjaminkott/site-package.
The builder schema (api/doc.json, SitePackageType) has no vendor or extension key field. It requires title and author with name, email, company and homepage.
The builder returned benjamin-kott/site-package, namespace BenjaminKott\SitePackage and key site_package. The vendor came from the company and the key from the title. I learned this only from the zip.
I then changed the Composer name and set name by hand to match what I had promised. Before the request, I could not say what the builder would write.
The skill also does not say what to do when the project has no own packages to take a vendor from.

## Query

GET https://get.typo3.org/api/doc.json; POST /api/v1/sitepackage/ title="Site Package" author.company="Benjamin Kott"

## Suggestion

In sitepackage-start, say that the builder derives the Composer vendor and namespace from author.company and the key from title. Say how to pick the fields to get a wanted vendor. In the skill, name the fallback when no own package exists: ask, or derive from the author.
