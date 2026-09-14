---
id: D-KNW-003
title: '`provenance` is not the third spelling of `binding`, and stays'
date: 2026-07-30
status: revoked
revokedBy: D-KNW-005
coveredBy: []
---

# D-KNW-003 — `provenance` is not the third spelling of `binding`, and stays

**The task intents move to `binding` and `provenance` stays as it is, because
its `installation` value is not an obligation at all.**

The plan was one field name for who an answer obliges, because it looked like
three forms of one thing. `binding: "core"` on the hints, `coreOnly: true` on
the task intents, `provenance: "core-only"` in `knowledge/server-scope.json`.
Two of them are the same axis. The third is not, and a read of its values is
what showed it.

## Decided

- The intents move to `binding`, because `coreOnly: true` asks exactly the
  question `binding` asks and answers it in a boolean. That is the shape that
  cannot carry a second audience the day one arrives.
- `provenance` stays as it is. Its values are `core-only`, `transferable` and
  `installation`, and the third is not an obligation at all. It says the answer
  comes from the installation rather than from a snapshot. A fold into `binding`
  would either drop that value or make "installation" something that obliges a
  caller. Two fields that overlap on one value are not one field with a name
  problem.

## Wrong if

- A fourth value arrives on either side that reads naturally in both. Then they
  are the same axis after all and the merge is the entry that was right.

## Confirmed on 2026-08-02

No fourth value has arrived on either side. Read out of the corpus as it stands,
`binding` is one value, `core`. That is on 28 entries across the six
`knowledge/architecture-hints/*.json` files and `knowledge/task-intents.json`,
hint-level and statement-level alike. `provenance` is the recorded three, on the
16 covered topics in `knowledge/server-scope.json`: four `core-only`, four
`installation`, eight `transferable`. The move this entry decided has happened:
the intents carry `binding` and no `coreOnly` boolean remains in either corpus.
Two places that do not know about each other catch the arrival of a fourth
value, and neither holds the pair. That is what this entry turns on. A session
that widens one vocabulary edits one pin and never faces the question whether
the new value reads on the other axis.

## Revoked on 2026-08-02

Hours later the fourth value arrived, and it was the one this entry asked for.
The three audiences of `R-AUD-001` named outright, `project` and `extension` in
place of the single negation `outside-core`, read on both axes at once. So
`binding` and `provenance` were one axis after all and the merge this entry
declined is the entry that was right. What held them apart was `installation`,
and it was never an obligation. It says where an answer comes from, which
`source` on the same topic already said. It is gone as a value, the four topics
that carried it are `any`. All four vocabularies, `binding`, `provenance`,
`audience` and the `outsideCore` boolean, are the `Knowledge\Scope` enum. See
[`D-KNW-005`](knw-005-scope-is-the-one-word-for-which-work-a-statement-is-for.md).
