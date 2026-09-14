---
id: D-DOC-007
title: One page per tool, and the answer on it whole
date: 2026-08-02
status: open
coveredBy:
  - ToolAnswersTest::everyCallOnAPageCarriesItsArgumentsAndItsAnswers
  - ToolAnswersTest::everyRecordedAnswerIsJson
  - ToolAnswersTest::everyToolTheTableDrivesHasARecordedAnswer
  - ToolAnswersTest::everyToolTheTableLeavesOutSaysWhy
  - ToolSurfaceTest::everyToolCarriesItsAnswerOrSaysWhyItHasNone
  - ToolSurfaceTest::theIndexReachesEveryToolAndTheDirectoryHoldsNoOther
---

# D-DOC-007 — One page per tool, and the answer on it whole

**The recording is a directory: `documentation/clients/tool-answers/<tool>.md`,
each answer as the client received it, and every tool in `tools.md` links to its
own.**

`D-DOC-006` put all of it on one page, and the page is what forced the cut. Both
go together. A reader arrives with one tool in hand. Neither the twenty-one
others nor `… 14 more` where the entries were is what they came for.

## Evidence

- The one page was 2,849 lines, and the person who asked for it reported it
  absent on the day it landed. One link reached it, from its head, and it read
  as one long file rather than as an answer per tool.
- Split and uncut it is 8,473 lines over 23 files. The largest is
  `typo3_component_lookup` at 1,339 lines, the median page is under 200, and the
  whole directory is 432 KB. The cut was a decision against 42 calls on one
  page.
- Four of the 43 recorded answers ended the block they stood in. Half of them
  are markdown themselves, since the script notes hand back commands in fenced
  blocks. The answer's own end fence closed the recording's. The count that says
  so is what remains outside the blocks: 43 calls, 39 `Data:` headings. Counting
  blocks does not say it, because the leaked pair reopens and the total comes
  out right.

## Decided

- One file per tool, named for the tool, and `readme.md` beside them as the map.
  The name is the one a caller already knows, which is the rule for anything
  visible outside this checkout.
- Each page carries the head, the day, the installation, whether the console
  answered, because it is the page somebody arrives on. `D-DOC-006` is why a
  recording without it is an assertion about nothing.
- The answers stand whole. The cut was a property of one page with all of them
  on it, and what it cut is the half a recording exists to show.
- A block's fence has more backticks than anything inside it, computed per block
  rather than fixed. So an answer that is itself markdown cannot end it.
- `tools.md` links per tool rather than once in its head. A link in a head is
  not followed by a reader who is already at the tool they came for.
- `tools:record` deletes a page no call writes any more. The command is the only
  thing that knows the table shrank.

## Assumed

- A 1,339-line page is one a reader opens and searches. No measure covers that,
  and the three largest are the ones it rests on.

## Wrong if

- A reader opens a page and still cannot see the answer. 1,300 lines of JSON is
  the same problem the one page had at 2,800. Then the cut comes back per page,
  on the two or three that need it, rather than everywhere.
- Somebody re-runs one tool's recording alone and the pages start to carry
  different days. Nothing in the command can do that today; a hand-edited page
  can.

## Since then

"Every tool in `tools.md` links to its own" was never true, and the head of that
page is what said so. The fence rule went with the markdown (`D-DOC-029`).
