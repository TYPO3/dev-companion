---
id: D-DOC-040
title: 'A renamed tool is corrected where the name is a claim'
date: 2026-08-22
status: open
coveredBy:
  - ToolNamingTest::everyToolADecisionOffersInBackticksIsRegistered
  - ToolNamingTest::everyToolNameWrittenInTheKnowledgeBaseIsRegistered
---

# D-DOC-040 — A renamed tool is corrected where the name is a claim

**A renamed tool stands under its current name wherever a reader would go and
call it. It keeps the old one where the sentence is about that name.**

`ToolNamingTest` held the knowledge base and the skills to the registry and
nothing else, so every other corpus went stale unwatched.

## Evidence

- Measured on 2026-08-22 against the 28 registered tools. `decisions/` named
  typo3_project_scope 70 times, typo3_extension_scope 47 and
  typo3_architecture_lookup 40, across 56 files. `knowledge/`, `skills/`,
  `requirements/` and `todo/` were clean, and `documentation/` named one
  hypothetical tool in an illustration.
- The three are renames on record: typo3_project_scope and typo3_extension_scope
  became `typo3_project_describe` and `typo3_extension_describe` at `a4470ee`
  (`D-SCO-011`), and typo3_architecture_lookup became `typo3_hint_lookup` at
  `7553cb3`.
- Seven further names in `decisions/` belong to no tool and never did.
  typo3_document_list, typo3_skeleton_lookup, typo3_migration_availability,
  typo3_debrief_guide and typo3_convention_lookup are proposals the maintainer
  declined. `typo3_versions` and `typo3_logo` are a TER field and a file name. A
  decision that records a rejection names the thing it rejected.
- `scenarios/runs/` had already solved it the other way. Each of the three
  recorded runs keeps the form its session used and closes with a line that
  names the current one. "`typo3_hint_lookup` was called
  typo3_architecture_lookup when this run was recorded".

## Decided

- The 157 mentions in `decisions/` carry the correction. Four passages keep the
  old form because the sentence is about it. `D-SCO-011`, which is the rename;
  `D-AUD-005`, whose finding is an exclusion variable set to a name no tool has.
  And two lines of `D-FBK-018`, one an exact quotation of a shipped file at a
  named commit and one the rename itself. `D-AUD-003` names both, because the
  claim is what a commit put into the `instructions`.
- `ToolNamingTest` widens to `documentation/`, `requirements/`, `todo/` and
  `scenarios/` outside `runs/` — the corpora where a name is a claim about
  today. All four were clean, so the guard holds a boundary rather than reports
  a breach.
- `feedback/` and `scenarios/runs/` stay out. Both are a session's own account
  of a date, and a rewrite of one edits the evidence. The run's last line is the
  form that reconciles them, and it costs one sentence per file.
- ~~`decisions/` stays out of the guard too, and this is the trade. The corpus
  legitimately names tools somebody proposed and rejected. So nothing can tell
  one of those from a name that went stale without a list of every rename ever
  made. Three renames did not earn that list.~~ Reversed the same day, see
  **Since then**.
- The illustration in `interface-contract.rst` names `typo3_rule_lookup` rather
  than an invented tool. The sentence is about a name with an underscore in it,
  which a real one shows as well and the guard can then read.

## Assumed

- That the three are all the renames. They are what the measurement found, and a
  rename whose old name nobody wrote down anywhere would leave no trace to
  count.
- That a reader of a decision wants the tool they can call. The alternative is
  that they want the name the entry had on its day, which is what
  `scenarios/runs/` assumes about a trace. The difference is that a trace is a
  list of calls and a decision is mostly prose about them.

## Wrong if

- ~~The next rename goes stale in `decisions/` anyway, because nothing guards it
  and this correction was a sweep rather than a habit. That is the known hole.
  What would close it is the list of renames this declined to start.~~ Closed
  the same day; see **Since then**.
- A corrected sentence turns out to have been about the old name after all. A
  read of every line the sweep changed that sat beside a date, a commit or the
  word "renamed" found the four exceptions. A fifth would show up as an entry
  that no longer makes sense.

## Since then

The maintainer settled three things the same day and each took something away. A
tool that does not exist cannot be legitimate. What the corpus holds is a
declined proposal, so the reason to leave it unguarded was never true of
anything in it. A declined proposal does not stay declined, and the name is what
a later session searches for when the demand comes back. So the five as prose
threw away the handle, and that change went back. And the guard may not hold a
list of former names, which carries the corpus's history in the code.

What replaces it is the distinction a reader wants anyway. **A tool name in
backticks is one to call, and a name under discussion stands plain.** The match
is the tool shape, so a `typo3_versions` field does not count as a name, and it
closes both **Wrong if**.
