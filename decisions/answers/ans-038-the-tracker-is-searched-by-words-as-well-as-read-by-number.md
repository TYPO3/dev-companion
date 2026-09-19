---
id: D-ANS-038
title: The tracker is searched by words as well as read by number
date: 2026-08-03
status: open
coveredBy:
  - ForgeTest::aMissNamesTheEnumerationAsACallToCompose
  - ForgeTest::aMissNamesWhatEachWordReachesOnItsOwn
  - ForgeTest::aMissOutsideTheBoundAsksTheTrackerNothingFurther
  - ForgeTest::aSearchThatAnsweredIsNotCountedWordByWord
---

# D-ANS-038 — The tracker is searched by words as well as read by number

**`typo3_forge_lookup` takes a word query beside the issue number. A session
asks before a patch whether other issues describe the same bug, and no number
answers that.**

`D-FBK-027` built the issue read and `D-ANS-033` the review search, both out of
the same cluster of sessions. What none of them covers is the step those
sessions took between the two: the search for the issues nobody had linked.

## Evidence

- Re-run on 2026-08-03 against the server as it is. `typo3_forge_lookup` with
  `issue: 105403` answers subject, tracker, status `Under Review`, target
  version, both relations and all six comments. That includes the two maintainer
  verdicts the session that reported it called the decisive evidence.
  `typo3_gerrit_lookup` answers `empty` with the private-change caveat. The half
  of `feedback/2026-08-02-144511` that asked for those has landed, and this
  entry is what remains of it.
- Two sessions searched the tracker by words from the same task shape:
  `feedback/2026-08-02-144511` and `145217`, whose task text was "find similar
  issues". Both used `/search.json?q=<terms>&issues=1`, and neither could have
  reached it from an issue number.
- `relations[]` answers a narrower question. It carries what a person linked:
  #105403 names #99203 and #105953, and `145217` reached #100696 through them.
  So it cannot see an issue nobody linked, which is what a duplicate is until
  somebody recognises it.
- The endpoint answers JSON to this server's own agent, measured the same day.
  That is `results[]` of `id`, `title`
  (`Bug #105403 (Under Review): f:image and cache busting issue`), `type`, `url`
  and `description`, in an envelope of `total_count`, `offset` and `limit`. A
  query nothing matches answers `total_count: 0`. It clears `D-ANS-034` without
  a parser, and tracker and status arrive without a second call per hit.
- One query is not the answer. `cache busting` returned 15, `f:image` 134 and
  `image cache` 279 on 2026-08-03, three wordings of one question. `145217`
  reports four wordings that found four different sets.

## Decided

- Built, and as a second way into `typo3_forge_lookup` rather than as a tool of
  its own. One subject, one verb, and `lookup` is already the verb whose answer
  is the entries that match, with nothing as a legitimate one.
  `typo3_gerrit_lookup` carries the same exclusive pair, stated where the caller
  composes the call (`D-ANS-012`).
- The boundary is identity and triage state per hit: issue number, subject,
  tracker, status and the URL a person reads it at. The description is not
  carried — a caller that wants the issue reads it by number, which is the other
  half of the same tool.
- Nothing here ranks the hits. The order is the tracker's own and the answer
  says which query produced it. So a caller with a narrow set asks again in
  other words rather than concludes that nobody reported anything else.
- Writing stays outside. A comment, an assignment and a reopen need a
  credential, and the reopen is where that bites. `145217` records that a closed
  issue has to reopen before a change can go up against it. That is a person's
  step rather than an absent call.

## Assumed

- That the anonymous search stays open, like the two endpoints already read. It
  is what the tracker's own UI searches with, so an outage is likelier than a
  policy change. Both arrive as `source-not-answering`.
- That the caller's question is which other issues mention this, rather than
  which one is the duplicate. Nothing here decides duplication, and a caller
  would believe an answer that looked like it did.

## Wrong if

- A caller reads an empty search as "nobody reported this". It is the failure
  `D-ANS-033` names one source over, and worse here. A report in different words
  is invisible to a word match rather than merely private.
- Callers routinely page past the first answer. Then the hits needed a rank, or
  a filter on tracker and open state, and a limit was the wrong knob.
- The two ways into the tool are no longer one question. A search that grows its
  own filters and its own answer shape is a second tool under the first one's
  name. `typo3_gerrit_lookup` is where the same split would show first.

## Since then

A judgement read a feedback against this entry and its own card retired into it.
That is the tracker half of the cluster, all of it answered here except the
search. The re-run says so. One call answers what the feedback established in
four, the three notes it called decisive included. It also answers the route it
found to the patch when one field was empty. The tool never requests that field,
so the trap it warned about is not reachable.

The rest needed no entry, because it is the fetch policy's rather than this
answer's.

## Since then

The empty search sends the caller back into the loop it is already in. A session
that wanted to know whether a defect was on the tracker spent eight wordings.
Two matched nothing and six returned something else. The one relevant issue
arrived as a relation of a hit. What settled it was the ninth call, an
enumeration that answered all 26 open issues of that area at once.

Re-run, the empty query offers two things to do next and neither is the
enumeration, while the enumeration carries the pointer the other way. So the
cross-reference stands only in the direction where the caller is not stuck.

## Since then

Written on 2026-08-24. The miss of a search now names the enumeration as a call
to compose, in the caller's own words. The area's description carries the
duplicate question beside the browse questions it already had. The test holds it
through the registry, which is what the reader seam is for. The tool builds its
own client, so a test of its text half had nowhere to hand a transport in.

The feedback next to it is half of this rewrite. It reports four words that
answer nothing where one of them answers five, and asks for a statement of the
combination rule.

## Since then

**A miss asks each of the caller's words on its own. The any-word re-read the
section above proposed answers something else, measured.** That measurement ran
on a query one of whose two words nobody had written. On the four words of the
session that reported it, it does not hold. The union answers 14673 where the
same URL without it answers none, ordered by issue number. So its first page is
the newest issues with the commonest word, and none of the five the caller was
after is in it.

So the call is spent and the question is not answered. One read per word is what
answers it, which is what the feedback asked for in the first place.

## Since then

**The miss delivers what the empty answer owed and promises more than the call
it names can carry.** A session that wanted to know whether a defect was on the
tracker before it filed one tried three wordings. It got three empties, and had
a negative it could not rely on. It asked for the enumeration by name and for an
empty answer that says what it does and does not establish. The two sections
above built both, before the judgement.

Re-run in the feedback's own words, the empty answer now opens with "which is
not that nobody reported it". It counts each word, names the narrowest, and
names the call to compose.

## Since then

Read on 2026-09-19 against `feedback/2026-09-18-093205`. A triage found its
issue family in one read of 90056, relations and notes included, and settled
that no fourth issue exists with the backlog by category. What cost it was the
lexical AND: "pagetree filter reset highlight" answered zero, and the session
proposed that the answer drop the rarest word itself. Measured the same day:
every three-word subset of that query answers 1 to 6 issues and none of them is
90056, and "highlight" alone answers 420 without it. The wording that reached
it, "pagetree search clear selected", is the issue's own vocabulary, which no
subset of the first query carries. So the zero answer stays as `f1e047d1` built
it, the per-term counts and the backlog route, and the suggestion has its answer
in the measure.

