---
id: D-KNW-122
title: A procedure document is routed by the evidence a task needs
date: 2026-08-25
status: open
coveredBy:
  - KnowledgeTest::everyGateOnTheRenderingProbeNamesTheEvidence
  - KnowledgeTest::theTestRunGuideNamesTheRenderingProbeWithTheFunctionalSuite
---

# D-KNW-122 — A procedure document is routed by the evidence a task needs

**The evidence a task needs routes a document that says how to produce evidence,
rather than the class of change that made the document necessary.**

Four of the five places that name `core/testing/proving-a-rendering` gate it on
TypoScript. A session that reviewed a PHP diff read one of those gates, skipped
the page, and built the harness by hand over six container rounds.

## Evidence

- The route and the description sit in one sentence, and the gate won. The
  scratch-probe paragraph of `typo3-core-patch-review` opens on "a diff that
  changes what the frontend renders". Then it names the page as "which cObj
  renders the snippet, which operator form takes markup that spans lines, and
  how the output is got out of a test that would otherwise print nothing". The
  feedback quotes the second half back as the line it needed on round trip one.
- The diff under review was PHP throughout, three lines in
  `PageContentErrorHandler.php` and a `protected`-to-`public` change in
  `PageRenderer.php`. Every finding the review made was about what a rendering
  contained. What the page sits under and what needs it are two sets, and the
  second is the larger.
- That is `D-KNW-071`'s second **Wrong if** word for word: a session with the
  document installed still builds its probe by hand.
- Four of the five routes name the diff and one names the evidence. The
  document's own `whenToUse`, the `covers` topic and the `routing` entry in
  `knowledge/server-scope.json` name TypoScript defaults, `ext_localconf.php`
  TypoScript and `lib.parseFunc`. So does the review skill's paragraph.
  `typo3-core-issue-triage` names none of them. "Where the symptom is rendered
  output, the throwaway has to produce it before it can assert anything, and the
  value is the unknown rather than the expectation."
- The `whenToUse` never reached this session. `ProjectDescribe::guides()` lists
  each document as `id` and `title` alone. So the card `D-KNW-057` composes from
  the front matter is what a client that renders a resource list sees, and this
  one rendered none. The gate the session read is the skill's.
- Nothing reaches the page on words instead. `bin/cli hints:probe` with "prove
  what a rendering contains from a functional test whose subject is a PHP class"
  returns `core-tests` and `project-extension-tests`. With "read the rendered
  HTML a functional test produced" it returns five more. None of the seven is
  about output to look at.
- `typo3_test_run_guide` is where a pointer of this kind already goes twice.
  `SCRIPTS_GUIDE` came after a session had every `runTests.sh` question answered
  there and never reached `typo3_script_lookup` (`D-ANS-061`).
  `BROWSER_CHECK_GUIDE` came after a review held `any/testing/browser-check` and
  never opened it (`D-KNW-069`). Both rest on the same reading: the moment a
  caller is about to run something is the moment it is certainly reading. This
  is the third session to lose a page that tool could have named.
- Half of what the feedback asks for is already on the page. "How the output is
  got out of a test that would otherwise print nothing" is its **Reading What It
  Rendered** section. `echo` from the test body does not care what the subject
  is. What is not there is how to tell apart *which part* of a rendering
  changed. The session wrote markers into body, `headerData`, `footerData` and
  meta, and instrumented a fixture `userFunc` to print a service's state
  mid-request.

## Decided

- The ladder's step 3, routing. The knowledge exists, the server delivered it,
  and the trigger describes a smaller set than the one that needs it.
- Queued rather than closed on the spot. The route from `typo3_test_run_guide`
  is a change to `src/`. A session has to read the half the page does not carry
  out of `.checkouts/` first.
- The four diff-shaped gates are rewritten together. A `whenToUse` and a skill
  paragraph that disagree route the same page twice, and the triage skill's
  sentence is the form the other four take.
- `typo3_test_run_guide` names the page where its answer carries a functional
  suite, in the shape the two constants in that file already have. Which
  condition exactly is the todo's to settle.
- The page's boundary does not move. It stays the proof rather than the render,
  and it names no patch. What the document gains is how to make a rendering say
  which part of it changed.
- The priority is `normal`. One session reported this page and three have now
  reported the shape, and `D-KNW-071` set `normal` for the page itself.
- What the `guides` array carries is not this entry's. Two feedback report that
  list as id-only and `D-GUI-012` holds it. A card that took them over would
  merge a gate's wording with the mechanism that renders it.

## Assumed

- That the wider gate does not over-fire. "Any finding that has to be settled by
  what a rendering contains" admits more reviews than the TypoScript one. A
  probe built where a read of the diff would have done costs a container round
  for nothing.
- That one page carries both subjects. A TypoScript snippet and a PHP request
  pipeline may want two fixtures under one procedure, or two documents.

## Wrong if

- A session reaches the page from `typo3_test_run_guide` and still builds its
  probe by hand. That would put the gap in the procedure rather than in the
  route.
- Reviews start to reach for a probe on diffs a read already settles, which
  would say the evidence-shaped gate is no gate at all.
- The PHP-subject harness turns out far enough from the TypoScript one that the
  page becomes a table of subjects. That is `D-KNW-071`'s third **Wrong if** in
  a second form.

## Since then

The four gates were rewritten together and `typo3_test_run_guide` names the page
wherever its answer carries a functional suite. The page kept one procedure.
What the PHP subject needed was a marker per region of the response. And a
`USER` fixture that prints what a service holds from inside the request. Both
sit under the harness the TypoScript subject already had. What that fixture
reads was the session's own finding, and `.checkouts/` says why. The rendered
body reaches the page renderer after every content object has run, on every
covered version. So a probe that reads it from inside one measures the moment
rather than the page.
