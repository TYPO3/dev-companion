---
id: D-KNW-071
title: Proving what a rendering change renders is a procedure this server carries
date: 2026-08-14
status: open
---

# D-KNW-071 — Proving what a rendering change renders is a procedure this server carries

**How a throwaway functional test renders frontend output in a core checkout,
and how a session reads what it rendered, is a document below
`knowledge/documents/core/testing/`.**

Two skills grant the probe and neither says what one consists of. A session that
reviewed a three-line TypoScript change had no other evidence available. No core
test covered the constellation and the diff says nothing about what it renders.
It spent five of six container rounds on its own harness.

## Evidence

- Re-run on 2026-08-14 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own question reaches `core-tests` and
  `project-extension-tests` on text alone. That question is "how do I render an
  RTE bodytext snippet through lib.parseFunc_RTE in a functional test and read
  the resulting HTML". Rephrased as "read the rendered HTML a functional test
  produces" it adds `security-sinks` and `browser-tests-outside-core`. None of
  the five is about output to look at.
- The vocabulary is absent. `parseFunc` occurs nowhere below `knowledge/` or
  `skills/`.
- The nearest hint answers the other question. `extension-test-frontend-request`
  is `executeFrontendSubRequest` and the cache-hash conditions: how to assert a
  response whose expected value is already known. The need was the opposite. The
  value was the unknown, and a functional test that passes prints nothing.
- Two skills write the permission down and neither writes the recipe.
  `typo3-core-patch-review` grants the scratch probe as "add a temporary fixture
  column, a model property or a test of your own, run a targeted suite against
  it". `typo3-core-issue-triage` carries the three rules a throwaway
  reproduction owes: where it goes, seen red before anybody believes it, taken
  out again. Both assume the test is already written.
- `D-SKL-032` left the question open for "the run that has both", and this is
  it. A session that could run a suite, that got every substantive finding out
  of the run, and that still paid for the harness. It counted the cost, six
  rounds at roughly two minutes, three of them with nothing but its own mistakes
  as output. That is what `D-FBK-027` measures.
- One of the four things the feedback names is already answered.
  `knowledge/documents/core/testing/scripts.md` states that sqlite is the
  default database for `-s functional` and the fastest, so the document belongs
  beside it rather than repeating it.
- The feedback's account of the second trap is not confirmed. It reports that
  the parser reads a `<` at the start of a TypoScript value as a reference. The
  tokenizer's operator table
  (`.checkouts/main/typo3/sysext/core/Classes/TypoScript/Tokenizer/LosslessTokenizer.php:432-451`)
  makes `=<` the reference and a bare `<` after the identifier the copy. It
  strips whitespace before it reads either. What actually bit that session is
  what the research has to establish.

## Decided

- Built, as a document rather than as hints. The gap was a procedure: which
  cObj, which operator form, how to make output visible, which suite invocation.
  A procedure written as statements is a set of sentences nobody can carry out
  in order (`D-FBK-043`).
- Its boundary is the proof, not the render. It says how a session builds a
  throwaway test so that a render becomes readable, and how it reads it. A
  functional test that passes prints nothing, so the session has to force the
  output out of it. What `lib.parseFunc_RTE` does belongs to the frontend-render
  hints, and the document names no patch.
- A session reaches it from where the skills already grant the probe. The review
  skill's scratch-probe paragraph and the triage skill's throwaway-test rules
  point at it. That is the half that decides whether the next session finds it
  at all. A document nothing routes to is the same gap one step further in.
- The feedback's own recipe is not copied down. Its author guessed about TYPO3
  exactly as much as this judgement would, and the tokenizer already contradicts
  one of its four claims. A session establishes every step against `.checkouts/`
  and binds it where it does not hold on all covered lines. That is the todo's
  first step.
- The priority is `normal`. One session reported it. The cost it counted is a
  class of patch where a read of the diff is not evidence at all. TypoScript
  defaults, `ext_localconf.php` TypoScript, anything below `lib.parseFunc`.

## Assumed

- That the shape generalises past this one patch. The session named a class of
  change rather than its own. Whether one page covers a TEXT probe, a
  FLUIDTEMPLATE probe and a page-request probe is what the research decides.
- That the sentinel-assertion trick is the way to read rendered output and not
  the session's own invention. The feedback states it as the only way and
  nothing here checked it against what the framework offers.

## Wrong if

- The research finds a core helper that already prints rendered output from a
  functional test, which would make the document one sentence that names it.
- A session with the document installed still builds its probe by hand, which
  would say the gap was the route rather than the procedure.
- The steps turn out to differ per covered line far enough that the page is a
  table of versions rather than a procedure. Then it is hints with `since` and
  `until`, and this entry is what was wrong about the shape.

## Since then

Built and routed to from two skills, and a run of the probe on all four covered
lines settled both **Assumed**.

The feedback's second trap does not exist as reported, and the research replaced
it with the one that does. One operator assigns the markup and the other, a
space away, makes the line a reference, a failure with no error in it. The
sentinel assertion is the worse way to read rendered output, since the test body
may simply print. So the first **Wrong if** did not hold. The third did not
either, and one section pair carries what differs between the majors.

The second **Wrong if** held. A review had the document installed, read the
paragraph that names it, and skipped it because the diff was PHP. It spent six
container rounds on the harness. So the gap was the route, which `D-KNW-122`
reads.
