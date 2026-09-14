---
id: D-ANS-062
title: An anonymous read cannot tell a restricted change from an absent one
date: 2026-08-07
status: open
coveredBy:
  - GerritTest::anEmptyAnswerForANamedChangeSaysWhatItCannotSeparate
---

# D-ANS-062 — An anonymous read cannot tell a restricted change from an absent one

**`typo3_gerrit_lookup` reads review.typo3.org without credentials, so a private
or work-in-progress change comes back as `empty`. `empty` is the word a caller
acts on as "no patch exists".**

## Evidence

- `feedback/2026-08-07-132416` is a review session that made the first finding
  of its review out of this. Lookup by the Change-Id in the commit under review
  returned `{"status":"empty","changes":[]}`. By issue `109572`, `empty`. By
  change number `95162`,
  `{"status":"unavailable","cause":"source-not-answering"}`.
- The change existed. `typo3_forge_lookup` on the same issue, in the same
  session, returned a journal entry from Gerrit Code Review. It names patch set
  1 and the review URL that ends in `95162`. The session read the two answers
  against each other and concluded that 95162 was somebody else's rival change.
- On that account it recommended coordination with the other author before the
  push, ranked under "what blocks the patch from being submitted at all". The
  user corrected it: the change is private and is the commit in the checkout.
- `Gerrit::` returns `empty` where the parsed change list is empty and
  `unavailable` with `source-not-answering` where the request itself fails. So
  one cause produces two statuses, and which depends on whether the query was a
  search or a direct read. The feedback names that split and never saw the
  source.
- The tool description frames the call as "Find out whether a TYPO3 core patch
  already exists", which is the sense that fails here.
- It compounds with `typo3-core-patch-review`, which instructs that an answer of
  nothing is a result and the report says so. That instruction is only safe
  while `empty` means absent.

## Decided

- `empty` is an overstatement wherever the query named a concrete Change-Id or
  change number. A search over commit messages that matches nothing is a real
  absence. A direct read that returns nothing is a permission effect or an
  absence, and the reader cannot tell which.
- The answer says so rather than the tool description. A client reads the
  description at install time; a session reads the answer when it writes the
  verdict. `R-ANS-024` already holds that a field answered empty is one nothing
  could fill.
- The evidence the server already holds is worth its spend. Where
  `typo3_forge_lookup` would surface a review URL for the same issue, an empty
  Gerrit answer for that number is positive evidence. It says restricted change
  rather than absent one. That is the feedback's own observation and it needs no
  credentials.
- This is not a case for a credential. Reading without credentials is what keeps
  the tool free of a secret to hold, and `D-FBK-042` puts the read-only boundary
  at the installation. What changes is what the answer claims.

## Assumed

- The two statuses have one cause here. `unavailable` for the direct read is
  consistent with a Gerrit that refuses an anonymous read of a restricted
  change. No second case is on record. But a review server that is simply down
  produces the same word, and nothing in this report separates them.
- A caller acts on the status rather than on the description. Three wrong
  conclusions in one session support it, and one session is one read.

## Wrong if

- The answer starts to report a change that is genuinely absent as possibly
  restricted, and a session hesitates over a patch nobody has pushed. That would
  say the hedge costs more than the wrong verdict it prevents.
- The Forge cross-check turns out to fire on merged changes too, so the signal
  says "restricted" about something anybody can read.
- Gerrit turns out to answer differently for a private change than for one that
  does not exist. That would mean the two were separable all along.

## Since then

A measurement of the first **Assumed** shows half of it is wrong. Asked of the
review server directly, both handles and a change number that exists nowhere all
answer with an empty list. So the tool is consistent after all. What the report
saw once was a server that did not answer that once, rather than a second shape
of the same effect. The feedback inferred a rule from a single read, which is
what one read supports.
