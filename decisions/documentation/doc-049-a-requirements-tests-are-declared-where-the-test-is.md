---
id: D-DOC-049
title: A requirement's tests are declared where the test is
date: 2026-08-23
status: open
restsOn: [D-DOC-004, D-DOC-048]
coveredBy:
  - RequirementsTest::everyRequirementNamesWhatHoldsIt
  - RequirementsTest::everyRequirementSaysWhatTheTestsHoldingItDeclare
---

# D-DOC-049 — A requirement's tests are declared where the test is

**A test carries `#[Requirement('R-XXX-000')]`, `bin/cli requirements:cover`
writes each entry's `heldBy`, and `## Held by` keeps what is not a test.**

One mechanism for both corpora: what `D-DOC-048` settled for a decision, in the
place a requirement said the same thing in another shape.

## Evidence

- 222 entries named 492 tests in a `## Held by` section on 2026-08-23. The same
  regex that read `coveredBy` before it became front matter read them out of the
  prose.
- 174 of those sections were a bare list of names and nothing else. 48 carry
  something a list cannot. A `bin/cli` command that derives the claim again, or
  a clause that says which half a test holds. Or a sentence that says the other
  half is not guarded.
- 17 names are a whole test class rather than a method, which the decision side
  had none of. The attribute takes a class for that reason, and it means what it
  meant in the section: every method in it.
- The section stayed in `D-DOC-045` because a move of the names alone would
  leave the clause with nothing to point at. The attribute is what makes that
  cheap. The name goes to the front matter and the clause keeps the name it
  qualifies as a reference.

## Decided

- `#[Requirement]` in `tests/Support/`, on a method or on the whole class, and
  `bin/cli requirements:cover` writes `heldBy` from it. `not guarded` stays what
  it was: an entry whose generated list is empty.
- `## Held by` stays and holds only what is not a test. A bullet that is nothing
  but a backticked test name fails `bin/cli requirements:check`, because that
  name is the front matter's. A bullet whose next line qualifies it stays.
- The two corpora share the writer, `Entry::withNames()`, and the lookup,
  `Sources::held()`. A second copy of either is what the checks exist to find.

## Assumed

- That a test which declares an entry holds that entry's claim, which is
  `D-DOC-048`'s assumption and is not measurable here either.
- That nothing outside this repository reads the section. `bin/cli`, the tests
  and people read it.

## Wrong if

- A requirement needs a name and a clause on one bullet often enough that the
  split reads as an obstacle. 48 of 222 carry one today; a number past the bare
  majority would mean the section was the right home after all.
- A test holds a requirement and a decision and the two attributes over it read
  as noise. Then the want is one attribute that names both corpora, and this is
  the entry that says why there are two.
- Somebody edits the generated `heldBy` by hand and the edit is lost. It fails
  the check, so what would show it is a commit that never ran one.
