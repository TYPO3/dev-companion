---
id: D-ANS-089
title: 'A person is a filter on the backlog'
date: 2026-08-19
status: confirmed
coveredBy:
  - ForgeTest::aNameCarriedByTwoPeopleResolvesToNeitherAndAnswersWithBoth
  - ForgeTest::aNameNoMemberCarriesIsResolvedFromTheIssuesThatNameIt
  - ForgeTest::aNameNothingHereCarriesReadsNothing
  - ForgeTest::aPersonIsResolvedAgainstTheProjectsOwnMembers
  - ForgeTest::aRowSaysWhoFiledIt
  - ForgeTest::theStatusIsWhatPutsWhatAPersonAlreadyFiledInReach
---

# D-ANS-089 — A person is a filter on the backlog

**`typo3_forge_lookup` narrows its enumeration by `reportedBy` and `assignedTo`
in a person's own name. It resolves that name to the id the tracker filters by,
and reaches a person's history with `status`.**

The question goes to one tracker that takes only a numeric user id and serves no
public user list. So a caller with a name in hand cannot ask it at all. The way
in that looks like it answers, a match of the name as words, answers a different
question with the same shape.

## Evidence

- `feedback/2026-08-19-131336` is the session that asked for one contributor's
  issues. `query="Frank Nägler"` answered 9 issues with `total=9`. It mixed the
  ones he holds with the ones a third party mentioned him in. The tracker
  answers 621 for `author_id=52` and 588 for `assigned_to_id=52`, both measured
  again here on 2026-08-19. The tool was wrong by two orders of magnitude and
  nothing in the answer said so.
- The session did the resolution by hand. It read an issue that person had
  touched, lifted the id out of its `author`, then paged
  `/issues.json?author_id=52` seven times. That is 1b on the judgement ladder:
  the answer is available and there is no way to ask for it.
- `/users.json` answers 401 without an administrator's credential, measured on
  2026-08-19. `/projects/typo3cms-core/memberships.json` answers names and ids
  without one, 185 members over two pages of a hundred.
- Somebody the project holds no membership for reported 24 of the 100 newest
  issues, measured on 2026-08-19. A map of the members alone would answer "no
  such person" about a quarter of the reporters.
- A full-text search for a name resolves some of those and not others. On the
  same day "Andreas Kießling" reached 5 issues, one of them his. "Konrad
  Michalik" reached none although he had filed two of the last hundred.

## Decided

- **The person filters narrow the enumeration rather than form a fourth way
  in.** `oneOf` makes `issue`, `query` and `open` exclusive, and
  `ToolContractTest::anArgumentInAnAlternativeNamesTheOnesItExcludes` holds each
  branch to name the others. So a person as a branch would have to declare
  itself exclusive of the assignee filter it should combine with. What the
  feedback asked for, a person's issues whatever their status, is `open` with
  `status`.
- **`status` widens the enumeration and does not rename it.** `open` is the word
  clients installed months ago call this with, which is what `AGENTS.md` says
  wins over a word this afternoon's prose would prefer. What a person has filed
  over the years is mostly closed: 617 of Frank Nägler's 621.
- **The tool resolves a name from the project's members first, and from the
  issues that carry it second.** The membership list is the only public place
  the tracker answers a name and an id together. The search covers the quarter
  of reporters outside it, and it is the step the session that reported it took
  by hand.
- **Only a name carried whole resolves.** This is where a person parts from an
  area. Half of "backend ui" is still the backend, and half of "Andreas
  Kießling" is four other people called Andreas. The partial match came first
  and answered that call with those four while the fallback that would have
  found him never ran.
- **A name that reaches two people resolves to neither and answers with both.**
  Two people merged into one backlog is a wrong answer nothing about it says is
  wrong. A word that reaches three areas is one question about a subsystem.
- **A name that resolves to nobody reads nothing**, the way a word that names no
  area does. The set it would otherwise get is the backlog of everybody, in the
  shape of a set about one person.
- **`reportedBy` is a field of every row.** The person dimension was already
  half in the answer: `assignedTo` was on the row and neither was on the way in.
  A page that says who reports it costs no call per row.
- **`query` says what it matches over.** The description and the answer both
  name it as full text over subject, description and comments. Both say that a
  name there reaches the issues that mention a person rather than the issues
  that are theirs. The failure this closes was not a wrong tool but an answer
  that read as a count.
- **A person filter passed without `open` enumerates anyway.** The schema's
  grammar stays and states the three ways in. This stands against the client
  that validates nothing and reaches a search for the empty string instead of
  the question it plainly asked.

## Assumed

- That a resolved name is worth two lists this server did not read before. The
  store holds the members for a day, and the search costs two calls only where
  the members did not carry the name.
- That the members are enough of a candidate list. Where the name resolves to
  nobody, what comes back is the boundary and the way past it rather than 185
  names nobody asked for.
- That nobody asks for two people at once. The tracker ANDs `reportedBy` and
  `assignedTo`, which answers "what did this person file that they also hold"
  and nothing wider.

## Wrong if

- A session names a person, is told the name resolves to nobody, and the person
  had filed issues all along. Then the two sources are not the ones the question
  is about, and what would settle it is where that name should have come from.
- A session passes `reportedBy` and reads the count as a person's total and
  never notices `status`. Then the default is the wrong one for the person
  filters and the argument should carry a different one when a name arrives.
- Nobody passes a person filter over the sessions that follow. Then the
  enumeration grew three arguments for a question one session asked once, and
  the answer was the sentence in `query` alone.

## Confirmed on 2026-08-19

The first session to use it resolved a name on its first call and said which
three things carried the answer — `feedback/archive/2026-08-19-134731`. The
`people` block told it who the name resolved to and that nobody else was in the
running. It reports the empty `candidates` as what made it state the count as
fact rather than hedge it; the umlaut round-tripped. `total` beside a capped
page let it report 621 honestly. The sentence in `status` is what made it pass
`all`. It says an enumeration which hides the closed ones "answers 4 where the
number is 621". It names the number the session then got, and the session asks
that the wording stay concrete.

That answers the third **Wrong if** in the other direction: a session used the
filters the day after they landed. What the same session found absent is
`D-ANS-090`, which is about the size of the set rather than about the name.
