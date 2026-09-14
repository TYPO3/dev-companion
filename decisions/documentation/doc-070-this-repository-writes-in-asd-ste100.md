---
id: D-DOC-070
title: This repository writes in ASD-STE100
date: 2026-09-14
status: open
coveredBy: []
---

# D-DOC-070 — This repository writes in ASD-STE100

**Every sentence this repository writes about itself follows the ASD-STE100
rules. The sweep that brings the corpus to them runs one directory at a time.**

The maintainer decided this on 2026-09-14, with the numbers below in front of
them. `D-DOC-068` set the method for a rule set from outside: read it one rule
at a time, against the corpus, with the counts. This entry records that
judgement and what the maintainer decided on it.

## Evidence

- **What STE is.** Issue 8 of 2021: about 65 rules in nine sections. And a
  dictionary of about 930 approved general words with one meaning and one part
  of speech each. Its readers are people who read maintenance procedures in a
  language that is not their first. The specification is free after
  registration, and its licence does not permit redistribution. The dictionary
  cannot ship here, and no check can hold it.
- **The corpus, measured on 2026-09-14** over the files `Prose::documents()`
  names: 32,468 sentences. The -ing and passive columns are a regex, so each is
  an upper bound.

  | Corpus                   | Sentences | Over 20 | Over 25 | Over 30 | -ing form | Passive |
  | ------------------------ | --------- | ------- | ------- | ------- | --------- | ------- |
  | AGENTS.md                | 288       | 48%     | 34%     | 23%     | 30%       | 28%     |
  | knowledge/documents      | 1141      | 42%     | 27%     | 13%     | 24%       | 23%     |
  | skills                   | 1806      | 49%     | 36%     | 25%     | 40%       | 24%     |
  | decisions + requirements | 26201     | 47%     | 31%     | 19%     | 34%       | 26%     |
  | documentation            | 2098      | 49%     | 36%     | 23%     | 30%       | 29%     |

- **402 of the 924 lead sentences run past 25 words.** A check that fails at 25
  today fails CI on every commit until the records sweep lands.
- **Five STE rules were here already, under this repository's names.** One topic
  per sentence, one meaning per word, no figure of speech, the imperative in a
  procedure. And a list or a table over prose (`D-DOC-001`).
- **Three STE rules the Prose list did not state found no defect.** Paragraphs
  over six sentences: 37 of 5153, and each one opened was a code block. Runs of
  four content words: 1584, and none of 25 read was a noun cluster. List items
  with two chained instructions: 3, each "X, then Y".
- **One word carries two meanings and the glossary did not say so.** In
  AGENTS.md and `documentation/`, "hold" means "is true on a version" 81 times
  and "a test guards a rule" 16 times.
- **Ten page names under `documentation/` are gerunds.** STE permits the -ing
  form inside a technical name, and a file name is one.
- **"Terse and condensed" is the motto the proposal came with.** STE itself adds
  words: it requires the article, prefers "you" to the passive, and forbids the
  noun cluster. `D-DOC-068` counted three of six plain versions as longer.

## Decided

- **The Prose list in AGENTS.md states the STE rules this corpus can keep.** A
  sentence has at most 25 words, or 20 in a procedure. A sentence names who
  acts. A verb stands in the present tense and in a finite form, so no -ing form
  outside a name. A paragraph has at most six sentences and opens with its
  topic. A procedure gives one instruction per sentence. A word has one meaning,
  and the glossary is the dictionary.
- **The glossary replaces the dictionary half.** The glossary defines the words
  this repository uses in a sense of its own, and `hold` enters it with its two
  senses. Plain words stand in for the approved list.
- **The active voice overturns `D-DOC-069`'s decline.** The maintainer chose the
  active voice for the records too. A sentence that states what must hold names
  what holds it — the test, the check, the session — as its actor.
- **`D-DOC-002` is revoked.** Its measure of 30 gives way to STE's 25 and 20.
  `bin/cli prose:check` reports the body against the new measures from this
  entry on. It still fails the leads at 30, and the todo for the records sweep
  moves that number to 25 when the sweep lands.
- **The check counts two more things.** Sentences with a passive form and
  sentences with an -ing form, per file, worst first, so a sweep has its
  worklist. Both are upper bounds, and the decision says so where the number
  prints.
- **The sweep runs one directory at a time, and each directory is one todo.**
  AGENTS.md is rewritten in the commit that states the rule, because a file that
  breaks the rule it states teaches the break. The rest follows in the order
  `D-DOC-068` gives. What a caller imitates first: `skills/`,
  `knowledge/documents/`, the tool descriptions and the instructions. Then
  `documentation/`, then `requirements/` and `decisions/`, then `scenarios/` and
  `todo/`.
- **A record is rewritten in form and never in substance.** A decision states
  what a session found on a date. The sweep changes its sentences and leaves its
  numbers, its ids and its dated sections as they are.
- **"Terse and condensed" folds into "Short and precise".** STE gives the reader
  one meaning per sentence; the token rule gives the machine fewer tokens. Where
  the two pull apart, the sentence follows STE and stays short by the other
  rules in the list.
- `coveredBy: []`. What a test can hold is the count, and the count is a report
  until the sweep ends.

## Assumed

- That an agent writes better STE than it writes the old register. Nothing here
  measures that. The patch page `any/writing/the-prose-a-patch-carries` asks a
  caller for the same active voice, so the two ends agree.
- That the regex counts stand for the corpus. A tagger puts the passive and the
  -ing share lower, not higher.
- That the records can change register without a change in meaning. The sweep
  todo for `decisions/` and `requirements/` asks each session to reread the lead
  after the rewrite. That sentence is what a reader stops after.

## Wrong if

- A sweep of the records changes what an entry says. Then the form and the
  substance were not separable there, and the records stay in the old register
  with a line that says so.
- A caller reports a hint or a checklist item as longer or less clear after its
  sweep. Then STE cost the token reader more than it gave the person, and the
  procedure limit of 20 is the number to read again.
- The lead check cannot move to 25 after the records sweep. Then some leads need
  more than 25 words, and the exception has to say so where the writer takes it.

## Since then

The records sweep landed on 2026-09-15 and the lead check moved to 25. Every
requirement and decision now opens within the measure, so the held number of 30
is gone and `Prose::leadsOverTheMeasure()` reads `Prose::MEASURE`. The third
**Wrong if** did not fire: no lead needed more than 25 words. What the sweep
found instead is a lesson about its own method. A first pass that hid sentences
behind a filter for verb forms left long sentences in the bodies. A second pass
over each group took them. Every quoted sentence stays as its source wrote it.
