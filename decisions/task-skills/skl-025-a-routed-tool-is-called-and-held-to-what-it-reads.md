---
id: D-SKL-025
title: 'A routed tool is called and held to what it reads'
date: 2026-08-08
status: open
coveredBy:
  - SkillTest::everyCallTheBaseFixesAnswersWithWhatItSendsTheSessionToRead
---

# D-SKL-025 — A routed tool is called and held to what it reads

**A test makes each of the four calls `skills/base.md` fixes. It asserts the
keys the base sends the session to read on the answer that comes back.**

Every other rule of the author's contract comes off the file, which
[writing-a-skill.md](../../documentation/contributing/writing-a-skill.rst)
states itself. The wording is present, and a reorganisation can leave it present
while the behaviour goes.

## Evidence

- A skill does not only name a tool, it says what to read out of the answer.
  Read off `skills/base.md` on 2026-08-08. Step 1 has the session read whether a
  declared command is a check, a change or unknown, and the guides it ends with
  as ids. Step 2 the test layers and the source language each XLF declares. Step
  3's condition on step 4 is a sentence in the brief rather than the `hints`
  key, which has content either way.
- `ROUTING_SKILLS` in `SkillTest` records which tools each skill routes through
  and in what order, and nothing calls one of them. So a tool that no longer
  reports one of those keys fails nothing. The skill still names it, the routing
  assertion still passes, and the session goes to a key that is not there.
- Mutated on 2026-08-08 against green assertions, one at a time. `guides`
  dropped from the project answer, the `change` mark renamed to `rewrite`, and
  the sentence a brief that carried every matched hint prints reworded. Each
  turns the new assertion red and leaves every other skill assertion green.
- `WordPress/agent-skills` is where the mechanism comes from: its
  `eval/harness/run.mjs` runs what a skill routes to and checks the shape it
  depends on. The rest of that harness is not worth a copy. It validates front
  matter in 133 lines, and its 41 scenario files have no consumer in the
  repository. Its own `docs/ai-authorship.md` says no formal evaluation system
  exists.

## Decided

- The four the base fixes come first, because every skill inherits them and
  their drift therefore costs the most. The tools in `ROUTING_SKILLS` are not
  covered here.
- The calls form a thread rather than a list. The extension key comes out of
  step 1's answer and the hint id out of step 3's, the way a session reads them.
  What the test holds is the pair, not two shapes that happen to share one
  method.
- The assertion is on the key the base sends the session to, never on the value
  this installation happens to give it. A value pinned here makes every change
  to the fixture a change to `SkillTest` as well.
- The two calls that need an installation go to the one `Upkeep\Fixture` writes.
  A core checkout has no console to answer them and a real site is one
  machine's, so neither could carry an assertion that runs everywhere. That is
  the exception `R-COD-003` already stood on for `FixtureTest`. It names it
  there now rather than leaves it to a second reader to rediscover.
- It is not a second `ToolContractTest`. That holds every answer to the schema
  its own tool declares. A tool's schema can be right while the base sends the
  session to a key no schema requires.

## Assumed

- The base's four are where drift costs most. Nothing measured that against the
  `ROUTING_SKILLS` tools. No recorded session has lost a step to a shape change
  in one of those yet.
- The written installation answers these four the way a real one does. That is
  what `FixtureTest` already rests on, and this adds no new claim to it.

## Wrong if

- A skill's prose goes stale against a tool that kept every key. The shape held
  and the sentence around it is no longer true. This cannot see that, and it
  would say the gap was narrower than the fix.
- The fixture no longer produces one of the branches the assertion reads, a
  brief that stops short, an extension of the project's own. Somebody keeps the
  test green with a looser assertion rather than with a shaped fixture.
- A tool that only `ROUTING_SKILLS` names drifts and costs a session a step,
  which would say the four were the wrong place to stop.
