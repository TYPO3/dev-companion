---
date: 2026-08-31T23:44:34+00:00
category: missing-knowledge
status: closed
closed: 2026-09-01
model: claude-opus-5[1m]
tool: typo3_rule_lookup, typo3_hint_lookup
directory: /home/benji/projects/site-tierheim
---

# Nothing says a new test must be seen to fail; 107 green tests ran through 118 broken records

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14. This is about the browser-testing guidance, which is good on what to assert and silent on what makes an assertion evidence.

The session's most expensive defect: a rename left 118 content elements carrying CType values the site no longer declared. The backend showed "Invalid value (tierheim_text)" badges. The full suite — 107 Playwright tests including a walk over the whole page tree and one test per backend page — was green through all of it. The user found it by opening the page module and sent me a screenshot.

Two separate tests were green for the wrong reason and neither was caught by running them.

First, a backend test asserting the animal records are findable. It navigated to /typo3/module/web/list?id=2 — a route that does not exist on v14, where the record list is /module/content/records. TYPO3 served the layout module instead, whose chrome contains the site name "Tierheim Wiesbaden", and the assertion was `toContainText('Tier')`. It passed on the module header of the wrong module. I only found it because the rename changed the site name to "Animal Shelter Wiesbaden" and the substring stopped matching.

Second, my own replacement for it. I wrote a test asserting the record list contains no "Invalid value" text, ran it, saw it pass, and reported the gap closed. It was passing because the record list does not render that badge at all — FormEngine does. The test could never fail.

What finally worked was a practice nothing here states: I set one row's CType to a broken value, ran the test, watched it go red, restored the row, ran it again and watched it go green. Both directions. That is what turned the third attempt into evidence, and it caught that the second attempt was worthless.

The browser-tests hint is precise about the things it covers — the iframe and page.frameLocator('#typo3-contentIframe'), the layout of e2e/fixtures/helper, that a defect which has to be seen is looked at before it is specced, that a functional subrequest proves server-side rendering and nothing about layout. project/testing/playwright, which I read at ~20:00, covers configuration, backend login and a spec per project. Neither says anything about establishing that a new assertion can fail.

For a browser suite that is the load-bearing practice, because the failure mode is specific to it: a locator that matches nothing, a substring that appears in chrome, an assertion on text a module never emits. All three of those pass silently, and all three happened here.

## Query

typo3_rule_lookup(documentId="project/testing/playwright") read at ~20:00. typo3_hint_lookup returning the browser-tests hint at 22:35. Neither states that a new test must be observed failing. Tests that passed for the wrong reason: Build/tests/browser/e2e/backend.spec.ts asserting toContainText('Tier') against /typo3/module/web/list?id=2 (non-existent route on v14, served the layout module); and a replacement asserting the record list contains no 'Invalid value' (a badge the record list never renders).

## Suggestion

One paragraph in project/testing/playwright, and a statement in the browser-tests hint:

A new assertion is not evidence until it has been seen to fail. Break the thing it is about — change the row, remove the element, rename the identifier — run the test, watch it go red, restore, watch it go green. Both directions, because a test that is red for an unrelated reason is the same problem in the other direction.

Then the three shapes that pass silently in a browser suite, which is why this matters here more than in a unit suite:
- a locator that matches nothing: an assertion on an empty locator set, a filter that eliminated everything, a loop over a list that came back empty. State the countermeasure — assert the count before the loop.
- a substring that appears in the surrounding chrome rather than in the content: module headers, the site name, navigation labels. Assert on data the page under test owns.
- an assertion on text the module never emits, which is what a wrong route or a wrong module produces. Assert on a route's own marker, not on text you expect to find there.

And a note that would have caught the first one directly: backend module routes changed in v14 and `web_list` is a module alias rather than a route path; a spec carrying /typo3/module/web/<x> from an older project silently lands somewhere else rather than 404ing. typo3_backend_module_lookup answers what the path is, and the browser-tests hint is where a caller writing a backend spec would see it named.
