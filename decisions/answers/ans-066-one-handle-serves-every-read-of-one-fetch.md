---
id: D-ANS-066
title: One handle serves every read of one Fetch
date: 2026-08-08
status: open
coveredBy: []
---

# D-ANS-066 — One handle serves every read of one Fetch

**`Fetch` keeps one curl handle for the life of the instance and sets every
option on it per read. So the connection under it serves every read.**

A manual search makes ten reads of one host, four inventories and up to six
pages. Each one opened a TCP connection and a TLS handshake it threw away. The
measurement of what it saves ran with `curl(1)` before anybody knew whether
PHP's curl does the same thing, which is what this settles.

## Evidence

- Measured 2026-08-08 through `Fetch` itself, curl 7.81.0 on PHP 8.3.23. The
  corpus is the sixteen `objects.inv` of the four manuals at the four covered
  versions, three rounds in turn. A handle per read is 1454 ms, one handle 442
  ms, a share handle with the connection pool 438 ms. Per read that is 90.9 ms
  against 27.6 and 27.4.
- So keep-alive survives PHP's curl. The `curl(1)` measurement the card carried
  was 98 ms against 21 ms per read, which is the same effect at the same scale.
- A share handle and a reused handle are within 1 % of each other, which is
  under the spread between rounds.
- The changed class had the same measurement afterwards: 450 ms over the same
  sixteen, level with both prototypes.
- A check against the host, rather than an assumption, covered two properties of
  a handle in reuse, on one handle and in this order. A read with no header
  answers 200 and 43938 bytes. The same URL with `If-None-Match` answers 304
  with an empty body over the same connection. A third read with no header
  answers 200 and 43938 bytes again. So revalidation works on a reused handle
  and a request header does not survive into the next read.

## Decided

- One handle per instance, created on the first read. A caller that wants a
  fresh connection takes a fresh `Fetch`, which is what every test already does.
- The share handle failed. It buys nothing measurable and it keeps the
  handle-per-read that the other variant removes, so it is one more concept for
  no gain.
- Every read sets every option a read varies, rather than resets them between
  reads. `curl_reset()` would throw away the connection along with the options,
  and the connection is the thing to keep.
- Reuse is per instance and not per session. `Documentation` builds one `Fetch`
  and makes all ten reads through it, which is the whole of what the card asked
  for. A static handle would reach across lookups. It would decide, for the
  tracker and the review server too, that this server holds a connection open to
  a host. That host is one it does not read right now.

- Nothing runs over it. The transport seam returns before the code reaches curl,
  so no unit test in this repository crosses the handle at all. That is the same
  limitation `D-ANS-065` records for revalidation, and the reason both have a
  measurement against the host instead.
## Assumed

- That the ten reads of one lookup are close enough in time for the host to
  still hold the connection. docs.typo3.org did over the sixteen reads measured,
  and a lookup is faster than that.
- That no caller shares one `Fetch` across hosts often enough to matter. Curl
  keeps a pool per handle rather than one connection. So a second host is a
  second entry in it rather than a re-dial of the first.

## Wrong if

- A read answers with the previous read's body or status, which is what an
  option that survives a read looks like from outside.
- What it saves disappears against a host that closes the connection per
  response. That would show as a manual lookup no faster than before this
  change.
- A long session holds a connection to docs.typo3.org open between lookups. It
  must not: the handle goes with the `Fetch`, and the `Fetch` goes with the
  `Documentation` that built it.

## Since then

Read on 2026-08-23, and the two halves a read can settle still hold. The handle
is a nullable instance property the first read creates and never static. Each
call sets every option a read varies on, and nothing resets it. That is what
keeps a header from a survival into the next read while the connection under it
stays.

The third **Wrong if** is what the lifetime says rather than a risk left open.
The manual source builds its own reader per lookup, so the handle exists for one
and dies with it.
