---
id: D-EVI-014
title: A paired run measures the answer rather than the tokens
date: 2026-10-30
status: open
coveredBy: []
---

# D-EVI-014 — A paired run measures the answer rather than the tokens

**A run with this server and one without do different work, so what they spend
does not say what a call costs. A pair settles which answer comes out, and
nothing about the price of reaching it.**

Four runs of `SITE-12` on 2026-10-30 spent more with this server than without
it, both times. Read as a price, that says the server costs the caller. Read
against the output, it says the two sessions did not attempt the same thing.

## Evidence

- Two pairs of `SITE-12`, each pair one run with this server and one without, on
  `claude-fable-5`. Totals in tokens:

  | Pair | with       | without    | ratio |
  | ---- | ---------- | ---------- | ----- |
  | 1    | 13,260,709 | 5,925,964  | 2.24  |
  | 2    | 36,887,788 | 23,386,875 | 1.58  |

- Cache read is 53% of the cost-equivalent in all four, output 31%, and fresh
  input under one percent. A session pays its whole context again on each turn,
  so the total follows the turns and the context they carry, which `D-FBK-020`
  already states.
- The tool results of the larger run came to about 80,000 tokens over 138 calls,
  against 36.5 million read back. What a call returns is not what a session pays
  for.
- The second pair did different work. The run with this server declared three
  backend layouts, moved seven pages onto them, and left the theme out of its
  package. The run without it reused the theme's layouts and required the theme.
  A migration is turns, and turns are the cost.
- Round trips moved rather than went. The run with this server made 79
  research-shaped calls against the other's 96, and added 31 calls to this
  server. Counting both, it asked 110 times and the other asked 96.

## Decided

- **A pair of runs reports what each session produced, and no claim about
  tokens.** Nothing here says this server makes a session cheaper, and nothing
  says it makes one dearer.
- **`AGENTS.md` and `D-FBK-020` claim round trips for one question, not the
  price of a session.** A tool earns its place where a question costs four calls
  and a trap. A reader who turns that into a saving over a whole session states
  something this repository has never measured.
- A measurement of the round trips needs one question, a settled answer, and the
  calls each side takes to reach it. The task has to be the same on both sides,
  which a free-form scenario cannot hold.

## Assumed

- That the token counts a client records are proportional across two runs of the
  same client. They are counts rather than charges, and a subscription bills
  neither.
- That the larger run of the second pair is an upper bound. A 25 minute break
  falls inside it, and a resume may re-read.

## Wrong if

- A pair whose two outputs meet every criterion of the case still spends more on
  one side. Then the spread is the server rather than the work, and the claim
  above is too careful.
- One question, asked both ways against a settled answer, takes the same calls
  with this server as without. Then the round trip claim in `AGENTS.md` is wrong
  where it is stated, rather than only where a reader extends it.
