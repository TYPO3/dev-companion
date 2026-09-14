---
id: D-DOC-008
title: The calls that reach outside stay in the shared table
date: 2026-08-02
status: confirmed
coveredBy:
  - DocumentationTest::aSourceThatDidNotAnswerIsStillAnAnswerToTheSchema
  - ToolContractTest::aToolCallAnswersWithTextAndMatchingData
---

# D-DOC-008 — The calls that reach outside stay in the shared table

**`Upkeep\ToolCalls` gains the two live `typo3_documentation_lookup` calls and
`ToolContractTest` drives them like every other. A host that does not answer is
an answer the schema already declares.**

The recording showed one tool that refused a question and never answered one.
The assumption was that the way out would cost the contract test its
independence from docs.typo3.org. It does not, and that is the whole of what
this settles.

## Evidence

- `typo3_documentation_lookup` was the only tool of the twenty-two in the
  recording whose page never carried an answer. The one call in the table asked
  for TYPO3 999, so the page showed `status: "unavailable"`, an empty `results`
  and `version-not-covered`. Both modes the description documents, a search and
  a canonical URL handed back as `page`, had no illustration.
- The premise that a live call makes CI depend on the host is false in the sense
  that matters. Driven with `https_proxy` pointed at a closed port, the search
  answers `status: unavailable` with `cause: source-not-answering` in 0.1
  seconds, in three lines of text. The data validates against the tool's own
  output schema with zero errors. `ToolContractTest` asserts non-empty text,
  non-empty data and schema validity. A host out of reach changes which path the
  test validates, not whether the assertion holds.
- What it costs when the host does answer: one table of contents per manual at
  14.3, 162 KB, 43 KB and 34 KB. Plus one request per result, and one more for
  the page. The search entry passes `limit: 3` rather than the default 6, so a
  contract run makes six requests in about 0.6 seconds.
- The slow case is a host that accepts the connection and then hangs.
  `CURLOPT_CONNECTTIMEOUT` 3 and `CURLOPT_TIMEOUT` 8 bound that at 32 seconds
  over four fetches, still green. A refused or unresolved host costs nothing at
  all, which is the shape a runner without egress has.
- Nothing recorded why the 999 call was the one that went in. It arrived with
  the tool in `7d29c77`, whose message says only what the feature was. So the
  reason read into it afterwards was inference rather than a decision anybody
  wrote down.

## Decided

- One table, no skip list. `ToolContractTest` validates both new calls alongside
  the other forty-four, so the recording still illustrates only calls something
  validates. That is why `D-DOC-006` put the table in one place.
- This entry took neither option the todo offered, and both fail for the same
  reason. A call the contract test skips by name and a second table only
  `tools:record` reads each buy independence from a host. The measurement says
  that independence is not needed, and each pays for it in the one currency this
  table exists to save.
- Two calls rather than one, because the second's argument is the first's
  answer. `documentation: search` asks two queries at 14.3 and
  `documentation: page` hands the canonical URL of its first result back. That
  is the two-step the tool's description tells a client to make.
- `limit: 3`. Each result costs another request to somebody else's host, and
  three results show the shape of a list as well as six do.
- 14.3 stands in both calls literally, like every other argument in the table.
  It is the newest released covered line and the one `tools:record` answers
  against, and the page URL carries the version in it either way.

## Assumed

- docs.typo3.org tolerates the traffic. A push runs the suite on three PHP
  versions, so it is eighteen requests per push and about 700 KB. They come from
  a user agent that names this server and its release.
- The recorded page stays at that URL. Nothing pins it, and a manual reorganised
  between releases is the ordinary way it would stop.

## Wrong if

- A CI run goes red on one of these calls. That would mean a path exists that
  answers neither a result nor `source-not-answering`. The fix is in the tool or
  its schema rather than in a skip list around it.
- The suite has to run where nothing may leave the network, or the host starts
  to refuse this user agent. Then the calls come out again, and the option to
  take is the skip list rather than the second table. One place that holds every
  call is worth more than one place that validates every call.
- Somebody re-runs the recording while the host is down and the page silently
  goes back to no answer. The head says which day it is of and says nothing
  about whether docs.typo3.org answered, so it would read as a defect in the
  tool.
- 14.3 leaves the covered versions and both calls fall to `version-not-covered`,
  which puts the page back where it started while everything stays green.

## Confirmed on 2026-08-23

The three calls are still in the shared table and the suite is green on them.
`typo3_documentation_lookup` answered `answered` for a live 14.3 search on
2026-08-23. The third call, the version outside the covered ones, is what holds
the other half of the schema and never reaches the network at all.

None of the four **Wrong if** has fired. No CI run has gone red on these.
Nothing has asked for the suite to run with the network shut, which is the one
case the entry reserves the skip list for.

The fourth is the one with a date on it rather than a report: 14.3 is still
covered, beside 12.4, 13.4 and `main`. When it leaves, both live calls fall to
`version-not-covered` and the page goes back to what it was. So what to watch is
`knowledge/versions.json`, and the recording is where it shows.
