---
id: D-ANS-083
title: 'The unsupported answer is the whole diagnostic'
date: 2026-08-17
status: open
coveredBy:
  - SkillTest::everyDischargedCallIsWrittenAsOneAndRoutedNowhere
---

# D-ANS-083 — The unsupported answer is the whole diagnostic

**A caller that hears its question has no answer here does not go on to
`typo3_server_scope`. The answer beside that sentence already carries what the
orientation would add.**

`typo3_server_scope` is the tool for a caller who does not know whether this
server can answer a question at all. It answers that at the size of the whole
catalogue. Two statements prescribe it to a caller who just got the one field it
was after.

## Evidence

- `feedback/2026-08-17-205904` is a session that made the call and measured it.
  It was the largest single tool result of a build that cost $29.59, roughly
  11,000 tokens. That is about $0.88 amortised over the 148 requests it stayed
  in context for, near 3% of the session. It changed no decision the session
  took.
- Re-run on 2026-08-17 from an empty directory, one process over stdio.
  `typo3_server_scope` is 77,413 characters on the wire: 36,291 of text and
  40,366 of `structuredContent`. The feedback's 39,926 is one of those halves.
  `typo3_project_describe` in the same session is 566 characters whole.
- **The `installation` block is 389 of those 40,366 characters**, under 1% of
  the answer the step prescribes. `covers` alone is 19,265, `doesNotCover` 7,532
  and `routing` 5,896.
- The 279 characters of the describe answer's `unsupported` object carry
  `cause: no-installation`, the reason, the same three `searched` directories,
  `misconfiguration` and both settings variables. What the scope block adds is
  `found: false`, four nulls and `packageCount: 0`. And a console reported out
  of reach because there is no installation to run it in.
- `Result\Unsupported` already records why the data moved. `searched` and
  `misconfiguration` are in the payload because "a caller just told that nothing
  could be asked is the one least able to guess that a second tool holds the way
  out". The fields moved and the sentence that names the second tool stayed.
- `D-ANS-005` put the distinction the skill step exists for into that same
  answer. `cause` is `no-installation`, `misconfigured` or
  `installation-not-answering` because `META-02` requires "nothing found" and
  "found but not running" to be distinguishable. A client cannot lexically match
  its way to that.
- `D-ANS-061` decided from the other side that `typo3_server_scope` named in an
  answer is not the lever, on three sessions that never called it.
  `GerritTest::aNamedChangeIsHandedTheWorkflowsThatOwnIt` holds the name out of
  that answer. This is the first session on record that did call it, and the
  cost is what it reports.
- `D-SKL-034` took a duplicate call on the second **Assumed** that it is cheap.
  That is "measured as a count and not against what a session gives up for it".
  This is that measurement, for a different step.
- What the repository budgets today is the initialize instructions, at 2,048
  characters — `R-ANS-013`. Nothing holds a tool answer to anything.
- The corpus: 20 open feedback, all from one build in
  `/home/benji/projects/site-demo`, and three of them measure payload rather
  than calls. `feedback/2026-08-17-212300` reports `availableHints` at 78% of
  everything `typo3_hint_lookup` transferred over 21 calls;
  `feedback/2026-08-17-211826` is the session's summary profile.

## Decided

- **The sentence comes out of `Result\Unsupported`.** For `no-installation` and
  `misconfigured` the orientation answer adds nothing the caller does not have.
  For `installation-not-answering` it adds the root, the kind and the package
  count of an installation the caller stands in.
- **Any `typo3_project_describe` answer discharges the `typo3_server_scope` step
  in `typo3-development-installation`**, the unsupported one included. The step
  exists for whether an installation and a console are in reach at all.
  `D-ANS-005` put that in `cause` on the call the base makes first. This is not
  a skip condition widened by appetite: the answer the step asks for arrived
  somewhere else and the skill was never read again.
- **Rejected: a smaller `typo3_server_scope` with the sentence kept.** A pointer
  to something the caller already holds costs a round trip whatever the answer
  weighs. The round trip is what `D-FBK-027` measures a tool by.
- **Not decided here: what an always-attached block costs across the server.**
  That is the series' question rather than this feedback's. `D-FBK-021` is why
  the judgement goes with the series, and `feedback/2026-08-17-211826` carries
  it.

## Assumed

- The three causes are the whole of the unanswerable cases.
  `ToolContractTest::onlyOneClassBuildsTheUnsupportedAnswer` holds that from the
  other side.
- A caller reads the fields under `unsupported` rather than the sentence beside
  them. `D-ANS-005` assumes it and a client met the shape on 2026-08-04.
- What the call cost is the size of the answer rather than the call. It comes
  from one client's transcript, and nothing in this repository reproduces the
  amortisation.

## Wrong if

- A session reports it could not tell a server that started in the wrong
  directory from an installation that merely does not run. That is after the
  sentence came out. Then `cause` did not carry the distinction and the pointer
  did.
- A session with the discharged step creates an installation beside one that
  already exists, or boots nothing where a repository declares one. Then the
  step did something the describe answer does not.
- `typo3_server_scope` gains a form that answers the binary question cheaply.
  Then the second **Decided** is about a call that costs nothing, and the step
  goes back unconditional.

## Since then

The second session on record to call this tool did so after its work rather than
before it. It reports the answer as accurate and names no decision it changed.
The exclusion it credits agreed with a choice already made, and the discovery
matched what the session had read by hand. That is the cost the earlier
measurement took, reported from the other side by a session that liked what it
got.

The third **Wrong if** fired two days later, when the answer gained an argument
that asks for one section of it.
