---
id: D-ANS-034
title: A source outside this package answers JSON, or it did not answer
date: 2026-08-03
status: revoked
revokedBy: D-ANS-096
---

# D-ANS-034 — A source outside this package answers JSON, or it did not answer

**The lookups that reach a host read JSON and nothing else. So the fields they
hand back are the same on a hit, on a miss and on a failure.**

Two halves of one rule. What comes in is JSON or it is not an answer; what goes
out is the declared schema, so nothing downstream parses anything.

## Evidence

- The tracker's bot protection answers **200 with a 7.5 kB HTML challenge
  page**, measured on 2026-08-03 with a `Mozilla/5.0 …` agent. This server's own
  agent and a plain `curl/…` both got JSON. A reader that accepts whatever
  arrives would have taken that page for an answer. The session that hit it by
  hand spent a round trip to find out (`feedback/2026-08-02-145217`).
- The review server prefixes every response with `)]}'` so a browser cannot
  execute it as a script. Both sources stripped and decoded for themselves, in
  two places, before this was one function.
- What the caller pays is calls, not tokens (`D-FBK-020`). A tool that answers
  in prose for one case and in fields for another costs a second read every
  time. The reader is the caller's model rather than a parser somebody wrote.

## Decided

- `Http\Fetch::decode()` is the one reader. It takes the XSSI guard off where
  there is one, decodes, and returns null for anything that is not an array. A
  page, a portal, a login form and an empty body are one answer to a source: no
  answer to the question. Which of them it was is not worth a branch.
- Nothing scrapes HTML, and no source joins that offers no JSON API. Where the
  answer would have to come out of a page, it is not a lookup this server has.
  The recipe belongs in `knowledge/` and the read stays with the caller.
- The answer's shape is the same in all three states. `status` is `answered`,
  `empty` or `unavailable`. The payload field is present and null where there is
  nothing. `unavailable` carries a `cause` from a closed list and a reason in
  words. A caller reads one field to know which of the three it has, and never
  the text.
- The failure vocabulary is two causes, not one. `source-not-answering` is a
  host that said nothing. `source-not-parseable` is a host that said something
  which was not the API. It is the one to watch, because it arrives with a 200.

## Assumed

- That these hosts will keep JSON on offer. Both are the APIs under their own
  web UIs, so a format change would break more than this.
- That two causes are enough. A rate limit, an outage and a policy change all
  arrive as `source-not-answering`, which is honest and says nothing about which
  happened. Where that turns out to matter, the cause list grows rather than the
  status.

## Wrong if

- A source worth a lookup answers only HTML. Then this rule keeps the answer
  out, and the choice is between a parser nobody can hold true and a recipe in
  `knowledge/`. That is the trade this entry says to make, and the first real
  case is where it meets its test.
- A caller reads the text of one of these answers to decide something the data
  half already says. Then the shape does not carry what it promises, and the fix
  is in the schema rather than in the prose.

## Revoked on 2026-08-23

Read on 2026-08-23, and the first **Wrong if** does not wait for its first case:
one was here before this entry was. The manual source reads a host that
publishes no API, and it has parsed rendered pages since three days earlier. So
"no HTML is scraped" describes the two sources this measured rather than the
lookups that reach a host as a class.

The other half holds everywhere, the manual included: the status vocabulary is
the same on all four sources that reach a host.
