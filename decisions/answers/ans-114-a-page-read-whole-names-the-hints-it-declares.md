---
id: D-ANS-114
title: 'A page read whole names the hints it declares'
date: 2026-08-27
status: open
coveredBy:
  - KnowledgeTest::aPageReadWholeNamesTheHintsItDeclares
---

# D-ANS-114 — A page read whole names the hints it declares

**A `documentId` answer names the hints the document declares in its text, as
the `typo3_hint_lookup` call that reads them. The search answer beside it
already does.**

A session read `any/testing/browser-check` whole and then spent five round trips
on a fact the hint that page declares states verbatim.

## Evidence

- `feedback/2026-08-24-225022`. Its first and largest point is that a backend
  module renders in an iframe. So `page.locator(...)` against the top document
  matched nothing four times in a row. It cost that session roughly five round
  trips of wrong hypotheses before it enumerated `page.frames()`. Those were a
  wrong page id, a wrong module route, an `SET[language]` parameter, a wrong
  selector.
- The fact is in the corpus already. `browser-tests` carries "A backend module
  renders inside an iframe — id typo3-contentIframe, name list_frame — so every
  locator in a backend spec goes through
  `page.frameLocator('#typo3-contentIframe')`, and one that does not simply
  matches nothing." That is the report's own finding, and it predates the
  report.
- `browser-check` declares `browser-tests` in its own front matter, so the
  relationship the session needed stands curated rather than guessed.
- The query was re-run here on 2026-08-27.
  `typo3_rule_lookup(documentId="any/testing/browser-check")` returns 3604
  characters of text, and the only two lines in it with `browser-tests` are the
  raw YAML of the front matter. The hint ids reach the caller as `alsoInHints`
  in the data half and as metadata in the prose half. No sentence says what they
  are and no call to make.
- The search half of the same tool does say it. `RuleLookup::answer()` appends
  `alsoInHints()` to the text for hints a query matched lexically, three at
  most. It reads "The hints also cover this — call typo3_hint_lookup with the
  id". So the weaker signal arrives in prose and the stronger one does not.
- [`D-KNW-060`](../knowledge/knw-060-what-a-backend-spec-locates-by-is-written-where-the-spec-is.md)
  decided that placement, and its first **Wrong if** did not fire. It describes
  a session that has the iframe sentence and still writes locators that find
  nothing. This session never had the sentence.
- The report's fifth point does not hold. It says the prepared instance is a
  bare TYPO3 until `styleguide:generate --create` runs.
  `Build/Scripts/setupAcceptanceComposer.sh` generates the content during setup
  on every covered major. That is `styleguide:generate -c tca` and then
  `-c frontend` or `-c frontend-systemplate` on `.checkouts/main`, `14.3` and
  `13.4`, and `styleguide:generate -c -- all` on `12.4`. The reporter saw both
  trees already there, which is that script after its run.
- Its second point holds and is unbound. `Build/tests/playwright/config.ts`
  defaults the backend login to `admin` and `password`, overridden by
  `ACCESSIBILITY_BACKEND_ADMIN_USERNAME` and
  `ACCESSIBILITY_BACKEND_ADMIN_PASSWORD`, identically on all four checkouts.
  Nothing in `knowledge/` or `skills/` said so.
- Its third point holds too and is `feedback/2026-08-24-225044`'s own subject.
  `runPlaywright()` publishes the web container with `-p 127.0.0.1::80` and
  reads the result back with `docker port ac-web-${SUFFIX} 80/tcp`, so the port
  is ephemeral by construction. The 43837 the help text names is the Playwright
  UI's port inside its own container, not the instance.

## Decided

- The judgement is **step 2 of the ladder**, delivery. The answer is here, in
  the hint the page the session read declares, and the answer it read named it
  as data alone.
- **Queued rather than closed on the spot.** The change is in
  `src/Tool/RuleLookup.php`, which
  [judging.rst](../../documentation/records/judging.rst) puts on the reviewed
  side of that line.
- The priority is `normal`, set by the five round trips the report counts
  against a change of one line and one assertion.
  [`D-FBK-027`](../feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)
  is the measure.
- The credentials land in the same run. They come off the four checkouts here,
  so nothing about TYPO3 remains to look up. A queue entry would send the next
  session to the same file —
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md).
  They go into `browser-check`, in the paragraph about the instance that carries
  them, because that is the page the session read.
- The dropdown is not taken. The report's fourth point is that a control inside
  a closed dropdown resolves and is never stable. It says itself that
  Playwright's error names the element, a statement that pays for nothing.
- The document says nothing about the styleguide. The page already says the
  instance is a styleguide, and that is what the checkouts show.

## Assumed

- That a session which read a whole page reads the line at its foot.
  [`D-ANS-070`](ans-070-a-document-is-handed-over-by-the-call-that-reads-it.md)
  assumed the same about a cut answer and that assumption fell. That is the one
  reason to expect this lever to be weaker than it looks. It is cheaper here:
  the caller has reached the end of the page and has no next query of its own.
- That the declared hints are few enough to name whole. The corpus has one and
  two per document today, so nothing decides what a page that declares ten would
  print.

## Wrong if

- A session receives the hint line under a whole page and still does not call
  `typo3_hint_lookup`. Then the delivery is not the lever, and the iframe fact
  belongs in `browser-check` itself rather than one call away from it.
- A document turns out to declare hints that only repeat what it says, so the
  line buys a call and returns the page again. Then what is worth a name is the
  hint a page does not already carry, and the declaration is the wrong list.
