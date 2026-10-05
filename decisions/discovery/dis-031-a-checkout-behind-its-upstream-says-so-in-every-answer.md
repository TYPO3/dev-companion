---
id: D-DIS-031
title: A checkout behind its upstream says so in every answer
date: 2026-10-05
status: open
coveredBy:
  - UpstreamTest
---

# D-DIS-031 — A checkout behind its upstream says so in every answer

**A server started from a git checkout asks GitHub at the start how far its
commit is behind `main`. While it is behind, every tool answer opens with the
count and the pull that ends it.**

## Evidence

- `feedback/archive/2026-10-05-115125-the-server-does-not-check-its-upstream.md`.
  The maintainer asked for it in the session that filed it: the server is a
  rolling release, and nothing told a session that its checkout had fallen
  behind.
- The maintainer chose on 2026-10-05: GitHub's compare API rather than a
  `git fetch`, one read at the start kept an hour, and the notice in front of
  every answer beside a section in `typo3_server_scope`.
- Read on 2026-10-05: `compare/<commit>...main` answers `ahead_by` 3 for a
  commit three behind, in 0.22 s, and 404 for a commit the upstream does not
  have.

## Decided

- The commit comes from the git directory as files, a worktree and `packed-refs`
  included. No git runs.
- The read waits two seconds at most and happens at the start alone. The answer
  is kept an hour in a file every session of the checkout shares, and each tool
  call reads that file rather than the network.
- `unavailable` is never reported as current. The section carries the time of
  the last read that worked, and the answers say nothing.
- A commit the upstream lacks, a local one, is `unknown`. Nothing is counted
  against it.
- The server reports and never pulls. A pull changes the guides a session
  already follows.
- An install without a git directory is `not-a-checkout`. Its update is
  Composer's, and the question waits until the package resolves from a registry.

## Wrong if

- GitHub's rate limit refuses the read often enough that a session reports the
  section `unavailable` while it was behind.
- A session reports the notice for a checkout that was current.
