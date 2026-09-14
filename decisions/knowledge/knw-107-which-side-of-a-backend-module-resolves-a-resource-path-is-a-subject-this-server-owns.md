---
id: D-KNW-107
title: Which side of a backend module resolves a resource path is a subject this server owns
date: 2026-08-24
status: confirmed
coveredBy:
  - HintsTest::eitherHalfOfABackendModuleReachesWhichSideResolvesAResource
  - HintsTest::whatTheClientMayCarryIsSaidApartFromWhatItMayDecide
---

# D-KNW-107 — Which side of a backend module resolves a resource path is a subject this server owns

**Which side of a backend module resolves a resource path the controller
validates anyway is inside this server's boundary and absent from it.**

The corpus answers how the build makes the TypeScript and where its unit tests
go, and says nothing about the PHP the module talks to. A session with both
files gets the asset pipeline.

## Evidence

- Re-run on 2026-08-24 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own two paths reaches `backend-typescript`,
  `javascript-unit-tests`, `system-extension-boundaries`,
  `routing-request-handling` and five more — the pipeline, the test layer and
  the hints every core file matches. The same probe on the words the session
  needed, "what backend TypeScript may hold and what belongs in PHP", reaches
  `icon-usage` and `console-commands`.
- The vocabulary is absent. A search of `knowledge/` and `skills/` for
  `typescript` reaches `backend-typescript`, `backend-ui` and three hint files
  that mention it in one line. It reaches two extension skill references about
  an extension's own build. None of them names a controller.
- The two hints beside the gap are each about something else.
  `backend-typescript` answers where the source lives, what builds it, what
  eslint decides and where a unit test goes; `backend-ui` answers what a custom
  element renders.
- Verified in `.checkouts/main` on 2026-08-24. The worked example the feedback
  names holds. `FormManagerController::createAction()` takes `$templatePath`,
  `isValidTemplatePath()` matches it against the prototype's `newFormTemplates`,
  and line 150 throws `FormException` 1329233410 where it does not. So the
  server resolves and validates that path on every request regardless of what
  the client sent.
- The same subsystem, in the same checkout, contradicts the feedback's own rule.
  `form-manager.ts:117` uses each `newFormTemplates` path as a select option's
  `value`, and `create-form-submission-service.ts:31` submits it back as
  `templatePath`. Backend TypeScript with an `EXT:` path is what the core does
  today, so "it must not hold one" is not the statement.
- One session, from one patch review. `bin/cli feedback:list` on 2026-08-24
  reports 39 open feedback in four directories, and no other one names a
  question about which layer owns a decision.
- The precedent is `D-KNW-070`. A fact that lives on the seam between backend
  JavaScript and the PHP behind it earned a hint of its own. The enumeration
  under it grew once a second session met the same seam.

## Decided

- Taken on, at step 1a of the ladder: the answer is nowhere here, and what fills
  it is a statement rather than a tool. The caller reads no extra source and the
  round trips stay one, which is what `D-FBK-027` asks of anything smaller than
  a tool.
- A hint of its own rather than sentences on `backend-typescript`. One hint is
  one question (`D-KNW-030`). That one answers what builds the asset, this one
  what the asset may decide, and different paths reach them. This one has to
  match a `Classes/` path as well, or it never fires for the half of the task
  that is PHP.
- The feedback's suggestion is evidence, not copy. Its rule — backend TypeScript
  must not hold `EXT:` resource paths — is what the reading has to correct: a
  value the server handed the client and validates on return is carried, and a
  value the client *derives* is the one resolved on the side that validates it.
  A suffix match like `endsWith('BlankForm.yaml')` is the second kind, and a
  path delivered in the module's initial data is the first.
- The priority is `normal` rather than `low`. One session reported it, and what
  it counted is three complete reworks of a patch. The user's same question
  again turned each, rather than any lookup.
- What the hint says about TYPO3 waits for the reading, which is the todo's
  first step: whether this holds beyond `ext:form` is what decides between a
  boundary rule and a fact about one wizard.

## Assumed

- That the boundary generalises. The session read one subsystem, and a rule
  written from one example is a form-manager fact in a rule's clothes.
- That a path-matched hint arrives at all. The session that reported called
  `typo3_hint_lookup` once, by id, so `appliesTo` is what decides whether this
  ever reaches a session with the two files.

## Wrong if

- The reading finds no second subsystem where the client submits an intent and
  the controller resolves it. Then it belongs to `form-framework` as a fact
  about that wizard, and this entry is why anybody looked for it.
- The core moves the other way — a patch lands that has the client resolve a
  resource the controller then trusts. Then this is a preference this repository
  holds and the core does not, which is not a subject this server owns.
- A session reports that the hint arrived and it put the resolution in
  TypeScript anyway. Then the gap was wording rather than knowledge.

## Confirmed on 2026-08-24

The boundary generalises and the first **Wrong if** does not hold. Two more
subsystems submit an intent and resolve the resource behind it on the server,
read on all four checkouts.

The second settled the other way round from how it stood. It asked whether the
core would move towards a client that resolves a resource. The checkouts show it
move away, inside this very subsystem. The one literal `EXT:` resource path in
the backend TypeScript tree is gone on the two newer lines.

So the feedback's own rule gets a correction rather than a copy. To carry a
value the server gave it is what the core does, and to work one out is what it
removed. One bullet stays out because no checkout does it. A hint that states it
would be this repository's preference in a TYPO3 fact's clothes.
