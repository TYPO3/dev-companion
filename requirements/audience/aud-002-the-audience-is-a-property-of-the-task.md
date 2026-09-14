---
id: R-AUD-002
title: 'The audience is a property of the task'
status: held
restsOn: [D-KNW-005]
heldBy:
  - ScopeTest::aBriefForPathsOfDifferentAudienceSaysWhichStepsAreForWhich
  - ScopeTest::twoPathsOfDifferentAudienceInOneCallStayApart
  - ScopeTest::whatTheCoreKeepsInBuildIsOnlyTheCores
  - ScopeTest::whereNothingPlacesTheWorkTheAnswerSaysSo
---

# R-AUD-002 — The audience is a property of the task

**The audience is a property of the task, not of the directory.**

People develop extensions inside a site installation, a site package is an
extension, and a core checkout can be the place someone debugs their site. The
server combines the signals. Where they disagree, the answer says the audience
is uncertain instead of a silent pick.

The unit of that decision is the path, because a call is not one piece of work.
`Scope::of()` answers `core`, `project`, `extension` or `uncertain` for one
path, and the tools that take a `paths` array answer per path. `uncertain` is
the case above: nothing in the call placed the work. An answer that picks the
core there is right half the time and says so never.

## From

Two paths of different audience in one session, where the first one decided for
both
([`D-SCO-007`](../../decisions/scope/sco-007-the-signals-are-combined-per-call.md),
2026-08-01). A later session revoked that entry and the one that built the
per-path answer, `D-SCO-008`. What this rests on now is
[`D-KNW-005`](../../decisions/knowledge/knw-005-scope-is-the-one-word-for-which-work-a-statement-is-for.md),
where the five cases and `uncertain` are the enum the code answers with.
