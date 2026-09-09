---
id: D-SKL-092
title: The client's own routing decides before a description does
date: 2026-09-09
status: open
coveredBy: []
---

# D-SKL-092 — The client's own routing decides before a description does

**A session that routes past its client's own loaded tools is out of reach of
anything this server publishes, so a wording lever is bounded rather than
untried.**

`D-SKL-033` stopped at what was delivered and said the choice was invisible. One
session made it visible and named its cause.

## Evidence

- **The session that saw its own routing.**
  [`feedback/2026-09-09-200500`](../../feedback/archive/2026-09-09-200500-a-model-routed-every-file-edit-through-bash.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5[1m]`. Counted off its own
  transcript: 96 `Bash` calls, no call to the client's `Read`, `Edit` or
  `Write`, no MCP call, no activation. Nine of the `Bash` calls were `python3`
  heredocs doing exact string replacement in PHP files, each reimplementing the
  match-uniqueness check the client's own `Edit` performs, and one of them wrote
  a `use` for a class that does not exist.
- **The cause is a harness instruction and not a judgement about any tool.** The
  session carried an instruction to work through Bash wherever Bash can do the
  job, edits included. `Edit` was loaded, first-party and free to call, and lost
  anyway.
- **The order of the two costs.** A tool of this server's arrives in that client
  as a deferred name, so calling one costs a `ToolSearch` round trip before the
  call. The report states the consequence rather than infers it: the cheapest
  server call cost strictly more than the grep it would have replaced.
- **What the same burst shows on the knowledge side.** Three of its reports name
  a fact as missing that this server already carries and already delivers.
  `typo3_test_run_guide` emits the note that `runTests.sh` stops reading its own
  options at the first word that is not one, carrying the error string a caller
  searches for, since 2026-08-24; the checkout skill's step 3 routes to the page
  saying the change refs sit on a remote a core clone does not fetch from; and
  `bin/cli hints:probe` with the third report's own question answers, top hit,
  the hint saying a change that alters what an installation renders is breaking
  on `main` and takes an `Important` on a maintained line. None arrived. None of
  the three tools was called.
- **The answering side had already been tried twice.** `D-SKL-038` put the two
  patch workflows into the `change` answer and then the order itself, and three
  sessions of this burst read that answer and opened no skill.

## Decided

- **A lever on the publishing side is weighed against this bound before it is
  built.** Rewriting a description, adding a trigger word, moving a sentence:
  all of them address which tool a session picks, and this session picked none.
- **What survives a Bash-first routing is what Bash cannot answer.** A
  convention, a judgement call, a cross-version fact, the state of a review.
  Anything a grep over the checkout answers loses reliably, so it is the weaker
  half of this server to invest in.
- **A session that made no call is not evidence that the answers are poor.** It
  is ambiguous between that and a session in which no call was cheap, and the
  two are identical in a call log. Adoption read off call counts says which
  tools were reached, never which were worth reaching.
- `coveredBy: []` — nothing here runs over a client's routing, and a test that
  asserted the bound would be asserting something outside this repository.

## Assumed

- That the instruction this session carried is one other sessions carry. It is a
  harness setting rather than anything this server or the core repository
  writes, and one report is what says it exists at all.
- That a client which defers schemas charges the round trip the report
  describes. Read off two sessions in the same client and not measured here.

## Wrong if

- A session reports reaching a tool of this server's from inside a Bash-first
  routing. Then the bound is softer than this entry states and the wording
  levers are worth their cost again.
- A client is reported that loads these schemas up front, and a session there
  still routes past them. Then the round trip is not what decides it and this
  entry names the wrong cause.
- The instruction turns out to be scoped to reads and searches in the clients
  that carry it. Then writes were never routed away and this session was
  following something narrower than it read.
