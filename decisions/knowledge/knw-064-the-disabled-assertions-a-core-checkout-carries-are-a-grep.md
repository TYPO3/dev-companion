---
id: D-KNW-064
title: 'The disabled assertions a core checkout carries are a grep'
date: 2026-08-08
status: open
coveredBy:
  - SkillTest::aTriageLooksForTheAssertionTheSuiteAlreadyCarries
---

# D-KNW-064 — The disabled assertions a core checkout carries are a grep

**A core checkout carries nine commented-out assertions marked as known
failures, in four files of one subsystem.** That is not a corpus a tool can
search over. The step that finds them is one grep in the skill that needs them.

## Evidence

- `feedback/2026-08-07-233543`. The best outcome of a triage is "the project
  already knows, here is the disabled assertion and here is the fixture". The
  session reached it for Forge 15984 in four steps of hand research. It found
  three data-provider rows in
  `typo3/sysext/frontend/Tests/Functional/SiteHandling/SlugLinkGeneratorTest.php`
  commented out with `// @todo Fails, not expanded to sub-pages`. They sit over
  a `Fixtures/SlugScenario.yaml` that already models a restricted page with a
  subpage. No lookup pointed at it.
- Counted in `.checkouts/main` on 2026-08-08. A commented-out assertion or data
  row with a known-failure `@todo` is **nine lines**, all under
  `typo3/sysext/frontend/Tests/Functional/SiteHandling/`. Six are the 15984 case
  across `SlugLinkGeneratorTest.php`. Three are "Currently fails since cHash is
  verified after(!) redirect to page 1100" across three files.
- The wider sets do not hold what the tool would be for. `markTestSkipped` and
  `markTestIncomplete` come to 50, and the reasons are nearly all the machine
  rather than a defect. No APCu, no Redis, no ImageMagick, Windows, a
  case-sensitive filesystem, non-Composer mode. Two of the fifty are defects the
  project knows.
- `@todo` inside `Tests/` comes to 208, and a sample says most are notes about
  the fixture or the test. "It would be better to not re-use sys_file 1 here",
  "do we need to discard the references manually?", "wrong assertion". A search
  over them answers a report with test-writing caveats.
- The text of the nine is specific enough to recognise once found and not
  specific enough to match a report against. "Fails, not expanded to sub-pages"
  and `extendToSubpages` share no word.

## Decided

- **No tool.** The todo asked for the price before the build, and the price is
  the finding. Nine lines is what `grep -rn "@todo" <sysext>/Tests` answers in
  one call. An index over them would be a surface, a schema and a contract test.
  All three would stand on a corpus smaller than one of its own answers.
- **The step goes where the work is.** `typo3-core-issue-triage` says to look
  for the test the core already wrote and switched off before the session writes
  one. It names the grep, and says which subsystem to narrow it to. The reason
  text is what identifies a hit, and the reader already stands in the checkout.
- **The skill names `markTestSkipped` as the thing it is not.** Fifty of them
  against two that are about a defect is a ratio that sends a session into the
  wrong fifty. The skill says so rather than leaves it to the session to
  discover.
- The one case measured also goes into the corpus. `frontend-access-restriction`
  names the rows and the fixture, so a caller who never opens the skill still
  reaches them from the subject.

## Assumed

- One checkout stands for the branches. Counted on `main` only; a maintained
  branch carries the same tests and nobody has counted there.
- The nine are the whole shape. A known failure parked some other way, a
  provider case removed outright or an assertion loosened with a comment above
  it, matches no pattern here. The tool would not count it either.

## Wrong if

- The count grows by an order of magnitude on a later branch. That would make
  the grep the thing that misses and put the index back on the table.
- A session reports that the grep answers with the two hundred `@todo` notes and
  nothing usable. That would say the subsystem limit is not enough and the
  pattern has to name the shape rather than the marker.
