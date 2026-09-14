---
id: D-ANS-028
title: 'A two-letter query word is searched for'
date: 2026-08-02
status: open
coveredBy:
  - DocumentationTest::aViewHelperNamedAfterAKeywordIsReachedByItsOwnName
  - TermSearchTest::aShortTermIsCarriedAsAWholeWord
  - TermSearchTest::aTwoLetterWordIsATerm
  - TermSearchTest::aTwoLetterWordThatSaysNothingAboutTheSubjectIsStillDropped
  - TermSearchTest::oneLetterIsNot
---

# D-ANS-028 — A two-letter query word is searched for

**`TermSearch::terms()` admits a word of two characters. `STOPWORDS` names the
two-letter words that say nothing about a subject, rather than a length that
cuts them off.**

The floor was three characters and nothing said why. Whether a short word is
noise depends on how the matcher treats it, not on its length.
`PREFIX_FROM_LENGTH` already answers that: below four characters the matcher
takes a term as a whole word. The prefix noise its docblock records is a
property of the prefix match and cannot reach a term the matcher takes whole.
That noise is `fal`, which reached seven hints through "fallback" and "false".
And `ist`, which decided a German query and occurs in no hint as a word at all.

The floor instead did the stopword list's work, silently, for every word that
short. Moving it means the list has to do its own.

## Evidence

- The miss the floor caused, and it is not marginal. The ViewHelper reference
  titles a page after the tag. So `Global/If.html` has the title "if", and the
  only word of `f:if` that can reach it is two characters long.
  `TermSearch::terms('f:if')` returned nothing at all, and the lookup answered
  `empty`.
  [`D-ANS-023`](ans-023-a-viewhelper-question-is-answered-by-widening-the-manual-index.md)
  measured that after it indexed the book and left it here.
- Both corpora go through it, so the measurement covers both before and after,
  over the 41 scenario prompts. That is the only corpus of real wordings this
  repository has. The hints: `bin/cli hints:coverage` prints the same page byte
  for byte, and all 41 prompts return the same hints as before. The prose
  sections: unchanged for all 41.
- The manuals, live at 14.3, 1419 pages over the four books. 2 of the 41 prompts
  changed, and both gained the same page. `SKILL-05` and `SKILL-08` each ask for
  checks that "run locally and in CI", and the word they used now reaches CI/CD
  Automation.
- The floor moved on its own, before the stopword list was complete, cost 7 of
  the 41. Five of them gained Setting up backend user groups, on the word "up"
  of "set it up from scratch". One of those five reordered its hints as well.
  That is the measurement that says the two halves are one change.
- What the change buys on its own query: `f:if` gets an answer rather than
  empty, and `Global/If.html` is in the ranked index at 14.3. It is not first.
  Ten of the 1419 pages carry `if` as a whole word: two TypoScript function
  pages, `security.ifAuthenticated`, `mfa.ifHasState`,
  `ShouldUseCachedPageDataIfAvailableEvent` and the rest. All ten score exactly
  198, so the order among them is the build order of the index, and
  `Global/If.html` is eighth. `f:if f:then f:else condition ViewHelper` puts it
  tenth of 204.
- The measurement covered the lever for that tie, and it is not this one. The
  manual weighs a field by `UNDILUTED_WORDS = 12` and no title it has is that
  long. So a page titled "if" and a page titled Should Use Cached Page Data If
  Available Event are worth the same. At a reference of 3 the page titled "if"
  is fourth for both queries — and the six results of all 41 prompts change.
  That is a decision about `Documentation`, with its own before and after.

## Decided

- Two characters, not one. A single letter is a whole word in the corpus as
  readily as in the query. So the `f` of `f:if` is the `f` of every other tag
  written out, and it separates nothing. `a` and `i` came out of `STOPWORDS` in
  the same commit: the floor drops them first, so they were entries nothing
  could reach.
- `am`, `go`, `he`, `no`, `so`, `up`, `us` and `we` go in, beside the fifteen
  two-letter stopwords that were already there. They are the English function
  words the floor had absorbed.
- `if` deliberately stays out. It is an English keyword and a ViewHelper and a
  TypoScript function. It is the whole of what a caller who asks about `f:if`
  has left after the tokenizer.
- `be` stays in, against the same argument and the other way round. It is the
  backend namespace on 17 pages of the ViewHelper reference. It is also the verb
  in every sentence that says what something should be. So an admitted `be`
  would put those 17 pages into every query that used the word.
- Not the tie in the manual's rank, and not `f:or` or `f:then`. The first is
  `Documentation`'s dilution reference and moves every manual answer. The second
  needs `or` and `then` out of a list that both corpora share, which would make
  `Global/Or.html` a candidate for every English "or". Both stay in the todo.

## Assumed

- The eight words added are the ones that had to be. They are what the 41
  prompts turned up. A two-letter function word nobody has written in a prompt
  yet is not in the list, and nothing will notice until it wins something.
- The two-letter words left out carry signal here — `id`, `fe`, `db`, `ui`, and
  the version numbers `12` and `13` a prompt names. None of them moved an answer
  in the sweep, which is weaker evidence than it looks. It says they cost
  nothing, not that they earn anything.
- What one afternoon measured holds for questions nobody asked that day. The
  manual half of it ran against 14.3 as published that day.

## Wrong if

- The stopword list has to grow every time somebody phrases a prompt with a
  two-letter word nobody wrote down. Five of the 41 prompts turned on "up"
  alone. So the next one is a word rather than a class, and no floor remains to
  catch it.
- A two-letter word that does carry signal starts to win questions that are not
  about it. `if` is where that would show first: it is a keyword in one book and
  a conjunction in every English sentence. `how do I check if a page is hidden`
  already answers with ShouldUseCachedPageDataIfAvailableEvent where it used to
  answer with the TCA checkbox pages.
- The hint and prose corpora turn out to have stayed the same only because their
  fields are long. A term nothing carries counts as if the square root of the
  corpus held it. So an admitted word the corpus has no answer for lowers the
  coverage of everything beside it. `record id uid field` returns three hints
  where it returned five.

## Since then

The half this entry left to the todo stands, and it is
[`D-ANS-032`](ans-032-the-manual-ranking-is-diluted-by-an-ordinary-titles-length.md).
`Documentation::UNDILUTED_WORDS` is 3, picked against the 41 scenario prompts
and the seven queries this repository already asserts an answer for, rather than
against `f:if`. Both numbers this entry recorded from that measurement held. The
page titled after the tag is fourth for both queries at a reference of 3, and
all 41 prompts change. What it does not reach is the last of the tie. Three of
the ten pages have the title `if`, no length reference separates identical
titles, and the page is fourth rather than first. The lever that remains is the
`f:` prefix. `f:or` and `f:then` stay in the todo, unchanged.
