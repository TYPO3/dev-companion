---
id: D-AUD-019
title: A lookup's description names the subjects it covers
date: 2026-09-17
status: open
coveredBy:
  - ToolContractTest::everyHintSubjectStandsInTheTextAClientSearchesAToolBy
---

# D-AUD-019 — A lookup's description names the subjects it covers

**A client that defers these tools searches a name, a description and the
argument descriptions for the subject a task is about. So a lookup's description
names the subjects it answers for.**

`D-AUD-003` records that the tool descriptions never arrive and puts the entry
point into the `instructions`. The API's own documentation says why they never
arrive, and that the description is a channel after all: the one a search reads.

## Evidence

- **The deferral is structural.** The tool search tool's documentation, read on
  2026-09-17, tells a caller to defer from ten tools or from 10k tokens of
  definitions. `Registry::definitions()` measured the same day: 31 tools, 31 402
  bytes of description and 47 452 of `inputSchema`, about 20k tokens before any
  `outputSchema`. The whole of `tools/list` is 306 174 bytes, and 220 575 of
  them are `outputSchema`. A client that follows the documentation defers every
  tool here, and the eighteen names without a description in `D-AUD-003` are
  that.
- **What the search reads.** The same page: the tool name, the description, the
  argument names and the argument descriptions. It reads neither the
  `instructions` nor `knowledge/server-scope.json` nor an `outputSchema`. The
  `instructions` reach a session that knows the tool's name. The search reaches
  the one that knows the subject.
- **The gap.** A regex over that text per tool, on the patterns a caller writes:
  `typoscript` reached `typo3_documentation_lookup` alone, `routing` and
  `dashboard` reached no tool, `security` reached `typo3_commit_message_guide`
  alone. `typo3_hint_lookup` covers one subject per file below
  `knowledge/hints/`, 38 that day, and its text named two of them. Both are the
  backend UI categories it names in order to withhold them.
- **What already holds.** Every argument of every tool carries a description.
  The `typo3_` prefix is the namespace the page asks for, so one search finds
  the group. The `instructions` are the system-prompt section it asks for.

## Decided

- `typo3_hint_lookup` ends its description with the subjects it covers, read off
  the file names below `knowledge/hints/` when the definition is built.
  `Hints::subjects()` is the read. A list somebody types is the second copy this
  repository never keeps.
- `ToolContractTest::everyHintSubjectStandsInTheTextAClientSearchesAToolBy`
  holds it over every tool's searchable text, so a subject a session adds
  arrives in the description with it.
- The subjects and not the vocabulary. `middleware` stands in eight hint files
  and in no file name, so the description does not carry it. What a word inside
  a hint reaches is the matcher's work once the tool is loaded.
- The `outputSchema` stays as it is. It is 72 percent of the wire, the search
  reads none of it, and whether a client shows it to the model is unmeasured.

## Assumed

- That a client's own search over deferred tools reads the same fields the API's
  does. Claude Code's `ToolSearch` describes itself as a keyword search over the
  deferred list, and `D-SKL-084` shows it reads the name.
- That a session searches at all. `D-SKL-092` bounds this: a session that routes
  past its client's own loaded tools reaches no description, however it reads.

## Wrong if

- A session reports that it searched for a subject in the list and the search
  did not return `typo3_hint_lookup`. Then the field the client reads is not the
  description, and the list stands in the wrong place.
- A client turns out to show the `outputSchema` to the model on load. Then the
  220 KB is the cost of every first call, and the schemas are the lever this
  entry left alone.
