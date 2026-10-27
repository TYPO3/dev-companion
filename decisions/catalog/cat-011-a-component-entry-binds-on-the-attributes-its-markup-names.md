---
id: D-CAT-011
title: A component entry binds on the attributes its markup names
date: 2026-10-27
status: open
coveredBy:
  - CatalogTest::aMarkupThatRestsOnABehaviourIsWithheldBeforeItArrives
---

# D-CAT-011 — A component entry binds on the attributes its markup names

**`bin/cli components:check` derives an entry's `since` from the attribute names
in its markup too, read against the core's TypeScript.**

## Evidence

- The dropdown entry recorded `since: 13`, and the lookup handed the Popover API
  markup to a 13.4 caller. On 13.4 the styleguide demo `Dropdown.html` writes
  `data-bs-toggle="dropdown"`, and no TypeScript there reads `popovertarget`.
- The class names and custom properties are the same on 13.4 and 14.3. So the
  derivation `D-CAT-001` describes found nothing to bind on. That is the first
  half of its **Wrong if**: a markup that changed while every name stayed.
- `markupDigests` recorded a different digest for v13 than for v14, which is the
  right signal. The check compares a digest only with its own earlier read, so a
  difference between two majors fails nothing.
- On 2026-10-27, against 12.4, 13.4, 14.3 and main, the attribute read moved one
  binding: the dropdown, from v13 to v14. `popovertarget` is in the 14.3
  TypeScript in `backend/dropdown.ts` and in two elements, and in no 13.4 file.
- In the core, `ddc694eda9` "Replace Bootstrap dropdown with Popover API"
  arrives in v14.2.0. A 14.0 or 14.1 installation still has Bootstrap's
  dropdown. The maintainer settled on 2026-10-27 that this server covers the LTS
  lines and `main` alone. So 14.3 is the v14 line, and `since: 14` is exact.

## Decided

- An attribute name in an entry's markup binds the entry where the newest
  covered TypeScript reads it and an older one does not. The class names stay on
  the Sass, because the TypeScript corpus once bound Buttons on an incidental
  use of `btn-secondary` (`D-CAT-001`).
- `classesSince` keeps reading the class list alone. A caller who asks about
  `.dropdown-menu` on 13.4 still gets the class.
- What the switch changes in behaviour is a hint, `backend-dropdown` in
  `knowledge/hints/backend-ui.json`. The catalog entry is markup and a contract,
  and it has no field for events or for the state a test reads.

## Assumed

- An attribute the core starts to read in its TypeScript is a behaviour the
  markup depends on. An attribute that only the HTML standard gives meaning,
  such as `popover` itself, appears in neither corpus as a new name.

## Wrong if

- An entry's markup comes to rest on an attribute that a template or a
  ViewHelper reads rather than the TypeScript. The read misses it as before.
- An attribute name appears in an unrelated v14 TypeScript file and binds an
  entry whose markup holds on v13. That is the `btn-secondary` case over again,
  and the entry needs a narrower corpus.
