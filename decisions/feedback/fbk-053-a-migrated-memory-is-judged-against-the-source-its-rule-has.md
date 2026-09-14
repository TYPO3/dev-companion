---
id: D-FBK-053
title: 'A migrated memory is judged against the source its rule has'
date: 2026-09-01
status: open
readings:
  - 2026-09-02
---

# D-FBK-053 — A migrated memory is judged against the source its rule has

**A migrated memory gets its judgement against the source its rule has, and not
against what the memory asserts.**

Read as reports they are fourteen gaps. Read as a migration they are three
kinds, and the kind decides the judgement. What the corpus already says, what a
source in the core settles, and what only the maintainer can.

## Evidence

- The batch is one session: `/home/benji/projects/typo3-cms`, one model, from
  21:03:31 to 21:06:13, each with the local file it came from. Every observation
  opens with "Migrating a local memory into the server".
- Four of them already have an answer, and one is the memory a hint came from a
  month ago. `record-system-properties` has said the Record API shape since
  2026-08-14, `fluid-object-access` the accessor order since the same day.
  `backend-routing-internals` says the three forms of an AJAX route.
  `core-static-analysis` says that a named argument fails in a method call and
  passes in a constructor. That is the value-object rule the memory states as
  taste.
- The named-argument rule has a source and the corpus names it:
  `NamedArgumentUsageRule` under `custom.namedArguments`, in
  `.checkouts/main/Build/phpstan/` and `14.3` and not in `13.4`.
- The rule against `empty()` rests on what the construct is rather than on a
  package. It is a falsy test, so six values satisfy it and the condition states
  none of them. `phpstan/phpstan-strict-rules` rejects the call for that reason
  and is in no core `composer.json` and no `Build/phpstan/*.neon`. So what the
  statement may not say is that the core's own analysis refuses it.
- The sign-off memory is older than the rule it contradicts. The maintainer
  reversed that rule on 2026-08-25 on the board's statement (`D-KNW-125`,
  `R-KNW-075`), and `core/contribution/commit-messages` has required the trailer
  since.

## Decided

- **The maintainer ruled on the class on 2026-09-01**, asked as one question
  rather than eight: these are rules and not one person's taste. Named arguments
  and the prose commit body are the core's, and no `empty()` comes from the
  strict rules. Minimal annotations, minimal diffs and a test comment that
  describes its case hold for code in general. Where the SVG assertion belongs
  is the one they left open.
- **Each card sits in the queue against the source its rule has**, because that
  is what will verify the statement and what binds it. A core check for the
  first two, a package the core does not run for `empty()`, and nothing but the
  practice for the general three.
- **A card whose subject the corpus already carries goes to the archive**, and
  the commit names the hint and the probe that reached it. Four of the fourteen,
  and none of them needed a read of TYPO3.
- **The sign-off half gets an answer rather than a second look.** `R-KNW-075`
  reserves that change for the maintainer, who confirmed on 2026-09-01 that the
  rule stands and the memory predates it.

## Assumed

- That a memory file states a rule the way its author last understood it. That
  is why the reversal of 2026-08-25 is in none of them. Every card in a
  migration is therefore read for its date as much as for its claim.

## Wrong if

- A card queued here turns out to have no source at all. The statement it
  becomes is the memory copied into `knowledge/` with a hint's authority.
- Another migration arrives and the same four subjects arrive again. That would
  make the lever the record tool rather than the judgement: it never tells a
  caller to ask whether the answer is already there.
