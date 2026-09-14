---
id: D-KNW-116
title: The page object typo3 setup leaves behind is a subject this server owns
date: 2026-08-24
status: open
coveredBy:
  - HintsTest::whatCreateSiteLeavesRenderingThePageIsAnsweredPerMajor
---

# D-KNW-116 — The page object typo3 setup leaves behind is a subject this server owns

**`installation-setup` gains what `--create-site` leaves behind: a welcome page
object the parser reads after every site set, which overrides what the sets
render.**

The hint says the command creates the root page and its site configuration and
stops there. A session that installed an extension's own site set got TYPO3's
welcome page, HTTP 200 and an empty log. It found the cause with a grep of the
core for a CSS length out of the rendered markup.

## Evidence

- [`feedback/2026-08-24-140130`](../../feedback/archive/2026-08-24-140130-typo3-setup-create-site-also-writes-a-site.md)
  quotes the hint correctly. `installation-setup` carries "--create-site \<url\>
  and TYPO3_SETUP_CREATE_SITE create the root page and its site configuration
  under config/sites/". No statement of that hint says what else lands there.
- The file the session paid for is nowhere below `knowledge/` or `skills/`.
  `setup.typoscript` occurs in `site-sets`, where it is one of the files a set
  may hold, and in `content-rendering-templates`, where it is an extension's own
  file. It never occurs as `config/sites/<identifier>/setup.typoscript`, and
  never as a layer the core reads before the sets.
- Re-run against the corpus as it is now, on 2026-08-24. `bin/cli hints:probe`
  on the install task reaches `installation-setup` first, which is the hint the
  session read. On the symptom, "site set is applied but the page renders TYPO3
  welcome content instead", it reaches `site-sets` and `site-label-language`.
  Neither mentions a site-level file. So the right hint was in hand and the
  statement is not in it.
- The claim holds on `.checkouts/14.3`. `SetupService::createSite()` calls
  `writeSiteSetupTypoScript()` as its last step. That writes
  `config/sites/<identifier>/setup.typoscript` with `page = PAGE`,
  `page.10 = COA`, the TYPO3 logo as a `TEXT` and a `CONTENT` over `tt_content`
  on `colPos = 0`.
- The order the feedback infers is the mechanism, read in
  `SysTemplateTreeBuilder::createSiteTemplateInclude()`. The builder adds the
  site's sets as child includes of the site node. Then it tokenises the site's
  own `setup.typoscript` into that node's own line stream. So the parser reads
  it after every set, and `page = PAGE` in it replaces the object a set built.
- The mechanism is version-bound and the collision is not. `12.4` and `13.4`
  have no `writeSiteSetupTypoScript()`. Their `createSite()` inserts a
  `sys_template` row instead, `root = 1`, `clear = 3`, with the same welcome
  `page = PAGE` in its `config`. That is the stronger form of the same trap,
  because `clear = 3` discards the sets rather than overrides one object.
- The corpus already warns about that row on the other side of the boundary.
  `extension-test-site` states that `setUpFrontendRootPage()` and site sets
  exclude each other because a `sys_template` with `clear` resets the AST. The
  feedback names that statement as what saved it in the functional suite.
- One session and one report, with a sibling on the same file.
  `feedback/2026-08-24-140317` comes from the same directory and asks where a
  page object comes from at all for an extension that ships none. It has no
  judgement yet and keeps its own card. `bin/cli feedback:list` on 2026-08-24
  read 35 open feedback across four checkouts, nine of them from that directory.

## Decided

- **Step 1a, and queued.** The session reached the hint that owns the subject,
  so nothing about delivery or routing would have helped. The gap is a
  statement, and what it says about TYPO3 needs a read on the checkouts the
  judgement run did not open.
- The statement goes on `installation-setup`, beside the `--create-site` line it
  continues. The caller is on an install task, and
  [`D-KNW-046`](knw-046-the-non-interactive-install-path-is-a-subject-this-server-owns.md)
  is the entry that made that hint the place the install mechanics live.
- **The two majors are one statement bound twice, not one flat claim.** What a
  caller has to do is the same on both, find what the command left to render the
  page and take it out. The file to look in differs, so the read establishes the
  boundary rather than assumes the `14` form.
- `site-sets` owes a neighbour line. A session whose set does not render arrives
  on the symptom words, which the probe answers with that hint. A statement only
  the install hint carries is in reach for the caller who already knows the
  cause.
- `normal` rather than the `low` the card arrived at. The failure is silent,
  HTTP 200, an empty log, the set loaded beyond doubt. So the caller pays a
  debug cycle rather than reads an error, which is the cost
  [`D-FBK-027`](../feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)
  measures.
- Not `high`. One session reported it, and the gap is a statement rather than a
  capability.
- Neither archived nor trimmed. Nothing below `knowledge/` states any part of
  the mechanism today.
- The sibling card stays where it is. `2026-08-24-140317` asks a different
  question about the same file, where a page object comes from for an extension
  that defines none. To take it over would answer it with the half this entry
  owns.
- Nothing holds this yet. The test declares the id in the commit that writes the
  statement.

## Assumed

- That `main` writes the file as `14.3` does. `writeSiteSetupTypoScript()` is
  there and this run did not read it line for line. It read `12.4` and `13.4`
  for the absence of it rather than for what their row does at render time.
- That this command created the installation the session had. TYPO3 14 makes
  both site options inert where a distribution is already active, which
  `D-KNW-046` established. So an installation a distribution seeded has neither
  the site nor the file.
- That a caller arrives on the install task rather than on the symptom. That is
  what puts the statement on `installation-setup`, and the neighbour line is
  what the assumption costs if it is wrong.

## Wrong if

- The read finds the `12.4` and `13.4` row does not reach a set, because of
  `clear = 3` or because the core skips the row itself where the site carries
  sets. Then the statement is `since: 14` rather than bound twice, and the LTS
  half is a different subject.
- The statement lands and a session whose set does not render still reaches
  `site-sets` alone. The lever was that hint's curation, and this is step 4
  rather than step 1a.
- A caller removes the file, the set still renders nothing, and the gap was
  where a page object comes from. Then `2026-08-24-140317` is the gap and this
  is one paragraph of it.
- A second `typo3 setup` run behind `--force` rewrites the file over a site with
  a corrected rendering. The statement would owe the re-run as well as the first
  install, and what it says about the file's removal is not enough.

## Since then

The statement landed, bound twice as decided, and the read settled the first
**Wrong if**. The row does reach a set, and the oldest major has no sets to
reach. The sets hang below the site's own include node, and the parser reads a
node's children before its own lines. It resets the AST for that include and
nothing else. So the row discards what the sets built and the newer file
replaces only the paths it assigns.

The re-run behind the force flag is half settled. The command writes the site
again whenever it gets a URL and no distribution is active, with no guard on
what already exists. Whether the run reaches that step is another entry's
subject.
