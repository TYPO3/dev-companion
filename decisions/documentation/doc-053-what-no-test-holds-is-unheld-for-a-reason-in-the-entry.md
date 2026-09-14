---
id: D-DOC-053
title: What no test holds is unheld for a reason in the entry
date: 2026-08-23
status: open
restsOn: [D-DOC-043, D-DOC-048, D-DOC-052]
coveredBy:
  - DecisionsTest::anEntryNamingThisCodeWithNoTestIsReadOut
---

# D-DOC-053 — What no test holds is unheld for a reason in the entry

**One sweep covered the corpus, and what stayed uncovered stayed for a reason
the entry itself carries. So the number the check prints is a state rather than
a backlog.**

An attribute arrives because a test would catch the entry's **Wrong if**, never
because a report would be shorter.

## Evidence

- Swept on 2026-08-23 over 453 decisions. 87 entries pointed at this
  repository's code and nothing held them; 23 remain, 16 of them `open` and 7
  `confirmed`.
- 49 gained an attribute. 43 of those were on a test written for the entry that
  said so in its docblock and nowhere a listing could read. The names had run
  one way until `D-DOC-048`.
- Two were new tests, and both were entries about a boundary rather than about
  an answer. `D-ANS-003`, where an embedding library would arrive as a
  dependency and a database tool as a connection. `D-KNW-058`, where a document
  id is a scope, a topic and a name.
- The 23 left fall in three kinds and none of them is neglect. Seven `confirmed`
  are process, how a session judges a feedback, how evidence comes about, and
  nothing runs over them. Nine record a gap or a defect nobody has closed, so
  there is no behaviour to hold. The rest decide that a domain earns a skill or
  that a fact belongs in a document, whose **Wrong if** only a session can
  settle.
- The four declined ones are the measure of the criterion. A test that asserts a
  skill exists does not hold "this domain earns a skill". A test that asserts no
  skill carries a fetch line watches for something nobody has written.

## Decided

- The count no longer moves on its own, and a session that wants to move it
  reads the entries rather than the number. What it may not do is attach a test
  that comes near the entry. That is `D-DOC-043`'s first **Assumed** turned into
  a habit, and it makes the report say the opposite of what it measures.
- `Decisions::uncovered()` leaves revoked entries out. `D-DOC-052` forbids a
  test that declares one, so a count of them as without a test would report as
  absent what the checks refuse.
- The report says how many are `open` and how many `confirmed`, and names this
  entry. A reader who meets the number needs to know that one sweep covered it,
  and where.

## Assumed

- That the reasons are readable in the entries. Nothing in the 23 says "no test
  can hold this", because a sentence in each is a sentence to keep true. The
  kinds stand named here instead and the entry says the rest.
- That a swept corpus stays swept. Every entry from now on stands under
  `D-DOC-048`, where the test declares the id. So a new entry arrives held or
  arrives aware that it is not.

## Wrong if

- The number climbs while nobody notices, which would mean new entries follow
  the old way. `bin/cli decisions:check` prints it on every run and
  `bin/cli repository:check` closes with it.
- One of the 23 goes stale: the code moves under an entry nothing watched. That
  is the case `D-DOC-043` exists for, and it would show as an entry whose
  statement describes a call that no longer exists.
- A session shortens the report with tests that do not hold what they declare.
  The count would fall and nothing more would hold, which nothing here can see.

## Since then

The sweep that wrote this entry read the **Decided** and stopped there. Eleven
of the 23 had closed further down, each with its test named in a **Since then**
rather than in the section that queued it. So "nine record a gap nobody has
closed" was wrong on its first day, and what was wrong is the sweep. An entry is
what it says now, not what its **Decided** said when it joined the queue. Twelve
remain, and the two kinds named above hold for them. The criterion is what the
entry keeps: a test that would pass with the statement deleted does not catch
the gap when it reopens.
