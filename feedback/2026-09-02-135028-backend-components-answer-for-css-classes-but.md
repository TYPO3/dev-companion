---
date: 2026-09-02T13:50:28+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_component_lookup
directory: /home/benji/projects/site-tierheim
---

# Backend components answer for CSS classes but not for the data attributes that drive them

## Observation

Task: same session — building a v14.3 backend module list with a filter row, per-row popover menus, a confirmation before delete, and a thumbnail column.

typo3_component_lookup answered well for markup and classes. Three times it was exactly right: "table" gave me table-fit and the modifiers; "badge" gave badge-warning/badge-danger/badge-pill; "dropdown" gave the sub-component list including dropdown-item-columns and dropdown-item-column-value — that last one fixed a complaint from the person I worked for ("hier sieht man nicht wirklich das es eine zahl ist ich glaube wir haben dafür css tools"). It is the right answer and it was there.

What it does not carry is the JavaScript contract of the same components, and twice that cost me:

1. I copied `data-on-change="submit"` onto two select fields from EXT:redirects' Overview.fluid.html. It does nothing outside that extension: grepping showed only cms-redirects/Resources/Public/JavaScript/redirects-module.js implements it. I shipped a dead attribute and removed it later.

2. For the delete confirmation I used core's t3js-modal-trigger pattern and wrote `data-bs-content` for the body text. The modal showed its default "Are you sure?" instead. The attribute is `data-content` — I found it by grepping `dataset\.[a-zA-Z]*` out of the minified modal.js: buttonCloseText, buttonOkText, content, eventName, hideHeader, severity, size, staticBackdrop, targetForm, title, uri, url.

Both are exactly the kind of thing a curated component catalog could carry, and both failed silently in the browser rather than raising.

## Query

typo3_component_lookup(query="table"|"badge"|"dropdown", targetVersion="14.3"). I never called it for "modal" — I assumed the catalog was the Sass component subset and would not carry JS behaviour, and did not test that assumption.

## Suggestion

Where a component has a JavaScript module behind it, name the module and the data attributes it reads. For the dropdown/modal entry that is @typo3/backend/modal.js and the t3js-modal-trigger dataset keys (title, content, severity, buttonOkText, buttonCloseText, targetForm) — with the note that the button may belong to another form via `form="…"`, which is how core's site configuration deletes.

And a warning worth carrying somewhere: `data-on-change="submit"` is EXT:redirects' own, not a backend behaviour. It reads like a global convention in a core template, which is exactly how I picked it up.
