---
id: D-EVI-003
title: A review runs the checks that cannot change the code
date: 2026-07-31
status: confirmed
coveredBy:
  - ProjectTest::aCommandThatWritesIsNeverReportedAsACheck
---

# D-EVI-003 — A review runs the checks that cannot change the code

**A task told not to change files runs the project's own commands whose
declaration says they hand the code back unchanged, and no others.**

`typo3_project_describe` reports the property per command, read off the declared
body.

Three recorded `REVIEW-02` runs read "do not change files" as "run nothing" and
quoted the commands as subjects of findings instead.

## Evidence

- The three runs, bootstrap_package at 02:55 and 08:15, syntax at 12:21,
  executed none of the ten and five commands on offer. Of the fifteen,
  `composer cgl:ci` (`php-cs-fixer --diff -v --dry-run fix`) and
  `composer test:php:lint` (`phplint`) appear in both repositories and rewrite
  nothing. In the syntax run two findings about php-cs-fixer and phplint came
  from CI configuration that either command would have settled in one
  invocation. Against them: `composer cgl` is the same tool without the flag,
  `composer set-version` and `composer changelog` write, and `composer test`
  reaches a functional suite. The two groups are one flag and one subcommand
  apart and no script name separates them.

## Decided

- The session runs the checks, and the answer says what they printed. The
  objection that a failed command tells you less than the configuration that
  fails it is about what a finding says. It is not about whether to gather it.
  It survives as a limit. The finding still names the configuration, and the run
  is what takes it from derived to established. The property answers the
  objection that a review must not change files, rather than an abstention from
  all of them. Rejected: a run of everything, which breaks the instruction the
  user gave. Rejected: a run of nothing, which is what the runs measured.
  Rejected: a question to the user per command, which the answer could not have
  formed before `typo3_project_describe` carried `runs`.

## Assumed

- That a body that declares a check is one. `php-cs-fixer --dry-run`,
  `phpstan analyze`, `phplint` and `eslint` without `--fix` do not rewrite the
  sources they point at. But they take configuration, a fixer can get a rule set
  that writes elsewhere, and a checker with a cache leaves a file behind. The
  claim is that the code comes back unchanged, not that the filesystem does.

## Wrong if

- A run reports a checkout modified by a command marked `check`. The
  classification would then be a promise the declaration cannot carry. What
  remains is to name the command and let the user decide. Or the reverse.
  Reviews that now run the checks report the same findings they read out of CI
  files. Then the runs cost time and settled nothing, and the base's three-way
  distinction did all the work by itself.

## Since then

A session went to look for the first **Wrong if** instead of a wait for it, on
2026-08-02, and the classification did carry it. The reader took a declared line
as the tool in front of it. So `phpstan analyse && php-cs-fixer fix` and four
shapes like it answered `check` while they rewrote the sources. The **Assumed**
held per tool and broke on the line. A chain is the convention in a
`package.json`, where `tsc --noEmit && vite build` is one script. The reader
reads every command on a line now.
`ProjectTest::aCommandThatWritesIsNeverReportedAsACheck` holds the writers to
never get that answer. That makes a wait for a run that reports a modified
checkout unnecessary. The decision stands. What was wrong was the read, not that
the declaration is readable.

## Since then

A review did run one of those checks and it would not start.
`feedback/2026-08-18-113412` reports that `composer cgl:ci` aborted in the
vendor tree's `platform_check.php` on an interpreter a minor below what the
installed packages require. So a command on offer as safe to run never reached
the code it pointed at. Neither **Wrong if** covers it. The classification was
right, and the checkout came back unmodified because nothing ran at all. What it
shows is that the property answers what a command does to the sources and never
whether it can start here. That is the second question
[D-ANS-086](../answers/ans-086-the-project-answer-carries-the-bound-that-stops-a-command.md)
puts into the same answer. The decision stands. What was absent is a number
beside the property rather than a different read of it.

## Confirmed on 2026-08-22

The mechanism holds. Each command declares `runs` as `check`, `change` or
`unknown`, read off the body in `Project::runs()`, and
`ProjectTest::aCommandThatWritesIsNeverReportedAsACheck` still keeps the writers
off the first value. Neither **Wrong if** has fired. No run has reported a
checkout modified by a command marked `check`. The one failure since, a command
that would not start, in the section above, is neither of them.

The tool is `typo3_project_describe` now, renamed by `D-SCO-011` after this
entry existed, and the paragraph under the statement still calls it
`typo3_project_describe`. The record stays as it read; this line is where a
reader finds out.
