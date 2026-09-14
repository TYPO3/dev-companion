---
id: D-ANS-021
title: The manual lookup says why a short query ranks better
date: 2026-08-02
status: open
coveredBy:
  - DocumentationTest::aResultNamesTheWordsOfTheQueryItWasMatchedOn
---

# D-ANS-021 — The manual lookup says why a short query ranks better

**The live-manual search ranks a table of contents, so a query that names its
subject spends its weight on everything else.**

The page the session was after is in the index and has been all along. What sank
it is that `Record API` is the cheapest thing in a query that carries it. Three
calls came back `answered` with six results each, in the shape a good answer
has.

## Evidence

- `feedback/2026-08-01-002928` re-run on 2026-08-02 against the server as it is
  now, `DocumentationLookup::answer()` in this worktree at `targetVersion: 14`.
  All three queries reproduce. `Record API Fluid template access record.header`
  returns *Multi-language Fluid templates* and `RecordAccessGrantedEvent`.
  `Record API access relation field` returns `RecordAccessGrantedEvent` and
  Extbase *Relations*. `Record API record get has` returns
  `PasswordHasBeenResetEvent` first. The feedback's account of what came back is
  accurate to the page.
- Its suggestion rests on a premise that is false. *Record objects*,
  `ApiOverview/Database/DatabaseRecords/RecordObjects.html`, is one of the 1230
  pages the three manual indexes contribute at 14.3. It scores above zero on all
  three queries. It ranks 28, 13 and 11, and `limit` was 6. Nothing needs
  indexing.
- It scores 159 on each of the three, because `record` and `api` are the only
  terms of any of them it can match. The winners score 254 to 380 on words that
  have nothing to do with the subject. `has` reached `PasswordHasBeenResetEvent`
  through the compound split, `get` reached `getText` and `getEnv`, and `acces`
  reached `RecordAccessGrantedEvent`.
- The name of the thing asked about is the cheapest half of every query. Over
  those 1230 pages, 635 carry `api`, which weighs 0.66, because `ApiOverview` is
  a path segment of most of TYPO3 Explained. 32 carry `record`, which weighs
  3.65. Every further word outweighs both — `acces` 16 pages at 4.34, `get` 5 at
  5.51, `has` 3 at 6.02, `relati` 1 at 7.11. So each word the session added
  moved the answer further from the page it wanted.
- `record.header` contributed the term `record` and nothing else.
  `TermSearch::terms()` keeps a dot inside a word, and `stem()` cuts anything
  over six characters to six. So the most specific thing the session knew
  arrived as the least specific term in the query.
- Short queries reach it, which is what `D-ANS-003` already said and this run
  confirms with the ranks above. `Record API` alone returns *Record objects*
  third; `record objects` returns it first.
- The search covers a table of contents and nothing else: `title` at weight 4,
  `path` at 2, `manual` at 2. `Documentation::lookup()` fetches page bodies only
  after the rank, for the pages it has already chosen. So no query has ever met
  the text of a page.
- Nothing in the answer distinguishes these three calls from good ones. `status`
  was `answered` each time, with six results that carry titles, canonical URLs
  and excerpts. The session had no way to see that the one term that names its
  subject had contributed least of all.

## Decided

- **Step 4 of the ladder, wording.** The rule the session needed is here and
  reached it. "Several short English queries" stands in the tool description, in
  the `queries` description, and in six skills. It did not take, because nothing
  says what "short" buys. The index is a table of contents, and a longer query
  re-aims rather than refines.
- **The suggestion fails on its premise and stands on what it is after.** The
  index has the page, so the feedback shrinks to the two round trips, which is
  the part that is real.
- **Step 1a was not the answer, and a check settled that rather than an
  assumption.** `bin/cli hints:probe` reaches `frontend-records` and
  `fluid-templates` for these queries, and neither carries the Record API's
  accessors. That is a knowledge gap about TYPO3, and it is not what this
  feedback reports. The session got its answer from the manual page in the end,
  and what it lost was the two calls before it. A `knowledge/` entry that copies
  a manual page is what the manual lookup exists to avoid.
- **Queued rather than closed on the spot.** Both candidates below touch the
  declared schema or the answer shape of `typo3_documentation_lookup`, which
  [judging.md](../../documentation/records/judging.rst) puts on the reviewed
  side of the autonomous line.
- **Two candidates, and neither is chosen here.** Say it where the caller
  composes the call. The descriptions state that the match covers page titles
  and section paths, and that words beyond the subject re-aim the search. Or say
  it where the answer arrives. The result names what the match was on, or the
  shortest sub-query that still returns these pages. That is the shape
  `D-ANS-016` settled on for the changelog miss.
- Recorded against the answer rather than against the search. `R-ANS-007` is the
  score exactly as designed, and `D-ANS-003` keeps retrieval lexical. The gap
  sits between a correct rank and what the caller hears about it.

## Assumed

- That the second candidate is the one that survives deferral. `D-AUD-003`
  established that tool descriptions are not a channel at all where a client
  defers them. This session called the tool three times, so it had fetched the
  schema. That is evidence that the first candidate reaches a caller, from one
  session and one client.
- That a caller with the shortest sub-query that works in hand follows it. That
  is the same assumption `D-ANS-016` left open, and nothing here measures it
  either.
- That the ranks hold as the manuals grow. They come off the 14.3 indexes on one
  day, and a page added to `DatabaseRecords/` moves them.

## Wrong if

- The rule lands in the descriptions and in the answer, and a session still
  arrives with a five-word question and reports the same miss. Then the wording
  is not what failed, and the suspect that remains is the index itself. A table
  of contents cannot answer a question in the words of an API. A match over
  section headings within a page is the next lever.
- A feedback reports the opposite cost: a two-word query answered with six pages
  about the wrong subject, where a longer one would have separated them. Then
  the description sells shortness as a rule where it is a trade. The answer is a
  sentence about which words to keep rather than about how many.
- The same three queries reach *Record objects* after somebody retitles the page
  upstream. Then this was a title that did not match its subject rather than a
  property of the search. One page's wording carried the whole finding.

## Since then

Built as the second candidate, and the first is not built beside it. This
entry's own **Assumed** decided it. A description a client defers reaches
nobody, while every caller that gets an answer reads it. Its counter-evidence
says the first candidate would have reached one caller, not that it reaches the
next.

Every search result names what it matched on, as the stems the search looked for
and the field each turned up in. The answer says once that page titles and
section paths are all there is to match.

## Since then

The first **Wrong if** fired, and the read it produced moves the lever it named.
A session arrived with three short questions, read the match line, described the
rank correctly from it and reported the same miss. So the wording arrived, the
session took it, and it did not save the call.

What it does not confirm is the sentence after it. This entry expected the index
to be next and page bodies to be what a table of contents cannot carry. Two of
those three queries have no page body to match either.
