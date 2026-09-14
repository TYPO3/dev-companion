---
id: D-ANS-015
title: 'A registration the extension answer misreads is inside its boundary'
date: 2026-08-02
status: open
coveredBy:
  - ProjectTest::anExtbasePluginIsToldApartFromAnElementWithoutATemplate
---

# D-ANS-015 — A registration the extension answer misreads is inside its boundary

**`typo3_extension_describe` answers the registrations an extension carries. One
it reports wrongly or never reaches is a defect inside that boundary, not an
argument about where the boundary runs.**

A conformance audit reports eight things it had to establish elsewhere and draws
the line itself. The server answers registration; the session reads
implementation from the checkout. Six of the eight are on the implementation
side. Two are registrations.

## Evidence

- `feedback/2026-07-31-193109`, re-run on 2026-08-02 through
  `bin/typo3-dev-companion` from `/home/benji/projects/site-new`, the directory
  it came from. `.mcp.json` there names this repository's binary.
- Six costs are the contents of files the caller already has open. Those are
  TCA, TypoScript, Fluid, controllers, repositories, eight test classes,
  `Initialisation/data.xml`, and a `PageTitleProvider` the extension does not
  have. The answer names `Initialisation/data.xml` as a registration file and
  says nothing about the uids in it, which is the line the feedback itself calls
  reasonable.
- The `PageTitleProvider` rule arrived.
  `knowledge/architecture-hints/general.json`, hint `frontend-records`, states
  that the `<title>` of such a detail view is a `PageTitleProvider` and nothing
  else. `feedback/2026-07-31-193005` records that `typo3_hint_lookup` returned
  that hint on both of the session's calls. What no lookup states is that this
  extension has none, which is the audit's.
- The PHP number is
  [`D-ANS-011`](ans-011-a-scope-answer-states-what-a-manifest-declares.md), from
  the same directory 25 minutes earlier. This one pairs the project's declared
  `^8.4` against a runtime 8.3.23 that bash read, which is a declaration against
  the effective number. `feedback/2026-07-31-193611` carries that half and has a
  card of its own.
- The German-label claim does not hold.
  `Configuration/Sets/Printworks/settings.definitions.yaml` carries English
  labels throughout — "Company details", "Template root path" — and last changed
  on 2026-07-30 11:39, a day before the report. The XLF half has its answer: the
  extension answer names all three files with "source-language de, no
  translations beside it", shipped in `fc80db8` (2026-07-31 02:08).
- `printworkssitepackage_catalogue` and `printworkssitepackage_teaser` are
  Extbase plugins. `Configuration/TCA/Overrides/tt_content.php` registers them
  with `ExtensionUtility::registerPlugin()`, `ext_localconf.php` configures
  them, and `products.typoscript` sets
  `plugin.tx_printworkssitepackage_catalogue < lib.printworksPlugin` and nothing
  under `tt_content.`. The answer lists both under "Content elements it adds" as
  "no templateName in this extension's TypoScript; another extension or the site
  may set it". A plugin renders through the dispatcher and has no `templateName`
  that could be absent. So the feedback's finding #3, that
  `Catalogue.typoscript` and `Teaser.typoscript` do not exist, is two files
  nothing was ever going to write.
- Nothing reaches `Configuration/Form/Printworks/config.yaml`.
  `Extension::ROOT_FILES` is a fixed list of paths, and since v14.2 (#109412)
  the core discovers a form set by its directory.
  `FormYamlCollectorConfigurator` collects `Configuration/Form/<SetName>/` in
  `.checkouts/14.3` and `.checkouts/main`, and the class exists in neither
  `.checkouts/13.4` nor `.checkouts/12.4`. Site sets are already answered from a
  convention of that same shape.
- All four extension files above last changed on 2026-07-29 and 2026-07-30. So
  the read covers the state the session saw, not one that moved under it.

## Decided

- The six implementation costs close. The strength the feedback states, that
  registration and runtime metadata get good answers, counts as evidence about
  this boundary. It is not a confirmation of the conformance skill, which is
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md).
  `D-SKL-001` and `D-SKL-002` gain nothing.
- The two registration costs go to the queue, one todo each, and the feedback
  shrinks to them. Both touch `src/` and both need a lookup of something about
  TYPO3, which [judging.md](../../documentation/records/judging.rst) puts on the
  far side of a close on the spot.
- The feedback's own suggestion is not taken. "Which TypoScript files are
  missing for registered CTypes" is the question that produced finding #3, and
  the catalogue case shows it malformed. The CType had its registration, the
  file was absent, and nothing was wrong. The queue takes instead an answer that
  says which kind of registration it looks at.
- Both queued items are the same failure `R-ANS-012` stands against. One is an
  absence reported as a defect the extension does not have, the other a file the
  answer never opens. Neither gets a requirement of its own.

## Assumed

- That the two identifiers reach the content-element list from the booted
  installation rather than from the file parser. `Extension::cTypes()` prefers
  the runtime list and attributes by the `EXT:` reference on the label or icon,
  and `registerPlugin()` sets both. The parser path recognises `addRecordType()`
  and `addTcaSelectItem()`, which that file does not call.
- That no site set in this installation supplies a `templateName` for either
  identifier. Nothing under `Configuration/Sets/` writes one, and the session
  checked the answer's hedge, that another extension or the site may set it,
  against this project only.

## Wrong if

- A site can set `tt_content.<plugin>.templateName` and have it take effect on
  an Extbase plugin. The absence would then be a real one, and the first queued
  item is a wording point rather than a defect.
- An extension answer that names its form sets still leaves a session to read
  `Configuration/Form/` by hand. The cost would then have been the content of
  the file rather than its registration, and the wrong half sits in the queue.
- A session reports a read of TCA, Fluid or a test class as a gap this server
  could close. It names the answer shape it wanted. The six closed here would
  then be a boundary taken from one session's own sentence rather than from what
  these tools can say.

## Since then

The first queued item landed. A read against two checkouts fired the first
**Wrong if** on its predicate and left its conclusion in place. A site can set
the template name and have it take effect. The registration puts the content
type on the wrapper with a generic template. So an override of it is meaningful.
The absence in this extension's TypoScript is still no gap, because core has
already set what the extension does not.
