---
id: D-ANS-007
title: '`unsupported` and `unavailable` are two answers, and `cause` says why'
date: 2026-08-02
status: confirmed
coveredBy:
  - DocumentationTest::aVersionOutsideTheCoveredOnesIsNotAskedFor
---

# D-ANS-007 — `unsupported` and `unavailable` are two answers, and `cause` says why

**`typo3_documentation_lookup` keeps `status`, and its `unavailable` object
carries a `cause` — the same word the installation answer uses, with values of
its own.**

Two tools said "this source could not be asked" in two vocabularies. The
installation-backed eight said `unsupported` with a `cause`; the manual said
`status` with `answered`, `empty` and `unavailable`. `D-KNW-005` settled four
spellings of one idea into `scope`, and the question was whether this is the
same case.

## Evidence

- It is not the same idea. `unsupported` means the server cannot answer the
  question from where it stands. Every field it requires says how to change
  that: where discovery walked, which variables name an installation and its
  console. The manual is reachable from anywhere or from nowhere, and none of
  those fields has a value there. A merge would either fake them or loosen the
  required list the eight promise.
- The remedies are opposite. A caller answers `unsupported` with a root; a
  caller answers a manual that did not respond with a second ask.
- What was actually wrong is inside the manual's own field. `unavailable`
  covered both a release outside the covered versions and a docs.typo3.org that
  does not respond. The first is permanent, and a second ask changes nothing;
  the second is transient. That is the distinction `D-ANS-005` introduced
  `cause` for on the other side, where prose carried it and only prose did.

## Decided

- Two shapes, and the line between them is what the caller can do about it.
  `unsupported` where the server does not support the question here at all,
  `status: unavailable` where the source that would have answered it did not
  answer.
- One word for why. `cause` is `version-not-covered` or `source-not-answering`
  on the manual, and `no-installation`, `misconfigured` or
  `installation-not-answering` on an installation. That is one key, disjoint
  values, and each names a remedy.
- `status` stays a discriminator rather than becomes a `oneOf`. The manual's
  three states are states of the answer, and `empty` is a result: the caller
  rephrases. On the installation side the same case is a count of zero. That is
  why the shape there splits in two and this one does not.

## Assumed

- A client that has learned `cause` on one tool reads it on the other with no
  second vocabulary to learn. That is the whole of what the two shapes now
  share. If that turns out not to be enough, the next step is the schema, not
  the prose.

## Wrong if

- A caller retries a `version-not-covered` answer, or gives up on a
  `source-not-answering` one. Both would mean the value does not reach the
  behaviour. The fix is to say the remedy in the reason rather than to add a
  third field.
- A third source arrives that is neither an installation nor a manual and fits
  neither shape. Then the split is between "here" and "elsewhere" rather than
  between the two sources this server has.

## Confirmed on 2026-08-22

The third source arrived and fits, which is the second **Wrong if** read the
right way round. The two kinds that were not here on the entry's date answer in
the shape the line predicts. They are reachable from anywhere or from nowhere,
so they carry the status vocabulary and an unavailable object with a cause. None
of them took the value that says the server cannot ask a question from where it
stands.
