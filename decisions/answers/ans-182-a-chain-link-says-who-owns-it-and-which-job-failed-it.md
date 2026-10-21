---
id: D-ANS-182
title: A chain link says who owns it and which job failed it
date: 2026-10-21
status: open
coveredBy:
  - GerritTest::aChainLinkSaysWhoOwnsItAndWhichJobFailedIt
---

# D-ANS-182 — A chain link says who owns it and which job failed it

**A change read by name carries its owner, and every link of its chain carries
its owner and its label state. Where Verified rejects a patch set, the change or
the link names the core-ci jobs that failed it, read from git.typo3.org.**

## Evidence

- [`feedback/2026-10-16-074733-a-change-read-by-name`](../../feedback/archive/2026-10-16-074733-a-change-read-by-name-carries-no-owner-but-the.md).
  `typo3-core-patch-checkout` decides on "the answer's owner field", and the
  answer had none. The session read the owner off the REST API in two calls.
- [`feedback/2026-10-16-074733-chain-links`](../../feedback/archive/2026-10-16-074733-chain-links-carry-no-verified-vote-and-no.md).
  A rebase of 13 links needed to know which were red and why. It cost 13 REST
  calls for the labels and a GitLab read per pipeline. The session took the
  traces for closed, because the API answered 401.
- Read on 2026-10-21. Every change payload carries `owner`, with the name, the
  username and the address under `o=DETAILED_ACCOUNTS`. A query of
  `change:A OR change:B …` answers every link's `submit_records` and owner in
  one call. core-ci names its pipelines `<branch>.<change>.<patch set>`, so
  `pipelines?ref=` finds one without the review log. `jobs?scope[]=failed`
  answers anonymously. A job's `raw` address serves its whole log without a
  login: 94 KB for job 5959622, with the failing Playwright tests in it.
- The maintainer chose the failed jobs over the link states alone on
  2026-10-21.

## Decided

- `CoreCi` in `src/Contribution/` is the new source, beside `Gerrit` and
  `Forge`. It reads git.typo3.org and nothing else.
- The jobs come only where Verified stands at REJECT, and on a link only where
  it is still open. A green link costs no GitLab call.
- A failed job carries its log address. Nothing here parses the log, because a
  Playwright run and a PHPUnit run report failures in two formats.
- The comparison with `git config user.email` stays the caller's. The owner's
  address is in the answer, and the checkout skill says to hold it against
  that.

## Assumed

- That core-ci keeps the ref form `<branch>.<change>.<patch set>`. A change to
  it empties `ci` with an empty pipeline, which reads as a person's vote.

## Wrong if

- A session reads the log address and still cannot find the failing test in
  it. Then the answer owes the test names, read out of the log.
- A chain long enough to hold many red links makes the read slow enough that a
  session reports the wait.
