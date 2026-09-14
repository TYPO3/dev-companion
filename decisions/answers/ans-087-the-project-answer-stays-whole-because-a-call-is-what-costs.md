---
id: D-ANS-087
title: The project answer stays whole because a call is what costs
date: 2026-08-19
status: open
---

# D-ANS-087 — The project answer stays whole because a call is what costs

**`typo3_project_describe` still answers everything it knows in one call rather
than lets the caller select parts.** A session pays per call. So what a
selection saves is bytes nobody pays for, and what it risks is a second context.

## Evidence

Measured on 2026-08-19 against `.fixtures/installation`, and against the
recorded answers `bin/cli tools:measure` reads.

- The whole answer is 3267 characters of text and 2464 of data. Among the 24
  tools it is fourteenth of twenty by recorded weight, at 14,338 bytes over two
  calls.
- Half of it is not about the repository. Lines 17 to 32 of the text and 1409 of
  the 2464 data characters are `guides`, the same fifteen procedures in every
  answer. The other half — version, the four PHP numbers, Node, extensions,
  sites, declared commands — is what no other call answers.
- `typo3_server_scope` is 86,061 characters in one answer, twelve times this
  one, with `covers` at 22,813 and `doesNotCover` at 7851. That is where an
  answer's weight actually is.
- `D-FBK-020` is `confirmed` and measured the cost model: one context per call,
  the context a call re-reads at 82k on average. Against that, 1409 characters
  cut is not a saving anybody can observe, while a fetch for what the cut took
  costs a whole context.

## Decided

- **No selection parameter.** This is the first call of a session, so a caller
  who chooses sections chooses them in its least informed moment. That is the
  failure `D-GUI-015` measured four times the same day. The wording that reaches
  an answer is the wording somebody already has the answer for.
- **The guides listing stays, though it is the largest single part.**
  `D-ANS-061` put it here because three core sessions had a resource list in
  hand and read none of it. `D-AUD-011`'s index entry now routes to this answer
  rather than to `typo3://guides`. Cutting it undoes both.
- **The weight question moves to `typo3_server_scope`**, where a selection is
  also the honest shape. An orientation answer has topics, and a caller who asks
  for one of them knows which.
  `todo/open/2026-08-19-014500-weigh-the-scope-answer-against-what-a-caller-asks-it-for.md`
  carries it.
- **No measurement here covers a client's context window.** The measurement
  covers bytes and the call model. Whether any client truncates this answer is
  unknown, and it is the first thing that would reopen this.

## Assumed

- That the fixture installation is the ordinary size. A project with forty
  extensions and eight sites grows the half that is about the repository, which
  is the half nobody proposed to cut.
- That a caller reads what it gets. `D-ANS-061` is the record of that failure
  for a resource list, not for an inline answer.

## Wrong if

- A filed session reports that this answer crowds out its task, or that a client
  truncates it. Then the size is a client property rather than a byte count, and
  the first candidate is the guides listing this entry keeps.
- Nobody turns out to read the guides half. Then `D-ANS-061`'s own **Wrong if**
  fires first, and the list moves rather than shrinks.
- A session asks for one part of the project answer by name. Then a caller needs
  the selection after all, and what it names is the evidence this entry lacks.

## Since then

The weight question this entry moved settled on 2026-08-19, and the evidence the
card said did not exist was in the archive. A session measured that answer at
roughly 11,000 tokens and near 3% of its cost and reports that it changed no
decision. So the answer is not that a call is what costs and the bytes are free.
Both are true at different sizes, and 94,000 characters is where the second
starts to cost.

What it did not change is the third **Decided**. What separates the two answers
is not their size; an orientation has parts a caller can name.
