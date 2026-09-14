---
id: D-DOC-002
title: The prose rule is measured, and only the lead fails on it
date: 2026-08-01
status: revoked
revokedBy: D-DOC-070
---

# D-DOC-002 — The prose rule is measured, and only the lead fails on it

**Thirty words is where the check reports a sentence in this repository as two.
The only place it fails is the bold lead of a requirement or a decision.**

Everything else `bin/cli prose:check` prints is a count.

Something holds every other rule in AGENTS.md: `ToolNamingTest` the names,
`bin/cli requirements:check` the file shapes, `ScopeTest` the scope. Whoever
happened to reread the paragraph held "One point per sentence". Prose is the one
thing this repository produces that nothing downstream can tell apart from prose
somebody thought through.

## Evidence

- 47 of 169 leads ran past 30 words, the longest at 96. Every one of them came
  apart into a rule and the enumeration behind it and lost no word. Across the
  whole corpus 805 of 3944 sentences are over, concentrated in six files that
  carry a fifth of them.

## Decided

- 30 words, because that is where the leads ceased to be one point. It is not a
  style ceiling read off a manual, and it is the same number in the check and in
  the rule it holds.
- The body gets a report and never fails. A long sentence can be the right one,
  and a rewrite made to satisfy a counter produces two short sentences that say
  what one said. What the count is for is the file with twenty of them, which is
  a file nobody has reread since its writer left.
- The lead fails, because that sentence has a job the rest of the file does not.
  A reader who stops after it knows what the entry settled, and nobody stops
  after 96 words.
- `feedback/` is not measured. A feedback is a session's report written in
  somebody else's agent, and this rule over it would report on the wrong author.

## Assumed

- ~~That the counts move down. The report is a number nobody has to act on. That
  is the same shape as the three states `bin/cli unresolved:list` names. Those
  sat unread until something printed them.~~ Read on 2026-08-22: the share held
  while the corpus grew sevenfold. So what the report moved is nothing.

## Wrong if

- The corpus total sits where it is for a month while sessions edit the files.
  Then a report is not enough and the measure needs a place in the work rather
  than a line in a check. Or a lead genuinely needs more than 30 words and the
  split makes it worse. Then the number measures the wrong thing, and the
  exception has to say so where the writer takes it.

## Since then

The half that fails works and the half that reports did not move. No lead has
run past the measure since the check arrived. The corpus stands at 21.4% of
sentences over it against 20.4% on this entry's day, three weeks and about seven
times the prose later. What the numbers also show is that the concentration
went: the ten worst files carry 8% of the long sentences where six carried a
fifth. So the count reads as a property of how this repository writes rather
than as a backlog.

## Revoked on 2026-09-14

The maintainer adopted ASD-STE100 for the whole corpus, and STE sets the
measure: 25 words in a descriptive sentence and 20 in a procedure. What this
entry got right stays in `D-DOC-070`: the body gets a report and the lead fails.
The lead still fails at 30 until the sweep of the records lands, because 402 of
the 924 lead sentences run past 25 today.
