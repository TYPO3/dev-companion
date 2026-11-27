---
id: D-ANS-197
title: An issue answer is built from the first provider that answers
date: 2026-11-27
status: open
coveredBy:
  - IssueTrackerTest
---

# D-ANS-197 — An issue answer is built from the first provider that answers

**`typo3_forge_lookup` reads its issues through a list of providers, Forger
first and forge.typo3.org second. A provider reads and normalises. The
`IssueTracker` asks them in order and builds every answer from their rows. A new
provider is one class that implements `IssueProvider` and one entry in
`IssueTracker::PROVIDERS`.**

The maintainer asked on 2026-11-27 for a provider for Forge and later Gerrit.
They named Forger as the preferred source. They also asked for a tracker that
can rebuild the answer from the data of any provider.

## Evidence

- Forger is the index TYPO3 GmbH keeps of forge.typo3.org and review.typo3.org.
  It serves a public JSON API at `/api/v1` with an OpenAPI document at
  `/api/doc.json`. forger.typo3.org redirects to forger.typo3.com with a 301.
- On 2026-11-27, Forger answered an issue list in 0.13 seconds. Its newest
  issue and change were minutes behind review.typo3.org. The same issue list
  from forge.typo3.org answered nothing in 20 seconds.
- A read of one issue on Forger carries the description, the comments with a
  bot flag, the relations, the files and the changes with their state. The
  tracker needs a journal read and a review-server query for the same.
- Forger takes one value per filter. `category[]` answers `The parameter
  "category" takes a single value.` Several numbers in `q` find the issues that
  mention them: "99203 105953" answered neither of the two.
- Forger's `q` finds any of the words with stems and ranks by relevance:
  "cache busting image" answered 6715. The tracker's search needs every word.
- Forger serves no list of areas, versions or members. The category counts of
  `breakdown=1` over every issue name 56 areas.

## Decided

- The provider owns the reads and the host's quirks. The tracker owns what a
  caller sees: the handles, the citations, the cited code, the counts, and the
  sentences about them. So two providers answer in one shape.
- The tracker asks the next provider where a read did not answer. For one issue
  it asks on a miss too, because an index can be minutes behind.
- One answer comes from one provider. The reads that fill it go to the provider
  whose first read answered, so no answer mixes two copies of the tracker.
- `source` names the host that answered. Every issue URL still points at
  forge.typo3.org.
- Forger reads one issue per relation, per citation and per row of a page. A
  read takes a tenth of a second and stays in the cache.
- A word that names several areas is one Forger read per area. An issue sits in
  one area, so the totals add up.
- A search says which of the two semantics answered it. The tool description
  names both.
- The new-issue form keeps reading the tracker. Its area and version ids exist
  only there. `typo3_gerrit_lookup` keeps reading the review server; a change
  provider is the next step.

## Wrong if

- Forger falls behind the tracker by more than a session notices. Then a fresh
  comment or status is missing from the answer, and the first provider needs a
  freshness check.
- Forger answers 429 under the reads a page of rows makes. Then the rows need a
  bulk read, which Forger does not serve on 2026-11-27.
- A caller acts on a Forger relation name such as `blocked` or `copied_from`
  where the tracker said `blocks` or `copied_to`. Then the provider maps them to
  the tracker's words.

## Since then

The change provider came on 2026-11-27 as `D-ANS-198`. `typo3_gerrit_lookup`
reads Forger first for an issue's changes, a search and a plain backlog.
`Gerrit` reads the issue fields of a commit message through `IssueTracker`.
