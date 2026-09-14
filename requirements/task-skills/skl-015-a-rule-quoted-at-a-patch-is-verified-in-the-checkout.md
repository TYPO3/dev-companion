---
id: R-SKL-015
title: 'A rule quoted at a patch is verified in the checkout'
status: held
restsOn: [D-KNW-043, D-SKL-005]
heldBy:
  - SkillTest::aRuleQuotedAtTheIssueIsVerifiedInTheCheckout
---

# R-SKL-015 — A rule quoted at a patch is verified in the checkout

**A review verifies a rule about what an API is for in the checkout. It reports
the rule at the strength its own source puts on it.**

An assessment does not rest on such a rule before that read, and a rule that
forbids something is the one it rests on hardest.

A tracker comment and a documentation page are claims about the code, the way a
path and an identifier are. The assessment that opens a core patch is where one
of them decides whether there is a patch at all. The review names what holds the
claim: the class, its docblock, the tests that cover the form under dispute.

Enforced in code, warned about as fragile and advised in prose are three
different rules, and two neighbour APIs regularly carry different ones. So the
review carries the strength rather than rounds it up. A rule hardened on the way
into an assessment closes the report on a prohibition nothing in the checkout
holds. It reads exactly like one with a check behind it.

## From

`feedback/2026-08-02-144814` (2026-08-02). Forge #105403 got the answer "you
*must not* use `f:image` for anything but FAL resources". The session repeated
it as correct until its user asked what it made of the statement. The checkout
contradicted it. The ViewHelper's own first example is an `EXT:` path, and the
core's functional suite renders that form with scale and crop. Both docblocks
warn about stability rather than support (`D-KNW-043`).
