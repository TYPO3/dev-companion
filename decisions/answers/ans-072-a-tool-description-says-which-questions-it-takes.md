---
id: D-ANS-072
title: 'A tool description says which questions it takes'
date: 2026-08-10
status: open
coveredBy:
  - ScopeTest::theTwoLookupsThatBothReadAsAConventionNameEachOther
  - ScopeTest::theTwoLookupsThatBothReadAsATableQuestionNameEachOther
---

# D-ANS-072 — A tool description says which questions it takes

**`typo3_script_lookup` names what it holds and hands the run-shape questions to
`typo3_test_run_guide`; the session that had both in its list called neither for
six shell round trips.**

It read "find notes for TYPO3 core scripts" as "find the right suite", which the
other tool had already answered, and grepped a 1400-line script instead.

## Evidence

- `feedback/2026-08-10-101802`. The questions were how the browser suite
  provisions its instance, whether arguments reach Playwright, which image and
  network flags it uses, and whether an instance can be reused. None of them
  sounds like a script lookup, and the session says so itself: it cannot report
  whether the tool would have answered, because it never made the call.
- Three of the four are answered now, and not by this tool.
  [`D-KNW-068`](../knowledge/knw-068-looking-at-a-backend-change-is-a-suite-the-core-already-carries.md)
  put the prepare and browser suites into `test-suite-hints.json` with what each
  prints, that the containerised run passes nothing through, and what
  `PLAYWRIGHT_USE_EXISTING_INSTANCE` does.
- The fourth is not, and this tool would not have carried it either: the
  document it answers from has no section on the browser suites at all — asked
  the session's own four questions, it matches one, on the word "arguments".

## Decided

- The description says what the document holds — the invocation and what it
  needs first, the passthrough and the options, the commands per subject, the
  pre-commit hook — and names `typo3_test_run_guide` for the question this
  session actually had. Two adjacent tools whose names both read as "about the
  scripts" is what the routing has to survive, and only the descriptions can do
  it.
- No section on the browser suites in the document. A prose document may only
  name a suite every covered major carries, which `KnowledgeTest` holds, and the
  browser suites are not on every one — that is exactly why the suite list is
  where they live, with a version range each.
- The container image and the network parameters stay unanswered here. A session
  building its own harness needs the route rather than runTests.sh's internals,
  and that is
  [`D-KNW-069`](../knowledge/knw-069-a-browser-in-a-container-reaches-a-site-on-the-router.md).

## Assumed

- That the name is not the whole of the problem. It was read as one kind of
  question and its description offered nothing to correct that, so the
  description is where the cheap fix is; whether a tool answering from one
  document earns a name beside `typo3_rule_lookup` at all is a larger question
  this does not settle.

## Wrong if

- Another session reports skipping it for the same reason. Then the word
  "script" is what misroutes, and renaming or folding the tool is the change —
  which costs a name clients already know.

## Since then

The description says both halves today: it opens on what the tool holds and
hands the rest over by name. No session reports skipping it for the reason this
entry names — the one feedback since is a client that delivered every tool as a
bare name, which is a cause on the client's side.

The pattern recurred one pair over: a session could not tell whether "what is
this codebase's idiom for X" goes to the hint lookup or the rule lookup, and
read both descriptions after the fact. Neither names the other and both
plausibly take the question, so **Decided** holds on the pair that carries most
of the corpus.
