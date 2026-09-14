---
id: D-ANS-093
title: "A major's deprecations come back in one call"
date: 2026-08-21
status: open
coveredBy:
  - ChangelogLookupTest::aMajorsDeprecationsComeBackInOneCall
---

# D-ANS-093 — A major's deprecations come back in one call

**The deprecations of one major come back in one call, and the tag bounds a
question rather than the sweep a package composes.**

`skills/base.md` step 5 has the caller compose the sweep out of one call per tag
per major. The session that followed it paid eleven calls to reach 72 of the 75
deprecations of 14. That was 1.7 times the payload the same version and type
cost without a tag.

## Evidence

- `feedback/2026-08-19-094403` counts the eleven calls it made and what each
  returned. Re-run on 2026-08-21 against `.checkouts/14.3` through
  `bin/typo3-dev-companion`, every count is what it reported. `ext:core` 30,
  `ext:extbase` 3, `ext:backend` 19, `ext:frontend` 4, `ext:fluid` 5, `ext:form`
  6, `TCA` 12, `TypoScript` 3, `Fluid` 5, `TSConfig` 1, `ext:install` 2.
- Those eleven calls return 90 entries and 72 distinct ones. The 18 duplicates
  are the overlap the feedback reports: the surface tags sit on the entries the
  `ext:` tags already returned. 72 of the 75 deprecations of 14 is the
  composition as the major, three entries short.
- The same eleven against `.checkouts/13.4`: 75 returned, 60 distinct, 60 of the
  63 deprecations of 13. Two of the tags reach nothing at all there.
- The payload measurement comes from the same runs, text and `structuredContent`
  together. The eleven calls cost 69,426 characters across eleven contexts. The
  same version and type with no tag is one call and costs 28,425 for the 50
  entries the cap admits. That is 528 characters an entry and about 41,600 for
  all 75.
- The cap is what stops the one call today rather than the tag. `limit` tops out
  at 50, and the answer says `75 changelog entries — showing the first 50`.
- The covered majors size what one call would have to carry. 128 deprecations at
  12, 63 at 13, 75 at 14, and 128, 105 and 99 breakings beside them. The other
  two types are larger — 217 and 221 features at 12 and 14 — and no feedback has
  asked for either whole.
- `D-SKL-003`'s own **Since then** measured the other package on record.
  `printworks_sitepackage`'s six `ext:` tags reach 34 of the 75. The surface
  tags the step names beside them add `TCA` 12, `Fluid` 5, `TypoScript` 3,
  `YAML` 3, `TSConfig` 1 and `FlexForm` 1. So the second composition on record
  is also most of the major, at twelve calls.

## Decided

- **Step 1b of the ladder, the shape.** The knowledge is here and the axes are
  right; the gap is a form in which the sweep is one call. No verb is absent,
  because an enumeration of a version and a type is what `lookup` already
  answers with the query left out. No wording would have reached it, because the
  answer the wording asks for does not fit in a reply.
- **Queued rather than closed on the spot.** It changes a tool's declared schema
  and the step of `skills/base.md` that asks for it.
  [judging.rst](../../documentation/records/judging.rst) puts that on the far
  side of the autonomous line.
- **A sweep of one major comes back whole in one call.** The boundary is the
  narrower set. A version and a type together are a set of about a hundred
  entries that a caller on an upgrade sweep wants all of. The whole changelog is
  not. Which mechanism carries it is the todo's step, and 128 is the largest set
  the covered majors put under it.
- **The feedback's own suggestion is not the shape.** A list-valued `tag`
  returns the union in one call. The union of the eleven is the enumeration less
  three entries and roughly the same payload. The caller still composes the list
  off its own surfaces and still guesses which of its tags reach nothing. It
  removes the round trips and keeps the reason they were wrong.
- **The package-driven sweep mode is not taken up here.** It would have
  `typo3_extension_describe` pick the tags, which is a second tool's answer that
  decides this one's filter. What it would compose is the major anyway.
  `feedback/2026-08-19-094432` is where the question stands what `describe` is
  the runtime half of, and this entry leaves it there.
- Recorded here rather than against `D-SKL-003`, because what changes is what
  the tool can return. That entry's second **Assumed** priced the composition
  and is what the measurement above answers, so it carries the read and not the
  decision.

## Assumed

- That the caller's call budget is the scarce thing and the payload is not. That
  is `D-FBK-020` and `D-FBK-027` applied to a number measured here: 41,600
  characters in one context against 69,426 across eleven.
- That a sweep asks for deprecations and breakings and for neither of the other
  two types. Every sweep on record passes `type: deprecation`, and the features
  of one major are three times the set.
- That a caller wants the entries rather than the shape of the set. `D-ANS-090`
  decided the other way for 621 Forge issues. What separates this one is that
  the session verified all 72 against its checkout and reported the calls as the
  cost rather than the read.

## Wrong if

- A session gets a major's deprecations in one call and narrows them by tag
  again to make them readable. Or it reports the answer as more than it could
  use. Then the tag bought readability rather than selectivity, and the
  composition was the right shape at the wrong price.
- A package turns up whose composed sweep is a small part of its major. Then the
  tag is selective for that shape of package, and the enumeration is a default
  rather than the answer.
- A caller passes the raised cap on a set nobody narrowed and gets an answer it
  cannot use. Then the ceiling belongs to the narrower set rather than to the
  parameter.

## Since then

The mechanism settled on the raised maximum and against a ceiling that applies
where two filters narrow. The schema decides it. The SDK validates the arguments
before a tool runs, so a maximum that depends on the other fields has no
declaration. Then the validator refuses the call the ceiling exists for before
the tool sees it. Or it is a clamp that contradicts the number the schema
states. The number carries the largest set the covered majors put under a
version and a type, with room for a major that still collects entries.
