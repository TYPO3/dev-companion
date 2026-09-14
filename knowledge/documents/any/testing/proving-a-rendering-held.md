---
description: >-
  How you show that a change to how a site renders changed only what it meant to: which pages you capture from the content that exists, why you capture twice before you edit anything, what has to run between the two captures, and how you read the diff.
whenToUse: >-
  When a change rewrites how pages render rather than what one page contains — a replaced rendering frame, a dropped dependency the site renders through, a moved template root, a rewritten layout — and you have to show that nothing else moved. To prove one unknown value a checkout does not produce, use the core rendering probe instead. To prove that one TypoScript condition matched, use the condition page.
hints:
  - sitepackage-fluid-styled-content
  - page-content-element-rendering
---

# Proving a Rendering Held Across a Change

A change to the frame every page renders in has no diff that says what it
renders. The files it touched are the ones you meant to touch. The suite is
green. Every element on the site went through the code you replaced. What
settles it is the rendered HTML on both sides of the change, captured from the
installation over HTTP.

The question is whether the site still renders what it rendered before. You ask
it of every element on the site rather than of one page. The claim is negative —
*nothing else moved* — so the method is subtraction. Capture, change, capture
again, and account for every line that differs. A frontend that renders is not a
frontend that renders the same.

## What to Capture, and Why Not the Templates

The content that exists decides which pages you capture, not what the templates
allow. A template root of thirty files says thirty elements could render, and
the rows say which of them do. A capture chosen from the first list spends most
of its pages on markup this site never emits. It misses the element that appears
once.

`typo3_record_lookup` with `table="tt_content"` and `groupBy="CType"` is that
list in one call. It names every element type in use and how many rows carry it.
One page per value is the floor.

Group by the columns the change reads as well. A frame that branches on
`frame_class`, `header_layout` or `layout` renders a different document per
value. The answer names the uid and pid of the rows that depart from the
column's TCA default. **Capture those pages first.** A value one row in a
hundred carries is invisible in a distribution. It is the row a branch written
for the majority drops.

Which URL a page id becomes is the same question the condition page answers,
under the same rule. Request without a backend session, because a preview with a
login disables the page cache and renders hidden records.

## Capture Twice Before You Edit Anything

Two captures of a page nobody has touched are not always identical. Every line
that differs between them is noise the real diff shows as well.

So capture the baseline twice first, into two directories, and diff it against
itself. What comes out is the site's own per-request variation. Subtract it from
every later diff. A list of what varies would go stale; the site answers it in
one extra capture.

Two sources account for most of it. A site with
`security.frontend.enforceContentSecurityPolicy` on emits a nonce per request.
So every inline script and style carries a value that differs every time.
Anything a page renders from the clock is the other source.

## What Has to Run Between the Two Captures

The second capture is the new rendering only where no cache built before the
change served it.

`cache:flush` takes `--group`, and **the group matters more than it looks**.
`pages` holds the page cache, the hash cache, the rootline and the compiled
TypoScript. `system` holds `fluid_template`, which is the compiled Fluid. So a
change to a template, a layout or a partial survives `cache:flush --group pages`
intact. The second capture is then the old template compiled, which reads as
*the change had no effect*. Flush without a group, which is `all`.

Where the change added or removed a package, run `extension:setup` after the
Composer step and before the capture. It applies the schema changes and imports
the static data the new packages bring. A flush does neither.

An asset URL carries a cache-busting segment from the file's own modification
time. So it moves when the file moves, and a flush does not touch it. A changed
stylesheet shows up in the diff as one changed URL, which is a difference the
change explains.

## Reading the Diff

Rendered HTML arrives with many tags to a line. So a line-based diff reports a
whole region as one changed line. It says nothing about which part of it moved.
Insert a newline between adjacent tags in both captures before you diff. Then a
difference is a line rather than a paragraph.

Then read the diffs together rather than one at a time. The evidence is the
union of every diff, with the site's own variation subtracted. Attribute each
remaining line to one of the changes you intended. **A line nothing explains is
the finding**, and it is the only output of this procedure that matters. Expect
every diff to be non-empty where the change was meant to alter markup.

## What It Does Not Prove

The capture is one request per page with no session. So it leaves out what an
editor with a session sees. It leaves out what a second language renders and
what a form does after a submit. It leaves out behaviour in the browser too. A
document that diffs clean can still have lost the CSS rule that positioned it.
That is the browser check instead.
