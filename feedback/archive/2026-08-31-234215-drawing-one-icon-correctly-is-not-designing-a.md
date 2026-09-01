---
date: 2026-08-31T23:42:15+00:00
category: missing-knowledge
status: closed
closed: 2026-09-01
model: claude-opus-5[1m]
tool: typo3_icon_lookup, typo3_rule_lookup
directory: /home/benji/projects/site-tierheim
---

# Drawing one icon correctly is not designing a set; mine were seven variants of the same box

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14, seven custom content elements needing seven icons. This is an addition to the feedback about typo3_icon_lookup and borrowing content-special-html, and it is the part that survived after I stopped borrowing.

The user, closing the session: "auch dort bist zu sehr starr geblieben und hast harte kästen gebaut, obwohl die icons deutlich mehr erlauben."

He is right and the screenshot I took of the element wizard shows it. What I drew, for all seven:

    <path fill="#FFF" d="M1 1h14v14H1V1z"/>          white ground
    <path fill="#999" d="M1 1v14h14V1H1zm1 1h12v12H2V2z"/>   grey frame
    plus a #666 bar, some #B9B9B9 lines, occasionally an #FF8700 accent

Seven framed rectangles differing by the arrangement of two or three internal lines. At 16 pixels in the wizard, Text, Section heading, List and Fee list are the same picture. Only Animal list and Teaser carry an orange accent large enough to tell them apart at a glance.

That defeats the reason I gave for drawing them at all. The comment I wrote into Configuration/Icons.php says: "in the wizard an element is recognised by its picture first". I then produced a set in which it is not.

What happened: the user handed me https://typo3.github.io/TYPO3.Icons/guide/drawing.html and https://typo3.github.io/TYPO3.Icons/icons/content.html and I read the first as a specification — grid, palette, stroke — and applied it as a template. I took the constraints and none of the range. The second URL, the content icon set, is where the range is: the core's own content icons use different silhouettes, not one silhouette with different filling.

This is a distinct requirement from the one in my earlier icon feedback. That one asked for a document on drawing an icon. This says such a document is not enough on its own: the failure mode of a careful reader is a compliant, uniform, unusable set. The unit of design is the set, and the constraint that matters is that any two of them are separable at 16 pixels.

## Query

Session task; icons drawn 22:05-22:11 after the user supplied https://typo3.github.io/TYPO3.Icons/guide/drawing.html and https://typo3.github.io/TYPO3.Icons/icons/content.html. Result: packages/animalshelter_sitepackage/Resources/Public/Icons/content-animalshelter-{headline,text,list,pricelist,teaser,team}.svg, all built on the same 14x14 framed rectangle. Validated as registered by typo3_icon_lookup; never checked for mutual distinguishability, because nothing checks that.

## Suggestion

If the drawing document from the earlier feedback gets written, it needs a section on the set rather than the icon, or it will produce exactly what I produced.

Worth stating in it:
- The distinguishing feature carries the meaning; the frame is only the family resemblance. An element whose icon differs from its neighbour by internal line count is not identifiable at the size it is rendered.
- Look at the core's content set before drawing: those icons differ in silhouette, not only in fill. Point at the set as the reference, not only at the drawing guide as the specification — a careful reader applies a specification uniformly, which is the failure.
- A cheap check any author can run: render the set at 16px side by side and name each one without looking at the labels. I had that screenshot and did not look at it that way.

And a smaller thing typo3_icon_lookup could do: when validating several own identifiers at once, it already knows their source SVG paths. Reporting that N of them share a near-identical path geometry would be a strange feature — but reporting the set together, as a strip, rather than as a list of booleans, would at least put them next to each other where the problem is visible.
