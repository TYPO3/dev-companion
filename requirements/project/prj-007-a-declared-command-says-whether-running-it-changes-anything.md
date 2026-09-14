---
id: R-PRJ-007
title: 'A declared command says whether running it changes anything'
status: held
heldBy:
  - ProjectTest::aCommandThatDeclaresNothingReadableIsNotCalledSafe
  - ProjectTest::aCommandThatWritesIsNeverReportedAsACheck
  - ProjectTest::aDeclaredCommandSaysWhetherRunningItChangesTheSources
  - ProjectTest::anEnvironmentAssignmentInFrontOfACommandIsNotTheCommand
---

# R-PRJ-007 — A declared command says whether running it changes anything

**Every command the project answer lists carries the body of its declaration and
what a run of that body does to the sources.**

That is a check, which hands the code back as it was; a change, which rewrites
something; or unknown, where the body does not say.

The answer reads it off the declaration and never runs it. It follows `@name`
references to the script they name, and a script that reaches one unreadable
line is unknown rather than safe. One line is one command only where nothing
chains another onto it. `&&`, `||`, `;` and `|` each start one that runs, and
the strongest claim among them is the claim about the line. What comes before
the tool is not the tool. A composer prefix and a `NAME=value` in front are the
environment the command gets. So they stay out of the tool name and in the
declaration the answer shows. Unknown is a third answer and not a quiet no. A
test suite runs the project's own code, and nothing in a manifest covers what
that code writes. Nor is a check a promise that nothing writes. A checker may
keep a cache of its own; what it does not do is hand the code back different.

Without it, "run the ones that change nothing" is an instruction nobody can
follow. A name does not carry the property and never will: `cgl` and `cgl:ci`
are the same tool one `--dry-run` apart.

## From

A `REVIEW-02` run against `georgringer/news` (2026-07-31), whose six declared
commands all came back `unknown` because each of them starts with
`PHP_CS_FIXER_IGNORE_ENV=1`. So the `--dry-run` line and the line that fixes
were the same answer. The run picked the safe one by its own read rather than
from anything this server told it. And three recorded `REVIEW-02` runs in two
repositories (2026-07-31) with the instruction not to change files. They ran
none of the fifteen commands the answer offered. Among them were
`composer cgl:ci` and `composer test:php:lint`, which change nothing. They would
have settled two of the findings the syntax run derived from CI configuration
instead.
