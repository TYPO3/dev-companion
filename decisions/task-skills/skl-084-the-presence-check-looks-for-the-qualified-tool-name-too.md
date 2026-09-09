---
id: D-SKL-084
title: The presence check looks for the qualified tool name too
date: 2026-08-28
status: open
coveredBy:
  - SkillTest::theBaseFixesTheOrderEveryTaskStartsIn
---

# D-SKL-084 — The presence check looks for the qualified tool name too

**A client may expose these tools under a qualified name, so the check for
whether this server is there looks for that name before concluding it is
absent.**

Every place this server names a tool spells the bare name, which is right for a
client that loads them all up front. For one that defers them, a search on the
bare name answers the same as a server that is not connected.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-001303`](../../feedback/archive/2026-08-28-001303-skills-and-server-instructions-name-tools.md).
  `select:typo3_project_describe,…` returned "No matching deferred tools found";
  the same five with `mcp__typo3-dev-companion__` in front returned every
  schema. One round trip, and it guessed the prefix.
- **The shape is this client's own**, and it is readable from the session
  writing this entry: Claude Code lists a deferred tool as
  `mcp__<server>__<tool>`, the server's name being the one the client's own
  configuration gives it.
- **Three places name the bare form**: the `instructions` sent at initialize,
  the skills' reference files, and every answer's `nextTools`.
- **One of them converts the empty result into an absence.** `skills/base.md`
  opens with "No `typo3_` tool in this session, or a first call that errors:
  stop, say this workflow needs the server and it is not there", and forbids
  falling back to general knowledge or reading the checkout. A session that
  searched the bare name has met that condition without the condition being
  true.

## Decided

- **The check is where the sentence goes**, because that is where the empty
  result is turned into an absence, and the report names it as the load-bearing
  half.
- **Against the report's other half**, the initialize instructions, on a
  measurement rather than on judgement: they run to 1948 characters and to 2033
  of the 2048 a client keeps once the stale-skills notice is in them, so what
  fits there is fifteen characters. `D-AUD-011` is what the cap cost the last
  time it was exceeded, and trading a sentence out of the instructions for this
  one is a judgement of its own rather than a line added.
- **Queued rather than made here.** `skills/base.md` is copied into every
  published skill, which is the contract the ladder keeps off the spot.
- **Against qualifying the names themselves** in `nextTools`, in the skills and
  in the tool descriptions. The prefix is the client's and the server's own name
  in it is the caller's configuration, so a spelling written here is wrong for
  somebody.

## Assumed

- That a client which qualifies the name does so predictably enough for a
  session to derive it, which this report did on its first retry.

## Wrong if

- A client is reported whose qualified form is not the server's name in front of
  the tool's, so the shape named here sends a session looking for the wrong
  string.
- A session reports stopping on the presence check with the initialize
  instructions in context. Then the instruction is not the lever and only the
  check's own wording is.

## Since then

The check carries it as of 2026-08-28, in the bullet above the one that stops
the workflow: a client may carry this server's name in each tool's, so an empty
search for the bare name is looked at again under the qualified form before it
is read as an answer about the server.
`SkillTest::theBaseFixesTheOrderEveryTaskStartsIn` holds the sentence.

The sentence works and stops one word short. A second session read it, did not
take the empty search for an absence — which is what it is there for — and paid
the round trip anyway, because the bare name is no good as a search argument
either and the sentence speaks of a search coming back empty rather than of what
to pass. One clause, queued the way this one was.
