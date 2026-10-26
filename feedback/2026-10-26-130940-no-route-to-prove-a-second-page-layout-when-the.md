---
date: 2026-10-26T13:09:40+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5[1m]
tool: typo3-sitepackage-development, typo3_hint_lookup
directory: /home/benji/site10-run2
---

# no route to prove a second page layout when the fresh install has one page

## Observation

Task: create a sitepackage on TYPO3 14.3.5 with two page layouts, then render one page per layout. The skill requires that proof. The fresh installation had only the root page with no layout. No hint or guide said how to get a page that uses the second layout. I wrote pages.backend_layout = "pagets__TwoColumns" directly into the SQLite database with PHP PDO. Then I flushed caches, requested the page, and reset the value. I found the database path in config/system/settings.php myself. First I tried "ddev mysql", which failed because the installation uses SQLite. typo3_project_describe does not report the database driver. I also had to find how to request the frontend inside DDEV. curl to localhost gave 404 until I set the Host header to the site base host. The browser-check guide could have carried this, but documentId lookup failed (separate feedback).

## Query

Task text: "Create a sitepackage for our site: the extension, its site set, a page template with header and footer, and two page layouts the editors can choose from." Skill step 5: "render one page for each page layout".

## Suggestion

Add a hint or guide "rendering every page layout on a fresh installation". It should say how to give a page a layout without the backend (DataHandler through a CLI script, or a temporary pages.backend_layout value on the root page), how to restore it, and how to request a page inside DDEV with the site Host header. typo3_project_describe should report the database driver, so an agent does not try ddev mysql on SQLite.
