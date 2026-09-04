---
id: D-KNW-150
title: What a site answers an error with is a subject this server owns
date: 2026-09-04
status: open
coveredBy:
  - HintsTest::whatASiteAnswersAnErrorWithIsStatedBesideWhatANotFoundWas
---

# D-KNW-150 — What a site answers an error with is a subject this server owns

**The corpus states the `errorHandling` a site configuration takes, beside the
hint that reads what a not-found was.**

One hint named that configuration as what decides the outcome and said nothing
about it.

## Evidence

- `feedback/2026-09-03-235538`. A project audit found `errorHandling` empty and
  repaired it — `errorCode`, `errorHandler: Page`, `errorContentSource` as a
  `t3://page?uid=` reference — from memory, with nothing here to confirm it at
  v14. It called `page-not-found-within-a-site`, which is diagnostic and names
  the configuration as the discriminator without saying what it holds.
- Read in `.checkouts/12.4`, `13.4`, `14.3` and `main`. `Site::__construct()`
  keys each `errorHandling` entry by its `errorCode`, and `getErrorHandler()`
  falls back to the entry with code `0` before throwing
  `PageErrorHandlerNotConfiguredException`.
- The handler words are constants of `Site`: `Page`, `Fluid` and `PHP` on all
  four branches, and `LoginRedirect` from 13. `PageContentErrorHandler` resolves
  `errorContentSource` through `LinkService`, sends an internal subrequest, and
  builds the answer with `responseFactory->createResponse($this->statusCode)` —
  so the body is the page and the status is the one the entry declared.
- Its three failure texts are in that class: the DoS guard where the error page
  is the address being handled, the `RuntimeException` where the subrequest
  answers 300 or above, and `Invalid error handler configuration:` where
  `LinkService` cannot resolve the reference.
- The other half the feedback names is `ErrorController::pageNotFoundAction()`
  thrown as `PropagateResponseException`, which
  `ActionController::handleError()` does itself from 13 where the TypoScript
  sets `mvc.showPageNotFoundIfTargetNotFoundException` or
  `mvc.showPageNotFoundIfRequiredArgumentIsMissingException`. Neither setting is
  in 12.4's Extbase.

## Decided

- Step 1a. `site-error-handling` is a hint of its own rather than statements on
  the diagnostic one: reading what a not-found was and configuring what answers
  it are two questions, and the first now closes by naming the second.
- Two statements are bound: `LoginRedirect` and the Extbase settings, both
  `since: 13`.
- The status codes are written as `HTTP 404` and `HTTP 403` rather than bare.
  `HintsTest` reads a bare three-digit number as a count taken from a checkout,
  and the qualified form is what the corpus already writes.
- `nav_hide` against `hidden` is stated, because the practice the feedback
  reports — an error page kept out of the menu — is one word away from the
  setting that breaks the handler.

## Assumed

- That `errorHandler` has no fifth value on a branch this reads. The four are
  constants of one class and the `switch` throws for anything else, so a fifth
  would arrive as a failing lookup rather than as a wrong answer.

## Wrong if

- A session reports the internal subrequest reaching a page an anonymous visitor
  cannot see, which would make the deliverability statement wrong.
- The fallback on `errorCode` 0 stops being read before the exception, which is
  the one behaviour here that no message names.
