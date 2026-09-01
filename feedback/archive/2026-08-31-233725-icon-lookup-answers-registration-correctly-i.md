---
date: 2026-08-31T23:37:25+00:00
category: idea
status: closed
closed: 2026-09-01
model: claude-opus-5[1m]
tool: typo3_icon_lookup, typo3_rule_lookup
directory: /home/benji/projects/site-tierheim
---

# icon_lookup answers registration correctly; I read the yes as permission to borrow another CType'...

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14; at this point I was registering six new content elements and choosing icons for them. Read from the full transcript, not recalled.

21:30:48 typo3_icon_lookup(query="user group team person", limit=12) -> 46 matches, scored, with a "why" per hit. Good answer.
21:32:04 typo3_icon_lookup(identifiers=["content-card","status-user-group-backend","content-text-teaser","content-special-html"]) -> all four registered:true, plus a scope note that these address the backend icon registry and frontend rendering needs its own SVG.

That answer is correct. All four identifiers exist. The tool was asked whether they are registered and it said they are.

What I did with it: put content-special-html on a custom content element of mine.

21:53:11 the user: "wir sollten kein html element iconn nutzen für eigene content elemente"
22:05:47 the user hands me https://typo3.github.io/TYPO3.Icons/guide/drawing.html
22:11:00 the user hands me https://typo3.github.io/TYPO3.Icons/icons/content.html
22:11:05 "hier findest du beispiele"

The error is entirely mine and it is a reading error, worth recording because it is the kind a lookup invites: I asked "does this identifier exist", got yes, and acted as though I had asked "does this icon describe my element". Existence and fitness are different questions and only the first one is a lookup. The user put it exactly: the icon is correct, it exists — it is unusable to describe a new element, which is why he asked for new ones.

The rest of that thread went right and is worth keeping: I drew seven SVGs and at 22:02:01 called typo3_icon_lookup with the seven new identifiers. It came back registered:true with source EXT:tierheim_sitepackage/Resources/Public/Icons/<name>.svg for each. That is the tool doing the job it is for, and it confirmed the registration actually resolved rather than that the files existed.

One gap I left: at 22:47 I renamed every identifier content-tierheim-* to content-animalshelter-*, and did not call typo3_icon_lookup again. The single use of this tool that would have caught a real regression is the one I skipped. It held — the browser screenshots show the icons — but I did not establish it.

What I could not get from this server at all, and had to be handed as two URLs by the user, was how to draw one: the palette, the grid, the stroke, the frame convention. That is the question I actually had.

## Query

typo3_icon_lookup(identifiers=["content-card","status-user-group-backend","content-text-teaser","content-special-html"]) at 21:32:04 -> four registered:true. Then typo3_icon_lookup(query="user group team person", limit=12) at 21:30:48. Then typo3_icon_lookup(identifiers=["content-tierheim-headline","content-tierheim-text","content-tierheim-list","content-tierheim-pricelist","content-tierheim-teaser","content-tierheim-team","content-tierheim-animallist"]) at 22:02:01 -> seven registered:true with EXT: source paths. No call after the rename to content-animalshelter-*.

## Suggestion

Two things, one small and one that is a real gap.

Small: in the `validated` block, say what an identifier is already bound to where the server can see it. `content-special-html` is the icon of a core CType; an entry reading registered:true, usedBy:["tt_content.CType=html"] would not answer "does it fit" — nothing can — but it turns a bare yes into "yes, and it is the HTML element's". A caller registering a different CType sees the collision without having to know the naming scheme. The same for content-text-teaser and content-card. Where an identifier is unbound, say that too: "registered, not bound to a CType" is the answer that means "free to use".

The real gap: there is no document here about drawing one. The user had to hand me https://typo3.github.io/TYPO3.Icons/guide/drawing.html and https://typo3.github.io/TYPO3.Icons/icons/content.html. A typo3_rule_lookup document — `any/icons/drawing-a-content-icon` — carrying the 16x16 grid, the palette (#FFF ground, #999 frame, #666 header bar, #B9B9B9 light lines, #FF8700 accent), the stroke width, and the convention that a content icon is a framed page with its distinguishing mark inside, would have answered the question I had after the borrowing was refused. As it stands the server can tell you an icon exists and can tell you yours resolves, and has nothing for the step between those two.
