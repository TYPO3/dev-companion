---
id: D-KNW-160
title: What the core owes for a changed TypeScript member is stated in the corpus
date: 2026-09-19
status: open
---

# D-KNW-160 — What the core owes for a changed TypeScript member is stated in the corpus

**`public-api-surface` says what a shipped TypeScript module's surface is and
what a moved export owes, beside the PHP signature rules it carried alone.**

A patch that changed a protected member's type on a backend module was sent to
the hint by `typo3-core-patch-development` to settle its target branch, and read
PHP autoload fatals and nothing about TypeScript. The session settled it itself
in the checkout and left it to the reviewer.

## Evidence

- `feedback/2026-09-18-093155`: the session read that the core ships no
  declarations and that no core file outside `tree.ts` reads the member, and
  decided nothing was owed, as an open point in its report.
- Read on 2026-09-19 in `.checkouts/13.4`, `14.3` and `main`: no `.d.ts` stands
  below `typo3/sysext`, and `Build/tsconfig.json` names no `declaration`. So a
  consumer types against nothing, and `protected` is a word the build drops.
- The changelog on `main` carries a Deprecation entry with the `:js:` role for
  `markFieldAsChanged()` moved out of `@typo3/backend/form-engine-validation`,
  with a deprecation notice in the browser console as its impact. That is the
  precedent for a moved export. Six backend modules on every covered line carry
  `@internal` in their TypeScript.
- `bin/cli hints:probe` on the member and the module reached the PHP half and no
  statement about TypeScript. Step 1a.

## Decided

- Two statements in `public-api-surface`, and the hint carries `typescript`
  among its domains. The surface is every export and every member a module
  outside the core can call from the built JavaScript. A moved or renamed export
  gets a Deprecation entry with the `:js:` role, and an `@internal` module owes
  none.
- Not stated: what a changed protected member's type owes. The changelog carries
  no precedent for it, and a statement about it would be the reporting session's
  own judgement copied in. The hint names the surface, and the patch's author
  decides against it.

## Wrong if

- The core starts to ship declarations. Then a changed member type has a
  compile-time consumer and the first statement is what goes stale.
- A changelog entry turns up for a changed member of a shipped module with no
  move. Then the second half of what the session asked has a precedent, and the
  hint states it.
