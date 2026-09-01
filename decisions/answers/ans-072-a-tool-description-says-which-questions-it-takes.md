---
id: D-ANS-072
title: 'A tool description says which questions it takes'
date: 2026-08-10
status: open
coveredBy:
  - ScopeTest::theToolsACallerCannotChooseBetweenNameEachOther
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
- **The pairing is a step of adding a tool, and no check proposes one.** Two
  tools sharing a `covers` entry is the candidate rule the data allows, and that
  set is wrong in both directions. It holds 45 pairs on 2026-09-01, 36 of them
  out of the one entry naming the nine installation tools, where the icon lookup
  against the label lookup is a choice nobody makes. The pair this entry's own
  test holds is not in the set at all: `typo3_record_lookup` carries a `covers`
  entry of its own.
- **Naming another tool back cannot be demanded either.** A description names
  one to hand a caller on as much as to tell itself apart, and the string does
  not say which it is doing. `typo3_gerrit_lookup` names the three tools its
  answer composes with, and none of the three owes it a mention.
- **What holds a pair is a row.**
  `ScopeTest::theToolsACallerCannotChooseBetweenNameEachOther` takes one per
  direction, so a pair somebody notices costs a line, and what made it a pair is
  written beside it.
- **Four pairs came out of reading the thirty-one descriptions on 2026-09-01.**
  `typo3_label_lookup` searches the labels an installation registers and
  `typo3_translation_domain_lookup` computes the reference to one from a path, a
  file no installation has yet included; neither named the other. The other
  three were half-linked, so what the reading added is the missing direction:
  the project answer to the extension answer, the schema lookup to the flex
  lookup, and the suite guide to the script lookup. `typo3_snapshot_scope` is
  named now by the lookup whose misses it judges, which is that fix one step
  away from a pair.
- **`typo3_forge_lookup` and `typo3_gerrit_lookup` stay as they are**, and so
  does `typo3_server_scope`. The first two name each other's server in their own
  prose, so a caller has the tracker against the review server to choose on, and
  the third routes to the rest rather than competing with them.
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
