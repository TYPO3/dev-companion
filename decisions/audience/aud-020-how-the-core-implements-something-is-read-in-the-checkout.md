---
id: D-AUD-020
title: How the core implements something is read in the checkout
date: 2026-10-05
status: open
coveredBy:
  - ScopeTest::theEntryPointClaimsTheWorkThatEndsBeforeAPatch
---

# D-AUD-020 — How the core implements something is read in the checkout

**The initialize instructions name how the core implements something beside what
changed and which branch: the session reads it in the checkout.
`typo3_backend_module_lookup` says that it reports route identifiers as the
registry composed them.**

## Evidence

- `feedback/archive/2026-09-28-112308-no-route-to-a-version-exact-core-source-fact-so.md`.
  A session that reviewed a documentation pull request checked three of four
  claims in `.Build/vendor` and never asked this server. It did not know whether
  source questions were in scope, and asked for a lookup or for one sentence
  that rules them out.
- Step 2 of the ladder, delivery. `doesNotCover` already said "PHP source as
  code … Read the class". Only `typo3_server_scope` carries that, and the
  session had no reason to call it.
- The same session found `typo3_backend_module_lookup` the better evidence for
  the claim about route names, once its installation ran. The description said
  "the route it answers on" and not that the identifiers are the composed ones.
- The maintainer chose the instructions on 2026-10-05, with the budget kept by a
  shorter first sentence.

## Decided

- One clause in the sentence that already lists what is the caller's to read.
  The first sentence gives back the characters: it names the same four things in
  fewer words. With the stale notice in front the instructions measured 2037
  characters on 2026-10-05, and `R-ANS-013` holds them to 2048.
- `doesNotCover` names the case in its topic. Its `instead` names the module
  lookup as the answer for what a registry made of the code.
- Not built: a lookup that answers which class and which line implement a
  behaviour. The checkout answers it in a few reads, and one session asked.

## Assumed

- That a session reads the instructions before it decides where to look.

## Wrong if

- A second session reports a source question it put to neither the checkout nor
  this server, because it did not know which one answers.
- A session reads the core source for something a registry lookup here answers,
  and reports the lookup only afterwards.
