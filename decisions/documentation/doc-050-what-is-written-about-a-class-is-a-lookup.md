---
id: D-DOC-050
title: What is written about a class is a lookup
date: 2026-08-23
status: open
restsOn: [D-DOC-044, D-DOC-048]
coveredBy:
  - EntriesTest::aPathAnswersWithTheClassesItDeclares
  - EntriesTest::aTestIsListedWhereItNamesTheClassAndHoldsAnEntry
  - EntriesTest::anEntryIsAnsweredForTheClassItNames
  - EntriesTest::bothCorporaAreOneListKeyedById
---

# D-DOC-050 — What is written about a class is a lookup

**`bin/cli entries:lookup <path>` answers which decisions and requirements name
the code at that path, and which tests hold them.**

The attributes answer from the end that fails, which is after the change. This
is the same bond read before one.

## Evidence

- Read on 2026-08-23. A test holds 242 of 450 decisions and 219 of 222
  requirements, so the run prints a change that breaks one, `D-DOC-044`.
- The other end is not reachable at all. The attributes sit on tests, so a
  session that opens `src/Knowledge/Hints.php` sees nothing written about it
  until something goes red. 85 decisions name a class of ours and no test holds
  them, where nothing ever goes red.
- Both halves of the lookup already existed. `Sources` maps a class to the file
  that declares it, and `RecordsTest` holds every backticked `Class::member` in
  the corpora to a class that has it. The gap was the command.

## Decided

- One subject for both corpora, `Upkeep\Entries`, because "what do the records
  say about this" is one question. Which corpus an entry sits in is what the
  answer carries rather than what the caller asks.
- A backticked class name is the reach. It is what `RecordsTest` already holds,
  so a name in the answer resolves or the suite goes red.
- A test class stands in the list with the entries it holds where a reader can
  take them in. It stands with their count where a hundred of them would be the
  whole answer. What a long line says is how much rides on the class; the
  entries themselves are what the failure prints.
- Named in `AGENTS.md` and in the reads a todo starts with, since a command
  nobody runs before the change answers nothing.

## Assumed

- That an entry about a behaviour names the class it belongs to. It is
  `D-DOC-043`'s second **Assumed**, and this command is where it now costs
  something: a quiet answer reads as "no decision exists".
- That a test which names a class runs over it. A test that names it in a
  docblock and asserts nothing about it stands in the list and holds nothing.

## Wrong if

- The answer is quiet for a class an entry does govern, and somebody changes the
  behaviour on the strength of the quiet. That is the first **Assumed** as it
  fails, and what would show it is an entry gone stale under a session that ran
  this.
- Readers take the listing as what will break. It says what names the class,
  which is neither what runs over it nor what a change would falsify.
- Nobody runs it. Then the need is a check that fails rather than a command that
  answers. This entry is the record of the cheaper thing tried first.
