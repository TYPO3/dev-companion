---
id: R-COD-002
title: 'What the server ships is held to the prose rule'
status: held
restsOn: [D-DOC-070]
heldBy:
  - ProseTest::theProseAClientIsHandedIsMeasured
---

# R-COD-002 — What the server ships is held to the prose rule

**What the server ships to a client follows the same prose rule as what this
repository writes about itself.**

The tool descriptions, the schema field texts and the `instructions` sent at
initialize are prose this repository answers for. A caller pays for all of it
before it has asked anything. One point per sentence, the rule before the
reason, no sentence that restates the one above it. That is the measure
[AGENTS.md](../../AGENTS.md) states, applied where the reader is a machine that
cannot skim.

## From

The session of 2026-08-03 that went to look for prose to cut. What a client
receives at connect measured 118202 characters. Of those, 14502 were the 26 tool
descriptions and 11507 the input schema fields. A cut of only what restated
something already in the payload took 1219 of them off. A read found the
restatement, not a report. `bin/cli prose:check` read `AGENTS.md`, `readme.md`
and the markdown below seven directories, and reached no file in `src/`.

**Measured since 2026-08-07.** `bin/cli prose:check` reads the payload beside
the corpus: the `instructions`, every tool description, and every `description`
in an input or output schema, nested fields included. It reports the weight and
the sentences over the measure. It reports rather than fails, which is what the
corpus half already does. A description over the measure can be the right one. A
rewrite driven by a counter produces two short sentences that say what one said.
The count is prose alone and is not the whole connect payload.

## Held by

- `ProseTest::theProseAClientIsHandedIsMeasured`, which holds that the read
  reaches the instructions and every tool the registry offers, rather than that
  nothing runs long.
