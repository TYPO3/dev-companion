---
id: D-DOC-073
title: A page opens on what it covers
date: 2026-11-27
status: open
coveredBy:
  - ProseTest::aSentenceIsFlaggedForWhatTheRulesReplace
  - ProseTest::onlyASentenceMainDoesNotHaveIsReportedAsNew
  - SiteTest::noBoldTextHoldsALiteral
---

# D-DOC-073 — A page opens on what it covers

**A page of `documentation/` says what it covers in its first paragraph. Its
prose uses plain words, lists and headings, as `D-SKL-112` asks of a skill.
Whether a list of steps follows is decided per page.**

The maintainer asked on 2026-11-27 whether the documentation has the problems
the skills had. The pilot rewrote `usage/installing.rst`.

## Evidence

- A count over `documentation/` without `server/tools/` on 2026-11-27: "carries"
  82 times, "carry" 45, "owns" 18, "owes" 12. The skills had 0, 1, 0 and 0 after
  `D-SKL-112`.
- Sentence length was not the problem. The pages averaged 14 to 16 words a
  sentence, so `bin/cli prose:check` reported nothing.
- The problem was the sentence form. A read of `records/working-a-todo.rst`
  found nouns for actions ("the read of it"), pictures for plain verbs ("hands
  over") and a start on what a thing is not.
- `readme.rst` had a passive form in a quarter of its sentences.

## Decided

- A page opens on what it covers. A list of steps follows only where the page is
  a procedure a reader works through in order. The maintainer said on 2026-11-27
  that this is decided per page.
- An instruction comes before its reason. A history, such as what an older build
  wrote, goes into a subsection of its own.
- A list item with more than one sentence has a blank line before the next item.
- The plain verb replaces "carry", "own" and "owe" where the sentence means
  "has", "is responsible for" or "must".
- A page keeps its labels, its links and its facts. A rewrite changes the form
  and nothing a reader or another page relies on.
- `bin/cli prose:check` opens on every sentence a checkout adds against `main`
  that has a passive, an -ing form or a replaced word. The maintainer asked on
  2026-11-27 that new text meet the rules more strongly. It is a report, because
  the two forms are upper bounds a regex cannot settle. AGENTS.md asks a writer
  to clear it before a commit.
- Whether a sentence is clear is a read, not a property a test can find. The
  tests hold the report, not the pages.

## Wrong if

- A reader still asks what a rewritten page means where the old page was clear.
  Then the plain words lost a distinction the old ones made.
