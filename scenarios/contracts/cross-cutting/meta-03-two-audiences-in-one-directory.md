# META-03 — Two audiences in one directory

**Environment:** `E-SITE` with the extension under `packages/` ·
**Contract:** `held` — `R-SCO-001` and `R-AUD-002` held
**Held by:** `ScopeTest::twoPathsOfDifferentAudienceInOneCallStayApart`,
`ScopeTest::aBriefForPathsOfDifferentAudienceSaysWhichStepsAreForWhich`,
`ScopeTest::aBriefForExtensionPathsAloneKeepsNoCoreStep`,
`ScopeTest::aPathInsideAnExtensionIsRecognisedByItsShape`,
`ScopeTest::aPathKnownAsSomebodysExtensionIsOutsideTheCore`,
`ScopeTest::whereNothingPlacesTheWorkTheAnswerSaysSo`

> I am touching `packages/acme_events/Classes/Domain/Repository/EventRepository.php`
> and `typo3/sysext/core/Classes/Database/Query/QueryBuilder.php` in the same
> session — the second one because I think the bug is actually in the core.
> Tell me what applies to each.

**What the agent needs from this server**

- To keep the two apart: one path is core work with everything that entails, the
  other is extension work with none of it.
- To say which is which from the shape of the paths, without being told in
  prose.

**What has to come out of it**

- The core path gets the core conventions, the core checks and the submission
  route; the extension path gets the conventions that transfer and nothing else.
- Where nobody can decide the audience, the answer says it is uncertain rather
  than picks one silently (`R-AUD-002`).

**How it fails**

- One verdict for the whole session, applied to both paths.
- The distinction only appearing after the user spells out "this is not core"
  (`R-SCO-001`).

All three tools take a `paths` array and answer per path. `typo3_task_guide`
composes one brief from them rather than two. It matches the hints per group and
names them per path. The checklist, the checks and the discovery steps stay one
list that says which paths the core's own steps are not for (`D-SCO-009`).
