---
id: D-DOC-022
title: The reader picks the colours and the page remembers it
date: 2026-08-09
status: open
---

# D-DOC-022 — The reader picks the colours and the page remembers it

**One button in the bar puts the site in light, in dark, or back on what the
machine says. The choice survives to the next page.**

The site had the machine's setting and nothing else. So a reader on a dark
desktop who wanted the page light had to change the desktop.

## Evidence

- The palette was two blocks of eleven variables, the second inside
  `@media (prefers-color-scheme: dark)`. A colour stood twice and could go wrong
  in one of the two.
- `light-dark()` reads the used `color-scheme`, which the stylesheet already
  declares. That is for the scrollbars, the search field and the caret, which a
  browser draws light on a dark page without it. So the pair is one declaration
  and the switch is one attribute.
- The stored choice has to arrive before the page paints. The theme's script
  runs deferred, and a reader who turned the light off would see it for a frame.
- Run in Chromium with the browser set to dark. The button walks system → light
  → dark → system, the body's background follows, and light survives a reload.

## Decided

- The three states are one button rather than three, and the button draws which
  one is on. A sun, a moon, and a half-filled circle for the machine's own
  setting. Its `aria-label` says the state and the next one, since an icon says
  neither.
- The machine's setting is the state a reader who never presses it is in, and
  two more presses put them back. It stands as the absence of the key, so a
  reader who chose nothing follows their machine when they change it.
- The colours are one set of `light-dark()` pairs. What the button writes is
  `data-theme` on `<html>`, and the stylesheet answers with
  `color-scheme: only light` or `only dark`.
- Two lines run in the head, before the stylesheet's first paint, and nothing
  else on this site does. What that buys is the frame; what it costs is the
  inline script `D-DOC-019` had removed from every page.
- The button stands hidden and the script shows it, like the menu button:
  nothing without a script can change what it names.

## Assumed

- That `light-dark()` is there. It is baseline, and a browser without it gets no
  colour from any of the eleven variables rather than the wrong one.
- That `localStorage` is available. A browser that refuses it throws at the
  write of the choice. That is a press that does not stick rather than a page
  that does not load.

## Wrong if

- A reader arrives on a page in the theme they turned off. That means the head
  script did not run or the attribute sits somewhere it does not reach.
- A colour reads wrongly in one of the two schemes, because a pair got one half
  from the other palette.
- The choice does not follow to the next page, or a reader who chose nothing
  stops following their machine.

## Since then

The three-state button is gone. The site follows the TYPO3 Support App design
system, whose mode switch is two segments with the active one filled by the
accent. A specimen is a copy rather than a variation,
[`D-DOC-023`](doc-023-the-site-is-built-to-the-typo3-support-app-design-system.md).
What that costs is the way back. A reader who has pressed a segment follows
their own choice from then on. The way to clear it is to clear the site's
storage. Everything else this entry settled stands, the two lines in the head
among it. The switch writes the same attribute, and the system reads it with
`color-scheme` the same way.

## Since then

The switch is the theme's `sds-theme`, two segments rather than one button:
[`D-DOC-024`](doc-024-the-sites-theme-is-a-package-this-repository-keeps-none-of.md).
`D-DOC-023` had already replaced the three-state button with the design system's
own. What this entry decided is what still happens. The reader picks, and the
choice lands before the first paint on the next page, so nobody sees the other
mode for a frame.

One thing it promised no longer holds, and it is the drawings. They are plain
`<img>` now, which is a document of its own that no page can reach into. So a
reader in dark reads a light drawing. The files drawn for it are still in the
checkout and still published.
