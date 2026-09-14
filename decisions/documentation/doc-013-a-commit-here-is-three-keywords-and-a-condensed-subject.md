---
id: D-DOC-013
title: A commit here is three keywords and a condensed subject
date: 2026-08-03
status: confirmed
---

# D-DOC-013 — A commit here is three keywords and a condensed subject

**A commit message in this repository is `[TASK]`, `[BUGFIX]` or `[FEATURE]`, a
subject under 52 characters, and a body wrapped at 72.**

`AGENTS.md` stated the shape of a body and named not one keyword, so a session
learned the set from `git log`. There, four keywords and no keyword at all are
all attested.

The two measures could not both be right. This repository's own
`typo3_commit_message_guide` describes this case in its own words for
`workflow="project"`, while the history runs wider than what that answers.

## Evidence

- Over the 926 commits on `main` on 2026-08-03: 671 `[TASK]`, 85 `[FEATURE]`, 26
  `[BUGFIX]`, 5 `[DOCS]`, no `[!!!]`, and 139 with no keyword.
- 708 of those 926 subjects run past 52 characters, at a median of 61 and a
  maximum of 97.
- 8587 of 15289 body lines run past 72, at a median of 74 and a ninetieth
  percentile of 79. That is the column `bin/cli prose:format` wraps the markdown
  corpus at.
- `Knowledge\CommitMessage` warns above 52 characters of subject, fails above
  72, and wraps a body at 72, over the enum `BUGFIX`, `FEATURE`, `TASK`, `DOCS`,
  `SECURITY`.

## Decided

- Three keywords, not five. `[DOCS]` and `[SECURITY]` belong to the core's
  process: the security team owns one and the changelog the other. This checkout
  runs neither, so a documentation change here is a `[TASK]`.
- The tool's widths, not the history's. The measure this repository states for
  other repositories is the one it can stand to itself. The alternative was a
  second number that exists only in this checkout.
- The 80 that `prose:format` wraps at stays what it is: the markdown corpus's
  column. It reached commit bodies because the same hand wrote both, not because
  anything said it applied.
- The history is not the rule. 708 subjects stay past 52 and nothing goes back
  over them; the rule holds from here.
- Nothing checks it. The `commit-msg` hook the card offered would have been the
  first thing in this checkout to refuse a commit, where `.githooks/pre-commit`
  repairs and never refuses. A hook that only warns prints what the reread
  before `git commit` already has to catch.

## Assumed

- That a condensed subject fits 52 characters. The history's median is 61, so
  this is a bet that the extra nine are the sentence rather than the subject.
- That a reread holds it, which is the bet the prose rule already makes and the
  one `D-DOC-002` records.

## Wrong if

- Subjects still land past 52 after this date. Then either 52 is the wrong
  number for a repository whose subjects say what a change did rather than which
  ticket it closes. Or a reread is not enough and the `commit-msg` hook comes
  back.
- A commit here genuinely needs `[DOCS]` or `[SECURITY]`. A security advisory
  against this server would be the case, and that keyword is then not the core's
  alone.

## Confirmed on 2026-08-23

Measured over 779 commits. The keyword half held without an exception, and no
change here has needed one of the core's two. The first **Wrong if** has fired:
397 subjects are 52 characters or longer at a median of 58, and nothing
converges. Exactly one is past 72, where most used to be past 52.

So the practice follows the tool's two severities rather than one demand, and
`AGENTS.md` stated both numbers as one rule. Put to the maintainer on
2026-08-23: the wording follows the tool. The maintainer declined a raise of 52,
because it would move what the guide tells every other project. The hook stays
refused for what **Decided** says.
