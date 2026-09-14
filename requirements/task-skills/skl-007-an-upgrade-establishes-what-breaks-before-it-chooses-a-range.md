---
id: R-SKL-007
title: 'An upgrade establishes what breaks before it chooses a range'
status: held
restsOn: [D-EVI-001]
heldBy:
  - SkillTest::aDefectInsideTheDeclaredRangeMatchesTheRemovalSkill
  - SkillTest::anUpgradeIsOrderedWorkAndStopsWhereAnotherSkillStarts
  - SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder
  - SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence
---

# R-SKL-007 — An upgrade establishes what breaks before it chooses a range

**What a package owes the TYPO3 majors it declares, and the ones it is meant to
declare, is work in a fixed order.**

The session establishes what breaks from the sweep before it chooses a range.
The dependency solver resolves the range rather than an assertion. The lowest
declared major decides every shape the session writes, and the session proves
every declared combination or names it as unproven. Where the work crosses no
range, the same order runs and the range step reads the declared range instead
of resolves one (`D-SKL-061`). That is a defect whose cause is a removal inside
the range the package already declares.

The sweep the base fixes is where it starts, so this does not restate it. What
this workflow adds are the two sources a changelog query cannot reach. The first
is the Extension Scanner, whose silence is worth what the `FullyScanned` /
`PartiallyScanned` tag says it is worth and nothing more. The second is the
deprecation annotations on the symbols this package calls. An annotation sits on
the class, while only the tags the sweep named reach an entry. Both answer from
the core that is **installed**, which is the boundary the order rests on. What
the target major changed is official documentation until the installation is on
it, never recall. The session runs the sweep again once it is.

The assessment workflow already decides correctly that the shapes an older
declared major requires are a requirement rather than debt. This states it as
the boundary of what may change, not as a judgement to arrive at. What the work
list does not justify is not this workflow's to change, and each of those has a
named owner.

## From

The `REVIEW-02` run of 2026-07-31 in an extension that declares two majors
against an installation a major behind, which established both halves at once.
It made the multi-major decisions, and made them well. It argued the older
major's YAML registration as required because the attribute form is unavailable
there. It refused the same excuse for a deprecated ViewHelper shape whose
replacement works on both. What it lacked was the order. The deprecation with
the largest consequence for that package's next major sat on 24 call sites in 11
files. The run reported the surface as clean. It reached the one deprecated API
it named because a finding walked into it. It never called the Extension Scanner
in a checkout that has one.

## Held by

- `SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder`, `EXT-01`.
- That a session works in this order is not guarded, and no forward run will
  hold it. `D-EVI-001` admits only an open review as forward evidence, and a
  review stops at findings by design.
