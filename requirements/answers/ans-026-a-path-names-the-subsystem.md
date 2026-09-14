---
id: R-ANS-026
title: 'A path names the subsystem'
status: held
restsOn: [D-ANS-060]
heldBy:
  - HintsTest::anExtbasePersistencePathIsNotAnsweredWithAnotherSubsystem
  - HintsTest::pruningThePathPatternsLeftBothSubjectsReachable
---

# R-ANS-026 — A path names the subsystem

**A hint lookup with a path under `typo3/sysext/extbase/Classes/Persistence/`
answers with that subsystem and not with the conventions of another.**

A caller that names a path has said which subsystem the question is about, in
the least ambiguous way the interface offers. An answer that hands back another
subsystem's conventions is worse than an empty one. The call exists to give the
conventions before the session forms a view of the code. A plausible set from
somewhere else reads as that step done.

## From

Two sessions in one core checkout on 2026-08-07. `feedback/2026-08-07-132426`
got `fal-storages-drivers` from `typo3_hint_lookup` with three Extbase
persistence paths. `persistence-reading` and `extbase-domain-mapping` sat in
`availableHints` and did not come back. `feedback/2026-08-07-065259` got the
same from `typo3_task_guide` with the same paths, hours earlier and in another
task. The cause is the bare `storage` in the FAL hint's `appliesTo`. It matched
the `Storage/` segment of the path as a prefix, and it ranked above the hints
that answer because `keywords` sorts before `score`.

The positive half stayed out on purpose until 2026-08-07, because there was
nothing to name. A read of both candidates showed why. `persistence-reading` is
the core `QueryBuilder`, `PageRepository` and the restrictions, and
`extbase-domain-mapping` is the model and the table behind it. Neither covers
the query parser, the column map or `Backend`. A session wrote
`extbase-persistence-internals` against the checkouts, and it is what answers
now.
