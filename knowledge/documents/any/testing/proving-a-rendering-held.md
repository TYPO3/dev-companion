---
description: >-
  How a change to how a site renders is shown to have changed only what it meant to: choosing what to capture from the content that exists, capturing twice before anything is edited so the site's own noise is subtracted, what has to run between the two captures, and how the diff is read.
whenToUse: >-
  When a change rewrites how pages are rendered rather than what one page contains — replacing a rendering frame, dropping a dependency the site renders through, moving a template root, rewriting a layout — and what has to be shown is that nothing else moved. Proving one unknown value a checkout does not produce is the core rendering probe instead, and proving that one TypoScript condition matched is the condition page.
hints:
  - sitepackage-fluid-styled-content
  - page-content-element-rendering
---

# Proving a Rendering Held Across a Change

A change to the frame every page renders in has no diff that says what it
renders. The files it touched are the ones you meant to touch, the suite is
green, and every element on the site went through the code you replaced. What
settles it is the rendered HTML on both sides of the change, captured from the
installation over HTTP.

The question is whether the site still renders what it rendered before, asked of
every element on it rather than of one page. The claim is negative — *nothing
else moved* — so the method is subtraction: capture, change, capture again, and
account for every line that differs. A frontend that renders is not a frontend
that renders the same.

## What to Capture, and Why Not the Templates

The set of pages to capture is decided by the content that exists rather than by
what the templates allow. A template root of thirty files says thirty elements
could render; the rows say which of them do, and a capture chosen from the first
list spends most of its pages on markup this site never emits while missing the
element that appears once.

`typo3_record_lookup` with `table="tt_content"` and `groupBy="CType"` is that
list in one call: every element type in use, with how many rows carry it. One
page per value is the floor. Group by the columns the change reads as well —
a frame that branches on `frame_class`, `header_layout` or `layout` renders a
different document per value, and the answer names the uid and pid of the rows
departing from the column's TCA default. **Those are the pages to capture
first.** A value one row in a hundred carries is invisible in a distribution and
is the row a branch written for the majority drops.

Which URL a page id becomes is the same question the condition page answers, and
under the same rule: request without a backend session, because a signed-in
preview disables the page cache and renders hidden records.

## Capture Twice Before Anything Is Edited

Two captures of a page nobody has touched are not always identical, and every
line that differs between them is noise the real diff will show as well.

So the first thing captured is the baseline twice, into two directories, and
diffed against itself. What comes out is the site's own per-request variation,
and it is what is subtracted from every later diff. A list of what varies would
go stale; the site answers it in one extra capture.

Two sources account for most of it. A site with
`security.frontend.enforceContentSecurityPolicy` on emits a nonce per request,
so every inline script and style carries a value that is different every time.
Anything a page renders from the clock is the other one.

## What Has to Run Between the Two Captures

The second capture is the new rendering only where nothing served it from a
cache built before the change.

`cache:flush` takes `--group`, and **the group matters more than it looks**.
`pages` holds the page cache, the hash cache, the rootline and the compiled
TypoScript; `system` holds `fluid_template`, which is the compiled Fluid. So a
change to a template, a layout or a partial survives `cache:flush --group pages`
intact, and the second capture is the old template compiled — the failure that
reads as *the change had no effect*. Flush without a group, which is `all`.

Where the change added or removed a package, `extension:setup` runs after the
Composer step and before the capture: it applies the schema changes and imports
the static data the new set of packages brings, which a flush does not.

An asset URL carries a cache-busting segment derived from the file's own
modification time, so it moves when the file moves and a flush does not touch
it. A changed stylesheet therefore shows up in the diff as one changed URL,
which is a difference the change explains.

## Reading the Diff

Rendered HTML arrives with many tags to a line, so a line-based diff reports a
whole region as one changed line and says nothing about which part of it moved.
Insert a newline between adjacent tags in both captures before diffing, so a
difference is a line rather than a paragraph.

Then the diffs are read together rather than one at a time. The evidence is the
union of every diff, with the site's own variation subtracted and each remaining
line attributed to one of the changes that were intended. **A line nothing
explains is the finding**, and it is the only output of this procedure that
matters — every diff being non-empty is expected where the change was meant to
alter markup.

## What It Does Not Prove

The capture is one request per page with no session, so what a signed-in editor
sees, what a second language renders and what a form does after it is submitted
are outside it. Behaviour in the browser is outside it too — a document that
diffs clean can still have lost the CSS rule that positioned it, which is the
browser check instead.
