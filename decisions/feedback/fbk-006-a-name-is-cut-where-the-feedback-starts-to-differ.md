---
id: D-FBK-006
title: A name is cut where the feedback starts to differ
date: 2026-08-01
status: confirmed
coveredBy:
  - FeedbackTest::notesThatOpenAlikeAreNamedAfterWhatTellsThemApart
---

# D-FBK-006 — A name is cut where the feedback starts to differ

**A feedback whose slug another feedback already carries takes its name from the
first word at which the two observations differ.**

Not from a counter, and not from a longer cut of the same first line.

The name has always been the first 48 characters of the observation. That is the
right end of the text, since an agent writes what happened first. It fails
exactly when a session files a series, because a series announces itself before
it says anything.

## Evidence

- 25 of 56 open feedback carried one of three names, 17 of them
  `debrief-of-the-typo3-14-testimonials-session`. Their observations shared nine
  words before the first that differed, and the shared part was 57 characters.
  That is longer than a name has room for, so no cut of the first line could
  have separated them. The eight feedback filed in thirteen seconds that morning
  were about Extbase, pid semantics, functional-test databases, the cache flush
  and four other things.

## Decided

- On a collision, read the first line of the feedback that already carry the
  name. Skip the words they open with in common, and cut the slug from what
  remains. The tool reads only the feedback that collide, so a record still
  costs one directory listing in the ordinary case. The counter stays as the
  fallback for a feedback that has nothing else to say. What this rejects is a
  title from the agent. That is one more field in a tool whose parameters are
  already its documentation. The field a session fills last is the one it fills
  worst, and the observation is what it came to write.

## Assumed

- That the first difference is the part worth a name. Where a series carries
  numbers, "5. Recommended: add en.xlf files", the number leads and the name
  starts with it. The words after it still say what the feedback is about, so
  the name is ugly rather than wrong.
- That the first feedback of a series may keep the shared first line. Nothing at
  its arrival says a series will follow. A later rename would change a name that
  a commit or a `**Serves:**` line may already have quoted.

## Wrong if

- Feedback start to collide on a slug with no shared first line at all, two
  different sentences cut to the same 48 characters. Then the skip of shared
  words changes nothing and the counter does the work again. Or a series turns
  out to differ only in its middle. Then the first difference is a word like
  "second" and every name in the group is a numeral.

## Confirmed on 2026-08-22

Neither **Wrong if** fired over 457 archived feedback. One pair shares a slug
and shares its first line as well, and the three numerals still say what they
are about after the number.

That pair is the limit of the statement rather than a breach. The uniqueness
read covers the directory under write rather than the archive, and the first of
the two closed on its own day. The names stay distinct by timestamp. A read of
the archive too would cost every record a second listing to separate two files
in 457.
