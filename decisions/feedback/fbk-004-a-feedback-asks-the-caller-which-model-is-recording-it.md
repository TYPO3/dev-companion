---
id: D-FBK-004
title: A feedback asks the caller which model is recording it
date: 2026-07-31
status: confirmed
---

# D-FBK-004 — A feedback asks the caller which model is recording it

**`typo3_feedback_record` asks the caller which model records the feedback and
declares the field required. It writes `unknown` rather than refuses the
feedback when it does not arrive.**

A feedback that reports behaviour is evidence about a model. Nothing on this
side of the connection knows which one. The MCP handshake carries `clientInfo`,
the client rather than the model behind it. The SDK keeps it in the session,
which a tool handler does not get. So the tool asks for it, in the one place the
answer is cheap: the call the caller already makes.

## Evidence

- The feedback of 2026-07-31 17:21, the conformance skill's instructions loaded,
  its lookups not run. It came from a checkout whose published skill is
  byte-identical to this repository's, `references/base.md` included. The text
  the session had said, in three places, to ask before a judgement. What the
  feedback could not say is who read it. That is the whole of the difference
  between "the instruction is missing" and "the instruction was present and
  walked past". Only the second of those is work in the skill.

## Decided

- One field, `model`, required in the schema so every client asks for it, never
  enforced in the write. `unknown` is a legitimate answer and the described one
  for a model that does not know its own identifier. An invented name attributes
  a habit to a model that never had it, and there is no way to find that out
  afterwards. The field is always written, so an unattributed feedback is
  visibly unattributed rather than indistinguishable from one recorded before
  the field existed.

## Assumed

- That a model asked for its identifier answers with the one it has rather than
  the one it believes it should be. Nothing verifies this and nothing can — the
  string is whatever arrives.
- That the model is the half worth the field. The client decides whether it
  offers a skill at all, which the previous day's finding about
  `chat.useAgentSkills` is exactly about. The tool does not ask for it here: it
  is knowable with no question, once the SDK hands `client_info` to a handler.

## Wrong if

- The recorded feedback come back mostly `unknown`, or with names no such model
  has. That would mean the attribution is noise and the field should go rather
  than get a read. Or every model asked reports the same behaviour. That makes
  it the instruction's problem after all and the attribution merely a thing
  measured on the way there.

## Confirmed on 2026-08-22

Measured over every recorded feedback, 461 of them. 320 carry a `model` line and
141 do not, and the split is the hour the field arrived. The newest without one
is `2026-07-31-172757` and the oldest with one is `2026-07-31-174524`. The field
has arrived every time since it existed.

The first half of **Wrong if** does not hold. `unknown` is 7 of the 320, all
seven recorded between 18:55 and 19:06 on that first evening and none since.
Nine distinct identifiers are on record: `claude-opus-5[1m]` 199 times,
`claude-opus-5` 55, `opencode/deepseek-v4-flash-free` 31, and six more. Only
`big-pickle`, three times, is not a released identifier.

The second half stays untried, and it is answerable for the first time. It asks
whether every model reports the same behaviour, and until now there was not
enough of more than one to ask.
