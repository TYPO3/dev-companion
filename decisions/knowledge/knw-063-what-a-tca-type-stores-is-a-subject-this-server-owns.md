---
id: D-KNW-063
title: 'What a TCA type stores is a subject this server owns'
date: 2026-08-07
status: open
---

# D-KNW-063 — What a TCA type stores is a subject this server owns

**Nothing here says what column a TCA type produces, whether it is nullable, or
what its empty value is.** One session handed a user a wrong verdict for want of
it.

## Evidence

- `feedback/2026-08-07-065228`. The verification of Forge 109572 turned on one
  fact. A TCA `type=datetime` column that is not nullable never holds SQL NULL,
  and the factory maps its empty value back to `null` on read. So a mapped
  object reports `null` while the row does not. The session established it in
  core source across many turns, and names four places: `DateTimeFactory`,
  `DateTimeFieldType`, `QueryHelper` and `Backend::insertObject()`.
- The feedback states what it cost, and it is the most expensive outcome in this
  corpus. The session first reported the behaviour as correct and filed the
  issue as not reproducible as written. It told the user the reporter was wrong
  about everything but the symptom. Only after the user pushed back did it find
  the inconsistency. The defect was real and the session had dismissed it.
- `feedback/2026-08-07-065342` is the same gap from the manual side.
  `typo3_documentation_lookup` at 13.4, three queries, six results, none used.
  Coverage 0.274 to 0.731, with the top match on the word "datetime" in a page
  title. The undocumented surface it names is whether `equals($property, null)`
  is API the core supports and what it does against a column that cannot hold
  NULL. That is what decides whether a report is a defect or a misuse.
- `feedback/2026-08-07-065329` is the same question a third time, as a tool gap.
  The live question was DDL: what column does `type=datetime` produce with and
  without `dbType`, is it nullable, what default does it carry. The session
  answered it with a six-variant fixture table it built and measured on sqlite,
  mariadb and postgres.
- `bin/cli hints:probe "TCA datetime nullable null empty value stored in the database"`
  reaches `tca-formengine` alone, on `appliesTo(6) + text(66)`. No hint in the
  corpus carries `nullable`, `DateTimeFactory` or the storage side of a TCA
  type. That is the 1a probe and it comes back empty.

## Decided

- This is a gap and this entry takes it on. Three reports out of two sessions,
  one of them with a wrong answer that reached a user, and the probe confirms
  nothing here answers it.
- The subject is the storage side of a TCA type rather than `type=datetime`
  alone. That is the column, its nullability, its empty value and what reads
  that value back. Datetime is where it was hit; the shape of the question is
  the same for every type whose empty value is not NULL.
- What it says about TYPO3 is not decided here and may not be. This run has read
  this repository and no core checkout. `bin/cli checkouts:status` reports all
  four absent in this checkout. A copy of the feedback's own account into
  `knowledge/` would put a guess where research belongs, which is the one
  failure nothing downstream can detect.
- `typo3_schema_lookup` is a separate finding and not the answer to this one. It
  returns `notnull` and `default` per column and needs a booted installation
  with the table in it. That is a different question from what a type produces.
  Its description names neither of those two fields, and that has a card of its
  own.

## Assumed

- The four places the feedback names are the right ones. They are a session's
  account of a checkout this run cannot open, so they are where the research
  starts rather than what it concludes.
- The manual has no page here. That is one session's search at 13.4 with three
  phrasings, and an absent page is the hardest thing to establish from a search.

## Wrong if

- The research finds the manual does cover it, which would make this a routing
  finding about `typo3_documentation_lookup` rather than a corpus gap.
- The behaviour turns out to differ across 12.4, 13.4, 14.3 and main in ways one
  statement cannot carry. That would make it a document rather than a hint.
- The core fixes Forge 109572 in a way that changes the storage rule, which
  would date the statement before anybody writes it.

## Since then

A session did the research against all four checkouts. All four places the
feedback named hold, and two things it did not know are part of the statement.

The first is a version boundary. The two classes arrived in one major and the
nullability default a major later still. So what the session reported flat on
the development line is `true` there and `false` on the LTS before it. That is
exactly what a statement bound to one branch looks like. The second is that the
write side is the same rule read backwards. So the round trip is null to zero to
null and the row is not NULL at any point.

The tool settled the second half of the card rather than the research. The
manual reaches nothing about those constraints, so that feedback is a corpus gap
and not a routing failure.
