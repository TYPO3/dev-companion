---
id: D-DOC-021
title: The site is searched in a dialog opened with Ctrl-K
date: 2026-08-09
status: revoked
revokedBy: D-DOC-024
---

# D-DOC-021 — The site is searched in a dialog opened with Ctrl-K

**The search is a modal dialog that the button in the bar or Ctrl-K opens. The
arrows and the return key move through the hits and open one.**

What it replaces wrote the hits into the sidebar, which is the list of pages a
reader had already looked past. On a narrow screen that sidebar folds away, so
the hits landed where nobody could see them.

## Evidence

- Under 860 px the stylesheet hides the sidebar until the menu button opens it.
  A reader who searched from a phone filled a list that was `display: none`.
- The hits and the page list shared one 16.5 rem column, and each hit carries a
  snippet. Twelve of them stood where 48 page names had been.
- Nothing opened the search but the pointer. The field carried no shortcut, and
  Ctrl-K is what the documentation sites this audience arrives from bind.
- `showModal()` carries the focus, the backdrop and the escape key. Written
  against a `div` all three are the theme's, which is most of what a modal
  costs.
- The return key that opens the best hit ran in Chromium. On a page two
  directories deep, Ctrl-K, `feedback`, return lands on `feedback/judging.html`,
  and one arrow down lands on the second hit instead.
- The built assets grew from 7.6 KB of CSS and 28.7 KB of JS to 11.6 KB and 30.4
  KB. The browser fetches them once for the whole site, `D-DOC-019`.

## Decided

- The dialog stands in the layout, closed, and the script opens it. A `<dialog>`
  with no script is invisible rather than dead, which is what the hidden menu
  button already does for the sidebar.
- The button in the bar says the shortcut, and the script rewrites it to `⌘ K`
  on a Mac. A shortcut nothing names is one only a reader who learnt it
  elsewhere has.
- The index still arrives when the dialog first opens rather than with the page.
  That is 245 KB nobody who does not search pays for.
- The script picks the best hit when it renders the results. So three letters
  and the return key reach a page whose name the reader already knows.
- The searched word carries a mark in the title and in the snippet. A snippet is
  130 characters out of the middle of a page. The search for one's own word in
  it again is the reader's work otherwise.
- The footer names the three keys. The arrows and the return key are the half of
  this that nothing else would show.

## Assumed

- That Ctrl-K is free. It is the browser's on no engine this ran in, and the
  handler takes it only where no other modifier is down.
- That `<dialog>` and `showModal()` are there. Both are baseline, and the button
  is the only way in, so a browser without them has no search at all.
- That nothing here runs a browser. The suite holds the index and the ranking;
  what the dialog does ran once in Chromium and nothing holds it after that.

## Wrong if

- A reader cannot reach the search without a keyboard, which the button in the
  bar is what prevents.
- The browser or a screen reader has Ctrl-K for something the reader needed
  more, and the page takes it away.
- The dialog opens over a page that still scrolls under it, or the return key
  opens a hit that is not the marked one.
- The assets grow past what one fetch for the whole site is worth.

## Revoked on 2026-08-12

The search is the theme's, and the theme's has no Ctrl-K —
[`D-DOC-024`](doc-024-the-sites-theme-is-a-package-this-repository-keeps-none-of.md).
`sds-search` sits in the bar and opens on a press. So the half of the statement
about the shortcut is not true of this site any more.

What the entry found is why the element is in the bar rather than in the rail,
and that account stands. A hit in the sidebar sits where a reader on a narrow
screen cannot see it. A shortcut that opens the field belongs in the element
rather than in a script kept here. Every site on this theme gets it at once.
