---
id: D-SKL-029
title: "Precedent is listed by the changelog's own axes"
date: 2026-08-09
status: open
coveredBy:
  - SkillTest::aPrecedentIsListedByTypeAndVersionBeforeItIsAskedForInWords
---

# D-SKL-029 — Precedent is listed by the changelog's own axes

**A review lists precedent by the changelog's own type and version with no query
at all. The checkout answers only what that listing cannot.**

The precedent bullet of `skills/typo3-core-patch-review` prescribes a title-word
query and, where the words miss, `Documentation/Changelog` in the checkout. Both
sessions that have followed it lost the entry their review turned on to the
words and found it by hand. The same tool returns it in one call with no query
at all.

## Evidence

- `feedback/2026-08-08-224429` reviewed Gerrit change 95179, a BUGFIX that makes
  stdWrap's `override` apply the value `0`. Its question was whether such a
  change owes an Important entry on an LTS line.
  `typo3_changelog_lookup(query: "stdWrap override")` came back empty. The
  session settled it with
  `ls Documentation/Changelog/13.4.x/ | grep -i important`, a read of the file,
  and `git log --all --grep 106401`.
- Re-run on 2026-08-09 from `/home/benji/projects/typo3-cms` through
  `bin/typo3-dev-companion`. The query answers the same: no entry carries both
  words, "stdwrap" reaches 43 entries and "override" 22.
- The same call with `type: important`, `version: 13.4` and no query returns 20
  entries in one call.
  `13.4.x Important: Treat 0 as a defined value for nullable datetime fields (#106401)`
  is eighth among them. It carries the stated title and the index tags, which
  the session's `ls` did not print.
- The session names that call itself. It did not make it because
  `skills/base.md` step 5 teaches the mode with no query. That step is the
  deprecation sweep, framed on `type: deprecation` throughout. That step has a
  condition, that the task produces a change, which a review that stops at
  findings does not.
- It is the second session to lose a precedent the same way.
  `feedback/2026-08-01-115112` reported that the lookup missed the precedent of
  a public-method removal and the grep found it. The bullet came from that
  report on 2026-08-03 and
  `SkillTest::aPrecedentIsListedByTypeAndVersionBeforeItIsAskedForInWords` holds
  it. It names the title-word query and the checkout, and no listing.
- The commit keyword the feedback asks to index by is not in what the tool
  reads. `Changelog::directory()` reads the core package's own
  `Documentation/Changelog`, which ships no git history. The entries above the
  installed major come from docs.typo3.org, which publishes the RST and no
  commit at all.
- The change kind is one call that exists away per candidate.
  `typo3_forge_lookup("106401")` answers `Bug`, and the commit in
  `/home/benji/projects/typo3-cms` is
  `[BUGFIX] Treat 0 as a defined value for nullable datetime fields`.
- What a listing costs depends on the line. On 13.4 the four types hold 0
  breaking, 7 deprecations, 1 feature and 20 important entries. On 14 the same
  two types hold 36 and 99, against a `limit` that defaults to 20 and caps at
  50.

## Decided

- **Step 4 of the ladder, wording.** The tool answers this today, the review
  called it, and the tool's own `query` field states the mode it needed. What
  routed the session past it is the step it stood in, which offers words and
  then the checkout.
- **Queued rather than closed on the spot.** The bullet is a skill contract
  installed into somebody else's project, and the assertions above hold the
  sentences that would move.
- **The step names the listing before the checkout, not instead of it.** A
  released line publishes few entries per type, so a reviewer can read the whole
  of what the core did of that kind. An open major is not, and there the tag is
  the bound `D-SKL-003` already established.
- **Indexing the changelog by the commit keyword is not the route.** The source
  the tool reads carries no commit. So the keyword would have to come from a
  checkout most callers do not have or from a Forge round trip per entry. A
  reviewer needs it for the handful of entries its read picked rather than for
  all of them.
- **`skills/base.md` is not where the answer lives.** Generalising step 5 would
  put a listing every task pays for into a sweep that step already conditions
  and bounds.

## Assumed

- That the Forge tracker states the change kind the commit keyword carries.
  Verified on #106401 alone, which is a coincidence rather than a rule. It is
  what the todo reads before the step sends a reviewer there.
- That a reviewer who has the type and the line can pick the precedent out of a
  listing by title. That is what the session did with 20 file names and no
  titles at all.

## Wrong if

- A review lists a type and a version, gets the cap back, and argues from the
  nearest entry it happened to see. Then the listing owes the tag on every line
  rather than on an open major, and the step says so.
- A third session with the reworded bullet still settles precedent by grep. Then
  the words were not what cost the first two, and the skill is not where the
  answer lives.
- The tracker turns out not to state what the commit keyword says. Then the
  change kind is a read of the commit, the checkout is what holds it, and the
  step routes there for that half.

## Since then

The step now names three sources in order: the listing by type and version, the
title words, the checkout. It says which line to list, which is the old bullet's
refusal of a filter set to the branch under review.

The assumption does not hold, and the third **Wrong if** is what the step says
instead. Measured over 128 changelog entries read twice, from the tracker and
from the keyword of the commit that added the file. The two agree on 101 and the
disagreement runs both ways. So the tracker answers what type the reporter
filed, and the commit answers what the change was. A review that argues an
earlier fix of this kind owed an entry reads the second. The security entries
have no tracker answer at all. The source returns 401, which reads as an outage
and is permanent, and the step says so in one sentence.
