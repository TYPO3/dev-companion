---
id: D-KNW-081
title: 'What a NEW placeholder may contain is a subject this server owns'
date: 2026-08-17
status: confirmed
---

# D-KNW-081 — What a NEW placeholder may contain is a subject this server owns

**The form a NEW placeholder needs to survive a relation field is inside this
server's boundary and absent from it. The feedback goes to the queue.**

The corpus tells a session that NEW placeholders in a relation field "resolve
once the children have been created in the same run". It says nothing about the
form one may take. A session that gives them readable names loses every child
relation, and the run reports success.

## Evidence

- The query reaches the right hints and none of them constrains the placeholder.
  `bin/cli hints:probe "NEW placeholder underscore inline relation datamap DataHandler"`
  returns `datahandler-basics`, `environment-placeholders`,
  `datahandler-testing`, `datahandler-writing` and `datahandler-relations`. The
  two the feedback names are the last two.
- The constraint is in nothing else here either. Below `knowledge/` and
  `skills/`, "underscore" occurs about a plugin signature, an extension key and
  a Sass partial, and `getUniqueId` occurs nowhere.
- The feedback's claim about TYPO3 holds, and one method carries it, one step
  earlier than the feedback places it. On `.checkouts/14.3`,
  `DataHandler::processRemapStack()` explodes a relation value that contains
  `NEW` on `_`. It pops the last part as the uid and reads the rest as the
  table, then looks up `substNEWwithIDs` under the popped part alone.
  `NEW_card1` therefore resolves to nothing and goes back as `NEW_`, which
  `RelationHandler::readList()` discards because the id is not an integer.
  Neither site logs, which is the silence the feedback reports.
- The statement lands unbound. That block is identical on 12.4, 13.4, 14.3 and
  `main`.
- The constraint belongs to a relation value rather than to the datamap key. The
  pid path strips a `-` at the start and looks up the whole placeholder. So
  `NEW_card1` works as a key and as a `-NEW_card1` position.
- The safe default the feedback names holds. `StringUtility::getUniqueId()` is
  `uniqid($prefix, true)` with the dots removed, so `getUniqueId('NEW')` carries
  no underscore.

## Decided

- Step 1a of the ladder, and queued rather than closed on the spot. What lands
  is a corpus statement about TYPO3, and this run may not write one.
- The statement goes on `datahandler-relations`, beside the sentence that NEW
  placeholders resolve in the same run. That is the sentence a reader checks
  when a relation does not resolve.
- One statement in a hint that exists rather than a hint of its own. The
  DataHandler family is six questions by `D-KNW-030` and this is part of what a
  datamap does to a relation field, which is `D-KNW-018`.
- It names the symptom as well as the rule. The children land, the parent's
  counter stays 0, and nothing logs. So a reader who has the symptom and not the
  rule has no other way in.
- Not step 2, 3 or 4. There is no rule here to move to where the task passes, no
  entry to route the query at, and no wording to correct.

## Assumed

- One statement is enough. `datahandler-writing` defines the placeholder as
  "NEW" plus a unique suffix, and a session naming its keys there is assumed to
  reach the relation hint before it writes a relation field.

## Wrong if

- A session naming its datamap keys never asks about relations, and so meets the
  rule only after it has paid for breaking it. The constraint then belongs where
  a session coins the placeholder as well.
- An underscore turns out to cost something outside a relation value, in a
  command map target, a group field, a FlexForm. So the rule is about the
  placeholder itself rather than about what a relation field gets.
- Something logs the drop somewhere neither method shows. This judgement read
  two code paths, not a run. "Nothing anywhere reports it" is the part of the
  statement least able to survive an error.

## Confirmed on 2026-08-18

A session read two of the three **Wrong if** out and the statement went in. The
underscore costs nothing outside a relation value. The split happens in one
place, and everything else that reads such an id looks the whole string up. Of
the five call sites that push a value array, four consume it and the fifth never
reads the remapped array. So the rule is about what a relation field gets, which
is where the statement puts it.

Nothing logs the drop, and the reading now covers every site rather than two:
the handler carries no logger, nothing compares the list before the drop with
the list after, and what the log does hold is the parent's own update with the
counter at zero. The first **Wrong if** stands, and only a second feedback would
show it.
