---
id: D-FBK-042
title: 'The read-only boundary is the installation'
date: 2026-08-04
status: open
coveredBy:
  - ExcludedToolsTest::theFeedbackToolsFollowTheChannelAndNoExclusionReachesThem
---

# D-FBK-042 — The read-only boundary is the installation

**"Write" without a target means into the caller's TYPO3 installation, and
nothing this server does reaches one.**

`typo3_feedback_record` writes into this repository's own checkout. That is the
other side of that line and a development tool for work on this server rather
than part of its use.

## Evidence

- `453e439` (2026-08-04) reported the exemption as a defect.
  `Registry::offered()` appends the feedback tools after the exclusion filter,
  so nobody can exclude `typo3_feedback_record`, "the one tool that writes". It
  read that as a violation of
  [`R-SCO-009`](../../requirements/scope/sco-009-individual-tools-can-be-excluded.md).
  Nothing in that account was about an installation, and the tool it named goes
  nowhere near one. One word carried both meanings and the security conclusion
  came out of the wrong one.
- [`D-AUD-005`](../audience/aud-005-an-exclusion-naming-no-tool-is-reported-and-the-server-starts.md)
  rests on the same word. Its **Wrong if** says an exclusion must never guard "a
  tool that writes". Under the installation meaning that holds today, under the
  checkout meaning it does not, and the entry cannot say which it meant.
- [AGENTS.md](../../AGENTS.md) defines the sixth tool verb as "the tool writes
  something" and the `VERBS` list in `ToolNamingTest` repeats it. Neither says
  where, and where is the whole of the distinction.
- `Channel::isAvailable()` is
  `InstalledVersions::getRootPackage()['name'] === 'typo3/dev-companion'`.
  Installed as a dependency the channel is not on offer at all, so the exemption
  reaches nobody who uses this server. Only somebody who works on it, in a
  checkout they own.
- Measured on 2026-08-04 in this checkout with
  `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS=typo3_feedback_record`. 26 tools on offer,
  the named one among them, and `typo3_server_scope` reports it under
  `excludedTools.names`. The initialize instructions open "typo3_feedback_record
  is left out of your tool list".

## Decided

- The two kinds of write carry separate names wherever the coarse word did both
  jobs. The verb list in [AGENTS.md](../../AGENTS.md) and in `ToolNamingTest`,
  the `Channel` docblock, and `Registry`.
- The feedback channel is a development tool for building this server. That is
  what `isAvailable()` has always enforced and what nothing wrote down.
- The feedback tools stay outside the exclusion filter, and
  [`R-SCO-009`](../../requirements/scope/sco-009-individual-tools-can-be-excluded.md)
  names them as the exception with its reason. What a caller could exclude there
  is the only route by which a session hands back what it found. The caller is
  the person who maintains this repository.
- The exception stays where it is in `offered()`, the feedback tools appended
  past the filter, with a comment that names the requirement. A second filter
  over `ExcludedTools::ALWAYS_OFFERED` had its weight. It would put both
  exceptions in one list and stop `typo3_server_scope` from the claim that a
  tool is gone which is on offer. The cost is a class in `src/Server/` that
  knows two tool names which only exist in one checkout. What the report costs a
  client is the in-band half of
  [`D-AUD-005`](../audience/aud-005-an-exclusion-naming-no-tool-is-reported-and-the-server-starts.md).
  That has a card and reaches every unknown name, not only these two.

- Nothing covers the first **Wrong if**. `Channel::isAvailable()` reads
  `InstalledVersions::getRootPackage()` through no seam, and every test runs in
  the standalone checkout where it answers true.
## Assumed

- Nobody excludes a feedback tool for a reason this does not cover. Only a
  standalone checkout reads the variable, where whoever set it works on this
  repository.
- The channel stays a development tool. A published server that accepted
  feedback from callers who do not own the checkout would write on somebody
  else's behalf. This entry would then be about a different channel.

## Wrong if

- The feedback channel is ever offered outside a standalone checkout. Then a
  tool that writes into a directory the caller does not own is on offer to
  somebody who cannot see it. The exemption becomes a real hole rather than a
  named one.
- Anything below `src/` writes into the installation it read. The posture is the
  boundary, not the tool count, and one such write makes every "read-only"
  sentence in this repository false at once.
- Another reader draws the same conclusion `453e439` did after this entry
  exists. Then the distinction stood in the wrong places, and the tool
  description a client reads is the next one to carry it.
