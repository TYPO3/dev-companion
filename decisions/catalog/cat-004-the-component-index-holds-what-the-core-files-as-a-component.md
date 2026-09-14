---
id: D-CAT-004
title: 'The component index holds what the core files as a component'
date: 2026-08-11
status: open
---

# D-CAT-004 — The component index holds what the core files as a component

**The index's candidate set is what the core itself files as a component: the
partials under `Build/Sources/Sass/component/` and the custom elements under
`element/`.**

A miss therefore means uncurated. The backend module chrome is a miss of that
kind rather than a subject the index declines.

A session that reworked Gerrit 95163 read the boundary the other way round. It
took `typo3_component_lookup` for a catalogue of discrete widgets: badge, card,
panel, input-group, the examples its own schema names. It concluded that the
chrome around every backend module was not in it. It never called the tool while
it authored `.module-docheader-wrapper` and four `--module-docheader-*` custom
properties. It established that from the styleguide templates in the checkout
instead, and said so in
`feedback/2026-08-10-182511-component-lookup-was-passed-over-while.md`.

## Evidence

- Re-run on 2026-08-11 against the bundled snapshot (TYPO3 15.0 @
  `4c8b38b2dd07`, verified 2026-07-28). `docheader`, `module` and
  `module docheader`, each with `targetVersion` 15.0, all return the miss text.
  The session's conclusion was right and its route to it was not.
- `.checkouts/main` at `c71b2bdb2f`: `Build/Sources/Sass/component/_module.scss`
  defines `.module`, `.module-docheader`, `.module-docheader-navigation`,
  `.module-docheader-buttons`, `.module-body` and some twenty `--module-*`
  custom properties. Every one of the 25 catalogued entries carries a `sassPath`
  into that same directory, `_card.scss` and `_panel.scss` among them.
- `--module-docheader-scroll-offset` has stood in that file since `859318a83d`
  (2025-03-18). The feedback lists it among the properties its patch authored,
  which is the round trip a call would have taken off it.
- Nothing a caller reads *before* the call states the curation rule. The
  description names badge, card, search box and input-group. The rule itself is
  in the miss text and in `typo3_snapshot_scope`, both of them one call further
  in than the decision to skip.
- The `css-components` hint already carries the core's directory vocabulary:
  `component/` one partial per component, `component/scaffold/` the topbar and
  module menu, `module/` the area styles. That reached the session. The class
  contract of the partial it edited did not.

## Decided

- The index is a curated subset of the core's own filing, and the filing is what
  says whether something is a candidate. Module chrome is one, so the index
  curates it rather than declines it.
- What that boundary is stands where a caller reads it before the call: the tool
  description, the `routing` entry in `knowledge/server-scope.json`, and
  `scope.components` in `knowledge/catalog/meta.json`.
- Rejected: a narrower routing line, discrete widgets alone. That draws a line
  the core does not draw. An instruction that sounds authoritative then sends
  every backend CSS task outside it to grep.
- Rejected: a tool of its own for backend CSS classes. The caller here is a core
  contributor with the checkout open. So the answer costs one read rather than
  the four round trips and a trap that earn a tool (`D-FBK-027`).

## Assumed

- Chrome markup is worth an entry. `EXT:backend` holds it in
  `Resources/Private/Layouts/Module.fluid.html` and
  `Resources/Private/Partials/DocHeader.fluid.html`, and `EXT:info` ships a
  `Module.fluid.html` of its own. So a second package that writes that markup is
  practice rather than a hypothetical.
- What the core files under `component/` is a set somebody can curate against.

## Wrong if

- The chrome entry hands out markup nobody should author by hand, because
  `ModuleTemplate` renders it for every backend module. That surfaces as an
  extension whose template carries copied docheader markup, or as a feedback
  that reports the entry as advice it had to ignore.
- Curation from the core's filing turns out to mean nothing, because that same
  directory also holds `_utility.scss`, `_type.scss` and `_root.scss`. Those are
  partials no caller would look for as a component. Then the candidate set is
  not the directory, and the rule has to name what inside it is one.

## Since then

A session decided against the tool before it called it, and the re-run says the
skip cost it two hits of six (`feedback/2026-08-13-214927`). Its suggestion,
that the description send the *does it exist* question to grep, is the narrower
line this entry rejected. It arrives by question shape rather than by subject. A
caller cannot sort its own selectors into the two questions before it asks, so
the line would send the curated hit to grep too. Both halves already reach a
caller in order, the description before the call and the miss text after it. A
route for a review of backend markup here came up and lost. It would have
reported one asserted class as a documented variant.
