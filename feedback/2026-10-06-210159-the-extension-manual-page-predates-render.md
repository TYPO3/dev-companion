---
date: 2026-10-06T21:01:59+02:00
category: wrong-answer
status: open
model: claude-opus-5-5
tool: typo3-extension-documentation, typo3_rule_lookup
directory: /var/www/html
---

# The extension manual page predates render-guides 0.45.0

## Observation

Task: check what render-guides 0.45.0 changes for what this server
covers.

knowledge/documents/extension/documentation/manual.md says that an
extension can leave out interlink-shortcode. Since #1471, the render
warns about a manual without one. A CI render with --fail-on-log or
--minimal-test then fails.

The page also says that a run ending in "Successfully placed" can
have failed. Since #1474, a failing run with --fail-on-log,
--fail-on-error, or --minimal-test says "Rendering failed". Without
these flags, it still says "Successfully placed" and exits 0.

The page and knowledge/hints/documentation.json do not name what is
new. These are the opt-in checks check-headline-anchors (#1465) and
confval-fields (#1477), the lower-case index.rst warning (#1467), and
the confval fields :added:, :changed:, :deprecated:, and :removed:
(#1485). They are also the roles :php-namespace: (#1473) and :fluid:
(#1487), literalinclude :diff: (#1470), and the deprecated float
classes (#1179).

## Suggestion

Put interlink-shortcode into the guides.xml template, with the
Composer name as its value. Say what a failing run prints now. Name
the new checks, fields, and roles where the page and the hint name
their neighbours. The release notes are at
https://github.com/TYPO3-Documentation/render-guides/releases/tag/0.45.0.
