---
id: D-KNW-048
title: 'What an impexp import rewrites in a site configuration is a subject this server owns'
date: 2026-08-03
status: open
---

# D-KNW-048 — What an impexp import rewrites in a site configuration is a subject this server owns

**What the import does to a site configuration inside an export file is inside
this server's boundary and absent from it. The feedback goes to the queue at
`normal`.**

Two statements in the corpus say that the import remaps only the root page id.
Both hold for a configuration in `Initialisation/Site/`, and neither holds for
one inside the export file. That is the route a distribution that ships nothing
but `data.xml` takes. So the corpus does not merely stay silent here. Read on
the route the session that reported was on, it points the other way.

## Evidence

- Re-run on 2026-08-03 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own query reaches `sitepackage-initial-content` at
  `appliesTo(27) + text(320)`, then `installation-upgrade`,
  `project-repository-layout` and `datahandler-seeding`. None of the four says
  what the import does to `base`.
- The narrower query reaches nothing.
  `bin/cli hints:probe "imported site base url 404 root"` matches no hint at all
  and returns 78 candidates as the index.
  `"distribution package import site configuration base URL rewritten impexp"`
  reaches `impexp-artifact`. That hint is about how to write the export rather
  than how to read it back.
- What the feedback claims about TYPO3 holds, read in `.checkouts/14.3` at
  `faf60eea22`. `Import::processSiteConfigurations()` starts at line 454, and
  lines 491 to 493 are the three the feedback quotes, `@TODO` comment included.
  The guard above it, at line 438, is
  `$this->importSiteConfigurations && $this->getBackendUser()->isAdmin()`. The
  same two assignments stand on `.checkouts/main` at `c71b2bdb2f`.
- It is new in the current major. Neither `processSiteConfigurations()` nor the
  `site_configurations` header key exists in `.checkouts/12.4` or
  `.checkouts/13.4`, in `Import.php` or anywhere else below
  `typo3/sysext/impexp/Classes/`. The feature arrived in 14.2 as
  `Feature-109340-IncludeSiteConfigurationsInImportExport.rst`.
- The manual does not answer it either, which is source 3 of the cost table.
  That changelog lists three things the import does. It remaps the `rootPageId`,
  skips a root page that already has a site, and suffixes an identifier another
  site has. The base is not among them. Its impact section says configurations
  "can now be preserved across export and import cycles". It names the base URL
  only as a column of the import preview.
- Nothing in core asserts it. The five site-configuration tests in
  `typo3/sysext/impexp/Tests/Functional/Import/PagesAndTtContentTest.php` cover
  the languages, the remapped root page, the skip, the `-1` suffix and the
  disabled flag. None of them reads `base`. The export fixture beside them
  carries `<base>https://example.com/</base>`, so the discarded value is real.
- The other route does what the corpus says it does.
  `ImportSiteConfigurationsOnPackageInitialization` copies the shipped directory
  into `config/sites/` and writes back `rootPageId` alone, so a `base` shipped
  in `Initialisation/Site/<identifier>/config.yaml` survives.
- So two statements are wrong on the route this session took. The second bullet
  of `initial-content-references` in `knowledge/hints/distribution.json` says
  the import "remaps the root page id to the page that was actually imported,
  and nothing else". The `limitToPages` bullet of `record-routing` in
  `knowledge/hints/records.json` says "of a site configuration shipped with an
  extension only rootPageId is remapped on import".
- The two conditions the feedback names as easy to misread are half covered.
  `initial-content-references` states the `Initialisation/Site/` route and the
  skip on an existing identifier. The admin condition on line 438 is nowhere
  below `knowledge/`. The impexp route skips on a site the root page already has
  rather than on the identifier.

## Decided

- Step 1a, and queued rather than closed on the spot. What lands is a statement
  about TYPO3 across four checkouts, which
  [`judging.md`](../../documentation/records/judging.rst) puts on the other side
  of the line from a wording fix.
- `normal` rather than the `low` the card arrived at. The corpus is not silent
  here. It carries two sentences a session on this route reads as an answer. A
  base nobody corrects is a 404 on every page of a fresh demo installation.
- Not `high`. The correction is one line in
  `config/sites/<identifier>/ config.yaml`, the session found it by itself, and
  nothing waits on it.
- Neither archived nor trimmed. Nothing below `knowledge/` or `skills/` answers
  any part of the observation today.
- The two skip conditions belong with the statement rather than in a card of
  their own. Both read as the cause and neither is, which is what the feedback
  spent its research on.
- This entry names the lines rather than leaves the research to the card. The
  todo is a statement to write and bind, not an investigation into whether there
  is one.
- Where the sentence goes is the todo's. `sitepackage-initial-content` and
  `initial-content-references` are both candidates. Which one carries it depends
  on whether it reads as an import property or as a rule about what a package
  ships.

## Assumed

- That `typo3/theme-camino` ships no `Initialisation/Site/`. Nothing on this
  machine holds the package. The feedback says so, and the behaviour it reports
  is what that condition produces.
- That the base rewrite is deliberate and stays. The `@TODO` beside it concerns
  page ids in routes and error handlers, not the base. The reason the feedback
  gives for it, that the importer cannot know the target domain, is sound.
- That one session wrote this feedback and the eleven beside it. They share a
  directory, a model, a subject and half an hour, and nothing in a feedback
  records a session.

## Wrong if

- The rewrite gains an event, an option or a subclass hook in a later release.
  The statement would then be about 14.2 to that version rather than about the
  major, and the correction would be the sentence worth the write.
- A session gets the written statement and still looks for the cause in the
  distribution package. The gap would be in the delivery or the routing, and
  this entry would have answered the expensive rung for a cheap problem.
- The two corpus statements turn out to describe the impexp route rather than
  `Initialisation/Site/`. They would then be plain errors rather than sentences
  a feature outgrew, and step 4 would have covered them.
- The sibling `feedback/2026-08-03-162826`, in hand on its own branch today,
  lands in the same hint. Its third point is this one from the other side.
  `--create-site` is inert in Composer mode, so no site configuration can carry
  the installation's own URL. The two would then be one gap carried by two
  cards, and one of them should carry both under a `**Serves:**` line naming the
  other.

## Since then

A session read three of the four **Wrong if** and none fired. The rewrite has
gained nothing. The same assignment, the same to-do under it, and no event or
option beside it, which is what the written statement says. The two corpus
statements were about one route and now say so, which makes them sentences a
feature outgrew rather than plain errors.

The sibling closed on its own the same day, and its point stands where this
entry expected the collision instead. That is two statements in two hints, one
per route, rather than one gap under two cards. What remains is the second, and
no research produces it.
