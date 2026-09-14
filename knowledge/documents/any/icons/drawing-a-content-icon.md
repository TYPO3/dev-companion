---
description: >-
  What you draw a content element's own icon to: the box every one of them shares, what the core's set varies to tell one from another, and the check that says a set works.
whenToUse: >-
  When an extension registers content elements or record types of its own and needs icons for them, and when a borrowed core identifier was refused. To register one, or to ask whether an identifier resolves, call typo3_icon_lookup.
hints:
  - icon-usage
---

# Drawing a Content Icon, and a Set of Them

An icon is what an editor picks an element by. The element wizard and the record
list render it at 16 pixels. At that size a reader recognises a picture or does
not. A label beside it is the fallback rather than the mechanism.

## What the Box Fixes

Every one of the 115 icons in the core's own `content` set is
`viewBox="0 0 16 16"`. That is what a read of
`typo3/sysext/core/Resources/Public/Icons/T3Icons/svgs/content/` found on
2026-09-01. That is the whole of what the set holds in common. You compose the
icon inside that box; you do not scale one into it. A shape drawn at a larger
size and reduced loses the pixel grid the flat fills sit on.

## What It Leaves Free, and What the Core Varies

The framed page is one motif among several, not the pattern. Of those 115 icons,
17 carry the frame path the text elements share. Of those 17, 6 carry the header
bar inside it. The core draws the rest as their own thing. An accordion is a
stack of panels, and a carousel is two frames that overlap. A form is a page
with a blue button at its foot. An idea is a yellow bulb with no page under it
at all.

The palette is wider than those elements suggest. It separates one icon from its
neighbour as much as the outline does. Beside the greys — `#FFF`, `#CCC`,
`#B9B9B9`, `#999`, `#666`, `#AAA` — the set uses `#59F` for an interactive part.
It uses `#FFC857` with `#E8A33D` beneath it for a highlight. It uses `#FF8700`
for an accent, and `#C83C3C` for a warning.

So you draw a text block as one of the framed pages, because that is what it is.
You do not draw a list of records, a gallery or a form that way. A framed page
with different internal lines is what makes a set nobody can read.

## The Set Is the Unit

A set drawn one icon at a time, each correct against the rules above, still
fails as a set. Seven framed rectangles that differ by two or three internal
lines are one picture at 16 pixels.

The check is cheap, and it is the only one that answers. Render the set at 16
pixels side by side. Name each one without its label. Whatever you cannot name
needs its own silhouette or its own colour, not another line inside the same
frame.

Look at the core's set before you draw, not only at the rules. A uniform set
satisfies the rules, and uniformity is how a careful reader fails.

## What Belongs Where

The SVG goes below `Resources/Public/Icons/` in the extension. You register the
identifier in `Configuration/Icons.php`. `typo3_icon_lookup` with the identifier
answers whether it resolves, and to which file. Whether the picture is right is
a different question, and no tool here answers it.
