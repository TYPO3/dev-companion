---
id: R-KNW-059
title: 'A change is told which cache group holds its old output'
status: held
restsOn: [D-KNW-027]
heldBy:
  - HintsTest::aChangeIsToldWhichCacheGroupHoldsItsOldOutput
  - HintsTest::clearingACacheAndDeclaringOneAreDifferentQuestions
---

# R-KNW-059 — A change is told which cache group holds its old output

**A change learns which cache group holds its old output, the command that
flushes it, and that a compiled Fluid template is not among them.**

The change is a template, a TypoScript file or a TCA file, and the three do not
answer the same.

Which command clears a cache is the smaller half. The half that costs a session
is that a Fluid template is the one thing in the list that clears itself. The
identifier a compiled template sits under is a hash of the file path and the
file's modification time. So an edited template goes under a new one, and
nothing reads the stale entry again. A caller who reaches for the cache after an
edit that does not appear looks at the one cache that was already correct. The
thing that still answers with the old page is the page cache, in another group,
in the database.

So the statement carries both, and either half alone leaves it wrong.
`typo3 cache:flush` without the mechanism sends the caller to delete a directory
that holds nothing they need deleted. The `pages` group is
`Typo3DatabaseBackend` for `pages`, `hash` and `rootline`, which no file
deletion reaches. The mechanism without the groups leaves them with a template
that recompiled and a page that did not change.

The reach is the second demand. The question arrives from a template, from
TypoScript and from TCA, which are three different domains. It has to stay apart
from the question of how a cache is *declared*. That one is `caching` in
`php.json` and is a different subject.

## From

`feedback/2026-08-01-003937`, a TYPO3 14 session that cleared caches with `rm`
on `var/cache/code/fluid_template`. Its user told it that it "did not clear the
caches properly after changes". Its own query reached nothing, and the nearest
hint the corpus had was about the declaration of a cache (2026-08-01).
