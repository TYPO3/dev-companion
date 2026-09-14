---
id: D-ANS-018
title: 'A plugin is a kind of content element'
date: 2026-08-02
status: confirmed
coveredBy:
  - ProjectTest::aPluginTheInstallationReportsIsStillToldApart
  - ProjectTest::anExtbasePluginIsToldApartFromAnElementWithoutATemplate
---

# D-ANS-018 — A plugin is a kind of content element

**`typo3_extension_describe` says which kind of registration a content element
is, and an Extbase plugin points at `plugin.tx_<identifier>` where an element
points at a `templateName`.**

Both are items of `tt_content.CType`, so one list holds them and the identifier
alone tells them apart from nothing. A plugin reported as an element with no
`templateName` cost a real audit a finding about two TypoScript files nobody was
ever going to write.

## Evidence

- `ExtensionUtility::configurePlugin()` generates the rendering definition
  itself, in `.checkouts/14.3` and in `.checkouts/13.4` alike. That is
  `tt_content.<signature> =< lib.contentElement`, `templateName = Generic`, and
  `20 = EXTBASEPLUGIN` with the extension and plugin name below it. It enters
  the setup after `defaultContentRendering`. So the answer's "no templateName in
  this extension's TypoScript" was true of the extension and false of the
  element.
- `Generic.fluid.html` in `.checkouts/14.3` — `Generic.html` in
  `.checkouts/13.4`, the same body — renders `{content}` where there is any and
  otherwise `<f:cObject typoscriptObjectPath="tt_content.{data.CType}.20">`.
  That cObject is the `EXTBASEPLUGIN`, which is what "renders through the
  dispatcher" means concretely.
- `tt_content.<identifier>.templateName` therefore does reach a plugin. It is
  the wrapper's template, and core sets it to `Generic`. One set over it has to
  render `tt_content.<identifier>.20` or `{content}`, or the plugin's own output
  is gone. That is the first **Wrong if** of
  [`D-ANS-015`](ans-015-a-registration-the-extension-answer-misreads-is-inside-its-boundary.md),
  fired on its predicate and not on its conclusion. The absence is still no gap,
  because what the extension does not set core has already set.
- The plugin's own templates come from `plugin.tx_<signature>.view`.
  `FrontendConfigurationManager::getPluginConfiguration()` reads
  `plugin.tx_<strtolower(extensionName)>` merged with
  `plugin.tx_<strtolower(extensionName . '_' . pluginName)>` in both checkouts,
  and that second string is the same signature `registerPlugin()` writes into
  the CType column. One identifier addresses both places, so the answer derives
  nothing to name the second.
- `registerPlugin()` can only stand in `Configuration/TCA/Overrides/`, which is
  the directory this answer already parses.
  `ExtensionManagementUtility::addPlugin()` below it needs the extension key an
  override file passes and throws with 1404068038 anywhere else.
  `.checkouts/13.4` says so in the message.
- On `.checkouts/14.3` `addPlugin()` writes to `CType` and nothing else
  (#105538). On `.checkouts/13.4` it writes to `list_type` unless
  `configurePlugin()` recorded `CType`. #105076 deprecates that form for removal
  in 14.0.

## Decided

- The `contentElements` entry carries `kind` — `element` or `plugin` — and, on a
  plugin, `pluginSettings`: the file of this extension's TypoScript that
  configures `plugin.tx_<identifier>`. That replaces the absence, because it is
  the file the caller wanted when they went to look for a template.
- The answer reads the kind from the `registerPlugin()` call rather than asks
  the booted installation. The call is in a file this answer already tokenises
  and core allows it nowhere else. So a probe topic would have bought the same
  fact at the price of a boot. It also classifies on the installation path.
  There the CType list is one list for every extension and says nothing about
  how an entry arrived in it.
- A plugin signature read that way joins `contentElements`, which lengthens the
  answer on a checkout that does not boot. `registerPlugin()` was in neither
  recognised call before, so the file-read answer lacked two registered plugins
  altogether.
- The answer says the `list_type` residue in prose rather than branches on the
  version. One sentence names it, `ext_localconf.php` and the version it went
  away in. An `Instance::typo3Major()` branch would have put a second shape of
  this answer into the code. The form it serves is gone in the newest covered
  major.
- `typoScriptValues()` now holds reference lines — `plugin.tx_x < lib.y` — and
  not only assignments. A store of `=` lines never saw a path that only a
  reference names. That is the shortest way an extension configures a plugin at
  all.

## Assumed

- That `plugin.tx_<identifier>` is where the caller's next question goes, rather
  than the controller and action list. `configurePlugin()` in
  `ext_localconf.php` holds that list and nothing here reads it, so the answer
  names the file it can name.
- That a plugin whose `registerPlugin()` arguments are not literals is rare
  enough to leave to the general rule. The answer reports such an identifier as
  an element again, with the `templateName` sentence that was wrong for the two
  in the audit.

## Wrong if

- A session reads `pluginSettings: null` as "this plugin has no configuration".
  It says nothing about the site's TypoScript or another extension's, the same
  way `templateName` never did.
- An installation on 13.4 or older reports a `list_type` plugin among the
  content elements of an extension. The identifier is then in the answer as a
  CType it is not, and the prose sentence is where a reader has to catch it.
- An extension registers a plugin from a loop or a constant and the answer calls
  it an element with no template. The classification would then need the runtime
  after all, and
  `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']` is where the
  probe would read it.
- A caller wanted the Fluid template file and `plugin.tx_<identifier>.view` did
  not get them there. A site set holds the path, or the controller and action
  decide the template name. The pointer would then be one step short of the
  answer.

## Confirmed on 2026-08-23

The split holds and no report names any of the four **Wrong if**. The second is
the one this entry could only answer in prose, and the prose is there. The
plugin paragraph says that before the newest major a plugin could register under
the old list type rather than as its own content type. It says that the call is
in a file nothing here reads.
