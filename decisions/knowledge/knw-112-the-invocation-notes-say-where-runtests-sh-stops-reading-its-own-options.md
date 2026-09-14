---
id: D-KNW-112
title: 'The invocation notes say where runTests.sh stops reading its own options'
date: 2026-08-24
status: open
coveredBy:
  - HintsTest::theInvocationNoteSaysWhereTheOptionParsingStops
---

# D-KNW-112 — The invocation notes say where runTests.sh stops reading its own options

**`getopts` stops at the first word that is not an option, so a path written
before `--` goes to phpunit together with everything after it.**

The corpus shows the form that works and never the one that fails. A session
that arrived with the wrong order had the command that works in an answer it did
not read. It had nothing to search on once phpunit had refused.

## Evidence

- Re-run on 2026-08-24 with the own suite and path of the session that reported.
  `typo3_test_run_guide` returns
  `CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- <path/to/Test.php>`
  as the targeted form. It returns
  `CI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup <path>`
  as a worked example. The order the feedback asks whether this tool covers is
  in the answer twice, so that half has its answer.
- Read on `.checkouts/12.4`, `13.4`, `14.3` and `main`. The script parses with
  `while getopts ":a:b:s:c:d:i:p:xy:nhu" OPT`, then `shift $((OPTIND - 1))`, and
  passes `"$@"` to the phpunit command. `getopts` stops at the first non-option
  word. So `-s functional -d sqlite <path> -- --filter x` leaves
  `<path> -- --filter x` for phpunit and phpunit reads `--filter` as a test
  file. Only the option letters differ across the four, `12.4` and `13.4` carry
  `g`, `13.4` carries `t:` as well. The parse is the same on all of them.
- Nothing in `knowledge/` or `skills/` says it. Searched on 2026-08-24. The one
  note about the separator is "Everything after `--` is handed to the underlying
  tool unchanged". That describes the form that works and says nothing about a
  word before it, and `Test file` appears nowhere.
- The line it prints names the sibling failure in the same block.
  `invocation.preconditions` quotes
  `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`
  and says it names phpunit rather than the directory. That sentence exists
  because a session hit it —
  [`D-AUD-009`](../audience/aud-009-the-entry-point-claims-patch-work.md).
- The description was not a channel. The session that reported had the tool as a
  bare name in a deferred list and never fetched its schema. So the first line
  it asks for a rewrite of was outside its context,
  [`D-AUD-003`](../audience/aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md).
- A skill that did not open named the tool at the step the task passed.
  `skills/typo3-core-patch-review/SKILL.md` sends the verification step to
  `typo3_test_run_guide` and says to take the commands from it and never from
  memory. `feedback/2026-08-24-122413`, from the same debrief a minute later,
  reports three skills that match the request word for word and none that
  activates. That card carries the activation question.

## Decided

- The mechanism goes into `invocation.notes`, beside the separator note that is
  already there. Where the option parse stops, and what phpunit gets when a path
  comes first. It is the rule rather than the symptom. So it covers the option
  written after a path as well as the path written before the separator.
- Not a description line, which is what the feedback asks for. The session that
  lost the container run never loaded the description, the name is spoken for by
  [`D-AUD-011`](../audience/aud-011-the-instructions-index-the-question-each-tool-answers.md),
  and `D-AUD-009` already rewrote this tool's first sentence for the same
  reading — a third question claimed by one description is paid for by the two
  already there.
- Queued rather than closed on the spot. The note states what the core's own
  script does, and [judging.rst](../../documentation/records/judging.rst) puts
  anything that has to be looked up in TYPO3 into a todo however small. The
  reading above is that lookup and the card carries it, so what is left is the
  wording.
- The statement is unbound. The parser is the same on all four covered branches,
  so no `since` and no `until`.
- The note is held by a test naming this entry, as the two notes beside it are.
  Nothing sweeps the wording of a note, so what a test can hold is that this one
  is still there: the parsing rule, `shift $((OPTIND - 1))`, and the line the
  failing run prints. Whether the wording lands is what the **Wrong if** below
  is about, and no test reaches that.

## Assumed

- That a session which wrote the command from somewhere else reaches the note at
  all. The measure is one that reached neither the note nor the answer around
  it. The note is for the call that follows the failure, which is the place the
  `bin/phpunit: not found` line already has.
- That the message the feedback quotes is phpunit's. `Test file` is in no
  `runTests.sh` on any covered branch and the parse above accounts for it, but
  nothing here ran the failed command.

## Wrong if

- A session reports the same failure after it read the answer. The note would be
  in the wrong half of it, and the order belongs beside the targeted command
  rather than among the notes.
- The failed form turns out to stop somewhere else, the script refuses the word
  before phpunit sees it. The mechanism would be wrong and the note would name a
  cause nobody meets.
- `feedback/2026-08-24-122413` lands on activation and a session with the review
  skill open still writes the command from the core's own `AGENTS.md`. Then the
  skill was not the rung above this one, and what routes a session at that
  moment is open.
