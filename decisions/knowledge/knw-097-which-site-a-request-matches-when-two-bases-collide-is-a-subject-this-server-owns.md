---
id: D-KNW-097
title: 'Which site a request matches when two bases collide is a subject this server owns'
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::whichOfTwoCollidingSiteBasesWinsIsStated
---

# D-KNW-097 — Which site a request matches when two bases collide is a subject this server owns

**Nothing below `knowledge/` says that a site base with a host beats one
without.**

So a site on `base: /` loses every request the moment a second site names the
host. The feedback goes to the queue at `normal`. The corpus carries the
matcher's other direction, a base that names a host nobody is on matches
nothing. It states the bare path as what "matches every host", which is the read
a caller with two sites has to arrive at the opposite of.

## Evidence

- Re-run on 2026-08-18 against the corpus as it is now. `bin/cli hints:probe` on
  the feedback's own query reaches `extbase-arguments` and nothing else.
  `"two sites collide which site does a request match"` reaches the same single
  stray.
- The wrong neighbourhood reproduces. `"frontend returns 404 at the site root"`
  returns `initial-content-references` first on `text only(224)`. That is the
  hint both feedback of this session followed into an import that was never
  there.
- The mechanism is half written, in the fifth statement of `installation-boot`.
  `SiteMatcher` puts the base's host, scheme and port on the route as
  requirements. So a clone served under another host matches no site and gets a
  `NullSite`. The sentence beside it is the one that misleads under collision.
  That is "a base that is a bare path carries no host requirement and matches
  every host". A match on every host is what loses the sort, not what wins it.
- That statement is unreachable from the symptom.
  `"site base host requirement SiteMatcher NullSite"` reaches it on
  `text only(105)`. Its curated phrases are boot-a-clone phrases from "fresh
  clone" to "ddev import-db", and none of the three probes above reached it. PHP
  was the selected domain on all three, so the hint was a candidate and lost
  lexically. That is the curation half of `D-ANS-081`'s third **Wrong if**, from
  the corpus side.
- The claim about TYPO3 holds where the feedback puts it.
  `BestUrlMatcher::sortMatchedRoutes()` sorts fallbacks to the end and then
  compares `MatchedRoute::getHostMatchScore()`, higher first. That is `1` where
  the route matched a host and `0` where it carried no host regex. Both classes
  are present on `.checkouts/12.4`, `13.4`, `14.3` and `main`.
- Two independent failures in one session from the one rule. The second was the
  own repair of the session that reported, a derived base with the plain path
  `/probe` while another site held the host. That is why it filed the report
  rather than noted it.
- A report beside it names the same delivery failure from a different mechanism.
  `feedback/2026-08-18-074545` reaches `initial-content-references` for a site
  `CreateSiteConfiguration` wrote, and its card in `todo/open/` has no judgement
  yet.
- `feedback/2026-08-18-074606` is the third from that session and says the
  domain has no skill owner. Its card is in hand as
  `todo/progress/2026-08-18-124747`, and that work reads the site-configuration
  hints rather than writes them.

## Decided

- Step 1a with a curation half, and queued rather than closed on the spot. What
  the statement says about TYPO3 needs a read, which
  [`judging.rst`](../../documentation/records/judging.rst) puts on the todo's
  side of the line whatever its size.
- `normal` rather than the `low` the card arrived at. One rule cost one session
  two failures, and a second feedback from the same session lands on the same
  wrong hint from another mechanism.
- Not `high`. One session wrote both reports, and the gap is a statement rather
  than a capability.
- A hint of its own, curated on the symptom, rather than a sixth statement
  inside `installation-boot`. Which site answered a request is not the boot
  sequence, and `D-KNW-092` made the same cut for the 500 case one hint earlier.
  That hint's curated phrases are what keep it off an install query, and a
  symptom loaded onto them costs there.
- Two halves, because the second is what names the failure. Which base wins is
  the mechanism. A site with a deleted or hidden root page still wins the match
  and then answers "The requested page does not exist". That is what separates a
  site-selection problem from the slug problem that message reads like.
- `initial-content-references` is not what gets the repair. It is right about
  the import it describes. Whether it owes a neighbour line that points at the
  new hint is the todo's to decide against `D-KNW-087`.
- `feedback/2026-08-18-074545` keeps its own card. It is the same gap in the
  same place by a different core mechanism. A fold would make a judgement for a
  card nobody has claimed. This entry names it so the run that judges it can
  carry both.
- Neither archived nor trimmed. Nothing answers any part of the feedback today.

## Assumed

- That the sort is what decided the request of the session that reported. This
  run read the class, not a request against that installation, and nothing here
  can reach it.
- That the rule holds unbound. Both classes exist on all four checkouts and this
  run read the score on `14.3` alone. So the statement may need a bound the todo
  establishes.
- That the message the feedback quotes is the one a deleted root page produces
  rather than one of several the page-not-found handler can render.
- That one session wrote this feedback and the ones beside it. They share a
  directory, a model and three quarters of an hour, and nothing in a feedback
  records a session.

## Wrong if

- The read finds something settles the collision before `BestUrlMatcher`, a
  route order in `SiteMatcher`, or an earlier return that never reaches the
  sort. The statement would rest on a class read out of its call path.
- The statement lands and a root-404 query still returns
  `initial-content-references` first. The lever would have been that hint's own
  curation rather than a hint beside it, and this is step 4 of the ladder.
- The new hint comes back on a query about an installation that works, or
  displaces `installation-boot` on a boot query. Its phrases would be the
  general words rather than the symptom.
- `12.4` or `13.4` scores or orders the matched routes differently. The
  statement is version-bound and the second **Assumed** was the wrong way round.
- A deleted root page turns out to answer something other than the quoted line,
  or the same as an absent slug. The half that makes the hint worth its reach
  would be wrong.
- The next session with two sites in collision reports it after the hint lands.
  What it lacked was which site had matched rather than why. The gap would be in
  what `typo3_project_describe` reports, the call this session names as the most
  useful thing it made, and not in the corpus.

## Since then

The hint exists and the read settled four of the six **Wrong if**. The sort is
on the call path, so the first does not hold. What it sorts by is one comparison
earlier than this entry had it. Each site's own entry route goes last, so the
language routes collide and the matcher compares the host before the path
matched. That is the part worth a statement: a base that names the host beats a
more specific path.

The second **Assumed** was the wrong way round and the statement is unbound. The
fifth **Wrong if** is half true and improves the hint. A deleted root page and a
mistyped slug produce the same sentence, and a hidden one differs by an
exclamation mark. So the message carries one discriminator and never says which
site answered. The third does not hold as measured, and the routing half went
the same way. Two surfaces named three causes and lacked this one.
