---
id: D-ANS-033
title: 'The review server is read anonymously'
date: 2026-08-03
status: confirmed
---

# D-ANS-033 — The review server is read anonymously

**`typo3_gerrit_lookup` reads review.typo3.org without a credential, so an empty
answer means nothing public names the issue rather than that nobody fixed it.**

A session asks the question before every core task, and no checkout can answer
it. `D-FBK-027` made it a tool rather than a recipe. This entry settles the
shape of the answer and the boundary it stops at.

## Evidence

- Four sessions in one week answered it by hand: `feedback/2026-08-02-144511`,
  `144848`, `145217`, `145230`. The cost is two round trips before a caller can
  read anything. That is the search, then the `)]}'` prefix the API opens with
  so a browser cannot execute the response as a script.
- Verified live while building. `issue: 110348` answers with change 95040,
  `[TASK] Deprecate AssetCollector media handling`, MERGED on `main`;
  `change: 89011` resolves to the phpunit raise of 2025-04-09.
- `issue: 105403` answers **empty**, and the checkout the call came from carries
  a patch for exactly that issue. That patch went up `%private`, which an
  anonymous read cannot see. The empty answer is therefore about the review
  server rather than about the world. A caller that reads it as "nobody has
  fixed this" took a true statement the wrong way.
- The second crawler is what showed there should be one. `Manual\Documentation`
  carried a `curl` block, and this source started as a copy of it before either
  saw use.

## Decided

- Anonymous, and read-only. The tool asks for no credential and stores none. So
  what a reviewer sees, what a private change carries, and every vote and CI
  result stay outside. `server-scope.json` names those rather than the sentence
  it carried before, "The server talks to nothing over the network". That had
  not been true since the manual index arrived.
- Three answers, not two. `answered`, `empty` where the server answered and
  knows nothing, and `unavailable` split into `source-not-answering` and
  `source-not-parseable`. A captive portal returns 200 with HTML, and a skip to
  the first bracket would parse a login page as a review.
- `message:<issue>` is the query. The issue number lives in the commit message,
  where `Resolves:` and `Related:` put it. So this asks "has somebody already
  fixed this" rather than "is there a change called this".
- The read moves to `Http\Fetch`, which both outside sources now go through.
  That is three seconds to connect, eight in total, three redirects, and this
  server's own user agent. It is a policy rather than a duplicated block, and
  the agent is the part that matters. Bot protection challenges browser-shaped
  agents and lets a plain client through, which is what the third source will
  need.

## Assumed

- That the anonymous REST API stays open. It is what the project's own web UI
  reads, so an outage is likelier than a policy change. Both come back as
  `source-not-answering`, which is honest but says nothing about which happened.
- That `message:` matches what a caller means. The lookup does not find a change
  that only mentions the issue in a comment rather than in the message. Nor one
  whose author forgot the trailer.
- That the XSSI prefix stays `)]}'`. The match is a fixed string. A Gerrit
  release that changes it makes every answer `source-not-parseable` rather than
  wrong, which is the failure direction worth a preference.

## Wrong if

- A run reads an empty answer as "no patch exists" and acts on it. The sentence
  is in the tool's own text and in this entry. If a caller still reads it that
  way, the answer needs to carry the private-change caveat as data rather than
  as prose.
- The review server starts to rate-limit or require a credential for the
  searches this makes. Then the tool answers `source-not-answering` so often
  that the call buys nothing, which is `D-FBK-027`'s own **Wrong if**.
- A second host needs a fetch policy this one cannot express. `Http\Fetch` has
  one timeout pair and one agent for everybody. The first host that needs
  otherwise is where that turns from a policy into an obstacle.

## Confirmed on 2026-08-03

The boundary held and what an anonymous read answers moved out by one field. A
core patch review credited the lookup with the fact that it held the patch set
that exists on the server. Neither half of the answer said which one that is. A
review of a superseded revision is wrong in every finding after it, and nothing
let the session notice.

The option that carries it works over the same anonymous path this entry
settled, verified on three handles with no credential. So the second **Wrong
if** did not fire. The cost is one option and a larger body.
