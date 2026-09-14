---
id: R-SCO-001
title: 'Outside the core is recognised from structure, not wording'
status: held
heldBy:
  - ScopeTest::aDotfileKeepsItsDotWhenAPathIsNormalised
  - ScopeTest::aPackageShapedPathInACoreCheckoutIsCoreWork
  - ScopeTest::aPathInsideAnExtensionIsRecognisedByItsShape
  - ScopeTest::aPathKnownAsSomebodysExtensionIsOutsideTheCore
  - ScopeTest::anExtensionRepositoryIsPlacedByItsRootManifest
  - ScopeTest::inACoreCheckoutNothingIsPushedOutsideByTheInstallationAlone
  - ScopeTest::inASiteInstallationTheWorkIsOutsideTheCore
  - ScopeTest::namingAnInstallationToReadDoesNotMoveWhereTheWorkIs
  - ScopeTest::namingTheCoreInOrderToRuleItOutIsNotEvidenceOfCoreWork
  - ScopeTest::theDeclaredExtensionKeyPlacesAPath
  - ScopeTest::theNamedInstallationIsTheEvidenceWhereNothingElseIs
---

# R-SCO-001 — Outside the core is recognised from structure, not wording

**The server recognises work outside the core from structural evidence rather
than from wording.**

That evidence is the kind of repository the session stands in, the shape of the
paths, and a path known as somebody's extension. The installation knows it, or
the root manifest, which declares it before anything sits installed under it.

Evidence of core work wins over the weaker signals, in this order. First a
`typo3/sysext/` path or the contribution workflow named outright. Then an
extension or project marker, then the package the installation knows the path
as, then the path shape. Then the contribution workflow named in prose, and last
what the repository the session sits in is. A `typo3/sysext/` path is the only
marker that ends the question outright. Prose that names the core in order to
rule it out reads to a substring search exactly like a claim of it.

That last signal is the repository the session **stands in**, not the
installation `TYPO3_DEV_COMPANION_ROOT` names. The variable says which registry
the icons and labels come from and moves nothing else. Only where the walk-up
reaches no installation is it the sole evidence there is, and then it answers.

The server reads what a path carries before anything said about the call, which
keeps two paths of one call apart
([`R-AUD-002`](../audience/aud-002-the-audience-is-a-property-of-the-task.md)).
A path shape is evidence only where it can be, and that holds in both
directions. `Build/Scripts/` and `Build/Sources/` are the core's own, and a bare
`Build/` is any repository that compiles something. Neither is core evidence
where the manifest at the root already says this checkout is not the core.
`Classes/`, `Configuration/` and `Resources/` are the mirror. They are the shape
of a package, and no evidence of one inside a core checkout. There they are the
shape of a path relative to a system extension directory.

## From

`outsideCore` that flipped only after the caller spelled out "not TYPO3 core" in
prose (2026-07-29).
