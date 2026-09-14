---
id: R-SKL-025
title: "An audit's list says what the repository already carries unmerged"
status: held
restsOn: [D-SKL-068]
heldBy:
  - SkillTest::anAuditsListSaysWhatTheRepositoryAlreadyCarriesUnmerged
---

# R-SKL-025 — An audit's list says what the repository already carries unmerged

**Every item on the list an audit shows carries what the repository already has
in flight against it. The audit does not show the list before it has established
that.**

A finding and the work in flight against it are two different readings, and the
checkout answers only the first. An item already fixed on a branch nobody merged
reads on the list exactly like one nothing has touched. The maintainer is the
only party who can tell them apart, which is what the agreement step exists to
make unnecessary.

The surface is wider than the open pull requests, and to state it is part of the
demand. A branch pushed without a pull request is where a maintainer's own
unfinished work sits, and it is the one an audit misses. A maintained release
line is the third. A step that names only the first sends a session to a
complete-looking answer about a third of the surface.

An item found already in flight is not thereby settled. What an unmerged branch
holds is a claim about a finding. The audit's own bar for a dropped candidate is
what it has to clear.

## From

The feedback of 2026-08-19 09:43. A v14 release audit of a blog extension mapped
23 open pull requests against its 17 items. It told the maintainer that nothing
had touched item 2. Thirteen branches had gone up without a pull request, and
one of them carried item 2 already fixed. It carried the same diagnosis the
audit had reached and the test the audit had found absent. It carried two
further v14 defects the audit had not found at all.

## Held by

The step is step 6 of `typo3-extension-health`, between the write of the list
and its display. The assertion reads it for its position, for the three
surfaces, for the one answer per item, and for the method under it. That method
includes the sentence that says the empty diff is the only reading the command
settles.
