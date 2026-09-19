---
id: D-GUI-020
title: The commit guide states the longest line the hook accepts
date: 2026-08-26
status: open
coveredBy:
  - CommitMessageGuideTest::theCoreAnswerNamesWhereTheHooksLengthBoundaryRuns
  - CommitMessageGuideTest::theOverlongLineCheckCarriesTheBoundaryItself
  - CommitMessageTest::aCoreBodyWrapsOneColumnUnderTheHookAndAProjectBodyAtIt
---

# D-GUI-020 — The commit guide states the longest line the hook accepts

**`typo3_commit_message_guide` names the longest line the commit hook accepts
and the first it refuses. So a caller with a stricter written rule in hand can
tell which one runs.**

The width stood as a property of the draft, "the body is wrapped at 72
characters". Only `body-line-too-long` named the hook, and that fires when the
draft is already over. A caller whose draft was clean was told nothing about the
boundary at all.

## Evidence

- `checkForLineLength()` in `.checkouts/main/Build/git-hooks/commit-msg` is
  `grep -q -E '^[^#].{72}'`. Run against files of 71, 72 and 73 characters on
  2026-08-26 it accepted the first two and refused the third. `^[^#]` consumes
  one character and `.{72}` needs 72 more, so 73 is the shortest line that
  matches. It is the hook's only length gate, beside the commit type, the
  `Resolves:` line and the `Releases:` line.
- The check is byte-identical in `.checkouts/12.4`, `13.4`, `14.3` and `main`.
  The only difference between the hook on the oldest covered branch and on
  `main` is that 12.4 also accepts `Fixes:` where `main` takes `Resolves:`
  alone.
- The guide does emit lines of exactly 72. A body of eleven five-letter words
  and one six-letter word came back as a 72-character line with no
  `body-line-too-long` check. The hook's own regex accepts that draft as it is.
  So the report's primary claim, that every draft at the wrap width is one
  character over, does not hold. `wrapParagraph()` with a word at
  `<= BODY_WIDTH` is right as it stands.
- The core checkout's `AGENTS.md` says "**No line of the message may reach 72
  characters** — the hook rejects the commit, footer trailers included". That is
  one character stricter than the hook it names in the same sentence. It is the
  rule the session that reported took the guide to violate.
- Three sessions in that checkout worked from the same imprecise statement.
  `feedback/2026-08-24-205132` hand-wrapped three messages to 66–68 characters
  and used no returned draft. `feedback/2026-08-25-114605` read the regex
  itself, measured every line at 68 or less, and still reported `AGENTS.md` as
  the repository's rule on length. `feedback/2026-08-25-105141` names "the
  72-character rule" among what made `AGENTS.md` look sufficient.
- The first of those three also reported a defect in the user's commit message
  on the strength of the stricter rule. Two body lines of exactly 72 characters,
  which the hook accepts.

## Decided

- The guide states the boundary rather than the width: the longest line the hook
  accepts, and the first one it refuses. Where a caller has a stricter rule in
  front of them, that sentence is what settles which one the commit runs
  through.
- It stands on a clean draft too. A check that only fires on a defect cannot
  tell a caller that the draft in their hand is already within the rule. That is
  the moment all three sessions were in.
- `knowledge/documents/core/contribution/commit-messages.md` says it as well, in
  the same run. It is prose about the core and it comes from a measure here
  rather than recall. It is what a caller who reaches the document rather than
  the tool reads. `D-FBK-052` is why that half is not queued.
- Rejected: a wrap at 71 and a failure at 72, which the report suggested. It
  spends a column of every line of every core commit message to satisfy a
  checkout file that is stricter than the hook it cites. It makes the guide
  report a defect the commit does not have.
- Rejected: a line of exactly 72 as a caveat. Same objection, one step quieter —
  a caller who acts on it shortens a line nothing refused.
- `CommitMessageGuideTest` holds that the answer states it, in both places it
  can. The `line-length-boundary` check on a clean core draft, and
  `body-line-too-long` on one that has a line over the width. Nothing holds that
  the two numbers are the right ones, because the regex they came from is in a
  checkout no unit test may reach.

## Assumed

- The hook a contributor has installed is `Build/git-hooks/commit-msg` from the
  checkout. `D-GUI-003` carries that assumption and why nothing here can read
  the installed copy; this rests on it unchanged.
- The hook is the only thing that measures a line. Gerrit's own server-side
  validation was not read, and nothing in the checkout describes it.

## Wrong if

- A caller commits a draft the guide returned with no length check and the hook
  refuses it. Then the stated boundary is not the one that runs, and it has to
  come out of the hook rather than from a restatement.
- Gerrit refuses a 72-character line the hook accepted. Then the hook is not the
  only gate, the boundary has a second source, and the stricter rule in the
  checkout's `AGENTS.md` describes that one.
- A session reads the stated boundary and still hand-measures to a margin. Then
  what stopped the three sessions was not the absent sentence, and the lever is
  somewhere else on the ladder.

## Since then

The third **Wrong if** fired on 2026-09-19, `feedback/2026-09-18-093147`. A
session read the stated boundary, committed a draft with a line of exactly 72,
found it with `awk` against the checkout's `AGENTS.md` and amended. So the
sentence settles which rule runs and not which rule a session obeys. The
maintainer took the wrap the first bullet under **Decided** rejected: a core
body wraps at `CommitMessage::CORE_WRAP_WIDTH`, 71, and a project body at the
hook's 72. The boundary sentence stays as it was, and `body-line-too-long` still
fires at the hook's line alone. The core's `AGENTS.md` is still one character
stricter than the hook it cites, and a patch there is the core's.
