---
date: 2026-08-31T23:49:29+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup, typo3_hint_lookup, typo3-content-element-development
directory: /home/benji/projects/site-tierheim
---

# Every visual check was reactive; the guide any/testing/browser-check was named to me and never fe...

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14. The user's charge was that I looked at neither frontend nor backend, and that the guides make visual feedback a requirement. The first half is not what the transcript shows and the second half is exactly right, so both are worth recording precisely.

Measured: 47 screenshots read, 85 Playwright or screenshot runs, spanning 19:24:57 to 23:27:18. I looked, repeatedly.

What the timestamps show is that I never initiated it.

First look at the frontend: 19:25:03. The session started at 18:38:34. That is 47 minutes of building with no rendered page seen, and it happened 66 seconds after the user wrote "bite prüfe alles durch und mach ein schönes modernes frontend" at 19:23:55.

First look at the backend: 22:04:30, three hours and twenty-six minutes in. It happened 78 seconds after "wie hast du das verifiziert? hast du dich ins backend eingeloggt? du musst alles visuell prüfen" at 22:03:33, which itself followed "ich bekomme fehler wenn ich auf das backend gehe" at 22:03:12. So the first time I opened the backend of an installation I had been building for three and a half hours was after the user told me it was broken.

Every later visual check follows the same shape: a complaint, then a screenshot. Not one of the 47 was taken because I had finished something and wanted to know whether it was right.

Now the part that is yours. This server carries a document for exactly this. The browser-tests hint names it in its documents array: typo3://guides/any/testing/browser-check, described in the hint as being "for looking at the element in an installation that already holds the content, which is the step before anything asserts a backend preview". I received that hint at 22:35 and did not fetch the guide. I did fetch its neighbour, project/testing/playwright, at around 20:00, and used it to build a suite — which is the other document, about specifying rather than looking.

So the ordering the guides prescribe — look first, then assert — I inverted: I wrote assertions for months of behaviour I had never seen, and the assertions passed. The two tests that were green for the wrong reason both came from that inversion.

typo3-content-element-development states the requirement too, in the sentence "A defect that has to be seen rather than asserted — a position, a stacking order, something that only appears while scrolling — is looked at rather than specced first." That skill was active from 21:30. The first backend screenshot is 34 minutes later and still not my idea.

## Query

Whole session. 47 image Reads between 19:25:03 and 23:27:27; 85 Playwright/screenshot Bash calls between 19:24:57 and 23:27:18. First frontend screenshot 19:25:03, 66s after the user asked for a check. First backend screenshot 22:04:30, 78s after the user asked how I had verified. typo3_rule_lookup(documentId="project/testing/playwright") fetched ~20:00; typo3_rule_lookup(documentId="any/testing/browser-check") named in the browser-tests hint documents array at 22:35 and never fetched.

## Suggestion

The document exists and did not reach me, which is a routing problem rather than a knowledge one.

Make browser-check the answer to a question a caller asks before they have a suite, not a sibling of the suite guide. As it stands both are listed together in the documents array of the browser-tests hint, and a caller who is thinking about tests fetches the one about tests. The two are different acts at different moments — looking at something before you believe it works, versus writing down what must keep working — and only the first one has a moment where nobody is asking for it.

Concretely: name it from the workflows where something has just been built. typo3-content-element-development already carries the sentence; it could carry the documentId beside it, since a documentId is fetchable and a sentence is not. The same in typo3-development-installation's "Prove it" section, which currently says the site answering is the proof and stops at status codes — a page that answers 200 and renders a wall of unreadable text is what this session shipped for two hours.

And one line in browser-check itself, if it is not already there: the backend is half of what has to be looked at. Every prompt in this session that produced a visual check named the frontend; the backend was only opened when it threw. An editor's view of a content element is not visible from any frontend screenshot, and the four defects that mattered most here — html elements in the selector, missing previews, borrowed icons, invalid CType badges — were all only visible there.
