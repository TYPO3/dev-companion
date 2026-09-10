---
id: D-DOC-069
title: AGENTS.md states the three patch rules this corpus breaks
date: 2026-09-10
status: open
coveredBy: []
---

# D-DOC-069 — AGENTS.md states the three patch rules this corpus breaks

**Of the six rules the patch page adds, three are written into AGENTS.md, one
folds into the naming rule, and two are declined for this corpus.**

`D-DOC-068` asked for the six to be weighed one at a time rather than adopted as
a block. This is that reading. It was taken over AGENTS.md, which is the file
the rules would be stated in and the first one a session imitates.

## Evidence

- **AGENTS.md read whole on 2026-09-10.** 201 sentences, of which 11 end on a
  preposition, 20 carry "above", "below" or "here", and 12 hold a mid-sentence
  colon.
- **Every one of the 11 trailing prepositions is an ordinary relative clause.**
  "The names their callers know them by", "the column it is already written at",
  "a queue to write into" — the sentence the reviewer rejected was contorted
  along its whole length rather than at its end.
- **17 of the 20 pointers point at nothing.** "Below `src/`" is a path, and
  "nothing here writes into the TYPO3 installation" means this repository. Three
  name a position in the file instead: the prose rule "below", the rules "below"
  that hold whatever the markup, and the comment rules that apply "as above".
- **The mid-sentence colons are mostly a definition or a list.** One is the
  shape the rule names — "Not a style question and not a matter of taste: a
  defect" — and it opens on a negation as well, which this file already forbids.
- **The corpus behind AGENTS.md has the same shape.** Counted on the same day
  over `documentation/`, `decisions/` and `requirements/`: 18690 sentences, 774
  ending on a preposition, 2091 carrying one of the three pointers. The splitter
  reads what a code block holds too, so each is an upper bound. Fourteen of each
  were read, and none was a sentence one of the six rules would have improved.

## Decided

- **Three rules are written into the Prose list.** A colon introduces a list or
  a definition rather than a punchline, a place is named rather than pointed at,
  and a longer correct sentence beats a short broken one.
- **"Use the words the codebase uses" folds into "One thing, one word".** That
  paragraph already owns the vocabulary, and a tenth bullet would state it a
  second time.
- **Against the trailing-preposition rule.** It would report every relative
  clause in the corpus, and none of the 11 in AGENTS.md is worth a rewrite.
- **Against "name who acts" for the prose here.** The agentless passive is the
  register every requirement and decision is written in, because what must hold
  is the subject and the session that holds it is not. It stays a rule for a
  patch's comments, where the actor is a class the codebase already names.
- **Nothing new is counted.** `bin/cli prose:check` gains no measure, for the
  reason `D-DOC-002` gives about the body and
  [writing a decision](../../documentation/records/writing-a-decision.rst) gives
  about the title shape. A counter that reports hundreds of correct sentences is
  read once and then ignored, and what keeps these three is the reread before
  the commit.
- **The four sentences in AGENTS.md the new rules condemn were rewritten in the
  same commit.** A file that breaks the rule it states teaches the break.
- `coveredBy: []`. What a test could hold is the count, and the count is what
  this entry decided against.

## Assumed

- That the patch page and this file may differ on two rules without confusing a
  session. A session reads the page for the patch it writes and this file for
  the change it makes here, and nothing measures whether it keeps the two apart.
- That AGENTS.md stands for the corpus on these three rules. It was read whole
  while `documentation/`, `decisions/` and `requirements/` were sampled.

## Wrong if

- A session cites the patch page against a sentence here that this file allows.
  Then the split of the six is what confuses, and both ends have to say one
  thing.
- A reviewer rejects a trailing preposition in a patch written out of this
  corpus. Then the declined rule was the one that mattered, and the register
  reaches further than this reading assumed.
- The colon or the pointer shape comes back in the next sweep. Then the reread
  does not keep them, and something has to count them after all.

## Since then

Run against `documentation/` the same day, and the colon rule came back too
broad. Of the 124 sentences it flagged, 113 open on a half that stands on its
own, which is the rule-then-reason shape this file asks for. The rule now names
the colon whose opening is only a setup.

The pointer rule held. Of its 131 raw hits, 91 are the path idiom or a heading
named rather than pointed at, and 23 named a position instead. 16 of those were
rewritten and 7 read as correct. One is stale and nobody here can say what it
meant, where `installing.rst` sends a reader to "the section above" for its
sources and the section above it has none.
