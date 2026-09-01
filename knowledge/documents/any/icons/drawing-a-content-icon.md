---
description: >-
  What a content element's own icon is drawn to: the box every one of them shares, what the core's set varies to tell one from another, and the check that says a set works.
whenToUse: >-
  When an extension registers content elements or record types of its own and needs icons for them, and when a borrowed core identifier has been refused. Registering one and asking whether an identifier resolves is typo3_icon_lookup's.
hints:
  - icon-usage
---

# Drawing a Content Icon, and a Set of Them

An icon is what an editor picks an element by. It is rendered at 16 pixels in
the element wizard and in the record list, and at that size a picture is
recognised or it is not — a label read beside it is the fallback rather than the
mechanism.

## What the Box Fixes

Every one of the 115 icons in the core's own `content` set is
`viewBox="0 0 16 16"`, read in
`typo3/sysext/core/Resources/Public/Icons/T3Icons/svgs/content/` on 2026-09-01.
That is the whole of what the set holds in common, and it is what a drawing has
to be composed inside rather than scaled into: a shape drawn at a larger size
and reduced loses the pixel grid the flat fills sit on.

## What It Leaves Free, and What the Core Actually Varies

The framed page is one motif among several rather than the pattern. Of those
115, 17 carry the frame path the text elements share and 6 carry the header bar
inside it. The rest are drawn as their own thing: an accordion is stacked
panels, a carousel is two frames overlapping, a form is a page with a blue
button at its foot, an idea is a yellow bulb with no page under it at all.

The palette is wider than those elements suggest, and it is what separates one
icon from its neighbour as much as the outline does. Beside the greys — `#FFF`,
`#CCC`, `#B9B9B9`, `#999`, `#666`, `#AAA` — the set uses `#59F` for an
interactive part, `#FFC857` with `#E8A33D` beneath it for a highlight, `#FF8700`
for an accent, and `#C83C3C` where something is a warning.

So an element that is genuinely a text block is drawn as one of the framed
pages, because that is what it is; an element that is a list of records, a
gallery or a form is not, and drawing it as a framed page with different
internal lines is what makes a set nobody can read.

## The Set Is the Unit

A set drawn one icon at a time, each correct against the rules above, still
fails as a set. Seven framed rectangles differing by the arrangement of two or
three internal lines are one picture at the size they are rendered.

The check is cheap and it is the only one that answers: render the set at 16
pixels side by side and name each one without reading its label. Whatever cannot
be named needs its own silhouette or its own colour, not another line inside the
same frame.

Look at the core's set before drawing, not only at the rules: the rules are
satisfied by a uniform set, and uniformity is the failure mode of a careful
reader.

## What Belongs Where

The SVG goes below `Resources/Public/Icons/` in the extension, and the
identifier is registered in `Configuration/Icons.php`. Whether an identifier
resolves, and to which file, is `typo3_icon_lookup` with the identifier — which
is a different question from whether the picture is right, and the only one
anything here can answer.
