---
date: 2026-09-18T09:31:32+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# javascript-unit-tests hint has no shape for a Lit component that loads data through AjaxRequest

## Observation

Trimmed on 2026-09-19 to what is still open. Points 1 and 2 are in the
`javascript-unit-tests` hint since that day: the core's own test stubs
`window.fetch`, and `AjaxResponse.resolve()` reads `Content-Type`. Points 3 and
4 remain, because neither holds without a run of the suite.


Task: write a JS unit test that reproduces the tree filter reset defect in Build/Sources/TypeScript/backend/tree/tree.ts.
The javascript-unit-tests hint told me where the file goes, that tests run on built output, and that sinon stubs a method on an imported object. That was correct and I used it. It stopped before the part that cost the session four full suite runs. I had to work out four things in the checkout:
1. Tree fetches through new AjaxRequest(url).get() inside the method, so the stub goes on AjaxRequest.prototype.get, and the fake reads this.url, a private field, to dispatch on the request.
2. The fake has to return an AjaxResponse built from a Response with Content-Type application/json, because resolve() reads that header.
3. The @open-wc/testing-helpers typings declare oneEvent with a required third parameter preventDefault. A two-argument call fails the build with TS2554. The implementation takes two.
4. A failing chai assertion on a Lit-rendered element (expect(element).to.be.null, to.have.class) hung the whole test file until the 120000ms testsFinishTimeout, and the reporter showed 234 passed with no failure. Asserting on primitives (arrays of data-tree-id values) made the same defect fail in three seconds. I did not establish the cause; the symptom is reproducible.
Tree is not a registered custom element, so the test defines a subclass with customElements.define and sets settings.defaultProperties in the constructor like PageTree does. That I read from page-tree.ts.

## Query

typo3_hint_lookup paths=["Build/Sources/TypeScript/backend/tree/tree.ts","Build/Sources/TypeScript/backend/tests/tree/tree-test.ts","typo3/sysext/backend/Resources/Public/JavaScript/tree/tree.js"] task="fix filter reset in tree web component and add JS unit test"

## Suggestion

Add to javascript-unit-tests: a backend component that fetches with AjaxRequest is tested by stubbing AjaxRequest.prototype.get and returning new AjaxResponse(new Response(JSON.stringify(data), { headers: { 'Content-Type': 'application/json' } })). Note that oneEvent takes three arguments in the shipped typings. Warn that an assertion whose subject is a rendered Lit element can hang the file on failure, and that the file then shows no failure at all. Say to assert on data read out of the element instead. One worked example for a Tree subclass would carry all four.
