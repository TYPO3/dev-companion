---
id: D-KNW-007
title: A hint says whose it is in both directions
date: 2026-08-02
status: open
coveredBy:
  - HintsTest::whatOnlyBindsACorePatchSaysSoOutsideTheCore
  - HintsTest::whatOnlyBindsOutsideTheCoreSaysSoInsideIt
  - VersionsTest::whoIsObligedIsWrittenAsDataToo
---

# D-KNW-007 — A hint says whose it is in both directions

**A hint or statement declares `project` or `extension` the same way it declares
`core`, and the answer labels it where the caller is somewhere else.**

The enum has had the cases since `D-KNW-005` and nothing wrote them. So a
statement could say "this obliges a core patch" and could not say "this is what
a repository outside the core has to do". It said nothing, which reads as `any`:
it holds wherever anybody writes TYPO3.

## Evidence

- Four hints are audience-specific and declared nothing: `core-tests` and
  `project-extension-tests` in `php.json`, `project-repository-layout` and
  `extension-repository-layout` in `general.json`. Their titles say whose they
  are and no field did. So `R-AUD-005`, an answer says who it obliges, held for
  the core half of the corpus only.
- At least seven more read the same way. `extension-documentation`,
  `extension-asset-build`, `extension-static-analysis`, `sitepackage-layout`,
  `sitepackage-initial-content`, `installation-upgrade`, `site-sets`. Each is a
  judgement about a statement rather than a mechanical edit, which is why the
  queue carries them rather than this commit.

## Decided

- The pairs stay two hints, one subject each. A merge into one hint whose
  statements carry a scope would cost each half its `appliesTo`, its `checks`
  and its title. Those are the three things that make it findable. The corpus
  already draws the line with a split. What the field adds is not the split but
  the declaration.
- The label is symmetric and the rule is one. A declared scope gets a name where
  the paths of the match are somewhere else. Nothing tells `project` and
  `extension` apart, because a session in a project works on the extensions in
  it. A notice between the two would fire on every sitepackage task.
- `Scope::of()` does not read a declared scope. It places a path, and a hint's
  scope is a property of the statement. A corpus that decides what the caller's
  repository is would invert exactly the distinction `any` and `uncertain` keep
  apart.
- It filters nothing. `D-KNW-001` was the case for a withheld answer rather than
  a qualified one. It turned on inverted advice: the backend's design system
  handed to a website theme. A project layout in a core answer is somebody
  else's convention, not the opposite of the right one.

## Assumed

- `Scope::Uncertain` gets no label at all. Where nothing placed the work there
  is nobody to contrast the statement with. A notice on every hint of an
  unplaced call is the noise this rule exists to avoid.

## Wrong if

- The label starts to appear on answers a caller is squarely inside, which would
  mean the group's scope is not what the hint matched for.
- A hint turns out to need both directions at once: a bind for a project and for
  a core patch in different sentences. The statement-level field is where that
  goes, and a hint that needs it per statement in both directions is one hint
  with two jobs.

## Since then

The seven that remained had their read, and four declare an audience. Three are
`extension`, each already with the statement that the core's counterpart is a
different hint whose paths do not transfer. One is `project`, as the repository
around an installation rather than a package in it.

The other three stay undeclared, and the checkouts settled that rather than the
titles. A sitepackage ships **in the core repository** and seeds its own
content, so its layout is an obligation the core has too. `extension` on them
would have told a contributor at work on it that their own subject is somebody
else's. That is the failure this field exists to prevent.
